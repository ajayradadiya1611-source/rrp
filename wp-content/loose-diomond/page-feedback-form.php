<?php

/**
 * Template Name: Feedback Form
 */

get_header();
// Prefill data for the "Request this Video" popup when the customer is logged in
    $vr_first_name = '';
    $vr_last_name  = '';
    $vr_email      = '';
    $vr_phone      = '';

    if ( is_user_logged_in() ) {
        $current_user  = wp_get_current_user();
        $vr_first_name = $current_user->first_name ?: $current_user->display_name;
        $vr_last_name  = $current_user->last_name;
        $vr_email      = $current_user->user_email;
        $vr_phone      = get_user_meta( $current_user->ID, 'billing_phone', true );
    }
?>

<!-- Shipping section start -->
<div class="feedback_section mx-auto">
    <div class="banner_section">
        <div class="py_60">
            <div class="container first_section ">
                <div class="row">
                    <div class="col-md-11 mx-auto">
                        <div class="text-center mb_35">
                            <h1 class="fw-400 h2 ">Feedback Form</h1>
                            <p class="mb-0">Let us know how we can make your experience better. Are there pages you use often that aren't listed under Current Pages? Can't find what you're looking for? Tell us, and we'll work to improve our navigation and make it easier to find.</p>
                        </div>
                    </div>

                    <form class="feedback_box mx-auto" id="ls-feedback-form">

    <p>
        <input type="text" name="name" class="form-control" placeholder="Your name*"  value="<?php if ( is_user_logged_in() ) { echo esc_attr( $vr_first_name ?? '' ).' '.esc_attr( $vr_last_name ?? '' ); }?>">
        <small class="text-danger error-name"></small>
    </p>

    <p>
        <input type="email" name="email" class="form-control" placeholder="Your email*"  value="<?php echo esc_attr( $vr_email ?? '' ); ?>">
        <small class="text-danger error-email"></small>
    </p>

    <p>
        <textarea name="message" class="form-control" rows="3" placeholder="Your message*"></textarea>
        <small class="text-danger error-message"></small>
    </p>

    <button type="submit" class="btn">SUBMIT FORM</button>

    <p class="mt-4 border border-danger text-danger p-2" id="ls-feedback-error" style="display: none;"></p>
    <p class="mt-4 border border-success text-success p-2" id="ls-feedback-success" style="display: none;"></p>

</form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shipping section end -->
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( LD_RECAPTCHA_SITE_KEY ); ?>" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.id !== 'ls-feedback-form') return;

        e.preventDefault();

        const successBox = document.getElementById('ls-feedback-success');
        const errorBox   = document.getElementById('ls-feedback-error');

        const setText = (selector, text) => {
            document.querySelectorAll(selector).forEach(el => el.textContent = text);
        };

        const showBox = (box, html) => {
            if (!box) return;
            box.innerHTML = html;
            box.style.transition = '';
            box.style.opacity = '1';
            box.style.display = 'block';
        };

        const hideBox = (box) => {
            if (!box) return;
            box.innerHTML = '';
            box.style.display = 'none';
        };

        // Equivalent of jQuery .fadeOut()
        const fadeOut = (box, duration = 400) => {
            if (!box || box.style.display === 'none') return;
            box.style.transition = `opacity ${duration}ms`;
            box.style.opacity = '0';
            setTimeout(() => {
                box.style.display = 'none';
                box.style.opacity = '1';
                box.style.transition = '';
            }, duration);
        };

        // Clear previous errors
        setText('.error-name, .error-email, .error-message', '');
        hideBox(successBox);
        hideBox(errorBox);

        let isValid = true;

        const name    = form.querySelector('input[name="name"]').value.trim();
        const email   = form.querySelector('input[name="email"]').value.trim();
        const message = form.querySelector('textarea[name="message"]').value.trim();

        // Name validation
        if (name === '') {
            setText('.error-name', 'Name is required');
            isValid = false;
        }

        // Email validation
        if (email === '') {
            setText('.error-email', 'Email is required');
            isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            setText('.error-email', 'Enter a valid email');
            isValid = false;
        }

        // Message validation
        if (message === '') {
            setText('.error-message', 'Message is required');
            isValid = false;
        }

        // Stop if validation fails
        if (!isValid) return;

        const formData  = new URLSearchParams(new FormData(form)); // same as .serialize()
        const submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        const spinner = document.createElement('span');
        spinner.className = 'spinner-border spinner-border-sm me-2 ls-spinner';
        submitBtn.appendChild(spinner);

        const resetButton = () => {
            submitBtn.disabled = false;
            const s = submitBtn.querySelector('.ls-spinner');
            if (s) s.remove();
        };

        const sendForm = (token) => {
            formData.set('recaptcha_token', token);
            fetch('<?php echo site_url() . '/wp-json/ls/v1/feedback'; ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: formData
            })
            .then(async res => {
                // Try to read JSON even on error responses (like xhr.responseJSON)
                let data = null;
                try { data = await res.json(); } catch (_) {}

                if (!res.ok) {
                    const err = new Error('Request failed');
                    err.responseJSON = data;
                    throw err;
                }
                return data;
            })
            .then(response => {
                if (response && response.success) {
                    showBox(successBox, response.success);
                    form.reset();
                } else if (response && response.error) {
                    showBox(errorBox, response.error);
                }

                setTimeout(() => {
                    fadeOut(successBox);
                    fadeOut(errorBox);
                }, 5000);

                resetButton();
            })
            .catch(err => {
                const res = err.responseJSON;

                // Handle field errors from backend
                if (res && res.errors) {
                    if (res.errors.name)    setText('.error-name', res.errors.name);
                    if (res.errors.email)   setText('.error-email', res.errors.email);
                    if (res.errors.message) setText('.error-message', res.errors.message);
                } else if (res && res.error) {
                    showBox(errorBox, res.error);
                } else {
                    showBox(errorBox, 'Something went wrong. Please try again.');
                }

                resetButton();
            });
        };

        if (typeof grecaptcha === 'undefined') {
            showBox(errorBox, 'reCAPTCHA failed to load. Please refresh the page and try again.');
            resetButton();
            return;
        }

        grecaptcha.ready(function () {
            grecaptcha.execute('<?php echo esc_js( LD_RECAPTCHA_SITE_KEY ); ?>', { action: 'feedback_form' })
                .then(sendForm)
                .catch(function () {
                    showBox(errorBox, 'reCAPTCHA verification failed. Please try again.');
                    resetButton();
                });
        });
    });

});
</script>
<?php
get_footer();
