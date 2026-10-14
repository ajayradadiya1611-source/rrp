<?php
/**
 * HTML Email Template: Custom Order Status
 *
 * Location in your theme:
 *   woocommerce/emails/custom-order-status.php
 *
 * WooCommerce will automatically pick this file up from your (child) theme's
 * woocommerce/ folder, so you can safely customise it there without touching
 * plugin/core files.
 *
 * Variables available (set by WC_Email_Custom_Order_Status_Base::trigger()):
 *
 * @var WC_Order $order
 * @var bool     $sent_to_admin
 * @var bool     $plain_text
 * @var WC_Email $email         The email object (gives access to heading, etc.)
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() – outputs the header HTML including the
 *         store logo, background colour and the $email_heading.
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<!-- =====================================================================
     INTRO MESSAGE
     ===================================================================== -->
<p style="margin: 0 0 16px;">
    <?php echo esc_html( $email->get_status_intro_message( $order ) ); ?>
</p>

<!-- =====================================================================
     ORDER DETAILS TABLE
     Outputs: item name, qty, line total, subtotal, shipping, taxes, total.
     @hooked WC_Emails::order_details()
     ===================================================================== -->
<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::email_order_meta() – outputs any extra order meta
 *         (e.g. custom checkout fields, PO numbers, etc.)
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() – outputs billing and (if different)
 *         shipping address blocks.
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::email_footer() – outputs the closing HTML, footer text
 *         and unsubscribe / legal copy set in WooCommerce > Settings > Emails.
 */
do_action( 'woocommerce_email_footer', $email );