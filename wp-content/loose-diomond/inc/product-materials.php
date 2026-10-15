<?php
/**
 * Variation "Materials" information (ported from the old akshar-plugin).
 *
 * Admin  : each variation gets a "Materials Label" and a list of
 *          "Materials Data" rows (variation meta section1label / section1data).
 * Front  : the single product page shows a "Product Information" block with
 *          the label + data of the currently selected variation.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------
 * 1. Admin fields on every variation
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_product_after_variable_attributes', 'ls_materials_variation_fields', 10, 3 );
function ls_materials_variation_fields( $loop, $variation_data, $variation ) {
    $vid   = $variation->ID;
    $label = get_post_meta( $vid, 'section1label', true );
    $rows  = get_post_meta( $vid, 'section1data', true );
    $rows  = is_array( $rows ) ? array_values( array_filter( $rows, 'strlen' ) ) : array();

    if ( empty( $rows ) ) {
        $rows = array( '' );
    }
    ?>
    <div class="ls-materials">
        <div class="ls-materials__label">
            <label>Materials Label</label>
            <input type="text" class="short" name="section1label[<?php echo esc_attr( $vid ); ?>]" value="<?php echo esc_attr( $label ); ?>">
        </div>

        <div class="ls-materials__data">
            <label>Materials Data (Section 1 Data)</label>
            <?php foreach ( $rows as $row ) : ?>
                <div class="ls-materials__row">
                    <input type="text" class="short" name="section1data[<?php echo esc_attr( $vid ); ?>][]" value="<?php echo esc_attr( $row ); ?>">
                    <button type="button" class="ls-materials__remove" aria-label="Remove row">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="button ls-materials__add" data-id="<?php echo esc_attr( $vid ); ?>">Add New Row</button>
    </div>
    <?php
}

/* ---------------------------------------------------------------------
 * 2. Save
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_save_product_variation', 'ls_materials_save_variation', 10, 2 );
function ls_materials_save_variation( $variation_id, $i ) {
    if ( isset( $_POST['section1label'][ $variation_id ] ) ) {
        update_post_meta( $variation_id, 'section1label', sanitize_text_field( wp_unslash( $_POST['section1label'][ $variation_id ] ) ) );
    }

    if ( isset( $_POST['section1data'][ $variation_id ] ) && is_array( $_POST['section1data'][ $variation_id ] ) ) {
        $rows = array_map( 'sanitize_text_field', wp_unslash( $_POST['section1data'][ $variation_id ] ) );
        update_post_meta( $variation_id, 'section1data', array_values( array_filter( $rows, 'strlen' ) ) );
    }
}

/* ---------------------------------------------------------------------
 * 3. Admin styles + add/remove row script (product edit screen only)
 * ------------------------------------------------------------------ */
add_action( 'admin_footer', 'ls_materials_admin_assets' );
function ls_materials_admin_assets() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'product' !== $screen->post_type ) {
        return;
    }
    ?>
    <style>
        .ls-materials { margin-top: 20px; }
        .ls-materials label { display: block; margin-bottom: 5px; }
        .ls-materials__label { max-width: 50%; margin-bottom: 20px; }
        .ls-materials input.short { width: 100%; }
        .ls-materials__row { display: flex; gap: 10px; margin-bottom: 10px; }
        .ls-materials__remove { padding: 0 10px; font-size: 18px; cursor: pointer; }
    </style>
    <script>
    jQuery(function ($) {
        function markChanged(el) {
            $(el).closest('.woocommerce_variation').addClass('variation-needs-update');
            $('button.cancel-variation-changes, button.save-variation-changes').prop('disabled', false);
        }

        $(document).on('click', '.ls-materials__add', function () {
            var id = $(this).data('id');
            $(this).siblings('.ls-materials__data').append(
                '<div class="ls-materials__row">' +
                    '<input type="text" class="short" name="section1data[' + id + '][]">' +
                    '<button type="button" class="ls-materials__remove" aria-label="Remove row">&times;</button>' +
                '</div>'
            );
            markChanged(this);
        });

        $(document).on('click', '.ls-materials__remove', function () {
            var holder = this;
            markChanged(holder);
            $(holder).closest('.ls-materials__row').remove();
        });

        $(document).on('input', '.ls-materials input', function () {
            markChanged(this);
        });
    });
    </script>
    <?php
}

/* ---------------------------------------------------------------------
 * 4. Front end: "Product Information" block (called from single-product.php)
 * ------------------------------------------------------------------ */
function ls_render_product_information( $product ) {
    if ( ! $product || ! $product->is_type( 'variable' ) ) {
        return;
    }

    $blocks = array();
    foreach ( $product->get_children() as $vid ) {
        $label = get_post_meta( $vid, 'section1label', true );
        $rows  = get_post_meta( $vid, 'section1data', true );
        $rows  = is_array( $rows ) ? array_filter( $rows, 'strlen' ) : array();

        if ( '' === $label && empty( $rows ) ) {
            continue;
        }
        $blocks[ $vid ] = array( 'label' => $label, 'rows' => $rows );
    }

    if ( empty( $blocks ) ) {
        return;
    }
    ?>
    <div class="container-fluid">
         <div class=" my-5 product-info">
            <h2 class="product-info__title">Product Information</h2>

            <?php foreach ( $blocks as $vid => $block ) : ?>
                <div class="product-info__variation row" data-variation-id="<?php echo esc_attr( $vid ); ?>" hidden>
                    <div class="product-info__col col-sm-6 col-lg-4">
                        <?php if ( '' !== $block['label'] ) : ?>
                            <p class="product-info__subtitle"><?php echo esc_html( $block['label'] ); ?></p>
                        <?php endif; ?>
                        <?php foreach ( $block['rows'] as $row ) : ?>
                            <p class="product-info__item">- <?php echo esc_html( $row ); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <div class="product-info__col col-sm-6 col-lg-4">
                        <p class="product-info__subtitle">Delivery</p>
                        <p class="product-info__item">- Express international shipping with insurance</p>
                        <p class="product-info__item">- Expected delivery time: 3-4 working weeks</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
   

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var form  = document.querySelector('.custom-variations-form');
        var boxes = document.querySelectorAll('.product-info__variation');
        if (!form || !boxes.length) return;

        function sync() {
            var input = form.querySelector('.variation_id');
            var id = input ? input.value : '0';
            var any = false;
            boxes.forEach(function (box) {
                var show = box.getAttribute('data-variation-id') === id;
                box.hidden = !show;
                any = any || show;
            });
            var wrap = document.querySelector('.product-info');
            if (wrap) wrap.classList.toggle('is-empty', !any);
        }

        // the variation script sets .variation_id after each attribute change
        form.querySelectorAll('.attribute-select').forEach(function (sel) {
            sel.addEventListener('change', function () { setTimeout(sync, 0); });
        });
        sync();
        setTimeout(sync, 300);
    });
    </script>
    <?php
}
