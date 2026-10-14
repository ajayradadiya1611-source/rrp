<?php

/**
 * Template Name: Lab grown engagement rings
 */

get_header();
?>
 <div class="">
   <div class="banner_section">
      <div class="container">
         <div class="row">
            <div class="col-lg-6">
               <div class="d-flex align-items-center h-100 text-center text-md-start">
                  <div class="engagement-text w-100">
                    
                    <h1 class="fw-600 mb-2 mb-sm-4">
                      Lab Grown Diamond Rings
                    </h1>

                    <p class="fw-normal">
                      Looking for the best engagement ring? Decided on what you want but are not able to find it? Browse through our engagement ring catalog and select lab grown diamond engagement rings that best suit your taste.
                    </p>

                    <a href="/build-a-ring/" 
                       class="btn btn-dark ls-blackbtn fw-700 mb-3 mb-md-0 px-4">
                       START WITH A SETTING
                    </a>

                  </div>
                </div>
            </div>
            <div class="col-lg-6"><img src="<?php echo get_site_url();?>/wp-content/uploads/2022/03/enggagement-new.png" alt="Enggagement Ring"></div>
         </div>
      </div>
   </div>
</div>
<div class="oc-check-stan">
   <h2 class="text-center fw-600">Check Our Stunning Collection of Lab Diamond Rings</h2>
   <div id="inventory-listing-root"></div>
</div>
<div class="book-in-store">
   <div class="container">
      <div class="row">
         <div class="col-lg-6"><img src="<?php echo get_site_url();?>//wp-content/uploads/2022/04/book_enggagement_ring-555x362.png" alt="Enggagement Ring"></div>
         <div class="col-lg-6">
           <div class="d-flex align-items-center h-100 text-center text-md-start">
              <div class="engagement-text w-100">
                
                <h1 class="fw-600 mb-2 mb-sm-4">
                  Book an In-store or Virtual Appointment
                </h1>

                <p class="fw-normal">
                  Contact our expert gemologists at Loose Grown Diamond to get better clarity of the diamond you seek. Set a virtual appointment, today!
                </p>

                <a href="/contact-us/" 
                   class="btn btn-dark ls-blackbtn fw-700 mb-3 mb-md-0 px-4 d-inline-block mx-auto mx-md-0">
                   BOOK AN APPOINTMENT
                </a>

              </div>
            </div>
         </div>
      </div>
   </div>
</div>



<div class="container my-5">
    <h2 class="text-center mb-4">Frequently Asked Questions</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="faq_list">
                <div class="faq_accordion" id="faqAccordion">
<?php 
            $faqs = get_field('faq'); // repeater field

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

<?php
get_footer();
