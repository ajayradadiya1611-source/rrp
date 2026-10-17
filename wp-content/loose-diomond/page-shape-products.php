<?php
/**
 * Template Name: Shape Products Page
 *
 * Same layout as woocommerce/archive-product.php (engagement-ring
 * version): breadcrumb, top banner, product
 * grid, start engagement rings, testimonials, "Choose How You Shine",
 * "Explore Other Categories", bottom description, bottom category
 * links, related blogs and FAQ.
 *
 * All ACF content is read from THIS PAGE (same field names as the
 * product_cat fields). Products are pre-filtered by the page's ACF
 * "Filter query" (e.g. style=bezel&shape=emerald-cut&price=1139.00,4472.00)
 * — see components/inventory-listing/inc/acf-shape-page.php.
 */

defined( 'ABSPATH' ) || exit;

// Add WooCommerce body classes so shop styles apply like on the archive.
add_filter( 'body_class', function ( $classes ) {
    foreach ( array( 'woocommerce', 'woocommerce-page' ) as $c ) {
        if ( ! in_array( $c, $classes, true ) ) {
            $classes[] = $c;
        }
    }
    return $classes;
} );

get_header( 'shop' );
global $text_change;

$page_id  = get_queried_object_id();
$acf_page = $page_id; // ACF "post id" for get_field() on this page

$image = get_the_post_thumbnail_url( $page_id, 'full' );

if ( empty( $image ) ) {
    $image = get_template_directory_uri() . '/aj_custom/category-top-banner/default.png';
}

// Breadcrumb: Home > Engagement Rings > {Page title}
$ring_term = get_term_by( 'slug', 'engagement-rings', 'product_cat' );
$ring_link = $ring_term ? get_term_link( $ring_term ) : '';
$crumbs    = array( array( 'Home', home_url( '/' ) ) );
if ( $ring_term && ! is_wp_error( $ring_link ) ) {
    $crumbs[] = array( $ring_term->name, $ring_link );
}
$crumbs[] = array( get_the_title( $page_id ), '' );
$crumbs   = apply_filters( 'lgd_shape_page_breadcrumbs', $crumbs, $page_id );

/**
 * SEO: JSON-LD @graph (Organization, WebSite, BreadcrumbList,
 * CollectionPage, FAQPage) — same as archive-product.php.
 */
$schema_home = trailingslashit( home_url() );
$schema_url  = trailingslashit( strtok( get_permalink( $page_id ), '?' ) );
$schema_name = get_the_title( $page_id );
$schema_desc = '';
if ( class_exists( '\RankMath\Paper\Paper' ) ) {
    $schema_desc = (string) \RankMath\Paper\Paper::get()->get_description();
}
if ( '' === trim( $schema_desc ) && function_exists( 'get_field' ) ) {
    $schema_desc = (string) get_field( 'short_description', $acf_page );
}
$schema_desc = trim( wp_strip_all_tags( $schema_desc ) );
if ( '' === $schema_desc ) {
    $schema_desc = sprintf( 'Shop %s at RRP Diamond.', $schema_name );
}

$schema_graph   = array();
$schema_graph[] = array(
    '@type'  => 'Organization',
    '@id'    => $schema_home . '#organization',
    'name'   => 'RRP Diamond',
    'url'    => $schema_home,
    'logo'   => get_template_directory_uri() . '/rrp-img/comman/logo.svg',
    'sameAs' => array(
        'https://www.facebook.com/LooseGrownDiamond/',
        'https://twitter.com/grown_diamond',
    ),
);
$schema_graph[] = array(
    '@type'      => 'WebSite',
    '@id'        => $schema_home . '#website',
    'url'        => $schema_home,
    'name'       => 'RRP Diamond',
    'publisher'  => array( '@id' => $schema_home . '#organization' ),
    'inLanguage' => 'en-US',
);
$bc_items = array();
$pos      = 1;
foreach ( $crumbs as $crumb ) {
    $bc_items[] = array(
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => wp_strip_all_tags( $crumb[0] ),
        'item'     => $crumb[1] ? $crumb[1] : $schema_url,
    );
}
$schema_graph[] = array(
    '@type'           => 'BreadcrumbList',
    '@id'             => $schema_url . '#breadcrumb',
    'itemListElement' => $bc_items,
);
$schema_graph[] = array(
    '@type'       => 'CollectionPage',
    '@id'         => $schema_url . '#webpage',
    'url'         => $schema_url,
    'name'        => $schema_name,
    'description' => $schema_desc,
    'inLanguage'  => 'en-US',
    'isPartOf'    => array( '@id' => $schema_home . '#website' ),
    'breadcrumb'  => array( '@id' => $schema_url . '#breadcrumb' ),
);
$schema_faqs = function_exists( 'get_field' ) ? get_field( 'faq', $acf_page ) : array();
if ( ! empty( $schema_faqs ) ) {
    $faq_items = array();
    foreach ( $schema_faqs as $faq ) {
        $q = isset( $faq['title'] ) ? trim( wp_strip_all_tags( $faq['title'] ) ) : '';
        $a = isset( $faq['description'] ) ? trim( wp_strip_all_tags( $faq['description'] ) ) : '';
        if ( '' === $q || '' === $a ) {
            continue;
        }
        $faq_items[] = array(
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
        );
    }
    if ( $faq_items ) {
        $schema_graph[] = array(
            '@type'      => 'FAQPage',
            '@id'        => $schema_url . '#faq',
            'mainEntity' => $faq_items,
        );
    }
}
echo '<script type="application/ld+json">' . wp_json_encode(
    array( '@context' => 'https://schema.org', '@graph' => $schema_graph ),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
) . '</script>' . "\n";
?>
    <div class="">
        <div class="banner_section mb_30">
          <div class="page_heading ">
                <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <?php foreach ( $crumbs as $crumb ) : ?>
                            <li class="breadcrumb-item">
                                <?php if ( ! empty( $crumb[1] ) ) : ?>
                                    <a href="<?php echo esc_url( $crumb[1] ); ?>"><?php echo esc_html( $crumb[0] ); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html( $crumb[0] ); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                </div>
            </div>
           <div class="bg-banner-custom">
                  <div class="container-fluid">
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="d-flex align-items-center h-100 text-center text-md-start">
                              <div class="engagement-text w-100">

                                <h1 class="mb-2 mb-sm-4 font_literata">
                                  <?php echo esc_html( get_the_title( $page_id ) ); ?>
                                </h1>

                                <div class="fw-normal font_size_15 mb-3">
                                  <?php
                                        echo get_field('short_description', $acf_page)
                                    ?>
                                </div>
                                <div class="d-flex gap-2 text-start mb-2">
                                   <div class="row g-4 oc-icon-ring">

                                        <div class="col-3 col-md-6 d-flex flex-column gap-2">
                                          <img src="<?php echo get_template_directory_uri();?>/aj_custom/breadcum/icon-made-in-usa.png" alt="Made in USA" width="40" height="40">
                                          <span>Made in the USA*</span>
                                        </div>

                                        <div class="col-3 col-md-6 d-flex flex-column gap-2">
                                          <img src="<?php echo get_template_directory_uri();?>/aj_custom/breadcum/icon-worldwide.png" alt="Worldwide shipping" width="40" height="40">
                                          <span>Ships Worldwide*</span>
                                        </div>

                                        <div class="col-3 col-md-6 d-flex flex-column gap-2">
                                          <img src="<?php echo get_template_directory_uri();?>/aj_custom/breadcum/icon-support.png" alt="24hr support" width="40" height="40">
                                          <span>24/7 Expert Support*</span>
                                        </div>

                                        <div class="col-3 col-md-6 d-flex flex-column gap-2">
                                          <img src="<?php echo get_template_directory_uri();?>/aj_custom/breadcum/icon-returns.png" alt="<?php echo RETURN_DAYS;?>-day returns" width="40" height="40">
                                          <span><?php echo RETURN_DAYS;?>-Day Easy Returns</span>
                                        </div>

                                      </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-6 aj-right-cat-top d-none d-lg-block">
                            <img src="<?php echo $image; ?>" alt="<?php echo esc_attr( get_the_title( $page_id ) ); ?>">
                        </div>
                     </div>
                  </div>
               </div>
        </div>

         
    </div>

    <?php lgd_render_inventory_listing(); ?>

<?php
    echo '<div class="section bg_light_gray">';
    single_product_what_customer_say();
    echo '</div>';
 ?>
  <div class="section choose_shine_section">
    <div class="container">
        <h2 class="text_black_secondary h1 fw-normal font_literata mb-4 mb-md-5 text-center">Choose How You Shine</h2>
        <div class="row g-3 g-md-4 justify-content-center">
           <?php
            $categories = [
                [
                    'url'  => '/build-a-ring/',
                    'img'  => get_template_directory_uri() . '/aj_custom/how-sign/1.png',
                    'alt'  => 'Best sellers',
                    'name' => 'Best sellers',
                ],
                [
                    'url'        => '#customSettingModal',
                    'img'        => get_template_directory_uri() . '/aj_custom/how-sign/2.png',
                    'alt'        => 'Custom setting',
                    'name'       => 'Custom setting?',
                    'cta'        => 'Yes, please',
                    'modal'      => true,
                ],
                [
                    'url'  => '/ready-to-ship/engagement-rings/',
                    'img'  => get_template_directory_uri() . '/aj_custom/how-sign/3.png',
                    'alt'  => 'Ready to ship',
                    'name' => 'Ready to ship',
                ],
            ];
            ?>

            <?php foreach ($categories as $category) : ?>
                <div class="col-md-4 col-sm-6 col-12">

                    <a
                        href="<?php echo esc_url($category['url']); ?>"
                        class="text-decoration-none d-block"
                        <?php if (!empty($category['modal'])) : ?>
                            data-bs-toggle="modal"
                            data-bs-target="#customSettingModal"
                        <?php endif; ?>
                    >
                        <div class="choose_sine<?php echo !empty($category['cta']) ? ' choose_sine--cta' : ''; ?>">

                            <img
                                src="<?php echo esc_url($category['img']); ?>"
                                alt="<?php echo esc_attr($category['alt']); ?>"
                                loading="lazy"
                            >

                            <div class="choose_sine__content">
                                <p class="choose_sine__title mb-0">
                                    <?php echo esc_html($category['name']); ?>
                                </p>

                                <?php if (!empty($category['cta'])) : ?>
                                    <span class="choose_sine__btn">
                                        <?php echo esc_html($category['cta']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </a>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php
$short_description = get_field(
    'short_description_bottom',
    $acf_page
);

$short_description_bottom = get_field(
    'short_description_bottom_description',
    $acf_page
);

if (!empty($short_description) || !empty($short_description_bottom)):
?>

<div class="container my-5">
    <div class="ls_inventory_desc">
        <div class="ls_inventory_desc_inner">

            <?php if (!empty($short_description)): ?>
                <div>
                    <p class="pro-cat_desc">
                        <?php echo wp_kses_post($short_description); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($short_description_bottom)): ?>
                <div class="pro-cat_desc_hide">
                    <?php echo wp_kses_post($short_description_bottom); ?>
                </div>
            <?php endif; ?>

        </div>

        <?php if (!empty($short_description_bottom)): ?>
            <div id="invtoggleBtn" class="oc-invtoggleBtn">
                Read More
            </div>
        <?php endif; ?>

    </div>
</div>

<?php endif; ?>
<?php
$bottom_categories =  get_field('bottom_category', $acf_page) ;

if ($bottom_categories) : ?>
    <div class="shop_by_category_wrap">
        <div class="container">
            <div class="row  gy-4">

                <?php foreach ($bottom_categories as $category) : ?>
                    <div class="col-md-4 col-sm-12 shop_by_category_col">
                        
                        <?php if (!empty($category['title'])) : ?>
                            <h2><?php echo esc_html($category['title']); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($category['links'])) : ?>
                            <ul>
                                <?php foreach ($category['links'] as $link) : ?>
                                    <?php if (!empty($link['link_title']) && !empty($link['link_value'])) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($link['link_value']); ?>">
                                                <?php echo esc_html($link['link_title']); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$related_blog_ids = get_field('oc_related_blog', $acf_page);

if ($related_blog_ids) :
    // Normalize: ACF may return IDs or post objects depending on field settings
    $related_blog_ids = array_map(function($item) {
        return is_object($item) ? $item->ID : $item;
    }, $related_blog_ids);

    $related_posts = get_posts(array(
        'post__in'       => $related_blog_ids,
        'orderby'        => 'post__in', // preserve the order set in ACF
        'posts_per_page' => count($related_blog_ids),
        'post_type'      => 'post',
    ));
?>
<div class="container related_blogs_wrap">
    <div class="text-center">
        <h2 class="related_blogs_title text_black_secondary h1 fw-normal font_literata mb-4 text-center">Engagement Ring Education</h2>
    </div>
    <div class="row">
        <?php foreach ($related_posts as $blog_post) : ?>
            <div class="col-md-6 mb-4">
                <div class="related_blog_card">
                    <h3>
                        <a href="<?php echo esc_url(get_permalink($blog_post->ID)); ?>">
                            <?php echo esc_html(get_the_title($blog_post->ID)); ?>
                        </a>
                    </h3>
                    <p>
                        <?php echo wp_trim_words(get_the_excerpt($blog_post->ID), 20, '...'); ?>
                    </p>
                    <span class="blog_author">
                        <?php echo esc_html(get_the_author_meta('display_name', $blog_post->post_author)); ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
<?php 
$faqs = get_field('faq', $acf_page);

if (!empty($faqs)): 
?>

<div class="container mt-5 mb-5 mb-md-2">
    <h2 class="text_black_secondary h1 fw-normal font_literata mb-4 text-center">Frequently Asked Questions</h2>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="faq_list">
                <div class="accordion" id="faqAccordion">

                    <?php foreach ($faqs as $index => $faq): 
                        $id = 'faq' . ($index + 1);

                        $question = $faq['title'];
                        $answer   = $faq['description'];

                        // Skip empty FAQ items
                        if (empty($question) && empty($answer)) {
                            continue;
                        }
                    ?>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#<?php echo esc_attr($id); ?>">
                                    <?php echo esc_html($question); ?>
                                </button>
                            </h3>

                            <div id="<?php echo esc_attr($id); ?>"
                                 class="accordion-collapse collapse"
                                 data-bs-parent="#faqAccordion">

                                <div class="accordion-body">
                                    <?php echo wp_kses_post($answer); ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<?php

get_footer( 'shop' );
