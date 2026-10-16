<?php
/**
 * Output JS inline in wp_footer (no external file required)
 */
function ls_subscribe_footer_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        'use strict';

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function showMessage(box, message, type) {
            var next = box.nextElementSibling;
            if (next && next.classList.contains('ls-subscribe-msg')) next.remove();

            var p = document.createElement('p');
            p.className = 'ls-subscribe-msg';
            p.textContent = message;
            p.style.cssText = 'margin-top:8px;font-size:13px;font-weight:500;color:' +
                (type === 'success' ? '#2ecc71' : '#e74c3c') + ';';
            box.insertAdjacentElement('afterend', p);

            // success stays visible (the form is hidden); errors fade out
            if (type !== 'success') {
                setTimeout(function () {
                    p.style.transition = 'opacity 400ms';
                    p.style.opacity = 0;
                    setTimeout(function () { p.remove(); }, 400);
                }, 3000);
            }
        }

        function subscribe(btn) {
            var box   = btn.closest('.search-box.footer-box');
            if (!box) return;
            var input = box.querySelector('input[type="text"], input[type="email"]');
            var email = input ? input.value.trim() : '';

            document.querySelectorAll('.ls-subscribe-msg').forEach(function (m) { m.remove(); });

            if (!email) {
                showMessage(box, 'Please enter your email address.', 'error');
                return;
            }
            if (!emailRegex.test(email)) {
                showMessage(box, 'Please enter a valid email address.', 'error');
                return;
            }

            btn.disabled = true;

            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: new URLSearchParams({
                    action: 'ls_email_subscribe',
                    nonce:  '<?php echo esc_js( wp_create_nonce( 'ls_subscribe_nonce' ) ); ?>',
                    email:  email
                })
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (response) {
                var msg = response.data && response.data.message ? response.data.message : '';
                if (response.success) {
                    box.style.display = 'none';
                    showMessage(box, msg || 'Thanks for subscribing!', 'success');
                    if (input) input.value = '';
                } else {
                    showMessage(box, msg || 'Subscription failed. Please try again.', 'error');
                    btn.disabled = false;
                }
            })
            .catch(function () {
                showMessage(box, 'Network error. Please try again.', 'error');
                btn.disabled = false;
            });
        }

        // Button click
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.subscribe_newsletter');
            if (btn) {
                e.preventDefault();
                subscribe(btn);
            }
        });

        // Enter key in the email field
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            var box = e.target.closest('.search-box.footer-box');
            if (!box || e.target.tagName !== 'INPUT') return;
            var btn = box.querySelector('.subscribe_newsletter');
            if (btn) {
                e.preventDefault();
                subscribe(btn);
            }
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'ls_subscribe_footer_script' );



/**
 * Email Subscription - Custom Table + AJAX Handler
 * Add this code to your theme's functions.php or a custom plugin file.
 */
 
// ─────────────────────────────────────────────
// 1. CREATE CUSTOM TABLE ON PLUGIN/THEME ACTIVATION
// ─────────────────────────────────────────────
function ls_create_email_subscribers_table() {
    global $wpdb;
 
    $table_name      = $wpdb->prefix . 'ls_email_subscribers';
    $charset_collate = $wpdb->get_charset_collate();
 
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email      VARCHAR(255)        NOT NULL,
        status     TINYINT(1)          NOT NULL DEFAULT 1,
        ip_address VARCHAR(45)                  DEFAULT NULL,
        created_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) {$charset_collate};";
 
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
// Run on activation (plugin) or after_switch_theme (theme)
add_action( 'after_switch_theme', 'ls_create_email_subscribers_table' );
// Also run once on init in case table is missing (safe with IF NOT EXISTS)
add_action( 'init', 'ls_create_email_subscribers_table' );

// ─────────────────────────────────────────────
// 3. AJAX HANDLER (logged-in + logged-out users)
// ─────────────────────────────────────────────
function ls_handle_email_subscribe() {
 
    // --- Security: verify nonce ---
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ls_subscribe_nonce' ) ) {
        wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'textdomain' ) ) );
    }
 
    // --- Validate email ---
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
 
    if ( empty( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter your email address.', 'textdomain' ) ) );
    }
 
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'textdomain' ) ) );
    }
 
    global $wpdb;
    $table_name = $wpdb->prefix . 'ls_email_subscribers';
 
    // --- Check for duplicate ---
    $exists = $wpdb->get_var(
        $wpdb->prepare( "SELECT id FROM {$table_name} WHERE email = %s", $email )
    );
 
    if ( $exists ) {
        wp_send_json_error( array( 'message' => __( 'You are already subscribed!', 'textdomain' ) ) );
    }
 
    // --- Get visitor IP ---
    $ip = '';
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }
 
    // --- Insert into custom table ---
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'email'      => $email,
            'ip_address' => $ip,
            'status'     => 1,
        ),
        array( '%s', '%s', '%d' )
    );
 
    if ( false === $inserted ) {
        wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again later.', 'textdomain' ) ) );
    }
 
    wp_send_json_success( array( 'message' => __( 'Thank you for subscribing to the RRP Diamond newsletter.', 'textdomain' ) ) );
}
add_action( 'wp_ajax_ls_email_subscribe',        'ls_handle_email_subscribe' );
add_action( 'wp_ajax_nopriv_ls_email_subscribe', 'ls_handle_email_subscribe' );