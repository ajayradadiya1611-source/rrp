<?php
/**
 * Custom Order Fields (ACF) for products in the "custom" category.
 * - Repeater of label/value pairs
 * - Number field: Custom Order Estimated Days
 * Both are copied into the order line item meta at checkout.
 */

add_action('init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group([
        'key'    => 'group_custom_order_fields',
        'title'  => 'Custom Order Fields',
        'fields' => [
            [
                'key'           => 'field_custom_order_estimated_days',
                'label'         => 'Custom Order Estimated Days',
                'name'          => 'custom_order_estimated_days',
                'type'          => 'number',
                'instructions'  => 'Estimated number of days to complete this custom order.',
                'min'           => 0,
                'step'          => 1,
                'append'        => 'days',
                'default_value' => '',
            ],
            [
                'key'          => 'field_custom_order_repeater',
                'label'        => 'Custom Order Fields',
                'name'         => 'custom_order_fields',
                'type'         => 'repeater',
                'instructions' => 'Add custom label/value pairs for this product. Only shown for products in the "Custom" category.',
                'layout'       => 'table',
                'button_label' => 'Add Row',
                'sub_fields'   => [
                    [
                        'key'   => 'field_custom_order_label',
                        'label' => 'Custom Order Label',
                        'name'  => 'custom_order_label',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_custom_order_value',
                        'label' => 'Custom Order Value',
                        'name'  => 'custom_order_value',
                        'type'  => 'text',
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'product',
                ],
                [
                    'param'    => 'post_taxonomy',
                    'operator' => '==',
                    'value'    => 'product_cat:custom',
                ],
            ],
        ],
    ]);
});

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    $product_id = $values['product_id'];

    if (!has_term('custom', 'product_cat', $product_id)) {
        return;
    }

    // Estimated days -> same meta keys as ls_save_order_item_estimated_delivery().
    // Runs at priority 20 so the custom product's value overrides the default one.
    $days = absint(get_field('custom_order_estimated_days', $product_id));
    if ($days > 0) {
        $date = date_i18n('l, M j', strtotime('+' . $days . ' days', current_time('timestamp')));
        $item->add_meta_data('_ls_estimated_delivery_days', $days, true);
        $item->add_meta_data('_ls_estimated_delivery_date', $date, true);
    }

    // Label/value rows
    /*$custom_rows = get_field('custom_order_fields', $product_id);
    if (!empty($custom_rows) && is_array($custom_rows)) {
        foreach ($custom_rows as $i => $row) {
            $label = $row['custom_order_label'] ?? '';
            $val   = $row['custom_order_value'] ?? '';
            if ($label !== '' || $val !== '') {
                $item->add_meta_data($label !== '' ? $label : 'Custom Field ' . ($i + 1), $val, true);
            }
        }
    }*/
}, 20, 4);