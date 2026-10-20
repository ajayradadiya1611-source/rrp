<?php

/**
 * Verify a Google reCAPTCHA v3 token server-side.
 * Returns true when valid, action matches and score >= LD_RECAPTCHA_MIN_SCORE.
 */
function ls_verify_recaptcha_v3($token, $expected_action = '')
{
    if (empty($token) || !defined('LD_RECAPTCHA_SECRET_KEY')) {
        return false;
    }

    $resp = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'timeout' => 10,
        'body'    => [
            'secret'   => LD_RECAPTCHA_SECRET_KEY,
            'response' => $token,
            'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
        ],
    ]);

    if (is_wp_error($resp)) {
        return false;
    }

    $data = json_decode(wp_remote_retrieve_body($resp), true);
    if (empty($data['success'])) {
        return false;
    }

    if ($expected_action !== '' && (!isset($data['action']) || $data['action'] !== $expected_action)) {
        return false;
    }

    $min = defined('LD_RECAPTCHA_MIN_SCORE') ? (float) LD_RECAPTCHA_MIN_SCORE : 0.5;
    return isset($data['score']) && (float) $data['score'] >= $min;
}


add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/contact-us', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_contact_us',
        'permission_callback' => '__return_true', 
    ]);
});

function ls_rest_contact_us(WP_REST_Request $request)
{
    

    $name = sanitize_text_field($request->get_param('name'));
    $email = sanitize_email($request->get_param('email'));
    $message = sanitize_textarea_field($request->get_param('message'));
    $number = sanitize_text_field($request->get_param('number'));

    // Contact Us page = 'contact', the Schedule Now popup = 'consultation'
    $form_type = sanitize_key($request->get_param('form_type'));
    if (!in_array($form_type, ['contact', 'consultation'], true)) {
        $form_type = 'contact';
    }

    if (empty($name) || empty($email) || empty($message)) {
        return new WP_REST_Response(['error' => 'Name, Email and Message are required.'], 400);
    }

    $to = get_option('admin_email');
    $subject = 'Thank You for Contacting Us | RRP Diamond ';
    ob_start();

    
        $html_content = '
        Hello ' . esc_html($name) . ',<br><br>
        Thank you for reaching out to RRP Diamond. We&rsquo;re happy to have received your inquiry. One of our customer support representatives will get back to you within 12&ndash;24 business hours.<br><br>
        If you didn&rsquo;t hear back, please check your spam or junk folder.<br><br>
        If you have any further questions or need immediate assistance, please feel free to contact us via WhatsApp:<br>
        <a href="https://wa.me/919016106290">+91 9016106290</a><br><br>
        Best regards,<br>
        Team RRP Diamond<br>';
    
    
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'New Contact Message',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

     $body = ob_get_clean();
     ob_start();
    $html_content2 = '';
    $html_content2 .= '<strong>Submission details:</strong><br><br>';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html($name) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html($email) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Phone</td><td style="padding:8px;">' . esc_html($number) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Message</td><td style="padding:8px;">' . nl2br(esc_html($message)) . '</td></tr>';
    $html_content2 .= '</table><br>';
    $html_content2 .= 'Regards,<br>'.SITE_OC_NAME;
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'New Contact Message',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

     $body2 = ob_get_clean();
     $headers = ['Content-Type: text/html; charset=UTF-8'];
    // Handle file attachments (optional) -- upload and store file paths (no media library)
    $attachment_ids = [];
    $mail_attachments = [];

    if ( ! empty( $_FILES['files'] ) ) {
        $files = $_FILES['files'];

        // Max 3 files
        if ( count( $files['name'] ) > 3 ) {
            return new WP_REST_Response( ['error' => 'Maximum 3 photos allowed (less than 2MB each).'], 400 );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        foreach ( $files['name'] as $key => $fn ) {
            $file_size = $files['size'][ $key ];

            // 2MB size check
            if ( $file_size > 2 * 1024 * 1024 ) {
                return new WP_REST_Response( ['error' => 'Each file must be less than 2MB.'], 400 );
            }

            $tmp_name = $files['tmp_name'][ $key ];
            $filename = sanitize_file_name( $files['name'][ $key ] );

            if ( ! is_uploaded_file( $tmp_name ) ) {
                continue;
            }

            $contents = file_get_contents( $tmp_name );
            if ( $contents === false ) continue;

            $upload = wp_upload_bits( $filename, null, $contents );
            if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
                $file_path = $upload['file'];
                $mail_attachments[] = $file_path;
                $attachment_ids[] = $file_path;
            }
        }
    
        }
     
    // Use mail_attachments (file paths) for sending; store attachment IDs (or paths) in DB
    $response = wp_remote_post(
        ZOHOAPIENDPOINT.'lead-create',
        [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'lead_type'         => 'contact',
                'name'         => $name,
                'email'        => $email,
                'message'      => $message,
                'mobile'       => $number,
                'country_code' => '91', // or dynamic
            ]),
            'timeout' => 15,
        ]
    );   


    if ( wp_mail( $to, $subject, $body2, $headers, $mail_attachments ) ) {
        // store submission (serialize attachments)
        ls_store_submission( [
            'form_type'   => $form_type,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $number,
            'message'     => $message,
            'reference'   => '',
            'attachments' => $attachment_ids,
        ] );

        // send confirmation to user (include attachments if desired)
        wp_mail( $email, $subject, $body, $headers, $mail_attachments );
        return new WP_REST_Response( ['success' => 'Your message has been sent.'], 200 );
    } else {
        return new WP_REST_Response( ['error' => 'Failed to send your message. Please try again later.'], 500 );
    }
}




add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/ask-us', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_ask_us',
        'permission_callback' => '__return_true', 
    ]);
});

function ls_rest_ask_us(WP_REST_Request $request)
{
    

    $to = get_option('admin_email');
    $subject ='We’ve Received Your Diamond Search Request | RRP Diamond"';
    $name = sanitize_text_field($request->get_param('name'));
    $email = sanitize_email($request->get_param('email'));
    $message = sanitize_textarea_field($request->get_param('message'));
    $phone = sanitize_text_field($request->get_param('phone'));
    $refurl = sanitize_text_field($request->get_param('reference_url'));

    // Which form sent this (stored as the submission's form type)
    $form_type = sanitize_key($request->get_param('form_type'));
    if (!in_array($form_type, ['ask-us', 'ring-quote'], true)) {
        $form_type = 'ask-us';
    }

    if (empty($name) || empty($email) || empty($phone)) {
        return new WP_REST_Response(['error' => '* All Fields are required'], 400);
    }

    ob_start();
    
        $html_content = '
        Hello ' . esc_html($name) . ',<br><br>
        Thank you for reaching out to RRP Diamond. We&rsquo;re happy to have received your inquiry. One of our customer support representatives will get back to you within 12&ndash;24 business hours.<br><br>
        If you didn&rsquo;t hear back, please check your spam or junk folder.<br><br>
        If you have any further questions or need immediate assistance, please feel free to contact us via WhatsApp:<br>
        <a href="https://wa.me/919016106290">+91 9016106290</a><br><br>
        Best regards,<br>
        Team RRP Diamond<br>';
    
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'Custom Setting Request',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body = ob_get_clean();
    ob_start();
    $html_content2 = '';
    $html_content2 .= '<strong>Submission details:</strong><br><br>';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html( $name ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html( $email ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Phone</td><td style="padding:8px;">' . esc_html( $phone ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Reference URL</td><td style="padding:8px;">' . esc_html( $refurl ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Message</td><td style="padding:8px;">' . nl2br( esc_html( $message ) ) . '</td></tr>';
    $html_content2 .= '</table><br>';
    $html_content2 .= 'Regards,<br>'.SITE_OC_NAME;
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'Custom Setting Request',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body2 = ob_get_clean();
    $headers = ['Content-Type: text/html; charset=UTF-8'];


    $response = wp_remote_post(
        ZOHOAPIENDPOINT.'lead-create',
        [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'lead_type'         => 'diamond_search',
                'message'      => $message,
                'name'         => $name,
                'email'        => $email,
                'mobile'       => $phone,
                'reference'       => $refurl,
                'country_code' => '91', // or dynamic
            ]),
            'timeout' => 15,
        ]
    );   

    if ( wp_mail( $to, $subject, $body2, $headers ) ) {
        ls_store_submission([
            'form_type' => $form_type,
            'name'      => $name,
            'email'     => $email,
            'phone'     => $phone,
            'message'   => $message,
            'reference' => $refurl,
            'attachments'=> $attachment_ids
        ]);
        wp_mail( $email, $subject, $body, $headers );
        return new WP_REST_Response(['success' => 'A diamond specialist is reviewing your request. Need faster assistance? You can call, WhatsApp, or text us directly.'], 200);
    } else {
        return new WP_REST_Response(['error' => 'Failed to send your message. Please try again later.'], 500);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/custom-setting-request', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_custom_setting_request',
        'permission_callback' => '__return_true', 
    ]);
});
function ls_rest_custom_setting_request(WP_REST_Request $request)
{
    

    $firstName = sanitize_text_field($request->get_param('firstName'));
    $lastName  = sanitize_text_field($request->get_param('lastName'));
    $phone     = sanitize_text_field($request->get_param('phoneNumber'));
    $email     = sanitize_email($request->get_param('email'));
    $refurl    = esc_url_raw($request->get_param('refSite'));
    $message   = sanitize_textarea_field($request->get_param('message'));

    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
        return new WP_REST_Response(['error' => '* All Fields are required'], 400);
    }

    $attachment_paths = [];
    $attachment_urls  = [];

    if (!empty($_FILES['files'])) {

        $files = $_FILES['files'];

        // Max 3 files
        if (count($files['name']) > 3) {
            return new WP_REST_Response(
                ['error' => 'Maximum 3 photos allowed (less than 2MB each).'],
                400
            );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        foreach ($files['name'] as $key => $name) {

            $file_size = $files['size'][$key];

            // 2MB size check
            if ($file_size > 2 * 1024 * 1024) {
                return new WP_REST_Response(
                    ['error' => 'Each file must be less than 2MB.'],
                    400
                );
            }

            $tmp_name = $files['tmp_name'][$key];
            $filename = sanitize_file_name($files['name'][$key]);

            if (!is_uploaded_file($tmp_name)) {
                continue;
            }

            $contents = file_get_contents($tmp_name);

            if ($contents === false) {
                continue;
            }

            $upload = wp_upload_bits($filename, null, $contents);

            if (empty($upload['error']) && !empty($upload['file'])) {

                // Server file path
                $attachment_paths[] = $upload['file'];

                // Public URL
                $attachment_urls[] = $upload['url'];
            }
        }
    }
    //print_r( $_FILES);
    //print_r( $attachments);

    $to = get_option('admin_email');
    $subject = 'Thank You for Your Custom Setting Inquiry | RRP Diamond';


    ob_start();
    
        $html_content = '
        Hello ' . esc_html($firstName) . ',<br><br>
        Thank you for submitting your Custom Setting Request. We&rsquo;re delighted to help you create a setting tailored to your vision. One of our jewelry specialists will contact you within 12&ndash;24 business hours to discuss your requirements and assist you with the next steps.<br><br>
        If you didn&rsquo;t hear back, please check your spam or junk folder.<br><br>
        If you have any further questions or need immediate assistance, please feel free to contact us via WhatsApp:<br>
        <a href="https://wa.me/919016106290">+91 9016106290</a><br><br>
        Best regards,<br>
        Team RRP Diamond<br>';
    
    
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $firstName,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'Custom Setting Request',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

     $body = ob_get_clean();
      ob_start();
    $html_content2 = '';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html( $firstName.' '.$lastName ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html( $email ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Phone</td><td style="padding:8px;">' . esc_html( $phone ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Reference URL</td><td style="padding:8px;">' . esc_html( $refurl ) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Message</td><td style="padding:8px;">' . nl2br( esc_html( $message ) ) . '</td></tr>';
    $html_content2 .= '</table><br>';
    $html_content2 .= 'Regards,<br>'.SITE_OC_NAME;
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'Custom Setting Request',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

     $body2 = ob_get_clean();
     $headers = ['Content-Type: text/html; charset=UTF-8'];

     $response = wp_remote_post(
        ZOHOAPIENDPOINT.'lead-create',
        [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'lead_type' => 'custom_setting',
                'description'   => $message,
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'email'         => $email,
                'reference_url' => $refurl,
                'size'          => '',
                'metal'         => '',
                'sku'           => '',
                'mobile'        => $phone,
                'images'        => $attachment_urls, // or actual image URLs
                'country_code'  => 91,
               
            ]),
            'timeout' => 15,
        ]
    );
     /*if (is_wp_error($response)) {

        echo $response->get_error_message();

    } else {

        echo '<pre>';
        print_r($response);

        echo wp_remote_retrieve_body($response);
        echo '</pre>';
    }
     exit;*/

    if (wp_mail($to, $subject, $body2, $headers, $attachment_paths)) {
        // store submission
        ls_store_submission([
            'form_type' => 'custom-setting-request',
            'name'      => $firstName . ' ' . $lastName,
            'email'     => $email,
            'phone'     => $phone,
            'message'   => $message,
            'reference' => $refurl,
            'attachments'=> $attachment_paths
        ]);
        wp_mail($email, $subject, $body, $headers, $attachment_paths);
        return new WP_REST_Response(['success' => 'A jewelry specialist is reviewing your request. Need faster assistance? You can call, WhatsApp, or text us directly.'], 200);
    } else {
        return new WP_REST_Response(['error' => 'Failed to send your message. Please try again later.'], 500);
    }
}


add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/feedback', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_feedback',
        'permission_callback' => '__return_true', 
    ]);
});

function ls_rest_feedback(WP_REST_Request $request)
{
    $name = sanitize_text_field($request->get_param('name'));
    $email = sanitize_email($request->get_param('email'));
    $message = sanitize_textarea_field($request->get_param('message'));

    if (!ls_verify_recaptcha_v3($request->get_param('recaptcha_token'), 'feedback_form')) {
        return new WP_REST_Response(['error' => 'reCAPTCHA verification failed. Please refresh the page and try again.'], 403);
    }

    if (empty($name) || empty($email) || empty($message)) {
        return new WP_REST_Response(['error' => 'All fields are required.'], 400);
    }

    $to = get_option('admin_email');
    $subject = 'Thank You for Your Feedback | RRP Diamond' ;
    ob_start();
    
        $html_content = '
        Hi ' . esc_html($name) . ',<br><br>
        Thank you for sharing your feedback with us. We truly appreciate you taking the time to help us improve your experience with RRP Diamond.<br><br>
        Our team will review your feedback and get back to you shortly if a response is required.<br><br>
        If you have any further questions or need immediate assistance, please feel free to contact us via WhatsApp:<br>
        <a href="https://wa.me/919016106290">+91 9016106290</a><br><br>
        Best regards,<br>
        Team RRP Diamond<br>';
    
    
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'New Feedback Message',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body = ob_get_clean();
     ob_start();
    $html_content2 = '';
    $html_content2 .= '<strong>Submission details:</strong><br><br>';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html($name) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html($email) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Message</td><td style="padding:8px;">' . nl2br(esc_html($message)) . '</td></tr>';
    $html_content2 .= '</table><br>';
    $html_content2 .= 'Regards,<br>'.SITE_OC_NAME;
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'New Feedback Message',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body2 = ob_get_clean();
     $headers = ['Content-Type: text/html; charset=UTF-8'];

     $response = wp_remote_post(
        ZOHOAPIENDPOINT.'lead-create',
        [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'lead_type' => 'feedback',
                'fname'     => $name,   // map from your form
                'email'     => $email,
                'message'   => $message,
                'store'     => '',
                'website'   => '',
                'phone'     => '',
                'country'   => '',
                'city'      => '',
                'form_name' => 'contact',
                'edate'     => current_time('Y-m-d H:i:s'),
            ]),
            'timeout' => 15,
        ]
    ); 
    if (wp_mail($to, $subject, $body2, $headers)) {
        // store submission
        ls_store_submission([
            'form_type' => 'feedback',
            'name'      => $name,
            'email'     => $email,
            'phone'     => '',
            'message'   => $message,
            'reference' => '',
            'attachments'=> ''
        ]);
        wp_mail($email, $subject, $body, $headers);
        return new WP_REST_Response(['success' => 'Your message has been sent.'], 200);
    } else {
        return new WP_REST_Response(['error' => 'Failed to send your message. Please try again later.'], 500);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/wholesale', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_wholesale',
        'permission_callback' => '__return_true',
    ]);
});

function ls_rest_wholesale(WP_REST_Request $request)
{
    $name    = sanitize_text_field($request->get_param('name'));
    $email   = sanitize_email($request->get_param('email'));
    $store   = sanitize_text_field($request->get_param('store'));
    $website_raw = trim((string) $request->get_param('website'));
    $website = esc_url_raw(preg_match('#^https?://#i', $website_raw) ? $website_raw : 'https://' . $website_raw);
    if ($website_raw === '') { $website = ''; }
    $number  = sanitize_text_field($request->get_param('number'));
    $country = sanitize_text_field($request->get_param('country'));
    $city    = sanitize_text_field($request->get_param('city'));
    $source  = sanitize_text_field($request->get_param('source'));

    if (empty($name) || empty($email) || empty($store) || empty($website) || empty($number) || empty($country) || empty($city)) {
        return new WP_REST_Response(['error' => 'All fields are required.'], 400);
    }
    if (!is_email($email)) {
        return new WP_REST_Response(['error' => 'Please enter a valid email address.'], 400);
    }

    // Everything beyond name/email/phone is kept in the message column
    $details = [
        'Store Name' => $store,
        'Website'    => $website,
        'Country'    => $country,
        'City'       => $city,
        'Source'     => $source,
    ];
    $message = '';
    foreach ($details as $label => $value) {
        if ($value !== '') {
            $message .= $label . ': ' . $value . "\n";
        }
    }
    $message = trim($message);

    $to      = get_option('admin_email');
    $subject = 'Thank You for Your Wholesale Inquiry | RRP Diamond';
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    ob_start();
    $html_content = '
        Hello ' . esc_html($name) . ',<br><br>
        Thank you for your wholesale inquiry. Our team will review your details and get back to you within 12&ndash;24 business hours.<br><br>
        If you didn&rsquo;t hear back, please check your spam or junk folder.<br><br>
        Best regards,<br>
        Team RRP Diamond<br>';
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        [
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'Wholesale Inquiry',
        ],
        '',
        get_stylesheet_directory() . '/'
    );
    $body = ob_get_clean();

    ob_start();
    $html_content2  = '<strong>Submission details:</strong><br><br>';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html($name) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html($email) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Phone</td><td style="padding:8px;">' . esc_html($number) . '</td></tr>';
    foreach ($details as $label => $value) {
        if ($value !== '') {
            $html_content2 .= '<tr><td style="padding:8px;">' . esc_html($label) . '</td><td style="padding:8px;">' . esc_html($value) . '</td></tr>';
        }
    }
    $html_content2 .= '</table><br>Regards,<br>' . SITE_OC_NAME;
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        [
            'uname'         => $name,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'New Wholesale Inquiry',
        ],
        '',
        get_stylesheet_directory() . '/'
    );
    $body2 = ob_get_clean();

    // Store first so the lead is never lost if mail fails
    ls_store_submission([
        'form_type'   => 'wholesale',
        'name'        => $name,
        'email'       => $email,
        'phone'       => $number,
        'message'     => $message,
        'reference'   => '',
        'attachments' => '',
    ]);

    wp_remote_post(
        ZOHOAPIENDPOINT . 'lead-create',
        [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'lead_type' => 'wholesale',
                'fname'     => $name,
                'email'     => $email,
                'message'   => $message,
                'store'     => $store,
                'website'   => $website,
                'phone'     => $number,
                'country'   => $country,
                'city'      => $city,
                'form_name' => 'wholesale',
                'edate'     => current_time('Y-m-d H:i:s'),
            ]),
            'timeout' => 15,
        ]
    );

    wp_mail($to, $subject, $body2, $headers);
    wp_mail($email, $subject, $body, $headers);

    return new WP_REST_Response(['success' => 'Thank you! Your wholesale inquiry has been received.'], 200);
}
