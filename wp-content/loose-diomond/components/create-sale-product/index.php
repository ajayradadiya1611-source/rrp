<?php
/**
 * Create Sale Product — module loader
 *
 * Lists inventory rows flagged `sale = 1` (from `ls_inventory` and
 * `ls_inventory_c`) and lets an admin create the matching WooCommerce
 * product for any row that doesn't have one yet — same product-creation
 * logic used by the Add To Cart flow (`add_product_to_wc()` /
 * `add_product_to_wc_c()` in inc/ls-addtocart-api.php and
 * inc/ls-addtocart-c-api.php), just triggered manually from wp-admin
 * instead of lazily on first add-to-cart.
 *
 * This is the ONLY file functions.php needs to require for this feature.
 * It wires up:
 *   - the REST API (api/routes.php)
 *   - the wp-admin menu page + enqueue of the built React app (out/create-sale-product)
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/create-sale-product/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API -------------------------------------------------------------
require_once __DIR__ . '/api/routes.php';

// --- Admin page + React app enqueue -----------------------------------
add_action( 'admin_menu', function () {
    add_menu_page(
        'Create Sale Product',
        'Create Sale Product',
        'manage_woocommerce',
        'lgd-create-sale-product',
        'lgd_render_create_sale_product_page',
        'dashicons-cart',
        56
    );
} );

function lgd_render_create_sale_product_page() {
    echo '<div id="create-sale-product-root"></div>';
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( strpos( $hook, 'lgd-create-sale-product' ) === false ) {
        return;
    }

    $dist = get_template_directory() . '/out/create-sale-product';
    $uri  = get_template_directory_uri() . '/out/create-sale-product';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build` in react/
    }

    wp_enqueue_script(
        'lgd-create-sale-product',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    wp_localize_script( 'lgd-create-sale-product', 'lgdCreateSaleProductData', array(
        'apiUrl' => rest_url( 'lgd/v1' ),
        'nonce'  => wp_create_nonce( 'wp_rest' ),
    ) );

    // App.scss is compiled by Vite into a CSS file under dist/assets — enqueue it if present.
    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'lgd-create-sale-product',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }
} );
