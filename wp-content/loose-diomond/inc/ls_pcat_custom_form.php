<?php

add_action('wp_footer', 'ls_pcat_pup');
function ls_pcat_pup()
{
     // Prefill data for the "Request this Video" popup when the customer is logged in
    $vr_first_name = '';
    $vr_last_name  = '';
    $vr_email      = '';
    $vr_phone      = '';

    if ( is_user_logged_in() ) {
        $current_user  = wp_get_current_user();
        $vr_first_name = $current_user->first_name ?: $current_user->display_name;
        $vr_last_name  = $current_user->last_name;
        $vr_email      = $current_user->user_email;
        $vr_phone      = get_user_meta( $current_user->ID, 'billing_phone', true );
    }
    //if (!is_page(array('custom-engagement-ring', 'lab-created-diamond-ring', 'inventory', 'lab-created-colored-diamonds'))) {
        ?>
        <div class="modal fade" id="customSettingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="text-center mb-4">
                            <h3 class="fw-600 text-uppercase">Custom Setting Request</h3>
                            <p class="desc" style="font-size:14px;margin-bottom:12px;">
                                Complimentary CAD/3D and consultation • Trusted by 27,000+ couples
                            </p>
                        </div>

                        <form name="pcat_custom_request" class="ls-cntctform" id="pcat_custom_request" action="" method="POST" enctype="multipart/form-data" novalidate>

                            <div class="row row_gap_18">

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="firstName" placeholder="First name*"  value="<?php echo esc_attr( $vr_first_name ?? '' ); ?>" required>
                                    <span class="error-label text-danger" data-for="firstName"></span>
                                </div>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="lastName" placeholder="Last name*"  value="<?php echo esc_attr( $vr_last_name ?? '' ); ?>" required>
                                    <span class="error-label text-danger" data-for="lastName"></span>
                                </div>

                                <div class="col-md-6">
                                    <input type="tel" class="form-control" name="phoneNumber" error-for="phoneNumber" placeholder="Phone number*" required  value="<?php echo esc_attr( $vr_phone ?? '' ); ?>">
                                    <span class="error-label text-danger" data-for="phoneNumber"></span>
                                </div>

                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email" placeholder="Your email*" required  value="<?php echo esc_attr( $vr_email ?? '' ); ?>">
                                    <span class="error-label text-danger" data-for="email"></span>
                                </div>

                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="refSite" placeholder="Reference URL">
                                </div>

                                <div class="col-md-12">
                                    <input type="file" class="form-control ls_fileupld" name="files[]" max="3" multiple>
                                    <div class="note-text fw-normal mt-1">
                                        Note: Upload Reference Images/URL (Highly Recommended). Inspiration photos help us match your design accurately.
                                    </div>
                                    <div class="lsimgprev d-flex gap-2 mx-2 mt-3"></div>
                                </div>

                                <div class="col-md-12">
                                    <textarea class="form-control" name="message" rows="7" required
                                        placeholder="To begin your custom design, please share:&#10;• Ring size&#10;• Metal (14K / 18K, White / Yellow / Rose Gold or Platinum)&#10;• Center stone size or SKU&#10;• Reference link or upload photos above&#10;&#10;(If unsure, just tell us — our specialist will guide you.)"
                                    ></textarea>
                                    <span class="error-label text-danger" data-for="message"></span>
                                    <div class="note-text fw-normal mt-1">Before submitting: Please ensure you've included ring size, metal, and center stone details. It helps us create your CAD & 3D faster.</div>
                                </div>

                                <div class="col-md-12 text-end mt-3">
                                    <button type="submit" class="ask_quote_btn">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p class="mt-4 border border-danger text-danger p-2 ls-error" style="display: none;">Error</p>
                        <p class="mt-4 border border-success text-success p-2 ls-success" style="display: none;">Success</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

            var form = document.getElementById('pcat_custom_request');
            if (!form) return;

            var fileInput = form.querySelector('.ls_fileupld') || document.querySelector('.ls_fileupld');
            var preview   = document.querySelector('.lsimgprev');
            var masterFiles = [];

            /* ---------- Validation ---------- */

            function findLabel(target, el) {
                var label = form.querySelector('.error-label[data-for="' + target + '"]');
                if (!label && el && el.name) {
                    label = form.querySelector('.error-label[data-for="' + el.name + '"]');
                }
                return label;
            }

            function clearOnInput(el, label) {
                function handler() {
                    label.textContent = '';
                    el.removeEventListener('input', handler);
                    el.removeEventListener('change', handler);
                }
                el.addEventListener('input', handler);
                el.addEventListener('change', handler);
            }

            function validateFormJS(form) {
                var required = form.querySelectorAll('[required]');
                var errors = [];

                form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });

                required.forEach(function (el) {
                    if (el.type === 'file') return;

                    var val = (el.value || '').trim();
                    var target = el.getAttribute('error-for') || el.getAttribute('data-target') ||
                                 el.getAttribute('target') || el.name || el.id;

                    if (!val) {
                        errors.push({ el: el, message: 'This field is required.', target: target });
                        return;
                    }

                    if (el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        errors.push({ el: el, message: 'Please enter a valid email address.', target: target });
                        return;
                    }

                    if (el.type === 'tel' && !/^\+?[0-9\s\-()]{7,15}$/.test(val)) {
                        errors.push({ el: el, message: 'Please enter a valid phone number.', target: target });
                        return;
                    }

                    var pattern = el.getAttribute('pattern');
                    if (pattern) {
                        try {
                            if (!new RegExp(pattern).test(val)) {
                                errors.push({ el: el, message: 'Invalid format.', target: target });
                            }
                        } catch (e) {}
                    }
                });

                return { valid: errors.length === 0, errors: errors };
            }

            /* ---------- Messages ---------- */

            function showMsg(box, msg) {
                if (!box) return;
                box.innerHTML = msg;
                box.style.transition = '';
                box.style.opacity = 1;
                box.style.display = 'block';
            }

            function fadeOut(box) {
                if (!box || box.style.display === 'none') return;
                box.style.transition = 'opacity 400ms';
                box.style.opacity = 0;
                setTimeout(function () { box.style.display = 'none'; }, 400);
            }

            /* ---------- Submit ---------- */

            form.querySelectorAll('.ask_quote_btn').forEach(function (submitBtn) {
                submitBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    var validation = validateFormJS(form);
                    if (!validation.valid) {
                        validation.errors.forEach(function (err) {
                            var label = findLabel(err.target || err.el.name || err.el.id, err.el);
                            if (label) {
                                label.textContent = err.message;
                                clearOnInput(err.el, label);
                            }
                        });
                        validation.errors[0].el.focus();
                        return;
                    }

                    var formData = new FormData(form);
                    formData.delete('files[]');
                    masterFiles.forEach(function (file) {
                        if (file && file.size > 0 && file.name) formData.append('files[]', file);
                    });

                    submitBtn.disabled = true;
                    submitBtn.insertAdjacentHTML('beforeend',
                        '<span class="spinner-border spinner-border-sm me-2 ls-spinner" role="status" aria-hidden="true"></span>');

                    var modalBody  = form.closest('.modal-body') || form.parentElement;
                    var msgError   = modalBody.querySelector('.ls-error');
                    var msgSuccess = modalBody.querySelector('.ls-success');

                    function resetBtn() {
                        submitBtn.disabled = false;
                        var sp = submitBtn.querySelector('.ls-spinner');
                        if (sp) sp.remove();
                    }

                    fetch("<?php echo esc_url( rest_url( 'ls/v1/custom-setting-request' ) ); ?>", {
                        method: 'POST',
                        body: formData
                    })
                    .then(function (res) {
                        return res.json().catch(function () { return {}; }).then(function (data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function (r) {
                        var response = r.data;

                        if (!r.ok) {
                            showMsg(msgError, response.error || 'An error occurred. Please try again.');
                            setTimeout(function () { fadeOut(msgError); }, 5000);
                            return;
                        }

                        if (response.success) {
                            showMsg(msgSuccess, response.success);
                            form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });
                            window.location.replace("<?php echo esc_url( home_url( '/thank-you/' ) ); ?>");
                            return;
                        }

                        if (response.error) {
                            showMsg(msgError, response.error);
                            if (response.fieldErrors) {
                                Object.keys(response.fieldErrors).forEach(function (name) {
                                    var input = form.querySelector('[name="' + name + '"]');
                                    var label = form.querySelector('.error-label[data-for="' + name + '"]');
                                    if (!label && input) {
                                        var t = input.getAttribute('error-for') || input.getAttribute('data-target') ||
                                                input.getAttribute('target') || input.id || name;
                                        label = form.querySelector('.error-label[data-for="' + t + '"]');
                                    }
                                    if (label) {
                                        label.textContent = response.fieldErrors[name];
                                        if (input) clearOnInput(input, label);
                                    }
                                });
                            }
                        } else {
                            showMsg(msgError, 'An unexpected response was received.');
                        }
                        setTimeout(function () { fadeOut(msgError); }, 5000);
                    })
                    .catch(function () {
                        showMsg(msgError, 'An error occurred. Please try again.');
                        setTimeout(function () { fadeOut(msgError); }, 5000);
                    })
                    .finally(resetBtn);
                });
            });

            /* ---------- File uploads (multi-select, keeps a master list) ---------- */

            function syncFilesToInput() {
                if (!fileInput) return;
                var dt = new DataTransfer();
                masterFiles.forEach(function (f) { dt.items.add(f); });
                fileInput.files = dt.files;
            }

            if (fileInput && preview) {
                fileInput.addEventListener('change', function (e) {
                    Array.from(e.target.files).forEach(function (file) {
                        var dup = masterFiles.some(function (f) {
                            return f.name === file.name && f.size === file.size;
                        });
                        if (dup) return;

                        masterFiles.push(file);
                        preview.insertAdjacentHTML('beforeend',
                            '<span class="pip">' +
                                '<img class="imageThumb" src="' + URL.createObjectURL(file) + '" />' +
                                '<span class="remove">×</span>' +
                            '</span>');
                    });
                    syncFilesToInput();
                });

                preview.addEventListener('click', function (e) {
                    var removeBtn = e.target.closest('.remove');
                    if (!removeBtn) return;

                    var pip  = removeBtn.closest('.pip');
                    var pips = Array.from(preview.querySelectorAll('.pip'));
                    var idx  = pips.indexOf(pip);

                    if (idx > -1) masterFiles.splice(idx, 1);
                    var img = pip.querySelector('img');
                    if (img) URL.revokeObjectURL(img.src);
                    pip.remove();

                    syncFilesToInput();
                });
            }
        });
        </script>

        <style type="text/css">
            .ls-cntctform .lsimgprev .pip {
                float: left;
                padding-right: 5px;
                position: relative;
            }
            .ls-cntctform .lsimgprev .pip img {
                width: 80px;
                height: 80px;
                object-fit: cover;
            }
            .ls-cntctform .lsimgprev .pip .remove {
                width: 20px;
                height: 20px;
                position: absolute;
                top: -8px;
                right: -5px;
                background: rgb(0 0 0 / 0.80);
                color: #ffffff;
                border-radius: 100%;
                text-align: center;
                line-height: 18px;
                font-size: 14px;
                cursor: pointer;
            }
            .error-label {
                display: block;
                font-size: 12px;
                min-height: 16px;
                margin-top: 3px;
            }
        </style>
        <?php
    //}

        ?>
        <div class="modal fade" id="askUsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h3 class=" text-uppercase  fw-600">Diamond Search Request</h3>
                            <p class="desc" style="font-size:14px;margin-bottom:12px;">
                                We'll help you find the best diamond based on your preferences and budget.
                            </p>
                        </div>
                        <form name="ask_us_request" class="ls-askusform" id="ask_us_request" action="" method="POST" enctype="multipart/form-data" novalidate>
                            <div class='row mb-3'>
                                <div class="col-md-4">
                                    <input type="text" placeholder='Your name *' class="form-control" id="name" name="name" required value="<?php if ( is_user_logged_in() ) {echo esc_attr( $vr_first_name ?? '' ).' '.esc_attr( $vr_last_name ?? '' ); }?>">
                                    <span class="error-label text-danger" data-for="name"></span>
                                </div>
                                <div class="col-md-4">
                                    <input type="email" placeholder='Your email *' class="form-control" id="email" name="email" required  value="<?php echo esc_attr( $vr_email ?? '' ); ?>">
                                    <span class="error-label text-danger" data-for="email"></span>
                                </div>
                                <div class="col-md-4">
                                    <input type="tel" placeholder="Phone number*" class="form-control" error-for="phone" id="phone" name="phone" required  value="<?php echo esc_attr( $vr_phone ?? '' ); ?>">
                                    <span class="error-label text-danger" data-for="phone"></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <input type="url"
                                        placeholder='Reference link or certificate number (optional but recommended)'
                                        pattern="[Hh][Tt][Tt][Pp][Ss]?:\/\/(?:(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)*(?:\.(?:[a-zA-Z\u00a1-\uffff]{2,}))(?::\d{2,5})?(?:\/[^\s]*)?"
                                        class="form-control"
                                        id="reference_url"
                                        name="reference_url"
                                    />
                                    <span class="error-label text-danger" data-for="reference_url"></span>
                                    <div class="note-text mt-2">Note: If you have a reference URL, we're happy to review and match or beat the price where possible.</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <textarea
                                        class="form-control"
                                        id="message"
                                        name="message"
                                        rows="7"
                                        placeholder="Optional details that help us find the best diamond:&#10;• Shape (Oval, Round, Emerald, etc.)&#10;• Carat range&#10;• Budget range&#10;• Color / clarity preference (if known)&#10;• Timeline (if needed by a certain date)"
                                        required
                                    ></textarea>
                                    <span class="error-label text-danger" data-for="message"></span>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <button type="submit" class="ls_dmnd_search_req_submit">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <p class="mt-4 border border-danger text-danger p-2 ls-error" style="display: none;">Error</p>
                        <p class="mt-4 border border-success text-success p-2 ls-success" style="display: none;">Success</p>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {

            /* ---------- Validation ---------- */

            function validateFormJS(form) {
                var required = form.querySelectorAll('[required]');
                var errors = [];

                form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });

                required.forEach(function (el) {
                    if (el.type === 'file') return;

                    var val = (el.value || '').trim();
                    var target = el.getAttribute('error-for') || el.getAttribute('data-target') ||
                                 el.getAttribute('target') || el.name || el.id;

                    if (!val) {
                        errors.push({ el: el, message: 'The field is required.', target: target });
                        return;
                    }

                    if (el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        errors.push({ el: el, message: 'Please enter a valid email address.', target: target });
                        return;
                    }

                    if (el.type === 'tel' && !/^\+?[0-9\s\-()]{7,15}$/.test(val)) {
                        errors.push({ el: el, message: 'Please enter a valid phone number.', target: target });
                        return;
                    }

                    var pattern = el.getAttribute('pattern');
                    if (pattern) {
                        try {
                            if (!new RegExp(pattern).test(val)) {
                                errors.push({ el: el, message: 'Invalid format.', target: target });
                            }
                        } catch (e) {}
                    }
                });

                return { valid: errors.length === 0, errors: errors };
            }

            function clearOnInput(el, label) {
                function handler() {
                    label.textContent = '';
                    el.removeEventListener('input', handler);
                    el.removeEventListener('change', handler);
                }
                el.addEventListener('input', handler);
                el.addEventListener('change', handler);
            }

            /* ---------- Messages ---------- */

            function showMsg(box, msg) {
                if (!box) return;
                box.innerHTML = msg;
                box.style.transition = '';
                box.style.opacity = 1;
                box.style.display = 'block';
            }

            function fadeOut(box) {
                if (!box || box.style.display === 'none') return;
                box.style.transition = 'opacity 400ms';
                box.style.opacity = 0;
                setTimeout(function () { box.style.display = 'none'; }, 400);
            }

            /* ---------- Submit ---------- */

            document.querySelectorAll('.ls_dmnd_search_req_submit').forEach(function (submitBtn) {
                submitBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    var form = submitBtn.closest('form');
                    if (!form) return;

                    var validation = validateFormJS(form);
                    if (!validation.valid) {
                        validation.errors.forEach(function (err) {
                            var tgt = err.target || err.el.name || err.el.id;
                            var label = form.querySelector('.error-label[data-for="' + tgt + '"]') ||
                                        form.querySelector('.error-label[data-for="' + (err.el.name || '') + '"]');
                            if (label) {
                                label.textContent = err.message;
                                clearOnInput(err.el, label);
                            }
                        });
                        validation.errors[0].el.focus();
                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.insertAdjacentHTML('beforeend',
                        '<span class="spinner-border spinner-border-sm me-2 ls-spinner" role="status" aria-hidden="true"></span>');

                    var modalBody  = form.closest('.modal-body') || form.parentElement;
                    var msgError   = modalBody.querySelector('.ls-error');
                    var msgSuccess = modalBody.querySelector('.ls-success');

                    function resetBtn() {
                        submitBtn.disabled = false;
                        var sp = submitBtn.querySelector('.ls-spinner');
                        if (sp) sp.remove();
                    }

                    fetch("<?php echo esc_url( rest_url( 'ls/v1/ask-us' ) ); ?>", {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(function (res) {
                        return res.json().catch(function () { return {}; }).then(function (data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function (r) {
                        var response = r.data;

                        if (!r.ok) {
                            showMsg(msgError, response.error || 'An error occurred. Please try again.');
                        } else if (response.success) {
                            showMsg(msgSuccess, response.success);
                            form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });
                            form.reset();
                            setTimeout(function () {
                                var modalEl = document.getElementById('askUsModal');
                                if (modalEl && window.bootstrap) {
                                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                                }
                            }, 5000);
                        } else if (response.error) {
                            showMsg(msgError, response.error);
                            if (response.fieldErrors) {
                                Object.keys(response.fieldErrors).forEach(function (name) {
                                    var input = form.querySelector('[name="' + name + '"]');
                                    var label = form.querySelector('.error-label[data-for="' + name + '"]');
                                    if (!label && input) {
                                        var t = input.getAttribute('error-for') || input.getAttribute('data-target') ||
                                                input.getAttribute('target') || input.id || name;
                                        label = form.querySelector('.error-label[data-for="' + t + '"]');
                                    }
                                    if (label) {
                                        label.textContent = response.fieldErrors[name];
                                        if (input) clearOnInput(input, label);
                                    }
                                });
                            }
                        } else {
                            showMsg(msgError, 'An unexpected response was received.');
                        }

                        setTimeout(function () {
                            fadeOut(msgSuccess);
                            fadeOut(msgError);
                        }, 5000);
                    })
                    .catch(function () {
                        showMsg(msgError, 'An error occurred. Please try again.');
                        setTimeout(function () { fadeOut(msgError); }, 5000);
                    })
                    .finally(resetBtn);
                });
            });
        });
        </script>
        <?php
}
add_action('wp_footer', 'ls_video_request_pup');
function ls_video_request_pup()
{
    // $has_video / $diamond / $vr_* are set in diamond-detail.php; only render
    // the popup when we're on that template and the diamond has no video.
    global $has_video, $diamond, $vr_first_name, $vr_last_name, $vr_email, $vr_phone;
    // Prefill data for the "Request this Video" popup when the customer is logged in
    $vr_first_name = '';
    $vr_last_name  = '';
    $vr_email      = '';
    $vr_phone      = '';

    if ( is_user_logged_in() ) {
        $current_user  = wp_get_current_user();
        $vr_first_name = $current_user->first_name ?: $current_user->display_name;
        $vr_last_name  = $current_user->last_name;
        $vr_email      = $current_user->user_email;
        $vr_phone      = get_user_meta( $current_user->ID, 'billing_phone', true );
    }
   /* if ( ! isset( $has_video ) || $has_video ) {
        return;
    }*/
    ?>
    <div class="modal fade" id="videoRequestModal" tabindex="-1" aria-labelledby="videoRequestModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content video-request-modal-content">
          <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
          <div class="modal-body">
            <h3 class="video-request-title" id="videoRequestModalLabel">Request This Video</h3>
            <p class="video-request-subtitle">No video available yet. Share your details, and we'll get one for you.</p>

            <div class="ls-success alert alert-success" style="display:none;"></div>
            <div class="ls-error alert alert-danger" style="display:none;"></div>

            <form id="videoRequestForm" novalidate>
              <input type="hidden" name="sku" value="<?php echo esc_attr( $diamond->sku ?? '' ); ?>">
              <input type="hidden" name="refSite" value="<?php echo esc_url( home_url( add_query_arg( null, null ) ) ); ?>">
              <div class="row gx-3">
                <div class="col-sm-6 mb-3">
                  <input type="text" class="form-control" name="firstName" placeholder="First name*" required value="<?php echo esc_attr( $vr_first_name ?? '' ); ?>">
                  <label class="error-label text-danger" data-for="firstName"></label>
                </div>
                <div class="col-sm-6 mb-3">
                  <input type="text" class="form-control" name="lastName" placeholder="Last name*" required value="<?php echo esc_attr( $vr_last_name ?? '' ); ?>">
                  <label class="error-label text-danger" data-for="lastName"></label>
                </div>
                <div class="col-sm-6 mb-3">
                  <input type="tel" class="form-control" id="vrPhoneNumber" name="phoneNumber" placeholder="Phone number*"  error-for="phoneNumber"  required value="<?php echo esc_attr( $vr_phone ?? '' ); ?>">
                  <label class="error-label text-danger" data-for="phoneNumber"></label>
                </div>
                <div class="col-sm-6 mb-3">
                  <input type="email" class="form-control" name="email" placeholder="Your email*" required value="<?php echo esc_attr( $vr_email ?? '' ); ?>">
                  <label class="error-label text-danger" data-for="email"></label>
                </div>
              </div>

              <div class="mb-3">
                <textarea class="form-control video-request-note-field" name="note" rows="3" placeholder="Note - Video requests take 24&ndash;48 business hours. As inventory is live, the diamond may sell out before the video is available."></textarea>
              </div>

              <button type="submit" class="video-request-submit-btn aj-black-btn-color">Submit Request</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <style>
      #videoRequestModal .video-request-title{font-family:inherit;text-transform:uppercase;letter-spacing:1px;font-weight:600;text-align:center;margin-bottom:10px;}
      #videoRequestModal .video-request-subtitle{text-align:center;color:#777;margin-bottom:24px;}
      #videoRequestModal .video-request-note-field{background:#f7f7f7;border:1px solid #eee;color:#888;font-size:13px;line-height:1.5;resize:vertical;}
      #videoRequestModal .video-request-note-field::placeholder{color:#888;}
      #videoRequestModal .video-request-submit-btn{width:100%;border-radius:4px;padding:14px;text-transform:uppercase;letter-spacing:1px;font-weight:600;}
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        var form = document.getElementById('videoRequestForm');
        if (!form) return;

        var phoneInputEl = document.getElementById('vrPhoneNumber');

        // Look up the existing intl-tel-input instance (initialized elsewhere).
        // Done lazily so it works even if the global init runs after this script.
        function getIti() {
            if (phoneInputEl && window.intlTelInputGlobals &&
                typeof window.intlTelInputGlobals.getInstance === 'function') {
                return window.intlTelInputGlobals.getInstance(phoneInputEl) || null;
            }
            return null;
        }

        // Populate hidden SKU from the clicked trigger each time the modal opens
        document.addEventListener('show.bs.modal', function (e) {
            if (!e.target || e.target.id !== 'videoRequestModal') return;
            var trigger = e.relatedTarget;
            var sku = trigger ? trigger.getAttribute('data-sku') : null;
            if (sku) {
                var skuInput = form.querySelector('input[name="sku"]');
                if (skuInput) skuInput.value = sku;
            }
        });

        function clearOnInput(el, label) {
            function handler() {
                label.textContent = '';
                el.removeEventListener('input', handler);
                el.removeEventListener('change', handler);
            }
            el.addEventListener('input', handler);
            el.addEventListener('change', handler);
        }

        function validateFormJS(form, iti) {
            var required = form.querySelectorAll('[required]');
            var errors = [];

            form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });

            required.forEach(function (el) {
                if (el.type === 'file') return;

                var val = (el.value || '').trim();
                var target = el.getAttribute('error-for') || el.getAttribute('data-target') ||
                             el.getAttribute('target') || el.name || el.id;

                // Phone field with intl-tel-input
                if (el.id === 'vrPhoneNumber' && iti) {
                    if (!val) {
                        errors.push({ el: el, message: 'The field is required.', target: target });
                    } else if (!iti.isValidNumber()) {
                        errors.push({ el: el, message: 'Please enter a valid phone number.', target: target });
                    }
                    return;
                }

                if (!val) {
                    errors.push({ el: el, message: 'The field is required.', target: target });
                    return;
                }

                if (el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    errors.push({ el: el, message: 'Please enter a valid email address.', target: target });
                    return;
                }

                var pattern = el.getAttribute('pattern');
                if (pattern) {
                    try {
                        if (!new RegExp(pattern).test(val)) {
                            errors.push({ el: el, message: 'Invalid format.', target: target });
                        }
                    } catch (e) {}
                }
            });

            return { valid: errors.length === 0, errors: errors };
        }

        function show(box, text) {
            if (!box) return;
            box.textContent = text;
            box.style.display = 'block';
        }

        function hide(box) {
            if (!box) return;
            box.style.display = 'none';
            box.textContent = '';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var iti       = getIti();
            var modalBody = form.closest('.modal-body') || form.parentElement;
            var success   = modalBody.querySelector('.ls-success');
            var error     = modalBody.querySelector('.ls-error');
            var btn       = form.querySelector('.video-request-submit-btn');

            hide(success);
            hide(error);

            var validation = validateFormJS(form, iti);
            if (!validation.valid) {
                validation.errors.forEach(function (err) {
                    var tgt = err.target || err.el.name || err.el.id;
                    var label = form.querySelector('.error-label[data-for="' + tgt + '"]') ||
                                form.querySelector('.error-label[data-for="' + (err.el.name || '') + '"]');
                    if (label) {
                        label.textContent = err.message;
                        clearOnInput(err.el, label);
                    }
                });
                validation.errors[0].el.focus();
                return;
            }

            // Fill the hidden phoneNumber field (site's own handler never runs)
            if (iti) {
                var hiddenPhone = form.querySelector('input[type="hidden"][name="phoneNumber"]');
                if (hiddenPhone) hiddenPhone.value = iti.getNumber();
            }

            function resetBtn() {
                if (!btn) return;
                btn.disabled = false;
                btn.textContent = 'Submit Request';
            }

            if (btn) {
                btn.disabled = true;
                btn.insertAdjacentHTML('beforeend',
                    '<span class="spinner-border spinner-border-sm me-2 ls-spinner" role="status" aria-hidden="true"></span>');
            }

            var body = new URLSearchParams(new FormData(form));
            body.set('action', 'submit_ask_video_request');

            fetch("<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>", {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            })
            .then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function (r) {
                var response = r.data;

                if (r.ok && response.success) {
                    show(success, response.data);
                    form.querySelectorAll('.error-label').forEach(function (l) { l.textContent = ''; });

                    // Clear explicitly (reset() would restore prefilled values)
                    form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], textarea')
                        .forEach(function (el) { el.value = ''; });
                    if (iti) iti.setNumber('');

                    setTimeout(function () {
                        var modalEl = document.getElementById('videoRequestModal');
                        if (modalEl && window.bootstrap) {
                            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                        }
                    }, 1800);
                } else {
                    show(error, response.data || 'Something went wrong. Please try again.');

                    if (response.fieldErrors) {
                        Object.keys(response.fieldErrors).forEach(function (name) {
                            var input = form.querySelector('[name="' + name + '"]');
                            var label = form.querySelector('.error-label[data-for="' + name + '"]');
                            if (!label && input) {
                                var t = input.getAttribute('error-for') || input.getAttribute('data-target') ||
                                        input.getAttribute('target') || input.id || name;
                                label = form.querySelector('.error-label[data-for="' + t + '"]');
                            }
                            if (label) {
                                label.textContent = response.fieldErrors[name];
                                if (input) clearOnInput(input, label);
                            }
                        });
                    }
                }
            })
            .catch(function () {
                show(error, 'Something went wrong. Please try again.');
            })
            .finally(resetBtn);
        });
    });
    </script>
    <?php
}

add_action('wp_ajax_submit_ask_video_request', 'ls_ajax_video_request');
add_action('wp_ajax_nopriv_submit_ask_video_request', 'ls_ajax_video_request');

function ls_ajax_video_request()
{
    $firstName = isset($_POST['firstName']) ? sanitize_text_field($_POST['firstName']) : '';
    $lastName  = isset($_POST['lastName']) ? sanitize_text_field($_POST['lastName']) : '';
    $phone     = isset($_POST['phoneNumber']) ? sanitize_text_field($_POST['phoneNumber']) : '';
    $email     = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $sku       = isset($_POST['sku']) ? sanitize_text_field($_POST['sku']) : '';
    $refurl    = isset($_POST['refSite']) ? esc_url_raw($_POST['refSite']) : '';
    $note      = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
        wp_send_json_error('* All fields are required');
    }

    if (!is_email($email)) {
        wp_send_json_error('Please enter a valid email address.');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ls_contact_submissions';

    // Prevent duplicate requests for the same diamond by the same customer
    if ($sku) {
        $already_requested = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE form_type = %s AND email = %s AND reference_url = %s",
                'video-request',
                $email,
                $sku
            )
        );

        if ($already_requested > 0) {
            wp_send_json_error('You have already requested a video for this diamond. Our team will reach out within 24-48 business hours.');
        }
    }

    $to = get_option('admin_email');
    $subject = 'Loose Grown Diamond - "Video Request"';
    $message = 'Customer requested a video for diamond SKU: ' . $sku;

    ob_start();
    
        $html_content = '
        Hi ' . esc_html($firstName) . ',<br><br>
        Greetings from Loose Grown Diamond,<br><br>
        We received your video request for SKU <strong>' . esc_html($sku) . '</strong>. As inventory is live, please note the diamond may sell out before the video is ready. Our team will get back to you within 24-48 business hours.<br><br>
        For faster assistance, you can directly call, WhatsApp, or text us at +91 9016106290.<br><br>
        Regards,<br>' . SITE_OC_NAME;
    

    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $firstName,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content,
            'email_heading' => 'Video Request',
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body = ob_get_clean();
    ob_start();
    $html_content2 = '';
    $html_content2 .= '<strong>Submission details:</strong><br><br>';
    $html_content2 .= '<table style="border-collapse:collapse;width:100%;max-width:600px;">';
    $html_content2 .= '<tr><td style="padding:8px;">Name</td><td style="padding:8px;">' . esc_html($firstName . ' ' . $lastName) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Email</td><td style="padding:8px;">' . esc_html($email) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Phone</td><td style="padding:8px;">' . esc_html($phone) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">SKU</td><td style="padding:8px;">' . esc_html($sku) . '</td></tr>';
    $html_content2 .= '<tr><td style="padding:8px;">Reference URL</td><td style="padding:8px;">' . esc_html($refurl) . '</td></tr>';
    if ($note !== '') {
        $html_content2 .= '<tr><td style="padding:8px;">Note</td><td style="padding:8px;">' . nl2br(esc_html($note)) . '</td></tr>';
    }
    $html_content2 .= '</table><br>';
    $html_content2 .= 'Regards,<br>' . SITE_OC_NAME;

    wc_get_template(
        'inc/email-templates/forms/customer-send.php',
        array(
            'uname'         => $firstName,
            'email'         => $email,
            'message'       => $message,
            'html_content'  => $html_content2,
            'email_heading' => 'Video Request',
        ),
        '',
        get_stylesheet_directory() . '/'
    );

    $body2 = ob_get_clean();
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_remote_post(
        ZOHOAPIENDPOINT . 'lead-create',
        [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'lead_type'    => 'video_request',
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $email,
                'mobile'       => $phone,
                'sku'          => $sku,
                'reference_url'=> $refurl,
                'note'         => $note,
                'country_code' => 91,
            ]),
            'timeout' => 15,
        ]
    );

    if (wp_mail($to, $subject, $body2, $headers)) {
        ls_store_submission([
            'form_type' => 'video-request',
            'name'      => $firstName . ' ' . $lastName,
            'email'     => $email,
            'phone'     => $phone,
            'message'   => $note !== '' ? $message . ' | Note: ' . $note : $message,
            'reference' => $sku,
            'attachments' => '',
        ]);
        wp_mail($email, $subject, $body, $headers);
        wp_send_json_success('Thanks! We\'ll get a video ready for this diamond and reach out within 24-48 business hours.');
    } else {
        wp_send_json_error('Failed to send your request. Please try again later.');
    }
}