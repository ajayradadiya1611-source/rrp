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
    'hero_banner_image_desktop' => get_template_directory_uri().'/aj_custom/home-page/wholesale-main-desktop.webp',
    'hero_banner_image_mobile' => get_template_directory_uri().'/aj_custom/home-page/wholesale-main-mobile.webp', 
);
$text_change['home_page'] = $home_page;
$common_page = array(
    "trustpilot_total_rating" => '250'
);
$common_page['top_bar'] = [
    "INTERNATIONAL SHIPPING & FREE RETURNS (USA) | WE PRICE MATCH | 100% MONEY BACK GUARANTEE",
   
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
    ["name" => "Blog", "url" => get_site_url() . "/blog/"],
];

$common_page['desktop_footer_3'] = [
   /* ["name" => "Diamond Financing", "url" => get_site_url() ."/diamond-financing/"],
    ["name" => "Engagement Ring Financing", "url" => get_site_url() ."/engagement-ring-financing/"],*/
    ["name" => "Crypto Payment", "url" => get_site_url() . "/crypto-payment/"],
    ["name" => "Shipping Policy", "url" => get_site_url() . "/shipping/"],
    ["name" => "Return/Refund Policy", "url" => get_site_url() . "/return-policy/"],
    ["name" => "Price Match Policy", "url" => get_site_url() . "/diamond-price-match/"],
    ["name" => "Jewelry Insurance", "url" => get_site_url() . "/jewelry-insurance/"],
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
        'url'   => $text_change['inventory_url'] . '?shape=round',
    ],
    [
        'name'  => 'Emerald',
        'image' => get_template_directory_uri() . '/assets/image/header/emerald.svg',
        'url'   => $text_change['inventory_url'].'?shape=emerald',
    ],
    [
        'name'  => 'Marquise',
        'image' => get_template_directory_uri() . '/assets/image/header/marquise.svg',
        'url'   => $text_change['inventory_url'].'?shape=marquise',
    ],
    [
        'name'  => 'Oval',
        'image' => get_template_directory_uri() . '/assets/image/header/oval.svg',
        'url'   => $text_change['inventory_url'].'?shape=oval',
    ],
    [
        'name'  => 'Radiant',
        'image' => get_template_directory_uri() . '/assets/image/header/radiant.svg',
        'url'   => $text_change['inventory_url'].'?shape=radiant',
    ],
];

$right_shapes = [
    [
        'name'  => 'Princess',
        'image' => get_template_directory_uri() . '/assets/image/header/princess.svg',
        'url'   => $text_change['inventory_url'].'?shape=princess',
    ],
    [
        'name'  => 'Asscher',
        'image' => get_template_directory_uri() . '/assets/image/header/asscher.svg',
        'url'   => $text_change['inventory_url'].'?shape=asscher',
    ],
    [
        'name'  => 'Cushion',
        'image' => get_template_directory_uri() . '/assets/image/header/cushion.svg',
        'url'   => $text_change['inventory_url'].'?shape=cushion',
    ],
    [
        'name'  => 'Pear',
        'image' => get_template_directory_uri() . '/assets/image/header/pear.svg',
        'url'   => $text_change['inventory_url'].'?shape=pear',
    ],
    [
        'name'  => 'Heart',
        'image' => get_template_directory_uri() . '/assets/image/header/heart.svg',
        'url'   => $text_change['inventory_url'].'?shape=heart',
    ],
];

$common_page['left_shapes']  = $left_shapes;
$common_page['right_shapes'] = $right_shapes;


$left_ring_shapes = [
    [
        'name'  => 'Round',
        'image' => get_template_directory_uri() . '/assets/image/header/round.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=round-brilliant-cut',
    ],
    [
        'name'  => 'Emerald',
        'image' => get_template_directory_uri() . '/assets/image/header/emerald.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=emerald-cut',
    ],
    [
        'name'  => 'Marquise',
        'image' => get_template_directory_uri() . '/assets/image/header/marquise.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=marquise-cut',
    ],
    [
        'name'  => 'Oval',
        'image' => get_template_directory_uri() . '/assets/image/header/oval.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=oval-cut',
    ],
    [
        'name'  => 'Radiant',
        'image' => get_template_directory_uri() . '/assets/image/header/radiant.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=radiant-cut',
    ],
];

$right_ring_shapes = [
    [
        'name'  => 'Princess',
        'image' => get_template_directory_uri() . '/assets/image/header/princess.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=princess-cut',
    ],
    [
        'name'  => 'Asscher',
        'image' => get_template_directory_uri() . '/assets/image/header/asscher.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=asscher-cut',
    ],
    [
        'name'  => 'Cushion',
        'image' => get_template_directory_uri() . '/assets/image/header/cushion.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=cushion-cut',
    ],
    [
        'name'  => 'Pear',
        'image' => get_template_directory_uri() . '/assets/image/header/pear.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=pear-cut',
    ],
    [
        'name'  => 'Heart',
        'image' => get_template_directory_uri() . '/assets/image/header/heart.svg',
        'url'   => get_site_url() . '/build-a-ring/?shape=heart-cut',
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
        'url'   => get_site_url().'/build-a-ring/?style=solitaire',
    ],
    [
        'name'  => 'Diamond Band',
        'image' => get_template_directory_uri().'/assets/image/header/diamond-band.svg',
        'url'   => get_site_url().'/build-a-ring/?style=diamond-band',
    ],
    [
        'name'  => 'Three Stone',
        'image' => get_template_directory_uri().'/assets/image/header/three-stone.svg',
        'url'   => get_site_url().'/build-a-ring/?style=three-stone',
    ],
    [
        'name'  => 'Cluster',
        'image' => get_template_directory_uri().'/assets/image/header/cluster.svg',
        'url'   => get_site_url().'/build-a-ring/?style=cluster',
    ],
    [
        'name'  => 'Unique',
        'image' => get_template_directory_uri().'/assets/image/header/unique.svg',
        'url'   => get_site_url().'/build-a-ring/?style=unique',
    ],
];


$right_ring_styles = [
    [
        'name'  => 'Halo',
        'image' => get_template_directory_uri().'/assets/image/header/halo-rings.svg',
        'url'   => get_site_url().'/build-a-ring/?style=halo',
    ],
    [
        'name'  => 'Hidden Halo',
        'image' => get_template_directory_uri().'/assets/image/header/hidden-halo.svg',
        'url'   => get_site_url().'/build-a-ring/?style=hidden-halo',
    ],
    [
        'name'  => 'Bezel',
        'image' => get_template_directory_uri().'/assets/image/header/bezel.svg',
        'url'   => get_site_url().'/build-a-ring/?style=bezel',
    ],
    [
        'name'  => 'Dainty',
        'image' => get_template_directory_uri().'/assets/image/header/dainty.svg',
        'url'   => get_site_url().'/build-a-ring/?style=dainty',
    ],
    [
        'name'  => 'Vintage Inspired',
        'image' => get_template_directory_uri().'/assets/image/header/vintage-rings.svg',
        'url'   => get_site_url().'/build-a-ring/?style=vintage-inspired',
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
];

$text_change['common_page'] = $common_page;
