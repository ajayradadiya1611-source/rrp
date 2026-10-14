<?php
/**
 * Template Name: Wholesale Login
 */

get_header('wholesale');
?>

    <div class="login-back">
      <div class="bg_soft">
        <div class="wholesale_page">
          <div class="container-fluid">
            <form id="ld-login-form" class="form_box mx-auto">
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                alt="loosegrowndiamond_logo"
                class="d-block mx-auto mb_30"
              />
              <div id="ld-login-messages" role="alert" aria-live="polite"></div>
              <div class="row gap-3 gap-12">
                <div class="col-12">
                  <label for="email" class="form-label"
                    >Email address<span class="text-danger">*</span></label
                  >
                  <input type="text" name="user" id="email" class="form-control" placeholder="Enter your email" />
                  <div class="ld-field-error text-danger mt-1" id="email-error"></div>
                </div>
                <div class="col-12">
                  <label for="password" class="form-label"
                    >Password<span class="text-danger">*</span></label
                  >
                  <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" />
                  <div class="ld-field-error text-danger mt-1" id="password-error"></div>
                </div>
                <input type="hidden" id="ld-login-nonce" value="<?php echo wp_create_nonce('ajax-login-nonce'); ?>" />
                <div class="col-12">
                  <button type="submit" class="common_btn w-100">Get Access Now</button>
                </div>

                <a
                  class="text-decoration-none d-flex justify-content-end mt-2 fs-14"
                  style="color: #000"
                  href="/forget-password/"
                  >Forgot Password?</a
                >
                <p class="text-center mb-0">
                  Not a member? <a href="<?php echo get_site_url().'/register/'; ?>"> Join Now</a>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- start footer section -->
      <div class="footer">
        <p class="text-center mb-0 fs-12 text-black">
          © <?php echo SITE_OC_NAME;?> 2026. 
        </p>
      </div>
    </div>
<script>
var ld_ajax_login = {
  ajax_url: '<?php echo admin_url("admin-ajax.php"); ?>',
  nonce: '<?php echo wp_create_nonce("ajax-login-nonce"); ?>'
};

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('ld-login-form');
  const msg = document.getElementById('ld-login-messages');

  const fieldEls = {
    user: document.getElementById('email'),
    password: document.getElementById('password')
  };

  const errorEls = {
    user: document.getElementById('email-error'),
    password: document.getElementById('password-error')
  };

  function clearFieldErrors() {
    Object.values(errorEls).forEach(el => { if (el) el.innerHTML = ''; });
    Object.values(fieldEls).forEach(el => { if (el) el.classList.remove('is-invalid'); });
  }

  // clear per-field error when user types
  Object.keys(fieldEls).forEach(key => {
    const fld = fieldEls[key];
    const err = errorEls[key];
    if (!fld) return;
    fld.addEventListener('input', () => {
      if (err) err.innerHTML = '';
      fld.classList.remove('is-invalid');
      if (msg) { msg.innerHTML = ''; msg.className = ''; }
    });
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    msg.innerHTML = '';
    msg.className = '';
    clearFieldErrors();

    const user = (fieldEls.user ? fieldEls.user.value.trim() : '');
    const pass = (fieldEls.password ? fieldEls.password.value : '');
    const nonceInput = document.getElementById('ld-login-nonce');
    const nonce = nonceInput ? nonceInput.value : ld_ajax_login.nonce;

    let hasErrors = false;
    if (!user) {
      if (errorEls.user) errorEls.user.textContent = 'Please enter your email';
      if (fieldEls.user) fieldEls.user.classList.add('is-invalid');
      hasErrors = true;
    }
    if (!pass) {
      if (errorEls.password) errorEls.password.textContent = 'Please enter your password.';
      if (fieldEls.password) fieldEls.password.classList.add('is-invalid');
      hasErrors = true;
    }

    if (hasErrors) return;

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch(ld_ajax_login.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        action: 'ld_ajax_login',
        user: user,
        password: pass,
        nonce: nonce
      })
    })
    .then(response => response.json())
    .then(res => {
      clearFieldErrors();
      if (res && res.success) {
        msg.classList.remove('alert-danger');
        msg.classList.add('alert', 'alert-success');
        msg.innerHTML = res.data?.message || 'Login successful.';
        if (res.data?.redirect) {
          window.location.href = res.data.redirect;
        } else {
          setTimeout(() => location.reload(), 800);
        }
      } else {
        const serverData = res?.data?.messages;
        if (serverData && typeof serverData === 'object' && !Array.isArray(serverData)) {
          Object.keys(serverData).forEach(key => {
            const el = errorEls[key];
            if (el) {
              el.innerHTML = Array.isArray(serverData[key]) ? serverData[key].join('<br>') : serverData[key];
              if (fieldEls[key]) fieldEls[key].classList.add('is-invalid');
            } else {
              msg.classList.add('alert', 'alert-danger');
              msg.innerHTML = (msg.innerHTML ? msg.innerHTML + '<br>' : '') + (Array.isArray(serverData[key]) ? serverData[key].join('<br>') : serverData[key]);
            }
          });
        } else {
          const msgs = Array.isArray(serverData) ? serverData : (serverData ? [serverData] : ['Login failed.']);
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
      msg.innerHTML = 'Request failed.';
      btn.disabled = false;
    });
  });
});
</script>
<style type="text/css">
  .login-back{
    /*height: 100vh;*/
  }
  /*.footer{
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
  }*/
</style>
<?php get_footer('wholesale'); ?>