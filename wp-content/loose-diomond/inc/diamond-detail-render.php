<?php
/**
 * Diamond Detail — shared page renderer
 *
 * The actual diamond-detail page markup used to live only in
 * diamond-detail.php (served for /diamond/{sku}/ via the diamond_sku
 * rewrite in inc/diamond-details-function.php). It's now factored out
 * into ls_render_diamond_detail_page() below so a second caller can
 * reuse the exact same page structure:
 *
 *   - diamond-detail.php  → fetches the row by SKU (from the URL),
 *                            then calls this function.
 *   - woocommerce/single-product.php → for a product tagged "sale",
 *                            looks the row up by the product's SKU,
 *                            then calls this same function instead of
 *                            rendering the normal WooCommerce product
 *                            template.
 *
 * Both callers just need a `$diamond` row from ls_inventory /
 * ls_inventory_c (with ->is_colored already set — see
 * _diamond_lookup_by_sku() below) and pass it straight through.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}


/**
 * Build the ordered (worst → best) set of Cut icons, flagging which one
 * matches the current diamond so it can render full-opacity + circled.
 */
function ls_get_cut_visual_set( $cut_label ) {
    $current = strtolower( trim( $cut_label ) );

    $order = array(
        array( 'key' => 'good',      'label' => 'Good',      'file' => 'good.svg' ),
        array( 'key' => 'very good', 'label' => 'Very Good', 'file' => 'very_good.svg' ),
        array( 'key' => 'excellent', 'label' => 'Excellent', 'file' => 'excellent.svg' ), // fixed: was 'excelent.svg' typo
        array( 'key' => 'ideal',     'label' => 'Ideal',     'file' => 'ideal.svg' ),
    );

    foreach ( $order as &$item ) {
        $item['active'] = ( $item['key'] === $current );
    }
    unset( $item );

    return $order;
}

/**
 * Build the ordered (worst → best) set of Color range icons.
 * D-F = colorless (best), G-J = near colorless, K-Z = faint color (worst).
 */
function ls_get_color_visual_set( $color_label ) {
    $current = strtoupper( trim( $color_label ) );

    $order = array(
        array( 'label' => 'K-Z', 'file' => 'K-Z.svg', 'grades' => str_split( 'KLMNOPQRSTUVWXYZ' ) ),
        array( 'label' => 'G-J', 'file' => 'G-J.svg', 'grades' => array( 'G', 'H', 'I', 'J' ) ),
        array( 'label' => 'D-F', 'file' => 'D-F.svg', 'grades' => array( 'D', 'E', 'F' ) ),
    );

    $matched = false;
    foreach ( $order as &$item ) {
        $item['active'] = in_array( $current, $item['grades'], true );
        if ( $item['active'] ) {
            $matched = true;
        }
    }
    unset( $item );

    // Fancy/unrecognized grade — fall back to the K-Z (first/worst) bucket
    // rather than showing nothing highlighted.
    if ( ! $matched ) {
        $order[0]['active'] = true;
    }

    return $order;
}

/**
 * Build the ordered (worst → best) set of Clarity range icons.
 */
function ls_get_clarity_visual_set( $clarity_label ) {
    $current = strtoupper( trim( $clarity_label ) );

    $order = array(
        array( 'label' => 'SI2-SI1',   'file' => 'SI2-SI1.svg',   'grades' => array( 'SI1', 'SI2', 'I1', 'I2', 'I3' ) ),
        array( 'label' => 'VS2-VS1',   'file' => 'VS2-VS1.svg',   'grades' => array( 'VS1', 'VS2' ) ),
        array( 'label' => 'VVS2-VVS1', 'file' => 'VVS2-VVS1.svg', 'grades' => array( 'VVS1', 'VVS2' ) ),
        array( 'label' => 'IF-FL',     'file' => 'IF-FL.svg',     'grades' => array( 'FL', 'IF' ) ),
    );

    $matched = false;
    foreach ( $order as &$item ) {
        $item['active'] = in_array( $current, $item['grades'], true );
        if ( $item['active'] ) {
            $matched = true;
        }
    }
    unset( $item );

    // Anything below SI1/SI2 (I1-I3) is already bucketed into SI2-SI1 above,
    // so this fallback only guards against a totally unrecognized label.
    if ( ! $matched ) {
        $order[0]['active'] = true;
    }

    return $order;
}

/**
 * Sets up the per-diamond nocache headers + the $GLOBALS['diamond_detail']
 * value that the wp_head hook (inc/diamond-details-function.php) reads to
 * output the canonical/OG/schema SEO tags. MUST run before get_header() —
 * wp_head() fires during get_header(), so this has to happen first
 * regardless of who calls get_header() (the render function itself, or a
 * caller doing it separately — see $render_header_footer below).
 */
function ls_prepare_diamond_detail_head( $diamond, $is_colored ) {
    nocache_headers();
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

    $diamond->is_colored = $is_colored;
    $GLOBALS['diamond_detail'] = $diamond;
}

/**
 * Render the full diamond detail page — image gallery, spec table,
 * pricing/add-to-cart, related content, etc. Used both for the
 * dedicated /diamond/{sku}/ page and (for products tagged "sale") in
 * place of the normal WooCommerce single-product template.
 *
 * @param object $diamond Row from ls_inventory or ls_inventory_c, with
 *                         ->is_colored set (see _diamond_lookup_by_sku()).
 * @param bool   $is_colored Whether $diamond came from ls_inventory_c
 *                            (fancy color) rather than ls_inventory (white).
 * @param bool   $render_header_footer Default true: the function calls
 *                            get_header('shop')/get_footer('shop') itself
 *                            (diamond-detail.php's usage). Pass false when
 *                            the caller wants to load header/footer itself
 *                            — woocommerce/single-product.php does this,
 *                            calling ls_prepare_diamond_detail_head() +
 *                            get_header('shop') before this function and
 *                            get_footer('shop') after, so header/footer
 *                            visibly load from single-product.php's own
 *                            code instead of from inside this function.
 * @param bool   $is_sale_page Default false. Pass true (used by the
 *                            "sale" tag branch in single-product.php) to
 *                            swap the normal button set (Add To Ring /
 *                            Ask About a Custom Setting / Add To Bag) for
 *                            just Add To Cart + a Similar Diamonds button
 *                            linking to /lab-diamonds/?is_sale=1.
 */
function ls_render_diamond_detail_page( $diamond, $is_colored, $render_header_footer = true, $is_sale_page = false ) {
    global $oc_cut, $oc_color, $oc_clarity, $oc_certificate, $inv_color;
    global $product, $oc_common, $inv_shape_imgids, $inv_intensity_c;
    global $wpdb;

// ── Helpers ───────────────────────────────────────────────────────
$cut      = $oc_cut[$diamond->cut]                     ?? $diamond->cut;
// Colored diamonds (ls_inventory_c) don't grade on the D-Z scale —
// use intensity + fancy color name instead of the $oc_color lookup.
if ($is_colored) {
    $color = ucfirst($oc_color[$diamond->color] ?? $diamond->color);
    $intensity=$diamond->intensity;
    $intensity_label = ucfirst(array_search($diamond->intensity, $inv_intensity_c));
} else {
    $color = ucfirst(array_search($diamond->color, $inv_color));
     $intensity_label='';
    $intensity = 0;
}
$clarity  = $oc_clarity[$diamond->clarity]             ?? $diamond->clarity;
$cert     = $oc_certificate[$diamond->certificate_type] ?? 'Unknown';
$in_stock = ($diamond->status == 0);
$shape    = $diamond->shape_title ?? $diamond->shape;
$shape_img = $shape_title = $diamond->shape;
$shape_img = str_replace("_", " ", $shape_img);

$thumbnail_id = $inv_shape_imgids[strtolower($shape_img)] ?? null;
$thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
$thumbnail_url='';
// ── Shape image mapping (pick an icon based on shape value) ───────
$shape_key = strtolower(str_replace(' ', '_', ($diamond->shape ?? $diamond->shape_title ?? '')) ) ;

// ── Ring Preview / Carat Size Guide image (used by .dcsg-container below) ──
// Same static per-shape image the size guide always used
// (aj_custom/diamond/ring-preview/{shape_key}.png), just exposed as an
// array with a 'url' key so the markup can read
// $image_diamond_carat_size_guide['url'] — swap this in for an ACF/
// attachment lookup later without touching the markup again.
$image_diamond_carat_size_guide = array(
    'url' => get_template_directory_uri() . '/aj_custom/diamond/ring-preview/' . $shape_key . '.png',
);

// Colored diamonds (ls_inventory_c) aren't in the white-diamond media
// library ($inv_shape_imgids is shape-only, no color variant), so use
// the shape+color image path instead — same pattern the React grid
// cards already use (assets/image/ls-inventory-color/diamond/{shape}/{color}.png).
if ($is_colored) {
    $color_slug = strtolower(trim($color));
    if ($color_slug !== '') {
        $thumbnail_url = get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/' . $shape_key . '/' . $color_slug . '.png';
    }
}
global $shape_png_white;
$shape_images = $shape_png_white;

$shape_image = isset($shape_images[$shape_key]) ? $shape_images[$shape_key] : (get_template_directory_uri() . '/aj_custom/diamond/dimensions-top-value.png');
if($thumbnail_url==''){
    $thumbnail_url = $shape_image;
}

$shape_images_demensions_wh = array(
    'round'               => get_template_directory_uri() . '/aj_custom/diamond/demensions/round-wh.svg',
    'princess'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/princess-wh.svg',

    'cushion'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',
    'cushion_brilliant' => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',
    'cushion_modified' => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',
    'antique_cushion' => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',
    'old_mine_cushion' => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',
    'square_cushion' => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg',

    'oval'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/oval-wh.svg',
    'oval_step' => get_template_directory_uri() . '/aj_custom/diamond/demensions/oval-wh.svg',

    'emerald'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/emerald-wh.svg',
    'square_emerald' => get_template_directory_uri() . '/aj_custom/diamond/demensions/emerald-wh.svg',

    'pear'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/pear-wh.svg',
    'pear_step' => get_template_directory_uri() . '/aj_custom/diamond/demensions/pear-wh.svg',

    'asscher'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/asscher-wh.svg',

    'radiant'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant-wh.svg',
    'rectangular_radiant' => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant-wh.svg',
    'radiant_modified' => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant-wh.svg',
    'square_radiant' => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant-wh.svg',
    
    'marquise'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/marquise-wh.svg',
    'dutch_marquise' => get_template_directory_uri() . '/aj_custom/diamond/demensions/marquise-wh.svg',

    'heart'               => get_template_directory_uri() . '/aj_custom/diamond/demensions/heart-wh.svg',
    'rose_cut'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/rose_cut-wh.svg',

    'old_european'        => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_european-wh.svg',
    'eurocut' => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_european-wh.svg',

    'half_moon'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/half_moon-wh.svg',
    'baguette_trapezoid'  => get_template_directory_uri() . '/aj_custom/diamond/demensions/baguette_trapezoid-wh.svg',
    'baguette'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/baguette-wh.svg',
    'hexagon'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/hexagon-wh.svg',
    'kite'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/kite-wh.svg',
    'old_mine'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_mine-wh.svg',

    'trapezoid'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid-wh.svg',
    'trapez_step' => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid-wh.svg',
    'trapez_brilliant' => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid-wh.svg',
    
    'triangular'          => get_template_directory_uri() . '/aj_custom/diamond/demensions/triangular-wh.svg',
);
$shape_l_wh = isset($shape_images_demensions_wh[$shape_key]) ? $shape_images_demensions_wh[$shape_key] : (get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg');
$shape_r_wh = isset($shape_images_demensions_wh[$shape_key]) ? $shape_images_demensions_wh[$shape_key] : (get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion-wh.svg');
$shape_images_demensions_all = array(
    'round'               => get_template_directory_uri() . '/aj_custom/diamond/demensions/round.png',
    'princess'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/princess.png',

    'cushion'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',
    'cushion_brilliant'   => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',
    'cushion_modified'    => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',
    'antique_cushion'     => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',
    'old_mine_cushion'    => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',
    'square_cushion'      => get_template_directory_uri() . '/aj_custom/diamond/demensions/cushion.png',

    'oval'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/oval.png',
    'oval_step'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/oval.png',

    'emerald'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/emerald.png',
    'square_emerald'      => get_template_directory_uri() . '/aj_custom/diamond/demensions/emerald.png',

    'pear'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/pear.png',
    'pear_step'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/pear.png',

    'asscher'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/asscher.png',

    'radiant'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant.png',
    'rectangular_radiant' => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant.png',
    'radiant_modified'    => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant.png',
    'square_radiant'      => get_template_directory_uri() . '/aj_custom/diamond/demensions/radiant.png',

    'marquise'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/marquise.png',
    'dutch_marquise'      => get_template_directory_uri() . '/aj_custom/diamond/demensions/marquise.png',

    'heart'               => get_template_directory_uri() . '/aj_custom/diamond/demensions/heart.png',
    'rose_cut'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/rose_cut.png',

    'old_european'        => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_european.png',
    'eurocut'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_european.png',

    'half_moon'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/half_moon.png',
    'baguette_trapezoid'  => get_template_directory_uri() . '/aj_custom/diamond/demensions/baguette_trapezoid.png',
    'baguette'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/baguette.png',
    'hexagon'             => get_template_directory_uri() . '/aj_custom/diamond/demensions/hexagon.png',
    'kite'                => get_template_directory_uri() . '/aj_custom/diamond/demensions/kite.png',
    'old_mine'            => get_template_directory_uri() . '/aj_custom/diamond/demensions/old_mine.png',

    'trapezoid'           => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid.png',
    'trapez_step'         => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid.png',
    'trapez_brilliant'    => get_template_directory_uri() . '/aj_custom/diamond/demensions/trapezoid.png',

    'triangular'          => get_template_directory_uri() . '/aj_custom/diamond/demensions/triangular.png',
);
$shape_l_all = isset($shape_images_demensions_all[$shape_key]) ? $shape_images_demensions_all[$shape_key] : (get_template_directory_uri() . '/aj_custom/diamond/demensions/round.png');
$shape_r_all = isset($shape_images_demensions_all[$shape_key]) ? $shape_images_demensions_all[$shape_key] : (get_template_directory_uri() . '/aj_custom/diamond/demensions/round.png');
$first_10_shapes = [
  'round',
  'princess',
  'cushion',
  'oval',
  'emerald',
  'pear',
  'asscher',
  'radiant',
  'marquise',
  'heart',
  'cushion_brilliant',
  'cushion_modified',
  'antique_cushion',
  'old_mine_cushion',
  'square_cushion',
  'oval_step',
  'square_emerald',
  'pear_step',
  'rectangular_radiant',
  'radiant_modified',
  'square_radiant',
  'dutch_marquise'
];

// Named fancy cuts (Portuguese, Carre, Flanders, Ashoka, Capsule, Moval,
// Crisscut, ...) resolve to a standard base shape -> treat as setting-eligible.
$ls_base_shape_key = function_exists('ls_get_base_shape_name')
    ? str_replace(' ', '_', strtolower(ls_get_base_shape_name($shape_key)))
    : '';
if ($ls_base_shape_key !== '' && in_array($ls_base_shape_key, $first_10_shapes, true)
    && !in_array($shape_key, $first_10_shapes, true)) {
    $first_10_shapes[] = $shape_key;
}

if (in_array($shape_key, $first_10_shapes, true)) {
    $is_setting=true;
}else{
    $is_setting=false;
}

// Shapes that aren't offered as standard ring settings — these get the
// "Ask About a Custom Setting" prompt instead. Kept as its own explicit
// list (rather than just "not in $first_10_shapes") so it can be edited
// independently if the two lists ever need to diverge.

$exclude_side_preview  = [
    'round',
    'princess',
    'cushion',
    'oval',
    'emerald',
    'pear',
    'asscher',
    'radiant',
    'marquise',
    'heart',
];

if (in_array($shape_key, $exclude_side_preview, true)) {
    $is_preview=true;
}else{
    $is_preview=false;
}
$is_custom_setting_shape = !in_array($shape_key, $first_10_shapes, true);

if ( (int) $diamond->certificate_type === 4 ) {
    $is_setting              = false;
    $is_custom_setting_shape = false;
}

// ── Nocache headers ───────────────────────────────────────────────
// ── Share with wp_head hooks — zero extra queries ─────────────────
// Both done via ls_prepare_diamond_detail_head() — either here (default
// usage) or already done by the caller before get_header() (see the
// $render_header_footer doc note above).
if ( $render_header_footer ) {
    ls_prepare_diamond_detail_head( $diamond, $is_colored );
    get_header('shop');
}

$inventory_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'default';
$cert_info = ls_parse_certificate($diamond->certificate_type ?? '', $diamond->certificate ?? '');

$size_mm = $diamond->mm ?? '';

$parts = explode(' x ', str_replace(' MM', '', $size_mm));
$lw = explode(' - ', $parts[0]);

$size_mm_l = $diamond->length ?? '';
$size_mm_w = $diamond->width ?? '';
$size_mm_h = $diamond->height ?? '';

$size_mm = ($diamond->length ?? '') . ' x ' . ($diamond->width ?? '') . ' x ' . ($diamond->height ?? '');
/*echo "<pre>";
print_r($diamond);
echo "</pre>";
exit;*/
$multiplier = 0.7;

$price = (int) round($diamond->price );
$org_price = (int) round($price / $multiplier);

$is_in_cart = false;
$is_in_cart_ring = false;
if ( function_exists('WC') && WC()->cart && ! empty( $diamond->pid ) ) {
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        if ( $cart_item['product_id'] == $diamond->pid && function_exists('ls_is_diamond_in_cart_ring') && ls_is_diamond_in_cart_ring( $diamond->pid ) ) {
           
            $is_in_cart_ring = true;
            
        }
        if ( $cart_item['product_id'] == $diamond->pid ) {
            /*echo "<pre>";
            print_r($cart_item);
            echo "</pre>";
            exit;*/
            $is_in_cart = true;
            break;
        }
       
    }
}

$delivery_days = $diamond->delivery_days;

// Estimated delivery date — delivery_days from today (site timezone), falls back to 7 days if unset.
$delivery_days_offset = ( isset( $diamond->delivery_days ) && (int) $diamond->delivery_days > 0 ) ? (int) $diamond->delivery_days : 7;

// If a ring setting was already chosen in the ring builder (setting chosen
// first, diamond chosen second), the final delivery date has to account
// for both lead times together — the setting has to be made and the
// diamond set into it, so the two add up rather than run in parallel.
// The setting's lead time depends on its selected metal (Platinum ships
// later than Gold, and lead time also depends on the setting's Style —
// see ls_style_metal_delivery_days()).
if ( ! empty( $_SESSION['ls_ring_builder']['setting']['sp_vid'] ) ) {
    $rb_setting_delivery_days = ls_get_setting_delivery_days_by_variation( $_SESSION['ls_ring_builder']['setting']['sp_vid'] );
    $delivery_days_offset     = $delivery_days_offset + $rb_setting_delivery_days;
}

$estimated_delivery_date = ls_est_delivery_date( $delivery_days_offset );

$raw_video_field = trim( $diamond->video ?? '' );

$video_type = ''; // 'loupe', 'medialink', 'other', or '' (empty)
$video_url  = '';

if ( $raw_video_field !== '' ) {
    $video_url = $raw_video_field;

    if ( stripos( $raw_video_field, 'loupe360' ) !== false ) {
        $video_type = 'loupe';
    } elseif ( stripos( $raw_video_field, 'medialink' ) !== false ) {
        $video_type = 'medialink';
    } else {
        $video_type = 'other';
    }
}

$is_loupe     = ( $video_type === 'loupe' );
$is_medialink = ( $video_type === 'medialink' );
$has_video    = ( $video_type !== '' );

// ── "Ask About a Custom Setting" chat message ──────────────────────
// Built dynamically per-diamond (carat/shape/etc. come from
// ls_build_pname_diamond(), same helper the H1 above uses, plus the SKU
// as the unique reference) instead of a hardcoded example string, and
// escaped with esc_js() for safe use inside a single-quoted JS string —
// the openZohoChat(...) call sits inside a DOUBLE-quoted onclick
// attribute, so the message itself must be single-quoted here (same
// pattern as the "Need it sooner?" link elsewhere on this page) rather
// than reusing double quotes, which would break the attribute.
$custom_setting_msg = sprintf(
    "Hi! I'm interested in this diamond — %s Lab Grown Diamond (%s). I'd like to discuss a custom setting for it. Can someone help me with that?",
    ls_build_pname_diamond( $diamond ),
    $diamond->sku
);

?>


 

<div class="banner_section diamond-details-banner">
   <div class="page_heading mb_30" bis_skin_checked="1">
   <div class="container-fluid" bis_skin_checked="1">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" 
                onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'">
                    Back to Search
                </a>
            </li>
         </ol>
      </nav>
   </div>
</div>
   <?php 
        if($inventory_type === 'ringbuilder') {
            ?>
             <div class="container-fluid">
                <?php 
                    // Ring Builder Breadcrumb - Always show this
                    ls_ring_builder_breadcrumb('diamond');
                    ?>
            </div>
            <?php

        }
        ?>

    <div class="container-fluid">
        <div class="product_image">
            <div class="row">
                <div class="col-lg-7">
                
                    <div class="img_preview" style="position: relative;">
                    

                  

                    <div class="ls_rimg_glr ls_sp_glr">
                      <div class="thumbs">
                       
                          <div class="img  active">
                            <img src="<?php echo $thumbnail_url; ?>" alt="">
                          </div>
                          <div class="img ">
                            <p>Video</p>
                            <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="">
                          </div>
                          <?php
                          if($is_preview==true){

                         
                          ?>
                          <div class="img  ">
                            <p>Size Preview</p>
                            <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/hand-ring.svg" alt="">
                          </div>
                          <?php
                            }
                          ?>
                          <?php
                          if($diamond->certificate_type!=0 && $diamond->certificate_type!=4){
                            ?>
                            <div class="img  ">
                                <p>Certificate</p>
                                <img src="<?php echo get_template_directory_uri().'/aj_custom/diamond/certificate/'.$diamond->certificate_type; ?>.svg" alt="">
                              </div>
                            <?php
                          }
                          ?>
                           
                          <div class="img  ">
                            <p>Dimensions</p>
                            <img src="<?php echo $shape_l_wh; ?>" alt="">
                          </div>
                            <div class="img  ">
                            <p>Proportions</p>
                            <img src="<?php echo $shape_l_all; ?>" alt="">
                          </div>
                          <div class="img  ">
                            <p>Guarantee</p>
                            <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/guarantee.svg" alt="">
                          </div>
                      </div>

                      <div class="img_slide" style="position:relative;">
                          <div class="img" >
                                <div class="img_gallery_big" >
                                    <img src="<?php echo $thumbnail_url; ?>" alt="">
                                </div>
                                <p class="text-center mt-1 dm-text">
                                Diamond photo is for visual purposes only.
                                <?php
                                if($raw_video_field!=''){
                                    echo 'Play Video for a real diamond.';
                                }
                                ?>
                            </p>
                          </div>
                           <div class="img video_tab" style="display:none;">
                                <div class="video_link_multi">
                                    <?php if ( $is_loupe ) : ?>
                                        <!-- <div class="tap_to_view_video_details_wrap">
                                          <a class="" href="<?php echo esc_url( $video_url ); ?>" target="_blank">
                                              <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="">
                                          </a>
                                          <a class="tap_to_view_video_details_btn mt-2" href="<?php echo esc_url( $video_url ); ?>" target="_blank">Play Video</a>
                                        </div> -->
                                      <iframe src="https://loupe360.com/diamond/<?php echo $diamond->sku;?>/video/500/500" sku="<?php echo $diamond->sku;?>" width="500px" height="500px" class="loupe_resize" frameborder="0" scrolling="no" style="border:0;"></iframe>
                                    <?php elseif ( $is_medialink ) : ?>
                                        <div class="tap_to_view_video_details_wrap">
                                          <a class="" href="<?php echo esc_url( $video_url ); ?>" target="_blank">
                                              <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="">
                                          </a>
                                          <a class="tap_to_view_video_details_btn mt-2" href="<?php echo esc_url( $video_url ); ?>" target="_blank">Play Video</a>
                                        </div>
                                    <?php elseif ( $video_type === 'other' ) : ?>
                                          <div class="tap_to_view_video_details_wrap">
                                          <a class="" href="<?php echo esc_url( $video_url ); ?>" target="_blank">
                                              <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="">
                                          </a>
                                          <a class="tap_to_view_video_details_btn mt-2" href="<?php echo esc_url( $video_url ); ?>" target="_blank">Play Video</a>
                                        </div>
                                    <?php else : ?>
                                        <div class="tap_to_view_video_details_wrap video_request_prompt">
                                          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="" data-bs-toggle="modal" data-bs-target="#videoRequestModal">
                                          <h3 class="mt-2 mb-1">Want to see this diamond in montion?</h3>
                                          <p class="mt-2 mb-1">Request a 360 video and we'll send it within 24 business hours.</p>
                                          <button type="button" class="tap_to_view_video_details_btn mt-2 js-video-request-trigger" data-bs-toggle="modal" data-bs-target="#videoRequestModal">Request Video</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php
                          if($is_preview==true){

                         
                          ?>

                          <div class="img image_diamond_carat_size_guide_slide" style="display:none;" >
                

                            <div class="dcsg-container"
                                 crt="<?php echo $diamond->carat; ?>"
                                 data-bg="<?php echo esc_url( get_template_directory_uri().'/aj_custom/single-product/hand-with-setting.png' ); ?>"
                                 data-ring="<?php echo esc_url( $image_diamond_carat_size_guide['url'] ); ?>"></div>

                            

                          </div>
                          <?php
                            }
                          ?>
                           <?php
                          if($diamond->certificate_type!=0 && $diamond->certificate_type!=4){
                            ?>
                          <div class="img" style="display:none;">
                            <div class="tap_to_view_details_wrap">
                              <img src="<?php echo get_template_directory_uri().'/aj_custom/diamond/certificate/'.$diamond->certificate_type; ?>.svg" alt="">

                              <h3 class="tap_to_view_details_title mt-2 mb-2"><?php echo $cert_info['type'];?> Certified Diamond</h3>
                              <a class="tap_to_view_details_btn" href="<?php echo $diamond->certificate;?>" target="_blank">Tap to view details</a>
                            </div>
                          </div>
                          <?php
                      }
                          ?>
                          <div class="img " style="display:none;">
                            <div class="diamond_dimensions">
                                <canvas 
                                  class="diamond-canvas" 
                                  width="500" 
                                  height="500" 
                                  style="background-image: url('<?php echo $shape_l_wh; ?>'); background-size: 100% 100% ; background-repeat: no-repeat; background-position: center;"
                                  data-image="<?php echo $shape_l_wh; ?>"
                                  data-labels='[
                                    {"text": "L: <?php echo $size_mm_l;?>mm", "x": 40, "y": 273},
                                    {"text": "W: <?php echo $size_mm_w;?>mm", "x": 250, "y": 470}
                                  ]'>
                                </canvas>
                            </div>
                           <!--  <div class="dim-l-line"><div class="dim-l-text">L: <?php echo $size_mm_l;?>mm</div></div>
                            <div class="dim-w-line"><div class="dim-w-text">W: <?php echo $size_mm_w;?>mm</div></div>
                            <img src="<?php echo $shape_l_wh; ?>" alt=""> -->
                          </div>
                         <?php
                            // Human-readable Girdle / Culet labels for the dimensions-all
                            // canvas. Mirrors the $girdle_labels / $culet_labels lookups
                            // used later in this file for the main spec table.
                            $dcsg_girdle_labels = array( 'Ext. Thin', 'Very Thin', 'Thin', 'Medium', 'Sli. Thick', 'Thick', 'Very Thick', 'Ext. Thick', 'All' );
                            $dcsg_culet_labels  = array( 'None', 'Very Small', 'Medium', 'Slightly Large', 'Large', 'All' );

                            $dcsg_girdle_val = ( isset($diamond->girdle_thickness) && $diamond->girdle_thickness !== '' && (int) $diamond->girdle_thickness !== 0 )
                                ? ( $dcsg_girdle_labels[ (int) round($diamond->girdle_thickness) - 1 ] ?? $diamond->girdle_thickness )
                                : '';
                            $dcsg_culet_val = ( isset($diamond->culet) && $diamond->culet !== '' && (int) $diamond->culet !== 0 )
                                ? ( $dcsg_culet_labels[ (int) round($diamond->culet) - 1 ] ?? $diamond->culet )
                                : '';

                            // Per-shape label coordinates for the "dimensions all" canvas.
                            // Each shape's outline sits differently inside the 500x500
                            // canvas, so L/W/Table/Depth/Girdle/Culet/Cert need their own
                            // x,y per shape. Add/adjust entries as needed — anything not
                            // listed falls back to the 'default' (round) positions.
                            $dcsg_positions = array(
                                'default' => array(
                                    
                                    'table' => array(60, 430), 'depth' => array(150, 360),
                                    'girdle' => array(60, 390), 'culet' => array(150, 420),
                                    'cert' => array(60, 450),
                                    'inscription' => array(170, 210),
                                ),
                                'round' => array(
                                    
                                    'table' => array(245, 50), 'depth' => array(445, 360),
                                    'girdle' => array(50, 303), 'culet' => array(250, 475),
                                    'cert' => array(150, 200),
                                    'inscription' => array(100, 360),
                                ),
                                'oval' => array(
                                    
                                    'table' => array(245, 100), 'depth' => array(445, 300),
                                    'girdle' => array(50, 303), 'culet' => array(250, 425),
                                    'cert' => array(160, 200),
                                    'inscription' => array(110, 380),
                                ),
                                'cushion' => array(
                                    
                                    'table' => array(240, 45), 'depth' => array(445, 360),
                                    'girdle' => array(40, 275), 'culet' => array(250, 480),
                                    'cert' => array(150, 165),
                                    'inscription' => array(70, 355),
                                ),
                                'pear' => array(
                                    
                                    'table' => array(260, 105), 'depth' => array(445, 330),
                                    'girdle' => array(40, 300), 'culet' => array(310, 425),
                                    'cert' => array(170, 200),
                                    'inscription' => array(140, 335),
                                ),
                                'emerald' => array(
                                    
                                    'table' => array(240, 95), 'depth' => array(445, 340),
                                    'girdle' => array(40, 305), 'culet' => array(240, 430),
                                    'cert' => array(170, 200),
                                    'inscription' => array(100, 380),
                                ),
                                'marquise' => array(
                                    
                                    'table' => array(245, 80), 'depth' => array(445, 340),
                                    'girdle' => array(40, 295), 'culet' => array(250, 445),
                                    'cert' => array(150, 200),
                                    'inscription' => array(100, 350),
                                ),
                                'heart' => array(
                                    
                                    'table' => array(260, 90), 'depth' => array(440, 340),
                                    'girdle' => array(40, 295), 'culet' => array(300, 435),
                                    'cert' => array(170, 200),
                                    'inscription' => array(120, 345),
                                ),
                                'princess' => array(
                                    
                                    'table' => array(240, 26), 'depth' => array(445, 340),
                                    'girdle' => array(40, 280), 'culet' => array(240, 493),
                                    'cert' => array(170, 160),
                                    'inscription' => array(97, 385),
                                ),
                                'asscher' => array(
                                    
                                    'table' => array(240, 80), 'depth' => array(445, 350),
                                    'girdle' => array(40, 300), 'culet' => array(240, 470),
                                    'cert' => array(170, 190),
                                    'inscription' => array(130, 430),
                                ),
                                'radiant' => array(
                                    
                                    'table' => array(240, 35), 'depth' => array(445, 370),
                                    'girdle' => array(30, 290), 'culet' => array(240, 490),
                                    'cert' => array(170, 175),
                                    'inscription' => array(110, 430),
                                ),
                                'rose_cut' => array(
                                    
                                    'table' => array(250, 330), 'depth' => array(450, 180),
                                    'girdle' => array(55, 190), 'cert' => array(170, 300),
                                    'inscription' => array(170, 160),
                                ),
                                'old_european' => array(
                                    
                                    'table' => array(245, 40), 'depth' => array(445, 370),
                                    'girdle' => array(53, 280), 'culet' => array(245, 480),
                                    'cert' => array(170, 185),
                                    'inscription' => array(70, 370),
                                ),
                                'half_moon' => array(
                                    
                                    'table' => array(240, 100), 'depth' => array(445, 300),
                                    'girdle' => array(33, 293), 'culet' => array(245, 415),
                                    'cert' => array(170, 197),
                                    'inscription' => array(120, 345),
                                ),
                                'baguette_trapezoid' => array(
                                    
                                    'table' => array(250, 150), 'depth' => array(445, 320),
                                    'girdle' => array(35, 350),
                                    'cert' => array(170, 240),
                                    'inscription' => array(170, 370),
                                ),
                                'baguette' => array(
                                    
                                    'table' => array(240, 115), 'depth' => array(445, 350),
                                    'girdle' => array(30, 325),
                                    'cert' => array(175, 225),
                                    'inscription' => array(150, 375),
                                ),
                                'hexagon' => array(
                                    
                                    'table' => array(250, 87), 'depth' => array(445, 340),
                                    'girdle' => array(30, 310), 'culet' => array(240, 490),
                                    'cert' => array(170, 205),
                                    'inscription' => array(170, 395),
                                ),
                                'kite' => array(
                                    
                                    'table' => array(250, 115), 'depth' => array(445, 360),
                                    'girdle' => array(30, 330), 'culet' => array(330, 400),
                                    'cert' => array(170, 225),
                                    'inscription' => array(140, 375),
                                ),
                                'old_mine' => array(
                                    
                                    'table' => array(240, 27), 'depth' => array(445, 370),
                                    'girdle' => array(33, 290), 'culet' => array(240, 485),
                                    'cert' => array(170, 180),
                                    'inscription' => array(95, 375),
                                ),
                                'trapezoid' => array(
                                    
                                    'table' => array(240, 130), 'depth' => array(445, 340),
                                    'girdle' => array(30, 330),
                                    'cert' => array(170, 220),
                                    'inscription' => array(140, 375),
                                ),
                                'triangular' => array(
                                    
                                    'table' => array(295, 100), 'depth' => array(445, 330),
                                    'girdle' => array(35, 310), 'culet' => array(315, 415),
                                    'cert' => array(170, 210),
                                    'inscription' => array(150, 360),
                                ),
                            );

                            // Shapes not explicitly listed above (rose_cut, old_european,
                            // half_moon, baguette_trapezoid, baguette, hexagon, kite,
                            // old_mine, trapezoid, triangular) use 'default' until tuned.
                            $dcsg_shape_alias = array(
                                'cushion_brilliant' => 'cushion', 'cushion_modified' => 'cushion',
                                'antique_cushion' => 'cushion', 'old_mine_cushion' => 'cushion', 'square_cushion' => 'cushion',
                                'oval_step' => 'oval',
                                'pear_step' => 'pear',
                                'square_emerald' => 'emerald',
                                'dutch_marquise' => 'marquise',
                                'rectangular_radiant' => 'radiant', 'radiant_modified' => 'radiant', 'square_radiant' => 'radiant',
                                'eurocut' => 'old_european',
                                'trapez_step' => 'trapezoid', 'trapez_brilliant' => 'trapezoid',
                            );
                            $dcsg_lookup_key = $dcsg_shape_alias[$shape_key] ?? $shape_key;
                            $dcsg_pos = $dcsg_positions[$dcsg_lookup_key] ?? $dcsg_positions['default'];

                            $dcsg_labels = array(
                                array( 'text' => 'Table: ' . ($diamond->av_table ?? '') . '%', 'xy' => $dcsg_pos['table'] ),
                                array( 'text' => 'Depth: ' . ($diamond->depth ?? '') . '%',    'xy' => $dcsg_pos['depth'] ),
                                array( 'text' => 'Girdle: ' . $dcsg_girdle_val,          'xy' => $dcsg_pos['girdle'] ),
                                array( 'text' => ($cert_info['id'] ?? ''),  'xy' => $dcsg_pos['cert'] ),
                                array( 'text' => 'Inscription Number','xy' => $dcsg_pos['inscription'] ),
                            );
                            if ( isset( $dcsg_pos['culet'] ) ) {
                                $dcsg_labels[] = array( 'text' => 'Culet: ' . $dcsg_culet_val, 'xy' => $dcsg_pos['culet'] );
                            }
                            $dcsg_labels_out = array_map(function( $item ) {
                                return array(
                                    'text' => $item['text'],
                                    'x'    => $item['xy'][0],
                                    'y'    => $item['xy'][1],
                                );
                            }, $dcsg_labels);
                          ?>
                         <div class="img diamond_dimensions_all" style="display:none;">
                            <div class="diamond_dimensions">
                                <canvas 
                                  class="diamond-canvas" 
                                  width="500" 
                                  height="500"  
                                  style="background-image: url('<?php echo $shape_l_all; ?>'); background-size: 100% 100% ; background-repeat: no-repeat; background-position: center;"
                                  data-image="<?php echo $shape_l_wh; ?>"
                                  data-labels='<?php echo wp_json_encode( $dcsg_labels_out ); ?>'>
                                </canvas>
                            </div>
                          </div> 
                          <div class="img diamond_guarantee" style="display:none;">
                            <div class="diamond_guarantee_wrap text-center">
                                <div class="guarantee-icon mb-3">
                                    <img src="<?php echo get_template_directory_uri().'/aj_custom/diamond/shield.svg';?>">
                                </div>
                                <h2 class="guarantee-title">Why customers trust us for safe online diamond shopping</h2>
                                <div class="row guarantee-grid">
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">1</div>
                                        <div class="guarantee-item-title">100% Money-Back Guarantee</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">2</div>
                                        <div class="guarantee-item-title"><?php echo RETURN_DAYS;?>-Day Returns & Exchanges</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">3</div>
                                        <div class="guarantee-item-title">IGI, GIA & GCAL Certified Diamonds*</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">4</div>
                                        <div class="guarantee-item-title">Free Insured Shipping*</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">5</div>
                                        <div class="guarantee-item-title">Price Match & Beat Guarantee</div>
                                    </div>
                                     <?php
                                  
                                  
                                  ?>
                                    <div class="col-md-4 col-6 mb-4 text-center">
                                        <div class="guarantee-bubble">6</div>
                                        <div class="guarantee-item-title">5,000+ Verified Raving Reviews</div>
                                    </div>
                                    <?php
                                  
                                  ?>
                                </div>
                                <?php
                                  
                                  
                                  ?>
                                  <div class="mt-4">
                                    <button class="btn guarantee-btn mt-0">Learn More</button>
                                    <p class="mt-3 guarantee-proof">Proof? <a href="/reviews/">Read our reviews</a></p>
                                </div>
                                  <?php
                                  
                                  ?>
                                
                            </div>
                          </div>
                         
                          <div class="top-single-icon">
                               <?php
                               if ($diamond->shipping==1) {
                                ?>
                                <a href="javascript:void(0)" class="oc-single-express" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Ship Within 3 Days" >
                                        <img src="<?php echo esc_url( get_template_directory_uri().'/aj_custom/inventory/delivery.svg' ); ?>">
                                    </a>
                                <?php
                               }
                               if ($diamond->shipping==2) {
                                ?>
                                <a href="javascript:void(0)" class="oc-single-express" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Overnight Delivery Available — Order Before 11:00 AM EDT">
                                        <img src="<?php echo esc_url( get_template_directory_uri().'/aj_custom/inventory/space-rocket.svg' ); ?>">
                                    </a>
                                <?php
                               }

                                if ($diamond->sale==1) {
                                    ?>
                                    
                                    <a href="javascript:void(0)" class="oc-single-instore" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Available In Store">
                                        <img src="<?php echo esc_url( get_template_directory_uri().'/aj_custom/category/store-icon.svg' ); ?>">
                                    </a>
                                    <?php
                                }
                                ?>   
                                <button class="wish-btn btn p-0" data-product-id="<?php echo esc_attr($diamond->id); ?>" aria-pressed="false">
                                    <i class="fa-heart fa-regular fa-lg"></i>
                                </button>
                          </div>
                        
                        <div class="ls_slide_arrows">
                                <button class="ls_arrow_btn ls_prev_btn" type="button" aria-label="Previous image"><i class="fa-solid fa-less-than"></i></button>
                                        <button class="ls_arrow_btn ls_next_btn" type="button" aria-label="Next image"><i class="fa-solid fa-greater-than "></i></button>
                            </div>
                      </div>
                    </div>

                    <div class="product-help-box text-center">Need help? <a href="javascript:void(0);"  onclick="$zoho.salesiq.floatwindow.visible('show'); return false;" class="chat-link">Chat with us</a> or call<a href="tel:+<?php echo SUPPORT_MOBILE?>"> +<?php echo SUPPORT_MOBILE?></a></div>
                    </div>

                </div>

                <div class="col-lg-5">
                    <div class="product_information">
                       <h1 class="fw-500 text_black_secondary text-capitalize">
    <?php echo esc_html( ls_build_pname_diamond($diamond) ); ?> Lab Grown Diamond
</h1>

                        <?php
if ( $diamond->certificate != '' ) {
    if ( ! empty( $cert_info['id'] ) ) {
        /*echo "<pre>";
        print_r($cert_info);
        echo "<pre>";*/
        ?>
        <p><?php echo $cert_info['type'];?> Certificate Number: <a href="<?php echo esc_url( $diamond->certificate ?? '#' ); ?>" class="text-decoration-none" target="_blank"><strong><?php echo esc_html( $cert_info['id'] ); ?></strong></a></p>
        <?php
    } else {
        ?>
        <p>Certificate Number: Non Certified</p>
        <?php
    }
}else{
    echo '<p>Certificate Number: Non Certified</p>';
}
?>
                        <ul class="details_list">
                          <li>Cut: <strong><?php echo esc_html($cut); ?></strong></li>
                          <li>Color : <strong><?php echo esc_html( $color ); ?></strong></li>
                          <li>Clarity : <strong><?php echo esc_html($clarity); ?></strong></li>
                        </ul>
                        <?php
                       /* echo "<pre>";
                        print_r($diamond);
                        echo "</pre>";*/

                        ?>
                          <!-- Simple product Add to Cart (uses linked WooCommerce product `pid`) -->
                          <form class="cart" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($diamond->pid ?? 0); ?>">
                            <input type="hidden" name="type" value="<?php echo esc_attr($inventory_type); ?>">

                            <div class="price-display oc_simple_product" style="margin-bottom: 10px; font-size: 16px; font-weight: 500;">
                              <?php if (!empty($org_price) && $org_price > $price) : ?>
    <span class="price">
        <del>was <?php echo wc_price($org_price, ['decimals' => 0]); ?></del>
        <ins><?php echo wc_price($price, ['decimals' => 0]); ?></ins>
    </span>
<?php else: ?>
    <span class="price"><?php echo wc_price($price, ['decimals' => 0]); ?></span>
<?php endif; ?>
                            </div>
                            
                            <div class="add_cart d-flex flex-column align-items-center gap-3">
                              <input type="hidden" name="quantity" min="1" value="1">
                              <input type="hidden" name="custom_note" value="<?php echo esc_attr(wp_strip_all_tags($diamond->note ?? '')); ?>">
                              <?php if ( $is_sale_page ) : ?>
                                <?php // Sale-tag pages: just Add To Cart + Similar Diamonds — no Add To Ring / Ask About a Custom Setting. ?>
                                <?php if ( $is_in_cart ) : ?>
                                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="common_btn md_common_btn w-100">
                                  <span>Go To Cart</span>
                                </a>
                                <?php else : ?>
                                <button type="button" class="common_btn md_common_btn w-100" data-type="default">
                                  <span>Add To Bag</span>
                                  <span class="single_p_loader ls_stng_loder spinner-border spinner-border-sm d-none"></span>
                                </button>
                                <?php endif; ?>
                                <a href="<?php echo esc_url( add_query_arg( 'is_sale', 1, home_url( '/lab-diamonds/' ) ) ); ?>" class="common_btn md_common_btn common_btn_outline w-100">
                                  <span>Similar Diamonds</span>
                                </a>
                              <?php else : ?>
                              <?php
                              if($is_setting==true && $is_in_cart_ring==false && (int) $diamond->certificate_type !== 4){
                              ?>


                              <button type="button" class="common_btn md_common_btn   w-100" data-type="<?php echo $is_colored ? 'ringbuilder_c' : 'ringbuilder'; ?>">
                                <span>Add To Ring</span>
                                <span class="single_p_loader ls_stng_loder spinner-border spinner-border-sm d-none"></span>
                              </button>


                              <?php
                              }
                              ?>
                              <?php if ( $is_in_cart ) : ?>
                              <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="common_btn md_common_btn w-100">
                                <span>Go To Cart</span>
                              </a>

                              <?php else : ?>
                                   <?php
                                   // "Add To Bag" is only outlined when it's sharing space with
                                   // another CTA (Add To Ring and/or Ask About a Custom Setting) —
                                   // if it's the only button, it stays solid.
                                   $has_multiple_bag_buttons = $is_setting == true || $is_custom_setting_shape;
                                   ?>
                                   <?php if ( $is_custom_setting_shape ) : ?>
                                   <a  href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#customSettingModal"  class="common_btn md_common_btn w-100">
                                  <span>Ask About a Custom Setting</span>
                                </a>
                                   <?php endif; ?>

                                <button type="button" class="common_btn md_common_btn<?php echo $has_multiple_bag_buttons ? ' common_btn_outline' : ''; ?> w-100" data-type="default">
                                  <span>Add To Bag</span>
                                  <span class="single_p_loader ls_stng_loder spinner-border spinner-border-sm d-none"></span>
                                </button>
                              <?php endif; ?>
                              <?php endif; ?>
                              
                            </div>
                          </form>

                         <?php
                            
                            ?>
                            <div class="est_delivery_date mt-2">
                                <p class="mb-0">
                                    <?php echo $price > 500 ? 'Free Insured ' : ' Insured '; ?> shipping by <strong><?php echo esc_html( $estimated_delivery_date ); ?></strong>
                                    <i class="fa-regular fa-circle-question ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Estimated delivery date. Actual delivery may vary based on your shipping location and carrier." style="cursor: pointer;"></i>
                                </p>
                                <p class="mb-0">
                                    if you order in the next 15 hours. <a href="javascript:void(0)" onclick="openZohoChat('Hi, I need this diamond sooner than the estimated delivery date. Product page: ' + window.location.href)">Need it sooner?</a>
                                </p>
                            </div>
                            <?php
                            
                            ?>
                      
                        <div class="ls-iconcnt">
                            <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-1.svg" title=""><span class="ls-cnt">Insured Shipping Worldwide</span></div>
                            <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-2.svg" title=""><span class="ls-cnt">Easy <?php echo RETURN_DAYS; ?> Day Returns</span></div>
                            <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-3.svg"><span class="ls-cnt">Manufacturer Warranty</span></div>
                            <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-4.svg"><span class="ls-cnt">Price Match/Beat Guarantee*</span></div>
                            <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-5.svg"><span class="ls-cnt">Complimentary Jewelry Box &amp; Bag Wrapping</span></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="container my-5">
  <?php
    // Dynamic values for the details table — fallbacks if fields are missing
    $carat_val     = isset($diamond->carat) ? $diamond->carat : '';
   
    $cut_label     = isset($cut) ? $cut : ($diamond->cut ?? '—');
    $color_label   = $color;
    $clarity_label = isset($clarity) ? $clarity : ($diamond->clarity ?? '—');
    $lsFunction = lsFunction($cut_label, $color_label, $clarity_label,$diamond->dtype,$intensity);
 /*echo "<pre>";
    print_r($diamond->dtype);
   print_r($lsFunction);
   echo "</pre>";*/
    // Certificate text
    $certificate_text = 'Diamond';

    switch ((int) ($diamond->certificate_type ?? 0)) {
        case 0:
            $certificate_text = 'Diamond (Non Certified)';
            break;
        case 1:
            $certificate_text = 'Diamond, IGI Certificate';
            break;
        case 2:
            $certificate_text = 'Diamond, GIA Certificate';
            break;
        case 3:
            $certificate_text = 'Diamond, GCAL Certificate';
            break;
        case 4:
            $certificate_text = 'Diamond, Matching Pair';
            break;
    }

    // "In the box" value with an eye icon that opens the certificate PDF
    // in a new tab, mirroring the .certilink treatment used elsewhere.
    $certificate_text_html = esc_html( $certificate_text );
   
    if ( (int) ($diamond->certificate_type ?? 0) === 0 ) {
        $certificate_text_html .= ' <i class="fa-regular fa-circle-question ms-2 text-muted"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="The diamond doesn\'t include a GIA or IGI grading report. The quality is graded by the manufacturer."
            aria-label="Non Certified Info"></i>';
    } elseif ( ! empty( $diamond->certificate ) &&  $diamond->certificate_type==2) {

       $certificate_text_html .= ' <a href="javascript:void(0);" class="text-decoration-none ms-2"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="GIA graded lab grown diamonds have a digital copy of the grading report accessible on their report website."
    aria-label="View Certificate">
    <i class="fa-regular fa-circle-question"></i>
</a>';
    }

    // Table & Depth
    $table_depth = '—';
    if (
        isset($diamond->av_table, $diamond->depth) &&
        $diamond->av_table !== '' &&
        $diamond->depth !== ''
    ) {
        $table_depth = $diamond->av_table . '% , ' . $diamond->depth . '%';
    }
  ?>

  <?php
    // Label lookups for the grade fields (mirrors the React
    // girdleLabels / fluorescenceLabels / polishSymmetryLabels arrays
    // in components/FilterSlider.jsx).
    $girdle_labels    = array( 'Ext. Thin', 'Very Thin', 'Thin', 'Medium', 'Sli. Thick', 'Thick', 'Very Thick', 'Ext. Thick', 'All' );
    $fluorescence_labels = array( 'None', 'Faint', 'Medium', 'Strong', 'Very Strong', 'All' );
    $polish_symmetry_labels = array( 'Good', 'Very Good', 'Excellent', 'All' );
    $culet_labels = array( 'None', 'Very Small', 'Medium', 'Slightly Large', 'Large', 'All' );

    $girdle_val = ( isset($diamond->girdle_thickness) && $diamond->girdle_thickness !== '' && (int) $diamond->girdle_thickness !== 0 )
        ? ( $girdle_labels[ (int) round($diamond->girdle_thickness) - 1 ] ?? $diamond->girdle_thickness )
        : '—';
    $crown_val = ( isset($diamond->crown_angle) && $diamond->crown_angle !== '' && $diamond->crown_angle !== null )
        ? $diamond->crown_angle . '°'
        : '—';
    $pavilion_val = ( isset($diamond->pavilion_angle) && $diamond->pavilion_angle !== '' && $diamond->pavilion_angle !== null )
        ? $diamond->pavilion_angle . '°'
        : '—';
    $fluorescence_val = ( isset($diamond->fluorescence) && $diamond->fluorescence !== '' && (int) $diamond->fluorescence !== 0 )
        ? ( $fluorescence_labels[ (int) round($diamond->fluorescence) - 1 ] ?? $diamond->fluorescence )
        : '—';
    $polish_val = ( isset($diamond->polish) && $diamond->polish !== '' && (int) $diamond->polish !== 0 )
        ? ( $polish_symmetry_labels[ (int) round($diamond->polish) - 1 ] ?? $diamond->polish )
        : '—';
    $symmetry_val = ( isset($diamond->symmetry) && $diamond->symmetry !== '' && (int) $diamond->symmetry !== 0 )
        ? ( $polish_symmetry_labels[ (int) round($diamond->symmetry) - 1 ] ?? $diamond->symmetry )
        : '—';
    $culet_val = ( isset($diamond->culet) && $diamond->culet !== '' && (int) $diamond->culet !== 0 )
        ? ( $culet_labels[ (int) round($diamond->culet) - 1 ] ?? $diamond->culet )
        : '—';

    // Each entry: label, value (required), desc (optional small subtext).
    // Order below is LEFT COLUMN (top to bottom) followed by RIGHT COLUMN
    // (top to bottom) — the grid CSS uses grid-auto-flow: column with a
    // fixed row count, so DOM order fills column 1 first, then column 2.
    // left-last / right-last classes are computed automatically below —
    // to add a new field later, just append it to the correct column
    // group, no other markup changes needed.
    $dd_items = array(
        // ── Left column ──
        array(
            'label' => 'Carat',
            'value' => $carat_val ?: '—',
        ),
        array(
            'label' => 'L x W x H',
            'value' => $size_mm ?: '—',
        ),
        array(
            'label' => 'Cut',
            'value' => $cut_label,
            'desc'  => $lsFunction['cut'],
        ),
        array(
            'label' => 'Clarity',
            'value' => $clarity_label,
            'desc'  => $lsFunction['clarity'],
        ),
        array(
            'label' => 'Girdle Thickness',
            'value' => $girdle_val,
        ),
        array(
            'label' => 'Crown Angle',
            'value' => $crown_val,
        ),
        array(
            'label' => 'Pavilion Angle',
            'value' => $pavilion_val,
        ),
        array(
            'label' => 'SKU',
            'value' => $diamond->sku ?? '—',
        ),
        // ── Right column ──
        array(
            'label' => 'Table & Depth',
            'value' => $table_depth,
        ),
        array(
            'label' => 'L/W Ratio',
            'value' => $diamond->lwratio ?? '—',
        ),
        $is_colored
            ? array(
                'label' => 'Intensity',
                'value' => $intensity_label,
                'desc'  => $lsFunction['intensity'],
              )
            : array(
                'label' => 'Color',
                'value' => $color,
                'desc'  => $lsFunction['color'],
              ),
        array(
            'label' => 'Growth Type',
            'value' => get_attrkey_val('dtype', $diamond->dtype),
            'desc'  => $lsFunction['dtype'],
        ),
        array(
            'label' => 'Fluorescence',
            'value' => $fluorescence_val,
        ),
        array(
            'label' => 'Polished',
            'value' => $polish_val,
        ),
        array(
            'label' => 'Symmetry',
            'value' => $symmetry_val,
        ),
        array(
            'label' => 'Culet',
            'value' => $culet_val,
        ),
        array(
            'label' => 'In the box',
            'value' => $certificate_text_html,
            'html'  => true,
        ),
    );

    // Auto-split into two even columns (left/right), same as the grid's
    // visual layout, and flag the last item of each column so the CSS
    // border/margin rules (.left-last / .right-last) land correctly —
    // no need to hand-tag a class when adding/removing fields above.
    $dd_total      = count( $dd_items );
    $dd_left_count = (int) ceil( $dd_total / 2 );
  ?>
  <div class="diamond_detail_wrap">
    <h2 class="font_literata">Diamond Details</h2>

    <div class="diamond-details-grid">
      <?php foreach ( $dd_items as $dd_index => $dd ) :
          $dd_class = '';
          if ( $dd_index === $dd_left_count - 1 ) {
              $dd_class = 'left-last';
          } elseif ( $dd_index === $dd_total - 1 ) {
              $dd_class = 'right-last';
          }
      ?>
        <div class="dd-item<?php echo $dd_class ? ' ' . esc_attr($dd_class) : ''; ?>">
          <div class="dd-label text-muted"><?php echo esc_html( $dd['label'] ); ?></div>
          <?php if ( isset($dd['desc']) && $dd['desc'] !== '' ) : ?>
            <div class="dd-value">
              <div class="fw-bold"><?php echo ! empty( $dd['html'] ) ? $dd['value'] : esc_html( $dd['value'] ); ?></div>
              <div class="small"><?php echo esc_html( $dd['desc'] ); ?></div>
            </div>
          <?php else : ?>
            <div class="dd-value fw-bold"><?php echo ! empty( $dd['html'] ) ? $dd['value'] : esc_html( $dd['value'] ); ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
<?php
// Ordered (worst → best) icon sets for the side-by-side comparison rows
// used in the Cut / Color / Clarity accordion sections below.
$cut_visual_set     = ls_get_cut_visual_set( $cut_label );
$color_visual_set   = $is_colored ? array() : ls_get_color_visual_set( $color_label );
$clarity_visual_set = ls_get_clarity_visual_set( $clarity_label );

$cut_icon_base     = get_template_directory_uri() . '/aj_custom/diamond/single/cut/';
$color_icon_base   = get_template_directory_uri() . '/aj_custom/diamond/single/colour/';
$clarity_icon_base = get_template_directory_uri() . '/aj_custom/diamond/single/clarity/';

// Diamond title used in the "You've selected a {title}" sentences below.
$diamond_title = ls_build_pname_diamond( $diamond ).'  Lab Grown Diamond ';

// ── Shape-specific blurb for the Cut accordion ─────────────────────
$dd_shape_info = array(
    'round'              => array( 'blurb' => "Round diamonds are the preferred choice of 56% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/all-there-is-to-know-about-round-cut-diamonds/' ),
    'marquise'           => array( 'blurb' => "Marquise diamonds are the preferred choice of 3% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/marquise-cut-diamonds/' ),
    'oval'               => array( 'blurb' => "Oval diamonds are the preferred choice of 16% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/oval-cut-diamond/' ),
    'emerald'            => array( 'blurb' => "Emerald diamonds are the preferred choice of 6% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/emerald-cut-diamond/' ),
    'pear'               => array( 'blurb' => "Pear diamonds are the preferred choice of 4% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/pear-shaped-diamond/' ),
    'princess'           => array( 'blurb' => "Princess diamonds are the preferred choice of 5% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/princess-cut-diamond/' ),
    'radiant'            => array( 'blurb' => "Radiant diamonds are the preferred choice of 4% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/radiant-cut-diamond/' ),
    'cushion'            => array( 'blurb' => "Cushion diamonds are the preferred choice of 8% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/cushion-cut/' ),
    'heart'              => array( 'blurb' => "Heart diamonds are the preferred choice of 1% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/heart-shaped/' ),
    'asscher'            => array( 'blurb' => "Asscher diamonds are the preferred choice of 2% of our customers.", 'link' => 'https://www.loosegrowndiamond.com/blog/asscher-cut-diamonds/' ),
    'rose_cut'           => array( 'blurb' => "Rose cuts are primarily chosen for vintage, antique-inspired, and artisan/custom jewelry.", 'link' => 'https://www.loosegrowndiamond.com/blog/rose-cut-diamonds/' ),
    'old_european'       => array( 'blurb' => "Old European Cuts are almost exclusively chosen for antique, vintage, and heirloom-style jewelry.", 'link' => 'https://www.loosegrowndiamond.com/blog/old-european-cut-diamond/' ),
    'half_moon'          => array( 'blurb' => "Half Moon diamonds are most commonly used as elegant side stones in three-stone and custom engagement rings, making them one of the rarest diamond shapes.", 'link' => 'https://www.loosegrowndiamond.com/blog/half-moon-diamond/' ),
    'baguette_trapezoid' => array( 'blurb' => "Baguette Trapezoid diamonds showcase a distinctive rectangular tapered shape with step-cut facets, making them an ideal accent stone for three-stone rings and sophisticated custom settings." ),
    'baguette'           => array( 'blurb' => "Baguette diamonds feature a sleek rectangular shape with step-cut facets, offering clean lines and understated brilliance. They are a timeless choice for elegant side stones and modern jewelry designs.", 'link' => 'https://www.loosegrowndiamond.com/blog/baguette-cut-diamond/' ),
    'hexagon'            => array( 'blurb' => "Hexagon diamonds feature a bold six-sided silhouette that delivers a contemporary look with striking geometric appeal. Their distinctive shape makes them a standout choice for modern and custom jewelry.", 'link' => 'https://www.loosegrowndiamond.com/blog/hexagon-diamond-ring/' ),
    'kite'               => array( 'blurb' => "Kite diamonds showcase a unique angular silhouette that adds a modern, artistic touch to any design. Popular in custom and asymmetrical settings, they create a bold and distinctive statement.", 'link' => 'https://www.loosegrowndiamond.com/blog/kite-cut-diamond/' ),
    'old_mine'           => array( 'blurb' => "Old Mine diamonds feature a vintage cushion-like shape with large facets and a warm, romantic sparkle. They are a timeless choice for antique, heirloom-inspired, and vintage-style jewelry.", 'link' => 'https://www.loosegrowndiamond.com/blog/about-old-mine-cut-diamonds/' ),
    'trapezoid'          => array( 'blurb' => "Trapezoid diamonds showcase a distinctive tapered shape with step-cut facets, making them an ideal accent stone for three-stone rings and sophisticated custom settings.", 'link' => 'https://www.loosegrowndiamond.com/blog/trapezoid-diamonds/' ),
    'triangular'         => array( 'blurb' => "Triangular diamonds feature a bold three-sided silhouette that adds a contemporary edge to any design. Often used as striking side stones or unique center stones, they create a modern and eye-catching look." ),
);

$dd_shape_paragraph = '';
if ( isset( $dd_shape_info[ $shape_key ] ) ) {
    $dd_shape_entry     = $dd_shape_info[ $shape_key ];
    $dd_shape_paragraph = 'You’ve selected a ' . esc_html( $diamond_title ) . '. ' . esc_html( $dd_shape_entry['blurb'] );
    if ( ! empty( $dd_shape_entry['link'] ) ) {
        $dd_shape_paragraph .= ' <a href="' . esc_url( $dd_shape_entry['link'] ) . '" target="_blank">Discover why they\'re so popular.</a>';
    }
}

// ── Color-grade-specific blurb for the Color accordion ─────────────
$dd_color_info = array(
    'M' => array( 'text' => "graded M Color (Faint Color), featuring a noticeable warm tone that appeals to those who appreciate a vintage-inspired look." ),
    'L' => array( 'text' => "graded L Color (Faint Color), offering a warmer appearance that pairs beautifully with yellow or rose gold settings." ),
    'K' => array( 'text' => "graded K Color (Faint Color), showing a slight warm tint while remaining a budget-friendly choice." ),
    'J' => array( 'text' => "graded J Color (Near Colorless), providing excellent value with a subtle warmth that's often difficult to notice once set." ),
    'I' => array( 'text' => "graded I Color (Near Colorless), offering beautiful brilliance at a more affordable price." ),
    'H' => array( 'text' => "graded H Color (Near Colorless), a popular choice that balances exceptional appearance and value.", 'link' => 'https://www.loosegrowndiamond.com/blog/is-h-color-diamond-good/' ),
    'G' => array( 'text' => "graded G Color (Near Colorless), delivering excellent value with little to no visible color.", 'link' => 'https://www.loosegrowndiamond.com/blog/g-color-diamond/' ),
    'F' => array( 'text' => "graded F Color (Colorless), the final grade in the colorless range with outstanding brilliance.", 'link' => 'https://www.loosegrowndiamond.com/blog/f-color-diamond/' ),
    'E' => array( 'text' => "graded E Color (Colorless), offering exceptional colorless beauty that's virtually indistinguishable from D." ),
    'D' => array( 'text' => "graded D Color (Colorless), the highest color grade available.", 'link' => 'https://www.loosegrowndiamond.com/blog/diamond-color-d/' ),
);

$dd_color_paragraph = '';
$dd_color_key        = strtoupper( trim( $color_label ) );
if ( isset( $dd_color_info[ $dd_color_key ] ) ) {
    $dd_color_entry      = $dd_color_info[ $dd_color_key ];
    $dd_color_paragraph  = 'Your ' . esc_html( $diamond_title ) . ' is ' . esc_html( $dd_color_entry['text'] );
    if ( ! empty( $dd_color_entry['link'] ) ) {
        $dd_color_paragraph .= ' <a href="' . esc_url( $dd_color_entry['link'] ) . '" target="_blank">Learn more about ' . esc_html( $dd_color_key ) . ' color diamonds here.</a>';
    }
}

// ── Clarity-grade-specific blurb for the Clarity accordion ─────────
$dd_clarity_info = array(
    'I2'   => array( 'text' => "graded I2 Clarity (Included 2), meaning prominent inclusions are visible to the naked eye and are more likely to impact the diamond's brilliance and durability.", 'link' => 'https://www.loosegrowndiamond.com/blog/i2-diamond/' ),
    'I1'   => array( 'text' => "graded I1 Clarity (Included 1), meaning inclusions are visible to the naked eye and may affect the diamond's appearance or durability.", 'link' => 'https://www.loosegrowndiamond.com/blog/i1-clarity-diamond/' ),
    'SI3'  => array( 'text' => "graded SI3 Clarity (Slightly Included 3), meaning inclusions are more prominent and may be visible without magnification." ),
    'SI2'  => array( 'text' => "graded SI2 Clarity (Slightly Included 2), meaning inclusions are noticeable under 10× magnification and may be visible to the naked eye in some diamonds.", 'link' => 'https://www.loosegrowndiamond.com/blog/si2-clarity-diamonds/' ),
    'SI1'  => array( 'text' => "graded SI1 Clarity (Slightly Included 1), meaning inclusions are visible under 10× magnification but are often eye-clean, depending on the diamond.", 'link' => 'https://www.loosegrowndiamond.com/blog/si1-clarity-diamond/' ),
    'VS2'  => array( 'text' => "graded VS2 Clarity (Very Slightly Included 2), meaning minor inclusions are difficult to detect under 10× magnification and are typically invisible to the naked eye.", 'link' => 'https://www.loosegrowndiamond.com/blog/vs2-diamond-clarity-guide/' ),
    'VS1'  => array( 'text' => "graded VS1 Clarity (Very Slightly Included 1), meaning any inclusions are extremely difficult to see under 10× magnification.", 'link' => 'https://www.loosegrowndiamond.com/blog/vs1-diamond-clarity-guide/' ),
    'VVS2' => array( 'text' => "graded VVS2 Clarity (Very Very Slightly Included 2), meaning any inclusions are extremely difficult to see under 10× magnification.", 'link' => 'https://www.loosegrowndiamond.com/blog/vvs-diamonds-guide/' ),
    'VVS1' => array( 'text' => "graded VVS1 Clarity (Very Very Slightly Included 1), meaning any inclusions are extremely difficult to detect under 10× magnification.", 'link' => 'https://www.loosegrowndiamond.com/blog/vvs-diamonds-guide/' ),
    'IF'   => array( 'text' => "graded IF Clarity (Internally Flawless), meaning no internal inclusions are visible under 10× magnification, with only insignificant surface blemishes possible.", 'link' => 'https://www.loosegrowndiamond.com/blog/if-diamond/' ),
    'FL'   => array( 'text' => "graded FL Clarity (Flawless), meaning no internal or external imperfections are visible under 10× magnification.", 'link' => 'https://www.loosegrowndiamond.com/blog/flawless-diamond/' ),
);

$dd_clarity_paragraph = '';
$dd_clarity_key        = strtoupper( trim( $clarity_label ) );
if ( isset( $dd_clarity_info[ $dd_clarity_key ] ) ) {
    $dd_clarity_entry      = $dd_clarity_info[ $dd_clarity_key ];
    $dd_clarity_paragraph  = 'Your ' . esc_html( $diamond_title ) . ' is ' . esc_html( $dd_clarity_entry['text'] );
    if ( ! empty( $dd_clarity_entry['link'] ) ) {
        $dd_clarity_paragraph .= ' <a href="' . esc_url( $dd_clarity_entry['link'] ) . '" target="_blank">Learn more about ' . esc_html( $dd_clarity_key ) . ' clarity diamonds or explore our complete diamond clarity guide here.</a>';
    }
}
?>
  <div class="diamond-details">
    <div class="container">
      <h2 class="section-title">The Four C's of Your Diamond</h2>

      <div class="accordion" id="ddAccordionExample">
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#ddCollapseOne"
              aria-expanded="true" aria-controls="ddCollapseOne">
              Carat : <span class="fw-semibold"><?php echo esc_html( $carat_val ?: '—' ); ?> ct</span>
            </button>
          </h3>
          <div id="ddCollapseOne" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <div class="acc-inner">
                <div class="acc-text">
                  <p>Carat measures a diamond's weight, not its size. Our gemologists evaluate each diamond's dimensions and proportions to ensure it looks as large and beautiful as possible for its carat weight.</p>
                  <p>Use the checklist below to see how your diamond compares to our expert standards.</p>
                </div>
                <div class="acc-visual">
                    <?php
                    if($is_preview==true){
                    ?>
                 <div class="dcsg-container"
                                 crt="<?php echo $diamond->carat; ?>"
                                 data-bg="<?php echo esc_url( get_template_directory_uri().'/aj_custom/single-product/hand-with-setting.png' ); ?>"
                                 data-ring="<?php echo esc_url( $image_diamond_carat_size_guide['url'] ); ?>"></div>
                            <?php
                        }
                            ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header">
           <button class="accordion-button" type="button" data-bs-toggle="collapse"
  data-bs-target="#ddCollapseTwo" aria-expanded="true" aria-controls="ddCollapseTwo">
  Cut : <span class="fw-semibold"><?php echo esc_html( $cut_label ); ?></span>
</button>
          </h3>
          <div id="ddCollapseTwo" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <div class="acc-inner">
                <div class="acc-text">
                     <?php if ( $dd_shape_paragraph !== '' ) : ?>
                    <p><?php echo $dd_shape_paragraph; ?></p>
                  <?php endif; ?>
                  <p>Cut has the greatest impact on a diamond's sparkle. Our gemologists evaluate key proportions such as depth, table, and angles - to ensure exceptional brilliance.</p>
                  <p>Use the checklist below to see how your diamond compares to our expert standards.</p>
                 
                </div>
                <div class="acc-visual acc-visual-row">
                  <?php foreach ( $cut_visual_set as $v ) : ?>
                    <div class="acc-visual-icon<?php echo $v['active'] ? ' active' : ''; ?>">
                        <?php
                        if($v['active']){
                            echo '<div class="acc-visual-head">Your <br/>Diamond</div>';
                        }
                        ?>
                     
                      <img src="<?php echo esc_url( $cut_icon_base . $v['file'] ); ?>" alt="<?php echo esc_attr( $v['label'] ); ?> cut diamond" />
                      <div class="acc-visual-label"><?php echo esc_html( $v['label'] ); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
  data-bs-target="#ddCollapseThree" aria-expanded="true" aria-controls="ddCollapseThree">
  Color : <span class="fw-semibold"><?php echo esc_html( $color_label ); ?></span>
</button>
          </h3>
          <div id="ddCollapseThree" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <div class="acc-inner">
                <div class="acc-text">
                    <?php if ( $dd_color_paragraph !== '' ) : ?>
                    <p><?php echo $dd_color_paragraph; ?></p>
                  <?php endif; ?>
                 <?php
                if($is_colored){
                    $dd_fancy_color_info = array(
                        'Green'  => "Symbolizing new beginnings and growth, fancy green diamonds are adored by nature lovers for resembling the beauty of the wild.",
                        'Pink'   => "Fancy pink diamonds are often worn by celebrities as they are the height of luxury and incredibly rare. Representing love, happiness, and romance, pink diamonds are a uniquely beautiful choice for engagement rings. Available in a variety of shapes, these dazzling gemstones offer exceptional brilliance and timeless elegance.",
                        'Orange' => "A warm, vibrant gemstone that radiates joy and positivity, fancy orange diamonds range from soft peachy tones to vivid, intense orange. Their unique color makes them a bold and eye-catching choice.",
                        'Purple' => "Fancy purple diamonds often display subtle undertones of pink, gray, or violet, adding to their depth and beauty. Rare and sophisticated, they are prized for their unique appearance and luxurious appeal.",
                        'Blue'   => "Among the rarest diamonds in the world, fancy blue diamonds range from soft icy blue to deep ocean tones. Often featuring secondary hues of violet, gray, or green, these extraordinary gems are admired for their elegance and unmatched beauty.",
                        'Black'  => "Known for their bold and mysterious appearance, fancy black diamonds owe their rich color to natural inclusions within the stone. Symbolizing strength, power, and individuality, they make a striking and modern choice for jewelry.",
                        'Yellow' => "Fancy yellow diamonds, also known as canary diamonds, range from soft lemon shades to vivid, intense yellow. Their bright, cheerful color symbolizes happiness, optimism, and warmth.",
                        'Red'    => "Among the rarest diamonds ever discovered, fancy red diamonds are prized for their exceptional beauty and unmatched rarity. Their rich, vibrant color symbolizes passion, strength, and enduring love, making them one of the world's most sought-after gemstones.",
                        'Gray'   => "Fancy gray diamonds offer a sophisticated blend of modern elegance and understated beauty. Ranging from light silver to deep charcoal, these unique gemstones are perfect for those seeking a refined and contemporary look.",
                        'Brown'  => "Fancy brown diamonds, often known as champagne or cognac diamonds, are admired for their warm, earthy tones and timeless elegance. Their rich color and affordability make them a distinctive and stylish choice for fine jewelry.",
                    );

                    // $color is already ucfirst()'d earlier in the file (ucfirst($oc_color[...])),
                    // so normalize case defensively in case a lookup value comes through differently.
                    $dd_fancy_color_key = ucfirst( strtolower( trim( $color ) ) );

                    if ( isset( $dd_fancy_color_info[ $dd_fancy_color_key ] ) ) {
                        ?>
                        <p><?php echo esc_html( $dd_fancy_color_info[ $dd_fancy_color_key ] ); ?></p>
                        <?php
                    }
                }else{
                    ?>
                    <p>Diamond color is graded from D (Colorless) to Z. While colorless diamonds are the rarest, many buyers prefer near-colorless or warmer diamonds for their value and appearance. </p>
                    <p>Our gemologists review the following color characteristics before recommending a diamond:</p>
                    <?php
                }
                ?>
                  
                  
                </div>
                <div class="acc-visual acc-visual-row  oc-color-p">
                  <?php foreach ( $color_visual_set as $v ) : ?>
                    <div class="acc-visual-icon<?php echo $v['active'] ? ' active' : ''; ?>">
                         <?php
                        if($v['active']){
                            echo '<div class="acc-visual-head">Your <br/>Diamond</div>';
                        }
                        ?>
                      <img src="<?php echo esc_url( $color_icon_base . $v['file'] ); ?>" alt="<?php echo esc_attr( $v['label'] ); ?> color diamond" />
                      <div class="acc-visual-label"><?php echo esc_html( $v['label'] ); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h3 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
  data-bs-target="#ddCollapseFour" aria-expanded="true" aria-controls="ddCollapseFour">
  Clarity : <span class="fw-semibold"><?php echo esc_html( $clarity_label ); ?></span>
</button>
          </h3>
          <div id="ddCollapseFour" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <div class="acc-inner">
                <div class="acc-text">
                    <?php if ( $dd_clarity_paragraph !== '' ) : ?>
                    <p><?php echo $dd_clarity_paragraph; ?></p>
                  <?php endif; ?>
                  <p>Our gemologists recommend choosing an eye-clean diamond with no durability concerns. While clarity preferences vary by budget and shape, we avoid inclusions such as cavities that could weaken the diamond.</p>
                  <p>Use the checklist below to see how your diamond compares to our expert standards. For a professional assessment, contact one of our gemologists before purchasing.</p>
                  
                </div>
                <div class="acc-visual acc-visual-row">
                  <?php foreach ( $clarity_visual_set as $v ) : ?>
                    <div class="acc-visual-icon<?php echo $v['active'] ? ' active' : ''; ?>">
                         <?php
                        if($v['active']){
                            echo '<div class="acc-visual-head">Your <br/>Diamond</div>';
                        }
                        ?>
                      <img src="<?php echo esc_url( $clarity_icon_base . $v['file'] ); ?>" alt="<?php echo esc_attr( $v['label'] ); ?> clarity diamond" />
                      <div class="acc-visual-label"><?php echo esc_html( $v['label'] ); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /accordion -->
    </div>
  </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.innerWidth <= 768) {
        document.querySelectorAll('#ddAccordionExample .accordion-collapse.show').forEach(function (el) {
            const collapseInstance = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapseInstance.hide();
        });
        document.querySelectorAll('#ddAccordionExample .accordion-button').forEach(function (btn) {
            btn.classList.add('collapsed');
            btn.setAttribute('aria-expanded', 'false');
        });
    }
});
</script>
<div class="bay-section-new">
  <div class="container text-center">
    <h2 class="bay-heading-new text-capitalize">Built around you, not the retailer</h2>
    <p class="bay-subline-new">
      <?php
      
        echo 'We ll let <a href="/reviews/">our customers</a> speak for us.';  
      
      ?>
      
    </p>

    <div class="row features-row-new">
      <div class="col-lg-3 col-6 feature-item-new">
        <div class="feature-icon-new">
          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/bay-section-new/source-pricing.svg" alt="Direct-from-source pricing" />
        </div>
        <div class="feature-title-new">Direct-from-source pricing</div>
        <p class="feature-desc-new">No middlemen. Just better value.</p>
      </div>

      <div class="col-lg-3 col-6 feature-item-new">
        <div class="feature-icon-new">
          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/bay-section-new/price-match.svg" alt="Price match guarantee" />
        </div>
        <div class="feature-title-new">Price match guarantee</div>
        <p class="feature-desc-new">Get the best diamond at the best price.</p>
      </div>

      <div class="col-lg-3 col-6 feature-item-new">
        <div class="feature-icon-new">
          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/bay-section-new/risk-free-return.svg" alt="30-day risk-free returns" />
        </div>
        <div class="feature-title-new"><?php echo '30'; ?>-day risk-free returns</div>
        <p class="feature-desc-new">Shop with confidence &mdash; easy returns, no hassle.</p>
      </div>

      <div class="col-lg-3 col-6 feature-item-new">
        <div class="feature-icon-new">
          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/bay-section-new/certified-expert-guidance.svg" alt="Certified expert guidance" />
        </div>
        <div class="feature-title-new">Certified expert guidance</div>
        <p class="feature-desc-new">Real gemologists, no sales pressure.</p>
      </div>
    </div>
    <div class="bay-video-container">
    <video
                    class="img-fluid"
                    autoplay="autoplay"
                    loop="loop"
                    muted="muted">
                    <source
                        src="<?php echo get_site_url(); ?>/wp-content/uploads/ls_custom/loose-grown-diamond-banner-video.mp4"
                        type="video/mp4" />
                </video>
     <!-- <div class="video-wrapper">
        <img id="posterImg" src="<?php echo get_template_directory_uri();?>/assets/image/melee-diamond-new/video-poster.png" alt="Diamond jewelry on hand" class="bay-poster-img" />
        <video id="videoPlayer" class="bay-video d-none" controls muted>
          <source src="<?php echo get_site_url(); ?>/wp-content/uploads/ls_custom/loose-grown-diamond-banner-video.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
        <button id="playBtn" class="play-btn-new" aria-label="Play video">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22">
            <path d="M8 5v14l11-7z"/>
          </svg>
        </button>
      </div> -->
    </div>
  </div>
</div>

<!-- Related Product -->
  <div class="oc-related-product pt-5">
    <div class="container">
      <h2 class="text-center fw-600 pt-2  pb-3 single_rt_product">Similar Diamonds</h2>
      <div
        id="ls-similar-diamonds-root"
        data-shape="<?php echo esc_attr($diamond->shape ?? ''); ?>"
        data-exclude-sku="<?php echo esc_attr($diamond->sku ?? ''); ?>"
        data-colored="<?php echo $is_colored ? '1' : '0'; ?>"
        data-is-sale="<?php echo $is_sale_page ? '1' : '0'; ?>"
        data-cut="<?php echo esc_attr($diamond->cut ?? ''); ?>"
        data-color="<?php echo esc_attr($diamond->color ?? ''); ?>"
        data-clarity="<?php echo esc_attr($diamond->clarity ?? ''); ?>"
        data-carat="<?php echo esc_attr($diamond->carat ?? ''); ?>"
        data-certificate-type="<?php echo esc_attr($diamond->certificate_type ?? ''); ?>"
        data-table="<?php echo esc_attr($diamond->av_table ?? ''); ?>"
        data-depth="<?php echo esc_attr($diamond->depth ?? ''); ?>"
        data-lwratio="<?php echo esc_attr($diamond->lwratio ?? ''); ?>"
      ></div>
    </div>
  </div>


<div class="section" style="background-color: #F7F9FB;">
    <?php
single_product_what_customer_say();
?>

</div>

<?php
single_diomond_faq();
?>


   <script>
  // Values injected by PHP (keep this block in the PHP template)
  window.LS_PRODUCT_CONFIG = {
    defaultInventoryType: '<?php echo esc_js($inventory_type); ?>',
    iid: '<?php echo esc_js($diamond->id); ?>',
    action: '<?php echo $is_colored ? "ls_add_to_cart_c" : "ls_add_to_cart"; ?>',
    ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
    nonce: '<?php echo esc_js(wp_create_nonce('ls_add_to_cart_nonce')); ?>'
  };
</script>

<script>
(function () {
  function init() {
    var form = document.querySelector('.product_information form.cart');
    if (!form) return;

    var cfg = window.LS_PRODUCT_CONFIG || {};
    var buttons = form.querySelectorAll('button[type="button"]');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', async function (e) {
        e.preventDefault();

        var clickedType = btn.dataset.type || cfg.defaultInventoryType;
        var pidInput = form.querySelector('input[name="add-to-cart"]');
        var pid = pidInput ? pidInput.value : '';
        var loader = btn.querySelector('.single_p_loader');

        btn.disabled = true;
        if (loader) loader.classList.remove('d-none');

        try {
          var ajaxObj = window.ls_ajax_object || {};
          var ajaxUrl = ajaxObj.ajax_url || cfg.ajaxUrl;
          var nonce = ajaxObj.nonce || cfg.nonce;

          var formData = new FormData();
          formData.append('action', cfg.action);
          formData.append('product_id', pid);
          formData.append('iid', cfg.iid);
          formData.append('quantity', 1);
          formData.append('type', clickedType);
          if (nonce) formData.append('nonce', nonce);

          var resp = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
          });
          var json = await resp.json();

          if (!resp.ok || (json && json.success === false)) {
            var msg = (json && (json.data || json.message)) ||
              'Failed to add to cart. Please try again.';
            console.error('Add to cart failed', msg);
            alert(msg);
            return;
          }

          var data = json.success ? json.data : json;
          if (data.status) {
            window.location.href = data.redirect || '/cart';
          } else {
            console.error('Add to cart failed', data);
            alert(data.content || 'Failed to add to cart. Please try again.');
          }
        } catch (err) {
          console.error('API error', err);
          alert('Failed to add to cart due to a network error. Please try again.');
        } finally {
          btn.disabled = false;
          if (loader) loader.classList.add('d-none');
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>


<?php if ( $render_header_footer ) { get_footer('shop'); } ?>
<?php
}
