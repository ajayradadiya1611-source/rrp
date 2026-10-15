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
});