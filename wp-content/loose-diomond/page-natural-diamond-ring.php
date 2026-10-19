<?php
/**
 * Template Name: Natural Diamond Ring
 *
 * Ported from the old theme's "Natural Diamond Ring (LS)" template.
 * The quote form posts to the existing REST route /wp-json/ls/v1/ask-us.
 *
 * Images/video are read from  <theme>/rrp-img/natural-diamond-ring/  :
 *   hero-bg.jpg, Deccans.png, checkersaga-logo.png, age.png, banner-video.mp4,
 *   Review3.jpg, Review2.jpg, Review1.jpg, Review5.jpg,
 *   Customer1.webp, Customer2.webp, Customer4.webp, TrustPilot-review-btn.svg,
 *   icon-2.svg, icon-1-1.svg, Arrow.svg, Order.svg, Call.svg, Ship.svg
 */

get_header();

$img = get_template_directory_uri() . '/rrp-img/natural-diamond-ring/';

$reviews = [
    [ 'Perfect! I love this ring.', 'Perfect! I love this ring. It has so much sparkle for such a dainty ring! Complements my other rings really well! Exactly what I wanted!! This came quickly and is great quality! Ships via UPS requires someone to be present at delivery, it can be tricky to receive if you are not at home all day. Make sure to use UPS tracking to know when you should be home.', 'Victoria Calva', 'Madrid, Spain', 'Customer1.webp' ],
    [ 'My fiancée and I are so happy...', 'My fiancée and I are so happy with our experiences through RRP Diamond. The service has been wonderful and the quality is exceptional. Thank you so much for ensuring quality product and happy customers. It’s nice to shop for something like this, without feeling the pressure to buy, and the pushiness from a sales person. We truly appreciate what you guys do!', 'Ravi Desai', 'Dubai, UAE', 'Customer2.webp' ],
    [ 'My ring is so beautiful!', 'My ring is so beautiful! The quality of ring is amazing and the picture doesn’t do it justice. I also really love the engraving option! It looked great. It is exactly what I had in mind. Shipping was extremely fast, I had the ring in less than a week I highly recommend buying jewelry from RRP, you wont be disappointed. I will definitely be shopping here come anniversary time!', 'Jenny L', 'Boston, USA', 'Customer4.webp' ],
];
$steps = [
    [ 'Order.svg', 'Ask for Free Quote', 'Ask us for a free quote with your desired jewelry design and we will send it to you.' ],
    [ 'Call.svg',  'Make a Payment',     'Once you have finalized the design and agree with the price, proceed to make the payment.' ],
    [ 'Ship.svg',  'Relax While We Ship', 'Once the payment has been made, relax and track your ring.' ],
];
?>
<main id="main" class="ndr">

    <!-- Hero -->
    <section class="ndr-hero" style="background-image:url('<?php echo esc_url( $img . 'hero-bg.jpg' ); ?>');">
        <div class="container">
            <h1 class="ndr-hero__title">Looking for a Ring at<br><span class="ndr-hero__tag">lowest rate</span> in the<br>world?</h1>
            <p class="ndr-hero__sub">You’re at the right Place.</p>
        </div>
    </section>

    <!-- Featured in -->
    <section class="ndr-featured">
        <div class="container">
            <h2 class="ndr-ttl">We’ve been featured in</h2>
            <div class="row align-items-center text-center row-gap-3">
                <div class="col-md-4"><img class="img-fluid" src="<?php echo esc_url( $img . 'Deccans.png' ); ?>" alt="Deccan Chronicle" loading="lazy"></div>
                <div class="col-md-4"><img class="img-fluid" src="<?php echo esc_url( $img . 'checkersaga-logo.png' ); ?>" alt="Checkersaga" loading="lazy"></div>
                <div class="col-md-4"><img class="img-fluid" src="<?php echo esc_url( $img . 'age.png' ); ?>" alt="The Asian Age" loading="lazy"></div>
            </div>
        </div>
    </section>

    <!-- Video -->
    <section class="ndr-video">
        <video autoplay loop muted playsinline>
            <source src="<?php echo esc_url( $img . 'banner-video.mp4' ); ?>" type="video/mp4">
        </video>
    </section>

    <!-- Quote form -->
    <section class="ndr-req">
        <div class="container text-center">
            <h2 class="ndr-ttl">Ask For A Free Quote For Your Ring</h2>
            <p class="ndr-sub">Fill in the form below and we will give you a quote, It’s Free</p>

            <form class="ndr-form" id="dimond_ring_request" novalidate>
                <div class="row row-gap-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="first-name" placeholder="First name">
                        <small class="text-danger text-start d-block" data-err="first-name"></small>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="last-name" placeholder="Last name">
                        <small class="text-danger text-start d-block" data-err="last-name"></small>
                    </div>
                    <div class="col-md-6">
                        <input type="tel" class="form-control" name="phone" placeholder="Mobile Number">
                        <small class="text-danger text-start d-block" data-err="phone"></small>
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" placeholder="Your email">
                        <small class="text-danger text-start d-block" data-err="email"></small>
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" name="message" rows="4" placeholder="Your message"></textarea>
                        <small class="text-danger text-start d-block" data-err="message"></small>
                    </div>
                </div>
                <input type="hidden" name="reference_url" value="<?php echo esc_url( get_permalink() ); ?>">
                <button type="submit" class="ndr-form__btn">Ask for quote</button>
                <p class="ndr-form__msg text-success" id="ndr-success" hidden></p>
                <p class="ndr-form__msg text-danger" id="ndr-error" hidden></p>
            </form>
        </div>
    </section>

    <!-- Jewellery made by us -->
    <section class="ndr-made">
        <div class="container text-center">
            <h2 class="ndr-ttl">Jewellery made by us</h2>
            <p class="ndr-sub">Here are some of the jewelry pieces made by us for our international customers</p>
        </div>
        <div class="container-fluid px-0">
            <div class="row g-1 mx-0">
                <?php foreach ( [ 'Review3.jpg', 'Review2.jpg', 'Review1.jpg', 'Review5.jpg' ] as $file ) : ?>
                    <div class="col-6 col-md-3"><img class="img-fluid w-100" src="<?php echo esc_url( $img . $file ); ?>" alt="Jewellery made by RRP Diamond" loading="lazy"></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Reviews -->
    <section class="ndr-reviews">
        <div class="container">
            <h2 class="ndr-ttl text-center">See what our customers are saying</h2>
            <div class="row row-gap-4">
                <?php foreach ( $reviews as $r ) : ?>
                    <div class="col-md-4">
                        <div class="ndr-review">
                            <h3 class="ndr-review__ttl"><?php echo esc_html( $r[0] ); ?></h3>
                            <p class="ndr-review__txt"><?php echo esc_html( $r[1] ); ?></p>
                            <div class="ndr-review__who">
                                <img src="<?php echo esc_url( $img . $r[4] ); ?>" alt="<?php echo esc_attr( $r[2] ); ?>" width="40" height="40" loading="lazy">
                                <div>
                                    <strong><?php echo esc_html( $r[2] ); ?></strong>
                                    <span><?php echo esc_html( $r[3] ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-center mt-4">
                <a href="https://www.trustpilot.com/review/rrpdiamonds.com" target="_blank" rel="noopener"><img src="<?php echo esc_url( $img . 'TrustPilot-review-btn.svg' ); ?>" alt="More reviews on Trustpilot" height="44"></a>
            </p>
        </div>
    </section>

    <!-- How you get lowest price -->
    <section class="ndr-low">
        <div class="container text-center">
            <h2 class="ndr-ttl">How you get lowest price?</h2>
            <p class="ndr-sub">Our prices are lower than others, because we have our own diamond manufacture company called RRP Diamond and it’s all we give you directly. No Middleman.</p>
            <div class="ndr-low__row">
                <div class="ndr-low__box">
                    <img src="<?php echo esc_url( $img . 'icon-2.svg' ); ?>" alt="" loading="lazy">
                    <h4>RRP Diamond + Jeweler</h4>
                    <small>(We as a Manufacturer)</small>
                </div>
                <div class="ndr-low__arrow">
                    <small>Directly Selling To</small>
                    <img src="<?php echo esc_url( $img . 'Arrow.svg' ); ?>" alt="" loading="lazy">
                </div>
                <div class="ndr-low__box">
                    <img src="<?php echo esc_url( $img . 'icon-1-1.svg' ); ?>" alt="" loading="lazy">
                    <h4>You (as Buyer)</h4>
                    <small>(Customer like You)</small>
                </div>
            </div>
        </div>
    </section>

    <!-- How to order -->
    <section class="ndr-order">
        <div class="container text-center">
            <h2 class="ndr-ttl">How to order</h2>
            <div class="row row-gap-4">
                <?php foreach ( $steps as $s ) : ?>
                    <div class="col-md-4">
                        <img src="<?php echo esc_url( $img . $s[0] ); ?>" alt="" loading="lazy">
                        <h5><?php echo esc_html( $s[1] ); ?></h5>
                        <p><?php echo esc_html( $s[2] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<style>
.ndr { padding: 0; }
.ndr-ttl { font-size: 18px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; text-align: center; }
.ndr-sub { font-size: 12px; color: #333; margin-bottom: 25px; text-align: center; }

.ndr-hero { background-color: #14142b; background-position: center; background-repeat: no-repeat; background-size: cover; padding: 140px 0 160px; color: #fff; }
.ndr-hero__title { color: #fff; font-size: 40px; line-height: 70px; letter-spacing: 1px; font-weight: 500; text-align: left; margin: 0; }
.ndr-hero__tag { background: #fff; color: #000; border-radius: 5px; padding: 5px 10px; }
.ndr-hero__sub { color: #fff; font-size: 30px; line-height: 37px; letter-spacing: 1px; font-weight: 500; margin: 0; }

.ndr-featured { padding: 80px 0; }
.ndr-featured .ndr-ttl { margin-bottom: 30px; }

.ndr-video { padding-bottom: 100px; }
.ndr-video video { width: 100%; height: 100%; display: block; }

.ndr-req { padding: 80px 0; background: #f9f9f9; }
.ndr-form { width: 70%; margin: 30px auto 0; }
.ndr-form .form-control { border-radius: 4px; padding: 12px; font-size: 13px; }
.ndr-form .iti { width: 100%; }
.ndr-form__btn { background: #000; color: #fff; border: 1px solid #000; border-radius: 4px; padding: 14px 50px; font-size: 15px; line-height: 15px; text-transform: uppercase; float: right; margin-top: 15px; }
.ndr-form__btn:hover { background: #fff; color: #000; }
.ndr-form__btn:disabled { opacity: .6; }
.ndr-form__msg { clear: both; padding-top: 15px; }

.ndr-made { padding: 80px 0 0; }

.ndr-reviews { padding: 80px 0; background: #f9f9f9; }
.ndr-review { background: #fff; border-radius: 8px; padding: 20px; height: 100%; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
.ndr-review__ttl { font-size: 14px; font-weight: 600; margin-bottom: 10px; }
.ndr-review__txt { font-size: 11px; line-height: 1.7; color: #555; }
.ndr-review__who { display: flex; align-items: center; gap: 10px; margin-top: 15px; }
.ndr-review__who img { border-radius: 50%; object-fit: cover; }
.ndr-review__who strong { display: block; font-size: 12px; }
.ndr-review__who span { font-size: 10px; color: #888; }

.ndr-low { padding: 80px 0; }
.ndr-low__row { display: flex; align-items: center; justify-content: center; gap: 30px; flex-wrap: wrap; }
.ndr-low__box { background: #f2f2f2; border: 1px solid #e3e3e3; border-radius: 4px; padding: 30px 40px; min-width: 260px; }
.ndr-low__box img { height: 28px; margin-bottom: 15px; }
.ndr-low__box h4 { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
.ndr-low__arrow small { display: block; font-size: 11px; margin-bottom: 6px; }

.ndr-order { padding: 80px 0; }
.ndr-order .ndr-ttl { margin-bottom: 40px; }
.ndr-order img { height: 28px; margin-bottom: 18px; }
.ndr-order h5 { font-size: 16px; font-weight: 600; color: #353a40; margin-bottom: 8px; }
.ndr-order p { font-size: 11px; color: #555; margin: 0; }

@media (max-width: 768px) {
    .ndr-hero { background-position: right; }
    .ndr-hero__title { font-size: 30px; line-height: 55px; }
    .ndr-hero__sub { font-size: 25px; line-height: 30px; }
    .ndr-form { width: 100%; }
    .ndr-form__btn { width: 100%; }
}
@media (max-width: 480px) {
    .ndr-hero { padding: 40px 0 300px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('dimond_ring_request');
    if (!form) return;

    var ok  = document.getElementById('ndr-success');
    var bad = document.getElementById('ndr-error');
    var btn = form.querySelector('.ndr-form__btn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = function (n) { var el = form.elements[n]; return el ? el.value.trim() : ''; };

        var errs = {};
        if (!v('first-name')) errs['first-name'] = 'Required';
        if (!v('last-name'))  errs['last-name']  = 'Required';
        if (!v('phone'))      errs.phone = 'Required';
        else if (!/^\+?[0-9\s\-()]{7,20}$/.test(v('phone'))) errs.phone = 'Enter a valid phone number';
        if (!v('email')) errs.email = 'Required';
        else if (!/^\S+@\S+\.\S+$/.test(v('email'))) errs.email = 'Enter a valid email';
        if (!v('message')) errs.message = 'Required';

        form.querySelectorAll('[data-err]').forEach(function (el) {
            el.textContent = errs[el.getAttribute('data-err')] || '';
        });
        ok.hidden = bad.hidden = true;
        if (Object.keys(errs).length) return;

        var body = new URLSearchParams();
        body.set('name', v('first-name') + ' ' + v('last-name'));
        body.set('email', v('email'));
        body.set('phone', v('phone'));
        body.set('message', v('message'));
        body.set('reference_url', v('reference_url'));

        btn.disabled = true;
        fetch(<?php echo wp_json_encode( rest_url( 'ls/v1/ask-us' ) ); ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
            if (res.ok) {
                ok.textContent = 'Thank you for your enquiry. We will be in touch shortly.';
                ok.hidden = false;
                form.reset();
            } else {
                bad.textContent = (res.j && res.j.error) || 'Something went wrong. Please try again.';
                bad.hidden = false;
            }
        })
        .catch(function () {
            bad.textContent = 'Something went wrong. Please try again.';
            bad.hidden = false;
        })
        .finally(function () { btn.disabled = false; });
    });
});
</script>
<?php get_footer(); ?>
