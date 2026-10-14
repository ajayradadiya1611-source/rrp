<?php

/**
 * Template Name: custom-engagement-rings
 */

get_header();
?>

<?php
start_engagement_rings();
?>
<?php
start_engagement_rings_by_shape();
?>

 <div class="section">
        <!-- diamond buying resources section -->
        <div class="container-fluid">
            <h1 class="font_literata text_black_secondary text-center mb-0">Design Your Own Engagement Ring</h1>
            <div class="py_60">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 ">
                            <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Why-Choose-Lab-Grown-Diamonds1.jpg"
                                class="card-img-top img-fluid"
                                alt="Why Choose Lab-Created Diamonds">
                            <div class="card-body px-0">
                                <h3 class="h5">Why Choose Lab-Created Diamonds?</h3>
                                <p>
                                    Lab-grown diamonds are environmentally friendly and cost-efficient.
                                    They undergo the same processing and grading standards as mined diamonds,
                                    based on the 4Cs of diamond certification.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 ">
                            <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Why-Choose-Lab-Grown-Diamonds1.jpg"
                                class="card-img-top img-fluid"
                                alt="Why Choose Lab-Created Diamonds">
                            <div class="card-body px-0">
                                <h3 class="h5">Why Choose Lab-Created Diamonds?</h3>
                                <p>
                                    Lab-grown diamonds are environmentally friendly and cost-efficient.
                                    They undergo the same processing and grading standards as mined diamonds,
                                    based on the 4Cs of diamond certification.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 ">
                            <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Why-Choose-Lab-Grown-Diamonds1.jpg"
                                class="card-img-top img-fluid"
                                alt="Why Choose Lab-Created Diamonds">
                            <div class="card-body px-0">
                                <h3 class="h5">Why Choose Lab-Created Diamonds?</h3>
                                <p>
                                    Lab-grown diamonds are environmentally friendly and cost-efficient.
                                    They undergo the same processing and grading standards as mined diamonds,
                                    based on the 4Cs of diamond certification.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 ">
                            <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2023/09/Why-Choose-Lab-Grown-Diamonds1.jpg"
                                class="card-img-top img-fluid"
                                alt="Why Choose Lab-Created Diamonds">
                            <div class="card-body px-0">
                                <h3 class="h5">Why Choose Lab-Created Diamonds?</h3>
                                <p>
                                    Lab-grown diamonds are environmentally friendly and cost-efficient.
                                    They undergo the same processing and grading standards as mined diamonds,
                                    based on the 4Cs of diamond certification.
                                </p>
                            </div>
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
