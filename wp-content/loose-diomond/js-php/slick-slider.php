<?php
add_action('wp_enqueue_scripts', function () {

	/*
     * Slick Slider
     * Load only on WooCommerce product categories
     * or the New Engagement Rings page template.
     */
    if (
        is_product_category() ||
        is_page_template('page-new-engagement-rings.php')
    ) {

        wp_enqueue_style(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
            [],
            '1.8.1'
        );

        wp_enqueue_style(
            'slick-carousel-theme',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
            ['slick-carousel'],
            '1.8.1'
        );

        wp_enqueue_script(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
            ['jquery'],
            '1.8.1',
            true
        );
    }

});

/**
 * Load selected CSS files asynchronously.
 */
add_filter('style_loader_tag', function ($html, $handle, $href) {

    if (is_admin()) {
        return $html;
    }

    $async_handles = [
        // Slick
        'slick-carousel',
        'slick-carousel-theme',
    ];

    if (in_array($handle, $async_handles, true)) {

        $html = str_replace(
            "media='all'",
            "media='print' onload=\"this.media='all';this.onload=null;\"",
            $html
        );

        $html .= '<noscript>
            <link rel="stylesheet" href="' . esc_url($href) . '">
        </noscript>' . "\n";
    }

    return $html;

}, 10, 3);
