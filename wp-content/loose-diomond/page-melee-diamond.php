<?php

/**
 * Template Name: Melee Diamond
 */

get_header();
?>
  <div class="melee-banner-section">
    <div class="container">
      <div class="row align-items-center">
        
        <!-- Image -->
        <div class="col-md-5 px-0 order-1 order-md-2 melee-banner bg-white">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/image/melee-diamond-new/melee-banner.svg" 
               alt="melee-banner"
               class="img-fluid w-100" />
        </div>

        <!-- Content -->
        <div class="col-md-7 px-0 order-2 order-md-1">
          <div class="melee-content-section">
            <h1 class="banner-title text-center text-md-start">Melee Diamonds</h1>
            <p class="mb-0 text-center text-md-start">
              Our melee diamond collection features premium small diamonds in the most popular sizes.
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="melee-products">
    <div class="container">
      <div class="melee-products-list">
       <?php

        // ✅ Product IDs with custom images
        $products_data = [
            [
                'id' => 26963,
                'is_round'=>true,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/round.png'
            ],
            [
                'id' => 26268,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/princess.png'
            ],
            [
                'id' => 26350,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cushion.png'
            ],
            [
                'id' => 25963,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/heart.png'
            ],
            [
                'id' => 26432,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/marquise.png'
            ],
            [
                'id' => 26185,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/pear.png'
            ],
            [
                'id' => 26517,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/oval.png'
            ],
            [
                'id' => 26603,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/emerald.png'
            ],
            [
                'id' => 26685,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/baguette.png'
            ],
            [
                'id' => 29471,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/old mine.png'
            ],
            [
                'id' => 29561,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/old european.png'
            ],
            [
                'id' => 27700,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/triangular.png'
            ],
            [
                'id' => 27427,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/shield.png'
            ],
            [
                'id' => 27509,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/hexagon.png'
            ],
            [
                'id' => 27782,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/trapezoid.png'
            ],
            [
                'id' => 27591,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/baguette trapezoid.png'
            ],
            [
                'id' => 2018787,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/round/pink.png'
            ],
            [
                'id' => 2024435,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/round/pink.png'
            ],
            [
                'id' => 2020571,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/round/yellow.png'
            ],
            [
                'id' => 2020174,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/round/green.png'
            ],
            [
                'id' => 2024933,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/melee-diamond-new/purple-color-round-diamond.jpg'
            ],
            [
                'id' => 2019574,
                'is_round'=>false,
                'image' => get_template_directory_uri() . '/assets/image/ls-inventory-color/diamond/round/blue.png'
            ],
        ];



        foreach ($products_data as $item) :

            $pid = $item['id'];
            $image_url = $item['image'];

            $product = wc_get_product($pid);
            if (!$product) continue;

            $attributes = $product->get_variation_attributes();
            $available_variations = $product->get_available_variations();
            $selected_attributes = $product->get_default_attributes();
        ?>

        <?php
          // Prepare variations data for frontend (if any)
          $variations_data = [];
          if ( ! empty( $available_variations ) ) {
            foreach ( $available_variations as $var ) {
              // Derive a plain text price for frontend display (no spans)
              $plain_price = '';
              if ( isset( $var['display_price'] ) && $var['display_price'] !== '' ) {
                $plain_price = wp_strip_all_tags( wc_price( $var['display_price'] ) );
              } elseif ( ! empty( $var['price_html'] ) ) {
                $plain_price = wp_strip_all_tags( $var['price_html'] );
              }

              $variations_data[] = [
                'variation_id' => $var['variation_id'] ?? '',
                'attributes'   => $var['attributes'] ?? [],
                'price_html'   => $var['price_html'] ?? '',
                'display_price'=> $var['display_price'] ?? '',
                'plain_price'  => $plain_price,
              ];
            }
          }
          $variations_json = wp_json_encode( $variations_data );
        ?>

        <div class="melee-product-info" data-variations='<?php echo esc_attr( $variations_json ); ?>' data-slug="<?php echo esc_attr( $product->get_slug() ); ?>">

          <!-- ✅ STATIC IMAGE -->
          <div class="melee-product-image">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" />
          </div>

          <!-- DETAILS -->
          <div class="melee-product-details">

             <a href="<?php echo get_permalink($pid); ?>" class="text-decoration-none"><h2><?php echo esc_html($product->get_name()); ?></h2></a>

            <p class="estimated-text"><?php if($item['is_round']==true){echo 'Estimated shipping time is 3-5 days';}else{ echo 'Estimated shipping time is up to 2 weeks. ';} ?></p>

            <?php
            // Determine initial price display as plain text (no span tags)
            $initial_price = '';
            if ( $product->is_type( 'variable' ) && ! empty( $variations_data ) ) {
              // try to find matching default variation
              $found = false;
              foreach ( $variations_data as $v ) {
                $match = true;
                foreach ( $v['attributes'] as $k => $val ) {
                  $attr_key = str_replace( 'attribute_', '', $k );
                  $default_val = $selected_attributes[ $attr_key ] ?? '';
                  if ( $default_val && $val !== $default_val ) {
                    $match = false;
                    break;
                  }
                }
                if ( $match ) {
                  $initial_price = $v['plain_price'] ?: wp_strip_all_tags( $product->get_price_html() );
                  $found = true;
                  break;
                }
              }
              if ( ! $found ) {
                $initial_price = $variations_data[0]['plain_price'] ?: wp_strip_all_tags( $product->get_price_html() );
              }
            } else {
              $initial_price = '';
              $prod_price = $product->get_price();
              if ( $prod_price !== '' && $prod_price !== null ) {
                $initial_price = wp_strip_all_tags( wc_price( $prod_price ) );
              } else {
                $initial_price = wp_strip_all_tags( $product->get_price_html() );
              }
            }
            ?>

            <p class="melee-price"><?php echo esc_html( $initial_price ); ?></p>

            <div class="row collection-row">

              <?php 
              $x=0;

              foreach ($attributes as $attr_name => $options) : ?>
                <div class="<?php if(count($attributes)>2 && $x==0){echo 'col-12';}else{echo 'col-6';} ?> ">

                  <label>
                    <?php echo wc_attribute_label($attr_name); ?>
                  </label>

                  <select
                    class="form-select"
                    name="attribute_<?php echo esc_attr(sanitize_title($attr_name)); ?>">

                    <?php
                    if (taxonomy_exists($attr_name)) {

                        $terms = wc_get_product_terms($pid, $attr_name, [
                            'fields' => 'all',
                            'orderby' => 'menu_order'
                        ]);

                        foreach ($terms as $term) {

                            if (!in_array($term->slug, $options)) continue;

                            $selected = selected(
                                $selected_attributes[sanitize_title($attr_name)] ?? '',
                                $term->slug,
                                false
                            );
                            ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" <?php echo $selected; ?>>
                              <?php echo esc_html($term->name); ?>
                            </option>
                            <?php
                        }

                    } else {

                        foreach ($options as $option) {

                            $selected = selected(
                                $selected_attributes[sanitize_title($attr_name)] ?? '',
                                $option,
                                false
                            );
                            ?>
                            <option value="<?php echo esc_attr($option); ?>" <?php echo $selected; ?>>
                              <?php echo esc_html($option); ?>
                            </option>
                            <?php
                        }

                    }
                    ?>

                  </select>

                </div>
              <?php
              $x++;
            endforeach; ?>

            </div>

            <!-- BUTTON -->
            <a href="<?php echo get_permalink($pid); ?>" class="common_btn w-100">
              View Details
            </a>

          </div>

        </div>

        <div class="melee-product-divider"></div>

        <?php endforeach; ?>
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

<script type="text/javascript">
(function(){
  document.querySelectorAll('.melee-product-info').forEach(function(prod){

    const variations = prod.dataset.variations ? JSON.parse(prod.dataset.variations) : [];
    const priceEl = prod.querySelector('.melee-price');
    const selects = prod.querySelectorAll('select[name^="attribute_"]');
    if (!priceEl || !selects.length) return;

    function getSelectedMap(){
      const map = {};
      selects.forEach(function(s){ 
        map[s.name] = s.value; 
      });
      return map;
    }

    function findVariation(selected){
      return variations.find(function(v){
        return Object.keys(v.attributes).every(function(k){
          const selVal = selected[k] || selected[k.replace('attribute_','')] || '';
          return String(v.attributes[k]) === String(selVal);
        });
      });
    }

    function updatePrice(){
      const selected = getSelectedMap();
      const match = findVariation(selected);

      if (match) {
          const isRoundCalibrated = [
              'round-shape-melee-calibrated-diamonds',
              'old-mine-cut-lab-grown-hpht-diamonds',
              'old-european-cut-melee-lab-grown-diamonds',
          ].includes(prod.dataset.slug);
          const isWholesale = window.themeData.WEBSITE_TYPE === 'WHOLESALE';

          const priceLabel = (isWholesale && isRoundCalibrated)
              ? ' <span> / Per Carat </span>'
              : ' <span> / Per Diamond </span>';

          if (match.plain_price) {
              priceEl.innerHTML = match.plain_price + priceLabel;
          } else if (match.price_html) {
              const tmp = document.createElement('div');
              tmp.innerHTML = match.price_html;
              priceEl.innerHTML = (tmp.textContent || tmp.innerText || '') + priceLabel;
          }
      }
    }

    // OPTIONAL: auto-select first option if nothing is selected
    selects.forEach(function(s){
      if (!s.value && s.options.length > 0) {
        s.selectedIndex = 0;
      }
    });

    // Bind change events
    selects.forEach(function(s){ 
      s.addEventListener('change', updatePrice); 
    });

    // Trigger on page load
    updatePrice();

  });
})();
</script>
<?php
get_footer();
