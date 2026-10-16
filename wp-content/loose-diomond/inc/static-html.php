<?php

function single_product_faq() {

    $faqs = get_product_category_faqs();

    $product_id = get_the_ID();
    $terms = get_the_terms($product_id, 'product_cat');

    if (!$terms || is_wp_error($terms)) {
        return;
    }

    $all_faqs = array();

    foreach ($terms as $term) {
        $slug = $term->slug;

        if (!empty($faqs[$slug])) {
            $all_faqs = array_merge($all_faqs, $faqs[$slug]);
        }
    }

    if (empty($all_faqs)) {
        return;
    }
    ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
            <div class="faq_list">
                <div class="faq_accordion" id="faqAccordion">

                    <?php 
                
                    foreach ($all_faqs as $index => $faq) : ?>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index != 0 ? 'collapsed' : ''; ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $index; ?>"
                                        aria-expanded="<?php echo $index == 0 ? 'false' : 'false'; ?>"
                                        aria-controls="collapse<?php echo $index; ?>">
                                    <span><?php echo esc_html($faq['question']); ?></span>

                <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true ) : ?>
                    <span class="faq-icon ">
                        <i class="fa-brands fa-youtube"></i>
                    </span>
                <?php endif; ?>
                                </button>
                            </h3>

                            <div id="collapse<?php echo $index; ?>"
                                class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordion">

                                <div class="accordion-body">

                                    <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true && $faq['youtube_link']!='') : ?>

                                        <div class="ratio ratio-16x9">
                                            <iframe 
                                                src="<?php echo esc_url(str_replace('youtu.be/', 'www.youtube.com/embed/', $faq['youtube_link'])); ?>" 
                                                title="YouTube video"
                                                allowfullscreen>
                                            </iframe>
                                        </div>

                                    <?php else : ?>

                                        <?php echo $faq['answer']; ?>

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
    <?php
}
function testimonial_aj() {
    ?>
    <!-- ── Testimonial Slider + Brand Logos Section ── -->
<div class="testimonial-section mt-5">
  <div class="container">

    <!-- Slider -->
    <div class="testimonial-slider-wrap">

      <!-- Prev Arrow -->
      <button class="testi-arrow testi-prev" aria-label="Previous">&#8249;</button>

      <!-- Slides -->
      <div class="testi-slides">

        <div class="testi-slide active">
          <blockquote class="testi-quote">
            &ldquo;LGD Wholesale has been a great solution for our stores.&rdquo;
          </blockquote>
          <p class="testi-author">&mdash; Kristy Nasifoglu, Director at Green</p>
        </div>

        <div class="testi-slide">
          <blockquote class="testi-quote">
            &ldquo;The quality and pricing are unmatched in the industry.&rdquo;
          </blockquote>
          <p class="testi-author">&mdash; James Carter, Owner at JC Fine Jewelry</p>
        </div>

        <div class="testi-slide">
          <blockquote class="testi-quote">
            &ldquo;Fast shipping and excellent customer support every time.&rdquo;
          </blockquote>
          <p class="testi-author">&mdash; Sarah Mitchell, Buyer at Carats4Less</p>
        </div>

      </div>
      <!-- /testi-slides -->

      <!-- Next Arrow -->
      <button class="testi-arrow testi-next" aria-label="Next">&#8250;</button>

    </div>
    <!-- /testimonial-slider-wrap -->

    <!-- Brand Logos -->
    <div class="brand-logos-row">
      <div class="brand-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/product-brand/brand-huisman.png" alt="HUISMAN" />
      </div>
      <div class="brand-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/product-brand/brand-carats4less.png" alt="Carats4Less" />
      </div>
      <div class="brand-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/product-brand/brand-jc-fine-jewelry.png" alt="JC Fine Jewelry" />
      </div>
      <div class="brand-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/product-brand/brand-lumy.png" alt="LUMÝ" />
      </div>
      <div class="brand-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/product-brand/brand-maia-lyra.png" alt="MAIA LYRA" />
      </div>
    </div>

  </div>
</div>
<script>
(function(){
  var slides = document.querySelectorAll('.testi-slide');
  var current = 0;

  function showSlide(n) {
    slides[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
  }

  document.querySelector('.testi-prev').addEventListener('click', function(){
    showSlide(current - 1);
  });
  document.querySelector('.testi-next').addEventListener('click', function(){
    showSlide(current + 1);
  });
})();
</script>

    <?php
}
function single_diomond_faq() {
    
    $all_faqs = array(
        array(
            'question'    => 'What is a lab grown diamond?',
            'answer'      => '<p>Lab grown diamonds are the diamonds created in a lab under controlled conditions with advanced technology replicating the formation process of natural diamonds.</p>',
            'isyoutube'   => false,
            'youtube_link'=> '',
        ),
        array(
            'question'    => 'Are lab grown diamonds real?',
            'answer'      => '<p>Yes, Lab grown diamonds are real diamonds with identical features like physical, chemical, and optical appearance to mined diamonds.</p>',
            'isyoutube'   => false,
            'youtube_link'=> '',
        ),
        array(
            'question'    => 'How are lab grown diamonds made?',
            'answer'      => '<p>Lab diamonds are made using HPHT (High Pressure High Temperature) and CVD (Chemical Vapor Deposition), helping carbon crystallisation into the formation of diamond.</p>',
            'isyoutube'   => false,
        ),
    );
    ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
            <div class="faq_list">
                <div class="faq_accordion" id="faqAccordion">

                    <?php 
                
                    foreach ($all_faqs as $index => $faq) : ?>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index != 0 ? 'collapsed' : ''; ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $index; ?>"
                                        aria-expanded="<?php echo $index == 0 ? 'false' : 'false'; ?>"
                                        aria-controls="collapse<?php echo $index; ?>">
                                    <span><?php echo esc_html($faq['question']); ?></span>

                <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true ) : ?>
                    <span class="faq-icon ">
                        <i class="fa-brands fa-youtube"></i>
                    </span>
                <?php endif; ?>
                                </button>
                            </h3>

                            <div id="collapse<?php echo $index; ?>"
                                class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordion">

                                <div class="accordion-body">

                                    <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true && $faq['youtube_link']!='') : ?>

                                        <div class="ratio ratio-16x9">
                                            <iframe 
                                                src="<?php echo esc_url(str_replace('youtu.be/', 'www.youtube.com/embed/', $faq['youtube_link'])); ?>" 
                                                title="YouTube video"
                                                allowfullscreen>
                                            </iframe>
                                        </div>

                                    <?php else : ?>

                                        <?php echo $faq['answer']; ?>

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
    <?php
}
function single_product_what_customer_say(){
    global $text_change;
    $common_page = $text_change['common_page'];
    ?>
    <!-- see what our customers are saying -->
    
        <div class="container-fluid">
            <div class="text-center py_60 pt-0">
                <h2 class="text_black_secondary h1 fw-normal font_literata mb-3 text-center">What our customers say</h2>
                <div class="text_black_secondary fw-400 mb-0 h5">We've served <?php echo $common_page['trustpilot_total_rating']?>+ customers worldwide and counting
                </div>
            </div>
            <div class="row justify-content-center">
                <?php
                $reviews = [
                    [
                        'title' => 'Good stock at good prices',
                        'description' => "I purchased a sample amount of rose cut diamonds for my business. The parcel turned up safely and well wrapped. The quality of stones is considerably better than the same price stones in the UK, will be using again. Also excellent communication.",
                        'image' => get_template_directory_uri() . '/assets/image/icon/review_client_img_1.svg',
                        'name' => 'David Price',
                        'location' => 'United Kingdom',
                    ],
                    [
                        'title' => 'Gorgeous diamonds',
                        'description' => "Gorgeous diamonds and the delivery was super fast will definitely buy more in the future.",
                        'image' => get_template_directory_uri() . '/assets/image/icon/review_client_img_2.svg',
                        'name' => 'Marie Faulcon',
                        'location' => 'United States',
                    ],
                    [
                        'title' => 'Great communication fantastic service…',
                        'description' => "Great communication fantastic service would highly recommend to anyone I will be using them again and you should too I have been in the diamond trade for 30 years and times have changed this company understands what you want and delivers on time I am a very satisfied customer",
                        'image' => get_template_directory_uri() . '/assets/image/icon/review_client_img_3.svg',
                        'name' => 'Dawn Greenfield',
                        'location' => 'United Kingdom',
                    ],
                ];

                                ?>
                                <?php foreach ($reviews as $review) : ?>
                    <div class="col-lg-4 col-sm-6">
                        <div class="review_box bg-white mb-4 mb-lg-0">
                            <h3 class="text-light-blue fw-600 mb-0">
                                <?php echo esc_html($review['title']); ?>
                            </h3>

                            <p>
                                <?php echo esc_html($review['description']); ?>
                            </p>

                            <div class="review_client_img_box d-flex align-items-center">
                                <img 
                                    src="<?php echo esc_url($review['image']); ?>" 
                                    alt="<?php echo esc_attr($review['name']); ?>" 
                                    loading="lazy"
                                >

                                <div class="oc-user-review-data">
                                    <p >
                                        <?php echo esc_html($review['name']); ?>
                                        <i class="fa-solid fa-circle-check"></i>
                                    </p>

                                    <span>
                                        <?php echo esc_html($review['location']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
            
                ?>
                <div class="oc-line-trust mt-5">
             <div
                  class="trustpilot-widget"
                  data-locale="en-US"
                  data-template-id="5419b6ffb0d04a076446a9af"
                  data-businessunit-id="5f6c3ba7296f83000100bdf0"
                  data-style-height="auto"
                  data-style-width="100%"
                 
                  data-token="f24cfa9d-6ac7-4b9b-a381-8fcf03556d14"
                >
                  <a href="https://www.trustpilot.com/review/www.loosegrowndiamond.com" target="_blank" rel="noopener noreferrer">
                    Trustpilot
                  </a>
                </div>
            </div>
           
                <?php
            
            ?>
            
        </div>
   
    <?php
}

function single_product_experience_history(){
    ?>
   <!-- experience history section -->
    <div class="etheme_list_view">
        <div class="container-fluid">
            <div class="row align-items-center asfg">
                <div class="col-md-6 order-md-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/manufactring.png" alt="manufactring" class="rounded-pill">
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="blog_text">
                        <h2 class="mb-0">42+ Years Of Experience In <br class="d-none d-md-block"> Diamond Industry
                        </h2>
                        <br>
                        <p class="fw-400 mb-0 h6">We are India based Manufacturer and has served more than <?php echo '68,432+';?>
                            Customers
                            Internationally.</p>
                    </div>
                </div>
                <div class="col-md-6 order-md-3">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/image/quality_diamonds.png" alt="quality_diamonds" class="rounded-pill">
                </div>
                <div class="col-md-6 order-md-4">
                    <div class="blog_text">
                        <h2 class="mb-0">
                            Top Quality In The Market With International Certificates</h2>
                        <br>
                        <p class="fw-400 mb-0 h6">We provides diamond with certifications (GIA and IGI). Our main
                            focus is
                            to gain a customer's trust and giving you best buying experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function single_product_buy_direct_us(){
    ?>
     <!--  -->
    <div class="light_gray_background">
        <div class="container-fluid">
            <h2 class="h1 fw-600 text-center">Buy Directly From Us. No Middleman</h2>
            <p class="font_size_15 text-center mb_70 text-dark">Our prices are lower than the regular sellers because we
                directly
                give you
                diamonds
                from our factory. No
                Middleman.
            </p>
            <div class="etheme_step_row">
                <div class="row">
                    <div class="col-md-4">
                        <div class="etheme_box_m text-center">
                            <div>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg" alt="loosegrowndiamond_logo">
                                <div class="text_black_secondary fw-600 my-2 h5">RRP Diamond </div>
                                <span class="font_size_15">(We as a Manufacturer) </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="etheme_box_m etheme_white_box text-center">
                            <div>
                                <p class="text_black_secondary fw-500 mb-0">Directly Selling To</p>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/arrow_1.svg" alt="arrow_1">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="etheme_box_m text-center">
                            <div>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/user.svg" alt="user">
                                
                                <div class="text_black_secondary fw-600 my-2 h5">You (as Buyer)  </div>
                                <span class="font_size_15">(Jewerly Owners, Diamond Traders) </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}

function help_box(){
    ?>
    <div class="help_box">
        <h3 class="text-center">We’re Here To Help</h3>
        <div class="row  text-center">
            <div class="col-lg-4">
                <a href="tel:+<?php echo SUPPORT_MOBILE?>">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <b> Call us</b> +<?php echo SUPPORT_MOBILE?>
                </a>
            </div>
            <div class="col-lg-4" id="border">
                <a href="javascript:void(0);"  onclick="$zoho.salesiq.floatwindow.visible('show'); return false;">
                    Chat with us
                    <br>
                    <span>
                        <b>AVAILABLE NOW</b>
                    </span>
                </a>
            </div>
            <div class="col-lg-4">
                <a href="mailto:<?php echo SUPPORT_EMAIL;?>">
                    <i class="fa-solid fa-envelope"></i>
                    <b> Email us</b> Customer service
                </a>
            </div>
        </div>
    </div>
    <?php
}
function start_engagement_rings($title_tag='h3'){
    ?>
    <div class="container-fluid">
        <<?php echo $title_tag;?> class="text_black_secondary h1 fw-normal font_literata mb-0 text-center">Shop Engagement Rings By Style</<?php echo $title_tag;?>>
        <div class="py_60">
            <div class="row justify-content-center row_gap_50">
                <?php
                $ring_styles = [
                    [
                        'name' => 'Solitaire',
                        'image' => 'solitaire-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/solitaire'
                    ],
                    [
                        'name' => 'Halo',
                        'image' => 'hidden-halo-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/halo/'
                    ],
                    [
                        'name' => 'Bezel',
                        'image' => 'bezel-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/bezel'
                    ],
                    [
                        'name' => 'Cluster',
                        'image' => 'cluster.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/cluster'
                    ],
                    [
                        'name' => 'Three Stone',
                        'image' => 'three-stone-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/three-stone'
                    ],
                    [
                        'name' => 'Diamond Band',
                        'image' => 'diamondband-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/diamond-band'
                    ],
                    [
                        'name' => 'Unique',
                        'image' => 'unique-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/unique/'
                    ],
                    [
                        'name' => 'Hidden Halo',
                        'image' => 'hidden-halo-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/hidden-halo/'
                    ],
                    [
                        'name' => 'Dainty',
                        'image' => 'dainty-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/dainty/'
                    ],
                    [
                        'name' => 'Vintage Inspired',
                        'image' => 'vintage-Inspired-ring.webp',
                        'url' => get_site_url() . '/lab-grown-diamond-engagement-rings/vintage/'
                    ],
                ];
                ?>

                <?php foreach ($ring_styles as $style) : ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <a href="<?php echo esc_url($style['url']); ?>" class="text-decoration-none">
                            <div class="mb-4 mb-lg-0 ring-style">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/aj_custom/home-page/engagement-rings-by-style/' . $style['image']); ?>"
                                    alt="<?php echo esc_attr($style['name']); ?>"
                                    class="w-100 mb_15"
                                    loading="lazy">
                                <p class="fw-600 text-uppercase lc mb-0 text-center text-dark">
                                    <?php echo esc_html($style['name']); ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

