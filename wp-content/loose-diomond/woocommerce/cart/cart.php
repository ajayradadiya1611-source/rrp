<?php
/**
 * Cart Page — React version
 *
 * The whole cart UI (items, upgrades,
 * coupons, order summary, trust block, modals, loader) is rendered by
 * the React app in components/cart-page/react (built to out/cart-page).
 *
 * This template only:
 *   - fires the standard WooCommerce hooks (so notices / plugins still work)
 *   - prints the mount point with the initial cart state inlined as JSON
 *     (no loading flash on first paint — later refreshes use AJAX)
 *
 * Data is built by lgd_cart_page_get_data() in
 * components/cart-page/api/cart-data.php.
 *
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
do_action( 'woocommerce_before_cart_collaterals' );

if ( is_cart() && function_exists( 'lgd_render_cart_page' ) ) {
    lgd_render_cart_page();
}

do_action( 'woocommerce_after_cart' );
