<?php
/**
 * WooCommerce Order Confirmation Template
 * Place in: yourtheme/woocommerce/checkout/thankyou.php
 * Or use via hook: woocommerce_thankyou
 *
 * @param int $order_id
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
    return;
}

// ─── Dynamic Order Data ───────────────────────────────────────────────────────
$customer_first_name  = $order->get_billing_first_name();
$order_number         = $order->get_order_number();
$order_date           = wc_format_datetime( $order->get_date_created() );
$payment_method_title = $order->get_payment_method_title();
$order_note           = $order->get_customer_note();

// Shipping address
$shipping_first_name  = $order->get_shipping_first_name();
$shipping_last_name   = $order->get_shipping_last_name();
$shipping_company     = $order->get_shipping_company();
$shipping_address_1   = $order->get_shipping_address_1();
$shipping_address_2   = $order->get_shipping_address_2();
$shipping_city        = $order->get_shipping_city();
$shipping_postcode    = $order->get_shipping_postcode();
$shipping_state       = $order->get_shipping_state();
$shipping_country     = WC()->countries->countries[ $order->get_shipping_country() ] ?? $order->get_shipping_country();
$billing_phone        = $order->get_shipping_phone();
$billing_email        = $order->get_billing_email();

// Billing address
$billing_first_name   = $order->get_billing_first_name();
$billing_last_name    = $order->get_billing_last_name();
$billing_company      = $order->get_billing_company();
$billing_address_1    = $order->get_billing_address_1();
$billing_address_2    = $order->get_billing_address_2();
$billing_city         = $order->get_billing_city();
$billing_postcode     = $order->get_billing_postcode();
$billing_state        = $order->get_billing_state();
$billing_country      = WC()->countries->countries[ $order->get_billing_country() ] ?? $order->get_billing_country();

// Totals
$subtotal             = $order->get_subtotal();
$shipping_total       = $order->get_shipping_total();
$order_total          = $order->get_total();
$currency_symbol      = get_woocommerce_currency_symbol();

$payment_method = $order->get_payment_method();
if ( $order->get_status() == 'pending' && $order->get_payment_method() === 'lgdcrypto' ) {
    $order->update_status( 'on-hold' );
}
if ( $order->get_status() == 'pending' && $order->get_payment_method() === 'lgdcrypto_bc' ) {
    //$order->update_status( 'on-hold' );
}

if ( $order->get_status() == 'pending' && $order->get_payment_method() === 'oc_zelle' ) {
    $order->update_status( 'on-hold' );
}
do_action( 'oc_woocommerce_thankyou', $order->get_id() );
?>

<div class="order_confirmation_section pb-0 py-sm-4 pb-lg-5">

    <!-- Logo -->
    <div class="pb_30 d-none d-lg-block">
        <img
            alt="loosegrowndiamond_logo"
            loading="lazy"
            width="44"
            height="30"
            decoding="async"
            class="d-block mx-auto"
            src="<?php echo get_template_directory_uri(); ?>/rrp-img/comman/logo.svg"
        >
    </div>

    <!-- Thank You Header -->
    <div class="thankyou_page">
        <div class="header-section text-center">
            <div class="d-flex justify-content-center">
                <h3><?php esc_html_e( 'Thank you for your order', 'your-textdomain' ); ?></h3>
                <h3 class="text-capitalize ms-1"><?php echo esc_html( $customer_first_name ); ?></h3>
            </div>
            <h6 class="mb-0">
                <?php esc_html_e( 'Order', 'your-textdomain' ); ?>
                <span>#<?php echo esc_html( $order_number ); ?></span>
            </h6>
        </div>
    </div>

    <!-- Post-purchase question (React) -->
    <div id="ls-thankyou-question-root"></div>

    <!-- Order Meta -->
    <div class="order_confirmation_deatil">

        <!-- Desktop Meta Row -->
        <div class="d-flex justify-content-between d-none d-sm-flex">
            <div class="th_order_data">
                <span class="mb-2"><?php esc_html_e( 'Order Date', 'your-textdomain' ); ?></span>
                <p class="mb_30"><?php echo esc_html( $order_date ); ?></p>
            </div>
            <div class="th_order_data">
                <span class="mb-2"><?php esc_html_e( 'Order ID', 'your-textdomain' ); ?></span>
                <p class="mb_30">#<?php echo esc_html( $order_number ); ?></p>
            </div>
            <div class="th_order_data">
                <span class="mb-2"><?php esc_html_e( 'Payment', 'your-textdomain' ); ?></span>
                <p class="mb_30 text-capitalize"><?php echo esc_html( $payment_method_title ); ?></p>
            </div>
        </div>

        <!-- Mobile Meta Rows -->
        <div class="d-sm-none">
            <div class="th_order_data d-flex justify-content-between">
                <span class="mb-2"><?php esc_html_e( 'Order Date', 'your-textdomain' ); ?></span>
                <p class="mb_30"><?php echo esc_html( $order_date ); ?></p>
            </div>
            <div class="th_order_data d-flex justify-content-between">
                <span class="mb-2"><?php esc_html_e( 'Order ID', 'your-textdomain' ); ?></span>
                <p class="mb_30">#<?php echo esc_html( $order_number ); ?></p>
            </div>
            <div class="th_order_data d-flex justify-content-between">
                <span class="mb-2"><?php esc_html_e( 'Payment', 'your-textdomain' ); ?></span>
                <p class="mb_30"><?php echo esc_html( $payment_method_title ); ?></p>
            </div>
        </div>

        <!-- Order Note -->
        <?php if ( $order_note ) : ?>
            <div class="mb-4">
                <span class="mb-2"><?php esc_html_e( 'Note', 'your-textdomain' ); ?></span>
                <p class="mb-0"><?php echo esc_html( $order_note ); ?></p>
            </div>
        <?php endif; ?>




        <!-- Shipping & Billing Addresses -->
        <div class="my_50">
            <div class="container-fluid px-0">
                <div class="row">

                    <!-- Shipping Address -->
                    <div class="col-sm-6">
                        <div class="shipping_address">
                            <span class="d-block mb_20" style="color: rgb(119, 119, 119);">
                                <?php esc_html_e( 'Shipping Address', 'your-textdomain' ); ?>
                            </span>
                            <span>
                                <?php echo esc_html( trim( "$shipping_first_name $shipping_last_name" ) ); ?>
                                <?php if ( $shipping_company ) : ?>
                                    <br><?php echo esc_html( $shipping_company ); ?>
                                <?php endif; ?>
                                <br><?php echo esc_html( $shipping_address_1 ); ?>
                                <?php if ( $shipping_address_2 ) : ?>
                                    <br><?php echo esc_html( $shipping_address_2 ); ?>
                                <?php endif; ?>
                                <br><?php echo esc_html( "$shipping_city, $shipping_postcode" ); ?>
                                <br class="d-none d-sm-block">
                                <?php echo esc_html( "$shipping_state, $shipping_country" ); ?>
                                <br><br class="d-none d-sm-block">
                                <i class="fa-solid fa-phone"></i>&nbsp;
                                <?php echo esc_html( $billing_phone ); ?>
                                <br>
                                <i class="fa-solid fa-envelope"></i>&nbsp;
                                <?php echo esc_html( $billing_email ); ?>
                            </span>
                        </div>
                        <hr class="border_color_gray d-sm-none">
                    </div>

                    <!-- Billing Address -->
                    <div class="col-sm-6">
                        <div class="shipping_address">
                            <span class="d-block mb_20" style="color: rgb(119, 119, 119);">
                                <?php esc_html_e( 'Billing Address', 'your-textdomain' ); ?>
                            </span>
                            <span>
                                <?php echo esc_html( trim( "$billing_first_name $billing_last_name" ) ); ?>
                                <?php if ( $billing_company ) : ?>
                                    <br><?php echo esc_html( $billing_company ); ?>
                                <?php endif; ?>
                                <br><?php echo esc_html( $billing_address_1 ); ?>
                                <?php if ( $billing_address_2 ) : ?>
                                    <br><?php echo esc_html( $billing_address_2 ); ?>
                                <?php endif; ?>
                                <br><?php echo esc_html( "$billing_city, $billing_postcode" ); ?>
                                <br class="d-none d-sm-block">
                                <?php echo esc_html( "$billing_state, $billing_country" ); ?>
                            </span>
                        </div>
                    </div>

                </div><!-- /.row -->
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="product_detail">
            <table class="table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Product Details', 'your-textdomain' ); ?></th>
                        <th class="text-center"><?php esc_html_e( 'Qty.', 'your-textdomain' ); ?></th>
                        <th class="text-end"><?php esc_html_e( 'Price', 'your-textdomain' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $order->get_items() as $item_id => $item ) :

                        /** @var WC_Order_Item_Product $item */
                        $product        = $item->get_product();
                        $product_name   = $item->get_name();
                        $quantity       = $item->get_quantity();
                        $item_total     = $order->get_formatted_line_subtotal( $item );

                        // Thumbnail
                        $thumbnail = $product && $product->get_image_id()
                            ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                            : wc_placeholder_img_src( 'thumbnail' );

                        // SKU
                        $sku = $product ? $product->get_sku() : '';

                        // Video URL (custom meta, adjust key as needed)
                        $video_url = wc_get_order_item_meta( $item_id, 'video_url', true );
                        $setting_variation = wc_get_order_item_meta( $item_id, 'setting_variation', true );
                        $diamond = wc_get_order_item_meta( $item_id, 'diamond', true );

                        $sku =$item->get_meta('_sku_snapshot');
                        // Thumbnail
                        $thumbnail = $product && $product->get_image_id()
                            ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                            : wc_placeholder_img_src( 'thumbnail' );
                    ?>
                    <tr class="<?php if(!empty($setting_variation)){echo "setting_variation";}?> ">
                        <td class="<?php if(!empty($diamond)){echo "border-0";}?>">
                            <div class="d-flex gap-10 align-items-start">
                                <img
                                    alt="<?php echo esc_attr( $product_name ); ?>"
                                    loading="lazy"
                                    width="50"
                                    height="50"
                                    decoding="async"
                                    src="<?php echo esc_url( $thumbnail ); ?>"
                                >
                                <div class="details text-capitalize">
                                    <?php 
                                    echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
                                   // echo esc_html( $product_name ); 
                                    ?>

                                    <div class="mt-2"></div>

                                    <?php if ( $sku ) : ?>
                                        <p class="m-0">
                                            <span class="me-1"><?php esc_html_e( 'SKU:', 'your-textdomain' ); ?></span>
                                            <span><?php echo esc_html( $sku ); ?></span>
                                        </p>
                                    <?php endif; ?>

                                    <?php
                                    /**
                                     * Loop through item meta (Metal, Setting Size, etc.)
                                     * These keys come from product attributes or custom fields saved at checkout.
                                     */
                                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                                    $meta_data = $item->get_formatted_meta_data( '_', true );
                                    $hidden_keys = apply_filters( 'woocommerce_hidden_order_itemmeta', array() );
                                    foreach ( $meta_data as $meta ) :
                                           // Skip hidden meta keys
                                            if ( in_array( $meta->key, $hidden_keys ) ) {
                                                continue;
                                            }

                                        $label = wp_kses_post( $meta->display_key );
                                        $value = wp_strip_all_tags( $meta->display_value );
                                    ?>
                                        <p class="m-0 text-capitalize">
                                            <span><?php echo esc_html( $label ); ?>: </span>
                                            <span><?php echo esc_html( $value ); ?></span>
                                        </p>
                                    <?php 
                                    endforeach; 
                                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                                    ?>

                                    <?php if ( $video_url ) : ?>
                                        <p class="m-0">
                                            <span class="text_black_secondary"><?php esc_html_e( 'Video:', 'your-textdomain' ); ?> </span>
                                            <a target="_blank" href="<?php echo esc_url( $video_url ); ?>" style="opacity: 0.7;">
                                                <i class="fa fa-video" style="color: black; text-align: center;"></i>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class=" text-center <?php if(!empty($diamond)){echo "border-0";}?>">
                            <?php echo esc_html( $quantity ); ?> <?php esc_html_e( 'qty.', 'your-textdomain' ); ?>
                        </td>
                        <td class="text-end <?php if(!empty($diamond)){echo "border-0";}?>">
                            <?php echo wp_kses_post( $item_total ); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Order Totals -->
            <div class="total_box">
                    <div>

                        <!-- Subtotal -->
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw-normal"><?php esc_html_e( 'Subtotal', 'your-textdomain' ); ?></p>
                            <p class="fw-medium">
                                <?php echo wp_kses_post( wc_price( $subtotal, [ 'currency' => $order->get_currency() ] ) ); ?>
                            </p>
                        </div>

                        <!-- Coupon Discount(s) -->
                        <?php
                        $coupon_items = $order->get_items( 'coupon' );

                        if ( ! empty( $coupon_items ) ) :
                            foreach ( $coupon_items as $coupon_item ) :
                                /** @var WC_Order_Item_Coupon $coupon_item */
                                $coupon_code   = $coupon_item->get_code();
                                $coupon_amount = $coupon_item->get_discount();

                                if ( $coupon_amount <= 0 ) {
                                    continue;
                                }

                                $coupon_label_map = array(
                                    'lgd_crypto2' => 'Crypto Discount',
                                    'lgd_wire2'   => 'Wire Discount',
                                );
                                $mapped_label = isset( $coupon_label_map[ strtolower( $coupon_code ) ] )
                                    ? $coupon_label_map[ strtolower( $coupon_code ) ]
                                    : null;
                        ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="fw-normal">
                                    <?php if ( $mapped_label ) : ?>
                                        <span><?php echo esc_html( $mapped_label ); ?></span>
                                    <?php else : ?>
                                        <span>Coupon (<span class="text-uppercase"><?php echo esc_html( $coupon_code ); ?></span>)</span>
                                    <?php endif; ?>
                                </p>
                                <p class="fw-medium text-success">
                                    -<?php echo wp_kses_post( wc_price( $coupon_amount, [ 'currency' => $order->get_currency() ] ) ); ?>
                                </p>
                            </div>
                        <?php
                            endforeach;
                        endif;
                        ?>

                        <!-- Shipping -->
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw-normal">
                                <?php esc_html_e( 'Shipping', 'your-textdomain' ); ?>
                            </p>
                            <p class="fw-medium">
                                <?php
                                echo ( $shipping_total == 0 )
                                    ? esc_html__( 'Free', 'your-textdomain' )
                                    : wp_kses_post( wc_price( $shipping_total, [ 'currency' => $order->get_currency() ] ) );
                                ?>
                            </p>
                        </div>

                      <?php
                        $total_tax = $order->get_total_tax();

                        if ( $total_tax > 0 ) :
                        ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="fw-normal">
                                    <?php esc_html_e( 'Tax', 'woocommerce' ); ?>
                                </p>

                                <p class="fw-medium">
                                    <?php
                                    echo wp_kses_post(
                                        wc_price(
                                            $total_tax,
                                            [ 'currency' => $order->get_currency() ]
                                        )
                                    );
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php
                        $fees = $order->get_fees();

                        if ( ! empty( $fees ) ) :
                            foreach ( $fees as $fee ) :
                        ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="fw-normal">
                                    <?php echo esc_html( $fee->get_name() ); ?>
                                </p>
                                <p class="fw-medium">
                                    <?php
                                    echo wp_kses_post(
                                        wc_price(
                                            $fee->get_total(),
                                            [ 'currency' => $order->get_currency() ]
                                        )
                                    );
                                    ?>
                                </p>
                            </div>
                        <?php
                            endforeach;
                        endif;
                        ?>

                    </div>

                    <div class="border_bottom_gray"></div>

                    <!-- Total -->
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="fw-bold mb-0">
                            <?php esc_html_e( 'Total', 'your-textdomain' ); ?>
                        </p>
                        <p class="fw-bold mb-0">
                            <?php echo wp_kses_post( wc_price( $order_total, [ 'currency' => $order->get_currency() ] ) ); ?>
                        </p>
                    </div>
                </div>
            <!-- Tracking Note -->
            <p class="track_text mb-0">
                <?php esc_html_e( "We'll send you shipping confirmation when your item(s) are on the way. Additionally you can", 'your-textdomain' ); ?>
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
                    <span class="fw-bold"><?php esc_html_e( 'track your order.', 'your-textdomain' ); ?></span>
                </a>
                <?php esc_html_e( 'We appreciate your business, and hope you enjoy your purchase.', 'your-textdomain' ); ?>
            </p>

        </div><!-- /.product_detail -->

      

    </div><!-- /.order_confirmation_deatil -->

</div><!-- /.order_confirmation_section -->