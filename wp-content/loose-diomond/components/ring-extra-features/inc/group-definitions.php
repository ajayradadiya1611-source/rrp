<?php
/**
 * Ring Extra Features — fixed groups and fixed options
 *
 * Everything here is fixed in code — five groups, each with a fixed
 * option list. Only PRICE varies per product, via a plain ACF number
 * field per option (see inc/acf-fields.php); leave a field blank to
 * not offer that option on a given product.
 *
 * Fixed copy (none of this is per-product, none of it comes from ACF):
 *   - 'subtitle' on a group — one line shown under the group heading
 *     in the modal.
 *   - 'note' on an option — shown as a "?" tooltip next to that option
 *     in the modal (hover/tap to read it).
 *
 * To rename/add/remove a GROUP or an OPTION, or edit any subtitle or
 * tooltip text: edit this file. To reprice an option on a given
 * product: edit its ACF field on that product's edit screen — no code
 * change needed for that.
 *
 * Structure:
 *   'group_key' => array(
 *       'label'          => 'Shown as the group heading in the modal + ACF tab',
 *       'subtitle'       => 'One line shown under the group heading',
 *       'selection_type' => 'single' | 'multiple',
 *       'options'        => array(
 *           'option_key' => array( 'label' => 'Option label', 'note' => 'Tooltip text' ),
 *       ),
 *   )
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function lgd_ref_get_group_definitions() {
    return array(
        'band_width' => array(
            'label'          => 'Band Width',
            'subtitle'       => '1.60mm is the standard band width. Upgrade for a stronger look.',
            'selection_type' => 'single',
            'options'        => array(
                'band_1_60mm' => array( 'label' => '1.60mm', 'note' => 'Classic & delicate — Best with 0.50–1.00 ct' ),
                'band_1_80mm' => array( 'label' => '1.80mm', 'note' => 'Slim & refined — Best with 0.60–1.25 ct' ),
                'band_2_00mm' => array( 'label' => '2.00mm', 'note' => 'Elegant & balanced — Best with 0.75–1.50 ct' ),
                'band_2_20mm' => array( 'label' => '2.20mm', 'note' => 'Balanced & versatile — Best with 0.90–1.75 ct' ),
                'band_2_50mm' => array( 'label' => '2.50mm', 'note' => 'Refined & substantial — Best with 1.00–2.00 ct' ),
                'band_3_00mm' => array( 'label' => '3.00mm', 'note' => 'Strong & substantial — Best with 1.25–2.50 ct' ),
                'band_3_50mm' => array( 'label' => '3.50mm', 'note' => 'Bold & substantial — Best with 1.50–3.00+ ct' ),
            ),
        ),
        'band_style' => array(
            'label'          => 'Band Style',
            'subtitle'       => 'Choose the shank style that fits your look.',
            'selection_type' => 'single',
            'options'        => array(
                'euro_shank'      => array( 'label' => 'Euro Shank', 'note' => 'A rounded interior band for a smooth, comfortable fit.' ),
                'tapered'         => array( 'label' => 'Tapered', 'note' => 'Narrows gracefully toward the center for a refined look.' ),
                'reverse_tapered' => array( 'label' => 'Reverse Tapered', 'note' => 'Widens toward the center to showcase the stone.' ),
            ),
        ),
        'prong' => array(
            'label'          => 'Prongs',
            'subtitle'       => 'Choose the prong style for your center stone.',
            'selection_type' => 'single',
            'options'        => array(
                'pave_prongs'      => array( 'label' => 'Pave Prongs', 'note' => 'Delicate, low-profile prongs that let the stone stand out.' ),
                'six_prongs_round' => array( 'label' => '6 Prongs', 'note' => 'Extra security with a classic six-point setting.' ),
                'double_prongs'    => array( 'label' => 'Double Prongs', 'note' => 'Doubled prongs for added strength and durability.' ),
            ),
        ),
        'east_west' => array(
            'label'          => 'East-West',
            'subtitle'       => 'Turn the center stone horizontally for a unique look.',
            'selection_type' => 'single',
            'options'        => array(
                'east_west' => array( 'label' => 'East West', 'note' => 'Sets the stone horizontally for a modern, elongated look.' ),
            ),
        ),
        'two_tone' => array(
            'label'          => 'Two Tone',
            'subtitle'       => 'Turn the center stone horizontally for a unique look.',
            'selection_type' => 'single',
            'options'        => array(
                'two_tone' => array( 'label' => 'Two Tone', 'note' => 'Combines two metal colors for a striking contrast.' ),
            ),
        ),
    );
}

/**
 * ACF number field name holding an option's price for a product.
 */
function lgd_ref_price_field_name( $option_key ) {
    return 'ref_price_' . $option_key;
}

/**
 * Normalizes an option definition to ['label' => string, 'note' => string|null],
 * since an option can be written either as a plain string or as an
 * array with 'label' (+ optional 'note').
 */
function lgd_ref_normalize_option( $option_def ) {
    if ( is_array( $option_def ) ) {
        return array(
            'label' => $option_def['label'] ?? '',
            'note'  => $option_def['note'] ?? null,
        );
    }
    return array( 'label' => $option_def, 'note' => null );
}
