<?php

/**
 * Template Name: Why Choose Us
 */

get_header();
?>

<!-- why choose us section -->
<div class="why_choose_us">
    <div class="banner_section">
        <div class="py_60">
            <div class="container-fluid">
                <h1 class="text-center main_title mb_30">Why Choose Us</h1>
                <div class="why_quote">
                    <p class="mb-0">
                        Save over 40% compared to traditional retail prices on lab-grown diamonds. By eliminating inventory, showroom, and overhead costs, we ship diamonds directly from the manufacturer to your doorstep.
                    </p>
                </div>
                <video
                    class="img-fluid"
                    autoplay="autoplay"
                    loop="loop"
                    muted="muted">
                    <source
                        src="<?php echo get_site_url(); ?>/wp-content/uploads/ls_custom/loose-grown-diamond-banner-video.mp4"
                        type="video/mp4" />
                </video>
                <div class="row row_gap_30">
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/certified.svg" alt="certified" />
                            <p class="box_why_title">2,500,000+ GIA, IGI & GCAL Certified Lab-Grown Diamonds</p>
                            <p class="mb-0">
                                Choose from over 2.5 million lab-grown diamonds, sourced directly from the manufacturers who cut and polish each stone. This direct-to-consumer approach allows us to offer exceptional quality at highly competitive prices.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/handmade.svg" alt="handmade" />
                            <p class="box_why_title">
                                Fancy-Cut Diamonds, Handcrafted to Order
                            </p>
                            <p class="mb-0">
                                Every fancy-cut lab-grown diamond is handcrafted to order by our master craftsmen. Since no two diamonds are alike, we create each piece specifically for you, including custom-shaped lab-grown diamonds.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/right-hands.svg" alt="right-hands" />
                            <p class="box_why_title">You're in the Right Hands</p>
                            <p class="mb-0">
                                Our founder is a third-generation diamond manufacturer, and with over 42 years of combined industry experience, our team has the expertise to make your diamond purchase truly special.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/happy-customers.svg" alt="happy-customers" />
                            <p class="box_why_title">Thousands of Happy Customers</p>
                            <p class="mb-0">
                                Don't just take our word for it. Thousands of customers have trusted Loose Grown Diamond for their special moments. Read their reviews and discover why they love our products and customer service.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/guarantee.svg" alt="guarantee" />
                            <p class="box_why_title">Manufacturer Warranty</p>
                            <p class="mb-0">
                                Every purchase is backed by our <?php echo RETURN_DAYS;?> day manufacturer warranty, covering eligible manufacturing defects for your peace of mind.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/worldwide_delivery.svg" alt="worldwide_delivery" />
                            <p class="box_why_title">Worldwide Delivery</p>
                            <p class="mb-0">
                                We ship worldwide. Wherever you are, your order will be fully insured during transit and delivered safely to your doorstep.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
