<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 *
 * @var bool $show_downloads Controls whether the downloads table should be rendered.
 */

 // phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

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


$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$downloads          = $order->get_downloadable_items();
$actions            = array_filter(
	wc_get_account_orders_actions( $order ),
	function ( $key ) {
		return 'view' !== $key;
	},
	ARRAY_FILTER_USE_KEY
);

// We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
$show_customer_details = $order->get_user_id() === get_current_user_id();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section>
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>



	<!-- Order Items Table -->
        <div class="oc-order-details product_detail">
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

                        

                        // SKU
                       

                        // Video URL (custom meta, adjust key as needed)
                        $video_url = wc_get_order_item_meta( $item_id, 'video_url', true );
                        $setting_variation = wc_get_order_item_meta( $item_id, 'setting_variation', true );
                        $diamond = wc_get_order_item_meta( $item_id, 'diamond', true );
                        $_diamond_data = wc_get_order_item_meta( $item_id, '_diamond_data', true );

                        $sku =$item->get_meta('_sku_snapshot');
                        //print_r($_diamond_data);
                        $c_type = wc_get_order_item_meta( $item_id, 'c_type', true );
                        if(!empty($_diamond_data)){
                            $thumbnail = get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/'.strtolower($_diamond_data->shape).'.png';
                        }else{
                            // Thumbnail
                            $thumbnail = $product && $product->get_image_id()
                                ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                                : wc_placeholder_img_src( 'thumbnail' );
                        }
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

                                    $product_name = apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false );

                                    if ( $c_type == 1 || $c_type == 3 ) {
                                        echo wp_kses_post( $product_name );
                                        
                                    } else {
                                        $product = $item->get_product();
                                        if ( $product ) {
                                            $product_link = get_permalink( $product->get_id() );
                                            echo '<a href="' . esc_url( $product_link ) . '" class="text-decoration-none" >' . wp_kses_post( $product_name ) . '</a>';
                                        } else {
                                            echo wp_kses_post( $product_name );
                                        }
                                    }
                                 
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
                                     * Loop through item meta (Metal, Setting Size, Certificate Type, etc.)
                                     * These keys come from product attributes or custom fields saved at checkout.
                                     */
                                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                                    $meta_data = $item->get_formatted_meta_data( '_', true );
                                    $hidden_keys = apply_filters( 'woocommerce_hidden_order_itemmeta', array() );
									foreach ( $meta_data as $meta ) :

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
        <p class="fw-normal">
            <?php esc_html_e( 'Subtotal', 'your-textdomain' ); ?>
        </p>
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

    <!-- Tax -->
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

    <!-- Payment Method -->
    <div class="d-flex align-items-center justify-content-between">
        <p class="fw-normal">
            <?php esc_html_e( 'Payment Method', 'your-textdomain' ); ?>
        </p>
        <p class="fw-medium">
            <?php echo esc_html( $order->get_payment_method_title() ); ?>
        </p>
    </div>

</div>

<div class="border_bottom_gray"></div>
<!-- Total -->
<div class="d-flex align-items-center justify-content-between">
    <p class="fw-bold <?php echo empty( $actions ) ? 'mb-0' : ''; ?>">
        <?php esc_html_e( 'Total', 'your-textdomain' ); ?>
    </p>

    <p class="fw-bold <?php echo empty( $actions ) ? 'mb-0' : ''; ?>">

        <?php
        $refunded_total = $order->get_total_refunded();

        if ( $refunded_total > 0 ) {

            // Original total with strike
            echo '<span style="text-decoration: line-through;color:#00000099">';
            echo wp_kses_post(
                wc_price( $order_total, [ 'currency' => $order->get_currency() ] )
            );
            echo '</span> ';

            // Show 0 beside it
            echo '<span>';
            echo wp_kses_post(
                wc_price( 0, [ 'currency' => $order->get_currency() ] )
            );
            echo '</span>';

        } else {

            echo wp_kses_post(
                wc_price( $order_total, [ 'currency' => $order->get_currency() ] )
            );

        }
        ?>

    </p>
</div>
                <?php if ( ! empty( $actions ) ) : ?>
                  
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fw-normal"><?php esc_html_e( 'Actions', 'woocommerce' ); ?></p>

                            <p class="fw-medium">
                                <?php
                                $wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) 
                                    ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) 
                                    : '';

                                foreach ( $actions as $key => $action ) {

                                    if ( empty( $action['aria-label'] ) ) {
                                        $action_aria_label = sprintf(
                                            __( '%1$s order number %2$s', 'woocommerce' ),
                                            $action['name'],
                                            $order->get_order_number()
                                        );
                                    } else {
                                        $action_aria_label = $action['aria-label'];
                                    }

                                    echo '<a href="' . esc_url( $action['url'] ) . '" 
                                        class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button ' . sanitize_html_class( $key ) . ' order-actions-button me-2 oc-dark" 
                                        aria-label="' . esc_attr( $action_aria_label ) . '">'
                                        . esc_html( $action['name'] ) .
                                    '</a>';

                                    unset( $action_aria_label );
                                }
                                ?>
                            </p>
                        </div>
                   
                <?php endif; ?>
            </div>

            <!-- Tracking Note -->
            
        </div><!-- /.product_detail -->
	<div class="oc_orderdetail_sub">
		<?php /*if ( ! empty( $actions ) ) : ?>
		<div class="woocommerce-order-actions">
		    <div class="order-actions-row">
		        <div class="order-actions--heading"><?php esc_html_e( 'Actions', 'woocommerce' ); ?>:</div>
		        <div class="order-actions--buttons">
		            <?php
		            $wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		            foreach ( $actions as $key => $action ) {
		                if ( empty( $action['aria-label'] ) ) {
		                    
		                    $action_aria_label = sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
		                } else {
		                    $action_aria_label = $action['aria-label'];
		                }
		                echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button ' . sanitize_html_class( $key ) . ' order-actions-button" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
		                unset( $action_aria_label );
		            }
		            ?>
		        </div>
		    </div>
		</div>
		<?php endif;*/ ?>
		<!-- Order Totals -->
           <!--  <div class="total_box">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="fw-normal"><?php esc_html_e( 'Subtotal', 'your-textdomain' ); ?></p>
                        <p class="fw-medium">
                            <?php echo wp_kses_post( wc_price( $subtotal, [ 'currency' => $order->get_currency() ] ) ); ?>
                        </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="fw-normal"><?php esc_html_e( 'Shipping', 'your-textdomain' ); ?><br></p>
                        <p class="fw-medium">
                            <?php echo wp_kses_post( wc_price( $shipping_total, [ 'currency' => $order->get_currency() ] ) ); ?>
                        </p>
                    </div>
                </div>

                <div class="border_bottom_gray"></div>

                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold mb-0"><?php esc_html_e( 'Total', 'your-textdomain' ); ?></p>
                    <p class="fw-bold mb-0">
                        <?php echo wp_kses_post( wc_price( $order_total, [ 'currency' => $order->get_currency() ] ) ); ?>
                    </p>
                </div>
            </div> -->

		<!-- <div class="woocommerce-order-totals">
		    <?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
		        <div class="order-totals-row order-totals-row--<?php echo esc_attr( $key ); ?>">
		            <div class="order-totals-label"><?php echo esc_html( $total['label'] ); ?></div>
		            <div class="order-totals-value"><?php echo wp_kses_post( $total['value'] ); ?></div>
		        </div>
		    <?php endforeach; ?>

		    <?php if ( $order->get_customer_note() ) : ?>
		        <div class="order-totals-row order-totals-row--note">
		            <div class="order-totals-label"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></div>
		            <div class="order-totals-value">
		                <?php
		                $customer_note = wc_wptexturize_order_note( $order->get_customer_note() );
		                echo wp_kses( nl2br( $customer_note ), array( 'br' => array() ) );
		                ?>
		            </div>
		        </div>
		    <?php endif; ?>
		</div> -->
	</div>

	<?php 
	/*do_action( 'woocommerce_order_details_after_order_table', $order ); */

	?>
	
</section>
<div class="my_50 ">
    <?php if ( $order && $order->get_customer_note() ) : ?>
        <div class="d-flex gap-2">
            <p class="fw-normal"><?php esc_html_e( 'Order Note : ', 'your-textdomain' ); ?></p>
            <p class="fw-medium">
                <?php echo esc_html( $order->get_customer_note() ); ?>
            </p>
        </div>
    <?php endif; ?>
</div>
<div class="my_50 oc-view-detail-address">

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
<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	//wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
