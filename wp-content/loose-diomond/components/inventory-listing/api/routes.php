<?php
/**
 * Inventory Listing — API
 *
 * This module does NOT register any new REST routes. The product
 * grid fetches from the theme's existing shared endpoints, already
 * registered elsewhere and loaded globally via functions.php:
 *
 *   - GET /wp-json/custom/v1/get-products          → inc/ls-category-product-get-api.php
 *   - GET /wp-json/custom/v1/get-attribute-terms    → inc/ls-category-product-get-api.php
 *
 * Those endpoints are also used by App.jsx / App2.jsx (the other
 * apps still living in react/inventory/), so they intentionally stay
 * defined in one shared place instead of being duplicated per module.
 *
 * This file exists only so the module's structure matches the other
 * components/ tools (index.php always requires api/routes.php) — if
 * inc/ls-category-product-get-api.php is ever removed or renamed,
 * update the paths above.
 *
 * Loaded automatically via index.php in this folder — don't require
 * it directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}
