<?php
/**
 * Template Name: Promo Code
 */

get_header();
?>

<!-- promo code section start -->
<div class="promo_code_section mx-auto">
    <div class="banner_section">
        <div class="py_60">
            <div class="container first_section mx-auto px-0">
                <div class="">
                    <div class="text-center mb_50">
                        <h1 class="fw-400  h2">Current Offers and Promo Code</h1>
                        <p class="mb-0">Looking for the latest Loose Grown Diamond discounts, promotions, coupon codes, and exclusive offers? You've come to the right place! We believe in complete transparency and want to help you find the best deal on your purchase.</p>
                    </div>
                    <div class="deal_box">
                        <div class="text-center">
                            <h2 class="mb_20 fw-400">Save on Your Diamond, Ring & Jewelry</h2>
                            <p>Save 2% on your lab-grown diamond, engagement ring, or jewelry purchase of $1,500 or more with promo code LOVE20 or LGD20.</p>
                            <p>Offer cannot be combined with any other promotions. Valid through December 31, 2026.</p>
                            <div class="crpto_ads ">
                                <span id="promocode1" class="code">LOVE20</span>
                                <span class="copy_code" onclick="copyToClipboard('#promocode1')">Copy code</span>
                            </div>
                            <div class="crpto_ads mx-auto my-0">
                                <span id="promocode2" class="code">LGD20</span>
                                <span class="copy_code" onclick="copyToClipboard('#promocode2')">Copy code</span>
                            </div>
                        </div>
                    </div>
                    <div class="flower_image p-0">
                        <img src="<?php echo get_site_url();?>/wp-content/uploads/2023/08/Loose-Grown-Diamond-Promo-Code.png" alt="image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- promo code section end -->

<?php
get_footer();
