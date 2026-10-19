<?php
/**
 * Diamond Price Calculator — AJAX handler (ported from the old theme).
 *
 * Reads the price from the existing custom tables `tbl_size` and `tbl_price`
 * (same tables the old theme's admin screens maintain).
 *
 * Request: admin-ajax.php?action=submit_price_calculator_request
 * POST   : shape, size (carat), color, clarity, disc, pc_lab, pc_prop, frm_name
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function submit_price_calculator_request() {
    global $wpdb;

    $frm_name   = isset( $_POST['frm_name'] ) ? sanitize_text_field( wp_unslash( $_POST['frm_name'] ) ) : '';
    $shape_id   = isset( $_POST['shape'] )    ? sanitize_text_field( wp_unslash( $_POST['shape'] ) )    : '';
    $user_size  = isset( $_POST['size'] )     ? sanitize_text_field( wp_unslash( $_POST['size'] ) )     : '';
    $color_id   = isset( $_POST['color'] )    ? sanitize_text_field( wp_unslash( $_POST['color'] ) )    : '';
    $clarity_id = isset( $_POST['clarity'] )  ? sanitize_text_field( wp_unslash( $_POST['clarity'] ) )  : '';
    $discount   = isset( $_POST['disc'] )     ? sanitize_text_field( wp_unslash( $_POST['disc'] ) )     : '';

    $status            = false;
    $submit_message    = '';
    $price_per_carat   = '';
    $total_price_carat = '';

    if ( '' === $shape_id ) {
        $submit_message = 'You must select a value for shape.';
    } elseif ( '' === $user_size || ! is_numeric( $user_size ) ) {
        $submit_message = 'You must select a value for carat.';
    } elseif ( '' === $color_id ) {
        $submit_message = 'You must select a value for color.';
    } elseif ( '' === $clarity_id ) {
        $submit_message = 'You must select a value for clarity.';
    } elseif ( (float) $user_size >= 8 ) {
        $submit_message = "THAT'S HUUUUGE! Contact us for an estimate on 8+ Carat diamonds.";
    } else {
        $carat = (float) $user_size;

        // "-10" lowers the price by 10%, "10" raises it by 10% (same as the old theme)
        $minus    = ( false !== strpos( $discount, '-' ) );
        $discount = (float) trim( str_replace( '-', '', $discount ) );

        if ( $discount < 0.5 || $discount > 99 ) {
            $submit_message = 'Discount allow between 0.5 to 99 ';
        } else {
            $size_id = $wpdb->get_var( $wpdb->prepare(
                'SELECT id FROM `tbl_size` WHERE `from_p` <= %f AND `to_p` >= %f LIMIT 1',
                $carat,
                $carat
            ) );

            $price = null;
            if ( $size_id ) {
                $price = $wpdb->get_var( $wpdb->prepare(
                    'SELECT price FROM `tbl_price` WHERE `shape_id` = %d AND `size_id` = %d AND `clarity_id` = %d AND `color_id` = %d LIMIT 1',
                    (int) $shape_id,
                    (int) $size_id,
                    (int) $clarity_id,
                    (int) $color_id
                ) );
            }

            if ( null === $price ) {
                $submit_message = 'No price found for this combination. Please contact us for an estimate.';
            } else {
                $price    = (float) $price;
                $price_ct = $minus ? $price - ( $price * $discount / 100 ) : $price + ( $price * $discount / 100 );
                $total    = $price_ct * $carat;

                $status            = true;
                $submit_message    = '$' . $price;
                $price_per_carat   = '$' . $price_ct . ' /ct';
                $total_price_carat = '$' . $total;
            }
        }
    }

    wp_send_json( array(
        'status'            => $status,
        'message'           => $submit_message,
        'price_carat'       => $price_per_carat,
        'total_price_carat' => $total_price_carat,
        'form_name'         => $frm_name,
    ) );
}
add_action( 'wp_ajax_submit_price_calculator_request', 'submit_price_calculator_request' );
add_action( 'wp_ajax_nopriv_submit_price_calculator_request', 'submit_price_calculator_request' );
