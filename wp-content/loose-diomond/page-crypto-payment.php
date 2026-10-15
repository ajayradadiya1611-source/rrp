<?php

/**
 * Template Name: Crypto Payment
 */

get_header();
?>

<!-- Crypto section start -->
<div class="cryto_payment mx-auto">
    <div class="banner_section">
        <div class="py_60 banner_inner">
            <div class="container mb_50 first_section">
                <div class="row">
                    <h1 class="h2 text_black_secondary fw-600 mb_25 text-center">Crypto Payment</h1>
                    <div class="col-md-8">
                        <p class="mb-0">TRC20 Address :</p>
                        <div class="d-flex  mb_20">
                            <p class="me-2 mb-0" id="p1">TGHhWnoczXqbj9WttDu4AEUFEVVs2uFNR4</p>
                            <span onclick="copyToClipboard('#p1')" class="lscopy_txt">
                                <i class="fa fa-clone"></i>
                                <span class="ls_copied">Copied</span>
                            </span>
                        </div>
                        <ul>
                            <li>Send <strong>only USDT</strong> to this deposit address.</li>
                            <li>Ensure you are using the <strong>Tron (TRC20) network.</strong></li>
                            <li>Do not send <strong>NFTs</strong> to this address.</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <img src="<?php echo get_template_directory_uri();?>/aj_custom/comman/USDTCode.png"
                            alt="qr_code" class="img-fluid qr_image">
                    </div>
                </div>

            </div>
            <div class="container second_section">
                <div class="row">
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Payment FAQ’s</h2>
                    <div class="faq_list">
<?php
$crypto_faqs = [
    [
        'title'       => 'What types of payment do you accept?',
        'description' => '<p>We accept all major credit cards (Visa, MasterCard, American Express, and Discover), PayPal as well as Crypto.</p><p>Many customers purchasing items valued over $1,000 prefer paying by wire/wise transfer. <strong>Wire transfer orders receive a 2% discount from our listed price.</strong> Customers paying by wire will receive wiring instructions once they have placed the order. We must receive confirmation from our bank that a wire transfer has been completed before items can be shipped.</p><p>If you have any questions, Please <a href="/contact-us/"><strong>contact us</strong></a> for or <strong>call/WhatsApp us at +91 9016106290</strong></p><p>PS: If we didn’t received the order payment within 3 working business days, the order will be cancelled due to non-payment &amp; we are not responsible for the same.</p>',
    ],
    [
        'title'       => 'Will I receive a discount if I pay by Crypto?',
        'description' => '<div class="faq_item_p">Yes. Bank wire/wise.com/Crypto customers receive a 2% discount off their entire order. Please request coupon code from our <a href="/contact-us/"><strong>support</strong></a>.</div>',
    ],
    [
        'title'       => 'Will I be charged sales tax on my order?',
        'description' => '<p>No, We don’t collects sales tax on orders that are shipped from our South Asia Locations.</p>',
    ],
    [
        'title'       => 'Can I pay with two different credit cards?',
        'description' => '<p>Yes. If you’d like pay with two different cards, please place your order over the phone with customer service at +91 9016106290. We are available 24 hours a day, 7 days a week.</p>',
    ],
    [
        'title'       => 'Do you offer Financing/EMI?',
        'description' => '<p>No. as of now we don’t offer a EMI/finance.</p>',
    ],
];
?>

<div class="accordion accordion-order" id="accordionorderExample">
    <?php foreach ($crypto_faqs as $index => $faq): ?>
        <div class="accordion-item">
            <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#order-collapse<?= $index ?>" aria-expanded="false"
                    aria-controls="order-collapse<?= $index ?>">
                    <?= $faq['title'] ?>
                </button>
            </h3>
            <div id="order-collapse<?= $index ?>" class="accordion-collapse collapse"
                data-bs-parent="#accordionorderExample">
                <div class="accordion-body">
                    <?= $faq['description'] ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
                         <?php 
    help_box();
    ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Crypto section end -->

<?php
get_footer();
