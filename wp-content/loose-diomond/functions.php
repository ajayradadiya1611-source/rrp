<?php

/**
 * Loose Diomond functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Loose_Diomond
 */

if (! defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

define('LD_RECAPTCHA_SITE_KEY', '6Lc2a54tAAAAAExcxKygkVz5fByttLWN1gjyEAc6');
define('LD_RECAPTCHA_SECRET_KEY', '6Lc2a54tAAAAAOvWIl2KuDjK_qkTNmpozQMKITUD');
define('LD_RECAPTCHA_MIN_SCORE', 0.5); // optional, defaults to 0.5

define('RETURN_DAYS', '7');
define('SITE_OC_NAME', 'RRP Diamond');
define('SUPPORT_EMAIL', 'support@loosegrowndiamond.com');

define('SUPPORT_MOBILE', '91 9016106290');
define('WA_SUPPORT_MOBILE', '+919016106290');
define('CONTENTSQUARE', '6fe1a3c7dd8f0');
define('ZOHOCHATBOT', 'siq335d327ea7c08a4066f85c9ff99ba220040132820e4c43ecccf727119b416690');
define('ZOHOAPIENDPOINT', 'https://api.rrpdiamonds.com/lgd/webhook/');

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function loose_diomond_setup()
{
    /* 
        * Make theme available for translation.
        * Translations can be filed in the /languages/ directory.
        * If you're building a theme based on Loose Diomond, use a find and replace
        * to change 'loose-diomond' to the name of your theme in all the template files.
        */
    load_theme_textdomain('loose-diomond', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
        * Let WordPress manage the document title.
        * By adding theme support, we declare that this theme does not use a
        * hard-coded <title> tag in the document head, and expect WordPress to
        * provide it for us.
        */
    add_theme_support('title-tag');

    /*
        * Enable support for Post Thumbnails on posts and pages.
        *
        * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
        */
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'loose-diomond'),
        )
    );

    /*
        * Switch default core markup for search form, comment form, and comments
        * to output valid HTML5.
        */
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'loose_diomond_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'loose_diomond_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function loose_diomond_content_width()
{
    $GLOBALS['content_width'] = apply_filters('loose_diomond_content_width', 640);
}
add_action('after_setup_theme', 'loose_diomond_content_width', 0);


function getShapeValueById($id, $shapes) {
    foreach ($shapes as $shape) {
        if ($shape['id'] == $id) {
            return $shape['value'];
        }
    }
    return null;
}

/**
 * Parse certificate info from type and URL/string
 *
 * @param mixed $certi_type
 * @param string $certificate
 * @return array ['id' => string, 'type' => string]
 */
function ls_parse_certificate($certi_type, $certificate) {
    $certi_id = '';
    $certi = '';

    if ( $certi_type == 3 ) {
        $a = explode("?certificate_id=", $certificate);
        $certi_id = $a[1] ?? '';
        $certi = 'GCAL';

    } elseif ( $certi_type == 2 ) {
        $a = explode("reportno=", $certificate);
        //print_r($a);
        $certi_id = $a[1] ?? '';
        $certi = 'GIA';

    } elseif ( $certi_type == 1 )  {
        $a = explode("?r=", $certificate);
        $certi_id = $a[1] ?? '';
        $certi = 'IGI';
    }

    return array('id' => $certi_id, 'type' => $certi);
}
/**
 * Enqueue scripts and styles.
 */
function loose_diomond_scripts()
{
    wp_enqueue_style(
        'bootstrap',
        get_template_directory_uri() . '/assets/css/bootstrap.min.css',
        [],
        '5.3.0'
    );

    wp_enqueue_style(
        'aj-style-scss',
        get_template_directory_uri() . '/out/theme/css/styles.css',
        ['bootstrap'],
        '1.0.0'
    );

    wp_enqueue_style(
        'loose-diomond-style',
        get_stylesheet_uri(),
        array(),
        _S_VERSION
    );

    wp_style_add_data('loose-diomond-style', 'rtl', 'replace');

    wp_enqueue_style(
        'intlTelInput-css',
        get_template_directory_uri() . '/assets/css/intlTelInput.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'intlTelInput-js',
        get_template_directory_uri() . '/assets/js/intlTelInput.js',
        ['jquery'],
        '1.0',
        true
    );

}
add_action('wp_enqueue_scripts', 'loose_diomond_scripts');


/**
 * Load selected CSS files asynchronously.
 */
add_filter('style_loader_tag', function ($html, $handle, $href) {

    if (is_admin()) {
        return $html;
    }

    $async_handles = [
        'intlTelInput-css',
        'custom_splitit_checkout_page_css',
        'loose-diomond-style',
    ];

    if (in_array($handle, $async_handles, true)) {

        $html = str_replace(
            "media='all'",
            "media='print' onload=\"this.media='all';this.onload=null;\"",
            $html
        );

        $html .= '<noscript>
            <link rel="stylesheet" href="' . esc_url($href) . '">
        </noscript>' . "\n";
    }

    return $html;

}, 10, 3);

add_action( 'wp_enqueue_scripts', function () {
    if ( is_admin() ) return;

    wp_scripts()->add_data( 'jquery', 'group', 1 );
    wp_scripts()->add_data( 'jquery-core', 'group', 1 );
    wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
}, 1 );

// ── Defer jQuery (add BELOW the function) ──────────────────────────────
/*add_filter('script_loader_tag', function($tag, $handle) {
    $defer = ['jquery-core', 'jquery-migrate', 'intlTelInput-js'];
    if (in_array($handle, $defer, true) && !str_contains($tag, 'defer')) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);
*/

/**
 * Print theme data to window for JS (template directory URL, ajax URL).
 */
function loose_diomond_print_theme_data()
{
    $data = array(
        'templateUrl' => get_template_directory_uri(),
        'ajaxUrl'     => admin_url('admin-ajax.php'),
        'RETURN_DAYS'=> RETURN_DAYS
    );
?>
    <script>
        window.themeData = <?php echo wp_json_encode($data); ?>;
    </script>
<?php
}
add_action('wp_head', 'loose_diomond_print_theme_data');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

function start_session() {
    if (!session_id()) {
        session_start();
    }
    
}
add_action('init', 'start_session', 1);

/*
 * Include inventory API helper if present.
 */
add_action( 'after_setup_theme', 'yourtheme_setup' );
function yourtheme_setup() {
    add_theme_support( 'woocommerce' );
}

include('inc/global-var.php');
include('inc/json-var-retail.php');
include('inc/retail-json.php');
include('inc/comman-function.php');
include('inc/ls-custom-myaccount.php');
include('inc/ls_pcat_custom_form.php');
include('inc/static-html.php');
include('inc/ls-common-sections.php');
include('inc/ls-category-product-get-api.php');
include('inc/contact-us-form-api.php');
include('inc/checkout-login-popup.php');
include('inc/newsletter.php');
include('inc/retail.php');
include('inc/ls-unit-option.php');
include('inc/ls-variation-guides.php');
include('inc/product-materials.php');

include('inc/custom-payment-gateway.php');
include('inc/admin-contact-form.php');
include('inc-admin/admin-page.php');
include('inc-admin/order-tracking.php');

include('inc/blog-shortcode.php');
require_once get_template_directory() . '/components/inventory-listing/index.php';
require_once get_template_directory() . '/components/cart-page/index.php';


include('plain-components/comman_option.php');
include('plain-components/custom_order.php');
include('plain-components/hide_order_meta.php');
include('plain-components/mailhook.php');
include('plain-components/cart.php');
include('plain-components/mobile-checkout-order-summary.php');
include('js-php/checkout.php');
include('js-php/slick-slider.php');



add_action('woocommerce_order_status_cancelled', 'trigger_customer_cancelled_email', 20, 1);

function trigger_customer_cancelled_email($order_id) {

    if (!$order_id) return;

    $mailer = WC()->mailer();
    $emails = $mailer->get_emails();

    if (!empty($emails['WC_Email_Customer_Cancelled_Order'])) {
        $emails['WC_Email_Customer_Cancelled_Order']->trigger($order_id);
    }
}


/**
 * AJAX handler for adding products to cart
 * Handles both regular and ring builder setting products
 */
add_action('wp_ajax_custom_ajax_add_to_cart', 'custom_ajax_add_to_cart_handler');
add_action('wp_ajax_nopriv_custom_ajax_add_to_cart', 'custom_ajax_add_to_cart_handler');

function custom_ajax_add_to_cart_handler() {
    // Parse the serialized form data
    parse_str($_POST['form_data'], $form_data);

    // Extract and sanitize form inputs
    $product_id = isset($form_data['product_id']) ? absint($form_data['product_id']) : 0;
    $quantity = isset($form_data['quantity']) ? absint($form_data['quantity']) : 1;
    $variation_id = isset($form_data['variation_id']) ? absint($form_data['variation_id']) : 0;

    // Validate product ID
    if (!$product_id) {
        wp_send_json([
            'message' => 'Invalid product.',
            'success' => false
        ]);
        wp_die();
    }

    // Validate quantity
    if ($quantity < 1) {
        wp_send_json([
            'message' => 'Quantity must be at least 1.',
            'success' => false
        ]);
        wp_die();
    }

    // Handle variable products
    $variation_attributes = [];
    if ($variation_id) {
        foreach ($form_data as $key => $value) {
            if (strpos($key, 'attribute_') === 0 && $key !== 'attribute_pa_unit') {
                $variation_attributes[$key] = sanitize_text_field($value);
            }
        }

        // Validate that variation attributes are present
        if (empty($variation_attributes)) {
            wp_send_json([
                'message' => 'Please select product options.',
                'success' => false
            ]);
            wp_die();
        }
    }

    // Unit option (carat / pieces) is stored as cart item data, not as a variation attribute
    if (isset($form_data['attribute_pa_unit'])) {
        $_REQUEST['attribute_pa_unit'] = sanitize_text_field($form_data['attribute_pa_unit']);
    }

    // Add product to cart
    try {
        $cart_item_data = ls_prepare_cart_item_data($cart_item_data, $product_id);
        if ($variation_id) {
            $cart_item_key = WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                $variation_id,
                $variation_attributes,
                $cart_item_data
            );
        } else {
            $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, null, null, $cart_item_data);
        }

        if ($cart_item_key) {
            wp_send_json([
                'message' => 'Product added to cart successfully!',
                'success' => true,
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', []),
                'redirect_url' => wc_get_cart_url(),
            ]);
        } else {
            wp_send_json([
                'message' => 'Unable to add product to cart. Please try again.',
                'success' => false
            ]);
        }
    } catch (Exception $e) {
        wp_send_json([
            'message' => 'Error: ' . $e->getMessage(),
            'success' => false
        ]);
    }

    wp_die();
}




add_action('wp_footer', 'ls_form_submission_script');
function ls_form_submission_script() {
    if (is_page_template('page-feedback-form.php') || is_page('feedback-form')) {
        ?>
        
        <?php
    }
    
    ?>
   <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        if (document.body.classList.contains('woocommerce-checkout')) return;
        if (typeof window.intlTelInput !== 'function') return;

        document.querySelectorAll('input[type="tel"]').forEach(function (input) {

            // Skip inputs already initialized elsewhere
            if (window.intlTelInputGlobals && intlTelInputGlobals.getInstance(input)) return;

            var existingValue = input.value.trim();
            var originalName  = input.getAttribute('name');

            // Hidden input takes the real name, visible one stops submitting
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            if (originalName) hiddenInput.name = originalName;
            input.insertAdjacentElement('afterend', hiddenInput);
            input.removeAttribute('name');

            var iti = window.intlTelInput(input, {
                initialCountry: existingValue ? "" : "auto",
                separateDialCode: false,
                geoIpLookup: function (callback) {
                    if (existingValue) return;
                    fetch("https://ipinfo.io/json")
                        .then(function (res) { return res.json(); })
                        .then(function (data) { callback(data && data.country ? data.country : "in"); })
                        .catch(function () { callback("in"); });
                },
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });

            // Build full number from the local value
            function getFullNumber() {
                var localVal = input.value.trim();
                if (!localVal) return '';
                var data = iti.getSelectedCountryData();
                var dialCode = data && data.dialCode ? "+" + data.dialCode : "";
                return localVal.charAt(0) === "+" ? localVal : dialCode + localVal;
            }

            function sync() {
                hiddenInput.value = getFullNumber();
            }

            // Existing value: show local part only, keep full number in hidden input
            if (existingValue) {
                var normalized = existingValue.charAt(0) === "+" ? existingValue : "+" + existingValue;
                iti.setNumber(normalized);

                var data = iti.getSelectedCountryData();
                if (data && data.dialCode) {
                    input.value = normalized.replace("+" + data.dialCode, "").trim();
                }
                hiddenInput.value = normalized;
            }

            // 'input' covers typing, paste and autofill
            input.addEventListener('input', sync);
            input.addEventListener('countrychange', sync);

            input.addEventListener('blur', function () {
                input.value = input.value.trim();
                sync();
            });
        });
    });
    </script>
    <?php
}

add_action('wp_footer', 'ls_custom_script');
function ls_custom_script() {
    ?>
        <script>
            // HEADER MENU JS
        document.addEventListener('DOMContentLoaded', function () {

            // Sticky header on scroll
            var header = document.querySelector('header');
            function onScroll() {
                if (!header) return;
                header.classList.toggle('nav-fixed', window.scrollY >= 10);
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // Diamond shape chart
            var shapes = document.querySelectorAll('.diamond_chart div');
            shapes.forEach(function (shape) {
                shape.addEventListener('click', function () {
                    var currVal = this.dataset.shape || '';
                    var descBox = currVal.replace(' ', '');

                    shapes.forEach(function (s) { s.classList.remove('selected'); });
                    this.classList.add('selected');

                    document.querySelectorAll('.s_desc').forEach(function (d) {
                        d.classList.remove('active');
                    });
                    var target = document.getElementById(descBox + '_cut');
                    if (target) target.classList.add('animate-zoom', 'active');
                });
            });

            // Delegated click handlers
            document.addEventListener('click', function (e) {

                // Crypto payment copy text
                var lsCopy = e.target.closest('.lscopy_txt');
                if (lsCopy) {
                    var copied = lsCopy.querySelector('.ls_copied');
                    if (copied) {
                        copied.classList.add('active');
                        setTimeout(function () { copied.classList.remove('active'); }, 2000);
                    }
                }
            });
        });


        // Fade helpers (replacement for jQuery fadeIn / fadeOut)
        function lsFadeIn(el, duration) {
            duration = duration || 400;
            el.style.opacity = 0;
            el.style.display = 'block';
            el.style.transition = 'opacity ' + duration + 'ms';
            requestAnimationFrame(function () { el.style.opacity = 1; });
        }

        function lsFadeOut(el, duration) {
            duration = duration || 400;
            el.style.transition = 'opacity ' + duration + 'ms';
            el.style.opacity = 0;
            setTimeout(function () { el.style.display = 'none'; }, duration);
        }


        // Copy to clipboard (works with existing onclick="copyToClipboard('#id')")
        function copyToClipboard(element, evt) {
            evt = evt || window.event;

            var source = typeof element === 'string' ? document.querySelector(element) : element;
            if (!source) return;
            var text = source.textContent.trim();

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                var temp = document.createElement('input');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                temp.remove();
            }

            // Show "Copied" text, hide after 2 seconds
            if (evt && evt.currentTarget && evt.currentTarget.querySelector) {
                var copiedText = evt.currentTarget.querySelector('.ls_copied');
                if (copiedText) {
                    lsFadeIn(copiedText);
                    setTimeout(function () { lsFadeOut(copiedText); }, 2000);
                }
            }
        }
        </script>
    <?php
}

function wooc_extra_register_fields() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_first_name"><?php _e( 'Name', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
    <?php
}

add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }

    if (empty(trim($_POST['billing_first_name'])) || !preg_match('/^(?![\s.]+$)[a-zA-Z-\s.]*$/', $_POST['billing_first_name'])) {
        $validation_errors->add( 'billing_first_name_error', __( 'Only alphabets, space and - are allowed in <strong>Billing First Name</strong>.', 'woocommerce' ) );
    }

    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone number is required!.', 'woocommerce' ) );
    }

  
    return $validation_errors;

}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );

/**
* Below code save extra fields.
*/
function wooc_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['shipping_phone'] ) ) {
        // Phone input filed which is used in WooCommerce
        update_user_meta( $customer_id, 'shipping_phone', sanitize_text_field( $_POST['shipping_phone'] ) );
    }

    if ( isset( $_POST['billing_phone'] ) ) {
        // Phone input filed which is used in WooCommerce
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
        update_user_meta( $customer_id, 'shipping_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }

    if ( isset( $_POST['billing_first_name'] ) ) {
        //First name field which is by default
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );

        // First name field which is used in WooCommerce
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }


}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

add_action('wp_ajax_custom_update_cart_qty', 'custom_update_cart_qty');
add_action('wp_ajax_nopriv_custom_update_cart_qty', 'custom_update_cart_qty');
function custom_update_cart_qty() {

    if ( ! isset($_POST['cart_item_key']) || ! isset($_POST['qty']) ) {
        wp_send_json_error();
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $qty           = intval($_POST['qty']);

    WC()->cart->set_quantity($cart_item_key, $qty, true);

    WC()->cart->calculate_totals();
    
    // 🔹 IMPORTANT: revalidate coupons
    WC()->cart->check_cart_coupons();

    ob_start();
    wc_print_notices();
    $notices = ob_get_clean();

        // Build item totals array
    $item_totals = [];
    $item_regular_totals = [];

    foreach ( WC()->cart->get_cart() as $key => $item ) {
        $product = $item['data'];
        $qty     = $item['quantity'];

        $item_totals[$key] = wc_price( $item['line_total'] ); // sale price × qty

        // ← ADD THIS - regular price × qty
        if ( $product->is_on_sale() ) {
            $regular_price = (float) $product->get_regular_price();
            $item_regular_totals[$key] = number_format( $regular_price * $qty, 2 );
        } else {
            $item_regular_totals[$key] = null; // not on sale, hide it
        }
    }

    wp_send_json_success([
        'notices'             => $notices,
        'cart_total'          => WC()->cart->get_cart_total(),
        'cart_subtotal'       => WC()->cart->get_cart_subtotal(),
        'item_totals'         => $item_totals,
        'item_regular_totals' => $item_regular_totals, // ← ADD THIS
    ]);
}

/**
 * Remove purchased products from wishlist cookie after successful order
 *
 * This inspects the order items and removes matching entries from the
 * `ld_wishlist` cookie (used by the theme's wishlist JS). It attempts to
 * match by product id and variation id where applicable.
 *
 * @param int $order_id
 */
function ls_clear_wishlist_on_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $items = $order->get_items();
    if ( empty( $items ) ) {
        return;
    }

    $cookie_name = 'ld_wishlist';
    $raw = isset( $_COOKIE[ $cookie_name ] ) ? wp_unslash( $_COOKIE[ $cookie_name ] ) : '';
    if ( ! $raw ) {
        return;
    }

    $decoded = json_decode( $raw, true );
    if ( ! is_array( $decoded ) ) {
        return;
    }

    // Build list of purchased product/variation pairs
    $purchased = array();
    foreach ( $items as $item ) {
        $purchased[] = array(
            'product_id'   => $item->get_product_id(),
            'variation_id' => $item->get_variation_id(),
        );
    }

    // Filter wishlist: keep entries that do NOT match any purchased item
    $filtered = array_filter( $decoded, function ( $entry ) use ( $purchased ) {
        if ( ! is_array( $entry ) ) {
            return true;
        }

        $eid = isset( $entry['id'] ) ? (string) $entry['id'] : '';
        $evar = '';
        if ( isset( $entry['variationId'] ) ) {
            $evar = (string) $entry['variationId'];
        } elseif ( isset( $entry['variation_id'] ) ) {
            $evar = (string) $entry['variation_id'];
        }

        foreach ( $purchased as $p ) {
            $pid = isset( $p['product_id'] ) ? (string) $p['product_id'] : '';
            $vid = isset( $p['variation_id'] ) ? (string) $p['variation_id'] : '';

            if ( $evar !== '' ) {
                // If wishlist entry has a variation, require both product id and variation to match
                if ( $eid !== '' && $eid === $pid && $evar === $vid ) {
                    return false; // remove
                }
            } else {
                // No variation on wishlist entry: remove if id matches product or variation id
                if ( ( $eid !== '' && ( $eid === $pid || ( $vid !== '' && $eid === $vid ) ) ) ) {
                    return false; // remove
                }
            }
        }

        return true;
    } );

    $filtered = array_values( $filtered );

    if ( empty( $filtered ) ) {
        // Clear cookie if nothing left
        setcookie( $cookie_name, '', time() - 3600, '/' );
        // Also unset in $_COOKIE for this request
        if ( isset( $_COOKIE[ $cookie_name ] ) ) {
            unset( $_COOKIE[ $cookie_name ] );
        }
    } else {
        // Write updated cookie for one year
        $expire = defined( 'YEAR_IN_SECONDS' ) ? time() + YEAR_IN_SECONDS : time() + 365 * 24 * 60 * 60;
        setcookie( $cookie_name, wp_json_encode( $filtered ), $expire, '/' );
        // Update $_COOKIE for this request
        $_COOKIE[ $cookie_name ] = wp_json_encode( $filtered );
    }
}
add_action( 'oc_woocommerce_thankyou', 'ls_clear_wishlist_on_order', 10, 1 );

add_filter('woocommerce_add_cart_item_data', 'add_product_meta_to_cart', 10, 3);

function add_product_meta_to_cart($cart_item_data, $product_id, $variation_id) {

    // IMPORTANT: remove debug
   

    $cart_item_data = ls_prepare_cart_item_data($cart_item_data, $product_id);

    return $cart_item_data;
}

function ls_prepare_cart_item_data($cart_item_data, $product_id) {

    $product = wc_get_product($product_id);

    // Category आधारित flags
    $cart_item_data['is_readyToShipProduct'] = has_term('ready-to-ship', 'product_cat', $product_id);

    $is_ring_product = false;
    // Ring product
     if (has_term('wedding-ring', 'product_cat', $product_id)) {
        $is_ring_product = true;
    }
    $cart_item_data['is_ring_product'] = $is_ring_product;
    /*print_r(has_term(['wedding-rings', 'engagement-rings'], 'product_cat', $product_id));
    exit; */
    // Custom note
    if (isset($_POST['custom_note'])) {
        $cart_item_data['custom_note'] = sanitize_text_field(
            wp_unslash($_POST['custom_note'])
        );
    }

    // Quantity box logic
    if (
        $cart_item_data['is_readyToShipProduct'] == true ||
        has_term('custom', 'product_cat', $product_id)
    ) {
        $cart_item_data['is_qty_box'] = false;
    } else {
        $cart_item_data['is_qty_box'] = true;
    }

    return $cart_item_data;
}

add_filter( 'woocommerce_get_item_data', 'ls_fn_display_ringsize', 10, 2 );
function ls_fn_display_ringsize( $item_data, $cart_item ) {

    $product = $cart_item['data'];

     if ( isset(  $cart_item['diamond_data']) ) {
        $record= $cart_item['diamond_data'];
        //print_r($record);

        $cert_info = ls_parse_certificate($record->certificate_type ?? '', $record->certificate ?? '');
        $certi_id = $cert_info['id'];
        $certi = $cert_info['type'];

        // ✅ Save to order item meta
        if ( ! empty( $certi ) ) {
            $item_data[] = array(
                'key'  => 'Certificate Type',
                'value' => $certi
            );
        }

        if ( ! empty( $certi_id ) ) {
            $item_data[] = array(
                'key'  => 'Certificate ID',
                'value' => sanitize_text_field( $certi_id )
            );
            
        }
    }
    if (isset($cart_item['custom_note'])) {
        $item_data[] = array(
            'key'  => 'Description',
            'value' => $cart_item['custom_note']
        );
    }
    return $item_data;
}

add_filter('woocommerce_cart_item_name', 'custom_cart_item_name', 10, 3);

function custom_cart_item_name($product_name, $cart_item, $cart_item_key) {

    // Get product object
    $product = $cart_item['data'];

    if (!$product) {
        return $product_name;
    }

    // Dynamic values
    $product_id = $product->get_id();
    $sku        = $product->get_sku();
    $title      = $product->get_name();

    $custom_title = $product_name;
    $shipping = isset($cart_item['diamond_data']->shipping)
                        ? intval($cart_item['diamond_data']->shipping)
                        : null;
    $express_shipping_img ='';
                if($shipping==1){
                    $express_shipping_img ='&nbsp;<img class="shippin-icon" src="'.get_template_directory_uri().'/assets/image/header/ready-to-ship.svg"/>';
                } 

    $is_overnight_img ='';
                if($shipping==2){
                    $is_overnight_img ='&nbsp;<img class="shippin-icon" src="'.get_template_directory_uri().'/aj_custom/inventory/space-rocket.svg"/>';
                } 

    return $custom_title.$express_shipping_img.$is_overnight_img;
}






add_filter('woocommerce_order_item_name', 'custom_order_item_name_with_diamond', 1000000, 2);

function custom_order_item_name_with_diamond($item_name, $item) {
    $diamond = $item->get_meta('_diamond_data');
    $express_shipping_img ='';
    $is_overnight_img ='';
    if (!empty($diamond)) {

        // Since it's an object (stdClass)
        $shape = $diamond->shape ?? '';
        $price = $diamond->price ?? '';

        $shipping = isset($diamond->shipping)
    ? intval($diamond->shipping)
    : 0;
    
                if($shipping==1){
                    $express_shipping_img ='&nbsp;<img class="shippin-icon" src="'.get_template_directory_uri().'/aj_custom/mail/ready-to-ship.svg"  width="15"/>';
                } 
                if($shipping==2){
                    $is_overnight_img ='&nbsp;<img class="shippin-icon" src="'.get_template_directory_uri().'/aj_custom/mail/space-rocket.svg" width="15"/>';
                } 
    }

    return $item_name.$express_shipping_img.$is_overnight_img;
}
add_filter('woocommerce_admin_order_preview_line_items', 'lgd_modify_preview_item_name', 10, 2);
function lgd_modify_preview_item_name($line_items, $order) {
    foreach ($line_items as $item_id => $item) {
        $name_with_icons = custom_order_item_name_with_diamond($item->get_name(), $item);
        $item->set_name($name_with_icons);
    }
    return $line_items;
}
add_action( 'woocommerce_order_item_meta_end', 'display_diamond_data', 10, 4 );

function display_diamond_data( $item_id, $item, $order, $plain_text ) {
    //echo "swww";
    $diamond_data = $item->get_meta( '_diamond_data' );
    //print_r($diamond_data);
    if ( ! empty( $diamond_data ) ) {

        if($diamond_data->certificate_type==0 || $diamond_data->certificate_type==4){
            echo '<div class="order-custom-certi-text">This diamond is sold without a certificate (no certificate included).</div>';
        }
    }
    
}

add_action( 'woocommerce_checkout_create_order_line_item', 'ls_save_all_custom_data_to_order_item', 10, 4 );


function ls_save_all_custom_data_to_order_item( $item, $cart_item_key, $values, $order ) {
   /* echo "<pre>";
    print_r($values); 
    exit;*/
    global $wpdb;
     $product_id = $item->get_product_id();
     if ( isset( $values['diamond_data'] ) ) {

        $item->add_meta_data('_diamond_data', $values['diamond_data'], true);

        

        $record = $values['diamond_data'];
       // exit;
        //print_r($record);
        $cert_info = ls_parse_certificate($record->certificate_type ?? '', $record->certificate ?? '');
        $certi_id = $cert_info['id'];
        $certi = $cert_info['type'];
        /*print_r($record);
        print_r($certi);
        print_r($certi_id);
        exit;*/

        // ✅ Save to order item meta
        if ( ! empty( $certi ) ) {
            $item->add_meta_data( 'Certificate Type', $certi, true );
        }

        if ( ! empty( $certi_id ) ) {
            $item->add_meta_data( 'Certificate ID', sanitize_text_field( $certi_id ), true );
            
        }
        if ( ! empty( $certi_id ) ) {
            $item->add_meta_data( '_certificate_url', sanitize_text_field($certificate ), true );
            
        }
    }

    if (isset($values['custom_note'])) {
        $item->add_meta_data('Description', $values['custom_note'], true);
    }
    add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values, $order) {
        $product = $item->get_product();
        if ($product) {
            $item->add_meta_data('_sku_snapshot', $product->get_sku(), true);
        }
    }, 10, 4);
}

function ls_change_product_price_display( $price, $product  ) {
    if ( has_term( 'melee-diamond', 'product_tag', $product->id ) ) {

        $price .= ' / Per Diamond';
    }    
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'ls_change_product_price_display',100,2 );

add_filter( 'wc_add_to_cart_message_html', '__return_false' );
add_filter( 'woocommerce_cart_item_removed_notice', '__return_false' );

add_action( 'template_redirect', function() {
    if ( is_cart() && isset($_GET['removed_item']) ) {
        wc_clear_notices();
    }
});

// Remove payment
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

// Remove shipping table row (shipping methods)
remove_action( 'woocommerce_review_order_before_payment', 'woocommerce_checkout_shipping', 20 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

add_action('wp_ajax_wishlist_move_to_cart', 'wishlist_move_to_cart');
add_action('wp_ajax_nopriv_wishlist_move_to_cart', 'wishlist_move_to_cart');

function wishlist_move_to_cart() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error();
    }

    $product_id = intval($_POST['product_id']);

    if (!$product_id) {
        wp_send_json_error();
    }

    // support optional variation
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $variation = array();
    if (!empty($_POST['variation_attrs'])) {
        $raw = wp_unslash( $_POST['variation_attrs'] );
        $decoded = json_decode( $raw, true );
        if (is_array($decoded)) {
            // ensure string values
            foreach ($decoded as $k => $v) {
                $variation[$k] = is_array($v) ? $v : strval($v);
            }
        }
    }
    $cart_item_data = array();
    $cart_item_data = ls_prepare_cart_item_data($cart_item_data, $product_id);
    if ($variation_id) { 
        $added = WC()->cart->add_to_cart($product_id, 1, $variation_id, $variation,$cart_item_data);
    } else {
        $added = WC()->cart->add_to_cart($product_id, 1,null, null,  $cart_item_data);
    }

    if ($added) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Unable to add to cart'));
    }
}



add_action('wp_ajax_custom_apply_coupon', 'custom_apply_coupon_handler');
add_action('wp_ajax_nopriv_custom_apply_coupon', 'custom_apply_coupon_handler');

function custom_apply_coupon_handler() {
    check_ajax_referer('custom_coupon_nonce', 'security');

    if ( null === WC()->cart ) {
        wc_load_cart();
    }

    if ( ! WC()->session ) {
        WC()->initialize_session();
    }

    $coupon_code = isset($_POST['coupon_code'])
        ? sanitize_text_field(wp_unslash($_POST['coupon_code']))
        : '';

    if ( empty($coupon_code) ) {
        wp_send_json_error(['message' => 'Please enter a coupon code.']);
    }

    if ( WC()->cart->has_discount($coupon_code) ) {
        wp_send_json_error(['message' => 'Coupon already applied.']);
    }

    $coupon = new WC_Coupon($coupon_code);

    if ( ! $coupon->get_id() ) {
        wp_send_json_error([
            'message' => sprintf('Coupon "%s" does not exist.', esc_html($coupon_code)),
        ]);
    }

    wc_clear_notices();

    $result  = WC()->cart->apply_coupon($coupon_code);
    $notices = wc_get_notices();
    //print_r($notices);

    // ❌ ERROR CASE
    if ( ! empty($notices['error']) ) {

        $messages = array_map(function($notice) {
            return is_array($notice)
                ? wp_strip_all_tags($notice['notice'])
                : $notice;
        }, $notices['error']);

        wc_clear_notices();

        wp_send_json_error([
            'message' => implode(' ', $messages)
        ]);
    }

    // ✅ SUCCESS CASE (THIS WAS MISSING)
    if ( $result ) {

        $success_notices = $notices['success'] ?? [];

        $messages = array_map(function($notice) {
            return is_array($notice)
                ? wp_strip_all_tags($notice['notice'])
                : $notice;
        }, $success_notices);

        wc_clear_notices();

        $message = ! empty($messages)
            ? implode(' ', $messages)
            : 'Coupon applied successfully!';

        wp_send_json_success([
            'message' => $message
        ]);
    }

    // ⚠️ FALLBACK (rare edge case)
    wc_clear_notices();

    wp_send_json_error([
        'message' => 'Could not apply coupon. Please try again.'
    ]);
}

/*add_filter('woocommerce_coupon_message', '__return_empty_string');
add_filter('woocommerce_coupon_error', '__return_empty_string');*/



add_action('oc_woocommerce_thankyou', function ($order_id) {

    // Skip logged-in users
    if (is_user_logged_in()) {
        return;
    }

    $order = wc_get_order($order_id);

    if (!$order || $order->get_customer_id()) {
        return;
    }

    // Find existing user by billing email
    $user = get_user_by('email', $order->get_billing_email());

    if ($user) {

        // Attach order to existing account
        $order->set_customer_id($user->ID);
        $order->save();

    }

}); 
add_action('woocommerce_checkout_process', function() {
    if (!is_user_logged_in() && !empty($_POST['billing_email'])) {

        $email = sanitize_email($_POST['billing_email']);
        $user  = get_user_by('email', $email);

        if ($user) {
            // Optional: log in existing user
           // wp_set_current_user($user->ID);
           // wp_set_auth_cookie($user->ID);
        } else {
            // Force WooCommerce to create account
            $_POST['createaccount'] = 1;

            // Generate password to avoid error
            $_POST['account_password'] = wp_generate_password();
        }
    }

    if (!empty($_POST['same_as_shipping_address']) && $_POST['same_as_shipping_address'] == 'on') {

        // List of address fields
        $fields = [
            'first_name',
            'last_name',
            'country',
            'address_1',
            'address_2',
            'city',
            'state',
            'postcode',
            'phone'
        ];

        foreach ($fields as $field) {
            if (!empty($_POST['shipping_' . $field])) {
                $_POST['billing_' . $field] = sanitize_text_field($_POST['shipping_' . $field]);
            }
        }
    }
});

// Remove password field from checkout
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    if ( isset( $fields['account']['account_password'] ) ) {
       // unset( $fields['account']['account_password'] );
    }
     // ✅ Add Heading BEFORE Shipping Email
    $fields['shipping']['contact_details_heading'] = array(
        'type'     => 'text', // special type
        'label'    => 'CONTACT DETAILS',
        'class'    => array('form-row-wide custom-shipping-label-contact-details'),
        'priority' => 108, // 👈 before email (110)
    );
     // Add Shipping Phone field
    $fields['shipping']['shipping_phone'] = array(
        'label'       => __('Phone', 'woocommerce'),
        'placeholder' => __('Enter phone number', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'tel',
        'priority'    => 110,
    );

    $fields['shipping']['shipping_email'] = array(
        'label'       => 'Email',
        'placeholder' => 'Enter shipping email',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'email',
        'priority'    => 109,
    );
   
    return $fields;
});


add_filter( 'woocommerce_checkout_fields' , 'ls_priority_woocommerce_checkout_fields' );
function ls_priority_woocommerce_checkout_fields( $fields ) {

    unset($fields['shipping']['shipping_company']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_phone']);
    /* $fields['shipping']['shipping_address_1']['priority'] = 40;
     $fields['shipping']['shipping_address_2']['priority'] = 50;
     $fields['shipping']['shipping_country']['priority'] = 65;
     $fields['shipping']['shipping_state']['priority'] = 70;
     $fields['shipping']['shipping_city']['priority'] = 80;*/
     //$fields['shipping']['shipping_postcode']['label'] = 'ZIP Code';
     $fields['billing']['billing_postcode']['label'] = 'ZIP Code';
    $fields['billing']['billing_address_1']['priority'] = 40;
    $fields['billing']['billing_address_2']['priority'] = 50;
    $fields['billing']['billing_country']['priority'] = 60;
    $fields['billing']['billing_state']['priority'] = 70;
    $fields['billing']['billing_city']['priority'] = 80;
    $fields['shipping']['shipping_address_1']['priority'] = 40;
    $fields['shipping']['shipping_address_2']['priority'] = 50;
    $fields['shipping']['shipping_country']['priority'] = 60;
    $fields['shipping']['shipping_state']['priority'] = 70;
    $fields['shipping']['shipping_city']['priority'] = 80;

return $fields;
}

add_filter( 'woocommerce_default_address_fields' , 'ls_change_checkout_field_name', 9999 );
function ls_change_checkout_field_name( $fields ) {

    $fields['address_1']['label'] = 'Address Line 1';
    $fields['address_2']['label'] = 'Address Line 2';
    $fields['country']['label'] = 'Country';
    /*$fields['state']['priority'] = 70;
    $fields['city']['priority'] = 80;*/
    $fields['address_2']['placeholder'] = 'Apartment, suite, unit, etc.';
    $fields['city']['label'] = 'City';
    $fields['postcode']['label'] = 'ZIP Code';
    //$fields['postcode']['label'] = 'ZIP Code';

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'custom_order_comments_placeholder');

function custom_order_comments_placeholder($fields) {
    $fields['order']['order_comments']['placeholder'] = 'Special Instructions or Additional Notes';
    return $fields;
}

add_filter( 'woocommerce_get_script_data', function( $params, $handle ) {

    if ( 'wc-address-i18n' !== $handle || empty( $params['locale'] ) ) {
        return $params;
    }

    $locale = json_decode( html_entity_decode( $params['locale'] ), true );

    if ( ! is_array( $locale ) ) {
        return $params;
    }

    foreach ( $locale as &$country_locale ) {

        // Address Line 1
        $country_locale['address_1']['label']    = 'Address Line 1';
        $country_locale['address_1']['priority'] = 40;

        // Address Line 2
        $country_locale['address_2']['label']       = 'Address Line 2';
        $country_locale['address_2']['placeholder'] = 'Apartment, suite, unit, etc.';
        $country_locale['address_2']['priority']    = 50;

        // Country
        $country_locale['country']['label']    = 'Country';
        $country_locale['country']['priority'] = 60;

        // State
        $country_locale['state']['priority'] = 70;

        // City
        $country_locale['city']['label']    = 'City';
        $country_locale['city']['priority'] = 80;

        // ZIP Code
        $country_locale['postcode']['label']    = 'ZIP Code';
        $country_locale['postcode']['priority'] = 90;

        // Contact Details Heading
        $country_locale['contact_details_heading']['label']    = 'CONTACT DETAILS';
        $country_locale['contact_details_heading']['type']     = 'heading';
        $country_locale['contact_details_heading']['class']    = array( 'form-row-wide', 'custom-shipping-label-contact-details' );
        $country_locale['contact_details_heading']['hidden']   = false;
        $country_locale['contact_details_heading']['priority'] = 108;

        // Email
        $country_locale['email']['label']    = 'Email';
        $country_locale['email']['priority'] = 109;

        // Phone
        $country_locale['phone']['label']    = 'Phone';
        $country_locale['phone']['priority'] = 110;

        // Hide company field
        $country_locale['company']['hidden'] = true;
    }

    $params['locale'] = wp_json_encode( $locale );
    return $params;

}, 10, 2 );

/**
 * Get cart item meta as a raw array.
 *
 * @param array $cart_item
 * @return array [ ['key' => '', 'value' => '', 'display' => ''], ... ]
 */
function get_cart_item_data_array( $cart_item,$is_cart=false ) {
    /*echo "<pre>";
    print_r($cart_item);
    echo "</pre>";*/
    $item_data = [];

    // Variation attributes
    if ( ! empty( $cart_item['variation'] ) ) {
        foreach ( $cart_item['variation'] as $name => $value ) {
            $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

            if ( taxonomy_exists( $taxonomy ) ) {
                $term = get_term_by( 'slug', $value, $taxonomy );
                $item_data[] = [
                    'key'     => wc_attribute_label( $taxonomy ),
                    'value'   => $term && ! is_wp_error( $term ) ? $term->name : $value,
                    'display' => '',
                ];
            } elseif ( ! empty( $value ) ) {
                $item_data[] = [
                    'key'     => wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] ),
                    'value'   => $value,
                    'display' => '',
                ];
            }
        }
    }

    // Other meta (plugins, custom fields, etc.)
    $other_data = apply_filters( 'woocommerce_get_item_data', [], $cart_item );
    if ($is_cart === true) {

        // Optional: reindex array
        $other_data = array_values($other_data);

        
    }
    foreach ( $other_data as $data ) {
        $item_data[] = [
            'key'     => $data['key'],
            'value'   => wp_kses_post( $data['value'] ),
            'display' => ! empty( $data['display'] ) ? wp_kses_post( $data['display'] ) : '',
        ];
    }
   // print_r($item_data);

    return $item_data;
}


add_action( 'wp_ajax_oc_remove_coupon',        'custom_remove_coupon_handler' );
add_action( 'wp_ajax_nopriv_oc_remove_coupon', 'custom_remove_coupon_handler' );

function custom_remove_coupon_handler() {
    check_ajax_referer( 'remove_coupon', 'security' );

    $coupon_code = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : '';

    if ( empty( $coupon_code ) ) {
        wp_send_json_error( array( 'message' => 'No coupon code provided.' ) );
    }

    if ( ! WC()->cart->has_discount( $coupon_code ) ) {
        wp_send_json_error( array( 'message' => 'Coupon not found in cart.' ) );
    }

    WC()->cart->remove_coupon( $coupon_code );
    WC()->cart->calculate_totals();

    // Re-run festival coupon auto-apply (hallo30) now that the other coupon is gone,
    // so it applies without a page refresh.
    if ( function_exists( 'ls_reapply_festival_coupon_after_remove' ) ) {
        ls_reapply_festival_coupon_after_remove( $coupon_code );
    }

    wp_send_json_success( array( 'message' => 'Coupon removed successfully.' ) );
}





add_filter( 'woocommerce_bacs_accounts', 'bbloomer_conditional_bacs_accounts', 9999, 2 );
 
function bbloomer_conditional_bacs_accounts( $account_details, $order_id ) {
   
    return array();
}


// ─── SHIPPING ADDRESS ────────────────────────────────────────────────────────
add_filter('woocommerce_order_get_formatted_shipping_address', 'custom_shipping_address_html', 10000, 3);
function custom_shipping_address_html($formatted, $raw_address, $order) {
   /* echo "<pre>";
    print_r($raw_address);
    echo "</pre>";*/
     // Prevent running in admin (except AJAX if needed)
    if (is_admin() ) {
        if($raw_address['country']=='US'){
            $countries_obj = WC()->countries;
        $countries = $countries_obj->countries;

        $countryCode = $raw_address['country'];
        $countryName = isset($countries[$countryCode]) ? $countries[$countryCode] : $countryCode;
            return $formatted."<br>".$countryName;
        }else{
            return $formatted;
        }
       // return $formatted."dd";
    }
    if (!$order instanceof WC_Order) {
        return $formatted;
    }

    $address_1 = $order->get_shipping_address_1() ?: $order->get_billing_address_1();
    $city      = $order->get_shipping_city()      ?: $order->get_billing_city();

    if (empty($address_1) && empty($city)) {
        return $formatted;
    }

    $first_name   = $order->get_shipping_first_name() ?: $order->get_billing_first_name();
    $last_name    = $order->get_shipping_last_name()  ?: $order->get_billing_last_name();
    $company      = $order->get_shipping_company()    ?: $order->get_billing_company();
    $address_2    = $order->get_shipping_address_2()  ?: $order->get_billing_address_2();
    $state_code   = $order->get_shipping_state()      ?: $order->get_billing_state();
    $postcode     = $order->get_shipping_postcode()   ?: $order->get_billing_postcode();
    $country_code = $order->get_shipping_country()    ?: $order->get_billing_country();
    $country      = WC()->countries->countries[ $country_code ] ?? $country_code;
    $state        = WC()->countries->get_states( $country_code )[ $state_code ] ?? $state_code;

    $html  = '<div class="my-shipping-address">';
    $html .= '<p class="name"><strong>' . esc_html(trim($first_name . ' ' . $last_name)) . '</strong></p>';
    if (!empty($company)) {
        $html .= '<p class="company">' . esc_html($company) . '</p>';
    }
    $html .= '<p class="line1">'    . esc_html($address_1) . '</p>';
    if (!empty($address_2)) {
        $html .= '<p class="line2">' . esc_html($address_2) . '</p>';
    }
    $html .= '<p class="city">'     . esc_html($city)     . '</p>';
    $html .= '<p class="state">'    . esc_html($state)    . '</p>';
    $html .= '<p class="postcode">' . esc_html($postcode) . '</p>';
    $html .= '<p class="country">'  . esc_html($country)  . '</p>';
    $html .= '</div>';

    return $html;
}

// ─── BILLING ADDRESS ─────────────────────────────────────────────────────────
add_filter('woocommerce_order_get_formatted_billing_address', 'custom_billing_address_html', 10000, 3);
function custom_billing_address_html($formatted, $raw_address, $order) {
     // Prevent running in admin (except AJAX if needed)
    if (is_admin() ) {
        if($raw_address['country']=='US'){
            $countries_obj = WC()->countries;
        $countries = $countries_obj->countries;

        $countryCode = $raw_address['country'];
        $countryName = isset($countries[$countryCode]) ? $countries[$countryCode] : $countryCode;
            return $formatted."<br>".$countryName;
        }else{
            return $formatted;
        }
       // return $formatted."dd";
    }
    if (!$order instanceof WC_Order) {
        return $formatted;
    }

    $address_1 = $order->get_billing_address_1();
    $city      = $order->get_billing_city();

    if (empty($address_1) && empty($city)) {
        return $formatted;
    }

    $first_name   = $order->get_billing_first_name();
    $last_name    = $order->get_billing_last_name();
    $company      = $order->get_billing_company();
    $address_2    = $order->get_billing_address_2();
    $state_code   = $order->get_billing_state();
    $postcode     = $order->get_billing_postcode();
    $country_code = $order->get_billing_country();
    $country      = WC()->countries->countries[ $country_code ] ?? $country_code;
    $state        = WC()->countries->get_states( $country_code )[ $state_code ] ?? $state_code;

    $html  = '<div class="my-billing-address">';
    $html .= '<p class="name"><strong>' . esc_html(trim($first_name . ' ' . $last_name)) . '</strong></p>';
    if (!empty($company)) {
        $html .= '<p class="company">' . esc_html($company) . '</p>';
    }
    $html .= '<p class="line1">'    . esc_html($address_1) . '</p>';
    if (!empty($address_2)) {
        $html .= '<p class="line2">' . esc_html($address_2) . '</p>';
    }
    $html .= '<p class="city">'     . esc_html($city)     . '</p>';
    $html .= '<p class="state">'    . esc_html($state)    . '</p>';
    $html .= '<p class="postcode">' . esc_html($postcode) . '</p>';
    $html .= '<p class="country">'  . esc_html($country)  . '</p>';
    if (!empty($order->get_billing_phone())) {
        //$html .= '<p class="phone">' . esc_html($order->get_billing_phone()) . '</p>';
    }
    if (!empty($order->get_billing_email())) {
      //  $html .= '<p class="email">' . esc_html($order->get_billing_email()) . '</p>';
    }
    $html .= '</div>';

    return $html;
}

add_filter('woocommerce_billing_fields', 'remove_billing_phone_field');

function remove_billing_phone_field($fields) {
    unset($fields['billing_phone']);
    return $fields;
}
add_filter('woocommerce_shipping_fields', function($fields) {

    $fields['shipping_phone'] = array(
        'label'       => __('Phone', 'woocommerce'),
        'required'    => true,
        'type'        => 'tel',
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'priority'    => 110,
    );

    return $fields;
});

// Client-side: block non-phone characters instantly
add_action('wp_footer', function() {
    if (!is_checkout()) return;
    ?>
    <script>
    jQuery(function($) {
        // Block invalid characters on keypress
        $('#shipping_phone').on('input', function() {
            var val = $(this).val();
            // Strip anything that's not digit, +, -, space, (, )
            $(this).val(val.replace(/[^\d\s\+\-\(\)]/g, ''));
        });

        // Validate format on blur
        $('#shipping_phone').on('blur', function() {
            var phone = $(this).val().trim();
            var valid = /^[+]?[\d\s\-\(\)]{7,15}$/.test(phone);
            var row = $(this).closest('.form-row');

            if (phone && !valid) {
                row.removeClass('woocommerce-validated')
                   .addClass('woocommerce-invalid');
                if (!row.find('.phone-error').length) {
                    row.append('<span class="phone-error" style="color:red;font-size:12px;">Please enter a valid phone number.</span>');
                }
            } else {
                row.removeClass('woocommerce-invalid')
                   .addClass('woocommerce-validated');
                row.find('.phone-error').remove();
            }
        });
    });
    </script>
    <?php
});
add_filter('body_class', function($classes) {
    if (is_page('blog')) {
        $classes[] = 'woocommerce';
    }
    return $classes;
});

/*add_filter('woocommerce_required_field_notice', function($message, $field_name) {
    return str_replace('Shipping ', '', $message);
}, 100000, 2);*/






add_filter('woocommerce_enqueue_styles', 'custom_wc_styles');

function custom_wc_styles($styles) {

    if (!is_cart() && !is_checkout() && !is_account_page()) {
        return []; // remove everywhere
    }

    return $styles; // allow on checkout
}


add_action('wp_enqueue_scripts', 'optimize_woocommerce_scripts', 100);

function optimize_woocommerce_scripts() {

    // If NOT Woo pages → remove everything
    if ( !is_cart() && !is_checkout()  && !is_account_page()) {

        $scripts = [
            'wc-jquery-blockui',
            'wc-add-to-cart',
            'wc-js-cookie',
            'woocommerce',
            'wc-blocks-style',
            'wc-add-to-cart',
           /* 'wc-order-attribution',*/
            'wc-blocks-style',
            'wc-add-to-cart-variation'
        ];

        foreach ($scripts as $script) {
            wp_dequeue_script($script);
            wp_deregister_script($script);
        }
    }

    if ( ! is_checkout() && ! is_order_received_page() ) {
        wp_dequeue_script( 'katapult_js' );
        wp_deregister_script( 'katapult_js' );
        wp_dequeue_script( 'katapult-blocks-integration' );
        wp_deregister_script( 'katapult-blocks-integration' );
        wp_dequeue_script( 'katapult-gateway' );
        wp_deregister_script( 'katapult-gateway' );

        
    }


    



}

add_action('wp_enqueue_scripts', function() {

    if ( !is_user_logged_in() ) {
        wp_dequeue_style('dashicons');
    }

    wp_dequeue_style('woo-variation-gallery-css');
    wp_dequeue_style('woo-variation-gallery-slider-css');

    wp_deregister_style('woo-variation-gallery-css');
    wp_deregister_style('woo-variation-gallery-slider-css');
    // Stripe blocks

    // WooCommerce blocks
    wp_dequeue_style('wc-blocks-style');
    wp_deregister_style('wc-blocks-style');

    wp_dequeue_style('wp-block-library');
    if ( ! is_checkout() && ! is_order_received_page() ) {
        //wp_dequeue_style( 'custom_splitit_checkout_page_css' );
        //wp_deregister_style( 'custom_splitit_checkout_page_css' );
    }

}, 100000);

add_action('wp_enqueue_scripts', function () {
    wp_register_style('wc-stripe-blocks-checkout-style', false);
}, 100000);



add_filter('disable_woo_variation_gallery', function( $disable ) {

    // Do NOT affect admin
    if ( is_admin() ) {
        return $disable;
    }

    // Disable everywhere else on frontend
    return true;

});


function my_custom_body_class( $classes ) {



    // Example with condition
    $classes[] = 'oc-retail';

    return $classes;
}
add_filter( 'body_class', 'my_custom_body_class' );

add_filter('wc_stripe_upe_params', 'custom_wc_stripe_upe_params',10000);

function custom_wc_stripe_upe_params($params) {

    $params['optimizedCheckoutClassicTitle'] = 'Credit / Debit Card';
    $params['paymentMethodsConfig']['card']['title'] = 'Credit / Debit Card';

    return $params;
}


add_filter('rank_math/frontend/title', 'custom_wc_thankyou_rankmath_title');

function custom_wc_thankyou_rankmath_title($title) {

    if (is_order_received_page()) {
        $title = 'Thank You | Your Order Has Been Received';
    }

    return $title;
}


add_action( 'woocommerce_after_calculate_totals', function ( $cart ) {
    if ( ! isset( $_GET['wc-ajax'] ) ) return;
    $stripe_express = array(
        'wc_stripe_get_shipping_options',
        'wc_stripe_update_shipping_method',
        'wc_stripe_get_cart_details',
        'wc_stripe_get_selected_product_data',
    );
    if ( ! in_array( sanitize_text_field( wp_unslash( $_GET['wc-ajax'] ) ), $stripe_express, true ) ) return;

    $behavior = wc_prices_include_tax() ? 'inclusive' : 'exclusive';
    \Stripe\StripeTaxForWooCommerce\Stripe\Tax_Calculation\Cart_Controller::calculate_taxes( $cart, $behavior );
}, 1000 );

add_filter( 'wc_order_is_editable', '__return_true' );

add_filter( 'get_avatar', 'loose_diamond_custom_author_avatar', 10, 6 );

function loose_diamond_custom_author_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {

    $user = false;

    if ( is_numeric( $id_or_email ) ) {
        $user = get_userdata( $id_or_email );
    } elseif ( $id_or_email instanceof WP_User ) {
        $user = $id_or_email;
    } elseif ( $id_or_email instanceof WP_Post ) {
        $user = get_userdata( $id_or_email->post_author );
    } elseif ( $id_or_email instanceof WP_Comment ) {
        $user = get_userdata( $id_or_email->user_id );
    }

    if ( ! $user ) {
        return $avatar;
    }

    // Replace only Chris Galey's avatar.
    if ( $user->display_name === 'Chris Galey' ) {

        $image = get_template_directory_uri() . '/aj_custom/comman/chris-galey.png';

        return sprintf(
            '<img alt="%s" src="%s" class="avatar avatar-%d photo" height="%d" width="%d" loading="lazy" decoding="async">',
            esc_attr( $user->display_name ),
            esc_url( $image ),
            (int) $size,
            (int) $size,
            (int) $size
        );
    }

    return $avatar;
}
add_filter( 'woocommerce_add_message', function( $message ) {
    if ( strpos( $message, 'Coupon code applied successfully' ) !== false ) {
        return '';
    }
    return $message;
});






add_filter( 'woocommerce_add_to_cart_validation', 'validate_product_price', 10, 5 );

function validate_product_price( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {

    // Check either tag
    if (
        has_term( 'white-diamond', 'product_tag', $product_id ) ||
        has_term( 'color-diamond', 'product_tag', $product_id )
    ) {

        $product = wc_get_product( $variation_id ? $variation_id : $product_id );

        // Price should not be 0
        if ( ! $product || $product->get_price() <= 0 ) {
            wc_add_notice( 'This product cannot be added because the price is 0.', 'error' );
            return false;
        }
    }

    return $passed;
}

add_filter( 'woocommerce_get_breadcrumb', 'lgd_custom_breadcrumb_mapping', 20, 2 );
function lgd_custom_breadcrumb_mapping( $crumbs, $breadcrumb ) {

    // 1) Map by slug (last path segment of the crumb URL) => new URL and optional new label
    $slug_map = array(
         'lab-grown-diamond-earrings'     => array( 'url' => home_url( '/lab-diamond-earrings/' ) ),
         'lab-grown-diamond-wedding-bands'     => array( 'url' => home_url( '/lab-diamond-wedding-bands/' ) ),
         'lab-grown-diamond-pendants'     => array( 'url' => home_url( '/lab-diamond-pendants' ) ),
         'lab-grown-diamond-bracelets'     => array( 'url' => home_url( 'lab-diamond-bracelets' ) ),
         'lab-grown-diamond-necklaces'     => array( 'url' => home_url( 'lab-diamond-necklaces' ) ),
         'melee-diamond'     => array( 'url' => home_url( 'melee-diamond' ) ),
    );

    // 2) Map by exact label (useful for crumbs with no slug, e.g. Home)
    $label_map = array(
        // 'Home' => array( 'url' => home_url( '/' ) ),
    );

    foreach ( $crumbs as $i => $crumb ) {
        $label = isset( $crumb[0] ) ? $crumb[0] : '';
        $url   = isset( $crumb[1] ) ? $crumb[1] : '';

        $slug = $url ? basename( untrailingslashit( wp_parse_url( $url, PHP_URL_PATH ) ) ) : '';

        $map = null;
        if ( $slug && isset( $slug_map[ $slug ] ) ) {
            $map = $slug_map[ $slug ];
        } elseif ( isset( $label_map[ $label ] ) ) {
            $map = $label_map[ $label ];
        }

        if ( $map ) {
            if ( ! empty( $map['url'] ) )   { $crumbs[ $i ][1] = $map['url']; }
            if ( ! empty( $map['label'] ) ) { $crumbs[ $i ][0] = $map['label']; }
        }
    }

    // 3) Optional: remove a crumb entirely by slug
    $remove_slugs = array( /* 'uncategorized' */ );
    $crumbs = array_values( array_filter( $crumbs, function ( $c ) use ( $remove_slugs ) {
        if ( empty( $c[1] ) ) return true;
        $s = basename( untrailingslashit( wp_parse_url( $c[1], PHP_URL_PATH ) ) );
        return ! in_array( $s, $remove_slugs, true );
    } ) );

    // 4) Optional: insert a crumb after Home on product pages
    // if ( is_product() ) {
    //     array_splice( $crumbs, 1, 0, array( array( 'Diamonds', home_url( '/diamonds/' ) ) ) );
    // }

    return $crumbs;
}
/**
 * Disable ALL Rank Math schema on category archives.
 */
add_filter( 'rank_math/json_ld', function ( $data, $jsonld ) {

    if (( function_exists( 'is_product_category' ) && is_product_category() ) ) {
        return [];
    }

    return $data;
}, 99, 2 );


add_action( 'woocommerce_before_order_object_save', function ( $order ) {
    if ( ! $order instanceof WC_Order ) return;

    $changes = $order->get_changes();
    if ( ( $changes['status'] ?? '' ) !== 'cancelled' ) return;

    // Allow manual cancel by a logged-in staff member in wp-admin
    $is_manual = is_admin()
        && is_user_logged_in()
        && current_user_can( 'edit_shop_orders' )
        && ! wp_doing_cron()
        && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );
    if ( $is_manual ) return;

    // Block auto-cancel only if the order is paid
    if ( ! $order->get_date_paid() && ! $order->get_transaction_id() ) return;

    $order->set_status( $order->get_data()['status'] );
    $order->add_order_note( 'Auto-cancel blocked: order already paid (expired old checkout session).' );
}, 10 );


add_action( 'template_redirect', function () {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }

    // Don't interfere with order-received / pay-for-order endpoints
    if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) {
        return;
    }

    if ( ( is_cart() || is_checkout() ) && WC()->cart->is_empty() ) {
        wp_safe_redirect( home_url( '/lab-diamonds/' ) );
        exit;
    }
}, 20 );

function lgd_img_size( $url ) {
    static $cache = [];
    if ( ! $url ) return null;
    if ( isset( $cache[ $url ] ) ) return $cache[ $url ];

    $path = str_replace(
        [ content_url(), site_url() ],
        [ WP_CONTENT_DIR, untrailingslashit( ABSPATH ) ],
        strtok( $url, '?' )
    );

    $size = ( $path && file_exists( $path ) ) ? @getimagesize( $path ) : false;
    return $cache[ $url ] = $size ? [ (int) $size[0], (int) $size[1] ] : null;
}

/**
 * Metal Swatches (site-wide) — converts every select[name="attribute_pa_metal"]
 * into .dr-swatch colour boxes. Select stays hidden so existing JS still works.
 */
add_action( 'wp_footer', 'lgd_metal_swatches_footer', 99 );
function lgd_metal_swatches_footer() {
    ?>
    <style>
/* Hide the real dropdown once it has been converted */
select.ls-metal-enhanced { display: none !important; }

.ls-metal-swatches {    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 6px;
    justify-content: flex-start; }
.ls-metal-swatches .dr-swatch { cursor: pointer; }
.ls-metal-swatches .dr-swatch.is-disabled { opacity: .35; pointer-events: none; }
.ls-metal-swatch-label { display: block; font-size: 13px; margin-top: 6px; }
    </style>
    <script>
/**
 * Site-wide metal swatches.
 * Converts every select[name="attribute_pa_metal"] into .dr-swatches.
 * The select stays as the real form field (hidden); clicking a swatch sets
 * its value and fires a native "change" so existing JS reacts as before.
 */
(function () {
  'use strict';

  var SELECTOR = 'select[name="attribute_pa_metal"]';

  // Same mapping as the product cards (inc/static-html.php)
  function swatchClass(label) {
    var lc = (label || '').toLowerCase();
    if (lc.indexOf('rose') !== -1) return 'dr-swatch--rg';
    if (lc.indexOf('yellow') !== -1) return 'dr-swatch--yg';
    if (lc.indexOf('platinum') !== -1) return 'dr-swatch--pl';
    return 'dr-swatch--w';
  }

  function enhance(sel) {
    if (sel.classList.contains('ls-metal-enhanced')) return;
    sel.classList.add('ls-metal-enhanced');

    var wrap = document.createElement('div');
    wrap.className = 'dr-swatches ls-metal-swatches';
    var lbl = document.createElement('span');
    lbl.className = 'ls-metal-swatch-label';

    function build() {
      wrap.innerHTML = '';
      Array.prototype.forEach.call(sel.options, function (opt) {
        if (!opt.value) return; // skip "Choose an option"
        var text = opt.textContent.trim();
        var sw = document.createElement('span');
        sw.className = 'dr-swatch ' + swatchClass(text);
        sw.setAttribute('role', 'button');
        sw.setAttribute('tabindex', '0');
        sw.setAttribute('title', text);
        sw.setAttribute('aria-label', text);
        sw.setAttribute('data-value', opt.value);
        if (opt.disabled) sw.classList.add('is-disabled');
        sw.addEventListener('click', function () { pick(sw); });
        sw.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(sw); }
        });
        wrap.appendChild(sw);
      });
      sync();
    }

    function sync() {
      var current = '';
      Array.prototype.forEach.call(wrap.children, function (sw) {
        var on = sw.getAttribute('data-value') === sel.value;
        sw.classList.toggle('is-active', on);
        if (on) current = sw.getAttribute('title');
      });
      lbl.textContent = current;
    }

    function pick(sw) {
      var v = sw.getAttribute('data-value');
      if (sel.value === v) return;
      sel.value = v;
      sel.dispatchEvent(new Event('change', { bubbles: true }));
      sync();
    }

    sel.addEventListener('change', sync);
    // WooCommerce re-renders options (e.g. disabling unavailable ones) — rebuild.
    new MutationObserver(build).observe(sel, { childList: true, subtree: true, attributes: true });

    sel.parentNode.insertBefore(wrap, sel.nextSibling);
    wrap.parentNode.insertBefore(lbl, wrap.nextSibling);
    build();
  }

  function scan(root) {
    (root || document).querySelectorAll(SELECTOR).forEach(enhance);
  }

  function init() {
    scan();
    // Pick up dropdowns added later (quick view, AJAX, React)
    new MutationObserver(function (muts) {
      muts.forEach(function (m) {
        m.addedNodes.forEach(function (n) {
          if (n.nodeType !== 1) return;
          if (n.matches && n.matches(SELECTOR)) enhance(n);
          else if (n.querySelectorAll) scan(n);
        });
      });
    }).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
    </script>
    <?php
}