<?php

/**
 * Template Name: new-engagement-rings
 */

get_header();
?>
<div class="banner_section mb_30">
   <div class="page_heading " bis_skin_checked="1">
       <div class="container-fluid" bis_skin_checked="1">
          <nav aria-label="breadcrumb">
             <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><?php echo esc_html( get_field('oc_breadcrumb_heading') ?: "Engagement Rings" ); ?></li>
             </ol>
          </nav>
       </div>
    </div>
    <div class="bg_light_gray oc-engagement-hero">
      <div class="container-fluid">
          <div class="row flex-column-reverse flex-lg-row">
            <div class="col-lg-6 oc-hero-content">
               <div class="d-flex align-items-center h-100 text-center text-md-start">
                  <div class="engagement-text w-100">
                     <div class="oc-line-trust mb-3">
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
                    <h1 class="mb-2 mb-sm-4 font_literata">
                      <?php echo esc_html( get_field('oc_hero_heading') ?: "Create the Ring She’ll Never Forget" ); ?>
                    </h1>

                    <p class="fw-normal">
                     <?php echo esc_html( get_field('oc_hero_description') ?: 'Choose from 1,000+ settings and 2.5M+ certified lab diamonds to create her perfect ring — without the retail markup.' ); ?>
                    </p>
                    <div class="d-flex flex-column flex-md-row gap-2 text-center text-md-start">
                        <?php
                        $hero_btn1_text = get_field('oc_hero_button_1_text') ?: 'Start With a Setting';
                        $hero_btn1_url  = get_field('oc_hero_button_1_url') ?: '/build-a-ring/';
                        $hero_btn2_text = get_field('oc_hero_button_2_text') ?: 'Start With a Lab Diamond';
                        $hero_btn2_url  = get_field('oc_hero_button_2_url') ?: '/lab-diamonds/';
                        ?>
                        <a href="<?php echo esc_url($hero_btn1_url); ?>"
                           class="btn aj-black-btn-color aj-btn-big  w-md-auto">
                            <?php echo esc_html($hero_btn1_text); ?>
                        </a>

                        <a href="<?php echo esc_url($hero_btn2_url); ?>"
                           class="btn aj-transparent-border-black-color aj-btn-big px-4 w-md-auto">
                            <?php echo esc_html($hero_btn2_text); ?>
                        </a>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-6 oc-hero-image d-block">
                <?php
                $hero_image = get_field('oc_hero_image');
                $hero_image_url = $hero_image ? $hero_image['url'] : get_template_directory_uri() . '/aj_custom/new-engagement-rings/hero.png';
                $hero_image_alt = $hero_image ? $hero_image['alt'] : 'Engagement Ring';
                ?>
                <img src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr($hero_image_alt); ?>">
            </div>
         </div>
      </div>
   </div>
</div>

 <div class="section ">
        <!-- diamond buying resources section -->
        <div class="container-fluid">
            <h2 class="h1 font_literata text_black_secondary text-center mb-0"><?php echo esc_html( get_field('oc_popular_rings_heading') ?: 'Most Popular Diamond Engagement Rings' ); ?></h2>
            <div class="py_60">
                <?php
                /* ── Popular rings slider: pulled from ACF product pickers, with the
                   original hardcoded slugs kept as a fallback if no products are selected ── */
                if ( WEBSITE_TYPE == 'WHOLESALE' ) {
                    $dr_products       = get_field('oc_popular_rings_products_wholesale');
                    $dr_featured_post  = get_field('oc_popular_rings_featured_wholesale');

                    $dr_fallback_slugs = array(
                        'adaya-oval-solitaire-lab-diamond-engagement-ring-lgdov-85',
                        'adaya-round-solitaire-lab-diamond-engagement-ring-lgdbr-108',
                        'adaya-emerald-solitaire-lab-diamond-engagement-ring-lgdem-84',
                        'tulip-oval-lab-diamond-band-engagement-ring-lgdov-94',
                        'adaya-radiant-solitaire-lab-diamand-engagement-ring-lgdrad-81',
                        'arya-oval-solitaire-lab-diamond-engagement-ring-lgdov-86',
                        'nimi-oval-solitaire-lab-diamond-engagement-ring-lgdov-117',
                        'nimi-round-solitaire-lab-diamond-engagement-ring-lgdbr-118',
                        'adaya-cushion-solitaire-lab-diamond-engagement-ring-lgdcu-82',
                        'adaya-marquise-solitaire-lab-diamond-engagement-ring-lgdmq-81'
                    );
                    $dr_fallback_featured_slug = 'adaya-oval-solitaire-lab-diamond-engagement-ring-lgdov-85';
                } else {
                    $dr_products       = get_field('oc_popular_rings_products_retail');
                    $dr_featured_post  = get_field('oc_popular_rings_featured_retail');

                    $dr_fallback_slugs = array(
                        'adaya-oval-cut-lab-grown-diamond-solitaire-ring-lgdov-85',
                        'adaya-round-cut-lab-grown-diamond-solitaire-ring-lgdbr-108',
                        'adaya-emerald-cut-lab-grown-diamond-solitaire-ring-lgdem-84',
                        'tulip-oval-cut-lab-grown-diamond-band-ring-lgdov-94',
                        'adaya-radiant-cut-lab-grown-diamond-solitaire-ring-lgdrad-81',
                        'arya-oval-cut-lab-grown-diamond-solitaire-ring-lgdov-86',
                        'nimi-oval-cut-lab-grown-diamond-solitaire-ring-lgdov-117',
                        'nimi-round-cut-lab-grown-diamond-solitaire-ring-lgdbr-118',
                        'adaya-cushion-cut-lab-grown-diamond-solitaire-ring-lgdcu-82',
                        'adaya-marquise-cut-lab-grown-diamond-solitaire-ring-lgdmq-81'
                    );
                    $dr_fallback_featured_slug = 'adaya-oval-cut-lab-grown-diamond-solitaire-ring-lgdov-85';
                }

                if ( ! empty($dr_products) ) {
                    $dr_slugs = wp_list_pluck($dr_products, 'post_name');
                } else {
                    $dr_slugs = $dr_fallback_slugs;
                }

                if ( $dr_featured_post instanceof WP_Post ) {
                    $dr_featured_slug = $dr_featured_post->post_name;
                } elseif ( ! empty($dr_slugs) ) {
                    // No featured product chosen: default to the first slug in the list.
                    $dr_featured_slug = $dr_slugs[0];
                } else {
                    $dr_featured_slug = $dr_fallback_featured_slug;
                }

                render_dr_slider( $dr_slugs, $dr_featured_slug );
                ?>
            </div>
        </div>
    </div>
</div>


<?php
/* ───────── Shop Engagement Rings By Style (dynamic) ───────── */
$ring_styles_heading = get_field('oc_ring_styles_heading') ?: 'Shop Engagement Rings By Style';
$ring_styles = get_field('oc_ring_styles_by_style');


if(!empty($ring_styles)){


?>
<div class="container-fluid">
    <h2 class="text_black_secondary h1 fw-normal font_literata mb-0 text-center"><?php echo esc_html($ring_styles_heading); ?></h2>
    <div class="py_60">
        <div class="row justify-content-center row_gap_50">
            <?php foreach ($ring_styles as $style) :
                $style_image_url = is_array($style['image']) ? $style['image']['url'] : $style['image'];
                $style_image_alt = is_array($style['image']) && !empty($style['image']['alt']) ? $style['image']['alt'] : $style['label'];
            ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="<?php echo esc_url($style['link']); ?>" class="text-decoration-none">
                    <div class="mb-4 mb-lg-0 ring-style">
                        <img src="<?php echo esc_url($style_image_url); ?>" alt="<?php echo esc_attr($style_image_alt); ?>" class="w-100 mb_15" loading="lazy">
                        <p class="fw-600 text-uppercase lc mb-0 text-center text-dark">
                            <?php echo esc_html($style['label']); ?>
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
?>
<?php
/* ───────── Shop Engagement Rings By Shape (only admin-selected shapes shown; link per shape is dynamic) ───────── */

// Fixed icon/label lookup — used only to fill in the icon and display label for whichever
// shapes the admin adds below. This is not looped directly; it's a lookup by shape slug.
$shape_lookup = array(
    'round'    => array('label' => 'ROUND',    'icon' => 'round.svg'),
    'princess' => array('label' => 'PRINCESS', 'icon' => 'princess.svg'),
    'radiant'  => array('label' => 'RADIANT',  'icon' => 'radiant.svg'),
    'asscher'  => array('label' => 'ASSCHER',  'icon' => 'asscher.svg'),
    'cushion'  => array('label' => 'CUSHION',  'icon' => 'cushion.svg'),
    'oval'     => array('label' => 'OVAL',     'icon' => 'oval.svg'),
    'emerald'  => array('label' => 'EMERALD',  'icon' => 'emerald.svg'),
    'pear'     => array('label' => 'PEAR',     'icon' => 'pear.svg'),
    'marquise' => array('label' => 'MARQUISE', 'icon' => 'marquise.svg'),
    'heart'    => array('label' => 'HEART',    'icon' => 'heart.svg'),
);

// The list actually shown on the frontend comes entirely from ACF — only shapes
// the admin added a row for appear here, each with its own link.
$shape_rows = get_field('oc_shape_dropdowns');

$shape_icon_base = get_template_directory_uri() . '/aj_custom/home-page/shape-icon/';
if(!empty($shape_rows)){
?>
<div class="bg_light_blue section">
    <div class="container-fluid">
        <div class="text-center">
            <h2 class=" h1 text_black_secondary font_literata text-center mb-3">
                <?php echo esc_html( get_field('oc_shape_section_heading') ?: 'Shop Engagement Rings By Shape' ); ?>
            </h2>
        </div>
        <div class="diamond_shap_section pb-0">
            <?php if ( ! empty($shape_rows) ) :
                foreach ($shape_rows as $row) :
                    if ( empty($row['shape']) || empty($shape_lookup[$row['shape']]) ) continue;
                    $shape_info = $shape_lookup[$row['shape']];
                    $shape_link = ! empty($row['link']) ? $row['link'] : '#';
            ?>
            <div class="diamond_colum text-center">
                <a class="text-decoration-none" href="<?php echo esc_url($shape_link); ?>">
                    <img src="<?php echo esc_url($shape_icon_base . $shape_info['icon']); ?>">
                    <span><?php echo esc_html($shape_info['label']); ?></span>
                </a>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
<?php
}
?>


<?php
/* ───────── Design Your Own Engagement Ring (dynamic banner cards) ───────── */
$design_own_heading = get_field('oc_design_own_heading') ?: 'Design Your Own Engagement Ring';
$banner_cards = get_field('oc_design_own_cards');

// Fallback to the original hardcoded cards if no ACF data has been entered yet.
if ( empty($banner_cards) ) {
    $banner_cards = array(
        array(
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/banner1.png'),
            'title' => 'Start with a Setting',
            'description' => 'From sourcing to custom manufacturing, we help jewelers and retailers scale faster with high-quality lab-grown diamond jewelry.',
            'link' => '/build-a-ring/',
        ),
        array(
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/banner2.png'),
            'title' => 'Start with a Lab Diamond',
            'description' => 'Our curated collection of bridal sets features expertly matched engagement and wedding rings so you can find the perfect pairing.',
            'link' => '/lab-diamonds/',
        ),
        array(
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/banner3.png'),
            'title' => 'Custom Engagement Rings',
            'description' => 'From sourcing to custom manufacturing, we help jewelers and retailers scale faster with high-quality lab-grown diamond jewelry.',
            'link' => '/build-a-ring/',
        ),
        array(
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/banner4.png'),
            'title' => 'Ready to Ship Engagement Rings',
            'description' => 'Our curated collection of bridal sets features expertly matched engagement and wedding rings so you can find the perfect pairing.',
            'link' => '/ready-to-ship/engagement-rings',
        ),
    );
}
?>
 <div class="section bg_light_gray">
        <!-- diamond buying resources section -->
        <div class="container-fluid">
            <h2 class="font_literata h1 text_black_secondary text-center mb-5"><?php echo esc_html($design_own_heading); ?></h2>
            <div class="py_0">
                <div class="row g-4 mb-4">
                    <?php foreach ($banner_cards as $card) :
                        $card_image = is_array($card['image']) ? $card['image']['url'] : $card['image'];
                        $card_title = $card['title'];
                        $card_desc  = $card['description'];
                        $card_link  = $card['link'];
                    ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 bg_trasnparent">
                            <a 
                             href="<?php echo $card_link === '#customSettingModal' ? 'javascript:void(0);' : esc_url($card_link); ?>"
    <?php if ($card_link === '#customSettingModal') : ?>
        data-bs-toggle="modal"
        data-bs-target="#customSettingModal"
    <?php endif; ?>
     class="text-decoration-none">
                                <img src="<?php echo esc_url($card_image); ?>"
                                class="card-img-top img-fluid"
                                alt="<?php echo esc_attr($card_title); ?>">
                            </a>
                            <div class="card-body px-0 ">
                                <a
    href="<?php echo $card_link === '#customSettingModal' ? 'javascript:void(0);' : esc_url($card_link); ?>"
    <?php if ($card_link === '#customSettingModal') : ?>
        data-bs-toggle="modal"
        data-bs-target="#customSettingModal"
    <?php endif; ?>
    class="text-decoration-none"
>
                                    <h3 class="h4 font_literata"><?php echo esc_html($card_title); ?></h3>
                                </a>
                                <p>
                                    <?php echo esc_html($card_desc); ?>
                                </p>
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
/* ───────── Engagement Ring Reviews (dynamic) ───────── */
$reviews_heading = get_field('oc_reviews_heading') ?: 'Engagement Ring Reviews';
$reviews = get_field('oc_reviews');

// Fallback to the original hardcoded reviews if no ACF data has been entered yet.
if ( empty($reviews) ) {
    $reviews = array(
        array(
            'name' => 'David Clancy',
            'title' => 'Renai Oval Three Stone Engagement Ring',
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/engagement-review-1.png'),
            'review' => "Had a great experience with Loose Grown Diamond, from finding the perfect stones to receiving excellent support, fast shipping, and a beautiful ring that received many compliments.",
            'link' => '/product/renai-oval-cut-lab-grown-diamond-three-stone-ring-lgdov-54/',
        ),
        array(
            'name' => 'danny darko',
            'title' => 'Nimi Cushion Solitaire Engagement Ring',
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/engagement-review-2.png'),
            'review' => "Excellent experience with Loose Grown Diamond, offering great prices, helpful customer service, CAD approval, quality checks & quick shipping. The ring turned out beautifully & exceeded expectations.",
            'link' => '/product/nimi-cushion-cut-lab-grown-diamond-solitaire-ring-lgdcu-111/',
        ),
        array(
            'name' => 'Dean Christensen',
            'title' => 'Renai Oval Three Stone Engagement Ring',
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/engagement-review-3.png'),
            'review' => "Outstanding experience with LGD! Their team was incredibly helpful & responsive, making the online purchase stress-free. The beautiful ring exceeded expectations, and I’ll definitely return for future purchases.",
            'link' => '/product/renai-oval-cut-lab-grown-diamond-three-stone-ring-lgdov-54/',
        ),
        array(
            'name' => 'Lina H',
            'title' => 'Shyam Oval Diamond Band Engagement Ring',
            'image' => array('url' => get_template_directory_uri() . '/aj_custom/new-engagement-rings/engagement-review-4.png'),
            'review' => "Exceptional experience with LGD! Their communication, expert guidance, & craftsmanship were outstanding. The stunning ring exceeded expectations, & I’m thrilled with the quality and service.",
            'link' => '/product/shyam-oval-cut-lab-grown-diamond-band-ring-lgdov-92/',
        ),
    );
}
?>
<div class="section">
    <div class="container-fluid">
            <h2 class="font_literata text_black_secondary text-center h1 mb-0"><?php echo esc_html($reviews_heading); ?></h2>
            <div class="py_0">
                <div class="row justify-content-center row_gap_50">
                   <div class="row g-4">
                        <?php foreach ($reviews as $review) :
                            $review_image = is_array($review['image']) ? $review['image']['url'] : $review['image'];
                        ?>
                            <div class="col-6 col-md-3">
                                <div class="review-card">
                                    <img src="<?php echo esc_url($review_image); ?>" class="img-fluid w-100 mb-2" alt="<?php echo esc_attr($review['title']); ?>">

                                    <div class="small mb-2">
                                        <?php echo esc_html($review['name']); ?> ★★★★★
                                    </div>

                                    <h3 class="h6"><?php echo esc_html($review['title']); ?></h3>

                                    <p class="small text-muted">
                                        <?php echo esc_html($review['review']); ?>
                                    </p>

                                    <a href="<?php echo esc_url($review['link']); ?>">
                                        Shop now
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php
$related_blogs = get_field('oc_related_blog_engagement');

if ($related_blogs) : ?>
<div class="section bg_light_gray ">
    <div class="container-fluid related_blogs_wrap ">
            <div class="text-center">
                <h2 class="related_blogs_title"><?php echo esc_html( get_field('oc_related_blog_heading') ?: 'Engagement Ring Education' ); ?></h2>
            </div>

            <div class="row">
                <?php foreach ($related_blogs as $post) :
                    setup_postdata($post); ?>

                    <div class="col-md-6 mb-4">
                        <div class="related_blog_card bg_trasnparent">
                            <h3>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <p>
                                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                            </p>

                            <span class="blog_author">
                                <?php echo get_the_author(); ?>
                            </span>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
    </div>
</div>
<?php
wp_reset_postdata();
endif;
?>

 <div class="section bg_light_gray">
    <div class="container-fluid">
        <div class="row align-items-center ">
            <div class="col-md-6 order-md-3">
                <?php
                $help_image = get_field('oc_help_section_image');
                $help_image_url = $help_image ? $help_image['url'] : get_template_directory_uri() . '/aj_custom/new-engagement-rings/wearehere.png';
                $help_image_alt = $help_image ? $help_image['alt'] : 'quality_diamonds';
                ?>
                <img src="<?php echo esc_url($help_image_url); ?>" alt="<?php echo esc_attr($help_image_alt); ?>" class="">
            </div>
            <div class="col-md-6 order-md-4">
                <div class="">
                    <h2 class="font_literata mb-3 h1">
                        <?php echo esc_html( get_field('oc_help_section_heading') ?: 'We’re Here for You — Online & In Store' ); ?></h2>
                    <p class="fw-400 h6 mb-5"><?php echo esc_html( get_field('oc_help_section_description') ?: 'Whether you visit our showroom or connect online, we make diamond shopping personal, easy, and tailored just for you.' ); ?></p>
                   <div class="d-flex flex-column flex-md-row gap-2 text-center text-md-start">
                        <?php
                        $help_btn1_text = get_field('oc_help_button_1_text') ?: 'Visit a Showroom';
                        $help_btn1_url  = get_field('oc_help_button_1_url') ?: 'https://maps.app.goo.gl/7ngQyM7CKwQGY8D29';
                        $help_btn2_text = get_field('oc_help_button_2_text') ?: 'Book a Virtual Appointment';
                        $help_btn2_url  = get_field('oc_help_button_2_url') ?: '/contact-us/';
                        ?>
                        <a href="<?php echo esc_url($help_btn1_url); ?>" target="_blank" class="btn aj-black-btn-color aj-btn-big px-4"><?php echo esc_html($help_btn1_text); ?></a>
                        <a href="<?php echo esc_url($help_btn2_url); ?>" class="btn aj-transparent-border-black-color aj-btn-big px-4"><?php echo esc_html($help_btn2_text); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container my-5">
  <div class="ls_inventory_desc">
    <div class="ls_inventory_desc_inner">
              <div>
              <p class="pro-cat_desc"><?php echo get_field('short_description_bottom');?></p>
             </div>
              <div  class="pro-cat_desc_hide" >

               <?php echo get_field('short_description_bottom_description');?>
              </div>
       </div>
       <div id="invtoggleBtn" class="oc-invtoggleBtn">
              Read More
            </div>           
  </div>
  
</div>
<div class="section ">
<div class="container mt-5 mb-5 mb-md-2">
  <h2 class="text-center mb-4 font_literata h1 "><?php echo esc_html( get_field('oc_faq_heading') ?: 'Frequently Asked Questions' ); ?></h2>
  <div class="row justify-content-center">
    <div class="col-md-8">


        <div class="faq_list">
          <div class="accordion " id="faqAccordion">

           <?php
            $faqs = get_field('engagement_faq'); // repeater field

            if (!empty($faqs)):
                foreach ($faqs as $index => $faq):
                    $id = 'faq' . ($index + 1);

                    $question = $faq['title'];
                    $answer   = $faq['description'];
            ?>

            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo esc_attr($id); ?>">
                        <?php echo esc_html($question); ?>
                    </button>
                </h3>

                <div id="<?php echo esc_attr($id); ?>"
                     class="accordion-collapse collapse"
                     data-bs-parent="#faqAccordion">

                    <div class="accordion-body">
                        <?php echo wp_kses_post($answer); ?>
                    </div>
                </div>
            </div>

            <?php
                endforeach;
            endif;
            ?>

          </div>
        </div>
     </div>
  </div>
</div>
</div>
<?php
get_footer();
?>