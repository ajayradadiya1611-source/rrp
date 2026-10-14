<?php
/**
 * Custom WooCommerce Order Status Emails
 *
 * Registers transactional emails for all custom order statuses.
 * Add this file to your theme's functions.php (via require_once) or drop it
 * inside a custom plugin.
 *
 * Statuses covered:
 *  - Order Received
 *  - Order Confirmed
 *  - CAD & 3D Design
 *  - Crafting
 *  - Quality Check (QC)
 *  - Dispatched from Factory
 *  - Shipped
 *  - Delivered
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ---------------------------------------------------------------------------
// 1. BASE EMAIL CLASS
//    All 8 status emails extend this one class.
// ---------------------------------------------------------------------------

if ( ! class_exists( 'WC_Email_Custom_Order_Status_Base' ) ) :

class WC_Email_Custom_Order_Status_Base extends WC_Email {

    /** @var string The wc- status key (without the "wc-" prefix) */
    public $status_key;

    /**
     * @param string $status_key     e.g. 'order-received'
     * @param string $status_label   e.g. 'Order Received'
     * @param string $default_subject
     * @param string $default_heading
     */
    public function __construct( $status_key, $status_label, $default_subject, $default_heading ) {

        $this->status_key = $status_key;

        // Unique ID used internally by WooCommerce
        $this->id             = 'wc_custom_status_' . str_replace( '-', '_', $status_key );
        $this->customer_email = true;   // sent TO the customer
        $this->title          = $status_label;
        $this->description    = sprintf( 'Sent to the customer when their order status changes to "%s".', $status_label );

        // Default subject & heading (editable in WooCommerce > Settings > Emails)
        $this->subject  = $default_subject;
        $this->heading  = $default_heading;

        // Template paths – we use an inline fallback so no extra template files are required
        $this->template_html  = 'emails/custom-order-status.php';
        $this->template_plain = 'emails/plain/custom-order-status.php';

        // Hook that fires this email automatically on status change
        add_action( 'woocommerce_order_status_' . $status_key, array( $this, 'trigger' ), 10, 2 );

        // Call the parent constructor AFTER setting properties
        parent::__construct();
    }

    // -----------------------------------------------------------------------
    // Trigger
    // -----------------------------------------------------------------------

    /**
     * Fires when the order reaches this status.
     *
     * @param int           $order_id
     * @param WC_Order|null $order
     */
    public function trigger( $order_id, $order = null ) {

        $this->setup_locale();

        if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }

        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }

        $this->object                         = $order;
        $this->recipient                      = $this->object->get_billing_email();
        $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
        $this->placeholders['{order_number}'] = $this->object->get_order_number();

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send(
                $this->get_recipient(),
                $this->get_subject(),
                $this->get_content(),
                $this->get_headers(),
                $this->get_attachments()
            );
        }

        $this->restore_locale();
    }

    // -----------------------------------------------------------------------
    // Content helpers
    // -----------------------------------------------------------------------

    /** Returns placeholders supported in subject/heading fields. */
    public function get_default_subject() {
        return $this->subject;
    }

    public function get_default_heading() {
        return $this->heading;
    }

    /** HTML email body */
    public function get_content_html() {
        return $this->build_html_content( false );
    }

    /** Plain-text email body */
    public function get_content_plain() {
        return $this->build_html_content( true );
    }

    /**
     * Builds email content inline (no separate template file needed).
     *
     * @param bool $plain
     * @return string
     */
    private function build_html_content( $plain = false ) {

        $order   = $this->object;
        $heading = $this->get_heading();

        if ( $plain ) {
            return $this->build_plain_content( $order, $heading );
        }

        // ---- HTML content ----
        ob_start();

        // WooCommerce email header
        do_action( 'woocommerce_email_header', $heading, $this );

        // Status-specific intro message
        echo wp_kses_post( $this->get_status_intro_message( $order ) );
       

        if ( in_array( $order->get_status(), array( 'order-received', 'order-confirmed' ) ) ) {
            // Order details table
           do_action(
                'woocommerce_email_order_details',
                $order,
                (bool) $this->sent_to_admin,
                (bool) $this->plain_text,
                $this
            );

            // Order meta (custom fields)
            do_action(
                'woocommerce_email_order_meta',
                $order,
                (bool) $this->sent_to_admin,
                (bool) $this->plain_text,
                $this
            );

            // Customer billing/shipping address
            do_action(
                'woocommerce_email_customer_details',
                $order,
                (bool) $this->sent_to_admin,
                (bool) $this->plain_text,
                $this
            );
        }
        if ( in_array( $order->get_status(), array( 'order-received' ) ) ) : ?> 
            <p class="has-text-align-center">
                Thanks again! If you need any help with your order, please contact us at <a href="mailto:<?php echo SUPPORT_EMAIL;?>" style="color:#000;"><?php echo SUPPORT_EMAIL;?></a>.
            </p>
        <?php endif; 
        // WooCommerce email footer
        do_action( 'woocommerce_email_footer', $this );

        return ob_get_clean();
    }

    /**
     * Plain-text version of the email.
     */
    private function build_plain_content( $order, $heading ) {
        $content  = '= ' . $heading . " =\n\n";
        $content .= $this->get_status_intro_message( $order ) . "\n\n";
        $content .= '---' . "\n";
        $content .= sprintf( __( 'Order number: %s', 'woocommerce' ), $order->get_order_number() ) . "\n";
        $content .= sprintf( __( 'Order date: %s', 'woocommerce' ), wc_format_datetime( $order->get_date_created() ) ) . "\n";
        $content .= sprintf( __( 'Order total: %s', 'woocommerce' ), $order->get_formatted_order_total() ) . "\n\n";

        // Items
        foreach ( $order->get_items() as $item ) {
            $content .= $item->get_name() . ' x ' . $item->get_quantity() . ' — ' . wc_price( $item->get_total() ) . "\n";
        }

        $content .= "\n" . '---' . "\n";
        $content .= sprintf( __( 'Thank you for your order!', 'woocommerce' ) ) . "\n";

        return $content;
    }

    /**
     * Returns the status-specific introductory paragraph shown at the top of the email.
     * Override this in a child class or extend the filter to customise per-status copy.
     *
     * @param WC_Order $order
     * @return string
     */
    public function get_status_intro_message( $order ) {
        $first_name = $order->get_billing_first_name();

        $tracking_numbers = $order->get_meta('_tracking_numbers');

        // Backward compatibility
        if (empty($tracking_numbers)) {
            $old_number = $order->get_meta('_tracking_number');
            $old_carrier = $order->get_meta('_carrier');
            if ($old_number || $old_carrier) {
                $tracking_numbers = array(
                    array(
                        'carrier' => $old_carrier,
                        'number'  => $old_number
                    )
                );
            }
        }

        $tracking_html = '';

        if (!empty($tracking_numbers) && is_array($tracking_numbers)) {
            foreach ($tracking_numbers as $t) {
                $carrier = isset($t['carrier']) ? $t['carrier'] : '';
                $number  = isset($t['number']) ? $t['number'] : '';

                if (!$carrier && !$number) continue;

                // If the helper exists, link the tracking number to the carrier tracking page.
                if ( function_exists( 'get_tracking_link' ) ) {
                    $link = get_tracking_link( $carrier, $number );
                    $tracking_html .= "
                        <p style='margin:0;'>
                            <strong>Carrier:</strong> " . esc_html( $carrier ) . "<br>
                            <strong>Tracking Number:</strong> <a href='" . esc_url( $link ) . "' target='_blank' rel='noopener noreferrer'>" . esc_html( $number ) . "</a>
                        </p><br>
                    ";
                } else {
                    $tracking_html .= "
                        <p style='margin:0;'>
                            <strong>Carrier:</strong> " . esc_html( $carrier ) . "<br>
                            <strong>Tracking Number:</strong> " . esc_html( $number ) . "
                        </p><br>
                    ";
                }
            }
        }

        // Determine tracking-update ETA note based on carrier(s) used on this order.
        // FedEx: updates within ~24 hours. All other carriers: ~48-72 hours.
        $has_fedex         = false;
        $has_other_carrier = false;

        if ( ! empty( $tracking_numbers ) && is_array( $tracking_numbers ) ) {
            foreach ( $tracking_numbers as $t ) {
                $carrier_name = isset( $t['carrier'] ) ? strtolower( trim( $t['carrier'] ) ) : '';
                if ( $carrier_name === '' ) {
                    continue;
                }
                if ( strpos( $carrier_name, 'fedex' ) !== false ) {
                    $has_fedex = true;
                } else {
                    $has_other_carrier = true;
                }
            }
        }

        

        

            $messages = array(
            'order-received' => sprintf(
            '<div>
            <p>Hi %s,</p>

            <p>Just to let you know — we\'ve received the payment for your order.</p>

            <h3 style="margin-top:20px; margin-bottom:10px;">Next Steps</h3>

            <p>
                We\'ll source your diamond from our overseas vendors, which usually takes
                <strong>24–48 working hours</strong>. If available, we’ll ship it; if not,
                we’ll email you with the best alternatives.
                You can cancel if unsatisfied — <strong>no charge!</strong>
            </p></div>
            ',
            $first_name
        ),
            'order-confirmed' => sprintf(
                '
                <div>

                    <p>Dear %s,</p>

                    <p>
                        Congratulations! Your order has been confirmed and is now being processed.
                    </p>

                    <p>
                        To view your order details and estimated delivery timeframe, please log in to 
                        <a href="%s" style="color:#000; text-decoration:underline;">My Account</a> on our website.
                    </p>

                    <p>
                        We\'ll send you a shipping confirmation email with tracking information as soon as your order has been dispatched.
                    </p>

                </div>
                ',
                $first_name,
                wc_get_account_endpoint_url( 'orders' )
            ),
            'cad-3d-design' => sprintf(
                '
                <div>

                    <p>Hello %s,</p>

                    <p>
                        We hope you\'re doing well.
                    </p>

                    <p>
                        Your order <strong>#%s</strong> has now moved to our CAD &amp; 3D Design department. 
                        Our designers are creating detailed CAD drawings and 3D renderings of your jewelry 
                        to ensure every detail is perfect before production begins.
                    </p>

                    <p>
                        This stage typically takes <strong>24 business hours</strong>. Once your design is ready, 
                        we\'ll email it to you for review and approval before we begin crafting your jewelry.
                    </p>

                    <p>
                        If you have any questions or would like to make any changes, please don\'t hesitate to 
                        contact us at 
                        <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                            ' . SUPPORT_EMAIL . '
                        </a>. Our team is always happy to assist you.
                    </p>

                    <p>
                        Thank you for choosing Loose Grown Diamond.
                    </p>

                    <p style="margin-top:20px;">
                        Best regards,<br>
                        ' . SITE_OC_NAME . '
                    </p>

                </div>
                ',
                $first_name,
                $order->get_order_number()
            ),
            'crafting' => sprintf(
            '
            <div>

                <p>Hello %s,</p>

                <p>
                    We hope you\'re doing well.
                </p>

                <p>
                    We\'re pleased to let you know that your order <strong>#%s</strong> has now moved to the crafting stage. 
                    Our skilled artisans have begun creating your jewelry with great care and attention to detail.
                </p>

                <p>
                    The crafting process typically takes up to <strong>2–3 business weeks</strong>. 
                    If you\'ve selected Express Shipping, your jewelry is expected to be crafted in under 2 weeks.
                </p>

                <p>
                    Once the crafting process is complete, we\'ll notify you and provide another update as your order moves to the next stage.
                </p>

                <p>
                    If you have any questions or concerns, please don\'t hesitate to contact us at 
                    <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                        ' . SUPPORT_EMAIL . '
                    </a>. We\'re always happy to help.
                </p>

                <p>
                    Thank you for choosing Loose Grown Diamond.
                </p>

                <p style="margin-top:20px;">
                    Best regards,<br>
                    ' . SITE_OC_NAME . '
                </p>

            </div>
            ',
            $first_name,
            $order->get_order_number()
        ),
            'quality-check' => sprintf(
                '
                <div>

                    <p>Hello %s,</p>

                    <p>
                        We hope you\'re doing well.
                    </p>

                    <p>
                        We\'re pleased to let you know that your order <strong>#%s</strong> has completed the crafting stage 
                        and has now moved to our Quality Check process.
                    </p>

                    <p>
                        At Loose Grown Diamond, every piece of jewelry undergoes a thorough inspection to ensure it meets 
                        our high standards of craftsmanship, quality, and finish. Our team carefully examines every detail 
                        so you can be confident that you\'re receiving a beautifully crafted piece.
                    </p>

                    <p>
                        Because we know your jewelry is both a financial and emotional investment, we take every step to 
                        ensure it arrives in perfect condition. This quality check typically takes up to 
                        <strong>48 business hours</strong>. Once your order has passed inspection, it will be prepared 
                        for dispatch, and we\'ll send you a shipping confirmation with tracking details.
                    </p>

                    <p>
                        If you have any questions or concerns, please don\'t hesitate to contact us at 
                        <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                            ' . SUPPORT_EMAIL . '
                        </a>. We\'re always happy to help.
                    </p>

                    <p>
                        Thank you for choosing Loose Grown Diamond.
                    </p>

                    <p style="margin-top:20px;">
                        Best regards,<br>
                        ' . SITE_OC_NAME . '
                    </p>

                </div>
                ',
                $first_name,
                $order->get_order_number()
            ),
            'dispatched' => sprintf(
                '
                <div>

                    <p>Hello %s,</p>

                    <p>
                        We\'re excited to let you know that your order <strong>#%s</strong> has been shipped from our factory 
                        and is on its way to our NYC office.
                    </p>

                    <p>
                        Your order may take up to <strong>2–5 business days</strong> to arrive at our NYC office. 
                        Once it arrives, we\'ll perform a final quality review, ship it to your delivery address, 
                        and email you your tracking number so you can track your shipment every step of the way.
                    </p>

                    <p>
                        If you have any questions or concerns, please don\'t hesitate to contact us at 
                        <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                            ' . SUPPORT_EMAIL . '
                        </a>. We\'re always happy to help.
                    </p>

                    <p>
                        Thank you for choosing Loose Grown Diamond.
                    </p>

                    <p style="margin-top:20px;">
                        Best regards,<br>
                        ' . SITE_OC_NAME . '
                    </p>

                </div>
                ',
                $first_name,
                $order->get_order_number()
            ),
            'shipped' => sprintf(
                '
                <div>

                    <p>Hello %s,</p>

                    <p>
                        We\'re excited to let you know that your order <strong>#%s</strong> has been shipped and is on its way!
                    </p>

                    <p>
                        You can track your order at any time by logging in to 
                        <a href="' . wc_get_account_endpoint_url( 'orders' ) . '" style="color:#000; text-decoration:underline;">
                            My Account
                        </a> on our website and viewing your order details. Alternatively, you can use the tracking information below:
                    </p>

                    <div style="background:#f5f5f5; padding:12px; border-radius:6px; margin:15px 0;">
                        %s
                    </div>

                    <p>%s</p>

                    <p>
                        Please note that it may take up to <strong>24 hours</strong> for the tracking information to appear on the carrier\'s website.
                    </p>

                    <p>
                        To help protect your purchase, we recommend insuring your ring or jewelry against loss, theft, or accidental damage. 
                        Jewelry is a valuable investment, and insurance can provide added peace of mind. 
                        We recommend exploring trusted <a href="https://my.jewelersmutual.com/jewelry-insurance-quote-apply/t/qcm8gVgRy0SR1ErzagkM9Q">third-party jewelry insurance</a>.
                    </p>

                    <p>
                        If you have any questions or concerns, please don\'t hesitate to contact us at 
                        <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                            ' . SUPPORT_EMAIL . '
                        </a>. We\'re always happy to help.
                    </p>

                    <p>
                        Thank you for choosing Loose Grown Diamond. We hope you enjoy your new jewelry!
                    </p>

                    <p style="margin-top:20px;">
                        Best regards,<br>
                        ' . SITE_OC_NAME . '
                    </p>

                </div>
                ',
                $first_name,
                $order->get_order_number(),
                $tracking_html,
                $tracking_eta_note
            ),
            'delivered' => sprintf(
            '
            <div>

                <p>Hello %s,</p>

                <p>
                    We\'re delighted to let you know that your order <strong>#%s</strong> has been successfully delivered.
                </p>

                <p>
                    We hope you love your new jewelry and that it brings you joy for years to come. 
                    It has been our pleasure to create this special piece for you.
                </p>

                <p>
                    <strong>✨ Share Your Moment</strong>
                </p>

                <p>
                    We\'d love to see your new jewelry! Share your photos or proposal story on Instagram 
                    and tag <strong>@LooseGrownDiamond</strong> for a chance to be featured on our social media.
                </p>

                <p>
                    <strong>🛡️ Protect Your Investment</strong>
                </p>

                <p>
                    Your jewelry is a valuable investment. For added peace of mind, we recommend protecting it 
                    against loss, theft, or accidental damage with a trusted <a href="https://my.jewelersmutual.com/jewelry-insurance-quote-apply/t/qcm8gVgRy0SR1ErzagkM9Q">third-party jewelry insurance</a>.
                </p>

                <p>
                    Thank you for choosing Loose Grown Diamond. We truly appreciate your trust and support, 
                    and we hope to serve you again in the future.
                </p>

                <p>
                    If you have any questions about your order or need any assistance, please don\'t hesitate 
                    to contact us at 
                    <a href="mailto:' . SUPPORT_EMAIL . '" style="color:#000; text-decoration:underline;">
                        ' . SUPPORT_EMAIL . '
                    </a>. Our customer support team is always happy to help.
                </p>

                <p>
                    Thank you once again, and enjoy your new jewelry!
                </p>

                <p style="margin-top:20px;">
                    Best regards,<br>
                    ' . SITE_OC_NAME . '
                </p>

            </div>
            ',
            $first_name,
            $order->get_order_number()
        ),
            'delivered-jewelry' => '<div>
                                    <p>Hi ' . $first_name . ',</p>
                                    <p>Congratulations once again on your new Diamond/Ring — a timeless piece that deserves lasting brilliance. Now that it\'s in your hands, let\'s make sure it stays stunning for years to come.</p>

                                    <h3>How to Keep Your Diamond Sparkling</h3>
                                    <p>Lab-grown diamonds may be created differently, but they deserve the same attention and care. Here\'s a quick way to clean your diamond safely at home:</p>
                                    <ul>
                                        <li>Mix warm water with a few drops of mild dish soap in a bowl.</li>
                                        <li>Soak the diamond jewelry for 20 to 40 minutes.</li>
                                        <li>Use a soft toothbrush to gently scrub around the diamond and setting.</li>
                                        <li>Rinse thoroughly under warm water and dry with a lint-free cloth.</li>
                                    </ul>
                                    <p>You can also explore our full cleaning guide here: 
                                    <a href="https://www.loosegrowndiamond.com/how-to-clean-a-lab-grown-diamond/" style="color:#000; text-decoration:underline;">How to Clean a Lab-Grown Diamond</a></p>

                                    <h3>Protect Your Precious Piece</h3>
                                    <p>Accidents happen — but peace of mind is priceless. We recommend protecting your ring with jewelry insurance, which can cover:</p>
                                    <ul>
                                        <li>Loss, theft, or damage</li>
                                        <li>Worldwide coverage</li>
                                        <li>Repairs or replacement at full value</li>
                                        <li>Quick and seamless claims process</li>
                                    </ul>
                                    <p>You can learn more about insuring your jewelry here: 
                                    <a href="https://www.loosegrowndiamond.com/jewelry-insurance/" style="color:#000; text-decoration:underline;">Jewelry Insurance 101</a></p>

                                    <h3>Looking for More?</h3>
                                    <p>Looking for more tips on diamonds, settings, and styling ideas? Check out our full blog here: 
                                    <a href="https://www.loosegrowndiamond.com/blog/" style="color:#000; text-decoration:underline;">Explore More Jewelry Guides</a></p>

                                    <p>Thank you for choosing Loose-Grown Diamond — we\'re honored to be part of your journey. If you need any help or have questions, we\'re just an email or a call away.</p>

                                    <p>Warm regards,<br>
                                    <strong>The Loose Grown Diamond Team</strong></p>
                                </div>'
        );

        

        

        $message = isset( $messages[ $this->status_key ] )
            ? $messages[ $this->status_key ]
            : sprintf( 'Hi %s, the status of your order has been updated.', $first_name );

        /**
         * Filter the intro message for a given custom status email.
         *
         * @param string   $message
         * @param WC_Order $order
         * @param string   $status_key
         */
        return apply_filters( 'wc_custom_status_email_intro_message', $message, $order, $this->status_key );
    }
}

endif;

 
// ---------------------------------------------------------------------------
// 2. ONE CONCRETE CLASS PER STATUS
//    WooCommerce requires unique class names in the email registry.
// ---------------------------------------------------------------------------

// --- Order Received ---
if ( ! class_exists( 'WC_Email_Status_Order_Received' ) ) :
class WC_Email_Status_Order_Received extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'order-received',
            'Order Received',
            __( 'Your order has been received – {order_number}', 'woocommerce' ),
            __( 'Order Received', 'woocommerce' )
        );
    }
}
endif;

// --- Order Confirmed ---
if ( ! class_exists( 'WC_Email_Status_Order_Confirmed' ) ) :
class WC_Email_Status_Order_Confirmed extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'order-confirmed',
            'Order Confirmed',
            __( 'Your order is confirmed – {order_number}', 'woocommerce' ),
            __( 'Order Confirmed', 'woocommerce' )
        );
    }
}
endif;

// --- CAD & 3D Design ---
if ( ! class_exists( 'WC_Email_Status_Cad_3d_Design' ) ) :
class WC_Email_Status_Cad_3d_Design extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'cad-3d-design',
            'CAD & 3D Design',
            __( 'Design work has started on your order – {order_number}', 'woocommerce' ),
            __( 'CAD & 3D Design In Progress', 'woocommerce' )
        );
    }
}
endif;

// --- Crafting ---
if ( ! class_exists( 'WC_Email_Status_Crafting' ) ) :
class WC_Email_Status_Crafting extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'crafting',
            'Crafting',
            __( 'Your order is being crafted – {order_number}', 'woocommerce' ),
            __( 'Crafting Your Order', 'woocommerce' )
        );
    }
}
endif;

// --- Quality Check (QC) ---
if ( ! class_exists( 'WC_Email_Status_Quality_Check' ) ) :
class WC_Email_Status_Quality_Check extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'quality-check',
            'Quality Check (QC)',
            __( 'Your order is undergoing quality checks – {order_number}', 'woocommerce' ),
            __( 'Quality Check in Progress', 'woocommerce' )
        );
    }
}
endif;

// --- Dispatched from Factory ---
if ( ! class_exists( 'WC_Email_Status_Dispatched' ) ) :
class WC_Email_Status_Dispatched extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'dispatched',
            'Dispatched from Factory',
            __( 'Your order has left our factory – {order_number}', 'woocommerce' ),
            __( 'Dispatched from Factory', 'woocommerce' )
        );
    }
}
endif;

// --- Shipped ---
if ( ! class_exists( 'WC_Email_Status_Shipped' ) ) :
class WC_Email_Status_Shipped extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'shipped',
            'Shipped',
            __( 'Your order is on its way – {order_number}', 'woocommerce' ),
            __( 'Your Order Has Shipped', 'woocommerce' )
        );
    }
}
endif;

// --- Delivered ---
if ( ! class_exists( 'WC_Email_Status_Delivered' ) ) :
class WC_Email_Status_Delivered extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'delivered',
            'Delivered',
            __( 'Your order has been delivered – {order_number}', 'woocommerce' ),
            __( 'Order Delivered', 'woocommerce' )
        );
    }
}
endif;

// --- Delivered (Jewelry) ---
if ( ! class_exists( 'WC_Email_Status_Delivered_Jewelry' ) ) :
class WC_Email_Status_Delivered_Jewelry extends WC_Email_Custom_Order_Status_Base {
    public function __construct() {
        parent::__construct(
            'delivered-jewelry',
            'Delivered (Jewelry)',
            __( 'Your jewellery order has been delivered – {order_number}', 'woocommerce' ),
            __( 'Jewellery Order Delivered', 'woocommerce' )
        );
    }
}
endif;


// ---------------------------------------------------------------------------
// 3. REGISTER ALL EMAIL CLASSES WITH WOOCOMMERCE
//    They now appear under WooCommerce > Settings > Emails.
// ---------------------------------------------------------------------------

 
// ---------------------------------------------------------------------------
// 4. OPTIONAL: CUSTOMISE A SPECIFIC STATUS EMAIL'S MESSAGE
//
//    Use this filter anywhere (e.g. functions.php) to override the intro
//    paragraph for a particular status without editing the class above.
//
//    Example – change the "shipped" message:
//
//    add_filter( 'wc_custom_status_email_intro_message', function( $message, $order, $status_key ) {
//        if ( $status_key === 'shipped' ) {
//            return sprintf(
//                'Hi %s, your parcel is on its way! Track it here: https://example.com/track',
//                $order->get_billing_first_name()
//            );
//        }
//        return $message;
//    }, 10, 3 );
// ---------------------------------------------------------------------------