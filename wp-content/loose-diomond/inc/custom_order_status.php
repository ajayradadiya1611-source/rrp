<?php
//https://quadlayers.com/edit-custom-order-status-in-woocommerce/
//https://rudrastyh.com/woocommerce/order-statuses.html
//https://wordpress.stackexchange.com/questions/199289/how-to-create-a-custom-order-status-in-woocommerce
//https://www.businessbloomer.com/woocommerce-set-custom-order-status-for-new-orders/


// 1. Register Order Status
add_filter('woocommerce_register_shop_order_post_statuses', 'bbloomer_register_custom_order_status');
function bbloomer_register_custom_order_status($order_statuses)
{
    // Status must start with "wc-"
    $order_statuses['wc-order-received'] = array(
        'label'                     => _x('Order Received', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Order Received <span class="count">(%s)</span>', 'Order Received <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-order-confirmed'] = array(
        'label'                     => _x('Order Confirmed', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Order Confirmed <span class="count">(%s)</span>', 'Order Confirmed <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-cad-3d-design'] = array(
        'label'                     => _x('CAD & 3D Design', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('CAD & 3D Design <span class="count">(%s)</span>', 'CAD & 3D Design <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-crafting'] = array(
        'label'                     => _x('Crafting', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Crafting <span class="count">(%s)</span>', 'Crafting <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-quality-check'] = array(
        'label'                     => _x('Quality Check (QC)', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Quality Check (QC) <span class="count">(%s)</span>', 'Quality Check (QC) <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-dispatched'] = array(
        'label'                     => _x('Dispatched from Factory', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Dispatched from Factory <span class="count">(%s)</span>', 'Dispatched from Factory <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-shipped-shiv'] = array(
        'label'                     => _x('Shipped Shiv', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Shipped Shiv <span class="count">(%s)</span>', 'Shipped Shiv <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-shipped'] = array(
        'label'                     => _x('Shipped', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'woocommerce'),
    );

    $order_statuses['wc-delivered'] = array(
        'label'                     => _x('Delivered', 'Order status', 'woocommerce'),
        'public'                    => true, // previously false                                
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'woocommerce'),
    );

    return $order_statuses;
}

// 2. Show Order Status in the Dropdown @ Single Order and "Bulk Actions" @ Orders
add_filter('wc_order_statuses', 'bbloomer_show_custom_order_status');
function bbloomer_show_custom_order_status($order_statuses)
{
    $order_statuses['wc-order-received'] = _x('Order Received', 'Order status', 'woocommerce');
    $order_statuses['wc-order-confirmed'] = _x('Order Confirmed', 'Order status', 'woocommerce');
    $order_statuses['wc-cad-3d-design'] = _x('CAD & 3D Design', 'CAD & 3D Design', 'woocommerce');
    $order_statuses['wc-crafting'] = _x('Crafting', 'Crafting', 'woocommerce');
    $order_statuses['wc-quality-check'] = _x('Quality Check (QC)', 'Quality Check (QC)', 'woocommerce');

    //$order_statuses['wc-dispatched-from-factory'] = _x( 'Dispatched from Factory', 'Dispatched from Factory', 'woocommerce' );

    $order_statuses['wc-dispatched'] = _x('Dispatched from Factory', 'lstest', 'woocommerce');
    $order_statuses['wc-shipped'] = _x('Shipped', 'Shipped', 'woocommerce');
    $order_statuses['wc-shipped-shiv'] = _x('Shipped Shiv', 'Shipped shiv', 'woocommerce');
    $order_statuses['wc-delivered'] = _x('Delivered', 'Delivered', 'woocommerce');

    return $order_statuses;
}

add_filter('bulk_actions-edit-shop_order', 'bbloomer_get_custom_order_status_bulk');
function bbloomer_get_custom_order_status_bulk($bulk_actions)
{
    // Note: "mark_" must be there instead of "wc"
    $bulk_actions['mark_order-received'] = 'Change status to Order Received';
    $bulk_actions['mark_order-confirmed'] = 'Change status to Order Confirmed';
    $bulk_actions['mark_cad-3d-design'] = 'Change status to CAD & 3D Design';
    $bulk_actions['mark_crafting'] = 'Change status to Crafting';
    $bulk_actions['mark_quality-check'] = 'Change status to Quality Check (QC)';

    // $bulk_actions['mark_dispatched-from-factory'] = 'Change status to Dispatched from Factory';

    $bulk_actions['mark_dispatched'] = 'Change status to Dispatched from Factory';
    $bulk_actions['mark_shipped'] = 'Change status to Shipped';
    $bulk_actions['mark_shipped-shiv'] = 'Change status to Shipped Shiv';
    $bulk_actions['mark_delivered'] = 'Change status to Delivered';

    return $bulk_actions;
}

//from::https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column

// For displaying in columns.
add_filter('manage_edit-shop_order_columns', 'set_custom_edit_shop_order_columns');
function set_custom_edit_shop_order_columns($columns)
{
    $columns['custom_order_note'] = __('Custom Message', 'your_text_domain');
    return $columns;
}

// Add the data to the custom columns for the order post type:
add_action('manage_shop_order_posts_custom_order_note', 'custom_shop_order_column', 10, 2);
function custom_shop_order_column($column, $post_id)
{
    switch ($column) {
        case 'custom_order_note':
            echo esc_html(get_post_meta($post_id, 'custom_order_note', true));
            break;
    }
}

// For display and saving in order details page.
add_action('add_meta_boxes', 'add_shop_order_meta_box');
function add_shop_order_meta_box()
{
    add_meta_box(
        'custom_order_note',
        __('Custom Message', 'your_text_domain'),
        'shop_order_display_callback',
        'shop_order'
    );
}

// For displaying.
function shop_order_display_callback($post)
{
    $value = get_post_meta($post->ID, 'custom_order_note', true);
    echo '<input type="text" style="width:100%" id="custom_order_note" name="custom_order_note" value="' . esc_attr($value) . '">';
}

// For saving.
function save_shop_order_meta_box_data($post_id)
{
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'shop_order' == $_POST['post_type']) {
        if (! current_user_can('edit_shop_order', $post_id)) {
            return;
        }
    }

    // Make sure that it is set.
    if (! isset($_POST['custom_order_note'])) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field($_POST['custom_order_note']);
    // Update the meta field in the database.
    update_post_meta($post_id, 'custom_order_note', $my_data);
}
add_action('save_post', 'save_shop_order_meta_box_data');
