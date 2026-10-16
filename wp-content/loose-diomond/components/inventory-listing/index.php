<?php
/**
 * Get min/max price for products in a given product_cat term (including variations),
 * cached via transient for performance.
 */
function loose_get_category_price_range( $term_id ) {
    $cache_key = 'loose_cat_price_range_' . $term_id;
    $cached    = get_transient( $cache_key );
    if ( $cached !== false ) {
        return $cached;
    }

    global $wpdb;

    $result = $wpdb->get_row( $wpdb->prepare("
        SELECT
            MIN(CAST(pm.meta_value AS DECIMAL(10,2))) AS min_price,
            MAX(CAST(pm.meta_value AS DECIMAL(10,2))) AS max_price
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
        INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        WHERE pm.meta_key = '_price'
            AND pm.meta_value != ''
            AND p.post_status = 'publish'
            AND p.post_type IN ('product','product_variation')
            AND tt.taxonomy = 'product_cat'
            AND tt.term_id = %d
    ", $term_id ) );

    if ( $result && $result->min_price !== null ) {
        set_transient( $cache_key, $result, 12 * HOUR_IN_SECONDS );
        return $result;
    }

    return false;
}

/**
 * Inventory Listing — module loader
 *
 * Renders the paginated/filterable diamond product grid (moved out
 * of react/inventory/App3.jsx). Filters (style/shape/price), sorting,
 * pagination and the wishlist toggle are all handled client-side by
 * this module's own React build.
 *
 * This is the ONLY file functions.php needs to require for this
 * feature. It wires up:
 *   - api/routes.php (no new routes — see that file for why)
 *   - the frontend script/style enqueue + window.LD_THEME localization
 *     for the built React app (out/inventory-listing), on the same
 *     pages the old shared bundle used to cover it on: product
 *     categories, product search results, page-lab-grown-engagement-
 *     rings.php, and single product pages
 *   - a template helper, lgd_render_inventory_listing(), that any
 *     template can call to print the mount point
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/inventory-listing/index.php';
 *
 * This replaces the corresponding block that used to live in
 * inc/script-load.php's loose_diomond_scripts_react() (the block
 * enqueuing 'loose-diomond-inventory' for is_product_category() /
 * search / page-lab-grown-engagement-rings.php / is_product()) — that
 * block moved into this file. The rest of that function (App.jsx/
 * App2.jsx/SimilarDiamonds.jsx's page-inventory.php / page-inventory2.php
 * / page-ring-preview.php / diamond_sku bundle) moved to its own
 * module too: components/inventory-app/index.php.
 *
 * loose_get_category_price_range() (below) used to live in
 * inc/script-load.php too — moved here since this module is its only
 * remaining caller.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API (no new routes — see api/routes.php) --------------------------
require_once __DIR__ . '/api/routes.php';

// --- ACF fields for the "Shape Products Page" template -----------------
require_once __DIR__ . '/inc/acf-shape-page.php';

// --- Frontend enqueue + localization ------------------------------------

/**
 * Print the mount point for the product grid. The script/style enqueue
 * and window.LD_THEME localization happen separately, automatically,
 * via the wp_enqueue_scripts hook below — this just prints the div.
 * Call it from any template that needs the product grid, in place of
 * the old bare `<div id="ls-inventory-root3"></div>`.
 */
function lgd_render_inventory_listing() {
    echo '<div id="inventory-listing-root"></div>';
}

add_action( 'wp_enqueue_scripts', 'lgd_enqueue_inventory_listing' );

function lgd_enqueue_inventory_listing() {

    if ( ! (
        is_product_category()
        || ( ! empty( $_GET['s'] ) && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' )
        || is_page_template( 'page-lab-grown-engagement-rings.php' )
        || is_page_template( 'page-shape-products.php' )
        || is_product()
        || is_shop()
    ) ) {
        return;
    }

    $dist = get_template_directory() . '/out/inventory-listing';
    $uri  = get_template_directory_uri() . '/out/inventory-listing';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build` in components/inventory-listing/react/
    }

    $script_ver = filemtime( $dist . '/index.js' );

    wp_enqueue_script(
        'lgd-inventory-listing',
        $uri . '/index.js',
        array(),
        $script_ver,
        true
    );

    // Vite builds this as an ES module. On single product pages this
    // script loads alongside the older shared 'loose-diomond-inventory'
    // bundle (react/inventory/'s successor, components/inventory-app/, still serving App.jsx/App2.jsx).
    // As plain classic scripts, both would share the page's top-level
    // lexical scope, and their independently-minified top-level
    // `const` bindings (e.g. a bundled React import) can collide —
    // "Identifier '...' has already been declared". Loading this one
    // as a module gives it its own scope and avoids that entirely.
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( $handle !== 'lgd-inventory-listing' ) {
            return $tag;
        }
        return str_replace( ' src', ' type="module" src', preg_replace( '/\stype=["\'][^"\']*["\']/', '', $tag, 1 ) );
    }, 10, 2 );

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'lgd-inventory-listing',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }

    // --- cart product IDs (for "already in cart" state on cards) -------
    $cart_product_ids = array();
    if ( class_exists( 'WooCommerce' ) && WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $product_id = ! empty( $cart_item['variation_id'] )
                ? $cart_item['variation_id']
                : $cart_item['product_id'];
            $cart_product_ids[] = $product_id;
        }
        $cart_product_ids = array_unique( $cart_product_ids );
    }

    // --- price range (category default, or theme-wide fallback) --------
    $min_price = 0;
    $max_price = 50000;

    if ( is_product_category() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $price_range = loose_get_category_price_range( $term->term_id );
           
            if ( $price_range ) {
                $min_price = $price_range->min_price;
                $max_price = $price_range->max_price;
            }
        }
    }

    // --- Shape Products Page: ACF "Filter query" acts like a URL query --
    //     e.g. style=bezel&shape=emerald-cut&price=1139.00,4472.00
    $shape_page = is_page_template( 'page-shape-products.php' )
        ? lgd_get_shape_page_settings()
        : null;

    if ( $shape_page && $shape_page['term'] ) {
        $price_range = loose_get_category_price_range( $shape_page['term']->term_id );
        if ( $price_range ) {
            $min_price = $price_range->min_price;
            $max_price = $price_range->max_price;
        }
    }

    $min_carat = '0';
    $max_carat = '50.56';

    $filters_arr = array(
        'style'       => isset( $_GET['style'] ) ? array( $_GET['style'] ) : array(),
        'shape'       => isset( $_GET['shape'] ) ? array( $_GET['shape'] ) : array(),
        'price_range' => isset( $_GET['price'] )
            ? array_map( 'floatval', explode( ',', $_GET['price'] ) )
            : array( (float) $min_price, (float) $max_price ),
        'ls_sortby'   => isset( $_GET['ls_sortby'] ) ? $_GET['ls_sortby'] : '',
        'ls_sort'     => isset( $_GET['ls_sort'] ) ? $_GET['ls_sort'] : '',
    );

    // Preset page: merge ACF preset, then real URL params win key by key.
    // Any key (style, shape, price, future ones) is supported.
    if ( $shape_page ) {
        // Only URL keys that the preset uses (ignores utm_*, fbclid, etc.).
        $from_url = array_intersect_key(
            lgd_parse_filter_query( isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '' ),
            $shape_page['filters']
        );
        $merged = array_merge( $shape_page['filters'], $from_url );
        foreach ( $merged as $key => $val ) {
            if ( $key === 'price' ) {
                if ( count( $val ) === 2 ) $filters_arr['price_range'] = $val;
            } else {
                $filters_arr[ $key ] = $val;
            }
        }
    }

    $filters_default = array(
        'shape'       => array(),
        'style'       => array(),
        'price_range' => array( (float) $min_price, (float) $max_price ),
        'ls_sortby'   => '',
        'ls_sort'     => '',
    );

    $localize_data = array(
        'templateUrl'    => get_template_directory_uri(),
        'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
        'API_BASE_URL'   => site_url() . '/wp-json/ls/v1',
        'SITE_URL'       => site_url(),
        'filters'        => $filters_arr,
        'filtersDefault' => $filters_default,
        'cartProductIds' => $cart_product_ids,
        'inventoryRange' => array(
            'min_price' => (int) $min_price,
            'max_price' => (int) $max_price,
            'min_carat' => (float) $min_carat,
            'max_carat' => (float) $max_carat,
        ),
        'with_ordering'  => true,
        'perPage'        => 21,
    );

    if ( is_product_category() ) {
        $category                       = get_queried_object();
        $localize_data['categoryId']    = $category->term_id;
        $localize_data['categorySlug']  = $category->slug;
        $localize_data['taxonomy']      = 'product_cat';

        $parent_category = $category->parent ? get_term( $category->parent, 'product_cat' ) : null;

        $localize_data['parentCategoryId']   = $parent_category ? $parent_category->term_id : 0;
        $localize_data['parentCategorySlug'] = $parent_category ? $parent_category->slug : '';

    } elseif ( ! empty( $_GET['s'] ) && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
        $localize_data['searchQuery'] = $_GET['s'];

    } elseif ( function_exists( 'is_shop' ) && is_shop() ) {
        // /shop/ has no category or search term: list every product
        $localize_data['isShop'] = true;

    } elseif ( is_product() ) {
        $product_id = get_the_ID();

        $terms = get_the_terms( $product_id, 'product_cat' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $term                           = $terms[0];
            $localize_data['categoryId']   = $term->term_id;
            $localize_data['categorySlug'] = $term->slug;
        }

        $localize_data['taxonomy']      = 'product_cat';
        $localize_data['with_ordering'] = false;
        $localize_data['perPage']       = 8;
    }

    // Categories that list only specific shape terms (slugs, in display order)
    $term_include_by_category = array(
        'black-diamonds' => array(
            'shape' => array(
                'radiant',
                'princess',
                'pear',
                'oval',
                'marquise',
                'heart',
                'cushion',
                'asscher',
                'emerald',
            ),
        ),
    );
    foreach ( array( 'categorySlug', 'parentCategorySlug' ) as $slug_key ) {
        $slug = isset( $localize_data[ $slug_key ] ) ? $localize_data[ $slug_key ] : '';
        if ( $slug && isset( $term_include_by_category[ $slug ] ) ) {
            $localize_data['termInclude'] = $term_include_by_category[ $slug ];
            break;
        }
    }

    wp_localize_script(
        'lgd-inventory-listing',
        'LD_THEME',
        $localize_data
    );
}
