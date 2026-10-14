<?php
/**
 * Category Product Position — module loader
 *
 * wp-admin tool: pick a product category, drag & drop its products into the
 * order you want, and save. The saved order is used by the frontend
 * "Sort by : Best Seller" option (custom/v1/get-products?orderby=best_seller).
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/category-product-position/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/api/routes.php';

add_action( 'admin_menu', function () {
    add_menu_page(
        'Category Product Position',
        'Product Position',
        'manage_woocommerce',
        'lgd-category-product-position',
        'lgd_render_category_product_position_page',
        'dashicons-sort',
        58
    );
} );

function lgd_render_category_product_position_page() {
    echo '<div id="category-product-position-root"></div>';
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( strpos( $hook, 'lgd-category-product-position' ) === false ) {
        return;
    }

    $dist = get_template_directory() . '/out/category-product-position';
    $uri  = get_template_directory_uri() . '/out/category-product-position';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build`
    }

    wp_enqueue_script(
        'lgd-category-product-position',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    wp_localize_script( 'lgd-category-product-position', 'lgdCategoryProductPositionData', array(
        'apiUrl'   => rest_url( 'lgd/v1' ),
        'nonce'    => wp_create_nonce( 'wp_rest' ),
        'adminUrl' => admin_url(),
    ) );

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'lgd-category-product-position',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }
} );
