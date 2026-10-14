<?php
/**
 * LGD Custom Shortcodes
 *
 * Includes:
 *  - [blog_button_group] ... [/blog_button_group]
 *  - [blog_page_btn title="" url=""]
 *  - [ls_products_byid id="" btn="true|false" btn_text="" btn_link=""]
 *  - [blog_page_inventory shape="" carat="" cut="" color="" clarity=""]
 *  - [su_youtube_advanced url="" width="" height="" title="" autoplay="" mute="" loop=""]
 *  - [lgd_instagram_post url="" width="" hidecaption="true|false"]
 */

/* ------------------------------------------------------------------ */
/*  Button Group Wrapper                                              */
/* ------------------------------------------------------------------ */

add_shortcode( 'blog_button_group', 'blog_button_group_func' );
function blog_button_group_func( $atts, $content = null ) {
	return '<div class="blog_btn_group">' . do_shortcode( $content ) . '</div>';
}

/* ------------------------------------------------------------------ */
/*  Single Buy/Link Button                                            */
/* ------------------------------------------------------------------ */

add_shortcode( 'blog_page_btn', 'blog_page_btn_func' );
function blog_page_btn_func( $atts ) {

	$atts = shortcode_atts(
		array(
			'title' => 'Buy Now',
			'url'   => '',
		),
		$atts,
		'blog_page_btn'
	);

	ob_start();
	?>
	<div class="blog_page_btn">
		<a class="ls_bbtn" href="<?php echo esc_url( $atts['url'] ); ?>" target="_blank" rel="noopener">
			<?php echo esc_html( $atts['title'] ); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  Products by ID Grid                                               */
/* ------------------------------------------------------------------ */

add_shortcode( 'ls_products_byid', 'ls_products_byid_func' );
function ls_products_byid_func( $atts ) {

	$atts = shortcode_atts(
		array(
			'id'       => '',
			'btn'      => 'false',
			'btn_text' => 'See More',
			'btn_link' => '#',
		),
		$atts,
		'ls_products_byid'
	);

	if ( empty( $atts['id'] ) ) {
		return '';
	}

	$ids = array_filter( array_map( 'intval', explode( ',', $atts['id'] ) ) );

	if ( empty( $ids ) ) {
		return '';
	}

	$products = wc_get_products(
		array(
			'include' => $ids,
			'status'  => 'publish',
			'limit'   => -1,
			'orderby' => 'include',
		)
	);

	if ( empty( $products ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="filter_result_flex">
		<div class="ls_products_flex">
			<?php foreach ( $products as $product ) :
				$product_id  = $product->get_id();
				$product_url = get_permalink( $product_id );
				$title       = $product->get_name();

				$thumb_html = get_the_post_thumbnail(
					$product_id,
					'medium',
					array(
						'class'   => 'ls_product_thumb',
						'loading' => 'lazy',
					)
				);

				if ( empty( $thumb_html ) ) {
					$thumb_html = wc_placeholder_img( 'medium' );
				}
				?>
				<div class="ls_product_tile" data-id="<?php echo esc_attr( $product_id ); ?>">
					<a href="<?php echo esc_url( $product_url ); ?>" target="_blank" class="ls_product_tile_img">
						<?php echo $thumb_html; ?>
					</a>
					<div class="ls_product_tile_title">
						<a href="<?php echo esc_url( $product_url ); ?>" target="_blank">
							<?php echo esc_html( $title ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $atts['btn'] === 'true' ) : ?>
			<div class="ls_more_btn_wrap">
				<a href="<?php echo esc_url( $atts['btn_link'] ); ?>" class="ls_more_btn aj-black-btn-color aj-common-btn">
					<?php echo esc_html( $atts['btn_text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  Inventory Filter Table                                            */
/* ------------------------------------------------------------------ */

add_shortcode( 'blog_page_inventory', 'blog_page_inventory' );
function blog_page_inventory( $atts ) {

	global $wpdb, $inv_cut, $inv_color, $inv_clarity;

	$atts = shortcode_atts(
		array(
			'pid'     => '',
			'shape'   => '',
			'carat'   => '',
			'cut'     => '',
			'color'   => '',
			'clarity' => '',
		),
		$atts,
		'blog_page_inventory'
	);

	$where = '';

	/* ---- Shape ---- */
	$shapes = array_filter( explode( ',', $atts['shape'] ) );

	if ( ! empty( $shapes ) ) {
		$shape_clauses = array();
		foreach ( $shapes as $value ) {
			$shape_clauses[] = "shape='" . esc_sql( $value ) . "'";
		}
		$where .= '( ' . implode( ' OR ', $shape_clauses ) . ' )';
	}

	/* ---- Carat ---- */
	$carat_range = explode( ',', $atts['carat'] );

	if ( ! empty( $atts['carat'] ) ) {
		if ( $carat_range[0] == 0.0 ) {
			$carat_range[0] = 0.1;
		}

		if ( $carat_range[0] == $carat_range[1] ) {
			$where .= " AND ( carat = '" . esc_sql( $carat_range[0] ) . "' )";
		} else {
			$where .= " AND ( carat BETWEEN '" . esc_sql( $carat_range[0] ) . "' AND '" . esc_sql( $carat_range[1] ) . "' )";
		}
	}

	/* ---- Cut ---- */
	$cut_range = explode( ',', $atts['cut'] );

	if ( ! empty( $atts['cut'] ) ) {
		$cut_from = $cut_range[0];
		$cut_to   = $cut_range[1];

		if ( $cut_from == 0 ) {
			$cut_from = 1;
		}

		if ( $cut_from == $cut_to ) {
			$where .= " AND ( cut = '" . esc_sql( $cut_from ) . "' )";
		} else {
			$where .= " AND ( cut >= '" . esc_sql( $cut_from ) . "' AND cut <= '" . esc_sql( $cut_to ) . "' )";
		}
	}

	/* ---- Color ---- */
	$color_range = explode( ',', $atts['color'] );

	if ( ! empty( $atts['color'] ) ) {
		$color_from = $color_range[0];
		$color_to   = $color_range[1];

		if ( $color_from == 0 ) {
			$color_from = 1;
		}

		if ( $color_from == $color_to ) {
			$where .= " AND ( color = '" . esc_sql( $color_from ) . "' )";
		} else {
			$where .= " AND ( color >= '" . esc_sql( $color_from ) . "' AND color <= '" . esc_sql( $color_to ) . "' )";
		}
	}

	/* ---- Clarity ---- */
	$clarity_range = explode( ',', $atts['clarity'] );

	if ( ! empty( $atts['clarity'] ) ) {
		$clarity_from = $clarity_range[0];
		$clarity_to   = $clarity_range[1];

		if ( $clarity_from == 0 ) {
			$clarity_from = 1;
		}

		if ( $clarity_from == $clarity_to ) {
			$where .= " AND ( clarity = '" . esc_sql( $clarity_from ) . "' )";
		} else {
			$where .= " AND ( clarity >= '" . esc_sql( $clarity_from ) . "' AND clarity <= '" . esc_sql( $clarity_to ) . "' )";
		}
	}

	/* ---- Exclude purchased/sold items ---- */
	$where .= ' AND ( status = 0 )';

	$sql    = "SELECT * FROM ls_inventory WHERE $where ORDER BY sale DESC LIMIT 5";
	$result = $wpdb->get_results( $sql );

	ob_start();
	?>
	<div class="filter_result">
		<table class="prdctlist">
			<thead>
				<tr>
					<th sortid="shape">Shape</th>
					<th sortid="carat">Carat</th>
					<th sortid="cut">Cut</th>
					<th sortid="color">Color</th>
					<th sortid="clarity">Clarity</th>
					<th sortid="price">Price</th>
					<th sortid="video"></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $result as $row ) :
					$shape   = $row->shape;
					$cut     = array_search( $row->cut, $inv_cut );
					$color   = array_search( $row->color, $inv_color );
					$clarity = array_search( $row->clarity, $inv_clarity );
					?>
					<tr data-id="284365">
						<td class="shape">
							<img src="/wp-content/themes/royal-child/ls-inventory/assets/shape-diamond/<?php echo esc_attr( str_replace( ' ', '-', strtolower( $shape ) ) ); ?>.svg">
							<?php echo esc_html( strtolower( $shape ) ); ?>
						</td>
						<td><?php echo esc_html( $row->carat ); ?></td>
						<td><?php echo esc_html( $cut ); ?></td>
						<td><?php echo esc_html( $color ); ?></td>
						<td><?php echo esc_html( $clarity ); ?></td>
						<td>
							<del class="ls_oprice">$<?php echo esc_html( $row->org_price ); ?></del>
							$<?php echo esc_html( $row->price ); ?>
						</td>
						<td>
							<a href="/lab-diamonds/?sku=<?php echo esc_attr( $row->sku ); ?>" target="_blank" class="lsbtn">View</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  YouTube Video Embed                                                */
/* ------------------------------------------------------------------ */

add_shortcode( 'su_youtube_advanced', 'custom_youtube_video_shortcode' );
function custom_youtube_video_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'url'      => '',
			'width'    => '100%',
			'height'   => '500',
			'title'    => '',
			'autoplay' => '1',
			'mute'     => '1',
			'loop'     => '1',
		),
		$atts,
		'su_youtube_advanced'
	);

	if ( empty( $atts['url'] ) ) {
		return '';
	}

	preg_match(
		'/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
		$atts['url'],
		$matches
	);

	$video_id = $matches[1] ?? '';

	if ( ! $video_id ) {
		return '';
	}

	$src = 'https://www.youtube.com/embed/' . $video_id .
		'?autoplay=' . $atts['autoplay'] .
		'&mute=' . $atts['mute'] .
		'&loop=' . $atts['loop'] .
		'&playlist=' . $video_id .
		'&rel=0&modestbranding=1&playsinline=1';

	ob_start();
	?>
	<div class="custom-youtube-video">
		<iframe
			width="100%"
			height="<?php echo esc_attr( $atts['height'] ); ?>"
			src="<?php echo esc_url( $src ); ?>"
			title="<?php echo esc_attr( $atts['title'] ); ?>"
			frameborder="0"
			allow="autoplay; encrypted-media"
			allowfullscreen>
		</iframe>
	</div>
	<?php
	return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  Instagram Post Embed                                              */
/* ------------------------------------------------------------------ */

add_shortcode( 'lgd_instagram_post', 'lgd_instagram_embed_shortcode' );
function lgd_instagram_embed_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'url'         => '',
			'width'       => '540',
			'hidecaption' => 'false',
		),
		$atts,
		'lgd_instagram_post'
	);

	$url = trim( $atts['url'] );

	if ( empty( $url ) || strpos( $url, 'instagram.com' ) === false ) {
		return '<p><em>Instagram Embed: please provide a valid Instagram post URL.</em></p>';
	}

	// Instagram expects a trailing slash on the permalink.
	$url = rtrim( $url, '/' ) . '/';

	// Unique wrapper id so multiple embeds on one page don't collide.
	static $lgd_ig_instance = 0;
	$lgd_ig_instance++;
	$wrapper_id = 'lgd-ig-embed-' . $lgd_ig_instance;

	$width       = intval( $atts['width'] );
	$hidecaption = ( strtolower( $atts['hidecaption'] ) === 'true' );

	ob_start();
	?>
	<div id="<?php echo esc_attr( $wrapper_id ); ?>" class="lgd-instagram-embed-wrap" style="display:flex;justify-content:center;max-width:100%;">
		<blockquote
			class="instagram-media"
			<?php echo $hidecaption ? '' : 'data-instgrm-captioned'; ?>
			data-instgrm-permalink="<?php echo esc_url( $url . '?utm_source=ig_embed&utm_campaign=loaded' ); ?>"
			data-instgrm-version="14"
			style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin:1px; max-width:<?php echo esc_attr( $width ); ?>px; min-width:326px; padding:0; width:99.375%; width:calc(100% - 2px);">
		</blockquote>
	</div>
	<script>
		(function () {
			// Load Instagram's embed.js once per page, then re-process
			// each time this shortcode runs (handles multiple embeds,
			// AJAX-loaded content, and page builders that reload blocks).
			function lgdProcessInstagramEmbeds() {
				if ( window.instgrm && window.instgrm.Embeds ) {
					window.instgrm.Embeds.process();
				}
			}

			if ( window.instgrm ) {
				lgdProcessInstagramEmbeds();
			} else if ( ! document.getElementById( 'lgd-instagram-embed-script' ) ) {
				var script  = document.createElement( 'script' );
				script.id   = 'lgd-instagram-embed-script';
				script.async = true;
				script.src  = 'https://www.instagram.com/embed.js';
				script.onload = lgdProcessInstagramEmbeds;
				document.body.appendChild( script );
			} else {
				window.addEventListener( 'load', lgdProcessInstagramEmbeds );
			}
		})();
	</script>
	<?php
	return ob_get_clean();
}