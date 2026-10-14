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
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Cryptocurrency Payment FAQs</h2>
                    <div class="faq_list">
<?php
global $crypytoFaqData;
?>

<div class="accordion accordion-order" id="accordionorderExample">
    <?php foreach ($crypytoFaqData as $index => $faq): ?>
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
