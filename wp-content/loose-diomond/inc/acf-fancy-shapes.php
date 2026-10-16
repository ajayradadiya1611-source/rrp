<?php
/**
 * ACF fields for the "Fancy Shape White Diamonds" page template
 * (page-fancy-shape-white-diamonds.php).
 *
 *   shape_cards       Repeater (add as many as needed):
 *                       card_title  - card heading (line breaks allowed)
 *                       card_link   - "Shop Now" URL
 *                       card_image  - card image
 *
 * Registered in code. No-ops if ACF is off.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( array(
        'key'    => 'group_lgd_fancy_shapes',
        'title'  => 'Fancy Shapes Content',
        'fields' => array(
            array(
                'key'          => 'field_lgd_fs_cards',
                'label'        => 'Shape Cards',
                'name'         => 'shape_cards',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add Card',
                'instructions' => 'Leave empty to show the default six cards.',
                'sub_fields'   => array(
                    array(
                        'key'          => 'field_lgd_fs_card_title',
                        'label'        => 'Title',
                        'name'         => 'card_title',
                        'type'         => 'textarea',
                        'rows'         => 2,
                        'new_lines'    => 'br',
                        'instructions' => 'Press Enter for a line break.',
                        'wrapper'      => array( 'width' => '40' ),
                    ),
                    array(
                        'key'          => 'field_lgd_fs_card_link',
                        'label'        => 'Shop Now Link',
                        'name'         => 'card_link',
                        'type'         => 'url',
                        'wrapper'      => array( 'width' => '30' ),
                    ),
                    array(
                        'key'           => 'field_lgd_fs_card_image',
                        'label'         => 'Image',
                        'name'          => 'card_image',
                        'type'          => 'image',
                        'return_format' => 'array',
                        'preview_size'  => 'medium',
                        'wrapper'       => array( 'width' => '30' ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-fancy-shape.php',
                ),
            ),
        ),
    ) );
} );
