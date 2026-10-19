<?php
/**
 * Shared page sections used by more than one template.
 *
 * ls_render_faq_cards() – the "FAQ cards" block (Read more / Less).
 *   Used by home-custom.php and page-melee-diamonds-for-sale.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Print a full `.section > .container-fluid` block with FAQ cards.
 *
 * @param string $title Section heading.
 * @param array  $faqs  [ [ 'question' => string, 'answer' => string[] ], ... ]
 */
function ls_render_faq_cards( $title, array $faqs ) {
    static $script_printed = false;
    ?>
    <div class="section">
        <div class="container-fluid">
            <h2 class="resource-cards__title"><?php echo esc_html( $title ); ?></h2>
            <div class="resource-cards">
                <?php foreach ( $faqs as $faq ) : ?>
                    <article class="resource-card">
                        <h3 class="resource-card__title">
                            <?php echo esc_html( $faq['question'] ); ?>
                            <svg class="resource-card__icon" width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                                <path d="M1 5l4-4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </h3>
                        <div class="resource-card__text">
                            <?php foreach ( (array) $faq['answer'] as $paragraph ) : ?>
                                <p><?php echo esc_html( $paragraph ); ?></p>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="resource-card__toggle" aria-expanded="false">Read more</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    if ( ! $script_printed ) {
        $script_printed = true;
        ?>
        <script>
        document.querySelectorAll('.resource-card').forEach(function (card) {
            var text = card.querySelector('.resource-card__text');
            var btn = card.querySelector('.resource-card__toggle');
            if (text.scrollHeight <= text.clientHeight + 1) {
                btn.hidden = true;
                return;
            }
            btn.addEventListener('click', function () {
                var open = card.classList.toggle('is-open');
                btn.textContent = open ? 'Less' : 'Read more';
                btn.setAttribute('aria-expanded', open);
            });
        });
        </script>
        <?php
    }
}

/**
 * "Book a Free 30 Minute Consultation" band + "Why RRP Diamond?" icon row.
 * Used on the single product page.
 */
function ls_render_consult_why() {
    $icons = get_template_directory_uri() . '/rrp-img/melee-diamonds-for-sale/icons/';
    $why   = [
        [ 'Our_-Sources.svg',      'Our Sources',      'Our diamonds are conflict and blood free.' ],
        [ 'Our_-Assortments.svg',  'Our Assortments',  'Quality ensured by proficient assorters.' ],
        [ 'Our_-Calibration.svg',  'Our Calibration',  'Fully calibrated diamonds, equal in size.' ],
        [ 'Our_-Packaging.svg',    'Our Packaging',    'Individually packed and labeled by size.' ],
        [ 'Express_-Delivery.svg', 'Express Delivery', 'Fast and insured delivery worldwide.' ],
        [ 'No_-Lab_Grown.svg',     'No Lab Grown',     'All diamonds tested, 100% natural and mined.' ],
    ];
    ?>
    <div id="book_Consultation" class="ls-consult">
        <div class="container-fluid">
            <div class="row">
                <div class="col-2 d-none d-md-block">
                    <img src="<?php echo get_template_directory_uri() . '/rrp-img/pdp/bg_book_consultation_2.png'; ?>" alt="">
                </div>
                <div class="col-12 col-md-8">
                    <h2>Book a Free 30 Minute Consultation Call with Gemologist</h2>
                    <a class="ls-consult__btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" data-bs-toggle="modal" data-bs-target="#lsConsultModal">Schedule Now</a>
                </div>
                <div class="col-2 d-none d-md-block">
                    <img src="<?php echo get_template_directory_uri() . '/rrp-img/pdp/bg_book_consultation_1.png'; ?>" alt="">
                </div>
            </div>
        </div>
    </div>
    <?php
    $c_name = $c_email = $c_phone = '';
    if ( is_user_logged_in() ) {
        $cu      = wp_get_current_user();
        $c_name  = trim( ( $cu->first_name ?: $cu->display_name ) . ' ' . $cu->last_name );
        $c_email = $cu->user_email;
        $c_phone = get_user_meta( $cu->ID, 'billing_phone', true );
    }
    ?>
    <div class="modal fade ls-consult-modal" id="lsConsultModal" tabindex="-1" aria-labelledby="lsConsultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ls-consult-modal__box">
                        <h3 class="text-center">CONSULTATION REQUEST</h3>
                    <form class="ls-consult-modal__form" id="ls-consult-form" novalidate>
                        <div class="row row-gap-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your name*" value="<?php echo esc_attr( $c_name ); ?>">
                                <small class="text-danger" data-err="name"></small>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your email*" value="<?php echo esc_attr( $c_email ); ?>">
                                <small class="text-danger" data-err="email"></small>
                            </div>
                            <div class="col-md-12">
                                <input type="tel" name="number" class="form-control" placeholder="Your Number*" value="<?php echo esc_attr( $c_phone ); ?>">
                                <small class="text-danger" data-err="number"></small>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control" rows="3" placeholder="Your message*"></textarea>
                                <small class="text-danger" data-err="message"></small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary text-uppercase mt-4">Submit</button>
                        <p class="mt-3 border border-danger text-danger p-2" id="ls-consult-error" style="display:none;">Something went wrong. Please try again.</p>
                        <p class="mt-3 border border-success text-success p-2" id="ls-consult-success" style="display:none;">Thank you! We will contact you shortly.</p>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('ls-consult-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var v = function (n) { return form.elements[n].value.trim(); };
            var errs = {};
            if (!v('name')) errs.name = 'Name is required';
            if (!v('email')) errs.email = 'Email is required';
            else if (!/^\S+@\S+\.\S+$/.test(v('email'))) errs.email = 'Enter a valid email';
            if (!v('number')) errs.number = 'Phone number is required';
            else if (!/^\+?[0-9\s\-()]{7,15}$/.test(v('number'))) errs.number = 'Enter a valid phone number';
            if (!v('message')) errs.message = 'Message is required';
            form.querySelectorAll('[data-err]').forEach(function (el) { el.textContent = errs[el.getAttribute('data-err')] || ''; });
            if (Object.keys(errs).length) return;

            var btn = form.querySelector('button[type="submit"]');
            var ok = document.getElementById('ls-consult-success');
            var bad = document.getElementById('ls-consult-error');
            ok.style.display = bad.style.display = 'none';
            btn.disabled = true;
            fetch('<?php echo esc_url_raw( site_url( '/wp-json/ls/v1/contact-us' ) ); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams(new FormData(form))
            }).then(function (r) {
                if (!r.ok) throw new Error('failed');
                return r.json();
            }).then(function () {
                ok.style.display = 'block';
                form.reset();

                setTimeout(function () {
                    var modalEl = document.getElementById('lsConsultModal');
                    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                    ok.style.display = 'none';
                }, 2000);
            }).catch(function () {
                bad.style.display = 'block';
            }).finally(function () { btn.disabled = false; });
        });
    });
    </script>
    <div id="why_lab_cdmnd" class="ls-why">
        <div class="container">
            <h2 class="ls-why__title">Why RRP Diamond?</h2>
            <div class="ls-why__grid">
                <?php foreach ( $why as $item ) : ?>
                    <div class="ls-why__item">
                        <img src="<?php echo esc_url( $icons . $item[0] ); ?>" alt="<?php echo esc_attr( $item[1] ); ?>" loading="lazy">
                        <h5><?php echo esc_html( $item[1] ); ?></h5>
                        <p><?php echo esc_html( $item[2] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}
