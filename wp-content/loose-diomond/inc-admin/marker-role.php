<?php

/**
 * 1. Create the "marketer" role.
 * Runs on activation AND on every 'init' so the role self-heals if it's
 * ever deleted or if this file is dropped in after WooCommerce activates.
 */
register_activation_hook( __FILE__, 'lgd_add_marketer_role' );
add_action( 'init', 'lgd_add_marketer_role' );

function lgd_add_marketer_role() {

	$capabilities = array(
		// Basic wp-admin access
		'read'                       => true,
		'upload_files'                => true, // needed for product images

		// Product post type (WooCommerce)
		'edit_product'                => true,
		'edit_products'               => true,
		'edit_others_products'        => true,
		'edit_published_products'     => true,
		'publish_products'            => true,
		'read_private_products'       => true,
		'delete_product'              => true,
		'delete_products'             => true,
		'delete_published_products'   => true,
		'delete_others_products'      => true,

		// Product taxonomies: categories, tags, attributes
		'manage_product_terms'        => true,
		'edit_product_terms'          => true,
		'delete_product_terms'        => true,
		'assign_product_terms'        => true,

		// Needed by WooCommerce core admin screens and most WooCommerce
		// extensions (CTX Feed's "Manage Feeds" screen included) to load
		// their admin pages.
		'manage_woocommerce'          => true,
	);

	$existing = get_role( 'marketer' );

	if ( ! $existing ) {
		add_role( 'marketer', 'Marketer', $capabilities );
	} else {
		// Keep capabilities in sync if the role already exists (e.g. after
		// you edit the $capabilities array above and re-save/deactivate-reactivate).
		foreach ( $capabilities as $cap => $grant ) {
			$existing->add_cap( $cap, $grant );
		}
	}
}

/**
 * 2. Strip the admin menu down to just:
 *    - Products (edit.php?post_type=product) and its submenus
 *    - CTX Feed "Manage Feeds" (admin.php?page=webappick-manage-feeds) and its submenus
 * for users whose only role is "marketer".
 */
add_action( 'admin_menu', 'lgd_restrict_marketer_menu', 999 );

function lgd_restrict_marketer_menu() {

	$user = wp_get_current_user();

	if ( ! in_array( 'marketer', (array) $user->roles, true ) ) {
		return;
	}

	global $menu;

	if ( ! is_array( $menu ) ) {
		return;
	}

	// Top-level menu slugs to KEEP. Adjust the CTX Feed slug below if your
	// site registers it under a different parent slug — check the URL bar
	// while hovering the top-level "CTX Feed" menu item as an admin.
	$allowed_top_slugs = array(
		'edit.php?post_type=product',
		'webappick-manage-feeds',
	);

	foreach ( $menu as $item ) {
		$slug = $item[2];
		if ( ! in_array( $slug, $allowed_top_slugs, true ) ) {
			remove_menu_page( $slug );
		}
	}
}

/**
 * 3. Send marketer users straight to the Products list instead of the
 * standard wp-admin dashboard (which they no longer have menu access to).
 */
add_action( 'admin_init', 'lgd_marketer_redirect_dashboard' );

function lgd_marketer_redirect_dashboard() {

	global $pagenow;

	$user = wp_get_current_user();

	if ( in_array( 'marketer', (array) $user->roles, true ) && 'index.php' === $pagenow ) {
		wp_safe_redirect( admin_url( 'edit.php?post_type=product' ) );
		exit;
	}
}

/**
 * 4. Also point the admin bar / login redirect at Products for a cleaner UX.
 */
add_filter( 'login_redirect', 'lgd_marketer_login_redirect', 10, 3 );

function lgd_marketer_login_redirect( $redirect_to, $requested_redirect_to, $user ) {

	if ( isset( $user->roles ) && is_array( $user->roles ) && in_array( 'marketer', $user->roles, true ) ) {
		return admin_url( 'edit.php?post_type=product' );
	}

	return $redirect_to;
}