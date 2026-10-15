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
