<?php

// Register a custom REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/get-products', [
        'methods' => 'GET',
        'callback' => 'get_products_by_category_and_taxonomy',
        'args' => [
            'category_id' => [
                'required' => false,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ],
            'taxonomy' => [
                'required' => false,
                'validate_callback' => function ($param) {
                    return is_string($param);
                },
            ],   
            'search' => [
                'required' => false,
                'validate_callback' => function ($param) {
                    return is_string($param);
                },
            ],
            'orderby' => [
                'required' => false,
                'validate_callback' => function ($param) {
                    if (!is_string($param) || $param === '') return false;
                    // price | best_seller (admin "Product Position" order)
                    return in_array(strtolower($param), ['price', 'best_seller'], true);
                },
            ],
            'order' => [
                'required' => false,
                'validate_callback' => function ($param) {
                    if (!is_string($param) || $param === '') return false;
                    return in_array(strtolower($param), ['asc', 'desc'], true);
                },
            ],
        ],
    ]);
});

    // Register endpoint to fetch product attribute terms (e.g., calibrated-lab-grown)
    add_action('rest_api_init', function () {
        register_rest_route('custom/v1', '/get-attribute-terms', [
            'methods'  => 'GET',
            'callback' => 'get_attribute_terms',
            'args'     => [
                'attribute' => [
                    'required' => true,
                    'validate_callback' => function ($param) {
                        return is_string($param) && !empty($param);
                    },
                ],
                'exclude' => [
                    'required' => false,
                    'validate_callback' => function ($param) {
                        return is_string($param);
                    },
                ],
            ],
        ]);
    });

    /**
     * Return terms for a product attribute slug.
     * Example: attribute=calibrated-lab-grown -> taxonomy pa_calibrated-lab-grown
     */
    function get_attribute_terms($request) {

    $attribute_param = sanitize_text_field($request->get_param('attribute'));
    $exclude_param   = sanitize_text_field($request->get_param('exclude'));

    if (empty($attribute_param)) {
        return new WP_Error('no_attribute', 'Attribute is required', ['status' => 400]);
    }

    // Convert attributes into array
    $attributes = array_map('trim', explode(',', $attribute_param));

    // Convert exclude slugs into array
    $exclude_slugs = [];
    if (!empty($exclude_param)) {
        $exclude_slugs = array_map('sanitize_title', explode(',', $exclude_param));
    }

    $result = [];

    foreach ($attributes as $attribute) {

        $taxonomy = 'pa_' . $attribute;

        if (!taxonomy_exists($taxonomy)) {
            $result[$attribute] = [];
            continue;
        }

        // First convert exclude slugs to IDs
        $exclude_ids = [];

        if (!empty($exclude_slugs)) {
            $exclude_ids = get_terms([
                'taxonomy'   => $taxonomy,
                'slug'       => $exclude_slugs,
                'fields'     => 'ids',
                'hide_empty' => false,
            ]);

            if (is_wp_error($exclude_ids)) {
                $exclude_ids = [];
            }
        }

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'exclude'    => $exclude_ids, // ✅ Correct way
        ]);

        if (is_wp_error($terms)) {
            $result[$attribute] = [];
            continue;
        }

        $out = [];

        foreach ($terms as $t) {
            $out[] = [
                'term_id' => $t->term_id,
                'name'    => $t->name,
                'slug'    => $t->slug,
                'count'   => $t->count,
            ];
        }

        $result[$attribute] = $out;
    }

    return new WP_REST_Response(['terms' => $result], 200);
}

/**
 * Callback function to fetch products based on category ID and taxonomy or search query.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing product data.
 */
function get_products_by_category_and_taxonomy($request) {
    global $oc_common;
    $category_id = $request['category_id'];
    $taxonomy    = $request['taxonomy'];
    $search      = $request->get_param('search');
    $page        = $request->get_param('page')     ? intval($request->get_param('page'))     : 1;
    $per_page    = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 50;

    if (empty($category_id) && empty($taxonomy) && empty($search)) {
        return new WP_Error('missing_params', 'Either category_id + taxonomy or search is required', ['status' => 400]);
    }

    // ── Tax query ────────────────────────────────────────────────────────────
    $tax_query = [];

    if (!empty($category_id) && !empty($taxonomy)) {
        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => $category_id,
        ];
    }

    $skip_keys  = ['category_id', 'taxonomy', 'search', 'page', 'per_page', 'price_min', 'price_max', 'orderby', 'order'];
    $all_params = $request->get_params();

    foreach ($all_params as $key => $val) {
        if (in_array($key, $skip_keys, true) || $val === null || $val === '') {
            continue;
        }

        // Ready-to-ship button: AND condition on product_cat "ready-to-ship"
        if ($key === 'ready_ship') {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => ['ready-to-ship'],
                'operator' => 'IN',
            ];
            continue;
        }

        // Special-case: ready_to_ship should filter by product categories (product_cat)
        if ($key === 'ready_to_ship') {
            $raw_parts = array_filter(array_map('trim', explode(',', sanitize_text_field((string) $val))), 'strlen');
            if (empty($raw_parts)) {
                continue;
            }

            $all_numeric = true;
            foreach ($raw_parts as $p) {
                if (!ctype_digit($p)) { $all_numeric = false; break; }
            }

            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => $all_numeric ? 'term_id' : 'slug',
                'terms'    => $all_numeric ? array_map('intval', $raw_parts) : $raw_parts,
                'operator' => 'IN',
            ];

            continue;
        }

        // Fallback: treat other params as product attribute (pa_*) taxonomies
        $attr = 'pa_' . sanitize_text_field($key);
        if (!taxonomy_exists($attr)) {
            continue;
        }

        $raw_parts = array_filter(array_map('trim', explode(',', sanitize_text_field((string) $val))), 'strlen');
        if (empty($raw_parts)) {
            continue;
        }

        $all_numeric = true;
        foreach ($raw_parts as $p) {
            if (!ctype_digit($p)) { $all_numeric = false; break; }
        }

        $tax_query[] = [
            'taxonomy' => $attr,
            'field'    => $all_numeric ? 'term_id' : 'slug',
            'terms'    => $all_numeric ? array_map('intval', $raw_parts) : $raw_parts,
            'operator' => 'IN',
        ];
    }

    // ── Build WP_Query args ──────────────────────────────────────────────────
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    ];

    // ── Search ───────────────────────────────────────────────────────────────
    if (!empty($search)) {
        $args['s'] = sanitize_text_field($search);
    }

    // ── Tax query ─────────────────────────────────────────────────────────────
    if (!empty($tax_query)) {
        $args['tax_query'] = count($tax_query) > 1
            ? array_merge(['relation' => 'AND'], $tax_query)
            : $tax_query;
    }

    // ── Meta query: stock status ALWAYS applied (even during search) ──────────
    $price_min  = $request->get_param('price_min');
    $price_max  = $request->get_param('price_max');

    $meta_query = [
        [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ],
    ];

    // Price filtering only applies when not searching
    if (empty($search)) {
        if (is_numeric($price_min) && is_numeric($price_max)) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => [floatval($price_min), floatval($price_max)],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ];
        } elseif (is_numeric($price_min)) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => floatval($price_min),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        } elseif (is_numeric($price_max)) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => floatval($price_max),
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ];
        }
    }

    // ── Ordering by price ─────────────────────────────────────────────────────
    $orderby_param = $request->get_param('orderby');
    $order_param   = $request->get_param('order');

    if ($orderby_param && strtolower($orderby_param) === 'price' && empty($search)) {
        $meta_query[] = [
            'key'  => '_price',
            'type' => 'NUMERIC',
        ];
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order']    = ($order_param && in_array(strtoupper($order_param), ['ASC', 'DESC'], true))
            ? strtoupper($order_param)
            : 'ASC';
    }

    // ── Ordering by Best Seller (admin Product Position, then total_sales) ───
    if ($orderby_param && strtolower($orderby_param) === 'best_seller' && function_exists('lgd_ppos_apply_best_seller_order')) {
        $pos_term_id = (!empty($category_id) && $taxonomy === 'product_cat') ? (int) $category_id : 0;
        $args = lgd_ppos_apply_best_seller_order($args, $pos_term_id);
    }

    // Always apply meta_query — stock status filter must never be skipped
    if (!empty($meta_query)) {
        $args['meta_query'] = count($meta_query) > 1
            ? array_merge(['relation' => 'AND'], $meta_query)
            : $meta_query;
    }

    // ── Run query ─────────────────────────────────────────────────────────────
    $query       = new WP_Query($args);
    $total       = (int) $query->found_posts;
    $total_pages = (int) $query->max_num_pages;

    // ── Build response ────────────────────────────────────────────────────────
    $products = [];

    foreach ($query->posts as $post) {
        $product = wc_get_product($post->ID);
        if (!$product) {
            continue;
        }

        $product_id = $product->get_id();
        $price      = $product->get_price();
        $org_price  = null;

        if ($product->is_on_sale()) {
            if ($product->is_type('simple')) {
                $org_price = $product->get_regular_price();
                $price     = $product->get_sale_price();
            } elseif ($product->is_type('variable')) {
                $org_price = $product->get_variation_regular_price();
                $price     = $product->get_variation_sale_price();
            }
        }

        // Format prices here
        $price     = oc_format_price($price);
        $org_price = oc_format_price($org_price);

        // Featured image first
        $gallery_images    = [];
        $featured_image_id = get_post_thumbnail_id($product_id);
        if ($featured_image_id) {
            $gallery_images[] = wp_get_attachment_image_url($featured_image_id, 'full');
        }

        // Gallery images
        $gallery_image_ids = get_post_meta($product_id, '_product_image_gallery', true);
        if (!empty($gallery_image_ids)) {
            foreach (explode(',', $gallery_image_ids) as $image_id) {
                $gallery_images[] = wp_get_attachment_image_url($image_id, 'full');
            }
        }

        $product_badge = get_field('oc_product_badge', $product_id) ?? '';

        $sku = $product->get_sku();
        $is_sale = has_term('sale', 'product_tag', $product_id);

        $productlink = get_permalink($product_id);

        $is_ready_to_ship = has_term('ready-to-ship', 'product_cat', $product_id);
        $ready_to_ship_categories = [
            'ready-to-ship-wedding-band',
            'ready-to-ship-diamond-earrings',
            'ready-to-ship-pendants',
            'ready-to-ship-bracelets',
            'necklaces',
        ];

        $delivery = has_term( $ready_to_ship_categories, 'product_cat', $product_id ) ? 1 : 0;

        // ── Metal swatches (one per distinct pa_metal, same as .dr-swatches cards) ──
        $metal_swatches = [];
        if ($product->is_type('variable')) {
            $seen_metals = [];
            foreach ($product->get_available_variations() as $v) {
                $slug = $v['attributes']['attribute_pa_metal'] ?? '';
                if ($slug === '' || isset($seen_metals[$slug])) {
                    continue;
                }
                $seen_metals[$slug] = true;
                $term = get_term_by('slug', $slug, 'pa_metal');

                // Slider images for this metal: variation main image + its
                // woo_variation_gallery_images (same source as single-product.php).
                $metal_images = [];
                if (!empty($v['image']['full_src'])) {
                    $metal_images[] = $v['image']['full_src'];
                }
                $wvg_raw = get_post_meta($v['variation_id'], 'woo_variation_gallery_images', true);
                $wvg_ids = !empty($wvg_raw) ? (array) maybe_unserialize($wvg_raw) : [];
                foreach ($wvg_ids as $img_id) {
                    $url = wp_get_attachment_image_url((int) $img_id, 'full');
                    if ($url && !in_array($url, $metal_images, true)) {
                        $metal_images[] = $url;
                    }
                }

                // Price for this metal (same 30%-off RETAIL rule as the product price)
                $v_obj   = wc_get_product($v['variation_id']);
                $v_price = $v_obj ? $v_obj->get_price() : '';
                $v_org   = null;
                if ($v_obj && $v_obj->is_on_sale()) {
                    $v_org   = $v_obj->get_regular_price();
                    $v_price = $v_obj->get_sale_price();
                }

                $metal_swatches[] = [
                    'slug'      => $slug,
                    'label'     => $term ? $term->name : $slug,
                    'images'    => $metal_images ?: $gallery_images,
                    'price'     => oc_format_price($v_price),
                    'org_price' => $v_org ? oc_format_price($v_org) : null,
                    'link'      => add_query_arg('attribute_pa_metal', $slug, $productlink),
                ];
            }
        }
        $products[] = [
            'id'             => $product_id,
            'title'          => $product->get_name(),
            'sku'            => $sku,
            'link'           => $productlink,
            'price'          => $price,
            'org_price'      => $org_price == 0 ? null : $org_price,
            'gallery_images' => $gallery_images,
            'product_badge'  => $product_badge,
            'shipping'       => $is_ready_to_ship ? 1 : 0,
            'is_sale_diamond'=> $is_sale ? 1 : 0,
            'delivery' => $delivery,
            'metal_swatches' => $metal_swatches,
        ];
    }

    wp_reset_postdata();

    return new WP_REST_Response([
        'products'   => $products,
        'pagination' => [
            'total'        => $total,
            'total_pages'  => $total_pages,
            'current_page' => $page,
            'per_page'     => $per_page,
        ],
    ], 200);
}


function oc_format_price($price) {
    if ($price === null || $price === '') return null;

    // Remove trailing .00
    return rtrim(rtrim(number_format((float)$price, 2, '.', ''), '0'), '.');
}