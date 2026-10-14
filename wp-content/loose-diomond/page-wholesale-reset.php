<?php
/**
 * Template Name: Wholesale Reset
 */

get_header('wholesale');
?>

    <div class="login-back">
      <div class="bg_soft">
        <div class="wholesale_page">
          <div class="container-fluid">
            <form id="ld-reset-do-form" class="form_box mx-auto">
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                alt="loosegrowndiamond_logo"
                class="d-block mx-auto mb_30"
              />
              <div id="ld-reset-do-messages" role="alert" aria-live="polite"></div>
              <div class="row gap-3 gap-12">
                <div class="col-12">
                  <label for="pass1" class="form-label">New Password<span class="text-danger">*</span></label>
                  <input type="password" name="pass1" id="pass1" class="form-control" placeholder="••••••••" />
                  <div class="ld-field-error text-danger mt-1" id="pass1-error"></div>
                </div>
                <div class="col-12">
                  <label for="pass2" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                  <input type="password" name="pass2" id="pass2" class="form-control" placeholder="••••••••" />
                  <div class="ld-field-error text-danger mt-1" id="pass2-error"></div>
                </div>

                <?php
                  $key = isset($_GET['key']) ? wp_unslash($_GET['key']) : '';
                  $login = isset($_GET['login']) ? wp_unslash($_GET['login']) : '';

                  $show_form = false;
                  $error_message = '';
                  if ($key && $login) {
                      $check = check_password_reset_key($key, $login);
                      if (is_wp_error($check)) {
                          $error_message = $check->get_error_message();
                      } else {
                          $show_form = true;
                      }
                  } else {
                      $error_message = 'Invalid reset link.';
                  }
                ?>

                <?php if ($show_form): ?>
                <input type="hidden" id="ld-reset-key" value="<?php echo esc_attr($key); ?>" />
                <input type="hidden" id="ld-reset-login" value="<?php echo esc_attr($login); ?>" />
                <input type="hidden" id="ld-reset-do-nonce" value="<?php echo wp_create_nonce('ajax-wholesale-do-reset-nonce'); ?>" />

                <div class="col-12">
                  <button type="submit" class="common_btn w-100  oc-dark">Reset Password</button>
                </div>
                <?php else: ?>
                <div class="col-12">
                  <div class="alert alert-danger" role="alert"><?php echo esc_html($error_message); ?></div>
                  <p class="text-center"><a href="<?php echo esc_url(home_url('/forget-password/')); ?>">Request a new reset link</a></p>
                </div>
                <?php endif; ?>

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
var ld_wholesale_do_reset = {
  ajax_url: '<?php echo admin_url("admin-ajax.php"); ?>',
  nonce: '<?php echo wp_create_nonce("ajax-wholesale-do-reset-nonce"); ?>'
};

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('ld-reset-do-form');
  const msg = document.getElementById('ld-reset-do-messages');
  const pass1 = document.getElementById('pass1');
  const pass2 = document.getElementById('pass2');
  const keyEl = document.getElementById('ld-reset-key');
  const loginEl = document.getElementById('ld-reset-login');
  const nonceEl = document.getElementById('ld-reset-do-nonce');

  [pass1, pass2].forEach(el => {
    if (!el) return;
    el.addEventListener('input', () => {
      document.getElementById(el.id + '-error').innerHTML = '';
      el.classList.remove('is-invalid');
      msg.innerHTML = ''; msg.className = '';
    });
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    msg.innerHTML = ''; msg.className = '';
    document.getElementById('pass1-error').innerHTML = '';
    document.getElementById('pass2-error').innerHTML = '';
    pass1.classList.remove('is-invalid'); pass2.classList.remove('is-invalid');

    const v1 = pass1 ? pass1.value : '';
    const v2 = pass2 ? pass2.value : '';
    if (!v1) { document.getElementById('pass1-error').textContent = 'Please enter a new password'; pass1.classList.add('is-invalid'); return; }
    if (!v2) { document.getElementById('pass2-error').textContent = 'Please confirm your password'; pass2.classList.add('is-invalid'); return; }
    if (v1 !== v2) { document.getElementById('pass2-error').textContent = 'Passwords do not match'; pass2.classList.add('is-invalid'); return; }
    if (v1.length < 6) { document.getElementById('pass1-error').textContent = 'Password must be at least 6 characters'; pass1.classList.add('is-invalid'); return; }

    const payload = new URLSearchParams({
      action: 'ld_wholesale_do_reset',
      key: keyEl ? keyEl.value : '',
      login: loginEl ? loginEl.value : '',
      pass1: v1,
      pass2: v2,
      nonce: nonceEl ? nonceEl.value : ld_wholesale_do_reset.nonce
    });

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch(ld_wholesale_do_reset.ajax_url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload })
    .then(r => r.json())
    .then(res => {
      if (res && res.success) {
        msg.classList.add('alert', 'alert-success');
        msg.innerHTML = res.data?.message || 'Password reset successful.';
        if (res.data?.redirect) { setTimeout(() => window.location.href = res.data.redirect, 800); }
      } else {
        msg.classList.add('alert', 'alert-danger');
        const serverData = res?.data?.messages;
        msg.innerHTML = serverData ? (Array.isArray(serverData) ? serverData.join('<br>') : serverData) : 'Request failed.';
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
  
</style>
<?php get_footer('wholesale'); ?>