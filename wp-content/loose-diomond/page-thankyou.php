<?php

/**
 * Template Name: Thank you
 */

get_header();
?>
<div class="banner_section">
    <div class="py_60">
        <div class="container-fluid">

            <div class="text-center  oc-thank-text" style="color: #000000;">
                <h1 class=" fw-semibold mb-4">THANK YOU – REQUEST RECEIVED</h1>
                <h4>One of our jewelry specialists will contact you within 12 hours to discuss your design and next steps.</h4>
                <h4>Trusted by 27,000+ happy couples I Crafted with precision I Guided by expertise</h4>

                <p class="et_eb_sec1_subtitle mt-2 to mt-4">
                    <strong>Need a Faster Response?</strong>
                </p>
            </div>

            <!-- Buttons Row -->
            <div class="text-center mt-3">
                <div class="d-flex justify-content-center gap-2 flex-wrap oc-thank-btn-group">
                    <a href="https://wa.me/<?php echo WA_SUPPORT_MOBILE;?>" target="_blank" class="ls-button ls_bbtn  text-decoration-none">
                        Chat On Whatsapp
                    </a>
                    

                    <a href="tel:+<?php echo SUPPORT_MOBILE?>" class="ls-button ls_bbtn  text-decoration-none">
                        Speak With a Jewelry Expert Now
                    </a>

                    <a href="mailto:<?php echo SUPPORT_EMAIL;?>" class="ls-button ls_bbtn  text-decoration-none">
                        Email Support
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>
<?php
get_footer();
