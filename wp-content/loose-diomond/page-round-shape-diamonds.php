<?php

/**
 * Template Name: Round Shape Diamonds
 */

get_header();
global $text_change;
?>


<!-- start banner section -->
<div class="banner_section">
    <div class="page_heading mb_30">
        <div class="container-fluid">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Round Shape Diamonds</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="engagement_rings_banner text-center mb_50">
            <h2 class="fw-600">Round Brilliant Lab Created Diamonds</h2>
        </div>
    </div>
</div>

<!-- start diamond shape section -->
<div class="container-fluid mb-4">
    <div class="row row-gap-4">
        <div class="col-md-6">
            <div class="fancy_diamond_box"
                style="background-image: url('<?php echo get_site_url(); ?>/wp-content/uploads/2021/07/gia-igi-certified-diamond.png?id=79615'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <h2 class="fw-700 mb_40">Solitaire <br>
                    Diamonds</h2>
                <a href="<?php echo $text_change['inventory_url']; ?>" class="btn">
                    Shop Now
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="fancy_diamond_box"
                style="background-image: url('<?php echo get_site_url(); ?>/wp-content/uploads/2019/03/Melee-Diamonds.jpg?id=23839'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <h2 class="fw-700 mb_40">Melee Diamond <br>
                    (Packet)</h2>
                <a href="<?php echo get_site_url(); ?>/product/lab-created-melee-diamond/" class="btn">
                    Shop Now
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="fancy_diamond_box" style="background-image: url('<?php echo get_site_url(); ?>/wp-content/uploads/2019/03/RD.jpg?id=23851'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                <h2 class="fw-700 mb_40">Calibrated <br>
                    Diamonds</h2>
                <a href="<?php echo get_site_url(); ?>/product/round-shape-calibrated-diamonds/" class="btn">
                    Shop Now
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="rrp_desc">
            <h2>Round Brilliant Shape Diamonds:</h2>
            <p>Due to his shape, it’s know as round shape diamonds. Round shape diamonds are the most popular and most used shape in the world. It is also the most expensive.</p>
            <h2>IGI Certified Solitaire Diamonds:</h2>
            <p>Buy online IGI Certified lab grown diamonds at wholesale price from us we have a large range of certified lab grown diamonds.</p>
            <h2>HPHT Melee Diamonds:</h2>
            <p>White diamonds&nbsp;are in fact lacking in color – they are ‘colorless’, like water. Buy white diamonds from us at wholesale price as you’re buying from the manufacturer directly.</p>
            <h2>CVD Melee Diamonds:</h2>
        </div>
        <p>CVD diamonds also refer to Synthetic diamonds. We are a global manufacturer and supplier for CVD diamonds along with Synthetic diamonds jewelry such as CVD diamonds engagement ring, CVD diamonds wedding ring, CVD diamonds necklace and Synthetic diamonds earrings.</p>
    </div>
</div>

<?php
get_footer();
