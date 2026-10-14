<?php
/**
 * Template Name: Inventory2
 */

get_header();
global $text_change;
$inventory_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'default';
?>

<script type="text/javascript">
   
    
    // Set Inventory Type from URL parameter
    window.INVENTORY_TYPE = '<?php echo esc_js($inventory_type); ?>';
    
</script>
<div class="ls_inventorypg d-flex">
    <div class="banner_section">
        <div class="inventory_title_parent">
            <div class="container-fluid inventory_title">
                <div class="text-center mb_60"><h1 class="text_black_secondary fw-600 font_size_30"><?php echo get_the_title();?></h1>
                 <p class="font_size_15"><?php echo get_field('short_description');?></p></div>
            </div>
            <?php 
            //if($inventory_type === 'ringbuilder') {
                ?>
                 <div class="container-fluid">
                    <?php 
                        // Ring Builder Breadcrumb - Always show this
                        ls_ring_builder_breadcrumb('diamond');
                        ?>
                        <div id="ring-builder-breadcrumb-root"></div>
                </div>
                <?php

           // }
            ?>
       </div>
    </div>
    
</div>




<div id="ls-inventory-root2"></div>

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

<?php
$bottom_categories = get_field('bottom_category');

if ($bottom_categories) : ?>
    <div class="shop_by_category_wrap">
        <div class="container">
            <div class="row  gy-4">

                <?php foreach ($bottom_categories as $category) : ?>
                    <div class="col-md-4 col-sm-12 shop_by_category_col">
                        
                        <?php if (!empty($category['title'])) : ?>
                            <h4><?php echo esc_html($category['title']); ?></h4>
                        <?php endif; ?>

                        <?php if (!empty($category['links'])) : ?>
                            <ul>
                                <?php foreach ($category['links'] as $link) : ?>
                                    <?php if (!empty($link['link_title']) && !empty($link['link_value'])) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($link['link_value']); ?>">
                                                <?php echo esc_html($link['link_title']); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
<?php endif; ?>
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