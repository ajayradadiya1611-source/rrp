<?php
/**
 * Template Name: custom-engagement-rings-aj
 */

get_header();
?>
 <div class="engagement-ring-banner" style='background: url("<?php echo get_template_directory_uri() . '/aj_custom/custom-engagement-rings-aj/custom-ring-banner.png';?>");background-size: cover;
    background-repeat: no-repeat;
    background-position: center;'>
      <div class="container">
        <h1 class="banner-title">
         Custom Engagement Rings Made Only For You.

        </h1>
        <div class=" d-flex align-items-center justify-content-center">
            <a  href="#custom-ring-form" data-bs-target="#customSettingModal" class="common_btn ring-button aj-white-btn-color">
              Design My Ring
            </a>
        </div>

        
      </div>
</div>
<div class="">
  <?php
$marquee_items = [
    'Best Price Promise',
    'Skin-Friendly',
    'Conflict-Free Diamonds',
    'Stress-Free Purchase',
    'Free Insured Shipping*',
    'Easy &amp; Free ' . RETURN_DAYS . ' Day Returns*',
    'Manufacturer Warranty',
];

$marquee_repeat = 3; // copies for seamless scroll

ob_start(); ?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="17" height="17" viewBox="0 0 17 17" fill="none">
    <mask id="mask0_4396_88374" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="17" height="17">
        <rect width="17" height="17" fill="url(#pattern0_4396_88374)"></rect>
    </mask>
    <g mask="url(#mask0_4396_88374)">
        <rect width="17" height="17" fill="black"></rect>
    </g>
    <defs>
        <pattern id="pattern0_4396_88374" patternContentUnits="objectBoundingBox" width="1" height="1">
            <use xlink:href="#image0_4396_88374" transform="scale(0.0588235)"></use>
        </pattern>
        <image id="image0_4396_88374" width="17" height="17" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAYAAAA7bUf6AAAAJGVYSWZJSSoACAAAAAEAPAECAAkAAAAaAAAAAAAAAGltYWdlcnk0AAAnPYy6AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAEaSURBVHgBvZMxTsNAEEX/TIygoMgRwg1IgeRyC7hCBKKIOQHcgHCDcANTIEQofASbCgRF4ATkCCkQgpDM4I0IshG7i1LkN9bK/7/9O/YCq9CViVshDwcNzEnIQ76XmTHNKX+8vMr61lFRjJ0b+SATnpwo0NzE+zGWaWJnETENLcSuP2XaPiwen/7d5GY3Ng2mfAGwWuMouzQ728EmNqzCXUATOKRCZwJJD4r7UQ0y2Ct3mDXKc5Mz/AcuFZX+fv7wXGti58CMLoF6vjCU0k5+d1tr8luD8uxKUUaE1k9UMSrNSTXshSxA4Gj4vRzPRNvVOVTl/E865eckaG/eQrTvAnghVpFsnNsWb5g/l9e1iU9DnuAFFOACq9AXVIVht6tjtuYAAAAASUVORK5CYII="></image>
    </defs>
</svg>
<?php $marquee_icon = ob_get_clean(); ?>

<div class="marquee">
    <div class="marquee__content">
        <?php for ( $i = 0; $i < $marquee_repeat; $i++ ) : ?>
            <?php foreach ( $marquee_items as $item ) : ?>
                <div class="marquee__item">
                    <?php echo $marquee_icon; ?>
                    <p><?php echo $item; ?></p>
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>
</div>

<section class="how-it-works">
  <div class="container">
    <h2 class="how-it-works__title text-center">How It Works</h2>
    <ol class="how-it-works__steps">
      <li class="how-it-works__step">
        <span class="how-it-works__num" aria-hidden="true">1</span>
        <h3 class="how-it-works__step-title">Share your vision</h3>
        <p class="how-it-works__text">Tell us about the ring you imagine. Share inspiration photos, links, or sketches, and our experts will guide you with recommendations and a quote.</p>
      </li>
      <li class="how-it-works__step">
        <span class="how-it-works__num" aria-hidden="true">2</span>
        <h3 class="how-it-works__step-title">Your design comes to life</h3>
        <p class="how-it-works__text">Our designers create detailed 3D CAD renderings of your ring, showing multiple angles. You’ll review the design and request revisions until it’s perfect.</p>
      </li>
      <li class="how-it-works__step">
        <span class="how-it-works__num" aria-hidden="true">3</span>
        <h3 class="how-it-works__step-title">Crafted just for you</h3>
        <p class="how-it-works__text">Once you approve the design, our expert jewelers begin crafting your ring. Your custom ring is completed and delivered within 3–4 weeks.</p>
      </li>
    </ol>
    <div class="text-center">
      <a href="#custom-ring-form" data-bs-target="#customSettingModal" class="common_btn">Start My Design</a>
    </div>
  </div>
</section>
<section class="start-designing bg_light_gray">
  <div class="container-fluid">
    <div class="row align-items-center g-4">
      <div class="col-12 col-md-8">
        <h2 class="start-designing__title">Start Designing Your<br class="d-none d-md-inline"> Dream Ring</h2>
        <p class="start-designing__text">
          Work with our designers to create a ring that’s uniquely yours.
          You only pay after approving the final design.
        </p>
        <span class="start-designing__badge">Free CAD design • No obligation • Expert guidance</span>
      </div>
 
      <div class="col-12 col-md-4 text-md-end">
        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#customSettingModal" class="common_btn">Begin My Custom Ring</a>
      </div>
    </div>
  </div>
</section>
 
<?php
$custom_rings = array(
  array(
    'video'    => 'https://media.loosegrowndiamond.com/2230545.mp4',
    'badge'    => 'Lab-Grown Diamond',
    'title'    => 'Marquise Unique Engagement Ring',
    'designed' => 'Designed for Johnathon Simmons in Union Territory, USA',
    'specs'    => '2.00ct Marquise• VVS2',
  ),
  array(
    'video'    => 'https://media.loosegrowndiamond.com/2233782.mp4',
    'badge'    => 'Lab-Grown Diamond',
    'title'    => 'Marquise Unique Engagement Ring',
    'designed' => 'Designed for Kendall Clanton in North Carolina, USA',
    'specs'    => '3.00ct Marquise• VVS2',
  ),
  array(
    'video'    => 'https://media.loosegrowndiamond.com/2234517.mp4',
    'badge'    => 'Lab-Grown Diamond',
    'title'    => 'Round Diamond Band Engagement Ring',
    'designed' => 'Designed for Marion Barleta in California, USA',
    'specs'    => '3.33ct Round• IF',
  ),
  array(
    'video'    => 'https://media.loosegrowndiamond.com/2227867.mp4',
    'badge'    => 'Lab-Grown Diamond',
    'title'    => 'Oval Diamond Unique Engagement Ring',
    'designed' => 'Designed for Joseph Fischer in Virginia, USA',
    'specs'    => '3.00ct Oval• VVS2',
  ),
);
?>
<section class="custom-rings">
  <div class="container">
    <div class="custom-rings__head text-center">
      <h2 class="custom-rings__title">Real Custom Rings We’ve Created</h2>
      <p class="custom-rings__intro">
        Each ring starts with an idea. Explore some of the one-of-a-kind engagement rings
        we’ve designed and crafted for our customers.
      </p>
    </div>

    <div class="row g-1 g-md-2 custom-rings__grid">
      <?php foreach ( $custom_rings as $ring ) : ?>
        <div class="col-6 col-lg-3">
          <div class="custom-rings__card">
            <div class="custom-rings__media">
              <video
                class="custom-rings__video js-custom-rings-video"
                muted loop playsinline preload="none"
                aria-label="<?php echo esc_attr( $ring['title'] ); ?>">
                <source data-src="<?php echo esc_url( $ring['video'] ); ?>" type="video/mp4">
              </video>
              <!-- <span class="custom-rings__badge"><?php echo esc_html( $ring['badge'] ); ?></span> -->
            </div>
            <div class="custom-rings__body">
              <h3 class="custom-rings__card-title"><?php echo esc_html( $ring['title'] ); ?></h3>
              <p class="custom-rings__designed"><?php echo esc_html( $ring['designed'] ); ?></p>
              <p class="custom-rings__specs"><?php echo esc_html( $ring['specs'] ); ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center">
      <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#customSettingModal" class="common_btn">Get My Free Design</a>
    </div>
  </div>
</section>

<script>
// Load + play videos only when visible, pause when off-screen
(function () {
  var vids = document.querySelectorAll('.js-custom-rings-video');
  if (!vids.length) return;

  function load(v) {
    if (v.dataset.loaded) return;
    v.querySelectorAll('source[data-src]').forEach(function (s) { s.src = s.dataset.src; });
    v.load();
    v.dataset.loaded = '1';
  }

  if (!('IntersectionObserver' in window)) {
    vids.forEach(function (v) { load(v); v.play().catch(function(){}); });
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      var v = e.target;
      if (e.isIntersecting) { load(v); v.play().catch(function(){}); }
      else { v.pause(); }
    });
  }, { rootMargin: '200px 0px', threshold: 0.25 });

  vids.forEach(function (v) { io.observe(v); });
})();
</script>


<section class="rings-video bg_light_gray">
  <div class="container-fluid">
    <div class="rings-video__head text-center">
      <h2 class="rings-video__title">Curious How Custom Rings Are Made?</h2>
      <p class="rings-video__intro">Watch how we design and craft your perfect ring from scratch.</p>
    </div>
   
    <div class="rings-video__frame">
      <video class="img-fluid rings-video__video" autoplay="autoplay" loop="loop" muted="muted" playsinline>
        <source src="https://media.loosegrowndiamond.com/custom-ring-video.mp4" type="video/mp4">
      </video>
    </div>
   
    <div class="text-center">
      <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#customSettingModal" class="common_btn">Design Your Ring</a>
    </div>
  </div>
</section>
 

<?php


$img_dir = get_template_directory_uri() . '/aj_custom/custom-engagement-rings-aj/review/';

$custom_stories = array(
  array(
    'image' => '1.png',
    'name'  => 'Liena Quezada',
    'text'  => 'The customer experience is beyond amazing they were very communicative the entire process I have really bad anxiety and sometimes felt like a bother emailing…',
    'link'  => 'https://maps.app.goo.gl/x7NKiwwN47cSX1Dg7',
  ),
  array(
    'image' => '2.png',
    'name'  => 'Samantha Marshall',
    'text'  => 'The process of building a custom engagement ring and wedding band was very smooth. Emails were responded to quickly and everyone seemed very knowledgeable...',
    'link'  => 'https://maps.app.goo.gl/ToB3eeTtU471HhRq6',
  ),
  array(
    'image' => '3.png',
    'name'  => 'Ali Kiani',
    'text'  => 'Amazing service from start to finish. The team understood my exact vision for the ring design and delivered it along with outstanding value, clearly outperforming…',
    'link'  => 'https://maps.app.goo.gl/Mv7DVoBMDjVXQyk98',
  ),
  array(
    'image' => '4.png',
    'name'  => 'Sam Adams',
    'text'  => 'Chris was super helpful with taking my requested concept for the band design and then integrating the chosen diamond. Very responsive, fair pricing, and thrilled...',
    'link'  => 'https://maps.app.goo.gl/MSEpFpK2d5G6XsBJ8',
  ),
  array(
    'image' => '5.png',
    'name'  => 'Morgan Aspin',
    'text'  => 'Great communication. Excellent product! Best prices out there. Quick, insured shipping. Descreet packagaing. Simple to navigate through website.',
    'link'  => 'https://maps.app.goo.gl/38V5pwR1Vd3W6s8g9',
  ),
  array(
    'image' => '6.png',
    'name'  => 'Adam Epstein',
    'text'  => 'There is a massive selection of diamonds to choose from. I purchased an oval. The big 4 filters help narrow it down, but you have to look at the diamond report to…',
    'link'  => 'https://maps.app.goo.gl/Tdxnpt7FdtUWcPCz7',
  ),
  array(
    'image' => '7.png',
    'name'  => 'Steve Ash',
    'text'  => 'Normally, I don’t do product reviews because most products are just so so anyway, but in this case after receiving our ring from Loose Grown Diamond, my wife…',
    'link'  => 'https://maps.app.goo.gl/fNTk7sthGdDSsUbe6',
  ),
  array(
    'image' => '8.png',
    'name'  => 'Avneet Summan',
    'text'  => 'I am extremely pleased with my purchase from Loose Grown Diamond! My ring turned out exactly how I envisioned it. I did a custom design and sent them a picture of…',
    'link'  => 'https://maps.app.goo.gl/yHodRX1nuoBduUVh8',
  ),
  array(
    'image' => '9.png',
    'name'  => 'Bryce Saxton',
    'text'  => 'Beautiful ring. Customization made it so I could get something you can’t find in any store. Quality is incredible. Design process is fun and adjustments to design was easy...',
    'link'  => 'https://maps.app.goo.gl/Xis9pTFKJgGud6ig8',
  ),
  array(
    'image' => '10.png',
    'name'  => 'Miranda Wichhart',
    'text'  => 'My fiancé and I purchased our wedding bands, a spacer band, and my engagement ring from Loose Grown Diamonds, and the entire experience exceeded...',
    'link'  => 'https://www.reddit.com/r/LabGrownDiamonds/comments/1sp7yrp/engagement_ring_wedding_bands_the_best/',
  ),
);

$icon_dir = get_template_directory_uri() . '/aj_custom/custom-engagement-rings-aj/';
?>
<section class="custom-stories">
  <div class="container">
    <h2 class="custom-stories__title text-center">Truly Custom Stories</h2>

    <div class="custom-stories__slider js-custom-stories-slider">
      <?php foreach ( $custom_stories as $story ) : ?>
        <div class="custom-stories__slide">
          <div class="custom-stories__card">
            <div class="custom-stories__media">
              <img
                src="<?php echo esc_url( $img_dir . $story['image'] ); ?>"
                alt="<?php echo esc_attr( $story['name'] ); ?>"
                width="270" height="270" loading="lazy">
            </div>
            <p class="custom-stories__name"><?php echo esc_html( $story['name'] ); ?></p>
            <p class="custom-stories__text"><?php echo esc_html( $story['text'] ); ?></p>
            <a href="<?php echo esc_url( $story['link'] ); ?>" class="custom-stories__more">Read More</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="custom-stories__nav">
      <button type="button" class="custom-stories__arrow js-custom-stories-prev" aria-label="Previous story">
        <img src="<?php echo esc_url( $icon_dir . 'arrow-left.svg' ); ?>" alt="" width="16" height="16">
      </button>
      <button type="button" class="custom-stories__arrow js-custom-stories-next" aria-label="Next story">
        <img src="<?php echo esc_url( $icon_dir . 'arrow-right.svg' ); ?>" alt="" width="16" height="16">
      </button>
    </div>
  </div>
</section>
<script type="text/javascript">
 /**
 * Truly Custom Stories slider — Slick init
 * File: assets/js/custom-stories-slider.js
 */
document.addEventListener('DOMContentLoaded', function () {
  if (!document.querySelector('.js-custom-stories-slider')) return;
  if (!window.jQuery || typeof jQuery.fn.slick !== 'function') return;

  jQuery('.js-custom-stories-slider').slick({
    centerMode: false,
    variableWidth: true,   // card widths come from SCSS (250px / 300px)
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: '.js-custom-stories-prev',
    nextArrow: '.js-custom-stories-next',
    dots: false,
    swipeToSlide: true,
    speed: 400
  });
});
</script>
<?php
$aj_fn = $aj_ln = $aj_em = $aj_ph = '';
if ( is_user_logged_in() ) {
    $aj_u  = wp_get_current_user();
    $aj_fn = $aj_u->first_name ?: $aj_u->display_name;
    $aj_ln = $aj_u->last_name;
    $aj_em = $aj_u->user_email;
    $aj_ph = get_user_meta( $aj_u->ID, 'billing_phone', true );
}
?>
<div class="section  aj-inline-custom bg_light_gray" id="custom-ring-form" >
    <div class="container-fluid">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="font_literata mb-3 h1">Ready to Create Your Custom Ring?</h2>
            <p class="fw-400 h6 mb-0">Start your custom ring journey with our expert designers. Review your design first — and only proceed when it’s perfect</p>
        </div>
        <div class="aj-inline-custom__box">
            <p class="text-center aj-inline-custom__sub">Complimentary CAD/3D and consultation • Trusted by 27,000+ couples</p>

            <form name="aj_custom_request" class="ls-cntctform" id="aj_custom_request" action="" method="POST" enctype="multipart/form-data" novalidate>
                <div class="row row_gap_18">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="firstName" placeholder="First name*" value="<?php echo esc_attr( $aj_fn ); ?>" required>
                        <span class="error-label text-danger" data-for="firstName"></span>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="lastName" placeholder="Last name*" value="<?php echo esc_attr( $aj_ln ); ?>" required>
                        <span class="error-label text-danger" data-for="lastName"></span>
                    </div>
                    <div class="col-md-6">
                        <input type="tel" class="form-control" name="phoneNumber" placeholder="Phone number*" value="<?php echo esc_attr( $aj_ph ); ?>" required>
                        <span class="error-label text-danger" data-for="phoneNumber"></span>
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" placeholder="Your email*" value="<?php echo esc_attr( $aj_em ); ?>" required>
                        <span class="error-label text-danger" data-for="email"></span>
                    </div>
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="refSite" placeholder="Reference URL">
                    </div>
                    <div class="col-md-12">
                        <input type="file" class="form-control aj-fileupld" name="files[]" accept="image/*" multiple>
                        <div class="note-text fw-normal mt-1">Note: Upload Reference Images/URL (Highly Recommended). Inspiration photos help us match your design accurately.</div>
                        <div class="aj-imgprev d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                    <div class="col-md-12">
                        <textarea class="form-control" name="message" rows="7" required
                            placeholder="To begin your custom design, please share:&#10;• Ring size&#10;• Metal (14K / 18K, White / Yellow / Rose Gold or Platinum)&#10;• Center stone size or SKU&#10;• Reference link or upload photos above&#10;&#10;(If unsure, just tell us — our specialist will guide you.)"></textarea>
                        <span class="error-label text-danger" data-for="message"></span>
                        <div class="note-text fw-normal mt-1">Before submitting: Please ensure you've included ring size, metal, and center stone details. It helps us create your CAD &amp; 3D faster.</div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="aj-inline-submit aj-black-btn-color common_btn">Submit</button>
                    </div>
                </div>
            </form>
            <p class="mt-4 border border-danger text-danger p-2 aj-msg-error" style="display:none;"></p>
            <p class="mt-4 border border-success text-success p-2 aj-msg-success" style="display:none;"></p>
        </div>
    </div>
</div>
<style>
.aj-inline-custom__box{background:#fff;padding:32px;border:1px solid #e5e5e5;max-width:860px;margin:0 auto}
.aj-inline-custom__sub{font-size:14px;color:#777;margin-bottom:20px}
.aj-inline-custom .aj-inline-submit{width:100%;background:#000;color:#fff;border:0;padding:14px 20px;font-size:16px;text-transform:uppercase;font-weight:600}
.aj-inline-custom .aj-inline-submit:hover{background:#333}
.aj-inline-custom .aj-inline-submit:disabled{opacity:.7}
.aj-imgprev .pip{position:relative}
.aj-imgprev .pip img{width:80px;height:80px;object-fit:cover;border:1px solid #ddd}
.aj-imgprev .pip .remove{position:absolute;top:-8px;right:-8px;width:20px;height:20px;line-height:18px;text-align:center;border-radius:50%;background:#000;color:#fff;cursor:pointer;font-size:14px}
@media (max-width:767px){
  .aj-inline-custom__box{padding:16px}
  textarea.form-control{
    min-height: 200px;
  }
}
</style>
<script>
jQuery(function($){
  var $form = $('#aj_custom_request');
  if (!$form.length) return;
  var form  = $form[0];
  var $box  = $form.closest('.aj-inline-custom__box');
  var $err  = $box.find('.aj-msg-error');
  var $ok   = $box.find('.aj-msg-success');
  var files = [];

  function validate(){
    var errors = [];
    $form.find('.error-label').text('');
    $form.find('[required]').each(function(){
      var v = $.trim(this.value || ''), msg = '';
      if (!v) msg = 'This field is required.';
      else if (this.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = 'Please enter a valid email address.';
      else if (this.type === 'tel' && !/^\+?[0-9\s\-()]{7,15}$/.test(v)) msg = 'Please enter a valid phone number.';
      if (msg){
        var $l = $form.find('.error-label[data-for="'+this.name+'"]').text(msg);
        $(this).one('input change', function(){ $l.text(''); });
        errors.push(this);
      }
    });
    if (errors.length) errors[0].focus();
    return !errors.length;
  }

  // Image uploads with preview + remove
  $form.find('.aj-fileupld').on('change', function(e){
    Array.from(e.target.files).forEach(function(f){
      if (files.some(function(x){ return x.name === f.name && x.size === f.size; })) return;
      files.push(f);
      var r = new FileReader();
      r.onload = function(ev){
        $form.find('.aj-imgprev').append('<span class="pip"><img src="'+ev.target.result+'"><span class="remove">&times;</span></span>');
      };
      r.readAsDataURL(f);
    });
    $(this).val('');
  });
  $form.on('click', '.aj-imgprev .remove', function(){
    var $pip = $(this).closest('.pip');
    files.splice($pip.index(), 1);
    $pip.remove();
  });

  $form.on('submit', function(e){
    e.preventDefault();
    if (!validate()) return;

    var fd = new FormData(form);
    fd.delete('files[]');
    files.forEach(function(f){ fd.append('files[]', f); });

    var $btn = $form.find('.aj-inline-submit');
    $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-2 aj-spin"></span>');
    $err.hide(); $ok.hide();

    $.ajax({
      url: "<?php echo esc_url( site_url( '/wp-json/ls/v1/custom-setting-request' ) ); ?>",
      type: 'POST', data: fd, processData: false, contentType: false,
      success: function(res){
        if (res && res.success){
          // Reset form, uploaded files, previews and error labels
          form.reset();
          files = [];
          $form.find('.aj-imgprev').empty();
          $form.find('.error-label').text('');

          // Show success message, hide after 15 seconds
          clearTimeout($ok.data('timer'));
          $ok.html(res.success).stop(true, true).show();
          $ok.data('timer', setTimeout(function(){ $ok.fadeOut(); }, 15000));
          return;
        }
        $err.html((res && res.error) || 'An unexpected response was received.').show();
        if (res && res.fieldErrors){
          $.each(res.fieldErrors, function(name, msg){
            $form.find('.error-label[data-for="'+name+'"]').text(msg);
          });
        }
      },
      error: function(xhr){
        $err.html(xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'An error occurred. Please try again.').show();
      },
      complete: function(){
        $btn.prop('disabled', false).find('.aj-spin').remove();
      }
    });
  });

  // Other "Start / Design" buttons on this page scroll to this form
  $('[data-bs-target="#customSettingModal"]').removeAttr('data-bs-toggle').on('click', function(e){
    e.preventDefault();
    $('html,body').animate({scrollTop: $('#custom-ring-form').offset().top - 80}, 500);
  });
});
</script>
<div class="container mt-5 mb-5 mb-md-2">
  <h2 class="text-center mb-4 font_literata fw-400 h1 ">Frequently Asked Questions</h2>
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
<?php get_footer(); ?>