<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Loose_Diomond
 */

get_header();
?>

<main id="primary" class="site-main">

	<div class="banner_section">
		<div class="container-fluid">
			<div class="row blog-details">
				<div class="col-12 col-md-8">
					<div class="blog-details-section">

						<?php
						while (have_posts()) :
							the_post();
						?>

							<!-- Blog Title -->
							<h1 class="blog-details-heading mb-20">
								<?php the_title(); ?>
							</h1>

							<!-- Meta -->
							<p class="posted-meta">
							    Posted on <?php echo get_the_date('F j, Y'); ?> by
							    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="text-yellow fw-semibold">
							        <?php the_author(); ?>
							    </a>

							    <?php
							    $second_author_id = get_field('second_author');

							    if ($second_author_id) :
							        $second_author = is_array($second_author_id) ? $second_author_id['ID'] : $second_author_id;
							        $second_author_name = get_the_author_meta('display_name', $second_author);
							    ?>
							        &amp;
							        <a href="<?php echo get_author_posts_url($second_author); ?>" class="text-yellow fw-semibold">
							            <?php echo esc_html($second_author_name); ?>
							        </a>
							    <?php endif; ?>

							    

							    <br>
							    Last updated on <?php echo get_the_modified_date('F j, Y'); ?> at <?php echo get_the_modified_time('g:i a'); ?>
							</p>

							<hr class="post-hr" />

							<!-- Featured Image -->
							<?php if (has_post_thumbnail()) : ?>
								<div class="mb-40">
									<?php the_post_thumbnail('full', ['class' => 'img-fluid']); ?>
								</div>
							<?php endif; ?>

							<div class="mb-40 blog-details-summary">
								<h3>Summary</h3>
								<div>
									<?php
									echo  get_field('summary_box'); 
									?>
								</div>
							</div>

							<!-- Post Content -->
							<div class="mb-40">
								<?php the_content(); ?>
							</div>

							<!-- Categories -->
							<h6>
								<?php the_category(', '); ?>
							</h6>

							<!-- Share Social -->
							<div class="share-social">
								<p class="share-with-summarize mb-0">SHARE</p>
								<a href="">
									<i class="fa-brands fa-twitter"></i>
								</a>
								<a href="">
									<i class="fa-brands fa-facebook-f"></i>
								</a>
								<a href="">
									<i class="fa-brands fa-pinterest"></i>
								</a>
								<a href="">
									<i class="fa-regular fa-envelope"></i>
								</a>
							</div>

							<!-- Author Box -->
							<div class="author-main">
								<?php echo get_avatar(get_the_author_meta('ID'), 100); ?>
								<div>
									<h4 class="author_name"><?php the_author(); ?></h4>
									<p class="author_description mb-0">
										<?php the_author_meta('description'); ?>
									</p>
									<div class="author-social d-flex align-items-center gap-3 mt-3">
									    <a href="https://x.com/chrisgaley20"
									       class="text-dark text-decoration-none fs-5"
									       target="_blank"
									       rel="noopener noreferrer"
									       aria-label="X">
									        <i class="fa-brands fa-x-twitter"></i>
									    </a>

									    <a href="https://www.linkedin.com/in/chris-galey-40531b421/"
									       class="text-dark text-decoration-none fs-5"
									       target="_blank"
									       rel="noopener noreferrer"
									       aria-label="LinkedIn">
									        <i class="fa-brands fa-linkedin-in"></i>
									    </a>
									</div>
								</div>
							</div>

							<hr class="divider-main" />

							<!-- Related Posts -->
							<div class="related-post">
								<div class="related-post-head">
									<h3 class="related-post-title">Related posts</h3>
									<hr />
								</div>

								<div class="row">
									<?php
									$related = new WP_Query([
										'category__in' => wp_get_post_categories(get_the_ID()),
										'post__not_in' => [get_the_ID()],
										'posts_per_page' => 3
									]);

									if ($related->have_posts()):
										while ($related->have_posts()): $related->the_post();
									?>
											<div class="col-lg-4">
												<div class="post-card">
													<div class="post-image">
														<a href="<?php the_permalink(); ?>">
															<?php if (has_post_thumbnail()) {
																the_post_thumbnail('medium', ['class' => 'img-fluid']);
															} ?>
															<div class="overlay">
																<button class="common_btn">More details</button>
															</div>
														</a>
													</div>

													<h3 class="post-title">
														<a href="<?php the_permalink(); ?>">
															<?php echo wp_trim_words(get_the_title(), 6); ?>
														</a>
													</h3>

													<p class="post-description">
														<?php echo wp_trim_words(get_the_excerpt(), 15); ?>
													</p>
												</div>
											</div>
									<?php
										endwhile;
										wp_reset_postdata();
									endif;
									?>
								</div>
							</div>

							<!-- Post Navigation -->
							<!-- <div class="post-navigation py-4">
								<?php
								the_post_navigation([
									'prev_text' => '<span class="nav-subtitle">Previous:</span> <span class="nav-title">%title</span>',
									'next_text' => '<span class="nav-subtitle">Next:</span> <span class="nav-title">%title</span>',
								]);
								?>
							</div> -->

							<!-- Comments -->
							<?php
							if (comments_open() || get_comments_number()) :
								comments_template();
							endif;
							?>

						<?php endwhile; ?>

					</div>
				</div>
				<div class="col-12 col-md-4">
    <div class="blog-sidebar-section">

        <!-- Popular Posts -->
        <div class="sidebar-widget popular-posts mb-40">
            <h4 class="sidebar-widget-title mb-20">Popular Posts</h4>
            <?php
            $popular_posts = new WP_Query([
                'posts_per_page' => 5,
                'post__not_in'   => [get_the_ID()],
                'meta_key'       => 'post_views_count', // requires a view-counter to populate this meta key
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'ignore_sticky_posts' => true,
            ]);

            // Fallback to comment count if no view-count meta exists yet
            if (!$popular_posts->have_posts()) {
                $popular_posts = new WP_Query([
                    'posts_per_page' => 5,
                    'post__not_in'   => [get_the_ID()],
                    'orderby'        => 'comment_count',
                    'order'          => 'DESC',
                    'ignore_sticky_posts' => true,
                ]);
            }

            if ($popular_posts->have_posts()) :
                while ($popular_posts->have_posts()) : $popular_posts->the_post();
            ?>
                <div class="sidebar-post-item d-flex gap-3">
                    <a href="<?php the_permalink(); ?>" class="sidebar-post-thumb flex-shrink-0">
                        <?php if (has_post_thumbnail()) {
                            the_post_thumbnail('thumbnail', ['class' => 'img-fluid']);
                        } ?>
                    </a>
                    <div class="sidebar-post-content">
                        <h6 class="sidebar-post-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo wp_trim_words(get_the_title(), 8); ?>
                            </a>
                        </h6>
                        <span class="sidebar-post-date"><?php echo get_the_date('F j, Y'); ?></span>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No popular posts found.</p>';
            endif;
            ?>
        </div>

        <!-- Recent Posts -->
        <div class="sidebar-widget recent-posts mb-40">
            <h4 class="sidebar-widget-title mb-20">Recent Posts</h4>
            <?php
            $recent_posts = new WP_Query([
                'posts_per_page' => 5,
                'post__not_in'   => [get_the_ID()],
                'orderby'        => 'date',
                'order'          => 'DESC',
                'ignore_sticky_posts' => true,
            ]);

            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>
                <div class="sidebar-post-item d-flex gap-3 ">
                    <a href="<?php the_permalink(); ?>" class="sidebar-post-thumb flex-shrink-0">
                        <?php if (has_post_thumbnail()) {
                            the_post_thumbnail('thumbnail', ['class' => 'img-fluid']);
                        } ?>
                    </a>
                    <div class="sidebar-post-content">
                        <h6 class="sidebar-post-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo wp_trim_words(get_the_title(), 8); ?>
                            </a>
                        </h6>
                        <span class="sidebar-post-date"><?php echo get_the_date('F j, Y'); ?></span>
                    </div>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No recent posts found.</p>';
            endif;
            ?>
        </div>

        <!-- Offer -->
        <!-- <div class="sidebar-widget sidebar-offer mb-40">
           
               
                <div class="sidebar-offer-box text-center p-4">
                    <h5 class="mb-10">Limited Time Offer</h5>
                    <p class="mb-15">Get 10% off on your first lab-grown diamond purchase.</p>
                    <a href="/shop" class="common_btn">Shop Now</a>
                </div>
           
        </div> -->

    </div>
</div>
			</div>
			
		</div>
	</div>

</main>

<?php
get_footer();
