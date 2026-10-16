<?php
/**
 * Customer Reset Password email
 *
 * Custom override for RRP Diamond.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 9.8.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

// Greet by first name, fall back to username.
$user         = get_user_by( 'id', $user_id );
$display_name = ( $user && ! empty( $user->first_name ) ) ? $user->first_name : $user_login;

$reset_url = add_query_arg(
	array(
		'key'   => $reset_key,
		'id'    => $user_id,
		'login' => rawurlencode( $user_login ),
	),
	wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
);
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>

<p><?php printf( esc_html__( 'Hello %s,', 'woocommerce' ), esc_html( $display_name ) ); ?></p>

<p><?php esc_html_e( 'We received a request to reset the password for your RRP Diamond account.', 'woocommerce' ); ?></p>

<p><?php echo wp_kses( sprintf( __( 'Username: <b>%s</b>', 'woocommerce' ), esc_html( $user_login ) ), array( 'b' => array() ) ); ?></p>

<p><?php esc_html_e( 'If you requested a password reset, click the link below to create a new password:', 'woocommerce' ); ?></p>

<p><a class="link" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_html_e( 'Reset Your Password', 'woocommerce' ); ?></a></p>

<p><?php esc_html_e( "If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.", 'woocommerce' ); ?></p>

<p><?php esc_html_e( 'For security reasons, we recommend not sharing this email or your password reset link with anyone.', 'woocommerce' ); ?></p>

<p><?php esc_html_e( 'Best regards,', 'woocommerce' ); ?><br>
<?php esc_html_e( 'Team RRP Diamond', 'woocommerce' ); ?></p>

<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content email-additional-content-aligned">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

do_action( 'woocommerce_email_footer', $email );