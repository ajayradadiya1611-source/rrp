<?php
/**
 * Zero Price Products — module loader
 *
 * wp-admin tool that lists every WooCommerce product whose price is 0
 * (or empty) and lets an admin permanently delete them — single or bulk.
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/zero-price-products/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/api/routes.php';

add_action( 'admin_menu', function () {
    add_menu_page(
        'Zero Price Products',
        'Zero Price Products',
        'manage_woocommerce',
        'lgd-zero-price-products',
        'lgd_render_zero_price_products_page',
        'dashicons-warning',
        57
    );
} );

function lgd_render_zero_price_products_page() {
    echo '<div id="zero-price-products-root"></div>';
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( strpos( $hook, 'lgd-zero-price-products' ) === false ) {
        return;
    }

    $dist = get_template_directory() . '/out/zero-price-products';
    $uri  = get_template_directory_uri() . '/out/zero-price-products';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build`
    }

    wp_enqueue_script(
        'lgd-zero-price-products',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    wp_localize_script( 'lgd-zero-price-products', 'lgdZeroPriceProductsData', array(
        'apiUrl'   => rest_url( 'lgd/v1' ),
        'nonce'    => wp_create_nonce( 'wp_rest' ),
        'adminUrl' => admin_url(),
    ) );

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'lgd-zero-price-products',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }
} );
