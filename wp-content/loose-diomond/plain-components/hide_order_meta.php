<?php

add_filter( 'woocommerce_hidden_order_itemmeta', 'ls_hide_order_itemmeta' );
function ls_hide_order_itemmeta( $hidden_meta_keys ) {
   // $hidden_meta_keys[] = 'Estimated Delivery';
    return $hidden_meta_keys;
}

add_filter('woocommerce_order_item_get_formatted_meta_data', 'ls_process_order_item_meta', 10, 2);
function ls_process_order_item_meta($formatted_meta, $item) {
    $item_name = $item->get_name();

    foreach ($formatted_meta as $key => $meta) {
        // Remove unwanted meta keys
        if (isset($meta->key) && in_array($meta->key, [
            '_sku_snapshot'
        ])) {
            unset($formatted_meta[$key]);
            continue;
        }
    }

    return $formatted_meta;
}