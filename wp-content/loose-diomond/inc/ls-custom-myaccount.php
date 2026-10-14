<?php

add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );
function my_account_menu_order() {
    $menuOrder = array(
        'orders'             => __( 'Orders', 'woocommerce' ),
        'my-wishlist'        => __( 'Wishlist', 'woocommerce' ),
        'edit-address'       => __( 'Addresses', 'woocommerce' ),
        'edit-account'       => __( 'Account Details', 'woocommerce' ),
        'customer-logout'    => __( 'Logout', 'woocommerce' ),
    );

    return $menuOrder;
}

add_filter('woocommerce_get_endpoint_url', 'change_wishlist_menu_link', 10, 4);
function change_wishlist_menu_link($url, $endpoint, $value, $permalink) {
    if ($endpoint === 'my-wishlist') {
        return home_url('/wishlist/');
    }

    return $url;
}

//LS custom redirect my-account page to my-account/orders/ page
//add_action ('template_redirect', 'ls_myaccount_redirect_to_orders');
function ls_myaccount_redirect_to_orders() {
    global $wp;

    if( $wp->request == 'my-account' ) {
        wp_redirect( site_url( 'my-account/orders/' ) );
        exit;
    }

    return;
}

// Remove cancel button from WooCommerce My account Orders
add_filter('woocommerce_my_account_my_orders_actions', 'remove_myaccount_orders_cancel_button', 10, 2);
function remove_myaccount_orders_cancel_button( $actions, $order ){
    unset($actions['cancel']);
    return $actions;
}

add_filter('ngettext', 'remove_item_count_from_my_account_orders', 105, 3 );
function remove_item_count_from_my_account_orders( $translated, $text, $domain ) {
    switch ( $text ) {
        case '%1$s for %2$s item' :
            $translated = '%1$s';
            break;

        case '%1$s for %2$s items' :
            $translated = '%1$s';
            break;
    }

    return $translated;
}

// ls change text login usename labl
add_filter(  'gettext',  'register_text'  );
add_filter(  'ngettext',  'register_text'  );
function register_text( $translated ) {
    $translated = str_ireplace(  'Username or email',  'Email',  $translated );
    return $translated;
}

add_filter( 'gettext', function( $translated_text, $text, $domain ) {
    if ( $domain === 'woocommerce' && $text === 'Please provide a valid account username.' ) {
        $translated_text = 'Please provide a valid Email Address.';
    }
    return $translated_text;
}, 10, 3 );

add_action('woocommerce_login_form_start', 'add_text_above_login_form');
function add_text_above_login_form() {
    echo '<p class="login-info">Already have an account? Sign in.</p>';
}

add_action('woocommerce_register_form_start', 'add_text_above_register_form');
function add_text_above_register_form() {
    echo '<div class="reg-info">
        <p style="margin: 0;">Create your Loose Grown Diamond account and enjoy:</p>
        <ul style="font-size: 14px;">
            <li>Easily request a ring resize online</li>
            <li>Faster, more secure checkout</li>
            <li>Access to exclusive offers and promotions</li>
            <li>Track orders and view your order history</li>
        </ul>
    </div>';
}


