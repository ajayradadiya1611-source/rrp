<?php

/**
 * Template Name: Home Page
 */

get_header();
global $text_change;
global $oc_common;
$home_page = $text_change['home_page'];
$common_page = $text_change['common_page'];
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
</div>

<!-- start shop engagement rings section -->
<?php
$er_dir = get_template_directory() . '/assets/image/';
$er_uri = get_template_directory_uri() . '/assets/image/';

$er_items = [
    [
        'file' => 'engagement_rings_1.webp',
        'url'  => $text_change['inventory_url'],
        'alt'  => 'Start with a lab grown diamond',
        'text' => 'Start with Diamonds',
    ],
    [
        'file' => 'engagement_rings_2.webp',
        'url'  => get_site_url() . '/build-a-ring/',
        'alt'  => 'Start with an engagement ring setting',
        'text' => 'Start with Setting',
    ],
];
?>
<!-- start shop engagement rings section -->
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
<?php
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
<section class="shape-circles shape-circles--melee">
    <div class="container">
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
</section>

<!-- start info cards section -->
<div class="section info-cards-section">
    <div class="info-cards container">
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

<!-- start black diamonds by shape section -->
<section class="shape-circles shape-circles--black">
    <div class="container">
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
</section>

<!-- start diamond resources -->
<div class="bg_light_gray">
   <?php
    


    ?>
        <div class="section">
            <!-- diamond buying resources section -->
            <div class="container-fluid">
                <h2 class="h1 font_literata text_black_secondary text-center mb-0">Diamond Buying Resources</h2>
                <div class="py_60">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm diamond_resources_card">
                                <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Why-Choose-Lab-Grown-Diamonds1.jpg"
                                    class="card-img-top img-fluid"
                                    alt="Why Choose Lab-Created Diamonds"
                                    loading="lazy">
                                <div class="card-body">
                                    <h3 class="h5">Why Choose Lab-Created Diamonds?</h3>
                                    <p>
                                        Lab-grown diamonds are environmentally friendly and cost-efficient.
                                        They undergo the same processing and grading standards as mined diamonds,
                                        based on the 4Cs of diamond certification.
                                    </p>
                                    <a href="<?php echo get_site_url(); ?>/real-facts-about-lab-created-diamonds/" class="text-dark fw-medium">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm diamond_resources_card">
                                <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/What-are-Lab-Grwon-Diamonds-555x278.jpg"
                                    class="card-img-top img-fluid"
                                    alt="What are Lab Grown Diamonds"
                                    loading="lazy">
                                <div class="card-body">
                                    <h3 class="h5">What are Lab Grown Diamonds?</h3>
                                    <p>
                                        Lab-grown diamonds are created in a lab using two methods:
                                        High-Pressure High-Temperature (HPHT) and Chemical Vapor Deposition (CVD),
                                        unlike mined diamonds extracted from the earth.
                                    </p>
                                    <a href="<?php echo get_site_url(); ?>/different-types-of-lab-grown-diamonds/" class="text-dark fw-medium">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 align-items-stretch">
        
        <div class="col-md-6 d-flex p-0">
            <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Lab-Grown-Diamond-Grading-and-Report1-555x278.jpg"
                class="img-fluid w-100 h-100 object-fit-cover"
                alt="Lab Created Diamonds Grading & Certification"
                loading="lazy">
        </div>

        <div class="col-md-6 d-flex p-0">
            <div class="card border-0 shadow-sm w-100 p-4 d-flex flex-column justify-content-center diamond_resources_card">
                <h3 class="h5 mb-3">
                    Lab Created Diamonds Grading &amp; Certification
                </h3>

                <p class="mb-3">
                    We send all of our lab-grown diamonds for inspection to renowned
                    institutions such as GIA, IGI, and GCAL.
                </p>

                <a href="<?php echo get_site_url(); ?>/how-lab-grown-diamonds-are-certification-and-grading/"
                    class="text-dark fw-medium">
                    Read More
                </a>
            </div>
        </div>

    </div>
                </div>
            </div>
        </div>
        <?php 
     
        ?>

    <!-- start lab diamond section -->
    <div class="bg_lab_diamond">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <img
              src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/home-page/mobile-Lab-Grown-Color-Diamond-1-1.webp' ); ?>"
              class="d-md-none img-fluid"
              alt="Lab Grown Color Diamond"
              loading="lazy"
              decoding="async">
            <div class="lab_diamond_text">
              <h2 class="text_white_primary font_literata mb_10 h1">Shop Colored Lab Diamonds</h2>
              <p class="text_white_primary fw-400 mb_40">Select your perfect fancy color lab created diamond from thousands of <br class="d-none d-lg-block">ethically sourced diamonds.</p>
              <a href="<?php echo esc_url( home_url( '/lab-created-colored-diamonds/' ) ); ?>" class="common_btn white_btn">Shop Colored Lab Grown Diamonds</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- start customers say section -->
    <div class="section">
        <?php
        echo single_product_what_customer_say();
        ?>
    </div>
</div>

<!-- start featured section -->
<div class="pt-5">
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

<!-- start instagram section -->
<div class="section  mb-0  pb-0">
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

                <span class="fw-medium text-decoration-underline text-dark">
                    @loosegrown_diamond
                </span>
            </a>
        </div>
        <!-- Image Grid -->
        
    </div>
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

<?php
get_footer();