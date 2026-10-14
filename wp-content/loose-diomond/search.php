<?php

/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Loose_Diomond
 */

get_header();
?>

<main id="primary" class="site-main">

	<div class="py_30">
		<div class="banner_section">
			<div class="container-fluid">

				<?php if (have_posts()) : ?>

					<header class="page-header">
						<h1 class="page-title">
							<?php
							/* translators: %s: search query. */
							printf(esc_html__('Search Results for: %s', 'loose-diomond'), '<span>' . get_search_query() . '</span>');
							?>
						</h1>
					</header><!-- .page-header -->

					<?php
					/* Start the Loop */
					while (have_posts()) :
						the_post();

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						// get_template_part('template-parts/content', 'search');
						?>
						<header class="entry-header">
							<?php
							if (is_singular()) :
								the_title('<h1 class="entry-title">', '</h1>');
							else :
								the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
							endif;

							if ('post' === get_post_type()) :
							?>
								<div class="entry-meta">
									<?php
									loose_diomond_posted_on();
									loose_diomond_posted_by();
									?>
								</div><!-- .entry-meta -->
							<?php endif; ?>
						</header>
						<?php loose_diomond_post_thumbnail(); ?>
						<div class="entry-content">
							<?php
							the_content(
								sprintf(
									wp_kses(
										/* translators: %s: Name of current post. Only visible to screen readers */
										__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'loose-diomond'),
										array(
											'span' => array(
												'class' => array(),
											),
										)
									),
									wp_kses_post(get_the_title())
								)
							);

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . esc_html__('Pages:', 'loose-diomond'),
									'after'  => '</div>',
								)
							);
							?>
						</div>
						<?php

					endwhile;

					the_posts_navigation();

				else :

					// get_template_part('template-parts/content', 'none');
					?>
					<header class="entry-header">
						<?php
						if (is_singular()) :
							the_title('<h1 class="entry-title">', '</h1>');
						else :
							the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
						endif;

						if ('post' === get_post_type()) :
						?>
							<div class="entry-meta">
								<?php
								loose_diomond_posted_on();
								loose_diomond_posted_by();
								?>
							</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->

					<?php loose_diomond_post_thumbnail(); ?>

					<div class="entry-content">
						<?php
						the_content(
							sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'loose-diomond'),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								wp_kses_post(get_the_title())
							)
						);

						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__('Pages:', 'loose-diomond'),
								'after'  => '</div>',
							)
						);
						?>
					</div><!-- .entry-content -->
					<?php

				endif;
				?>
			</div>
		</div>
	</div>

</main><!-- #main -->

<?php
get_sidebar();
get_footer();
