<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Loose_Diomond
 */
global $text_change;
$common_page = $text_change['common_page'];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" media="print" onload="this.media='all';this.onload=null;">

<style id="fa-font-display-fix">
  /* Font Awesome 6: same files, swap instead of block */
  @font-face {
    font-family: "Font Awesome 6 Free";
    font-style: normal;
    font-weight: 900;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-solid-900.woff2) format("woff2");
  }
  @font-face {
    font-family: "Font Awesome 6 Free";
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-regular-400.woff2) format("woff2");
  }
  @font-face {
    font-family: "Font Awesome 6 Brands";
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-brands-400.woff2) format("woff2");
  }

  /* v4/v5 compatibility families (the ones in your snippet) */
  @font-face {
    font-family: "FontAwesome";
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-solid-900.woff2) format("woff2");
  }
  @font-face {
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-solid-900.woff2) format("woff2");
  }
  @font-face {
    font-family: "Font Awesome 5 Free";
    font-weight: 400;
    font-display: swap;
    src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-regular-400.woff2) format("woff2");
  }
</style>

  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/13.1.5/nouislider.css" media="print" onload="this.media='all';this.onload=null;">

 

  <noscript>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/13.1.5/nouislider.css">
  </noscript> -->
  

  <?php wp_head(); ?>

<?php
if(WEBSITE_TYPE=='RETAIL'){
?>
  <!-- Google Tag Manager -->
<!-- <script>!function(){"use strict";function l(e){for(var t=e,r=0,n=document.cookie.split(";");r<n.length;r++){var o=n[r].split("=");if(o[0].trim()===t)return o[1]}}function s(e){return localStorage.getItem(e)}function u(e){return window[e]}function A(e,t){e=document.querySelector(e);return t?null==e?void 0:e.getAttribute(t):null==e?void 0:e.textContent}var e=window,t=document,r="script",n="dataLayer",o="https://tracking.loosegrowndiamond.com",a="",i="erpdmsijm",c="8=BA5UNDkpXSIgLDkwWlFWBFBYR0JLAB5PAQUGAAgDAh0aBA0aDAkfHAlEChwA",g="cookie",v="server_side_tagging",E="",d=!1;try{var d=!!g&&(m=navigator.userAgent,!!(m=new RegExp("Version/([0-9._]+)(.Mobile)?.*Safari.").exec(m)))&&16.4<=parseFloat(m[1]),f="stapeUserId"===g,I=d&&!f?function(e,t,r){void 0===t&&(t="");var n={cookie:l,localStorage:s,jsVariable:u,cssSelector:A},t=Array.isArray(t)?t:[t];if(e&&n[e])for(var o=n[e],a=0,i=t;a<i.length;a++){var c=i[a],c=r?o(c,r):o(c);if(c)return c}else console.warn("invalid uid source",e)}(g,v,E):void 0;d=d&&(!!I||f)}catch(e){console.error(e)}var m=e,g=(m[n]=m[n]||[],m[n].push({"gtm.start":(new Date).getTime(),event:"gtm.js"}),t.getElementsByTagName(r)[0]),v=I?"&bi="+encodeURIComponent(I):"",E=t.createElement(r),f=(d&&(i=8<i.length?i.replace(/([a-z]{8}$)/,"kp$1"):"kp"+i),!d&&a?a:o);E.async=!0,E.src=f+"/"+i+".js?"+c+v,null!=(e=g.parentNode)&&e.insertBefore(E,g)}();</script> -->
<!-- End Google Tag Manager -->




<?php
}else{
?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WLZLSKP8');</script>
    <!-- End Google Tag Manager -->
<?php
}
?>
 

</head>




  <?php 
  $myaccount_link = is_user_logged_in() ? home_url('/my-account/orders/') : home_url('/my-account/');
   $is_checkout =false;
  if(is_checkout()   && ! is_order_received_page() ){
    $is_checkout = true;
  } 
  ?>

<body <?php body_class(); ?>>
  <!-- Google Tag Manager (noscript) -->
  <?php
if(WEBSITE_TYPE=='RETAIL'){
  ?>
  <!-- Google Tag Manager (noscript) -->
<!-- <noscript><iframe src="https://tracking.loosegrowndiamond.com/ns.html?id=GTM-PMFPC75" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
<!-- End Google Tag Manager (noscript) -->
  <?php
}else{
?>
<!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLZLSKP8"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
<!-- End Google Tag Manager (noscript) -->
<?php
}
?>
  <?php wp_body_open(); ?>
  <!-- top bar section -->
  <?php
  if($is_checkout==false){

  
  ?>
  <div class="top_bar d-none d-md-block">
    <div class="container-fluid">
      <div class="top_bar_slider">
        <ul>
          <?php
          foreach ($common_page['top_bar'] as $index => $item) {

              $style = ($index != 0) ? 'style="display:none;"' : '';

              echo "<li {$style}>$item</li>";
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="top_bar d-block d-md-none">
    <div class="container-fluid">
      <div class="top_bar_slider">
        <ul>
          <?php
          foreach ($common_page['top_bar_mobile'] as $index => $item) {

              $style = ($index != 0) ? 'style="display:none;"' : '';

              echo "<li {$style}>$item</li>";
          }
          ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- start header section -->
  <header class="header_wrap">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <button
          class="navbar-toggler p-0 border-0"
          type="button"
          data-bs-target="#offcanvasExample"
          data-bs-toggle="offcanvas"
          aria-controls="#offcanvasExample">
          <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?php echo home_url(); ?>">
          <img
            src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
            alt="loosegrowndiamond_logo" />
        </a>
        <form class="d-flex align-items-center gap-3 d-lg-none" role="search">
          <i class="fa-solid fa-magnifying-glass" id="mobileSearchIcon"></i>
         <!--  <div class="d-flex flex-column gap-1">
            <a href="<?php echo esc_url($myaccount_link); ?>" class="text-decoration-none">
                  <div class="account-avatar <?php if ( is_user_logged_in() ) {echo "account_logged";}else{echo "account_not_logged";}?>"> 
                      <?php
                      if ( is_user_logged_in() ) {
                          $current_user = wp_get_current_user();
                          echo strtoupper( substr( $current_user->display_name, 0, 1 ) );
                      } else {
                          ?>
                          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/header/user.svg" alt="User">
                          <?php
                      }
                      ?>
                  </div>
                </a>
          </div> -->
          <a href="<?php echo home_url(); ?>/wishlist/"><i class="fa-heart fa-regular fa-lg"></i></a>
          <a href="<?php echo wc_get_cart_url(); ?>" class="position-relative cart-badge">
            <img
              src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/shopping_cart.svg"
              alt="shopping_cart" />
            <span class="cart-count">
              <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
          </a>
        </form>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown dropdown-mega position-static">
              <a
                class="nav-link dropdown-toggle"
                href="<?php echo $text_change['inventory_url'] ; ?>"
                >
                Lab Diamonds
                <i class="fa-solid fa-angle-down ms-1"></i>
              </a>
              <div class="dropdown-menu lab-diamonds-mega-menu">
                <div class="mega_content">
                  <div class="container-fluid">
                    <div class="d-flex gap-4 justify-content-between">

                      <div class="lab-shape-section">
                        <p>SHOP BY SHAPE</p>
                        <div class="row">
                          <div class="col-6 pe-0">
                            <ul class="text_widget mb-0 ds-left_shapes">
                              <?php foreach ($common_page['left_shapes'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <div>
                                        <img src="<?= $item['image']; ?>" 
                                             alt="<?= strtolower($item['name']); ?>" 
                                             class="img-fluid" loading="lazy" />
                                      </div>
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                            </ul>
                          </div>
                          <div class="col-6 ps-0">
                            <ul class="text_widget mb-0  ds-right_shapes">
                              <?php foreach ($common_page['right_shapes'] as $item): ?>
                                <li>
                                  <a href="<?= $item['url']; ?>">
                                    <div>
                                      <img src="<?= $item['image']; ?>" 
                                           alt="<?= strtolower($item['name']); ?>" 
                                           class="img-fluid" loading="lazy" />
                                    </div>
                                    <?= $item['name']; ?>
                                  </a>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <div class="border-right"></div>
                      <div class="">
                        <p>SHOP BY CARAT</p>
                        <ul class="text_widget mb-0 lab-simple-list">
                          <?php foreach ($common_page['carat_diamonds'] as $item): ?>
                            <li>
                              <a href="<?= $item['url']; ?>">
                                <?= $item['name']; ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="border-right"></div>
                      <div class="">
                        <p>SHOP BY PRICE</p>
                        <ul class="text_widget mb-0 lab-simple-list">
                          <?php foreach ($common_page['shop_by_price'] as $item): ?>
                              <li>
                                <a href="<?= $item['url']; ?>">
                                  <?= $item['name']; ?>
                                </a>
                              </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="border-right"></div>
                      <div class="">
                        <p>FANCY COLOR DIAMONDS</p>
                        <div class="row">
                          <div class="col-6 pe-0">
                            <ul class="text_widget mb-0 ds-left_shapes">
                              <?php foreach ($common_page['left_color_diamonds'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <img src="<?= $item['image']; ?>" 
                                           alt="<?= strtolower($item['name']); ?> diamond"
                                           class="img-fluid" loading="lazy" />
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                            </ul>
                          </div>
                          <div class="col-6 ps-0">
                            <ul class="text_widget mb-0 ds-right_shapes">
                              <?php foreach ($common_page['right_color_diamonds'] as $item): ?>
                                <li>
                                  <a href="<?= $item['url']; ?>">
                                    <img src="<?= $item['image']; ?>" 
                                         alt="<?= strtolower($item['name']); ?> diamond"
                                         class="img-fluid" loading="lazy" />
                                    <?= $item['name']; ?>
                                  </a>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <div class="border-right"></div>
                      <!-- <div class="d-flex gap-3 pe-3"> -->
                        <div>
                          <p>POPULAR COLLECTIONS</p>
                          <ul class="text_widget mb-0 lab-simple-list">
                            <li><a href="/melee-diamond">Melee Diamonds</a></li>
                            <li><a href="/contact-us">Custom Cut Diamonds</a></li>
                            <li>
                              <a href="/lab-diamonds/?is_sale=1" class="tomato-color pt-0">
                                Diamonds On Sale <img src="<?php echo get_template_directory_uri(); ?>/assets/image/header/fire.gif" alt="" loading="lazy">
                              </a>
                            </li>
                          </ul>
                        </div>
                      <div class="border-right"></div>

                        <div class="lab-promo-card pe-3">
                          <a href="<?php echo $text_change['inventory_url']; ?>" class="text-decoration-none" >
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/image/header/lab-diamond-promo.png" alt="Lab Diamonds Promo"
                              class="lab-promo-img" loading="lazy" />
                              <?php
                              if(WEBSITE_TYPE=='RETAIL'){
                                echo '<p class="px-0 my-2 lh-sm "  class="">Save 30% on Lab Diamonds with code <span
                                class="tomato-color cursor">hallo30</span></p>';
                              }
                              ?>
                            
                          </a>
                          <a href="<?php echo $text_change['inventory_url']; ?>" class="promo-shop-link">Shop Now</a>
                        </div>
                      <!-- </div> -->

                    </div>
                  </div>
                </div>
              </div>
              
            </li>
            <?php
             if(WEBSITE_TYPE=='WHOLESALE'){
            ?>
            <li class="nav-item">
              <a
                class="nav-link"
                href="<?php echo $text_change['inventory_url'] ; ?>"
                >
                INVENTORY
              </a>
            </li>
            <?php
          }
            ?>
            <li class="nav-item dropdown dropdown-mega position-static">
              <a
                class="nav-link dropdown-toggle"
                href="<?php echo get_site_url(); ?>/lab-grown-diamond-engagement-rings/"
                >
                Engagement rings
                <i class="fa-solid fa-angle-down ms-1"></i>
              </a>
              <div class="dropdown-menu">
                <div class="mega_content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-3 border-right">
                        <p class="text-uppercase">Create Your Diamond Ring</p>
                        <ul class="text_widget mb-3 mb-lg-0">
                          <li>
                            <a href="<?php echo get_site_url(); ?>/build-a-ring/">Start with a Setting</a>
                          </li>
                          <li>
                            <a href="<?php echo get_site_url(); ?>/lab-diamonds/">Start with a Diamond</a>
                          </li>
                          <li>
                            <a href="<?php echo get_site_url(); ?>/custom-engagement-rings/">Custom Engagement Ring</a>
                          </li>
                          <li>
                            <a href="/ready-to-ship/engagement-rings">Ready To Ship Engagement Ring<div>
                                        <img
                                             src="<?php echo get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg';?>"
                                             class="img-fluid menutruckicon" loading="lazy" />
                                      </div></a>
                          </li>
                        </ul>
                      </div>
                      <div class="col-4 border-right">
                        <p class="text-uppercase">Shop By Style</p>
                        <div class="row">
                          <div class="col-6 pe-0">
                            <ul class="text_widget mb-3 mb-lg-0">
                              <?php foreach($common_page['left_ring_styles'] as $style){ ?>

                              <li>
                              <a href="<?= $style['url'] ?>">
                              <div>
                              <img src="<?= $style['image'] ?>" alt="<?= strtolower($style['name']) ?>" class="img-fluid" loading="lazy">
                              </div>
                              <?= $style['name'] ?>
                              </a>
                              </li>

                              <?php } ?>
                            </ul>
                          </div>
                          <div class="col-md-6 ps-0">
                            <ul class="text_widget mb-3 mb-lg-0">
                                                            
                              <?php foreach($common_page['right_ring_styles'] as $style){ ?>

                              <li>
                              <a href="<?= $style['url'] ?>">
                              <div>
                              <img src="<?= $style['image'] ?>" alt="<?= strtolower($style['name']) ?>" class="img-fluid" loading="lazy">
                              </div>
                              <?= $style['name'] ?>
                              </a>
                              </li>

                              <?php } ?>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <div class="col-4 border-right">
                        <p class="text-uppercase">Shop By Shape</p>
                        <div class="row">
                          <div class="col-md-6">
                            <ul class="text_widget mb-3 mb-lg-0 ds-left_shapes">
                              <?php foreach ($common_page['left_ring_shapes'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <div>
                                        <img src="<?= $item['image']; ?>" 
                                             alt="<?= strtolower($item['name']); ?>" 
                                             class="img-fluid" loading="lazy" />
                                      </div>
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                            </ul>
                          </div>
                          <div class="col-md-6">
                            <ul class="text_widget mb-3 mb-lg-0 ds-right_shapes">
                              <?php foreach ($common_page['right_ring_shapes'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <div>
                                        <img src="<?= $item['image']; ?>" 
                                             alt="<?= strtolower($item['name']); ?>" 
                                             class="img-fluid" loading="lazy" />
                                      </div>
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown dropdown-mega position-static">
              <a
                class="nav-link dropdown-toggle"
                href="<?php echo get_site_url(); ?>/fine-jewelry/"
               >
                Fine Jewelry
                <i class="fa-solid fa-angle-down ms-1"></i>
              </a>
              <div class="dropdown-menu">
                <div class="mega_content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-sm-6 col-lg-3 border-right">
                        <p>
                          <a href="<?php echo get_site_url(); ?>/lab-diamond-earrings/" class="text-decoration-none">
                            EARRINGS
                          </a>
                        </p>
                        <ul class="text_widget mb-3 mb-lg-0 oc-fine-sub-menu">
                          <?php if (!empty($common_page['fine_earrings']) && is_array($common_page['fine_earrings'])): ?>
                            <?php foreach ($common_page['fine_earrings'] as $item): ?>
                              <li>
                                <a href="<?= esc_url($item['url']); ?>">
                                  <div>
                                    <img
                                      src="<?= esc_url($item['image']); ?>"
                                      alt="<?= esc_attr(strtolower(str_replace(' ', '-', $item['name']))); ?>"
                                      class="img-fluid" loading="lazy" />
                                  </div>
                                  <?= esc_html($item['name']); ?>
                                </a>
                              </li>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </ul>
                      </div>
                      <div class="col-sm-6 col-lg-3 border-right">
                        <p>
                          <a href="<?php echo get_site_url(); ?>/lab-diamond-wedding-bands/" class="text-decoration-none">
                            WEDDING BANDS 
                          </a>
                        </p>
                        <ul class="text_widget mb-3 mb-lg-0 oc-fine-sub-menu">
                          <?php if (!empty($common_page['wedding_rings']) && is_array($common_page['wedding_rings'])): ?>
                            <?php foreach ($common_page['wedding_rings'] as $item): ?>
                              <li>
                                <a href="<?= esc_url($item['url']); ?>">
                                  <div>
                                    <img
                                      src="<?= esc_url($item['image']); ?>"
                                      alt="<?= esc_attr(strtolower(str_replace(' ', '-', $item['name']))); ?>"
                                      class="img-fluid" loading="lazy" />
                                  </div>
                                  <?= esc_html($item['name']); ?>
                                </a>
                              </li>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </ul>
                      </div>
                      <div class="col-sm-6 col-lg-3 border-right">
                        <p>
                          <a href="<?php echo get_site_url(); ?>/lab-diamond-pendants/" class="text-decoration-none">
                            PENDANTS
                          </a>
                        </p>
                        <ul class="text_widget mb-3 mb-lg-0 oc-fine-sub-menu">
                          <?php if (!empty($common_page['pendants']) && is_array($common_page['pendants'])): ?>
                            <?php foreach ($common_page['pendants'] as $item): ?>
                              <li>
                                <a href="<?= esc_url($item['url']); ?>">
                                  <div>
                                    <img src="<?= esc_url($item['image']); ?>" alt="<?= esc_attr(strtolower(str_replace(' ', '-', $item['name']))); ?>" class="img-fluid" loading="lazy" />
                                  </div>
                                  <?= esc_html($item['name']); ?>
                                </a>
                              </li>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </ul>
                      </div>
                      <div class="col-sm-6 col-lg-3 border-right">
                        <p>
                          <a href="<?php echo get_site_url(); ?>/lab-diamond-bracelets/" class="text-decoration-none">
                            BRACELETS
                          </a>
                        </p>
                        <ul class="text_widget mb-3 mb-lg-0 oc-fine-sub-menu">
                          <?php if (!empty($common_page['bracelets']) && is_array($common_page['bracelets'])): ?>
                            <?php foreach ($common_page['bracelets'] as $item): ?>
                              <li>
                                <a href="<?= esc_url($item['url']); ?>">
                                  <div>
                                    <img src="<?= esc_url($item['image']); ?>" alt="<?= esc_attr(strtolower(str_replace(' ', '-', $item['name']))); ?>" class="img-fluid" loading="lazy" />
                                  </div>
                                  <?= esc_html($item['name']); ?>
                                </a>
                              </li>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </ul>
                        <p class="my-3">
                          <a href="<?php echo get_site_url(); ?>/lab-diamond-necklaces/" class="text-decoration-none">
                            NECKLACES
                          </a>
                        </p>
                        <ul class="text_widget mb-3 mb-lg-0 oc-fine-sub-menu">
                          <?php if (!empty($common_page['necklaces']) && is_array($common_page['necklaces'])): ?>
                            <?php foreach ($common_page['necklaces'] as $item): ?>
                              <li>
                                <a href="<?= esc_url($item['url']); ?>">
                                  <div>
                                    <img src="<?= esc_url($item['image']); ?>" alt="<?= esc_attr(strtolower(str_replace(' ', '-', $item['name']))); ?>" class="img-fluid" loading="lazy" />
                                  </div>
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
            </li>
            <li class="nav-item dropdown">
                <a href="<?php echo get_site_url(); ?>/product-category/ready-to-ship/" 
                   class="nav-link dropdown-toggle" 
                >
                    Ready To Ship
                    <i class="fa-solid fa-angle-down ms-1"></i>
                </a>

                <ul class="dropdown-menu">
                    <?php foreach ($common_page['ready_to_ship'] as $item): ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo get_site_url() . $item['url']; ?>">
                                <?php echo $item['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li class="nav-item">
              <a href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#customSettingModal" class="nav-link">CUSTOM</a>
            </li>

          </ul>
          <div
            class="d-lg-flex d-none align-items-center gap-3"
            role="search">
            <div class="position-relative search-box">
              <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
              <input
                class="form-control"
                type="text"
                name="s"
                placeholder="Search"
                aria-label="Search" />
         
                    <i class="fa-solid fa-magnifying-glass oc-lg-search-btn"></i>
                 

                  <input type="hidden" name="post_type" value="product" />
              </form>
            </div>
            <div class="d-flex flex-column gap-1">
           
              <a href="<?php echo esc_url($myaccount_link); ?>" class="text-decoration-none">
                  <div class="account-avatar <?php if ( is_user_logged_in() ) {echo "account_logged";}else{echo "account_not_logged";}?>"> 
                      <?php
                      if ( is_user_logged_in() ) {
                          $current_user = wp_get_current_user();
                          echo strtoupper( substr( $current_user->display_name, 0, 1 ) );
                      } else {
                          ?>
                          <img src="<?php echo get_template_directory_uri(); ?>/aj_custom/header/user.svg" alt="User">
                          <?php
                      }
                      ?>
                  </div>
              </a>
            </div>
            <a href="<?php echo home_url(); ?>/wishlist/"><i class="fa-heart fa-regular fa-lg"></i></a>
            <a href="<?php echo wc_get_cart_url(); ?>" class="position-relative cart-badge">
              <img
                src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/shopping_cart.svg"
                alt="shopping_cart" />
              <span class="cart-count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
              </span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    <div
      class="search-box search-box-mobile"
      id="mobileSearchBox"
      style="display: none">
      <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <input
        class="form-control"
        type="text"
        name="s"
        placeholder="Search"
        aria-label="Search"
        value="" />
      <button
        type="submit"
        id="mobileSearchBtn"
        class="lscartbtn ls_add_to_cart d-flex align-items-center justify-content-center"
        style="white-space: nowrap; cursor: pointer; min-width: 100px">
        Search
      </button>
      <input type="hidden" name="post_type" value="product" />
      </form>
    </div>
  </header>

  <!-- offcanvas -->
  <div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel"
  >
    <div class="offcanvas-header">
      <div class="offcanvas-title" id="offcanvasExampleLabel">MENU</div>
      <button
        class="offcanvas-close"
        data-bs-dismiss="offcanvas"
        aria-label="Close"
      >
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="offcanvas-nav">
      <div class="accordion" id="headeraccordionExample">
        <div class="accordion-item">
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseOne"
              aria-expanded="true"
              aria-controls="collapseOne"
            >
              LAB DIAMONDS
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </div>
          <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#headeraccordionExample">
            <div class="accordion-body main-accordion">
              <div class="accordion" id="labDiamondAccordion">
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondOne" aria-expanded="true" aria-controls="labDiamondOne">
                      SHOP BY SHAPE
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div id="labDiamondOne" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php foreach ($common_page['left_shapes'] as $item): ?>
                          <li>
                            <a href="<?= $item['url']; ?>">
                              <?= $item['name']; ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                        <?php foreach ($common_page['right_shapes'] as $item): ?>
                          <li>
                            <a href="<?= $item['url']; ?>">
                              <?= $item['name']; ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondTwo" aria-expanded="true" aria-controls="labDiamondTwo">
                      SHOP By CARAT
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div id="labDiamondTwo" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php foreach ($common_page['carat_diamonds'] as $item): ?>
                          <li>
                            <a href="<?= $item['url']; ?>">
                              <?= $item['name']; ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondThree" aria-expanded="true" aria-controls="labDiamondThree">
                      SHOP BY PRICE
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div id="labDiamondThree" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php foreach ($common_page['shop_by_price'] as $item): ?>
                              <li>
                                <a href="<?= $item['url']; ?>">
                                  <?= $item['name']; ?>
                                </a>
                              </li>
                          <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondFour" aria-expanded="true" aria-controls="labDiamondFour">
                      FANCY COLOR DIAMONDS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div id="labDiamondFour" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php foreach ($common_page['left_color_diamonds'] as $item): ?>
                          <li>
                            <a href="<?= $item['url']; ?>">
                              <?= $item['name']; ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                        <?php foreach ($common_page['right_color_diamonds'] as $item): ?>
                          <li>
                            <a href="<?= $item['url']; ?>">
                              <?= $item['name']; ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#labDiamondFive" aria-expanded="true" aria-controls="labDiamondFive">
                      POPULAR COLLECTIONS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div id="labDiamondFive" class="accordion-collapse collapse" data-bs-parent="#accordionEnagagement">
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <li><a href="/melee-diamond">Melee Diamonds</a></li>
                        <li><a href="/contact-us">Custom Cut Diamonds</a></li>
                        <li>
                          <a href="/lab-diamonds/?is_sale=1" class="tomato-color">
                                Diamonds On Sale <img src="<?php echo get_template_directory_uri(); ?>/assets/image/header/fire.gif" alt="" loading="lazy">
                              </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
                              if(WEBSITE_TYPE=='RETAIL'){
                                echo '<p class="text-white px-0 my-2 lh-1">Save 30% on Lab Diamonds with code <span
                class="tomato-color cursor">hallo30</span></p>';
                              }
                              ?>
            
            <a href="<?php echo $text_change['inventory_url']; ?>" class="promo-shop-link mb-2 d-block">Shop Now</a>
          </div>
        </div>

<?php
             if(WEBSITE_TYPE=='WHOLESALE'){
            ?>
            <div class="accordion-item">
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              aria-expanded="true"
              aria-controls="collapseOne"
            >
              <a href="<?php echo $text_change['inventory_url'] ; ?>"  class="nav-link">INVENTORY</a>
            </button>
          </div>
        </div>
            <?php
          }
            ?>
           

        <div class="accordion-item">
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseThree"
              aria-expanded="true"
              aria-controls="collapseThree"
            >
              ENGAGEMENT RINGS
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </div>
          <div
            id="collapseThree"
            class="accordion-collapse collapse"
            data-bs-parent="#headeraccordionExample"
          >
            <div class="accordion-body main-accordion">
              <div class="accordion" id="accordionEnagagement">
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed text-uppercase "
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#enagagementOne"
                      aria-expanded="true"
                      aria-controls="enagagementOne"

                    >
                      Create Your Diamond Ring
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="enagagementOne"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionEnagagement"
                  >
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <li><a class="" href="<?php echo get_site_url(); ?>/build-a-ring/">Start With A Setting</a></li>
                        <li>
                            <a href="<?php echo get_site_url(); ?>/lab-diamonds/">Start with a Diamond</a>
                          </li>
                        <li><a href="<?php echo get_site_url(); ?>/custom-engagement-rings/">Custom Engagement Ring</a></li>
                        <li>
                            <a href="/ready-to-ship/engagement-rings" style="color:red;">Ready To Ship Engagement Ring</a>
                          </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed text-uppercase"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#enagagementTwo"
                      aria-expanded="true"
                      aria-controls="enagagementTwo"
                    >
                      Shop By Style
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="enagagementTwo"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionEnagagement"
                  >
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php foreach($common_page['left_ring_styles'] as $style){ ?>

                              <li>
                              <a href="<?= $style['url'] ?>">
                              <?= $style['name'] ?>
                              </a>
                              </li>

                              <?php } ?>
                               <?php foreach($common_page['right_ring_styles'] as $style){ ?>

                              <li>
                              <a href="<?= $style['url'] ?>">
                              <?= $style['name'] ?>
                              </a>
                              </li>

                              <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed text-uppercase"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#enagagementThree"
                      aria-expanded="true"
                      aria-controls="enagagementThree"
                    >
                      Shop By Shape
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="enagagementThree"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionEnagagement"
                  >
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                         <?php foreach ($common_page['left_shapes'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                                <?php foreach ($common_page['right_shapes'] as $item): ?>
                                  <li>
                                    <a href="<?= $item['url']; ?>">
                                      <?= $item['name']; ?>
                                    </a>
                                  </li>
                                <?php endforeach; ?>
                       
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseFour"
              aria-expanded="true"
              aria-controls="collapseFour"
            >
              FINE JEWELRY
              <i class="fa-solid fa-angle-down"></i>
            </button>
          </div>
          <div
            id="collapseFour"
            class="accordion-collapse collapse"
            data-bs-parent="#headeraccordionExample"
          >
            <div class="accordion-body main-accordion">
              <div class="accordion" id="accordionFineJewelery">
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#FineOne"
                      aria-expanded="true"
                      aria-controls="FineOne"
                    >
                      EARRINGS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="FineOne"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionFineJewelery"
                  >
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
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#FineTwo"
                      aria-expanded="true"
                      aria-controls="FineTwo"
                    >
                      WEDDING BAND
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="FineTwo"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionFineJewelery"
                  >
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
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#FineThree"
                      aria-expanded="true"
                      aria-controls="FineThree"
                    >
                      PENDANTS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="FineThree"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionFineJewelery"
                  >
                    <div class="accordion-body">
                      <ul class="mb-0 ps-0">
                        <?php if (!empty($common_page['pendants']) && is_array($common_page['pendants'])): ?>
                          <?php foreach (array_slice($common_page['pendants'], 0, -1) as $item): ?>
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
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      aria-expanded="true"
                      aria-controls="collapseOne"
                    >
                      <a href="/chains"  class="nav-link">CHAINS</a>
                    </button>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#FineFour"
                      aria-expanded="true"
                      aria-controls="FineFour"
                    >
                      BRACELETS
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="FineFour"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionFineJewelery"
                  >
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
                  <div class="h2 accordion-header">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#FineFive"
                      aria-expanded="true"
                      aria-controls="FineFive"
                    >
                      NECKLACES
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <div
                    id="FineFive"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionFineJewelery"
                  >
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
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#m-ready-to-ship"
              aria-expanded="true"
              aria-controls="FineFive"
            >
              READY TO SHIP <i class="fa-solid fa-angle-down"></i>
            </button>
          </div>
          <div
              id="m-ready-to-ship"
              class="accordion-collapse collapse"
              data-bs-parent="#accordionFineJewelery"
            >
              <div class="accordion-body">
                <ul class="mb-0 ps-0">
                  <?php if (!empty($common_page['ready_to_ship']) && is_array($common_page['ready_to_ship'])): ?>
                      <?php foreach ($common_page['ready_to_ship'] as $item): ?>
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
          <div class="h2 accordion-header">
            <button
              class="accordion-button collapsed"
              type="button"
              aria-expanded="true"
              aria-controls="collapseOne"
            >
              <a href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#customSettingModal" class="nav-link">CUSTOM</a>
            </button>
          </div>
        </div>

        <div class="accordion-item">
          <div class="h2 accordion-header">
          <button
              class="accordion-button collapsed"
              type="button"
              aria-expanded="true"
              aria-controls="collapseOne"
            >
              <a href="<?php echo esc_url($myaccount_link); ?>"  class="nav-link"><?php
                      if ( is_user_logged_in() ) {
                          
                          echo "MY ACCOUNT";
                      } else {
                        echo "SIGN IN / UP";
                      }
                      ?></a>
             </button>
          </div>
        </div>


      </div>

   
    </div>
  </div>
  <?php 
  }else{
    ?>
    <div class="top_bar d-block d-md-none">
      <div class="container-fluid">
        <div class="top_bar_slider">
          <ul>
            <?php
            foreach ($common_page['top_bar_checkout_mobile'] as $index => $item) {

                $style = ($index != 0) ? 'style="display:none;"' : '';

                echo "<li {$style}>$item</li>";
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
     <div class="is_checkout_header">
       <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between gap-3">
       
              <a class="navbar-brand" href="<?php echo home_url(); ?>">
                <img
                  src="<?php echo get_template_directory_uri(); ?>/assets/image/logo/loosegrowndiamond_logo.svg"
                  alt="loosegrowndiamond_logo" />
              </a>
              <div class="oc-checkout-mobile">
               <!--  <div class="oc_nhelp">Need help? <a href="tel:+<?php echo SUPPORT_MOBILE?>">+<?php echo SUPPORT_MOBILE?></a></div> -->
                <div class="oc-checkout-mobile-icon">
                  <div>
                    <a href="tel:+<?php echo SUPPORT_MOBILE?>">
                      <img  src="<?php echo get_template_directory_uri(); ?>/aj_custom/checkout/mobile.svg" />
                    </a>
                   </div>
                    <div>
                    <a href="javascript:void(0)" onclick="openZohoChat('Hi : ' + window.location.href)">
                      <img  src="<?php echo get_template_directory_uri(); ?>/aj_custom/checkout/live-chat.svg" />
                    </a>
                    </div>
                    
                   <div>
                    <a href="/cart">
                      <img  src="<?php echo get_template_directory_uri(); ?>/aj_custom/checkout/shopping_cart.svg" />
                    </a>
                  </div>
                </div>
              </div>
            </div>
        </div>
     </div>

    <?php
  }