<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
global $wpdb;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$sku     = $_product->get_sku();
			$certificate_type = null;

            if ( isset($cart_item['diamond_data']) ) {
                $certificate_type = isset($cart_item['diamond_data']->certificate_type) 
                    ? intval($cart_item['diamond_data']->certificate_type) 
                    : null;

            }



			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td  colspan="2">
						<div class="oc_row_c_p">
							<div class="oc_row_c_p_img">
								<?php
								$product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								if ( ! $product_permalink ) {
									echo $thumbnail;
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								}
								?>
							</div>
							<div class="oc_row_c_p_cont">
								<div class="oc_row_c_p_name">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) ; ?>
								</div>
								
								<div  class="oc_row_c_p_data">
									<?php
									$item_data = get_cart_item_data_array( $cart_item );

									if ( ! empty( $item_data ) ) : ?>
										<div class="oc_row_c_p_data">
											<?php foreach ( $item_data as $data ) : 
												$display = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
											?>
												<div class="oc-checkout-cart-item-meta">
													<span class="oc-checkout-meta-label"><?php echo esc_html( $data['key'] ); ?>:</span>
													<span class="oc-checkout-meta-value"><?php echo wp_kses_post( $display ); ?></span>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								
								</div>
								<?php
								if ( $certificate_type === 0 || $certificate_type === 4 ) {
		                           echo '<div class="custom-certi-text-checkout" >';
		                            echo 'This diamond is sold without a certificate (no certificate included).';
		                            echo  '</div>';
		                        }
								?>
							</div>
						</div>
						
					</td>
				</tr>
				<tr class="oc_row_qty">
					<td  colspan="2">
						<div class="oc_row_c_p_qty">
							<div class="oc_row_c_v_qty">
								<?php echo $cart_item['quantity'].' qty.'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<div class="oc_row_c_v_total">
								<div class="oc_row_c_v_total_inner">
								<?php 
								

								

								// Show the current/subtotal price (respecting taxes/formatting)
								checkoutregularprice($cart_item,$_product,$cart_item_key);
								// If product has a regular price different from current price, show regular subtotal struck-through
								checkoutwasprice($cart_item,$_product,$cart_item_key);
								?>
								<div>
							</div>
						</div>
						
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		

		

			

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th class="border-top-0"><?php echo esc_html( $fee->name ); ?>
				<?php
				if($fee->name=='Payment Processing Charge'){
					echo '<i class="fa-solid fa-eye "
				   data-bs-toggle="tooltip"
				   data-bs-placement="top"
				   title="Processing Fee - A 5% processing fee applies to payments made via Stripe and Affirm. To avoid this fee, you can use Wire, Wise, USDT, or Zelle."></i>';
				}
				?>
				 </th>
				<td class="border-top-0"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() ) : ?>

    <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>

        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
            <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php echo esc_html( $tax->label ); ?></th>
                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <tr class="tax-total">
            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
            <td><?php wc_cart_totals_taxes_total_html(); ?></td>
        </tr>
    <?php endif; ?>

<?php endif; ?>
		<?php if (WC()->cart->get_applied_coupons()) : ?>
	        <?php foreach (WC()->cart->get_applied_coupons() as $coupon_code) :
	            $coupon          = new WC_Coupon($coupon_code);
	            $discount_amount = WC()->cart->get_coupon_discount_amount($coupon_code);
	            ?>
	            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $coupon_code ) ); ?>">
		            	<td>
		                    <?php
							$coupon_label_map = array(
							    'lgd_crypto2' => 'Crypto Discount',
							    'lgd_wire2'   => 'Wire Discount',
							);
							$mapped_label = isset( $coupon_label_map[ strtolower( $coupon_code ) ] )
							    ? $coupon_label_map[ strtolower( $coupon_code ) ]
							    : null;
							?>
							<?php if ( $mapped_label ) : ?>
							    <span><?php echo esc_html( $mapped_label ); ?></span>
							<?php else : ?>
							    <span>Coupon 
                                    <span class="coupon_lb"><?php echo esc_html( $coupon_code ); ?> <a href="#" class="remove_coupon text-decoration-none" data-coupon="<?php echo esc_attr($coupon_code); ?>">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a></span></span>
							<?php endif; ?>
		                    
		                </td>
		                <td><span class="oc_cp_amount text-success">-<?php echo wc_price($discount_amount); ?></span>
		                	
		                </td>
		            </div>
		        </tr>
	        <?php endforeach; ?>
	    <?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<?php
		$excluded_coupons = array( 'lgd_wire2', 'lgd_crypto2' );
		$applied_coupons  = WC()->cart->get_applied_coupons();
		$real_coupons     = array_diff( $applied_coupons, $excluded_coupons );
		$has_coupon       = ! empty( $real_coupons );
		if (!$has_coupon){
		?>
		<tr class="coupon-row" >
			
			<td colspan="2">

				<?php 
				
				 ?>
	            <p class="coupon_form fw-500 mb-2 <?php echo $has_coupon ? 'd-none' : '';?>">Apply promo code +</p>
	            <div class="promo_code d-none">
	            	<div class="promo_code_inner">
		                <input type="text" placeholder="Enter Promo Code">
		                <button class="btn apply_btn">Apply</button>
		            </div>
	                <div class="cart-coupon-message"></div> 
	            </div>
	            

            <!-- 
				<div class="oc-custom-coupon">
					<div class="oc-review_coupon_label">
						<a href="#" class="apply_revowpm_coode">Apply promo code +</a>
					</div>
					<div class="custom-coupon-wrapper">
						<input type="text" 
						       name="coupon_code" 
						       class="input-text" 
						       id="custom_coupon_code" 
						       placeholder="<?php esc_attr_e( 'Enter coupon code', 'woocommerce' ); ?>" />

						<button type="button" 
						        class="button" 
						        id="custom_apply_coupon">
							<?php esc_html_e( 'APPLY', 'woocommerce' ); ?>
						</button>
					</div>
					<div class="coupon-message"></div>
				</div> -->
			</td>
		</tr> 
		<?php 
		}
		?>
		
			<tr class="oc_shipping">
	<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
	<td  class="text-end">
		<?php
			$shipping_total = WC()->cart->get_shipping_total();
			$shipping_tax   = WC()->cart->get_shipping_tax();
			$total_shipping = $shipping_total + $shipping_tax;

			if ( $total_shipping == 0 ) {
				echo esc_html__( 'FREE', 'woocommerce' );
			} else {
				echo wc_price( $shipping_total );
			}
		?>
	</td>
</tr>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
