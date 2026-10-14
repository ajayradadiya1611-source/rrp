<?php

/**
 * Template Name: Lab Created Diamond Ring
 */

get_header();
?>

<!-- start banner section -->
<div class="banner_section">
    <div class="ring_bg_img">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5">
                    <h1>Looking for a Ring at
                        <span>lowest rate</span> in the
                        world?
                    </h1>
                    <h2>You're at the right Place.</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- start featured logo section -->
<div class="py_80">
    <div class="container-fluid">
        <h3 class="text_black_secondary text-uppercase text-center mb_50">WE'VE BEEN FEATURED IN</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/deccans.png" alt="deccans">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/checkersaga.png" alt="checkersaga">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/asian.png" alt="asian">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- start video section -->
<div>
    <video autoplay loop muted class="w-100">
        <source src="<?php echo get_template_directory_uri(); ?>/assets/image/loose_grown_diamond_banner_video.mp4" type="video/mp4">
    </video>
</div>

<!-- start form section -->
<div class="light_gray_background">
    <div class="container-fluid">
        <div class="text-center">
            <h3 class="text_black_secondary text-uppercase mb_15">ASK FOR A FREE QUOTE FOR YOUR RING</h3>
            <p class="text_black_secondary">Fill in the form below and we will give you a quote, It's Free</p>
        </div>
        <form id="ls-ask-quote-form" enctype="multipart/form-data">
            <div class="row row_gap_18">
                <div class="col-md-6">
                    <input type="text" name="firstName" class="form-control" placeholder="First name*">
                </div>
                <div class="col-md-6">
                    <input type="text" name="lastName" class="form-control" placeholder="Last name*">
                </div>
                <div class="col-md-6">
                    <input type="tel" name="phoneNumber" class="form-control" placeholder="Mobile Number*">
                </div>
                <div class="col-md-6">
                    <input type="email" name="email" class="form-control" placeholder="Your email*">
                </div>
                <div class="col-md-12">
                    <input type="text" name="refSite" class="form-control" placeholder="Reference URL ">
                </div>
                <div class="col-md-12">
                    <input type="file" name="files[]" class="form-control ls_fileupld" placeholder="Your email" multiple>
                    <h5 class="note-text fw-normal mt-2">Note: Upload Reference Images/URL (Highly Recommended). Inspiration photos help us match your design accurately.</h5>
                    <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                </div>
                <div class="col-md-12">
                    <textarea name="message" id="message" class="form-control" rows="7" placeholder="To begin your custom design, please share: 
    • Ring size 
    • Metal (14K / 18K, White / Yellow / Rose Gold or Platinum) 
    • Center stone size or SKU 
    • Reference link or upload photos above 
    
(If unsure, just tell us — our specialist will guide you.)"></textarea>
                    <h5 class="note-text fw-normal mt-2">Before submitting: Please ensure you’ve included ring size, metal, and center stone details. It helps us create your CAD &amp; 3D faster.</h5>
                </div>
                <div class="col-md-12">
                    <div class="text-end">
                        <button class="btn ask_quote_btn" type="submit">Submit</button>
                    </div>
                </div>
            </div>
        </form>
        <p class="mt-4 border border-danger text-danger p-2" id="ls-ask-quote-error" style="display: none;">Error</p>
        <p class="mt-4 border border-success text-success p-2" id="ls-ask-quote-success" style="display: none;">Success</p>
    </div>
</div>

<!-- start jewelry section -->
<div class="py_80">
    <div class="container-fluid-lg">
        <div class="text-center px-3 mb_40">
            <h3 class="text_black_secondary mb_15">JEWELRY MADE BY US</h3>
            <p class="text_black_secondary fw-500">Here are some of the jewelry pieces made by us for our
                international customers</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6 px-0 px-md-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/review1.jpg" alt="review1" class="review_img">
            </div>
            <div class="col-lg-3 col-6 px-0 px-md-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/review2.jpg" alt="review2" class="review_img">
            </div>
            <div class="col-lg-3 col-6 px-0 px-md-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/review3.jpg" alt="review3" class="review_img">
            </div>
            <div class="col-lg-3 col-6 px-0 px-md-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/review4.jpg" alt="review4" class="review_img">
            </div>
        </div>
    </div>
</div>

<!-- start see what our customer are saying -->
<div class="light_gray_background pb-0">

    <?php
        echo single_product_what_customer_say();
    ?>
</div>

<div class="light_gray_background">
    <div class="container-fluid">
        <h3 class="text-uppercase text_black_secondary text-center mb_40">OUR PRICES ARE LOWER THAN THE REGULAR
            SELLERS BECAUSE WE DIRECTLY GIVE YOU DIAMONDS FROM OUR FACTORY. NO MIDDLEMAN.</h3>
        <div class="d-flex align-items-center justify-content-center flex-wrap">
            <div class="ls_cntntbox_1 etheme_box_m text-center mb-4 mb-md-0">
                <div>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg" alt="loosegrowndiamond_logo">
                    <h5 class="text_black_secondary fw-500 my-2">Loose Grown Diamond </h5>
                    <span class="font_size_12">(We as a Manufacturer) </span>
                </div>
            </div>
            <div class="ls_cntntbox_2 etheme_box_m etheme_white_box text-center d-none d-md-flex">
                <div>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/arrow_1.svg" alt="arrow_1">
                </div>
            </div>
            <div class="ls_cntntbox_3 etheme_box_m text-center">
                <div>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/user.svg" alt="user">
                    <h4 class="text_black_secondary fw-600 my-2">Our Buyers</h4>
                    <span class="font_size_12">Jewelry Owners, Diamond Traders</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py_80">
    <div class="container-fluid">
        <h3 class="text-uppercase text_black_secondary mb_35 text-center ">How to Order</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="order_section_box text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/order_icon.svg" alt="order_icon" class="mb_35">
                    <h5 class="mb_10">Ask for Free Quote</h5>
                    <p class="mb-4">Ask us for a free quote with your desired jewelry design and we will send it to you.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order_section_box text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/call_icon.svg" alt="call_icon" class="mb_35">
                    <h5 class="mb_10">Make a Payment</h5>
                    <p class="mb-4">Once you have finalized the design and agree with the price, proceed to make the payment.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order_section_box text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/Ship_icon.svg" alt="ship_icon" class="mb_35">
                    <h5 class="mb_10">Relax while we Ship</h5>
                    <p class="mb-4">Once the payment has been made, relax and track your ring.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
