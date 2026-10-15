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
    <div class="hero-split__inner container">
        <div class="hero-split__content">
            <h1 class="hero-split__title"><?php echo $home_page['hero_banner_heading']; ?></h1>
            <p class="hero-split__text"><?php echo $home_page['hero_banner_description']; ?></p>
            <div class="hero-split__actions">
                <a href="<?php echo esc_url( $home_page['hero_banner_button_link2'] ); ?>" class="hero-split__btn"><?php echo $home_page['hero_banner_button_text2']; ?></a>
                <a href="<?php echo esc_url( $home_page['hero_banner_button_link1'] ); ?>" class="hero-split__btn hero-split__btn--outline"><?php echo $home_page['hero_banner_button_text1']; ?></a>
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
        'Easy &amp; Free ' . RETURN_DAYS . ' Day Returns*',
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
                    <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/home-page/shop-by-engagement/engagement_rings_1.webp"
                        alt="Start with a lab grown diamond"
                        width="555" height="400"
                        class="img-fluid mb-3 mb-md-4"
                        loading="lazy" decoding="async">
                    <p class="fw-600 text-uppercase mb-3 mb-md-0">Melee Diamonds</p>
                </a>
            </div>
            <div class="col-6">
                <a href="/product/calibrated-diamonds/" class="engagement_rings text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/home-page/shop-by-engagement/engagement_rings_2.webp"
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
                    <a class="info-card__btn" href="<?php echo esc_url( home_url( '/melee-diamond' ) ); ?>">Shop Melee Diamonds</a>
                </div>
            </article>

            <article class="info-card info-card--wide info-card--media-right">
                <div class="info-card__body">
                    <h3 class="info-card__title">Do natural diamonds have value?</h3>
                    <p class="info-card__text">Natural diamonds are more precious. But many people in this culture may have outmoded logic. Its worth is determined by its emotional and monetary value. Natural diamonds have a much higher monetary value than lab diamonds (30%–40%) due to the difficult manufacturing process.</p>
                    <a class="info-card__btn" href="<?php echo esc_url( $text_change['inventory_url'] ); ?>">Shop Diamonds</a>
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
                    <a class="info-card__btn" href="<?php echo esc_url( $text_change['inventory_url'] ); ?>">Shop Diamonds</a>
                </div>
            </article>
        </div>
    </div>
</div>

<!-- start black diamonds by shape section -->
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

<!-- start salt and pepper section -->
<div class="section salt-pepper" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/rrp-img/home/salt-pepper-diamonds.png' ); ?>');">
    <div class="container-fluid">
        <div class="salt-pepper__content">
            <h2 class="salt-pepper__title">Shop Natural Salt and Pepper Diamonds</h2>
            <p class="salt-pepper__text">Select your perfect one of a kind natural diamond from thousands of ethically sourced salt and pepper diamonds.</p>
            <a class="salt-pepper__btn" href="<?php echo esc_url( $text_change['inventory_url'] . '?shape=salt-pepper' ); ?>">Shop Salt and Pepper Diamonds</a>
        </div>
    </div>
</div>
<?php single_product_buy_direct_us(); ?>
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
<div class="section">
    <?php echo single_product_what_customer_say(); ?>
</div>
<!-- start diamond resources -->
<div class="bg_light_gray">

    <!-- natural diamond FAQs section -->
    <div class="section">
        <div class="container-fluid">
            <h2 class="resource-cards__title">Natural Diamond's FAQs</h2>
            <?php
            $faqs = [
                [
                    'question' => 'How much is mined diamonds worth?',
                    'answer'   => [
                        'The typical cost of a mined diamond is roughly $2500–$3000 for a 1-carat brilliant round cut. We considered this form of the diamond since it is the most popular on the market. However, the true value of a mined diamond is determined by the 4Cs: Carat, Colour, Cut, and Clarity. The mined diamonds are valued higher due to long-standing customs.',
                    ],
                ],
                [
                    'question' => 'Is Natural diamond better than lab?',
                    'answer'   => [
                        'Natural diamonds are inferior to lab-grown diamonds. No scientific proof exists that natural diamonds are superior to lab-grown diamonds. This is simple. The only difference is the cut. A laboratory duplicates the conditions found deep within the earth to manufacture a pure diamond. A new generation values sustainability and environmental responsibility and prefers lab-grown diamonds.',
                    ],
                ],
                [
                    'question' => 'How can you test a diamond if a diamond is real?',
                    'answer'   => [
                        'To test a diamond, use a diamond tester, fogger, or magnifying lens. Buying a diamond is an expensive and emotional decision. So it\'s only natural to check its legitimacy. And since almost everyone thinks about it, it can be verified without expertise. We need to be ready to spot a fake diamond because there are so many.',
                        'Place a diamond in front of you and blow on it. A real diamond won\'t fog up easily since the fog doesn\'t cling to the diamond\'s surface.',
                        'A magnifying glass can identify a diamond. Examine the diamond using the magnifying glass.',
                        'Use a diamond tester to spot a fake: You can either buy a diamond tester or have a reputable jeweller use one.',
                    ],
                ],
                [
                    'question' => 'Do fake diamonds float?',
                    'answer'   => [
                        'Fake diamonds versus real diamonds Lab-made stones like YAG (yttrium aluminium garnet), CZ (synthetic cubic zirconia), and others are strikingly comparable to real diamonds. This makes you wonder how you can tell the real ones from the fakes without an expert\'s help. One way to detect if a diamond is real is if it floats. Fill a glass with water and drop your favourite possession into it to test its authenticity. A diamond that floats is a fake, as true diamonds never do. Diamonds are much denser than water, and denser things sink. So a fake diamond floats while a real diamond drowns.',
                    ],
                ],
                [
                    'question' => 'How do you tell if a diamond is real with a flashlight?',
                    'answer'   => [
                        'If a spotlight shines on the diamond, it shows a spectrum of seven colours.',
                        'In some situations, a flashlight can save your life, but in this case, it can assist you in evaluating whether your diamond is real or not. How do you ask? It\'s simple; just hold the flashlight in front of the diamond and watch the light pass through it. If the white light disperses into a rainbow, the diamond is real and you need not be concerned. The reason is that a real diamond has a larger refractive index, which makes spectrum beams reflect.',
                    ],
                ],
                [
                    'question' => 'Are diamond conflicts-free?',
                    'answer'   => [
                        'Man-made and natural diamonds are the two types—grown in nature and mined by people.',
                        'Since less physical effort and more high-tech machinery are involved, there is a very limited likelihood of conflict.',
                        'But natural diamonds are full of conflicts, from human exploitation to blood wars, child abuse, and even murder. While companies may claim to be free of conflicts, most are not. The term "bleeding diamonds" is frequently used to describe natural diamonds. A conflict-free natural diamond is not easy to come by.',
                        'We recommend lab-grown diamonds if you want non-conflict diamonds at a reasonable price. Most lab-grown diamonds look like mined diamonds.',
                    ],
                ],
                [
                    'question' => 'Are Black Diamonds Blood Diamonds?',
                    'answer'   => [
                        'Blood Diamonds are linked to blood, violence, assault, murder, and unethical behavior. Black diamonds are plentiful. The addition of minerals or treatment of natural diamonds to make them "grey" can produce black. Blood diamonds are either naturally dark or have been mined and processed to be dark.',
                        'Lab-grown diamonds may readily achieve the "black color effect". Due to their origin, black diamonds are neither blood nor non-blood. Blood Diamonds are like real diamonds. So natural diamonds are probably blood diamonds or immoral diamonds.  The less manual labour required to manufacture black diamonds in a lab, the less probable they are to be blood diamonds.',
                    ],
                ],
                [
                    'question' => 'What color is the rarest diamond?',
                    'answer'   => [
                        'The rarest color diamond is currently worth a whopping 8 million USD for 5.11 carats. Yes, this diamond color is so rare that only 20-30 pieces of this color exist. Can you guess the color of this rarest diamond? It is crimson red. Yes, this diamond gets its color from a rare phenomenon while forming its structure allowing light to pass through it. Few other rare colors occurring in natural diamonds are blue, pink, and yellow.',
                    ],
                ],
                [
                    'question' => 'How Natural diamonds are formed?',
                    'answer'   => [
                        'Diamonds are formed when high temperatures and pressures are applied to deep-sea carbon deposits (about 90 to 125 miles below the surface). The formation time of stones varies from days to years. Trace elements reacting during diamond creation produce colored diamonds. Also, it\'s hard to know, but geologists can determine a diamond\'s age by looking at the minerals it contains.',
                        'But most natural diamonds are millions, if not billions, of years old. It\'s fascinating that diamond growth isn\'t always linear. A change in temperature or pressure can cause a rough diamond to fail. The gemstone may have to wait hundreds or even millions of years for the correct conditions to grow.',
                    ],
                ],
                [
                    'question' => 'Are natural diamonds ethical?',
                    'answer'   => [
                        'The term "ethical diamond" was made up to help people find diamonds that were mined in a way that didn\'t hurt workers or the environment.',
                        'Ethical diamonds are those that come from mines with safe working conditions, fair remuneration, and eco-friendly procedures.',
                    ],
                ],
            ];
            ?>
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
                            <?php foreach ( $faq['answer'] as $paragraph ) : ?>
                                <p><?php echo esc_html( $paragraph ); ?></p>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="resource-card__toggle" aria-expanded="false">Read more</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

 




<!-- start instagram section -->
<div class="section mb-0 pb-0">
    <div class="container-fluid">
        <h2 class="text-center py_25 pt-0 mb-0 home_we_have_instagram_title">Follow us on Instagram</h2>
        <div class="instagram-header d-flex justify-content-center align-items-center gap-2 mb-4">
            <a href="https://www.instagram.com/loosegrown_diamond/"
                target="_blank"
                rel="noopener nofollow"
                class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                    alt="loosegrown_diamond Instagram profile"
                    class="rounded-circle"
                    width="40"
                    height="40"
                    loading="lazy">
                <span class="fw-medium text-decoration-underline text-dark">@loosegrown_diamond</span>
            </a>
        </div>
    </div>

    <!-- image grid -->
    <div class="row g-3 mx-0">
        <?php
        $insta_dir = get_template_directory_uri() . '/aj_custom/home-page/insta/';
        for ( $i = 1; $i <= 6; $i++ ) : ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="insta-item">
                    <img
                        src="<?php echo esc_url( $insta_dir . $i . '.jpeg' ); ?>"
                        class="img-fluid rounded"
                        alt="Loose Grown Diamond Instagram post <?php echo $i; ?>"
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

<?php get_footer(); ?>
