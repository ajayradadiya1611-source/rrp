<?php
/*include('ls-inventory/ls_inventory.php');
include('ls-inventory-color/ls_inventory_c.php');*/
include('variation-image-data.php'); 
include('marker-role.php'); 
add_action('woocommerce_product_options_general_product_data', 'show_c_type_dropdown');

function show_c_type_dropdown() {
    global $post,$inv_shape;

    $value = get_post_meta($post->ID, 'c_type', true);

    woocommerce_wp_select(array(
        'id'          => 'c_type',
        'label'       => 'Category Type',
        'value'       => $value,
        'options'     => array(
            '0' => 'Regular',
            '1' => 'White Diamond',
            '2' => 'Setting',   // Ring builder
            '3' => 'Color Diamond', // Color Diamond
            '4' => 'Ring',
        ),
        'desc_tip'    => true,
        'description' => 'Select the category type'
    ));

    $s_dtype = get_post_meta($post->ID, 's_dtype', true);
        // Add dynamic shape options
    if (!empty($inv_shape)) {
        foreach ($inv_shape as $shape_key => $shape_val) {
            $options[$shape_val] = $shape_key;
        }
    }

    woocommerce_wp_select(array(
        'id'          => 's_dtype',
        'label'       => 'Diamond Type',
        'value'       => $s_dtype,
        'options'     => $options,
        'desc_tip'    => true,
        'description' => 'Select the category type'
    ));
}

add_action('woocommerce_process_product_meta', 'save_c_type_dropdown');

function save_c_type_dropdown($post_id) {
    if (isset($_POST['c_type'])) {
        update_post_meta($post_id, 'c_type', sanitize_text_field($_POST['c_type']));
    }
    if (isset($_POST['s_dtype'])) {
        update_post_meta($post_id, 's_dtype', sanitize_text_field($_POST['s_dtype']));
    }
}

// ===============================
// 1. Add "Registered Date" Column
// ===============================
add_filter('manage_users_columns', function($columns) {
    $columns['registered'] = 'Registered Date';
    return $columns;
});


// ===============================
// 2. Show Registration Date Value
// ===============================
add_filter('manage_users_custom_column', function($value, $column_name, $user_id) {

    if ($column_name == 'registered') {
        $user = get_userdata($user_id);
        return date('Y-m-d H:i:s', strtotime($user->user_registered));
    }

    return $value;

}, 10, 3);


// ===============================
// 3. Make Column Sortable
// ===============================
add_filter('manage_users_sortable_columns', function($columns) {
    $columns['registered'] = 'registered';
    return $columns;
});


// ===============================
// 4. Enable Sorting Logic
// ===============================
add_action('pre_get_users', function($query) {

    if (!is_admin()) return;

    if ($query->get('orderby') == 'registered') {
        $query->set('orderby', 'registered');
    }

});

function custom_admin_css() {
    ?>
    <style type="text/css">
        .wc-order-preview-addresses > div:first-child {
    float: right !important;
}
    </style>
    <?php
}
add_action('admin_head', 'custom_admin_css');