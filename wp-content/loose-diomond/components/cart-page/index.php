<?php
/**
 * Cart Page (React) — module loader
 *
 * Replaces the old jQuery/PHP markup in woocommerce/cart/cart.php with a
 * React app. Same build pattern as the theme's other components/*.
 *
 * Wires up:
 *   - api/cart-data.php  — lgd_cart_page_get_data() (builds the full cart
 *                          state as JSON) + admin-ajax actions:
 *                            lgd_cart_page_data     (GET fresh state)
 *                            lgd_cart_remove_item   (remove a line)
 *                            lgd_cart_remove_coupon (remove a coupon)
 *                          Everything else re-uses the existing handlers
 *                          (custom_update_cart_qty, custom_apply_coupon).
 *   - script/style enqueue for out/cart-page on is_cart()
 *   - lgd_render_cart_page() — prints the mount div + initial JSON
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/cart-page/index.php';
 *
 * Build (theme root):  npx vite build --config vites/vite.cart-page.config.js
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/api/cart-data.php';

/**
 * Prints the React mount point with the initial state inlined, so the
 * first paint needs no extra request.
 */
function lgd_render_cart_page() {
    $data = lgd_cart_page_get_data();
    echo '<div id="ls-cart-root"></div>';
    echo '<script>window.LS_CART_INITIAL = ' . wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP ) . ';</script>';

    if ( ! file_exists( get_template_directory() . '/out/cart-page/index.js' ) ) {
        echo '<p style="color:red">Cart app not built — run <code>npx vite build --config vites/vite.cart-page.config.js</code>.</p>';
    }
}

add_action( 'wp_enqueue_scripts', 'lgd_enqueue_cart_page' );

function lgd_enqueue_cart_page() {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }
    if ( WC()->cart && WC()->cart->is_empty() ) {
        return; // cart-empty.php is used instead
    }

    $dist = get_template_directory() . '/out/cart-page';
    $uri  = get_template_directory_uri() . '/out/cart-page';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return;
    }

    wp_enqueue_script( 'ls-cart-page', $uri . '/index.js', array(), filemtime( $dist . '/index.js' ), true );

    // ES module — own scope for bundled React (same as other components).
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( $handle !== 'ls-cart-page' ) {
            return $tag;
        }
        return str_replace( ' src', ' type="module" src', preg_replace( '/\stype=["\'][^"\']*["\']/', '', $tag, 1 ) );
    }, 10, 2 );

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style( 'ls-cart-page-style', $uri . '/assets/index.css', array(), filemtime( $css_file ) );
    }

    wp_localize_script( 'ls-cart-page', 'LS_CART', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonces'  => array(
            'cart'        => wp_create_nonce( 'lgd_cart_page' ),
            'coupon'      => wp_create_nonce( 'custom_coupon_nonce' ),
        ),
    ) );
}
