<?php

add_filter( 'woocommerce_hidden_order_itemmeta', 'ls_hide_order_itemmeta' );
function ls_hide_order_itemmeta( $hidden_meta_keys ) {
   // $hidden_meta_keys[] = '_ls_estimated_delivery_date';
   // $hidden_meta_keys[] = 'Estimated Delivery';
    return $hidden_meta_keys;
}

add_filter('woocommerce_order_item_get_formatted_meta_data', 'ls_process_order_item_meta', 10, 2);
function ls_process_order_item_meta($formatted_meta, $item) {
    $item_name = $item->get_name();

    foreach ($formatted_meta as $key => $meta) {
        // Remove unwanted meta keys
        if (isset($meta->key) && in_array($meta->key, [
            'diamond',
            'setting',
            'setting_variation',
            'c_type',
            '_design_difficulty',
            '_certificate_url',
            '_is_ring_building',
            '_ls_estimated_delivery_days',
            '_sku_snapshot',
            '_lgd_rb_type',
            '_lgd_rb_pair_id'
        ])) {
            unset($formatted_meta[$key]);
            continue;
        }

        // Estimated delivery date: relabel, or remove if it's a Custom Order
        if ($meta->key === '_ls_estimated_delivery_date') {
            $meta->display_key = 'Estimated Delivery';
            continue;
        }
        if ($meta->key === '_report_style') {
            $meta->display_key = 'Report Style';
            continue;
        }
        if ($meta->key === '_report_sub_style') {
            $meta->display_key = 'Report Sub Style';
            continue;
        }
    }

    return $formatted_meta;
}