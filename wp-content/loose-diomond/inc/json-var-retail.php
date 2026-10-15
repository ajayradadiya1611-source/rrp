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
    "Need Help? Speak with a GIA-Certified Gemologist | <a href='tel:+1 646-288-0810'>+1 646-288-0810</a>",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | Use Code: hallo30 🎃",
];

$common_page['top_bar_checkout'] = [
    "50,000+ Happy Couples",
    "Free Resizing",
    "Lifetime Warranty",
    "30-Day Money-Back Guarantee",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | Use Code: HALLO30 🎃",
];
$common_page['top_bar_mobile'] = [
    "International Shipping & Free Returns (USA)",
    "Price Match & 100% Money-Back Guarantee",
    "Excellent ⭐ 5,000+ Verified 5-Star Reviews",
    "Speak with a GIA Gemologist <a href='tel:+1 646-288-0810'>+1 646-288-0810</a>",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | HALLO30 🎃",
];
$common_page['top_bar_checkout_mobile'] = [
    "50,000+ Happy Couples",
    "Free Resizing",
    "Lifetime Warranty",
    "30-Day Money-Back Guarantee",
    "🎃 HALLOWEEN Sale: Flat 30% Off Diamonds | HALLO30 🎃",
];

$common_page['desktop_footer_1']= [
    ["name" => "Lab Diamonds", "url" => "/lab-diamonds/"],
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

$left_color_diamonds = [
    [
        'name'  => 'Yellow',
        'image' => get_template_directory_uri() . '/assets/image/header/yellow-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=yellow',
    ],
    [
        'name'  => 'Pink',
        'image' => get_template_directory_uri() . '/assets/image/header/pink-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=pink',
    ],
    [
        'name'  => 'Purple',
        'image' => get_template_directory_uri() . '/assets/image/header/purple-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=purple',
    ],
    [
        'name'  => 'Red',
        'image' => get_template_directory_uri() . '/assets/image/header/red-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=red',
    ],
    [
        'name'  => 'Blue',
        'image' => get_template_directory_uri() . '/assets/image/header/blue-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=blue',
    ],
    
];

$right_color_diamonds = [
    [
        'name'  => 'Green',
        'image' => get_template_directory_uri() . '/assets/image/header/green-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=green',
    ],
    [
        'name'  => 'Orange',
        'image' => get_template_directory_uri() . '/assets/image/header/orange-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=orange',
    ],
    [
        'name'  => 'Brown',
        'image' => get_template_directory_uri() . '/assets/image/header/brown-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=brown',
    ],
    [
        'name'  => 'Black',
        'image' => get_template_directory_uri() . '/assets/image/header/black-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=black',
    ],
    [
        'name'  => 'Grey',
        'image' => get_template_directory_uri() . '/assets/image/header/gray-diamond.svg',
        'url'   => get_site_url() . '/lab-created-colored-diamonds/?color=grey',
    ],
];
$common_page['left_color_diamonds']=$left_color_diamonds;
$common_page['right_color_diamonds']=$right_color_diamonds;

$left_shapes = [
    [
        'name'  => 'Round',
        'image' => get_template_directory_uri() . '/assets/image/header/round.svg',
        'url'   => get_site_url() . '/collections/round-shape-lab-grown-diamonds/',
    ],
    [
        'name'  => 'Emerald',
        'image' => get_template_directory_uri() . '/assets/image/header/emerald.svg',
        'url'   => get_site_url() . '/collections/emerald-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Marquise',
        'image' => get_template_directory_uri() . '/assets/image/header/marquise.svg',
        'url'   => get_site_url() . '/collections/marquise-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Oval',
        'image' => get_template_directory_uri() . '/assets/image/header/oval.svg',
        'url'   => get_site_url() . '/collections/oval-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Radiant',
        'image' => get_template_directory_uri() . '/assets/image/header/radiant.svg',
        'url'   => get_site_url() . '/collections/radiant-shape-lab-grown-diamonds/'
    ],
];

$right_shapes = [
    [
        'name'  => 'Princess',
        'image' => get_template_directory_uri() . '/assets/image/header/princess.svg',
        'url'   => get_site_url() . '/collections/princess-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Asscher',
        'image' => get_template_directory_uri() . '/assets/image/header/asscher.svg',
        'url'   => get_site_url() . '/collections/asscher-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Cushion',
        'image' => get_template_directory_uri() . '/assets/image/header/cushion.svg',
        'url'   => get_site_url() . '/collections/cushion-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Pear',
        'image' => get_template_directory_uri() . '/assets/image/header/pear.svg',
        'url'   => get_site_url() . '/collections/pear-shape-lab-grown-diamonds/'
    ],
    [
        'name'  => 'Heart',
        'image' => get_template_directory_uri() . '/assets/image/header/heart.svg',
        'url'   => get_site_url() . '/collections/heart-shape-lab-grown-diamonds/'
    ],
];

$common_page['left_shapes']  = $left_shapes;
$common_page['right_shapes'] = $right_shapes;


$left_ring_shapes = [
    [
        'name'  => 'Round',
        'image' => get_template_directory_uri() . '/assets/image/header/round.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/round',
    ],
    [
        'name'  => 'Emerald',
        'image' => get_template_directory_uri() . '/assets/image/header/emerald.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/emerald',
    ],
    [
        'name'  => 'Marquise',
        'image' => get_template_directory_uri() . '/assets/image/header/marquise.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/marquise',
    ],
    [
        'name'  => 'Oval',
        'image' => get_template_directory_uri() . '/assets/image/header/oval.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/oval',
    ],
    [
        'name'  => 'Radiant',
        'image' => get_template_directory_uri() . '/assets/image/header/radiant.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/radiant',
    ],
];

$right_ring_shapes = [
    [
        'name'  => 'Princess',
        'image' => get_template_directory_uri() . '/assets/image/header/princess.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/princess',
    ],
    [
        'name'  => 'Asscher',
        'image' => get_template_directory_uri() . '/assets/image/header/asscher.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/asscher',
    ],
    [
        'name'  => 'Cushion',
        'image' => get_template_directory_uri() . '/assets/image/header/cushion.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/cushion',
    ],
    [
        'name'  => 'Pear',
        'image' => get_template_directory_uri() . '/assets/image/header/pear.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/pear',
    ],
    [
        'name'  => 'Heart',
        'image' => get_template_directory_uri() . '/assets/image/header/heart.svg',
        'url'   => get_site_url() . '/lab-grown-diamond-engagement-rings/heart',
    ],
];

$common_page['left_ring_shapes']  = $left_ring_shapes;
$common_page['right_ring_shapes'] = $right_ring_shapes;

$carat_diamonds = [
    [
        'name' => '1 Carat Lab Diamonds',
        'url'  => '/collections/1-carat-lab-grown-diamonds/?carat=1.00,1.25',
    ],
    [
        'name' => '2 Carat Lab Diamonds',
        'url'  => '/collections/2-carat-lab-grown-diamonds/?carat=2.00,2.25',
    ],
    [
        'name' => '3 Carat Lab Diamonds',
        'url'  => '/collections/3-carat-lab-grown-diamonds/?carat=3.00,3.25',
    ],
    [
        'name' => '4 Carat Lab Diamonds',
        'url'  => '/collections/4-carat-lab-grown-diamonds/?carat=4.00,4.25',
    ],
    [
        'name' => '5 Carat Lab Diamonds',
        'url'  => '/collections/5-carat-lab-grown-diamonds/?carat=5.00,5.25',
    ],
];

$common_page['carat_diamonds'] = $carat_diamonds;
$shop_by_price = [
    [
        'name' => 'Under $500',
        'url'  => $text_change['inventory_url'].'?price=0,500',
    ],
    [
        'name' => '$500 - $1000',
        'url'  => $text_change['inventory_url'].'?price=500,1000',
    ],
    [
        'name' => '$1000 - $2000',
        'url'  => $text_change['inventory_url'].'?price=1000,2000',
    ],
    [
        'name' => '$2000 - $3000',
        'url'  => $text_change['inventory_url'].'?price=2000,3000',
    ],
    [
        'name' => '$3000 - $4000',
        'url'  => $text_change['inventory_url'].'?price=3000,4000',
    ],
    [
        'name' => '$4000 & Above',
        'url'  => $text_change['inventory_url'].'?price=4000,999999',
    ],
];
$common_page['shop_by_price'] = $shop_by_price;

$common_page['fine_earrings'] = [
    [
        'name'  => 'Stud Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/stud-earrings.svg',
        'url'   => get_site_url() . '/lab-diamond-earrings/studs/',
    ],
    [
        'name'  => 'Hoops Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/hoops-drops-earrings.svg',
        'url'   => get_site_url() . '/lab-diamond-earrings/hoops/',
    ],
    [
        'name'  => 'Drops Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/hoops-drops-earrings.svg',
        'url'   => get_site_url() . '/lab-diamond-earrings/drops/',
    ],
    [
        'name'  => 'Halo Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/haloearrings.svg',
        'url'   => get_site_url() . '/lab-diamond-earrings/halo/',
    ],
    [
        'name'  => 'Cluster Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/cluster-earrings.svg',
        'url'   => get_site_url() . '/lab-diamond-earrings/cluster/',
    ],
    [
        'name'  => 'Ready To Ship Earrings',
        'image' => get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg',
        'url'   => '/ready-to-ship/diamond-earrings/',
    ],
];

$common_page['wedding_rings'] = [
    [
        'name'  => 'Diamond Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/diamond-rings.svg',
        'url'   => get_site_url() . '/lab-diamond-wedding-bands/bands/',
    ],
    [
        'name'  => 'Anniversary Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/anniversary-rings.svg',
        'url'   => get_site_url() . '/lab-diamond-wedding-bands/anniversary/',
    ],
    [
        'name'  => 'Eternity Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/eternity-rings.svg',
        'url'   => get_site_url() . '/lab-diamond-wedding-bands/eternity/',
    ],
    [
        'name'  => 'Stackable Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/stackable-rings.svg',
        'url'   => get_site_url() . '/lab-diamond-wedding-bands/stackable/',
    ],
    [
        'name'  => 'Curved Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/curved-bands.svg',
        'url'   => get_site_url() . '/lab-diamond-wedding-bands/curved/',
    ],
    [
        'name'  => 'Ready To Ship Wedding Bands',
        'image' => get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg',
        'url'   => get_site_url() . '/ready-to-ship/wedding-bands/',
    ],
];

$common_page['pendants'] = [
    [
        'name'  => 'Solitaire Pendants',
        'image' => get_template_directory_uri() . '/assets/image/header/solitaire-pendants.svg',
        'url'   => get_site_url() . '/lab-diamond-pendants/solitaire/',
    ],
    [
        'name'  => 'Halo Pendants',
        'image' => get_template_directory_uri() . '/assets/image/header/halo-pendants.svg',
        'url'   => get_site_url() . '/lab-diamond-pendants/halo/',
    ],
    [
        'name'  => 'Diamond Pendants',
        'image' => get_template_directory_uri() . '/assets/image/header/diamond-pendant.svg',
        'url'   => '/lab-grown-diamond-pendants',
    ],
    [
        'name'  => 'Ready To Ship Pendants',
        'image' => get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg',
        'url'   => get_site_url() . '/ready-to-ship/pendants/',
    ],
    [
        'name'  => 'Chains',
        'image' => get_template_directory_uri() . '/assets/image/header/necklaceIcon.svg',
        'url'   => get_site_url() . '/chains',
    ],
];

$common_page['bracelets'] = [
    [
        'name'  => 'Tennis Bracelets',
        'image' => get_template_directory_uri() . '/assets/image/header/tennis-bracelets.svg',
        'url'   => get_site_url() . '/lab-diamond-bracelets/tennis/',
    ],
    [
        'name'  => 'Solitaire Bracelets',
        'image' => get_template_directory_uri() . '/assets/image/header/tennis-bracelets.svg',
        'url'   => get_site_url() . '/lab-diamond-bracelets/tennis/',
    ],
    [
        'name'  => 'Ready To Ship Bracelets',
        'image' => get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg',
        'url'   => '/ready-to-ship/bracelets/',
    ],
];

$common_page['necklaces'] = [
    [
        'name'  => 'Diamond Necklaces',
        'image' => get_template_directory_uri() . '/assets/image/header/necklaceIcon.svg',
        'url'   => '/lab-diamond-necklaces/tennis',
    ],
    [
        'name'  => 'Tennis Necklaces',
        'image' => get_template_directory_uri() . '/assets/image/header/necklaceIcon.svg',
        'url'   =>  '/lab-diamond-necklaces/tennis',
    ],
    [
        'name'  => 'Ready To Ship Necklaces',
        'image' => get_template_directory_uri() . '/assets/image/header/ready-to-ship.svg',
        'url'   => '/ready-to-ship/necklaces',
    ],
];

$left_ring_styles = [
    [
        'name'  => 'Solitaire',
        'image' => get_template_directory_uri().'/assets/image/header/solitaire.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/solitaire',
    ],
    [
        'name'  => 'Diamond Band',
        'image' => get_template_directory_uri().'/assets/image/header/diamond-band.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/diamond-band',
    ],
    [
        'name'  => 'Three Stone',
        'image' => get_template_directory_uri().'/assets/image/header/three-stone.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/three-stone',
    ],
    [
        'name'  => 'Cluster',
        'image' => get_template_directory_uri().'/assets/image/header/cluster.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/cluster',
    ],
    [
        'name'  => 'Unique',
        'image' => get_template_directory_uri().'/assets/image/header/unique.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/unique',
    ],
];


$right_ring_styles = [
    [
        'name'  => 'Halo',
        'image' => get_template_directory_uri().'/assets/image/header/halo-rings.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/halo',
    ],
    [
        'name'  => 'Hidden Halo',
        'image' => get_template_directory_uri().'/assets/image/header/hidden-halo.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/hidden-halo',
    ],
    [
        'name'  => 'Bezel',
        'image' => get_template_directory_uri().'/assets/image/header/bezel.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/bezel',
    ],
    [
        'name'  => 'Dainty',
        'image' => get_template_directory_uri().'/assets/image/header/dainty.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/dainty',
    ],
    [
        'name'  => 'Vintage Inspired',
        'image' => get_template_directory_uri().'/assets/image/header/vintage-rings.svg',
        'url'   => get_site_url().'/lab-grown-diamond-engagement-rings/vintage',
    ],
];

$common_page['left_ring_styles']  = $left_ring_styles;
$common_page['right_ring_styles'] = $right_ring_styles;
$common_page['ready_to_ship'] = [
    
    [
        'name'  => 'Engagement Rings',
        'url'   => '/ready-to-ship/engagement-rings',
    ],
    [
        'name'  => 'Diamond Earrings',
        'url'   => '/ready-to-ship/diamond-earrings',
    ],
    [
        'name'  => 'Wedding Band',
        'url'   => '/ready-to-ship/wedding-bands',
    ],
    [
        'name'  => 'Pendants',
        'url'   => '/ready-to-ship/pendants',
    ],
    [
        'name'  => 'Bracelets',
        'url'   => '/ready-to-ship/bracelets',
    ],
    [
        'name'  => 'Necklaces',
        'url'   => '/ready-to-ship/necklaces/',
    ],
    [
        'name'  => 'Lab Diamonds',
        'url'   => '/ready-to-ship/lab-diamonds/',
    ],
];

$text_change['common_page'] = $common_page;
