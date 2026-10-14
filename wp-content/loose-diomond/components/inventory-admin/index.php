<?php
/**
 * Inventory Admin — module loader
 *
 * React admin for the diamond inventory tables:
 *   - Diamonds          (white, table: ls_inventory)
 *   - Colored Diamonds  (color, table: ls_inventory_c)
 *
 * Wires up:
 *   - the REST API (api/routes.php, namespace ls/v1)
 *   - the wp-admin menu pages + enqueue of the built React app (out/inventory-admin)
 *
 * Loaded from inc-admin/admin-page.php:
 *   require_once get_template_directory() . '/components/inventory-admin/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

// --- API -------------------------------------------------------------
require_once __DIR__ . '/api/routes.php';

// --- Admin pages -------------------------------------------------------
add_action( 'admin_menu', function () {
	add_menu_page(
		'Diamonds', 'Diamonds', 'edit_products', 'ls_mst_diamonds',
		function () { ls_render_inventory_admin_root( 'white' ); }, '', 3
	);
	add_menu_page(
		'Colored Diamonds', 'Colored Diamonds', 'edit_products', 'ls_mst_diamonds_c',
		function () { ls_render_inventory_admin_root( 'color' ); }, '', 4
	);
} );

function ls_render_inventory_admin_root( $type ) {
	echo '<div id="ls-inventory-admin-root" data-type="' . esc_attr( $type ) . '"></div>';
}

// --- React app enqueue ---------------------------------------------------
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, array( 'toplevel_page_ls_mst_diamonds', 'toplevel_page_ls_mst_diamonds_c' ), true ) ) {
		return;
	}

	$dist = get_template_directory() . '/out/inventory-admin';
	$uri  = get_template_directory_uri() . '/out/inventory-admin';

	if ( ! file_exists( $dist . '/index.js' ) ) {
		return; // build not present yet
	}

	wp_enqueue_script(
		'ls-inventory-admin',
		$uri . '/index.js',
		array(),
		filemtime( $dist . '/index.js' ),
		true
	);

	wp_localize_script( 'ls-inventory-admin', 'lsInventoryAdmin', array(
		'root'  => esc_url_raw( rest_url( 'ls/v1/' ) ),
		'nonce' => wp_create_nonce( 'wp_rest' ),
	) );

	// App.scss is compiled by Vite into dist/assets/index.css
	$css_file = $dist . '/assets/index.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'ls-inventory-admin',
			$uri . '/assets/index.css',
			array(),
			filemtime( $css_file )
		);
	}
}, 20 );

// Vite outputs an ES module — load this handle as type="module".
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( $handle !== 'ls-inventory-admin' ) {
		return $tag;
	}
	return '<script type="module" src="' . esc_url( $src ) . '" id="' . $handle . '-js"></script>';
}, 10, 3 );
