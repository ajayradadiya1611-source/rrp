<?php
/**
 * Template Name: Diamond Price Calculator
 *
 * Posts to admin-ajax.php (action: submit_price_calculator_request),
 * handled in inc/price-calculator.php.
 */

get_header();
?>
<main id="main" class="dpc">
    <div class="container">
        <h1 class="dpc__title">Diamond Price Calculator</h1>
        <p class="dpc__intro">This calculator works as a guideline of value for most diamonds. It is not an indication of our purchase or sale price. We are very competitive in our valuations, but many factors go into pricing specific diamonds. This original Diamond Price Calculator™ has been relied upon by millions across the world since 2018.</p>

        <form class="dpc__form" id="dprice_calculator_request" method="POST" novalidate>
            <input type="hidden" name="action" value="submit_price_calculator_request">
            <input type="hidden" name="frm_name" value="dprice_calculator_request">

            <div class="row row-gap-3">
                <div class="col-md-6">
                    <label for="shape">Shape<span class="dpc__req">*</span></label>
                    <select name="shape" id="shape" data-required>
                        <option value=""> -- Select -- </option>
                        <option value="1">Asscher</option>
                        <option value="1">Cushion</option>
                        <option value="1">Emerald</option>
                        <option value="1">Heart</option>
                        <option value="1">Marquise</option>
                        <option value="1">Oval</option>
                        <option value="1">Pear</option>
                        <option value="2">Round</option>
                        <option value="1">Princess</option>
                        <option value="1">Radiant</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="size">Carat<span class="dpc__req">*</span></label>
                    <input type="text" name="size" id="size" value="1.00" data-required>
                </div>

                <div class="col-md-6">
                    <label for="color">Color<span class="dpc__req">*</span></label>
                    <select name="color" id="color" data-required>
                        <option value=""> -- Select -- </option>
                        <option value="7">D</option>
                        <option value="8">E</option>
                        <option value="9">F</option>
                        <option value="10">G</option>
                        <option value="11">H</option>
                        <option value="12">I</option>
                        <option value="13">J</option>
                        <option value="14">K</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="clarity">Clarity<span class="dpc__req">*</span></label>
                    <select name="clarity" id="clarity" data-required>
                        <option value=""> -- Select -- </option>
                        <option value="19">IF</option>
                        <option value="20">VVS1</option>
                        <option value="21">VVS2</option>
                        <option value="22">VS1</option>
                        <option value="23">VS2</option>
                        <option value="24">SI1</option>
                        <option value="25">SI2</option>
                        <option value="26">SI3</option>
                        <option value="27">I1</option>
                        <option value="28">I2</option>
                        <option value="29">I3</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="pc_lab">Laboratory</label>
                    <select name="pc_lab" id="pc_lab">
                        <option value=""> -- Select -- </option>
                        <option value="A">AGS Lab</option>
                        <option value="E">EGL USA</option>
                        <option value="F">EGL Int'l</option>
                        <option value="G">GIA Lab</option>
                        <option value="H">HRD Lab</option>
                        <option value="I">IGI Lab</option>
                        <option value="N">None</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="pc_prop">Proportions</label>
                    <select name="pc_prop" id="pc_prop">
                        <option value=""> -- Select -- </option>
                        <option value="5">Excellent</option>
                        <option value="6">Very Good</option>
                        <option value="7">Good</option>
                        <option value="8">Fair</option>
                        <option value="9">Poor</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="disc">Discount %<span class="dpc__req">*</span></label>
                    <input type="text" name="disc" id="disc" value="10" data-required>
                </div>
            </div>

            <div class="dpc__result">
                <p class="dpc__line"><span>Price:</span> <b class="dpc__msg">Hit Calculate to see our price!</b></p>
                <p class="dpc__line dpc__more" hidden><span>Price:</span> <b class="dpc__ppc"></b></p>
                <p class="dpc__line dpc__more" hidden><span>Total Price:</span> <b class="dpc__total"></b></p>
            </div>

            <div class="dpc__actions">
                <button type="submit" class="dpc__btn">Calculate</button>
            </div>
        </form>
    </div>
</main>

<style>
.dpc { padding: 30px 0 60px; }
.dpc__title { text-align: center; font-size: 32px; font-weight: 400; letter-spacing: 1px; margin-bottom: 20px; }
.dpc__intro { text-align: center; max-width: 1150px; margin: 0 auto 60px; color: #767676; line-height: 1.8; letter-spacing: 1px; }
.dpc__form { max-width: 800px; margin: 0 auto; }
.dpc__form label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
.dpc__req { color: #d00; margin-left: 2px; }
.dpc__form select,
.dpc__form input[type="text"] { width: 100%; height: 60px; padding: 15px; border: 0; border-radius: 4px; background-color: #f7f7f7; font-size: 14px; line-height: 15px; }
.dpc__form select:focus-visible,
.dpc__form input:focus-visible { outline: 0; box-shadow: 0 0 0 1px #000; }
.dpc__form .is-invalid { box-shadow: 0 0 0 1px #d00; }
.dpc__result { margin: 30px 0 10px; }
.dpc__line { margin: 0 0 4px; font-size: 14px; }
.dpc__line span { font-weight: 600; }
.dpc__line b { font-weight: 600; color: #118a00; }
.dpc__line b.is-error { color: #d00; }
.dpc__actions { text-align: right; }
.dpc__btn { background: #000; color: #fff; border: 1px solid #000; padding: 20px 50px; font-size: 15px; text-transform: uppercase; border-radius: 4px; }
.dpc__btn:hover { background: #fff; color: #000; }
.dpc__btn:disabled { opacity: .6; }
@media (max-width: 575px) { .dpc__title { font-size: 26px; } .dpc__btn { width: 100%; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('dprice_calculator_request');
    if (!form) return;

    var msg   = form.querySelector('.dpc__msg');
    var more  = form.querySelectorAll('.dpc__more');
    var ppc   = form.querySelector('.dpc__ppc');
    var total = form.querySelector('.dpc__total');
    var btn   = form.querySelector('.dpc__btn');
    var labels = { shape: 'shape', size: 'carat', color: 'color', clarity: 'clarity', disc: 'discount' };

    function showMore(show) { more.forEach(function (el) { el.hidden = !show; }); }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var ok = true;
        form.querySelectorAll('[data-required]').forEach(function (el) {
            var bad = el.value.trim() === '';
            el.classList.toggle('is-invalid', bad);
            if (bad && ok) {
                msg.textContent = 'You must select a value for ' + (labels[el.name] || el.name) + '.';
                msg.classList.add('is-error');
                showMore(false);
            }
            if (bad) ok = false;
        });
        if (!ok) return;

        btn.disabled = true;
        fetch(<?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            msg.textContent = res.message || '';
            msg.classList.toggle('is-error', !res.status);
            if (res.status) {
                ppc.textContent = res.price_carat;
                total.textContent = res.total_price_carat;
            }
            showMore(!!res.status);
        })
        .catch(function () {
            msg.textContent = 'Something went wrong. Please try again.';
            msg.classList.add('is-error');
            showMore(false);
        })
        .finally(function () { btn.disabled = false; });
    });
});
</script>
<?php get_footer(); ?>
