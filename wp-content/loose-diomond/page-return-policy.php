<?php

/**
 * Template Name: Return And Refund Policy
 */

get_header();
?>

<!-- Return and Refund section start -->
<div class="return_section mx-auto">
    <div class="banner_section">
        <div class="py_60 banner_inner">
            <div class="container mb_35 first_section oc-return-policy">
                <div class="row">
                       <h1 class="h2 text_black_secondary fw-600 mb_10 text-center oc-return-policy-title">Return/Refund</h1>
                       <p class="ls_subtxt fw-400 mb-2 mb-md-3">Thanks for shopping at RRP Diamond.</p>
                       <p class="ls_subtxt fw-400 mb-2 mb-md-3">If you are not entirely satisfied with your purchase, we’re here to help.</p>

                       <div class="mb_50">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Return</h2>
                          <ul>
                             <li class="fw-400 ls_subtxt">You have <strong>7 calendar days</strong> <strong>to return an item from the date you received it.</strong></li>
                             <li class="fw-400 ls_subtxt">To be eligible for a return, your item must be unused and in the same condition that you received it. All returned items must be returned with all documentation (including Natural diamonds certificate).</li>
                             <li class="fw-400 ls_subtxt">Your item must be in the original packaging.</li>
                          </ul>
                       </div>
                       <div class="mb_50">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Refunds</h2>
                          <ul>
                             <li class="fw-400 ls_subtxt">Once we receive your item, we will inspect it and notify you that we have received your returned item.</li>
                             <li class="fw-400 ls_subtxt">We will immediately notify you on the status of your refund after inspecting the item.</li>
                             <li class="fw-400 ls_subtxt">If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within a 1-3 working days, depending on your card issuer’s policies.</li>
                          </ul>
                       </div>
                       <div class="mb_50">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Shipping</h2>
                          <ul>
                             <li class="fw-400 ls_subtxt">We will be responsible for paying for your shipping costs for returning your item.</li>
                             <li class="fw-400 ls_subtxt">Return Shipping costs are <strong>FREE</strong>.</li>
                          </ul>
                       </div>
                       <div class="">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Contact Us</h2>
                          <ul>
                             <li class="fw-400 ls_subtxt">If you have any questions on how to return your item to us, <a href="/contact-us/">contact us.</a></li>
                             <li class="fw-400 ls_subtxt"><strong>Orders that have been exchanged, re-sized, engraved and custom pieces are not eligible for return and shall be considered a final sale. Previously returned orders will be considered an exchange.</strong></li>
                          </ul>
                       </div>
                    </div>
            </div>
            <div class="container second_section">
                <div class="row">
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Return/Refund FAQ’s</h2>
                    <div class="faq_list">
                       <?php
$return_faqs = [
    [
        'title'       => 'Why is the return policy of RRP Diamond only for 7-days?',
        'description' => '<p>As we are both diamond manufacturers and sellers it should be noted that we in no way include any extra charges to our final prices, like ‘middle-men fee’. Something which is commonly billed when purchasing from a diamond retailer. With a minimal profit margin we are giving you the best quality diamonds as swiftly as we can, hence we keep a 7-day return policy to ensure maximum efficiency for all our customers.</p><p>Many times 3-4 customers end up liking the same diamond/diamond jewelry, if we sell it to someone and they want to return it within 7-days we are then able to sell that diamond to another interested buyer. This helps us maintain the market balance.</p>',
    ],
    [
        'title'       => 'What is RRP’s Cancellation Policy?',
        'description' => 'All Orders are eligible for cancellation when it is in packed/shipped status, as long as the cancel option is available.',
    ],
    [
        'title'       => 'I just cancelled my order. When will I receive my refund?',
        'description' => 'You will receive refund into the source account within 1-3 days from the time of order cancellation.',
    ],
    [
        'title'       => 'Can I modify the shipping address of my order after it has been placed?',
        'description' => 'Yes, please <strong><a href="/contact-us/">contact us</a></strong> to change your address.',
    ],
    [
        'title'       => 'What is the time-period within which I can return a product?',
        'description' => 'Products can be returned within 7 days of receiving the product. Post this, return is not allowed.',
    ],
    [
        'title'       => 'How can I initiate a return?',
        'description' => 'You will have to send us an email along with order number &amp; the reason for your return.',
    ],
    [
        'title'       => 'When are returns not possible?',
        'description' => 'We cannot accept returns after return timeline has lapsed. Product must have in the same condition (unused)along with the certificate.(if apply).<br>We cannot accept returns on CUSTOM ORDER.',
    ],
    [
        'title'       => 'Can special/bespoke orders returned &amp; eligible for refund?',
        'description' => 'Special orders cannot be returned. A ring plus center diamond that we mount together – such as the Design your Own rings – is not considered a special order.',
    ],
    [
        'title'       => 'Can I get a replacement for the product I’ve ordered?',
        'description' => 'Unfortunately, we can’t replace products that you’ve already ordered. Instead, you can request a return and place a separate order for the new products you want to order.',
    ],
    [
        'title'       => 'What is the pick-up process for the return of a product?',
        'description' => 'Once you’ve made a return request with us via email, our team will get back you with the shipping address.',
    ],
    [
        'title'       => 'Why has my return request been rejected??',
        'description' => 'It’s unfortunate, but the reason for making the return does not comply with our policy.',
    ],
    [
        'title'       => 'What is the mode of payment for refunds?',
        'description' => 'All refunds are credited back to the account/card used to place the original order.',
    ],
    [
        'title'       => 'How will I know that my refund has been initiated?',
        'description' => 'You’ll never have to ask. We’ll send you an email confirming the initiation of your refund. Your refund should reach you within 3-4 working days.',
    ],
    [
        'title'       => 'I still haven’t got my refund. Why?',
        'description' => 'If we’ve sent you a confirmation on the approval of your refund, you will definitely get your refund. However, in rare cases, we face technical difficulties that can delay refund transfers. If the wait seems too long, <a href="/contact-us/"><strong>contact us</strong></a> for support.',
    ],
];
?>
                        <div class="accordion accordion-order" id="accordionorderExample">
                            <?php foreach ($return_faqs as $index => $faq): ?>
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
<!-- Return and Refund section end -->

<?php
get_footer();