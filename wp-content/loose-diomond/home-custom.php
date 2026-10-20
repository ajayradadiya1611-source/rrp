<?php

/**
 * Template Name: Home Page
 */

get_header();
global $text_change;
global $oc_common;
$home_page = $text_change['home_page'];
$common_page = $text_change['common_page'];

$melee_shapes = [
    'round'        => 'Round',
    'princess'     => 'Princess',
    'emerald'      => 'Emerald',
    'baguette'     => 'Baguette',
    'pear'         => 'Pear',
    'old_european' => 'Old European',
];
$black_shapes = [
    'round'    => 'Round',
    'oval'     => 'Oval',
    'pear'     => 'Pear',
    'princess' => 'Princess',
    'cushion'  => 'Cushion',
    'marquise' => 'Marquise',
];
?>

<!-- start banner section -->
<div class="banner_section home_banner_hero hero-split">
    <div class="hero-split__inner container-fluid">
        <div class="hero-split__content">
            <h1 class="hero-split__title">Naturally Beautiful,<br/>Ethically Sourced Diamonds</h1>
            <p class="hero-split__text">Conflict free. Direct to you. No middlemen</p>
            <div class="hero-split__actions">
                <a href="/shop/" class="hero-split__btn">Shop Natural Melee Diamonds</a>
            </div>
        </div>
    </div>
    <?php
    $hero_d = $home_page['hero_banner_image_desktop'];
    $hero_m = $home_page['hero_banner_image_mobile'];
    $size_d = lgd_img_size( $hero_d );
    $size_m = lgd_img_size( $hero_m );
    ?>
    <div class="hero-split__media">
        <picture>
            <source
                media="(min-width: 992px)"
                srcset="<?php echo esc_url( $hero_d ); ?>"
                <?php if ( $size_d ) : ?>width="<?php echo $size_d[0]; ?>" height="<?php echo $size_d[1]; ?>"<?php endif; ?>>
            <img
                src="<?php echo esc_url( $hero_m ); ?>"
                <?php if ( $size_m ) : ?>width="<?php echo $size_m[0]; ?>" height="<?php echo $size_m[1]; ?>"<?php endif; ?>
                alt="Lab grown diamond engagement rings"
                fetchpriority="high"
                loading="eager">
        </picture>
    </div>
    <!-- <div class="ls_btmheader text-center">
        Buy now, pay later with affirm.
    </div> -->
    
</div>
<?php
    $marquee_items = [
        'Best Price Promise',
        'Skin-Friendly',
        'Conflict-Free Diamonds',
        'Stress-Free Purchase',
        'Free Insured Shipping*',
        'Easy & Free 7 Day Returns*',
        'Manufacturer Warranty',
    ];

    $marquee_repeat = 3; // copies for seamless scroll

    ob_start(); ?>
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="17" height="17" viewBox="0 0 17 17" fill="none">
        <mask id="mask0_4396_88374" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="17" height="17">
            <rect width="17" height="17" fill="url(#pattern0_4396_88374)"></rect>
        </mask>
        <g mask="url(#mask0_4396_88374)">
            <rect width="17" height="17" fill="black"></rect>
        </g>
        <defs>
            <pattern id="pattern0_4396_88374" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0_4396_88374" transform="scale(0.0588235)"></use>
            </pattern>
            <image id="image0_4396_88374" width="17" height="17" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAYAAAA7bUf6AAAAJGVYSWZJSSoACAAAAAEAPAECAAkAAAAaAAAAAAAAAGltYWdlcnk0AAAnPYy6AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAEaSURBVHgBvZMxTsNAEEX/TIygoMgRwg1IgeRyC7hCBKKIOQHcgHCDcANTIEQofASbCgRF4ATkCCkQgpDM4I0IshG7i1LkN9bK/7/9O/YCq9CViVshDwcNzEnIQ76XmTHNKX+8vMr61lFRjJ0b+SATnpwo0NzE+zGWaWJnETENLcSuP2XaPiwen/7d5GY3Ng2mfAGwWuMouzQ728EmNqzCXUATOKRCZwJJD4r7UQ0y2Ct3mDXKc5Mz/AcuFZX+fv7wXGti58CMLoF6vjCU0k5+d1tr8luD8uxKUUaE1k9UMSrNSTXshSxA4Gj4vRzPRNvVOVTl/E865eckaG/eQrTvAnghVpFsnNsWb5g/l9e1iU9DnuAFFOACq9AXVIVht6tjtuYAAAAASUVORK5CYII="></image>
        </defs>
    </svg>
    <?php $marquee_icon = ob_get_clean(); ?>

    <div class="marquee">
        <div class="marquee__content">
            <?php for ( $i = 0; $i < $marquee_repeat; $i++ ) : ?>
                <?php foreach ( $marquee_items as $item ) : ?>
                    <div class="marquee__item">
                        <?php echo $marquee_icon; ?>
                        <p><?php echo $item; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>
<!-- start popular categories section -->
<div class="section">
    <div class="container-fluid">
        <h2 class="text-center mb_40 h2">Popular Categories</h2>
        <div class="row">
            <div class="col-6">
                <a href="/product/melee-diamonds/" class="engagement_rings text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/home/popular_categories-1.svg"
                        alt="Start with a lab grown diamond"
                        width="555" height="400"
                        class="img-fluid mb-3 mb-md-4"
                        loading="lazy" decoding="async">
                    <p class="fw-600 text-uppercase mb-3 mb-md-0">Melee Diamonds</p>
                </a>
            </div>
            <div class="col-6">
                <a href="/product/calibrated-diamonds/" class="engagement_rings text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/home/popular_categories-2.svg"
                        alt="Start with an engagement ring setting"
                        width="555" height="400"
                        class="img-fluid mb-3 mb-md-4"
                        loading="lazy" decoding="async">
                    <p class="fw-600 text-uppercase mb-3 mb-md-0">Calibrated Diamonds</p>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- start melee shapes section -->
<div class="bg_light_gray">
<div class="section shape-circles shape-circles--melee">
    <div class="container-fluid">
        <h2 class="shape-circles__title">10+ Melee Diamonds Available in all Classic &amp; Custom Shapes</h2>
        <p class="shape-circles__subtitle">Choose from our wide range of high-Quality Natural Melee Diamonds.</p>
        <div class="shape-circles__list">
            <?php foreach ( $melee_shapes as $slug => $label ) : ?>
                <a class="shape-circles__item" href="<?php echo esc_url( $text_change['inventory_url'] . '?shape=' . $slug ); ?>">
                    <span class="shape-circles__circle">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/diamond/shape/' . $slug . '.webp' ); ?>" alt="<?php echo esc_attr( $label ); ?>" width="108" height="108" loading="lazy">
                    </span>
                    <span class="shape-circles__label"><?php echo esc_html( $label ); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>

<!-- start info cards section -->
<div class="section info-cards-section">
    <div class="container-fluid">
        <div class="info-cards">
            <?php $info_img = get_template_directory_uri() . '/rrp-img/home/'; ?>

            <article class="info-card info-card--tall">
                <div class="info-card__media">
                    <img src="<?php echo esc_url( $info_img . 'blog1.svg' ); ?>" width="360" height="326" alt="What are melee diamonds" loading="lazy">
                </div>
                <div class="info-card__body">
                    <h3 class="info-card__title">What are Melee Diamonds?</h3>
                    <p class="info-card__text">The word "melee" is used in the diamond business to refer to smaller brilliant-cut diamonds and any other small loose diamonds that are used to decorate the settings of bigger jewels. Melee is defined by the GIA as little diamonds weighing less than 1/5 carat that is either single cut or full cut (ct). Melee diamonds can be as little as 0.001 carats (1/1000 carat) in size.</p>
                    <a class="info-card__btn aj-blue-btn-color" href="<?php echo esc_url( home_url( '/melee-diamond' ) ); ?>">Shop Melee Diamonds</a>
                </div>
            </article>

            <article class="info-card info-card--wide info-card--media-right">
                <div class="info-card__body">
                    <h3 class="info-card__title">Do natural diamonds have value?</h3>
                    <p class="info-card__text">Natural diamonds are more precious. But many people in this culture may have outmoded logic. Its worth is determined by its emotional and monetary value. Natural diamonds have a much higher monetary value than lab diamonds (30%–40%) due to the difficult manufacturing process.</p>
                    <a class="info-card__btn aj-blue-btn-color" href="<?php echo esc_url( $text_change['inventory_url'] ); ?>">Shop Diamonds</a>
                </div>
                <div class="info-card__media">
                    <img src="<?php echo esc_url( $info_img . 'blog2.svg' ); ?>" width="300" height="381" alt="Do natural diamonds have value" loading="lazy">
                </div>
            </article>

            <article class="info-card info-card--wide info-card--media-left">
                <div class="info-card__media">
                    <img src="<?php echo esc_url( $info_img . 'blog3.svg' ); ?>" width="300" height="381" alt="Do natural diamonds last forever" loading="lazy">
                </div>
                <div class="info-card__body">
                    <h3 class="info-card__title">Do Natural Diamonds Last Forever?</h3>
                    <p class="info-card__text">Diamonds don't last indefinitely. Diamonds deteriorate to graphite under normal conditions because graphite has a lower energy configuration. Both diamonds (the material used in wedding rings) and graphite (the material used in pencils) are crystalline forms of pure carbon.</p>
                    <a class="info-card__btn aj-blue-btn-color" href="<?php echo esc_url( $text_change['inventory_url'] ); ?>">Shop Diamonds</a>
                </div>
            </article>
        </div>
    </div>
</div>

<!-- start black diamonds by shape section -->
<div class="bg_light_gray">
<div class="section shape-circles shape-circles--black">
    <div class="container-fluid">
        <h2 class="shape-circles__title">Shop Natural Black Diamonds by Shape</h2>
        <p class="shape-circles__subtitle">Choose from a collection of Natural Black Diamonds. Each Diamond is crafted with Love &amp; Passion.</p>
        <div class="shape-circles__list">
            <?php foreach ( $black_shapes as $slug => $label ) : ?>
                <a class="shape-circles__item" href="<?php echo esc_url( $text_change['inventory_url'] . '?shape=' . $slug ); ?>">
                    <span class="shape-circles__circle">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/rrp-img/home/shape/' . $slug . '.svg' ); ?>" alt="<?php echo esc_attr( $label ); ?>" width="165" height="165" loading="lazy">
                    </span>
                    <span class="shape-circles__label"><?php echo esc_html( $label ); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>

<!-- start salt and pepper section -->
<div class="section salt-pepper">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-6 col-12">
                <div class="salt-pepper__content">
                    <h2 class="salt-pepper__title">Shop Natural Salt and Pepper Diamonds</h2>
                    <p class="salt-pepper__text">Select your perfect one of a kind natural diamond from thousands of ethically sourced salt and pepper diamonds.</p>
                    <a class="salt-pepper__btn btn_comman_dark" href="<?php echo esc_url( $text_change['inventory_url'] . '?shape=salt-pepper' ); ?>">Shop Salt and Pepper Diamonds</a>
                </div>
            </div>
            <div class="col-lg-7 col-md-6 col-12">
                <img class="img-fluid w-100 salt-pepper__img" src="<?php echo esc_url( get_template_directory_uri() . '/rrp-img/home/salt-pepper-diamonds.png' ); ?>" alt="Natural salt and pepper diamonds" loading="lazy">
            </div>
        </div>
    </div>
</div>
<div class="bg_light_gray">
<?php single_product_buy_direct_us(); ?>
</div>
<!-- start customers say section -->
<!-- start featured section -->
<div class="section">
    <div class="container-fluid">
        <h2 class="text-center py_60 pt-0 mb-0 text-uppercase home_we_have_featured_title">We've been featured in</h2>
        <div class="featured_img">
            <div>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/digital_journal.svg" alt="digital_journal" loading="lazy">
            </div>
            <div>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/yahoo_news.svg" alt="yahoo_news" loading="lazy">
            </div>
            <div>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/deccan_chronicle.svg" alt="deccan_chronicle" loading="lazy">
            </div>
            <div>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/asian_age.svg" alt="asian_age" loading="lazy">
            </div>
        </div>
    </div>
</div>
<div class="bg_light_gray">
<div class="section">
    <?php echo single_product_what_customer_say(); ?>
</div>
</div>
<!-- start diamond resources -->

    <div class="section">
    <!-- natural diamond FAQs section -->
    <?php
    $faqs = [
        [
            'question' => 'How much is a mined diamond worth?',
            'answer'   => [
                'A mined 1-carat round brilliant diamond typically costs anywhere from about $2,500 to well over $10,000, depending on its colour, clarity, cut and certification. The round brilliant is the most popular shape, so it is the usual benchmark.',
                'The value of any diamond is set by the 4Cs: Carat, Colour, Cut and Clarity. Mined diamonds generally cost more than lab-grown diamonds of the same quality because of rarity, long-standing tradition and a longer supply chain.',
            ],
        ],
        [
            'question' => 'Is a natural diamond better than a lab-grown diamond?',
            'answer'   => [
                'Natural and lab-grown diamonds are chemically, physically and optically identical. Both are real diamonds made of carbon, and both are graded on the same 4Cs scale.',
                'The real differences are origin and price. Natural diamonds form over billions of years deep in the earth and cost more. Lab-grown diamonds are created in a controlled environment in weeks and usually cost significantly less for the same size and quality. Many buyers choose lab-grown for value and sustainability, while others prefer natural for rarity and tradition.',
            ],
        ],
        [
            'question' => 'How can you tell if a diamond is real?',
            'answer'   => [
                'The most reliable ways are a diamond tester (thermal or electrical conductivity), examination by a qualified gemologist, and a grading certificate from a lab such as GIA or IGI.',
                'A magnifying loupe can also help. Real diamonds usually have tiny natural inclusions and sharp, clean facet edges, while many fakes look flawless or have rounded edges.',
                'Home tricks like the fog test are not fully reliable on their own, because some imitations can pass them. If you are spending real money, always insist on a certificate.',
            ],
        ],
        [
            'question' => 'Do fake diamonds float?',
            'answer'   => [
                'No. Common imitations such as cubic zirconia, moissanite and glass are all denser than water, so they sink just like real diamonds. The float test cannot tell you whether a stone is genuine.',
                'To check a stone properly, use a diamond tester, have a gemologist examine it, or buy one that comes with a grading report from a recognised lab.',
            ],
        ],
        [
            'question' => 'How do you tell if a diamond is real with a flashlight?',
            'answer'   => [
                'A flashlight is not a dependable test. Diamonds do split light into flashes of white and colour, known as brilliance and fire. However, cubic zirconia and moissanite often show even more rainbow colour than a diamond does.',
                'Excessive rainbow flashes can in fact be a sign of an imitation. For a trustworthy result, use a diamond tester or ask a jeweller or gemologist to verify the stone.',
            ],
        ],
        [
            'question' => 'Are diamonds conflict-free?',
            'answer'   => [
                'Not always. Some mined diamonds have been linked to conflict, forced labour and human rights abuses, which is where the term "blood diamond" comes from. The Kimberley Process was introduced to reduce this, and many mined diamonds today are traceable and responsibly sourced, but it is not a perfect system.',
                'Lab-grown diamonds are created in controlled facilities and have no connection to mining conflicts, which makes them a straightforward choice if conflict-free origin is important to you.',
            ],
        ],
        [
            'question' => 'Are black diamonds blood diamonds?',
            'answer'   => [
                'No. The colour of a diamond has nothing to do with whether it is a "blood diamond". That term refers to diamonds mined in war zones and sold to fund armed conflict, and it can apply to a diamond of any colour.',
                'Natural black diamonds get their colour from dense graphite or other mineral inclusions, and some are treated to deepen the colour. Black diamonds can also be grown in a lab, which removes any sourcing concerns.',
            ],
        ],
        [
            'question' => 'What is the rarest diamond colour?',
            'answer'   => [
                'Red is widely considered the rarest natural diamond colour. Only a very small number of true red diamonds are known, and the largest examples have sold for millions of dollars, with prices reported at several million dollars for stones of around 5 carats.',
                'Other rare natural fancy colours include blue, pink, green and vivid orange. Yellow and brown are more common.',
            ],
        ],
        [
            'question' => 'How are natural diamonds formed?',
            'answer'   => [
                'Natural diamonds form when carbon is subjected to extreme heat and pressure in the Earth\'s mantle, roughly 150 to 200 kilometres (90 to 125 miles) below the surface. They are then brought up by deep volcanic eruptions through structures called kimberlite pipes.',
                'Most natural diamonds are between 1 and 3.5 billion years old. Trace elements present during formation can create coloured diamonds. For example, nitrogen produces yellow and boron produces blue.',
            ],
        ],
        [
            'question' => 'Are natural diamonds ethical?',
            'answer'   => [
                'It depends on the source. An ethical natural diamond comes from a mine with safe working conditions, fair pay and responsible environmental practices, and ideally has a traceable origin.',
                'Because mining has an environmental footprint and sourcing standards vary, many buyers prefer lab-grown diamonds, which avoid mining altogether. If you choose natural, ask your jeweller for details about where the stone came from.',
            ],
        ],
    ];

    ?>

    

    <div class="container">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="faq_list">
                    <div class="faq_accordion accordion" id="faqAccordion">

                        <?php foreach ( $faqs as $index => $faq ) : ?>
                            <?php
                            $is_youtube = ! empty( $faq['isyoutube'] ) && $faq['isyoutube'] === true;
                            $has_video  = $is_youtube && ! empty( $faq['youtube_link'] );
                            ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="heading<?php echo (int) $index; ?>">
                                    <button class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?php echo (int) $index; ?>"
                                            aria-expanded="false"
                                            aria-controls="collapse<?php echo (int) $index; ?>">
                                        <span><?php echo esc_html( $faq['question'] ); ?></span>

                                        <?php if ( $is_youtube ) : ?>
                                            <span class="faq-icon">
                                                <i class="fa-brands fa-youtube"></i>
                                            </span>
                                        <?php endif; ?>
                                    </button>
                                </h3>

                                <div id="collapse<?php echo (int) $index; ?>"
                                     class="accordion-collapse collapse"
                                     aria-labelledby="heading<?php echo (int) $index; ?>"
                                     data-bs-parent="#faqAccordion">

                                    <div class="accordion-body">

                                        <?php if ( $has_video ) : ?>

                                            <div class="ratio ratio-16x9">
                                                <iframe
                                                    src="<?php echo esc_url( str_replace( 'youtu.be/', 'www.youtube.com/embed/', $faq['youtube_link'] ) ); ?>"
                                                    title="YouTube video"
                                                    loading="lazy"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>

                                        <?php else : ?>

                                            <?php foreach ( (array) $faq['answer'] as $para ) : ?>
                                                <p><?php echo wp_kses_post( $para ); ?></p>
                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="bg_light_gray">
    <!-- start instagram section -->
    <div class="section mb-0 ">
        <div class="container-fluid">
            <h2 class="text-center py_25 pt-0 mb-0 home_we_have_instagram_title">Follow us on Instagram</h2>
            <div class="instagram-header d-flex justify-content-center align-items-center gap-2 mb-4">
                <a href="https://www.instagram.com/rrpdiamond/"
                    target="_blank"
                    rel="noopener nofollow"
                    class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/comman/logo.svg"
                        alt="rrpdiamond Instagram profile"
                        class=""
                        width="30"
                        height="30"
                        loading="lazy">
                    <span class="fw-medium text-decoration-underline text-dark">@rrpdiamond</span>
                </a>
            </div>
        </div>

        <!-- image grid -->
        <div class="row g-3 mx-0">
            <?php
            $insta_dir = get_template_directory_uri() . '/rrp-img/home/insta/';
            for ( $i = 1; $i <= 6; $i++ ) : ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="insta-item">
                        <img
                            src="<?php echo esc_url( $insta_dir . $i . '.webp' ); ?>"
                            class="img-fluid rounded"
                            alt="RRP Diamond Instagram post <?php echo $i; ?>"
                            width="400"
                            height="400"
                            loading="lazy"
                            decoding="async"
                            fetchpriority="low">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
