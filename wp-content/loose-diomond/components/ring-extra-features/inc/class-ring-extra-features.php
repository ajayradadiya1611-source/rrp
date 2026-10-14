<?php
/**
 * Ring Extra Features — data layer + front-end + cart integration
 *
 * Groups AND options are fixed in code (inc/group-definitions.php):
 * Band Width, Band Style, Prong, East West, Two Tone. Only PRICE
 * varies per product, via a plain ACF number field per option — leave
 * a field blank to not offer that option on a given product.
 *
 * Shows up in THREE places, all driven by the same product-ID-aware
 * render/verify methods:
 *   1. Ring Preview / builder page — page-ring-preview.php calls
 *      render_button_and_modal( $sp_id ) directly, and
 *      ls_rb_addtobag() in functions.php (tagged "LGD EXTRA FEATURES")
 *      re-verifies the posted selection before creating the cart item.
 *   2. This theme's actual single-product.php — a fully custom
 *      template that never calls WooCommerce's standard add-to-cart
 *      hooks, so it's wired via tagged edits directly in that file
 *      (see modified-theme-files/single-product.php) instead of a
 *      hook, and capture_cart_item_data() below parses that template's
 *      own custom AJAX payload shape ($_POST['form_data']) rather than
 *      assuming a flat $_POST field.
 *   3. woocommerce_before_add_to_cart_button / woocommerce_add_cart_item_data
 *      hooks are still registered below as a compatibility fallback —
 *      harmless no-ops on this site's actual template, but they mean
 *      this component keeps working unmodified on any other theme (or
 *      a future template rewrite) that renders a standard WooCommerce
 *      add-to-cart form instead of this one's custom markup.
 *
 * Cart:  price is re-verified from that product's own ACF data
 *        server-side on add-to-cart — every path above — then applied
 *        to the cart item price via woocommerce_before_calculate_totals
 *        below. Never trusts the client's own total or label.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class LGD_Ring_Extra_Features {

    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'front_assets' ) );

        // Path 2: normal single product pages (see class docblock above).
        add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_on_single_product' ) );
        add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'capture_cart_item_data' ), 20, 3 );

        add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'apply_price' ) );
        add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'show_in_cart' ), 10, 2 );
        add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'save_to_order_item' ), 10, 4 );

        // Cart page: remove one upgrade from a cart item without removing
        // the whole line (the setting + diamond stay in the cart).
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'cart_assets' ) );
        add_action( 'wp_ajax_ls_ref_remove_upgrade', array( __CLASS__, 'ajax_remove_upgrade' ) );
        add_action( 'wp_ajax_nopriv_ls_ref_remove_upgrade', array( __CLASS__, 'ajax_remove_upgrade' ) );

        // Cart page: "Add Extra Feature" / "Edit Upgrades" button on the
        // setting line item itself. render_cart_edit_button() is called
        // directly from woocommerce/cart/cart.php (not hooked here) —
        // see that template's oc_woocommerce_after_cart_item_name spot.
        add_action( 'wp_ajax_ls_ref_update_upgrades', array( __CLASS__, 'ajax_update_upgrades' ) );
        add_action( 'wp_ajax_nopriv_ls_ref_update_upgrades', array( __CLASS__, 'ajax_update_upgrades' ) );
    }

    /**
     * Renders the button + modal inline inside WooCommerce's own
     * <form class="cart"> on a normal single product page — no
     * template edit needed. Renders nothing if this product has no
     * upgrade options priced (same guard as the ring-builder path).
     */
    public static function render_on_single_product() {
        global $product;
        if ( ! $product instanceof WC_Product ) return;
        self::render_button_and_modal( $product->get_id() );
    }

    /**
     * Re-verifies a posted selection and attaches it to the cart item.
     * This filter fires on EVERY call to WC_Cart::add_to_cart(),
     * regardless of what triggered it — including this theme's own
     * custom_ajax_add_to_cart_handler() in functions.php, which is how
     * the single-product "Add To Bag" buttons actually work here. That
     * handler doesn't post a flat $_POST['extra_features'] though — it
     * posts the whole serialized form as one string,
     * $_POST['form_data'], and parses it internally with parse_str().
     * So we do the same parsing here rather than assuming a flat
     * $_POST field. Falls back to a flat $_POST['extra_features'] for
     * any other add-to-cart path (e.g. a default WooCommerce form
     * submit) that doesn't nest things this way.
     *
     * Runs at priority 20 so it doesn't interfere with this theme's
     * existing add_product_meta_to_cart callback on the same filter.
     */
    public static function capture_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
        $raw_json = null;

        if ( ! empty( $_POST['form_data'] ) ) {
            $parsed_form = array();
            parse_str( wp_unslash( $_POST['form_data'] ), $parsed_form );
            if ( ! empty( $parsed_form['extra_features'] ) ) {
                $raw_json = $parsed_form['extra_features'];
            }
        }

        if ( $raw_json === null && ! empty( $_POST['extra_features'] ) ) {
            $raw_json = wp_unslash( $_POST['extra_features'] );
        }
        
        if ( empty( $raw_json ) ) return $cart_item_data;

        $raw = json_decode( $raw_json, true );
        $priced_id = $product_id; // ACF fields live on the specific product/variation being priced
        $priced = self::verify_and_price_selection( $raw, $priced_id );

        if ( $priced['total'] > 0 ) {
            $cart_item_data['ls_ring_builder']['extra_features_total'] = $priced['total'];
            $cart_item_data['ls_ring_builder']['extra_features_items'] = $priced['items'];
        }
        
        return $cart_item_data;
    }

    /* -----------------------------------------------------------
     * OPTION LOOKUP (per product)
     * --------------------------------------------------------- */

    /**
     * An option's price on this product, or null if not offered (its
     * ACF number field left blank).
     */
    public static function get_fixed_option_price( $product_id, $option_key ) {
        if ( ! function_exists( 'get_field' ) ) return null; // ACF not active
        $value = get_field( lgd_ref_price_field_name( $option_key ), $product_id );
        if ( $value === '' || $value === null || $value === false ) return null;
        return (float) $value;
    }

    /**
     * Groups + options actually available for this product — only
     * options with a price set on THIS product. A group with zero
     * available options is dropped entirely.
     */
    public static function get_available_groups_for_product( $product_id ) {
        $groups = lgd_ref_get_group_definitions();
        $result = array();

        foreach ( $groups as $group_key => $group ) {
            $options = array();

            foreach ( $group['options'] as $option_key => $option_def ) {
                $price = self::get_fixed_option_price( $product_id, $option_key );
                if ( $price === null ) continue; // not offered on this product
                $normalized = lgd_ref_normalize_option( $option_def );
                $options[ $option_key ] = array(
                    'label' => $normalized['label'],
                    'note'  => $normalized['note'],
                    'price' => $price,
                );
            }

            if ( empty( $options ) ) continue;

            $result[ $group_key ] = array(
                'label'          => $group['label'],
                'subtitle'       => $group['subtitle'] ?? null,
                'selection_type' => $group['selection_type'],
                'options'        => $options,
            );
        }

        return $result;
    }

    /**
     * Recomputes the true price of a posted selection server-side for
     * a specific product — called from ls_rb_addtobag() in
     * functions.php. Never trusts the client's own price or label; it
     * always re-reads straight from that product's own ACF field.
     *
     * $raw_selection: decoded JSON the front-end posts, e.g.
     *   [{"group_key":"band_width","option_keys":["band_2_00mm"]},
     *    {"group_key":"east_west","option_keys":["east_west"]}]
     *
     * @return array{total: float, items: array<array{name:string, price:float}>}
     */
    public static function verify_and_price_selection( $raw_selection, $product_id ) {
        $groups = lgd_ref_get_group_definitions();
        $total  = 0.0;
        $items  = array();

        if ( empty( $raw_selection ) || ! is_array( $raw_selection ) ) {
            return array( 'total' => 0.0, 'items' => array() );
        }

        foreach ( $raw_selection as $group_sel ) {
            $group_key = isset( $group_sel['group_key'] ) ? sanitize_key( $group_sel['group_key'] ) : '';
            if ( ! isset( $groups[ $group_key ] ) ) continue;
            if ( empty( $group_sel['option_keys'] ) || ! is_array( $group_sel['option_keys'] ) ) continue;

            $group = $groups[ $group_key ];

            foreach ( $group_sel['option_keys'] as $option_key ) {
                $option_key = sanitize_key( $option_key );
                if ( ! isset( $group['options'][ $option_key ] ) ) continue; // not a real option

                $price = self::get_fixed_option_price( $product_id, $option_key );
                if ( $price === null ) continue; // not offered on this product — ignore even if posted

                $normalized = lgd_ref_normalize_option( $group['options'][ $option_key ] );
                $total  += $price;
                $items[] = array(
                    'name'      => $normalized['label'],
                    'price'     => $price,
                    'group_key' => $group_key,
                    'option_key' => $option_key,
                );
            }
        }

        return array( 'total' => round( $total, 2 ), 'items' => $items );
    }

    /* -----------------------------------------------------------
     * FRONT-END: assets + mount point markup
     * --------------------------------------------------------- */

    /**
     * Enqueues the compiled React bundle (built from
     * components/ring-extra-features/react via
     * vites/vite.ring-extra-features.config.js — same build pattern as
     * this theme's other components). Run `npm run build` from the
     * theme root after any change to that React app.
     */
    public static function front_assets() {
        if ( ! is_page_template( 'page-ring-preview.php' ) && ! is_product()
            && ! ( function_exists( 'is_cart' ) && is_cart() ) ) return;

        $dist = get_template_directory() . '/out/ring-extra-features';
        $uri  = get_template_directory_uri() . '/out/ring-extra-features';

        if ( ! file_exists( $dist . '/index.js' ) ) return; // not built yet — run `npm run build`

        wp_enqueue_script(
            'lgd-ref-front',
            $uri . '/index.js',
            array(),
            filemtime( $dist . '/index.js' ),
            true
        );
        wp_script_add_data( 'lgd-ref-front', 'type', 'module' ); // built as an ES module by Vite

        $css_file = $dist . '/assets/index.css';
        if ( file_exists( $css_file ) ) {
            wp_enqueue_style( 'lgd-ref-front', $uri . '/assets/index.css', array(), filemtime( $css_file ) );
        }
    }

    /**
     * Call this from page-ring-preview.php, right before the
     * "Add To Bag" button, inside the .ring-builder-form — pass the
     * setting product's ID so options/prices shown match that product.
     *
     * Outputs a mount point only (a plain div with the product's
     * available groups/prices serialized into data-* attributes) — the
     * React app (main.jsx) finds every such mount point on the page and
     * renders an independent button+modal instance into each. Each
     * instance manages its own state via React's component state, so —
     * unlike the previous jQuery version — nothing is shared across
     * instances even if this method is called more than once on one
     * page (a quick-view popup, an upsell/related-product widget, etc.).
     *
     * $preselected_raw_json: optional, a raw JSON string in the same
     * shape the front-end itself posts (e.g. carried over from the
     * "Select This Setting" step's session — see ls_rb_addtobag() and
     * handle_ring_builder_setting() in functions.php). Pre-fills the
     * instance's initial state so the choice isn't lost, and is
     * reflected visually (button label, selected cards, live price)
     * without the customer having to reopen the modal.
     */
    public static function render_button_and_modal( $product_id, $preselected_raw_json = '', $cart_item_key = '' ) {
        $groups = self::get_available_groups_for_product( $product_id );
        if ( empty( $groups ) ) return; // no upgrades priced for this product — button stays hidden

        // Default selection on the product page too: if nothing was passed
        // in, fall back to the ring-builder session selection when it
        // belongs to this same setting product (so the choice made earlier
        // is pre-selected on single-product.php, not only on Ring Preview).
        if ( $preselected_raw_json === '' && $cart_item_key === '' ) {
            if ( session_status() === PHP_SESSION_NONE && ! headers_sent() ) {
                @session_start();
            }
            $rb_setting = $_SESSION['ls_ring_builder']['setting'] ?? array();
            if ( ! empty( $rb_setting['extra_features_raw'] )
                && isset( $rb_setting['sp_id'] )
                && (int) $rb_setting['sp_id'] === (int) $product_id ) {
                $preselected_raw_json = (string) $rb_setting['extra_features_raw'];
            }
        }

        $currency  = get_woocommerce_currency_symbol();
        $image_base = get_template_directory_uri() . '/components/ring-extra-features/front/images';
        ?>
        <div
            class="ls-ref-mount"
            data-groups="<?php echo esc_attr( wp_json_encode( $groups ) ); ?>"
            data-preselected="<?php echo esc_attr( $preselected_raw_json ); ?>"
            data-currency="<?php echo esc_attr( $currency ); ?>"
            data-image-base="<?php echo esc_attr( $image_base ); ?>"
            data-input-name="extra_features"
            data-product-id="<?php echo esc_attr( $product_id ); ?>"
            <?php if ( $cart_item_key !== '' ) : ?>
            data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"
            <?php endif; ?>
            data-simple-price="<?php
                // Only meaningful for simple products — variable products
                // get their live price from the variations_form JS events
                // instead (see RingExtraFeatures/index.jsx). Output the
                // raw numeric price (no currency symbol/HTML) so the
                // front-end can add it to this component's own total.
                $ref_product = wc_get_product( $product_id );
                echo esc_attr( $ref_product ? $ref_product->get_price() : '' );
            ?>"
        ></div>
        <?php
    }

    /* -----------------------------------------------------------
     * CART PRICE + DISPLAY
     * --------------------------------------------------------- */

    /**
     * Adds the verified upgrades total to the setting cart item's
     * price. Re-derives the base price fresh each run so repeated
     * calls to this hook in one request never compound the total.
     */
    public static function apply_price( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
        foreach ( $cart->get_cart() as $cart_item ) {
            if ( empty( $cart_item['ls_ring_builder']['extra_features_total'] ) ) continue;
            $extra = (float) $cart_item['ls_ring_builder']['extra_features_total'];
            // Use the VARIATION's own price when the item is a variation
            // (e.g. 14K White Gold vs Platinum) — the parent product's
            // price is not the price of the metal the customer picked.
            $price_source_id = ! empty( $cart_item['variation_id'] )
                ? (int) $cart_item['variation_id']
                : (int) $cart_item['product_id'];
            $base_product = wc_get_product( $price_source_id );
            if ( ! $base_product ) continue;
            $base_price = (float) $base_product->get_price();
            $cart_item['data']->set_price( $base_price + $extra );
        }
    }

    public static function show_in_cart( $item_data, $cart_item ) {
        if ( ! empty( $cart_item['ls_ring_builder']['extra_features_items'] ) ) {
            // Cart page only (not checkout review, emails, etc.) — this is
            // where the remove link is interactive.
            $is_cart_page = ! is_admin() && function_exists( 'is_cart' ) && is_cart();

            foreach ( $cart_item['ls_ring_builder']['extra_features_items'] as $feature ) {
                $value = esc_html( $feature['name'] ) . ' (+' . get_woocommerce_currency_symbol() . number_format( $feature['price'], wc_get_price_decimals() ) . ')';

                if ( $is_cart_page && isset( $feature['option_key'] ) ) {
                    $value .= sprintf(
                        ' <a href="#" class="ls-ref-cart-remove" data-cart-item-key="%1$s" data-option-key="%2$s" aria-label="%3$s"><i class="fa fa-times" aria-hidden="true"></i></a>',
                        esc_attr( $cart_item['key'] ?? '' ),
                        esc_attr( $feature['option_key'] ),
                        esc_attr( sprintf( 'Remove %s', $feature['name'] ) )
                    );
                }

                $item_data[] = array(
                    'key'   => 'Upgrade',
                    'value' => $value,
                );
            }
        }
        return $item_data;
    }

    /**
     * Enqueues the small cart-page script that wires up .ls-ref-cart-remove
     * links. Vanilla JS, no build step — kept separate from the ring
     * builder's React bundle since it only ever runs on the cart page.
     */
    public static function cart_assets() {
        if ( ! function_exists( 'is_cart' ) || ! is_cart() ) return;

        wp_register_script( 'lgd-ref-cart-remove', false, array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'lgd-ref-cart-remove' );
        wp_add_inline_script( 'lgd-ref-cart-remove', self::cart_remove_inline_js() );
        wp_localize_script( 'lgd-ref-cart-remove', 'lgdRefCart', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ls_ref_remove_upgrade' ),
            'updateNonce' => wp_create_nonce( 'ls_ref_update_upgrades' ),
        ) );

        wp_register_style( 'lgd-ref-cart-remove', false, array(), '1.0' );
        wp_enqueue_style( 'lgd-ref-cart-remove' );
      
    }

    private static function cart_remove_inline_js() {
        return <<<JS
jQuery(function($){
    $(document.body).on('click', '.ls-ref-cart-remove', function(e){
        e.preventDefault();
        var \$link = $(this);
        if (\$link.hasClass('is-removing')) return;
        \$link.addClass('is-removing');

        $.post(lgdRefCart.ajaxUrl, {
            action: 'ls_ref_remove_upgrade',
            nonce: lgdRefCart.nonce,
            cart_item_key: \$link.data('cart-item-key'),
            option_key: \$link.data('option-key')
        }).done(function(response){
            if (response && response.success) {
                // Simplest reliable way to reflect the new price/meta
                // everywhere on the page (mini-cart, totals, item list).
                window.location.reload();
            } else {
                \$link.removeClass('is-removing');
                alert((response && response.data && response.data.message) || 'Could not remove that upgrade. Please try again.');
            }
        }).fail(function(){
            \$link.removeClass('is-removing');
            alert('Could not remove that upgrade. Please try again.');
        });
    });
});
JS;
    }

    /**
     * AJAX: remove one upgrade option from one cart item's stored
     * selection, re-total the remaining ones, and persist. Leaves the
     * cart item itself (setting + diamond) untouched.
     */
    /**
     * Converts a stored items array (name/price/group_key/option_key, as
     * kept on the cart item) back into the posted-selection shape this
     * class's own front-end and verify_and_price_selection() expect —
     * used to pre-fill the cart-page modal and to keep the "Rebuild Ring"
     * snapshot in sync after a cart-page edit.
     */
    public static function items_to_raw_json( $items ) {
        if ( empty( $items ) ) return '';
        return wp_json_encode( array_map( function ( $item ) {
            return array(
                'group_key'   => $item['group_key'],
                'option_keys' => array( $item['option_key'] ),
            );
        }, $items ) );
    }

    /**
     * Keeps $_SESSION['ls_ring_builder_cartadded'][$cart_item_key]['setting']['extra_features_raw']
     * in sync with a cart item's current items, so "Rebuild Ring" (see
     * ls_change_rb() in comman-function.php) never restores a stale
     * selection after an edit made here or in ajax_remove_upgrade().
     */
    public static function sync_cartadded_snapshot( $cart_item_key, $items ) {
        /*print_r($_SESSION);
        exit;*/
        if ( ! isset( $_SESSION['ls_ring_builder_cartadded'][ $cart_item_key ]['setting'] ) ) return;

        if ( ! empty( $items ) ) {
            $_SESSION['ls_ring_builder_cartadded'][ $cart_item_key ]['setting']['extra_features_raw'] = self::items_to_raw_json( $items );
        } else {
            unset( $_SESSION['ls_ring_builder_cartadded'][ $cart_item_key ]['setting']['extra_features_raw'] );
        }
    }

    public static function ajax_remove_upgrade() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ls_ref_remove_upgrade' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid request.' ), 403 );
        }

        $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
        $option_key    = isset( $_POST['option_key'] ) ? sanitize_key( wp_unslash( $_POST['option_key'] ) ) : '';

        if ( ! $cart_item_key || ! $option_key || ! WC()->cart ) {
            wp_send_json_error( array( 'message' => 'Missing data.' ), 400 );
        }

        $cart_contents = WC()->cart->get_cart();
        if ( empty( $cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'] ) ) {
            wp_send_json_error( array( 'message' => 'Upgrade not found.' ), 404 );
        }

        $items = $cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'];
        $items = array_values( array_filter( $items, function ( $item ) use ( $option_key ) {
            return ! isset( $item['option_key'] ) || $item['option_key'] !== $option_key;
        } ) );

        $new_total = array_sum( wp_list_pluck( $items, 'price' ) );

        if ( ! empty( $items ) ) {
            WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'] = $items;
            WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_total'] = $new_total;
        } else {
            // No upgrades left on this item at all — drop the ring builder
            // meta entirely so apply_price()/show_in_cart() stop firing for it.
            unset( WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'] );
            unset( WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_total'] );
        }

        // Keep the "Rebuild Ring" snapshot in sync (see helper docblock).
        self::sync_cartadded_snapshot( $cart_item_key, $items );

        WC()->cart->set_session();
        WC()->cart->calculate_totals();

        wp_send_json_success();
    }

    /**
     * Renders the "Add Extra Feature" / "Edit Upgrades" button + modal
     * directly on the cart page's setting line item, pre-filled with
     * whatever is already on that cart item, so a selection can be added
     * or changed without leaving the cart. Called directly from
     * woocommerce/cart/cart.php (not hooked) — see that template's
     * oc_woocommerce_after_cart_item_name spot.
     * Renders nothing for the diamond line, for a non-ring-builder item,
     * or off the cart page (front_assets() also gates the JS bundle to
     * is_cart() so this markup is inert everywhere else).
     */
    public static function render_cart_edit_button( $cart_item, $cart_item_key ) {
        if ( ! function_exists( 'is_cart' ) || ! is_cart() ) return;
        $ls_rb_type = $cart_item['ls_ring_builder']['ls_rb_type'] ?? '';
        if ( $ls_rb_type !== 'setting_prdct' ) return;

        $product_id = $cart_item['product_id'];
        $existing_items = $cart_item['ls_ring_builder']['extra_features_items'] ?? array();
        $preselected_raw = self::items_to_raw_json( $existing_items );

        self::render_button_and_modal( $product_id, $preselected_raw, $cart_item_key );
    }

    /**
     * AJAX: replace a cart item's whole upgrade selection with what was
     * just confirmed in the cart-page modal (render_cart_edit_button()).
     * Re-verifies and re-prices every option server-side exactly like
     * every other add/remove path in this class — never trusts the
     * client's own total or labels.
     */
    public static function ajax_update_upgrades() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ls_ref_update_upgrades' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid request.' ), 403 );
        }

        $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
        if ( ! $cart_item_key || ! WC()->cart ) {
            wp_send_json_error( array( 'message' => 'Missing data.' ), 400 );
        }

        $cart_contents = WC()->cart->get_cart();
        if ( empty( $cart_contents[ $cart_item_key ] ) ) {
            wp_send_json_error( array( 'message' => 'Cart item not found.' ), 404 );
        }

        $raw_json = ! empty( $_POST['extra_features'] ) ? wp_unslash( $_POST['extra_features'] ) : '';
        $raw_selection = $raw_json ? json_decode( $raw_json, true ) : array();

        $cart_item  = $cart_contents[ $cart_item_key ];
        $priced_id  = $cart_item['product_id'];
        $priced     = self::verify_and_price_selection( $raw_selection, $priced_id );

        if ( $priced['total'] > 0 ) {
            WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_total'] = $priced['total'];
            WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'] = $priced['items'];
        } else {
            unset( WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_items'] );
            unset( WC()->cart->cart_contents[ $cart_item_key ]['ls_ring_builder']['extra_features_total'] );
        }

        self::sync_cartadded_snapshot( $cart_item_key, $priced['items'] );

        WC()->cart->set_session();
        WC()->cart->calculate_totals();

        wp_send_json_success();
    }

    public static function save_to_order_item( $item, $cart_item_key, $values, $order ) {
        if ( ! empty( $values['ls_ring_builder']['extra_features_items'] ) ) {
            foreach ( $values['ls_ring_builder']['extra_features_items'] as $feature ) {
                $item->add_meta_data( 'Upgrade', $feature['name'] . ' (+' . wc_price( $feature['price'] ) . ')' );
            }
        }
    }
}