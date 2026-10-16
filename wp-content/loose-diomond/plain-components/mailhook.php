<?php
add_filter( 'woocommerce_email_order_items_args', 'bbloomer_order_with_product_images', 9999 );
 
function bbloomer_order_with_product_images( $args ) {
   $args['show_image'] = true;
   return $args;
}

add_action( 'woocommerce_email_customer_details', 'add_custom_customer_info', 20, 4 );

function add_custom_customer_info( $order, $sent_to_admin, $plain_text, $email ) {
    if ( $sent_to_admin ) return;

    //echo '<p><strong>Custom Note:</strong> Thank you for your purchase!</p>';
}

add_action( 'woocommerce_email_before_order_table', 'show_customer_note_email', 10, 4 );

function show_customer_note_email( $order, $sent_to_admin, $plain_text, $email ) {

    $order_number   = $order->get_order_number();
    $order_date = wc_format_datetime( $order->get_date_created() );
    $payment    = $order->get_payment_method_title();

    echo '<table style="width:100%; margin:0 0 10px 0; border-collapse:collapse; border-spacing:0;color:#000;">';
    
    echo '<tr>
            <th style="text-align:left; padding:2px 0; font-weight:600;">Order Date</th>
            <th style="text-align:left; padding:2px 0; font-weight:600;">Order ID</th>
            <th style="text-align:left; padding:2px 0; font-weight:600;">Payment</th>
          </tr>';

    echo '<tr>
            <td style="padding:2px 0;">' . esc_html( $order_date ) . '</td>
            <td style="padding:2px 0;">#' . esc_html( $order_number ) . '</td>
            <td style="padding:2px 0;">' . esc_html( $payment ) . '</td>
          </tr>';

    echo '</table>';

    // Customer note
    if ( $order->get_customer_note() ) {

        echo '<p style="margin-top:10px;margin-bottom:10px;">
                <strong>Note</strong><br>';
        echo wp_kses(
            nl2br( wptexturize( $order->get_customer_note() ) ),
            array( 'br' => array() )
        );
        echo '</p>';
    }
    if ( $sent_to_admin !== true 
        && $order->get_payment_method() === 'lgdcrypto'
        && in_array( $order->get_status(), array( 'pending', 'on-hold' ) ) ) {
            ?>
            <div >
                <div class="customerBox2"></div>
                <h2>CRYPTO DETAILS:</h2>
                <p><strong>USDT Address</strong></p>
                <div class="row">
                    <div class="col-md-8">
                        <p class="crypto_adrs">TRC20 Address : <br><span id="p1">TGHhWnoczXqbj9WttDu4AEUFEVVs2uFNR4</span></p>
                        <ul style="color: #000;
    margin-left: 17px;
    padding: 0;
    list-style: disc;">
                            <li>Send <strong>only USDT</strong> to this deposit address.</li>
                            <li>Ensure you are using the <strong>Tron (TRC20) network</strong>.</li>
                            <li>Do not send <strong>NFTs</strong> to this address.</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <img src="<?php echo get_template_directory_uri();?>/aj_custom/comman/USDTCode.png">
                    </div>
                </div>
            </div>
            <?php
    }
    /*if ( $sent_to_admin !== true 
        && $order->get_payment_method() === 'lgdcrypto_bc'
        && in_array( $order->get_status(), array( 'pending', 'on-hold' ) ) ) {

        $order_id = $order->get_order_number();
        $payment_method = $order->get_payment_method_title();

        echo '
        <div style="margin-bottom:10px;margin-top:10px;">
            ' . esc_html( $payment_method ) . ' 
            Just to let you know — we\'ve received Order Summary #' . esc_html( $order_id ) . ', 
            and it is now on-hold until we confirm that payment has been received. 
            Here’s what you ordered.
        </div>';
    }*/

    // Optional: skip admin emails
    if ($sent_to_admin!=true && $order->get_payment_method() == 'bacs' &&
    in_array( $order->get_status(), array( 'pending', 'on-hold' ) )){



    echo '
    <div style="margin-bottom:10px;margin-top:10px;" >
        <h2 style="margin-bottom:15px;">Wire Details:</h2>

        <h3 style="margin-top:20px;font-size:18px;">Bank Account Details for United States (Domestic)</h3>
        <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
            <tr>
                <th colspan="2">Beneficiary Details</th>
            </tr>
            <tr>
                <td>Beneficiary Name</td>
                <td>Grown Diamond LLC</td>
            </tr>
            <tr>
                <td>Account Number</td>
                <td>202325705551</td>
            </tr>
            <tr>
                <td>Account Type</td>
                <td>Checking</td>
            </tr>
            <tr>
                <td>Beneficiary Address</td>
                <td>30 North Gould Street, Sheridan, WY 82801</td>
            </tr>

            <tr>
                <th colspan="2">Receiving Bank Details</th>
            </tr>
            <tr>
                <td>ABA Routing Number</td>
                <td>091311229</td>
            </tr>
            <tr>
                <td>Bank Name</td>
                <td>Choice Financial Group <br/> Mercury uses Choice Financial Group as a banking partner</td>
            </tr>
            <tr>
                <td>Bank Address</td>
                <td>4501 23rd Avenue S, Fargo, ND 58104</td>
            </tr>
        </table>

        <h3 style="margin-top:20px;font-size:18px;">Bank Account Details for Other Countries (International)</h3>
        <p>Use these details to send USD from bank accounts outside the United States.</p>
        <ul style="margin:0;padding:0;">
            <li>Payments typically take 4–5 business days.</li>
            <li>Your bank may charge additional transfer fees.</li>
        </ul>

       <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;margin-top: 20px;">
            <tr>
                <th colspan="2">Bank & Beneficiary Details</th>
            </tr>
            <tr>
                <td>Account Holder</td>
                <td>Grown Diamond LLC</td>
            </tr>
            <tr>
                <td>SWIFT/BIC</td>
                <td>CHFGUS44021</td>
            </tr>
            <tr>
                <td>Wire Routing Number</td>
                <td>091311229</td>
            </tr>
            <tr>
                <td>Account Number</td>
                <td>202325705551</td>
            </tr>
            <tr>
                <td>Bank Name</td>
                <td>Choice Financial Group</td>
            </tr>
            <tr>
                <td>Bank Address</td>
                <td>4501 23rd Avenue S, Fargo, ND 58104, USA</td>
            </tr>
            <tr>
                <td>Beneficiary Name</td>
                <td>Grown Diamond LLC</td>
            </tr>
            <tr>
                <td>Beneficiary Address</td>
                <td>30 North Gould Street, Sheridan, WY 82801, USA</td>
            </tr>
        </table>
    </div>';
    }
    if(in_array( $order->get_status(), array( 'pending', 'on-hold' )) && ($order->get_payment_method() === 'bacs' || $order->get_payment_method() === 'lgdcrypto')) {
        echo '<p>';
        echo 'If you have any questions about payment options, please contact us at '.SUPPORT_EMAIL.'. You can also chat with us online, and one of our representatives will usually respond immediately during business hours.';
        echo '</p>';
    }
    if ($sent_to_admin!=true && $order->get_payment_method() == 'oc_zelle' &&
    in_array( $order->get_status(), array( 'pending', 'on-hold' ) )){



    echo '
    <div style="margin-bottom:10px;margin-top:10px;" >
        <p style="margin-bottom:15px;">Zelle Details: billing@rrpdiamonds.com</p>
    </div>';
    }

    // ✅ Customer details manually added here
    $billing  = $order->get_address( 'billing' );
    $shipping = $order->get_address( 'shipping' );

    echo '<table style="width:100%; margin-bottom:10px; padding:0;border-collapse:collapse; color:#000;">';
    echo '<tr>';

     // Shipping address
    echo '<td style="width:50%; vertical-align:top;padding:0;">';
    echo '<h3 style="margin:0 0 8px;">Shipping Address</h3>';
    echo '<p style="margin:0; line-height:1.6;">';
    echo esc_html( $shipping['first_name'] . ' ' . $shipping['last_name'] ) . '<br>';
    if ( ! empty( $shipping['company'] ) ) echo esc_html( $shipping['company'] ) . '<br>';
    echo esc_html( $shipping['address_1'] ) . '<br>';
    if ( ! empty( $shipping['address_2'] ) ) echo esc_html( $shipping['address_2'] ) . '<br>';
    echo esc_html( $shipping['city'] . ', ' . $shipping['state'] . ' ' . $shipping['postcode'] ) . '<br>';
    echo esc_html(  WC()->countries->countries[$shipping['country']] ). '<br>';
     if ( ! empty( $shipping['phone'] ) ) echo esc_html( $shipping['phone'] ) . '<br>';
    if ( ! empty( $billing['email'] ) ) echo esc_html( $billing['email'] );
    echo '</p>';
    echo '</td>';
    
    // Billing address
    echo '<td style="width:50%; vertical-align:top; padding:0;">';
    echo '<h3 style="margin:0 0 8px;">Billing Address</h3>';
    echo '<p style="margin:0; line-height:1.6;">';
    echo esc_html( $billing['first_name'] . ' ' . $billing['last_name'] ) . '<br>';
    if ( ! empty( $billing['company'] ) ) echo esc_html( $billing['company'] ) . '<br>';
    echo esc_html( $billing['address_1'] ) . '<br>';
    if ( ! empty( $billing['address_2'] ) ) echo esc_html( $billing['address_2'] ) . '<br>';
    echo esc_html( $billing['city'] . ', ' . $billing['state'] . ' ' . $billing['postcode'] ) . '<br>';
    echo esc_html(  WC()->countries->countries[$billing['country']] ) . '<br>';
    if ( ! empty( $billing['phone'] ) ) echo esc_html( $billing['phone'] ) . '<br>';
   // if ( ! empty( $billing['email'] ) ) echo esc_html( $billing['email'] );
    echo '</p>';
    echo '</td>';

   

    echo '</tr>';
    echo '</table>';
}
add_action( 'woocommerce_email_after_order_table', 'my_custom_email_content', 10, 4 );

function my_custom_email_content( $order, $sent_to_admin, $plain_text, $email ) {

    // Skip admin emails
    if ( $sent_to_admin ) {
        return;
    }
    $payment_method = $order->get_payment_method();
    
        // Processing order email
        if ( $email->id === 'customer_processing_order' ) {

            echo '<p>Your order is currently being processed.</p>';
        }

        // Completed order email
        if ( $email->id === 'customer_completed_order' ) {

            echo '<p>Your order has been completed and shipped.</p>';
        }

        
        // On hold order email
        if ( $email->id === 'customer_on_hold_order' ) {

            if (
                $order->get_payment_method() === 'lgdcrypto_bc'
                && in_array( $order->get_status(), array( 'pending', 'on-hold' ), true )
            ) {

                echo '
                <p>
                    If you have any questions or need assistance completing your payment, 
                    please contact us at 
                    <a href="mailto:' . esc_attr( SUPPORT_EMAIL ) . '" style="color:#000; text-decoration:underline;">
                        ' . esc_html( SUPPORT_EMAIL ) . '
                    </a>. Our customer support team will be happy to assist you.
                </p>

                <p>
                    Thank you for choosing RRP Diamond.
                </p>

                <p>
                    Best regards,<br/>
                    RRP Diamond
                </p>';

            } else {

                echo '
                <p>
                    We look forward to fulfilling your order soon.<br/>
                    We\'ll send you a shipping confirmation email as soon as your item(s) are on the way. 
                    You can also <a href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">
                        track your order
                    </a> at any time. 
                    We appreciate your business and hope you enjoy your purchase.
                </p>';

            }
        }
}
add_filter('woocommerce_email_heading_customer_invoice', 'custom_invoice_email_headinga', 10, 2);

function custom_invoice_email_headinga($heading, $email) {

    $data = $email->get_data();
    $first_name = $data['billing']['first_name'];

    if (!empty($first_name)) {
        return 'Thank you for your order ' . esc_html($first_name) . ',';
    }

    return $heading;
}

add_action( 'woocommerce_email', function( $email_class ) {
    remove_action( 'woocommerce_email_customer_details', array( $email_class, 'customer_details' ), 10 );
    remove_action( 'woocommerce_email_customer_details', array( $email_class, 'email_addresses' ), 20 );
    remove_action( 'woocommerce_email_customer_details', array( $email_class, 'additional_checkout_fields' ), 30 );
});

add_filter( 'woocommerce_get_order_item_totals', function( $total_rows, $order, $tax_display ) {
    
    // Run only in emails
    if ( did_action( 'woocommerce_email_before_order_table' ) ) {
        if ( isset( $total_rows['payment_method'] ) ) {
            unset( $total_rows['payment_method'] );
        }
    }

    return $total_rows;

}, 10, 3 );

/**
 * Send customer + admin emails for LS Paypal (lgdcrypto_bc) orders that stay in "pending".
 * WooCommerce never emails on pending, so we fire the On-hold (customer) and New order (admin)
 * emails manually. The customer-on-hold-order.php template already handles pending + lgdcrypto_bc.
 * Guarded by order meta so it only sends once per order.
 */
function lgd_send_pending_order_emails_bc( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order || $order->get_payment_method() !== 'lgdcrypto_bc' ) {
        return;
    }
    if ( ! $order->has_status( 'pending' ) ) {
        return;
    }
    if ( $order->get_meta( '_lgd_bc_pending_email_sent' ) ) {
        return;
    }

    $emails = WC()->mailer()->get_emails();

    if ( ! empty( $emails['WC_Email_Customer_On_Hold_Order'] ) ) {
        $emails['WC_Email_Customer_On_Hold_Order']->trigger( $order_id, $order );
    }
    if ( ! empty( $emails['WC_Email_New_Order'] ) ) {
        $emails['WC_Email_New_Order']->trigger( $order_id, $order );
    }

    $order->update_meta_data( '_lgd_bc_pending_email_sent', time() );
    $order->add_order_note( 'Pending payment email sent to customer (LS Paypal).' );
    $order->save();
}

// Classic checkout + Blocks checkout.
add_action( 'woocommerce_checkout_order_processed', 'lgd_send_pending_order_emails_bc', 30, 1 );
add_action( 'woocommerce_store_api_checkout_order_processed', function( $order ) {
    lgd_send_pending_order_emails_bc( $order->get_id() );
}, 30, 1 );
