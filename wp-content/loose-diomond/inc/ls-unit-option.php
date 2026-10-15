<?php
/**
 * Unit option (Carat / Pieces) for variable products.
 *
 * Ported from the old rrpdiamond-child theme (attribute_pa_unit feature).
 *
 * Product meta  ls_unit_option : 10 = None, 0 = Both, 2 = Per Carat, 3 = Per Piece
 * Variation meta pieces        : number of pieces in the variation
 *
 * Flow: product page "Unit" select (attribute_pa_unit) -> add to cart
 * -> cart item data (ls_unit) -> cart price -> order item meta "Unit".
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------
 * 1. Admin: product-level "Unit option" (Product data > General)
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_product_options_general_product_data', 'ls_unit_option_admin_field' );
function ls_unit_option_admin_field() {
    woocommerce_wp_select( array(
        'id'          => 'ls_unit_option',
        'label'       => __( 'Unit option', 'woocommerce' ),
        'description' => __( 'Show a Carat / Pieces unit selector on the product page.', 'woocommerce' ),
        'desc_tip'    => true,
        'options'     => array(
            '10' => __( 'None', 'woocommerce' ),
            '0'  => __( 'Both', 'woocommerce' ),
            '2'  => __( 'Per Carat', 'woocommerce' ),
            '3'  => __( 'Per Piece', 'woocommerce' ),
        ),
    ) );
}

add_action( 'woocommerce_process_product_meta', 'ls_unit_option_admin_save' );
function ls_unit_option_admin_save( $post_id ) {
    if ( isset( $_POST['ls_unit_option'] ) ) {
        update_post_meta( $post_id, 'ls_unit_option', sanitize_text_field( wp_unslash( $_POST['ls_unit_option'] ) ) );
    }
}

/* ---------------------------------------------------------------------
 * 2. Admin: "Pieces" field on each variation
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_variation_options_pricing', 'ls_unit_variation_pieces_field', 10, 3 );
function ls_unit_variation_pieces_field( $loop, $variation_data, $variation ) {
    $parent_id      = wp_get_post_parent_id( $variation->ID );
    $ls_unit_option = get_post_meta( $parent_id, 'ls_unit_option', true );

    if ( '0' === (string) $ls_unit_option || '3' === (string) $ls_unit_option ) {
        woocommerce_wp_text_input( array(
            'id'    => 'pieces[' . $loop . ']',
            'class' => 'short',
            'label' => __( 'Pieces', 'woocommerce' ),
            'value' => get_post_meta( $variation->ID, 'pieces', true ),
        ) );
    }
}

add_action( 'woocommerce_save_product_variation', 'ls_unit_variation_pieces_save', 10, 2 );
function ls_unit_variation_pieces_save( $variation_id, $i ) {
    if ( isset( $_POST['pieces'][ $i ] ) ) {
        update_post_meta( $variation_id, 'pieces', esc_attr( wp_unslash( $_POST['pieces'][ $i ] ) ) );
    }
}

/* ---------------------------------------------------------------------
 * 3. Variation data for the product page script
 * ------------------------------------------------------------------ */
add_filter( 'woocommerce_available_variation', 'ls_unit_available_variation', 10, 3 );
function ls_unit_available_variation( $data, $product, $variation ) {
    $data['ls_pieces'] = (float) get_post_meta( $variation->get_id(), 'pieces', true );
    return $data;
}

/* ---------------------------------------------------------------------
 * 4. Cart item data (separate cart lines for carat / pieces)
 * ------------------------------------------------------------------ */
function ls_unit_allowed( $unit ) {
    return in_array( $unit, array( 'carat', 'pieces' ), true ) ? $unit : '';
}

add_filter( 'woocommerce_add_cart_item_data', 'ls_unit_add_cart_item_data', 20, 3 );
function ls_unit_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    if ( isset( $_REQUEST['attribute_pa_unit'] ) ) {
        $unit = ls_unit_allowed( sanitize_text_field( wp_unslash( $_REQUEST['attribute_pa_unit'] ) ) );
        if ( $unit ) {
            $cart_item_data['ls_unit'] = $unit;
        }
    }
    return $cart_item_data;
}

add_filter( 'woocommerce_get_item_data', 'ls_unit_item_data', 20, 2 );
function ls_unit_item_data( $item_data, $cart_item ) {
    if ( ! empty( $cart_item['ls_unit'] ) ) {
        $item_data[] = array(
            'key'   => __( 'Unit', 'woocommerce' ),
            'value' => ucfirst( $cart_item['ls_unit'] ),
        );
    }
    return $item_data;
}

add_action( 'woocommerce_checkout_create_order_line_item', 'ls_unit_save_order_item_meta', 10, 4 );
function ls_unit_save_order_item_meta( $item, $cart_item_key, $values, $order ) {
    if ( ! empty( $values['ls_unit'] ) ) {
        $item->add_meta_data( 'Unit', ucfirst( $values['ls_unit'] ), true );
    }
}

/* ---------------------------------------------------------------------
 * 5. Cart price: "pieces" = variation price / pieces
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_before_calculate_totals', 'ls_unit_alter_cart_price', 9999 );
function ls_unit_alter_cart_price( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    foreach ( $cart->get_cart() as $cart_item ) {
        $product = $cart_item['data'];
        if ( ! $product || $product->get_meta( '_ls_unit_applied' ) ) {
            continue;
        }

        $ls_unit_option = (string) get_post_meta( $cart_item['product_id'], 'ls_unit_option', true );
        $unit           = $cart_item['ls_unit'] ?? '';

        if ( 'pieces' !== $unit && '3' !== $ls_unit_option ) {
            continue;
        }

        $variation_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
        $pieces       = (float) get_post_meta( $variation_id, 'pieces', true );

        if ( $pieces > 0 ) {
            $product->set_price( (float) $product->get_price() / $pieces );
            $product->add_meta_data( '_ls_unit_applied', 1, true ); // in-memory guard against double division
        }
    }
}

/* ---------------------------------------------------------------------
 * 6. "/ per carat" suffix on prices of per-carat products
 * ------------------------------------------------------------------ */
add_filter( 'woocommerce_get_price_html', 'ls_unit_price_suffix', 9999, 2 );
function ls_unit_price_suffix( $price_html, $product ) {
    if ( ! $product || ! is_a( $product, 'WC_Product' ) || '' === $product->get_price() ) {
        return $price_html;
    }
    if ( '2' === (string) get_post_meta( $product->get_id(), 'ls_unit_option', true ) ) {
        $price_html .= ' <span class="ls_perval">/ per carat</span>';
    }
    return $price_html;
}
