<?php
/**
 * Category Product Position — REST API + frontend ordering helper
 *
 *   GET  /lgd/v1/product-position/categories            product_cat tree (id, name, parent, count, has_order)
 *   GET  /lgd/v1/product-position/products?term_id=12   products of a category, in saved order
 *   POST /lgd/v1/product-position/save   { term_id, product_ids: [] }   save order
 *   POST /lgd/v1/product-position/reset  { term_id }                    clear saved order
 *
 * Storage: term meta `_lgd_product_positions` on the category = ordered array
 * of product IDs (index 0 = position 1).
 *
 * "Best Seller" order on the frontend (lgd_ppos_apply_best_seller_order()):
 *   1. products with a saved position, in that order
 *   2. everything else by WooCommerce `total_sales` DESC, then newest first
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'LGD_PPOS_NS' ) ) {
    define( 'LGD_PPOS_NS', 'lgd/v1' );
}
if ( ! defined( 'LGD_PPOS_META' ) ) {
    define( 'LGD_PPOS_META', '_lgd_product_positions' );
}

add_action( 'rest_api_init', function () {

    register_rest_route( LGD_PPOS_NS, '/product-position/categories', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_ppos_categories',
        'permission_callback' => 'lgd_ppos_permission_check',
    ) );

    register_rest_route( LGD_PPOS_NS, '/product-position/products', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_ppos_products',
        'permission_callback' => 'lgd_ppos_permission_check',
        'args'                => array(
            'term_id' => array(
                'required'          => true,
                'validate_callback' => function ( $v ) { return is_numeric( $v ) && (int) $v > 0; },
            ),
        ),
    ) );

    register_rest_route( LGD_PPOS_NS, '/product-position/save', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_ppos_save',
        'permission_callback' => 'lgd_ppos_permission_check',
        'args'                => array(
            'term_id'     => array(
                'required'          => true,
                'validate_callback' => function ( $v ) { return is_numeric( $v ) && (int) $v > 0; },
            ),
            'product_ids' => array(
                'required'          => true,
                'validate_callback' => function ( $v ) { return is_array( $v ); },
            ),
        ),
    ) );

    register_rest_route( LGD_PPOS_NS, '/product-position/reset', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_ppos_reset',
        'permission_callback' => 'lgd_ppos_permission_check',
        'args'                => array(
            'term_id' => array(
                'required'          => true,
                'validate_callback' => function ( $v ) { return is_numeric( $v ) && (int) $v > 0; },
            ),
        ),
    ) );
} );

function lgd_ppos_permission_check( WP_REST_Request $request ) {
    return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
}

/**
 * Saved order for a term (array of int product IDs).
 */
function lgd_ppos_get_order( $term_id ) {
    $ids = get_term_meta( (int) $term_id, LGD_PPOS_META, true );
    return is_array( $ids ) ? array_values( array_filter( array_map( 'intval', $ids ) ) ) : array();
}

/**
 * GET /product-position/categories
 */
function lgd_ppos_categories( WP_REST_Request $request ) {
    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'orderby'    => 'name',
    ) );

    if ( is_wp_error( $terms ) ) {
        return $terms;
    }

    $items = array();
    foreach ( $terms as $t ) {
        $items[] = array(
            'id'        => (int) $t->term_id,
            'name'      => html_entity_decode( $t->name ),
            'slug'      => $t->slug,
            'parent'    => (int) $t->parent,
            'count'     => (int) $t->count,
            'has_order' => count( lgd_ppos_get_order( $t->term_id ) ) > 0,
        );
    }

    return rest_ensure_response( array( 'items' => $items ) );
}

/**
 * GET /product-position/products?term_id=
 * Returns products of the category (incl. children cats, like the frontend)
 * already sorted the same way the frontend "Best Seller" sort will show them.
 */
function lgd_ppos_products( WP_REST_Request $request ) {
    $term_id = (int) $request->get_param( 'term_id' );
    $term    = get_term( $term_id, 'product_cat' );
    if ( ! $term || is_wp_error( $term ) ) {
        return new WP_Error( 'invalid_term', 'Category not found.', array( 'status' => 404 ) );
    }

    $ids = get_posts( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array( array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $term_id,
        ) ),
        'lgd_best_seller_term' => $term_id,
        'suppress_filters'     => false,
    ) );

    $saved     = lgd_ppos_get_order( $term_id );
    $saved_pos = array_flip( $saved );

    $items = array();
    foreach ( $ids as $id ) {
        $product = wc_get_product( $id );
        if ( ! $product ) {
            continue;
        }
        $thumb = get_the_post_thumbnail_url( $id, 'thumbnail' );
        $items[] = array(
            'id'           => (int) $id,
            'title'        => $product->get_name(),
            'sku'          => (string) $product->get_sku(),
            'price'        => (string) $product->get_price(),
            'stock_status' => (string) $product->get_stock_status(),
            'total_sales'  => (int) $product->get_total_sales(),
            'image'        => $thumb ? $thumb : '',
            'edit_url'     => get_edit_post_link( $id, 'raw' ),
            'saved'        => isset( $saved_pos[ $id ] ),
        );
    }

    return rest_ensure_response( array(
        'term'        => array( 'id' => $term_id, 'name' => html_entity_decode( $term->name ) ),
        'saved_count' => count( $saved ),
        'items'       => $items,
    ) );
}

/**
 * POST /product-position/save
 */
function lgd_ppos_save( WP_REST_Request $request ) {
    $term_id = (int) $request->get_param( 'term_id' );
    $term    = get_term( $term_id, 'product_cat' );
    if ( ! $term || is_wp_error( $term ) ) {
        return new WP_Error( 'invalid_term', 'Category not found.', array( 'status' => 404 ) );
    }

    $ids = array();
    foreach ( (array) $request->get_param( 'product_ids' ) as $id ) {
        $id = (int) $id;
        if ( $id > 0 && ! in_array( $id, $ids, true ) && get_post_type( $id ) === 'product' ) {
            $ids[] = $id;
        }
    }

    update_term_meta( $term_id, LGD_PPOS_META, $ids );

    return rest_ensure_response( array( 'success' => true, 'term_id' => $term_id, 'count' => count( $ids ) ) );
}

/**
 * POST /product-position/reset
 */
function lgd_ppos_reset( WP_REST_Request $request ) {
    $term_id = (int) $request->get_param( 'term_id' );
    delete_term_meta( $term_id, LGD_PPOS_META );
    return rest_ensure_response( array( 'success' => true, 'term_id' => $term_id ) );
}

/* -------------------------------------------------------------------------
 * Frontend ordering
 * ---------------------------------------------------------------------- */

/**
 * Call on WP_Query args to sort by "Best Seller" for a category.
 * $term_id may be 0 (e.g. search page) → only total_sales ordering.
 */
function lgd_ppos_apply_best_seller_order( array $args, $term_id = 0 ) {
    $args['lgd_best_seller_term'] = (int) $term_id;
    $args['suppress_filters']     = false;
    return $args;
}

add_filter( 'posts_clauses', function ( $clauses, $query ) {
    $term_id = $query->get( 'lgd_best_seller_term', null );
    if ( $term_id === null || $term_id === '' ) {
        return $clauses;
    }

    global $wpdb;
    $term_id = (int) $term_id;

    // total_sales join (alias avoids clashing with meta_query joins)
    $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} lgd_ts ON ( lgd_ts.post_id = {$wpdb->posts}.ID AND lgd_ts.meta_key = 'total_sales' ) ";

    $order = array();

    $saved = $term_id ? lgd_ppos_get_order( $term_id ) : array();
    if ( ! empty( $saved ) ) {
        // FIELD() returns 0 for "not in list"; reverse list + DESC puts
        // saved products first (in saved order) and the rest after.
        $list    = implode( ',', array_map( 'intval', array_reverse( $saved ) ) );
        $order[] = "FIELD({$wpdb->posts}.ID, {$list}) DESC";
    }

    $order[] = 'CAST(COALESCE(lgd_ts.meta_value, 0) AS UNSIGNED) DESC';
    $order[] = "{$wpdb->posts}.post_date DESC";
    $order[] = "{$wpdb->posts}.ID DESC";

    $clauses['orderby'] = implode( ', ', $order );

    return $clauses;
}, 20, 2 );

/**
 * Keep positions clean: when a product is permanently deleted, drop it from
 * every saved category order.
 */
add_action( 'before_delete_post', function ( $post_id ) {
    if ( get_post_type( $post_id ) !== 'product' ) {
        return;
    }
    $terms = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
    if ( is_wp_error( $terms ) ) {
        return;
    }
    foreach ( $terms as $term_id ) {
        $ids = lgd_ppos_get_order( $term_id );
        if ( in_array( (int) $post_id, $ids, true ) ) {
            update_term_meta( $term_id, LGD_PPOS_META, array_values( array_diff( $ids, array( (int) $post_id ) ) ) );
        }
    }
} );
