<?php
/**
 * Diamond Inventory List Schema (ItemList + Product JSON-LD)
 * ------------------------------------------------------------------
 * Injects a Product + ItemList JSON-LD block into <head> for the two
 * inventory listing templates so Google / ChatGPT / Perplexity crawlers
 * can read live diamond inventory straight from the raw HTML, without
 * executing the React app.
 *
 *   - page-inventory.php  → white diamonds  (table: ls_inventory,
 *                            REST callback: ls_inventory_filter_api)
 *   - page-inventory2.php → colored diamonds (table: ls_inventory_c,
 *                            REST callback: ls_inventory_colored_api)
 *
 * IMPORTANT — this reuses the REAL filtering, not a re-guess of it:
 * rather than rebuilding the WHERE-clause logic a third time (once for
 * the REST API, once for the React app's initial state in
 * inc/script-load.php, and again here), this builds a WP_REST_Request
 * from whatever's actually driving the page — the query string AND the
 * page's ACF `filter_url` preset — using the exact param names
 * ls_inventory_filter_api() / ls_inventory_colored_api() already expect
 * (see inc/ls-inventory-api.php), and calls those functions directly.
 * That's the same merge order inc/script-load.php's
 * loose_diomond_scripts_react() uses to build the React app's initial
 * `filters` state, so the crawler sees exactly what a visitor's first
 * paint of the same URL would show. If those filter-building functions
 * change, this stays in sync automatically — there's nothing to keep
 * updated in parallel here.
 *
 * NOT replicated: `type=ringbuilder` / `type=ringbuilder_c` presets.
 * Those come from live PHP session state (which diamond/setting the
 * visitor picked mid ring-builder flow) and have no meaning for a
 * crawler with no session — those requests just fall through to the
 * page's normal listing here, which is the closest sane default.
 *
 * ASSUMPTIONS worth a quick check before relying on this in prod:
 *  - Neither table has a per-diamond photo column, so — same as the
 *    React grid itself (InventoryResults.jsx) — this uses the generic
 *    per-shape (or per-shape+color, for colored) placeholder image
 *    rather than inventing a photo URL that doesn't exist.
 *  - Shipping label / transit days come from the real `shipping` +
 *    `delivery_days` columns (0/1/2 = standard/express/overnight — see
 *    the "SHIPPING FILTER" comment in ls-inventory-api.php), not the
 *    shippingDays scheme from the original spec doc, which doesn't
 *    match any column that actually exists.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'LS_INVENTORY_SCHEMA_ITEM_LIMIT' ) ) {
    define( 'LS_INVENTORY_SCHEMA_ITEM_LIMIT', 40 );
}

add_action( 'wp_head', 'ls_render_inventory_list_schema', 30 );

function ls_render_inventory_list_schema() {

    $is_colored = is_page_template( 'page-inventory2.php' );

    if ( ! $is_colored && ! is_page_template( 'page-inventory.php' ) ) {
        return;
    }

    $result = ls_get_inventory_schema_data( $is_colored );

    if ( ! $result || empty( $result['data'] ) ) {
        return;
    }

    $current_url = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '' ) );

    $page_title = get_the_title();
    $list_name  = ( $is_colored ? 'Lab Grown Colored Diamonds' : 'Lab Grown Diamonds' )
        . ( $page_title ? " — {$page_title}" : '' );

    $items = array();
    $position = 1;

    foreach ( $result['data'] as $diamond ) {
        $item = $is_colored
            ? ls_build_colored_inventory_schema_item( $diamond )
            : ls_build_inventory_schema_item( $diamond );

        if ( $item ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'item'     => $item,
            );
            $position++;
        }
    }

    if ( empty( $items ) ) {
        return;
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => $list_name,
        'description'     => sprintf(
            'Browse %s lab grown %sdiamonds. IGI & GIA certified, ships within 48 hours.',
            number_format_i18n( $result['total'] ),
            $is_colored ? 'colored ' : ''
        ),
        'url'             => esc_url_raw( $current_url ),
        'numberOfItems'   => $result['total'],
        'itemListElement' => $items,
    );

    echo '<script type="application/ld+json" id="diamond-inventory-schema">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
        . '</script>' . "\n";
}

/**
 * Build the exact param set ls_inventory_filter_api() /
 * ls_inventory_colored_api() expect, from whatever's actually live for
 * this page load: the query string, then the page's ACF `filter_url`
 * preset layered on top (same precedence as script-load.php).
 */
function ls_inventory_schema_effective_params( $is_colored ) {

    $params = array();

    // ── Range filters that pass straight through as "min,max" strings
    // (both APIs read these with the same param name).
    $range_get_keys = array(
        'carat'          => 'carat_range',
        'price'          => 'price_range',
        'cut'            => 'cut_range',
        'clarity'        => 'clarity_range',
        'table'          => 'table_range',
        'depth'          => 'depth_range',
        'lwratio'        => 'lwratio_range',
        'polish'         => 'polish_range',
        'symmetry'       => 'symmetry_range',
        'fluorescence'   => 'fluorescence_range',
        'girdle'         => 'girdle_range',
        'culet'          => 'culet_range',
        'crown_angle'    => 'crown_angle_range',
        'pavilion_angle' => 'pavilion_angle_range',
        'width'          => 'width_range',
        'height'         => 'height_range',
        'length'         => 'length_range',
    );

    foreach ( $range_get_keys as $get_key => $api_key ) {
        if ( ! empty( $_GET[ $get_key ] ) ) {
            $params[ $api_key ] = sanitize_text_field( wp_unslash( $_GET[ $get_key ] ) );
        }
    }

    // White-diamond color range — API param name is literally `color`
    // (not `color_range`), see ls_inventory_filter_api().
    if ( ! $is_colored && ! empty( $_GET['color'] ) ) {
        $params['color'] = sanitize_text_field( wp_unslash( $_GET['color'] ) );
    }

    if ( ! empty( $_GET['shape'] ) ) {
        $params['shape'] = array_map( 'sanitize_text_field', explode( ',', wp_unslash( $_GET['shape'] ) ) );
    }
    if ( ! empty( $_GET['certificate'] ) ) {
        $params['certificate_type'] = array_map( 'sanitize_text_field', explode( ',', wp_unslash( $_GET['certificate'] ) ) );
    }
    if ( ! empty( $_GET['sku'] ) ) {
        $params['ls_sku'] = sanitize_text_field( wp_unslash( $_GET['sku'] ) );
    }
    foreach ( array( 'shipping', 'is_sale', 'is_media', 'heartarrow' ) as $flag ) {
        if ( isset( $_GET[ $flag ] ) ) {
            $params[ $flag ] = intval( $_GET[ $flag ] );
        }
    }
    if ( ! empty( $_GET['ls_sortby'] ) ) {
        $params['ls_sortby'] = sanitize_text_field( wp_unslash( $_GET['ls_sortby'] ) );
    }
    if ( ! empty( $_GET['ls_sort'] ) ) {
        $params['ls_sort'] = sanitize_text_field( wp_unslash( $_GET['ls_sort'] ) );
    }

    if ( $is_colored ) {
        // ls_inventory_colored_api() reads the exact hue match as
        // `colored` (int), and fancy-color intensity as `intensity`.
        if ( ! empty( $_GET['color'] ) ) {
            $params['colored'] = intval( $_GET['color'] );
        }
        if ( ! empty( $_GET['intensity'] ) ) {
            $params['intensity'] = array_map( 'intval', explode( ',', wp_unslash( $_GET['intensity'] ) ) );
        }
    }

    // ── ACF `filter_url` preset — overrides shape / carat / clarity /
    // color / cut / certificate / heartarrow, same fields + precedence
    // as loose_diomond_scripts_react() in inc/script-load.php. This is
    // what makes a page like "Round Diamonds" always show round
    // diamonds regardless of the query string.
    $filter_url = get_field( 'filter_url' );

    if ( ! empty( $filter_url ) ) {
        parse_str( $filter_url, $fu );

        if ( ! empty( $fu['shape'] ) ) {
            $params['shape'] = array_map( 'sanitize_text_field', explode( ',', $fu['shape'] ) );
        }
        if ( ! empty( $fu['carat'] ) ) {
            $params['carat_range'] = sanitize_text_field( $fu['carat'] );
        }
        if ( ! empty( $fu['clarity'] ) ) {
            $params['clarity_range'] = sanitize_text_field( $fu['clarity'] );
        }
        if ( ! empty( $fu['cut'] ) ) {
            $params['cut_range'] = sanitize_text_field( $fu['cut'] );
        }
        if ( ! empty( $fu['certificate'] ) ) {
            $params['certificate_type'] = array_map( 'sanitize_text_field', explode( ',', $fu['certificate'] ) );
        }
        if ( ! empty( $fu['heartarrow'] ) ) {
            $params['heartarrow'] = sanitize_text_field( $fu['heartarrow'] );
        }
        if ( ! empty( $fu['color'] ) ) {
            if ( $is_colored ) {
                $params['colored'] = intval( $fu['color'] );
            } else {
                $params['color'] = sanitize_text_field( $fu['color'] );
            }
        }
    }

    return $params;
}

/**
 * Runs the real REST callback (in-process, no HTTP round-trip) for the
 * effective filter set and returns its response data, or null on
 * failure.
 */
function ls_get_inventory_schema_data( $is_colored ) {

    if ( ! function_exists( 'ls_inventory_filter_api' ) || ! function_exists( 'ls_inventory_colored_api' ) ) {
        return null;
    }

    $params = ls_inventory_schema_effective_params( $is_colored );
    $params['ls_start']    = 1;
    $params['ls_per_page'] = LS_INVENTORY_SCHEMA_ITEM_LIMIT;

    $request = new WP_REST_Request( 'POST' );
    foreach ( $params as $key => $value ) {
        $request->set_param( $key, $value );
    }

    $response = $is_colored ? ls_inventory_colored_api( $request ) : ls_inventory_filter_api( $request );

    if ( is_wp_error( $response ) ) {
        return null;
    }

    $data = ( $response instanceof WP_REST_Response ) ? $response->get_data() : $response;

    if ( ! is_array( $data ) || ! isset( $data['data'] ) ) {
        return null;
    }

    return $data;
}

/**
 * Build a single Product node for a WHITE diamond row (ls_inventory).
 */
function ls_build_inventory_schema_item( $diamond ) {

    global $oc_cut, $oc_clarity, $oc_certificate, $inv_color;

    if ( empty( $diamond->sku ) ) {
        return null;
    }

    $price = $diamond->sale_price ?: $diamond->price;
    if ( empty( $price ) ) {
        return null;
    }

    $cut     = $oc_cut[ $diamond->cut ] ?? $diamond->cut;
    $color   = ucfirst( array_search( $diamond->color, $inv_color ) ?: $diamond->color );
    $clarity = $oc_clarity[ $diamond->clarity ] ?? $diamond->clarity;
    $cert    = $oc_certificate[ $diamond->certificate_type ] ?? 'Non-Certified';

    $shape_raw = $diamond->shape_title ?? $diamond->shape ?? '';
    $shape_key = strtolower( str_replace( ' ', '_', $shape_raw ) );
    $shape     = ucwords( str_replace( '_', ' ', $shape_raw ) );

    $image_url = get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/' . $shape_key . '.png';

    return ls_build_inventory_schema_item_common( $diamond, $shape, $image_url, $cut, $color, $clarity, $cert );
}

/**
 * Build a single Product node for a COLORED diamond row (ls_inventory_c).
 * Uses the fancy-color hue ($oc_color) + intensity instead of the D-M
 * white-diamond color scale, and the per-shape+color placeholder image
 * the colored grid cards use.
 */
function ls_build_colored_inventory_schema_item( $diamond ) {

    global $oc_cut, $oc_clarity, $oc_certificate, $oc_color, $inv_intensity_c;

    if ( empty( $diamond->sku ) ) {
        return null;
    }

    $price = $diamond->sale_price ?: $diamond->price;
    if ( empty( $price ) ) {
        return null;
    }

    $cut       = $oc_cut[ $diamond->cut ] ?? $diamond->cut;
    $hue       = ucfirst( $oc_color[ $diamond->color ] ?? $diamond->color );
    $intensity = ucfirst( array_search( $diamond->intensity, $inv_intensity_c ) ?: '' );
    $color     = trim( $intensity . ' ' . $hue );
    $clarity   = $oc_clarity[ $diamond->clarity ] ?? $diamond->clarity;
    $cert      = $oc_certificate[ $diamond->certificate_type ] ?? 'Non-Certified';

    $shape_raw  = $diamond->shape_title ?? $diamond->shape ?? '';
    $shape_key  = strtolower( str_replace( ' ', '_', $shape_raw ) );
    $shape      = ucwords( str_replace( '_', ' ', $shape_raw ) );
    $color_slug = strtolower( $hue );

    $image_url = get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/' . $shape_key . '/' . $color_slug . '.png';

    return ls_build_inventory_schema_item_common( $diamond, $shape, $image_url, $cut, $color, $clarity, $cert );
}

/**
 * Shared node assembly for both white and colored rows — everything
 * that doesn't depend on how `$color` was decoded.
 */
function ls_build_inventory_schema_item_common( $diamond, $shape, $image_url, $cut, $color, $clarity, $cert ) {

    $price = $diamond->sale_price ?: $diamond->price;
    $product_url = home_url( '/diamond/' . rawurlencode( $diamond->sku ) . '/' );

    $fluorescence_labels    = array( 'None', 'Faint', 'Medium', 'Strong', 'Very Strong' );
    $polish_symmetry_labels = array( 'Good', 'Very Good', 'Excellent' );

    $fluorescence = ! empty( $diamond->fluorescence )
        ? ( $fluorescence_labels[ (int) $diamond->fluorescence - 1 ] ?? '' )
        : 'None';
    $polish = ! empty( $diamond->polish )
        ? ( $polish_symmetry_labels[ (int) $diamond->polish - 1 ] ?? '' )
        : '';
    $symmetry = ! empty( $diamond->symmetry )
        ? ( $polish_symmetry_labels[ (int) $diamond->symmetry - 1 ] ?? '' )
        : '';

    $measurements = '';
    if ( ! empty( $diamond->length ) && ! empty( $diamond->width ) && ! empty( $diamond->height ) ) {
        $measurements = "{$diamond->length} x {$diamond->width} x {$diamond->height} mm";
    }

    $table_val = isset( $diamond->av_table ) ? $diamond->av_table : ( $diamond->table ?? '' );
    $depth_val = $diamond->depth ?? '';

    $description = sprintf(
        '%s Certified %s Carat %s cut lab grown diamond. %s cut quality, %s color, %s clarity.%s',
        $cert,
        $diamond->carat,
        $shape,
        $cut,
        $color,
        $clarity,
        ( $table_val !== '' && $depth_val !== '' )
            ? sprintf( ' Table: %s%%, Depth: %s%%.', $table_val, $depth_val )
            : ''
    );

    // ── Shipping — real `shipping` (0/1/2) + `delivery_days` columns.
    $shipping_code = isset( $diamond->shipping ) ? (int) $diamond->shipping : 0;
    $delivery_days = ( isset( $diamond->delivery_days ) && (int) $diamond->delivery_days > 0 )
        ? (int) $diamond->delivery_days
        : 7;

    switch ( $shipping_code ) {
        case 2:
            $shipping_label = 'Overnight Delivery';
            $transit_min    = 1;
            $transit_max    = 1;
            break;
        case 1:
            $shipping_label = 'Express Shipping';
            $transit_min    = 2;
            $transit_max    = 3;
            break;
        default:
            $shipping_label = 'Standard Shipping';
            $transit_min    = $delivery_days;
            $transit_max    = $delivery_days;
            break;
    }

    $item = array(
        '@type'       => 'Product',
        'name'        => sprintf(
            '%s Carat %s Cut Lab Grown Diamond — %s Color, %s Clarity',
            $diamond->carat,
            $shape,
            $color,
            $clarity
        ),
        'description' => $description,
        'url'         => esc_url_raw( $product_url ),
        'image'       => esc_url_raw( $image_url ),
        'sku'         => strtoupper( $diamond->sku ),
        'brand'       => array(
            '@type' => 'Brand',
            'name'  => 'Loose Grown Diamond',
        ),
        'offers' => array(
            '@type'           => 'Offer',
            'url'             => esc_url_raw( $product_url ),
            'price'           => (string) $price,
            'priceCurrency'   => 'USD',
            'availability'    => 'https://schema.org/InStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
            'priceValidUntil' => gmdate( 'Y-m-d', strtotime( '+90 days' ) ),
            'shippingDetails' => array(
                '@type'         => 'OfferShippingDetails',
                'shippingLabel' => $shipping_label,
                'deliveryTime'  => array(
                    '@type'        => 'ShippingDeliveryTime',
                    'handlingTime' => array(
                        '@type'    => 'QuantitativeValue',
                        'minValue' => 0,
                        'maxValue' => 1,
                        'unitCode' => 'd',
                    ),
                    'transitTime'  => array(
                        '@type'    => 'QuantitativeValue',
                        'minValue' => $transit_min,
                        'maxValue' => $transit_max,
                        'unitCode' => 'd',
                    ),
                ),
                'shippingDestination' => array(
                    '@type'          => 'DefinedRegion',
                    'addressCountry' => 'US',
                ),
                'shippingRate' => array(
                    '@type'    => 'MonetaryAmount',
                    'value'    => '0',
                    'currency' => 'USD',
                ),
            ),
        ),
        'additionalProperty' => array(
            array( '@type' => 'PropertyValue', 'name' => 'Shape',         'value' => $shape ),
            array( '@type' => 'PropertyValue', 'name' => 'Carat Weight',  'value' => (string) $diamond->carat ),
            array( '@type' => 'PropertyValue', 'name' => 'Cut',           'value' => $cut ),
            array( '@type' => 'PropertyValue', 'name' => 'Color Grade',   'value' => $color ),
            array( '@type' => 'PropertyValue', 'name' => 'Clarity Grade', 'value' => $clarity ),
            array( '@type' => 'PropertyValue', 'name' => 'Certificate',   'value' => $cert ),
            array( '@type' => 'PropertyValue', 'name' => 'Fluorescence',  'value' => $fluorescence ),
        ),
    );

    if ( $polish !== '' ) {
        $item['additionalProperty'][] = array( '@type' => 'PropertyValue', 'name' => 'Polish', 'value' => $polish );
    }
    if ( $symmetry !== '' ) {
        $item['additionalProperty'][] = array( '@type' => 'PropertyValue', 'name' => 'Symmetry', 'value' => $symmetry );
    }
    if ( $table_val !== '' ) {
        $item['additionalProperty'][] = array( '@type' => 'PropertyValue', 'name' => 'Table', 'value' => $table_val . '%' );
    }
    if ( $depth_val !== '' ) {
        $item['additionalProperty'][] = array( '@type' => 'PropertyValue', 'name' => 'Depth', 'value' => $depth_val . '%' );
    }
    if ( $measurements !== '' ) {
        $item['additionalProperty'][] = array( '@type' => 'PropertyValue', 'name' => 'Measurements', 'value' => $measurements );
    }

    return $item;
}