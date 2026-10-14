	<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Loose_Diomond
 */

get_header();
$page_type_option = get_field('page_type_option');
?>

	<main id="primary" class="site-main">
		<div class="<?php if(!is_checkout()){echo 'py_30';}?>">
			<div class="banner_section <?php if (!is_order_received_page()) {echo '';} if (is_order_received_page()) {echo 'oc-order-received';}?>">
				<div class="container-fluid">
				    <?php
				    if($page_type_option=='Simple Title'){
				        echo '<h1 class="text-center mb-3 title_df_page">' . get_the_title() . '</h1>';
				    }
				    ?>
				</div>
				<div class="<?php if (!is_order_received_page()) {echo 'container-fluid';} ?>">
					<?php
						while ( have_posts() ) :
							the_post();

							the_content();

							

						endwhile; // End of the loop.
					?>
				</div>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_footer();
