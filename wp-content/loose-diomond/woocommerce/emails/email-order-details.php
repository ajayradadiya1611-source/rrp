<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 10.1.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$heading_class              = $email_improvements_enabled ? 'email-order-detail-heading' : '';
$order_table_class          = $email_improvements_enabled ? 'email-order-details' : '';
$order_total_text_align     = $email_improvements_enabled ? 'right' : 'left';

if ( $email_improvements_enabled ) {
	add_filter( 'woocommerce_order_shipping_to_display_shipped_via', '__return_false' );
}

/**
 * Action hook to add custom content before order details in email.
 *
 * @param WC_Order $order Order object.
 * @param bool     $sent_to_admin Whether it's sent to admin or customer.
 * @param bool     $plain_text Whether it's a plain text email.
 * @param WC_Email $email Email object.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); 

$style_border_bottom = 'border-bottom:1px solid rgba(0,0,0,.2);';


?>

<h2 class="<?php echo esc_attr( $heading_class ); ?>">
	<?php
	if ( $email_improvements_enabled ) {
		echo wp_kses_post( __( 'Order summary', 'woocommerce' ) );
	}
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/*if ( $email_improvements_enabled ) {
		echo '<br><span>';
	}
	$order_number_string = __( '[Order #%s]', 'woocommerce' );
	if ( $email_improvements_enabled ) {
		$order_number_string = __( 'Order #%s', 'woocommerce' );
	}
	echo wp_kses_post( $before . sprintf( $order_number_string . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	if ( $email_improvements_enabled ) {
		echo '</span>';
	}*/
	?>
</h2>

<div style="margin-bottom: <?php echo $email_improvements_enabled ? '24px' : '40px'; ?>;">
	<table class="td font-family <?php echo esc_attr( $order_table_class ); ?>" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
		<?php /*if ( ! $email_improvements_enabled ) {*/ ?>
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php echo $style_border_bottom;?>"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;<?php echo $style_border_bottom;?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:right;<?php echo $style_border_bottom;?>"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<?php /*}*/ ?> 
		<tbody>
			<?php
			$image_size = $email_improvements_enabled ? 48 : 32;
			echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$order,
				array(
					'show_sku'      => $sent_to_admin,
					'show_image'    => $email_improvements_enabled,
					'image_size'    => array( $image_size, $image_size ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
				)
			);
			?>
		</tbody>
		<tfoot>
			<?php
			$item_totals       = $order->get_order_item_totals();

			$coupon_label_map = array(
				'lgd_crypto2' => 'Crypto Discount',
				'lgd_wire2'   => 'Wire Discount',
			);

			// Build one row per coupon actually applied to the order (rather than
			// trusting get_order_item_totals() to have already split them out —
			// on some setups it collapses everything into a single "Discount" row).
			$coupon_rows = array();
			foreach ( $order->get_items( 'coupon' ) as $coupon_item ) {
				/** @var WC_Order_Item_Coupon $coupon_item */
				$coupon_code   = $coupon_item->get_code();
				$coupon_amount = $coupon_item->get_discount();

				if ( $coupon_amount <= 0 ) {
					continue;
				}

				$mapped_label = isset( $coupon_label_map[ strtolower( $coupon_code ) ] )
					? $coupon_label_map[ strtolower( $coupon_code ) ]
					: null;

				$label = $mapped_label
					? esc_html( $mapped_label ) . ':'
					: 'Coupon (' . esc_html( strtoupper( $coupon_code ) ) . '):';

				$coupon_rows[ 'ls_coupon_' . $coupon_code ] = array(
					'label' => $label,
					'value' => '-' . wc_price( $coupon_amount, array( 'currency' => $order->get_currency() ) ),
					'type'  => 'discount',
				);
			}

			// Replace whatever discount-type row(s) get_order_item_totals() produced
			// with our explicit per-coupon rows, in the same position.
			if ( $coupon_rows ) {
				$rebuilt        = array();
				$coupons_placed = false;

				foreach ( $item_totals as $key => $total ) {
					if ( isset( $total['type'] ) && 'discount' === $total['type'] ) {
						if ( ! $coupons_placed ) {
							$rebuilt        = $rebuilt + $coupon_rows;
							$coupons_placed = true;
						}
						continue;
					}
					$rebuilt[ $key ] = $total;
				}

				$item_totals = $rebuilt;
			}

			$item_totals_count = count( $item_totals );

			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $key => $total ) {
					$i++;
					$last_class = ( $i === $item_totals_count ) ? ' order-totals-last' : '';
					$valuecolor='#000';
					if($total['type']=='discount'){
							$valuecolor='#198754';
							$total['value'] = '<span style="color:#198754 !important;">' .wp_strip_all_tags( $total['value'] ). '</span>';
					}
					?>
					<?php
					$is_total = ( 'total' === ( $total['type'] ?? '' ) );
					$th_style = 'border:0;vertical-align:middle;text-align:left;padding:8px 12px;padding-top:5px;padding-left:0;color:#000;'
					    . ( $is_total ? 'font-weight:normal;padding-bottom:24px;' : '' );
					$td_style = 'border:0;vertical-align:middle;text-align:right;padding:8px 12px;padding-top:5px;padding-right:0;color:'.$valuecolor.';'
					    . ( $is_total ? 'font-weight:normal;font-size:20px;padding-bottom:24px;' : '' );
					?>
					<tr class="order-totals order-totals-<?php echo esc_attr( $total['type'] ?? 'unknown' ); ?><?php echo esc_attr( $last_class ); ?>">
					    <th class="td text-align-left" scope="row" colspan="2" style="<?php echo esc_attr( $th_style ); ?>" align="left">
					        <?php
					        echo wp_kses_post( $total['label'] ) . ' ';
					        if ( $email_improvements_enabled ) {
					            echo isset( $total['meta'] ) ? wp_kses_post( $total['meta'] ) : '';
					        }
					        ?>
					    </th>
					    <td class="td text-align-right" style="<?php echo esc_attr( $td_style ); ?>" align="right"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
				}
			}
			if ( $order->get_customer_note() && ! $email_improvements_enabled ) {
				?>
				<tr>
					<th class="td text-align-left" scope="row" colspan="2"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td class="td text-align-left"><?php echo wp_kses( nl2br( wc_wptexturize_order_note( $order->get_customer_note() ) ), array() ); ?></td>
				</tr>
				<?php
			}
			/*if ( $order->get_customer_note() && $email_improvements_enabled ) {
				?>
				<tr class="order-customer-note">
					<td class="td text-align-left" colspan="3">
						<b><?php esc_html_e( 'Customer note', 'woocommerce' ); ?></b><br>
						<?php echo wp_kses( nl2br( wc_wptexturize_order_note( $order->get_customer_note() ) ), array( 'br' => array() ) ); ?>
					</td>
				</tr>
				<?php
			}*/
			?>
		</tfoot>
	</table>
</div>

<?php
if ( $email_improvements_enabled ) {
	remove_filter( 'woocommerce_order_shipping_to_display_shipped_via', '__return_false' );
}

/**
 * Action hook to add custom content after order details in email.
 *
 * @param WC_Order $order Order object.
 * @param bool     $sent_to_admin Whether it's sent to admin or customer.
 * @param bool     $plain_text Whether it's a plain text email.
 * @param WC_Email $email Email object.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );