<?php

/**
 * Template Name: Page Content
 */

get_header();
$page_type_option = get_field('page_type_option');
?>

    <main id="primary" class="site-main">
        <div class="py_30">
            <div class="banner_section">
                <div class="container-fluid  narrow-content-title">
                    <?php
                    if($page_type_option=='Simple Title'){
                        echo '<h1 class="h2 text-center mb-3">' . get_the_title() . '</h1>';
                    }
                    ?>
                </div>
                <div class="container-fluid narrow-content-desc">
                    <div class="row justify-content-center">
                         <div class="col-md-7">
                    <?php
                        while ( have_posts() ) :
                            the_post();

                            the_content();

                            

                        endwhile; // End of the loop.
                    ?>
                </div>
            </div>
                </div>
            </div>
        </div>
    </main><!-- #main -->

<?php
get_footer();
