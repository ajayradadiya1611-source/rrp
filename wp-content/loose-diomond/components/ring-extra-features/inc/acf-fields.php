<?php
/**
 * Ring Extra Features — ACF field registration
 *
 * Registers one ACF Field Group on the Product edit screen: a tab per
 * fixed group (Band Width / Band Style / Prong / East West / Two Tone),
 * each with one plain number field per fixed option (e.g. "1.60mm
 * (+$)"). Leave a field blank to not offer that option on a given
 * product.
 *
 * Registered in code (acf_add_local_field_group) rather than built by
 * hand in the ACF admin UI, so it ships with the theme/component and
 * doesn't need to be recreated per environment. Requires the ACF (or
 * ACF Pro) plugin to be active — this file no-ops harmlessly if it isn't.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    $groups = lgd_ref_get_group_definitions();
    $fields = array();

    foreach ( $groups as $group_key => $group ) {
        // Tab per group, so the fields are organised in the product
        // editor the same way they're grouped in the customer-facing modal.
        $fields[] = array(
            'key'   => 'field_ref_tab_' . $group_key,
            'label' => $group['label'],
            'name'  => '',
            'type'  => 'tab',
        );

        foreach ( $group['options'] as $option_key => $option_def ) {
            $option = lgd_ref_normalize_option( $option_def );
            $fields[] = array(
                'key'          => 'field_' . lgd_ref_price_field_name( $option_key ),
                'label'        => $option['label'] . ' (+$)',
                'name'         => lgd_ref_price_field_name( $option_key ),
                'type'         => 'number',
                'instructions' => 'Leave blank to not offer "' . $option['label'] . '" as an upgrade on this product. 0 offers it for free.'
                    . ( $option['note'] ? ' (' . $option['note'] . ')' : '' ),
                'required'     => 0,
                'min'          => 0,
                'step'         => 0.01,
            );
        }
    }

    acf_add_local_field_group( array(
        'key'      => 'group_ring_extra_features',
        'title'    => 'Ring Upgrade Options & Prices',
        'fields'   => $fields,
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'product',
                ),
            ),
        ),
        'position'  => 'normal',
        'style'     => 'default',
        'instructions_placement' => 'field',
    ) );
} );
