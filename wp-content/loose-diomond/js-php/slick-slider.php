<?php
add_action('wp_enqueue_scripts', function () {

	/*
     * Slick Slider
     * Load only on WooCommerce product categories
     * or the New Engagement Rings page template.
     */
    if (
        is_product_category() ||
        is_page_template('page-new-engagement-rings.php')
    ) {

        wp_enqueue_style(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
            [],
            '1.8.1'
        );

        wp_enqueue_style(
            'slick-carousel-theme',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
            ['slick-carousel'],
            '1.8.1'
        );

        wp_enqueue_script(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
            ['jquery'],
            '1.8.1',
            true
        );
    }

});

/**
 * Load selected CSS files asynchronously.
 */
add_filter('style_loader_tag', function ($html, $handle, $href) {

    if (is_admin()) {
        return $html;
    }

    $async_handles = [
        // Slick
        'slick-carousel',
        'slick-carousel-theme',
    ];

    if (in_array($handle, $async_handles, true)) {

        $html = str_replace(
            "media='all'",
            "media='print' onload=\"this.media='all';this.onload=null;\"",
            $html
        );

        $html .= '<noscript>
            <link rel="stylesheet" href="' . esc_url($href) . '">
        </noscript>' . "\n";
    }

    return $html;

}, 10, 3);

add_action('wp_footer', function () {
    if (
        is_product_category() ||
        is_page_template('page-new-engagement-rings.php')
    ) :
    ?>
    
<script>
/* ─── SWATCH LOGIC (plain JS) ─────────────────────────────── */
function drActivateSwatch(sw) {
  var card = sw.closest('.dr-card');
  if (!card) return;

  /* 1. Active state */
  card.querySelectorAll('.dr-swatch').forEach(function (s) {
    s.classList.remove('is-active');
  });
  sw.classList.add('is-active');

  /* 2. Image swap */
  var newSrc    = sw.dataset.img;
  var newSrcset = sw.dataset.srcset;

  if (newSrc) {
    var img = card.querySelector('.dr-card__media img');
    if (img) {
      img.src = newSrc;

      if (newSrcset) img.setAttribute('srcset', newSrcset);
      else img.removeAttribute('srcset');

      img.removeAttribute('sizes');

      if (img.hasAttribute('data-src'))    img.setAttribute('data-src', newSrc);
      if (img.hasAttribute('data-srcset')) img.setAttribute('data-srcset', newSrcset || '');

      img.classList.remove('skip-lazy', 'lazyload', 'lazy');
      img.classList.add('lazyloaded');
    }
  }

  /* 3. Price swap */
  var newPrice = sw.dataset.price;
  if (newPrice) {
    var price = card.querySelector('.dr-card__price');
    if (price) price.innerHTML = newPrice;
  }
}

/* Delegated click (also works on Slick's cloned slides) */
document.addEventListener('click', function (e) {
  var sw = e.target.closest('.dr-swatch');
  if (sw) drActivateSwatch(sw);
});

/* ─── INIT ───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

  /* Activate first swatch in each card (before Slick clones them) */
  document.querySelectorAll('.dr-slider .dr-card').forEach(function (card) {
    var first = card.querySelector('.dr-swatch');
    if (first) drActivateSwatch(first);
  });

  /* Slick still needs jQuery */
  if (window.jQuery && jQuery.fn.slick && document.querySelector('.dr-slider')) {
    var $slider = jQuery('.dr-slider');

    $slider.slick({
      slidesToShow: 5,
      slidesToScroll: 1,
      infinite: true,
      arrows: false,
      dots: false,
      speed: 450,
      responsive: [
        { breakpoint: 992, settings: { slidesToShow: 5 } },
        { breakpoint: 768, settings: { slidesToShow: 2 } },
        { breakpoint: 520, settings: { slidesToShow: 1, arrows: false, dots: true } }
      ]
    });

    document.querySelectorAll('.dr-arrow--prev').forEach(function (btn) {
      btn.addEventListener('click', function () { $slider.slick('slickPrev'); });
    });
    document.querySelectorAll('.dr-arrow--next').forEach(function (btn) {
      btn.addEventListener('click', function () { $slider.slick('slickNext'); });
    });
  }
});

</script>
<script type="text/javascript">
  

  
  document.addEventListener('DOMContentLoaded', function () {

    /* ─── EDUCATION SLIDER (Slick needs jQuery) ──────────────── */
    if (window.jQuery && jQuery.fn.slick && document.querySelector('.education-slider')) {
      var $slider = jQuery('.education-slider');
      var prevBtns = document.querySelectorAll('.education-arrow--prev');
      var nextBtns = document.querySelectorAll('.education-arrow--next');

      function updateEducationArrows() {
        var slick = $slider.slick('getSlick');
        if (!slick) return;
        var current = slick.currentSlide;
        var count   = slick.slideCount;
        var toShow  = slick.options.slidesToShow;

        prevBtns.forEach(function (b) { b.disabled = current === 0; });
        nextBtns.forEach(function (b) { b.disabled = current >= count - toShow; });
      }

      // Bind before init so the 'init' event is caught
      $slider.on('init afterChange breakpoint', updateEducationArrows);

      $slider.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        infinite: false,
        speed: 400,
        cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
        responsive: [
          { breakpoint: 1200, settings: { slidesToShow: 3 } },
          { breakpoint: 768,  settings: { slidesToShow: 2 } },
          { breakpoint: 480,  settings: { slidesToShow: 1 } }
        ]
      });

      prevBtns.forEach(function (b) {
        b.addEventListener('click', function () { $slider.slick('slickPrev'); });
      });
      nextBtns.forEach(function (b) {
        b.addEventListener('click', function () { $slider.slick('slickNext'); });
      });
    }

    
  });

</script>
    <?php
    endif;
});