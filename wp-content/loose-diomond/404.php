<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Loose_Diomond
 */

get_header();
global $text_change;
global $wp_query;
$is_product_404 = false;

// Check if the 404 was triggered for a single product request
if ( isset( $wp_query->queried_object ) && isset( $wp_query->query_vars['post_type'] ) ) {

    if ( $wp_query->query_vars['post_type'] === 'product' ) {
        $is_product_404 = true;
    }
}
?>

	<main id="primary" class="site-main">
		<div class="py_30">
			<div class="banner_section">
				<div class="container-fluid">
					<section class="error-404 not-found">
						<div class="page-content page-404">
							<div class="row">
								<div class="col-md-12">
									<?php if ( $is_product_404 ) : ?>
										<h1 class="largest">404</h1>
										<h1><?php esc_html_e('Oops! Page not found', 'loose-diamond') ?></h1>
										<!-- <hr class="horizontal-break"> -->
										<p><?php esc_html_e('Sorry, but the page you are looking for is not found.', 'loose-diamond') ?> </p>
										<?php // get_search_form( true ); ?>
										<a href="<?php echo home_url(); ?>" class="ls_bbtn"><?php esc_html_e('Go to home page', 'loose-diamond'); ?></a>
									<?php else : ?>
										<h1 class="largest">410</h1>
										<h1><?php esc_html_e('Oops! Page has been removed', 'loose-diamond') ?></h1>
										<!-- <hr class="horizontal-break"> -->
										<p class="pt-3 pb-3 mb-0"><?php esc_html_e("Don't worry - The diamond has already found it's forever. We can help you find an equally beautiful alternative.", 'loose-diamond') ?> </p>
										<?php // get_search_form( true ); ?>
										<a href="<?php echo $text_change['inventory_url']; ?>" class="ls_bbtn"><?php esc_html_e('Find Similar Diamonds', 'loose-diamond'); ?></a>
										<script>
											setTimeout(function() {
												window.location.href = "<?php echo $text_change['inventory_url']; ?>";
											}, 5000);
										</script>
									<?php endif; ?>
								</div>
							</div>

						</div><!-- .page-content -->
					</section><!-- .error-404 -->
				</div>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_footer();
