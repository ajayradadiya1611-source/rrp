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
    "trustpilot_total_rating" => '13,100'
);
$common_page['top_bar'] = [
    "International Shipping & Free Returns (USA) | We Price Match | 100% Money-Back Guarantee",
    "Excellent ⭐ 5,000+ Verified 5-Star Reviews You Can Trust",
    "Need Help? Speak with a GIA-Certified Gemologist | <a href='tel:+91 9016106290'>+91 9016106290</a>",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | Use Code: hallo30 🎃",
];

$common_page['top_bar_checkout'] = [
    "50,000+ Happy Couples",
    "Free Resizing",
    "Lifetime Warranty",
    "7-Day Money-Back Guarantee",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | Use Code: HALLO30 🎃",
];
$common_page['top_bar_mobile'] = [
    "International Shipping & Free Returns (USA)",
    "Price Match & 100% Money-Back Guarantee",
    "Excellent ⭐ 5,000+ Verified 5-Star Reviews",
    "Speak with a GIA Gemologist <a href='tel:+91 9016106290'>+91 9016106290</a>",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | HALLO30 🎃",
];
$common_page['top_bar_checkout_mobile'] = [
    "50,000+ Happy Couples",
    "Free Resizing",
    "Lifetime Warranty",
    "7-Day Money-Back Guarantee",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | HALLO30 🎃",
];

$common_page['desktop_footer_1']= [
    ["name" => "Lab Diamonds", "url" => "/shop/"],
    ["name" => "Engagement Rings", "url" => "/build-a-ring/"],
    ["name" => "Custom Engagement Ring", "url" => "/custom-engagement-rings/"],
    ["name" => "Wedding Bands", "url" => "/lab-diamond-wedding-bands"],
    ["name" => "Earrings", "url" => "/lab-diamond-earrings"],
    ["name" => "Tennis Bracelets", "url" => "/lab-diamond-bracelets"],
    ["name" => "Tennis Necklaces", "url" => "/lab-diamond-necklaces"],
    ["name" => "Pendants", "url" => "/lab-diamond-pendants"],
];

$common_page['desktop_footer_2'] = [
    ["name" => "About Us", "url" =>  get_site_url() . "/about-us/"],
    ["name" => "Why Choose Us", "url" => get_site_url() . "/why-choose-us/"],
    ["name" => "Compare Us", "url" => get_site_url() . "/comparison/"],
    ["name" => "Blog", "url" => get_site_url() . "/blog/"],
    ["name" => "Reddit Reviews", "url" => get_site_url() . "/loose-grown-diamond-on-reddit/"],
    ["name" => "Reviews", "url" => get_site_url() . "/reviews/"],
    ["name" => "Wholesale Inquiries", "url" => "https://wholesale.loosegrowndiamond.com/"]
];

$common_page['desktop_footer_3'] = [
    ["name" => "Diamond Financing", "url" => get_site_url() ."/diamond-financing/"],
    ["name" => "Engagement Ring Financing", "url" => get_site_url() ."/engagement-ring-financing/"],
    ["name" => "Crypto Payment", "url" => get_site_url() . "/crypto-payment/"],
    ["name" => "Shipping Policy", "url" => get_site_url() . "/shipping/"],
    ["name" => "Return/Refund Policy", "url" => get_site_url() . "/return-policy/"],
    ["name" => "Price Match Policy", "url" => get_site_url() . "/diamond-price-match/"],
    ["name" => "Jewelry Insurance", "url" => get_site_url() . "/jewelry-insurance/"],
    ["name" => "Promo Code", "url" => get_site_url() . "/promo-code/"],
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