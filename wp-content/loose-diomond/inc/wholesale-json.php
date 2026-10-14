<?php
global $text_change;
$inventory_faq = array(
    array(
        'question' => 'What are lab grown diamonds?',
        'answer'   => 'Lab grown diamonds are the diamonds created in a lab under controlled conditions with advanced technology replicating the formation process of natural diamonds.'
    ),
    array(
        'question' => 'How are lab grown diamonds made?',
        'answer'   => 'Lab diamonds are made using HPHT (High Pressure High Temperature) and CVD (Chemical Vapor Deposition), helping carbon crystallisation into the formation of diamond.'
    ),
    array(
        'question' => 'Are lab grown diamonds real diamonds?',
        'answer'   => 'Yes, Lab grown diamonds are real diamonds with identical features like physical, chemical, and optical appearance to mined diamonds.'
    ),
    array(
        'question' => 'Are lab grown diamonds cheaper than natural diamonds?',
        'answer'   => 'Mostly, yes, lab grown diamonds are cheaper than natural ones of similar size and quality.'
    ),
    array(
        'question' => 'Are lab made diamonds worth buying?',
        'answer'   => 'Lab made diamonds are worth buying as they provide benefits like practical shopping, valuable size, quality, and efficient prices. The key benefit is that it provides high grading and certifications.'
    ),
    array(
        'question' => 'Are lab diamonds real or man-made diamonds?',
        'answer'   => 'Lab grown diamonds are real diamonds. They’re man-made in labs but have the same physical, chemical, and optical properties as natural diamonds.'
    ),
    array(
        'question' => 'Where can I buy lab grown diamonds online?',
        'answer'   => 'To get high quality lab grown diamonds online one of the most reputable online platforms is Loose Grown Diamond. Our website provides certified diamonds with detailed specifications and transparent policies.'
    ),
    array(
        'question' => 'Are lab grown diamonds more ethical than mined diamonds?',
        'answer'   => 'Lab diamonds are ethical and environmentally friendly as they do not include labor exploitation, and depend on the manufacturer’s resources.'
    ),
    array(
        'question' => 'Which certification is best for lab grown diamonds?',
        'answer'   => 'Certifications from reputed gemological institutions like IGI, GIA, and GCAL with guaranteed reports.'
    ),
    array(
        'question' => 'Do you offer financing options for lab grown diamonds?',
        'answer'   => 'Yes, Loose grown diamond provides exceptional <a href="/diamond-financing/">financing</a> options for lab diamonds with transparent policies and easy installments.'
    ),
    array(
        'question' => 'How often is your loose lab diamond inventory updated?',
        'answer'   => 'Inventory updates depend on retailer to retailer. However, many platforms refresh their listings daily as per availability.'
    ),
    array(
        'question' => 'Can I use lab diamonds for an engagement ring?',
        'answer'   => 'Lab diamonds are most popular for their wide range of engagement rings. They are widely used due to their quality, value, and durability.'
    ),
    array(
        'question' => 'Are lab diamonds suitable for wedding bands and fine jewelry?',
        'answer'   => 'Yes, the durability and hardness as that of natural diamonds make the lab grown diamonds perfect for wedding bands and fine jewelry.'
    ),
    array(
        'question' => 'Can loose lab diamonds be used to create custom jewelry?',
        'answer'   => 'Yes, loose lab grown diamonds are specifically made for customization with smooth detail in size, style, carvings, and setting style.'
    )
);

$text_change['inventory_faq'] = $inventory_faq; 
$inventory_color_faq = array(
    array(
        'question' => 'Are fancy color lab diamonds real?',
        'answer'   => 'Yes. Fancy colored lab diamonds are genuine diamonds, which feature physical, chemical, and optical similarities in the same way as the naturally colored diamonds.'
    ),
    array(
        'question' => 'What is a fancy color lab diamond?',
        'answer'   => 'Fancy colored lab diamond- A lab made diamond that appears with such natural colors as yellow, pink, or even blue.'
    ),
    array(
        'question' => 'Which color in fancy color lab diamonds is best?',
        'answer'   => 'The most suitable color is all about taste. The yellow and pink are more popular and less valued and the blue and red are less popular and more valuable.'
    ),
    array(
        'question' => 'How rare are fancy color lab diamonds?',
        'answer'   => 'Fancy colored diamonds are also extremely rare in nature. Lab tech ensures more accessibility of these colors as well as keeping their quality nondefective.'
    ),
    array(
        'question' => 'Can I use fancy color diamonds for an engagement ring?',
        'answer'   => 'Yes. They are glamorous and suitable for everyday use, and this makes them the best engagement rings.'
    ),
    array(
        'question' => 'Are fancy color diamonds ethically sourced?',
        'answer'   => 'Yes, Lab grown diamonds come from controlled processes that reduce environmental impact and avoid mining problems.'
    ),
    array(
        'question' => 'Can I customize jewelry with fancy color diamonds?',
        'answer'   => 'Yes, you can customize jewelry with fancy color diamonds. Our website lets you choose the diamond color, size, and setting to create a piece that perfectly matches your style.'
    ),
    array(
        'question' => 'Where to buy colored lab grown diamonds online?',
        'answer'   => 'You can buy certified fancy colored lab grown diamonds online from trusted retailers like Loose Grown Diamond, which offer certified diamonds, transparent pricing, and customization options to suit your preferences.'
    )
);

$text_change['inventory_color_faq'] = $inventory_color_faq;
// Placeholder for globals, assuming they are defined elsewhere


function get_product_category_faqs() {

    $faqs = array(

        'engagement-rings' => array(
            array(
    'question' => 'What\'s the band width of the ring? Can I change it?',
    'answer'   => 'The band width is provided in the product information. Yes! You can customize your ring\'s band width.<br><br>

Please note that the standard band width depends on the size of the center diamond. If you choose a larger or smaller center stone, our design team will adjust the band width accordingly to ensure the ring remains well-proportioned. For example, a ring with a 1 ct center diamond typically has a band width of 1.80 mm or less.<br><br>

If you\'d like a different band width, simply include your request in the <strong>Order Notes</strong> section during checkout.<br><br>

<strong>Custom Band Width Pricing:</strong><br>
&bull; Up to 1.80 mm: Complimentary<br>
&bull; Up to 2.00 mm: +$100<br>
&bull; Up to 2.20 mm: +$200<br>
&bull; Up to 2.40 mm: +$400<br>
&bull; Above 2.40 mm: Please contact us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> for a custom quote.<br><br>

Please note: Increasing the band width may also increase the ring\'s total gold weight, which is reflected in the customization cost.'
),
            array(
                'question' => 'If I purchase a diamond and a setting together, will they arrive mounted or separately?',
                'answer'   => 'If you purchase a diamond and setting together, we will professionally set the diamond in your chosen setting before shipping. Your completed jewelry will arrive securely packaged in a <a href="https://www.youtube.com/shorts/EEFLGnzTZCk">premium jewelry box</a>, ready to wear or gift.'
            ),
            array(
                'question' => 'What are the color and clarity grades of the side/small diamonds in the setting?',
                'answer'   => 'If the center stone is D, E, or F color with VVS or VS clarity, we use a mix of DEF color and VVS–VS clarity diamonds for the side stones. If the center stone is G, H, or I color with VS or SI clarity, we use a mix of GHI color and VS–SI clarity diamonds to complement the center stone.'
            ),
            array(
                'question' => 'Can I make my engagement ring two-tone? ',
                'answer'   => 'Yes! We offer two-tone customization, allowing you to mix different metal colors — for example, a yellow gold band with a white gold basket and prongs. You can also choose combinations of white, yellow, or rose gold, as well as platinum.'
            ),
            array(
                'question' => 'Do you provide a ring box with the purchase?',
                'answer'   => 'Yes! Every order includes a premium, ready-to-gift ring box with a luxurious finish and soft interior, perfect for proposals, gifts, or safekeeping. 
                    <div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/EEFLGnzTZCk?rel=0" allowfullscreen="allowfullscreen"></iframe></div>',
                'isyoutube' => true,
            ),
            array(
                'question' => 'What does your warranty cover?',
                'answer'   => "Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within ".RETURN_DAYS." days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we'll be happy to assist you.<br><br>
                If your jewelry requires a service that isn't covered under our warranty, please reach out to us. We'll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                ."
            ),
            array(
                'question' => 'Does LGD create custom engagement rings?',
                'answer'   => 'Yes! We would be delighted to bring your dream ring design to life. To get started, email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or contact our customer service team at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question' => 'Do you offer free resizing for all your rings?',
                'answer'   => 'Yes! We offer one complimentary ring resize within '.RETURN_DAYS.' days of delivery on all rings purchased from Loose Grown Diamond.<br><br>
                To request a resize, simply contact our support team via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>. To help us process your request as quickly as possible, please have your order number and desired ring size ready.<br><br>
                Please note: Certain wedding bands cannot be resized and are not covered under this policy.'
            ),
            array(
                'question' => 'How should I get my finger sized?',
                'answer'   => 'You can use our <a href="/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf">Ring Sizer</a> or visit a local jewelry store for professional sizing. When getting sized, note the type of ring sizer used — thin, wide, or comfort fit — as this may affect the recommended ring size.'
            ),
            array(
                'question' => 'Can you engrave my ring or jewelry for free?',
                'answer'   => 'Yes! We offer complimentary engraving on eligible rings and jewelry to make your piece even more personal.<br><br>
                When placing your order, simply enter your engraving request on the cart page. If you have any questions or special requests, our customer support team will be happy to assist you.<br><br>
                <strong>Please note:</strong> Engraving is subject to space limitations and the design of the jewelry.'
            ),
            array(
                'question' => 'What is the typical crafting time for rings or jewelry with side stones?',
                'answer'   => 'The crafting time depends on the shape of the side stones:<br><br>
                <strong>Round side stones:</strong> Typically add 3–4 business days to the crafting process.<br><br>
                <strong>Fancy-shaped side stones:</strong> May extend the crafting time to 1–3 weeks, depending on availability and the complexity of the design.<br><br>
                <strong>Please note:</strong> Custom jewelry or designs with specialty side stones may require additional crafting time. If you need your order by a specific date, please contact our customer support team, and we\'ll do our best to accommodate your request'
            ),
            array(
                'question' => 'Can I get a free appraisal for my purchase?',
                'answer'   => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
            array(
                'question' => 'Can I place my order by phone?',
                'answer'   => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question' => 'Can I have my item sooner than this? ',
                'answer'   => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question' => 'How can I design my own lab-grown diamond ring?',
                'isyoutube' => true,
                'youtube_link' => 'https://youtu.be/SiFuLzZM-Rk'
            ),
            array(
                'question' => 'Can I take a virtual tour of your factory?',
                'isyoutube' => true,
                'youtube_link' => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question' => "What's the process after placing an order? ",
                'answer'   => 'Yes! Once you place your order and your center diamond has been secured, we\'ll create a CAD/3D design of your ring and email it to you within 24-48 business hours for your review and approval.<br><br>
                Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
                <strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question' => "Can you help me create a ring using the diamond I already own?",
                'answer'   => 'Absolutely! We\'d be happy to create a custom ring using your existing diamond.<br><br>
                You can choose from our wide selection of ring settings available on our website, or, if you have a specific design in mind, simply share your inspiration with us. Our team will work with you to create a setting that perfectly fits your diamond.<br><br>
                To ensure a precise and secure fit, we\'ll need your diamond\'s grading certificate (IGI, GIA, or GCAL) or its exact measurements before we begin.<br><br>
                <strong>Please note:</strong><br>
                &bull; A $100 mounting fee applies for setting your diamond.<br>
                &bull; A $100 fee applies for fully insured two-way shipping of your diamond.<br>
                &bull; Once we receive your diamond, we\'ll inspect it before proceeding with the setting process.<br><br>
                If you have any questions, our customer support team will be happy to guide you through the process.'
            ),
        ),
        'wedding-rings' => array(
            array(
                'question'      => 'Will the band width be the same for all ring sizes?',
                'answer'        => "<p>The band width listed on the product page is based on a <strong>US size 7</strong> ring. For other ring sizes, the width may vary slightly to maintain the ring's overall proportions and total carat weight.</p>

<p><strong>Larger ring sizes:</strong> The band width may be slightly thinner than listed.</p>
<p><strong>Smaller ring sizes:</strong> The band may be slightly wider than listed.</p>

<p>In some cases, additional metal may be required, which may result in an extra charge.</p>

<p>After you place your order, our team will prepare and share the CAD and 3D design for your approval before production begins.</p>"    
            ),
            array(
                'question'      => 'Do wedding rings typically come with a diamond certificate?',
                'answer'        => 'No, our wedding rings do not typically include individual diamond certificates. However, the diamonds used in our rings are generally <strong>DEF color and VVS–VS clarity</strong>, ensuring exceptional quality and brilliance.'
            ),
            array(
                'question' => 'Do you provide a ring box with the purchase?',
                'answer'   => 'Yes! Every order includes a premium, ready-to-gift ring box with a luxurious finish and soft interior, perfect for proposals, gifts, or safekeeping. 
                    <div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/EEFLGnzTZCk?rel=0" allowfullscreen="allowfullscreen"></iframe></div>',
                'isyoutube' => true,
            ),
            array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                '
            ),
            array(
                'question'      => 'Does LGD create custom wedding rings?',
                'answer'        => 'Yes! We would be delighted to bring your dream wedding ring design to life. To get started, email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or contact our customer service team at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'Do you offer free resizing for all your rings?',
                'answer'        => 'Yes! We offer one complimentary ring resize within '.RETURN_DAYS.' days of delivery on all rings purchased from Loose Grown Diamond.<br><br>
                To request a resize, simply contact our support team via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>. To help us process your request as quickly as possible, please have your order number and desired ring size ready.<br><br>
                <strong>Please note:</strong> Certain wedding bands cannot be resized and are not covered under this policy.'
            ),
            array(
                'question'      => 'How should I get my finger sized?',
                'answer'        => 'You can use our <a href="/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf">Ring Sizer</a> or visit a local jewelry store for professional sizing. When getting sized, note the type of ring sizer used — thin, wide, or comfort fit — as this may affect the recommended ring size.'
            ),
            array(
                'question'      => 'Can you engrave my ring or jewelry for free?',
                'answer'        => 'Yes! We offer complimentary engraving on eligible rings and jewelry to make your piece even more personal.<br><br>
                When placing your order, simply enter your engraving request on the cart page. If you have any questions or special requests, our customer support team will be happy to assist you.<br><br>
                <strong>Please note:</strong> Engraving is subject to space limitations and the design of the jewelry.'
            ),
            array(
                'question' => 'Can I place my order by phone?',
                'answer'   => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question' => 'Can I have my item sooner than this? ',
                'answer'   => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question'      => "What's the process after placing an order?",
                'answer'        => 'Yes! Once you place your order, we\'ll create a CAD/3D design of your wedding ring and email it to you within 24-48 business hours for your review and approval.<br><br>
                Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
                <strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

        'lab-diamond-earrings' => array(
            array(
                'question'      => 'Do the earrings come with certified or non-certified diamonds?',
                'answer'        => 'By default, our earrings feature non-certified diamonds with DEF color and VVS–VS clarity. However, you can choose certified diamonds from our inventory. Simply share the diamond SKU(s) with us, and we\'ll craft your earrings using your selected diamonds.',
            ),
            array(
                'question'      => 'Can I customize diamond earrings?',
                'answer'        => 'Yes! LGD offers customization options that allow you to choose the diamonds, setting, and design that best suit your preferences. Can\'t find the perfect setting? Share a reference image or URL with us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>, and we\'ll recreate it for you.'
            ),
            array(
                'question'      => 'Can I order a single earring?',
                'answer'        => 'Yes! Please contact us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or via live chat, and a member of our support team will create a custom order link for you.'
            ),
            array(
                'question'      => 'Does LGD create custom earrings?',
                'answer'        => 'Yes! We would be delighted to bring your dream earrings design to life. To get started, email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or contact our customer service team at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                .'
            ),
            array(
                'question'      => 'What types of diamond earring settings are available?',
                'answer'        => 'Common styles include stud, halo, bezel, hoop, and drop settings, each offering a unique look and level of security for the diamonds.'
            ),
            array(
                'question'      => 'Are colored diamonds used in earrings?',
                'answer'        => 'Yes, fancy-colored diamonds can be used in earrings to create unique, vibrant, and eye-catching designs. Choose your colored diamonds here: '.site_url().'/lab-created-colored-diamonds/'
            ),
            array(
                'question'      => 'Can I place my order by phone?',
                'answer'        => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'Can I have my item sooner than this?',
                'answer'        => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'How do I choose the right diamond size for earrings?',
                'answer'        => 'The right diamond size depends on your personal preference and budget. Consider your style, the occasion, and the overall look you want to achieve.'
            ),
            array(
                'question'      => "What's the process after placing an order?",
                'answer'        => 'Yes! Once you place your order, we\'ll create a CAD/3D design of your wedding ring and email it to you within 24-48 business hours for your review and approval.<br><br>
Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
<strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

        'lab-diamond-pendants' => array(
            array(
                'question'      => 'Can I customize a diamond pendant?',
                'answer'        => 'Yes! Customize your pendant with your choice of diamond, setting, and design. Can\'t find the perfect setting? Share a reference image or URL with us via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>, and we\'ll recreate it for you.',
            ),
            array(
                'question'      => 'Do pendants come with a chain included?',
                'answer'        => 'No, pendants do not include a chain by default. However, you can explore our available chain options <a href="/chains">here</a> and choose the perfect one for your pendant.'
            ),
            array(
                'question'      => 'Is it possible to get a certified diamond in a pendant?',
                'answer'        => 'Yes! Simply select a certified diamond from our inventory and share the SKU with us via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>. Our team will help you create a custom pendant using your selected diamond.'
            ),
            array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                .'
            ),
            array(
                'question'      => 'Do the pendants come with certified or non-certified diamonds?',
                'answer'        => 'By default, our pendants feature non-certified diamonds with DEF color and VVS–VS clarity. However, you can choose a certified diamond from our inventory.<br><br>
                Simply share the selected diamond SKU with us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or via live chat, and our support team will create a custom order link for you.'
            ),
            array(
                'question'      => 'Can I place my order by phone?',
                'answer'        => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'Can I have my item sooner than this?',
                'answer'        => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'Can I add an engraving to a diamond pendant?',
                'answer'        => 'Yes! LGD offers complimentary engraving, allowing you to personalize your pendant with a message, name, or date. Simply add your engraving instructions in the Order Notes section at checkout.<br><br>
                <strong>Please note:</strong> Engraving is subject to space limitations and the design of the jewelry.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question'      => "What's the process after placing an order?",
                'answer'        => 'Yes! Once you place your order, we\'ll create a CAD/3D design of your wedding ring and email it to you within 24-48 business hours for your review and approval.<br><br>
                Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
                <strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

        'chains' => array(
            array(
                'question'      => 'What chain length should I choose?',
                'answer'        => 'Chain lengths typically range from 16 to 20 inches. Consider your neck size, personal style, and desired fit when choosing the right length.'
            ),
            array(
                'question'      => 'Can I customize the design of my platinum chain?',
                'answer'        => 'Yes! We offer customization options, allowing you to choose the length, thickness, and style of your platinum chain. You can also add a pendant to create a personalized piece.'
            ),
            array(
                'question'      => 'Can the chain length be adjusted?',
                'answer'        => 'Yes! If you\'d like a different chain length, we can customize it for you. Simply add your sizing instructions in the Order Notes section at checkout.'
            ),
            array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                .'
            ),
            array(
                'question'      => 'Can I place my order by phone?',
                'answer'        => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'Can I have my item sooner than this?',
                'answer'        => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

        'lab-diamond-bracelets' => array(
            array(
                'question'      => 'Can the size of a tennis bracelet be adjusted?',
                'answer'        => 'Yes! If you\'d like a different size, we can adjust the bracelet for you. Simply add your sizing instructions in the <strong>Order Notes</strong> section at checkout.'
            ),
            array(
                'question'      => 'How loose should a tennis bracelet be?',
                'answer'        => 'A tennis bracelet should fit comfortably and move slightly on your wrist without feeling too loose. For the ideal fit, you should be able to fit one finger between the bracelet and your wrist.'
            ),
         array(
                'question'      => 'Can you shorten a tennis bracelet?',
                'answer'        => 'Yes! Tennis bracelets can be shortened by removing one or more links. We can customize the bracelet length for you, or a local jeweler can adjust it after purchase.'
            ),
           array(
                'question'      => 'Can I Wear My Diamond Bracelet Every Day? ',
                'answer'        => 'Of course! In fact, it’s very common for people to wear them 24/7.'
            ),
            array(
                'question'      => 'Does LGD create custom diamond tennis bracelets?',
                'answer'        => 'Yes! LGD would be delighted to create your dream custom diamond tennis bracelet. To get started, email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or contact our customer service team at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
           array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                .'
            ),
            array(
                'question'      => 'Can I place my order by phone?',
                'answer'        => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
           array(
                'question'      => 'Can I have my item sooner than this?',
                'answer'        => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'Can I add an engraving to a tennis bracelet?',
                'answer'        => 'Yes! LGD offers complimentary engraving, allowing you to personalize your bracelet with a message, name, or date. Simply add your engraving instructions in the Order Notes section at checkout.<br><br>
                <strong>Please note:</strong> Engraving is subject to the available space and the jewelry\'s design.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
           array(
                'question'      => "What's the process after placing an order?",
                'answer'        => 'Yes! Once you place your order, we\'ll create a CAD/3D design of your wedding ring and email it to you within 24-48 business hours for your review and approval.<br><br>
                Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
                <strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

        'lab-diamond-necklaces' => array(
            array(
                'question'      => 'What are the different necklace sizes?',
                'answer'        => 'Necklaces are offered in a range of sizes, starting from 16 inches for choker-style necklaces, and extending up to approximately 30 inches, which is often favoured for achieving ideal designs.',
            ),
            array(
                'question'      => 'How many carats should a diamond necklace have?',
                'answer'        => 'The diamond’s usage and budget should determine the diamond carat for a necklace. If you want a tennis diamond necklace, then a carat weight of 3-12 carats is suitable.'
            ),
            array(
                'question'      => 'How much does a diamond necklace cost?',
                'answer'        => 'Diamond necklaces can range in price from $3000 to $10,000 (and higher).'
            ),
            array(
                'question'      => 'How do I choose the right diamond necklace?',
                'answer'        => 'Consider your personal style, budget, and the occasion you intend to wear the necklace for. Research different styles and diamond qualities to make an informed decision.'
            ),
            array(
                'question'      => 'Are diamond necklaces a popular gift?',
                'answer'        => 'Yes, diamond necklaces are a popular and cherished gift for special occasions like birthdays, anniversaries, engagements and weddings.'
            ),
            array(
                'question'      => 'Can I customize a diamond necklace?',
                'answer'        => 'Yes! LGD can create a custom diamond necklace based on your preferred design. If you can\'t find the perfect setting, share a reference image or URL with us via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>, and we\'ll recreate it for you.'
            ),
            array(
                'question'      => 'Is it possible to get certified diamonds in a necklace?',
                'answer'        => 'Yes! You can customize your necklace with certified diamonds. To get started, contact us via live chat or email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>.'
            ),
            array(
                'question'      => 'What does your warranty cover?',
                'answer'        => 'Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>
                We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>
                If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.<br><br>
                '
            ),
            array(
                'question'      => 'Can I place my order by phone?',
                'answer'        => 'Absolutely! If you\'d prefer to place your order over the phone, our Customer Support team will be happy to assist you.<br><br>
                <strong>Call us:</strong> <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.'
            ),
            array(
                'question'      => 'Can I have my item sooner than this?',
                'answer'        => 'Yes! Email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to <a href="/product/express-shipping/">Express Shipping</a>.'
            ),
            array(
                'question'      => 'Can I add an engraving to a tennis necklace?',
                'answer'        => 'Yes! LGD offers complimentary engraving, allowing you to personalize your bracelet with a message, name, or date. Simply add your engraving instructions in the Order Notes section at checkout.<br><br>
                <strong>Please note:</strong> Engraving is subject to the available space and the jewelry\'s design.'
            ),
            array(
                'question'      => 'Can I take a virtual tour of your factory?',
                'answer'        => '',
                 'isyoutube'    => true,
                'youtube_link'  => 'https://youtu.be/0-p0uZiwInA'
            ),
            array(
                'question'      => "What's the process after placing an order?",
                'answer'        => 'Yes! Once you place your order, we\'ll create a CAD/3D design of your wedding ring and email it to you within 24-48 business hours for your review and approval.<br><br>
                Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.<br><br>
                <strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.'
            ),
            array(
                'question'      => 'Can I get a free appraisal for my purchase?',
                'answer'        => 'Yes! We provide a complimentary digital appraisal letter for your purchase.<br><br>
                Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.'
            ),
        ),

    );

    return $faqs;
}


global $shippingFaqData;

$shippingFaqData = [
    [
        'title' => 'Do you ship worldwide?',
        'description' => '<div><p>Yes! We offer worldwide shipping to most countries.</p>
<p>Please note: We currently do not ship to the following countries:</p>
<p>Pakistan<br>
China<br>
Nepal<br>
Sri Lanka<br>
Bangladesh<br>
Bhutan<br>
Myanmar<br>
Afghanistan<br>
Jamaica</p></div>'
    ],
    [
        'title' => 'When will my order ship?',
        'description' => '<div><p><strong>Loose Diamonds:</strong></p>
<p>Shipping($50): Orders typically dispatch within 2 to 5 business days.</p>
<p>🚚 <strong>Truck Icon:</strong><br>
Diamonds with the truck icon are in our U.S. inventory and typically arrive within 3–5 business days, even with Free Shipping.</p>
<p>🚀 <strong>Rocket Icon:</strong><br>
Diamonds with the rocket icon qualify for overnight delivery on business days only. (Excludes weekends and holidays.)</p>
<p>Please note: If your order includes multiple diamonds and one or more aren\'t eligible for overnight delivery, the entire order will ship using our standard shipping timeline.</p>
<p><strong>Engagement Rings &amp; Fine Jewelry:</strong></p>
<ul>
   <li><strong> Shipping:</strong> Delivered within 3-4 weeks</li>
</ul>
<p><strong>Need your order by a specific date?</strong><br>
If you have a delivery deadline or would like your package held at a FedEx location, add your request in the Order Notes at checkout or email <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a> with your order number. We\'ll do our best to accommodate your request.</p>
<p>Please note: We do not ship on weekends or public holidays.</p></div>'
    ],
    [
        'title' => 'Can I get my order faster if I didn\'t choose Express Shipping?',
        'description' => '<div><p>Yes! Email us at <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a> with your order number, and we\'ll do our best to expedite your order. For the quickest delivery, we recommend upgrading to Express Shipping.</p></div>'
    ],
    [
        'title' => 'What\'s included in my order?',
        'description' => '<div><ul>
   <li>Diamond or jewelry</li>
   <li>Original IGI, GIA, or GCAL grading report (for eligible certified diamonds)</li>
   <li>Digital appraisal letter (available upon request)</li>
</ul>
<p>Please note: For orders shipped to the Gulf and European regions, the original grading report for certified diamonds under 1.00 ct or valued below $1,500 may be shipped separately.</p></div>'
    ],
    [
        'title' => 'Are your shipments fully insured?',
        'description' => '<div><p>Yes! Every order is fully insured and requires an adult signature (21+) upon delivery.</p></div>'
    ],
    [
        'title' => 'How long will it take to receive my order?',
        'description' => '<div><p>Once your order has been shipped, delivery typically takes 1-3 business days.</p>
<p>Please note: This estimate does not include the crafting time for rings and fine jewelry.</p></div>'
    ],
    [
        'title' => 'Can I track my order online?',
        'description' => '<div><p>Yes! You can check the status of your order anytime by visiting the Orders section in <a href="/my-account/">My Account</a>.</p>
<p>Once your order has been shipped, we\'ll email you a tracking number, which you can use to track your package on the carrier\'s website.</p></div>'
    ],
    [
        'title' => 'Are there any duties or taxes I have to pay?',
        'description' => '<div><p>If you\'re located in the United States, no customs duties or import taxes are payable.</p>
<p>For international orders, customs duties, taxes, and import fees are the responsibility of the recipient. Once your package arrives in your country, the shipping carrier will contact you to collect any applicable charges before delivery.</p>
<p>The amount varies by country, state, and local regulations, so we\'re unable to provide an exact estimate. We recommend checking with your local customs authority or government website for more information.</p>
<p>Please note:</p>
<p>Customers in Europe may also be required to pay customs broker/clearance fees.<br>
We cannot undervalue shipments or declare a lower invoice value on customs documents.</p></div>'
    ],
    [
        'title' => 'Do I have to pay customs duties and taxes if I\'m from Canada?',
        'description' => '<div><p>Yes. If you\'re shipping to Canada, you\'ll be responsible for paying any applicable customs duties, taxes, and import fees in accordance with Canadian import regulations. These charges are collected directly by the shipping carrier before delivery.</p></div>'
    ],
    [
        'title' => 'What should I do if my order doesn\'t arrive within the estimated delivery time?',
        'description' => '<div>If your order hasn\'t arrived within the estimated timeframe, please email us at <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a> or contact our Customer Support team at +' . SUPPORT_MOBILE . '. We\'ll be happy to help you track your package and provide an update on its status.</div>'
    ],
    [
        'title' => 'I\'m not going to be home when my package is scheduled to arrive. What should I do?',
        'description' => '<div>No problem! We can often arrange to have your package held for pickup at a nearby FedEx location. Simply contact us via live chat or email <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a>, and we\'ll be happy to help arrange it for you.</div>'
    ],
    [
        'title' => 'Can you ship my package to a FedEx location for pickup?',
        'description' => '<div><p>Absolutely! We can ship your order to a FedEx Hold at Location for convenient pickup. FedEx will hold your package for up to 2 business days.</p>
<p>To arrange this, please email <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a> with your order number and your preferred FedEx pickup location along with the zip code.</p>
<p>Please note:<br>
Once your order has been shipped, we are unable to reroute it to a different address or FedEx location.<br>
FedEx will attempt delivery up to three times before returning the package to us.<br>
If the package is returned and needs to be reshipped, a $50 reshipping fee will apply.</p></div>'
    ],
    [
        'title' => 'What if my diamond or jewelry gets lost during shipment?',
        'description' => '<div><p>At Wholesale Loose Grown Diamond, we take every precaution to ensure your order arrives safely. All shipments are fully insured during transit and require an adult signature upon delivery.</p>
<p>In the unlikely event that your package is lost in transit, we will investigate the shipment with the carrier. If the package is confirmed as lost, you will receive either a full refund or a replacement, based on your preference and product availability.</p></div>'
    ],
    [
        'title' => 'What\'s the claim process if my shipment is lost?',
        'description' => '<div><p>If your shipment is suspected to be lost, we will immediately file a claim with the shipping carrier and begin an investigation.</p>
<p>Once the carrier confirms the shipment is lost, we will arrange a replacement or full refund, depending on your preference and product availability. The claims process typically takes 2–4 weeks, so we kindly ask for your patience and cooperation while the investigation is completed.</p></div>'
    ],
];


global $returnfaqData;
$returnfaqData = [
    [
        'title' => 'What is the return period?',
        'description' => '<div><p>You may return eligible items within ' . RETURN_DAYS . ' days of delivery. Returns requested after ' . RETURN_DAYS . ' days are not eligible for a refund.</p></div>'
    ],
    [
        'title' => 'Why do you offer a ' . RETURN_DAYS . '-day return policy?',
        'description' => '<div><p>We want you to shop with complete confidence. That\'s why we offer a ' . RETURN_DAYS . '-day return policy, giving you plenty of time to inspect your purchase, try it on, and ensure it\'s exactly what you expected.</p><p>If you\'re not completely satisfied, you can return your eligible item within ' . RETURN_DAYS . ' days of delivery, subject to our Returns &amp; Refund Policy.</p><p><strong>Note: The ' . RETURN_DAYS . '-day return period begins on the date your order is delivered.</strong></p></div>'
    ],
    [
        'title' => 'What is Loose Grown Diamond\'s cancellation policy?',
        'description' => '<div><p>You may cancel your order before it has been packed or shipped. Once an order has been shipped, it can no longer be canceled. Please contact our support team as soon as possible if you need assistance.</p></div>'
    ],
    [
        'title' => 'I just canceled my order. When will I receive my refund?',
        'description' => '<div class="faq_item_p">Refunds are issued to your original payment method within 24–48 business hours after your cancellation is confirmed.</div>'
    ],
    [
        'title' => 'Can I change the shipping address after placing my order?',
        'description' => '<div>Yes! Please contact our customer support team as soon as possible. We can update your shipping address before your order has been shipped.</div>'
    ],
    [
        'title' => 'Can special or bespoke orders be returned and refunded?',
        'description' => '<div>
<div>Yes! Special, custom and bespoke orders are eligible for return and a refund, subject to our ' . RETURN_DAYS . '-Day Returns &amp; Refund Policy.</div>
</div>'
    ],
    [
        'title' => 'When are returns not accepted?',
        'description' => '<div><p>Returns cannot be accepted if:</p>
<ul>
<li>The ' . RETURN_DAYS . '-day return period has expired.</li>
<li>The item is used, damaged, or not in its original condition.</li>
<li>The required IGI, GIA, or GCAL grading report (where applicable) is missing.</li>
<li>The item is a round or fancy-shaped melee diamond, calibrated diamond, custom cut diamond, or fancy color melee diamond, as these are final sale and non-returnable.</li>
</ul></div>'
    ],
    [
        'title' => 'How can I initiate a return?',
        'description' => '<div>To start a return, simply email <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a> with your order number and the reason for your return.</div><p><strong>Note: Each customer is allowed up to 3 diamond returns or exchanges per month. For any returns beyond that, a 10% restocking fee will apply.</strong></p>'
    ],
    [
        'title' => 'Do I have to pay for return shipping?',
        'description' => '<div><p>For U.S. customers, we provide a prepaid return shipping label.</p><p>For international customers, we reimburse up to $50 USD toward return shipping costs within the ' . RETURN_DAYS . '-day return period. If the return shipping cost exceeds $50 USD, the customer is responsible for the remaining balance.</p></div>'
    ],
    [
        'title' => 'What is the refund method?',
        'description' => '<div><p>All refunds are issued to the original payment method used to place the order.</p></div>'
    ],
    [
        'title' => 'Why is my refund less than my original purchase amount?',
        'description' => '<div>If you\'re located outside the U.S., your refund may be lower due to currency conversion fees or exchange rate fluctuations charged by your bank or payment provider.</div><div>We always process a full refund for the approved amount. However, we are not responsible for any currency conversion or foreign transaction fees applied by your bank.</div><div>If you\'d like a refund receipt, please email <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a>. We also recommend contacting your bank for more information about any applicable conversion charges.</div>'
    ],
    [
        'title' => 'Will there be any deductions if I return an order purchased using my review Store Credit?',
        'description' => '<div><p>Yes. If you purchased your order using Store Credit earned by posting a review on social media platforms such as Reddit, Instagram, TikTok, or similar platforms, a $50 shipping and handling fee will be deducted from your Store Credit balance for each return.</p><p>Please note: This policy applies only to promotional Store Credit earned through review campaigns and does not apply to other forms of Store Credit or refunds.</p></div>'
    ],
    [
        'title' => 'How will I know when my refund has been initiated?',
        'description' => '<div>We\'ll send you an email confirmation as soon as your refund has been initiated. Refunds typically appear in your account within 2–10 business days, depending on your bank or payment provider.</div>'
    ],
    [
        'title' => 'I still haven\'t received my refund. Why?',
        'description' => '<div><p>If you\'ve received a refund confirmation email, your refund has been successfully initiated. Most refunds are processed within 2–10 business days, but delays can occasionally occur due to your bank or payment provider.</p><p>If you haven\'t received your refund after this timeframe, please contact us at <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a>, and we\'ll be happy to assist you.</p></div>'
    ],
    [
        'title' => 'Can I get a refund for customs duties or taxes if I return my order?',
        'description' => '<div><p>No. We are not responsible for refunding any customs duties, import taxes, or other government fees paid on your order.</p><p>To request a refund of these charges, please contact your local customs authority or visit their official website for guidance on the refund process.</p></div>'
    ],
];

global $crypytoFaqData;
$crypytoFaqData = [
    [
        'title' => 'How do I pay with ETH, BTC, USDT, or other cryptocurrencies?',
        'description' => '<div>You\'ll receive a wallet address or QR code that allows you to pay using Bitcoin (BTC), Ethereum (ETH), USDT, or other supported cryptocurrencies.</div>'
    ],
    [
        'title' => 'What types of payment do you accept?',
        'description' => '<div><p>We accept all major credit cards (Visa, Mastercard, American Express, and Discover), as well as Stripe, Zelle, Wire transfer, Wise and Crypto Payments.</p><p>If you choose to pay by Wire Transfer, we\'ll send you the wiring instructions after you place your order. Please note that we\'ll begin processing your order only after we receive confirmation from our bank that the payment has been received.</p><p>If you have any questions, please <a href="/contact-us/"><strong>contact us</strong></a>, live chat or <strong>Call/WhatsApp us at +' . SUPPORT_MOBILE . '</strong>.</p><p>PS: If payment is not received within 2 business days of placing your order, your order will be automatically canceled due to non-payment. Product availability and pricing cannot be guaranteed after cancellation.</p></div>'
    ],
    [
        'title' => 'Can I pay with two different credit cards?',
        'description' => '<div><p>Yes. If you\'d like to pay with two different credit cards, please email us at <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a>, live chat or contact us via WhatsApp, text, or phone at +' . SUPPORT_MOBILE . '. We\'re available 24 hours a day, 7 days a week to assist you.</p></div>'
    ],
    [
        'title' => 'Do you offer financing/EMI?',
        'description' => '<div>Yes. We offer EMI and financing options for <strong>loose diamonds</strong> and <strong>jewelry</strong> through Affirm, Splitit, Afterpay, and Klarna.</div>'
    ],
    [
        'title' => 'I\'m trying to place an order through Splitit, but it isn\'t working. Why?',
        'description' => '<div>You must have enough available credit on your card to cover the full value of your purchase, as an authorization hold is placed on the outstanding balance to guarantee future payments to the merchant. For more information, please refer to this article or contact Splitit: <a href="https://www.splitit.com/faq/shoppers/">https://www.splitit.com/faq/shoppers/</a></div>'
    ],
];

global $diamondFinanceGeneralFaqData;
$diamondFinanceGeneralFaqData = [
    [
        'title'       => 'Can you Finance Diamonds?',
        'description' => '<div>Yes, diamond financing options are available, making it easier to purchase a diamond without paying the full amount upfront.</div>'
    ],
    [
        'title'       => 'What credit score do I need to finance a Diamond?',
        'description' => '<div>A good credit score increases your chances of approval and may qualify you for better financing terms and options.</div>'
    ],
    [
        'title'       => 'Is it better to finance a Diamond or pay cash?',
        'description' => '<div>Financing your diamond can be a smart choice, as making timely installment payments may help you build your credit score. It also helps you manage your finances without exceeding your budget and allows you to plan your future expenses more effectively.</div>'
    ],
    [
        'title'       => 'Does diamond financing affect credit score?',
        'description' => '<div>Yes, making timely installment payments can have a positive impact on your credit score.</div>'
    ],
    [
        'title'       => 'Can I finance a Diamond with bad credit?',
        'description' => '<div>Yes, financing may still be available through BNPL or other alternative payment options, even if you have a poor credit score. However, financing terms may vary.</div>'
    ],
    [
        'title'       => 'Is BNPL safe for Diamonds?',
        'description' => '<div>Yes, Buy Now, Pay Later (BNPL) is a secure and convenient payment option offered through trusted financing partners.</div>'
    ],
    [
        'title'       => 'Can I get financing on bulk diamonds?',
        'description' => '<div>Yes, financing options may be available for bulk diamond purchases, subject to eligibility and approval.</div>'
    ],
    [
        'title'       => 'What happens if I miss a payment?',
        'description' => '<div>Missing a payment may negatively affect your credit score, result in late fees, and, in some cases, lead to account restrictions.</div>'
    ],
    [
        'title'       => 'Can I pay early without penalty or charges?',
        'description' => '<div>Yes, most financing options allow early repayment without penalties or additional charges.</div>'
    ],
];
global $engagementRingFinancingFaqData;
$engagementRingFinancingFaqData = [
    [
        'title'       => 'What credit score do I need to finance an engagement ring?',
        'description' => '<div>Most financing options require applicants to have a fair to good credit score, while some BNPL and Afterpay options may be available to customers with limited or average credit.</div>'
    ],
    [
        'title'       => 'Is it better to finance a ring or pay cash?',
        'description' => '<div>Paying with cash eliminates interest charges, while financing helps preserve your savings and provides greater financial flexibility. The best option depends on your cash flow and financial goals.</div>'
    ],
    [
        'title'       => 'Does engagement ring financing affect my credit score?',
        'description' => '<div>Yes. Making timely payments can improve your credit score, while missed payments may negatively affect it.</div>'
    ],
    [
        'title'       => 'Can I finance an engagement ring with bad credit?',
        'description' => '<div>Yes, customers with bad credit may still qualify for financing through BNPL, Afterpay, and, in some cases, Affirm. Eligibility and financing terms may vary.</div>'
    ],
    [
        'title'       => 'Is BNPL safe for engagement rings?',
        'description' => '<div>Yes, BNPL is a secure payment option when used responsibly. Be sure to review the payment schedule, applicable fees, and any late payment penalties before choosing this option.</div>'
    ],
    [
        'title'       => 'What happens if I miss a payment?',
        'description' => '<div>Missing a payment may result in late fees, interest charges (if applicable), and may negatively impact your credit score, depending on the financing provider.</div>'
    ],
];

global $faq_page_about;
$faq_page_about = [
                            [
                                'title' => 'What is Loose Grown Diamond?',
                                'description' => '<div><p>Loose Grown Diamond is a leading manufacturer and retailer of premium lab-grown diamonds and fine jewelry, offering exceptional quality directly to customers at competitive prices.</p></div>'
                            ],
                            [
                                'title' => 'Why are our prices so competitive?',
                                'description' => '<div>
<div>Our prices are lower because we source directly from manufacturers including our own facilities and operate with a lean, efficient team. By cutting out unnecessary middlemen, we pass the savings directly to you.</div>
</div>'
                            ],
                            [
                                'title' => 'Do you have a retail store near me?',
                                'description' => '<div><p>Loose Grown Diamond has a showroom and office in New York City. If you\'d like to visit us, please schedule an appointment in advance, and our team will be happy to assist you.</p>
                                <p>As an online-first retailer, we keep our operating costs low and work directly with manufacturers. This allows us to offer certified lab-grown diamonds and fine jewelry at manufacturer-direct prices without compromising on quality or service.</p> 
                                <p>We offer 7-day returns policy and Expert support from our GIA-certified gemologists.</p></div>'
                                    
                            ],
                            [
                                'title' => 'Do you have gemologists on staff?',
                                'description' => '<div>
<div>Absolutely! We have GIA-certified gemologists on staff who are available to answer your questions and provide expert guidance on diamonds, certifications, and jewelry to help you make an informed purchase.</div>
</div>'
                            ],
                            [
                                'title' => 'Where is the corporate office of Loose Grown Diamond located?',
                                'description' => '<div><p>Our corporate office is located in New York City, in the heart of the Diamond District. The address is listed below:<br><br>Loose Grown Diamond Corporate Office<br>55W 47th St., Suite #790<br>New York, NY-10036, USA<br><br>📍 View on Google Maps:<br><a href="https://g.page/r/CfgPY3z14oWaEAE">https://g.page/r/CfgPY3z14oWaEAE</a></p><p>If you\'d like to visit us, please schedule an appointment in advance, and our team will be happy to assist you.</p></div>'
                            ],
                            [
                                'title' => 'Can I take a virtual tour of your factory?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/0-p0uZiwInA?rel=0" allowfullscreen="allowfullscreen"></iframe></div>'
                            ],
                        ];
global $faq_page_diamond_jewelry;

$faq_page_diamond_jewelry = [
                            [
                                'title' => 'How can you offer such low prices on your diamonds and jewelry?',
                                'description' => '<div><p>We manufacture our own jewelry and source our loose diamonds directly from our factories and trusted manufacturing partners. By cutting out middlemen and keeping our operations efficient, we offer premium-quality diamonds and jewelry at manufacturer-direct prices delivering exceptional value without compromising on quality.</p></div>'
                            ],
                            [
                                'title' => 'Do you offer a Low Price Guarantee?',
                                'description' => '<div><p>Yes! We offer a Low Price Guarantee. We are committed to providing the highest-quality diamonds and jewelry at the most competitive prices.</p><p>If you find a same certified diamond with the same specifications at a lower price from an eligible competitor, simply share the product URL with us. We\'ll review it and, in many cases, match or even beat the competitor\'s price.</p><p>Learn more about our Price Match & Beat Guarantee <a href="/diamond-price-match/">here</a>.</p></div>'
                            ],
                            [
                                'title' => 'I found two diamonds with the same carat, color, clarity, and cut, but they have different prices. Am I missing something?',
                                'description' => '<div><p>Not at all! This is completely normal.</p><p>We partner with 750+ trusted manufacturers and offer an inventory of over 2.5 million certified lab-grown diamonds. Since each manufacturer sets its own pricing, you may find two diamonds with identical specifications listed at different prices.</p><p>Rest assured, a lower price does not mean lower quality. The diamonds are independently graded by reputable laboratories, such as IGI, GIA, or GCAL, ensuring they meet the quality stated on their grading reports.</p><p>If you\'re deciding between two similar diamonds, our GIA-trained gemologists are happy to help you compare them and recommend the best value for your budget.</p></div>'
                            ],
                            [
                                'title' => 'Are your lab-grown diamonds eco-friendly and conflict-free?',
                                'description' => '<div>
<div><strong>Yes!</strong> Lab-grown diamonds are <strong>conflict-free</strong> and generally considered a more environmentally responsible alternative to mined diamonds.</div>
</div>'
                            ],
                            [
                                'title' => 'I found the same diamond on another retailer\'s website. Can I buy it from you?',
                                'description' => '<div>
<div>Absolutely! Simply share the product URL and we\'ll review it and we\'ll match or even beat the competitor\'s price.</div><p>You\'ll enjoy the same certified diamond, exceptional customer service, and the confidence of shopping with Loose Grown Diamond.</p>
</div>'
                            ],
                            [
                                'title' => 'How do I search for a diamond using its SKU or Certificate Number?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe
                                            src="https://www.youtube.com/embed/KGOp8Pcj8zk?si=8wjXPBfAv3zeDPzd"
                                            title="YouTube video player"
                                            frameborder="0"
                                            allow="
                                              accelerometer;
                                              autoplay;
                                              clipboard-write;
                                              encrypted-media;
                                              gyroscope;
                                              picture-in-picture;
                                              web-share;
                                            "
                                            allowfullscreen
                                          ></iframe></div>'
                            ],
                            [
                                'title' => 'How often do you add diamonds to your inventory?',
                                'description' => '<div>
<div>Our inventory is updated <strong>every 2 hours</strong>, with new certified diamonds added throughout the day. This ensures you always have access to the latest selection and the best available prices.</div>
</div>'
                            ],
                            [
                                'title' => 'If I buy a lab-grown diamond, will anyone be able to tell?',
                                'description' => '<div>
<div>No - not with the naked eye. Lab-grown diamonds are physically, chemically, and optically identical to natural diamonds. Even experienced jewelers cannot distinguish between them without specialized equipment.</div><p>It takes advanced diamond-testing equipment and trained professionals to identify whether a diamond is natural or lab-grown.</p>
</div>'
                            ],
                            [
                                'title' => 'How do I search for a diamond using the filters?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/hQGY4T98_5M?rel=0" allowfullscreen="allowfullscreen"></iframe></div>'
                            ],
                            [
                                'title' => 'How do I view a diamond\'s 360° video?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/ps7l7Nhezh0?rel=0" allowfullscreen="allowfullscreen"></iframe></div>'
                            ],
                            [
                                'title' => 'What if the diamond doesn\'t have a 360° video?',
                                'description' => '<div><p>Don\'t worry! Some diamonds may not have a 360° video available because the manufacturer hasn\'t uploaded one yet.</p><p>Simply contact our team, and we\'ll do our best to obtain a 360° video or a live tweezer/natural light video of the diamond from the manufacturer.</p><p>Please note:</p><ul><li>We rely on our manufacturing partners to provide 360° and live tweezer videos.</li><li>Video requests typically take up to 24-48 business hours to process.</li><li>Since our inventory is live, the diamond may sell out before the video is received.</li><li>While we\'ll do our best to obtain the requested video, we cannot guarantee its availability, quality, or that the manufacturer will approve the request.</li><li>A maximum of 2 video requests is allowed per customer.</li></ul><p>If you\'ve found a diamond you love, we recommend placing your order first to reserve it while we request the video. This helps ensure the diamond doesn\'t sell out before your purchase is complete.</p></div>'
                            ],
                            [
                                'title' => 'How can I design my own lab-grown diamond ring?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/SiFuLzZM-Rk?rel=0" allowfullscreen="allowfullscreen"></iframe></div>'
                            ],
                            [
                                'title' => 'How can I design my custom diamond ring for free?',
                                'description' => '<div>
<div>We offer complimentary CAD and 3D design services for your first custom ring design.</div><p>Simply click here to submit your custom design request. To help us get started, please include your ring size, preferred metal, center stone, and any reference images or design ideas.</p><p>Your first CAD and 3D design is completely free. If you request additional design revisions that require creating a new CAD model or a completely new setting design, each subsequent CAD design will incur a non-refundable fee of $20.</p><p>This fee helps cover the time and resources required to create additional custom designs.</p>
</div>'
                            ],
                            [
                                'title' => 'How can I find fancy-color lab-grown diamonds?',
                                'isyoutube' => true,
                                'description' => '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/gtI8X086erc?rel=0" allowfullscreen="allowfullscreen"></iframe></div>'
                            ],
                            [
                                'title' => 'How can I be sure I\'ll receive the exact diamond I ordered?',
                                'description' => '<div><p>Every certified diamond we sell is accompanied by an independent grading report from IGI, GIA, or GCAL, confirming its quality and unique characteristics.</p><p>You can verify that you\'ve received the correct diamond by matching the report details such as the certificate number, carat weight, color, clarity, and measurements. You may also have the diamond inspected by a local jeweler or independent gemologist for additional peace of mind.</p><p>Please note: Calibrated, melee and non certified diamonds are not individually certified and therefore do not include a grading report.</p></div>'
                            ],
                            [
                                'title' => 'Can I put a diamond on hold?',
                                'description' => '<div><p>Yes! We can request a temporary hold on a diamond with the manufacturer for up to 6 hours. This gives you time to confirm your decision with your partner, family, or friends before completing your purchase.</p><p>Holds are processed on a first-come, first-served basis and are subject to the manufacturer\'s approval.</p><p>Please note:</p><ul><li>A hold request is sent directly to the manufacturer, as we do not control the inventory or reservation process.</li><li>While we will do our best to secure the diamond, we cannot guarantee that the manufacturer will approve the hold request.</li><li>If the diamond is sold before the hold is confirmed, we\'ll help you find the closest available alternative or assist you with a refund if applicable.</li></ul><p>Tip: The best way to guarantee your diamond is reserved is to place your order as soon as possible.</p></div>'
                            ],
                            [
                                'title' => 'How precise are your calibrated diamonds?',
                                'description' => '<div><p>Our calibrated lab-grown diamonds are manufactured to a high level of precision, with a size tolerance of ±0.10 mm. This ensures consistent dimensions, making them ideal for pre-made jewelry settings, repairs, and matching projects.</p><p>Please note: A slight variation of up to 0.10 mm is normal and falls within industry standards for calibrated diamonds.</p></div>'
                            ],
                            [
                                'title' => 'What is the crafting time for rings and fine jewelry?',
                                'description' => '<div>
<p>Our standard crafting time is up to 2 weeks for all rings and fine jewelry before shipment.</p><p><strong>Please note:</strong> Crafting times may vary depending on the design, customization, and order volume. If you need your order by a specific date, please contact our customer support team, and we\'ll do our best to accommodate your request.</p>
</div>'
                            ],
                            [
                                'title' => 'What is the typical crafting time for rings or jewelry with side stones?',
                                'description' => '<div><p>The crafting time depends on the shape of the side stones:</p><ul><li><strong>Round side stones:</strong> Typically add 3–4 business days to the crafting process.</li><li><strong>Fancy-shaped side stones:</strong> May extend the crafting time to 1–3 weeks, depending on availability and the complexity of the design.</li></ul><p><strong>Please note:</strong> Custom jewelry or designs with specialty side stones may require additional crafting time. If you need your order by a specific date, please contact our customer support team, and we\'ll do our best to accommodate your request.</p></div>'
                            ],
                            [
                                'title' => 'What is the crafting time for treated colored melee diamonds?',
                                'description' => '<div><p>The standard crafting time for jewelry featuring treated colored melee diamonds (Blue, Green, Pink, Purple, and Yellow) is typically 2–4 weeks.</p><p><strong>Please note:</strong> The exact crafting time may vary depending on the color, availability, and complexity of the design. If you need your order by a specific date, please contact our customer support team, and we\'ll do our best to accommodate your request.</p></div>'
                            ],
                           [
                                'title' => 'Can I see the CAD/3D design of my ring or jewelry before it\'s made?',
                                'description' => '<div><p>Yes! Once you place your order and your center diamond has been secured, we\'ll create a CAD/3D design of your ring or jewelry and email it to you within 48 business hours for your review and approval.</p><p>Production will begin only after you approve the CAD/3D design, ensuring you\'re completely satisfied before we start crafting your jewelry.</p><p><strong>Please note:</strong> Custom design requests or multiple design revisions may require additional time.</p></div>'
                            ],
                            [
                                'title' => 'Is there a fee for creating the CAD/3D design for my custom ring?',
                                'description' => '<div><p>Yes! Your first CAD/3D design is completely free. We don\'t charge for creating the initial CAD/3D model of your custom jewelry, and we offer unlimited revisions to that design until you\'re satisfied.</p><p>However, if you decide to change the <strong>entire ring setting</strong> or request a <strong>new CAD/3D design</strong> after the initial design has been completed, each additional CAD/3D design will incur a <strong>non-refundable fee of $20</strong>.</p><p>This fee helps cover the time and resources required to create a completely new design.</p></div>'
                            ],
                            [
                                'title' => 'If I order a diamond and a setting from Loose Grown Diamond, will it arrive as a complete ring?',
                                'description' => '<div><p>Yes! If you purchase both a diamond and a setting from Loose Grown Diamond, your diamond will be professionally set by our expert jewelers and shipped to you as a fully assembled, ready-to-wear ring.</p><p>Before shipping, every ring undergoes a thorough quality inspection to ensure it meets our high standards of craftsmanship and security.</p></div>'
                            ],
                            [
                                'title' => 'Can I buy a setting without a diamond from Loose Grown Diamond?',
                                'description' => '<div><p>Yes! You can purchase a setting without a center diamond from Loose Grown Diamond.</p><p>If you plan to use your own diamond, please provide us with the diamond\'s grading certificate (IGI, GIA, or GCAL) or its exact measurements (length × width × depth) before placing your order. This allows our jewelers to craft the setting for a precise and secure fit.</p><p>Please note: Since the setting is custom-made specifically for your diamond, orders for settings without a center diamond are final sale and are not eligible for return or exchange.</p><p>If you have any questions about compatibility, our customer support team will be happy to assist you before you place your order.</p></div>'
                            ],
                            [
                                'title' => 'Does your fine jewelry come with a certificate?',
                                'description' => '<div>
                                    <div>Our fine jewelry collection, including wedding rings, pendants, necklaces, bracelets, chains, and earrings, does not come with an individual diamond grading certificate by default.</div><p>We have intentionally chosen not to certify these pieces, as they are set with <strong>high-quality DEF color and VVS/VS clarity lab-grown diamonds</strong>, ensuring exceptional brilliance and value.</p><p>If you would like your jewelry to be individually certified, we\'re happy to arrange it upon request. Please contact us before placing your order, and we\'ll provide you with the available certification options and the associated cost.</p></div>'
                            ],
                            [
                                'title' => 'Can you help me create a ring using the diamond I already own?',
                                'description' => '<div><p>Absolutely! We\'d be happy to create a custom ring using your existing diamond.</p><p>You can choose from our wide selection of ring settings available on our website, or, if you have a specific design in mind, simply share your inspiration with us. Our team will work with you to create a setting that perfectly fits your diamond.</p><p>To ensure a precise and secure fit, we\'ll need your diamond\'s grading certificate (IGI, GIA, or GCAL) or its exact measurements before we begin.</p><p>Please note:</p><ul><li>A $100 mounting fee applies for setting your diamond.</li><li>A $100 fee applies for fully insured two-way shipping of your diamond.</li><li>Once we receive your diamond, we\'ll inspect it before proceeding with the setting process.</li></ul><p>If you have any questions, our customer support team will be happy to guide you through the process.</p></div>'
                            ],
                            [
                                'title' => 'Can I get a free appraisal for my purchase?',
                                'description' => '<div><p>Yes! We provide a complimentary digital appraisal letter for your purchase.</p><p>Simply email us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> with your order number, and our team will send you a fully digital appraisal letter at no additional cost. The appraisal can be used for insurance purposes and includes the relevant details of your purchase.</p></div>'
                            ],
                        ];
global $faq_page_payments;
$faq_page_payments = [
                            [
                                'title' => 'What payment methods do you accept?',
                                'description' => '<div><p>We accept a wide range of secure payment methods, including:</p><p></p><ul><li>Visa, Mastercard, American Express, and Discover</li><li>Stripe</li><li>Zelle</li><li>Wire Transfer / Wise</li><li>Cryptocurrency</li></ul><p>Wire transfer instructions will be provided after you place your order. Please note that your order will be processed once we receive confirmation that the funds have cleared in our bank account.</p><p>If you have any questions, please contact us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> or call/WhatsApp us at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>.</p><p><strong>Please note:</strong> If payment is not received within 2 business days of placing your order, your order will be automatically canceled due to non-payment. Product availability and pricing cannot be guaranteed after cancellation.</p></div>'
                            ],
                            [
                                'title' => 'Can I pay with two different credit cards?',
                                'description' => '<div><p>Yes! If you\'d like to split your payment between two credit cards, we\'d be happy to assist you.</p><p>Please contact us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a>, or reach out via WhatsApp, text, or phone at <a href="tel:+'.SUPPORT_MOBILE.'">'.SUPPORT_MOBILE.'</a>. Our customer support team is available 24 hours a day, 7 days a week and will help you complete your order.</p></div>'
                            ],
                            [
                                'title' => 'Do you offer financing or EMI?',
                                'description' => '<div>Yes, we do offer EMI and financing and installment payment options for eligible <strong>loose diamonds</strong> and <strong>jewelry</strong> through Affirm, Splitit, Afterpay, and Klarna.</div>'
                            ],
                            [
                                'title' => 'I\'m trying to place an order through Splitit, but it isn\'t working. Why?',
                                'description' => '<div><p>Splitit requires that your credit card has enough available credit to cover the full value of your purchase. Although you\'ll pay in installments, Splitit places an authorization hold for the outstanding balance to guarantee future payments to the merchant.</p><p>If you don\'t have sufficient available credit on your card, your transaction may be declined.</p><p>For more information, please refer to Splitit\'s FAQ or contact Splitit Customer Support directly: <a href="https://www.splitit.com/faq/shoppers/">https://www.splitit.com/faq/shoppers/</a></p></div>'
                            ],
                            [
                                'title' => 'How do I pay with Bitcoin (BTC), Ethereum (ETH), USDT, or other cryptocurrencies?',
                                'description' => '<div><p>Paying with cryptocurrency is easy! Once you place your order, we\'ll provide you with a wallet address or QR code that allows you to pay using Bitcoin (BTC), Ethereum (ETH), USDT, or other supported cryptocurrencies.</p><p>After your payment has been confirmed on the blockchain, we\'ll begin processing your order.</p><p>Please note: If you have any questions, our customer support team will be happy to assist you.</p></div>'
                            ],
                        ];
global $faq_page_insurance_care;
$faq_page_insurance_care = [
                            [
                                'title' => 'Do you clean or provide care for jewelry purchased from your website?',
                                'description' => '<div>Yes. We offer complimentary cleaning and care services within '.RETURN_DAYS.' days of delivery to ensure your jewelry arrives in perfect condition and continues to look its best.<br><br>After the initial '.RETURN_DAYS.'-day period, we do not provide ongoing cleaning, maintenance, or repair services. For long-term peace of mind, you can purchase an optional Extend Protection Plan at checkout. Depending on the plan selected, it provides coverage for accidental damage, mechanical breakdowns, and other eligible repairs.<br><br>Jewelry Care Tip:<br><br>Over time, the prongs on any ring may loosen with normal wear. To help prevent diamonds from becoming loose or falling out, we recommend having your jewelry professionally inspected every 9–12 months. A jeweler can check the setting, tighten the prongs if needed, and ensure your jewelry remains secure and in excellent condition.</div>'
                            ],
                            [
                                'title' => 'What does your warranty cover?',
                                'description' => '<div><p>Our warranty covers any manufacturing defects or issues present when you receive your jewelry. It also includes <strong>complimentary prong tightening, polishing, and rhodium plating within '.RETURN_DAYS.' days of delivery.</strong><br><br>We stand behind the quality of our craftsmanship. If you notice any defects or issues upon receiving your order, please contact us, and we\'ll be happy to assist you.<br><br>If your jewelry requires a service that isn\'t covered under our warranty, please reach out to us. We\'ll inspect your item and provide you with a repair assessment and any applicable service costs before proceeding.</p></div>'
                            ],
                            [
                                'title' => 'Do you offer jewelry insurance?',
                                'description' => '<div><p>While we do not directly provide jewelry insurance, we do provide an insurance appraisal with your purchase that you can use to obtain coverage.</p><p>For added peace of mind, we\'ve partnered with Jewelers Mutual® Insurance Company and Extend to offer the option of insuring your jewelry through their personal jewelry insurance plans. Applying is quick and easy, helping protect your jewelry against theft, loss, damage, and more. <a href="/jewelry-insurance/">Learn more here</a>.</p><p>Important:</p><p>Jewelry insurance is available in all U.S. states and Canada, except the province of Quebec. Loose Grown Diamond is not a licensed insurance agent or broker for Jewelers Mutual Insurance Company and does not sell or endorse insurance products. Coverage is subject to Jewelers Mutual\'s terms, conditions, and eligibility requirements.</p></div>'
                            ],
                        ];