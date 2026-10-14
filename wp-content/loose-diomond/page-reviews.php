<?php

/**
 * Template Name: Reviews
 */

get_header();
global $common_page;
?>

<!-- offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasExampleLabel">MENU</h5>
      <button class="offcanvas-close" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="offcanvas-nav">
      <div class="accordion" id="headeraccordionExample">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              LAB DIAMONDS
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#headeraccordionExample">
            <div class="accordion-body main-accordion">
              <div class="accordion" id="labDiamondAccordion">
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondOne" aria-expanded="true" aria-controls="labDiamondOne">
                      SHOP BY SHAPE
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="labDiamondOne" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['left_shapes']) && is_array($common_page['left_shapes'])): ?>
                          <?php foreach ($common_page['left_shapes'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($common_page['right_shapes']) && is_array($common_page['right_shapes'])): ?>
                          <?php foreach ($common_page['right_shapes'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondTwo" aria-expanded="true" aria-controls="labDiamondTwo">
                      Shop By Style
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="labDiamondTwo" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['carat_diamonds']) && is_array($common_page['carat_diamonds'])): ?>
                          <?php foreach ($common_page['carat_diamonds'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondThree" aria-expanded="true" aria-controls="labDiamondThree">
                      SHOP BY PRICE
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="labDiamondThree" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['shop_by_price']) && is_array($common_page['shop_by_price'])): ?>
                          <?php foreach ($common_page['shop_by_price'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondFour" aria-expanded="true" aria-controls="labDiamondFour">
                      FANCY COLOR DIAMONDS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="labDiamondFour" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['left_color_diamonds']) && is_array($common_page['left_color_diamonds'])): ?>
                          <?php foreach ($common_page['left_color_diamonds'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                         <?php if (!empty($common_page['right_color_diamonds']) && is_array($common_page['right_color_diamonds'])): ?>
                          <?php foreach ($common_page['right_color_diamonds'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondFive" aria-expanded="true" aria-controls="labDiamondFive">
                      POPULAR COLLECTIONS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="labDiamondFive" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <li><a href="/melee-diamond">Melee Diamonds</a></li>
                        <li><a href="/contact-us">Custom Cut Diamonds</a></li>
                        <li><a href="/lab-diamonds/sale">Diamonds On Sale</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
                              
                                echo '<p class="text-white px-0 my-2 lh-1">
              Save 30% on Lab Diamonds with code
              <span class="tomato-color cursor">hallo30</span>
            </p>';
                              
                              ?>
            
            <a href="javascript:void(0)" class="promo-shop-link mb-2 d-block">Shop Now</a>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
              ENGAGEMENT RINGS
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </h2>
          <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#headeraccordionExample">
            <div class="accordion-body main-accordion">
              <div class="accordion" id="accordionEnagagement">
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#enagagementOne" aria-expanded="true" aria-controls="enagagementOne">
                      Create Your Diamond Ring
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="enagagementOne" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <li><a href="/build-a-ring/">Start With A Setting</a></li>
                        <li><a href="">Bespoke Jewelry</a></li>
                        <li><a href="/product-category/ready-to-ship-engagement-ring/">Ready To Ship Engagement Rings</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#enagagementTwo" aria-expanded="true" aria-controls="enagagementTwo">
                      Shop By Style
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="enagagementTwo" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['left_ring_styles']) && is_array($common_page['left_ring_styles'])): ?>
                          <?php foreach ($common_page['left_ring_styles'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($common_page['right_ring_styles']) && is_array($common_page['left_ring_styles'])): ?>
                          <?php foreach ($common_page['right_ring_styles'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#enagagementThree" aria-expanded="true" aria-controls="enagagementThree">
                      Shop By Shape
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="enagagementThree" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['left_ring_shapes']) && is_array($common_page['left_ring_shapes'])): ?>
                          <?php foreach ($common_page['left_ring_shapes'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                         <?php if (!empty($common_page['right_ring_shapes']) && is_array($common_page['right_ring_shapes'])): ?>
                          <?php foreach ($common_page['right_ring_shapes'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
              FINE JEWELRY
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </h2>
          <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#headeraccordionExample">
            <div class="accordion-body main-accordion">
              <div class="accordion" id="accordionFineJewelery">
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#FineOne" aria-expanded="true" aria-controls="FineOne">
                      Earrings
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="FineOne" class="accordion-collapse collapse" data-bs-parent="#accordionFineJewelery">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['fine_earrings']) && is_array($common_page['fine_earrings'])): ?>
                          <?php foreach ($common_page['fine_earrings'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#FineTwo" aria-expanded="true" aria-controls="FineTwo">
                      Wedding Rings
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="FineTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFineJewelery">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                         <?php if (!empty($common_page['wedding_rings']) && is_array($common_page['wedding_rings'])): ?>
                          <?php foreach ($common_page['wedding_rings'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#FineThree" aria-expanded="true" aria-controls="FineThree">
                      Pendants
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="FineThree" class="accordion-collapse collapse" data-bs-parent="#accordionFineJewelery">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['pendants']) && is_array($common_page['pendants'])): ?>
                          <?php foreach ($common_page['pendants'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#FineFour" aria-expanded="true" aria-controls="FineFour">
                      Bracelets
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="FineFour" class="accordion-collapse collapse" data-bs-parent="#accordionFineJewelery">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['bracelets']) && is_array($common_page['bracelets'])): ?>
                          <?php foreach ($common_page['bracelets'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#FineFive" aria-expanded="true" aria-controls="FineFive">
                      Necklaces
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </h2>
                  <div id="FineFive" class="accordion-collapse collapse" data-bs-parent="#accordionFineJewelery">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                         <?php if (!empty($common_page['necklaces']) && is_array($common_page['necklaces'])): ?>
                          <?php foreach ($common_page['necklaces'] as $item): ?>
                            <li>
                              <a href="<?= esc_url($item['url']); ?>">
                                <?= esc_html($item['name']); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseOne">
              <a href="">READY TO SHIP</a>
            </button>
          </h2>
        </div>
      </div>
    </div>
  </div>

  <div class="banner_section reviews-banner">
    <div class="container-fluid">
      <div class="banner_text text-center">
        <h1>LOOSE GROWN DIAMOND REVIEWS</h1>
        <p class="reviews-sub-title">
          Reflections From Our Satisfied Clients
        </p>
      </div>
    </div>
  </div>

  <div class="more-than-reviews">
    <div class="container">
      <?php
      $google_rating     = (int) str_replace(',', '', get_field('google_rating', 'option'));
      $trustpilot_rating = (int) str_replace(',', '', get_field('trustpilot_rating', 'option'));

      $total_reviews = number_format($google_rating + $trustpilot_rating);
      ?>
      <p class="text-center morethan">
        More than <b><?php echo $total_reviews;?> five-star</b> reviews from real users
      </p>
      <div class="row">
        <div class="col-md-6">
          <div class="reviews-count-card">
            <div class="main-counts">
              <div>
                <h2><?php echo  get_field('trustpilot_rating', 'option'); ?></h2>
                <p class="mb-0">Reviews</p>
              </div>
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/trustpilot-reviews.svg" alt="trustpilot-reviews" class="img-fluid" />
            </div>
            <div class="text-center">
              <a href="https://www.trustpilot.com/review/www.loosegrowndiamond.com" class="common_btn reviews_btn">
                See More
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="reviews-count-card">
            <div class="main-counts">
              <div>
                <h2><?php echo  get_field('google_rating', 'option'); ?></h2>
                <p class="mb-0">Reviews</p>
              </div>
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/google-reviews.svg" alt="google-reviews" class="img-fluid" />
            </div>
            <div class="text-center">
              <a href="https://g.page/r/CfgPY3z14oWaEAE" class="common_btn reviews_btn">
                See More
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="percentage-review">
        <div class="review-stars">
          <h2>99.9%</h2>
           <div class="oc-reviews gap-1">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/review-star.svg" alt="review-star.svg" />
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/review-star.svg" alt="review-star.svg" />
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/review-star.svg" alt="review-star.svg" />
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/review-star.svg" alt="review-star.svg" />
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/review-star.svg" alt="review-star.svg" />
              </div>
        </div>
        <p class="five-stars-review">Five star user reviews</p>
      </div>
    </div>
  </div>

  <div class="latest-reviews">
    <div class="container">
      <h2 class="text-center section-title">
        Latest reviews from users like you
      </h2>
      <div class="max-w-1140">
        <?php
          global $wpdb;
$gbp_table = $wpdb->prefix . 'gbp_reviews';
 
// How many reviews to show here, and only show reviews with this rating or higher.
$gbp_limit      = 9;
$gbp_min_rating = 4;
 
$latest_reviews_raw = $wpdb->get_results(
  $wpdb->prepare(
    "SELECT * FROM {$gbp_table} WHERE rating >= %d ORDER BY review_time DESC, id DESC LIMIT %d",
    $gbp_min_rating,
    $gbp_limit
  ),
  ARRAY_A
);
 
$img_base = esc_url( get_template_directory_uri() ) . '/assets/image/reddit/';
 
// Google's Business Profile place link for this location (used for the "Read more" button
// and the reviewer name link, since individual review permalinks aren't exposed by the API).
// Replace with your actual Place ID reviews URL, or leave blank to hide the link/button.
$gbp_place_reviews_url = ''; // e.g. 'https://www.google.com/maps/place/?q=place_id:ChIJxxxxxxxxxxxxxxxx';
        ?>
        
<?php if ( ! empty( $latest_reviews_raw ) ) : ?>
<div class="row">
  <?php foreach ( $latest_reviews_raw as $row ) :
    $name    = $row['author'] ? $row['author'] : 'Anonymous';
    $avatar  = $row['avatar']; // full remote URL as stored by the plugin
    $stars   = (int) $row['rating'];
    $text    = $row['comment'];
    $photos  = json_decode( $row['photos'], true );
    $photos  = is_array( $photos ) ? $photos : array();
 
    $time_label = '';
    if ( ! empty( $row['review_time'] ) ) {
      $ts = strtotime( $row['review_time'] );
      if ( $ts ) {
        $time_label = human_time_diff( $ts, current_time( 'timestamp' ) ) . ' ago';
      }
    }
  ?>
  <div class="col-lg-4">
    <div class="review-card">
      <div class="review-details">
        <div class="reviewer-info">
          <div class="reviewer-image">
            <?php if ( $avatar ) : ?>
              <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
            <?php endif; ?>
          </div>
          <div>
            <?php if ( $gbp_place_reviews_url ) : ?>
              <a href="<?php echo esc_url( $gbp_place_reviews_url ); ?>" target="_blank" rel="noopener" class="reviewer-name"><?php echo esc_html( $name ); ?></a>
            <?php else : ?>
              <span class="reviewer-name"><?php echo esc_html( $name ); ?></span>
            <?php endif; ?>
            <p class="review-time"><?php echo esc_html( $time_label ); ?></p>
          </div>
        </div>
        <img src="<?php echo $img_base; ?>google-icon.svg" alt="google-icon" class="google-icon" />
      </div>
 
      <div class="review-verified">
        <div class="review-stars">
          <?php for ( $i = 0; $i < $stars; $i++ ) : ?>
            <img src="<?php echo $img_base; ?>review-star.svg" alt="review-star" />
          <?php endfor; ?>
        </div>
        <img src="<?php echo $img_base; ?>ti-verified.svg" alt="ti-verified" />
      </div>
 
      <p class="review-statement"><?php echo esc_html( wp_trim_words( $text, 60 ) ); ?></p>
 
      <?php if ( $gbp_place_reviews_url ) : ?>
        <a target="_blank" rel="noopener" href="<?php echo esc_url( $gbp_place_reviews_url ); ?>" class="read-more-btn">Read more</a>
      <?php endif; ?>
 
      <?php if ( ! empty( $photos ) ) : ?>
      <div class="product-list" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        <?php
        $total   = count( $photos );
        $visible = min( $total, 4 );
        $extra   = $total > 4 ? $total - 4 : 0;
        for ( $i = 0; $i < $visible; $i++ ) :
        ?>
        <div class="product-image">
          <img src="<?php echo esc_url( $photos[ $i ] ); ?>" alt="review-product-image" />
          <?php if ( $extra > 0 && $i === $visible - 1 ) : ?>
            <div class="overlay-main">+<?php echo (int) $extra; ?></div>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else : ?>
  <p><?php esc_html_e( 'No reviews yet.', 'your-textdomain' ); ?></p>
<?php endif; ?>

      </div>
      <div class="text-center mt-3">
      <a href="https://g.page/r/CfgPY3z14oWaEAE" target="_blank" class="common_btn reviews_btn mt-2">
                View More
              </a>
            </div>

      <p class="google-review-score text-center">
        <span class="fw-bold">Google</span>
        rating score:
        <span class="fw-bold"> 5.0 </span>
        of 5, based on <a class="fw-bold"><?php echo  get_field('google_rating', 'option'); ?> reviews</a>
      </p>
    </div>
  </div>

  <div class="reviews-faq">
    <div class="container second_section">
      <h2 class="text_black_secondary fw-600 mb_25 text-center">FAQ's</h2>
      <div class="max-w-1140">
        <div class="faq_list">
          <div class="accordion accordion-order" id="accordionorderExample">
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#order-collapseOne" aria-expanded="false" aria-controls="order-collapseOne">
                  Why should I leave a review on Google or Trustpilot?
                </button>
              </h3>
              <div id="order-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                  <p>
                    Leaving a review on Google or Trustpilot helps us improve our products and services. It also helps future customers make informed purchasing decisions.
                  </p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#order-collapseTwo" aria-expanded="false" aria-controls="order-collapseTwo">
                  How do I leave a review for Loose Grown Diamond?
                </button>
              </h3>
              <div id="order-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                  You can leave a review by visiting our Review page. Review links are also emailed to all customers after they complete a purchase.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#order-collapseThree" aria-expanded="false" aria-controls="order-collapseThree">
                  Are reviews on Loose Grown Diamond verified?
                </button>
              </h3>
              <div id="order-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                  Yes. All reviews are verified based on actual purchases. Review links are sent only to customers after they complete a purchase, ensuring that all reviews come from genuine buyers.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#order-collapseFour" aria-expanded="false" aria-controls="order-collapseFour">
                  How can I submit a photo or video with my review?
                </button>
              </h3>
              <div id="order-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                  When submitting your review, you'll see an option to upload photos or videos. Sharing images or videos of your purchase helps other customers make informed buying decisions.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#order-collapseFive" aria-expanded="false" aria-controls="order-collapseFive">
                  How are reviews rated?
                </button>
              </h3>
              <div id="order-collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                  Reviews are rated on a 5-star scale, with 5 stars being the highest rating. You can rate the overall product, diamond quality, customer service, packaging, shipping, and your overall shopping experience.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
get_footer();
