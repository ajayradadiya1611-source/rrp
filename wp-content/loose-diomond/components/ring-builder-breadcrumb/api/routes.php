<?php
/**
 * Ring Builder Breadcrumb — API
 *
 * This module does NOT register any new REST routes. The one
 * network call it makes — clearing a selected diamond/setting/the
 * whole ring — is an existing wp_ajax action, already registered
 * elsewhere and loaded globally via functions.php:
 *
 *   - wp_ajax_ls_rb_clear / wp_ajax_nopriv_ls_rb_clear
 *     → ls_rb_clear()  in inc/comman-function.php
 *
 * This file exists only so the module's structure matches the other
 * components/ tools (index.php always requires api/routes.php) — if
 * ls_rb_clear() is ever removed or renamed, update the reference
 * above (and the fetch() call in
 * react/src/components/RingBuilderBreadcrumb/index.jsx).
 *
 * Loaded automatically via index.php in this folder — don't require
 * it directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}
