<?php

// ls_render_diamond_detail_page() and the visual-set helpers — shared by
// diamond-detail.php (below) and woocommerce/single-product.php (for
// products tagged "sale").
require_once __DIR__ . '/diamond-detail-render.php';

add_action('template_redirect', function () {
 
    if (!is_product()) {
        return;
    }
 
    global $post;
    if (!$post) {
        return;
    }
 
    // Get the SKU for this product.
    $product = wc_get_product($post->ID);
    if (!$product) {
        return;
    }
    $sku = $product->get_sku();
    if (!$sku) {
        return;
    }
 
    // Certificate type meta — adjust the meta key below if yours differs.
    $c_type = get_post_meta($post->ID, 'c_type', true);
    if ($c_type === '') {
        $c_type = get_post_meta($post->ID, '_c_type', true);
    }
 
    if (!in_array((string) $c_type, ['1', '3'], true)) {
        return;
    }
 
    // Products tagged "sale" stay on their own product URL and render
    // via the sale-tag branch in woocommerce/single-product.php (same
    // page structure, via ls_render_diamond_detail_page()) instead of
    // being redirected to /diamond/{sku}/.
    if (has_term('sale', 'product_tag', $post->ID)) {
        return;
    }
 
    $redirect_url = home_url('/diamond/' . rawurlencode($sku) . '/');
 
    wp_safe_redirect($redirect_url, 301);
    exit;
});

// ── Diamond SKU Rewrite ──────────────────────────────────────────
add_action('init', function () {
    add_rewrite_rule(
        '^diamond/([a-zA-Z0-9_-]+)/?$',
        'index.php?diamond_sku=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'diamond_sku';
    return $vars;
});

add_filter('template_include', function ($template) {
    if (get_query_var('diamond_sku')) {

        $custom = get_stylesheet_directory() . '/diamond-detail.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
});

/**
 * Look up a diamond by SKU across BOTH inventory tables — white
 * diamonds (ls_inventory) and fancy color diamonds (ls_inventory_c).
 * Colored-diamond SKUs were previously invisible to this file
 * (title/meta/schema all silently fell back to defaults) since only
 * ls_inventory was ever queried.
 *
 * Returns the row plus an `is_colored` flag so callers can adjust
 * field handling (colored diamonds use color/intensity, not the
 * D-Z $oc_color scale).
 */
function _diamond_lookup_by_sku($sku, $columns = '*') {
    global $wpdb;

    $diamond = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT {$columns} FROM ls_inventory WHERE sku = %s LIMIT 1",
            $sku
        )
    );

    if ($diamond) {
        $diamond->is_colored = false;
        return $diamond;
    }

    $diamond = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT {$columns} FROM ls_inventory_c WHERE sku = %s LIMIT 1",
            $sku
        )
    );

    if ($diamond) {
        $diamond->is_colored = true;
        return $diamond;
    }


    return null;
}

// ── Diamond Title – covers title-tag + wp_title + ob fallback ────

// For themes with add_theme_support('title-tag')
add_filter('pre_get_document_title', function ($title) {
    return _diamond_build_title($title);
}, 10);

// For older themes using wp_title()
add_filter('wp_title', function ($title) {
    return _diamond_build_title($title);
}, 10);

// ── Shared title builder ──────────────────────────────────────────
function _diamond_build_title($default) {
    if (!get_query_var('diamond_sku')) return $default;

    $diamond = $GLOBALS['diamond_detail'] ?? null;
    if (!$diamond) return;

    global $oc_cut, $oc_color, $oc_clarity, $oc_certificate;

    // Colored diamonds (ls_inventory_c) don't grade color on the D-Z
    // scale, so $oc_color would silently return nothing useful for
    // them — use the color/intensity fields directly instead.
    $is_colored = !empty($diamond->is_colored);

    $cut     = $oc_cut[$diamond->cut] ?? $diamond->cut;
    $clarity = $oc_clarity[$diamond->clarity] ?? $diamond->clarity;
    $cert    = $oc_certificate[$diamond->certificate_type] ?? '';
    $shape   = $diamond->shape_title ?? $diamond->shape;

    if ($is_colored==true) {
        $color = get_attrkey_val('color_c', $diamond->color);
        
    } else {
        $color = get_attrkey_val('color', $diamond->color);
    }

    $current_url = home_url('/diamond/' . rawurlencode($diamond->sku) . '/');
    $site_name   = get_bloginfo('name');
   // $og_title    = "{$diamond->carat} Ct {$shape} Diamond – {$color} {$clarity} {$cut}";
    $og_title    = $diamond->carat.' Carat '.ucfirst($shape).' Cut Lab Grown Diamond, '.$cut.', '.$color.', '.$cert.' Certified';
    return $og_title ;
}


add_action('wp_head', function () {
    if (!get_query_var('diamond_sku')) return;

    $diamond = $GLOBALS['diamond_detail'] ?? null;
    if (!$diamond) return;

    global $oc_cut, $oc_color, $oc_clarity, $oc_certificate;

    // Colored diamonds (ls_inventory_c) don't grade color on the D-Z
    // scale, so $oc_color would silently return nothing useful for
    // them — use the color/intensity fields directly instead.
    $is_colored = !empty($diamond->is_colored);

    $cut     = $oc_cut[$diamond->cut] ?? $diamond->cut;
    $clarity = $oc_clarity[$diamond->clarity] ?? $diamond->clarity;
    $cert    = $oc_certificate[$diamond->certificate_type] ?? '';
    $shape   = $diamond->shape_title ?? $diamond->shape;

    if ($is_colored==true) {
        $color = get_attrkey_val('color_c', $diamond->color);
        
    } else {
        $color = get_attrkey_val('color', $diamond->color);
    }
    /*$meta_desc = sprintf(
        'Buy %s Carat %s %s %s %s Diamond. %s Certified. SKU: %s. Price: $%s. %s.',
        $diamond->carat,
        $shape,
        $color,
        $clarity,
        $cut,
        $cert,
        strtoupper($diamond->sku),
        number_format($diamond->sale_price ?: $diamond->price, 2),
        $diamond->status == 0 ? 'In Stock' : 'Out of Stock'
    );*/
    $meta_desc ='Buy a '.ucfirst($shape).' '.$diamond->carat.' lab-grown diamond in '.$cut.' and '.$color.', '.strtoupper($diamond->sku).' certified by '.$cert.'. Priced at '. number_format($diamond->sale_price ?: $diamond->price, 2).'  30% lower than retail.';

    $current_url = home_url('/diamond/' . rawurlencode($diamond->sku) . '/');
    $site_name   = get_bloginfo('name');
    $og_title    = $diamond->carat.' Carat '.ucfirst($shape).' Cut Lab Grown Diamond, '.$cut.', '.$color.', '.$cert.' Certified';

    // ── Canonical ────────────────────────────────────────────────
    echo '<link rel="canonical" href="' . esc_url($current_url) . '" />' . "\n";

    // ── Standard Meta ────────────────────────────────────────────
    echo '<meta name="description" content="' . esc_attr($meta_desc) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow" />' . "\n";

    // ── Open Graph ───────────────────────────────────────────────
    echo '<meta property="og:type"        content="product" />' . "\n";
    echo '<meta property="og:url"         content="' . esc_url($current_url) . '" />' . "\n";
    echo '<meta property="og:site_name"   content="' . esc_attr($site_name) . '" />' . "\n";
    echo '<meta property="og:title"       content="' . esc_attr($og_title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($meta_desc) . '" />' . "\n";

    // ── Twitter Card ─────────────────────────────────────────────
    echo '<meta name="twitter:card"        content="summary" />' . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr("{$diamond->carat} Ct {$shape} Diamond") . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($meta_desc) . '" />' . "\n";

    // ── Schema.org JSON-LD (Product) ─────────────────────────────
    echo _diamond_build_product_schema_script($diamond, $current_url, $meta_desc);

}, 1);


/**
 * Build the Product JSON-LD <script> for a single diamond detail page
 * (/diamond/{sku}/). Server-side, so it's in the raw HTML for Google /
 * AI crawlers. Mirrors the field labels shown in the on-page spec table
 * (inc/diamond-detail-render.php) — NOT the brief's sample code tables,
 * which don't match our DB (e.g. clarity 1 = I2 here, not FL).
 */
function _diamond_build_product_schema_script($diamond, $page_url, $fallback_desc = '') {
    global $oc_cut, $oc_color, $oc_clarity, $oc_certificate, $inv_color, $inv_intensity_c, $shape_png_white;

    $is_colored = !empty($diamond->is_colored);
    $sku        = strtoupper($diamond->sku);

    // ── Human-readable values ────────────────────────────────────
    $shape_raw = (string) ($diamond->shape_title ?? $diamond->shape ?? '');
    $shape     = ucwords(str_replace('_', ' ', strtolower($shape_raw)));
    $shape_key = strtolower(str_replace(' ', '_', (string) ($diamond->shape ?? $diamond->shape_title ?? '')));

    $cut     = $oc_cut[$diamond->cut] ?? '';
    $clarity = $oc_clarity[$diamond->clarity] ?? '';
    $carat   = number_format((float) $diamond->carat, 2, '.', '');

    if ($is_colored) {
        $color_name = ucfirst($oc_color[$diamond->color] ?? '');
        $intensity  = is_array($inv_intensity_c) ? array_search($diamond->intensity, $inv_intensity_c) : '';
        $color      = trim(ucwords(str_replace('_', ' ', (string) $intensity)) . ' ' . $color_name);
    } else {
        $color = is_array($inv_color) ? (string) array_search($diamond->color, $inv_color) : '';
    }

    $cert_type = (int) ($diamond->certificate_type ?? 0);
    $cert      = $oc_certificate[$cert_type] ?? 'Non-Certified';

    $fluor_labels = ['None', 'Faint', 'Medium', 'Strong', 'Very Strong'];
    $ps_labels    = ['Good', 'Very Good', 'Excellent'];
    $grade = function ($v, $labels) {
        if (!isset($v) || $v === '' || (int) $v === 0) return '';
        return $labels[(int) round($v) - 1] ?? '';
    };
    $fluorescence = $grade($diamond->fluorescence ?? null, $fluor_labels);
    $polish       = $grade($diamond->polish ?? null, $ps_labels);
    $symmetry     = $grade($diamond->symmetry ?? null, $ps_labels);

    $table = (isset($diamond->av_table) && $diamond->av_table !== '' && (float) $diamond->av_table > 0)
        ? number_format((float) $diamond->av_table, 2, '.', '') : '';
    $depth = (isset($diamond->depth) && $diamond->depth !== '' && (float) $diamond->depth > 0)
        ? number_format((float) $diamond->depth, 2, '.', '') : '';

    $size_mm = '';
    if (!empty($diamond->length) && !empty($diamond->width) && !empty($diamond->height)) {
        $size_mm = $diamond->length . ' x ' . $diamond->width . ' x ' . $diamond->height;
    }

    // ── Image (same logic as the page's main gallery thumbnail) ─────
    $image = '';
    if ($is_colored && $color_name = strtolower(trim($oc_color[$diamond->color] ?? ''))) {
        $image = get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/' . $shape_key . '/' . $color_name . '.png';
    }
    if ($image === '') {
        $image = $shape_png_white[$shape_key]
            ?? get_template_directory_uri() . '/aj_custom/diamond/dimensions-top-value.png';
    }

    // ── Price (same value the page displays) ─────────────────────
    $price = number_format((float) round($diamond->price), 2, '.', '');

    // ── Shipping ─────────────────────────────────────────────────
    // DB `shipping`: 2 = Overnight, 1 = Ships within 3 days, else standard
    $ship = (int) ($diamond->shipping ?? 0);
    if ($ship === 2) {
        $ship_label = 'Overnight Delivery'; $t_min = 1; $t_max = 1;
    } elseif ($ship === 1) {
        $ship_label = 'Express Shipping';   $t_min = 2; $t_max = 3;
    } else {
        $ship_label = 'Standard Shipping';  $t_min = 5; $t_max = 7;
    }

    // ── Name / description ───────────────────────────────────────
    $name = "{$carat} Carat {$shape} Cut Lab Grown Diamond";
    $bits = array_filter([$color ? "{$color} Color" : '', $clarity ? "{$clarity} Clarity" : '']);
    if ($bits) $name .= ' — ' . implode(', ', $bits);

    $desc = ($cert_type && isset($oc_certificate[$cert_type]) ? "{$cert} Certified " : '')
          . "{$carat} Carat {$shape} cut lab grown diamond.";
    if ($cut)     $desc .= " {$cut} cut quality,";
    if ($color)   $desc .= " {$color} color,";
    if ($clarity) $desc .= " {$clarity} clarity.";
    $desc = rtrim($desc, ',');
    if ($size_mm)         $desc .= " Size: {$size_mm} mm.";
    if ($table && $depth) $desc .= " Table: {$table}%, Depth: {$depth}%.";
    $desc = preg_replace('/\s+/', ' ', trim(str_replace(',.', '.', $desc)));
    if ($desc === '') $desc = $fallback_desc;

    // ── additionalProperty (skip empty values) ───────────────────
    $props = [
        'Shape'         => $shape,
        'Carat Weight'  => $carat,
        'Cut'           => $cut,
        'Color Grade'   => $color,
        'Clarity Grade' => $clarity,
        'Certificate'   => $cert,
        'Fluorescence'  => $fluorescence,
        'Polish'        => $polish,
        'Symmetry'      => $symmetry,
        'Table'         => $table ? "{$table}%" : '',
        'Depth'         => $depth ? "{$depth}%" : '',
        'Measurements'  => $size_mm ? "{$size_mm} mm" : '',
    ];
    $additional = [];
    foreach ($props as $k => $v) {
        if ($v === '' || $v === null) continue;
        $additional[] = ['@type' => 'PropertyValue', 'name' => $k, 'value' => (string) $v];
    }

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        '@id'         => $page_url . '#product',
        'name'        => $name,
        'description' => $desc,
        'url'         => $page_url,
        'image'       => $image,
        'sku'         => $sku,
        'mpn'         => $sku,
        'category'    => 'Lab Grown Diamonds',
        'material'    => 'Lab Grown Diamond',
        'brand'       => ['@type' => 'Brand', 'name' => 'Loose Grown Diamond'],
        'offers'      => [
            '@type'           => 'Offer',
            'url'             => $page_url,
            'price'           => $price,
            'priceCurrency'   => 'USD',
            'availability'    => ((int) $diamond->status === 0)
                                    ? 'https://schema.org/InStock'
                                    : 'https://schema.org/OutOfStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
            'priceValidUntil' => gmdate('Y-m-d', strtotime('+90 days')),
            'seller'          => ['@type' => 'Organization', 'name' => 'Loose Grown Diamond'],
            'shippingDetails' => [
                '@type'         => 'OfferShippingDetails',
                'shippingLabel' => $ship_label,
                'deliveryTime'  => [
                    '@type'        => 'ShippingDeliveryTime',
                    'handlingTime' => ['@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'd'],
                    'transitTime'  => ['@type' => 'QuantitativeValue', 'minValue' => $t_min, 'maxValue' => $t_max, 'unitCode' => 'd'],
                ],
                'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'US'],
                'shippingRate'        => ['@type' => 'MonetaryAmount', 'value' => '0', 'currency' => 'USD'],
            ],
        ],
        'additionalProperty' => $additional,
    ];

    return '<script type="application/ld+json" id="diamond-product-schema">'
        . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . '</script>' . "\n";
}