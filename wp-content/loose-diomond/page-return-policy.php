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
                       <h1 class="h2 text_black_secondary fw-600 mb_10 text-center  oc-return-policy-title">Return Policy</h1>
                       <p class="ls_subtxt fw-400 mb-2 mb-md-3">Thanks for shopping at Loose Grown Diamond.</p>
                       <p class="ls_subtxt fw-400 mb-2 mb-md-3">If you are not completely satisfied with your purchase, we're here to help.</p>
                       <p class="ls_subtxt fw-400 mb-2 mb-md-3">To initiate a return, please email <?php echo SUPPORT_EMAIL;?> with your order number and the reason for your return.</p>
                        <?php
                     if(WEBSITE_TYPE=='RETAIL'){
                     ?>
                       <p><span>Please note: A maximum of two returns or exchanges per customer, per calendar year is permitted.</span></p>
                       <?php
                   }else{
                    ?>
                     <p><span>Note: Each customer is allowed up to 3 diamond returns or exchanges per month. For any returns beyond that, a 10% restocking fee will apply.</span></p>
                    <?php
                   }
                       ?>

                       <div class="mb_50">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Return</h2>
                          <?php
                     if(WEBSITE_TYPE=='RETAIL'){
                     ?>
                     <ul>
                        <p class="ls_subtxt fw-400 mb-2 mb-md-3">You may return your item within 30 calendar days from the date of delivery.</p>
                        <p class="ls_subtxt fw-400 mb-2 mb-md-3">To be eligible for a return:</p>
                             <li class="fw-400 ls_subtxt">The item must be unused and in the same condition in which it was received.</strong></li>
                             <li class="fw-400 ls_subtxt">All original documentation, including the IGI, GIA, or GCAL grading report (where applicable), must be returned with the item.</li>
                             <li class="fw-400 ls_subtxt"> The item must be returned in its original packaging.</li>
                          </ul>
                     <?php
                 }else{
                    ?>
                    <ul>
                             <li class="fw-400 ls_subtxt">You have 7 calendar days to return an item from the date you received it.</li>
                             <li class="fw-400 ls_subtxt">To be eligible for a return, your item must be unused and in the same condition that you received it. All returned items must be returned with all documentation (including Lab grown diamonds certificate).</li>
                             <li class="fw-400 ls_subtxt"> Your item must be in the original packaging. </li>
                             <li class="fw-400 ls_subtxt">  The return is chargeable and costs $50 for each shipment. </li>
                          </ul>
                    <?php
                 }
                 ?>
                          
                       </div>
                       <div class="mb_50">
                        
                            <?php
                     if(WEBSITE_TYPE=='RETAIL'){
                     ?>
                    <h3 class="h5ls_subtxt fw-400 mb-2 mb-md-3 "> U.S. Returns:</h3>
                    <p class="ls_subtxt fw-400 mb-2 mb-md-3">
                    Returns within the United States are free and include a prepaid, fully insured FedEx return shipping label. Simply print the return label, securely repack the item in its original packaging, include all original documentation (including the diamond grading certificate), and either schedule a FedEx pickup or drop it off at your nearest FedEx location.<br>
                    Please note: A $150 fee will be charged if the original diamond grading certificate is not returned.</p>
                     <h3 class="h5ls_subtxt fw-400 mb-2 mb-md-3 "> International Returns:</h3>
                    <p class="ls_subtxt fw-400 mb-2 mb-md-3">
                    For international orders, we reimburse up to $50 USD toward the return shipping cost. We will provide you with the return shipping address. Once you have shipped the package, simply email us a copy of your shipping receipt, and we will reimburse you up to $50 USD.</p>
                     <?php
                 }else{
                    ?>
                      <h4 class=" fw-600 mb-2 mb-md-3">Shipping</h4>
                   <ul>
                             <li class="fw-400 ls_subtxt">The return is chargeable and costs $50 for each shipment and include a pre-paid, insured FedEx shipping label. Print the return label, repack items in original packaging, add diamond certifications, and arrange a FedEx pickup or drop-off at the nearest FedEx location. Note: A $150 fee applies for not returning the diamond certificate.</li>
                             <li class="fw-400 ls_subtxt">We’ll provide the shipping address; you just need to ship the items there. We have shipping agents in the USA, Canada, Germany, Australia, Dubai, and Hong Kong. You will receive the specific local address from our company over the email.</li>
                          </ul>
                    <?php
                 }
                 ?>
                          
                       </div>
                       <div class="mb_50">
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Refunds</h2>
                          <?php
                     if(WEBSITE_TYPE=='RETAIL'){
                     ?>
                          <p class="ls_subtxt fw-400 mb-2 mb-md-3">Once we receive your returned item, we will inspect it within 24–48 business hours and notify you once the inspection is complete.</p>
                          <p class="ls_subtxt fw-400 mb-2 mb-md-3">After the inspection is complete, we will issue a refund to your original payment method. Depending on your card issuer's policies, the refund will be credited to your account within 2–10 business days.</p>
                          <?php
                 }else{
                    ?>
                          <ul>
                             <li class="fw-400 ls_subtxt">Once we receive your item, we will inspect it and notify you that we have received your returned item.</li>
                             <li class="fw-400 ls_subtxt">We will immediately notify you of the status of your refund after inspecting the item.</li>
                             <li class="fw-400 ls_subtxt">If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within 5-10 working days, depending on your card issuer’s policies.</li>
                          </ul>
                          <?php
                 }
                 ?>
                       </div>
                       <div>
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Contact Us</h2>
                          <?php
                     if(WEBSITE_TYPE=='RETAIL'){
                     ?>
                          <p class="ls_subtxt fw-400 mb-2 mb-md-3">If you have any questions about returning your item, please contact us at <a class="linkHover" href="mailto:support@loosegrowndiamond.com">support@loosegrowndiamond.com</a>.</p>
                       </div>
                       <div>
                          <h2 class="h4 fw-600 mb-2 mb-md-3">Klarna Orders</h2>
                          <p class="ls_subtxt fw-400 mb-2 mb-md-3"><strong>Please note:</strong> For orders purchased using Klarna, please include a return address when returning your item. Eligible items may be returned within 30 days of delivery for a full refund, provided they are unused, unopened, and in their original condition (where applicable). Restocking fees and store credit/exchange options do not apply to Klarna purchases. Approved refunds will be processed within 30 business days, subject to Klarna's Merchant Protection Program.</p>
                       </div>
                          <?php
                 }else{
                    ?>
                          <ul class="mb-0">
                             <li class="ls_subtxt">If you have any questions on how to return your item to us, <a class="linkHover" href="/contact-us">contact us.</a></li>
                          </ul>
                       </div>
                          <?php
                 }
                 ?>
                    </div>
            </div>
            <div class="container second_section">
                <div class="row">
                    <h2 class="text_black_secondary fw-600 mb_25 text-center">Return/Refund FAQ's</h2>
                    <div class="faq_list">
                       <?php
                        global $returnfaqData;
                        ?>
                        <div class="accordion accordion-order" id="accordionorderExample">
                            <?php foreach ($returnfaqData as $index => $faq): ?>
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