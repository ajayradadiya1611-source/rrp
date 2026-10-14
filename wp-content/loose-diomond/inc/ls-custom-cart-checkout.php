<?php 
/**
 * Send custom diamond email after order is placed
 */
add_action('woocommerce_checkout_order_processed', 'ls_send_diamond_email_after_order_placed', 20, 1);
function ls_send_diamond_email_after_order_placed($order_id) {
    // Don't send if order ID is invalid
    if (!$order_id) {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        error_log("Order {$order_id} not found");
        return;
    }

    unset($_SESSION['ls_ring_builder']); //ringbuilder process session
    unset($_SESSION['ls_ring_builder_cartadded']); //Global session that store cart added records
    
    // Check if email was already sent
    if ($order->get_meta('_lgd_diamond_email_sent', true)) {
        error_log("Diamond email already sent for order {$order_id}");
        return;
    }
    
    // Check if order contains ONLY diamond products
    $has_only_diamond_products = true;
    $non_diamond_products = array();
    
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product) {
            $c_type = get_post_meta($product->get_id(), 'c_type', true);
            
            if ($c_type == '1' || $c_type == '3') {
                error_log("Order {$order_id} - Found diamond product: {$product->get_name()} (ID: {$product->get_id()}, c_type: {$c_type})");
            } else {
                $has_only_diamond_products = false;
                $non_diamond_products[] = array(
                    'name' => $product->get_name(),
                    'id' => $product->get_id(),
                    'c_type' => $c_type
                );
                error_log("Order {$order_id} - Found non-diamond product: {$product->get_name()} (ID: {$product->get_id()}, c_type: {$c_type})");
            }
        }
    }
    
    // If order has any non-diamond products, don't send email
    if (!$has_only_diamond_products) {
        error_log("Order {$order_id} contains non-diamond products. Email NOT sent.");
        error_log("Non-diamond products found: " . json_encode($non_diamond_products));
        return;
    }
    
    // If order is empty (shouldn't happen but just in case)
    if (count($order->get_items()) === 0) {
        error_log("Order {$order_id} has no products");
        return;
    }
    
    // Get customer email and name
    $to = $order->get_billing_email();
    if (empty($to)) {
        error_log("No email address found for order {$order_id}");
        return;
    }
    
    $first_name = $order->get_billing_first_name();
    if (empty($first_name)) {
        $first_name = $order->get_shipping_first_name();
    }
    if (empty($first_name)) {
        $first_name = __('there', 'woocommerce');
    }
    
    // Get customer order number for reference
    $order_number = $order->get_order_number();
    
    // Email subject - Add order reference
    $subject = "Regarding Your Order #{$order_number}: – Loose Grown Diamond Complete Your Ring";
    
    // Email headers with proper classification
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('woocommerce_email_from_address') . '>',
        'Reply-To: ' . get_option('woocommerce_email_from_address'),
        'X-Priority: 1',
        'X-Mailer: PHP/' . phpversion(),
        'List-Unsubscribe: <mailto:' . get_option('woocommerce_email_from_address') . '?subject=Unsubscribe>',
        'Precedence: bulk',
        'Auto-Submitted: auto-generated',
        'X-Auto-Response-Suppress: OOF, AutoReply',
    );
    
    ob_start();

    wc_get_template(
        'inc/email-templates/order/user-custom-diamond.php',
        array(
            'order_number' => $order_number,
            'first_name'   => $first_name
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $message_body = ob_get_clean();

    // Send email
    $sent = wp_mail($to, $subject, $message_body, $headers);
    
    if ($sent) {
        // Mark as sent
        $order->update_meta_data('_lgd_diamond_email_sent', current_time('mysql'));
        $order->save();
        error_log("✅ Diamond email sent successfully to {$to} for order {$order_id}");
    } else {
        error_log("❌ Failed to send diamond email to {$to} for order {$order_id}");
    }
}


