<?php

/**
 * Template Name: Melee Diamonds For Sale
 *
 * Product cards are looked up by product slug; their images live in
 * rrp-img/<slug>/ (run download-melee-images.ps1 once to fetch them).
 * Shared sections reused from the theme:
 *   single_product_buy_direct_us()      – "Buy Directly From Us"
 *   ls_render_faq_cards()               – FAQ cards (inc/ls-common-sections.php)
 *   single_product_what_customer_say()  – customer reviews
 */

get_header();

$img = get_template_directory_uri() . '/rrp-img/';
$sale_img = $img . 'melee-diamonds-for-sale/';

// slug => main image file inside rrp-img/<slug>/
$melee_products = [
    'melee-diamonds'                    => 'D1-1.png',
    'calibrated-diamonds'               => 'D1-1.png',
    'princess-shape-natural-diamonds'   => 'princess-cut-product-2.png',
    'marquise-shape-natural-diamonds'   => 'Maquesh-Diamond.png',
    'pear-shape-natural-diamonds'       => 'pear-shape.png',
    'baguette-shape-natural-diamond'    => 'baggutu-shape-diamond-.png',
    'emerald-cut-natural-diamonds'      => 'emerald-shape.png',
    'old-european-cut-natural-diamonds' => 'natural-old-cut-diamonds.jpg',
];

$why_rrp = [
    [ 'Our_-Sources.svg',     'Our Sources',      'Our diamonds are conflict and blood free.' ],
    [ 'Our_-Assortments.svg', 'Our Assortments',  'Quality ensured by proficient assorters.' ],
    [ 'Our_-Calibration.svg', 'Our Calibration',  'Fully calibrated diamonds, equal in size.' ],
    [ 'Our_-Packaging.svg',   'Our Packaging',    'Individually packed and labeled by size.' ],
    [ 'Express_-Delivery.svg','Express Delivery', 'Fast and insured delivery worldwide.' ],
    [ 'No_-Lab_Grown.svg',    'No Lab Grown',     'All diamonds tested, 100% natural and mined.' ],
];

$faqs = [
    [
        'question' => 'Are Melee Diamonds Gia Certified?',
        'answer'   => [ 'Yes, these diamonds are GIA certified. It is also officially stated by the GIA that each diamond weighing less than 0.2 carats will be a Melee diamond. Although, GIA does segregate between lab-grown and natural small diamonds.' ],
    ],
    [
        'question' => 'Are Melee Diamonds Worth Anything?',
        'answer'   => [ 'Tiny diamonds are priced based on the 4Cs that make up the identity of a diamond. Their small size does not make them cheaper than the actual diamonds, just altered based on the carats. While buying Melee diamonds for sale, one can get a considerable discount, but the quality shall be reassured.' ],
    ],
    [
        'question' => 'What Are Diamond Chips Called?',
        'answer'   => [ 'Diamond chips are exactly what the name suggests, and are formed out of chipped larger diamonds, while they are being faceted. These diamond chips are usually used in place of Melee diamonds in some settings, where uneven facets and cuts are permissible.' ],
    ],
    [
        'question' => 'Are Melee Diamonds Graded?',
        'answer'   => [ 'Yes, these tiny diamonds are graded based on the same levels, in which normal diamonds are graded, like Color, Cut and Clarity, while Carats are less than 0.2. Some of them even consider only tiny diamonds less than 0.15 carat. Color, cut and clarity are identified conventionally, through the process followed for usual size diamonds.' ],
    ],
    [
        'question' => 'Are Melee Diamonds a Good Investment?',
        'answer'   => [ 'Based on the carat size, small loose diamonds are cheaper as compared to larger diamonds. It also provides a scope of unique designed jewelry. Experts say that it helps to diversify the investment portfolio if one is looking towards that in the market. Melee diamonds for sale can be looked up while one is buying them, to avail of some discounts.' ],
    ],
    [
        'question' => 'How Do You Set Melee Diamonds?',
        'answer'   => [ 'Make sure that every melee diamond is placed between two metal channels and off of any lower support rings or bars. Make sure there is either a bar or a ring below each melee diamond. These join the channel walls, preserving their stability and the security of the diamonds.' ],
    ],
];
?>



<div class="melee-sale">

    <!-- hero -->
    <div class="section melee-sale__hero">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="melee-sale__title">Melee Diamonds For Sale</h1>
                    <p>Melee diamonds are small loose diamonds that have the same intrinsic properties as that of normal diamonds, but are just less than 0.2 carats in weight. And we at RRP Diamonds offer an assortment of Melee diamonds for sale.</p>
                    <p>These small wonders are available in two types of cuts: full and half cut. You should know that Melee diamonds are usually preferred to be used in coordination with larger diamonds, to emphasize the designs and patterns.</p>
                    <a class="melee-sale__btn" href="<?php echo esc_url( home_url( '/product/melee-diamonds/' ) ); ?>">Explore Melee Diamonds</a>
                </div>
                <div class="col-md-6">
                    <video class="melee-sale__video" autoplay loop muted playsinline>
                        <source src="<?php echo esc_url( $img . 'why-choose/loose-grown-diamond-banner-video.mp4' ); ?>" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>

    <!-- products (looked up by slug) -->
    <div class="section">
        <div class="container-fluid">
            <h2 class="melee-sale__heading">Check Our Stunning Collection of Melee Diamonds</h2>
            <div class="melee-sale__products">
                <?php
                foreach ( $melee_products as $slug => $file ) :
                    $post = get_page_by_path( $slug, OBJECT, 'product' );
                    if ( ! $post ) {
                        continue;
                    }
                    $product = wc_get_product( $post->ID );
                    if ( ! $product ) {
                        continue;
                    }
                    $local = get_template_directory() . '/rrp-img/' . $slug . '/' . $file;
                    $src   = file_exists( $local )
                        ? $img . $slug . '/' . $file
                        : wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
                    ?>
                    <div class="melee-sale__product">
                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="melee-sale__product-link">
                            <?php if ( $src ) : ?>
                                <img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" width="300" height="300" loading="lazy">
                            <?php endif; ?>
                            <h3><?php echo esc_html( $product->get_name() ); ?></h3>
                            <span class="melee-sale__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                        </a>
                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="melee-sale__btn melee-sale__btn--block" rel="nofollow">Add to Cart</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- message from our CEO -->
    <div class="section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="melee-sale__heading melee-sale__heading--left">A Message from our CEO</h2>
                    <p>RRP Diamonds is an Indian wholesaler that deals with clients around the world.</p>
                    <p>RRP Diamonds have been in the business since 1964. Mr. Ramesh Bhai Dobariya, who has a fantastic eye for diamonds, had the vision to develop a business that would manufacture the finest natural diamonds in the world and rank among the most well-known marketing organizations globally. RRP Diamond offers IGI &amp; GIA-certified natural diamonds.</p>
                    <p>RRP Diamond offers melee diamonds ranging in from 0.09mm to 4.00mm. Our craftsmen manufacture around 80,000 diamonds per month with passion and integrity.</p>
                    <p>When placing an order on our site, you can choose your preferred carat weight and diamond mm size from the drop-down menu.</p>
                    <p>RRP Diamond gives you the best deals in price, quality, and assortment over other brands. We are one of the only brands which offers the lowest price in the market for melee diamonds.</p>
                </div>
                <div class="col-md-4">
                    <img class="melee-sale__photo" src="<?php echo esc_url( $sale_img . 'rrp_diamond_founder.jpg' ); ?>" alt="Ramesh Bhai Dobariya, CEO of RRP Diamond" loading="lazy">
                </div>
            </div>
        </div>
    </div>

    <!-- book an appointment -->
    <div class="section melee-sale__band">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img class="melee-sale__photo" src="<?php echo esc_url( $sale_img . 'book_enggagement_ring.png' ); ?>" alt="Book an appointment" loading="lazy">
                </div>
                <div class="col-md-6">
                    <h2 class="melee-sale__heading melee-sale__heading--left">Book an In-store or Virtual Appointment</h2>
                    <p>Contact our expert gemologists at RRP Diamond to get better clarity of the diamond you seek. Set a virtual appointment, today!</p>
                    <a class="melee-sale__btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Book an Appointment</a>
                </div>
            </div>
        </div>
    </div>

    <!-- why choose a melee diamond -->
    <div class="section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2 class="melee-sale__heading melee-sale__heading--left">Why would you choose a Melee Diamond from RRP Diamonds?</h2>
                    <p>Melee diamonds are an ideal and appealing choice for jewelry pieces, as they make viewers aware of the maximum potential a jewelry can reach in terms of design.</p>
                    <p>A Melee diamond ideally finds its way to a ring design of pave, channel or halo, where the placements of these diamonds emphasize the main gem on the ring. Giving it an alluring look, that will make neck&rsquo;s turn.</p>
                    <p>At RRP Diamonds we offer melee diamonds of two categories, namely, single cut and full cut. The single cut has 17-18 facets only, while a full cut melee diamond has 57-58 facets. Where you would like to place these natural small loose diamonds is up to you.</p>
                    <p>These small loose diamonds are versatile gems, they can be used in watches, rings, earrings, cufflinks, pendants and many other diverse styles of jewelry and fashion.</p>
                    <a class="melee-sale__btn" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">Know About Us</a>
                </div>
                <div class="col-md-5">
                    <img class="melee-sale__photo" src="<?php echo esc_url( $sale_img . 'Melee-diamond-sorting.png' ); ?>" alt="Melee diamond sorting" loading="lazy">
                </div>
            </div>
        </div>
    </div>

    <!-- melee diamond wholesale -->
    <div class="section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img class="melee-sale__photo" src="<?php echo esc_url( $sale_img . 'melee-diamonds-wholesale.png' ); ?>" alt="Melee diamond wholesale" loading="lazy">
                </div>
                <div class="col-md-7">
                    <h2 class="melee-sale__heading melee-sale__heading--left">Melee diamond Wholesale</h2>
                    <p>As a melee diamond supplier, the merchant has to take care to segregate these diamonds efficiently. At RRP Diamonds we have been selling melee diamonds for decades now and have managed to gain a great proportion of satisfied customers.</p>
                    <p>Many suppliers sell melee diamond wholesale or retail. We however, sell to both businesses, as well as, customers. Being a melee diamond wholesale seller we segregate these tiny gems based on the cuts, color, and quality, hence making it easier for you to decide.</p>
                    <p>Reason jewelers/people prefer to buy melee diamonds wholesale from RRP might be because they require a larger quantity or because it is cheaper to buy in this manner.</p>
                    <a class="melee-sale__btn" href="<?php echo esc_url( home_url( '/product/melee-diamonds/' ) ); ?>">Buy Melee Diamond</a>
                </div>
            </div>
        </div>
    </div>

    <!-- why RRP diamond -->
    <div class="section melee-sale__band">
        <div class="container-fluid">
            <h2 class="melee-sale__heading">Why RRP Diamond?</h2>
            <div class="melee-sale__why">
                <?php foreach ( $why_rrp as $item ) : ?>
                    <div class="melee-sale__why-item">
                        <img src="<?php echo esc_url( $sale_img . 'icons/' . $item[0] ); ?>" alt="<?php echo esc_attr( $item[1] ); ?>" loading="lazy">
                        <h3><?php echo esc_html( $item[1] ); ?></h3>
                        <p><?php echo esc_html( $item[2] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php single_product_buy_direct_us(); ?>

<div class="section">
    <?php echo single_product_what_customer_say(); ?>
</div>
<div class="bg_light_gray">
    <div class="section">
    <!-- natural diamond FAQs section -->
    

    

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
</div>

<?php get_footer(); ?>
