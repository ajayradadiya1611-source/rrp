<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Products tagged "sale" render with the same page structure as the
// dedicated diamond detail page (/diamond/{sku}/) — same markup, same
// shared function (ls_render_diamond_detail_page(), in
// inc/diamond-detail-render.php) — instead of the normal WooCommerce
// single-product template below. header/footer are loaded from here
// (single-product.php), not from inside the shared function — see the
// $render_header_footer=false argument below.
//
// NOTE: global $product isn't reliably a WC_Product object yet this
// early — WooCommerce normally sets it up inside the Loop below
// (wc_setup_product_data()). Fetch the product from the queried post
// id directly instead of trusting the global at this point.
$ls_sale_pid     = get_queried_object_id();
$ls_sale_product = $ls_sale_pid ? wc_get_product( $ls_sale_pid ) : null;
$ls_sale_diamond = null;

if ( $ls_sale_product && has_term( 'sale', 'product_tag', $ls_sale_pid ) ) {
	$ls_sale_diamond = function_exists( '_diamond_lookup_by_sku' )
		? _diamond_lookup_by_sku( $ls_sale_product->get_sku() )
		: null;
}

if ( $ls_sale_diamond ) {
	$ls_sale_is_colored = ! empty( $ls_sale_diamond->is_colored );

	// Sets nocache headers + $GLOBALS['diamond_detail'] (read by the
	// wp_head SEO hook) — must run before get_header(), since wp_head()
	// fires during get_header().
	ls_prepare_diamond_detail_head( $ls_sale_diamond, $ls_sale_is_colored );

	get_header('shop');
	ls_render_diamond_detail_page( $ls_sale_diamond, $ls_sale_is_colored, false, true );
	get_footer('shop');
	return; // fully rendered above — skip the rest of this template
}
// No "sale" tag, or no matching inventory row for this SKU (e.g. a
// non-diamond product tagged "sale" by mistake) — normal template below.

get_header('shop');
global $product,$oc_common;
$pid = $product->get_id();

// Does any variation have a YouTube link?
$has_video = false;
$parent_video_link = trim( (string) get_post_meta( $pid, 'oc_video_link', true ) );

if ( $product->is_type('variable') ) {
    foreach ( $product->get_children() as $child_id ) {
        if ( trim( (string) get_post_meta( $child_id, 'oc_video_link', true ) ) !== '' ) {
            $has_video = true;
            break;
        }
    }
    // fallback: if no variation has a link, check parent
    if ( ! $has_video && $parent_video_link !== '' ) {
        $has_video = true;
    }
} else {
    // simple product
    if ( $parent_video_link !== '' ) {
        $has_video = true;
    }
}

$is_melee_diamond = has_term( 'melee-diamond', 'product_tag', $pid );
$c_ctype = get_post_meta($product->id, 'c_type', true);
$image_white_black = get_field('image_white_black',$pid);
$image_diamond_carat_size_guide = get_field('image_diamond_carat_size_guide',$pid);
$ct_badge = get_field('ct_badge',$pid);

$ls_ringsize_arr = ls_get_ringsize_options();

$magic360_meta = json_decode((get_post_meta( $pid, '_magic360_data', true )), true);

$has_magic360images = array();

if(!empty($magic360_meta)){
	$has_magic360images = $magic360_meta['images_ids'];	
}

/*// Find midpoint
$half = ceil(count($has_magic360images) / 2);

// Reorder array
$has_magic360images = array_merge(
    array_slice($has_magic360images, $half),
    array_slice($has_magic360images, 0, $half)
);
*/
$spin_imageurls = array();

foreach($has_magic360images as $spinimg){
	$spin_imageurls[] = wp_get_attachment_url($spinimg);
}
//$spin_imageurls = array_reverse($spin_imageurls);
$spin_images = implode(" ", $spin_imageurls);


?>


<!-- start banner section  -->
<div class="banner_section">
	<div class="page_heading mb_30">
		<div class="container-fluid">
			<nav aria-label="breadcrumb">
				<?php 
					if ( has_term( 'ready-to-ship', 'product_cat', $pid ) && wp_is_mobile() ) {

					    $categories = get_the_terms( $pid, 'product_cat' );

					    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {

					        foreach ( $categories as $category ) {

					            // Get parent category
					            if ( $category->parent ) {
					                $parent = get_term( $category->parent, 'product_cat' );

					                if ( ! is_wp_error( $parent ) && $parent->slug === 'ready-to-ship' ) {
					                    ?>
					                    <ol class="breadcrumb mb-0">
					                        <li class="breadcrumb-item">
					                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
					                                < <?php echo esc_html( $category->name ); ?>
					                            </a>
					                        </li>
					                    </ol>
					                    <?php
					                    break;
					                }
					            }
					        }
					    }

					} else { 

					    woocommerce_breadcrumb( array( 
					        'delimiter'   => '', 
					        'wrap_before' => '<ol class="breadcrumb mb-0">', 
					        'wrap_after'  => '</ol>', 
					        'before'      => '<li class="breadcrumb-item">', 
					        'after'       => '</li>', 
					        'home'        => 'Home', 
					    ) ); 

					}
					?>
			</nav>
		</div>
	</div>

	<div class="container-fluid">
		<div class="product_image">
			<div class="row">
				<div class="col-lg-7">
					<div class="img_preview" style="position: relative;">
					<?php
					// Build images array: featured image first, then gallery
					$sp_images_array = array();

					$image_id = $product->get_image_id();
					if ($image_id) {
						$main_img_src = wp_get_attachment_image_src($image_id, 'woocommerce_single')[0];
						if ($main_img_src) $sp_images_array[] = $main_img_src;
					}

					$gallery_ids = $product->get_gallery_image_ids();
					foreach ($gallery_ids as $gid) {
						$gimg = wp_get_attachment_image_src($gid, 'woocommerce_single')[0];
						if ($gimg) $sp_images_array[] = $gimg;
					}

					// Magic360 spin icon
					$ls_magic360_settings = get_option('WooCommerceMagic360CoreSettings');
					$spin_icon = !empty($ls_magic360_settings['product']['selector-path']['value']) ? $ls_magic360_settings['product']['selector-path']['value'] : '';

					// Does this product have a 360 spin?
					$has_spin = !empty($has_magic360images) && !empty($spin_imageurls) && $spin_icon;

					?>

					<?php if (!empty($sp_images_array)): ?>
					<div class="ls_rimg_glr ls_sp_glr">
						<div class="thumbs">
							<?php
							// Order: image[0] → spin (if any) → image[1] → image[2] → ...
							foreach ($sp_images_array as $idx => $src):
								// Output first image thumb
								if ($idx === 0): ?>
									<div class="img active">
										<img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
									</div>
									<?php // Inject spin thumb right after first image
									if ($has_spin): ?>
										<div class="img ls_spinthumb">
											<img src="<?php echo esc_url($spin_icon); ?>" alt="360 spin">
										</div>
									<?php endif; ?>
									<?php if ( $has_video ) : ?>
									<div class="img oc_video_link_thumb" >
										<img class="ls_yt_thumb_img" src="<?php echo get_template_directory_uri(); ?>/aj_custom/diamond/video-play.svg" alt="Video thumbnail">
									</div>
								<?php endif; ?>
								<?php else: ?>
									<div class="img">
										<img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
							<?php
							if ( !empty($image_white_black) && !empty($image_white_black['url']) ) {
								?>
								<div class="img image_white_black_thumb">
									<img src="<?php echo esc_url($spin_icon); ?>" alt="360 spin">
								</div>
								<?php
							}
							if ( !empty($image_diamond_carat_size_guide) && !empty($image_diamond_carat_size_guide['url']) ) {
								?>
								<div class="img image_diamond_carat_size_guide_thumb">
									<img src="<?php echo esc_url($spin_icon); ?>" alt="360 spin">
								</div>
								<?php
							}
							?>
							
						</div>

						<div class="img_slide" style="position:relative;">
							<?php
							// Order mirrors thumbs: image[0] (visible) → spin → image[1] → image[2] → ...
							foreach ($sp_images_array as $idx => $src):
								if ($idx === 0): ?>
									<div class="img"<?php echo ! empty( $ct_badge ) ? ' data-ct-badge="' . esc_attr( $ct_badge ) . '"' : ''; ?>>
										<img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
									</div>
									<?php // Inject spin slide right after first image slide
									if ($has_spin): ?>
										<div class="img ls_spinimg" >
											<div class="images">
												<div class="spinContainer">
													<a class="Magic360" href="#" data-magic360-options="selector-path:/wp-content/plugins/mod_woocommerce_magic360/magic360-woocommerce/core/360icon.png;columns:<?php echo count($spin_imageurls);?>;autospin:once;hint:false;rows:1;images:<?php echo esc_attr($spin_images); ?>">
														<img class="no-sirv-lazy-load" itemprop="image" src="<?php echo esc_url($spin_imageurls[0]); ?>" />
													</a>
												</div>
											</div>
										</div>
									<?php endif; ?>
									<?php if ( $has_video ) : ?>
									<div class="img oc_video_link_slide" style="display:none;">
									    <div class="oc_video_link_wrap" style="position:relative;width:100%;height:100%;">
									        <span class="ls_video_loader spinner-border spinner-border-sm" style="position:absolute;top:50%;left:50%;margin:-.5rem 0 0 -.5rem;display:none;"></span>
									        <video class="ls_product_video" src="" controls playsinline preload="none"
									               style="width:100%;height:100%;object-fit:contain;opacity:0;transition:opacity .3s ease;"></video>
									    </div>
									</div>
									<?php endif; ?>
								<?php else: ?>
									<div class="img">
										<img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
									</div>
								<?php endif; ?>
							<?php endforeach;



							if ( !empty($image_white_black) && !empty($image_white_black['url']) ) {
								?>
								<div class="img image_white_black_slide">
						
									<div class="rtos-stage">
									  <div class="rtos-canvas-wrap">
									    <img class="rtos-img-dark" src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>body_ring_dark.jpg">
									    <img class="rtos-img-white" src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>body_ring_light.jpg">
									    <img class="rtos-img-ring" src="<?php echo $image_white_black['url'];?>">
									  </div>

									  <div class="rtos-slider-wrap">
									    <span class="rtos-tone-label">Light</span>

									    <div class="rtos-v-track" id="rtos-track">
									      <div class="rtos-v-track-bg"></div>
									      <div class="rtos-v-thumb" id="rtos-thumb"></div>
									    </div>

									    <span class="rtos-tone-label">Dark</span>
									  </div>
									</div>

								</div>
								<?php
							}
							if ( !empty($image_diamond_carat_size_guide) && !empty($image_diamond_carat_size_guide['url']) ) {
								?>
								<div class="img image_diamond_carat_size_guide_slide">
									

									<div class="dcsg-container"
                                 crt="<?php echo $diamond->carat; ?>"
                                 data-bg="<?php echo esc_url( get_template_directory_uri().'/aj_custom/single-product/hand-with-setting.png' ); ?>"
                                 data-ring="<?php echo esc_url( $image_diamond_carat_size_guide['url'] ); ?>"></div>

									

								</div>
								<?php
							}
							?>
							
<div class="top-single-icon-left">
	<?php

	$ready_to_ship_categories = [
            'ready-to-ship-wedding-band',
            'ready-to-ship-diamond-earrings',
            'ready-to-ship-pendants',
            'ready-to-ship-bracelets',
            'necklaces',
        ];

        $ajdelivery = has_term( $ready_to_ship_categories, 'product_cat', $product_id ) ? 1 : 0;
        if($ajdelivery==true){

       
?>
	<div class="ready-to-ship-icon-badge" data-bs-toggle="tooltip"
	        data-bs-placement="top"
	        data-bs-original-title="Shipped in 2 business days">
	    <img
	        class="ready-to-ship-icon"
	        src="<?php echo esc_url( get_template_directory_uri() . '/aj_custom/category/ready-to-ship-blue.svg' ); ?>"
	        
	        alt="Ready to Ship"
	    />
	    <span class=""
	        >
	        Ready to Ship
	    </span>
	</div>
	<?php
}
	?>
</div>
							<div class="top-single-icon">
								<button class="wish-btn btn p-0" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-pressed="false">
									<i class="fa-heart fa-regular fa-lg"></i>
								</button>
							</div>
							<div class="ls_slide_arrows">
							    <button class="ls_arrow_btn ls_prev_btn" type="button" aria-label="Previous image"><i class="fa-solid fa-less-than"></i></button>
                                        <button class="ls_arrow_btn ls_next_btn" type="button" aria-label="Next image"><i class="fa-solid fa-greater-than "></i></button>
							</div>
						</div>
					</div>

					<?php else: ?>
						<?php
						$image_id = $product->get_image_id();
						echo wp_get_attachment_image($image_id, 'full', false, array('alt' => $product->get_name()));
						?>
						
						<div class="top-single-icon">
							<button class="wish-btn btn p-0" data-product-id="<?php echo esc_attr($product->get_id()); ?>"  aria-pressed="false">
								<i class="fa-heart fa-regular fa-lg"></i>
							</button>
						</div>
					<?php endif; ?>

					</div>

					<div class="product-help-box text-center">Need help? <a href="javascript:void(0);" 
   onclick="$zoho.salesiq.floatwindow.visible('show'); return false;" 
   class="chat-link">
   Chat with us
</a> or call<a href="tel:+<?php echo SUPPORT_MOBILE?>"> +<?php echo SUPPORT_MOBILE?></a></div>
				</div>

				<div class="col-lg-5">
					<div class="product_information">
						<h1 class="fw-500 text_black_secondary"><?php echo esc_html($product->get_name()); ?></h1>

						<?php
						
						?>
						<div class="reviwewidgetp">
							<?php  echo do_shortcode( '[rating_snippet sku="' . $product->get_sku() . '"]' );?>
						</div>
						<?php
						
						?>

						<?php
						//if (has_term( array('custom'), 'product_cat', $product_id ) ){
						?>
						

						
						<?php
							$custom_fields = get_field( 'custom_order_fields', $product->get_id() );
							$custom_fields_html = '';
							$customer_notes = '';
							if ( ! empty( $custom_fields ) ) {
							    $custom_fields_html = '<span class="diamond_description">';

							    foreach ( $custom_fields as $index => $field ) {
							        if ( $index > 0 ) {
							            $custom_fields_html .= ' | ';
							            $customer_notes .= ' | ';
							        }

							        $custom_fields_html .= esc_html( $field['custom_order_label'] ) . ' : ';
							        $custom_fields_html .= esc_html( $field['custom_order_value'] );
							        $customer_notes .= esc_html( $field['custom_order_label'] ) . ' : ';
							        $customer_notes .= esc_html( $field['custom_order_value'] );
							    }

							    $custom_fields_html .= '</span>';
							}

							echo $custom_fields_html;
							$short_description = $product->get_short_description();

if ( ! empty( $short_description ) ) {
    echo '<span class="diamond_description">' . $short_description . '</span>';
}
							?>
					

						

						<?php if ($product->is_type('variable')) :
							$attributes = $product->get_variation_attributes();
							$available_variations = $product->get_available_variations();
							foreach ($available_variations as $key => $variation) {
							    $vid = $variation['variation_id'];
							    $available_variations[$key]['section1label'] = get_post_meta($vid, 'section1label', true);
							    $available_variations[$key]['section1data']  = get_post_meta($vid, 'section1data', true);
							    $available_variations[$key]['oc_video_link']        = get_post_meta($vid, 'oc_video_link', true);
								$available_variations[$key]['parent_oc_video_link'] = $parent_video_link; // ADD THIS
							    
							    // woo_variation_gallery_images stores a serialized array: a:4:{i:0;i:147388;i:1;i:147386;...}
							    $wvg_raw  = get_post_meta($vid, 'woo_variation_gallery_images', true);
							    $wvg_ids  = !empty($wvg_raw) ? (array) maybe_unserialize($wvg_raw) : array();
							    $wvg_imgs = array();
							    foreach ($wvg_ids as $img_id) {
							        $img_id = (int) $img_id;
							        if (!$img_id) continue;
							        $full  = wp_get_attachment_image_src($img_id, 'woocommerce_single');
							        $thumb = wp_get_attachment_image_src($img_id, 'woocommerce_thumbnail');
							        if ($full) {
							            $wvg_imgs[] = array(
							                'src'    => $full[0],
							                'thumb'  => $thumb ? $thumb[0] : $full[0],
							                'srcset' => wp_get_attachment_image_srcset($img_id, 'woocommerce_single') ?: '',
							                'alt'    => get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: '',
							            );
							        }
							    }
							    $available_variations[$key]['wvg_images'] = $wvg_imgs;
							}
							$selected_attributes = $product->get_default_attributes();
						?>
							<!-- FIX: Variations are now passed via an inline JS variable instead of
							     an HTML data attribute, to avoid htmlspecialchars() breaking the JSON. -->
							<script type="text/javascript">
								var productVariations = <?php echo wp_json_encode($available_variations); ?>;
							</script>

							<form class="variations_form cart custom-variations-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								
									<?php foreach ($attributes as $attr_name => $options) : ?>
											<div class="common_select_dropdown mb_15">
												<label for="<?php echo esc_attr(sanitize_title($attr_name)); ?>">
													<?php echo wc_attribute_label($attr_name); ?>
												</label>
												<select
													id="<?php echo esc_attr(sanitize_title($attr_name)); ?>"
													name="attribute_<?php echo esc_attr(sanitize_title($attr_name)); ?>"
													class="form-select attribute-select"
													data-attribute_name="attribute_<?php echo esc_attr(sanitize_title($attr_name)); ?>">
													<!-- <option value=""><?php _e('Choose an option', 'woocommerce'); ?></option> -->
													<?php
														if (taxonomy_exists($attr_name)) {
														    $terms = wc_get_product_terms($product->get_id(), $attr_name, [
														        'fields'  => 'all',
														        'orderby' => 'menu_order'
														    ]);
														    foreach ($terms as $term) {
														        if (!in_array($term->slug, $options)) {
														            continue;
														        }
														        $selected = selected($selected_attributes[sanitize_title($attr_name)] ?? '', $term->slug, false);
														        ?>
														        <option value="<?php echo esc_attr($term->slug); ?>" <?php echo $selected; ?>>
														            <?php echo esc_html($term->name); ?>
														        </option>
														        <?php
														    }
														} else {
														    foreach ($options as $option) {
														        $selected = selected($selected_attributes[sanitize_title($attr_name)] ?? '', $option, false);
														        ?>
														        <option value="<?php echo esc_attr($option); ?>" <?php echo $selected; ?>>
														            <?php echo esc_html($option); ?>
														        </option>
														        <?php
														    }
														}
													?>
												</select>
											</div>
										
									<?php endforeach; ?>
								

								<div class="row">
									<div class="col">
										<?php 
											// Ready-to-ship products (category 'ready-to-ship' or any of its
											// sub-categories) are already made in a fixed size, so no ring size dropdown.
											$ls_is_ready_to_ship = has_term( 'ready-to-ship', 'product_cat', $product_id );
											if ( ! $ls_is_ready_to_ship ) {
												$ls_rts_parent = get_term_by( 'slug', 'ready-to-ship', 'product_cat' );
												if ( $ls_rts_parent ) {
													$ls_rts_children = get_term_children( $ls_rts_parent->term_id, 'product_cat' );
													if ( ! is_wp_error( $ls_rts_children ) && ! empty( $ls_rts_children ) ) {
														$ls_is_ready_to_ship = has_term( $ls_rts_children, 'product_cat', $product_id );
													}
												}
											}
											if ( ! $ls_is_ready_to_ship && has_term( array('diamond-rings','anniversary-rings','eternity-rings', 'stackable-rings'), 'product_cat', $product_id ) ){   
												$setting_size = '';
												?>
													<div class="rb_settingsize common_select_dropdown mb_15">
														<label for="ring-size">Ring Size (US) <a href="<?php echo get_site_url(); ?>/wp-content/uploads/2021/08/LGD-RING-SIZER.pdf" target="_blank" class="ring-size-guide"><i class="fa-solid fa-question"></i></a></label>
														<select name="ringsize" class="ringsize form-select">
															<option value="">- Ring Size (US)* -</option>
															<?php foreach($ls_ringsize_arr as $key => $value){
																echo '<option value="'.$key.'" '.selected($setting_size, $value).' >'.$value.'</option>';
															} ?>
														</select>
													</div>
												<?php
											}
										?>
									</div>
								</div>

								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
								<input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
								<input type="hidden" name="variation_id" class="variation_id" value="0">
								<?php
								if ( class_exists( 'LGD_Ring_Extra_Features' ) ) {
									LGD_Ring_Extra_Features::render_button_and_modal( $product->get_id() );
								}
								?>
								<div class="price-display" style="margin-bottom: 15px; font-size: 16px; font-weight: 500;"></div>

								<?php if ( function_exists( 'lgd_extend_pdp_offer_markup' ) ) { lgd_extend_pdp_offer_markup( $product ); } ?>

										<div class="add_cart d-flex align-items-center gap_13">
												<?php
												// LGD EXTRA FEATURES: real "Add To Bag" branch — adds
												// straight to cart, so this is captured directly by
												// capture_cart_item_data() via $_POST['form_data']. This
												// template never calls woocommerce_before_add_to_cart_button,
												// so the button/modal is rendered directly here instead,
												// inside this <form>, so its hidden "extra_features" input
												// rides along in $form.serialize().
												
												?>
												<input type="hidden" name="quantity" class="qty" min="1" value="1">
												<button type="button" class="common_btn md_common_btn  w-100" id="ajax-add-to-cart" disabled>
													<span>Add To Bag</span>
													<span class="single_p_loader ls_stng_loder spinner-border spinner-border-sm d-none"></span>
												</button>
																					</div>
										<div class="ajax-cart-message" style="display:none; margin-top: 10px; padding: 10px; border-radius: 5px;"></div>
								
							</form>

							<?php
								$is_ring_product = has_term(
									array('diamond-rings','anniversary-rings','eternity-rings','stackable-rings'),
									'product_cat',
									$product_id
								);
							?>

							

						<?php else: ?>
							<!-- Simple product Add to Cart -->
							<form class="cart" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" enctype="multipart/form-data">
								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
							
										<div class="price-display oc_simple_product" style="margin-bottom: 15px; font-size: 16px; font-weight: 500;">
											<?php if ( $product->is_on_sale() ) {
											    $regular = wc_price( $product->get_regular_price() );
											    $sale    = wc_price( $product->get_sale_price() );

											    echo '<span class="price">
											            <del>' . $regular . '</del>
											            <ins>' . $sale . '</ins>
											          </span>';
											} else {
											    echo '<span class="price">' . wc_price( $product->get_price() ) . '</span>';
											}?>
										</div>

										<?php if ( function_exists( 'lgd_extend_pdp_offer_markup' ) ) { lgd_extend_pdp_offer_markup( $product ); } ?>

										<?php
										// LGD EXTRA FEATURES: same reasoning as the variable-product
										// form above — rendered directly here since this template
										// never calls woocommerce_before_add_to_cart_button.
										if ( class_exists( 'LGD_Ring_Extra_Features' ) ) {
											LGD_Ring_Extra_Features::render_button_and_modal( $product->get_id() );
										}
										?>

										<div class="add_cart d-flex align-items-center gap_13 ">
											<input type="hidden" name="quantity" min="1" value="1">
											<input type="hidden" name="custom_note" value="<?php echo esc_attr(wp_strip_all_tags($customer_notes)); ?>">
											<button type="button" id="lgd-simple-add-to-cart" class="common_btn md_common_btn  w-100">
												<span>Add To Bag</span>
													<span class="single_p_loader ls_stng_loder spinner-border spinner-border-sm d-none"></span>
											</button>
										</div>
									
							</form>
						<?php endif; ?>
						<div class="klarna_price nrml mt-2">
							<svg version="1.1" viewBox="0 0 45 25" height="25" width="45" xmlns="http://www.w3.org/2000/svg">
								<title>Klarna Payment Badge</title>
								<g fill="none" fill-rule="evenodd">
									<g transform="translate(-310 -37)" fill-rule="nonzero">
										<g transform="translate(310 37)">
											<rect x="5.6843e-14" width="45" height="25" rx="4.321" fill="#FFB3C7"/>
											<g transform="translate(4.4136 8.4)" fill="#0A0B09">
												<path d="m36.38 6.2463c-0.58875 0-1.066 0.48158-1.066 1.0757 0 0.594 0.47725 1.0757 1.066 1.0757 0.58874 0 1.0661-0.48167 1.0661-1.0757 0-0.59416-0.47734-1.0757-1.0661-1.0757zm-3.5073-0.83166c0-0.81338-0.68897-1.4726-1.5389-1.4726s-1.539 0.65925-1.539 1.4726c0 0.81339 0.68898 1.4728 1.539 1.4728s1.5389-0.65941 1.5389-1.4728zm0.0057148-2.8622h1.6984v5.7242h-1.6984v-0.36584c-0.47982 0.3302-1.059 0.52431-1.6837 0.52431-1.6531 0-2.9933-1.3523-2.9933-3.0205s1.3402-3.0204 2.9933-3.0204c0.6247 0 1.2039 0.1941 1.6837 0.5244v-0.36619zm-13.592 0.74562v-0.74554h-1.7389v5.7241h1.7428v-2.6725c0-0.90167 0.96849-1.3863 1.6405-1.3863 0.0068818 0 0.013306 6.6771e-4 0.020188 7.527e-4v-1.6656c-0.68973 0-1.3241 0.298-1.6646 0.7452zm-4.3316 2.1166c0-0.81338-0.68905-1.4726-1.539-1.4726-0.84991 0-1.539 0.65925-1.539 1.4726 0 0.81339 0.68905 1.4728 1.539 1.4728 0.84998 0 1.539-0.65941 1.539-1.4728zm0.0056186-2.8622h1.6985v5.7242h-1.6985v-0.36584c-0.47982 0.3302-1.059 0.52431-1.6836 0.52431-1.6532 0-2.9934-1.3523-2.9934-3.0205s1.3402-3.0204 2.9934-3.0204c0.62464 0 1.2038 0.1941 1.6836 0.5244v-0.36619zm10.223-0.15396c-0.67846 0-1.3206 0.21255-1.7499 0.79895v-0.64465h-1.6911v5.7239h1.7119v-3.0081c0-0.87046 0.57847-1.2967 1.275-1.2967 0.74646 0 1.1756 0.44996 1.1756 1.2849v3.0199h1.6964v-3.6401c0-1.3321-1.0496-2.238-2.4179-2.238zm-17.374 5.8782h1.7777v-8.2751h-1.7777v8.2751zm-7.8091 0.0022581h1.8824v-8.2789h-1.8824v8.2789zm6.584-8.2789c0 1.7923-0.69219 3.4596-1.9256 4.6989l2.602 3.5803h-2.325l-2.8278-3.891 0.72981-0.55152c1.2103-0.91484 1.9045-2.3132 1.9045-3.8367h1.8421z"/>
											</g>
										</g>
									</g>
								</g>
							</svg>
							
							<span class="text_black_secondary font_size_13">
								4 interest-free payments. 
								<a href="<?php echo get_site_url(); ?>/klarna/" target="_blank">
									<u>Learn More</u>
								</a>
							</span>
							
						</div>
						<?php 
						
						?>
						<div class="ls-iconcnt">
					       <!--  <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-1.svg" title=""><span class="ls-cnt">Insured Shipping Worldwide</span></div>
						     -->
					        <?php 
							if($is_melee_diamond ==false){
							?>
						    <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-2.svg" title=""><span class="ls-cnt">Easy <?php echo RETURN_DAYS;?> Day Returns</span></div>
						    <?php 
							}
						    ?>
						    <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-3.svg"><span class="ls-cnt">Manufacturer Warranty</span></div>
						    <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-4.svg"><span class="ls-cnt">Price Match*</span></div>
						    <div><img src="<?php echo get_template_directory_uri().'/aj_custom/single-product/'; ?>Frame-5.svg"><span class="ls-cnt">Complimentary Jewelry Box &amp; Bag Wrapping</span></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<?php
// For a variable product, the correct "active product" for Extend is the
// default/pre-selected variation — not the parent. The attribute-select
// change handler further down already resolves this correctly on load
// (it fires a 'change' trigger immediately in document.ready), but this
// block runs 1000ms later via setTimeout, so without this it would
// clobber that correct value with the wrong parent id/price. Resolve the
// same default variation here (mirrors the JS findMatchingVariation()
// logic) so this call is correct too, regardless of timing.
$ls_active_product_id    = $product->get_id();
$ls_active_product_price = (float) $product->get_price();

if ( $product->is_type( 'variable' ) && ! empty( $available_variations ) && ! empty( $selected_attributes ) ) {
	foreach ( $available_variations as $ls_variation_row ) {
		$ls_is_match = true;
		foreach ( $selected_attributes as $ls_attr_name => $ls_attr_value ) {
			$ls_variation_attr = $ls_variation_row['attributes'][ 'attribute_' . $ls_attr_name ] ?? '';
			// Empty variation attribute value = "Any" for that attribute, same
			// as the JS matching logic — only mismatch on a non-empty value.
			if ( $ls_variation_attr !== '' && strtolower( (string) $ls_variation_attr ) !== strtolower( (string) $ls_attr_value ) ) {
				$ls_is_match = false;
				break;
			}
		}
		if ( $ls_is_match ) {
			$ls_active_product_id    = $ls_variation_row['variation_id'];
			$ls_active_product_price = (float) $ls_variation_row['display_price'];
			break;
		}
	}
}
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var productId = <?php echo (int) $ls_active_product_id; ?>;
    var priceCents = <?php echo (int) round( $ls_active_product_price * 100 ); ?>;
    var tries = 0;

    (function waitForExtend() {
        if (window.LGDExtend && typeof window.LGDExtend.setActiveProduct === 'function') {
            window.LGDExtend.setActiveProduct(productId, priceCents);
        } else if (tries++ < 50) {
            setTimeout(waitForExtend, 100);
        }
    })();
});
function appendParam(params, key, value) {
    if (value === null || value === undefined) {
      params.append(key, '');
    } else if (Array.isArray(value)) {
      value.forEach(function (v, i) {
        var subKey = (typeof v === 'object' && v !== null) ? key + '[' + i + ']' : key + '[]';
        appendParam(params, subKey, v);
      });
    } else if (typeof value === 'object') {
      Object.keys(value).forEach(function (k) {
        appendParam(params, key + '[' + k + ']', value[k]);
      });
    } else if (typeof value === 'boolean') {
      params.append(key, value ? '1' : '');
    } else {
      params.append(key, value);
    }
  }
</script>
<script>
var lsProductSlug = <?php echo wp_json_encode( $product->get_slug() ); ?>;
</script>
<?php if ($product->is_type('variable')) { ?>
<script type="text/javascript">

document.addEventListener('DOMContentLoaded', function () {

	var form = document.querySelector('.custom-variations-form');
	if (!form) return;

	var variations       = (typeof productVariations !== 'undefined') ? productVariations : [];
	var variationIdInput = form.querySelector('.variation_id');
	var addToCartBtn     = document.getElementById('ajax-add-to-cart');
	var priceDisplays    = document.querySelectorAll('.price-display');
	var messageDiv       = document.querySelector('.ajax-cart-message');
	var attrSelects      = form.querySelectorAll('.attribute-select');

	/* ─── Small helpers ─────────────────────────────────────── */

	function escHtml(str) {
		var d = document.createElement('div');
		d.textContent = (str == null ? '' : String(str));
		return d.innerHTML;
	}

	function setPriceHtml(html) {
		priceDisplays.forEach(function (el) { el.innerHTML = html; });
	}

	function fadeIn(el, duration) {
		if (!el) return;
		duration = duration || 200;
		el.style.opacity = 0;
		el.style.display = '';
		if (getComputedStyle(el).display === 'none') el.style.display = 'block';
		el.style.transition = 'opacity ' + duration + 'ms';
		requestAnimationFrame(function () { el.style.opacity = 1; });
	}

	function showError(msg) {
		if (!messageDiv) return;
		messageDiv.innerHTML = msg;
		messageDiv.style.backgroundColor = '#f8d7da';
		messageDiv.style.color = '#721c24';
		messageDiv.style.border = '1px solid #f5c6cb';
		fadeIn(messageDiv, 400);
	}

	function setCartDisabled(disabled) {
		if (addToCartBtn) addToCartBtn.disabled = disabled;
	}

	// Fire a jQuery event when jQuery is present (WooCommerce + the Extra
	// Features component listen through jQuery, so native events won't reach them).
	function jqTrigger(target, name, args) {
		if (window.jQuery) window.jQuery(target).trigger(name, args || []);
	}

	/* ─── LGD EXTRA FEATURES: confirmed upgrade total ───────── */

	function lsRefExtraTotal() {
		var mount = form.querySelector('.ls-ref-mount');
		if (!mount) return 0;

		var groups = mount.getAttribute('data-groups') || '{}';
		try { groups = JSON.parse(groups); } catch (e) { return 0; }

		var input = form.querySelector('input[name="extra_features"]');
		var raw = input ? input.value : '';
		if (!raw) return 0;

		var sel;
		try { sel = JSON.parse(raw); } catch (e) { return 0; }

		var total = 0;
		(sel || []).forEach(function (g) {
			var grp = groups[g.group_key];
			if (!grp || !grp.options) return;
			(g.option_keys || []).forEach(function (k) {
				if (grp.options[k]) total += parseFloat(grp.options[k].price) || 0;
			});
		});
		return total;
	}

	/* ─── Price rendering ───────────────────────────────────── */

	var lsLastVariation = null;

	function lsRenderVariationPrice(variation) {
		if (!variation.display_price) return;

		var extra     = lsRefExtraTotal();
		var showPrice = (parseFloat(variation.display_price) + extra).toFixed(2);
		var showReg   = (parseFloat(variation.display_regular_price) + extra).toFixed(2);
		var priceHtml;

		if (parseFloat(variation.display_price) < parseFloat(variation.display_regular_price)) {
			priceHtml =
				'<span class="price" data-ls-own="1">' +
					'<del><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' + showReg + '</bdi></span></del>' +
					'<ins><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' + showPrice + '</bdi></span></ins>' +
				'</span>';
		} else {
			priceHtml =
				'<span class="price" data-ls-own="1">' +
					'<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' + showPrice + '</bdi></span>' +
				'</span>';
		}

		<?php if ( $is_melee_diamond == true ) { ?>
		priceHtml += '<span class="oc-single-per-diamond"> / Per Diamond</span>';
		<?php } ?>

		setPriceHtml(priceHtml);
	}

	// Re-render when the Extra Features React component overwrites the price
	if (priceDisplays.length && window.MutationObserver) {
		var lsPriceObserver = new MutationObserver(function () {
			if (!lsLastVariation) return;
			var ours = Array.prototype.some.call(priceDisplays, function (el) {
				return el.querySelector('[data-ls-own]');
			});
			if (ours) return;
			lsRenderVariationPrice(lsLastVariation);
		});
		priceDisplays.forEach(function (el) {
			lsPriceObserver.observe(el, { childList: true, subtree: true, characterData: true });
		});
	}

	/* ─── Video slide: load/stop based on visibility ────────── */

	(function () {
		var slide = document.querySelector('.ls_sp_glr .oc_video_link_slide');
		if (!slide || slide.__lsYtObserved) return;
		slide.__lsYtObserved = true;

		var video  = slide.querySelector('video.ls_product_video');
		var loader = slide.querySelector('.ls_video_loader');
		if (!video) return;

		function hideVideo() {
			video.style.opacity = '0';
			if (loader) loader.style.display = video.getAttribute('src') ? '' : 'none';
		}
		function showVideo() {
			video.style.opacity = '1';
			if (loader) loader.style.display = 'none';
		}

		video.addEventListener('canplaythrough', function () {
			if (video.getAttribute('src')) showVideo();
		});
		video.addEventListener('emptied',   hideVideo);
		video.addEventListener('loadstart', hideVideo);
		video.addEventListener('error', function () {
			if (loader) loader.style.display = 'none';
		});

		new MutationObserver(function () {
			var visible = slide.classList.contains('ls-slide-visible');
			var link    = slide.getAttribute('data-video-link');
			if (visible && link) {
				if (video.getAttribute('src') !== link) {
					video.style.opacity = '0';
					video.setAttribute('preload', 'auto');
					video.setAttribute('src', link);
					if (loader) loader.style.display = '';
					video.load();
					video.play().catch(function () {});
				}
			} else {
				video.pause();
				video.removeAttribute('src');
				video.load();
			}
		}).observe(slide, { attributes: true, attributeFilter: ['class'] });
	})();

	/* ─── Variation matching ────────────────────────────────── */

	function clearVariation() {
		if (variationIdInput) variationIdInput.value = '0';
		setCartDisabled(true);
		lsLastVariation = null;
		setPriceHtml('');
	}

	function findMatchingVariation() {
		var attributes = {};
		var allSelected = true;

		attrSelects.forEach(function (sel) {
			var name  = sel.getAttribute('data-attribute_name');
			var value = sel.value;
			if (value === '') allSelected = false;
			attributes[name] = value;
		});

		if (!allSelected) {
			clearVariation();
			return null;
		}

		for (var i = 0; i < variations.length; i++) {
			var variation = variations[i];
			var match = true;
			for (var attr in attributes) {
				var vAttr = variation.attributes[attr] ? variation.attributes[attr].toString().toLowerCase().trim() : '';
				var aAttr = attributes[attr] ? attributes[attr].toString().toLowerCase().trim() : '';
				if (vAttr !== '' && vAttr !== aAttr) { match = false; break; }
			}
			if (match) return variation;
		}
		return null;
	}

	/* ─── Section 1 (features list + read more) ─────────────── */

	function renderSection1(variation) {
		var section = document.getElementById('variation-section1');
		if (!section) return;
		section.innerHTML = '';
		if (!variation.section1label) return;

		var html = '<h3 class="att-subtitle">' + escHtml(variation.section1label.toLowerCase()) + '</h3>';

		if (Array.isArray(variation.section1data)) {
			var visibleCount = 4;
			var items = variation.section1data.filter(function (v) { return v !== ''; });

			items.slice(0, visibleCount).forEach(function (item) {
				html += '<p class="ftd">- ' + escHtml(item) + '</p>';
			});

			if (items.length > visibleCount) {
				html += '<div class="section1-more" style="display:none;">';
				items.slice(visibleCount).forEach(function (item) {
					html += '<p class="ftd">- ' + escHtml(item) + '</p>';
				});
				html += '</div>';
				html += '<button type="button" class="btn btn-link p-0 section1-readmore">Read more</button>';
			}
		}

		section.innerHTML = html;

		var btn  = section.querySelector('.section1-readmore');
		var more = section.querySelector('.section1-more');
		if (btn && more) {
			btn.addEventListener('click', function () {
				var open = more.style.display !== 'none';
				more.style.display = open ? 'none' : '';
				btn.textContent = open ? 'Read more' : 'Read less';
			});
		}
	}

	/* ─── Gallery rebuild ───────────────────────────────────── */

	var glr        = document.querySelector('.ls_sp_glr');
	var thumbsWrap = glr ? glr.querySelector('.thumbs') : null;
	var slideWrap  = glr ? glr.querySelector('.img_slide') : null;

	function removeImgsExcept(wrap, keepSelector) {
		wrap.querySelectorAll('.img').forEach(function (el) {
			if (!el.matches(keepSelector)) el.remove();
		});
	}

	// One delegated thumb click handler (replaces the per-rebuild .off/.on)
	if (thumbsWrap && slideWrap) {
		thumbsWrap.addEventListener('click', function (e) {
			var thumb = e.target.closest('.img');
			if (!thumb || !thumbsWrap.contains(thumb)) return;

			var thumbs = Array.from(thumbsWrap.querySelectorAll('.img'));
			var slides = Array.from(slideWrap.querySelectorAll('.img'));
			var idx = thumbs.indexOf(thumb);

			thumbs.forEach(function (t) { t.classList.remove('active'); });
			thumb.classList.add('active');
			slides.forEach(function (s) { s.style.display = 'none'; });
			if (slides[idx]) fadeIn(slides[idx], 200);
		});
	}

	function rebuildGallery(variation) {
		if (!glr || !thumbsWrap || !slideWrap) return;

		var hasSpin = <?php echo $has_spin ? 'true' : 'false'; ?>;
		var images  = [];

		if (variation.image && variation.image.src) {
			images.push({
				src:    variation.image.src,
				thumb:  variation.image.thumb_src || variation.image.src,
				srcset: variation.image.srcset || '',
				alt:    variation.image.alt || ''
			});
		}

		if (Array.isArray(variation.wvg_images)) {
			variation.wvg_images.forEach(function (img) {
				images.push({
					src:    img.src,
					thumb:  img.thumb || img.src,
					srcset: img.srcset || '',
					alt:    img.alt || ''
				});
			});
		}

		if (!images.length) return;

		/* Thumbs */
		removeImgsExcept(thumbsWrap, '.ls_spinthumb, .image_white_black_thumb, .image_diamond_carat_size_guide_thumb, .oc_video_link_thumb');
		if (!hasSpin) removeImgsExcept(thumbsWrap, '.image_white_black_thumb, .image_diamond_carat_size_guide, .oc_video_link_thumb');

		var thumbAnchor = hasSpin
			? thumbsWrap.querySelector('.ls_spinthumb')
			: thumbsWrap.querySelector('.oc_video_link_thumb');

		images.forEach(function (img, idx) {
			var thumb = document.createElement('div');
			thumb.className = 'img' + (idx === 0 ? ' active' : '');
			var tImg = document.createElement('img');
			tImg.src = img.thumb;
			tImg.alt = img.alt;
			thumb.appendChild(tImg);

			if (idx === 0) {
				if (thumbAnchor) thumbAnchor.before(thumb);
				else thumbsWrap.prepend(thumb);
			} else {
				thumbsWrap.appendChild(thumb);
			}
		});

		/* Slides */
		removeImgsExcept(slideWrap, '.ls_spinimg, .image_white_black_slide, .image_diamond_carat_size_guide_slide, .oc_video_link_slide');
		if (!hasSpin) removeImgsExcept(slideWrap, '.image_white_black_slide, .image_diamond_carat_size_guide_slide, .oc_video_link_slide');

		var slideAnchor = hasSpin
			? slideWrap.querySelector('.ls_spinimg')
			: slideWrap.querySelector('.oc_video_link_slide');
		// Insert new slides before the icons (left express icon comes first in
		// the markup), so the icons always stay at the bottom of the slide list.
		var topIcon = slideWrap.querySelector('.top-single-icon-left') || slideWrap.querySelector('.top-single-icon');
		var ctBadge = <?php echo wp_json_encode( (string) $ct_badge ); ?>;

		images.forEach(function (img, idx) {
			var slide = document.createElement('div');
			slide.className = 'img';
			slide.style.display = idx === 0 ? '' : 'none';

			if (idx === 0 && ctBadge) {
				var badge = document.createElement('span');
				badge.className = 'ct_badge';
				badge.innerHTML = ctBadge;
				slide.appendChild(badge);
			}

			var sImg = document.createElement('img');
			sImg.src = img.src;
			if (img.srcset) sImg.setAttribute('srcset', img.srcset);
			sImg.alt = img.alt;
			slide.appendChild(sImg);

			var anchor = (idx === 0 && slideAnchor) ? slideAnchor : topIcon;
			if (anchor) anchor.before(slide);
			else slideWrap.appendChild(slide);
		});

		/* Reset active state */
		var newThumbs = thumbsWrap.querySelectorAll('.img');
		var newSlides = slideWrap.querySelectorAll('.img');
		newThumbs.forEach(function (t, i) { t.classList.toggle('active', i === 0); });
		newSlides.forEach(function (s, i) { s.style.display = i === 0 ? '' : 'none'; });

		/* Video: data only (loading handled by the observer above) */
		var ytThumb = thumbsWrap.querySelector('.oc_video_link_thumb');
		var ytSlide = slideWrap.querySelector('.oc_video_link_slide');
		var ytVideo = ytSlide ? ytSlide.querySelector('video.ls_product_video') : null;

		var videoLink = (variation.oc_video_link || '').toString().trim() ||
		                (variation.parent_oc_video_link || '').toString().trim();

		if (ytVideo) {
			ytVideo.pause();
			ytVideo.removeAttribute('src');
			ytVideo.load();
		}

		if (ytSlide) {
			if (videoLink) ytSlide.setAttribute('data-video-link', videoLink);
			else ytSlide.removeAttribute('data-video-link');
		}
		if (ytThumb) ytThumb.style.display = videoLink ? '' : 'none';

		/* Re-init arrows via ls-gallery.js (expects a jQuery object) */
		function initNav() {
			if (window.lsInitGalleryNav) {
				window.lsInitGalleryNav(window.jQuery ? window.jQuery(glr) : glr);
				return true;
			}
			return false;
		}
		try {
			if (!initNav()) setTimeout(initNav, 100);
		} catch (e) {
			console.error('lsInitGalleryNav error', e);
		}
	}

	/* ─── Attribute change ──────────────────────────────────── */

	function onAttributeChange() {
		var variation = findMatchingVariation();

		if (!variation) {
			clearVariation();
			return;
		}

		if (variationIdInput) variationIdInput.value = variation.variation_id;
		setCartDisabled(false);

		renderSection1(variation);

		lsLastVariation = variation;
		lsRenderVariationPrice(variation);

		rebuildGallery(variation);
		if (messageDiv) messageDiv.style.display = 'none';

		// Extra Features component listens for WooCommerce's found_variation (jQuery event)
		jqTrigger(form, 'found_variation', [variation]);

		if (window.LGDExtend && variation.variation_id) {
			window.LGDExtend.setActiveProduct(variation.variation_id, Math.round(variation.display_price * 100));
		}

			}

	attrSelects.forEach(function (sel) {
		sel.addEventListener('change', onAttributeChange);
	});

	// Resolve pre-selected attributes on load
	if (attrSelects.length) onAttributeChange();

	/* ─── Ring size ─────────────────────────────────────────── */

	var ringsize = document.querySelector('.ringsize');
	if (ringsize) {
		ringsize.addEventListener('change', function () {
			if (ringsize.value !== '') ringsize.classList.remove('border-danger');
		});
	}

	/* ─── Add to cart ───────────────────────────────────────── */

	if (addToCartBtn) {
		addToCartBtn.addEventListener('click', function (e) {
			e.preventDefault();

			var isRingProduct = <?php echo wp_json_encode( (bool) $is_ring_product ); ?>;
			if (isRingProduct && ringsize && ringsize.value === '') {
				ringsize.classList.add('border-danger');
				ringsize.scrollIntoView({ behavior: 'smooth', block: 'center' });
				return;
			}

			var variationId = variationIdInput ? variationIdInput.value : '';
			if (!variationId || variationId === '0') {
				showError('Please select all options.');
				return;
			}

			var qtyInput = form.querySelector('[name="quantity"]');
			var lgdQuantity = parseInt((qtyInput && qtyInput.value) || 1, 10);
			var loaders = document.querySelectorAll('.ls_stng_loder');

			function setLoading(on) {
				setCartDisabled(on);
				loaders.forEach(function (l) { l.classList.toggle('d-none', !on); });
			}

			function lgdProceedAddToCart() {
				var body = new URLSearchParams();
				body.set('action', 'custom_ajax_add_to_cart');
				body.set('form_data', new URLSearchParams(new FormData(form)).toString());

				setLoading(true);

				fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
					method: 'POST',
					credentials: 'same-origin',
					body: body
				})
				.then(function (res) {
					if (!res.ok) throw new Error('HTTP ' + res.status);
					return res.json();
				})
				.then(function (response) {
					setLoading(false);

					if (response.redirect_url && response.success) {
						window.location.href = response.redirect_url;
						return;
					}

					if (response.success) {
						if (response.fragments) {
							// WooCommerce listens via jQuery
							jqTrigger(document.body, 'wc_fragment_refresh');
							jqTrigger(document.body, 'added_to_cart', [
								response.fragments, '',
								window.jQuery ? window.jQuery(addToCartBtn) : addToCartBtn
							]);
						}
					} else {
						showError(response.message || 'Unable to add product to cart.');
					}
				})
				.catch(function (err) {
					setLoading(false);
					showError('Error adding to cart. Please try again.');
					console.error('AJAX Error:', err);
				});
			}

			if (window.LGDExtend) {
				window.LGDExtend.addPlanIfSelected(lgdQuantity, lgdProceedAddToCart);
			} else {
				lgdProceedAddToCart();
			}
		});
	}
});
</script>
<?php } ?>

<?php
if($c_ctype==4){
	single_product_product_info();
	echo '<div class="light_gray_background ">';
	single_product_what_customer_say();
	echo '</div>';
	if (! has_term( array('custom'), 'product_cat', $product_id ) ){   
	?>
	<div class="oc-related-product  pt-5">
	   <h2 class="text-center fw-600  pt-2 single_rt_product">Related Product</h2>
	   <div id="inventory-listing-root"></div>
	</div>
	<?php
	}
	single_product_faq();
}else{
   single_product_buy_direct_us();
   single_product_experience_history();
   echo '<div class="light_gray_background ">';
   single_product_what_customer_say();
   echo '</div>';
   single_product_shop_by_shape();
   if (! has_term( array('custom'), 'product_cat', $product_id ) ){
   ?>
	<div class="oc-related-product   pt-5">
	   <h2 class="text-center fw-600  pt-2 single_rt_product">Related Product</h2>
	   <div id="inventory-listing-root"></div>
	</div>
	<?php
	}
   single_product_faq();
}


?>
<?php

?>
<div class="container-fluid my-5">
	<?php  echo do_shortcode( '[product_reviews_widget sku="' . $product->get_sku() . '"]' );?>
</div>
<?php

?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {

    var params = new URLSearchParams(window.location.search);

    params.forEach(function (value, key) {
        document.querySelectorAll('[name="' + CSS.escape(key) + '"]').forEach(function (el) {
            if (el.tagName === 'SELECT') {
                // only set if that option exists, so a bad URL value doesn't blank the select
                var hasOption = Array.prototype.some.call(el.options, function (o) {
                    return o.value === value;
                });
                if (hasOption) el.value = value;
            } else {
                el.value = value;
            }
        });
    });

    setTimeout(function () {
        document.querySelectorAll('.variations_form select').forEach(function (sel) {
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }, 200);
});
</script>

<script type="text/javascript">
	jQuery(function ($) {
		// Simple-product Add to Bag: offer the Extend plan (if selected) before
		// submitting the normal WooCommerce add-to-cart form. Falls back to a
		// plain form submit automatically if the protection plugin is inactive.
		$('#lgd-simple-add-to-cart').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $form = $btn.closest('form.cart');
			var quantity = parseInt($form.find('[name="quantity"]').val() || 1, 10);

			function submitForm() {
				$form.trigger('submit');
			}

			if (window.LGDExtend) {
				window.LGDExtend.addPlanIfSelected(quantity, submitForm);
			} else {
				submitForm();
			}
		});
	});
</script>
<?php get_footer('shop'); ?>
