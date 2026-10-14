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
<div class="banner_section home_banner_hero">
    <div class="bg_img" style="<?php  ?>">
        <div class="<?php  ?>">
            <?php
            
                $hero_d = $home_page['hero_banner_image_desktop'];
                $hero_m = $home_page['hero_banner_image_mobile'];
                $size_d = lgd_img_size( $hero_d );
                $size_m = lgd_img_size( $hero_m );
                ?>
                <div class="retail_banner">
                    <picture>
                        <source
                            media="(min-width: 992px)"
                            srcset="<?php echo esc_url( $hero_d ); ?>"
                            <?php if ( $size_d ) : ?>width="<?php echo $size_d[0]; ?>" height="<?php echo $size_d[1]; ?>"<?php endif; ?>>
                        <img
                            src="<?php echo esc_url( $hero_m ); ?>"
                            <?php if ( $size_m ) : ?>width="<?php echo $size_m[0]; ?>" height="<?php echo $size_m[1]; ?>"<?php endif; ?>
                            alt="Lab grown diamond engagement rings"
                            class="d-block mx-auto mt-0 img-fluid"
                            fetchpriority="high"
                            loading="eager">
                    </picture>
                </div>
                <?php
            
            ?>
            <div class="banner_text text-center ">
                <?php
                     
                ?>
                <div class="d-flex align-items-center justify-content-center flex-wrap gap-3">
                    <a href="<?php echo $home_page['hero_banner_button_link2']; ?>" class="common_btn"><?php echo $home_page['hero_banner_button_text2']; ?></a>
                    <a href="<?php echo $home_page['hero_banner_button_link1']; ?>" class="common_btn"><?php echo $home_page['hero_banner_button_text1']; ?></a>
                    
                </div>
            </div>
            <?php
            
            ?>
        </div>
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
        <h2 class="text-center mb_40 h2">SHOP ENGAGEMENT RINGS</h2>
        <div class="row">
            <div class="col-6">
                <a href="<?php echo esc_url( $text_change['inventory_url'] ); ?>" class="engagement_rings text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/home-page/shop-by-engagement/engagement_rings_1.webp"
                        alt="Start with a lab grown diamond"
                        width="555" height="400"
                        class="img-fluid mb-3 mb-md-4"
                        loading="lazy" decoding="async">
                    <p class="fw-600 text-uppercase mb-3 mb-md-0">Start with Diamonds</p>
                </a>
            </div>
            <div class="col-6">
                <a href="<?php echo esc_url( get_site_url() . '/build-a-ring/' ); ?>" class="engagement_rings text-decoration-none">
                    <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/home-page/shop-by-engagement/engagement_rings_2.webp"
                        alt="Start with an engagement ring setting"
                        width="555" height="400"
                        class="img-fluid mb-3 mb-md-4"
                        loading="lazy" decoding="async">
                    <p class="fw-600 text-uppercase mb-3 mb-md-0">Start with Setting</p>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- start customisable shapes section -->
<?php
start_engagement_rings_by_shape();
?>

<!-- start fine jewellery section -->
<div class="section">
    <div class="container-fluid">
        <h2 class="text-center text-uppercase lc mb_40 h2">Fine Jewelry</h2>
        <div class="row row_gap_60">
            <?php
            $categories = [
                [
                    'url'  => '/lab-diamond-wedding-bands/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/wedding-rings.webp',
                    'alt'  => 'wedding_rings',
                    'name' => 'Wedding Rings'
                ],
                [
                    'url'  => '/lab-diamond-necklaces/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/necklaces.webp',
                    'alt'  => 'necklaces',
                    'name' => 'Necklaces'
                ],
                [
                    'url'  => '/lab-diamond-earrings/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/earrings.webp',
                    'alt'  => 'earrings',
                    'name' => 'Earrings'
                ],
                [
                    'url'  =>  '/lab-diamond-bracelets/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/bracelets.webp',
                    'alt'  => 'bracelets',
                    'name' => 'Bracelets'
                ],
                [
                    'url'  => '/lab-diamond-pendants/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/pendants.webp',
                    'alt'  => 'pendants',
                    'name' => 'Pendants'
                ]
            ];
            ?>

            <?php foreach ($categories as $category) : ?>
                <div class="col-md-4 col-6">
                    <a href="<?php echo esc_url($category['url']); ?>" class="text-decoration-none">
                        <div class="fine_jewellery">
                            <img src="<?php echo esc_url($category['img']); ?>"
                                alt="<?php echo esc_attr($category['alt']); ?>"
                                loading="lazy">
                            <p class="mb-0"><?php echo esc_html($category['name']); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
            <div class="col-md-4 col-6">
                <a href="javascript:void(0);" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#customSettingModal">
                    <div class="fine_jewellery">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/custom-fine-jewellery.webp'); ?>"
                            alt="custom_jewellery"
                            loading="lazy">
                        <p class="mb-0">Custom Fine Jewelry</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- start engagement rings section -->

<?php
start_engagement_rings();
?>

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