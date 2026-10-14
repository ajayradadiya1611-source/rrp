<?php
/**
 * Inventory Listing — "Shape Products Page" ACF fields
 *
 * Adds a field group to any page using the "Shape Products Page"
 * template (page-shape-products.php):
 *
 *   - lgd_filter_query    Preset filters as a query string, e.g.
 *                         style=bezel&shape=emerald-cut&price=1139.00,4472.00
 *                         Any key works (style, shape, price, and any
 *                         future filter) — commas = multiple values.
 *                         Acts exactly like visiting the page with that
 *                         query string. A leading "?" is fine.
 *
 *   + banner image, short_description, short_description_bottom(_description),
 *     bottom_category, oc_related_blog, faq — same names/structure as the
 *     product_cat fields used by woocommerce/archive-product.php.
 *
 * Products always come from the "engagement-rings" category, 12 per page.
 *
 * Real URL params always win, key by key (e.g. ?shape=oval overrides
 * only the shape preset; style/price presets stay).
 *
 * Registered in code so it ships with the theme. No-ops if ACF is off.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( array(
        'key'    => 'group_lgd_shape_page',
        'title'  => 'Product Filter Preset',
        'fields' => array(
            array(
                'key'          => 'field_lgd_filter_query',
                'label'        => 'Filter query',
                'name'         => 'lgd_filter_query',
                'type'         => 'text',
                'placeholder'  => 'style=bezel&shape=emerald-cut&price=1139.00,4472.00',
                'instructions' => 'Paste the part after "?" from a filtered listing URL. Any filter key works; use commas for multiple values.',
                'required'     => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-shape-products.php',
                ),
            ),
        ),
        'position' => 'normal',
        'style'    => 'default',
    ) );
} );

/**
 * Parse a filter query string into filter arrays.
 *   "style=bezel&shape=emerald-cut,oval&price=1139,4472"
 *   → [ 'style' => ['bezel'], 'shape' => ['emerald-cut','oval'], 'price' => [1139.0, 4472.0] ]
 */
function lgd_parse_filter_query( $query ) {
    $query = ltrim( trim( (string) $query ), '?' );
    if ( $query === '' ) return array();

    parse_str( $query, $raw );
    $out = array();

    foreach ( $raw as $key => $val ) {
        $key = sanitize_key( $key );
        if ( $key === '' ) continue;

        $vals = is_array( $val ) ? $val : explode( ',', (string) $val );
        $vals = array_values( array_filter( array_map( 'trim', $vals ), 'strlen' ) );
        if ( ! $vals ) continue;

        if ( $key === 'price' ) {
            $out['price'] = array_map( 'floatval', array_slice( $vals, 0, 2 ) );
        } elseif ( in_array( $key, array( 'ls_sortby', 'ls_sort' ), true ) ) {
            $out[ $key ] = sanitize_text_field( $vals[0] );
        } else {
            $out[ $key ] = array_map( 'sanitize_title', $vals );
        }
    }
    return $out;
}

/**
 * Resolve the preset page settings (with fallbacks).
 * Returns array( 'filters' => array, 'term' => WP_Term|null, 'per_page' => int ).
 */
function lgd_get_shape_page_settings( $page_id = 0 ) {
    $page_id = $page_id ? $page_id : get_queried_object_id();

    $filters = array();
    $term    = null;
    $per     = 12;

    if ( function_exists( 'get_field' ) ) {
        $filters = lgd_parse_filter_query( get_field( 'lgd_filter_query', $page_id ) );
    }

    if ( ! $term ) {
        $t    = get_term_by( 'slug', 'engagement-rings', 'product_cat' );
        $term = $t ? $t : null;
    }

    return array( 'filters' => $filters, 'term' => $term, 'per_page' => $per );
}
