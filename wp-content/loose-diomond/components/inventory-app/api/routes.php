<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

/**
 * CORS ONLY for this endpoint
 */
add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {

    $route = $request->get_route();

    // Apply ONLY to this API
    if (strpos($route, '/ls/v1/inventory-filter') !== false) {

        $origin = get_http_origin();

        $allowed_origins = [
            'http://localhost',
            'http://localhost:3000',
            'https://stage.loosegrowndiamond.com'
        ];

        if ($origin && in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");
    }

    return $served;
}, 10, 4);


/**
 * Handle Preflight OPTIONS request
 */
add_action('rest_api_init', function () {

    register_rest_route('ls/v1', '/inventory-filter', [
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => 'ls_inventory_filter_api',
        'permission_callback' => '__return_true'
    ]);

    // OPTIONS support
    register_rest_route('ls/v1', '/inventory-filter', [
        'methods'  => WP_REST_Server::READABLE, // handles OPTIONS automatically
        'callback' => function () {
            return new WP_REST_Response(null, 200);
        },
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('ls/v1', '/inventory-colored', [
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => 'ls_inventory_colored_api',
        'permission_callback' => '__return_true'
    ]);
});


/**
 * Ring builder: a base-shape filter ("round") must also return diamonds
 * stored under a named cut that uses the same setting ("portuguese",
 * "carre", "flanders", "moval", "crisscut", "cushion_brilliant", ...).
 * Normal browsing keeps the exact base-shape filter.
 */
if (!function_exists('ls_expand_ringbuilder_shapes')) {
    function ls_expand_ringbuilder_shapes($shapes, WP_REST_Request $request) {
        global $inv_subshape_to_base;
        if (!is_array($shapes) || !ls_is_ringbuilder_request($request) || empty($inv_subshape_to_base)) {
            return $shapes;
        }
        $bases = array_map(function ($s) { return str_replace(' ', '_', strtolower(trim((string) $s))); }, $shapes);
        $out = $shapes;
        foreach ($inv_subshape_to_base as $sub => $base) {
            if (in_array(str_replace(' ', '_', strtolower($base)), $bases, true)) {
                $out[] = $sub;                          // portuguese / criss_cut
                $out[] = str_replace('_', ' ', $sub);   // criss cut
                $out[] = $sub . '_cut';                 // portuguese_cut
                $out[] = str_replace('_', ' ', $sub) . ' cut'; // portuguese cut
            }
        }
        return array_values(array_unique($out));
    }
}

/**
 * Inventory Filter API
 */
/**
 * True when the inventory request comes from the ring builder flow
 * (white: ringbuilder, fancy color: ringbuilder_c).
 */
if (!function_exists('ls_is_ringbuilder_request')) {
    function ls_is_ringbuilder_request(WP_REST_Request $request) {
        $type = sanitize_text_field((string) $request->get_param('inventory_type'));
        return in_array($type, array('ringbuilder', 'ringbuilder_c'), true);
    }
}

function ls_inventory_filter_api(WP_REST_Request $request)
{
    global $wpdb;

    $wh = "1=1";

    $start       = max(1, intval($request->get_param('ls_start')));
    $ls_per_page = max(1, intval($request->get_param('ls_per_page')) ?: 50);

    $ls_sortby = sanitize_text_field($request->get_param('ls_sortby'));
    $ls_sort   = sanitize_text_field($request->get_param('ls_sort'));

    /*
    PRICE FILTER
    */
   $price_range = explode(",", sanitize_text_field($request->get_param('price_range')));

    if (isset($price_range[0]) && isset($price_range[1])) {

        if ($price_range[0] === '' || $price_range[1] === '') {
            return;
        }

        if ($price_range[0] == $price_range[1]) {
            $wh .= $wpdb->prepare(" AND price = %f", $price_range[0]);
        } else {
            $wh .= $wpdb->prepare(" AND price BETWEEN %f AND %f", $price_range[0], $price_range[1]);
        }
    }
   /*print_r($wh);
    exit;*/
    /*
    SHAPE FILTER
    */
    $shapes = $request->get_param('shape');

    // subshape holds the specific named cut style (e.g. "square_emerald",
    // "pear_step", "cushion_modified") — these values live directly in the
    // `shape` column alongside the base shapes ("round", "emerald", ...).
    // When present, it's more specific than the base shape selection, so
    // it takes over the shape filter entirely instead of being AND'ed
    // against it (an emerald row stored as shape='square_emerald' would
    // never match a plain "shape IN ('emerald')" clause).
    $subshape = sanitize_text_field($request->get_param('subshape'));

    if (!empty($subshape)) {
        $wh .= $wpdb->prepare(" AND shape = %s", $subshape);
    } elseif (!empty($shapes) && is_array($shapes)) {

        $placeholders = implode(',', array_fill(0, count($shapes), '%s'));

        $wh .= $wpdb->prepare(
            " AND shape IN ($placeholders)",
            ...$shapes
        );
    }


    /*
    CARAT
    */
    $carat_range = explode(",", sanitize_text_field($request->get_param('carat_range')));

    if ($carat_range[0] !== '' && $carat_range[1] !== '') {

        if ($carat_range[0] == 0.0 || $carat_range[0] == 0) {
            $carat_range[0] = 0.1;
        }

        if ($carat_range[0] == $carat_range[1]) {
            $wh .= $wpdb->prepare(" AND carat = %f", $carat_range[0]);
        } else {
            $wh .= $wpdb->prepare(" AND carat BETWEEN %f AND %f", $carat_range[0], $carat_range[1]);
        }
    }

    /*
    CUT
    */
    $cut_range = explode(',', sanitize_text_field($request->get_param('cut_range')));

    if ($cut_range[0] !== '' && isset($cut_range[1]) && $cut_range[1] !== '') {

        $cut_from = (float) $cut_range[0];
        $cut_to   = (float) $cut_range[1];

        $cut_operator = '>';
        if ($cut_from == 0) {
            $cut_from = 1;
            $cut_operator = '>=';
        }

        if ($cut_from == $cut_to) {
            $wh .= $wpdb->prepare(" AND cut = %d", $cut_from);
        } else {
            $wh .= $wpdb->prepare(" AND (cut {$cut_operator} %d AND cut <= %d)", $cut_from, $cut_to);
        }
    }

    /*
    COLOR
    */
    $color_range = explode(',', sanitize_text_field($request->get_param('color')));

    if ($color_range[0] !== '' && isset($color_range[1]) && $color_range[1] !== '') {

        $color_from = (float) $color_range[0];
        $color_to   = (float) $color_range[1];

        $color_operator = '>';
        if ($color_from == 0 || $color_from == 1) {
            $color_from = ($color_from === 1.0) ? 2 : 1;
            $color_operator = '>=';
        }

        if ($color_from == $color_to) {
            $wh .= $wpdb->prepare(" AND color = %d", $color_from);
        } else {
            $wh .= $wpdb->prepare(" AND (color {$color_operator} %d AND color <= %d)", $color_from, $color_to);
        }
    }

    /*
    CLARITY
    */
    $clarity_range = explode(',', sanitize_text_field($request->get_param('clarity_range')));

    if ($clarity_range[0] !== '' && isset($clarity_range[1]) && $clarity_range[1] !== '') {

        $clarity_from = intval($clarity_range[0]);
        $clarity_to   = intval($clarity_range[1]);

        $clarity_operator = '>';

        // Legacy logic for manually added diamonds (value = 1)
        if ($clarity_from == 0 || $clarity_from == 1) {
            $clarity_from = ($clarity_from == 1) ? 2 : 1;
            $clarity_operator = '>=';
        }

        if ($clarity_from == $clarity_to) {
            $wh .= $wpdb->prepare(" AND clarity = %d", $clarity_from);
        } else {
            $wh .= $wpdb->prepare(" AND (clarity {$clarity_operator} %d AND clarity <= %d)", $clarity_from, $clarity_to);
        }
    }
    

    /*
    Certificate Type
    */
    $certificates = $request->get_param('certificate_type');

    if (!empty($certificates) && is_array($certificates)) {
        $cert_placeholders = implode(',', array_fill(0, count($certificates), '%s'));

        $wh .= $wpdb->prepare(
            " AND certificate_type IN ($cert_placeholders)",
            ...$certificates
        );
    }

    // Ring builder: never load Matching Pair diamonds (certificate_type 4).
    if (ls_is_ringbuilder_request($request)) {
        $wh .= " AND certificate_type != 4";
    }

    /*
    Diamond Type
    */
    $diamond_type = $request->get_param('dtype');

    if (!empty($diamond_type) && is_array($diamond_type)) {
        $dt_placeholders = implode(',', array_fill(0, count($diamond_type), '%s'));

        $wh .= $wpdb->prepare(
            " AND dtype IN ($dt_placeholders)",
            ...$diamond_type
        );
    }

    /*
    SKU override
    */
    $ls_sku = sanitize_text_field($request->get_param('ls_sku'));

    if ($ls_sku) {

        $replace_LG = ["LG", "LG-", "lg", "lg-", "Lg", "Lg-", "lG", "lG-"];
        $ls_sku = str_replace($replace_LG, "", $ls_sku);

        $wh .= $wpdb->prepare(
            " AND (sku = %s OR sku = %s OR sku = %s)",
            $ls_sku,
            "LG" . $ls_sku,
            "LG-" . $ls_sku
        );
    }

    /*
    EXCLUDE SKU (used by "Similar diamonds" on the single diamond page,
    so the diamond currently being viewed doesn't show in its own list)
    */
    $exclude_sku = sanitize_text_field($request->get_param('exclude_sku'));

    if ($exclude_sku) {
        $wh .= $wpdb->prepare(" AND sku != %s", $exclude_sku);
    }

    /*
    TABLE
    */
    $table_range_raw = sanitize_text_field($request->get_param('table_range'));

    if (!empty($table_range_raw) && $table_range_raw !== '50.00,80.00') {

        $table_range = array_map('floatval', explode(',', $table_range_raw));

        if ($table_range[0] == 0.0) {
            $table_range[0] = 0.1;
        }

        // legacy buffer logic
        $table_from = $table_range[0] - 0.5;
        $table_to   = $table_range[1] + 0.5;

        if ($table_from == $table_to) {
            $wh .= $wpdb->prepare(" AND av_table = %f", $table_from);
        } else {
            $wh .= $wpdb->prepare(
                " AND av_table BETWEEN %f AND %f",
                $table_from,
                $table_to
            );
        }
    }

    /*
    DEPTH
    */
    $depth_range_raw = sanitize_text_field($request->get_param('depth_range'));

    if (!empty($depth_range_raw) && $depth_range_raw !== '46.00,78.00') {

        $depth_range = array_map('floatval', explode(',', $depth_range_raw));

        if ($depth_range[0] == 0.0) {
            $depth_range[0] = 0.1;
        }

        if ($depth_range[0] == $depth_range[1]) {
            $wh .= $wpdb->prepare(" AND depth = %f", $depth_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND depth BETWEEN %f AND %f",
                $depth_range[0],
                $depth_range[1]
            );
        }
    }

    /*
    L/W RATIO
    */
    $lwratio_range_raw = sanitize_text_field($request->get_param('lwratio_range'));

    if (!empty($lwratio_range_raw) && $lwratio_range_raw !== '1.00,2.75') {

        $lwratio_range = array_map('floatval', explode(',', $lwratio_range_raw));

        if ($lwratio_range[0] == 0.0) {
            $lwratio_range[0] = 0.1;
        }

        if ($lwratio_range[0] == $lwratio_range[1]) {
            $wh .= $wpdb->prepare(" AND lwratio = %f", $lwratio_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND lwratio BETWEEN %f AND %f",
                $lwratio_range[0],
                $lwratio_range[1]
            );
        }
    }

    /*
    POLISH (numeric coded scale — 0=Good..2=Excellent, same operator
    pattern as cut_range: min==0 bumps to 1 with an inclusive >=,
    otherwise exclusive >, matching the legacy convention)
    */
    $polish_range = explode(',', sanitize_text_field($request->get_param('polish_range')));

    if ($polish_range[0] !== '' && isset($polish_range[1]) && $polish_range[1] !== '') {

        $polish_from = (float) $polish_range[0];
        $polish_to   = (float) $polish_range[1];

        $polish_operator = '>';
        if ($polish_from == 0) {
            $polish_from = 1;
            $polish_operator = '>=';
        }

        if ($polish_from == $polish_to) {
            $wh .= $wpdb->prepare(" AND polish = %d", $polish_from);
        } else {
            $wh .= $wpdb->prepare(" AND (polish {$polish_operator} %d AND polish <= %d)", $polish_from, $polish_to);
        }
    }

    /*
    SYMMETRY (numeric coded scale — same operator pattern as cut_range)
    */
    $symmetry_range = explode(',', sanitize_text_field($request->get_param('symmetry_range')));

    if ($symmetry_range[0] !== '' && isset($symmetry_range[1]) && $symmetry_range[1] !== '') {

        $symmetry_from = (float) $symmetry_range[0];
        $symmetry_to   = (float) $symmetry_range[1];

        $symmetry_operator = '>';
        if ($symmetry_from == 0) {
            $symmetry_from = 1;
            $symmetry_operator = '>=';
        }

        if ($symmetry_from == $symmetry_to) {
            $wh .= $wpdb->prepare(" AND symmetry = %d", $symmetry_from);
        } else {
            $wh .= $wpdb->prepare(" AND (symmetry {$symmetry_operator} %d AND symmetry <= %d)", $symmetry_from, $symmetry_to);
        }
    }

    /*
    FLUORESCENCE (numeric coded scale — same operator pattern as cut_range)
    */
    $fluorescence_range = explode(',', sanitize_text_field($request->get_param('fluorescence_range')));

    if ($fluorescence_range[0] !== '' && isset($fluorescence_range[1]) && $fluorescence_range[1] !== '') {

        $fluorescence_from = (float) $fluorescence_range[0];
        $fluorescence_to   = (float) $fluorescence_range[1];

        $fluorescence_operator = '>';
        if ($fluorescence_from == 0) {
            $fluorescence_from = 1;
            $fluorescence_operator = '>=';
        }

        if ($fluorescence_from == $fluorescence_to) {
            $wh .= $wpdb->prepare(" AND fluorescence = %d", $fluorescence_from);
        } else {
            $wh .= $wpdb->prepare(" AND (fluorescence {$fluorescence_operator} %d AND fluorescence <= %d)", $fluorescence_from, $fluorescence_to);
        }
    }

    /*
    GIRDLE THICKNESS (numeric coded scale — param is girdle_range, not
    girdle_thickness_range, matching the frontend's advancedFilters key.
    Same operator pattern as cut_range)
    */
    $girdle_range = explode(',', sanitize_text_field($request->get_param('girdle_range')));

    if ($girdle_range[0] !== '' && isset($girdle_range[1]) && $girdle_range[1] !== '') {

        $girdle_from = (float) $girdle_range[0];
        $girdle_to   = (float) $girdle_range[1];

        $girdle_operator = '>';
        if ($girdle_from == 0) {
            $girdle_from = 1;
            $girdle_operator = '>=';
        }

        if ($girdle_from == $girdle_to) {
            $wh .= $wpdb->prepare(" AND girdle_thickness = %d", $girdle_from);
        } else {
            $wh .= $wpdb->prepare(" AND (girdle_thickness {$girdle_operator} %d AND girdle_thickness <= %d)", $girdle_from, $girdle_to);
        }
    }

    /*
    CULET (numeric coded scale — same operator pattern as cut_range)
    */
    $culet_range = explode(',', sanitize_text_field($request->get_param('culet_range')));

    if ($culet_range[0] !== '' && isset($culet_range[1]) && $culet_range[1] !== '') {

        $culet_from = (float) $culet_range[0];
        $culet_to   = (float) $culet_range[1];

        $culet_operator = '>';
        if ($culet_from == 0) {
            $culet_from = 1;
            $culet_operator = '>=';
        }

        if ($culet_from == $culet_to) {
            $wh .= $wpdb->prepare(" AND culet = %d", $culet_from);
        } else {
            $wh .= $wpdb->prepare(" AND (culet {$culet_operator} %d AND culet <= %d)", $culet_from, $culet_to);
        }
    }

    /*
    CROWN ANGLE
    */
    $crown_angle_range_raw = sanitize_text_field($request->get_param('crown_angle_range'));

    if (!empty($crown_angle_range_raw)) {

        $crown_angle_range = array_map('floatval', explode(',', $crown_angle_range_raw));

        if ($crown_angle_range[0] == $crown_angle_range[1]) {
            $wh .= $wpdb->prepare(" AND crown_angle = %f", $crown_angle_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND crown_angle BETWEEN %f AND %f",
                $crown_angle_range[0],
                $crown_angle_range[1]
            );
        }
    }

    /*
    PAVILION ANGLE
    */
    $pavilion_angle_range_raw = sanitize_text_field($request->get_param('pavilion_angle_range'));

    if (!empty($pavilion_angle_range_raw)) {

        $pavilion_angle_range = array_map('floatval', explode(',', $pavilion_angle_range_raw));

        if ($pavilion_angle_range[0] == $pavilion_angle_range[1]) {
            $wh .= $wpdb->prepare(" AND pavilion_angle = %f", $pavilion_angle_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND pavilion_angle BETWEEN %f AND %f",
                $pavilion_angle_range[0],
                $pavilion_angle_range[1]
            );
        }
    }

    /*
    WIDTH (mm)
    */
    $width_range_raw = sanitize_text_field($request->get_param('width_range'));

    if (!empty($width_range_raw)) {

        $width_range = array_map('floatval', explode(',', $width_range_raw));

        if ($width_range[0] == $width_range[1]) {
            $wh .= $wpdb->prepare(" AND width = %f", $width_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND width BETWEEN %f AND %f",
                $width_range[0],
                $width_range[1]
            );
        }
    }

    /*
    HEIGHT (mm)
    */
    $height_range_raw = sanitize_text_field($request->get_param('height_range'));

    if (!empty($height_range_raw)) {

        $height_range = array_map('floatval', explode(',', $height_range_raw));

        if ($height_range[0] == $height_range[1]) {
            $wh .= $wpdb->prepare(" AND height = %f", $height_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND height BETWEEN %f AND %f",
                $height_range[0],
                $height_range[1]
            );
        }
    }

    /*
    LENGTH (mm)
    */
    $length_range_raw = sanitize_text_field($request->get_param('length_range'));

    if (!empty($length_range_raw)) {

        $length_range = array_map('floatval', explode(',', $length_range_raw));

        if ($length_range[0] == $length_range[1]) {
            $wh .= $wpdb->prepare(" AND length = %f", $length_range[0]);
        } else {
            $wh .= $wpdb->prepare(
                " AND length BETWEEN %f AND %f",
                $length_range[0],
                $length_range[1]
            );
        }
    }

    $heartarrow = sanitize_text_field($request->get_param('heartarrow'));
    if (!empty($heartarrow)  &&  ($heartarrow != '0')) {
        $wh .= $wpdb->prepare(" AND ha = %d", $heartarrow);
    }

    /*
    SHIPPING FILTER (merged column: 0 = none, 1 = express, 2 = overnight)
    */
    $shipping = intval($request->get_param('shipping'));
    if ($shipping === 1 || $shipping === 2) {
        $wh .= $wpdb->prepare(" AND shipping = %d", $shipping);
    }

    $is_sale = intval($request->get_param('is_sale'));
    if ($is_sale == 1) {
        $wh .= $wpdb->prepare(" AND sale = %d", 1);
    }

    /*
    MEDIA FILTER (Square Images / 360° View — checkbox sends the
    specific option(s) picked via `media`, but the DB only tracks a
    single availability flag, so the frontend also sends is_media=1
    whenever any option is checked)
    */
    $is_media = intval($request->get_param('is_media'));
    if ($is_media == 1) {
        $wh .= $wpdb->prepare(" AND is_media = %d", 1);
    }

    /*
    STATUS
    */
    $wh .= " AND status = 0";

    /*
    SORTING
    */
    $allowed_sort = ['price', 'carat', 'cut', 'color', 'clarity', 'sale', 'shape'];

    if (!in_array($ls_sortby, $allowed_sort)) {
        //$ls_sortby = 'sale';
    }

    $ls_sort = empty($ls_sort) ? 'ASC' : (strtoupper($ls_sort) === 'ASC' ? 'ASC' : 'DESC');

    $offset = $ls_per_page * ($start - 1);

    /*
    MAIN QUERY
    */
    $sale_ordering ='';
    if($ls_sortby==''){

       
        $sql = "SELECT * FROM ls_inventory
            WHERE $wh
             ORDER BY sale DESC, (shape = 'round') DESC, price ASC
            LIMIT %d OFFSET %d";
    }else{
         $sql = "SELECT * FROM ls_inventory
            WHERE $wh
            ORDER BY $ls_sortby $ls_sort
            LIMIT %d OFFSET %d";
    }
   

    $query = $wpdb->prepare($sql, $ls_per_page, $offset);
  /*  print_r($query);
    print_r($wh);
     print_r($ls_sortby);
    exit;*/
    $result = $wpdb->get_results($query);

    /*
    TOTAL
    */
    $total = $wpdb->get_var("SELECT COUNT(*) FROM ls_inventory WHERE $wh");

    return rest_ensure_response([
        'status' => !empty($result),
        'total'  => intval($total),
        'current_count' => count($result),
        'data' => $result
    ]);
}

function ls_inventory_colored_api(WP_REST_Request $request)
{

    global $wpdb;

    $where   = ["status = 0"];
    $params  = [];

    /*
    =============================
    PRICE
    =============================
    */
    if ($price = $request->get_param('price_range')) {

        list($min, $max) = array_map('floatval', explode(',', $price));

        if ($min == $max) {
            $where[] = "price = %f";
            $params[] = $min;
        } else {
            $where[] = "price BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    SHAPE
    =============================
    */
    // subshape (named cut style — e.g. "square_radiant", "cushion_brilliant")
    // lives in the same `shape` column as the base shapes and is more
    // specific, so it takes over the shape filter when present rather
    // than being combined with the base shape IN(...) clause.
    $subshape = sanitize_text_field($request->get_param('subshape'));

    if (!empty($subshape)) {
        $where[] = "shape = %s";
        $params[] = $subshape;
    } elseif ($shapes = $request->get_param('shape')) {
        if (is_array($shapes)) {
    
            $placeholders = implode(',', array_fill(0, count($shapes), '%s'));
            $where[] = "shape IN ($placeholders)";
            $params = array_merge($params, $shapes);
        }
    }

    /*
    =============================
    COLOR
    =============================
    */
    if ($color = $request->get_param('colored')) {
        $color = (int) $color; // convert to integer
        $where[] = "color = %d";
        $params[] = $color;
    }

    /*
    =============================
    INTENSITY
    =============================
    */
    if ($intensity = $request->get_param('intensity')) {
        // If it's a comma string like "6,4"
        if (!is_array($intensity)) {
            $intensity = explode(',', $intensity);
        }

        $intensity = array_map('intval', $intensity);

        $placeholders = implode(',', array_fill(0, count($intensity), '%d'));
        $where[] = "intensity IN ($placeholders)";
        $params = array_merge($params, $intensity);
    }

    /*
    =============================
    CARAT
    =============================
    */
    if ($carat = $request->get_param('carat_range')) {

        list($min, $max) = array_map('floatval', explode(',', $carat));

        if ($min == $max) {
            $where[] = "carat = %f";
            $params[] = $min;
        } else {
            $where[] = "carat BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    CUT
    =============================
    */
    if ($cut = $request->get_param('cut_range')) {

        list($cut_from, $cut_to) = array_map('floatval', explode(',', $cut));

        $operator = '>';
        if ($cut_from == 0) {
            $cut_from = 1;
            $operator = '>=';
        }

        if ($cut_from == $cut_to) {
            $where[]  = "cut = %d";
            $params[] = $cut_from;
        } else {
            $where[]  = "(cut {$operator} %d AND cut <= %d)";
            $params[] = $cut_from;
            $params[] = $cut_to;
        }
    }

    /*
    =============================
    CLARITY
    =============================
    */
    

    if ($clarity = $request->get_param('clarity_range')) {

        $clarity_range = explode(',', sanitize_text_field($clarity));

        if (isset($clarity_range[0]) && isset($clarity_range[1])) {

             $clarity_from = intval($clarity_range[0]);
             $clarity_to   = intval($clarity_range[1]);

            $clarity_operator = '>';

            // Legacy logic for manually added diamonds (value = 1)
            if ($clarity_from == 0 || $clarity_from == 1) {
                $clarity_from = ($clarity_from == 1) ? 2 : 1;
                $clarity_operator = '>=';
            }

            if ($clarity_from == $clarity_to) {
                $where[]  = "clarity = %d";
                $params[] = $clarity_from;
            } else {
                $where[]  = "(clarity {$clarity_operator} %d AND clarity <= %d)";
                $params[] = $clarity_from;
                $params[] = $clarity_to;
            }
        }
       /*print_r($clarity_range);
        print_r($params);*/
    }

    /*
    =============================
    CERTIFICATE
    =============================
    */
    if ($cert = $request->get_param('certificate_type')) {
        if (is_array($cert)) {
            $placeholders = implode(',', array_fill(0, count($cert), '%s'));
            $where[] = "certificate_type IN ($placeholders)";
            $params = array_merge($params, $cert);
        }
    }

    // Ring builder: never load Matching Pair diamonds (certificate_type 4).
    if (ls_is_ringbuilder_request($request)) {
        $where[] = "certificate_type != 4";
    }
    
    /*
    =============================
    DIAMOND TYPE
    =============================
    */
    if ($diamond_type = $request->get_param('dtype')) {
        if (is_array($diamond_type)) {
            $dt_placeholders = implode(',', array_fill(0, count($diamond_type), '%s'));
            $where[] = "dtype IN ($dt_placeholders)";
            $params = array_merge($params, $diamond_type);
        }
    }

    /*
    =============================
    SKU OVERRIDE (important)
    =============================
    */
    if ($sku = $request->get_param('ls_sku')) {

        $replace = ["LG", "LG-", "lg", "lg-", "Lg", "Lg-"];
        $sku = str_replace($replace, "", $sku);

        $where[] = "(LOWER(sku) = LOWER(%s) 
                 OR LOWER(sku) = LOWER(%s) 
                 OR LOWER(sku) = LOWER(%s))";

        $params[] = $sku;
        $params[] = "LG" . $sku;
        $params[] = "LG-" . $sku;
    }

    /*
    =============================
    EXCLUDE SKU (used by "Similar diamonds" on the single diamond page,
    so the diamond currently being viewed doesn't show in its own list)
    =============================
    */
    $exclude_sku = sanitize_text_field($request->get_param('exclude_sku'));
    if ($exclude_sku) {
        $where[] = "sku != %s";
        $params[] = $exclude_sku;
    }

    /*
    =============================
    TABLE
    =============================
    */
    $table_range_raw = sanitize_text_field($request->get_param('table_range'));

    if (!empty($table_range_raw) && $table_range_raw !== '50.00,80.00') {

        list($min, $max) = array_map('floatval', explode(',', $table_range_raw));

        if ($min == 0.0 || $min == 0) {
            $min = 0.1;
        }

        // legacy buffer logic
        $table_from = $min - 0.5;
        $table_to   = $max + 0.5;

        if ($table_from == $table_to) {
            $where[]  = "av_table = %f";
            $params[] = $table_from;
        } else {
            $where[]  = "av_table BETWEEN %f AND %f";
            $params[] = $table_from;
            $params[] = $table_to;
        }
    }

    /*
    =============================
    DEPTH
    =============================
    */
    $depth_range_raw = sanitize_text_field($request->get_param('depth_range'));

    if (!empty($depth_range_raw) && $depth_range_raw !== '46.00,78.00') {

        list($min, $max) = array_map('floatval', explode(',', $depth_range_raw));

        if ($min == 0.0 || $min == 0) {
            $min = 0.1;
        }

        if ($min == $max) {
            $where[]  = "depth = %f";
            $params[] = $min;
        } else {
            $where[]  = "depth BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    L/W RATIO
    =============================
    */
    $lwratio_range_raw = sanitize_text_field($request->get_param('lwratio_range'));

    if (!empty($lwratio_range_raw) && $lwratio_range_raw !== '1.00,2.75') {

        list($min, $max) = array_map('floatval', explode(',', $lwratio_range_raw));

        if ($min == 0.0 || $min == 0) {
            $min = 0.1;
        }

        if ($min == $max) {
            $where[]  = "lwratio = %f";
            $params[] = $min;
        } else {
            $where[]  = "lwratio BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    POLISH (numeric coded scale, range — same operator pattern as
    cut_range: min==0 bumps to 1 with an inclusive >=, otherwise
    exclusive >, matching the legacy convention)
    =============================
    */
    if ($polish = $request->get_param('polish_range')) {

        list($polish_from, $polish_to) = array_map('floatval', explode(',', $polish));

        $polish_operator = '>';
        if ($polish_from == 0) {
            $polish_from = 1;
            $polish_operator = '>=';
        }

        if ($polish_from == $polish_to) {
            $where[]  = "polish = %d";
            $params[] = $polish_from;
        } else {
            $where[]  = "(polish {$polish_operator} %d AND polish <= %d)";
            $params[] = $polish_from;
            $params[] = $polish_to;
        }
    }

    /*
    =============================
    SYMMETRY (numeric coded scale, range — same operator pattern as cut_range)
    =============================
    */
    if ($symmetry = $request->get_param('symmetry_range')) {

        list($symmetry_from, $symmetry_to) = array_map('floatval', explode(',', $symmetry));

        $symmetry_operator = '>';
        if ($symmetry_from == 0) {
            $symmetry_from = 1;
            $symmetry_operator = '>=';
        }

        if ($symmetry_from == $symmetry_to) {
            $where[]  = "symmetry = %d";
            $params[] = $symmetry_from;
        } else {
            $where[]  = "(symmetry {$symmetry_operator} %d AND symmetry <= %d)";
            $params[] = $symmetry_from;
            $params[] = $symmetry_to;
        }
    }

    /*
    =============================
    FLUORESCENCE (numeric coded scale, range — same operator pattern as cut_range)
    =============================
    */
    if ($fluorescence = $request->get_param('fluorescence_range')) {

        list($fluorescence_from, $fluorescence_to) = array_map('floatval', explode(',', $fluorescence));

        $fluorescence_operator = '>';
        if ($fluorescence_from == 0) {
            $fluorescence_from = 1;
            $fluorescence_operator = '>=';
        }

        if ($fluorescence_from == $fluorescence_to) {
            $where[]  = "fluorescence = %d";
            $params[] = $fluorescence_from;
        } else {
            $where[]  = "(fluorescence {$fluorescence_operator} %d AND fluorescence <= %d)";
            $params[] = $fluorescence_from;
            $params[] = $fluorescence_to;
        }
    }

    /*
    =============================
    GIRDLE THICKNESS (numeric coded scale, range — param is girdle_range,
    matching the frontend's advancedFilters key. Same operator pattern
    as cut_range)
    =============================
    */
    if ($girdle = $request->get_param('girdle_range')) {

        list($girdle_from, $girdle_to) = array_map('floatval', explode(',', $girdle));

        $girdle_operator = '>';
        if ($girdle_from == 0) {
            $girdle_from = 1;
            $girdle_operator = '>=';
        }

        if ($girdle_from == $girdle_to) {
            $where[]  = "girdle_thickness = %d";
            $params[] = $girdle_from;
        } else {
            $where[]  = "(girdle_thickness {$girdle_operator} %d AND girdle_thickness <= %d)";
            $params[] = $girdle_from;
            $params[] = $girdle_to;
        }
    }

    /*
    =============================
    CULET (numeric coded scale, range — param is culet_range. Same
    operator pattern as cut_range)
    =============================
    */
    if ($culet = $request->get_param('culet_range')) {

        list($culet_from, $culet_to) = array_map('floatval', explode(',', $culet));

        $culet_operator = '>';
        if ($culet_from == 0) {
            $culet_from = 1;
            $culet_operator = '>=';
        }

        if ($culet_from == $culet_to) {
            $where[]  = "culet = %d";
            $params[] = $culet_from;
        } else {
            $where[]  = "(culet {$culet_operator} %d AND culet <= %d)";
            $params[] = $culet_from;
            $params[] = $culet_to;
        }
    }

    /*
    =============================
    CROWN ANGLE
    =============================
    */
    $crown_angle_range_raw = sanitize_text_field($request->get_param('crown_angle_range'));

    if (!empty($crown_angle_range_raw)) {

        list($min, $max) = array_map('floatval', explode(',', $crown_angle_range_raw));

        if ($min == $max) {
            $where[]  = "crown_angle = %f";
            $params[] = $min;
        } else {
            $where[]  = "crown_angle BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    PAVILION ANGLE
    =============================
    */
    $pavilion_angle_range_raw = sanitize_text_field($request->get_param('pavilion_angle_range'));

    if (!empty($pavilion_angle_range_raw)) {

        list($min, $max) = array_map('floatval', explode(',', $pavilion_angle_range_raw));

        if ($min == $max) {
            $where[]  = "pavilion_angle = %f";
            $params[] = $min;
        } else {
            $where[]  = "pavilion_angle BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    WIDTH (mm)
    =============================
    */
    $width_range_raw = sanitize_text_field($request->get_param('width_range'));

    if (!empty($width_range_raw)) {

        list($min, $max) = array_map('floatval', explode(',', $width_range_raw));

        if ($min == $max) {
            $where[]  = "width = %f";
            $params[] = $min;
        } else {
            $where[]  = "width BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    HEIGHT (mm)
    =============================
    */
    $height_range_raw = sanitize_text_field($request->get_param('height_range'));

    if (!empty($height_range_raw)) {

        list($min, $max) = array_map('floatval', explode(',', $height_range_raw));

        if ($min == $max) {
            $where[]  = "height = %f";
            $params[] = $min;
        } else {
            $where[]  = "height BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    /*
    =============================
    LENGTH (mm)
    =============================
    */
    $length_range_raw = sanitize_text_field($request->get_param('length_range'));

    if (!empty($length_range_raw)) {

        list($min, $max) = array_map('floatval', explode(',', $length_range_raw));

        if ($min == $max) {
            $where[]  = "length = %f";
            $params[] = $min;
        } else {
            $where[]  = "length BETWEEN %f AND %f";
            $params[] = $min;
            $params[] = $max;
        }
    }

    $heartarrow = sanitize_text_field($request->get_param('heartarrow'));
    if (!empty($heartarrow)  &&  ($heartarrow != '0')) {
        $where[]  = "ha = %d";
        $params[] = $heartarrow;
    }

    /*
    =============================
    SHIPPING FILTER (merged column: 0 = none, 1 = express, 2 = overnight)
    =============================
    */
    $shipping = intval($request->get_param('shipping'));
    if ($shipping === 1 || $shipping === 2) {
        $where[]  = "shipping = %d";
        $params[] = $shipping;
    }

    $is_sale = intval($request->get_param('is_sale'));
    if ($is_sale === 1) {
        $where[]  = "sale = %d";
        $params[] = 1;
    }

    /*
    =============================
    MEDIA FILTER (see note in ls_inventory_filter_api)
    =============================
    */
    $is_media = intval($request->get_param('is_media'));
    if ($is_media === 1) {
        $where[]  = "is_media = %d";
        $params[] = 1;
    }

    /*
    =============================
    RING BUILDER
    =============================
    */
    if ($request->get_param('ls_is_rb')) {
        $where[] = "(vendor_country!='United States' OR vendor_country IS NULL OR vendor_country='')";
    }

    /*
    =============================
    PAGINATION
    (Better than OFFSET — but keeping for compatibility)
    =============================
    */
    $page     = max(1, intval($request->get_param('ls_start') ?: 1));
    $per_page = min(52, intval($request->get_param('ls_per_page') ?: 52));
    $offset   = $per_page * ($page - 1);

    /*
    =============================
    SORT
    =============================
    */
    $allowed_sort = ['price', 'carat', 'cut', 'clarity', 'color', 'sale', 'intensity', 'shape'];
    $sortby = in_array($request->get_param('ls_sortby'), $allowed_sort)
        ? $request->get_param('ls_sortby')
        : '';

    $sort = empty($request->get_param('ls_sort')) ? 'ASC' : (strtoupper($request->get_param('ls_sort')) === 'ASC' ? 'ASC' : 'DESC');
    //$sort = strtoupper($request->get_param('ls_sort')) === '' ? 'ASC' : 'DESC';
    

    $where_sql = implode(" AND ", $where);


    $sale_ordering ='';
    
    if($sortby==''){
        $sql = $wpdb->prepare(
            "SELECT * FROM ls_inventory_c
             WHERE $where_sql
             ORDER BY sale DESC, (shape = 'round') DESC, price ASC
             LIMIT %d OFFSET %d",
            array_merge($params, [$per_page, $offset])
        ); 
    }else{
        $sql = $wpdb->prepare(
            "SELECT * FROM ls_inventory_c
             WHERE $where_sql
             ORDER BY $sortby $sort
             LIMIT %d OFFSET %d",
            array_merge($params, [$per_page, $offset])
        ); 
    }
    
/*
echo $sql;
  exit;*/

    $data = $wpdb->get_results($sql);
    
    /*
    TOTAL
    */
    $total_sql = $wpdb->prepare(
        "SELECT COUNT(*) FROM ls_inventory_c
        WHERE $where_sql",
        $params
    );

    $total = $wpdb->get_var($total_sql);

    return rest_ensure_response([
        'status' => !empty($data),
        'total'  => intval($total),
        'current_count' => count($data),
        'data' => $data
    ]);
}