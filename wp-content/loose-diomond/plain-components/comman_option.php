<?php
// Register the Options Page (shows in admin menu)
add_action('init', function () {
    if (!function_exists('acf_add_options_page')) {
        return;
    }
    acf_add_options_page([
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'site-settings',
        'capability' => 'manage_options',
        'redirect'   => false,
        'icon_url'   => 'dashicons-star-filled',
        'position'   => 80,
    ]);
});

// Register the Fields for that Options Page
add_action('init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group([
        'key'    => 'group_site_ratings',
        'title'  => 'Ratings',
        'fields' => [
            [
                'key'           => 'field_google_rating',
                'label'         => 'Google Rating',
                'name'          => 'google_rating',
                'type'          => 'text',
                'instructions'  => 'e.g. 4.8 (1,250 reviews)',
                'default_value' => '',
            ],
            [
                'key'           => 'field_trustpilot_rating',
                'label'         => 'Trustpilot Rating',
                'name'          => 'trustpilot_rating',
                'type'          => 'text',
                'instructions'  => 'e.g. 4.7 (980 reviews)',
                'default_value' => '',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'site-settings',
                ],
            ],
        ],
    ]);
    acf_add_local_field_group(array(
    'key' => 'group_new_engagement_rings',
    'title' => 'New Engagement Rings Page',
    'fields' => array(

        // ───────── Hero ─────────
        array(
            'key' => 'field_oc_hero_tab',
            'label' => 'Hero Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_breadcrumb_heading',
            'label' => 'Breadcrumb Heading',
            'name' => 'oc_breadcrumb_heading',
            'type' => 'text',
            'default_value' => "Engagement Rings",
        ),
        array(
            'key' => 'field_oc_hero_heading',
            'label' => 'Hero Heading',
            'name' => 'oc_hero_heading',
            'type' => 'text',
            'default_value' => "Create the Ring She’ll Never Forget",
        ),
        array(
            'key' => 'field_oc_hero_description',
            'label' => 'Hero Description',
            'name' => 'oc_hero_description',
            'type' => 'textarea',
            'rows' => 3,
            'default_value' => 'Choose from 1,000+ settings and 2.5M+ certified lab diamonds to create her perfect ring — without the retail markup.',
        ),
        array(
            'key' => 'field_oc_hero_image',
            'label' => 'Hero Image',
            'name' => 'oc_hero_image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_oc_hero_button_1_text',
            'label' => 'Button 1 Text',
            'name' => 'oc_hero_button_1_text',
            'type' => 'text',
            'default_value' => 'Start With a Setting',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_hero_button_1_url',
            'label' => 'Button 1 URL',
            'name' => 'oc_hero_button_1_url',
            'type' => 'text',
            'default_value' => '/build-a-ring/',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_hero_button_2_text',
            'label' => 'Button 2 Text',
            'name' => 'oc_hero_button_2_text',
            'type' => 'text',
            'default_value' => 'Start With a Lab Diamond',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_hero_button_2_url',
            'label' => 'Button 2 URL',
            'name' => 'oc_hero_button_2_url',
            'type' => 'text',
            'default_value' => '/lab-diamonds/',
            'wrapper' => array('width' => '50'),
        ),

        // ───────── Popular Rings ─────────
        array(
            'key' => 'field_oc_popular_tab',
            'label' => 'Popular Rings Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_popular_rings_heading',
            'label' => 'Section Heading',
            'name' => 'oc_popular_rings_heading',
            'type' => 'text',
            'default_value' => 'Most Popular Diamond Engagement Rings',
        ),
        array(
            'key' => 'field_oc_popular_rings_message',
            'label' => '',
            'type' => 'message',
            'message' => 'Select the WooCommerce products to show in the slider. Two lists are shown depending on WEBSITE_TYPE (Wholesale vs Retail) since product slugs differ between the two.',
        ),
        array(
            'key' => 'field_oc_popular_rings_products_retail',
            'label' => 'Products — Retail',
            'name' => 'oc_popular_rings_products_retail',
            'type' => 'relationship',
            'instructions' => 'Products shown in the slider when the site is NOT wholesale.',
            'post_type' => array('product'),
            'filters' => array('search'),
            'elements' => array('featured_image'),
            'min' => 0,
            'max' => 10,
            'return_format' => 'object',
        ),
        array(
            'key' => 'field_oc_popular_rings_featured_retail',
            'label' => 'Featured Product — Retail',
            'name' => 'oc_popular_rings_featured_retail',
            'type' => 'post_object',
            'instructions' => 'The product highlighted as the featured item in the retail slider.',
            'post_type' => array('product'),
            'return_format' => 'object',
        ),
        array(
            'key' => 'field_oc_popular_rings_products_wholesale',
            'label' => 'Products — Wholesale',
            'name' => 'oc_popular_rings_products_wholesale',
            'type' => 'relationship',
            'instructions' => 'Products shown in the slider when WEBSITE_TYPE is WHOLESALE.',
            'post_type' => array('product'),
            'filters' => array('search'),
            'elements' => array('featured_image'),
            'min' => 0,
            'max' => 10,
            'return_format' => 'object',
        ),
        array(
            'key' => 'field_oc_popular_rings_featured_wholesale',
            'label' => 'Featured Product — Wholesale',
            'name' => 'oc_popular_rings_featured_wholesale',
            'type' => 'post_object',
            'instructions' => 'The product highlighted as the featured item in the wholesale slider.',
            'post_type' => array('product'),
            'return_format' => 'object',
        ),

        // ───────── Shop By Style ─────────
        array(
            'key' => 'field_oc_ring_styles_tab',
            'label' => 'Shop By Style Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_ring_styles_heading',
            'label' => 'Section Heading',
            'name' => 'oc_ring_styles_heading',
            'type' => 'text',
            'default_value' => 'Shop Engagement Rings By Style',
        ),
        array(
            'key' => 'field_oc_ring_styles_by_style',
            'label' => 'Styles',
            'name' => 'oc_ring_styles_by_style',
            'type' => 'repeater',
            'instructions' => 'Each row is one style tile (image + label + link) shown in the grid.',
            'layout' => 'table',
            'button_label' => 'Add Style',
            'min' => 0,
            'sub_fields' => array(
                array(
                    'key' => 'field_ring_style_image',
                    'label' => 'Image',
                    'name' => 'image',
                    'type' => 'image',
                    'required' => 1,
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                ),
                array(
                    'key' => 'field_ring_style_label',
                    'label' => 'Label',
                    'name' => 'label',
                    'type' => 'text',
                    'required' => 1,
                    'instructions' => 'e.g. Solitaire, Halo, Bezel',
                ),
                array(
                    'key' => 'field_ring_style_link',
                    'label' => 'Link URL',
                    'name' => 'link',
                    'type' => 'text',
                    'required' => 1,
                    'instructions' => 'Relative URL, e.g. /lab-grown-diamond-engagement-rings/solitaire',
                ),
            ),
        ),

        // ───────── Shop By Shape ─────────
        array(
            'key' => 'field_oc_shape_dropdowns_tab',
            'label' => 'Shop By Shape',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_shape_dropdowns_message',
            'label' => '',
            'type' => 'message',
            'message' => 'Only shapes added below will show on the frontend. Icon and label for each shape are fixed automatically — you just pick the shape and set its link.',
        ),
        array(
            'key' => 'field_oc_shape_section_heading',
            'label' => 'Section Heading',
            'name' => 'oc_shape_section_heading',
            'type' => 'text',
            'default_value' => 'Shop Engagement Rings By Shape',
        ),
        array(
            'key' => 'field_oc_shape_dropdowns',
            'label' => 'Shapes',
            'name' => 'oc_shape_dropdowns',
            'type' => 'repeater',
            'instructions' => 'Add one row per shape you want shown. Each row\'s link is used for that shape.',
            'layout' => 'table',
            'button_label' => 'Add Shape',
            'min' => 0,
            'sub_fields' => array(
                array(
                    'key' => 'field_shape_dropdown_shape',
                    'label' => 'Shape',
                    'name' => 'shape',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'round'    => 'Round',
                        'princess' => 'Princess',
                        'radiant'  => 'Radiant',
                        'asscher'  => 'Asscher',
                        'cushion'  => 'Cushion',
                        'oval'     => 'Oval',
                        'emerald'  => 'Emerald',
                        'pear'     => 'Pear',
                        'marquise' => 'Marquise',
                        'heart'    => 'Heart',
                    ),
                ),
                array(
                    'key' => 'field_shape_dropdown_link',
                    'label' => 'Link',
                    'name' => 'link',
                    'type' => 'text',
                    'required' => 1,
                ),
            ),
        ),

        // ───────── Design Your Own ─────────
        array(
            'key' => 'field_oc_design_own_tab',
            'label' => 'Design Your Own Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_design_own_heading',
            'label' => 'Section Heading',
            'name' => 'oc_design_own_heading',
            'type' => 'text',
            'default_value' => 'Design Your Own Engagement Ring',
        ),
        array(
            'key' => 'field_oc_design_own_cards',
            'label' => 'Cards',
            'name' => 'oc_design_own_cards',
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => 'Add Card',
            'min' => 0,
            'sub_fields' => array(
                array(
                    'key' => 'field_card_image',
                    'label' => 'Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_card_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'required' => 1,
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_card_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 2,
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_card_link',
                    'label' => 'Link URL',
                    'name' => 'link',
                    'type' => 'text',
                    'wrapper' => array('width' => '100'),
                ),
            ),
        ),

        // ───────── Reviews ─────────
        array(
            'key' => 'field_oc_reviews_tab',
            'label' => 'Reviews Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_reviews_heading',
            'label' => 'Section Heading',
            'name' => 'oc_reviews_heading',
            'type' => 'text',
            'default_value' => 'Engagement Ring Reviews',
        ),
        array(
            'key' => 'field_oc_reviews',
            'label' => 'Reviews',
            'name' => 'oc_reviews',
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => 'Add Review',
            'min' => 0,
            'sub_fields' => array(
                array(
                    'key' => 'field_review_image',
                    'label' => 'Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_review_name',
                    'label' => 'Customer Name',
                    'name' => 'name',
                    'type' => 'text',
                    'required' => 1,
                    'wrapper' => array('width' => '50'),
                ),
                array(
                    'key' => 'field_review_title',
                    'label' => 'Ring Title',
                    'name' => 'title',
                    'type' => 'text',
                    'wrapper' => array('width' => '50'),
                ),
                array(
                    'key' => 'field_review_text',
                    'label' => 'Review Text',
                    'name' => 'review',
                    'type' => 'textarea',
                    'rows' => 3,
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_review_link',
                    'label' => 'Product Link',
                    'name' => 'link',
                    'type' => 'text',
                    'wrapper' => array('width' => '100'),
                ),
            ),
        ),

        // ───────── Related Blogs ─────────
        array(
            'key' => 'field_oc_related_blog_tab',
            'label' => 'Related Blogs Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_related_blog_heading',
            'label' => 'Section Heading',
            'name' => 'oc_related_blog_heading',
            'type' => 'text',
            'default_value' => 'Engagement Ring Education',
        ),
        array(
            'key' => 'field_oc_related_blog',
            'label' => 'Related Blogs',
            'name' => 'oc_related_blog_engagement',
            'type' => 'relationship',
            'instructions' => 'Select the blog posts to show in the "Engagement Ring Education" section.',
            'post_type' => array('post'),
            'filters' => array('search', 'taxonomy'),
            'elements' => array('featured_image'),
            'min' => 0,
            'max' => 6,
            'return_format' => 'object',
        ),

        // ───────── Help / Showroom Section ─────────
        array(
            'key' => 'field_oc_help_tab',
            'label' => "Help Section (We're Here for You)",
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_help_section_heading',
            'label' => 'Heading',
            'name' => 'oc_help_section_heading',
            'type' => 'text',
            'default_value' => "We’re Here for You — Online & In Store",
        ),
        array(
            'key' => 'field_oc_help_section_description',
            'label' => 'Description',
            'name' => 'oc_help_section_description',
            'type' => 'textarea',
            'rows' => 2,
            'default_value' => 'Whether you visit our showroom or connect online, we make diamond shopping personal, easy, and tailored just for you.',
        ),
        array(
            'key' => 'field_oc_help_section_image',
            'label' => 'Image',
            'name' => 'oc_help_section_image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_oc_help_button_1_text',
            'label' => 'Button 1 Text',
            'name' => 'oc_help_button_1_text',
            'type' => 'text',
            'default_value' => 'Visit a Showroom',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_help_button_1_url',
            'label' => 'Button 1 URL',
            'name' => 'oc_help_button_1_url',
            'type' => 'text',
            'default_value' => 'https://maps.app.goo.gl/7ngQyM7CKwQGY8D29',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_help_button_2_text',
            'label' => 'Button 2 Text',
            'name' => 'oc_help_button_2_text',
            'type' => 'text',
            'default_value' => 'Book a Virtual Appointment',
            'wrapper' => array('width' => '50'),
        ),
        array(
            'key' => 'field_oc_help_button_2_url',
            'label' => 'Button 2 URL',
            'name' => 'oc_help_button_2_url',
            'type' => 'text',
            'default_value' => '/contact-us/',
            'wrapper' => array('width' => '50'),
        ),

        // ───────── FAQ ─────────
        array(
            'key' => 'field_oc_faq_tab',
            'label' => 'FAQ Section',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_oc_faq_heading',
            'label' => 'Section Heading',
            'name' => 'oc_faq_heading',
            'type' => 'text',
            'default_value' => 'Frequently Asked Questions',
        ),
        array(
            'key' => 'field_faq_repeater',
            'label' => 'FAQs',
            'name' => 'engagement_faq',
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => 'Add FAQ',
            'min' => 0,
            'sub_fields' => array(
                array(
                    'key' => 'field_faq_title',
                    'label' => 'Question',
                    'name' => 'title',
                    'type' => 'text',
                    'required' => 1,
                    'wrapper' => array('width' => '100'),
                ),
                array(
                    'key' => 'field_faq_description',
                    'label' => 'Answer',
                    'name' => 'description',
                    'type' => 'wysiwyg',
                    'required' => 1,
                    'tabs' => 'visual',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                    'wrapper' => array('width' => '100'),
                ),
            ),
        ),

    ),
    'location' => array(
        array(
            array(
                'param' => 'page_template',
                'operator' => '==',
                'value' => 'page-new-engagement-rings.php',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
));


});