<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Loose_Diomond
 */
global $text_change;
$home_page = $text_change['home_page'];
$common_page = $text_change['common_page'];

$upload_dir = wp_upload_dir();
$is_checkout =false;
if ( ( is_checkout() || is_cart() ) && ! is_order_received_page() ) {
  $is_checkout = true;
} 
?>

<?php
if($is_checkout == true){
?>
<div class="footer_cart py-3">
    <div class="container-fluid">

      <div class="is_checkout_footer d-flex  flex-column gap-5">
        <div class="d-flex justify-content-center align-items-center flex-wrap aj-footer-logo">
                  
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/banktransfer.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/crypto.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/zelle.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                   
                   
                 
                </div>
                <div class="d-flex justify-content-center align-items-center flex-wrap aj-footer-links-cart">

                    <div>
                        <a href="javascript:void(0)" onclick="openZohoChat('Hi : ' + window.location.href)" class="text-decoration-none">
                            Live Chat
                        </a>
                    </div>

                    <div>
                        <a href="tel:+<?php echo SUPPORT_MOBILE?>" class="text-decoration-none">
                            Call Us
                        </a>
                    </div>

                    <div>
                        <a href="/faq/" class="text-decoration-none">
                            FAQ
                        </a>
                    </div>

                    <div>
                        <a href="/reviews/" class="text-decoration-none">
                            Reviews
                        </a>
                    </div>

                    <!--
                    <div>
                        <a href="/diamond-price-match/" class="text-decoration-none">
                            Price Match Guarantee
                        </a>
                    </div>
                    -->

                    <div>
                        <a href="/shipping/" class="text-decoration-none">
                            Shipping Policy
                        </a>
                    </div>

                    <div>
                        <a href="/return-policy/" class="text-decoration-none">
                            Return Policy
                        </a>
                    </div>

                </div>
                

              <div class="oc_nhelp_copy text-black">© <?php echo SITE_OC_NAME;?> 2026.</div>
         </div>
     </div>
</div>
<?php
}else{
?>
<?php
$footer_class = ( is_front_page() || is_order_received_page() || is_page( 'melee-diamonds-for-sale' ) || is_page( 'natural-diamond-ring' ) )
    ? 'mt-0 mb-md-0'
    : 'mt-5 mb-md-0';
?>
<footer class="<?php echo esc_attr( $footer_class ); ?>">
  
  <div class="accordion footer-accordion d-sm-none" id="footerAccordion">
          <div class="accordion-item">
            <div class="h2 accordion-header">
              <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseOne"
                aria-expanded="false"
                aria-controls="collapseOne"
              >
                SHOP
                <div>
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-plus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-plus"
                    loading="lazy"
                  />
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-minus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-minus"
                    loading="lazy"
                  />
                </div>
              </button>
            </div>
            <div
              id="collapseOne"
              class="accordion-collapse collapse"
              data-bs-parent="#footerAccordion"
            >
              <div class="accordion-body">
                <ul class="widget_links mb-0">
                  <?php foreach ($common_page['desktop_footer_1'] as $item) { ?>
                    <li><a href="<?= $item['url']; ?>" <?php if($item['target']!=''){ echo ' data-bs-toggle="modal" data-bs-target="'.$item['target'].'"';}?>><?= $item['name']; ?></a></li>
                  <?php } ?>
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
                data-bs-target="#collapseTwo"
                aria-expanded="false"
                aria-controls="collapseTwo"
              >
                ABOUT
                <div>
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-plus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-plus"
                    loading="lazy"
                  />
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-minus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-minus"
                    loading="lazy"
                  />
                </div>
              </button>
            </div>
            <div
              id="collapseTwo"
              class="accordion-collapse collapse"
              data-bs-parent="#footerAccordion"
            >
              <div class="accordion-body">
                <ul class="widget_links mb-0">
                  <?php foreach ($common_page['desktop_footer_2'] as $item) { ?>
                  <li><a href="<?= $item['url']; ?>" <?php if($item['target']!=''){ echo ' data-bs-toggle="modal" data-bs-target="'.$item['target'].'"';}?>><?= $item['name']; ?></a></li>
                  <?php } ?>
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
                data-bs-target="#collapseThree"
                aria-expanded="false"
                aria-controls="collapseThree"
              >
                 <?php
              
                echo 'FINANCING & POLICIES';
              
              ?>
                <div>
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-plus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-plus"
                    loading="lazy"
                  />
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-minus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-minus"
                    loading="lazy"
                  />
                </div>
              </button>
            </div>
            <div
              id="collapseThree"
              class="accordion-collapse collapse"
              data-bs-parent="#footerAccordion"
            >
              <div class="accordion-body">
                <ul class="widget_links mb-0">
                  <?php foreach ($common_page['desktop_footer_3'] as $item) { ?>
                    <li><a href="<?= $item['url']; ?>"><?= $item['name']; ?></a></li>
                  <?php } ?>
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
                data-bs-target="#collapseFour"
                aria-expanded="false"
                aria-controls="collapseFour"
              >
                CUSTOMER SUPPORT
                <div>
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-plus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-plus"
                    loading="lazy"
                  />
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-minus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-minus"
                    loading="lazy"
                  />
                </div>
              </button>
            </div>
            <div
              id="collapseFour"
              class="accordion-collapse collapse"
              data-bs-parent="#footerAccordion"
            >
              <div class="accordion-body">
                <ul class="widget_links mb-0">
                  <?php foreach ($common_page['desktop_footer_4'] as $item) { ?>
                    <li><a href="<?= $item['url']; ?>"><?= $item['name']; ?></a></li>
                  <?php } ?>
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
                data-bs-target="#collapseFive"
                aria-expanded="false"
                aria-controls="collapseFive"
              >
                CONNECT WITH US
                <div>
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-plus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-plus"
                    loading="lazy"
                  />
                  <img
                    src="<?php echo get_template_directory_uri(); ?>/assets/image/footer-minus-mobile.png"
                    alt="footer-plus-mobile"
                    class="footer-minus"
                    loading="lazy"
                  />
                </div>
              </button>
            </div>
            <div
              id="collapseFive"
              class="accordion-collapse collapse"
              data-bs-parent="#footerAccordion"
            >
              <div class="accordion-body">
                <ul class="soical_links pt-0">
                  <li>
                    <a href="https://x.com/rrpdiamonds" target="_blank">
                      <i class="fa-brands fa-twitter"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.facebook.com/rrpdiamond" target="_blank">
                      <i class="fa-brands fa-facebook-f"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.instagram.com/rrpdiamond/" target="_blank">
                      <i class="fa-brands fa-instagram"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.youtube.com/rrpdiamond" target="_blank">
                      <i class="fa-brands fa-youtube"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://in.pinterest.com/rrpdiamond/" target="_blank">
                      <i class="fa-brands fa-pinterest"></i>
                    </a>
                  </li>
                  
                </ul>

                <p class="address-link">
                  <a href="tel:+<?php echo SUPPORT_MOBILE?>">   
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="10"
                                    height="10"
                                    viewBox="0 0 10 10"
                                    fill="none"
                                  >
                                    <g clip-path="url(#clip0_9461_797)">
                                      <path
                                        d="M9.72953 7.3385L8.334 5.94297C7.8356 5.44457 6.98831 5.64395 6.78895 6.29186C6.63943 6.74044 6.14103 6.98964 5.69246 6.88994C4.69566 6.64074 3.34997 5.34489 3.10077 4.29824C2.95125 3.84966 3.25029 3.35126 3.69885 3.20175C4.34678 3.00239 4.54614 2.15511 4.04773 1.6567L2.6522 0.261174C2.25348 -0.0877089 1.6554 -0.0877089 1.30651 0.261174L0.359548 1.20814C-0.587419 2.20495 0.459229 4.84649 2.80172 7.18898C5.14422 9.53148 7.78576 10.628 8.78257 9.63116L9.72953 8.68419C10.0784 8.28547 10.0784 7.68739 9.72953 7.3385Z"
                                        fill="#767676"
                                      />
                                    </g>
                                    <defs>
                                      <clipPath id="clip0_9461_797">
                                        <rect width="10" height="10" fill="white" />
                                      </clipPath>
                                    </defs>
                                  </svg>
                                  +<?php echo SUPPORT_MOBILE?>
                </a>
                </p>
                <p class="address-link address-link-label">
                INDIA FACTORY
              </p>
              <p class="address-link">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="10"
                  height="10"
                  viewBox="0 0 10 10"
                  fill="none"
                >
                  <path
                    d="M5.00004 0C3.00307 0 1.37842 1.62465 1.37842 3.6216C1.37842 6.09988 4.61941 9.73815 4.7574 9.89182C4.88701 10.0362 5.1133 10.0359 5.24268 9.89182C5.38066 9.73815 8.62166 6.09988 8.62166 3.6216C8.62162 1.62465 6.99699 0 5.00004 0ZM5.00004 5.44373C3.99531 5.44373 3.17793 4.62633 3.17793 3.6216C3.17793 2.61688 3.99533 1.79949 5.00004 1.79949C6.00475 1.79949 6.82213 2.61689 6.82213 3.62162C6.82213 4.62635 6.00475 5.44373 5.00004 5.44373Z"
                    fill="#767676"
                  />
                </svg>
                2nd Floor, Red Turning Point,<br />
Near, Matawadi Police Chowki, L.H.Road,<br />
Varachha, Surat, Gujarat - 395006
              </p>
                <p class="address-link address-link-label">
                USA OFFICE
              </p>
              <p class="address-link">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="10"
                  height="10"
                  viewBox="0 0 10 10"
                  fill="none"
                >
                  <path
                    d="M5.00004 0C3.00307 0 1.37842 1.62465 1.37842 3.6216C1.37842 6.09988 4.61941 9.73815 4.7574 9.89182C4.88701 10.0362 5.1133 10.0359 5.24268 9.89182C5.38066 9.73815 8.62166 6.09988 8.62166 3.6216C8.62162 1.62465 6.99699 0 5.00004 0ZM5.00004 5.44373C3.99531 5.44373 3.17793 4.62633 3.17793 3.6216C3.17793 2.61688 3.99533 1.79949 5.00004 1.79949C6.00475 1.79949 6.82213 2.61689 6.82213 3.62162C6.82213 4.62635 6.00475 5.44373 5.00004 5.44373Z"
                    fill="#767676"
                  />
                </svg>
                55W 47th St., Suite #790 <br />
                New York, NY-10036, USA
              </p>
              
              </div>
            </div>
          </div>
        </div>
  <div class="container-fluid">
        <div class="row desktop-row justify-content-start">
          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">SHOP</p>
            <ul class="widget_links">

              <?php foreach ($common_page['desktop_footer_1'] as $item) { ?>
                <li><a href="<?= $item['url']; ?>" <?php if($item['target']!=''){ echo ' data-bs-toggle="modal" data-bs-target="'.$item['target'].'"';}?>><?= $item['name']; ?></a></li>
              <?php } ?>
            </ul>
          </div>

          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">ABOUT</p>
            <ul class="widget_links">
              <?php foreach ($common_page['desktop_footer_2'] as $item) { ?>
                  <li><a href="<?= $item['url']; ?>"><?= $item['name']; ?></a></li>
              <?php } ?>
            </ul>
          </div>

          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">
              <?php
              
                echo 'FINANCING & POLICIES';
              
              ?>
            </p>
            <ul class="widget_links">
              <?php foreach ($common_page['desktop_footer_3'] as $item) { ?>
                  <li><a href="<?= $item['url']; ?>"><?= $item['name']; ?></a></li>
              <?php } ?>
            </ul>
          </div>

          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">
              CUSTOMER SUPPORT
            </p>
            <ul class="widget_links">
              <?php foreach ($common_page['desktop_footer_4'] as $item) { ?>
                  <li><a href="<?= $item['url']; ?>"><?= $item['name']; ?></a></li>
              <?php } ?>
            </ul>
          </div>

          <div class="col-sm-6 col-md-4 col-lg-3">
            <div>
              <p class="text_white_primary text-uppercase  footer_title">
                Connnect with us
              </p>
              <ul class="soical_links pt-2">
                 <li>
                    <a href="https://x.com/rrpdiamonds" target="_blank">
                      <i class="fa-brands fa-twitter"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.facebook.com/rrpdiamond" target="_blank">
                      <i class="fa-brands fa-facebook-f"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.instagram.com/rrpdiamond/" target="_blank">
                      <i class="fa-brands fa-instagram"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.youtube.com/rrpdiamond" target="_blank">
                      <i class="fa-brands fa-youtube"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://in.pinterest.com/rrpdiamond/" target="_blank">
                      <i class="fa-brands fa-pinterest"></i>
                    </a>
                  </li>
                  
                </ul>

              <p class="address-link">
                <a href="tel:+<?php echo SUPPORT_MOBILE?>">  
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="10"
                                height="10"
                                viewBox="0 0 10 10"
                                fill="none"
                              >
                                <g clip-path="url(#clip0_9461_797)">
                                  <path
                                    d="M9.72953 7.3385L8.334 5.94297C7.8356 5.44457 6.98831 5.64395 6.78895 6.29186C6.63943 6.74044 6.14103 6.98964 5.69246 6.88994C4.69566 6.64074 3.34997 5.34489 3.10077 4.29824C2.95125 3.84966 3.25029 3.35126 3.69885 3.20175C4.34678 3.00239 4.54614 2.15511 4.04773 1.6567L2.6522 0.261174C2.25348 -0.0877089 1.6554 -0.0877089 1.30651 0.261174L0.359548 1.20814C-0.587419 2.20495 0.459229 4.84649 2.80172 7.18898C5.14422 9.53148 7.78576 10.628 8.78257 9.63116L9.72953 8.68419C10.0784 8.28547 10.0784 7.68739 9.72953 7.3385Z"
                                    fill="#767676"
                                  />
                                </g>
                                <defs>
                                  <clipPath id="clip0_9461_797">
                                    <rect width="10" height="10" fill="white" />
                                  </clipPath>
                                </defs>
                              </svg>
                              +<?php echo SUPPORT_MOBILE?>
              </a>
              </p>
              <p class="address-link address-link-label">
                INDIA FACTORY
              </p>
              <p class="address-link">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="10"
                  height="10"
                  viewBox="0 0 10 10"
                  fill="none"
                >
                  <path
                    d="M5.00004 0C3.00307 0 1.37842 1.62465 1.37842 3.6216C1.37842 6.09988 4.61941 9.73815 4.7574 9.89182C4.88701 10.0362 5.1133 10.0359 5.24268 9.89182C5.38066 9.73815 8.62166 6.09988 8.62166 3.6216C8.62162 1.62465 6.99699 0 5.00004 0ZM5.00004 5.44373C3.99531 5.44373 3.17793 4.62633 3.17793 3.6216C3.17793 2.61688 3.99533 1.79949 5.00004 1.79949C6.00475 1.79949 6.82213 2.61689 6.82213 3.62162C6.82213 4.62635 6.00475 5.44373 5.00004 5.44373Z"
                    fill="#767676"
                  />
                </svg>
                2nd Floor, Red Turning Point,<br />
Near, Matawadi Police Chowki, L.H.Road,<br />
Varachha, Surat, Gujarat - 395006
              </p>
              <p class="address-link address-link-label">
                USA OFFICE
              </p>
              <p class="address-link">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="10"
                  height="10"
                  viewBox="0 0 10 10"
                  fill="none"
                >
                  <path
                    d="M5.00004 0C3.00307 0 1.37842 1.62465 1.37842 3.6216C1.37842 6.09988 4.61941 9.73815 4.7574 9.89182C4.88701 10.0362 5.1133 10.0359 5.24268 9.89182C5.38066 9.73815 8.62166 6.09988 8.62166 3.6216C8.62162 1.62465 6.99699 0 5.00004 0ZM5.00004 5.44373C3.99531 5.44373 3.17793 4.62633 3.17793 3.6216C3.17793 2.61688 3.99533 1.79949 5.00004 1.79949C6.00475 1.79949 6.82213 2.61689 6.82213 3.62162C6.82213 4.62635 6.00475 5.44373 5.00004 5.44373Z"
                    fill="#767676"
                  />
                </svg>
                55W 47th St., Suite #790 <br />
                New York, NY-10036, USA
              </p>
              
            </div>
          </div>
         
          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">
              Reviews
            </p>
            <!-- TrustBox widget - Mini -->
            <div class="footer_trustpilot mb-4">
              <div class="trustpilot_btn">
                  <a href="https://www.trustpilot.com/review/rrpdiamonds.com" target="_blank">
                    <img
                    src="<?php echo get_template_directory_uri().'/rrp-img/comman/trustpilot-review-btn.svg'; ?>"
                    alt="ratings"
                    class="img-fluid trustpilot_btn-rate-image"
                    loading="lazy"
                  />
                  </a>
              </div>
            </div>
              <!-- End TrustBox widget -->
               <a href="https://g.page/r/CfgPY3z14oWaEAE" target="_blank" rel="noopener">
            <img
              src="<?php echo get_template_directory_uri().'/aj_custom/footer/'; ?>google_review_widget.svg"
              alt="ratings"
              class="img-fluid rate-image"
              loading="lazy"
            />
            </a>
          </div>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <p class="text_white_primary text-uppercase footer_title">
              Sign Up
            </p>
            <p class="exclusive-offers">
              Sign Up For Exclusive offers and news
            </p>
            <div class="search-box footer-box">
              <input
                class="form-control"
                type="text"
                placeholder="Your Email Address"
                aria-label="Search"
              />
              <button
                type="button"
                class="subscribe_newsletter lscartbtn ls_add_to_cart d-flex align-items-center justify-content-center"
                style="white-space: nowrap; cursor: pointer"
              >
                Submit
              </button>
            </div>
          </div>
         
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="d-flex align-items-center row-gap-0 flex-wrap gap-3 aj-footer-logo">
               
               <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/banktransfer.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/crypto.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/zelle.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
            
            </div>
          </div>
        </div>

      
        <div class="row mobile-desktop d-sm-none mx-0">

            <?php
            
                ?>
          <div class="col-12 px-0">
            <p class="text_white_primary text-uppercase footer_title">
              Reviews
            </p>
            <div class="footer_trustpilot  mb-4">
            <div class="trustpilot_btn">
                  <a href="https://www.trustpilot.com/review/rrpdiamonds.com" target="_blank">
                    <img
                    src="<?php echo get_template_directory_uri().'/rrp-img/comman/trustpilot-review-btn.svg'; ?>"
                    alt="ratings"
                    class="img-fluid trustpilot_btn-rate-image"
                    loading="lazy"
                  />
                  </a>
              </div>
              </div>
              <a href="https://g.page/r/CfgPY3z14oWaEAE" target="_blank" rel="noopener">
                        <img
                          src="<?php echo get_template_directory_uri().'/aj_custom/footer/'; ?>google_review_widget.svg"
                          alt="ratings"
                          class="img-fluid rate-image"
                          loading="lazy"
                        />
            </a>
          </div>
          
          <div class="col-12 px-0">
            <p class="text_white_primary text-uppercase footer_title">
              Sign Up
            </p>
            <p class="exclusive-offers">
              Sign Up For Exclusive offers and news
            </p>
            <div class="search-box footer-box">
              <input
                class="form-control"
                type="text"
                placeholder="Your Email Address"
                aria-label="Search"
              />
              <button
                type="button"
                class="subscribe_newsletter lscartbtn ls_add_to_cart d-flex align-items-center justify-content-center"
                style="white-space: nowrap; cursor: pointer"
              >
                Submit
              </button>
            </div>
          </div>
          <?php
          
          ?>

           <div class="d-flex align-items-center row-gap-0 flex-wrap gap-3 px-0 aj-footer-logo">
             <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/banktransfer.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/crypto.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
                    <img src="<?php echo get_template_directory_uri(); ?>/rrp-img/payment/zelle.svg" alt="Visa Pay" class="img-fluid" loading="lazy" />
            </div>
        </div>
      </div>
</footer>
<?php
}
?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/popper.min.js" defer></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap.min.js" defer></script>


<script>
(function () {
  let loaded = false;

  function loadTrustpilot() {
    if (loaded) return;
    loaded = true;

    var script = document.createElement("script");
    script.src = "//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js";
    script.async = true;
    document.body.appendChild(script);

    // Remove listeners after loading
    window.removeEventListener("scroll", loadTrustpilot);
    window.removeEventListener("click", loadTrustpilot);
  }

  // Listen for first interaction
  window.addEventListener("scroll", loadTrustpilot, { once: true });
  window.addEventListener("click", loadTrustpilot, { once: true });
})();
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.bootstrap) return;

    // Initialize tooltips
    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]',
        trigger: 'hover focus'
    });

    // Show tooltip on touch
    document.addEventListener('touchstart', function (e) {
        const el = e.target.closest('[data-bs-toggle="tooltip"]');

        if (!el) return;

        const instance = bootstrap.Tooltip.getOrCreateInstance(el);
        instance.show();
    }, { passive: true });

    // Hide tooltip when scrolling
    document.addEventListener('scroll', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            const instance = bootstrap.Tooltip.getInstance(el);

            if (instance) {
                instance.hide();
            }
        });
    }, { capture: true, passive: true });

});
</script>
<script type="text/javascript">
  
// Search on the mobile (elements don't exist on checkout, so guard)
(function () {
  const searchIcon = document.getElementById("mobileSearchIcon");
  const searchBox = document.getElementById("mobileSearchBox");
  const searchBtn = document.getElementById("mobileSearchBtn");

  if (searchIcon && searchBox) {
    // Toggle search box on icon click
    searchIcon.addEventListener("click", function () {
      if (searchBox.style.display === "flex") {
        searchBox.style.display = "none";
      } else {
        searchBox.style.display = "flex";
      }
    });
  }

  if (searchBtn && searchBox) {
    // Hide search box after 5 seconds when search button clicked
    searchBtn.addEventListener("click", function () {
      setTimeout(function () {
        searchBox.style.display = "none";
      }, 5000);
    });
  }
})();


document.addEventListener('DOMContentLoaded', function () {
  const accordionButtons = document.querySelectorAll('.footer-accordion .accordion-button');

  // Set all as closed initially
  accordionButtons.forEach(function (button) {
    button.setAttribute('data-open', 'false');
    button.querySelector('.footer-plus').style.display = 'block';
    button.querySelector('.footer-minus').style.display = 'none';
  });

  accordionButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      const isOpen = button.getAttribute('data-open') === 'true';

      // Reset ALL buttons to closed state
      accordionButtons.forEach(function (btn) {
        btn.setAttribute('data-open', 'false');
        btn.querySelector('.footer-plus').style.display = 'block';
        btn.querySelector('.footer-minus').style.display = 'none';
      });

      // If it was closed, open it
      if (!isOpen) {
        button.setAttribute('data-open', 'true');
        button.querySelector('.footer-plus').style.display = 'none';
        button.querySelector('.footer-minus').style.display = 'block';
      }
    });
  });
});
</script>
<script type="text/javascript">
  document.addEventListener('click', function (e) {
    if (e.target.closest('.oc-lg-search-btn')) {
        e.preventDefault();

        const form = e.target.closest('.oc-lg-search-btn').closest('form');

        if (form) {
            form.submit();
        }
    }
});
</script>

</div><!-- #page -->
<?php
            
                ?>
                <script async type="text/javascript" src="https://zvrnp.loosegrowndiamond.com/loosegrowndiamond/load" onError="var fallbackScript = document.createElement('script'); fallbackScript.src='https://cdn.attn.tv/loosegrowndiamond/dtag.js'; document.head.appendChild(fallbackScript);"></script>
                <?php
              
              ?>
<?php

?>
<!-- Hotjar Tracking Code for https://www.loosegrowndiamond.com/ -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:3417948,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
<?php

?>


<?php wp_footer(); ?>


<script type="text/javascript">
  

  
  document.addEventListener('DOMContentLoaded', function () {
    /* ─── AUTHOR DESCRIPTION: SHOW MORE / LESS ──────────────── */
    var limit = 200;

    document.querySelectorAll('.author_description').forEach(function (el) {
      var fullText = el.textContent.trim();
      if (fullText.length <= limit) return;

      var shortText = fullText.substring(0, limit).trimEnd() + '...';
      var expanded = false;

      var link = document.createElement('a');
      link.href = 'javascript:void(0)';
      link.className = 'author_toggle';
      link.setAttribute('role', 'button');
      link.setAttribute('aria-expanded', 'false');

      function render() {
        el.textContent = (expanded ? fullText : shortText) + ' ';
        link.textContent = expanded ? 'Show Less' : 'Show More';
        link.setAttribute('aria-expanded', String(expanded));
        el.appendChild(link);
      }

      link.addEventListener('click', function (e) {
        e.preventDefault();
        expanded = !expanded;
        render();
      });

      render();
    });
  });

</script>
<script type="text/javascript">
(function () {
  const container = document.querySelector('.ls_inventory_desc .ls_inventory_desc_inner');
  const button = document.getElementById('invtoggleBtn');
  if (!container || !button) return; // only on inventory pages

  button.addEventListener('click', function (e) {
    e.preventDefault();
    container.classList.toggle('expanded');

    if (container.classList.contains('expanded')) {
      button.innerText = 'Read Less';
    } else {
      button.innerText = 'Read More';
    }
  });
})();
</script>
</body>

</html>