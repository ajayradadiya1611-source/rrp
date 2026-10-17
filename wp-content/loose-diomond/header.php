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

?>
  <!-- Google Tag Manager -->
<!-- <script>!function(){"use strict";function l(e){for(var t=e,r=0,n=document.cookie.split(";");r<n.length;r++){var o=n[r].split("=");if(o[0].trim()===t)return o[1]}}function s(e){return localStorage.getItem(e)}function u(e){return window[e]}function A(e,t){e=document.querySelector(e);return t?null==e?void 0:e.getAttribute(t):null==e?void 0:e.textContent}var e=window,t=document,r="script",n="dataLayer",o="https://tracking.loosegrowndiamond.com",a="",i="erpdmsijm",c="8=BA5UNDkpXSIgLDkwWlFWBFBYR0JLAB5PAQUGAAgDAh0aBA0aDAkfHAlEChwA",g="cookie",v="server_side_tagging",E="",d=!1;try{var d=!!g&&(m=navigator.userAgent,!!(m=new RegExp("Version/([0-9._]+)(.Mobile)?.*Safari.").exec(m)))&&16.4<=parseFloat(m[1]),f="stapeUserId"===g,I=d&&!f?function(e,t,r){void 0===t&&(t="");var n={cookie:l,localStorage:s,jsVariable:u,cssSelector:A},t=Array.isArray(t)?t:[t];if(e&&n[e])for(var o=n[e],a=0,i=t;a<i.length;a++){var c=i[a],c=r?o(c,r):o(c);if(c)return c}else console.warn("invalid uid source",e)}(g,v,E):void 0;d=d&&(!!I||f)}catch(e){console.error(e)}var m=e,g=(m[n]=m[n]||[],m[n].push({"gtm.start":(new Date).getTime(),event:"gtm.js"}),t.getElementsByTagName(r)[0]),v=I?"&bi="+encodeURIComponent(I):"",E=t.createElement(r),f=(d&&(i=8<i.length?i.replace(/([a-z]{8}$)/,"kp$1"):"kp"+i),!d&&a?a:o);E.async=!0,E.src=f+"/"+i+".js?"+c+v,null!=(e=g.parentNode)&&e.insertBefore(E,g)}();</script> -->
<!-- End Google Tag Manager -->




<?php

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

  ?>
  <!-- Google Tag Manager (noscript) -->
<!-- <noscript><iframe src="https://tracking.loosegrowndiamond.com/ns.html?id=GTM-PMFPC75" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
<!-- End Google Tag Manager (noscript) -->
  <?php

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
<?php
// Main menu (desktop + mobile share this). Only these items are shown.
$oc_main_menu = array(
    array(
        'label' => 'Natural Diamonds',
        'url'   => '#',
        'children' => array(
            array('name' => 'Melee Diamonds',   'url' => '/product/calibrated-diamonds/'),
            array('name' => 'Mix Lot Diamonds', 'url' => '/product/melee-diamonds/'),
            array('name' => 'Fancy Diamonds',   'url' => '/fancy-shape-white-diamonds/'),
        ),
    ),
    array('label' => 'Black Diamonds', 'url' => '/product-category/black-diamonds/'),
    array(
        'label' => 'Antique Diamonds',
        'url'   => '/antique-diamond/',
        'children' => array(
            array('name' => 'Salt and Pepper Diamonds',       'url' => '/product-category/salt-and-pepper-diamonds/'),
            array('name' => 'ICY Diamonds',                   'url' => '/product-category/natural-icy-diamonds/'),
            array('name' => 'Rose cut Chakri Round Diamonds', 'url' => '/product-category/rose-cut-chakri-round-diamond/'),
            array('name' => 'Polki Slice cut Diamonds',       'url' => '/product-category/polki-slice-cut-diamonds/'),
            array('name' => 'Diamond Beads',                  'url' => '/product-category/diamond-beads/'),
            array('name' => 'Cubes Rough Diamonds',           'url' => '/product-category/cubes-rough-diamonds/'),
            array('name' => 'Fancy Color Melee Diamonds',     'url' => '/fancy-color-melee-diamond/'),
            array('name' => 'Single Cut Diamonds',            'url' => '/product/single-cut-diamond/'),
        ),
    ),
    array('label' => 'Education', 'url' => '/blog/'),
);

?>
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
            src="<?php echo get_template_directory_uri(); ?>/rrp-img/comman/logo.svg"
            alt="loosegrowndiamond_logo" />
        </a>
        <form class="d-flex align-items-center gap-3 d-lg-none" role="search">
          <i class="fa-solid fa-magnifying-glass" id="mobileSearchIcon"></i>
         
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
            <?php foreach ($oc_main_menu as $m): ?>
              <?php if (!empty($m['children'])): ?>
              <li class="nav-item dropdown">
                <a href="<?php echo esc_url($m['url'] === '#' ? '#' : get_site_url() . $m['url']); ?>" class="nav-link dropdown-toggle">
                  <?php echo esc_html($m['label']); ?>
                <i class="fa-solid fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                  <?php foreach ($m['children'] as $c): ?>
                  <li><a class="dropdown-item" href="<?php echo esc_url(get_site_url() . $c['url']); ?>"><?php echo esc_html($c['name']); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
              <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo esc_url($m['url'] === '#' ? '#' : get_site_url() . $m['url']); ?>"><?php echo esc_html($m['label']); ?></a>
              </li>
              <?php endif; ?>
            <?php endforeach; ?>

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
        <?php foreach ($oc_main_menu as $mi => $m): ?>
        <div class="accordion-item">
          <div class="h2 accordion-header">
            <?php if (!empty($m['children'])): ?>
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#mobileMenu<?php echo $mi; ?>" aria-expanded="false" aria-controls="mobileMenu<?php echo $mi; ?>">
              <?php echo esc_html(strtoupper($m['label'])); ?>
              <i class="fa-solid fa-angle-down"></i>
            </button>
            <?php else: ?>
            <button class="accordion-button collapsed" type="button" aria-expanded="true">
              <a href="<?php echo esc_url($m['url'] === '#' ? '#' : get_site_url() . $m['url']); ?>" class="nav-link"><?php echo esc_html(strtoupper($m['label'])); ?></a>
            </button>
            <?php endif; ?>
          </div>
          <?php if (!empty($m['children'])): ?>
          <div id="mobileMenu<?php echo $mi; ?>" class="accordion-collapse collapse" data-bs-parent="#headeraccordionExample">
            <div class="accordion-body">
              <ul class="mb-0 ps-0">
                <?php foreach ($m['children'] as $c): ?>
                <li><a href="<?php echo esc_url(get_site_url() . $c['url']); ?>"><?php echo esc_html($c['name']); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

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
                  src="<?php echo get_template_directory_uri(); ?>/rrp-img/comman/logo.svg"
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