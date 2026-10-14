<?php
/**
 * Delete Out of Stock Inventory — module loader
 *
 * This is the ONLY file functions.php needs to require for this feature.
 * It wires up:
 *   - the REST API (api/routes.php)
 *   - the wp-admin menu page + enqueue of the built React app (out/delete-out-of-stock-inventory)
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/delete-out-of-stock-inventory/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API -------------------------------------------------------------
require_once __DIR__ . '/api/routes.php';

// --- Admin page + React app enqueue -----------------------------------
add_action( 'admin_menu', function () {
    add_menu_page(
        'Delete Out of Stock Inventory',
        'Delete OOS Inventory',
        'manage_woocommerce',
        'lgd-delete-oos-inventory',
        'lgd_render_delete_oos_inventory_page',
        'dashicons-trash',
        56
    );
} );

function lgd_render_delete_oos_inventory_page() {
    echo '<div id="delete-out-of-stock-inventory-root"></div>';
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
   
    if ( strpos( $hook, 'lgd-delete-oos-inventory' ) === false ) {
        return;
    }

    $dist = get_template_directory() . '/out/delete-out-of-stock-inventory';
    $uri  = get_template_directory_uri() . '/out/delete-out-of-stock-inventory';

   
    wp_enqueue_script(
        'lgd-delete-oos-inventory',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    wp_localize_script( 'lgd-delete-oos-inventory', 'lgdDeleteInventoryData', array(
        'apiUrl' => rest_url( 'lgd/v1' ),
        'nonce'  => wp_create_nonce( 'wp_rest' ),
    ) );

    // App.scss is compiled by Vite into a CSS file under dist/assets — enqueue it if present.
    $css_file = $dist . '/assets/index.css';

        wp_enqueue_style(
            'lgd-delete-oos-inventory',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    
} );
