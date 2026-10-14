<?php
/**
 * Inventory App — module loader
 *
 * Renders the three remaining apps from the original shared
 * react/inventory/ bundle: the main inventory filter/grid page
 * (App.jsx → #ls-inventory-root, page-inventory.php), the colored
 * variant (App2.jsx → #ls-inventory-root2, page-inventory2.php), and
 * the "similar diamonds" widget (SimilarDiamonds.jsx →
 * #ls-similar-diamonds-root, used on product pages via
 * inc/diamond-detail-render.php). App3 and App4 were split out
 * earlier into components/inventory-listing/ and
 * components/ring-builder-breadcrumb/ — these three stay bundled
 * together since that's how they're actually built and consumed
 * (one script handle, one window.LD_THEME payload, one
 * window.ls_ajax_object payload).
 *
 * This is the ONLY file functions.php needs to require for this
 * feature. It wires up:
 *   - api/routes.php — registers the ls/v1 inventory-filter and
 *     inventory-colored REST routes (moved from
 *     inc/ls-inventory-api.php; dedicated to App.jsx/App2.jsx, not
 *     shared elsewhere, so this is a real move)
 *   - the frontend script/style enqueue + window.LD_THEME /
 *     window.ls_ajax_object localization for the built React app
 *     (out/inventory), hooked to wp_enqueue_scripts on the same
 *     conditions as before: page-inventory.php, page-inventory2.php,
 *     page-ring-preview.php, is_product(), or the diamond_sku query var
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/inventory-app/index.php';
 *
 * Ported verbatim from inc/script-load.php's loose_diomond_scripts_react()
 * — only the doc comment and hook registration around it changed; the
 * function body (filter defaults, price/carat range queries, ring-builder
 * session handling, etc.) is untouched. That function used
 * loose_get_category_price_range(), but that helper is only used by
 * components/inventory-listing/ — it moved there instead, not here.
 *
 * Relies on getShapeValueById() (functions.php) and
 * ls_process_setting_data() (inc/comman-function.php) — both shared,
 * not duplicated here.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API: registers ls/v1 inventory-filter / inventory-colored ---------
require_once __DIR__ . '/api/routes.php';

// --- Frontend enqueue + mount helper ------------------------------------

/**
 * Print the mount points for all three apps this module serves. Call
 * this — or print the individual divs directly — from any template
 * that needs one of #ls-inventory-root, #ls-inventory-root2, or
 * #ls-similar-diamonds-root. The script/style enqueue and
 * window.LD_THEME / window.ls_ajax_object localization happen
 * separately, automatically, via the wp_enqueue_scripts hook below.
 */
function lgd_render_inventory_app() {
    echo '<div id="ls-inventory-root"></div>';
}

function lgd_render_inventory_app2() {
    echo '<div id="ls-inventory-root2"></div>';
}

function loose_diomond_scripts_react()
{
	$cart_product_ids = array();
	$cart_ring_product_ids = array(); // pids already added to cart via the ring builder (mirrors $is_in_cart_ring on the PHP detail page)

    if (class_exists('WooCommerce') && WC()->cart) {

        foreach (WC()->cart->get_cart() as $cart_item) {

            // Use variation ID if exists, otherwise product ID
            $product_id = !empty($cart_item['variation_id'])
                ? $cart_item['variation_id']
                : $cart_item['product_id'];

            $cart_product_ids[] = $product_id;

            if ( function_exists('ls_is_diamond_in_cart_ring') && ls_is_diamond_in_cart_ring( $product_id ) ) {
                $cart_ring_product_ids[] = $product_id;
            }
        }

        // Remove duplicates
        $cart_product_ids = array_values(array_unique($cart_product_ids));
        $cart_ring_product_ids = array_values(array_unique($cart_ring_product_ids));
    }

	// Enqueue Inventory React bundle and styles for Inventory page templates
	if (is_page_template(array('page-inventory.php', 'page-inventory2.php', 'page-ring-preview.php')) ||
		is_product() ||
		get_query_var('diamond_sku')) {
		$dist_dir = get_template_directory() . '/out/inventory';
		$script_file = $dist_dir . '/index.js';
		$style_file = $dist_dir . '/assets/index.css';

		$script_url = get_template_directory_uri() . '/out/inventory/index.js';
		$style_url = get_template_directory_uri() . '/out/inventory/assets/index.css';

		$script_ver = file_exists($script_file) ? filemtime($script_file) : _S_VERSION;
		$style_ver = file_exists($style_file) ? filemtime($style_file) : _S_VERSION;

		// Enqueue the inventory React bundle (ensure single registration)
		wp_enqueue_script('loose-diomond-inventory', $script_url, array('jquery'), $script_ver, true);

		add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	        if ( $handle !== 'loose-diomond-inventory' ) {
	            return $tag;
	        }
	        return str_replace( ' src', ' type="module" src', preg_replace( '/\stype=["\'][^"\']*["\']/', '', $tag, 1 ) );
	    }, 10, 2 );

		// Provide AJAX URL and nonce to the React app
		wp_localize_script('loose-diomond-inventory', 'ls_ajax_object', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('ls_add_to_cart_nonce'),
		));

		if (file_exists($style_file)) {
			wp_enqueue_style('loose-diomond-inventory-style', $style_url, array(), $style_ver);
		}

		$index_style_file = $dist_dir . '/index.css';

		$index_style_url = get_template_directory_uri() . '/out/inventory/assets/index.css';
		$index_style_ver = file_exists($index_style_file) ? filemtime($index_style_file) : _S_VERSION;

		wp_enqueue_style('loose-diomond-inventory-index-style', $index_style_url, array(), $index_style_ver);

		// Default values
		$min_price = '';
		$max_price = '';
		$min_carat = '';
		$max_carat = '';
		$min_length = '';
		$max_length = '';
		$min_width = '';
		$max_width = '';
		$min_height = '';
		$max_height = '';
		$min_fluorescence = '';
		$max_fluorescence = '';
		$min_polish = '';
		$max_polish = '';
		$min_symmetry = '';
		$max_symmetry = '';
		$min_crown_angle = '';
		$max_crown_angle = '';
		$min_pavilion_angle = '';
		$max_pavilion_angle = '';
		$min_girdle = '';
		$max_girdle = '';
		$min_culet = '';
		$max_culet = '';

		// ONLY run query if page-inventory.php
		if (is_page_template('page-inventory.php')) {
			global $wpdb;

			$result = $wpdb->get_row("
				SELECT
					MIN(price) AS min_price,
					MAX(price) AS max_price,
					MIN(carat) AS min_carat,
					MAX(carat) AS max_carat,
					MIN(length) AS min_length,
					MAX(length) AS max_length,
					MIN(width) AS min_width,
					MAX(width) AS max_width,
					MIN(height) AS min_height,
					MAX(height) AS max_height,
					MIN(fluorescence) AS min_fluorescence,
					MAX(fluorescence) AS max_fluorescence,
					MIN(polish) AS min_polish,
					MAX(polish) AS max_polish,
					MIN(symmetry) AS min_symmetry,
					MAX(symmetry) AS max_symmetry,
					MIN(crown_angle) AS min_crown_angle,
					MAX(crown_angle) AS max_crown_angle,
					MIN(pavilion_angle) AS min_pavilion_angle,
					MAX(pavilion_angle) AS max_pavilion_angle,
					MIN(girdle_thickness) AS min_girdle,
					MAX(girdle_thickness) AS max_girdle,
					MIN(culet) AS min_culet,
					MAX(culet) AS max_culet
				FROM ls_inventory  where price!=0  AND status = 0
			");

			if ($result) {
				$min_price = $result->min_price;
				$max_price = $result->max_price;
				$min_carat = $result->min_carat;
				$max_carat = $result->max_carat;
				// Fall back to the previous hardcoded defaults whenever a
				// column is missing/empty (e.g. NULL across all rows),
				// so the UI never breaks even before this data is fully
				// populated.
				$min_length         =  1;
				$max_length         = 20;
				$min_width          = 2;
				$max_width          = 20;
				$min_height         = 0.5;
				$max_height         = 15;
				$min_crown_angle    = 23;
				$max_crown_angle    = 40;
				$min_pavilion_angle = 38;
				$max_pavilion_angle = 43;
				$min_fluorescence   = $result->min_fluorescence !== null ? (float) $result->min_fluorescence : 0;
				$max_fluorescence   = $result->max_fluorescence !== null ? (float) $result->max_fluorescence : 4;
				$min_polish         = $result->min_polish !== null ? (float) $result->min_polish : 0;
				$max_polish         = $result->max_polish !== null ? (float) $result->max_polish : 2;
				$min_symmetry       = $result->min_symmetry !== null ? (float) $result->min_symmetry : 0;
				$max_symmetry       = $result->max_symmetry !== null ? (float) $result->max_symmetry : 2;
				$min_girdle         = $result->min_girdle !== null ? (float) $result->min_girdle : 0;
				$max_girdle         = $result->max_girdle !== null ? (float) $result->max_girdle : 7;
				$min_culet          = $result->min_culet !== null ? (float) $result->min_culet : 0;
				$max_culet          = $result->max_culet !== null ? (float) $result->max_culet : 4;
			}
		}
		if (is_page_template('page-inventory2.php')) {
			global $wpdb;

			$result = $wpdb->get_row("
				SELECT
					MIN(price) AS min_price,
					MAX(price) AS max_price,
					MIN(carat) AS min_carat,
					MAX(carat) AS max_carat,
					MIN(length) AS min_length,
					MAX(length) AS max_length,
					MIN(width) AS min_width,
					MAX(width) AS max_width,
					MIN(height) AS min_height,
					MAX(height) AS max_height,
					MIN(fluorescence) AS min_fluorescence,
					MAX(fluorescence) AS max_fluorescence,
					MIN(polish) AS min_polish,
					MAX(polish) AS max_polish,
					MIN(symmetry) AS min_symmetry,
					MAX(symmetry) AS max_symmetry,
					MIN(crown_angle) AS min_crown_angle,
					MAX(crown_angle) AS max_crown_angle,
					MIN(pavilion_angle) AS min_pavilion_angle,
					MAX(pavilion_angle) AS max_pavilion_angle,
					MIN(girdle_thickness) AS min_girdle,
					MAX(girdle_thickness) AS max_girdle,
					MIN(culet) AS min_culet,
					MAX(culet) AS max_culet
				FROM ls_inventory_c where price!=0  AND status = 0
			");

			if ($result) {
				$min_price = $result->min_price;
				$max_price = $result->max_price;
				$min_carat = $result->min_carat;
				$max_carat = $result->max_carat;
				// Same fallback values as page-inventory.php.
				$min_length         =  1;
				$max_length         = 20;
				$min_width          = 2;
				$max_width          = 20;
				$min_height         = 0.5;
				$max_height         = 15;
				$min_crown_angle    = 23;
				$max_crown_angle    = 40;
				$min_pavilion_angle = 38;
				$max_pavilion_angle = 43;
				$min_fluorescence   = $result->min_fluorescence !== null ? (float) $result->min_fluorescence : 0;
				$max_fluorescence   = $result->max_fluorescence !== null ? (float) $result->max_fluorescence : 4;
				$min_polish         = $result->min_polish !== null ? (float) $result->min_polish : 0;
				$max_polish         = $result->max_polish !== null ? (float) $result->max_polish : 2;
				$min_symmetry       = $result->min_symmetry !== null ? (float) $result->min_symmetry : 0;
				$max_symmetry       = $result->max_symmetry !== null ? (float) $result->max_symmetry : 2;
				$min_girdle         = $result->min_girdle !== null ? (float) $result->min_girdle : 0;
				$max_girdle         = $result->max_girdle !== null ? (float) $result->max_girdle : 7;
				$min_culet          = $result->min_culet !== null ? (float) $result->min_culet : 0;
				$max_culet          = $result->max_culet !== null ? (float) $result->max_culet : 4;
			}
		}
		$shapes = [
		    ['id' => 1, 'value' => 'round'],
		    ['id' => 2, 'value' => 'princess'],
		    ['id' => 3, 'value' => 'cushion'],
		    ['id' => 4, 'value' => 'oval'],
		    ['id' => 5, 'value' => 'emerald'],
		    ['id' => 6, 'value' => 'pear'],
		    ['id' => 7, 'value' => 'asscher'],
		    ['id' => 8, 'value' => 'radiant'],
		    ['id' => 9, 'value' => 'marquise'],
		    ['id' => 10, 'value' => 'heart'],
		    ['id' => 11, 'value' => 'rose_cut'],
		    ['id' => 12, 'value' => 'old_european'],
		    ['id' => 13, 'value' => 'half_moon'],
		    ['id' => 14, 'value' => 'baguette_trapezoid'],
		    ['id' => 15, 'value' => 'baguette'],
		    ['id' => 16, 'value' => 'hexagon'],
		    ['id' => 17, 'value' => 'kite'],
		    ['id' => 18, 'value' => 'old_mine'],
		    ['id' => 19, 'value' => 'trapezoid'],
		    ['id' => 20, 'value' => 'triangular'],
		];
		if(is_page_template('page-inventory.php')){

		    $filters_arr = array(
		        'shape'            => isset($_GET['shape']) ? explode(',', $_GET['shape']) : array(),
		        // Named "Cut Style" sub-shape (e.g. "square_emerald"); more specific
		        // than 'shape' and takes over the shape filter on the API side when
		        // present (see api/routes.php).
		        'subshape'         => isset($_GET['subshape']) ? sanitize_text_field($_GET['subshape']) : '',
		        'carat_range'      => isset($_GET['carat'])
		                                ? array_map('floatval', explode(',', $_GET['carat']))
		                                : array((float)$min_carat, (float)$max_carat),
		        'price_range'      => isset($_GET['price'])
		                                ? array_map('floatval', explode(',', $_GET['price']))
		                                : array((float)$min_price, (float)$max_price),
		        'certificate'      => isset($_GET['certificate']) ? explode(',', $_GET['certificate']) : array(),
		        'sku'              => isset($_GET['sku']) ? $_GET['sku'] : '',
		        'method'           => isset($_GET['method']) ? explode(',', $_GET['method']) : array(),
		        'table_range'      => isset($_GET['table'])
		                                ? array_map('floatval', explode(',', $_GET['table']))
		                                : array(50, 80),
		        'depth_range'      => isset($_GET['depth'])
		                                ? array_map('floatval', explode(',', $_GET['depth']))
		                                : array(46.00, 78.00),
		        'lwratio_range'    => isset($_GET['lwratio'])
		                                ? array_map('floatval', explode(',', $_GET['lwratio']))
		                                : array(1.00, 2.75),
		        'cut_range'        => isset($_GET['cut'])
		                                ? array_map('floatval', explode(',', $_GET['cut']))
		                                : array(0.00, 4.00),
		        'color_range'      => isset($_GET['color'])
		                                ? array_map('floatval', explode(',', $_GET['color']))
		                                : array(0.00, 10.00),
		        'clarity_range'    => isset($_GET['clarity'])
		                                ? array_map('floatval', explode(',', $_GET['clarity']))
		                                : array(0, 11),
		        'certificate_type' => isset($_GET['certificate']) ? explode(',', $_GET['certificate']) : array(),
		        'shipping' => isset($_GET['shipping']) ? intval($_GET['shipping']) : 0,
		        'is_sale'     => isset($_GET['is_sale']) ? intval($_GET['is_sale']) : 0,
		        'is_media'     => isset($_GET['is_media']) ? intval($_GET['is_media']) : 0,
		        'heartarrow'     => isset($_GET['heartarrow']) ? intval($_GET['heartarrow']) : 0,

		        'length_range'          => isset($_GET['length'])
		                                ? array_map('floatval', explode(',', $_GET['length']))
		                                : array((float)$min_length, (float)$max_length),
		        'width_range'           => isset($_GET['width'])
		                                ? array_map('floatval', explode(',', $_GET['width']))
		                                : array((float)$min_width, (float)$max_width),
		        'height_range'          => isset($_GET['height'])
		                                ? array_map('floatval', explode(',', $_GET['height']))
		                                : array((float)$min_height, (float)$max_height),
		        'fluorescence_range'    => isset($_GET['fluorescence'])
		                                ? array_map('floatval', explode(',', $_GET['fluorescence']))
		                                : array((float)$min_fluorescence, (float)$max_fluorescence),
		        'polish_range'          => isset($_GET['polish'])
		                                ? array_map('floatval', explode(',', $_GET['polish']))
		                                : array((float)$min_polish, (float)$max_polish),
		        'symmetry_range'        => isset($_GET['symmetry'])
		                                ? array_map('floatval', explode(',', $_GET['symmetry']))
		                                : array((float)$min_symmetry, (float)$max_symmetry),
		        'crown_angle_range'     => isset($_GET['crown_angle'])
		                                ? array_map('floatval', explode(',', $_GET['crown_angle']))
		                                : array((float)$min_crown_angle, (float)$max_crown_angle),
		        'pavilion_angle_range'  => isset($_GET['pavilion_angle'])
		                                ? array_map('floatval', explode(',', $_GET['pavilion_angle']))
		                                : array((float)$min_pavilion_angle, (float)$max_pavilion_angle),
		        'girdle_range'          => isset($_GET['girdle'])
		                                ? array_map('floatval', explode(',', $_GET['girdle']))
		                                : array((float)$min_girdle, (float)$max_girdle),
		        'culet_range'           => isset($_GET['culet'])
		                                ? array_map('floatval', explode(',', $_GET['culet']))
		                                : array((float)$min_culet, (float)$max_culet),

		        'ls_sortby'        => isset($_GET['ls_sortby']) ? $_GET['ls_sortby'] : '',
		        'ls_sort'          => isset($_GET['ls_sort']) ? $_GET['ls_sort'] : '',
		    );

		    $filters_default = $filters_blank = array(
		        'shape'            => array(),
		        'subshape'         => '',
		        'carat_range'      => array((float)$min_carat, (float)$max_carat),
		        'price_range'      => array((float)$min_price, (float)$max_price),
		        'certificate'      => array(),
		        'sku'              => '',
		        'method'           => array(),
		        'table_range'      => array(50, 80),
		        'depth_range'      => array(46.00, 78.00),
		        'lwratio_range'    => array(1.00, 2.75),
		        'cut_range'        => array(0.00, 4.00),
		        'color_range'      => array(0, 10),
		        'clarity_range'    => array(0, 11),
		        'certificate_type' => array(),
		        'shipping'  => 0,
		        'is_sale'     => 0,
		        'is_media'     => 0,
		        'heartarrow'     => 0,
		        'length_range'          => array((float)$min_length, (float)$max_length),
		        'width_range'           => array((float)$min_width, (float)$max_width),
		        'height_range'          => array((float)$min_height, (float)$max_height),
		        'fluorescence_range'    => array((float)$min_fluorescence, (float)$max_fluorescence),
		        'polish_range'          => array((float)$min_polish, (float)$max_polish),
		        'symmetry_range'        => array((float)$min_symmetry, (float)$max_symmetry),
		        'crown_angle_range'     => array((float)$min_crown_angle, (float)$max_crown_angle),
		        'pavilion_angle_range'  => array((float)$min_pavilion_angle, (float)$max_pavilion_angle),
		        'girdle_range'          => array((float)$min_girdle, (float)$max_girdle),
		        'culet_range'           => array((float)$min_culet, (float)$max_culet),
		        'ls_sortby'        => '',
		        'ls_sort'          => '',
		    );
		    // Get type from URL
			$type = isset($_GET['type']) ? $_GET['type'] : '';

			//if ($type == 'ringbuilder') {
				if($_SESSION['ls_ring_builder']['setting']=='' && $_SESSION['ls_ring_builder']['diamond']==''){
					$_GET['type']='';
				}else{
					$_GET['type']='ringbuilder';
					$ringdata= ls_process_setting_data($_SESSION['ls_ring_builder']['setting']);

					if($ringdata['shape']==''){
						// No setting chosen yet — just browsing/changing the diamond.
						// $_SESSION['ls_ring_builder']['diamond']['d_shape'] has already
						// been resolved down to the *base* shape id (see
						// ls_resolve_dp_shape_id() in ls-addtocart-api.php), so a diamond
						// saved under a named cut style (e.g. its `shape` column is
						// "square_emerald") would otherwise only filter by "emerald" and
						// vanish from its own list. Look the diamond back up by pid to
						// recover its real shape value and, when it's a named sub-shape,
						// filter by that exact value instead so it (and its siblings)
						// still show up.
						global $inv_subshape_to_base;
						$rb_diamond_pid   = isset($_SESSION['ls_ring_builder']['diamond']['d_pid']) ? absint($_SESSION['ls_ring_builder']['diamond']['d_pid']) : 0;
						$rb_raw_shape     = $rb_diamond_pid ? $wpdb->get_var($wpdb->prepare("SELECT shape FROM ls_inventory WHERE pid = %d", $rb_diamond_pid)) : '';
						$rb_raw_shape_key = function_exists('ls_normalize_shape_key') ? ls_normalize_shape_key($rb_raw_shape) : strtolower(trim((string) $rb_raw_shape));

						if (!empty($rb_raw_shape) && function_exists('ls_is_ring_group_cut') && ls_is_ring_group_cut($rb_raw_shape_key)) {
							// Named top-level cut (Portuguese, Carre, Flanders, Moval...) ->
							// select its whole group, e.g. round + portuguese.
							$filters_default['shape'] = $filters_blank['shape'] = $filters_arr['shape'] = ls_get_ring_shape_group($rb_raw_shape_key);
						} elseif (!empty($rb_raw_shape) && !empty($inv_subshape_to_base) && isset($inv_subshape_to_base[$rb_raw_shape_key])) {
							$rb_base_value = strtolower(str_replace(' ', '_', $inv_subshape_to_base[$rb_raw_shape_key]));
							$filters_default['shape'] = $filters_blank['shape'] = $filters_arr['shape'] = array($rb_base_value);
							$filters_default['subshape'] = $filters_blank['subshape'] = $filters_arr['subshape'] = $rb_raw_shape_key;
						} else {
							$rb_shape_val = getShapeValueById($_SESSION['ls_ring_builder']['diamond']['d_shape'], $shapes);
							$filters_default['shape'] = $filters_blank['shape'] = function_exists('ls_get_ring_shape_group') ? ls_get_ring_shape_group($rb_shape_val) : array($rb_shape_val);
						}
					}else{
						// Setting chosen: Round setting -> round + portuguese, etc.
						$rb_shape_val = getShapeValueById($ringdata['shape'], $shapes);
						$filters_default['shape'] = $filters_blank['shape'] = function_exists('ls_get_ring_shape_group') ? ls_get_ring_shape_group($rb_shape_val) : array($rb_shape_val);
					}
				}
			//}

			$filter_url = get_field('filter_url');

			if ( ! empty( $filter_url ) && empty( $_GET['sku'] ) ) { // SKU search resets filter_url presets

			    parse_str( $filter_url, $filter_url_array );

			    // Handle shape
			    if ( ! empty( $filter_url_array['shape'] ) ) {
			        $filters_arr['shape'] = explode( ',', $filter_url_array['shape'] );
			    }

			    // Handle subshape (named "Cut Style", e.g. "square_emerald").
			    // More specific than 'shape' — the API uses it in place of the
			    // shape filter when present.
			    if ( ! empty( $filter_url_array['subshape'] ) ) {
			        $subshape_key = strtolower( trim( sanitize_text_field( $filter_url_array['subshape'] ) ) );
			        $filters_arr['subshape'] = $subshape_key;

			        // No shape given? Derive the base shape so the UI's shape
			        // selector stays in sync with the chosen cut style.
			        if ( empty( $filter_url_array['shape'] ) ) {
			            global $inv_subshape_to_base;
			            if ( ! empty( $inv_subshape_to_base ) && isset( $inv_subshape_to_base[ $subshape_key ] ) ) {
			                $filters_arr['shape'] = array(
			                    strtolower( str_replace( ' ', '_', $inv_subshape_to_base[ $subshape_key ] ) ),
			                );
			            }
			        }
			    }

			    if ( ! empty( $filter_url_array['carat'] ) ) {
				    $filters_arr['carat_range'] = array_map( 'floatval', explode( ',', $filter_url_array['carat'] ) );
				}
				if ( ! empty( $filter_url_array['clarity'] ) ) {
				    $filters_arr['clarity_range'] = array_map( 'floatval', explode( ',', $filter_url_array['clarity'] ) );
				}
				if ( ! empty( $filter_url_array['color'] ) ) {
				    $filters_arr['color_range'] = array_map( 'floatval', explode( ',', $filter_url_array['color'] ) );
				}
				if ( ! empty( $filter_url_array['cut'] ) ) {
				    $filters_arr['cut_range'] = array_map( 'floatval', explode( ',', $filter_url_array['cut'] ) );
				}
				if ( ! empty( $filter_url_array['certificate'] ) ) {
				    $filters_arr['certificate_type'] = explode( ',', $filter_url_array['certificate'] );
				}
				if ( ! empty( $filter_url_array['heartarrow'] ) ) {
				    $filters_arr['heartarrow'] = intval( $filter_url_array['heartarrow'] );
				}
			}
		}

		if(is_page_template('page-inventory2.php')){
		    $filters_arr = array(
		        'color'            => isset($_GET['color']) ? $_GET['color'] : '',
		        'shape'            => isset($_GET['shape']) ? explode(',', $_GET['shape']) : array(),
		        'intensity'        => isset($_GET['intensity'])
		                                ? array_map('intval', explode(',', $_GET['intensity']))
		                                : array(),
		        'carat_range'      => isset($_GET['carat'])
		                                ? array_map('floatval', explode(',', $_GET['carat']))
		                                : array((float)$min_carat, (float)$max_carat),
		        'price_range'      => isset($_GET['price'])
		                                ? array_map('floatval', explode(',', $_GET['price']))
		                                : array((float)$min_price, (float)$max_price),
		        'certificate'      => isset($_GET['certificate']) ? explode(',', $_GET['certificate']) : array(),
		        'sku'              => isset($_GET['sku']) ? $_GET['sku'] : '',
		        'method'           => isset($_GET['method']) ? explode(',', $_GET['method']) : array(),
		        'table_range'      => isset($_GET['table'])
		                                ? array_map('floatval', explode(',', $_GET['table']))
		                                : array(50, 80),
		        'depth_range'      => isset($_GET['depth'])
		                                ? array_map('floatval', explode(',', $_GET['depth']))
		                                : array(46.00, 78.00),
		        'lwratio_range'    => isset($_GET['lwratio'])
		                                ? array_map('floatval', explode(',', $_GET['lwratio']))
		                                : array(1.00, 2.75),
		        'cut_range'        => isset($_GET['cut'])
		                                ? array_map('floatval', explode(',', $_GET['cut']))
		                                : array(0.00, 4.00),
		        'color_range'      => isset($_GET['color'])
		                                ? array_map('floatval', explode(',', $_GET['color']))
		                                : array(0.00, 10.00),
		        'clarity_range'    => isset($_GET['clarity'])
		                                ? array_map('floatval', explode(',', $_GET['clarity']))
		                                : array(0, 11),
		        'certificate_type' => isset($_GET['certificate']) ? explode(',', $_GET['certificate']) : array(),
		        'shipping' => isset($_GET['shipping']) ? intval($_GET['shipping']) : 0,
		        'is_sale'     => isset($_GET['is_sale']) ? intval($_GET['is_sale']) : 0,
		        'is_media'     => isset($_GET['is_media']) ? intval($_GET['is_media']) : 0,
		        'length_range'          => isset($_GET['length'])
		                                ? array_map('floatval', explode(',', $_GET['length']))
		                                : array((float)$min_length, (float)$max_length),
		        'width_range'           => isset($_GET['width'])
		                                ? array_map('floatval', explode(',', $_GET['width']))
		                                : array((float)$min_width, (float)$max_width),
		        'height_range'          => isset($_GET['height'])
		                                ? array_map('floatval', explode(',', $_GET['height']))
		                                : array((float)$min_height, (float)$max_height),
		        'fluorescence_range'    => isset($_GET['fluorescence'])
		                                ? array_map('floatval', explode(',', $_GET['fluorescence']))
		                                : array((float)$min_fluorescence, (float)$max_fluorescence),
		        'polish_range'          => isset($_GET['polish'])
		                                ? array_map('floatval', explode(',', $_GET['polish']))
		                                : array((float)$min_polish, (float)$max_polish),
		        'symmetry_range'        => isset($_GET['symmetry'])
		                                ? array_map('floatval', explode(',', $_GET['symmetry']))
		                                : array((float)$min_symmetry, (float)$max_symmetry),
		        'crown_angle_range'     => isset($_GET['crown_angle'])
		                                ? array_map('floatval', explode(',', $_GET['crown_angle']))
		                                : array((float)$min_crown_angle, (float)$max_crown_angle),
		        'pavilion_angle_range'  => isset($_GET['pavilion_angle'])
		                                ? array_map('floatval', explode(',', $_GET['pavilion_angle']))
		                                : array((float)$min_pavilion_angle, (float)$max_pavilion_angle),
		        'girdle_range'          => isset($_GET['girdle'])
		                                ? array_map('floatval', explode(',', $_GET['girdle']))
		                                : array((float)$min_girdle, (float)$max_girdle),
		        'culet_range'           => isset($_GET['culet'])
		                                ? array_map('floatval', explode(',', $_GET['culet']))
		                                : array((float)$min_culet, (float)$max_culet),
		        'ls_sortby'        => isset($_GET['ls_sortby']) ? $_GET['ls_sortby'] : '',
		        'ls_sort'          => isset($_GET['ls_sort']) ? $_GET['ls_sort'] : '',
		    );

		    $filters_default = $filters_blank = array(
		        'color'            => '',
		        'shape'            => array(),
		        'intensity'        => array(),
		        'carat_range'      => array((float)$min_carat, (float)$max_carat),
		        'price_range'      => array((float)$min_price, (float)$max_price),
		        'certificate'      => array(),
		        'sku'              => '',
		        'method'           => array(),
		        'table_range'      => array(50, 80),
		        'depth_range'      => array(46.00, 78.00),
		        'lwratio_range'    => array(1.00, 2.75),
		        'cut_range'        => array(0.00, 4.00),
		        'clarity_range'    => array(0, 11),
		        'certificate_type' => array(),
		        'shipping'  => 0,
		        'is_sale'     => 0,
		        'is_media'     => 0,
		        'length_range'          => array((float)$min_length, (float)$max_length),
		        'width_range'           => array((float)$min_width, (float)$max_width),
		        'height_range'          => array((float)$min_height, (float)$max_height),
		        'fluorescence_range'    => array((float)$min_fluorescence, (float)$max_fluorescence),
		        'polish_range'          => array((float)$min_polish, (float)$max_polish),
		        'symmetry_range'        => array((float)$min_symmetry, (float)$max_symmetry),
		        'crown_angle_range'     => array((float)$min_crown_angle, (float)$max_crown_angle),
		        'pavilion_angle_range'  => array((float)$min_pavilion_angle, (float)$max_pavilion_angle),
		        'girdle_range'          => array((float)$min_girdle, (float)$max_girdle),
		        'culet_range'           => array((float)$min_culet, (float)$max_culet),
		        'ls_sortby'        => '',
		        'ls_sort'          => '',
		    );
		    // Get type from URL
			$type = isset($_GET['type']) ? $_GET['type'] : '';

			if ($type == 'ringbuilder_c') {
				$ringdata= ls_process_setting_data($_SESSION['ls_ring_builder']['setting']);
			    $filters_default['shape'] = $filters_blank['shape'] = array(getShapeValueById($ringdata['shape'], $shapes)  );
			}

			$filter_url = get_field('filter_url');

			if ( ! empty( $filter_url ) && empty( $_GET['sku'] ) ) { // SKU search resets filter_url presets

			    parse_str( $filter_url, $filter_url_array );

			    // Handle shape
			    if ( ! empty( $filter_url_array['shape'] ) ) {
			        $filters_arr['shape'] = explode( ',', $filter_url_array['shape'] );
			    }
			    if ( ! empty( $filter_url_array['carat'] ) ) {
				    $filters_arr['carat_range'] = array_map( 'floatval', explode( ',', $filter_url_array['carat'] ) );
				}
				if ( ! empty( $filter_url_array['clarity'] ) ) {
				    $filters_arr['clarity_range'] = array_map( 'floatval', explode( ',', $filter_url_array['clarity'] ) );
				}
				if ( ! empty( $filter_url_array['color'] ) ) {
				    $filters_arr['color'] =  $filter_url_array['color'] ;
				}
				if ( ! empty( $filter_url_array['cut'] ) ) {
				    $filters_arr['cut_range'] = array_map( 'floatval', explode( ',', $filter_url_array['cut'] ) );
				}
				if ( ! empty( $filter_url_array['certificate'] ) ) {
				    $filters_arr['certificate_type'] = explode( ',', $filter_url_array['certificate'] );
				}
				if ( ! empty( $filter_url_array['heartarrow'] ) ) {
				    $filters_arr['heartarrow'] = intval( $filter_url_array['heartarrow'] );
				}
			}
		}

		if(get_query_var('diamond_sku')){
			$_GET['type'] = 'ringbuilder';
		}

		$argumentpass = array(
		    'templateUrl'     => get_template_directory_uri(),
		    'ajaxUrl'         => admin_url('admin-ajax.php'),
		    'API_BASE_URL'    => site_url() . '/wp-json/ls/v1',
		    'SITE_URL'        => site_url(),
		    'filters'         => $filters_arr,
		    'filtersDefault'  => $filters_default,    // <-- separate default values array
		    'filters_blank' => $filters_blank,
		    'cartProductIds'  => $cart_product_ids,
		    'cartRingProductIds' => $cart_ring_product_ids,
		    'inventoryRange'  => array(
		        'min_price' => (int)$min_price,
		        'max_price' => (int)$max_price,
		        'min_carat' => (float)$min_carat,
		        'max_carat' => (float)$max_carat,
		    ),
		);
		if(get_field('filter_url')!=''){
			$argumentpass['filter_url']='yes';
		}else{
			$argumentpass['filter_url']='no';
		}

		// Expose theme URLs and AJAX endpoint to the React app
		wp_localize_script(
			'loose-diomond-inventory',
			'LD_THEME',
			$argumentpass
		);
	}

	// App3 (product-grid) enqueue used to live here — it now lives in
	// its own standalone module: components/inventory-listing/index.php
	// (hooked to wp_enqueue_scripts there, same conditions as before:
	// is_product_category() || search || page-lab-grown-engagement-rings.php
	// || is_product()). See that file for the ported logic.
}
add_action('wp_enqueue_scripts', 'loose_diomond_scripts_react');