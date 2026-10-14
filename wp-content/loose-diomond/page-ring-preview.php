<?php

/**
 * Template Name: Ring Preview
 */

get_header();

global $ring_setting, $inv_shape, $select_diamond, $rb_setting_size, $inv_color_c;
$ls_rb_ses = $_SESSION['ls_ring_builder'];

$rb_setting_ses = $ls_rb_ses['setting'];
$rb_diamond_ses = $ls_rb_ses['diamond'];

$curreny_sym = get_woocommerce_currency_symbol();

// Check if we have both setting and diamond selected
$has_complete_ring = isset($ls_rb_ses) && !empty($ls_rb_ses) && !empty($rb_setting_ses) && !empty($rb_diamond_ses);

if ($has_complete_ring) {
    $sp_id = $rb_setting_ses['sp_id'];
    $sp_id_product = wc_get_product($sp_id);


    $sp_vid = $rb_setting_ses['sp_vid'];
    $d_pid = $rb_diamond_ses['d_pid'];
    $d_pid_product = wc_get_product($d_pid);
    // Get setting size if available
    $setting_size = '';
    if (isset($rb_setting_ses['sp_size']) && $rb_setting_ses['sp_size'] !== '') {
        $setting_size = $rb_setting_ses['sp_size'];
    }
}
?>

<!-- start banner section  -->
<div class="banner_section">
    <div class="page_heading">
        <div class="container-fluid">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo home_url().'/build-a-ring/'; ?>">Ring Building</a></li>
                    <?php if ($has_complete_ring): ?>
                        <li class="breadcrumb-item active" aria-current="page">

                            <?php 
                            echo ucwords(strtolower($sp_id_product->get_name()));  ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Build Your Ring</li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>


    <div class="container-fluid mt-4">
        <?php
        // Ring Builder Breadcrumb - Always show this
        ls_ring_builder_breadcrumb('review');
        ?>
        <div id="ring-builder-breadcrumb-root"></div>
    </div>

    <div class="container-fluid">
        <?php if ($has_complete_ring): ?>
            <?php
            // Build the images array
            $images_array = array();

            // Get diamond shape
            $ls_dshape = get_post_meta($sp_id, 's_dtype', true);
            $shape_name = array_search($ls_dshape, $inv_shape);

            // Variation image
            $rb_simg = wp_get_attachment_image_src(get_post_thumbnail_id($sp_vid), 'woocommerce_single')[0];
            if ($rb_simg != '')
                $images_array[] = '<div class="img"><img src="' . $rb_simg . '"></div>';

            // Variation gallery images
            $variation_glr_img = get_post_meta($sp_vid, 'woo_variation_gallery_images', true);
            if (!empty($variation_glr_img)) {
                foreach ($variation_glr_img as $vimgid) {
                    $vimg = wp_get_attachment_image_src($vimgid, 'woocommerce_single')[0];
                    if ($vimg != '')
                        $images_array[] = '<div class="img"><img src="' . $vimg . '"></div>';
                }
            }

            // Get diamond data from inventory
            global $wpdb;
            $ls_inventory = 'ls_inventory';
            $ls_inventory_c = 'ls_inventory_c';

            $is_id = $wpdb->get_results("SELECT * FROM $ls_inventory WHERE pid=$d_pid");
            $count_cid = count($is_id);

            if ($count_cid >= 1) {
                $inv_product = $wpdb->get_row("SELECT * FROM $ls_inventory WHERE pid=$d_pid");
            } else {
                $inv_product = $wpdb->get_row("SELECT * FROM $ls_inventory_c WHERE pid=$d_pid");
            }

            // Build diamond description
            $carat = $inv_product->carat;
            if ($carat != '') $carat = $carat . ' carat ';

            $cut_num = $inv_product->cut;
            $cut = '';
            if ($cut_num != '' && $cut_num != '0') {
                $cut = get_attrkey_val('cut', $cut_num);
                $cut = $cut . ' cut';
            }

            $color_num = $inv_product->color;
            $color = '';
            if ($count_cid >= 1) {
                if ($color_num != '') {
                    $color = get_attrkey_val('color', $color_num);
                    $color = $color . ' color, ';
                }
            } else {
                $color = array_search($color_num, $inv_color_c);
                $color = $color . ' color, ';
            }

            $clarity_num = $inv_product->clarity;
            $clarity = '';
            if ($clarity_num != '') {
                $clarity = get_attrkey_val('clarity', $clarity_num);
                $clarity = $clarity . ' clarity ';
            }

            // Get variation data
            $variation_data = new WC_Product_Variation($sp_vid);
            $var_attributes = $variation_data->get_variation_attributes();
            $variation_price = $variation_data->get_price();

            $v_reg_price = get_post_meta($sp_vid, '_regular_price', true);
            $v_price_sale = get_post_meta($sp_vid, '_sale_price', true);

            // Get diamond price
            $ls_product = wc_get_product($d_pid);
            $d_pprice = $ls_product->get_price();
            $dr_pprice = $ls_product->get_regular_price();

            // Calculate total price
            $ring_reg_price_text = '';
            if ($v_price_sale != '') {
                $ring_prdct_reg_price = $v_reg_price + $dr_pprice;
                $ring_reg_price_text = '<del>' . $curreny_sym . $ring_prdct_reg_price . '</del>';
            }

            $ring_prdct_price = $variation_price + $d_pprice;

            // Get metal and size
            $product_variation = wc_get_product($sp_vid);
            $ls_selmetal = $product_variation->get_attribute('metal');
            $ls_selsize = $product_variation->get_attribute('setting-size');

            // Build product description
            $prdct_attrdtl = $ls_selmetal . '. ' . $carat . ' ' . $shape_name . ' Cut, ' . $color . $clarity . $cut . '.';

            // Magic360 spin settings (mirror logic from woocommerce/single-product.php)
            $ls_magic360_settings = get_option('WooCommerceMagic360CoreSettings');
            $spin_icon = !empty($ls_magic360_settings['product']['selector-path']['value']) ? $ls_magic360_settings['product']['selector-path']['value'] : '';

            $magic360_meta = json_decode((get_post_meta($sp_id, '_magic360_data', true)), true);
            $has_magic360images = array();
            if (!empty($magic360_meta)) {
                $has_magic360images = $magic360_meta['images_ids'];
            }

           /* // Find midpoint
            $half = ceil(count($has_magic360images) / 2);

            // Reorder array
            $has_magic360images = array_merge(
                array_slice($has_magic360images, $half),
                array_slice($has_magic360images, 0, $half)
            );*/

           /* echo "<pre>";
            print_r($has_magic360images);
            echo "</pre>";*/

            $spin_imageurls = array();
            foreach ($has_magic360images as $spinimg) {
                $spin_imageurls[] = wp_get_attachment_url($spinimg);
            }
            $spin_images = implode(" ", $spin_imageurls);
            $has_spin = !empty($has_magic360images) && !empty($spin_imageurls) && $spin_icon;
            ?>

            <div class="product_image">
                <div class="row">
                    <div class="col-lg-7">
                        <?php if (!empty($images_array)): ?>
                            <div class="ls_rimg_glr ls_sp_glr">
                                <div class="thumbs">
                                    <?php
                                    $last_index_t = count($images_array);
                                    $i_t = 1;
                                    foreach ($images_array as $key => $value) {
                                        if ($i_t == 1 && $has_spin) {
                                            echo $value;
                                            echo '<div class="img ls_spinthumb"><img src="' . esc_url($spin_icon) . '"></div>';
                                        } else {
                                            echo $value;
                                        }
                                        $i_t++;
                                    }
                                    ?>
                                </div>
                                <div class="img_slide">
                                    <?php
                                    $i_s = 1;
                                    foreach ($images_array as $key => $value) {
                                        if ($i_s == 1) {
                                            echo $value;
                                            if ($has_spin) {
                                                ?>
                                                <div class="img ls_spinimg">
                                                    <div class="images">
                                                        <div class="spinContainer">
                                                            <a class="Magic360" href="#" data-magic360-options="selector-path:<?php echo esc_attr($spin_icon); ?>;fullscreen:false;autospin:once;rows:1;images:<?php echo esc_attr($spin_images); ?>;">
                                                                <img class="no-sirv-lazy-load" itemprop="image" src="<?php echo esc_url($spin_imageurls[0]); ?>" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            echo $value;
                                        }
                                        $i_s++;
                                    }
                                    ?>
                                    <div class="ls_slide_arrows">
                                        <button class="ls_arrow_btn ls_prev_btn" type="button" aria-label="Previous image"><i class="fa-solid fa-less-than"></i></button>
                                        <button class="ls_arrow_btn ls_next_btn" type="button" aria-label="Next image"><i class="fa-solid fa-greater-than "></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-5">
                        <div class="mt-3 mt-md-0">
                            <h4 class="product_title">
                                <?php 
echo ucwords(strtolower($sp_id_product->get_name())) 
    . ' with ' . 
    ucwords(strtolower($d_pid_product->get_name())); 
?>
                            </h4>
                            <?php
                        if(WEBSITE_TYPE=='RETAIL'){
                        ?>
                        <div class="reviwewidgetp">
                             <div class="ruk_rating_snippet" data-sku="<?php echo $sp_id_product->get_sku();?>"></div> 
                        </div>
                        <?php
                        }
                        ?>
                          <span class="diamond_description"><?php echo $prdct_attrdtl; ?></span>

                            <form class="cart ring-builder-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="ls_add_ringbuilder_to_cart">
                                <input type="hidden" name="setting_id" value="<?php echo esc_attr($sp_id); ?>">
                                <input type="hidden" name="variation_id" value="<?php echo esc_attr($sp_vid); ?>">
                                <input type="hidden" name="diamond_id" value="<?php echo esc_attr($d_pid); ?>">
                                <!-- Extend insurance price source: diamond price + ring (setting + upgrades) price -->
                                <input type="hidden" id="diamond_price" name="diamond_price" value="<?php echo esc_attr( round( (float) $d_pprice * 0.70, 2 ) ); // diamond price with 30% off ?>">
                                <input type="hidden" id="ring_price" name="ring_price" value="<?php echo esc_attr( (float) $variation_price ); ?>" data-base="<?php echo esc_attr( (float) $variation_price ); ?>">
                                <input type="hidden" name="diamond" value='<?php echo htmlspecialchars(json_encode($inv_product), ENT_QUOTES, "UTF-8"); ?>'>   
                                

                                <div class="row">
                                    <div class="col">
                                        <div class="common_select_dropdown mb_15">
                                            <label for="ring-size">Ring Size (US) <a href="<?php echo get_site_url(); ?>/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf" target="_blank" class="ring-size-guide"><i class="fa-solid fa-question"></i></a></label>
                                            <select id="ring-size" name="setting_size" class="form-select setting_size" >
                                                <option value="">- Select Ring Size -</option>
                                                <?php
                                                if (!empty($rb_setting_size)) {
                                                    foreach ($rb_setting_size as $key => $value) {
                                                        echo '<option value="' . $value . '" ' . selected($setting_size, $value, false) . '>' . $value . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <?php if ( function_exists( 'lgd_extend_pdp_offer_markup' ) ) { lgd_extend_pdp_offer_markup( $sp_id_product, array( 'ring_builder' => true ) ); } ?>

                                <?php
                                // LGD EXTRA FEATURES: "Add Extra Feature" button + upgrades
                                // modal, placed before Add To Bag as requested. Groups/options
                                // are fixed in code; renders nothing if this product ($sp_id)
                                // has no ACF upgrade prices filled in.
                                //
                                // Carries forward a selection already made at the "Select This
                                // Setting" step (single-product.php), stored in session by
                                // handle_ring_builder_setting() in functions.php, so it's
                                // reflected here rather than looking lost.
                                if ( class_exists( 'LGD_Ring_Extra_Features' ) ) {
                                    $ls_ref_carried_selection = $_SESSION['ls_ring_builder']['setting']['extra_features_raw'] ?? '';
                                    LGD_Ring_Extra_Features::render_button_and_modal( $sp_id, $ls_ref_carried_selection );
                                }
                                ?>

                                <div class="d-flex align-items-end gap-1 mb_15">
                                    <h4 class="mb-0" id="ring_total_price" data-base-price="<?php echo esc_attr( $ring_prdct_price ); ?>"><?php echo $curreny_sym . $ring_prdct_price; ?></h4>
                                    <?php if ( $ring_reg_price_text !== '' ) : ?>
                                    <del class="font_size_13 fw-600 text-light-gray-secondary" id="ring_total_reg_price" data-base-reg="<?php echo esc_attr( $ring_prdct_reg_price ); ?>" data-currency="<?php echo esc_attr( $curreny_sym ); ?>">was <span class="ring-reg-amount"><?php echo $curreny_sym . $ring_prdct_reg_price; ?></span></del>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="add_cart d-flex align-items-center gap_13 ">
                                            <input type="hidden" name="quantity" class="qty" min="1" value="1">
                                            <button type="submit" class="common_btn add_cart_btn w-100">
                                                Add To Bag
                                            </button>
                                        </div>
                                        <div class="ajax-cart-message" style="display:none; margin-top: 10px; padding: 10px; border-radius: 5px;"></div>
                                    </div>
                                </div>


                            </form>

                            <?php
                            if(WEBSITE_TYPE=='RETAIL'){

                            
                            // Combined delivery estimate for the finished ring:
                            // the setting's lead time (depends on its Style +
                            // selected Metal) plus the diamond's own lead time,
                            // added together since the setting has to be made
                            // and the diamond set into it — not run in parallel.
                            $ring_setting_delivery_days = ls_get_setting_delivery_days_by_variation( $sp_vid );
                            $ring_diamond_delivery_days = ls_get_diamond_delivery_days_by_pid( $d_pid );
                            $ring_total_delivery_days   = $ring_setting_delivery_days + $ring_diamond_delivery_days;
                            $ring_est_delivery_date     = ls_est_delivery_date( $ring_total_delivery_days );
                            ?>
                            <div class="est_delivery_date mt-3">
                                <p class="mb-0">
                                    <?php echo $ring_prdct_price > 500 ? 'Free insured' : 'Insured '; ?> shipping by <strong><?php echo esc_html( $ring_est_delivery_date ); ?></strong>
                                    <i class="fa-regular fa-circle-question ms-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Estimated delivery for your setting and diamond combined." style="cursor: pointer;"></i>
                                </p>
                                <p class="mb-0">
                                    if you order in the next 15 hours. <a href="javascript:void(0)" onclick="openZohoChat('Hi, I need this ring sooner than the estimated delivery date. Product page: ' + window.location.href)">Need it sooner?</a>
                                </p>
                            </div>
                            <?php
                            }
                            ?>

                            <div class="ls-iconcnt">
                                <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-1.svg" title=""><span class="ls-cnt">Insured Shipping Worldwide</span></div>
                                <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-2.svg" title=""><span class="ls-cnt">Easy <?php echo RETURN_DAYS;?> Day Returns</span></div>
                                <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-3.svg"><span class="ls-cnt">Manufacturer Warranty</span></div>
                                <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-4.svg"><span class="ls-cnt">Price Match*</span></div>
                                <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-5.svg"><span class="ls-cnt">Complimentary Jewelry Box &amp; Bag Wrapping</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <?php endif; ?>
    </div>
</div>

<div class="container-fluid my-5">
    <div class="ls-product-info">
        <h2 class="product_information_heading">PRODUCT INFORMATION</h2>
      <!--   <p class="product_information_desc">dummy text</p> -->
        <div class="row">
            <!-- LEFT COLUMN -->
            <?php 
                if($sp_vid != ''){ 
                    ?>
                    <div class="col-sm-4 mb-5 mb-sm-0">
                        <div id="variation-section1" class=" product_information_box ">
                          <h3 class="att-subtitle"><?php echo strtolower(get_post_meta($sp_vid, 'section1label', true)); ?></h3>
                            <?php 
                            $data =  get_post_meta( $sp_vid, 'section1data', true );
                            foreach($data as $value): 
                                echo '<p class="mb-1">'. $value .'</p>';
                            endforeach;
                            ?>
                        </div>
                    </div>

                       
                    <?php 
                } 
                if($d_pid != ''){
                    ?>
                    <div class="col-sm-4">
                        <div id="variation-section1" class=" product_information_box ">
                            <?php
                            $diamond_sku = get_post_meta( $d_pid, '_sku', true );
                            $table = 'ls_inventory';
                            $inv_record = $wpdb->get_row("SELECT * FROM ".$table." WHERE `pid` = ". $d_pid ."");
                            $certificate = $inv_record->certificate;
                            echo '<h3 class="att-subtitle">Diamond</h3>';
                            echo '<p class="mb-1">'. ls_get_post_title($d_pid) .'</p>';
                            if($diamond_sku!='')
                                echo '<p class="mb-1">SKU: '. $diamond_sku .'</p>';
                            if($certificate!='')
                                echo '<p class="mb-1">Certificate: <a href="'.$certificate.'" target="_blank">View diamond certificate</a></p>';
                            ?>  
                        </div>
                    </div>
                    <?php
                }
            ?>
             <?php if(!$is_readyToShipProduct){ ?>
                <div class="col-sm-4">
                    <div class="product_information_box light_yellow_box">
                        <h3 class="fw-semibold">Made to Order</h3>
                        <p class="mb-0">
                            We make your jewelry individually once we receive your order.
                        </p>
                    </div> 
                </div>
            <?php } ?>
            <?php
                if ( has_term('ready-to-ship', 'product_cat', $d_pid ) )
                    $is_readyToShipProduct = true;
                    if(!$is_readyToShipProduct){
                    ?>
                    <div class="col-sm-4">
                        <div  class="product_information_box ">
                            <h3 class="fw-semibold">Delivery</h3>
                            <p class="mb-1">- Express international shipping with insurance</p>
                            <p class="mb-1">- Expected delivery time: 3-4 working weeks</p>
                        </div>
                    </div>
                    <?php 
                } 
            ?>
            <div class="col-md-6 ">
                <div class="product_information_box ">
                    <h3 class="fw-semibold">Why Loose Grown Diamond?</h3>
                    <p class="mb-1">- Handcrafted</p>
                    <p class="mb-1">- Conflict Free & Eco-Friendly Diamonds</p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$faqs = get_product_category_faqs();

$product_id = $sp_id;
$terms = get_the_terms($product_id, 'product_cat');

if (!$terms || is_wp_error($terms)) {
    return;
}

$all_faqs = array();

foreach ($terms as $term) {
    $slug = $term->slug;

    if (!empty($faqs[$slug])) {
        $all_faqs = array_merge($all_faqs, $faqs[$slug]);
    }
}

if (empty($all_faqs)) {
    return;
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Frequently Asked Questions</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
        <div class="faq_list">
            <div class="faq_accordion" id="faqAccordion">

                <?php 
            
                foreach ($all_faqs as $index => $faq) : ?>

                    <div class="accordion-item">
                        <h3 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button "
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $index; ?>"
                                    aria-expanded="false"
                                    aria-controls="collapse<?php echo $index; ?>">
                                <span><?php echo esc_html($faq['question']);  ?></span>

                                 <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true ) : ?>
                                    <span class="faq-icon ">
                                        <i class="fa-brands fa-youtube"></i>
                                    </span>
                                <?php endif; ?>
                            </button>
                        </h3>

                        <div id="collapse<?php echo $index; ?>"
                            class="accordion-collapse collapse "
                            data-bs-parent="#faqAccordion">

                            <div class="accordion-body">

                                <?php if (!empty($faq['isyoutube']) && $faq['isyoutube'] === true && $faq['youtube_link']!='') : ?>

                                    <div class="ratio ratio-16x9">
                                        <iframe 
                                            src="<?php echo esc_url(str_replace('youtu.be/', 'www.youtube.com/embed/', $faq['youtube_link'])); ?>" 
                                            title="YouTube video"
                                            allowfullscreen>
                                        </iframe>
                                    </div>

                                <?php else : ?>

                                    <?php echo $faq['answer']; ?>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
            </div>
        </div>
    </div>
</div>
<?php
if(WEBSITE_TYPE=='RETAIL'){
?>
<div class="container-fluid my-5">
    <?php  echo do_shortcode( '[product_reviews_widget sku="' . $sp_id_product->get_sku() . '"]' );?>
</div>
<?php
}
?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------------------------------------------------------
     * RING BUILDER — FORM SUBMIT (Add To Bag)
     * --------------------------------------------------------- */
    const form       = document.querySelector('.ring-builder-form');
    if (!form) return;

    const messageDiv = document.querySelector('.ajax-cart-message');
    const submitBtn  = form.querySelector('button[type="submit"]');

    /* ---------------------------------------------------------
     * RING BUILDER — KEEP EXTEND OFFER IN SYNC WITH SIZE CHANGES
     * (defensive no-op today; mirrors single-product.php pattern)
     * --------------------------------------------------------- */
    const lgdRingBuilderVariationId = <?php echo esc_js( $has_complete_ring ? $sp_vid : 0 ); ?>;
    const lgdRingBuilderPriceCents  = <?php echo esc_js( $has_complete_ring ? round( $ring_prdct_price * 100 ) : 0 ); ?>;

    /* ---------------------------------------------------------
     * Helpers
     * --------------------------------------------------------- */

    // Equivalent of jQuery .fadeIn()
    function fadeIn(el, duration = 400) {
        if (!el) return;
        el.style.opacity = '0';
        el.style.display = 'block';
        el.style.transition = `opacity ${duration}ms`;
        requestAnimationFrame(() => { el.style.opacity = '1'; });
    }

    function showError(html) {
        if (!messageDiv) return;
        messageDiv.innerHTML = html;
        Object.assign(messageDiv.style, {
            backgroundColor: '#f8d7da',
            color: '#721c24',
            border: '1px solid #f5c6cb'
        });
        fadeIn(messageDiv);
    }

    function hasExtend() {
        return window.LGDExtend && typeof window.LGDExtend.setActiveProduct === 'function';
    }

    /* ---------------------------------------------------------
     * EXTEND INSURANCE PRICE = #diamond_price + #ring_price
     * #ring_price = setting variation price + confirmed upgrades
     * --------------------------------------------------------- */
    function lgdRbExtraTotal() {
        const mount = form.querySelector('.ls-ref-mount');
        if (!mount) return 0;

        // jQuery .data() auto-parsed JSON; dataset always returns a string
        let groups = {};
        const rawGroups = mount.dataset.groups;
        if (rawGroups) {
            try { groups = JSON.parse(rawGroups); } catch (e) { return 0; }
        }

        const extraInput = form.querySelector('input[name="extra_features"]');
        const raw = extraInput ? extraInput.value : '';
        if (!raw) return 0;

        let sel;
        try { sel = JSON.parse(raw); } catch (e) { return 0; }

        let total = 0;
        (sel || []).forEach(function (g) {
            const grp = groups[g.group_key];
            if (!grp || !grp.options) return;
            (g.option_keys || []).forEach(function (k) {
                if (grp.options[k]) total += parseFloat(grp.options[k].price) || 0;
            });
        });
        return total;
    }

    // Keep the "was" price in step with confirmed upgrades.
    function lgdRbSyncRegPrice(extra) {
        const reg = document.getElementById('ring_total_reg_price');
        if (!reg) return;
        const baseReg = parseFloat(reg.dataset.baseReg) || 0; // data-base-reg
        const txt = (reg.dataset.currency || '$') + (baseReg + extra).toFixed(2);
        const amt = reg.querySelector('.ring-reg-amount');
        if (amt && amt.textContent !== txt) amt.textContent = txt;
    }

    function lgdRbSyncExtendPrice() {
        const ring = document.getElementById('ring_price');
        const base = ring ? (parseFloat(ring.dataset.base) || 0) : 0;
        const extra = lgdRbExtraTotal();

        lgdRbSyncRegPrice(extra);

        const ringPrice = base + extra;
        if (ring) ring.value = ringPrice.toFixed(2);

        const diamondEl = document.getElementById('diamond_price');
        const diamondPrice = diamondEl ? (parseFloat(diamondEl.value) || 0) : 0;
        const totalCents = Math.round((ringPrice + diamondPrice) * 100);

        if (hasExtend() && lgdRingBuilderVariationId) {
            window.LGDExtend.setActiveProduct(lgdRingBuilderVariationId, totalCents);
        }
    }

    // Initial quote (after the Extend SDK + upgrades component load).
    setTimeout(lgdRbSyncExtendPrice, 1000);

    // Upgrades confirmed/removed -> re-sync insurance price.
    const lgdRbPriceEl = document.getElementById('ring_total_price');
    if (lgdRbPriceEl && window.MutationObserver) {
        new MutationObserver(function () {
            setTimeout(lgdRbSyncExtendPrice, 0);
        }).observe(lgdRbPriceEl, { childList: true, subtree: true, characterData: true });
    }

    const sizeSelect = form.querySelector('select[name="setting_size"]');
    if (sizeSelect) {
        sizeSelect.addEventListener('change', function () {
            if (hasExtend() && lgdRingBuilderVariationId) {
                // window.LGDExtend.setActiveProduct(lgdRingBuilderVariationId, lgdRingBuilderPriceCents);
            }
        });
    }

    /* ---------------------------------------------------------
     * SUBMIT
     * --------------------------------------------------------- */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Ring size validation
        if (sizeSelect && !sizeSelect.value) {
            sizeSelect.style.border = '1px solid red';
            sizeSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        const qtyInput = form.querySelector('.qty');
        const quantity = parseInt(qtyInput ? qtyInput.value : '', 10) || 1;

        // Add Extend plan first if selected, then add the ring.
        if (window.LGDExtend && typeof window.LGDExtend.addPlanIfSelected === 'function') {
            window.LGDExtend.addPlanIfSelected(quantity, submitRingBuilderForm);
        } else {
            submitRingBuilderForm();
        }
    });

    function submitRingBuilderForm() {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adding...';

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams(new FormData(form)) // same as .serialize()
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(response => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add To Bag';
            console.log('AJAX Response:', response);

            if (response.success) {
                if (response.redirect_url) {
                    setTimeout(() => { window.location.href = response.redirect_url; }, 1000);
                } else {
                    // WooCommerce listens for this via jQuery, so a native
                    // CustomEvent would NOT reach its handler.
                    if (window.jQuery) {
                        window.jQuery(document.body).trigger('wc_fragment_refresh');
                    }
                }
            } else {
                showError(response.message || 'Unable to add product to cart.');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add To Bag';
            showError('Error adding to cart. Please try again.');
            console.error('AJAX Error:', error);
        });
    }

});
</script>
<?php
get_footer();