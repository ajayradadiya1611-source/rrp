<?php
/**
 * Cart Page (React) — data builder + AJAX endpoints.
 *
 * lgd_cart_page_get_data() reproduces every value the old PHP template
 * computed, as plain JSON for the React app. Price strings are sent as
 * ready-made HTML (wc_price / cartregularprice / cartwasrprice) so the
 * store's currency formatting stays in one place: PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Capture echo-ing helpers into a string. */
function lgd_cart_capture( callable $fn, ...$args ) {
    ob_start();
    call_user_func_array( $fn, $args );
    return trim( ob_get_clean() );
}

function lgd_cart_engraving_fonts() {
    if ( function_exists( 'ls_get_engraving_fonts' ) ) {
        return ls_get_engraving_fonts();
    }
    return array(
        'Arial'              => array( 'label' => 'Arial',              'style' => 'font-family:Arial, sans-serif;' ),
        'Elephant'           => array( 'label' => 'Elephant',           'style' => 'font-family:Georgia,serif; font-weight:900; letter-spacing:1px;' ),
        'Brush Script MT'    => array( 'label' => 'Brush Script',       'style' => 'font-family:"Brush Script MT", cursive; font-style:italic;' ),
        'Footlight MT Light' => array( 'label' => 'Footlight',          'style' => 'font-family:"Footlight MT Light", serif;' ),
        'Bradley Hand ITC'   => array( 'label' => 'Bradley Hand ITC',   'style' => 'font-family:"Bradley Hand ITC", cursive; font-size:13px;' ),
        'Copperplate Gothic' => array( 'label' => 'Copperplate Gothic', 'style' => 'font-family:"Copperplate Gothic Light", serif; font-size:11px; letter-spacing:1px;' ),
        'Lucida Calligraphy' => array( 'label' => 'Lucida Calligraphy', 'style' => 'font-family:"Lucida Calligraphy", cursive; font-style:italic;' ),
    );
}

/**
 * Build the full cart state.
 */
function lgd_cart_page_get_data() {
    // Lets is_cart()-gated filters (Extend "Product / Term" rows etc.)
    // know this is the cart page even when called from admin-ajax.
    if ( ! defined( 'LGD_CART_PAGE_AJAX' ) ) {
        define( 'LGD_CART_PAGE_AJAX', true );
    }

    $cart = WC()->cart;
    if ( ! $cart ) {
        return array( 'items' => array(), 'empty' => true );
    }

    $fonts          = lgd_cart_engraving_fonts();
    $all_engravings = WC()->session ? WC()->session->get( 'ls_engraving', array() ) : array();
    $can_checkout   = true;
    $items          = array();

    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        if ( ! $_product ) {
            continue;
        }
        $oc_product = wc_get_product( $cart_item['product_id'] );

        $product_permalink = apply_filters(
            'woocommerce_cart_item_permalink',
            $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
            $cart_item,
            $cart_item_key
        );
        $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
        $thumbnail    = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

        // Certificate
        $certificate_type = null;
        if ( isset( $cart_item['diamond_data'] ) && isset( $cart_item['diamond_data']->certificate_type ) ) {
            $certificate_type = intval( $cart_item['diamond_data']->certificate_type );
        }

        // Stock
        $out_of_stock = $oc_product && ! $oc_product->is_in_stock();
        if ( $out_of_stock ) {
            $can_checkout = false;
        }

        // Meta rows (Upgrade rows are shown as cards instead)
        $meta = array();
        if ( function_exists( 'get_cart_item_data_array' ) ) {
            foreach ( get_cart_item_data_array( $cart_item, true ) as $data ) {
                $type = 'text';
                $meta[] = array(
                    'key'   => wp_strip_all_tags( $data['key'] ),
                    'html'  => wp_kses_post( ! empty( $data['display'] ) ? $data['display'] : $data['value'] ),
                    'type'  => $type,
                );
            }
        }

        // Engraving
        $saved = $all_engravings[ $cart_item_key ] ?? array();
        $eng_text = $saved['text'] ?? '';
        $eng_font = $saved['font'] ?? '';

        // Quantity
        $qty_box = ! empty( $cart_item['is_qty_box'] );
        $max_qty = $_product->get_max_purchase_quantity();
        if ( $_product->is_sold_individually() ) {
            $max_qty = 1;
        }

        // Remove link — built exactly like the old cart.php, then passed
        // through woocommerce_cart_item_remove_link.
        $default_remove = sprintf(
            '<a role="button" class="text-decoration-none" href="%s" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart-key="%s"><span class="d-block mt-3 fw-700">%s</span></a>',
            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
            esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
            esc_attr( $_product->get_id() ),
            esc_attr( $_product->get_sku() ),
            esc_attr( $cart_item_key ),
            esc_html__( 'Remove', 'woocommerce' )
        );
        $remove_html = apply_filters( 'woocommerce_cart_item_remove_link', $default_remove, $cart_item_key );

        $items[] = array(
            'removeHtml'       => $remove_html,
            'key'              => $cart_item_key,
            'productId'        => $_product->get_id(),
            'sku'              => $_product->get_sku(),
            'nameHtml'         => wp_kses_post( $product_name ),
            'nameText'         => wp_strip_all_tags( $product_name ),
            'permalink'        => $product_permalink ? esc_url_raw( $product_permalink ) : '',
            'thumbnailHtml'    => $thumbnail,
            'afterNameHtml'    => lgd_cart_capture( 'do_action', 'oc_woocommerce_after_cart_item_name', $cart_item, $cart_item_key ),
            'meta'             => $meta,
            'outOfStock'       => $out_of_stock,
            'noCertificate'    => ( $certificate_type === 0 || $certificate_type === 4 ),
            'isEngraving'      => ! empty( $cart_item['is_engraving_product'] ),
            'engraving'        => array( 'text' => $eng_text, 'font' => $eng_font ),
            'qty'              => (int) $cart_item['quantity'],
            'qtyBox'           => $qty_box,
            'maxQty'           => $max_qty > 0 ? (int) $max_qty : 0,
            // Same as old cart.php: cartregularprice( ..., true ) + cartwasrprice()
            'priceHtml'        => lgd_cart_capture( 'cartregularprice', $cart_item, $_product, $cart_item_key, true ),
            'wasPriceHtml'     => function_exists( 'cartwasrprice' ) ? lgd_cart_capture( 'cartwasrprice', $cart_item, $_product, $cart_item_key ) : '',
        );
    }

    // Coupons
    $coupon_label_map = array( 'lgd_crypto2' => 'Crypto Discount', 'lgd_wire2' => 'Wire Discount' );
    $coupons = array();
    foreach ( $cart->get_applied_coupons() as $code ) {
        $label = $coupon_label_map[ strtolower( $code ) ] ?? null;
        $coupons[] = array(
            'code'       => $code,
            'label'      => $label,
            'amountHtml' => wc_price( $cart->get_coupon_discount_amount( $code ) ),
        );
    }
    $real_coupons = array_diff( $cart->get_applied_coupons(), array( 'lgd_wire2', 'lgd_crypto2' ) );

    // Free-shipping progress
    $threshold  = 500;
    $cart_total = $cart->get_subtotal() - $cart->get_discount_total();

    // Notices (printed once, then cleared)
    $notices = function_exists( 'wc_print_notices' ) ? lgd_cart_capture( 'wc_print_notices' ) : '';

    $font_list = array();
    foreach ( $fonts as $value => $font ) {
        $font_list[] = array( 'value' => $value, 'label' => $font['label'], 'style' => $font['style'] );
    }
    return array(
        'empty'          => $cart->is_empty(),
        'items'          => $items,
        'subtotalHtml'   => $cart->get_cart_subtotal(),
        'coupons'        => $coupons,
        'hasRealCoupon'  => ! empty( $real_coupons ),
        'shippingHtml'   => $cart->get_shipping_total() > 0 ? wc_price( $cart->get_shipping_total() ) : 'Free',
        'totalHtml'      => wc_price( $cart->get_total( 'edit' ) - $cart->get_total_tax() ),
        'canCheckout'    => $can_checkout,
        'checkoutUrl'    => wc_get_checkout_url(),
        'cartUrl'        => wc_get_cart_url(),
        'freeShipLeft'   => ( $cart_total < $threshold ) ? number_format( $threshold - $cart_total, 2 ) : '',
        'returnDays'     => defined( 'RETURN_DAYS' ) ? RETURN_DAYS : '7',
        'supportMobile'  => defined( 'SUPPORT_MOBILE' ) ? SUPPORT_MOBILE : '',
        'themeUri'       => get_template_directory_uri(),
        'unboxingVideo'  => 'https://media.loosegrowndiamond.com/wait-till-you-see-inside-this-box.mp4',
        'engravingFonts' => $font_list,
        'noticesHtml'    => $notices,
        'pageTitle'      => lgd_cart_page_title(),
        'cartCount'      => (int) $cart->get_cart_contents_count(),
    );
}

/* ------------------------------------------------------------------
 * AJAX
 * ---------------------------------------------------------------- */

function lgd_cart_ajax_guard() {
    if ( ! check_ajax_referer( 'lgd_cart_page', 'security', false ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed. Please refresh the page.' ), 403 );
    }
    if ( ! WC()->cart ) {
        wp_send_json_error( array( 'message' => 'Cart not available.' ), 400 );
    }
}

add_action( 'wp_ajax_lgd_cart_page_data', 'lgd_cart_page_data_ajax' );
add_action( 'wp_ajax_nopriv_lgd_cart_page_data', 'lgd_cart_page_data_ajax' );
function lgd_cart_page_data_ajax() {
    lgd_cart_ajax_guard();
    WC()->cart->calculate_totals();
    wp_send_json_success( lgd_cart_page_get_data() );
}

add_action( 'wp_ajax_lgd_cart_remove_item', 'lgd_cart_remove_item_ajax' );
add_action( 'wp_ajax_nopriv_lgd_cart_remove_item', 'lgd_cart_remove_item_ajax' );
function lgd_cart_remove_item_ajax() {
    lgd_cart_ajax_guard();
    $key = sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ?? '' ) );
    if ( ! $key || ! WC()->cart->get_cart_item( $key ) ) {
        wp_send_json_error( array( 'message' => 'Item not found.' ) );
    }
    WC()->cart->remove_cart_item( $key );

    // Drop engraving for the removed line
    if ( WC()->session ) {
        $eng = WC()->session->get( 'ls_engraving', array() );
        unset( $eng[ $key ] );
        WC()->session->set( 'ls_engraving', $eng );
    }

    WC()->cart->calculate_totals();

    // Re-validate applied coupons against what's left in the cart, so a
    // coupon that no longer qualifies is dropped from the order summary —
    // e.g. hallo30 (diamonds only, custom_coupon_validation()) once the
    // last diamond is removed. WooCommerce removes invalid coupons here and
    // adds a notice, which the cart shows via noticesHtml.
    WC()->cart->check_cart_coupons();
    WC()->cart->calculate_totals();
    wp_send_json_success( lgd_cart_page_get_data() );
}

add_action( 'wp_ajax_lgd_cart_remove_coupon', 'lgd_cart_remove_coupon_ajax' );
add_action( 'wp_ajax_nopriv_lgd_cart_remove_coupon', 'lgd_cart_remove_coupon_ajax' );
function lgd_cart_remove_coupon_ajax() {
    lgd_cart_ajax_guard();
    $code = wc_format_coupon_code( wp_unslash( $_POST['coupon'] ?? '' ) );
    if ( $code ) {
        WC()->cart->remove_coupon( $code );
        // Auto-apply runs only on woocommerce_cart_loaded_from_session, i.e.
        // at the start of this request while the removed coupon (e.g. fb20)
        // was still blocking it. Re-run it now so the festival coupon
        // (hallo30) applies immediately instead of only after a page refresh.
        // If the user removed hallo30 itself, the session flag set by
        // woocommerce_removed_coupon keeps it from coming back.
        if ( function_exists( 'ls_reapply_festival_coupon_after_remove' ) ) {
            ls_reapply_festival_coupon_after_remove( $code );
        }
        wc_clear_notices();
    }
    WC()->cart->calculate_totals();
    wp_send_json_success( lgd_cart_page_get_data() );
}

/**
 * "Cart - SKU1, SKU2" title from the current cart contents.
 * Returns '' when the cart is empty or has no SKUs (keep the default title).
 * Shared by the Rank Math filter (page load) and the AJAX payload (pageTitle),
 * so the browser tab updates live after an item is removed.
 */
function lgd_cart_page_title() {
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return '';
    }
    $skus = array();
    foreach ( WC()->cart->get_cart() as $item ) {
        $product = $item['data'] ?? null;
        if ( $product && $product->get_sku() ) {
            $skus[] = $product->get_sku();
        }
    }
    return $skus ? 'Cart - ' . implode( ', ', $skus ) : '';
}

add_filter( 'rank_math/frontend/title', function( $title ) {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        $cart_title = lgd_cart_page_title();
        if ( $cart_title ) {
            $title = $cart_title;
        }
    }
    return $title;
} );
