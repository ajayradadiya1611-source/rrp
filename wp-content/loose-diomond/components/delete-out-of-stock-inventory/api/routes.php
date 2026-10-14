<?php
/**
 * Delete Out of Stock Inventory — REST API
 *
 * Two separate, explicit delete flows:
 *   - color-diamond  -> deletes from `ls_inventory_c` WHERE pid = product_id
 *   - white-diamond  -> deletes from `ls_inventory`   WHERE pid = product_id
 *
 * Both flows also FORCE delete the matching WooCommerce product
 * (bypasses Trash entirely — permanent delete), so the record and the
 * product are removed together, not left orphaned.
 *
 * Products that are out of stock but have NO row at all in the relevant
 * inventory table are still listed (flagged `no-inventory-record`) and can
 * still be deleted — the delete just skips the DB row step for those.
 *
 * Only products that have been out of stock for at least 45 days are
 * surfaced by the listing endpoint (see `date_query` in
 * lgd_get_out_of_stock_inventory) — recently-out-of-stock products are
 * intentionally withheld to give stock a chance to be replenished.
 *
 * A second, separate listing (`/in-stock-no-inventory`) surfaces the
 * opposite situation: products that ARE in stock but have no row at all in
 * the relevant inventory table. Same 45-day post_modified age gate, same
 * product_tag scoping. These are listed in their own tab in the UI and are
 * deletable through the SAME delete / delete-bulk endpoints used for the
 * out-of-stock list (they never had an inventory row to begin with, so the
 * DB-delete step is simply a no-op for them).
 *
 * Single delete and bulk delete are both supported.
 *
 * This file is loaded automatically via index.php in the parent
 * `delete-out-of-stock-inventory` folder — don't require it directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

define( 'LGD_DELETE_INV_NS', 'lgd/v1' );

add_action( 'rest_api_init', function () {

    // List out-of-stock inventory rows for a given type, to pick a product to delete
    register_rest_route( LGD_DELETE_INV_NS, '/out-of-stock-inventory', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_get_out_of_stock_inventory',
        'permission_callback' => 'lgd_delete_inventory_permission_check',
        'args'                => array(
            'type' => array(
                'required' => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
        ),
    ) );

    // List in-stock products that have NO row in the relevant inventory table
    register_rest_route( LGD_DELETE_INV_NS, '/in-stock-no-inventory', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_get_in_stock_no_inventory',
        'permission_callback' => 'lgd_delete_inventory_permission_check',
        'args'                => array(
            'type' => array(
                'required' => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
        ),
    ) );

    // Delete a single record (fully — DB row if it exists + product), by type + product id
    register_rest_route( LGD_DELETE_INV_NS, '/delete-inventory', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_delete_out_of_stock_inventory',
        'permission_callback' => 'lgd_delete_inventory_permission_check',
        'args'                => array(
            'type' => array(
                'required' => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
            'product_id' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return is_numeric( $value ) && (int) $value > 0;
                },
            ),
        ),
    ) );

    // Bulk delete — same rules as single delete, applied to a list of product ids
    register_rest_route( LGD_DELETE_INV_NS, '/delete-inventory-bulk', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_bulk_delete_out_of_stock_inventory',
        'permission_callback' => 'lgd_delete_inventory_permission_check',
        'args'                => array(
            'type' => array(
                'required' => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
            'product_ids' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return is_array( $value ) && count( $value ) > 0;
                },
            ),
        ),
    ) );
} );

/**
 * Only logged-in admins / shop managers can use this. Adjust capability
 * if you use a different role for inventory management.
 */
function lgd_delete_inventory_permission_check( WP_REST_Request $request ) {
    return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
}

/**
 * Map the request "type" to its DB table.
 *   color-diamond -> ls_inventory_c
 *   white-diamond -> ls_inventory
 */
function lgd_inventory_table_for_type( $type ) {
    return $type === 'color-diamond' ? 'ls_inventory_c' : 'ls_inventory';
}

/**
 * Map the request "type" to the WooCommerce product_tag slug used to
 * identify this diamond type ('color-diamond' or 'white-diamond' — same
 * slug as the request's `type` param).
 */
function lgd_product_tag_for_type( $type ) {
    return $type; // 'color-diamond' or 'white-diamond'
}

/**
 * How many days a product must have been sitting out-of-stock before it
 * is surfaced by the listing endpoint / eligible to show up for delete.
 */
function lgd_out_of_stock_min_age_days() {
    return 45;
}

/**
 * GET /lgd/v1/out-of-stock-inventory?type=color-diamond|white-diamond
 *
 * Source of truth is WooCommerce (products tagged 'color-diamond' or
 * 'white-diamond' via product_tag, with stock_status = outofstock) — NOT
 * the inventory table. The inventory table is only used to enrich a row
 * (sku/shape/carat) when a matching pid happens to exist there.
 *
 * Only products that have been out of stock for at least
 * lgd_out_of_stock_min_age_days() (45) days are included. WooCommerce has
 * no dedicated "stock status changed" timestamp, so `post_modified` is
 * used as the proxy — stock updates (via admin, import, or API) touch the
 * product's modified date. A product modified more recently than the
 * cutoff is skipped even if it's currently out of stock; it starts
 * appearing once it crosses the age threshold.
 *
 *   - stock_status = 'outofstock'          -> product out of stock, has a matching inventory row
 *   - stock_status = 'no-inventory-record'  -> product out of stock, no row in ls_inventory / ls_inventory_c
 */
function lgd_get_out_of_stock_inventory( WP_REST_Request $request ) {
    global $wpdb;

    $type  = $request->get_param( 'type' );
    $table = lgd_inventory_table_for_type( $type );

    // Guard: only ever operate on these two known, hard-coded table names.
    if ( ! in_array( $table, array( 'ls_inventory', 'ls_inventory_c' ), true ) ) {
        return new WP_Error( 'invalid_table', 'Invalid inventory type.', array( 'status' => 400 ) );
    }

    $tag = lgd_product_tag_for_type( $type );

    $min_age_days = lgd_out_of_stock_min_age_days();
    $cutoff_date  = gmdate( 'Y-m-d H:i:s', strtotime( "-{$min_age_days} days" ) );

    // Source of truth is WooCommerce's out-of-stock products tagged with
    // this diamond type — NOT the inventory table. A product with no row in
    // ls_inventory / ls_inventory_c still shows up here.
    $oos_query = new WP_Query( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $tag,
            ),
        ),
        'meta_query'     => array(
            array(
                'key'   => '_stock_status',
                'value' => 'outofstock',
            ),
        ),
        // Only products last modified BEFORE the cutoff — i.e. that have
        // been sitting untouched (and therefore presumably out of stock)
        // for at least $min_age_days days.
        'date_query'     => array(
            array(
                'column'    => 'post_date',
                'before'    => $cutoff_date,
                'inclusive' => false,
            ),
        ),
    ) );

    $product_ids = array_map( 'intval', $oos_query->posts );
    wp_reset_postdata();

    $results = array();

    if ( ! empty( $product_ids ) ) {
        // Pull any matching inventory rows in one query, keyed by pid, purely
        // to enrich the row (sku/shape/carat) when one happens to exist.
        $placeholders = implode( ',', array_fill( 0, count( $product_ids ), '%d' ) );
        $inv_rows     = $wpdb->get_results(
            $wpdb->prepare( "SELECT id, pid, sku, shape, carat FROM {$table} WHERE pid IN ({$placeholders})", $product_ids )
        );

        $inv_by_pid = array();
        foreach ( (array) $inv_rows as $row ) {
            $inv_by_pid[ (int) $row->pid ] = $row;
        }

        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( ! $product ) {
                continue;
            }

            $inv = isset( $inv_by_pid[ $product_id ] ) ? $inv_by_pid[ $product_id ] : null;

            $results[] = array(
                'inventory_id' => $inv ? (int) $inv->id : null,
                'product_id'   => $product_id,
                'sku'          => $inv ? $inv->sku : $product->get_sku(),
                'shape'        => $inv ? $inv->shape : '',
                'carat'        => $inv ? $inv->carat : '',
                'stock_status' => $inv ? 'outofstock' : 'no-inventory-record',
                'title'        => $product->get_name(),
            );
        }
    }

    return rest_ensure_response( array(
        'type'         => $type,
        'table'        => $table,
        'min_age_days' => $min_age_days,
        'count'        => count( $results ),
        'items'        => $results,
    ) );
}

/**
 * GET /lgd/v1/in-stock-no-inventory?type=color-diamond|white-diamond
 *
 * Opposite case of lgd_get_out_of_stock_inventory(): source of truth is
 * still WooCommerce (products tagged 'color-diamond' or 'white-diamond',
 * with stock_status = instock), but here we only keep the ones with no
 * matching row in ls_inventory / ls_inventory_c — matched by SKU, not pid,
 * since a stale/mismatched pid on an inventory row would otherwise hide a
 * genuinely orphaned product. Products with no SKU set are skipped (nothing
 * to match against). Same 45-day age gate as the out-of-stock listing, but
 * measured from post_date (creation date) rather than post_modified — a
 * product created very recently may just not have its inventory row
 * created/synced yet, so it's withheld until it's at least 45 days old.
 *
 * Every row returned here always has stock_status = 'in-stock-no-inventory'
 * (rows WITH a matching inventory record are never returned by this
 * endpoint — that's the out-of-stock listing's job).
 */
function lgd_get_in_stock_no_inventory( WP_REST_Request $request ) {
    global $wpdb;

    $type  = $request->get_param( 'type' );
    $table = lgd_inventory_table_for_type( $type );

    if ( ! in_array( $table, array( 'ls_inventory', 'ls_inventory_c' ), true ) ) {
        return new WP_Error( 'invalid_table', 'Invalid inventory type.', array( 'status' => 400 ) );
    }

    $tag = lgd_product_tag_for_type( $type );

    $min_age_days = lgd_out_of_stock_min_age_days();
    $cutoff_date  = gmdate( 'Y-m-d H:i:s', strtotime( "-{$min_age_days} days" ) );

    $in_stock_query = new WP_Query( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $tag,
            ),
        ),
        'meta_query'     => array(
            array(
                'key'   => '_stock_status',
                'value' => 'instock',
            ),
        ),
        'date_query'     => array(
            array(
                'column'    => 'post_date',
                'before'    => $cutoff_date,
                'inclusive' => false,
            ),
        ),
    ) );

    $product_ids = array_map( 'intval', $in_stock_query->posts );
    wp_reset_postdata();

    $results = array();

    if ( ! empty( $product_ids ) ) {
        // Build product_id -> sku, and skip anything with no SKU set (can't
        // match it against the inventory table at all).
        $sku_by_product_id = array();
        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( ! $product ) {
                continue;
            }
            $sku = $product->get_sku();
            if ( $sku === '' || $sku === null ) {
                continue;
            }
            $sku_by_product_id[ $product_id ] = $sku;
        }

        if ( ! empty( $sku_by_product_id ) ) {
            // Match against the inventory table by SKU (not pid) — a row is
            // considered "existing" for a product if its SKU appears in the
            // table at all, regardless of which pid that row is linked to.
            $skus         = array_values( $sku_by_product_id );
            $placeholders = implode( ',', array_fill( 0, count( $skus ), '%s' ) );
            $inv_rows     = $wpdb->get_results(
                $wpdb->prepare( "SELECT sku FROM {$table} WHERE sku IN ({$placeholders})", $skus )
            );

            $skus_with_inventory = array();
            foreach ( (array) $inv_rows as $row ) {
                $skus_with_inventory[ $row->sku ] = true;
            }

            foreach ( $sku_by_product_id as $product_id => $sku ) {
                if ( isset( $skus_with_inventory[ $sku ] ) ) {
                    continue; // SKU already has a row — belongs to the normal listing, not here
                }

                $product = wc_get_product( $product_id );
                if ( ! $product ) {
                    continue;
                }

                $results[] = array(
                    'inventory_id' => null,
                    'product_id'   => $product_id,
                    'sku'          => $sku,
                    'shape'        => '',
                    'carat'        => '',
                    'stock_status' => 'in-stock-no-inventory',
                    'title'        => $product->get_name(),
                );
            }
        }
    }

    return rest_ensure_response( array(
        'type'         => $type,
        'table'        => $table,
        'min_age_days' => $min_age_days,
        'count'        => count( $results ),
        'items'        => $results,
    ) );
}

/**
 * Core delete routine shared by the single and bulk endpoints.
 *
 * - Force-deletes the WooCommerce product first (permanent, skips Trash).
 * - Then deletes the inventory row(s) WHERE pid = product_id from the
 *   matching table, if any exist. If the product has no row in the table
 *   at all, that step simply affects 0 rows — it is NOT treated as an error,
 *   so products missing from ls_inventory / ls_inventory_c can still be
 *   deleted via the icon or bulk action.
 *
 * @return array{product_id:int, inventory_rows_deleted:int, product_deleted:bool}
 */
function lgd_delete_inventory_and_product( $table, $product_id ) {
    global $wpdb;

    $product_id = (int) $product_id;

    // 1) Force-delete the WooCommerce product first (true = bypass Trash, permanent).
    $product_deleted = false;
    $product         = wc_get_product( $product_id );

    if ( $product ) {
        $product_deleted = (bool) wp_delete_post( $product_id, true );
    }

    // 2) Delete the matching inventory record(s), if any — fully removed, no soft-delete.
    //    Returns 0 (not false) when there simply is no row, which is a valid outcome.
    $deleted_rows = $wpdb->delete(
        $table,
        array( 'pid' => $product_id ),
        array( '%d' )
    );

    return array(
        'product_id'             => $product_id,
        'inventory_rows_deleted' => $deleted_rows === false ? 0 : (int) $deleted_rows,
        'product_deleted'        => $product_deleted,
        'db_error'                => $deleted_rows === false,
    );
}

/**
 * POST /lgd/v1/delete-inventory
 * body: { "type": "color-diamond" | "white-diamond", "product_id": 12345 }
 */
function lgd_delete_out_of_stock_inventory( WP_REST_Request $request ) {
    $type       = $request->get_param( 'type' );
    $product_id = (int) $request->get_param( 'product_id' );
    $table      = lgd_inventory_table_for_type( $type );

    if ( ! in_array( $table, array( 'ls_inventory', 'ls_inventory_c' ), true ) ) {
        return new WP_Error( 'invalid_table', 'Invalid inventory type.', array( 'status' => 400 ) );
    }

    $result = lgd_delete_inventory_and_product( $table, $product_id );

    if ( $result['db_error'] ) {
        return new WP_Error( 'db_delete_failed', 'Product was deleted, but the inventory record could not be removed.', array( 'status' => 500 ) );
    }

    return rest_ensure_response( array(
        'success'                => true,
        'type'                   => $type,
        'table'                  => $table,
        'product_id'             => $result['product_id'],
        'inventory_rows_deleted' => $result['inventory_rows_deleted'],
        'product_deleted'        => $result['product_deleted'],
    ) );
}

/**
 * POST /lgd/v1/delete-inventory-bulk
 * body: { "type": "color-diamond" | "white-diamond", "product_ids": [123, 456, ...] }
 *
 * Deletes every product id in the list the same way the single-delete
 * endpoint does (product force-deleted, matching row removed if present).
 * One failure doesn't stop the rest — each id gets its own result entry.
 */
function lgd_bulk_delete_out_of_stock_inventory( WP_REST_Request $request ) {
    $type        = $request->get_param( 'type' );
    $product_ids = (array) $request->get_param( 'product_ids' );
    $table       = lgd_inventory_table_for_type( $type );

    if ( ! in_array( $table, array( 'ls_inventory', 'ls_inventory_c' ), true ) ) {
        return new WP_Error( 'invalid_table', 'Invalid inventory type.', array( 'status' => 400 ) );
    }

    $results = array();

    foreach ( $product_ids as $product_id ) {
        $product_id = (int) $product_id;
        if ( $product_id <= 0 ) {
            continue;
        }

        $result              = lgd_delete_inventory_and_product( $table, $product_id );
        $result['success']   = ! $result['db_error'];
        unset( $result['db_error'] );
        $results[] = $result;
    }

    return rest_ensure_response( array(
        'success' => true,
        'type'    => $type,
        'table'   => $table,
        'count'   => count( $results ),
        'results' => $results,
    ) );
}