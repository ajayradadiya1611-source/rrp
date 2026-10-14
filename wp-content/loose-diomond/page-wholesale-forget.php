<?php
/**
 * Template Name: Wholesale Forget
 */

get_header('wholesale');
?>

    <div class="login-back">
      <div class="bg_soft">
        <div class="wholesale_page">
          <div class="container-fluid">
            <form id="ld-reset-form" class="form_box mx-auto" >
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                alt="loosegrowndiamond_logo"
                class="d-block mx-auto mb_30"
                
              />
              <div id="ld-reset-messages" role="alert" aria-live="polite"></div>
              <div class="row gap-3 gap-12">
                <div class="col-12">
                  <label for="reset-email" class="form-label">Email address<span class="text-danger">*</span></label>
                  <input type="email" name="email" id="reset-email" class="form-control" placeholder="Enter your email" />
                  <div class="ld-field-error text-danger mt-1" id="reset-email-error"></div>
                </div>
                <input type="hidden" id="ld-reset-nonce" value="<?php echo wp_create_nonce('ajax-wholesale-reset-nonce'); ?>" />
                <div class="col-12">
                  <button type="submit" class="common_btn w-100 oc-dark" >Send Reset Link</button>
                </div>

                <p class="text-center mb-0 mt-4">
                  Not a member? <a href="<?php echo get_site_url().'/register/'; ?>"> Join Now</a>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- start footer section -->
      <div class="footer">
        <p class="text-center mb-0 fs-12">
          © <?php echo SITE_OC_NAME;?> <?php echo date('Y'); ?>.
        </p>
      </div>
    </div>

<script>
var ld_wholesale_reset = {
  ajax_url: '<?php echo admin_url("admin-ajax.php"); ?>',
  nonce: '<?php echo wp_create_nonce("ajax-wholesale-reset-nonce"); ?>'
};

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('ld-reset-form');
  const msg = document.getElementById('ld-reset-messages');
  const emailEl = document.getElementById('reset-email');
  const emailErr = document.getElementById('reset-email-error');

  emailEl && emailEl.addEventListener('input', function () {
    emailErr.innerHTML = '';
    emailEl.classList.remove('is-invalid');
    msg.innerHTML = ''; msg.className = '';
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    msg.innerHTML = ''; msg.className = '';
    emailErr.innerHTML = '';
    emailEl.classList.remove('is-invalid');

    const email = (emailEl ? emailEl.value.trim() : '');
    if (!email) {
      emailErr.textContent = 'Please enter your email';
      emailEl.classList.add('is-invalid');
      return;
    }

    const nonceInput = document.getElementById('ld-reset-nonce');
    const nonce = nonceInput ? nonceInput.value : ld_wholesale_reset.nonce;

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch(ld_wholesale_reset.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'ld_wholesale_reset', email: email, nonce: nonce })
    })
    .then(r => r.json())
    .then(res => {
      if (res && res.success) {
        msg.classList.add('alert', 'alert-success');
      //  msg.innerHTML = res.data?.message || 'If an account exists, a reset link has been sent.';
        msg.innerHTML ='Password reset email has been sent.'
        emailEl.value = '';
      } else {
        const serverData = res?.data?.messages;
        msg.classList.add('alert', 'alert-danger');
        if (serverData) {
          msg.innerHTML = Array.isArray(serverData) ? serverData.join('<br>') : serverData;
        } else {
          msg.innerHTML = 'Request failed.';
        }
      }
      btn.disabled = false;
    })
    .catch(() => {
      msg.classList.add('alert', 'alert-danger');
      msg.innerHTML = 'Request failed.';
      btn.disabled = false;
    });
  });
});
</script>

<style type="text/css">
  .login-back{ }
</style>
<?php get_footer('wholesale'); ?>