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
              <h1 class="h2 text_black_secondary fw-600 mb_10 text-center">Shipping Policy</h1>
              <p class="ls_subtxt">Thank you for visiting and shopping with Loose Grown Diamond. The following terms and conditions constitute our Shipping Policy.</p>
              <div class="mb_50">
                 <p>All shipments are fully insured, require a signature(21+) upon delivery , and are shipped through one of our trusted delivery partners, including FedEx, UPS, Aramex, and USPS.</p>
                    <?php
                    
                    ?>
                 </ul>
              </div>
              <div class="mb_50">
                 <h2 class="  fw-600 mb_20 h4">Shipping Rates &amp; Delivery Estimates</h2>
                 <?php
                 
                 ?>
                 <ul class="ls_mrgb">
                    <li class="ls_subtxt firstItem">Loose Diamonds:
                       <ul>
                          <li class="ls_subtxt">Free Insured Shipping: Delivered within 2 weeks (Orders under $500 incur a $35 USD shipping fee.)</li>
                          <li class="ls_subtxt">Express Insured Shipping ($120): Delivered within 1 week</li>
                          <li class="ls_subtxt">🚚 Truck Icon:
                             <ul>
                                <li class="ls_subtxt">Diamonds with the truck icon are in our U.S. inventory and typically arrive within 3–5 business days, even with Free Shipping.</li>
                             </ul>
                          </li>
                          <li class="ls_subtxt">🚀 Rocket Icon:
                             <ul>
                                <li class="ls_subtxt">Diamonds with the rocket icon qualify for overnight delivery on business days only. (Excludes weekends and holidays.)</li>
                                <li class="ls_subtxt">Please note: If your order includes multiple diamonds and one or more aren't eligible for overnight delivery, the entire order will ship using our standard shipping timeline.</li>
                             </ul>
                          </li>
                       </ul>
                    </li>
                    <li class="ls_subtxt lastItem">Engagement Rings &amp; Fine Jewelry:
                       <ul>
                          <li class="ls_subtxt">Free Insured Shipping: Delivered within 3-4 weeks</li>
                          <li class="ls_subtxt">Express Insured Shipping ($120): Delivered within 2-3 weeks</li>
                       </ul>
                    </li>
                 </ul>
                 <?php
                 
                 ?>
              </div>
              <div class="mb_50">
                 <h2 class="h4 fw-600 mb_20">Shipment Confirmation &amp; Order Tracking</h2>
                 <span class="ls_subtxt">
                    Once your order has shipped, you will receive a Shipment Confirmation email containing your tracking number(s). Your tracking information will become active within 24–48 hours. You can also check your order status anytime by<!-- --> <a href="/my-account/orders"> click here.</a>
                 </span>
              </div>
              <div class="mb_50">
                 <h2 class="h4 fw-600 mb_20">Customs, Duties, and Taxes</h2>
                 <span class="ls_subtxt">If you're located in the United States, <strong>no customs duties or import taxes</strong> are payable.</span>
                 <p>For international orders, customs duties, taxes, and import fees are the responsibility of the recipient. Once your package arrives in your country, the shipping carrier will contact you to collect any applicable charges before delivery.</p>
                 <p>The amount varies by country, state, and local regulations, so we're unable to provide an exact estimate. We recommend checking with your local customs authority or government website for more information.</p>
                 <p><strong>Please note:</strong> Customers in Europe may also be required to pay customs broker/clearance fees. We cannot undervalue shipments or declare a lower invoice value on customs documents.</p>
              </div>
              <div class="mb_20">
                 <h2 class="h4 fw-600 mb_20">Lost or Stolen Shipments</h2>
                 <span class="ls_subtxt"><?php echo SITE_OC_NAME;?> takes every precaution to ensure your order arrives safely. All shipments are fully insured during transit and require a signature upon delivery.</span>
                 <span class="ls_subtxt">If your shipment is lost during transit, you will be eligible for a full refund or a replacement.</span>
              </div>
              <?php
                     
                     ?>
                     <div>
                        <h2 class="h4 fw-600  mb_20">Shipping Insurance & Claims</h2>
                        <ul class="ls_mrgb">
                           <li class="ls_subtxt firstItem">If your shipment is marked as "Delivered" but you have not received it, please contact us within 24 hours of the delivery notification. Claims submitted after this period may not be eligible for insurance coverage, and we may be unable to assist further.</li>
                           <li class="ls_subtxt">Once your order has been dispatched, any changes to the delivery address including redirecting the shipment to a FedEx Hold for Pickup location will void the shipping insurance and claim coverage. We strongly recommend that you do not modify the delivery address after dispatch.</li>
                           <li class="ls_subtxt">If you choose to collect your package from a FedEx Hold for Pickup location, your shipment will remain insured for 24 hours from the time it arrives at the pickup location. Claims submitted after this period will not be eligible for coverage.</li>
                           <li class="ls_subtxt">If your package arrives tampered with or visibly damaged, please do not accept the delivery. Notify the courier immediately, as accepting a damaged package may void your eligibility for an insurance claim.</li>
                           <li class="ls_subtxt">If a shipping claim is required, the standard resolution time is up to 4 weeks. During this period, no refund or replacement can be issued while the claim is under review. If the claim is approved, you may choose either a replacement shipment or a full refund.</li>
                           <li class="ls_subtxt lastItem">Loose Grown Diamond is not responsible for delays caused by customs clearance. Shipments delayed by customs for more than 48 hours may be returned at the customer's expense and may no longer be eligible for shipping insurance or claim coverage.</li>
                        </ul>
                     </div>
                     <?php
                  
                     ?>
           </div>
        </div>
            <div class="container second_section">
                <div class="row">
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Shipping FAQ's</h2>
                    <div class="faq_list">
                        <?php
                        global $shippingFaqData;
                        ?>

                        <div class="accordion accordion-order" id="accordionorderExample">
                            <?php foreach ($shippingFaqData as $index => $faq): ?>
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