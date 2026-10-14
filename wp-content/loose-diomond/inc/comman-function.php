<?php

/**
 * First-touch registration source tracking.
 * On a visitor's first page load, capture where they came from
 * (utm_source, or the referring domain, or "Direct") into a
 * 30-day cookie `ld_source`. The cookie is only ever set once
 * per visitor, so it always reflects their original entry point
 * even if they browse the site for a while before registering
 * on the Wholesale form.
 */
add_action( 'wp_head', 'ld_output_source_capture_script', 1 );
function ld_output_source_capture_script() {
    ?>
    <script>
    (function () {
        try {
            // Already have a first-touch source stored — don't overwrite it
            if (document.cookie.split('; ').some(function (c) { return c.indexOf('ld_source=') === 0; })) {
                return;
            }

            var params = new URLSearchParams(window.location.search);
            var utmSource = params.get('utm_source') || params.get('source');
            var utmMedium = params.get('utm_medium');
            var source = utmSource ? (utmSource + (utmMedium ? ' / ' + utmMedium : '')) : '';

            if (!source) {
                if (document.referrer) {
                    try {
                        var refHost = new URL(document.referrer).hostname.replace(/^www\./, '');
                        var thisHost = window.location.hostname.replace(/^www\./, '');
                        source = (refHost === thisHost) ? 'Direct' : refHost;
                    } catch (e) {
                        source = 'Direct';
                    }
                } else {
                    source = 'Direct';
                }
            }

            var expires = new Date();
            expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = 'ld_source=' + encodeURIComponent(source) + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
        } catch (e) {}
    })();
    </script>
    <?php
}

/**
 * Global openZohoChat(customMessage) — used by the "Need it sooner?" link
 * in the shipping-estimate block on the ring/setting page, diamond page,
 * and ring preview page. Previously this same function was copy-pasted
 * inline into each of those three templates; it's now defined once here
 * and printed in wp_footer, so every page just calls openZohoChat().
 *
 * @param string $customMessage Optional message to prefill in the chat.
 *                               Falls back to a generic "sooner than the
 *                               estimated delivery date" message with the
 *                               current page URL if omitted.
 */
add_action( 'wp_footer', 'ls_output_zoho_chat_script' );
function ls_output_zoho_chat_script() {
    ?>
    <script>
        window.$zoho = window.$zoho || {};
        window.lgdZohoReady = false;
        window.lgdZohoPending = null;

        $zoho.salesiq = $zoho.salesiq || {
            ready: function () {
                window.lgdZohoReady = true;

                // If a button was clicked before the widget loaded, open it now
                if (window.lgdZohoPending) {
                    lgdShowZohoChat(window.lgdZohoPending);
                    window.lgdZohoPending = null;
                }
            }
        };

        function lgdShowZohoChat(message) {
            try {
                $zoho.salesiq.visitor.question(message);
                $zoho.salesiq.floatwindow.visible("show");
            } catch (e) {
                console.warn('Zoho SalesIQ open failed:', e);
            }
        }

        function openZohoChat(customMessage) {
            var pageUrl = window.location.href;
            var message = customMessage ||
                ('Hi, I need this sooner than the estimated delivery date. Product page: ' + pageUrl);

            if (window.lgdZohoReady) {
                lgdShowZohoChat(message);
            } else {
                // Widget still loading; ready() will open it with this message
                window.lgdZohoPending = message;
            }
        }
    </script>
    <script id="zsiqscript"
            src="https://salesiq.zohopublic.in/widget?wc=<?php echo esc_attr( ZOHOCHATBOT ); ?>"
            defer></script>
    <?php
}


/**
 * Refresh checkout when payment method changes
 */
add_action('wp_footer', 'refresh_checkout_on_payment_change');

function refresh_checkout_on_payment_change() {
    if (is_checkout() && !is_order_received_page()) : ?>
        <script type="text/javascript">
        jQuery(function($){
            $('form.checkout').on('change', 'input[name="payment_method"]', function(){
                $('body').trigger('update_checkout');
            });
        });
        </script>
    <?php
    endif;
}
/**
 * Whether the customer's currently chosen shipping method is free shipping.
 * Checked via session (works pre-order, e.g. cart/checkout review) with a
 * fallback to WC()->cart's calculated packages.
 */
function ls_cart_has_free_shipping() {

    // 1. Check chosen shipping methods (fast path, id-based)
    if ( WC()->session ) {
        $chosen = WC()->session->get( 'chosen_shipping_methods' );
        if ( is_array( $chosen ) ) {
            foreach ( $chosen as $method ) {
                if ( 0 === strpos( $method, 'free_shipping' ) ) {
                    return true;
                }
            }
        }
    }

    // 2. Check by label text on the currently chosen rate(s)
    if ( WC()->shipping() ) {
        $packages = WC()->shipping()->get_packages();

        if ( is_array( $packages ) ) {
            foreach ( $packages as $package_key => $package ) {
                if ( empty( $package['rates'] ) || ! is_array( $package['rates'] ) ) {
                    continue;
                }

                // Which rate id is actually selected for this package
                $chosen_rate_id = WC()->session
                    ? WC()->session->get( 'chosen_shipping_methods' )[ $package_key ] ?? ''
                    : '';

                foreach ( $package['rates'] as $rate_id => $rate ) {
                    // Only inspect the rate that's actually selected
                    if ( $chosen_rate_id && $rate_id !== $chosen_rate_id ) {
                        continue;
                    }

                    $label = $rate->get_label();

                    if ( $label && false !== stripos( $label, 'free' ) ) {
                        return true;
                    }

                    // Belt-and-suspenders: zero-cost rate also counts as free
                    if ( '' !== $rate->get_cost() && (float) $rate->get_cost() === 0.0 ) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}
















function custom_lost_password_css() {
    global $text_change;
    global $oc_common;
    $home_page = $text_change['home_page'];
    if (is_account_page()) {
        ?>
        <style>
          .woocommerce-lost-password div#navbarSupportedContent {
                display: none !important;
            }
          .woocommerce-lost-password  footer{
                 display: none !important;
            }
           .woocommerce-lost-password .top_bar{
                 display: none !important; 
            }
        </style>
        <?php
    }
    if ( is_checkout()   && ! is_order_received_page() ) {
        ?>
         <style>
            div#navbarSupportedContent {
                display: none !important;
            }
            footer,.navbar-toggler{
                display: none !important; 
            }
        </style>
        <?php
    }
    
        ?>
        <style type="text/css">
            
            .oc-retail .banner_section.home_banner_hero .bg_img{
                padding: 0;
                position: relative;
                background-image: none;
                /*background-size: cover;
                background-image: url(<?php echo $home_page['hero_banner_image_desktop']; ?>);*/
            }
            .home_banner_hero  .banner_text{
                 position: absolute;
                top: 60%;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                width: 100%;
            }
            @media (max-width: 767px) {
                .home_banner_hero .banner_text{
                    top: 35%;
                }
               .home_banner_hero .banner_text  > div{
                    padding: 0px 20px;
                }
            }
        </style>
        <?php
    
}
add_action('wp_head', 'custom_lost_password_css');


function oc_wp_footer() {
    if (is_checkout()) {
        ?>
          
        <?php
    }
    if (is_product() || get_query_var('diamond_sku')) {
        ?>
        <style>
            
        </style>
        <script>
         /**
         * ls-gallery.js
         * Thumbnail + slide gallery navigation for single product pages.
         * Exposes window.lsInitGalleryNav(glr) for external re-initialisation
         * (e.g. after a variation change rebuilds the DOM).
         */
        (function () {
            'use strict';

            /* ---------- CSS — injected once into <head> ---------- */
            function injectGalleryCss() {
                const cssId = 'ls-gallery-visibility-css';
                if (document.getElementById(cssId)) return;

                const style = document.createElement('style');
                style.id = cssId;
                style.textContent =
                    '.ls-slide{transition:opacity .2s ease;display:block;}' +
                    '.ls-slide-hidden{visibility:hidden;opacity:0;position:absolute;top:0;left:0;width:100%;}' +
                    '.ls-slide-visible{visibility:visible;opacity:1;position:relative;z-index:99;}' +
                    '.ls_sp_glr .img_slide{touch-action:pan-y;}';
                document.head.appendChild(style);
            }

            /* ---------- Slide visibility helpers ---------- */
            function showSlide(slide) {
                slide.classList.remove('ls-slide-hidden');
                slide.classList.add('ls-slide-visible');
            }

            function hideSlide(slide) {
                slide.classList.remove('ls-slide-visible');
                slide.classList.add('ls-slide-hidden');
            }

            function getVisibleSlideIndex(slides) {
                let idx = 0;
                slides.forEach((s, i) => {
                    if (s.classList.contains('ls-slide-visible')) idx = i;
                });
                return idx;
            }

            function goToSlide(thumbs, slides, idx) {
                thumbs.forEach((t, i) => t.classList.toggle('active', i === idx));
                slides.forEach((s, i) => (i === idx ? showSlide(s) : hideSlide(s)));
            }

            /* ---------- Per-gallery init ---------- */
            function initGalleryNav(wrap) {
                // Replaces jQuery's .off().on(): abort all listeners from a previous init
                if (wrap.__lsNavAbort) wrap.__lsNavAbort.abort();
                const controller = new AbortController();
                wrap.__lsNavAbort = controller;
                const opts = { signal: controller.signal };

                const thumbs = Array.from(wrap.querySelectorAll('.thumbs .img'));
                const slides = Array.from(wrap.querySelectorAll('.img_slide .img'));
                if (!slides.length) return;

                // Replace inline display:none / block with visibility classes
                slides.forEach((s, i) => {
                    s.classList.add('ls-slide');
                    s.style.display = '';
                    i === 0 ? showSlide(s) : hideSlide(s);
                });

                thumbs.forEach((t, i) => t.classList.toggle('active', i === 0));

                // Thumb click + hover
                thumbs.forEach((t, i) => {
                    ['click', 'mouseenter'].forEach((type) =>
                        t.addEventListener(type, () => goToSlide(thumbs, slides, i), opts)
                    );
                });

                const prevBtn = wrap.querySelector('.ls_prev_btn');
                const nextBtn = wrap.querySelector('.ls_next_btn');

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        const newIdx = (getVisibleSlideIndex(slides) - 1 + slides.length) % slides.length;
                        goToSlide(thumbs, slides, newIdx);
                    }, opts);
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        const newIdx = (getVisibleSlideIndex(slides) + 1) % slides.length;
                        goToSlide(thumbs, slides, newIdx);
                    }, opts);
                }

                // Touch swipe on the main preview
                const slideArea = wrap.querySelector('.img_slide');
                if (slideArea) {
                    let touchStartX = 0;
                    let touchStartY = 0;
                    const SWIPE_THRESHOLD = 40;

                    slideArea.addEventListener('touchstart', (e) => {
                        const t = e.touches[0];
                        touchStartX = t.clientX;
                        touchStartY = t.clientY;
                    }, { passive: true, signal: controller.signal });

                    slideArea.addEventListener('touchend', (e) => {
                        const t = e.changedTouches[0];
                        const dx = t.clientX - touchStartX;
                        const dy = t.clientY - touchStartY;

                        // Ignore mostly-vertical touches and taps
                        if (Math.abs(dx) < SWIPE_THRESHOLD || Math.abs(dx) < Math.abs(dy)) return;

                        const current = getVisibleSlideIndex(slides);
                        const newIdx = dx < 0
                            ? (current + 1) % slides.length                  // swipe left  → next
                            : (current - 1 + slides.length) % slides.length; // swipe right → prev

                        goToSlide(thumbs, slides, newIdx);
                    }, opts);
                }
            }

            /* ---------- Boot ---------- */
            function initAllGalleries() {
                const galleries = document.querySelectorAll('.ls_sp_glr');
                if (!galleries.length) return;

                injectGalleryCss();
                galleries.forEach(initGalleryNav);
            }

            // Public API — accepts a plain element OR a jQuery object,
            // so single-product.php works whether or not it passes jQuery(glr).
            window.lsInitGalleryNav = function (glr) {
                if (!glr) return;
                const el = glr.jquery ? glr[0] : glr;
                if (el) initGalleryNav(el);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAllGalleries);
            } else {
                initAllGalleries();
            }
        })();

        /* =========================================================
         * SKIN TONE SLIDER (rtos)
         * ========================================================= */
        (function () {
            const track    = document.getElementById('rtos-track');
            const thumb    = document.getElementById('rtos-thumb');
            const imgWhite = document.querySelector('.rtos-img-white');

            // Not every product has this slide; avoid errors on those pages
            if (!track || !thumb || !imgWhite) return;

            const PAD = 12;
            let dragging = false;

            function setRatio(clientY) {
                const rect   = track.getBoundingClientRect();
                const usable = rect.height - PAD * 2;

                let y = clientY - rect.top - PAD;
                y = Math.max(0, Math.min(usable, y));

                imgWhite.style.opacity = 1 - y / usable;
                thumb.style.top = y + 'px';
            }

            thumb.addEventListener('mousedown', (e) => {
                dragging = true;
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (dragging) setRatio(e.clientY);
            });

            document.addEventListener('mouseup', () => { dragging = false; });

            track.addEventListener('click', (e) => setRatio(e.clientY));

            thumb.addEventListener('touchstart', (e) => {
                dragging = true;
                e.preventDefault();
            }, { passive: false });

            document.addEventListener('touchmove', (e) => {
                if (dragging) setRatio(e.touches[0].clientY);
            }, { passive: false });

            document.addEventListener('touchend', () => { dragging = false; });
        })();
        </script>
        <?php
    }

    if ( is_account_page() && ! is_user_logged_in() ) {
        ?>
        <script>
          // ─── Shared Utilities ───────────────────────────────────────────────────────

          function showError(input, message) {
            const wrapper = input.closest('p') || input.parentElement;
           // wrapper.classList.add('woocommerce-invalid');

            const error = document.createElement('span');
            error.className = 'woocommerce-error-msg';
            error.style.cssText = 'color:#e2401c; font-size:13px; display:block; margin-top:4px;';
            error.textContent = message;

            if (!wrapper.querySelector('.woocommerce-error-msg')) {
              wrapper.appendChild(error);
            }

            input.style.borderColor = '#e2401c';
          }

          function clearErrors() {
            document.querySelectorAll('.woocommerce-error-msg').forEach(el => el.remove());
            //document.querySelectorAll('.woocommerce-invalid').forEach(el => el.classList.remove('woocommerce-invalid'));
            document.querySelectorAll('input').forEach(el => el.style.borderColor = '');
          }

          function attachLiveClear(ids) {
            ids.forEach(id => {
              const el = document.getElementById(id);
              if (el) {
                el.addEventListener('input', function () {
                  const wrapper = this.closest('p') || this.parentElement;
                  const err = wrapper.querySelector('.woocommerce-error-msg');
                  if (err) err.remove();
                  this.style.borderColor = '';
                  //wrapper.classList.remove('woocommerce-invalid');
                });
              }
            });
          }

          // ─── Login Form Validation ───────────────────────────────────────────────────

          document.querySelector('.woocommerce-form-login').addEventListener('submit', function (e) {
            clearErrors();

            const email    = document.getElementById('username');
            const password = document.getElementById('password');
            let isValid    = true;

            if (!email.value.trim()) {
              showError(email, 'Email address is required.');
              isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
              showError(email, 'Enter a valid email address.');
              isValid = false;
            }

            if (!password.value) {
              showError(password, 'Password is required.');
              isValid = false;
            } else if (password.value.length < 6) {
              showError(password, 'Password must be at least 6 characters.');
              isValid = false;
            }

            if (!isValid) {
              e.preventDefault();
              //document.querySelector('.woocommerce-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          });

          attachLiveClear(['username', 'password']);

          // ─── Register Form Validation ────────────────────────────────────────────────

          document.querySelector('button[name="register"]').addEventListener('click', function (e) {
            //e.preventDefault();
            clearErrors();

            const name     = document.getElementById('reg_billing_first_name');
            const phone    = document.getElementById('reg_billing_phone');
            const email    = document.getElementById('reg_email');
            const password = document.getElementById('reg_password');
            let isValid    = true;

            if (!name.value.trim()) {
              showError(name, 'Name is required.');
              isValid = false;
            } else if (!/^[a-zA-Z\s'-]{2,50}$/.test(name.value.trim())) {
              showError(name, 'Enter a valid name (letters only, 2–50 characters).');
              isValid = false;
            }

            if (!phone.value.trim()) {
              showError(phone, 'Phone number is required.');
              isValid = false;
            } else if (!/^\+?[\d\s\-().]{7,20}$/.test(phone.value.trim())) {
              showError(phone, 'Enter a valid phone number.');
              isValid = false;
            }

            if (!email.value.trim()) {
              showError(email, 'Email address is required.');
              isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
              showError(email, 'Enter a valid email address.');
              isValid = false;
            }

            if (!password.value) {
              showError(password, 'Password is required.');
              isValid = false;
            } else if (password.value.length < 8) {
              showError(password, 'Password must be at least 8 characters.');
              isValid = false;
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password.value)) {
              showError(password, 'Password must include uppercase, lowercase, and a number.');
              isValid = false;
            }

            if (isValid) {
              document.querySelector('.woocommerce-form-register').submit();
            }
          });

          attachLiveClear(['reg_billing_first_name', 'reg_billing_phone', 'reg_email', 'reg_password']);
        </script>
        <?php
    }
    if ( is_wc_endpoint_url( 'order-pay' ) ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {

            function changeStripeLabel() {
                const stripeLabel = document.querySelector('label[for="payment_method_stripe"]');

                if (stripeLabel && stripeLabel.textContent.trim() !== 'Credit / Debit Card') {
                    stripeLabel.textContent = 'Credit / Debit Card';
                }
            }

            // Initial
            changeStripeLabel();

            // WooCommerce fires updated_checkout via jQuery, so a native listener can't catch it
            if (window.jQuery) {
                window.jQuery(document.body).on('updated_checkout', changeStripeLabel);
            }

        });
        </script>
        <?php
    }
    ?>
   <script>
document.addEventListener('DOMContentLoaded', function () {

    function rotateList(list) {
        const items = list.querySelectorAll('li');

        if (items.length <= 1) return;

        let index = 0;

        items.forEach((item, i) => {
            item.style.display = i === 0 ? 'list-item' : 'none';
        });

        setInterval(() => {
            items[index].style.display = 'none';
            index = (index + 1) % items.length;
            items[index].style.display = 'list-item';
        }, 3000);
    }

    document.querySelectorAll('.top_bar ul').forEach(rotateList);

});
</script>
    <?php
}
add_action('wp_footer', 'oc_wp_footer');





add_filter('comments_open', function($open, $post_id) {
    if ('post' === get_post_type($post_id)) {
        return false;
    }
    return $open;
}, 10, 2);

add_filter('pings_open', function($open, $post_id) {
    if ('post' === get_post_type($post_id)) {
        return false;
    }
    return $open;
}, 10, 2);

function cartregularprice( $cart_item, $_product, $cart_item_key, $exclude_extras = false ) {
    $line_total = (float) $cart_item['line_subtotal'];

    echo wp_kses_post( wc_price( $line_total ) );
}
function cartwasrprice($cart_item,$_product,$cart_item_key){
    if ( $_product->is_on_sale() ) : 
        $regular_price = (float) $_product->get_regular_price();
        $qty           = (int) $cart_item['quantity'];

        $regular_total = $regular_price * $qty;
    ?>
    <span style="text-decoration:line-through; font-size:12px; font-weight:500; color:#00000099 ;">
        was <span class="lsregprc" 
              data-cart-key-reg="<?php echo esc_attr($cart_item_key); ?>"
              style="font-size:12px; font-weight:500; color:#00000099 ;"
              >
            <?php echo wp_kses_post( wc_price( $regular_total ) ); ?>
        </span>
    </span>
    <?php 
    endif;

    
}

function checkoutregularprice($cart_item, $_product, $cart_item_key) {
    $line_total = $cart_item['line_total'];
    echo '<span class="oc-current-price">' .wp_kses_post( wc_price( $line_total ) ). '</span>';
}
function checkoutwasprice($cart_item,$_product,$cart_item_key){
    $regular_price = $_product->get_regular_price();
    $current_price = $_product->get_price();
    if ( $regular_price && $regular_price != $current_price ) {
        $regular_subtotal = wc_price( floatval( $regular_price ) * $cart_item['quantity'] );
        echo '<span class="oc-regular-price" >was &nbsp;' . $regular_subtotal . '</span>';
    }
}

