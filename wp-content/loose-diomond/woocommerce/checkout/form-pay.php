<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.2.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals();
?>
<form id="order_review" method="post">

	<div class="oc-checkout-order-review" >
								<h3 id="order_review_heading">Order Summary</h3>
								<div id="order_review" class="woocommerce-checkout-review-order">

	<table class="shop_table woocommerce-checkout-review-order-table">

		<tbody>
			<?php foreach ( $order->get_items() as $item_id => $item ) :
				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}

				$_product        = $item->get_product();
				if ( ! $_product || ! $_product->exists() ) {
					continue;
				}
			?>
				<tr class="cart_item <?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td colspan="2">
						<div class="oc_row_c_p">
							<div class="oc_row_c_p_img">
								<?php
								$product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';
								$thumbnail         = $_product->get_image();
								if ( ! $product_permalink ) {
									echo $thumbnail;
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								}
								?>
							</div>
							<div class="oc_row_c_p_cont">
								<div class="oc_row_c_p_name">
									<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
								</div>

								<div class="oc_row_c_p_data">
    <?php
    $item_meta = $item->get_formatted_meta_data( '_', true );

    if ( ! empty( $item_meta ) ) : ?>
        <div class="oc_row_c_p_data">
            <?php foreach ( $item_meta as $meta ) :
                $display = ! empty( $meta->display_value ) ? $meta->display_value : $meta->value;
                $display = wp_kses_post( force_balance_tags( $display ) );
                $display = preg_replace( '/<\/?p[^>]*>/', '', $display );
                $display = trim( $display );
                $display = trim( wp_strip_all_tags( $display ) );
            ?>
                <div class="oc-checkout-cart-item-meta">
                    <span class="oc-checkout-meta-label"><?php echo wp_kses_post( $meta->display_key ); ?>:</span>
                    <span class="oc-checkout-meta-value"><?php echo $display; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

							</div>
						</div>
					</td>
				</tr>

				<tr class="oc_row_qty">
					<td colspan="2">
						<div class="oc_row_c_p_qty">
							<div class="oc_row_c_v_qty">
								<?php echo esc_html( $item->get_quantity() ) . ' qty.'; ?>
							</div>
							<div class="oc_row_c_v_total">
								<div class="oc_row_c_v_total_inner">
									<?php
									$regular_price = $_product->get_regular_price();
									$current_price = $_product->get_price();

									echo '<span class="oc-current-price">' . $order->get_formatted_line_subtotal( $item ) . '</span>';

									if ( $regular_price && $regular_price != $current_price ) {
										$regular_subtotal = wc_price( floatval( $regular_price ) * $item->get_quantity() );
										echo '<span class="oc-regular-price">was &nbsp;' . $regular_subtotal . '</span>';
									}
									?>
								</div>
							</div>
						</div>
					</td>
				</tr>

			<?php endforeach; ?>
		</tbody>

		<tfoot>
			<!-- Subtotal -->
			<tr class="cart-subtotal">
				<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
				<td><?php echo wc_price( $order->get_subtotal() ); ?></td>
			</tr>

			<!-- Shipping -->
			<tr class="oc_shipping">
				<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
				<td class="text-end">
					<?php
					$shipping_total = $order->get_shipping_total();
					$shipping_tax   = $order->get_shipping_tax();
					$total_shipping = $shipping_total + $shipping_tax;

					if ( $total_shipping == 0 ) {
						echo esc_html__( 'FREE', 'woocommerce' );
					} else {
						echo wc_price( $total_shipping );
					}
					?>
				</td>
			</tr>

			<!-- Fees -->
			<?php foreach ( $order->get_fees() as $fee_id => $fee ) : ?>
				<tr class="fee">
					<th class="border-top-0">
						<?php echo esc_html( $fee->get_name() ); ?>
						<?php
				if($fee->get_name()=='Payment Processing Charge'){
					echo '<i class="fa-solid fa-eye"
						   data-bs-toggle="tooltip"
						   data-bs-placement="top"
						   title="Processing Fee - A 5% processing fee applies to payments made via Stripe and Affirm. To avoid this fee, you can use Wire, Wise, USDT, or Zelle."></i>';
				}
				?>
						
					</th>
					<td class="border-top-0"><?php echo wc_price( $fee->get_total() ); ?></td>
				</tr>
			<?php endforeach; ?>

			<!-- Taxes -->
			<?php if ( wc_tax_enabled() && $order->get_total_tax() > 0 ) : ?>
			    <tr class="tax-total">
			        <th><?php esc_html_e( 'Tax', 'woocommerce' ); ?></th>
			        <td>
			            <?php
			            echo wp_kses_post(
			                wc_price(
			                    $order->get_total_tax(),
			                    [ 'currency' => $order->get_currency() ]
			                )
			            );
			            ?>
			        </td>
			    </tr>
			<?php endif; ?>

			<!-- Discounts / Coupons -->
			<?php if ( $order->get_discount_total() > 0 ) : ?>
				<tr class="cart-discount">
					<th><?php esc_html_e( 'Discount', 'woocommerce' ); ?></th>
					<td>-<?php echo wc_price( $order->get_discount_total() ); ?></td>
				</tr>
			<?php endif; ?>

			<!-- Order Total -->
			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<tr class="order-total">
				<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
				<td><?php echo $order->get_formatted_order_total(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

		</tfoot>
	</table>

</div>
</div>
	<?php
	/**
	 * Triggered immediately before the payment section.
	 * @since 8.2.0
	 */
	do_action( 'woocommerce_pay_order_before_payment' );
	?>

	<div id="payment" class="woocommerce-checkout-payment mt-4 ">
		<?php if ( $order->needs_payment() ) : ?>
			<div class="oc-paymen-button">
    <?php foreach ( $available_gateways as $gateway ) : ?>
        <button type="button" 
                class="oc-gateway-btn" 
                data-gateway="<?php echo esc_attr( $gateway->id ); ?>">

            <?php if ( $gateway->get_icon() ) :
                preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/', $gateway->get_icon(), $m );
                if ( ! empty( $m[1] ) ) : ?>
                    <img src="<?php echo esc_url( $m[1] ); ?>" alt="<?php echo esc_attr( $gateway->get_title() ); ?>">
                <?php endif;
            endif; ?>

            <span><?php echo esc_html( $gateway->get_title() ); ?></span>
        </button>
    <?php endforeach; ?>
</div>


			<ul class="wc_payment_methods payment_methods methods">
				<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>';
					wc_print_notice(
						apply_filters(
							'woocommerce_no_available_payment_methods_message',
							esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' )
						),
						'notice'
					);
					echo '</li>';
				}
				?>
			</ul>
		<?php endif; ?>

		<div class="form-row">
			<input type="hidden" name="woocommerce_pay" value="1" />

			<?php wc_get_template( 'checkout/terms.php' ); ?>

			<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

			<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt oc-dark ' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

			<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
		</div>
	</div>

</form>