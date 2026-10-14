<?php

/**
 * Template Name: Diamond Price Calculator
 */

get_header();
?>

<div class="banner_section">
    <div class="container-fluid">
        <div class="calculator-main">
            <h1 class="diamond-calculator-heading">Diamond Price Calculator</h1>
            <p class="calculator-sub-title">
                To determine the price of a diamond depending on factors such as
                color, clarity, carat, and more, use our diamond price calculator.
                To calculate your diamond value, view a price history chart, and
                find comparable diamonds, all you need to do is choose the diamond's
                shape, carat, color, clarity, and other details.
            </p>

            <form action="" id="diamond-price-calculator-form" method="post">
                <input type="hidden" name="action" value="ls_submit_price_calculator_request">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('price_calc_nonce'); ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label for="">Shape <span>*</span></label>
                            <select class="form-select" name="shape" aria-label="Shape select">
                                <option value="" selected>-- Select --</option>
                                <option value="1">Asscher</option>
                                <option value="1">Cushion</option>
                                <option value="1">Emerald</option>
                                <option value="1">Heart</option>
                                <option value="1">Marquise</option>
                                <option value="1">Oval</option>
                                <option value="1">Pear</option>
                                <option value="1">Round</option>
                                <option value="1">Princess</option>
                                <option value="1">Radiant</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label for="">Carat <span>*</span></label>
                            <input
                                type="text"
                                name="carat"
                                placeholder="1.00"
                                value="1.00"
                                class="form-control" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label>Color <span>*</span></label>
                            <select class="form-select" name="color" aria-label="Color select">
                                <option value="" selected>-- Select --</option>
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
                    </div>
                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label>Clarity <span>*</span></label>
                            <select class="form-select" name="clarity" aria-label="Clarity select">
                                <option value="" selected>-- Select --</option>
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
                    </div>
                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label>Laboratory <span>*</span></label>
                            <select class="form-select" name="laboratory" aria-label="Laboratory select">
                                <option value="" selected>-- Select --</option>
                                <option value="A">AGS Lab</option>
                                <option value="E">EGL USA</option>
                                <option value="F">EGL Int'l </option>
                                <option value="G">GIA Lab</option>
                                <option value="H">HRD Lab</option>
                                <option value="I">IGI Lab</option>
                                <option value="N">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label>Proportions <span>*</span></label>
                            <select class="form-select" name="proportions" aria-label="Proportions select">
                                <option value="" selected>-- Select --</option>
                                <option value="5">Excellent</option>
                                <option value="6">Very Good</option>
                                <option value="7">Good</option>
                                <option value="8">Fair</option>
                                <option value="9">Poor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="common_select_dropdown mb_25">
                            <label for="">Discount % <span>*</span></label>
                            <input
                                type="text"
                                name="discount"
                                placeholder="1.00"
                                value="1.00"
                                class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="cprice d-none">
                    <label class="calculate-price d-flex gap-2 align-items-center base_price">
                        Price:
                        <div class="form-message"></div>
                    </label>
                    <label class="calculate-price d-flex gap-2 align-items-center pp_carat">
                        Price:
                        <div class="form-message"></div>
                    </label>
                    <label class="calculate-price d-flex gap-2 align-items-center ttl_price">
                        Total Price:
                        <div class="form-message"></div>
                    </label>
                </div>
                <!-- Message -->
                <div class="response-message mt-2"></div>
                <div class="d-flex justify-content-end">
                    <button class="common_btn" type="submit" id="calculate-diamond-price">
                        CALCULATE
                        <div id="calculate-diamond-price-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <div class="calcualator-details">
            <h1 class="diamond-calculator-heading">
                How to Calculate Diamond Price or Value?- Diamond Price Calculator
            </h1>

            <!-- Shape -->
            <div class="calcualator-details-part">
                <h3 class="calcualator-details-name">Shape</h3>
                <p class="calculate-details-point">
                    Round and fancy forms including cushion, oval, emerald, princess,
                    pear, Asscher radiant, marquise, and heart are among the many
                    shapes available for diamonds.
                </p>
                <p class="calculate-details-point">
                    Carat Diamond size is measured in terms of weight, with 1 carat
                    equal to 0.2 grams.
                </p>
                <p class="calculate-details-point">
                    Pick the diamond’s carat (size). In the US, an engagement ring
                    with a diamond weighs an average of 1.08 to 1.2 carats. The price
                    estimate for a diamond increases with its size.
                </p>
            </div>

            <!-- Color -->
            <div class="calcualator-details-part">
                <h3 class="calcualator-details-name">Color</h3>
                <p class="calculate-details-point">
                    The GIA rates diamonds from D to Z, with D standing for a
                    flawlessly white diamond that has no color. Additionally, diamonds
                    in the D to K color range are advised by several jewelers.
                </p>
                <p class="calculate-details-point">
                    This applies to diamonds where colorlessness is desired, not to
                    specially colored diamonds. We advise purchasing a diamond with a
                    color of G or above.
                </p>
            </div>

            <!-- Clarity -->
            <div class="calcualator-details-part">
                <h3 class="calcualator-details-name">Clarity</h3>
                <p class="calculate-details-point">
                    A diamond’s inclusions and flaws are measured using clarity
                    grades. The majority of jewelers advise taking into account the
                    FL–VS2 clear range.
                </p>
                <p class="calculate-details-point">
                    IF to I2 clarity options are available in our diamond price
                    estimator. For the highest clarity grades, a qualified grader
                    would be needed to inspect the diamond under strong magnification
                    to find flaws.
                </p>
                <p class="calculate-details-point">
                    When looking for diamonds, we advise starting with those that are
                    rated VS2 and higher because they will have excellent clarity and
                    flaws that are invisible to the unaided eye.
                </p>
            </div>

            <!-- Laboratory -->
            <div class="calcualator-details-part">
                <h3 class="calcualator-details-name">Laboratory</h3>
                <p class="calculate-details-point">
                    The most renowned and well-known diamond grading company is GIA.
                    Although AGS has less stringent requirements, it is also
                    well-known that some jewelers send their products there to receive
                    higher marks.
                </p>
                <p class="calculate-details-point">
                    You can choose from multiple laboratories in our diamond price
                    calculator, including GIA, AGS, EGL, IGI, and HRD.
                </p>
            </div>

            <!-- Proportions -->
            <div class="calcualator-details-part">
                <h3 class="calcualator-details-name">Proportions</h3>
                <p class="calculate-details-point">
                    A new feature called “proportions” describes the diamond’s quality
                    using the symbols Good, Very Good, Excellent, Fair, and Poor in
                    our diamond price calculator, which will assist you in making a
                    decision.
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.id !== 'diamond-price-calculator-form') return;

        e.preventDefault();

        const spinner    = document.getElementById('calculate-diamond-price-spinner');
        const priceBox   = form.querySelector('.cprice');
        const messageBox = form.querySelector('.response-message');

        const setText = (selector, text) => {
            const el = form.querySelector(selector);
            if (el) el.textContent = text;
        };

        const showError = (msg) => {
            if (!messageBox) return;
            messageBox.classList.add('text-danger');
            messageBox.textContent = msg;
        };

        if (spinner) spinner.classList.remove('d-none');
        if (priceBox) priceBox.classList.add('d-none');
        if (messageBox) {
            messageBox.classList.remove('text-danger', 'text-success');
            messageBox.textContent = '';
        }

        fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
            method: 'POST',
            body: new FormData(form) // browser sets multipart headers automatically
        })
        .then(res => {
            if (!res.ok) throw new Error('Server error');
            return res.json();
        })
        .then(response => {
            if (response.success && response.data) {
                const data = response.data;

                if (priceBox) priceBox.classList.remove('d-none');

                setText('.base_price .form-message', data.message || '');
                setText('.pp_carat .form-message', data.price_carat || '');
                setText('.ttl_price .form-message', data.total_price_carat || '');

                // if (messageBox) {
                //     messageBox.classList.add('text-success');
                //     messageBox.textContent = 'Calculation successful.';
                // }
            } else {
                showError(response?.data?.message || 'Something went wrong.');
            }
        })
        .catch(() => {
            showError('Server error. Please try again.');
        })
        .finally(() => {
            if (spinner) spinner.classList.add('d-none');
        });
    });

});
</script>

<?php
get_footer();
