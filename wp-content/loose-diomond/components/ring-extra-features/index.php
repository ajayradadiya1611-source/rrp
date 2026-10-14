<?php
/**
 * Ring Extra Features — module loader
 *
 * Lets customers add paid upgrades to a ring on the Ring Preview page
 * via an "Add Extra Feature" button + modal. Groups and options are
 * FIXED in code (Band Width, Prong, East West, Two Tone — see
 * inc/group-definitions.php); the only thing that varies is price,
 * per product, set via ACF fields directly on the product edit screen
 * (see inc/acf-fields.php). There is no separate admin screen for this
 * — ACF on the product IS the admin UI.
 *
 * Requires the ACF or ACF Pro plugin to be active.
 *
 * This is the ONLY file functions.php needs to require for this feature.
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/ring-extra-features/index.php';
 *
 * Front-end usage (page-ring-preview.php, before the Add To Bag button,
 * passing the SETTING product's ID so prices match that product):
 *   if ( class_exists( 'LGD_Ring_Extra_Features' ) ) {
 *       LGD_Ring_Extra_Features::render_button_and_modal( $sp_id );
 *   }
 *
 * Cart integration (functions.php, inside ls_rb_addtobag(), right before
 * $custom_data_diamond = ls_prepare_cart_item_data(...) for the setting
 * item) — see modified-theme-files/functions.php in this zip, tagged
 * "LGD EXTRA FEATURES", for the exact snippet.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

require_once __DIR__ . '/inc/group-definitions.php';
require_once __DIR__ . '/inc/acf-fields.php';
require_once __DIR__ . '/inc/class-ring-extra-features.php';

LGD_Ring_Extra_Features::init();
