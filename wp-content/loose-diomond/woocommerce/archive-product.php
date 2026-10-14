<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
global $text_change;
$category = get_queried_object();
$category_id = $category->term_id;
$category_slug = $category->slug;
$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
$image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : get_template_directory_uri() . '/aj_custom/category-top-banner/default.png';
// Get parent category slug (if any)
$parent_slug = '';
if ( $category->parent ) {
    $parent_term = get_term( $category->parent, 'product_cat' );
    if ( $parent_term && ! is_wp_error( $parent_term ) ) {
        $parent_slug = $parent_term->slug;
    }
}

if ( $category_slug == 'engagement-rings' || $parent_slug == 'engagement-rings' ) {
    $is_ring_page = true;
} else {
    $is_ring_page = false;
}

 if($category_slug=='engagement-rings'){
    $category = array(
        'round-brilliant-cut' => 'round',
        'princess-cut'        => 'princess',
        'cushion-cut'         => 'cushion',
        'oval-cut'            => 'oval',
        'emerald-cut'         => 'emerald',
        'asscher-cut'         => 'asscher',
        'pear-cut'            => 'pear',
        'radiant-cut'         => 'radiant',
        'marquise-cut'        => 'marquise',
        'heart-cut'           => 'heart',
    );

    $ls_req_shape = isset($_REQUEST['shape']) ? sanitize_text_field(wp_unslash($_REQUEST['shape'])) : '';
    $top_banner = isset($category[$ls_req_shape]) ? $category[$ls_req_shape] : '';
    
 }else{
     $top_banner = $category_slug;
 }

/**
 * ------------------------------------------------------------------
 * SEO: single JSON-LD @graph for archive pages
 * Organization, WebSite, BreadcrumbList, CollectionPage, ItemList, FAQPage
 * (Rank Math schema is disabled on these pages via functions.php)
 * ------------------------------------------------------------------
 */
if ( ! is_search() || have_posts() ) {

    $schema_term    = get_queried_object();
    $schema_is_term = ( $schema_term instanceof WP_Term );
    $schema_home    = trailingslashit( home_url() );

    // Page URL (get_permalink() is wrong on term archives)
    if ( $schema_is_term ) {
        $schema_url = get_term_link( $schema_term );
        $schema_url = is_wp_error( $schema_url ) ? home_url( add_query_arg( array() ) ) : $schema_url;
    } elseif ( is_post_type_archive( 'product' ) ) {
        $schema_url = get_post_type_archive_link( 'product' );
    } else {
        $schema_url = home_url( add_query_arg( array() ) );
    }
    $schema_url = trailingslashit( strtok( $schema_url, '?' ) );

    // Name
    if ( is_product_category() || is_product_tag() ) {
        $schema_name = single_term_title( '', false );
    } elseif ( is_post_type_archive( 'product' ) ) {
        $schema_name = 'All Products';
    } else {
        $schema_name = wp_get_document_title();
    }

    // Description: Rank Math term meta description -> term description
    $schema_desc = '';
    if ( $schema_is_term ) {
        $schema_desc = get_term_meta( $schema_term->term_id, 'rank_math_description', true );
        if ( empty( $schema_desc ) && ! empty( $schema_term->description ) ) {
            $schema_desc = $schema_term->description;
        }
        // Rank Math may store variables like %term%
        if ( ! empty( $schema_desc ) && class_exists( '\RankMath\Helper' ) && method_exists( '\RankMath\Helper', 'replace_vars' ) ) {
            $schema_desc = \RankMath\Helper::replace_vars( $schema_desc, $schema_term );
        }
    }
    // Prefer the exact meta description Rank Math prints in <head> (includes its global template)
    if ( class_exists( '\RankMath\Paper\Paper' ) ) {
        $rm_desc = \RankMath\Paper\Paper::get()->get_description();
        if ( ! empty( $rm_desc ) ) {
            $schema_desc = $rm_desc;
        }
    }
    $schema_desc = trim( wp_strip_all_tags( (string) $schema_desc ) );
    // Final fallback so description is never empty
    if ( '' === $schema_desc ) {
        $schema_desc = sprintf( 'Shop %s at Loose Grown Diamond.', $schema_name );
    }

    // Logo
    $schema_logo = get_template_directory_uri() . '/assets/image/logo/loosegrowndiamond_logo.svg';

    $schema_graph = array();

    // Organization
    $schema_org = array(
        '@type'  => 'Organization',
        '@id'    => $schema_home . '#organization',
        'name'   => 'Loose Grown Diamond',
        'url'    => $schema_home,
        'logo'   => $schema_logo,
        'sameAs' => array(
            'https://www.facebook.com/LooseGrownDiamond/',
            'https://twitter.com/grown_diamond',
        ),
    );
    $schema_graph[] = $schema_org;

    // WebSite
    $schema_graph[] = array(
        '@type'      => 'WebSite',
        '@id'        => $schema_home . '#website',
        'url'        => $schema_home,
        'name'       => 'Loose Grown Diamond',
        'publisher'  => array( '@id' => $schema_home . '#organization' ),
        'inLanguage' => 'en-US',
    );

    // BreadcrumbList (same trail as the visible WooCommerce breadcrumb)
    $schema_crumbs = array();
    if ( class_exists( 'WC_Breadcrumb' ) ) {
        $wc_bc = new WC_Breadcrumb();
        $wc_bc->add_crumb( 'Home', $schema_home );
        $schema_crumbs = $wc_bc->generate();
    }
    /**
     * Override crumbs if needed, e.g. point "Engagement Rings" to /build-a-ring/.
     * Each crumb: array( 0 => name, 1 => url ).
     */
    $schema_crumbs = apply_filters( 'lgd_archive_schema_breadcrumbs', $schema_crumbs, $schema_term );

    $schema_has_breadcrumb = false;
    if ( ! empty( $schema_crumbs ) ) {
        $bc_items = array();
        $pos      = 1;
        $last     = count( $schema_crumbs );
        foreach ( $schema_crumbs as $i => $crumb ) {
            $crumb_name = isset( $crumb[0] ) ? wp_strip_all_tags( $crumb[0] ) : '';
            $crumb_url  = ! empty( $crumb[1] ) ? $crumb[1] : ( ( $i + 1 ) === $last ? $schema_url : '' );
            if ( '' === $crumb_name || '' === $crumb_url ) {
                continue;
            }
            $bc_items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $crumb_name,
                'item'     => $crumb_url,
            );
        }
        if ( $bc_items ) {
            $schema_graph[] = array(
                '@type'           => 'BreadcrumbList',
                '@id'             => $schema_url . '#breadcrumb',
                'itemListElement' => $bc_items,
            );
            $schema_has_breadcrumb = true;
        }
    }

    // ItemList (WooCommerce products in this category/tag)
    $schema_list_items = array();
    if ( $schema_is_term && in_array( $schema_term->taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
        $schema_products = wc_get_products( array(
            'status'     => 'publish',
            'visibility' => 'catalog',
            'limit'      => 20,
            ( 'product_cat' === $schema_term->taxonomy ? 'category' : 'tag' ) => array( $schema_term->slug ),
        ) );

        $pos = 1;
        foreach ( $schema_products as $p ) {
            if ( $p->is_type( 'variable' ) ) {
                $low  = $p->get_variation_price( 'min' );
                $high = $p->get_variation_price( 'max' );
            } else {
                $low  = $p->get_price();
                $high = $low;
            }
            if ( '' === $low || null === $low ) {
                continue;
            }

            $item = array(
                '@type'  => 'Product',
                'name'   => wp_strip_all_tags( $p->get_name() ),
                'url'    => $p->get_permalink(),
                'offers' => array(
                    '@type'         => 'AggregateOffer',
                    'priceCurrency' => get_woocommerce_currency(),
                    'lowPrice'      => (float) wc_format_decimal( $low, 2 ),
                    'highPrice'     => (float) wc_format_decimal( $high, 2 ),
                    'availability'  => $p->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ),
            );
            $img = wp_get_attachment_image_url( $p->get_image_id(), 'full' );
            if ( $img ) {
                $item['image'] = $img;
            }

            $schema_list_items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'item'     => $item,
            );
        }
    }

    // CollectionPage
    $schema_page = array(
        '@type'      => 'CollectionPage',
        '@id'        => $schema_url . '#webpage',
        'url'        => $schema_url,
        'name'        => $schema_name,
        'description' => $schema_desc,
        'inLanguage'  => 'en-US',
        'isPartOf'    => array( '@id' => $schema_home . '#website' ),
    );
    if ( $schema_has_breadcrumb ) {
        $schema_page['breadcrumb'] = array( '@id' => $schema_url . '#breadcrumb' );
    }
    if ( $schema_list_items ) {
        $schema_page['mainEntity'] = array( '@id' => $schema_url . '#itemlist' );
    }
    $schema_graph[] = $schema_page;

    if ( $schema_list_items ) {
        $schema_graph[] = array(
            '@type'           => 'ItemList',
            '@id'             => $schema_url . '#itemlist',
            'name'            => $schema_name,
            'itemListOrder'   => 'https://schema.org/ItemListUnordered',
            'numberOfItems'   => count( $schema_list_items ),
            'itemListElement' => $schema_list_items,
        );
    }

    // FAQPage (same ACF field as the visible accordion)
    $schema_faqs = $schema_is_term ? get_field( 'faq', 'product_cat_' . get_queried_object_id() ) : array();
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
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $a,
                ),
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
        array(
            '@context' => 'https://schema.org',
            '@graph'   => $schema_graph,
        ),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    ) . '</script>' . "\n";
}
?>
<?php
 if (is_search() && !have_posts()) {
?>
<main id="primary" class="site-main">
        <div class="py_30">
            <div class="banner_section">
                <div class="container-fluid">
                    <section class="error-404 not-found">
                        <div class="page-content page-404">
                            <div class="row">
                                <div class="col-md-12">
                                  
                                        <h1 class="largest">No Products were found</h1>
                                        <hr class="horizontal-break">
                                        <p><?php esc_html_e('Sorry, but the page you are looking for is not found.', 'loose-diamond') ?> </p>
                                        <?php // get_search_form( true ); ?>
                                        <a href="<?php echo $text_change['inventory_url']; ?>" class="ls_bbtn"><?php esc_html_e('Return to Shop', 'loose-diamond'); ?></a>
                                   
                                </div>
                            </div>

                        </div><!-- .page-content -->
                    </section><!-- .error-404 -->
                </div>
            </div>
        </div>
    </main><!-- #main -->

<?php
}else{
?>


    <?php 
    if($is_ring_page==true){
    ?>
    <div class="">
        <div class="banner_section mb_30">
          <div class="page_heading ">
                <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <?php
                        woocommerce_breadcrumb( array(
                            'delimiter'   => '',
                            'wrap_before' => '<ol class="breadcrumb mb-0">',
                            'wrap_after'  => '</ol>',
                            'before'      => '<li class="breadcrumb-item">',
                            'after'       => '</li>',
                            'home'        => 'Home',
                        ) );
                    ?>
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
                                  <?php 
                                    // Dynamic archive title
                                    if (is_search() && !have_posts()) {
                                        //echo 'No products found';
                                    } elseif (is_product_category() || is_product_tag()) {
                                        echo single_term_title('', false); // Category or tag title
                                    } elseif (is_post_type_archive('product')) {
                                        echo 'All Products'; // Default for main shop page
                                    } else {
                                        the_archive_title(); // Fallback
                                    }
                                    ?>
                                </h1>

                                <div class="fw-normal font_size_15 mb-3">
                                  <?php
                                        echo get_field('short_description', 'product_cat_' . get_queried_object_id())
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
                                          <img src="<?php echo get_template_directory_uri();?>/aj_custom/breadcum/icon-returns.png" alt="30-day returns" width="40" height="40">
                                          <span><?php echo RETURN_DAYS;?>-Day Easy Returns</span>
                                        </div>

                                      </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-lg-6 aj-right-cat-top d-none d-lg-block">
                            <img src="<?php echo $image; ?>" alt="Engagement Ring">
                        </div>
                     </div>
                  </div>
               </div>
        </div>

         
    </div>

    <?php
    }else{
    ?>
    <div class="">
        <div class="banner_section">
            <div class="page_heading mb_30">
                <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <?php
                        woocommerce_breadcrumb( array(
                            'delimiter'   => '',
                            'wrap_before' => '<ol class="breadcrumb mb-0">',
                            'wrap_after'  => '</ol>',
                            'before'      => '<li class="breadcrumb-item">',
                            'after'       => '</li>',
                            'home'        => 'Home',
                        ) );
                    ?>
                </nav>
                </div>
            </div>
            <div class="container-fluid">
                <div class="engagement_rings_banner text-center mb_30">
                    <h1 class="h2 fw-600">
                        <?php 
                        // Dynamic archive title
                        if (is_search() && !have_posts()) {
                            //echo 'No products found';
                        } elseif (is_product_category() || is_product_tag()) {
                            echo single_term_title('', false); // Category or tag title
                        } elseif (is_post_type_archive('product')) {
                            echo 'All Products'; // Default for main shop page
                        } else {
                            the_archive_title(); // Fallback
                        }
                        ?>
                    </h1>
                    <?php
                        // Dynamic description
                        if (is_product_category() || is_product_tag()) {
                            $term = get_queried_object();
                            if (!empty($term->description)) {
                                ?>
                                <p class="font_size_15">
                                    <?php echo esc_html($term->description); // Category or tag description ?>
                                </p>
                                <?php 
                            } 
                        }
                    ?>
                    <?php
                     if ( in_array( $category_slug, ['studs', 'hoops', 'drops', 'halo-lab-diamond-earrings', 'cluster', 'diamond-bands', 'anniversary-rings', 'eternity-rings', 'stackable-rings', 'curved-wedding-band', 'solitaire', 'halo', 'lab-diamond-earrings',  'chains', 'diamond-tennis-bracelets', 'diamond-tennis-necklaces'], true ) ) {
                        ?>
                         <div class="benefits-box-parent">
                            <div class="benefits-box">
                                <div class="benefit-item benefit-return">
                                    30 Day Returns
                                </div>

                                <div class="benefit-item benefit-resize">
                                    30 Day Resizing
                                </div>

                                <div class="benefit-item benefit-warranty">
                                    Manufacturer Warranty
                                </div>
                            </div>
                        </div>
                        <?php
                     }
                    ?>
                </div>
            </div>
        </div>
    </div>  
    <?php
    }
    ?>
    
    <?php 
    if($category_slug=='fine-jewelry'){
    ?>


    <!-- start diamond shape section -->
    <div class="container-fluid mb-4">
      <div class="row row-gap-4">
<?php

$categories = [
    [
        "name" => "Earrings",
        "url" => "/lab-diamond-earrings/",
        "image" => get_site_url()."/wp-content/uploads/2022/04/stud-Earings-1.png?id=777806"
    ],
    [
        "name" => "Pendants",
        "url" => "/lab-diamond-pendants/",
        "image" => get_site_url()."/wp-content/uploads/2022/05/Diamond-Pendants.png?id=932501"
    ],
    [
        "name" => "Wedding Rings",
        "url" => "/lab-diamond-wedding-bands/",
        "image" => get_site_url()."/wp-content/uploads/2022/04/wedding-rings.png?id=732540"
    ],
    [
        "name" => "Bracelets",
        "url" => "/lab-diamond-bracelets/",
        "image" => get_site_url()."/wp-content/uploads/2022/04/bracelets.png?id=732535"
    ],
    [
        "name" => "Necklaces",
        "url" => "/lab-diamond-necklaces/",
        "image" => get_site_url()."/wp-content/uploads/2024/06/Necklaces-image.png"
    ],
    [
        "name" => "Chains",
        "url" => "/chains/",
        "image" => get_site_url()."/wp-content/uploads/2024/06/Chains-image.png?id=2057867"
    ]
];

?><?php foreach ($categories as $category): ?>
  <div class="col-md-6">
    <div
      class="fancy_diamond_box"
      style="background-image: url('<?php echo $category['image']; ?>');"
    >
      <h2 class="fw-700 mb_40">
        <?php echo $category['name']; ?>
      </h2>
      <a href="<?php echo $category['url']; ?>" class="btn">Shop Now</a>
    </div>
  </div>
<?php endforeach; ?>

      </div>
    </div>

    <?php 
}else{
    ?>

<div id="inventory-listing-root"></div>
  <?php 
}
    ?>

<?php
if($is_ring_page==true){
    echo '<div class="section pt-5 pb-5">';
    start_engagement_rings();
    echo '</div>';
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
 <div class="section">
    <div class="container-fluid">
        <h2 class="text_black_secondary h1 fw-normal font_literata mb-3 text-center">Explore Other Categories</h2>
        <div class="row row_gap_60">
            <?php
            $categories = [
                [
                    'url'  => '/lab-diamond-wedding-bands/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/wedding-rings.webp',
                    'alt'  => 'wedding_rings',
                    'name' => 'Wedding Rings'
                ],
                [
                    'url'  => '/lab-diamond-necklaces/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/necklaces.webp',
                    'alt'  => 'necklaces',
                    'name' => 'Necklaces'
                ],
                [
                    'url'  => '/lab-diamond-earrings/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/earrings.webp',
                    'alt'  => 'earrings',
                    'name' => 'Earrings'
                ],
                [
                    'url'  =>  '/lab-diamond-bracelets/',
                    'img'  => get_template_directory_uri() . '/aj_custom/home-page/fine-jewelry/bracelets.webp',
                    'alt'  => 'bracelets',
                    'name' => 'Bracelets'
                ],
            ];
            ?>

            <?php foreach ($categories as $category) : ?>
                <div class="col-md-3 col-6">
                    <a href="<?php echo esc_url($category['url']); ?>" class="text-decoration-none">
                        <div class="fine_jewellery">
                            <img src="<?php echo esc_url($category['img']); ?>"
                                alt="<?php echo esc_attr($category['alt']); ?>"
                                loading="lazy">
                            <p class="mb-0"><?php echo esc_html($category['name']); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

 <?php   
}
?>

<?php
}
?>

<?php
$short_description = get_field(
    'short_description_bottom',
    'product_cat_' . get_queried_object_id()
);

$short_description_bottom = get_field(
    'short_description_bottom_description',
    'product_cat_' . get_queried_object_id()
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
$bottom_categories =  get_field('bottom_category', 'product_cat_' . get_queried_object_id()) ;

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
$related_blog_ids = get_field('oc_related_blog', 'product_cat_' . get_queried_object_id());

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
$faqs = get_field('faq', 'product_cat_' . get_queried_object_id());

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