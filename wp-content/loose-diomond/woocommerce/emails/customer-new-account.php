<?php
/**
 * Customer new account email
 *
 * Custom override for Loose Grown Diamond.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>

<p><?php printf( esc_html__( 'Hello %s,', 'woocommerce' ), esc_html( $user_login ) ); ?></p>

<p><?php esc_html_e( 'Welcome to Loose Grown Diamond!', 'woocommerce' ); ?></p>

<p><?php esc_html_e( 'Thank you for creating an account with us. Your account is now active and ready to use.', 'woocommerce' ); ?></p>

<p><?php echo wp_kses( sprintf( __( 'Your Username: <b>%s</b>', 'woocommerce' ), esc_html( $user_login ) ), array( 'b' => array() ) ); ?></p>

<?php if ( $password_generated && $set_password_url ) : ?>
	<p><a href="<?php echo esc_url( $set_password_url ); ?>"><?php esc_html_e( 'Click here to set your password.', 'woocommerce' ); ?></a></p>
<?php endif; ?>

<p><?php esc_html_e( 'From your account, you can view and manage your orders, update your account details, change your password, track your purchases, and more.', 'woocommerce' ); ?></p>

<p><?php esc_html_e( 'Access your account here:', 'woocommerce' ); ?><br>
<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( wc_get_page_permalink( 'myaccount' ) ); ?></a></p>

<p><?php esc_html_e( 'We look forward to helping you find the perfect diamond or jewelry piece.', 'woocommerce' ); ?></p>

<p><?php esc_html_e( 'Best regards,', 'woocommerce' ); ?><br>
<?php esc_html_e( 'Team Loose Grown Diamond', 'woocommerce' ); ?></p>

<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content email-additional-content-aligned">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

do_action( 'woocommerce_email_footer', $email );