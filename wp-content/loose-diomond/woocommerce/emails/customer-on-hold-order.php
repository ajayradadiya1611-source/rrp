<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 9.8.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>
<p>
<?php
if ( ! empty( $order->get_billing_first_name() ) ) {
	/* translators: %s: Customer first name */
	printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
} else {
	printf( esc_html__( 'Hi,', 'woocommerce' ) );
}
?>
</p>

<?php
$order_number = $order->get_order_number();
if ( $sent_to_admin !== true 
	    && $order->get_payment_method() === 'lgdcrypto_bc'
	    && in_array( $order->get_status(), array( 'pending', 'on-hold' ) ) ) {

	    $order_id = $order->get_id();
	    $payment_method = $order->get_payment_method_title();

	    echo '
		    <p style="margin-bottom:10px;margin-top:10px;">
		        Just to let you know, we\'ve received your Order #' . esc_html( $order_number ) . '. 
		        Your order is currently on hold while we wait for your payment to be confirmed.
		    </p>';

		if ( WEBSITE_TYPE == 'RETAIL' ) {
		    echo '
		    <p>
		        We\'ll work to secure your diamond from our overseas suppliers within 24–48 business hours. 
		        If the diamond is available, we\'ll proceed with your order. If it is no longer available, 
		        we\'ll email you with comparable alternatives. If none of the options meet your expectations, 
		        you may cancel your order at no charge.
		    </p>';
		}

		echo '
		    <p>
		        <strong>Having trouble paying with PayPal?</strong>
		    </p>

		    <p>
		        We\'re currently experiencing a temporary technical issue with PayPal. 
		        If you\'re unable to complete your payment using PayPal, please select one of our other 
		        available payment methods.
		    </p>

		    <p>
		        We accept Visa, Mastercard, Discover, American Express, Crypto, and bank transfers. 
		        Customers can also make low-cost international payments using Wise (formerly TransferWise) 
		        or send a direct wire transfer to our bank account.
		    </p>

		    <p>
		        Once your payment is confirmed, we\'ll begin processing your order and keep you updated every step of the way.
		    </p>';
	}else{
?>
<?php if ( $email_improvements_enabled ) : ?>
	
		<?php
		if(WEBSITE_TYPE=='RETAIL' ){
			if ( $order->get_payment_method() === 'bacs' ) {

			    echo '<p>We\'ve received your order. It\'s currently on hold while we confirm that your payment has been processed.
</p>';

			    echo '<p>We\'ll secure your diamond from our overseas suppliers within 24–48 business hours. If available, we’ll ship it; if not, we’ll email comparable alternatives. You can cancel if unsatisfied – no charge!
</p>';

			    echo '<p>We accept Visa, Mastercard, Discover, American Express, Crypto, and bank transfers. Customers can also make low-cost international payments using Wise (formerly TransferWise) or send a direct wire transfer to our bank account.</p>';
			}
			//echo $order->get_payment_method();
			if($order->get_payment_method() === 'lgdcrypto'){
				echo "<p>We've received your order. It's currently on hold while we confirm that your payment has been processed.";
				echo '</p>';
				echo '<p>We\'ll secure your diamond from our overseas suppliers within 24–48 business hours. If the diamond is available, we\'ll arrange shipment. If it\'s unavailable, we\'ll email you comparable alternatives. If you\'re not satisfied with the alternatives, you can cancel your order at no charge.</p>';
			}
			
		}else{
			echo '<p>We’ve received your order and it’s currently on hold until we can confirm your payment has been processed.
		</p>';
		}
		?>
	<p><?php esc_html_e( 'Here’s a reminder of what you’ve ordered:', 'woocommerce' ); ?></p>
<?php else : ?>
	<p><?php esc_html_e( 'Thanks for your order. It’s on-hold until we confirm that payment has been received.', 'woocommerce' ); ?></p>
<?php endif; ?>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?> 
<?php
	}
?>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
