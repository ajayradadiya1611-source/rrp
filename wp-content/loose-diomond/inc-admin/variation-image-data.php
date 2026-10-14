<?php
// ── Enqueue WP Media Library on product edit pages ────────────────────────────
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;
    global $post;
    if ( ! $post || get_post_type( $post ) !== 'product' ) return;
    wp_enqueue_media();
} );


// ── Render picker ─────────────────────────────────────────────────────────────
add_action( 'woocommerce_variation_options_pricing', function ( $loop, $variation_data, $variation ) {
    $vid     = $variation->ID;
    $wvg_raw = get_post_meta( $vid, 'woo_variation_gallery_images', true );
    /*echo "sss";
    print_r( $wvg_raw);*/
    $wvg_ids = ! empty( $wvg_raw )
               ? array_filter( array_map( 'intval', (array) maybe_unserialize( $wvg_raw ) ) )
               : [];
    ?>
    <div class="form-row form-row-full wvg-vis-wrap"
         data-loop="<?php echo esc_attr( $loop ); ?>"
         style="margin-top:12px;padding-top:10px;border-top:1px solid #eee;">

        <label style="font-weight:600;display:block;margin-bottom:8px;">
            <?php esc_html_e( 'Variation Gallery Images', 'wvg-vid' ); ?>
        </label>

        <!-- Stores comma-separated IDs in current order -->
        <input type="hidden"
               name="woo_variation_gallery_images[<?php echo esc_attr( $loop ); ?>]"
               class="wvg-vis-ids"
               value="<?php echo esc_attr( implode( ',', $wvg_ids ) ); ?>" />

        <!-- Thumbnails -->
        <div class="wvg-vis-picker" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
            <?php foreach ( array_values( $wvg_ids ) as $img_id ) :
                $thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                if ( ! $thumb_url ) continue;
                ?>
                <div class="wvg-vis-thumb"
                     data-id="<?php echo esc_attr( $img_id ); ?>"
                     style="
                        position:relative;
                        width:64px;height:64px;
                        border-radius:4px;
                        overflow:hidden;
                        background:#f0f0f0;
                     ">
                    <img src="<?php echo esc_url( $thumb_url ); ?>"
                         style="width:100%;height:100%;object-fit:cover;display:block;" />
                    <!-- Remove button -->
                    <button type="button"
                            class="wvg-vis-remove"
                            title="<?php esc_attr_e( 'Remove', 'wvg-vid' ); ?>"
                            style="
                               position:absolute;top:2px;right:2px;
                               background:#cc1818;color:#fff;
                               border:none;border-radius:50%;
                               width:16px;height:16px;
                               font-size:10px;line-height:16px;text-align:center;
                               cursor:pointer;padding:0;
                            ">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Images button -->
        <button type="button"
                class="button wvg-vis-add-btn">
            <?php esc_html_e( '+ Add Images', 'wvg-vid' ); ?>
        </button>

    </div>
    <?php
}, 10, 3 );


// ── Admin styles + JS ─────────────────────────────────────────────────────────
add_action( 'admin_footer', function () {
    global $post;
    if ( ! $post || get_post_type( $post ) !== 'product' ) return;
    ?>
    <style>
        .wvg-vis-thumb img { display:block; }
        .wvg-vis-remove { opacity:0; transition:opacity .15s; }
        .wvg-vis-thumb:hover .wvg-vis-remove { opacity:1; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        function setIds(input, ids) {
            input.value = ids.join(',');
            // bubbles so WooCommerce's jQuery handlers still mark the variation as changed
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function getIds(input) {
            return (input.value || '').split(',').filter(Boolean);
        }

        function buildThumb(id, url) {
            var thumb = document.createElement('div');
            thumb.className = 'wvg-vis-thumb';
            thumb.dataset.id = id;
            thumb.style.cssText = 'position:relative;width:64px;height:64px;border-radius:4px;overflow:hidden;background:#f0f0f0;';

            var img = document.createElement('img');
            img.src = url;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'wvg-vis-remove';
            btn.title = 'Remove';
            btn.innerHTML = '&times;';
            btn.style.cssText = 'position:absolute;top:2px;right:2px;background:#cc1818;color:#fff;border:none;border-radius:50%;width:16px;height:16px;font-size:10px;line-height:16px;text-align:center;cursor:pointer;padding:0;opacity:0;transition:opacity .15s;';

            thumb.appendChild(img);
            thumb.appendChild(btn);
            return thumb;
        }

        document.addEventListener('click', function (e) {

            // ── Remove image ─────────────────────────────────────
            var removeBtn = e.target.closest('.wvg-vis-remove');
            if (removeBtn) {
                e.preventDefault();
                var thumb = removeBtn.closest('.wvg-vis-thumb');
                var wrap  = thumb && thumb.closest('.wvg-vis-wrap');
                var input = wrap && wrap.querySelector('.wvg-vis-ids');
                if (!thumb || !input) return;

                var removeId = String(thumb.dataset.id);
                setIds(input, getIds(input).filter(function (id) { return id !== removeId; }));
                thumb.remove();
                return;
            }

            // ── Add images via Media Library ─────────────────────
            var addBtn = e.target.closest('.wvg-vis-add-btn');
            if (addBtn) {
                e.preventDefault();
                if (!window.wp || !wp.media) return;

                var wrap = addBtn.closest('.wvg-vis-wrap');
                if (!wrap) return;

                var frame = wp.media({
                    title:    'Add Gallery Images',
                    button:   { text: 'Add to Gallery' },
                    multiple: true,
                    library:  { type: 'image' }
                });

                frame.on('select', function () {
                    var attachments = frame.state().get('selection').toJSON();
                    var picker = wrap.querySelector('.wvg-vis-picker');
                    var input  = wrap.querySelector('.wvg-vis-ids');
                    if (!picker || !input) return;

                    var ids = getIds(input);

                    attachments.forEach(function (att) {
                        var id = String(att.id);
                        if (ids.indexOf(id) !== -1) return; // skip duplicates
                        ids.push(id);

                        var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                        picker.appendChild(buildThumb(id, url));
                    });

                    setIds(input, ids);
                });

                frame.open();
            }
        });
    });
    </script>
    <?php
} );


// ── Save back to woo_variation_gallery_images ─────────────────────────────────
add_action( 'woocommerce_save_product_variation', function ( $variation_id, $loop ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( ! isset( $_POST['woo_variation_gallery_images'][ $loop ] ) ) return;

    $raw_ids = sanitize_text_field( wp_unslash( $_POST['woo_variation_gallery_images'][ $loop ] ) );
    $ids     = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );

    if ( ! empty( $ids ) ) {
        update_post_meta( $variation_id, 'woo_variation_gallery_images', $ids );
    } else {
        delete_post_meta( $variation_id, 'woo_variation_gallery_images' );
    }
}, 10, 2 );