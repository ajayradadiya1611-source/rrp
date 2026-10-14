<?php
/**
 * Ring Preview Size — module loader
 *
 * Diamond Carat Size Guide (`.dcsg-*`): lets a shopper tap a carat-size
 * button to preview the ring at that relative scale on the hand image.
 * Init logic previously lived as an inline <script> in
 * inc/comman-function.php (inside oc_wp_footer()); it's now built from
 * this component's react/src (vanilla JS, no React needed) via Vite,
 * with the build output going to the theme's root /out/ring-preview-size
 * directory and enqueued here.
 *
 * Renders wherever inc/diamond-detail-render.php outputs the
 * `.dcsg-container` markup (product pages / ring preview pages).
 *
 * This is the ONLY file functions.php needs to require for this feature.
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/ring-preview-size/index.php';
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

add_action( 'wp_enqueue_scripts', 'lgd_enqueue_ring_preview_size' );

function lgd_enqueue_ring_preview_size() {
	// Loads on the same templates/contexts where the .dcsg-container markup is rendered
	if ( ! (
		is_page_template( array( 'page-ring-preview.php' ) ) ||
		is_product() ||
		get_query_var( 'diamond_sku' )
	) ) {
		return;
	}

	$dist = get_template_directory() . '/out/ring-preview-size';
	$uri  = get_template_directory_uri() . '/out/ring-preview-size';

	if ( ! file_exists( $dist . '/index.js' ) ) {
		return; // not built yet — run `npm run build` (see components/ring-preview-size/react)
	}

	wp_enqueue_script(
		'lgd-ring-preview-size',
		$uri . '/index.js',
		array(),
		filemtime( $dist . '/index.js' ),
		true
	);

	// dcsg.scss is compiled by Vite into a CSS file under out/ring-preview-size/assets — enqueue it if present.
	$css_file = $dist . '/assets/index.css';
	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'lgd-ring-preview-size',
			$uri . '/assets/index.css',
			array(),
			filemtime( $css_file )
		);
	}
}
