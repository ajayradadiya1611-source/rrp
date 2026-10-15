<?php

/**
 * Template Name: Wholesale Inquiries
 */

get_header();

// Prefill when the customer is logged in
$ws_name  = '';
$ws_email = '';
$ws_phone = '';

if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $ws_name      = trim( ( $current_user->first_name ?: $current_user->display_name ) . ' ' . $current_user->last_name );
    $ws_email     = $current_user->user_email;
    $ws_phone     = get_user_meta( $current_user->ID, 'billing_phone', true );
}

$ws_countries = function_exists( 'WC' ) && WC()->countries ? WC()->countries->get_countries() : [ 'US' => 'United States' ];
?>

<!-- wholesale inquiries section -->
<div class="contact_us wholesale_inquiries">
    <div class="banner_section">
        <div class="py_60">
            <div class="container-fluid">
                <h1 class="h2 text_black_secondary fw-400 mb_20 text-center">Wholesale Inquiries</h1>
                <p class="text-center mb_70">
                    If you are a retailer interested in stocking<br>
                    <?php echo esc_html( SITE_OC_NAME ); ?>&rsquo;s Diamonds/Jewelry, please fill out the contact form.
                </p>

                <form class="form_box mx-auto" id="ls-wholesale-form" novalidate>
                    <div class="row row-gap-4">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control" placeholder="Your name*" value="<?php echo esc_attr( $ws_name ); ?>">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Your Email*" value="<?php echo esc_attr( $ws_email ); ?>">
                            <small class="text-danger error-email"></small>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="store" class="form-control" placeholder="Store Name*">
                            <small class="text-danger error-store"></small>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="website" class="form-control" placeholder="Website*">
                            <small class="text-danger error-website"></small>
                        </div>
                        <div class="col-md-4">
                            <input type="tel" name="number" class="form-control" placeholder="Mobile Number*" value="<?php echo esc_attr( $ws_phone ); ?>">
                            <small class="text-danger error-number"></small>
                        </div>
                        <div class="col-md-4">
                            <select name="country" class="form-control">
                                <?php foreach ( $ws_countries as $code => $label ) : ?>
                                    <option value="<?php echo esc_attr( $label ); ?>" <?php selected( $code, 'US' ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-danger error-country"></small>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="city" class="form-control" placeholder="City*">
                            <small class="text-danger error-city"></small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary text-uppercase mt-4">Submit</button>
                    <p class="mt-4 border border-danger text-danger p-2" id="ls-wholesale-error" style="display: none;"></p>
                    <p class="mt-4 border border-success text-success p-2" id="ls-wholesale-success" style="display: none;"></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.body.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.id !== 'ls-wholesale-form') return;

        e.preventDefault();

        const successBox = document.getElementById('ls-wholesale-success');
        const errorBox   = document.getElementById('ls-wholesale-error');

        form.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
        successBox.style.display = 'none';
        errorBox.style.display   = 'none';

        let isValid = true;
        const val = n => {
            const el = form.querySelector('[name="' + n + '"]');
            return el && el.value ? el.value.trim() : '';
        };
        const setError = (n, msg) => {
            const el = form.querySelector('.error-' + n);
            if (el) el.textContent = msg;
            isValid = false;
        };

        if (val('name') === '')  setError('name', 'Name is required');
        if (val('store') === '') setError('store', 'Store name is required');
        if (val('website') === '') {
            setError('website', 'Website is required');
        } else if (!/^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/\S*)?$/i.test(val('website'))) {
            setError('website', 'Enter a valid website');
        }
        if (val('country') === '') setError('country', 'Country is required');
        if (val('city') === '')    setError('city', 'City is required');

        if (val('email') === '') {
            setError('email', 'Email is required');
        } else if (!/^\S+@\S+\.\S+$/.test(val('email'))) {
            setError('email', 'Enter a valid email');
        }

        let phone = val('number');
        if (phone === '') {
            const tel = form.querySelector('input[type="tel"]');
            phone = tel ? tel.value.trim() : '';
        }
        if (phone === '') {
            setError('number', 'Phone number is required');
        } else if (!/^\+?[0-9\s\-()]{7,20}$/.test(phone)) {
            setError('number', 'Enter a valid phone number');
        }

        if (!isValid) return;

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

        const formData = new URLSearchParams(new FormData(form));
        const srcMatch = document.cookie.match(/(?:^|; )ld_source=([^;]*)/);
        if (!formData.get('number') && phone) formData.set('number', phone);
        formData.set('source', srcMatch ? decodeURIComponent(srcMatch[1]) : '');

        fetch('<?php echo esc_url( rest_url( 'ls/v1/wholesale' ) ); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                successBox.innerHTML = response.success;
                successBox.style.display = 'block';
                form.reset();
            } else {
                errorBox.innerHTML = response.error || 'Something went wrong';
                errorBox.style.display = 'block';
            }
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
