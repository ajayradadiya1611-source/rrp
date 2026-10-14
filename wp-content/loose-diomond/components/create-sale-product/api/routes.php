<?php
/**
 * Create Sale Product — REST API
 *
 * Two separate, explicit "on sale" listings, same split as the
 * Delete Out of Stock Inventory tool:
 *   - color-diamond  -> `ls_inventory_c` WHERE sale = 1
 *   - white-diamond  -> `ls_inventory`   WHERE sale = 1
 *
 * For each row we report whether a WooCommerce product already exists
 * for it (pid set AND the product actually resolves via wc_get_product),
 * and expose an action to create the missing one.
 *
 * Product creation reuses the exact same logic the storefront's
 * "Add To Cart" flow already relies on to lazily create a product the
 * first time a diamond is added to cart:
 *   - add_product_to_wc()   in inc/ls-addtocart-api.php   (white / ls_inventory)
 *   - add_product_to_wc_c() in inc/ls-addtocart-c-api.php (color / ls_inventory_c)
 * Both functions already handle: skip if a product with the same SKU
 * exists (links it back via `pid` instead of duplicating), build the
 * product, set price/stock/tags/meta, and write `pid` back onto the
 * inventory row. We don't duplicate any of that here — we just call it.
 *
 * This file is loaded automatically via index.php in the parent
 * `create-sale-product` folder — don't require it directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

define( 'LGD_SALE_PRODUCT_NS', 'lgd/v1' );

add_action( 'rest_api_init', function () {

    // List sale=1 inventory rows for a given type, with create-status per row
    register_rest_route( LGD_SALE_PRODUCT_NS, '/sale-inventory', array(
        'methods'             => 'GET',
        'callback'            => 'lgd_sale_get_sale_inventory',
        'permission_callback' => 'lgd_sale_product_permission_check',
        'args'                => array(
            'type' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
        ),
    ) );

    // Create the WooCommerce product for a single inventory row (if missing)
    register_rest_route( LGD_SALE_PRODUCT_NS, '/create-sale-product', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_sale_create_sale_product',
        'permission_callback' => 'lgd_sale_product_permission_check',
        'args'                => array(
            'type' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
            'inventory_id' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return is_numeric( $value ) && (int) $value > 0;
                },
            ),
        ),
    ) );

    // Same, applied to a list of inventory row ids
    register_rest_route( LGD_SALE_PRODUCT_NS, '/create-sale-product-bulk', array(
        'methods'             => 'POST',
        'callback'            => 'lgd_sale_bulk_create_sale_product',
        'permission_callback' => 'lgd_sale_product_permission_check',
        'args'                => array(
            'type' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return in_array( $value, array( 'color-diamond', 'white-diamond' ), true );
                },
            ),
            'inventory_ids' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return is_array( $value ) && count( $value ) > 0;
                },
            ),
        ),
    ) );
} );

/**
 * Only logged-in admins / shop managers can use this.
 */
function lgd_sale_product_permission_check( WP_REST_Request $request ) {
    return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
}

/**
 * Map the request "type" to its DB table.
 *   color-diamond -> ls_inventory_c
 *   white-diamond -> ls_inventory
 */
function lgd_sale_inventory_table_for_type( $type ) {
    return $type === 'color-diamond' ? 'ls_inventory_c' : 'ls_inventory';
}

/**
 * Whether a given pid actually resolves to a live WooCommerce product.
 * A pid can be set on the inventory row yet point at a deleted product,
 * so we don't trust the column alone.
 */
function lgd_sale_product_exists( $pid ) {
    $pid = (int) $pid;
    if ( $pid <= 0 ) {
        return false;
    }
    return (bool) wc_get_product( $pid );
}

/**
 * GET /lgd/v1/sale-inventory?type=color-diamond|white-diamond
 *
 * Source of truth is the inventory table itself here (unlike the OOS
 * delete tool, which sources from WooCommerce) — we want every row
 * flagged `sale = 1`, whether or not a product has been created for it
 * yet, so admins can see and fill the gap.
 *
 *   - pid_status = 'created'     -> pid set AND product exists in WooCommerce
 *   - pid_status = 'not-created' -> pid empty/0, OR pid set but product missing
 */
function lgd_sale_get_sale_inventory( WP_REST_Request $request ) {
    global $wpdb;

    $type  = $request->get_param( 'type' );
    $table = lgd_sale_inventory_table_for_type( $type );

    // Guard: only ever operate on these two known, hard-coded table names.
    if ( ! in_array( $table, array( 'ls_inventory', 'ls_inventory_c' ), true ) ) {
        return new WP_Error( 'invalid_table', 'Invalid inventory type.', array( 'status' => 400 ) );
    }

    $rows = $wpdb->get_results(
        "SELECT id, pid, sku, shape, carat, color, clarity, price, org_price
         FROM {$table}
         WHERE sale = 1 and status = 0
         ORDER BY id DESC"
    );

    $results = array();

    foreach ( (array) $rows as $row ) {
        $created = lgd_sale_product_exists( $row->pid );

        $results[] = array(
            'inventory_id' => (int) $row->id,
            'product_id'   => $created ? (int) $row->pid : null,
            'sku'          => $row->sku,
            'shape'        => $row->shape,
            'carat'        => $row->carat,
            'color'        => $row->color,
            'clarity'      => $row->clarity,
            'price'        => $row->price,
            'pid_status'   => $created ? 'created' : 'not-created',
        );
    }

    return rest_ensure_response( array(
        'type'  => $type,
        'table' => $table,
        'count' => count( $results ),
        'items' => $results,
    ) );
}

/**
 * Core create routine shared by the single and bulk endpoints.
 *
 * Reuses add_product_to_wc() / add_product_to_wc_c() — the exact same
 * function the Add To Cart REST endpoint calls when a diamond with no
 * product yet is added to cart. That function already:
 *   - skips creation and links back via SKU if a product already exists
 *   - builds the WC_Product_Simple, sets price/stock/image/tags/meta
 *   - writes the new post ID back onto the inventory row's `pid` column
 *
 * @return array{inventory_id:int, product_id:int|null, created:bool, already_existed:bool}
 */
function lgd_sale_create_product_for_row( $type, $inventory_id ) {
    global $wpdb;

    $inventory_id = (int) $inventory_id;
    $table        = lgd_sale_inventory_table_for_type( $type );

    $row = $wpdb->get_row( $wpdb->prepare( "SELECT id, pid FROM {$table} WHERE id = %d", $inventory_id ) );

    if ( ! $row ) {
        return array(
            'inventory_id'     => $inventory_id,
            'product_id'       => null,
            'created'          => false,
            'already_existed'  => false,
            'error'            => 'Inventory row not found.',
        );
    }

    // Already has a live product — nothing to do.
    if ( lgd_sale_product_exists( $row->pid ) ) {
        return array(
            'inventory_id'    => $inventory_id,
            'product_id'      => (int) $row->pid,
            'created'         => false,
            'already_existed' => true,
        );
    }

    // No product yet (or pid pointed at a deleted one) — create it via the
    // same helper the storefront add-to-cart flow uses.
    $product_id = $type === 'color-diamond'
        ? add_product_to_wc_c( $inventory_id, array('ready-to-ship', 'ready-to-ship-lab-diamonds') )
        : add_product_to_wc( $inventory_id, array('ready-to-ship', 'ready-to-ship-lab-diamonds') );

    if ( ! $product_id ) {
        return array(
            'inventory_id'    => $inventory_id,
            'product_id'      => null,
            'created'         => false,
            'already_existed' => false,
            'error'           => 'Product creation failed (missing/invalid inventory data or price).',
        );
    }

    return array(
        'inventory_id'    => $inventory_id,
        'product_id'      => (int) $product_id,
        'created'         => true,
        'already_existed' => false,
    );
}

/**
 * POST /lgd/v1/create-sale-product
 * body: { "type": "color-diamond" | "white-diamond", "inventory_id": 123 }
 */
function lgd_sale_create_sale_product( WP_REST_Request $request ) {
    $type         = $request->get_param( 'type' );
    $inventory_id = (int) $request->get_param( 'inventory_id' );

    $result = lgd_sale_create_product_for_row( $type, $inventory_id );

    if ( ! empty( $result['error'] ) && ! $result['product_id'] ) {
        return new WP_Error( 'create_failed', $result['error'], array( 'status' => 500 ) );
    }

    return rest_ensure_response( array_merge( array( 'success' => true, 'type' => $type ), $result ) );
}

/**
 * POST /lgd/v1/create-sale-product-bulk
 * body: { "type": "color-diamond" | "white-diamond", "inventory_ids": [123, 456, ...] }
 *
 * Creates every inventory id in the list the same way the single-create
 * endpoint does. One failure doesn't stop the rest — each id gets its own
 * result entry.
 */
function lgd_sale_bulk_create_sale_product( WP_REST_Request $request ) {
    $type          = $request->get_param( 'type' );
    $inventory_ids = (array) $request->get_param( 'inventory_ids' );

    $results = array();

    foreach ( $inventory_ids as $inventory_id ) {
        $inventory_id = (int) $inventory_id;
        if ( $inventory_id <= 0 ) {
            continue;
        }
        $results[] = lgd_sale_create_product_for_row( $type, $inventory_id );
    }

    return rest_ensure_response( array(
        'success' => true,
        'type'    => $type,
        'count'   => count( $results ),
        'results' => $results,
    ) );
}
