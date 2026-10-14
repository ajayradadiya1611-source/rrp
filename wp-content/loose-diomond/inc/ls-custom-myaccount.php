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

//LS Custom add two new column add to order table on my account page by D
add_filter( 'woocommerce_account_orders_columns', 'ls_account_orders_custom_columns', 10, 1 );
function ls_account_orders_custom_columns( $columns ) {
    $new_columns = array();

    foreach ( $columns as $key => $name ) {
        $new_columns[ $key ] = $name;

        // Add after order total column
        if ( $key === 'order-total' ) {
            $new_columns['order-certificate'] = __( 'Certificate', 'woocommerce' );
            $new_columns['order-video'] = __( 'Video', 'woocommerce' );
        }
    }

    return $new_columns;
}

//LS add certficate column data to order table on my account page by D
add_action( 'woocommerce_my_account_my_orders_column_order-certificate', 'ls_account_orders_custom_column_for_certificate', 10, 1 );
function ls_account_orders_custom_column_for_certificate( $order ) {
    global $wpdb;
    echo '<div class="d-flex align-items-center justify-content-center gap-1">';

    foreach ( $order->get_items() as $item_key => $item ) {
        $pid     = $item->get_product_id();
        $product = $item->get_product();
        $sku     = $product ? $product->get_sku() : '';

        $c_type  = get_post_meta( $pid, 'c_type', true );

        if ( $c_type == 1 || $c_type == 3 ) {
            $record = null;
            $source = 'none';

            if ( ! empty( $sku ) ) {
                $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ls_inventory WHERE sku = %s LIMIT 1", $sku ) );
                if ( $record ) $source = 'ls_inventory_sku';
            }

            if ( ! $record && ! empty( $pid ) ) {
                $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ls_inventory WHERE product_id = %d LIMIT 1", $pid ) );
                if ( $record ) $source = 'ls_inventory_pid';
            }

            if ( ! $record && ! empty( $sku ) ) {
                $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ls_inventory_c WHERE sku = %s LIMIT 1", $sku ) );
                if ( $record ) $source = 'ls_inventory_c_sku';
            }

            if ( ! $record && ! empty( $pid ) ) {
                $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ls_inventory_c WHERE product_id = %d LIMIT 1", $pid ) );
                if ( $record ) $source = 'ls_inventory_c_pid';
            }

            if ($record && !empty($record->certificate)) {
                $certificate_url = $record->certificate;
            } else {
                $certificate_url = get_post_meta($item_key, "certificate_url", true);
                if ( ! empty( $certificate_url ) && $source === 'none' ) {
                    $source = 'meta_certificate_url';
                }
            }

            if (!empty($certificate_url)) {
                echo '<div class="certilink text-center" data-attr="' . esc_attr($source) . '" style="margin-top: 0px; font-size: 15px;"><a href="' . esc_url($certificate_url) . '" target="_blank"><i class="fa fa-file-pdf" aria-hidden="true"></i></a></div>';
            } else {
                echo " ";
            }

        } else {
            echo " ";
        }
    }
    echo '</div>';
}

//LS add video column data to order table on my account page by D
add_action( 'woocommerce_my_account_my_orders_column_order-video', 'ls_account_orders_custom_column_for_video', 10, 1 );
function ls_account_orders_custom_column_for_video( $order ) {
 	global $wpdb;    
    echo '<div class="d-flex align-items-center justify-content-center gap-1">';
    foreach ( $order->get_items() as $item_key => $item ) {
        $pid = $item['product_id'];
        $c_type = get_post_meta($pid, 'c_type', true);

        if($c_type==1){ 
	        $table = 'ls_inventory';
	        $record = null;
	        $source = 'none';

	        $product = wc_get_product( $pid );
	        $sku     = $product ? $product->get_sku() : '';

	        if ( ! empty( $sku ) ) {
	            $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE sku = %s LIMIT 1", $sku ) );
	            if ( $record ) $source = 'ls_inventory_sku';
	        }

	        if ( ! $record && ! empty( $pid ) ) {
	            $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE product_id = %d LIMIT 1", $pid ) );
	            if ( $record ) $source = 'ls_inventory_pid';
	        }

	        $certificate = $record->certificate;
	        $video = $record->video;
           // print_r($record); 
	        if($video!=''){
	          //  $parsed = parse_url($video);
	                 
	          //  if (empty($parsed['scheme'])) {
	              //  $video = 'http://' . ltrim($video, '/');
	                echo '<div class="text-center"><a class="vdobtn ls_play_video" href="'.$video.'" target="_blank" data-id="video_pup" data-attr="' . esc_attr($source) . '" style="padding: 0;"><i class="fa fa-video-camera"></i></a></div>';
	          //  }
            }else{
                echo " ";
            }
        }else{
            echo " ";
        }
    }
     echo '</div>';
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


