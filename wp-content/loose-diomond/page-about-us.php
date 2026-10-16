<?php

/**
 * Template Name: About Us
 */

get_header();
?>

<!-- about us section -->
<div class="about_us">
    <div class="banner_section">
        <div class="py_60">
            <div class="container-fluid">
                <div class="text-center">
                    <h1 class="h2 text_black_secondary fw-600 mb-0">Our Story</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row flex-lg-row-reverse row_gap_30">
            <div class="col-lg-6">
                <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/manufactring.jpg"
                    alt="manufactring"
                    class="d-block mx-auto ms-lg-auto img-fluid main_img" />
            </div>
            <div class="col-lg-6">
                <h2 class="main_title">
                    42+ Years of Experience in the Diamond Industry
                </h2>
                <p class="text_p mb_40">
                    Established in 1984, RRP Diamond has over 42 years of experience in the diamond industry. For more than four decades, we've been committed to creating a more sustainable future. We believe beautiful diamonds shouldn't come at the expense of our planet, which is why we proudly manufacture laboratory-grown diamonds.
                </p>
                <p class="text_p mb_40">
                    RRP Diamond is a trusted manufacturer and supplier of 100% GIA, IGI, and GCAL-certified lab-grown diamonds, as well as custom engagement rings, wedding bands, and fine jewelry.
                </p>
                <p class="text_p mb_35">
                    Our collection includes loose diamonds, melee diamonds, and fancy-shaped lab-grown diamonds, along with handcrafted jewelry made to order. Every piece is conflict-free, ethically crafted, environmentally responsible, and fully customizable to your preferred shape, size, metal, color, clarity, cut, and carat weight.
                </p>
            </div>
        </div>
    <div class="container-fluid pt-5 mb_135">
        <h2 class="mb_70 text-center">WHY BUY LAB-CREATED DIAMONDS FROM US?</h2>
        <div class="row row_gap_30">
            <div class="col-lg-4">
                <div class="about_box">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/business.svg" alt="business" />
                    <h3>Own Manufacturing Unit</h3>
                    <p class="mb-0">
                        We have our own diamond manufacturing facility in India, supplying lab-grown diamonds to wholesale and retail customers worldwide.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about_box">
                    <img
                        src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/attach_money.svg"
                        alt="attach_money" />
                    <h3>Lowest Prices</h3>
                    <p class="mb-0">
                        Because we manufacture our own diamonds, we can offer wholesale prices without the added cost of middlemen.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about_box">
                    <img
                        src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/secure-shopping.svg"
                        alt="secure-shopping" />
                    <h3>Secure Shopping</h3>
                    <p class="mb-0">
                        Our website is protected with SSL encryption, providing one of the most advanced levels of security for safe and secure online transactions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
