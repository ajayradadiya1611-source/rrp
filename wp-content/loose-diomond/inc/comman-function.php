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
add_action( 'wp_footer', 'ls_output_video_script' );
function ls_output_video_script() {
    ?>
    <script>
    function loupeResize() {
        const iframe = document.querySelector('.video_tab .loupe_resize');

        if (!iframe) return;

        const sku = iframe.getAttribute('sku');
        let size = 500;

        if (window.innerWidth <= 576) {
            size = 320;
        } else if (window.innerWidth <= 768) {
            size = 400;
        } else if (window.innerWidth <= 992) {
            size = 500;
        } else {
            size = 500;
        }

        iframe.width = size;
        iframe.height = size;
        iframe.src = `https://loupe360.com/diamond/${sku}/video/${size}/${size}`;
    }
    window.addEventListener('load', loupeResize);
window.addEventListener('resize', loupeResize);
 </script>
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


/**
 * Ring Builder Breadcrumb Functions
 * 
 * Direct function implementation that can be called from templates
 * Also includes shortcode wrapper for backward compatibility
 */

/**
 * Display ring builder breadcrumb
 * 
 * Main function that can be called directly from templates
 * Usage: ls_ring_builder_breadcrumb('setting');
 * 
 * @param string $current_page Current page: 'setting', 'diamond', or 'review'
 * @param bool $echo Whether to echo or return the output (default: true)
 * @return string|void HTML output if $echo is false
 */
function ls_ring_builder_breadcrumb($current_page = 'setting', $echo = true) {
    // Sanitize current page
    $current_page = sanitize_key($current_page);
    
    // Validate current page value
    $allowed_pages = array('setting', 'diamond', 'review');
    if (!in_array($current_page, $allowed_pages, true)) {
        $current_page = 'setting';
    }
    
    // Get ring builder data
    $rb_data = ls_get_ring_builder_data();
    
    // Generate breadcrumb HTML
    ob_start();
    ls_render_ring_builder_breadcrumb($current_page, $rb_data);
    $output = ob_get_clean();
    
    if ($echo) {
        echo $output;
    } else {
        return $output;
    }
}

/**
 * Get ring builder session data with validation
 * 
 * @return array Sanitized ring builder data
 */
function ls_get_ring_builder_data() {
    // Initialize session if needed
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Get session data with defaults
    $ls_rb_ses = isset($_SESSION['ls_ring_builder']) && is_array($_SESSION['ls_ring_builder']) 
        ? $_SESSION['ls_ring_builder'] 
        : array();
    
    $rb_setting_ses = isset($ls_rb_ses['setting']) && is_array($ls_rb_ses['setting']) 
        ? $ls_rb_ses['setting'] 
        : array();
    
    $rb_diamond_ses = isset($ls_rb_ses['diamond']) && is_array($ls_rb_ses['diamond']) 
        ? $ls_rb_ses['diamond'] 
        : array();

    $rb_started_with = isset($ls_rb_ses['started_with']) && is_string($ls_rb_ses['started_with'])
        ? $ls_rb_ses['started_with']
        : '';
    
    return array(
        'setting' => $rb_setting_ses,
        'diamond' => $rb_diamond_ses,
        'started_with' => $rb_started_with,
        'full_session' => $ls_rb_ses
    );
}

/**
 * Get global page URLs for ring builder
 * 
 * @return array URLs for different ring builder pages
 */
function ls_get_ring_builder_urls() {
    global $ring_setting, $select_diamond, $ring_preview, $select_cdiamond;
    
    return array(
        'setting' => !empty($ring_setting) ? esc_url($ring_setting) : '#',
        'diamond' => !empty($select_diamond) ? esc_url($select_diamond) : '#',
        'color_diamond' => !empty($select_cdiamond) ? esc_url($select_cdiamond) : '#',
        'preview' => !empty($ring_preview) ? esc_url($ring_preview) : '#'
    );
}

/**
 * Process setting data from session
 * 
 * @param array $rb_setting_ses Setting session data
 * @return array Processed setting information
 */
function ls_process_setting_data($rb_setting_ses) {
    if (empty($rb_setting_ses) || empty($rb_setting_ses['sp_id']) || empty($rb_setting_ses['sp_vid'])) {
        return array(
            'is_selected' => false,
            'product_id' => 0,
            'variation_id' => 0,
            'title' => '',
            'image_url' => '',
            'change_url' => '',
            'shape' => '',
            'price' => 0,
            'price_html' => ''
        );
    }
    
    $sp_id = absint($rb_setting_ses['sp_id']);
    $sp_vid = absint($rb_setting_ses['sp_vid']);
    
    // Validate product exists
    if (!wc_get_product($sp_id)) {
        return array('is_selected' => false);
    }
    
    // Get variation data
    $variation_data = new WC_Product_Variation($sp_vid);
    $var_attributes = $variation_data->get_variation_attributes();
    
    // Build query string for selected variation
    $query_string = ls_build_variation_query_string($var_attributes);
    
    // Get product title
    $product_title = ls_get_post_title($sp_id);
    $product_title = ucwords(strtolower($product_title));
    
    // Get product image
    $image_url = ls_get_setting_image($sp_id, $sp_vid);
    
    // Get diamond shape for setting
    $diamond_shape = get_post_meta($sp_id, 's_dtype', true);
    
    // Get URLs
    $urls = ls_get_ring_builder_urls();
    
    return array(
        'is_selected' => true,
        'product_id' => $sp_id,
        'variation_id' => $sp_vid,
        'title' => $product_title,
        'product_url' => get_permalink($sp_id) . $query_string,
        'image_url' => $image_url,
        'change_url' => $urls['setting'],
        'shape' => $diamond_shape,
        'price' => $variation_data ? $variation_data->get_price() : 0,
        'price_html' => $variation_data ? $variation_data->get_price_html() : ''
    );
}

/**
 * Process diamond data from session
 * 
 * @param array $rb_diamond_ses Diamond session data
 * @return array Processed diamond information
 */
function ls_process_diamond_data($rb_diamond_ses) {
    global $inv_shape, $inv_color_c, $inv_subshape_to_base, $wpdb;
    
    if (empty($rb_diamond_ses) || empty($rb_diamond_ses['d_pid'])) {
        return array(
            'is_selected' => false,
            'product_id' => 0,
            'title' => '',
            'image_url' => '',
            'change_url' => '',
            'price' => 0,
            'price_html' => ''
        );
    }
    
    $d_pid = absint($rb_diamond_ses['d_pid']);
    
    // Validate product exists
    if (!get_post($d_pid)) {
        return array('is_selected' => false);
    }
    
    // Get diamond properties
    $dmnd_shape_id = isset($rb_diamond_ses['d_shape']) ? sanitize_text_field($rb_diamond_ses['d_shape']) : '';
    $dmnd_color = isset($rb_diamond_ses['d_color']) ? sanitize_text_field($rb_diamond_ses['d_color']) : '';
    $c_type = get_post_meta($d_pid, 'c_type', true);
    
    // Get shape and color names
    $dshape_name = !empty($inv_shape) && is_array($inv_shape) ? array_search($dmnd_shape_id, $inv_shape) : '';
    $dcolor_name = !empty($inv_color_c) && is_array($inv_color_c) ? array_search($dmnd_color, $inv_color_c) : '';
    
    // Get diamond image
    $image_url = ls_get_diamond_image($dshape_name, $dcolor_name, $c_type);
    
    // Get product title
    $product_title = ls_get_post_title($d_pid);
    $product_title = ucwords(strtolower($product_title));
    
    // Determine change URL based on diamond type
    $urls = ls_get_ring_builder_urls();
    $change_url = !empty($dmnd_color) ? $urls['color_diamond'] : $urls['diamond'];

    // Get product price info
    $d_product = function_exists('wc_get_product') ? wc_get_product($d_pid) : false;
    $d_price = $d_product ? $d_product->get_price() : 0;
    $d_price_html = $d_product ? $d_product->get_price_html() : '';

    // Get SKU
    $d_sku = $d_product ? $d_product->get_sku() : '';

    // Append SKU to URL (as query param)
    if (!empty($d_sku)) {
        $args = array();

        if (!empty($d_sku)) {
            $args['sku'] = $d_sku;
            $args['viewsku'] = $d_sku;
        }

        // If this diamond was saved under a named cut style (e.g. its
        // `shape` column is "square_emerald" rather than plain "emerald"),
        // carry that forward as a `subshape` param too. Without it the
        // listing page only knows the base shape id from session and the
        // diamond can end up filtered out of its own "View"/"Change" list
        // (see components/inventory-app/index.php's ringbuilder branch,
        // which now also falls back to looking this up from `d_pid`, but
        // putting it in the URL keeps the link correct on its own too).
        $rb_raw_shape = $wpdb->get_var($wpdb->prepare("SELECT shape FROM ls_inventory WHERE pid = %d", $d_pid));
        $rb_raw_shape_key = function_exists('ls_normalize_shape_key') ? ls_normalize_shape_key($rb_raw_shape) : strtolower(trim((string) $rb_raw_shape));
        if (!empty($rb_raw_shape) && !empty($inv_subshape_to_base) && isset($inv_subshape_to_base[$rb_raw_shape_key])
            && !(function_exists('ls_is_ring_group_cut') && ls_is_ring_group_cut($rb_raw_shape_key))) {
            $args['subshape'] = $rb_raw_shape_key;
        }

        $change_url = add_query_arg($args, $change_url);
    }
    
    return array(
        'is_selected' => true,
        'product_id' => $d_pid,
        'title' => $product_title,
        'image_url' => $image_url,
        'change_url' => $change_url,
        'shape' => $dshape_name,
        'shape_slug' => str_replace(' ', '_', strtolower($dshape_name)),
        'color' => $dcolor_name,
        'type' => $c_type,
        'price' => $d_price,
        'price_html' => $d_price_html
    );
}

/**
 * Build variation query string from attributes
 * 
 * @param array $var_attributes Variation attributes
 * @return string Query string
 */
function ls_build_variation_query_string($var_attributes) {
    if (empty($var_attributes) || !is_array($var_attributes)) {
        return '';
    }
    
    $query_params = array();
    foreach ($var_attributes as $key => $value) {
        $query_params[] = urlencode($key) . '=' . urlencode($value);
    }
    
    return '?' . implode('&', $query_params);
}

/**
 * Get setting image URL
 * 
 * @param int $product_id Product ID
 * @param int $variation_id Variation ID
 * @return string Image URL
 */
function ls_get_setting_image($product_id, $variation_id) {
    // Try variation image first
    $image_id = get_post_thumbnail_id($variation_id);
    
    // Fallback to main product image
    if (!$image_id) {
        $image_id = get_post_thumbnail_id($product_id);
    }
    
    if ($image_id) {
        $image_data = wp_get_attachment_image_src($image_id, 'woocommerce_single');
        if ($image_data && isset($image_data[0])) {
            return esc_url($image_data[0]);
        }
    }
    
    // Default fallback image
    return esc_url(get_stylesheet_directory_uri() . '/wp-content/uploads/2021/11/Ring-1.svg');
}

/**
 * Get diamond image URL based on shape, color, and type
 * 
 * @param string $shape_name Diamond shape name
 * @param string $color_name Diamond color name
 * @param int $c_type Diamond type
 * @return string Image URL
 */
function ls_get_diamond_image($shape_name, $color_name, $c_type) {
    $base_url = get_stylesheet_directory_uri();
    
    // Color diamond (type 3)
    if ($c_type == 3 && !empty($shape_name) && !empty($color_name)) {
        // $image_path = '/ls-inventory-color/assets/diamond/' . strtolower($shape_name) . '/' . $color_name . '.png';
        $image_path = '/assets/image/ls-inventory-color/diamond/' . strtolower($shape_name) . '/' . $color_name . '.png';
        return esc_url($base_url . $image_path);
    }
    
    // Regular diamond
    if (!empty($shape_name)) {
        // $image_path = '/ls-inventory/assets/shape-diamond/' . strtolower($shape_name) . '.png';
        $image_path = '/assets/image/ls-inventory/shape-diamond/png/' . strtolower($shape_name) . '.png';
        return esc_url($base_url . $image_path);
    }
    
    // Default fallback image
    return esc_url($base_url . '/wp-content/uploads/2021/11/Diamond-SVG.svg');
}

/**
 * Render the ring builder breadcrumb HTML
 * 
 * @param string $current_page Current page identifier
 * @param array $rb_data Ring builder data
 */
function ls_render_ring_builder_breadcrumb($current_page, $rb_data) {
    // Process setting and diamond data
    $setting_data = ls_process_setting_data($rb_data['setting']);
    $diamond_data = ls_process_diamond_data($rb_data['diamond']);
    
    // Get URLs
    $urls = ls_get_ring_builder_urls();
    
    // Determine if complete ring
    $has_complete_ring = $setting_data['is_selected'] && $diamond_data['is_selected'];
    
    // Determine active classes
    $setting_active = ($current_page === 'setting') ? 'active' : '';
    $diamond_active = ($current_page === 'diamond') ? 'active' : '';
    $review_active = ($current_page === 'review') ? 'active' : '';
    
    // Add 'active' class if item is selected
    if ($setting_data['is_selected']) {
        $setting_active .= ' active';
    }
    if ($diamond_data['is_selected']) {
        $diamond_active .= ' active';
    }
    
    ?>
   
    <script type="text/javascript">
        window.LS_RING_BUILDER = <?php echo wp_json_encode(array(
            'current_page' => $current_page,
            'setting' => $setting_data,
            'diamond' => $diamond_data,
            'started_with' => isset($rb_data['started_with']) ? $rb_data['started_with'] : '',
            'urls' => $urls,
            'admin_ajax_url' => admin_url('admin-ajax.php'),
            'is_diamond_sku' => (bool) get_query_var('diamond_sku')
        ), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE) ?: 'null'; ?>;
    </script>
    <?php 
}

/**
 * Helper function to get post title safely
 * 
 * @param int $post_id Post ID
 * @return string Post title
 */
function ls_get_post_title($post_id) {
    $post = get_post($post_id);
    return $post ? get_the_title($post) : '';
}


/**
 * Build product name string for diamonds/settings
 *
 * @param string $shape_title Shape title (may contain 'Cut')
 * @param string $shape_cut_ttl Shape cut title to use when shape_title contains 'Cut'
 * @param string|float $carat Carat value
 * @param string $num_color Color value
 * @param string $num_clarity Clarity value
 * @param object|array|null $r Optional object/array fallback providing `shape_title`
 * @return string Formatted product name
 */
function ls_build_pname($r = null,$type='white') {
    // Expecting an object or array record `$r` with fields: shape, shape_title, carat, color, clarity
    if (is_null($r)) {
        return '';
    }

    if (is_object($r)) {
        $shape = $r->shape ?? '';
        $shape_title = $r->shape_title ?? $shape;
        $shape_cut_ttl = $r->shape_title ?? $shape;
        $carat = $r->carat ?? '';
        $raw_color = $r->color ?? '';
        $raw_clarity = $r->clarity ?? '';
    } elseif (is_array($r)) {
        $shape = $r['shape'] ?? '';
        $shape_title = $r['shape_title'] ?? $shape;
        $shape_cut_ttl = $r['shape_title'] ?? $shape;
        $carat = $r['carat'] ?? '';
        $raw_color = $r['color'] ?? '';
        $raw_clarity = $r['clarity'] ?? '';
    } else {
        // Not a record we understand
        return '';
    }

    $num_color = $raw_color;

    $num_clarity = $raw_clarity;
    if (function_exists('get_attrkey_val')) {
        // Try color_c mapping first (for colored diamonds), then fall back to color
        if ($raw_color !== '') {
            /*$try = @get_attrkey_val('color_c', $raw_color);
            if ($try !== $raw_color && $try !== null) {
                $num_color = $try;
            } else {*/
                if($type=='white'){
                    $try2 = get_attrkey_val('color', $raw_color);
                    if ($try2 !== $raw_color && $try2 !== null) {
                        $num_color = $try2;
                    }
                }else{
                    $try2 = get_attrkey_val('color_c', $raw_color);
                    if ($try2 !== $raw_color && $try2 !== null) {
                        $num_color = ucfirst($try2);
                    }
                }
                
            //}
        }

        if ($raw_clarity !== '') {
            $tryc = get_attrkey_val('clarity', $raw_clarity);
            if ($tryc !== null) {
                $num_clarity = $tryc;
            }
        }
    }

    $has_cut_in_title = function_exists('str_contains') ? str_contains($shape_title, 'Cut') : (strpos($shape_title, 'Cut') !== false);

    if ($has_cut_in_title) {
        $base = ucfirst(strtolower($shape_cut_ttl));
    } else {
        $base = ucfirst(strtolower($shape_title)) . ' Cut';
    }

    $parts = array_filter([
        trim($base),
        $carat !== '' ? trim($carat) . ' Carat' : '',
        $num_color !== '' ? trim($num_color) . ' Color' : '',
        $num_clarity !== '' ? trim($num_clarity) . ' Clarity' : ''
    ]);

    return implode(' ', $parts);
}

function ls_build_pname_diamond($r = null,$type='white') {
    // Expecting an object or array record `$r` with fields: shape, shape_title, carat, color, clarity
    if (is_null($r)) {
        return '';
    }

    if (is_object($r)) {
        $shape = $r->shape ?? '';
        $shape_title = $r->shape_title ?? $shape;
        $shape_cut_ttl = $r->shape_title ?? $shape;
        $carat = $r->carat ?? '';
        $raw_color = $r->color ?? '';
        $raw_clarity = $r->clarity ?? '';
    } elseif (is_array($r)) {
        $shape = $r['shape'] ?? '';
        $shape_title = $r['shape_title'] ?? $shape;
        $shape_cut_ttl = $r['shape_title'] ?? $shape;
        $carat = $r['carat'] ?? '';
        $raw_color = $r['color'] ?? '';
        $raw_clarity = $r['clarity'] ?? '';
    } else {
        // Not a record we understand
        return '';
    }

    $num_color = $raw_color;

    $num_clarity = $raw_clarity;
    if (function_exists('get_attrkey_val')) {
        // Try color_c mapping first (for colored diamonds), then fall back to color
        if ($raw_color !== '') {
            /*$try = @get_attrkey_val('color_c', $raw_color);
            if ($try !== $raw_color && $try !== null) {
                $num_color = $try;
            } else {*/
                if($type=='white'){
                    $try2 = get_attrkey_val('color', $raw_color);
                    if ($try2 !== $raw_color && $try2 !== null) {
                        $num_color = $try2;
                    }
                }else{
                    $try2 = get_attrkey_val('color_c', $raw_color);
                    if ($try2 !== $raw_color && $try2 !== null) {
                        $num_color = ucfirst($try2);
                    }
                }
                
            //}
        }

        if ($raw_clarity !== '') {
            $tryc = get_attrkey_val('clarity', $raw_clarity);
            if ($tryc !== null) {
                $num_clarity = $tryc;
            }
        }
    }

    $has_cut_in_title = function_exists('str_contains') ? str_contains($shape_title, 'Cut') : (strpos($shape_title, 'Cut') !== false);

    if ($has_cut_in_title) {
        $base = ucfirst(strtolower($shape_cut_ttl));
    } else {
        $base = ucfirst(strtolower($shape_title)) . ' Cut';
    }

    $parts = array_filter([
        $carat !== '' ? trim($carat) . ' Carat' : '',
        trim($base)
    ]);

    return implode(' ', $parts);
}



function ls_rb_clear(){

    global $select_diamond, $ring_setting, $inv_shape;

    $status = false;
    $redirect_url = $ring_setting;

    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if(!empty($type) && isset($_SESSION['ls_ring_builder'])){

        if($type === 'complete'){

            unset($_SESSION['ls_ring_builder']);
            $status = true;

        }    
        else if($type === 'diamond'){

            unset($_SESSION['ls_ring_builder']['diamond']);
            // If the diamond was the item that started the ring build and
            // it's now cleared, let the next selection (either side) set
            // the order fresh.
            if(isset($_SESSION['ls_ring_builder']['started_with']) && $_SESSION['ls_ring_builder']['started_with'] === 'diamond'){
                unset($_SESSION['ls_ring_builder']['started_with']);
            }
            $status = true;
            $redirect_url = $select_diamond;

        }else{

            // Started the ring build with a diamond first? Keep that
            // diamond's shape in mind before we drop the setting, so we
            // can send the user back to the correct shape-specific
            // setting page instead of the generic one.
            //print_r($_SESSION);
            $started_with_diamond = isset($_SESSION['ls_ring_builder']['started_with']) && $_SESSION['ls_ring_builder']['started_with'] === 'diamond';
            $rb_diamond_ses = isset($_SESSION['ls_ring_builder']['diamond']) ? $_SESSION['ls_ring_builder']['diamond'] : array();

            unset($_SESSION['ls_ring_builder']['setting']);
            if(isset($_SESSION['ls_ring_builder']['started_with']) && $_SESSION['ls_ring_builder']['started_with'] === 'setting'){
                unset($_SESSION['ls_ring_builder']['started_with']);
            }
            $status = true;

            if(!empty($rb_diamond_ses['d_shape'])){
                $dshape_name = !empty($inv_shape) && is_array($inv_shape) ? array_search($rb_diamond_ses['d_shape'], $inv_shape) : '';
                if(!empty($dshape_name)){
                    $redirect_url = ls_get_ring_setting_url_by_shape($dshape_name);
                }
            }
        }
    }

    wp_send_json([
        'status' => $status,
        'redirect_url' => $redirect_url
    ]);
}

add_action('wp_ajax_ls_rb_clear', 'ls_rb_clear');
add_action('wp_ajax_nopriv_ls_rb_clear', 'ls_rb_clear');


//if remove Setting product from cart we have manually removed its diamond product from the cart
add_action( 'woocommerce_cart_item_removed', 'after_remove_product_from_cart', 10, 2 );
function after_remove_product_from_cart($removed_cart_item_key, $cart) {
    $line_item = $cart->removed_cart_contents[ $removed_cart_item_key ];
    //$pid = $line_item[ 'product_id' ];
    //echo $removed_cart_item_key;
    //echo '<pre>'; print_r($line_item); echo '</pre>';
    //die();
    $ls_rb_type = $line_item['ls_ring_builder']['ls_rb_type'];
    if(isset($ls_rb_type) && $ls_rb_type!=''){
        //if remove setting or main product manually remove Diamond product
        if($ls_rb_type=='setting_prdct'){
            $d_product_id = $line_item['ls_ring_builder']['d_pid'];
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                if ( $cart_item['product_id'] == $d_product_id ){
                    WC()->cart->remove_cart_item( $cart_item_key );
                    //remove or unset this item record from custom global cart session by checking setting item key
                    $ls_ring_builder_cartadded = isset($_SESSION['ls_ring_builder_cartadded']) ? $_SESSION['ls_ring_builder_cartadded'] : array();
                    $i=0;
                    foreach($ls_ring_builder_cartadded as $key){
                        if($key['lscart_diamond_itemkey']==$cart_item_key){
                            unset($_SESSION['ls_ring_builder_cartadded'][$removed_cart_item_key]);
                        }
                        $i++;
                    }
                    //if user is on rebuild (change process ) process and without add to cart it remove product from cart remove small session
                    $ls_rb_ses = isset($_SESSION['ls_ring_builder']) ? $_SESSION['ls_ring_builder'] : array();
                    if($ls_rb_ses['change']['setting_itemkey']==$removed_cart_item_key){
                        unset($_SESSION['ls_ring_builder']['change']); //unset ringbuild process session
                    }
                }
            }
        }
    }
}

//removed remove or delete button from diamond product for ring builder
add_filter('woocommerce_cart_item_remove_link', 'customized_cart_item_remove_link', 20, 2 );
function customized_cart_item_remove_link( $button_link, $cart_item_key ){
    global $ring_preview;
    // Get the current cart item
    $cart_item = WC()->cart->get_cart()[$cart_item_key];
    $ls_rb_type = $cart_item['ls_ring_builder']['ls_rb_type'];
    if($ls_rb_type=='diamond_prdct')
        $button_link = '';
    else if($ls_rb_type=='setting_prdct'){
        $button_link .= '<div class="ls_rebuildlink"><a href="javascript:void(0);" class="ls_rebuild ls-blackbtn" itemkey="'.$cart_item_key.'">Rebuild Ring</a></div>';
    }
    return $button_link;
}

function ls_rb_woocommerce_cart_item_class( $class, $values, $item_key ) {
    
    global $woocommerce;
    $ls_cart = $woocommerce->cart->get_cart();
    $ls_rb_type = $ls_cart[$item_key]['ls_ring_builder']['ls_rb_type'];
    if(isset($ls_rb_type) && $ls_rb_type!=''){
        if($ls_rb_type=='setting_prdct')
            $class .= ' rb_setting_product ';
        else if($ls_rb_type=='diamond_prdct')
            $class .= ' rb_diamond_product ';
    }
    return $class;
}
add_filter( 'woocommerce_cart_item_class', 'ls_rb_woocommerce_cart_item_class', 10, 3 );


//Display Order item meta only on ordr edit page
add_action( 'woocommerce_after_order_itemmeta', 'display_admin_order_item_custom_button', 10, 3 );
function display_admin_order_item_custom_button( $item_id, $item, $product ){
    // Only "line" items and backend order pages
    if( ! ( is_admin() && $item->is_type('line_item') ) )
        return;
    $diamond = $item->get_meta('diamond');
    
    $setting_size = $item->get_meta('setting_size');
    if( ! empty($diamond) ) {
        
        echo '<p class="ls_settingprdct"><strong>This is Setting is of Diamond : </strong>'. get_the_title($diamond).' ('.$diamond.')</p>';   
    }
    $setting = $item->get_meta('setting');
    if( ! empty($setting) ) {
     echo '<p><strong>This Diamond is of Setting : </strong>'. get_the_title($setting).' ('.$setting.')</p>'; 
    }
    $setting_variation = $item->get_meta('setting_variation');
    if( ! empty($setting_variation) ) {
     echo '<p><strong>Variation ID : </strong>'. $setting_variation.'</p>';   
    }
}

//Change Ring builder ajax from cart page
//Change Ring builder ajax from cart page
function ls_change_rb(){
    ob_start();
    global $select_diamond, $ring_setting, $ring_preview;
    $status = false;
    $itemkey = $_POST['itemkey'];
    $redirect_url = $ring_preview;

    if($itemkey!=''){

        $ls_cartadded_rbses = isset($_SESSION['ls_ring_builder_cartadded']) ? $_SESSION['ls_ring_builder_cartadded'] : array();
        foreach($ls_cartadded_rbses as $key){
            if($key['lscart_setting_itemkey'] == $itemkey){

                // Keep the "Rebuild Ring" session in sync with whatever
                // upgrades are actually on this cart item right now (may be
                // none — that's fine, not an error). Uses the same helper
                // the cart-page edit/remove flows use, so
                // $_SESSION['ls_ring_builder_cartadded'][...]['setting']
                // itself stays in sync too (not just this request's copy).
                if ( class_exists( 'LGD_Ring_Extra_Features' ) && WC()->cart ) {
                    $cart_contents = WC()->cart->get_cart();
                    $items = $cart_contents[ $itemkey ]['ls_ring_builder']['extra_features_items'] ?? array();
                    LGD_Ring_Extra_Features::sync_cartadded_snapshot( $itemkey, $items );
                }

                $setting = $_SESSION['ls_ring_builder_cartadded'][ $itemkey ]['setting'] ?? $key['setting'];
                $_SESSION['ls_ring_builder']['setting'] = $setting;
                $_SESSION['ls_ring_builder']['diamond'] = $key['diamond'];
                //some extra paramter to identify its change ringbuilder process
                $_SESSION['ls_ring_builder']['change'] = array('setting_itemkey'=>$key['lscart_setting_itemkey'], 'diamond_itemkey'=>$key['lscart_diamond_itemkey']);
                $status = true;
                $redirect_url = $ring_preview;
            }

        }

    }
    echo json_encode(array('status'=>$status, 'redirect_url'=>$redirect_url));
    die();
}
add_action( 'wp_ajax_ls_change_rb', 'ls_change_rb' );
add_action( 'wp_ajax_nopriv_ls_change_rb', 'ls_change_rb' );



function single_product_product_info(){

    global $product, $wpdb;

    $did = $product->get_id();
    if(!$did) return;

    $is_readyToShipProduct = false;
    if ( has_term('ready-to-ship', 'product_cat', $product->get_id() ) )
                            $is_readyToShipProduct = true;

    $short_description = get_field('product_description',$did);
?>

<div class="container-fluid my-5">

    <div class="ls-product-info">
        <h2 class="product_information_heading">PRODUCT INFORMATION</h2>
        <div class="product_information_desc"><?php echo $short_description;?></div>
        <div class="row">

            <div class="col-md-7">
                <div class="row">
                    <!-- LEFT COLUMN -->
                    <div class="col-md-6">
                        <div id="variation-section1" class=" product_information_box ">
                            <!-- Dynamically updated on variation change -->
                        </div>


                    </div>
                     <?php if(!$is_readyToShipProduct){ ?>
                            <div class="col-md-6 ">
                        <div class="product_information_box light_yellow_box">
                            <h3 class="fw-semibold">Made to Order</h3>
                            <p class="mb-0">
                                We make your jewelry individually once we receive your order.
                            </p>
                        </div> </div>
                        <?php } ?>
                    <?php
                       

                        
                        if(!$is_readyToShipProduct){
                        ?>
                        <div class="col-md-6">
                         <div  class="product_information_box ">
                            <h3 class="fw-semibold">Delivery</h3>
                            <p class="mb-1">- Express international shipping with insurance</p>
                            <p class="mb-1">- Expected delivery time: 3-4 working weeks</p>
                        </div>
                    </div>
                        <?php } ?>

                    <!-- RIGHT COLUMN -->
                    
                     



                   
 <div class="col-md-6 ">

                        <div class="product_information_box ">
                            <h3 class="fw-semibold">Why Loose Grown Diamond?</h3>
                            <p class="mb-1">- Handcrafted</p>
                            <p class="mb-1">- Conflict Free & Eco-Friendly Diamonds</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<?php
}


// ─── PARENT PRODUCT VIDEO LINK ────────────────────────────────────────────────

/**
 * Add a "Video Link" field in the General tab of the parent product.
 * Hook: woocommerce_product_options_general_product_data
 */
add_action( 'woocommerce_product_options_general_product_data', 'lgd_parent_product_video_link_field' );
function lgd_parent_product_video_link_field() {
    global $post;
    woocommerce_wp_text_input( [
        'id'          => 'oc_video_link_parent', // was: oc_video_link
        'name'        => 'oc_video_link_parent', // explicit, don't rely on id
        'label'       => __( 'Video Link', 'woocommerce' ),
        'placeholder' => 'https://www.youtube.com/watch?v=...',
        'desc_tip'    => true,
        'description' => __( 'Enter a YouTube or video URL for this product. Variations can override this with their own video link.', 'woocommerce' ),
        'value'       => get_post_meta( $post->ID, 'oc_video_link', true ), // meta key can stay the same
    ] );
}

add_action( 'woocommerce_process_product_meta', 'lgd_save_parent_product_video_link' );
function lgd_save_parent_product_video_link( $post_id ) {

    if ( ! isset( $_POST['oc_video_link_parent'] ) ) {
        return;
    }

    $video_link = sanitize_text_field( wp_unslash( $_POST['oc_video_link_parent'] ) );
    $video_link = esc_url_raw( $video_link );

    update_post_meta( $post_id, 'oc_video_link', $video_link );
}
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
function variation_settings_fields( $loop, $variation_data, $variation ) {
        
    // Ring Builder
    $ls_variation = wc_get_product( $variation->ID );
    $is_setting_product = is_setting_product( $ls_variation->get_parent_id() );
    
    // ring type product - FIXED: use wc_get_product() instead of new WC_Product()
    $product = wc_get_product( get_the_ID() );
    // FIXED: use get_id() instead of deprecated ->id
    $c_ctype = get_post_meta( $product->get_id(), 'c_type', true );

    //if ( $is_setting_product['is_setting'] || $c_ctype == 4 ) {
    
    ?>
         <!-- NEW: YouTube link per variation -->
        <div class="product-info-label">
            <label>Video Link</label>
            <input 
                name="oc_video_link[<?php echo esc_attr( $variation->ID ); ?>]" 
                class="short oc_video_link" 
                placeholder=""
                value="<?php echo esc_attr( get_post_meta( $variation->ID, 'oc_video_link', true ) ); ?>" 
            />
        </div>
        <style>
            .product-info-labelpart{display:flex;margin-top: 25px;}
            .product-info-label label, .section1data label{display:block;margin-bottom: 5px;}
            .product-info-label {width: 50%;}
            .product-info-label input, .sectiondatasingle input{
                line-height: 2;
                padding: 5px 10px;
                border-radius: 4px;
                border: 1px solid #7e8993;
                background-color: #fff;
                color: #32373c;
                width: 100%;
            }
            .section1data{margin-top: 20px;}
            .sectiondatasingle{display: flex;margin-bottom: 10px;}
            .sectiondatasingle input{width: 100%;margin-right: 10px;}
            .aksahr-remove{padding: 0px 10px;font-size: 18px;cursor: pointer;}
            .section1button button{padding: 5px 10px;font-size: 13px;font-weight: 500;margin-bottom: 10px;}
        </style>

        <div class="product-info-labelpart">
            <div style="margin-right: 35px;" class="product-info-label">
                <label>Materials Label</label>
                <input 
                    name="section1label[<?php echo esc_attr( $variation->ID ); ?>]" 
                    class="short sectionlabel" 
                    value="<?php echo esc_attr( get_post_meta( $variation->ID, 'section1label', true ) ); ?>" 
                />
            </div>
        </div>
        
        <div class="section1data">
            <label>Materials Data (Section 1 Data)</label>
            <?php 
           $data = get_post_meta( $variation->ID, 'section1data', false );

            // Data is now wrapped in extra array, get first element:
            $data = ! empty( $data[0] ) ? $data[0] : [];

            if ( ! empty( $data ) && is_array( $data ) ) :
                foreach ( $data as $value ) :
                    if ( $value == '' ) continue;
                    ?>
                    <div class="sectiondatasingle">
                        <input 
                            name="section1data[<?php echo esc_attr( $variation->ID ); ?>][]" 
                            class="short section1datainput" 
                            value="<?php echo esc_attr( $value ); ?>" 
                        />
                        <button class="aksahr-remove" type="button">X</button>
                    </div>
                    <?php
                endforeach;
            else :
                ?>
                <div class="sectiondatasingle">
                    <input 
                        name="section1data[<?php echo esc_attr( $variation->ID ); ?>][]" 
                        class="short section1datainput" 
                    />
                    <button class="aksahr-remove" type="button">X</button>
                </div>
            <?php
            endif;
            ?>
        </div>

        <div class="section1button">
            <button type="button" data-id="<?php echo esc_attr( $variation->ID ); ?>">Add New Row</button>
        </div>

    </div>
    <?php
    //}
}


add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
function save_variation_settings_fields( $post_id, $loop ) {

    // Ring Builder
    $ls_variation = wc_get_product( $post_id );
    $is_setting_product = is_setting_product( $ls_variation->get_parent_id() );
    $pid = wp_get_post_parent_id( $post_id );
    $c_ctype = get_post_meta( $pid, 'c_type', true );

   // if ( $is_setting_product['is_setting'] || $c_ctype == 4 ) {

        // FIXED: save only current variation using $post_id directly, not looping all variations

        // Save section1label
        $section1label = isset( $_POST['section1label'][ $post_id ] )
            ? sanitize_text_field( $_POST['section1label'][ $post_id ] )
            : '';
        update_post_meta( $post_id, 'section1label', $section1label );

        // Save section1data
        $section1data = isset( $_POST['section1data'][ $post_id ] )
            ? (array) $_POST['section1data'][ $post_id ]
            : [];

        // Sanitize and filter empty values
        $section1data = array_values(
            array_filter(
                array_map( 'sanitize_text_field', $section1data ),
                fn( $v ) => $v !== ''
            )
        );

        update_post_meta( $post_id, 'section1data', $section1data );
    //}
    if ( isset( $_POST['oc_video_link'][ $post_id ] ) ) {
        update_post_meta(
            $post_id,
            'oc_video_link',
            esc_url_raw( wp_unslash( $_POST['oc_video_link'][ $post_id ] ) )
        );
    }
}


add_action( 'admin_footer', function () {
    ?>
    <script>
        jQuery(document).on('click', '.section1button button', function () {
            var id = jQuery(this).attr('data-id');
            jQuery(this)
                .closest('.woocommerce_variable_attributes')
                .find('.section1data')
                .append(
                    '<div class="sectiondatasingle">' +
                        '<input name="section1data[' + id + '][]" class="short section1datainput" />' +
                        '<button class="aksahr-remove" type="button">X</button>' +
                    '</div>'
                );
        });

        jQuery(document).on('click', '.aksahr-remove', function () {
            jQuery(this).closest('.sectiondatasingle').remove();

            // Trigger WooCommerce to detect changes so Save button enables
            jQuery('.woocommerce_variation').each(function (i) {
                var $desc = jQuery('#variable_description' + i);
                $desc.val($desc.val() + ' ').trigger('change');
            });
        });

        jQuery(document).on(
            'keyup change',
            '.section1datainput, .sectionlabel',
            function () {
                // Trigger WooCommerce change detection
                var $variation = jQuery(this).closest('.woocommerce_variation');
                var index = jQuery('.woocommerce_variation').index($variation);
                var $desc = jQuery('#variable_description' + index);
                if ($desc.length) {
                    $desc.val($desc.val()).trigger('change');
                }
            }
        );
    </script>
    <?php
} );


add_action('wp_head', 'custom_css_for_specific_products');
function custom_css_for_specific_products() {
    if (!is_product()) return;

    global $post;

    $target_slugs = [
        'round-shape-calibrated-diamonds',
        'princess-shape-cvd-type-lla-diamonds',
        'pear-shape-lab-grown-hpht-diamonds',
        'oval-shape-lab-grown-hpht-diamonds',
        'marquise-shape-lab-grown-hpht-diamonds',
        'heart-shape-lab-grown-hpht-diamonds',
        'emerald-shape-lab-grown-hpht-diamonds',
        'tapper-baguette-shape-lab-grown-hpht-diamonds',
        'triangle-shape-lab-grown-hpht-diamonds',
        'shield-shape-lab-grown-hpht-diamonds',
        'hexagon-shape-lab-grown-hpht-diamonds',
        'trapezoid-shape-lab-grown-hpht-diamonds',
        'pink-color-diamond',
        'intense-pink-color-diamond',
        'yellow-color-diamond',
        'green-color-diamond',
        'purple-color-diamond',
        'blue-color-diamond',
    ];
    $target_categories = ['custom'];
    $target_tags = ['single-image']; // add your tag slugs here

    if (
        in_array($post->post_name, $target_slugs) ||
        has_term($target_categories, 'product_cat', $post) ||
        has_term($target_tags, 'product_tag', $post)
    ) {

        echo '<style>
        .product_image .ls_rimg_glr{
                padding: 0;
        }
        .product_image .img{
                margin-bottom: 0;
        }
           .ls_rimg_glr .thumbs{
            display: none;
           }
           .ls_slide_arrows {
            display: none;
           }
        </style>';
    }
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
    if (is_page_template(array( 'page-ring-preview.php')) || is_product() || get_query_var('diamond_sku')) {
        ?>
        <style>
            
        </style>
        <script>
         /* =========================================================
         * DIAMOND CANVAS LABELS (already vanilla, unchanged)
         * ========================================================= */
        document.querySelectorAll(".diamond-canvas").forEach((canvas) => {
            const ctx = canvas.getContext("2d");

            let labels = [];
            try {
                labels = JSON.parse(canvas.dataset.labels || "[]");
            } catch (e) {
                console.warn("Invalid data-labels JSON on canvas:", canvas, e);
            }

            const dpr = window.devicePixelRatio || 1;
            const cssWidth  = parseInt(canvas.getAttribute("width"), 10) || 500;
            const cssHeight = parseInt(canvas.getAttribute("height"), 10) || 500;

            canvas.width  = cssWidth * dpr;
            canvas.height = cssHeight * dpr;

            ctx.scale(dpr, dpr);
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = "high";

            const cw = cssWidth;
            const ch = cssHeight;

            ctx.clearRect(0, 0, cw, ch);

            const paddingX = 8;
            const labelHeight = 28;

            labels.forEach((label) => {
                const text = label.text || "";
                const x = typeof label.x === "number" ? label.x : cw / 2;
                const y = typeof label.y === "number" ? label.y : ch / 2;

                ctx.font = label.font || "500 15px Arial";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";

                const metrics = ctx.measureText(text);

                ctx.fillStyle = label.bg || "#fcfbf7";
                ctx.fillRect(
                    x - metrics.width / 2 - paddingX,
                    y - labelHeight / 2,
                    metrics.width + paddingX * 2,
                    labelHeight
                );

                ctx.fillStyle = label.color || "#333333";
                ctx.fillText(text, x, y);
            });
        });

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
    if ( $cart_item['c_type'] == 1 || $cart_item['c_type'] == 3 ) {

        $line_total = $cart_item['line_total'];

        /*$regular_price = (float) $_product->get_regular_price();
        $qty           = (int) $cart_item['quantity'];

        $line_total = $regular_price * $qty;*/
    } else {
        //$line_total = $cart_item['line_total'];
        $line_total = (float) $cart_item['line_subtotal'];
    }

    // $exclude_extras = true → show price WITHOUT Extra Features (upgrades).
    // apply_price() adds extra_features_total per unit, so remove it x qty.
    if ( $exclude_extras && ! empty( $cart_item['ls_ring_builder']['extra_features_total'] ) ) {
        $extra_total = (float) $cart_item['ls_ring_builder']['extra_features_total'] * (int) $cart_item['quantity'];
        $line_total  = max( 0, (float) $line_total - $extra_total );
    }

    echo wp_kses_post( wc_price( $line_total ) );
}
function cartwasrprice($cart_item,$_product,$cart_item_key){
   // echo "dd".$cart_item['c_type'];
    if($cart_item['c_type']==1 || $cart_item['c_type']==3){
        
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
       
    }else{
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

    
}

function checkoutregularprice($cart_item, $_product, $cart_item_key) {
    $line_total = $cart_item['line_total'];
    echo '<span class="oc-current-price">' .wp_kses_post( wc_price( $line_total ) ). '</span>';
}
function checkoutwasprice($cart_item,$_product,$cart_item_key){
    $regular_price = $_product->get_regular_price();
    $current_price = $_product->get_price();
    if($cart_item['c_type']==1 || $cart_item['c_type']==3){
        $regular_price = (float) $_product->get_regular_price();
        $qty           = (int) $cart_item['quantity'];
        $regular_total = $regular_price * $qty;
        echo '<span class="oc-regular-price" >was &nbsp;' . wc_price($regular_total) . '</span>';
    }else{
        if ( $regular_price && $regular_price != $current_price ) {
            $regular_subtotal = wc_price( floatval( $regular_price ) * $cart_item['quantity'] );
            echo '<span class="oc-regular-price" >was &nbsp;' . $regular_subtotal . '</span>';
        }
    }
}
/**
 * Is this diamond (product id) currently SET IN A RING in the cart?
 * True only when a ring-setting cart line (ls_rb_type = setting_prdct)
 * is paired with this diamond (d_pid). A loose diamond (normal Add To Bag),
 * or an orphan diamond line whose setting was removed, returns false.
 */
if ( ! function_exists( 'ls_is_diamond_in_cart_ring' ) ) {
    function ls_is_diamond_in_cart_ring( $pid ) {
        if ( ! $pid || ! function_exists( 'WC' ) || ! WC()->cart ) return false;
        foreach ( WC()->cart->get_cart() as $ci ) {
            $t = $ci['ls_ring_builder']['ls_rb_type'] ?? '';
            if ( $t === 'setting_prdct' && (string) ( $ci['ls_ring_builder']['d_pid'] ?? '' ) === (string) $pid ) {
                return true;
            }
        }
        return false;
    }
}

