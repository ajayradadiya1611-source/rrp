<?php

global $is_festival_banner, $is_discount_enabled, $discount_object, $discount_coupon_code, $direct_discount, $inventory_banner, $inventory_banner_mob;

$is_festival_banner    = get_field('enable_festival_banner', 'option');
$is_discount_enabled   = get_field('enable_festival_discount', 'option');
$discount_object       = get_field('festival_coupon_code', 'option');
$discount_coupon_code  = !empty($discount_object) && isset($discount_object->post_title) ? $discount_object->post_title : '';
$direct_discount       = !empty(get_field('festival_coupon_discount', 'option')) ? get_field('festival_coupon_discount', 'option') : 0;
$inventory_banner      = get_field('festival_inventory_banner', 'option');
$inventory_banner_mob  = get_field('festival_inventory_banner_mob', 'option');



add_filter( 'woocommerce_package_rates', 'ls_shpng_woocommerce_package_rates', 999, 2 );

function ls_shpng_woocommerce_package_rates( $rates, $package ) {

    $sub_total = WC()->cart->cart_contents_total;

    // Check if any applied coupon has free shipping enabled
    $is_free_shipping = false;

    $applied_coupons = WC()->cart->get_applied_coupons();

    foreach ( $applied_coupons as $coupon_code ) {

        $coupon = new WC_Coupon( $coupon_code );

        if ( $coupon->get_free_shipping() ) {
            $is_free_shipping = true;
            break;
        }
    }

    // Continue only if no free shipping coupon applied
    if ( ! $is_free_shipping ) {

        // For orders below 500
        if ( $sub_total < 500 ) {

            $items       = WC()->cart->get_cart();
            $items_count = count( $items );

            $cnt_cp = 0;

            // Count products having product tag "custom"
            if ( $items_count >= 1 ) {

                foreach ( $items as $item => $values ) {

                    $product_id = $values['product_id'];

                    // Check product tag
                    if ( has_term( 'custom', 'product_tag', $product_id ) ) {
                        $cnt_cp++;
                    }
                }
            }

            // If all products have "custom" tag
            if ( $cnt_cp == $items_count ) {

                // Hide flat rate shipping
                unset( $rates['flat_rate:7'] );

            } else {

                // Hide free shipping
                unset( $rates['free_shipping:4'] );
            }

        } else {

            // For subtotal >= 500 hide flat rate
            unset( $rates['flat_rate:7'] );
        }
    }

    return $rates;
}


/*add_action('woocommerce_cart_calculate_fees', 'custom_payment_gateway_fee_discount', 20);

function custom_payment_gateway_fee_discount($cart) {

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    if (!$cart || $cart->is_empty()) {
        return;
    }

    $payment_method = WC()->session->get('chosen_payment_method');

    // Calculate on subtotal + shipping
    $subtotal = $cart->get_subtotal();
    $shipping = $cart->get_shipping_total();
    $amount = $subtotal;


    if (in_array($payment_method, array('lgdcrypto', 'bacs'), true)) {

        $discount = $amount * 0.02;

        if ($payment_method === 'lgdcrypto') {
            $label = 'Crypto Discount';
        } elseif ($payment_method === 'bacs') {
            $label = 'Wire  Discount';
        } else {
            $label = 'Payment Discount';
        }

        $cart->add_fee(
            __($label, 'woocommerce'),
            $discount,
            false
        );
    }
}*/

// Auto-apply/remove the gateway discount coupon based on selected payment method
add_action('woocommerce_before_calculate_totals', 'custom_payment_gateway_coupon_discount', 5000);

function custom_payment_gateway_coupon_discount($cart) {

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    if (!$cart) {
        return;
    }

    static $running = false;
    if ($running) {
        return;
    }
    $running = true;

    $payment_method = WC()->session->get('chosen_payment_method');

    $gateway_coupons = array(
        'lgdcrypto' => 'lgd_crypto2',
        'bacs'      => 'lgd_wire2',
    );

    $applied_coupons = $cart->get_applied_coupons();

    foreach ($gateway_coupons as $gateway => $coupon_code) {
        $should_have = ($payment_method === $gateway);
        $has_it      = in_array($coupon_code, $applied_coupons, true);

        if ($should_have && !$has_it) {
            $cart->apply_coupon($coupon_code);
        } elseif (!$should_have && $has_it) {
            $cart->remove_coupon($coupon_code);
        }
    }

    $running = false;
}



/**
 * Called right after the user removes a coupon via AJAX (React cart / checkout).
 * If the removed coupon is NOT the festival coupon (e.g. FB20), the festival
 * coupon (hallo30) should come back immediately — even if it had been kicked
 * out earlier when FB20 was applied (an individual-use coupon removes the
 * others, which also set the "removed by user" flag).
 */
function ls_reapply_festival_coupon_after_remove($removed_code) {
    global $discount_coupon_code, $is_discount_enabled;
    if (!$is_discount_enabled || empty($discount_coupon_code) || !WC()->cart || !WC()->session) return;

    if (wc_format_coupon_code($removed_code) !== wc_format_coupon_code($discount_coupon_code)) {
        WC()->session->__unset('ls_auto_apply_discount');
    }
    WC()->cart->calculate_totals();
    ls_auto_apply_discount_coupon(WC()->cart);
    wc_clear_notices();
    WC()->cart->calculate_totals();
}
