<?php
/**
 * Template Name: Wholesale Register
 */

get_header('wholesale');
?>

    <div class="Auth-back">
      <div class="wholesale_page">
        <div class="container-fluid">
          <div class="row main_row">
            <div class="col-lg-6">
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                alt="loosegrowndiamond_logo"
                class="losse-ground-logo"
              />
              <div class="main_box">
                <div class="sub_title">💎 Over 1.5M+ Certified Diamonds</div>
                <h1>Explore our vast collection of lab-grown diamonds</h1>
                <p class="text_p">
                  Get the best deals on certified lab-grown diamonds, fancy
                  shapes melee diamonds and fine jewelry. Customize selected
                  diamonds and jewelry to fit your unique needs.
                </p>
                <ul class="list-unstyled">
                  <li>
                    <img
                      src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/check_green.svg"
                      alt="check_green"
                    />
                    <span><?php echo RETURN_DAYS;?> Days Return Policy</span>
                  </li>
                  <li>
                    <img
                      src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/check_green.svg"
                      alt="check_green"
                    />
                    <span>Custom Cut Diamonds</span>
                  </li>
                  <li>
                    <img
                      src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/check_green.svg"
                      alt="check_green"
                    />
                    <span>Insured Shipping</span>
                  </li>
                  <li>
                    <img
                      src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/check_green.svg"
                      alt="check_green"
                    />
                    <span>Wholesale Prices</span>
                  </li>
                </ul>
              </div>
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/wholesale_bg.svg"
                alt="loosegrowndiamond"
                class="main_img"
              />
            </div>
            <div class="col-lg-6">
              <form id="ld-register-form" class="form_box mx-auto ms-lg-auto">
                <h3 class="text-center">Get access to over 1.5M+ Certified Diamonds</h3>
                <p class="text-center">Maximize profitability for your business with wholesale prices and custom-cut fancy diamonds.</p>

                <div id="ld-register-messages" style="margin-bottom:12px;"></div>

                <div class="row g-3">
                  <div class="col-12">
                    <label for="ld-fullname" class="form-label">Your name<span class="text-danger">*</span></label>
                    <input type="text" name="fullname" id="ld-fullname" class="form-control" placeholder="Enter your name" />
                    <div class="ld-field-error text-danger mt-1" id="ld-fullname-error"></div>
                  </div>
                  <div class="col-12">
                    <label for="ld-email" class="form-label">Your email<span class="text-danger">*</span></label>
                    <input type="email" name="email" id="ld-email" class="form-control" placeholder="Enter your email" />
                    <div class="ld-field-error text-danger mt-1" id="ld-email-error"></div>
                  </div>
                  <div class="col-12">
                    <label for="ld-phone" class="form-label">Your phone<span class="text-danger">*</span></label>
                    <input type="tel" name="phone" id="ld-phone" class="form-control" placeholder="Phone number"  />
                    <div class="ld-field-error text-danger mt-1" id="ld-phone-error"></div>
                  </div>
                  <div class="col-12">
                    <label for="ld-website" class="form-label">Your website / Social Media</label>
                    <input type="text" name="website" id="ld-website" class="form-control" placeholder="Website or social link (optional)" />
                    <div class="ld-field-error text-danger mt-1" id="ld-website-error"></div>
                  </div>
                  
                  <div class="col-12">
                    <label for="ld-password" class="form-label">Password<span class="text-danger">*</span></label>
                    <input type="password" name="password" id="ld-password" class="form-control" placeholder="••••••••" />
                    <div class="ld-field-error text-danger mt-1" id="ld-password-error"></div>
                  </div>
                  <input type="hidden" name="nonce" id="ld-register-nonce" value="<?php echo wp_create_nonce('ajax-register-nonce'); ?>" />
                  <input type="hidden" name="country_code" id="ld-country-code" value="" />
                  <input type="hidden" name="source" id="ld-source" value="" />
                  <input type="hidden" name="recaptcha_token" id="ld-recaptcha-token" value="" />

                  <div class="col-12">
                    <button type="submit" class="common_btn w-100 mb-4">Join Now</button>
                  </div>
                </div> 

                <p class="text-center mb-0">Already have access? <a href="<?php echo get_site_url().'/login/'; ?>"> Sign In</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="footer">
        <p class="text-center mb-0 text-black">
          © <?php echo SITE_OC_NAME;?> 2026. 
        </p>
        <p class="text-center mb-0">
          <a href="/privacy-policy/">Privacy & Cookie Policy</a>
        </p>
      </div>
    </div>
<?php get_footer('wholesale'); ?>
<?php if ( defined('LD_RECAPTCHA_SITE_KEY') && LD_RECAPTCHA_SITE_KEY ) : ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr(LD_RECAPTCHA_SITE_KEY); ?>"></script>
<?php endif; ?>
<script>
var ld_ajax = {
  ajax_url: '<?php echo admin_url("admin-ajax.php"); ?>',
  nonce: '<?php echo wp_create_nonce("ajax-register-nonce"); ?>',
  recaptcha_site_key: '<?php echo defined("LD_RECAPTCHA_SITE_KEY") ? esc_js(LD_RECAPTCHA_SITE_KEY) : ""; ?>'
};

// Read the first-touch source captured sitewide in the `ld_source` cookie.
// Falls back to a fresh on-page detection only if the cookie isn't present
// (e.g. cookies blocked), so the field is never left empty.
function ld_get_source_cookie() {
  const match = document.cookie.split('; ').find(row => row.indexOf('ld_source=') === 0);
  return match ? decodeURIComponent(match.split('=')[1]) : '';
}

function ld_detect_source() {
  try {
    const params = new URLSearchParams(window.location.search);
    // Explicit utm_source (or a plain ?source=) always wins, e.g. utm_source=test&utm_medium=chatgpt -> "test / chatgpt"
    const utmSource = params.get('utm_source') || params.get('source');
    const utmMedium = params.get('utm_medium');
    if (utmSource) {
      return utmSource.trim() + (utmMedium ? ' / ' + utmMedium.trim() : '');
    }

    // Fall back to the referring domain — store it exactly as-is, no name mapping
    if (document.referrer) {
      try {
        const refHost = new URL(document.referrer).hostname.replace(/^www\./, '');
        // Same-site referral (internal navigation) doesn't count as an external source
        if (refHost === window.location.hostname.replace(/^www\./, '')) {
          return 'Direct';
        }
        return refHost; // e.g. instagram.com, l.facebook.com, google.com — exactly what came in the referrer
      } catch (e) {
        return 'Direct';
      }
    }

    // No query param, no referrer: typed URL, bookmark, or app-embedded browser
    return 'Direct';
  } catch (e) {
    return 'Direct';
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('ld-register-form');
  const msg = document.getElementById('ld-register-messages');

  const sourceInput = document.getElementById('ld-source');
  if (sourceInput) {
    sourceInput.value = ld_get_source_cookie() || ld_detect_source();
  }

  const fieldEls = {
    fullname: document.getElementById('ld-fullname'),
    email: document.getElementById('ld-email'),
    phone: document.getElementById('ld-phone'),
    website: document.getElementById('ld-website'),
    password: document.getElementById('ld-password')
  };

  const errorEls = {
    fullname: document.getElementById('ld-fullname-error'),
    email: document.getElementById('ld-email-error'),
    phone: document.getElementById('ld-phone-error'),
    website: document.getElementById('ld-website-error'),
    password: document.getElementById('ld-password-error')
  };

  function clearFieldErrors() {
    Object.values(errorEls).forEach(el => { if (el) el.innerHTML = ''; });
    Object.values(fieldEls).forEach(el => { if (el) el.classList.remove('is-invalid'); });
  }

  // Clear individual field error when user edits that field (keep global/server messages)
  const requiredFields = ['fullname','email','phone','password'];
  Object.keys(fieldEls).forEach(key => {
    const fld = fieldEls[key];
    const err = errorEls[key];
    if (!fld) return;
    fld.addEventListener('input', () => {
      if (err) err.innerHTML = '';
      fld.classList.remove('is-invalid');
      // intentionally do NOT clear `msg` here so server/global messages remain until explicitly replaced
    });

    // on blur: only check required (empty) state first; do not perform other validations here
    if (requiredFields.indexOf(key) !== -1) {
      fld.addEventListener('blur', () => {
        if (fld.value.trim() === '') {
          if (err) err.textContent = 'This field is required.';
          fld.classList.add('is-invalid');
        }
      });
    }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    msg.innerHTML = '';
    msg.className = '';
    clearFieldErrors();

    const fullname = (fieldEls.fullname ? fieldEls.fullname.value.trim() : '');
    const email = (fieldEls.email ? fieldEls.email.value.trim() : '');
    const website = (fieldEls.website ? fieldEls.website.value.trim() : '');
    const phone = (fieldEls.phone ? fieldEls.phone.value.trim() : '');
    const password = (fieldEls.password ? fieldEls.password.value : '');
    const nonceInput = document.getElementById('ld-register-nonce');
    const nonce = nonceInput ? nonceInput.value : ld_ajax.nonce;
    const countryCode = (window.ld_iti && typeof window.ld_iti.getSelectedCountryData === 'function') ? ((window.ld_iti.getSelectedCountryData().iso2 || '').toUpperCase()) : '';
    const source = (sourceInput && sourceInput.value) ? sourceInput.value : (ld_get_source_cookie() || ld_detect_source());

    // First: required (empty) validation for required fields
    let hasErrors = false;
    const requiredChecks = {
      fullname: fullname,
      email: email,
      phone: phone,
      password: password
    };

    Object.keys(requiredChecks).forEach(key => {
      if (!requiredChecks[key] || requiredChecks[key].toString().trim() === '') {
        if (errorEls[key]) errorEls[key].textContent = 'This field is required';
        if (fieldEls[key]) fieldEls[key].classList.add('is-invalid');
        hasErrors = true;
      }
    });

    if (hasErrors) {
      return; // stop here; user must fill required fields first
    }

    // Remaining validations (only run if required fields are present)
    if (fullname.length < 4) {
      if (errorEls.fullname) errorEls.fullname.textContent = 'Full name must be at least 4 characters.';
      if (fieldEls.fullname) fieldEls.fullname.classList.add('is-invalid');
      hasErrors = true;
    }

    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
      if (errorEls.email) errorEls.email.textContent = 'Please enter a valid email address.';
      if (fieldEls.email) fieldEls.email.classList.add('is-invalid');
      hasErrors = true;
    }

    if (!phone || phone.replace(/\D/g, '').length < 6) {
      if (errorEls.phone) errorEls.phone.textContent = 'Please enter a valid phone number.';
      if (fieldEls.phone) fieldEls.phone.classList.add('is-invalid');
      hasErrors = true;
    }

    if (password.length < 6) {
      if (errorEls.password) errorEls.password.textContent = 'Password must be at least 6 characters.';
      if (fieldEls.password) fieldEls.password.classList.add('is-invalid');
      hasErrors = true;
    }

    if (hasErrors) {
      return;
    }

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;

    // Get a fresh reCAPTCHA v3 token (one per submit attempt) before hitting the server.
    function ld_get_recaptcha_token() {
      return new Promise(function (resolve) {
        if (!ld_ajax.recaptcha_site_key || typeof grecaptcha === 'undefined') {
          resolve(''); // reCAPTCHA not configured/loaded; server will handle accordingly
          return;
        }
        grecaptcha.ready(function () {
          grecaptcha.execute(ld_ajax.recaptcha_site_key, { action: 'wholesale_register' })
            .then(function (token) { resolve(token || ''); })
            .catch(function () { resolve(''); });
        });
      });
    }

    ld_get_recaptcha_token().then(function (recaptchaToken) {
      const tokenInput = document.getElementById('ld-recaptcha-token');
      if (tokenInput) tokenInput.value = recaptchaToken;

      return fetch(ld_ajax.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          action: 'ld_ajax_register',
          fullname: fullname,
          email: email,
          website: website,
          phone: phone,
          country_code: countryCode,
          password: password,
          source: source,
          nonce: nonce,
          recaptcha_token: recaptchaToken,
          recaptcha_action: 'wholesale_register'
        })
      });
    })
    .then(response => response.json())
    .then(res => {
      // Clear any previous server-side field errors
      clearFieldErrors();

      if (res && res.success) {
        msg.classList.remove('alert-danger');
        msg.classList.add('alert', 'alert-success');
        msg.innerHTML = res.data?.message || 'Registration successful.';
        form.reset();
        window.location.href = "/thank-you-register?utm_source=website&utm_medium=registration&utm_campaign=signup";
      } else {
        // Server might return either an array of messages or an object keyed by field
        const serverData = res?.data?.messages;
        if (serverData && typeof serverData === 'object' && !Array.isArray(serverData)) {
          Object.keys(serverData).forEach(key => {
            const el = errorEls[key];
            if (el) {
              el.innerHTML = Array.isArray(serverData[key]) ? serverData[key].join('<br>') : serverData[key];
              if (fieldEls[key]) fieldEls[key].classList.add('is-invalid');
            } else {
              // fallback to global messages
              msg.classList.add('alert', 'alert-danger');
              msg.innerHTML = (msg.innerHTML ? msg.innerHTML + '<br>' : '') + (Array.isArray(serverData[key]) ? serverData[key].join('<br>') : serverData[key]);
            }
          });
        } else {
          const msgs = Array.isArray(serverData) ? serverData : (serverData ? [serverData] : ['An error occurred. Please try again.']);
          msg.classList.remove('alert-success');
          msg.classList.add('alert', 'alert-danger');
          msg.innerHTML = msgs.join('<br>');
        }
      }
      btn.disabled = false;
    })
    .catch(() => {
      msg.classList.remove('alert-success');
      msg.classList.add('alert', 'alert-danger');
      msg.innerHTML = 'Request failed. Please try again.';
      btn.disabled = false;
    });
  });
});
</script>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {

      var input = document.querySelector('#ld-register-form input[name="phone"]');
      if (!input || typeof window.intlTelInput !== 'function') return;

      var errorBox    = document.getElementById('ld-phone-error');
      var countryCode = document.getElementById('ld-country-code');

      var iti = window.intlTelInput(input, {
          hiddenInput: "phone",
          separateDialCode: false,
          initialCountry: "us",
          customPlaceholder: function (selectedCountryPlaceholder) {
              return "e.g. " + selectedCountryPlaceholder;
          },
          geoIpLookup: function (callback) {
              fetch("https://ipinfo.io/json")
                  .then(function (res) { return res.json(); })
                  .then(function (data) { callback(data && data.country ? data.country : ""); })
                  .catch(function () { callback(""); });
          },
          utilsScript: "/wp-content/themes/loose-diomond/assets/js/utils.js"
      });

      // expose instance globally so the submit handler can access it
      window.ld_iti = iti;

      function updateCountryCode() {
          if (!countryCode) return;
          try {
              var data = iti.getSelectedCountryData();
              countryCode.value = (data && data.iso2 ? data.iso2 : '').toUpperCase();
          } catch (e) {}
      }

      // set initial hidden country code
      updateCountryCode();

      // keep country code in sync when the user picks a different flag
      input.addEventListener('countrychange', updateCountryCode);

      // on blur: validate
      input.addEventListener('blur', function () {
          if (errorBox) {
              errorBox.innerHTML = '';
              errorBox.style.display = 'none';
          }
          if (input.value.trim() === '') return;

          if (iti.isValidNumber()) {
              // Full international number (+ country code included)
              var fullNumber = iti.getNumber();
              document.querySelectorAll('#ld-register-form input[name="phone"]').forEach(function (el) {
                  el.value = fullNumber;
              });
              updateCountryCode();
          } else if (errorBox) {
              errorBox.innerHTML = 'Invalid phone number.';
              errorBox.style.display = 'block';
          }
      });
  });
</script>