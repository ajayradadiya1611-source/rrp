<?php

/**
 * AJAX registration: enqueue script and handler
 */
// Note: AJAX registration JS is now inlined in page-wholesale-register.php to avoid a separate file.

add_action('wp_ajax_nopriv_ld_ajax_register', 'ld_ajax_register_handler');
add_action('wp_ajax_ld_ajax_register', 'ld_ajax_register_handler');

/**
 * Verify a Google reCAPTCHA v3 token server-side.
 *
 * Define these in wp-config.php (or another early-loaded config file):
 *   define('LD_RECAPTCHA_SITE_KEY', 'xxxxx');
 *   define('LD_RECAPTCHA_SECRET_KEY', 'xxxxx');
 * Optionally:
 *   define('LD_RECAPTCHA_MIN_SCORE', 0.5); // 0.0 (bot) - 1.0 (human), default 0.5
 *
 * If LD_RECAPTCHA_SECRET_KEY isn't defined, verification is skipped (fails open)
 * so the site keeps working before reCAPTCHA is configured. Flip
 * LD_RECAPTCHA_REQUIRED to true once keys are set if you want it to fail closed instead.
 *
 * @param string $token  The g-recaptcha-response / execute() token from the client.
 * @param string $expected_action The action name passed to grecaptcha.execute().
 * @return true|WP_Error
 */
function ld_verify_recaptcha_v3($token, $expected_action = 'wholesale_register') {
    if ( ! defined('LD_RECAPTCHA_SECRET_KEY') || ! LD_RECAPTCHA_SECRET_KEY ) {
        // Not configured yet — don't block registrations.
        return true;
    }

    if ( empty($token) ) {
        return new WP_Error('recaptcha_missing', 'Verification failed. Please refresh the page and try again.');
    }

    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
        'timeout' => 10,
        'body'    => array(
            'secret'   => LD_RECAPTCHA_SECRET_KEY,
            'response' => $token,
            'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
        ),
    ));

    if ( is_wp_error($response) ) {
        // Network/API issue talking to Google — don't hard-block genuine users.
        error_log('reCAPTCHA verify request failed: ' . $response->get_error_message());
        return true;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ( empty($body['success']) ) {
        return new WP_Error('recaptcha_failed', 'Verification failed. Please try again.');
    }

    $min_score = defined('LD_RECAPTCHA_MIN_SCORE') ? floatval(LD_RECAPTCHA_MIN_SCORE) : 0.5;
    if ( isset($body['score']) && $body['score'] < $min_score ) {
        return new WP_Error('recaptcha_low_score', 'We couldn\'t verify you\'re not a robot. Please try again.');
    }

    if ( ! empty($body['action']) && $expected_action && $body['action'] !== $expected_action ) {
        return new WP_Error('recaptcha_action_mismatch', 'Verification failed. Please refresh the page and try again.');
    }

    return true;
}

function ld_ajax_register_handler() {
    global $oc_common;
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'ajax-register-nonce') ) {
        wp_send_json_error(array('messages' => array('Security check failed')));
    }

    // Verify Google reCAPTCHA v3 token before doing anything else
    $recaptcha_check = ld_verify_recaptcha_v3(
        isset($_POST['recaptcha_token']) ? wp_unslash($_POST['recaptcha_token']) : '',
        isset($_POST['recaptcha_action']) ? sanitize_text_field(wp_unslash($_POST['recaptcha_action'])) : 'wholesale_register'
    );
    if ( is_wp_error($recaptcha_check) ) {
        wp_send_json_error(array('messages' => array($recaptcha_check->get_error_message())));
    }

    // Treat posted 'fullname' as the user's full name / display name
    $name_raw  = isset($_POST['fullname']) ? wp_unslash($_POST['fullname']) : '';
    $name      = sanitize_text_field($name_raw);
    $email     = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $website   = isset($_POST['website']) ? sanitize_text_field(wp_unslash($_POST['website'])) : '';
    $phone     = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $password  = isset($_POST['password']) ? $_POST['password'] : '';
    $country_code  = isset($_POST['country_code']) ? $_POST['country_code'] : '';
    $source    = isset($_POST['source']) ? sanitize_text_field(wp_unslash($_POST['source'])) : '';

    $errors = array();

    if (empty($name) || mb_strlen($name) < 4) {
        $errors[] = 'Name must be at least 4 characters.';
    }
    if (!is_email($email)) {
        $errors[] = 'Invalid email address.';
    }
    if (email_exists($email)) {
        $errors[] = 'Email already registered.';
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!empty($errors)) {
        wp_send_json_error(array('messages' => $errors));
    }

    // Use the email as the user's login identifier (attempt to preserve readable form)
    if (is_email($email)) {
        // sanitize_user with $strict = false to keep more characters
        $login_candidate = sanitize_user($email, false);
    } else {
        $login_candidate = sanitize_user(strtolower(preg_replace('/\s+/', '', $name)));
    }
    if (empty($login_candidate)) {
        $login_candidate = 'user' . time();
    }
    $base = $login_candidate;
    $i = 1;
    while (username_exists($login_candidate)) {
        $login_candidate = $base . $i;
        $i++;
    }

    $user_id = wp_create_user($login_candidate, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('messages' => array($user_id->get_error_message())));
    }

    $user = new WP_User($user_id);
    $user->set_role('customer');

    // Set display name and optional name parts
    wp_update_user(array('ID' => $user_id, 'display_name' => $name));
    if (strpos($name, ' ') !== false) {
        $parts = preg_split('/\s+/', $name, 2);
        update_user_meta($user_id, 'first_name', $parts[0]);
        update_user_meta($user_id, 'last_name', isset($parts[1]) ? $parts[1] : '');
    } else {
        update_user_meta($user_id, 'first_name', $name);
    }

    // Save website/social link if provided
    if (!empty($website)) {
        update_user_meta($user_id, 'website', $website);
    }
    if (!empty($phone)) {
        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, 'shipping_phone', $phone);
    }
    if (!empty($country_code)) {
        update_user_meta($user_id, 'billing_country', $country_code);
        update_user_meta($user_id, 'shipping_country', $country_code);
    }

    // Mark user as awaiting admin approval for wholesale access
    update_user_meta($user_id, 'wholesale_approved', 0);
    update_user_meta($user_id, 'wholesale_requested_at', current_time('mysql'));

    // Save where the registration came from (utm_source / referrer domain / Direct)
    update_user_meta($user_id, 'registration_source', $source ?: 'Direct');

    // Optional: notify admin (uses site admin email)
    $admin_email = $oc_common['notification_email'];
    $subject = 'New wholesale registration request';
    $body = sprintf("New wholesale registration request:\n\nName: %s\nUsername(login): %s\nEmail: %s\nPhone: %s\nWebsite: %s\nSource: %s\nApprove at: %s/wp-admin/user-edit.php?user_id=%d", esc_html($name), esc_html($login_candidate), esc_html($email), esc_html($phone), esc_html($website), esc_html($source ?: 'Direct'), site_url(), intval($user_id));
    wp_mail($admin_email, $subject, $body, $headers);

    ob_start();
    $html_content = '';
    $html_content .= 'Hello '.$name_raw.' <br/> Thank you for joining the Loose Grown Diamond Wholesale Program. Your account has been created and is undergoing verification. We will notify you within 48 working hours once activated. You can then access and modify your account details and start placing orders.';
    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'html_content'  => $html_content,
            'email_heading' => 'Thank you for joining the Loose Grown Diamond Wholesale Program.',  // <-- dynamic heading
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body = ob_get_clean();
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, ' Thank you for joining the Loose Grown Diamond Wholesale Program.', $body, $headers);
    wp_send_json_success(array('message' => 'Registration received. Your account will be activated after admin approval.'));
    wp_die();
}


/* AJAX login for wholesale users (requires admin approval) */
add_action('wp_ajax_nopriv_ld_ajax_login', 'ld_ajax_login_handler');
add_action('wp_ajax_ld_ajax_login', 'ld_ajax_login_handler');

function ld_ajax_login_handler() {
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'ajax-login-nonce') ) {
        wp_send_json_error(array('messages' => array('Security check failed')));
    }

    $user_input = isset($_POST['user']) ? wp_unslash($_POST['user']) : ''; // email or username
    $password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';

    if (empty($user_input) || empty($password)) {
        wp_send_json_error(array('messages' => array('Please provide both email and password.')));
    }

    // Try to find user by email first, then by login
    if (is_email($user_input)) {
        $user = get_user_by('email', $user_input);
    } else {
        $user = get_user_by('login', $user_input);
    }

    if (!$user) {
        wp_send_json_error(array('messages' => array('Invalid credentials.')));
    }

    $user_id = $user->ID;

    // Check wholesale approval meta
    $approved = get_user_meta($user_id, 'wholesale_approved', true);
    if ($approved != '1' && $approved !== 1) {
        
        wp_send_json_error(array('messages' => array('Your account is pending admin approval.')));
    }

    // Attempt signon
    $creds = array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => true,
    );
    $user_signon = wp_signon($creds, is_ssl());

    if (is_wp_error($user_signon)) {
        $error_code = $user_signon->get_error_code();

        // Get WooCommerce lost password URL
        $lost_password_url = wc_lostpassword_url();

        if ($error_code === 'incorrect_password') {
            $message = 'Incorrect password. Please try again or <a href="' . esc_url($lost_password_url) . '">Forgot Password?</a>';
        } else {
            $message = $user_signon->get_error_message();
        }

        wp_send_json_error(array(
            'messages' => array($message)
        ));
    }
    wp_send_json_success(array('message' => 'Login successful. Redirecting...', 'redirect' => site_url()));
        // Validate phone: require at least 6 digits
        $phone_digits = preg_replace('/\D+/', '', $phone);
        if (empty($phone_digits) || strlen($phone_digits) < 6) {
            $errors[] = 'Please enter a valid phone number.';
        }
}

/**
 * Admin UI: add Wholesale Approved column and actions
 */
add_filter('manage_users_columns', 'ld_add_wholesale_column');
function ld_add_wholesale_column($columns) {
    $columns['wholesale_approved'] = 'Wholesale Approved';
    $columns['phone']   = 'Phone';
    $columns['website'] = 'Website';
    $columns['registration_source'] = 'Source';
    $columns['country'] = 'Country';
    return $columns;
}

add_action('manage_users_custom_column', 'ld_render_wholesale_column', 10, 3);
function ld_render_wholesale_column($value, $column_name, $user_id) {
    //if ($column_name !== 'wholesale_approved') return $value;
    $approved = get_user_meta($user_id, 'wholesale_approved', true);
    $label = ($approved == '1' || $approved === 1) ? '<span style="color:green;font-weight:600">Approved</span>' : '<span style="color:#d00">Pending</span>';

    // row action link to toggle
    if (current_user_can('edit_users') && 'wholesale_approved' === $column_name) {
        $action = ($approved == '1' || $approved === 1) ? 'revoke' : 'approve';
        $nonce = wp_create_nonce('ld_toggle_wholesale_' . $user_id);
        $url = admin_url('admin-post.php?action=ld_toggle_wholesale_approval&user_id=' . $user_id . '&do=' . $action . '&_wpnonce=' . $nonce);
        $label .= ' <a href="' . esc_url($url) . '" style="margin-left:8px">' . ucfirst($action) . '</a>';
        return $label;
    }
    if ( 'phone' === $column_name ) {
        return esc_html( get_user_meta( $user_id, 'phone', true ) );
    }

    if ( 'website' === $column_name ) {
        $website = get_user_meta( $user_id, 'website', true );
        if ( $website ) {
            return '<a href="' . esc_url( $website ) . '" target="_blank">' . esc_html( $website ) . '</a>';
        }
    }
    if ( 'registration_source' === $column_name ) {
        $source = get_user_meta( $user_id, 'registration_source', true );
        return $source ? esc_html( $source ) : '—';
    }

    if ( 'country' === $column_name ) {
        return esc_html( get_user_meta( $user_id, 'shipping_country', true ) );
    }

    return $value;
}

/**
 * Handle admin toggle via admin-post (redirects back)
 */
add_action('admin_post_ld_toggle_wholesale_approval', 'ld_handle_toggle_wholesale_approval');
function ld_handle_toggle_wholesale_approval() {
    if (! current_user_can('edit_users')) {
        wp_die('Unauthorized', 'Error', array('response' => 403));
    }

    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $do = isset($_GET['do']) ? sanitize_text_field($_GET['do']) : '';
    $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

    if (! $user_id || ! wp_verify_nonce($nonce, 'ld_toggle_wholesale_' . $user_id)) {
        wp_die('Invalid request', 'Error', array('response' => 400));
    }

    if ($do === 'approve') {
        update_user_meta($user_id, 'wholesale_approved', 1);

        // Send approval email to user
        $user = get_userdata($user_id);
        if ($user && ! empty($user->user_email)) {
            $first_name = get_user_meta($user_id, 'first_name', true);
            $display_name = $user->display_name ?: $user->user_login;
            $greeting = $first_name ? $first_name : $display_name;

            $to = $user->user_email;
            $subject = 'Your account has been approved - LGD Wholesale';
            
             ob_start();
             $html_content = "Hi " . $greeting . ",<br><br>";
            $html_content .= "I'm Victor, your manager at Loose Grown Diamond Wholesale. Your account is now approved! Please log in at <a href='" . site_url('/login/') . "'>". site_url()."</a><br>";
            $html_content .= "I'm here to help with any orders or questions. Let's get started on your first order!<br><br>";
            $html_content .= "- Victor";
             wc_get_template(
                'inc/email-templates/forms/customer-send.php',
                array(
                    'html_content'  => $html_content,
                    'email_heading' => 'Thank you for joining the Loose Grown Diamond Wholesale Program.',  // <-- dynamic heading
                ),
                '',
                get_stylesheet_directory() . '/'
            );

            $body = ob_get_clean();
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            wp_mail($to, $subject, $body, $headers);

            // Send notification to admin as well
            $admin_email = get_option('admin_email');
            $admin_subject = 'Wholesale account approved: ' . $user->user_email;
            $admin_body = "A wholesale account has just been approved.<br><br>";
            $admin_body .= "Name: " . esc_html($display_name) . "<br>";
            $admin_body .= "Email: " . esc_html($user->user_email) . "<br>";
            $admin_body .= "User ID: " . esc_html($user_id) . "<br>";
            wp_mail($admin_email, $admin_subject, $admin_body, $headers);
        }
    } else {
        update_user_meta($user_id, 'wholesale_approved', 0);
    }

    // redirect back to users list
    $redirect = wp_get_referer() ? wp_get_referer() : admin_url('users.php');
    wp_safe_redirect($redirect);
    exit;
}

/**
 * Show wholesale approval field on user profile and allow editing
 */
add_action('show_user_profile', 'ld_show_wholesale_profile_field');
add_action('edit_user_profile', 'ld_show_wholesale_profile_field');
function ld_show_wholesale_profile_field($user) {
    if (! current_user_can('edit_users')) return;
    $approved = get_user_meta($user->ID, 'wholesale_approved', true);
    ?>
    <h2>Wholesale Access</h2>
    <table class="form-table">
        <tr>
            <th><label for="wholesale_approved">Approved</label></th>
            <td>
                <input type="checkbox" name="wholesale_approved" id="wholesale_approved" value="1" <?php checked($approved, '1'); ?> />
                <span class="description">Check to allow wholesale login access.</span>
            </td>
        </tr>
    </table>
    <?php
}

/*add_action('personal_options_update', 'ld_save_wholesale_profile_field');
add_action('edit_user_profile_update', 'ld_save_wholesale_profile_field');
function ld_save_wholesale_profile_field($user_id) {
    if (! current_user_can('edit_user', $user_id)) return false;
    $approved = isset($_POST['wholesale_approved']) ? 1 : 0;
    update_user_meta($user_id, 'wholesale_approved', $approved);
}*/

function custom_auth_redirect() {
    if ( ! is_user_logged_in() ) {

        // Allow these pages
        if (
            ! is_page( array(
                'register',
                'login',
                'forget-password',
                'reset-password',
                'thank-you-register',
                'privacy-policy-2',
            ) )
            && ! is_account_page()
            && ! is_wc_endpoint_url( 'lost-password' )
        ) {
          /*  global $post;
        echo $post->post_name ;
        exit;*/
            wp_redirect( home_url( '/register' ) );
            exit;
        }
    }
}
add_action( 'template_redirect', 'custom_auth_redirect' );
function my_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', current_time('mysql') );
}
add_action( 'wp_login', 'my_last_login', 10, 2 );
function add_last_login_column($columns) {
    $columns['last_login'] = 'Last Login';
    return $columns;
}
add_filter('manage_users_columns', 'add_last_login_column');
function show_last_login_column($value, $column_name, $user_id) {
    if ($column_name == 'last_login') {
        $last_login = get_user_meta($user_id, 'last_login', true);
        return $last_login ? $last_login : 'Never';
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_last_login_column', 10, 3);
// Make column sortable
add_filter('manage_users_sortable_columns', function($columns) {
    $columns['last_login'] = 'last_login';
    return $columns;
});

// Handle the sort query
add_action('pre_get_users', function($query) {
    if (!is_admin()) return;
    
    $orderby = $query->get('orderby');
    if ($orderby === 'last_login') {
        $query->set('meta_key', 'last_login');
        $query->set('orderby', 'meta_value');
        $query->set('order', $query->get('order') ?: 'DESC');
    }
});
/*
 * AJAX: Wholesale password reset (send reset link)
 * Expects: POST 'email' and 'nonce' (action: 'ld_wholesale_reset')
 */

add_action('wp_ajax_nopriv_ld_wholesale_reset', 'ld_wholesale_reset_handler');
add_action('wp_ajax_ld_wholesale_reset', 'ld_wholesale_reset_handler');

function ld_wholesale_reset_handler() {

    // Security check
    if (
        ! isset($_POST['nonce']) ||
        ! wp_verify_nonce($_POST['nonce'], 'ajax-wholesale-reset-nonce')
    ) {
        wp_send_json_error(['messages' => ['Security check failed']]);
    }

    // Email validation
    $email = isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '';

    if (empty($email) || ! is_email($email)) {
        wp_send_json_error(['messages' => ['Please provide a valid email address.']]);
    }

    $user = get_user_by('email', $email);

    // Prevent enumeration
    if (! $user) {
        wp_send_json_success([
            'message' => 'If an account exists, a reset link has been sent.'
        ]);
    }

    // Generate reset key
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error([
            'messages' => [$reset_key->get_error_message()]
        ]);
    }

    // Build custom reset URL
    $reset_url = site_url('/reset-password/') .
        '?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login);

    // Email setup
    $to = $user->user_email;
    $subject = 'Reset your password';

    // ===== TEMPLATE RENDER =====
    ob_start();

    $html_content = '
    Hi ' . esc_html($user->display_name) . ',<br><br>
    
    Someone has requested a new password for the following account on Loose Grown Diamond:<br><br>
    
    Username: ' . esc_html($user->user_email) . '<br><br>
    
    If you didn’t make this request, just ignore this email. If you’d like to proceed, reset your password via the link below:<br><br>
    
    <a href="' . esc_url($reset_url) . '">Reset your password</a><br><br>
    
    Thanks for reading.
';

    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $user->display_name,
            'email'         => $user->user_email,
            'message'       => '',
            'html_content'  => $html_content,
            'email_heading' => 'Password Reset Request',
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body = ob_get_clean();

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    // Send email
    if (wp_mail($to, $subject, $body, $headers)) {

        wp_send_json_success([
            'message' => 'If an account exists, a reset link has been sent.'
        ]);

    } else {

        wp_send_json_error([
            'messages' => ['Failed to send reset email.']
        ]);
    }
}

/*
 * AJAX: Perform password reset using key+login (from emailed link)
 * Expects POST: 'key', 'login', 'pass1', 'pass2', 'nonce' (action: 'ld_wholesale_do_reset')
 */
add_action('wp_ajax_nopriv_ld_wholesale_do_reset', 'ld_wholesale_do_reset_handler');
add_action('wp_ajax_ld_wholesale_do_reset', 'ld_wholesale_do_reset_handler');

function ld_wholesale_do_reset_handler() {
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'ajax-wholesale-do-reset-nonce') ) {
        wp_send_json_error(array('messages' => array('Security check failed')));
    }

    $key = isset($_POST['key']) ? wp_unslash($_POST['key']) : '';
    $login = isset($_POST['login']) ? wp_unslash($_POST['login']) : '';
    $pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
    $pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';

    if (empty($key) || empty($login)) {
        wp_send_json_error(array('messages' => array('Invalid reset link.')));
    }
    if (empty($pass1) || empty($pass2)) {
        wp_send_json_error(array('messages' => array('Please provide and confirm your new password.')));
    }
    if ($pass1 !== $pass2) {
        wp_send_json_error(array('messages' => array('Passwords do not match.')));
    }
    if (strlen($pass1) < 6) {
        wp_send_json_error(array('messages' => array('Password must be at least 6 characters.')));
    }

    // Validate the reset key and login
    $user = check_password_reset_key($key, $login);
    if (is_wp_error($user)) {
        wp_send_json_error(array('messages' => array($user->get_error_message())));
    }

    // $user may be WP_User object
    if (! is_a($user, 'WP_User')) {
        wp_send_json_error(array('messages' => array('Invalid user.')));
    }

    // Perform the reset (this will also clear the reset key)
    reset_password($user, $pass1);

    // Log the user in immediately
    wp_set_auth_cookie($user->ID, true);

    wp_send_json_success(array('message' => 'Password reset successful. Redirecting...', 'redirect' => site_url()));
}

add_action('woocommerce_cart_calculate_fees', 'add_stripe_fee', 20, 1);
function add_stripe_fee($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    $chosen_payment_method = WC()->session->get('chosen_payment_method');

    if ($chosen_payment_method === 'stripe') {
        $percentage = 0.05; // 5%

        // Include subtotal + shipping
        $subtotal = $cart->get_subtotal();
        $shipping = $cart->get_shipping_total();

        $total_amount = $subtotal + $shipping;

        $fee = $total_amount * $percentage;

        $cart->add_fee('Payment Processing Charge', $fee, true);
    }
}

add_filter('woocommerce_package_rates', 'conditional_shipping_by_custom_category', 100, 2);
function conditional_shipping_by_custom_category($rates, $package) {

    $has_custom_product = false;

    // Check cart items
    foreach (WC()->cart->get_cart() as $cart_item) {
        if (has_term('custom', 'product_cat', $cart_item['product_id'])) {
            $has_custom_product = true;
            break;
        }
    }

    foreach ($rates as $rate_id => $rate) {

        // CASE 1: If custom product → keep ONLY free shipping
        if ($has_custom_product && $rate->method_id !== 'free_shipping') {
            unset($rates[$rate_id]);
        }

        // CASE 2: If NO custom product → remove free shipping
        if (!$has_custom_product && $rate->method_id === 'free_shipping') {
            unset($rates[$rate_id]);
        }
    }

    return $rates;
}


