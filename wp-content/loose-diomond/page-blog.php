<?php
/**
 * Template Name: Blog
 */

get_header();
?>
<div class="banner_section">
    <div class="mt_67">

      <div class="loosgrown-blog">
        <div class="container">
          <div class="row gy-4 gy-md-5">

            <?php
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;

            $blog_query = new WP_Query([
              'post_type'      => 'post',
              'post_status'    => 'publish',
              'posts_per_page' => 9,
              'paged'          => $paged,
            ]);

            if ($blog_query->have_posts()) :
              while ($blog_query->have_posts()) : $blog_query->the_post(); ?>

                <div class="col-md-6 col-lg-4">
                  <div class="card border-0">

                    <?php if (has_post_thumbnail()) : ?>
                      <img
                        src="<?php the_post_thumbnail_url('medium_large'); ?>"
                        class="card-img-top"
                        alt="<?php the_title_attribute(); ?>"
                      />
                    <?php else : ?>
                      <img
                        src="<?php echo get_template_directory_uri(); ?>/assets/image/banner-3.png"
                        class="card-img-top"
                        alt="<?php the_title_attribute(); ?>"
                      />
                    <?php endif; ?>

                    <div class="card-body">
                      <h2>
                        <a href="<?php the_permalink(); ?>" class="card-title text-decoration-none">
                          <?php the_title(); ?>
                        </a>
                      </h2>

                      <div class="blog-text">
                        <p class="card-text fw-normal">
                          <?php echo wp_trim_words(get_the_excerpt(), 20, '…'); ?>
                        </p>
                      </div>

                      <div class="d-flex justify-content-between align-items-center">
                        <a
                          href="<?php the_permalink(); ?>"
                          class="arrow-icon d-flex justify-content-center align-items-center rounded-circle"
                        >
                          <i class="fa fa-long-arrow-right"></i>
                        </a>
                        <p class="date-diaplay mb-0">
                          <?php echo get_the_date('F d, Y'); ?>
                        </p>
                      </div>
                    </div>

                  </div>
                </div>

              <?php endwhile; ?>

              <!-- Pagination -->
              <div class="col-12">
               <?php
                $pagination_links = paginate_links([
                    'total'      => $blog_query->max_num_pages,
                    'current'    => $paged,
                    'type'       => 'array', // IMPORTANT: get links as array
                    'prev_next'  => true,
                    'prev_text'  => '<i class="fa fa-angle-double-left"></i>',
                    'next_text'  => '<i class="fa fa-angle-double-right"></i>',
                ]);

                if ($pagination_links) {
                    echo '<nav class="blog_pagination woocommerce-pagination pagination-cubic">';
                    echo '<ul class="page-numbers">';

                    foreach ($pagination_links as $link) {
                        echo '<li>' . $link . '</li>';
                    }

                    echo '</ul>';
                    echo '</nav>';
                }
                ?>
              </div>

            <?php else : ?>
              <div class="col-12">
                <p>No posts found.</p>
              </div>
            <?php endif;

            wp_reset_postdata(); ?>

          </div>
        </div>
      </div>
    </div>
</div>
<?php get_footer(); ?>