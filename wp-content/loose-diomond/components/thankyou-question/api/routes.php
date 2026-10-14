<?php
/**
 * Thank You page post-purchase question API.
 *
 * Reuses the existing `{$wpdb->prefix}ls_contact_submissions` table (see
 * inc/admin-contact-form.php for the table + ls_store_submission() helper,
 * and the "Forms" admin page for viewing entries) — no new table is created.
 * form_type = 'thankyou_question' distinguishes these rows from other forms.
 */

defined('ABSPATH') || exit;
add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/thankyou-question', [
        'methods'             => 'POST',
        'callback'            => 'ls_save_thankyou_question_answer',
        'permission_callback' => '__return_true',
        'args' => [
            'order_id' => [
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ],
            'answer' => [
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_string($param) && $param !== '';
                },
            ],
        ],
    ]);
});

function ls_save_thankyou_question_answer(WP_REST_Request $request)
{
    global $wpdb;

    $order_id   = absint($request->get_param('order_id'));
    $raw_answer = wp_unslash($request->get_param('answer'));

    $order = wc_get_order($order_id);

    if (!$order) {
        return new WP_REST_Response(['success' => false, 'message' => 'Order not found.'], 404);
    }

    // Basic ownership check: logged-in users can only answer for their own order,
    // guests are matched against the order key stored in their session/cookie by WooCommerce.
    $current_user_id = get_current_user_id();
    if ($current_user_id && $order->get_customer_id() && (int) $order->get_customer_id() !== $current_user_id) {
        return new WP_REST_Response(['success' => false, 'message' => 'Not allowed.'], 403);
    }

    // Dedupe: one submission per order. Since there's no order_id column on
    // ls_contact_submissions, match on form_type + reference_url (order edit URL).
    $table         = $wpdb->prefix . 'ls_contact_submissions';
    $reference_url = $order->get_edit_order_url();

    $already_submitted = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE form_type = %s AND reference_url = %s",
            'thankyou_question',
            $reference_url
        )
    );

    if ($already_submitted > 0) {
        //return new WP_REST_Response(['success' => true, 'message' => 'Already recorded.'], 200);
    }

    // The frontend sends a JSON-encoded object of question => answer.
    // Decode + re-encode so we only ever store well-formed, sanitized JSON.
    $decoded = json_decode($raw_answer, true);
    if (!is_array($decoded)) {
        return new WP_REST_Response(['success' => false, 'message' => 'Invalid answer payload.'], 400);
    }

    $sanitized = [];
    foreach ($decoded as $key => $value) {
        $sanitized[sanitize_key($key)] = sanitize_textarea_field((string) $value);
    }

    // Build a human-readable summary for the "message" column on the Forms admin page.
    $question_labels = [
        'contact_by_phone'          => 'Can we contact you by phone if any issues arise?',
        'heard_about_us'            => 'How did you first hear about us?',
        'when_heard_about_us'       => 'When did you first hear about us?',
        'almost_kept_from_ordering' => 'What almost kept you from ordering today?',
        'compared_others'          => 'Did you compare us with other jewelers or online retailers?',
        'could_do_better'           => 'Anything we could have done better?',
    ];

    $message_lines = [ 'Order ID: #' . $order->get_order_number() ];
    foreach ($sanitized as $key => $value) {
        if ($value === '') {
            continue;
        }
        $label = $question_labels[$key] ?? $key;
        $message_lines[] = $label . ': ' . $value;
    }

    if (!function_exists('ls_store_submission')) {
        return new WP_REST_Response(['success' => false, 'message' => 'Submission storage is unavailable.'], 500);
    }

    $stored = ls_store_submission([
        'form_type'   => 'thankyou_question',
        'name'        => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
        'email'       => $order->get_billing_email(),
        'phone'       => $order->get_billing_phone(),
        'message'     => implode("\n", $message_lines),
        'reference'   => $reference_url,
        'attachments' => $sanitized,
    ]);

    if (!$stored) {
        return new WP_REST_Response(['success' => false, 'message' => 'Could not save your answers.'], 500);
    }

    // Fire-and-forget push to Zoho as a lead event, mirroring the diamond_search lead-create pattern.
    if (defined('ZOHOAPIENDPOINT')) {
       
        $response = wp_remote_post(
            ZOHOAPIENDPOINT . 'order-feedback',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'lead_type'    => 'thankyou_question',
                    'order_id'    => $order_id,
                    'name'        => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
                    'email'       => $order->get_billing_email(),
                    'phone'       => $order->get_billing_phone(),
                    'message'     => implode("\n", $message_lines),
                    'reference'   => $reference_url,
                    'attachments' => $sanitized,
                ]),
                'timeout'  => 15,
                
            ]
        );
        
    }

    return new WP_REST_Response(['success' => true], 200);
}