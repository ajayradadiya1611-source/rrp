<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Loose_Diomond
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if (have_posts()) : ?>

			<!-- ================= Banner Section ================= -->
			<div class="banner_section">
				<div class="container-fluid">
					<div class="cut_diamond_banner text-center">

						<h4 class="fw-600 mb_10">
							<?php echo wp_kses_post(get_the_archive_title()); ?>
						</h4>

						<div class="cut_breadcrumb">
							<a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
							/
							<span>
								Archive by <?php echo wp_kses_post(get_the_archive_title()); ?>
							</span>
						</div>

						<a href="javascript:history.go(-1)" class="back_history">
							<i class="fa-solid fa-angle-left"></i> Return to Previous Page
						</a>

					</div>
				</div>
			</div>

			<!-- ================= Blog Grid Section ================= -->
			<div class="py_60 pb-4">
				<div class="container-fluid">
					<div class="row">

						<?php while (have_posts()) : the_post(); ?>

							<div class="col-lg-4 col-md-6">
								<div class="blog_masonry mb_40">

									<a href="<?php the_permalink(); ?>">
										<?php if (has_post_thumbnail()) : ?>
											<?php the_post_thumbnail('medium_large', ['class' => 'img-fluid']); ?>
										<?php endif; ?>
									</a>

									<div class="blog_content">

										<a href="<?php the_permalink(); ?>">
											<?php echo wp_trim_words(get_the_title(), 7, '...'); ?>
										</a>

										<div class="short_line"></div>

										<h6>
											<?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
										</h6>

										<div class="d-flex align-items-center justify-content-between">
											<a href="<?php the_permalink(); ?>" class="right_arrow">
												<i class="fa-solid fa-arrow-right-long"></i>
											</a>

											<h5 class="mb-0">
												<?php echo get_the_date('F d, Y'); ?>
											</h5>
										</div>

									</div>
								</div>
							</div>

						<?php endwhile; ?>

						<!-- ================= Pagination ================= -->
						<div class="col-md-12">
							<nav class="blog_pagination" aria-label="Page navigation example">
								<ul class="pagination justify-content-center">

									<?php
									global $wp_query;

									$big = 999999999; // need an unlikely integer

									$links = paginate_links(array(
										'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
										'format'    => '?paged=%#%',
										'current'   => max(1, get_query_var('paged')),
										'total'     => $wp_query->max_num_pages,
										'type'      => 'array',
										'prev_text' => '«',
										'next_text' => '»',
									));

									if (!empty($links)) :
										foreach ($links as $link) :
											$active = strpos($link, 'current') !== false ? ' active' : '';
											?>
											<li class="page-item<?php echo $active; ?>">
												<?php echo str_replace('page-numbers', 'page-link', $link); ?>
											</li>
											<?php
										endforeach;
									endif;
									?>

								</ul>
							</nav>
						</div>

					</div>
				</div>
			</div>

		<?php else : ?>

			<div class="container py-5 text-center">
				<h3>No Posts Found</h3>
			</div>

		<?php endif; ?>

	</main>
	<!-- #main -->

<?php
get_sidebar();
get_footer();
