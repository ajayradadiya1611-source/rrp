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
                <div class="col-2">
                    <img src="<?php echo get_template_directory_uri() . '/rrp-img/pdp/bg_book_consultation_1.png'; ?>">
                </div>
                <div class="col-8">
                    <h2>Book a Free 30 Minute Consultation Call with Gemologist</h2>
            <a class="ls-consult__btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Schedule Now</a>
                </div>
                <div class="col-2">
                    <img src="<?php echo get_template_directory_uri() . '/rrp-img/pdp/bg_book_consultation_1.png'; ?>">
                </div>
            </div>
            
        </div>
    </div>
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
