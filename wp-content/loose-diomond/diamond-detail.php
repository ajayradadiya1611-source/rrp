<?php
/**
 * Template: Diamond Detail Page
 * URL: /diomond/{sku}
 *
 * Fetches the diamond row by SKU (from the URL) and hands off to
 * ls_render_diamond_detail_page() in inc/diamond-detail-render.php,
 * which holds the actual page markup (including the visual-set helper
 * functions — ls_get_cut_visual_set() etc. — so they must NOT be
 * redeclared here). That function is also called directly from
 * woocommerce/single-product.php for products tagged "sale", so both
 * entry points render the exact same page structure.
 */

global $oc_cut, $oc_color, $oc_clarity, $oc_certificate,$inv_color;

// ── Fetch Diamond ─────────────────────────────────────────────────
$sku = sanitize_text_field(get_query_var('diamond_sku'));

if (!$sku) {
    wp_redirect(home_url());
    exit;
}
global $product,$oc_common,$inv_shape_imgids,$inv_intensity_c;
global $wpdb;

$diamond = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM ls_inventory WHERE sku = %s AND status = 0 LIMIT 1",
        $sku
    )
);

// Not a white diamond — check the fancy color diamond table before 404ing.
$is_colored = false;
if (!$diamond) {
    $diamond = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM ls_inventory_c WHERE sku = %s AND status = 0 LIMIT 1",
            $sku
        )
    );
    if ($diamond) {
        $is_colored = true;
    }
}
/*echo "<pre>";
echo $is_colored;
print_r($diamond);
exit;*/
// ── 404 if not found ──────────────────────────────────────────────
if (!$diamond) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    get_template_part('404');
    exit;
}

ls_render_diamond_detail_page( $diamond, $is_colored );
