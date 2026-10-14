<?php
/**
 * Zero Price Products — REST API
 *
 *   GET  /lgd/v1/zero-price-products            list products with price 0 / empty
 *   POST /lgd/v1/zero-price-products/delete     { product_id }
 *   POST /lgd/v1/zero-price-products/delete-bulk { product_ids: [] }
 *
 * "Zero price" = the product's `_price` meta is missing, empty, or
 * numerically 0. Only top-level `product` posts are considered
 * (variations are skipped). Trashed / auto-draft posts are excluded.
 *
 * Delete = FORCE delete (bypasses Trash, permanent). The price is
 * re-checked server-side before deleting, so a product that got a
 * price in the meantime is never removed.
 *
 * Inventory rows (ls_inventory / ls_inventory_c) are NOT touched — a
 * stale `pid` is already treated as "no product" elsewhere
 * (e.g. lgd_sale_product_exists()) and the add-to-cart flow recreates it.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'LGD_ZERO_PRICE_NS' ) ) {
    define( 'LGD_ZERO_PRICE_NS', 'lgd/v1' );
}

add_action( 'rest_api_init', function () {

    register_rest_route( LGD_ZERO_PRICE_NS, '/zero-price-products', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_zero_price_list',
        'permission_callback' => 'lgd_zero_price_permission_check',
    ) );

    register_rest_route( LGD_ZERO_PRICE_NS, '/zero-price-products/delete', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_zero_price_delete',
        'permission_callback' => 'lgd_zero_price_permission_check',
        'args'                => array(
            'product_id' => array(
                'required'          => true,
                'validate_callback' => function ( $v ) {
                    return is_numeric( $v ) && (int) $v > 0;
                },
            ),
        ),
    ) );

    register_rest_route( LGD_ZERO_PRICE_NS, '/zero-price-products/delete-bulk', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_zero_price_delete_bulk',
        'permission_callback' => 'lgd_zero_price_permission_check',
        'args'                => array(
            'product_ids' => array(
                'required'          => true,
                'validate_callback' => function ( $v ) {
                    return is_array( $v ) && count( $v ) > 0;
                },
            ),
        ),
    ) );
} );

function lgd_zero_price_permission_check( WP_REST_Request $request ) {
    return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
}

/**
 * True if a price meta value counts as "zero".
 */
function lgd_zero_price_is_zero( $price ) {
    if ( $price === null || $price === '' ) {
        return true;
    }
    return is_numeric( $price ) && (float) $price == 0.0;
}

/**
 * GET /lgd/v1/zero-price-products
 */
function lgd_zero_price_list( WP_REST_Request $request ) {
    global $wpdb;

    // LEFT JOIN so products with no _price meta at all are included too.
    $rows = $wpdb->get_results(
        "SELECT p.ID, p.post_title, p.post_status, p.post_date,
                pm.meta_value  AS price,
                rp.meta_value  AS regular_price,
                sku.meta_value AS sku,
                st.meta_value  AS stock_status
         FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} pm  ON pm.post_id  = p.ID AND pm.meta_key  = '_price'
         LEFT JOIN {$wpdb->postmeta} rp  ON rp.post_id  = p.ID AND rp.meta_key  = '_regular_price'
         LEFT JOIN {$wpdb->postmeta} sku ON sku.post_id = p.ID AND sku.meta_key = '_sku'
         LEFT JOIN {$wpdb->postmeta} st  ON st.post_id  = p.ID AND st.meta_key  = '_stock_status'
         WHERE p.post_type = 'product'
           AND p.post_status NOT IN ('trash', 'auto-draft')
           AND ( pm.meta_value IS NULL OR pm.meta_value = '' OR CAST(pm.meta_value AS DECIMAL(20,4)) = 0 )
         GROUP BY p.ID
         ORDER BY p.ID DESC"
    );

    $items = array();
    foreach ( (array) $rows as $row ) {
        $tags = wp_get_post_terms( (int) $row->ID, 'product_tag', array( 'fields' => 'slugs' ) );
        $type = '';
        if ( ! is_wp_error( $tags ) ) {
            if ( in_array( 'white-diamond', $tags, true ) ) {
                $type = 'white-diamond';
            } elseif ( in_array( 'color-diamond', $tags, true ) ) {
                $type = 'color-diamond';
            }
        }

        $items[] = array(
            'product_id'    => (int) $row->ID,
            'title'         => $row->post_title,
            'sku'           => (string) $row->sku,
            'status'        => $row->post_status,
            'stock_status'  => (string) $row->stock_status,
            'price'         => $row->price === null ? null : (string) $row->price,
            'regular_price' => $row->regular_price === null ? null : (string) $row->regular_price,
            'type'          => $type,
            'date'          => $row->post_date,
        );
    }

    return rest_ensure_response( array(
        'count' => count( $items ),
        'items' => $items,
    ) );
}

/**
 * Core delete — re-validates the price, then force-deletes.
 */
function lgd_zero_price_delete_product( $product_id ) {
    $product_id = (int) $product_id;
    $post       = get_post( $product_id );

    if ( ! $post || $post->post_type !== 'product' ) {
        return array( 'product_id' => $product_id, 'deleted' => false, 'error' => 'Product not found.' );
    }

    $price = get_post_meta( $product_id, '_price', true );
    if ( ! lgd_zero_price_is_zero( $price ) ) {
        return array( 'product_id' => $product_id, 'deleted' => false, 'error' => 'Product now has a price (' . $price . ') — skipped.' );
    }

    $product = wc_get_product( $product_id );
    if ( $product ) {
        // Removes variations/children properly for variable products.
        if ( $product->is_type( 'variable' ) ) {
            foreach ( $product->get_children() as $child_id ) {
                wp_delete_post( $child_id, true );
            }
        }
        $deleted = (bool) $product->delete( true );
    } else {
        $deleted = (bool) wp_delete_post( $product_id, true );
    }

    return $deleted
        ? array( 'product_id' => $product_id, 'deleted' => true )
        : array( 'product_id' => $product_id, 'deleted' => false, 'error' => 'Delete failed.' );
}

function lgd_zero_price_delete( WP_REST_Request $request ) {
    $result = lgd_zero_price_delete_product( $request->get_param( 'product_id' ) );

    if ( ! $result['deleted'] ) {
        return new WP_Error( 'delete_failed', $result['error'], array( 'status' => 400 ) );
    }

    return rest_ensure_response( array_merge( array( 'success' => true ), $result ) );
}

function lgd_zero_price_delete_bulk( WP_REST_Request $request ) {
    $results = array();
    foreach ( (array) $request->get_param( 'product_ids' ) as $id ) {
        $id = (int) $id;
        if ( $id > 0 ) {
            $results[] = lgd_zero_price_delete_product( $id );
        }
    }

    return rest_ensure_response( array(
        'success' => true,
        'count'   => count( $results ),
        'results' => $results,
    ) );
}
