<?php

/**
 * Template Name: Contact Us
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

<!-- contact us section -->
<div class="contact_us">
    <div class="banner_section">
        <div class="py_60">
            <div class="container-fluid">
                <div>
                    <h1 class="h2 text_black_secondary fw-400 mb_20 text-center">Get in Touch </h1>
                    <p class="text-center mb_20">Fill out the form below or email us at <a
                            href="mailto:<?php echo SUPPORT_EMAIL;?>"><b><?php echo SUPPORT_EMAIL;?></b></a> </p>
                    <p class="text-center mb_20"><b>Business Phone (WhatsApp):</b> <a href="tel:+<?php echo SUPPORT_MOBILE ?>" >+<?php echo SUPPORT_MOBILE ?></a> </p>
                    <p class="text-center mb_20"><b>Office Address:</b> 55W 47th St., Suite#790 New York, NY-10036 USA </p>
                    <p class="text-center mb_70"><b>India Address:</b> 2nd Floor, Red Turning Point, Near, Matawadi Police Chowki, L.H.Road, Varachha, Surat, Gujarat – 395006 </p>
                    <form class="form_box mx-auto" id="ls-contact-us-form">
                        <div class="row row-gap-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your name*"  value="<?php if ( is_user_logged_in() ) { echo esc_attr( $vr_first_name ?? '' ).' '.esc_attr( $vr_last_name ?? '' );} ?>">
                                <small class="text-danger error-name"></small>
                            </div>

                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your email*"  value="<?php echo esc_attr( $vr_email ?? '' ); ?>">
                                <small class="text-danger error-email"></small>
                            </div>

                            <div class="col-md-12">
                                <input type="tel" name="number" class="form-control" placeholder="Your Number*" value="<?php echo esc_attr( $vr_phone ?? '' ); ?>">
                                <small class="text-danger error-number"></small>
                            </div>

                            <div class="col-12">
                                <textarea name="message" class="form-control" rows="3" placeholder="Your message*"></textarea>
                                <small class="text-danger error-message"></small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary text-uppercase mt-4">Let's talk</button>
                        <p class="mt-4 border border-danger text-danger p-2" id="ls-contact-us-error" style="display: none;">Error</p>
                        <p class="mt-4 border border-success text-success p-2" id="ls-contact-us-success" style="display: none;">Success</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.id !== 'ls-contact-us-form') return;

        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

        let isValid = true;

        const name    = form.querySelector('input[name="name"]').value.trim();
        const email   = form.querySelector('input[name="email"]').value.trim();
        const number  = form.querySelector('input[name="number"]').value.trim();
        const message = form.querySelector('textarea[name="message"]').value.trim();

        const setError = (cls, msg) => {
            const el = document.querySelector('.' + cls);
            if (el) el.textContent = msg;
            isValid = false;
        };

        // Name validation
        if (name === '') setError('error-name', 'Name is required');

        // Email validation
        if (email === '') {
            setError('error-email', 'Email is required');
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            setError('error-email', 'Enter a valid email');
        }

        // Phone validation
        if (number === '') {
            setError('error-number', 'Phone number is required');
        } else if (!/^\+?[0-9\s\-()]{7,15}$/.test(number)) {
            setError('error-number', 'Enter a valid phone number');
        }

        // Message validation
        if (message === '') setError('error-message', 'Message is required');

        if (!isValid) return;

        const submitBtn  = form.querySelector('button[type="submit"]');
        const successBox = document.getElementById('ls-contact-us-success');
        const errorBox   = document.getElementById('ls-contact-us-error');

        // Disable button + add spinner
        submitBtn.disabled = true;
        const spinner = document.createElement('span');
        spinner.className = 'spinner-border spinner-border-sm me-2 ls-spinner';
        submitBtn.appendChild(spinner);

        const resetButton = () => {
            submitBtn.disabled = false;
            const s = submitBtn.querySelector('.ls-spinner');
            if (s) s.remove();
        };

        // Same as jQuery .serialize()
        const formData = new URLSearchParams(new FormData(form));

        fetch('<?php echo site_url() . '/wp-json/ls/v1/contact-us'; ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Request failed');
            return res.json();
        })
        .then(response => {
            if (response.success) {
                successBox.innerHTML = response.success;
                successBox.style.display = 'block';
            } else if (response.error) {
                errorBox.innerHTML = response.error;
                errorBox.style.display = 'block';
            }
            form.reset();
            resetButton();
        })
        .catch(() => {
            errorBox.innerHTML = 'Something went wrong';
            errorBox.style.display = 'block';
            resetButton();
        });
    });

});
</script>

<?php
get_footer();
