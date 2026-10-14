<?php

/**
 * Template Name: FAQ
 */

get_header();
?>

<!-- FAQ section -->
<div class="faq">
    <div class="banner_section">
        <div class="py_60">
            <div class="container-fluid">
                <h1 class="text-center main_title fw-600 mb-0">
                    Frequently Asked Questions
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3">
                <div class="dropdown d-block d-md-none">
                    <button class="btn btn-dark w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        About Us
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#about">About Us</a></li>
                        <li><a class="dropdown-item" href="#insurance-care">Insurance & Care</a></li>
                        <li><a class="dropdown-item" href="#orders-ring-sizes">Orders & Ring Sizes</a></li>
                        <li><a class="dropdown-item" href="#our-diamond-jewelry">Our Diamond & Jewelry</a></li>
                        <li><a class="dropdown-item" href="#lab-created-diamonds">Lab Created diamonds</a></li>
                        <li><a class="dropdown-item" href="#payments">Payments</a></li>
                        <li><a class="dropdown-item" href="#shipping">Shipping</a></li>
                        <li><a class="dropdown-item" href="#returns-refund">Returns/Refund</a></li>
                    </ul>
                </div>

                <ul class="title_list d-none d-md-block">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#insurance-care">Insurance & Care</a></li>
                    <li><a href="#orders-ring-sizes">Orders & Ring Sizes</a></li>
                    <li><a href="#our-diamond-jewelry">Our Diamond & Jewelry</a></li>
                    <li><a href="#lab-created-diamonds">Lab Created diamonds</a></li>
                    <li><a href="#payments">Payments</a></li>
                    <li><a href="#shipping">Shipping</a></li>
                    <li><a href="#returns-refund">Returns/Refund</a></li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="faq_list">
                    <div id="faq-accordion">
                    <!-- About Us  -->
                    <h2 class="main_faq">About Us</h2>
                    <div class="accordion " id="about">
                         <?php
                        global $faq_page_about;
                        ?>

                        <?php foreach ($faq_page_about as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse1<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse1<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse1<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <!-- Insurance & Care -->
                    <h2 class="main_faq">Insurance & Care</h2>
                    <div class="accordion accordion-insurance" id="insurance-care">
                        <?php
                        global $faq_page_insurance_care;
                        
                        ?>

                        <?php foreach ($faq_page_insurance_care as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse2<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse2<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse2<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Orders & Ring Sizes -->
                    <h2 class="main_faq">Orders & Ring Sizes</h2>
                    <div class="accordion accordion-order" id="orders-ring-sizes">
                        <?php
                        $faqData = [
                            [
                                'title' => 'I saw a ring on your website and love it! Can I try it on?',
                                'description' => '<div><p><strong>Absolutely!</strong> We want you to shop with complete confidence. That\'s why we offer a <strong>'.RETURN_DAYS.'-day money-back guarantee.</strong></p><p>Once your ring arrives, you can try it on in the comfort of your home and see how it looks and feels. We\'re confident you\'ll love your purchase, but if it isn\'t the perfect fit or doesn\'t meet your expectations, you can return it within '.RETURN_DAYS.'-days for a refund.</p><p>This way, you can shop with peace of mind, knowing there\'s no risk in finding the perfect ring.</p></div>'
                            ],
                            [
                                'title' => 'Can I order through your website?',
                                'description' => '<div><p><strong>Absolutely!</strong> You can securely place your order directly through our website. Unless otherwise stated, all products displayed on the Loose Grown Diamond website are available for purchase.</p></div>'
                            ],
                            [
                                'title' => 'Can I custom-design an engagement ring?',
                                'description' => '<div>
<div><strong>Absolutely!</strong> We specialize in custom engagement rings. Simply share your design idea, inspiration photos, or requirements with us through our website or email, and our team will work with you to create a one-of-a-kind ring that\'s uniquely yours.</div><p>We\'ll guide you through every step of the process, from the initial concept and Free CAD design to the finished piece, ensuring your custom ring perfectly matches your vision.</p>
</div>'
                            ],
                            [
                                'title' => 'Can I reserve my diamond if I\'m choosing a custom setting?',
                                'description' => '<div><p><strong>Absolutely!</strong> If you\'ve found a diamond you love, we recommend purchasing it as soon as possible and sharing your order number with us. This allows us to reserve your diamond while we create your custom CAD and 3D design, which typically takes up to 24-48 hours.</p><p>Because our inventory is live and in high demand, diamonds can sell quickly. Purchasing your diamond first ensures it is reserved exclusively for you while we complete your custom setting design.</p></div>'
                            ],
                            [
                                'title' => 'What if I need help placing an order?',
                                'description' => '<div>If you need any assistance placing your order, our customer support team is here to help. You can reach us 24 hours a day, 7 days a week using the contact information below.<br><br>Customer Support: <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a><br>Email: <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a></div>'
                            ],
                            [
                                'title' => 'What if I have a question about my order, want to make changes, or need to cancel it?',
                                'description' => '<div>If you have any questions about your order, need to make changes, or would like to cancel it, our customer support team is here to help.<p></p>
<p>You can contact us 24 hours a day, 7 days a week by calling <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a> or by sending us an <a href="mailto:'.SUPPORT_EMAIL.'">email</a>.</p><p>You can also check your order status at any time by logging into <a href="/my-account/">your account</a>.</p></div>'
                            ],
                            [
                                'title' => 'How do I find my ring size?',
                                'description' => '<div><p>Use our Ring Size Guide by <a href="/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf">clicking here</a>, or visit a local jeweler to have your finger professionally measured for the most accurate fit.</p></div>'
                            ],
                            [
                                'title' => 'How do I know what ring size to buy? What if I get the wrong size?',
                                'description' => '<div><p>You can use our Ring Size Guide by <a href="/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf">clicking here</a>. For the most accurate fit, we recommend visiting a local jeweler to have your finger professionally measured.<br><br><p>Don\'t worry if you order the wrong size - we offer one complimentary ring resize within '.RETURN_DAYS.' days of delivery on all eligible rings purchased from Loose Grown Diamond.</p><p><strong>Please note:</strong> Certain wedding bands cannot be resized and are not covered under this policy.</p><p>To request a resize, simply contact our support team via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'"><b>'.SUPPORT_EMAIL.'</b></a>. To help us process your request as quickly as possible, please have your order number and desired ring size ready.</p></div>'
                            ],
                            [
                                'title' => 'Do you offer free resizing for all your rings?',
                                'description' => '<div><p>Yes! We offer one complimentary ring resize within '.RETURN_DAYS.' days of delivery on all rings purchased from Loose Grown Diamond.</p><p>To request a resize, simply contact our support team via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>. To help us process your request as quickly as possible, please have your order number and desired ring size ready.</p><p>Please note: Certain wedding bands cannot be resized and are not covered under this policy.</p></div>'
                            ],
                            [
                                'title' => 'Can you engrave my ring or jewelry for free?',
                                'description' => '<div><p>Yes! We offer complimentary engraving on eligible rings and jewelry to make your piece even more personal.</p><p>When placing your order, simply enter your engraving request on the cart page. If you have any questions or special requests, our customer support team will be happy to assist you.</p><p><strong>Please note:</strong> Engraving is subject to space limitations and the design of the jewelry.</p></div>'
                            ],
                            [
                                'title' => 'Can you help keep the ring a surprise? Is Loose Grown Diamond\'s packaging discreet?',
                                'description' => '<div><p><strong>Absolutely!</strong> Simply mention it in the <strong>Order Notes</strong> section during checkout, and we\'ll make the necessary arrangements.</p><p>Your order will be shipped in plain, unbranded packaging to help keep your surprise safe.</p><p><strong>Note:</strong> If you have any special delivery instructions, feel free to include them in the <strong>Order Notes</strong> or contact our customer support team after placing your order.</p></div>'
                            ],
                            [
                                'title' => 'Are your prices in U.S. dollars?',
                                'description' => '<div>
<div><strong>Yes.</strong> All prices displayed on our website are listed in <strong>U.S. Dollars (USD).</strong></div></div><div>Please note: If you make a payment from outside the U.S., your bank or card provider may charge additional foreign exchange (forex) or currency conversion fees. These fees are determined by your bank or payment provider and are not charged by us.</div>'
                            ],
                            [
                                'title' => 'Can I place my order by phone?',
                                'description' => '<div>
<div>Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br><strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a></div>
</div>'
                            ],
                            [
                                'title' => 'Do you offer Price Matching?',
                                'description' => '<div><p>Yes! We offer a Price Match Guarantee, and in many cases, we\'ll even beat the competitor\'s price. 😊</p><p>Simply share the SKU or a link to the certified diamond you\'d like us to match, and our team will review it and provide you with our best possible price. Read our full Price Match Policy <a href="/diamond-price-match/">here</a>.</p></div>'
                            ],
                            [
                                'title' => 'Can I customize the band width?',
                                'description' => ' Yes! You can customize your ring’s band width. The standard band width depends on the size of the center diamond. If you choose a larger or smaller center stone, our design team will adjust the band width accordingly to keep the ring well-proportioned.<br><br>

For example, a ring with a 1 ct center diamond typically has a band width of 1.50 mm or less.<br><br>

To customize the band width or other ring details, please click <strong>Add Extra Feature</strong> on the product page and select your preferred band width and other available customizations.<br><br>

Please note that increasing the band width may increase the ring’s total gold weight and customization cost.'
                            ],

                        ];
                        ?>

                        <?php foreach ($faqData as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse3<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse3<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                        
                                    </button>
                                </h3>
                                <div id="order-collapse3<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Our Diamond & Jewelry -->
                    <h2 class="main_faq">Our Diamond & Jewelry</h2>
                    <div class="accordion accordion-jewelry" id="our-diamond-jewelry">
                        <?php
                        global $faq_page_diamond_jewelry;
                        ?>

                        <?php foreach ($faq_page_diamond_jewelry as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse4<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse4<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse4<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Lab Created Diamonds -->
                    <h2 class="main_faq">Lab Created Diamonds</h2>
                    <div class="accordion accordion-diamonds" id="lab-created-diamonds">
                        <?php
                        $faqData = [
                            [
                                'title' => 'Are they the same as earth-created diamonds? If not, how are they different?',
                                'description' => '<div>
<div>Lab-grown diamonds and earth-created diamonds have identical optical, chemical, and physical properties. Their sparkle is exactly the same.</div>
</div>'
                            ],
                            [
                                'title' => 'Will I still receive a physical laboratory certificate/grading report?',
                                'description' => '<div><p>Yes. Every certified lab-grown diamond is accompanied by an independent grading report from GIA, IGI, or GCAL, detailing the diamond\'s 4Cs—cut, color, clarity, and carat weight.</p><p>If your diamond is mounted in a piece of jewelry, the original laboratory grading report will be included with your order.</p><p>Please note:</p><ul><li>GIA grading reports are also available in digital format through GIA\'s online Report Check service.</li><li>As of April 9, 2023, GIA resumed issuing printed Diamond Dossier® grading reports for all newly graded diamonds, returning to the format used before January 2023.</li><li>If your diamond was previously issued with a digital-only GIA report, you may request a printed copy directly from GIA at no additional cost. Digital reports remain available for customers who prefer them.</li></ul></div>'
                            ],
                            [
                                'title' => 'Why don\'t some fancy-shaped diamonds have a cut grade listed?',
                                'description' => '<div><p>Please note that GIA, IGI, and GCAL do not assign cut grades to fancy-shaped diamonds (any shape other than round). As a result, fancy shapes such as Oval, Cushion, Emerald, Pear, Marquise, Radiant, Princess, and others will not have an official cut grade listed on our website.</p><p>Instead, we recommend evaluating these diamonds based on their proportions, symmetry, polish, and overall appearance, or consulting one of our GIA-trained gemologists for guidance.</p><p>Please note: Our inventory is sourced from 750+ trusted manufacturing partners. While we make every effort to ensure the accuracy of our listings, occasional discrepancies may occur. We strongly recommend reviewing the grading certificate before placing your order to verify all diamond specifications.</p><p>If you notice any discrepancies after receiving your order, please contact us immediately. We offer a '.RETURN_DAYS.'-day return policy, allowing you to shop with confidence. After this period, we may not be able to accommodate claims related to listing discrepancies or product specifications.</p></div>'
                            ],
                            [
                                'title' => 'Are all the diamonds listed on your website manufactured by Loose Grown Diamond?',
                                'description' => '<div><p>No. While we manufacture many of our own diamonds and jewelry, we also partner with 750+ trusted manufacturers around the world to offer a wider selection and more competitive pricing.</p><p>Every diamond listed on our website meets our quality standards, regardless of whether it is manufactured by Loose Grown Diamond or one of our trusted manufacturing partners.</p></div>'
                            ],
                            [
                                'title' => 'Do you provide ASET images for your diamonds?',
                                'description' => '<div><p>No. We do not provide ASET images for our diamonds. However, most of our certified diamonds include 360° videos and grading reports to help you evaluate their quality. If you need assistance selecting a diamond, our GIA-trained gemologists are happy to help.</p></div>'
                            ],
                            [
                                'title' => 'I\'ve received an email saying the diamond I ordered is incorrectly priced or unavailable. What should I do?',
                                'description' => '<div><p>Occasionally, our manufacturing partners may list a diamond with an incorrect price or mark it as available when it is no longer in stock. Since our inventory is sourced from multiple manufacturers, we do not have direct control over these listings.</p><p>After you place your order, we contact the manufacturer to secure the diamond. This process can take 24–48 business hours, as many of our manufacturing partners are located overseas.</p><p>If the diamond is available, we\'ll proceed with your order as normal. If it is unavailable or the price has changed, we\'ll contact you with the following options:</p><ul><li>Choose a comparable alternative diamond.</li><li>Pay the price difference (if applicable).</li><li>Cancel your order and receive a full refund.</li></ul><p>We\'ll always work with you to find the best possible solution and keep you informed throughout the process.</p></div>'
                            ],
                        ];
                        ?>

                        <?php foreach ($faqData as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse5<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse5<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse5<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Payments -->
                    <h2 class="main_faq">Payments</h2>
                    <div class="accordion accordion-payment" id="payments">
                        <?php
                        global $faq_page_payments;
                        ?>

                        <?php foreach ($faq_page_payments as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse6<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse6<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse6<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Shipping -->
                    <h2 class="main_faq">Shipping</h2>
                    <div class="accordion accordion-shipping" id="shipping">
                        <?php
                        global $shippingFaqData;
                        $faqData = $shippingFaqData;
                        ?>

                        <?php foreach ($faqData as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse7<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse7<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse7<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Returns/Refund -->
                    <h2 class="main_faq">Returns/Refund</h2>
                    <div class="accordion accordion-refund" id="returns-refund">
                        <?php
                        global $returnfaqData;
                        ?>

                        <?php foreach ($returnfaqData as $index => $faq): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#order-collapse8<?= $index ?>" aria-expanded="false"
                                        aria-controls="order-collapse8<?= $index ?>">
                                        <?= $faq['title'] ?>
                                        <?php
                                        if(isset($faq['isyoutube'])){
                                            echo '<i class="fa-brands fa-youtube text-danger fs-3"></i>';
                                        }
                                        ?>
                                    </button>
                                </h3>
                                <div id="order-collapse8<?= $index ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#faq-accordion">
                                    <div class="accordion-body">
                                        <?= $faq['description'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </div>

                    <?php 
    help_box();
    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();