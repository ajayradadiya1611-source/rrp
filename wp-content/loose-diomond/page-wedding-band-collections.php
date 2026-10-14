<?php

/**
 * Template Name: wedding-band-collections
 */

get_header();
?>
<div class="banner_section mb_30">
   <div class="page_heading " bis_skin_checked="1">
       <div class="container-fluid" bis_skin_checked="1">
          <nav aria-label="breadcrumb">
             <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Diamond</li>
             </ol>
          </nav>
       </div>
    </div>
    <div class="bg_light_gray">
      <div class="container">
         <div class="row">
            <div class="col-lg-6">
               <div class="d-flex align-items-center h-100 text-center text-md-start">
                  <div class="engagement-text w-100">
                    
                    <h1 class="fw-600 mb-2 mb-sm-4">
                      Wedding Bands
                    </h1>

                    <p class="fw-normal">
                     Discover our customers' 20 most beloved women's wedding rings, each representing our unparalleled commitment to craftsmanship.
                    </p>
                    <div class="d-flex gap-2 text-md-start">
                        <a href="/lab-diamond-wedding-bands/" 
                           class="btn ls-blackbtn fw-700 mb-3 mb-md-0 px-4">
                           Shop Wedding Bands
                        </a>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-6"><img src="<?php echo get_template_directory_uri(); ?>/aj_custom/wedding-band-collections/banner.png" alt="Enggagement Ring"></div>
         </div>
      </div>
   </div>
</div>
<div class="container-fluid">
        <h1 class="font_literata text_black_secondary text-center mb-0">Engagement Rings By Style</h1>
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
                ];
                ?>

                <?php foreach ($ring_styles as $style) : ?>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-6">
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
<div class="section ">
        <!-- diamond buying resources section -->
        <div class="container-fluid">
            <h1 class="font_literata text_black_secondary text-center mb-0">Popular Engagement Ring</h1>  
            <div class="py_60">
                <?php
                $dr_ids = array( 1084016, 1084016, 1084016, 1084016, 1084016, 1084016, 1084016 );
                $dr_featured = 1084016;
                render_dr_slider(  $dr_ids, $dr_featured );
                ?>




            </div>
        </div>
    </div>
</div>
 <div class="section bg_light_gray">
    <div class="container-fluid">
        <div class="row align-items-center ">
            
                <div class="col-md-6 ">
                        <h1 class="font_literata mb-3 text-capitalize">
                           Can’t find your perfect ring? we’ll create it for you.</h1>
                        <h4 class="fw-400  mb-5">have a design in mind or need help choosing the perfect price? Schedule a 1-on-1 consultation and let us assist you.</h4>
                       <div class="d-flex gap-2 text-md-start">
                            <a href="/build-a-ring/" 
                               class="btn ls-blackbtn fw-700 mb-3 mb-md-0 px-4">
                               Talk To An Expert
                            </a>
                        </div>
                </div>
                <div class="col-md-6 ">
                     <div class="row">
                        <div class="col-md-6 ">
                            <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/wedding-band-collections/cant1.png" alt="quality_diamonds" class="">
                        </div>
                        <div class="col-md-6 ">
                            <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/wedding-band-collections/cant2.png" alt="quality_diamonds" class="">
                        </div>
                    </div>
                </div>
        </div>
       
    </div>
</div>


<?php
$related_blogs = get_field('oc_related_blog');

if ($related_blogs) : ?>
<div class="container related_blogs_wrap">
    <div class="text-center">
        <h2 class="related_blogs_title">Related Blogs</h2>
    </div>

    <div class="row">
        <?php foreach ($related_blogs as $post) : 
            setup_postdata($post); ?>

            <div class="col-md-6 mb-4">
                <div class="related_blog_card">
                    <h5>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h5>

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

<?php
wp_reset_postdata();
endif;
?>
<div class="section ">
        <!-- diamond buying resources section -->
        <div class="container-fluid">
            <h1 class="font_literata text_black_secondary text-center mb-0">Popular Engagement Ring</h1>  
            <div class="py_60">


                <?php
                // --- Usage Example ---
                $education_slides = [
                    [
                        'title'       => 'Wedding Band Guide',
                        'description' => 'Your go-to guide for choosing the perfect wedding ring.',
                        'image'       => 'https://placehold.co/400x300/f5f0eb/bba98a?text=Band+Guide',
                        'url'         => '/guides/wedding-band-guide',
                    ],
                    [
                        'title'       => 'How to Wear Your Wedding Ring',
                        'description' => 'Discover expert tips for styling your wedding ring.',
                        'image'       => 'https://placehold.co/400x300/f5f0eb/bba98a?text=Wear+Guide',
                        'url'         => '/guides/how-to-wear-wedding-ring',
                    ],
                    [
                        'title'       => 'Women\'s Wedding Band Styles',
                        'description' => 'Learn the different wedding ring types and designs for women.',
                        'image'       => 'https://placehold.co/400x300/f5f0eb/bba98a?text=Women%27s+Styles',
                        'url'         => '/guides/womens-wedding-band-styles',
                    ],
                    [
                        'title'       => 'Men\'s Wedding Band Styles',
                        'description' => 'Learn the different wedding band types and designs for men.',
                        'image'       => 'https://placehold.co/400x300/f5f0eb/bba98a?text=Men%27s+Styles',
                        'url'         => '/guides/mens-wedding-band-styles',
                    ],
                    [
                        'title'       => 'How to Measure Ring Size',
                        'description' => 'View our ring size chart, request a free ring sizer, & learn how to find your ring size.',
                        'image'       => 'https://placehold.co/400x300/f5f0eb/bba98a?text=Ring+Size',
                        'url'         => '/guides/how-to-measure-ring-size',
                    ],
                ];
                 
                render_education_slider( $education_slides );
                ?>






            </div>
        </div>
    </div>
</div>

 <div class="section bg_light_gray">
        <div class="container-fluid">
            <div class="row align-items-center ">
                <div class="col-md-6 order-md-3">
                    <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/wedding-band-collections/wearehere.png" alt="quality_diamonds" class="">
                </div>
                <div class="col-md-6 order-md-4">
                    <div class="">
                        <h1 class="font_literata mb-3">
                            Top Quality In The Market With International Certificates</h1>
                        <h4 class="fw-400  mb-5">We provides diamond with certifications (GIA and IGI). Our main
                            focus is
                            to gain a customer's trust and giving you best buying experience.</h4>
                       <div class="d-flex gap-2 text-md-start">
                        <a href="/build-a-ring/" 
                           class="btn ls-blackbtn fw-700 mb-3 mb-md-0 px-4">
                           Visit a Showroom
                        </a>
                        <a href="/build-a-ring/" 
                           class="btn ls-trasnparentbtn fw-700 mb-3 mb-md-0 px-4">
                            Book a Virtual Appointment
                        </a>
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

                <?php 
                the_content();
                ?>
              </div>
       </div>
       <div id="invtoggleBtn" class="oc-invtoggleBtn">
              Read More
            </div>           
  </div>
  
</div>
<div class="container mt-5 mb-5 mb-md-2">
  <h2 class="text-center mb-4">Frequently Asked Questions</h2>
  <div class="row justify-content-center">
    <div class="col-md-8">
      
    
        <div class="faq_list">
          <div class="accordion " id="faqAccordion">

           <?php 
            $faqs = get_field('faq'); // repeater field

            if (!empty($faqs)): 
                foreach ($faqs as $index => $faq): 
                    $id = 'faq' . ($index + 1);

                    $question = $faq['title'];
                    $answer   = $faq['description'];
            ?>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo esc_attr($id); ?>">
                        <?php echo esc_html($question); ?>
                    </button>
                </h2>

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


<?php
get_footer();
