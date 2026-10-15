<?php

/**
 * Template Name: FAQ
 */

get_header();

$faq_sections = [
    [
        'id'    => 'about',
        'title' => 'About Us',
        'class' => '',
        'items' => [
            [
                'title'       => 'What is RRP Diamond?',
                'description' => '<div>RRP Diamond is a Surat based manufacturing company which deals only in the manufacturing and selling of Natural diamonds/jewelry.</div>',
            ],
            [
                'title'       => 'Do you have a retail store located in my state?',
                'description' => '<div><p>RRP Diamond has a factory located in Surat city. You would find our factory on 177 Ghanshyam Nagar, remember to book an appointment with us in advance and will be more than happy to meet you. <b>We at RRP Diamond, operate mostly online. Operating online saves us operational costs and also helps us in keeping all our attention on the quality and manufacturing of your diamond. The reason for our high quality service is primarily that we focus all our attention on our service and hence you get a much lower price on our product then you would in comparison to any other local jewelry store or online store.</b></p></div>',
            ],
            [
                'title'       => 'Do you have gemologists on staff?',
                'description' => '<div>Absolutely, we currently employ graduates from the Gemological Institute of America. All of the graduates are an essential part of our team. They help us in offering great customer service. If our consumers have any sort of queries regarding our service or product you can get in touch with our staff gemologists.</div>',
            ],
            [
                'title'       => 'Where are the corporate office of RRP Diamond located?',
                'description' => '<div><p>Our corporate office is located in Surat city. You will find our office in the Diamond District. The exact address of our office is listed below.</p><p>RRP Diamond Corporate Office<br>
301, Ashok chamber, Moti-Bazaar,<br>
Mahidharpura, Surat, Gujarat – 395008</p></div>',
            ],
        ],
    ],
    [
        'id'    => 'insurance-care',
        'title' => 'Insurance & Care',
        'class' => 'accordion-insurance',
        'items' => [
            [
                'title'       => 'Do you clean or provide care for jewelry sold on your site?',
                'description' => '<div>NO, we do not provide cleaning services or external care for the diamond jewelry sold on our website, the <strong>reason behind this is that we already offer the lowest price possible on our product. We do not include any middleman cost or extra margin on our product. Including any other cost like cleaning would not be financially feasible for our business.</strong></div>',
            ],
            [
                'title'       => 'What is covered by your manufacturing warranty?',
                'description' => '<div><p>Our warranty covers any manufacturing defects that you might find the moment you received a product. Our warranty also covers prong-tightening, re-polishing and rhodium plating but only within the seven days of purchase. We give full guarantee that all our products will be free from manufacturing defects but in case you do find any please reach out to us. If you encounter any other problem with a product which is not covered under the warranty you can always reach out to us. We will ask for your product and after being inspected by our gemologist we shall let you know the charges for our services.</p></div>',
            ],
            [
                'title'       => 'Do you provide insurance on jewelry?',
                'description' => '<div>We only provide insurance on our guaranteed shipping, but not on our products.</div>',
            ],
        ],
    ],
    [
        'id'    => 'orders-ring-sizes',
        'title' => 'Orders & Ring Sizes',
        'class' => 'accordion-order',
        'items' => [
            [
                'title'       => 'I saw a ring on your site, and I love it! Can I try it on?',
                'description' => '<div>We provide a money back guarantee. Once you receive the product, feel free to try it on and see how it looks. We at RRP Diamonds, have full faith in our products and believe that you will absolutely love them after you receive them. In case you are not satisfied with the product you can avail the <strong>money back guarantee and return our product within 7-days.</strong></div>',
            ],
            [
                'title'       => 'Can I order through your website?',
                'description' => '<div>Yes absolutely you can order through our website. All the products which are displayed on RRP Diamond’s website are available for purchase (unless stated otherwise).</div>',
            ],
            [
                'title'       => 'Can I custom design an engagement ring?',
                'description' => '<div>Yes absolutely, you can ask for your desired design and get in touch with us through our website. We will be more than happy to design a customized engagement ring for you.</div>',
            ],
            [
                'title'       => 'What if I need help placing an order?',
                'description' => '<div>If you need any sort of assistance from us the number mentioned below is available 24 hours a day, 7 days a week. Customer service: +91 9016106290 or info@rrpdiamonds.com</div>',
            ],
            [
                'title'       => 'What if I have a question about my order, want to change it, or need to cancel it?',
                'description' => '<div><p>For questions about your order, call Customer Service at +91 9016106290, 24 hours a day, 7 days a week, or send an <a href="mailto:info@rrpdiamonds.com">e-mail</a>. You can check your order status online by clicking <a class="link--2Z1lc" href="/my-account/" target="_blank" rel="noindex noopener">here</a>. You will need to login.</p></div>',
            ],
            [
                'title'       => 'I’d like to place an international order. Do I pay customs fees to RRP or to FedEx/UPS?',
                'description' => '<div>We ship all International orders for free. Orders priced below $1500 are shipped through priority mail by UPS. Orders which are valued above $1500 are given insured shipping by FedEx/ UPS. Once you have received your package the courier will get in touch with you to get any duty taxes or fees on your product.</div>',
            ],
            [
                'title'       => 'What if my shipment is stuck in the Customs?',
                'description' => '<div>We are not responsible for the customs clearance, in any case if you are not able to clear the customs, we are not liable &amp; responsible.</div>',
            ],
            [
                'title'       => 'how do I get my ring size?',
                'description' => '<div>Please <a href="https://www.rrpdiamonds.com/wp-content/uploads/2022/06/RRP-Diamond-Ring-Sizer.pdf">click here</a> for a ring size chart option. OR We suggest you go to a local jewelry store and get your exact ring size measured from there for 100% accuracy.</div>',
            ],
            [
                'title'       => 'Can you engrave my ring/jewelry?',
                'description' => '<div>We provide free customization/personalization services and would be more than happy to engrave a ring/jewelry for you.</div>',
            ],
        ],
    ],
    [
        'id'    => 'our-diamond-jewelry',
        'title' => 'Our Diamond & Jewelry',
        'class' => 'accordion-jewelry',
        'items' => [
            [
                'title'       => 'Are your Natural diamonds Sustainable and Conflict Free?',
                'description' => '<div>Yes, Natural diamonds are sustainable and conflict free.</div>',
            ],
            [
                'title'       => 'I found a diamond on your website that I like. Is it available?',
                'description' => '<div>Absolutely, all of the products available on our website are up for purchase.</div>',
            ],
            [
                'title'       => 'How often do you add diamonds to your inventory?',
                'description' => '<div>We add new diamonds to our inventory almost everyday.</div>',
            ],
            [
                'title'       => 'What if the diamond does not have the 360 video?',
                'description' => '<div>Do not hesitate, many time diamonds video are not uploaded. Just contact us and we will share you the 360 video/ live video of the diamond.</div>',
            ],
            [
                'title'       => 'How can I make sure the diamonds I receive are actually the diamonds I ordered?',
                'description' => '<div>When we ship a product to our customers we also provide them with an independent and IGI/GIA report to certify their quality and that you’ve received the exact product you ordered. You can take our product to a local gemologist and compare them against all of the properties mentioned in the report to cross verify the credibility of your product.</div>',
            ],
            [
                'title'       => 'What is HOLD on the diamond?',
                'description' => '<div>We at RRP Diamond provide an option to customers of ‘holding a diamond’. In this option a customer has to notify us that they are interested in a diamond but want to confirm with friends/family/spouse as to whether they should purchase it or not. On doing so, we hold the diamond for them so that they can make a decision with ease. It works on <strong>first come first serve basis </strong></div>',
            ],
        ],
    ],
    [
        'id'    => 'natural-diamonds',
        'title' => 'Natural Diamonds',
        'class' => 'accordion-diamonds',
        'items' => [
            [
                'title'       => 'Are they same as lab-created diamonds? If not, how are they different?',
                'description' => '<div>Natural diamonds obtain their value from their uniqueness and rarity as billion-year-old precious gems older than life itself. Lab-grown diamonds are currently sold at a slightly reduced price compared to natural diamonds, but the cost continues to decline due to mass production. Therefore, they have little to no resale value.</div>',
            ],
            [
                'title'       => 'Will I still receive a laboratory certificate?',
                'description' => '<div><p>Yes –  natural diamond will be accompanied by an independent laboratory (GIA/IGI) report detailing its “4 Cs” – cut, color, clarity, and carat weight. If your diamond is mounted into jewelry, it will also be accompanied by an independent laboratory.</p></div>',
            ],
            [
                'title'       => 'Why some of the fancy shapes does not have the cut grade listed on the website?',
                'description' => '<div><p>Please note GIA and IGI does not provide cut ratings for fancy cut stones, i.e any shape outside of round. That’s why some of the fancy shapes does not have the cut grade listed on the website.</p></div>',
            ],
            [
                'title'       => 'Will the return policy be the same?',
                'description' => '<div>Yes! We stand behind all our products and want you to be 100% satisfied with your purchase. You may return your diamond/jewelry piece for a full refund within 7 days.</div>',
            ],
        ],
    ],
    [
        'id'    => 'payments',
        'title' => 'Payments',
        'class' => 'accordion-payment',
        'items' => [
            [
                'title'       => 'What types of payment do you accept?',
                'description' => '<div><p>We accept all major credit cards (Visa, MasterCard, American Express, and Discover), PayPal as well as Crypto.</p><p>Many customers purchasing items valued over $1,000 prefer paying by wire/wise transfer. Customers paying by wire will receive wiring instructions once they have placed the order. We must receive confirmation from our bank that a wire transfer has been completed before items can be shipped.</p><p>If you have any questions, Please <a href="/contact-us/"><strong>contact us</strong></a> for or <strong>call/WhatsApp us at +91 9016106290</strong></p></div>',
            ],
            [
                'title'       => 'Will I receive a discount if I pay by bank wire?',
                'description' => '<div>No.</div>',
            ],
            [
                'title'       => 'Will I be charged sales tax on my order?',
                'description' => '<div>No, We don’t collects sales tax on orders that are shipped from our South Asia Locations.</div>',
            ],
            [
                'title'       => 'Can I pay with two different credit cards?',
                'description' => '<div>Yes. If you’d like pay with two different cards, please place your order over the phone with customer service at +91 9016106290. We are available 24 hours a day, 7 days a week.</div>',
            ],
            [
                'title'       => 'Do you offer Financing/EMI?',
                'description' => '<div>No. As of now we don’t offer a EMI/finance.</div>',
            ],
        ],
    ],
    [
        'id'    => 'shipping',
        'title' => 'Shipping',
        'class' => 'accordion-shipping',
        'items' => [
            [
                'title'       => 'Do you Ship WORLDWIDE?',
                'description' => '<div>Yes, we do ship <strong>WORLDWIDE.</strong></div>',
            ],
            [
                'title'       => 'When will my order ship?',
                'description' => '<div><p>Free Shipping: Orders typically <strong>ship within 3 to 5 days.</strong> Express Shipping($125): Orders typically <strong>ship within 2 to 3 days.</strong></p><p><strong><i>Transit time is not included in the lead times stated above</i></strong> because transit times vary so dramatically.</p><p>If you need an order to ship by, or arrive by, an specific date we urge you to type in special instructions in the space provided during check out. We’ll do our best to exceed your request or attempt to reach you and advise the best we can do.</p><p>Note: We do not shipped on Weekends &amp; Holidays.</p></div>',
            ],
            [
                'title'       => 'Where is my diamond shipped from?',
                'description' => '<div>The diamonds are shipped from the India.</div>',
            ],
            [
                'title'       => 'Is free shipping Insured?',
                'description' => '<div>No, The shipment under free shipping is not Insured, If the good lost, We are not liable &amp; responsible.</div>',
            ],
            [
                'title'       => 'What’s included in my order?',
                'description' => '<div>In the box, you will receive Diamond/Ring, Hard Copy of IGI/GIA certificate (if applicable)</div>',
            ],
            [
                'title'       => 'When Does the shipment come with insurance?',
                'description' => '<div>If you have chose the Insured Shipping, on the checkout page, The shipment will be will ship with full insurance via Malca Amit and Ferrari, FedEx/UPS/Aramex.</div>',
            ],
            [
                'title'       => 'I received my diamond but I didn’t received the physical certificate Why?',
                'description' => '<div>For all the orders below $1,500 cart total – The diamond certificate shipped with individual shipment via USPS/UPS/FedEx/Aramex.</div>',
            ],
            [
                'title'       => 'How much time will it take to deliver it to my home?',
                'description' => '<div>Once it is dispatched, it will take 5-7 working days to deliver at your doorstep.</div>',
            ],
            [
                'title'       => 'Can I track my order online?',
                'description' => '<div>You can obtain the status of your current orders on the “Orders” under “My Account” page.</div>',
            ],
            [
                'title'       => 'Are there any duties I have to pay? How much duty/taxes do I have to pay in custom?',
                'description' => '<div>Customers have to pay the duties and taxes. Once the package arrives in your country, the courier will contact you to collect any taxes or duties. We don’t have any exact figure of how much duty as it depends on the state/country government. usually it’s up to 5%.</div>',
            ],
            [
                'title'       => 'What do I do if my order doesn’t arrive in the prescribed time frame?',
                'description' => '<div>Contact customer service at +91 9016106290 Monday to Friday from 9:00 AM to 5:00 PM IST, and they would gladly help you track the package for you.</div>',
            ],
            [
                'title'       => 'What if my diamond/jewelry gets lost during shipment?',
                'description' => '<div>RRP takes every precaution to ensure that your order arrives safely. Your order is fully insured during transit, and we require a signature for all packages.</div>',
            ],
        ],
    ],
    [
        'id'    => 'returns-refund',
        'title' => 'Return/Refund Policy',
        'class' => 'accordion-refund',
        'items' => [
            [
                'title'       => 'Why is the return policy of RRP Diamond only for 7-days?',
                'description' => '<div><p>As we are both diamond manufacturers and sellers it should be noted that we in no way include any extra charges to our final prices, like ‘middle-men fee’. Something which is commonly billed when purchasing from a diamond retailer. With a minimal profit margin we are giving you the best quality diamonds as swiftly as we can, hence we keep a 7-day return policy to ensure maximum efficiency for all our customers.</p><p>Many times 3-4 customers end up liking the same diamond/diamond jewelry, if we sell it to someone and they want to return it within 7-days we are then able to sell that diamond to another interested buyer. This helps us maintain the market balance.</p></div>',
            ],
            [
                'title'       => 'What is RRP’s Cancellation Policy?',
                'description' => '<div>All Orders are eligible for cancellation when it is in packed/shipped status, as long as the cancel option is available.</div>',
            ],
            [
                'title'       => 'I just cancelled my order. When will I receive my refund?',
                'description' => '<div>You will receive refund into the source account within 1-5 days from the time of order cancellation.</div>',
            ],
            [
                'title'       => 'Can I modify the shipping address of my order after it has been placed?',
                'description' => '<div>Yes, please <strong><a href="/contact-us/">contact us</a></strong> to change your address.</div>',
            ],
            [
                'title'       => 'Can special/bespoke orders returned &amp; eligible for refund?',
                'description' => '<div>Special orders cannot be returned. A ring plus center diamond that we mount together – such as the Design your Own rings – is not considered a special order.</div>',
            ],
            [
                'title'       => 'When are returns not possible?',
                'description' => '<div><p>We cannot accept returns after return timeline has lapsed. Product must have in the same condition (unused)along with the certificate.(if apply)We cannot accept returns on CUSTOM ORDER.</p></div>',
            ],
            [
                'title'       => 'How can I initiate a return?',
                'description' => '<div>You will have to send us an email along with order number &amp; the reason for your return.</div>',
            ],
            [
                'title'       => 'What is the time-period within which I can return a product?',
                'description' => '<div>Products can be returned within 7 days of receiving the product. Post this, return is not allowed.</div>',
            ],
            [
                'title'       => 'Can I get a replacement for the product I’ve ordered?',
                'description' => '<div>Unfortunately, we can’t replace products that you’ve already ordered. Instead, you can request a return and place a separate order for the new products you want to order.</div>',
            ],
            [
                'title'       => 'Why has my return request been rejected??',
                'description' => '<div>It’s unfortunate, but the reason for making the return does not comply with our policy.</div>',
            ],
            [
                'title'       => 'What is the pick-up process for the return of a product?',
                'description' => '<div>Once you’ve made a return request with us via email, our team will get back you with the shipping address.</div>',
            ],
            [
                'title'       => 'What is the mode of payment for refunds?',
                'description' => '<div>All refunds are credited back to the account/card used to place the original order.</div>',
            ],
            [
                'title'       => 'How will I know that my refund has been initiated?',
                'description' => '<div>You’ll never have to ask. We’ll send you an email confirming the initiation of your refund. Your refund should reach you within 1-4 working days.</div>',
            ],
            [
                'title'       => 'I still haven’t got my refund. Why?',
                'description' => '<div>If we’ve sent you a confirmation on the approval of your refund, you will definitely get your refund. However, in rare cases, we face technical difficulties that can delay refund transfers. If the wait seems too long, <a href="/contact-us/"><strong>contact us</strong></a> for support.</div>',
            ],
        ],
    ],
    [
        'id'    => 'origin-supply-chain',
        'title' => 'Origin & Supply Chain',
        'class' => 'accordion-origin',
        'items' => [
            [
                'title'       => 'What is the Kimberelry Process?',
                'description' => '<div><p>The Kimberley Process (KP) is an international, multi-stakeholder initiative created to increase transparency and oversight in the diamond industry in order to eliminate trade in conflict diamonds, or rough diamonds sold by rebel groups or their allies to fund conflict against legitimate governments.</p></div>',
            ],
            [
                'title'       => 'Where do you source your rough diamonds from?',
                'description' => '<div>RRP sources its rough diamonds from De Beers and Alrosa.</div>',
            ],
            [
                'title'       => 'Where are the polished diamonds manufactured?',
                'description' => '<div>Our diamonds are polished in Surat, Gujarat, India. The city is also known as Diamond City of the world.</div>',
            ],
            [
                'title'       => 'How can you Guarantee 100% Natural Diamonds?',
                'description' => '<div><p>We were one of the first suppliers to fully address Diamond Integrity Assurance by introducing 100% screening of all melee diamonds leaving our factory. We’ve also assessed possible contamination points and installed systems and processes to guarantee 100% natural diamonds.</p><p>The screening process for stones between 1mm and 3.8mm takes place in-house and is executed using HRD-developed M-Screen 4.0 technology. All stones smaller or bigger are screened at independent laboratories such as IIDGR (Forevermark) or IGI (International Gemological Institute).</p></div>',
            ],
        ],
    ],
];
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
                        <?php foreach ($faq_sections as $section): ?>
                            <li><a class="dropdown-item" href="#<?= $section['id'] ?>"><?= $section['title'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <ul class="title_list d-none d-md-block">
                    <?php foreach ($faq_sections as $section): ?>
                        <li><a href="#<?= $section['id'] ?>"><?= $section['title'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="faq_list">
                    <div id="faq-accordion">
                        <?php foreach ($faq_sections as $s => $section): ?>
                            <h2 class="main_faq"><?= $section['title'] ?></h2>
                            <div class="accordion <?= $section['class'] ?>" id="<?= $section['id'] ?>">
                                <?php foreach ($section['items'] as $index => $faq): ?>
                                    <div class="accordion-item">
                                        <h3 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#faq-collapse-<?= $s ?>-<?= $index ?>" aria-expanded="false"
                                                aria-controls="faq-collapse-<?= $s ?>-<?= $index ?>">
                                                <?= $faq['title'] ?>
                                            </button>
                                        </h3>
                                        <div id="faq-collapse-<?= $s ?>-<?= $index ?>" class="accordion-collapse collapse"
                                            data-bs-parent="#faq-accordion">
                                            <div class="accordion-body">
                                                <?= $faq['description'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php help_box(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
