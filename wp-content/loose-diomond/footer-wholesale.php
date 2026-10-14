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

?>

  <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/popper.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/myscript.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/intlTelInput.js"></script>
    <script>
      $(document).ready(function () {
        $(".ring_slick_slider").slick({
          arrows: false,
          dots: true,
          autoplay: true,
          asNavFor: ".ring_slick_slider_for",
        });
        $(".ring_slick_slider_for").slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          arrows: false,
          fade: true,
          asNavFor: ".ring_slick_slider",
          centerMode: true,
          focusOnSelect: true,
        });
      });
    </script>
    <script>
      $(document).ready(function () {
        $(".nav-link").click(function () {
          $(".filter_content").toggle();
        });
      });
    </script>
    <script src="https://t.contentsquare.net/uxa/<?php echo CONTENTSQUARE;?>.js"></script>

<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=<?php echo ZOHOCHATBOT;?>" defer></script>

<script src="https://app.termly.io/resource-blocker/92292f48-813a-49bb-9c0a-b1000c8508fe?autoBlock=on"></script>

</body>

</html>