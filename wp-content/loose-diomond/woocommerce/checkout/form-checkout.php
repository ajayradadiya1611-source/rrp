<?php
/**
 * Checkout Form
 * ...
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
echo "<h3>Checkout</h3>";
do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
	<div class="row">
		<div class="col-lg-8 col-md-7">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="col2-set" id="customer_details">
					<div class="col-lg-12">
						<h3>1. Shipping Details</h3>
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
                        
						<?php
						// Render the billing email field (label + input) here, under shipping
						$email_field = $checkout->get_checkout_fields( 'billing' )['billing_email'] ?? null;
						if ( $email_field ) :
						?>
                        
						<?php endif; ?>
					</div>
                    <div class="checkout-shipping-method lgd-ship">
                        <h3 class="lgd-ship__title">Shipping Method</h3>
                        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                            <?php
                            /*
                             * Business-day date helpers (skip Sat/Sun), site timezone.
                             */
                            if ( ! function_exists( 'lgd_ship_add_business_days' ) ) {
                                function lgd_ship_add_business_days( $days ) {
                                    $d = new DateTime( 'now', wp_timezone() );
                                    $days = max( 0, (int) $days );
                                    while ( $days > 0 ) {
                                        $d->modify( '+1 day' );
                                        if ( (int) $d->format( 'N' ) < 6 ) {
                                            $days--;
                                        }
                                    }
                                    return $d;
                                }
                            }
                            if ( ! function_exists( 'lgd_ship_method_type' ) ) {
                                function lgd_ship_method_type( $method ) {
                                    $label = strtolower( $method->get_label() );
                                    if ( strpos( $label, 'overnight' ) !== false || strpos( $label, 'next day' ) !== false ) {
                                        return 'overnight';
                                    }
                                    if ( strpos( $label, 'express' ) !== false || strpos( $label, '2 day' ) !== false || strpos( $label, '2-day' ) !== false ) {
                                        return 'express';
                                    }
                                    if ( 'free_shipping' === $method->get_method_id() || strpos( $label, 'free' ) !== false || 0 >= (float) $method->get_cost() ) {
                                        return 'free';
                                    }
                                    return 'standard';
                                }
                            }

                            // Processing days = slowest item in the cart.
                            $lgd_ship_days = 0;
                            foreach ( WC()->cart->get_cart() as $lgd_ci ) {
                                if ( function_exists( 'ls_get_cart_item_delivery_days' ) ) {
                                    $lgd_ship_days = max( $lgd_ship_days, (int) ls_get_cart_item_delivery_days( $lgd_ci ) );
                                }
                            }
                            $lgd_same_day = $lgd_ship_days <= 1;
                            $lgd_dispatch = $lgd_same_day ? 0 : $lgd_ship_days;

                            // Transit business days per type: [min, max]. Filterable.
                            $lgd_transit = apply_filters( 'lgd_shipping_transit_days', array(
                                'overnight' => array( 1, 1 ),
                                'express'   => array( 2, 3 ),
                                'free'      => array( 7, 7 ),
                                'standard'  => array( 5, 7 ),
                            ), $lgd_ship_days );

                            $lgd_tomorrow = lgd_ship_add_business_days( 1 )->format( 'Y-m-d' );
                            $lgd_tz_now   = new DateTime( 'now', wp_timezone() );
                            $lgd_tz_now->modify( '+1 day' );
                            $lgd_cal_tomorrow = $lgd_tz_now->format( 'Y-m-d' );

                            $packages = WC()->shipping()->get_packages();
                            foreach ( $packages as $index => $package ) :
                                $available_methods = $package['rates'];
                                $chosen_method     = WC()->session->get( 'chosen_shipping_methods' )[ $index ] ?? '';
                                if ( ! empty( $available_methods ) ) :
                                    if ( '' === $chosen_method || ! isset( $available_methods[ $chosen_method ] ) ) {
                                        $chosen_method = current( $available_methods )->get_id();
                                    }
                            ?>
                                <ul id="shipping_method" class="woocommerce-shipping-methods lgd-ship__list">
                                    <?php foreach ( $available_methods as $method ) :
                                        $type    = lgd_ship_method_type( $method );
                                        $range   = $lgd_transit[ $type ] ?? $lgd_transit['standard'];
                                        $from    = lgd_ship_add_business_days( $lgd_dispatch + $range[0] );
                                        $to      = lgd_ship_add_business_days( $lgd_dispatch + $range[1] );
                                        $is_rec  = ( 'overnight' === $type && $lgd_same_day );

                                        if ( $from->format( 'Y-m-d' ) === $to->format( 'Y-m-d' ) ) {
                                            $ymd = $from->format( 'Y-m-d' );
                                            $arrives = ( $ymd === $lgd_cal_tomorrow )
                                                ? 'Arrives tomorrow, ' . $from->format( 'D M j' )
                                                : 'Arrives by ' . $from->format( 'D, M j' );
                                        } else {
                                            $arrives = 'Arrives ' . $from->format( 'D, M j' ) . ' &ndash; ' . $to->format( 'D, M j' );
                                        }

                                        // Price incl. tax when the store displays it that way.
                                        $cost = (float) $method->get_cost();
                                        if ( WC()->cart->display_prices_including_tax() ) {
                                            $cost += array_sum( (array) $method->get_shipping_tax() );
                                        }
                                        $price_html = $cost > 0 ? wc_price( $cost ) : esc_html__( 'Free', 'woocommerce' );

                                        $input_id = sprintf( 'shipping_method_%1$d_%2$s', $index, sanitize_title( $method->id ) );
                                        $checked  = ( $method->id === $chosen_method );
                                    ?>
                                      <li class="lgd-ship__item<?php echo $checked ? ' is-selected' : ''; ?><?php echo $is_rec ? ' is-recommended' : ''; ?>">
                                        <?php printf(
                                            '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="%2$s" value="%3$s" class="shipping_method lgd-ship__radio" %4$s />',
                                            $index,
                                            esc_attr( $input_id ),
                                            esc_attr( $method->id ),
                                            checked( $checked, true, false )
                                        ); ?>
                                        <label for="<?php echo esc_attr( $input_id ); ?>" class="lgd-ship__card">
                                            <?php if ( $is_rec ) : ?><span class="lgd-ship__badge">Recommended</span><?php endif; ?>
                                            <span class="lgd-ship__dot" aria-hidden="true"></span>
                                            <span class="lgd-ship__info">
                                                <span class="lgd-ship__name">
                                                    <?php echo esc_html( trim( preg_replace( array( '/\s*:\s*\$\s*[\d.,]+\s*/', '/\s+\)/' ), array( '', ')' ), $method->get_label() ) ) ); ?>
                                                    <?php if ( 'overnight' === $type ) : ?>
                                                        <svg class="lgd-ship__bolt" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="lgd-ship__eta"><?php echo wp_kses_post( $arrives ); ?></span>
                                                <?php if ( $is_rec ) : ?>
                                                    <span class="lgd-ship__note">This diamond ships same-day &mdash; overnight available</span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="lgd-ship__price"><?php echo wp_kses_post( $price_html ); ?></span>
                                        </label>
                                        <?php do_action( 'woocommerce_after_shipping_rate', $method, $index ); ?>
                                      </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php
                                endif;
                            endforeach;
                            ?>
                            <p class="lgd-ship__foot">Insured, signature-required delivery on all options.</p>
                            <script>
                            (function () {
                                document.addEventListener('change', function (e) {
                                    if (!e.target.classList || !e.target.classList.contains('lgd-ship__radio')) return;
                                    var list = e.target.closest('.lgd-ship__list');
                                    if (!list) return;
                                    list.querySelectorAll('.lgd-ship__item').forEach(function (li) {
                                        li.classList.toggle('is-selected', li.contains(e.target));
                                    });
                                });
                            })();
                            </script>
                        <?php endif; ?>
                    </div>
					<div class="col-lg-12">
              <div class="oc-woocommerce-billing-fields">
              <div class="oc-billing-details-header">
              <h3>2. Billing Details</h3>
              
              <div class="same_as_ship">
                <input type="checkbox" checked name="same_as_shipping_address" id="same_as_shipping_address"> <label>Same as shipping address</label>
              </div>
              <?php do_action( 'woocommerce_checkout_billing' ); ?>
              </div>
            
            </div>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			

			<div class="checkout-payment">
                <div class="checkout-payment-header">
                    <h3>3. Payment Option</h3>
                    <span><i class="fa fa-lock" aria-hidden="true"></i> Secure & encrypted</span>
                </div>
				
				<?php woocommerce_checkout_payment(); ?>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 order-dk-right">
            <div class="d-flex  flex-column gap-2">
    			<div class="oc-checkout-order-review">
    				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
    				<h3 id="order_review_heading"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h3>
    				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
    				<div id="order_review" class="woocommerce-checkout-review-order">
    					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
    				</div>
    				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
    			</div>
                <?php
                 $cart_total = WC()->cart->get_subtotal() - WC()->cart->get_discount_total();

    $max_delivery_days = 0;
    foreach ( WC()->cart->get_cart() as $ls_ci_key => $ls_ci_item ) {
        $ls_ci_days = ls_get_cart_item_delivery_days( $ls_ci_item );
        if ( $ls_ci_days > $max_delivery_days ) {
            $max_delivery_days = $ls_ci_days;
        }
    }
                ?>
                <div>
                       <?php if ( $max_delivery_days > 0  &&  WEBSITE_TYPE == 'RETAIL' ) : ?>
                            <div class="font_size_12 fw-500 text-left ls_est_delivery_msg mt-1">
                                Insured shipping by <?php echo esc_html( ls_est_delivery_date( $max_delivery_days ) ); ?> with FedEx overnight delivery. <a href="javascript:void(0)" onclick="openZohoChat('Hi, I need this sooner than the estimated delivery date. Product page: ' + window.location.href)">Need it sooner?</a>
                            </div>
                        <?php endif; ?>
                </div>
                <div class="faq-checkout-order">
                  <h4>FAQ</h4>
                    <?php
                    $faqs = [
                                [
                                    'id'       => 'faq1',
                                    'question' => 'Can I return my order?',
                                    'answer'   => 'Absolutely. You have 30 days from the shipping date to return or exchange your order, with free, fully insured shipping both ways for customers in the USA. We’ll provide a prepaid return label and promptly process your return.',
                                ],
                                [
                                    'id'       => 'faq2',
                                    'question' => 'Will the packaging keep the surprise safe?',
                                    'answer'   => 'Yes! Our outer packaging is discreet and unbranded, so the surprise stays safe. Inside, your jewelry arrives in a beautiful box, ready for the big moment.',
                                ],
                                [
                                    'id'       => 'faq3',
                                    'question' => 'What if the ring doesn’t fit?',
                                    'answer'   => 'Every order is covered by our free ring resizing policy. If you need a resize, simply email us your order number and new ring size, and our team will provide the instructions, including insured shipping both ways. Eternity rings cannot be resized, but we’ll help you exchange them for the correct size within the return period.',
                                ],
                            ];
                    ?>
                    <div class="accordion" id="faqAccordion">

                        <?php foreach ($faqs as $index => $faq) : ?>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#<?php echo esc_attr($faq['id']); ?>"
                                    >
                                        <?php echo esc_html($faq['question']); ?>
                                    </button>
                                </h2>

                                <div
                                    id="<?php echo esc_attr($faq['id']); ?>"
                                    class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
                                    data-bs-parent="#faqAccordion"
                                >
                                    <div class="accordion-body">
                                        <?php echo esc_html($faq['answer']); ?>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgFgVfpZ_oRfQAc6mXWNFbB1dPUPeqTmI&libraries=places"></script>
<script type="text/javascript">
function initAutocomplete() {
    const billingInput  = document.getElementById("billing_address_1");
    const shippingInput = document.getElementById("shipping_address_1");

    // Helper: forcibly hide the Google dropdown
    function dismissDropdown(input) {
        input.blur();

        // Hide the pac-container immediately
        const pac = document.querySelector('.pac-container');
        if (pac) pac.style.display = 'none';

        // Re-show it only when user focuses the field again
        input.addEventListener('focus', function () {
            if (pac) pac.style.display = '';
        }, { once: true });
    }

    if (billingInput) {
        // Prevent browser's own autocomplete from fighting Google's
        billingInput.setAttribute('autocomplete', 'new-password');

        const billingAutocomplete = new google.maps.places.Autocomplete(billingInput);
        billingAutocomplete.addListener("place_changed", function () {
            fillAddress(billingAutocomplete, 'billing');
            dismissDropdown(billingInput);
        });
    }

    if (shippingInput) {
        shippingInput.setAttribute('autocomplete', 'new-password');

        const shippingAutocomplete = new google.maps.places.Autocomplete(shippingInput);
        shippingAutocomplete.addListener("place_changed", function () {
            fillAddress(shippingAutocomplete, 'shipping');
            dismissDropdown(shippingInput);
        });
    }
}
function fillAddress(autocomplete, type) {
    const place = autocomplete.getPlace();
    if (!place || !place.address_components) return;

    let streetNumber = '', route = '', city = '', state = '', postcode = '', country = '';

    place.address_components.forEach(function (component) {
        const types = component.types;
        if (types.includes('street_number'))             streetNumber = component.long_name;
        if (types.includes('route'))                     route        = component.long_name;
        if (types.includes('locality'))                  city         = component.long_name;
        if (types.includes('administrative_area_level_1')) state      = component.short_name;
        if (types.includes('postal_code'))               postcode     = component.long_name;
        if (types.includes('country'))                   country      = component.short_name;
    });

    // Helper: set a plain text/email/postcode input and fire events
    function setInput(id, value) {
        console.log("id",id);
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value;
        if(id=='shipping_address_1' || id=='billing_address_1'){
            return ;
        }
        el.dispatchEvent(new Event('input',  { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Helper: set a <select> (plain or Select2) and fire WooCommerce events
    function setSelect(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = value;
        // Vanilla change (non-Select2 selects)
        el.dispatchEvent(new Event('change', { bubbles: true }));
        // Select2 / WooCommerce change
        if (typeof jQuery !== 'undefined') {
            jQuery(el).val(value).trigger('change');
        }
    }

    // 1. Fill plain fields immediately
    setInput(type + '_address_1', (streetNumber + ' ' + route).trim());
    setInput(type + '_city',      city);
    setInput(type + '_postcode',  postcode);

    // 2. Set country first — WooCommerce will AJAX-load the matching state options
    setSelect(type + '_country', country);

    // 3. Set state AFTER WooCommerce has finished updating the state dropdown
    //    'country_to_state_changed' fires when the state <select> is rebuilt
    jQuery(document.body).one('country_to_state_changed', function () {
        setSelect(type + '_state', state);

    });

    // Fallback: if the event never fires (state is a free-text input, or no AJAX needed)
    setTimeout(function () {
        setSelect(type + '_state', state);
    }, 600);
}

// Run when page is ready
document.addEventListener("DOMContentLoaded", function () {
    if (typeof google !== "undefined") {
        initAutocomplete();
    }
});

// WooCommerce AJAX refresh fix
jQuery(document.body).on('updated_checkout', function () {
    if (typeof google !== "undefined") {
        initAutocomplete();
    }
});
</script>
<style>
    #contact_details_heading_field .woocommerce-input-wrapper, #contact_details_heading_field .optional {
        display: none !important;
    }
    #contact_details_heading_field {
    /* display: block; */
    width: 100%;
}
.same-as-shipping li[data-id="billing_email"],
.same-as-shipping li[data-id="billing_first_name"],
.same-as-shipping li[data-id="billing_last_name"],
.same-as-shipping li[data-id="billing_address_1"],
.same-as-shipping li[data-id="billing_state"],
.same-as-shipping li[data-id="billing_city"],
.same-as-shipping li[data-id="billing_postcode"] {
    display: none;
}
</style>
<script type="text/javascript">
function fixShippingErrors() {
    console.log("Running fixShippingErrors");

    document.querySelectorAll('.checkout-inline-error-message').forEach(function (el) {
        // Remove repeated "Shipping"
        el.textContent = el.textContent.replace(/^(Shipping\s+)+/i, '');
    });
}

document.addEventListener('DOMContentLoaded', function () {

    // Run on initial load
    fixShippingErrors();

    // Run after WooCommerce updates checkout.
    // WooCommerce fires these via jQuery, so native listeners can't catch them.
    if (window.jQuery) {
        window.jQuery(document.body).on('updated_checkout checkout_error', function () {
            setTimeout(fixShippingErrors, 300);
        });
    }

    // Clicking anywhere on a payment method row selects its radio
    document.addEventListener('click', function (e) {
        var li = e.target.closest('.wc_payment_methods li');
        if (!li) return;

        // Avoid conflict if clicking directly on input
        if (e.target.tagName !== 'INPUT') {
            var radio = li.querySelector('input[type="radio"]');
            if (radio) radio.click();
        }
    });

    // Insert custom div before shipping_email field (kept disabled, as before)
    /*
    var emailField = document.getElementById('shipping_email_field');
    if (emailField) {
        emailField.insertAdjacentHTML('beforebegin',
            '<div class="custom-shipping-label-contact-details">' +
                '<label>CONTACT DETAILS</label>' +
            '</div>'
        );
    }
    */
});

(function () {

  const FIELD_MAP = [
    ["#shipping_first_name", "#billing_first_name"],
    ["#shipping_last_name",  "#billing_last_name"],
    ["#shipping_company",    "#billing_company"],
    ["#shipping_country",    "#billing_country"],
    ["#shipping_address_1",  "#billing_address_1"],
    ["#shipping_address_2",  "#billing_address_2"],
    ["#shipping_city",       "#billing_city"],
    ["#shipping_state",      "#billing_state"],
    ["#shipping_postcode",   "#billing_postcode"],
    /*["#shipping_phone",      "#billing_phone"],*/
    ["#shipping_email",      "#billing_email"],
  ];

  const CHECKBOX_SEL     = "#same_as_shipping_address";
  const BILLING_WRAP_SEL = ".woocommerce-billing-fields";

  function copyField(srcSel, dstSel) {
    const src = document.querySelector(srcSel);
    const dst = document.querySelector(dstSel);
    if (!src || !dst) return;
    if (dst.value === src.value) return;
    dst.value = src.value;
    ["input", "change"].forEach((t) =>
      dst.dispatchEvent(new Event(t, { bubbles: true }))
    );
  }

  function syncAllFields() {
    FIELD_MAP.forEach(([src, dst]) => copyField(src, dst));
  }

  function applyVisibility(checkbox) {
    const wrap = document.querySelector(BILLING_WRAP_SEL);
    if (!wrap) return;
    if (checkbox.checked) {
      wrap.style.display = "none";
      document.body.classList.add('same-as-shipping');
      syncAllFields();
    } else {
      wrap.style.display = "";
      document.body.classList.remove('same-as-shipping');
    }
  }

  function attachShippingListeners(checkbox) {
    FIELD_MAP.forEach(([srcSel, dstSel]) => {
      const src = document.querySelector(srcSel);
      if (!src) return;
      ["input", "change"].forEach((type) => {
        src.addEventListener(type, () => {
          if (!checkbox.checked) return;
          copyField(srcSel, dstSel);
        });
      });
    });
  }

  // Always copy shipping email to billing email, regardless of checkbox state
  function alwaysCopyShippingEmail() {
    const src = document.querySelector('#shipping_email');
    if (!src) return;
    const copy = () => copyField('#shipping_email', '#billing_email');
    src.addEventListener('input', copy);
    src.addEventListener('change', copy);
    copy(); // initial copy on load
  }

  function init() {
    const checkbox = document.querySelector(CHECKBOX_SEL);
    alwaysCopyShippingEmail();

    if (!checkbox) {
      console.error("[billing-sync] Checkbox #same_as_shipping_address not found.");
      return;
    }

    checkbox.checked = true;
    applyVisibility(checkbox);

    checkbox.addEventListener("change", () => applyVisibility(checkbox));
    attachShippingListeners(checkbox);

    console.log("[billing-sync] ✅ Ready");
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var ajaxUrl     = '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>';
  var removeNonce = '<?php echo esc_js( wp_create_nonce( "remove_coupon" ) ); ?>';
  var applyNonce  = '<?php echo esc_js( wp_create_nonce( "custom_coupon_nonce" ) ); ?>';

  function post(data) {
    return fetch(ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams(data).toString()
    }).then(function (res) {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    });
  }

  // WooCommerce listens for this through jQuery
  function updateCheckout() {
    if (window.jQuery) jQuery(document.body).trigger('update_checkout');
  }

  document.body.addEventListener('click', function (e) {

    // Remove coupon
    var removeBtn = e.target.closest('.remove_coupon');
    if (removeBtn) {
      e.preventDefault();
      var coupon = removeBtn.dataset.coupon;
      if (!coupon) return;

      if (typeof lsShowLoader === 'function') lsShowLoader();

      post({ action: 'oc_remove_coupon', coupon: coupon, security: removeNonce })
        .then(updateCheckout)
        .catch(function () { console.error('Failed to remove coupon: ' + coupon); })
        .finally(function () { if (typeof lsHideLoader === 'function') lsHideLoader(); });
      return;
    }

    // Show / hide coupon form
    var couponForm = e.target.closest('.coupon_form');
    if (couponForm) {
      e.preventDefault();
      couponForm.classList.toggle('d-none');
      document.querySelectorAll('.promo_code').forEach(function (el) {
        el.classList.toggle('d-none');
      });
      return;
    }

    // Apply coupon
        // Apply coupon
    var applyBtn = e.target.closest('.apply_btn');
    if (applyBtn) {
      e.preventDefault();

      // Only look inside the promo box whose Apply button was clicked
      var box     = applyBtn.closest('.promo_code');
      var input   = box ? box.querySelector('input') : null;
      var message = box ? box.querySelector('.cart-coupon-message') : null;
      var code    = input ? input.value.trim() : '';

      if (!code) {
        if (message) message.innerHTML = '<span style="color:red;">Please enter a coupon code.</span>';
        return;
      }

      if (applyBtn.disabled) return;   // ignore double clicks
      applyBtn.disabled = true;
      if (typeof lsShowLoader === 'function') lsShowLoader();

      post({ action: 'custom_apply_coupon', security: applyNonce, coupon_code: code })
        .then(function (response) {
          var color = response.success ? 'green' : 'red';
          var text  = (response.data && response.data.message) || (response.success ? 'Coupon applied.' : 'Could not apply coupon.');
          if (message) message.innerHTML = '<span style="color:' + color + ';">' + text + '</span>';
          if (response.success) {
            input.value = '';
            updateCheckout();
          }
        })
        .catch(function () {
          if (message) message.innerHTML = '<span style="color:red;">Could not apply coupon. Please try again.</span>';
        })
        .finally(function () {
          applyBtn.disabled = false;
          if (typeof lsHideLoader === 'function') lsHideLoader();
        });
    }
  });
});
</script>