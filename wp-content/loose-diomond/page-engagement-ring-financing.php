<?php
/**
 * Template Name: Engagement Ring Financing
 */

get_header();
?>
 <div class="engagement-ring-banner">
      <div class="container">
        <h1 class="banner-title">
          Engagement Ring Financing & Easy Monthly Payments
        </h1>
        <p class="text-center">
          No Hidden Fees • Buy Now, Pay Later • Flexible Payment Plans • Instant Approval
        </p>
        <div class="row align-items-center justify-content-center">
          <div class="col-sm-6 d-flex justify-content-end align-items-center">
            <a href="/build-a-ring/" class="common_btn ring-button  oc-btn-bg-black">
              Shop Engagement Rings with Financing
            </a>
          </div>
          <div class="col-sm-6">
            <a href="/lab-diamond-wedding-bands/" class="common_btn ring-button  oc-btn-bg-black">
              Shop Wedding Bands with Financing
            </a>
          </div>
        </div>

        <div class="arrow-icon">
          <a href="#choose-payment">
            <span
              class="vc_icon_element-icon fa fa-solid fa-arrow-down-long"
              style="color: #ffffff6e !important"
            >
            </span>
          </a>
        </div>
      </div>
    </div>

    <div class="our-policies">
      <div class="container">
        <div class="row">
          <div class="col-6 col-md-3">
            <div class="policies-card">
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/shopping.svg" alt="shopping" />
              <p class="policy-name">Secure Checkout</p>
              <p class="policy-details mb-0">
                Shop with confidence using our secure checkout, protected payments, and trusted financing options.
              </p>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="policies-card">
              <img
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/shipping-protection.svg"
                alt="shopping"
              />
              <p class="policy-name">Fast Insured Shipping</p>
              <p class="policy-details mb-0">
                Enjoy fast, fully insured shipping with safe and secure delivery.
              </p>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="policies-card">
              <img
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/delivery-status.svg"
                alt="shopping"
              />
              <p class="policy-name"><?php echo RETURN_DAYS;?>-Day Returns</p>
              <p class="policy-details mb-0">
                Shop with confidence thanks to our hassle-free <?php echo RETURN_DAYS;?>-day return policy.
              </p>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="policies-card">
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/agent.svg" alt="shopping" />
              <p class="policy-name">Talk To Gemologist</p>
              <p class="policy-details mb-0">
                Need help financing your engagement ring? Speak with our gemologists for expert guidance on diamonds, budgets, and financing options.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="choose-payment" id="choose-payment">
      <div class="container">
        <h2 class="engagement-section-title  text-center  mb-5">
          Choose the Payment Option That Fits Your Budget
        </h2>
        <div class="row">
          <div class="col-lg-4 col-sm-6">
            <div class="payment-card">
              <img
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/payment-1.webp"
                alt="payment"
                class="img-fluid"
              />
              <div class="payment-info">
                <h3 class="payment-name">Affirm</h3>
                <ul>
                  <li>No Prepayment Penalties</li>
                  <li>No Hidden Fees</li>
                  <li>Secure & Trusted</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-sm-6">
            <div class="payment-card">
              <img
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/payment-2.webp"
                alt="payment"
                class="img-fluid"
              />
              <div class="payment-info">
                <h3 class="payment-name">Afterpay</h3>
                <ul>
                  <li>Pay in 4 or Monthly Installments</li>
                  <li>Instant Approval</li>
                  <li>No Hard Credit Check (Only for eligible plans.)</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-sm-6">
            <div class="payment-card">
              <img
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/image/reddit/payment-3.webp"
                alt="payment"
                class="img-fluid"
              />
              <div class="payment-info">
                <h3 class="payment-name">Split Payments</h3>
                <ul>
                  <li>Pay Just One EMI Now, 50% Later.</li>
                  <li>Reserve Your Ring Today</li>
                  <li>Pay the Balance Later</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="button-parts">
          <a href="mailto:<?php echo SUPPORT_EMAIL;?>" class="common_btn ring-button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M20 4H4C3 4 2 5 2 6v12c0 1 1 2 2 2h16c1 0 2-1 2-2V6c0-1-1-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"
              ></path>
            </svg>
            Email Us
          </a>
          <a href="tel:+<?php echo SUPPORT_MOBILE?>" class="common_btn ring-button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M6.6 10.8c1.5 3 3.6 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.4.6 3.7.6.6 0 1 .4 1 1v3.5c0 .6-.4 1-1 1C10.1 22 2 13.9 2 3.5c0-.6.4-1 1-1H6.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.7.1.4 0 .8-.3 1.1l-2.2 2.5z"
              ></path>
            </svg>
            Call Now
          </a>
        </div>
      </div>
    </div>

    <div class="payment-plans">
      <div class="container">
        <div class="mb-30">
          <h2>
            Engagement Ring Financing Options: Payment Plans, Loans & Buy Now, Pay Later
          </h2>
          <p>
            Purchasing an engagement ring requires two important decisions—one driven by emotion and the other by financial considerations. The ring symbolizes your commitment, love, and shared future, but it also represents a significant financial investment.
          </p>
          <p>
            For many customers, balancing their emotions with their budget can be challenging. Smart financing provides a practical solution.
          </p>
          <p>
            Financing allows you to choose your ideal ring while paying for your purchase in a way that fits your financial plans. Today, there are a variety of flexible engagement ring financing options available.
          </p>
          <p class="mb-0">
            Buying a diamond engagement ring is both an emotional and financial decision. The various financing options make the purchase easier and allow you to get the ring you desire without spending a fortune. Below are some of the flexible financing options that can help you manage your budget and make your special moment even more memorable.
          </p>
        </div>
        <div class="mb-30">
          <h3>Affirm</h3>
          <p class="mb-0">
            Affirm is a Buy Now, Pay Later (BNPL) service that allows customers to split their purchase into fixed monthly installments with transparent terms and no hidden fees. Customers can choose flexible payment plans at checkout and receive an instant approval decision.
          </p>
        </div>
        <div class="mb-30">
          <h3>After Pay</h3>
          <p class="mb-0">
           Afterpay is a Buy Now, Pay Later (BNPL) payment method that allows customers to pay through interest-free installments. It helps reduce the financial burden of making a purchase. The maximum eligible purchase amount for this payment method is $2,000.
          </p>
        </div>

        <div class="mb-30">
          <h3>Splitit</h3>
          <p class="mb-0">
            Splitit allows you to pay through fixed monthly installments over a period of 6 to 36 months. This option provides greater payment flexibility by allowing buyers to follow a fixed repayment schedule. At Loose Grown Diamond, you can choose Splitit – Monthly Credit Card Payments – No Fees at checkout.
          </p>
        </div>
        <div class="mb-30">
          <h3>Buy Now Pay Later (BNPL)</h3>
          <p class="mb-0">
            BNPL allows you to take your engagement ring home while paying for it through smaller monthly installments. It offers a quick and convenient payment solution, but customers should be aware of payment due dates and any applicable late fees.
          </p>
        </div>
        <div class="mb-30">
          <h3>Crypto</h3>
          <p class="mb-0">
            Loose Grown Diamond accepts cryptocurrency payments through supported digital currencies. It is a modern, fast, secure, and convenient payment method.
          </p>
        </div>

        <div class="mb-30">
          <h3>Direct Bank Transfer</h3>
          <p class="mb-0">
            Direct bank transfer is also accepted for customers who prefer this payment method. It is a secure and widely used option for making payments.
          </p>
        </div>
        <div class="mb-30">
          <h3>Klarna</h3>
          <p class="mb-0">
            Klarna makes it easy to buy your dream engagement ring today and pay over time. With Klarna's flexible payment options, you can choose a plan that fits your budget while
          </p>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Jewelry Type</th>
                <th>Financing Available</th>
                <th>EMI Duration</th>
                <th>Interest-Free Option</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Diamond Wedding Ring</td>
                <td>Yes</td>
                <td>3–12 Months</td>
                <td>Often Available</td>
              </tr>
              <tr>
                <td>Earrings</td>
                <td>Yes</td>
                <td>3–9 Months</td>
                <td>Sometimes</td>
              </tr>
              <tr>
                <td>Bracelets</td>
                <td>Yes</td>
                <td>3–12 Months</td>
                <td>Festive Offers</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- start customers say section -->
    <div class="section customer-section bg_light_gray">
      <?php
        echo single_product_what_customer_say();
        ?>
    </div>

    <div class="engagement-fancy-faq  mt-5">
      <div class="container second_section">
        <div class="row">
          <h2 class="text_black_secondary fw-600 mb_25 text-center">
            Engagement Ring Financing FAQ's
          </h2>
          <div class="faq_list">
            <div class="accordion accordion-order" id="accordionEngagementRingFinancing">
                <?php foreach ( $engagementRingFinancingFaqData as $index => $faq ) :
                    $collapse_id = 'ering-finance-collapse' . $index;
                ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#<?php echo esc_attr( $collapse_id ); ?>"
                                aria-expanded="false"
                                aria-controls="<?php echo esc_attr( $collapse_id ); ?>"
                            >
                                <?php echo esc_html( $faq['title'] ); ?>
                            </button>
                        </h3>
                        <div
                            id="<?php echo esc_attr( $collapse_id ); ?>"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionEngagementRingFinancing"
                        >
                            <div class="accordion-body">
                                <?php echo wp_kses_post( $faq['description'] ); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
          </div>


        </div>
      </div>
    </div>
    <div class="container mb-5">
       <?php 
    help_box();
    ?>
    </div>

<?php get_footer(); ?>