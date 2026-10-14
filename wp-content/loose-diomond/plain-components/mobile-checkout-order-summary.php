<?php
/**
 * Mobile Checkout Order Summary Toggle
 *
 * On mobile, shows a collapsed bar above the checkout form:
 * "🛍 X items ⌄        $Total"
 * Tapping it expands/collapses the WooCommerce order review table
 * (uses Bootstrap's collapse JS, which is already loaded on the site).
 *
 * Hook: woocommerce_before_checkout_form
 */
add_action( 'woocommerce_before_checkout_form', 'ls_mobile_checkout_order_summary', 5 );
function ls_mobile_checkout_order_summary() {

    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return;
    }

    $item_count = WC()->cart->get_cart_contents_count();
    $item_label = ( $item_count === 1 ) ? 'item' : 'items';
    $cart_total = WC()->cart->get_total();
    ?>
    <div class="ls-mobile-order-summary d-md-none">
        <button type="button"
                class="ls-mobile-order-summary__toggle"
                data-bs-toggle="collapse"
                data-bs-target="#ls-mobile-order-review"
                aria-expanded="false"
                aria-controls="ls-mobile-order-review">
            <span class="ls-mobile-order-summary__left">
                <svg class="ls-mobile-order-summary__bag" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 7h12l-1 13H7L6 7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M9 7V6a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                <?php echo esc_html( $item_count ); ?> <?php echo esc_html( $item_label ); ?>
                <svg class="ls-mobile-order-summary__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="ls-mobile-order-summary__total"><?php echo wp_kses_post( $cart_total ); ?></span>
        </button>

        <div class="collapse" id="ls-mobile-order-review">
            <div class="ls-mobile-order-summary__body">
                <div class="oc-checkout-order-review">
                    <?php woocommerce_order_review(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Flag the checkout page body so the theme's normal #order_review block
 * can be hidden on mobile (it is already rendered above, inside the
 * collapse) and shown normally again on desktop via CSS breakpoints.
 */
add_filter( 'body_class', function ( $classes ) {
    if ( is_checkout() && ! is_wc_endpoint_url() ) {
        $classes[] = 'ls-has-mobile-order-summary';
    }
    return $classes;
} );