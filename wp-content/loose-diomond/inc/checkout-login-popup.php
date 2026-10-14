<?php
add_action('woocommerce_before_checkout_form', 'custom_ajax_checkout_login', 5);
function custom_ajax_checkout_login() {

    if ( is_user_logged_in() ) return;
    ?>

    <div class="oc-returning-customer">
        Returning customer? 
        <a href="#" data-bs-toggle="modal" data-bs-target="#checkoutLoginModal">
            Log in
        </a>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="checkoutLoginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div id="login-message"></div>

                    <form id="custom-login-form">

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <input type="hidden" name="action" value="custom_ajax_login">
                        <?php wp_nonce_field('custom_login_nonce', 'security'); ?>

                        <button type="submit" class="btn btn-dark w-100">
                            Login
                        </button>

                        <!-- Extra Links -->
                        <div class="mt-3 text-center small">

                            <p class="mb-1">
                                <a href="<?php echo wp_lostpassword_url(); ?>">
                                    Forgot your password?
                                </a>
                            </p>

                            <p class="mb-0">
                                First time here? 
                                <a href="<?php echo wc_get_page_permalink('myaccount'); ?>">
                                    Create an Account
                                </a>
                            </p>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <?php
}
add_action('wp_ajax_nopriv_custom_ajax_login', 'custom_ajax_login');
function custom_ajax_login() {

    check_ajax_referer('custom_login_nonce', 'security');

    $creds = array(
        'user_login'    => sanitize_text_field($_POST['username']),
        'user_password' => $_POST['password'],
        'remember'      => true
    );

    $user = wp_signon($creds, false);

    if ( is_wp_error($user) ) {
        wp_send_json_error('Invalid email or password.');
    }

    wp_send_json_success('Login successful');
}
add_action('wp_footer', 'custom_ajax_login_script');
function custom_ajax_login_script() {

    if ( ! is_checkout() || is_user_logged_in() ) return;
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        var form = document.getElementById('custom-login-form');
        if (!form) return;

        var msgBox = document.getElementById('login-message');

        function setButtons(text, disabled) {
            form.querySelectorAll('button').forEach(function (btn) {
                btn.textContent = text;
                btn.disabled = disabled;
            });
        }

        function showMsg(type, text) {
            if (msgBox) msgBox.innerHTML = '<div class="alert alert-' + type + '">' + text + '</div>';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            setButtons('Logging in...', true);

            fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: new URLSearchParams(new FormData(form))
            })
            .then(function (res) { return res.json(); })
            .then(function (response) {
                if (response.success) {
                    showMsg('success', response.data);
                    setTimeout(function () {
                        location.reload(); // refresh checkout
                    }, 1000);
                } else {
                    showMsg('danger', response.data || 'Login failed. Please try again.');
                    setButtons('Login', false);
                }
            })
            .catch(function () {
                showMsg('danger', 'An error occurred. Please try again.');
                setButtons('Login', false);
            });
        });
    });
    </script>

    <?php
}