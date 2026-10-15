<?php

/**
 * Template Name: Shipping Policy
 */

get_header();
?>

<!-- Shipping section start -->
<div class="shipping_section mx-auto">
    <div class="banner_section">
        <div class="py_60 banner_inner">
           <div class="container mb_50 first_section">
           <div class="row">
              <h1 class="h2 text_black_secondary fw-600 mb_10 text-center">Shipping</h1>
              <p class="ls_subtxt">Thank you for visiting and shopping at RRP Diamond. Following are the terms and conditions that constitute our Shipping Policy.</p>

              <div class="mb_50">
                 <h2 class="fw-600 mb_20 h4">Shipment Processing Time</h2>
                 <ul class="ls_mrgb">
                    <li class="ls_subtxt firstItem">All orders are processed within 2-3 business days.</li>
                    <li class="ls_subtxt">Orders are not shipped or delivered on weekends nor on holidays.</li>
                    <li class="ls_subtxt lastItem">If we are experiencing a high volume of orders, shipments may be delayed by a few days. Please allow additional days in transit for delivery. If we expect a significant delay in the shipment of your order, we will contact you via email or telephone to inform you.</li>
                 </ul>
              </div>

              <div class="mb_50">
                 <h2 class="fw-600 mb_20 h4">Shipping Rates &amp; Delivery Estimates</h2>
                 <p class="ls_subtxt">Please note that all of our shipments are <strong>insured and will be delivered by one of our delivery partners, FedEx, UPS, Aramex or DHL.</strong></p>
                 <div class="table-responsive mb_20">
                    <table class="table table-bordered">
                       <tbody>
                          <tr>
                             <td><p class="ls_subtxt mb-0"><strong>Shipment method</strong></p></td>
                             <td><p class="ls_subtxt mb-0"><strong>Estimated delivery time</strong></p></td>
                             <td><p class="ls_subtxt mb-0"><strong>Shipment cost</strong></p></td>
                          </tr>
                          <tr>
                             <td><p class="ls_subtxt mb-0">Free Shipping</p></td>
                             <td><p class="ls_subtxt mb-0">9-11 business days</p></td>
                             <td><p class="ls_subtxt mb-0">Free</p></td>
                          </tr>
                          <tr>
                             <td><p class="ls_subtxt mb-0">Express Shipping</p></td>
                             <td><p class="ls_subtxt mb-0">5-7 business days</p></td>
                             <td><p class="ls_subtxt mb-0">$125</p></td>
                          </tr>
                       </tbody>
                    </table>
                 </div>
                 <ul class="ls_mrgb">
                    <li class="ls_subtxt firstItem">Shipping charges for your order will be calculated and displayed at checkout.</li>
                    <li class="ls_subtxt">Orders up to $500 will incur a shipping fee of $35 USD.</li>
                    <li class="ls_subtxt">Orders up to $1500 USD are shipped without insurance and signature required.</li>
                    <li class="ls_subtxt lastItem">As our diamonds are shipped out from various locations delivery times may vary. Please use the diamond’s SKU to find out from us where a shipment would originate from.</li>
                 </ul>
              </div>

              <div class="mb_50">
                 <h2 class="h4 fw-600 mb_20">Shipment Confirmation &amp; Order Tracking</h2>
                 <span class="ls_subtxt">
                    You will receive a Shipment Confirmation email once your order has shipped containing your tracking number(s). The tracking number will be active within 24-48 hours. If you want to check on your order status simply <a href="/my-account/">click here</a>.
                 </span>
              </div>

              <div class="mb_50">
                 <h2 class="h4 fw-600 mb_20">Customs, Duties and Taxes</h2>
                 <span class="ls_subtxt">RRP Diamond is not responsible for any customs and taxes applied to your order. All fees imposed during or after shipping are the responsibility of the customer (tariffs, taxes, etc.).</span>
              </div>

              <div class="mb_20">
                 <h2 class="h4 fw-600 mb_20">Lost/Stolen</h2>
                 <span class="ls_subtxt">RRP Diamond provide insured shipment. We are liable for any products stolen or lost during shipping. If you haven’t received your order, please contact the shipment carrier to file a claim.</span>
              </div>
           </div>
        </div>
            <div class="container second_section">
                <div class="row">
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Shipping FAQ’s</h2>
                    <div class="faq_list">
                        <?php
$shipping_faqs = [
    [
        'title'       => 'Do you Ship WORLDWIDE?',
        'description' => 'Yes, we do ship <strong>WORLDWIDE.</strong>',
    ],
    [
        'title'       => 'When will my order ship?',
        'description' => '<p>Free Shipping: Orders typically <strong>ship within 3 to 5 days of completed payment. </strong>Express Shipping($125): Orders typically <strong>ship within 2 to 3 days of completed payment.</strong></p><p><strong><i>Transit time is not included in the lead times stated above</i></strong> Transit times tend to vary somewhat. Current transit time estimates are between 7 and 11 working days.</p><p>If you need an order to arrive by a specific date make sure to mention this in the space provided at check out. We’ll do our best to exceed your request or attempt to reach you and advise the best we can do.</p><p>Note: We do not ship on Weekends &amp; Holidays.</p>',
    ],
    [
        'title'       => 'Where is my diamond shipped from?',
        'description' => 'The diamonds are shipped from the India.',
    ],
    [
        'title'       => 'What’s included in my order?',
        'description' => 'In the box, you will receive Diamond/Ring, Hard Copy of IGI/GIA certificate (if applicable)',
    ],
    [
        'title'       => 'Does this shipment come with insurance?',
        'description' => 'Yes, all the orders above $1500 cart total will ship with full insurance via FedEx/UPS. Orders below $1500 cart total will ship with non-insurance and signature mandatory via FedEx/UPS.',
    ],
    [
        'title'       => 'How much time will it take to deliver it to my home?',
        'description' => 'Once dispatched your order will take an estimated 5 to 11 working days to arrive at your doorstep.',
    ],
    [
        'title'       => 'Can I track my order online?',
        'description' => 'Once dispatched you should be able to track your order on the shipping company’s website using the tracking number we have given you via email.',
    ],
    [
        'title'       => 'Are there any duties I have to pay? What are the customs duty/taxes once my order arrives?',
        'description' => 'Customers have to pay the duties and taxes. Once the package arrives in your country, the courier will contact you to collect any taxes or duties. The exact figure varies country by country.',
    ],
    [
        'title'       => 'What do I do if my order doesn’t arrive in the prescribed time frame?',
        'description' => 'Contact our customer service on +91 9016106290 Monday to Friday from 9:00 AM to 5:00 PM IST Time, and we will gladly help you track your package.',
    ],
    [
        'title'       => 'What if my diamond/jewelry gets lost during shipment?',
        'description' => 'RRP takes every precaution to ensure that your order arrives safely. Your order is fully insured during transit, and we require a signature for all packages.',
    ],
];
?>

                        <div class="accordion accordion-order" id="accordionorderExample">
                            <?php foreach ($shipping_faqs as $index => $faq): ?>
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
<!-- Shipping section end -->

<?php
get_footer();