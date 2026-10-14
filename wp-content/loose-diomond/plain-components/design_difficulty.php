<?php
add_action('init', function () {

    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group([
        'key' => 'group_design_difficulty',
        'title' => 'Product Option By AJ',
        'fields' => [
            [
                'key' => 'field_design_difficulty',
                'label' => 'Design Difficulty',
                'name' => 'design_difficulty',
                'type' => 'select',
                'choices' => [
                    'very_easy' => '25 - Very Easy',
                    'easy'      => '50 - Easy',
                    'medium'    => '90 - Medium',
                    'hard'      => '120 - Hard',
                    'very_hard' => '165 - Very Hard',
                    'very_very_hard' => '220 - Very Very Hard',
                ],
                'default_value' => '',
                'return_format' => 'value',
                'allow_null' => 1,
                'multiple' => 0,
            ],
            // Field 1: Report Style (text)
            [
                'key' => 'field_report_style',
                'label' => 'Report Style',
                'name' => 'report_style',
                'type' => 'text',
                'default_value' => '',
            ],
            // Field 2: Report Sub Style (text)
            [
                'key' => 'field_report_sub_style',
                'label' => 'Report Sub Style',
                'name' => 'report_sub_style',
                'type' => 'text',
                'default_value' => '',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'product',
                ],
            ],
        ],
    ]);

});

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    $product_id = $values['product_id'];

    // Design Difficulty
    $design_difficulty = get_field('design_difficulty', $product_id);
    if (!empty($design_difficulty)) {
        $labels = [
            'very_easy' => '25 - Very Easy',
            'easy'      => '50 - Easy',
            'medium'    => '90 - Medium',
            'hard'      => '120 - Hard',
            'very_hard' => '165 - Very Hard',
            'very_very_hard' => '220 - Very Very Hard',
        ];
        $value = $labels[$design_difficulty] ?? $design_difficulty;
        $item->add_meta_data('_design_difficulty', $value, true);
    }

    // Report Style (text — no label mapping needed)
    $report_style = get_field('report_style', $product_id);
    if (!empty($report_style)) {
        $item->add_meta_data('_report_style', $report_style, true);
    }

    // Report Sub Style (text — no label mapping needed)
    $report_sub_style = get_field('report_sub_style', $product_id);
    if (!empty($report_sub_style)) {
        $item->add_meta_data('_report_sub_style', $report_sub_style, true);
    }

}, 10, 4);