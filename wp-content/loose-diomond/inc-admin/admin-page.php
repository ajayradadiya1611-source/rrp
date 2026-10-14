<?php
/*include('ls-inventory/ls_inventory.php');
include('ls-inventory-color/ls_inventory_c.php');*/
include('variation-image-data.php'); 
include('marker-role.php'); 
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