<?php
global $text_change,$oc_common;
$text_change['inventory_url'] = $oc_common['inventory_url'];
$home_page = array(
    'hero_banner_heading' => 'Beautiful, Certified Lab <br class="d-none d-md-block"> Grown Diamonds',
    'hero_banner_description' => 'Mining free. Direct to you. No middlemen',
    'hero_banner_button_text1' => 'Shop Lab Grown Diamonds',
    'hero_banner_button_link1' => $text_change['inventory_url'] ,
    'hero_banner_button_text2' => 'Shop Engagement Rings',
    'hero_banner_button_link2' => get_site_url().'/build-a-ring/',
    'hero_banner_image_desktop' => get_template_directory_uri().'/rrp-img/home/main-desktop.webp',
    'hero_banner_image_mobile' => get_template_directory_uri().'/rrp-img/home/main-mobile.webp',
);
$text_change['home_page'] = $home_page;
$common_page = array(
    "trustpilot_total_rating" => '1310'
);
$common_page['top_bar'] = [
    "INTERNATIONAL SHIPPING | We Price Match | 100% Money back Guarantee",
];

$common_page['top_bar_checkout'] = [
    "INTERNATIONAL SHIPPING | We Price Match | 100% Money back Guarantee",
];
$common_page['top_bar_mobile'] = [
    "INTERNATIONAL SHIPPING | We Price Match | 100% Money back Guarantee",
];
$common_page['top_bar_checkout_mobile'] = [
    "INTERNATIONAL SHIPPING | We Price Match | 100% Money back Guarantee",
];

$common_page['desktop_footer_1']= [
    ["name" => "Round Calibrated Melee Diamonds", "url" => "/product/calibrated-diamonds/"],
    ["name" => "Mix Lot Diamonds", "url" => "/product/melee-diamonds/"],
    ["name" => "Melee Diamonds Wholesale", "url" => "/melee-diamonds-for-sale/"],
    ["name" => "Fancy Shape Diamonds", "url" => "/fancy-shape-white-diamonds/"],
    ["name" => "Natural Diamond Engagement Rings", "url" => "/natural-diamond-ring/"]
];

$common_page['desktop_footer_2'] = [
    ["name" => "About Us", "url" =>  get_site_url() . "/about-us/"],
    ["name" => "Why Choose Us", "url" => get_site_url() . "/why-choose-us/"],
    ["name" => "Compare Us", "url" => get_site_url() . "/comparison/"],
    ["name" => "Blog", "url" => get_site_url() . "/blog/"],
    ["name" => "Wholesale Inquiries", "url" => "/wholesale-inquiries/"]
];

$common_page['desktop_footer_3'] = [
    ["name" => "Crypto Payment", "url" => get_site_url() . "/crypto-payment/"],
    ["name" => "Shipping Policy", "url" => get_site_url() . "/shipping/"],
    ["name" => "Return/Refund Policy", "url" => get_site_url() . "/return-policy/"],
    ["name" => "Price Match Policy", "url" => get_site_url() . "/diamond-price-match/"],
    ["name" => "Privacy Policy", "url" => get_site_url() . "/privacy-policy/"],
    ["name" => "Terms of Use", "url" => get_site_url() . "/terms-of-use/"]
];

$common_page['desktop_footer_4'] = [
    ["name" => "Contact Us", "url" => get_site_url() . "/contact-us/"],
    ["name" => "FAQ", "url" => get_site_url() . "/faq/"],
    ["name" => "Diamond Size Chart", "url" => get_site_url() . "/diamond-size-chart/"],
    ["name" => "Ring Sizer", "url" => get_site_url() . "/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf"],
    ["name" => "Feedback Form", "url" => get_site_url() . "/feedback-form/"]
];

$text_change['common_page']=$common_page;