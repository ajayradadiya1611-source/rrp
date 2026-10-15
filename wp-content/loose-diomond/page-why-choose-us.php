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
                        Over 40% savings on retail prices of <strong>Natural Diamond</strong>! No inventory costs, store costs, and overhead costs. Diamonds shipped directly from the manufacturer to your doorstep.
                    </p>
                </div>
                <video
                    class="img-fluid"
                    autoplay="autoplay"
                    loop="loop"
                    muted="muted">
                    <source
                        src="<?php echo get_template_directory_uri(); ?>/rrp-img/why-choose/loose-grown-diamond-banner-video.mp4"
                        type="video/mp4" />
                </video>
                <div class="row row_gap_30">
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/certified.svg" alt="certified" />
                            <p class="box_why_title">40,000+ GIA/IGI Certified Natural Diamonds</p>
                            <p class="mb-0">
                                Diamonds directly from those that cut and polish the stones – Direct to the factory, we’re uniquely placed to offer you the world’s most luxurious stones at a price that’s affordable.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/handmade.svg" alt="handmade" />
                            <p class="box_why_title">Fancy Cut Diamonds are handmade to order by our master craftsmen</p>
                            <p class="mb-0">
                                We strongly believe that as no two diamonds are alike, neither should the jewelry and we make each piece to order which in turn keeps our costs down and therefore the price you pay. we will make the custom shape Natural diamonds just for you.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/right-hands.svg" alt="right-hands" />
                            <p class="box_why_title">You’re in the right hands</p>
                            <p class="mb-0">
                                With a company founder that is a third-generation diamond manufacturer and a combined experience within our workshops of over 43+ years, we know what it takes to make your unique purchase extra special.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/happy-customers.svg" alt="happy-customers" />
                            <p class="box_why_title">Thousands of happy customers</p>
                            <p class="mb-0">
                                Don’t just take our word for it, we have made so many dreams come true with our Natural diamonds and you can read online from those that have experienced our products and customer service.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/guarantee.svg" alt="guarantee" />
                            <p class="box_why_title">Manufacturer Warranty</p>
                            <p class="mb-0">
                                All our items are covered against our manufacturer warranty such as a production warranty that addresses any manufacturing issues within <?php echo RETURN_DAYS;?> days.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/worldwide_delivery.svg" alt="worldwide_delivery" />
                            <p class="box_why_title">Worldwide Delivery</p>
                            <p class="mb-0">
                                We ship Worldwide. Wherever you are, we will dispatch your item to you fully insured by us until the item has reached your address.
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
