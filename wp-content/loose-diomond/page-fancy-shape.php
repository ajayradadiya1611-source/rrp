<?php
/**
 * Template Name: Fancy Shape
 *
 * Six fancy-shape melee category cards. Images are the product images in
 * rrp-img/<slug>/ (same files as the Melee Diamonds For Sale page).
 */

get_header();

$cards = [
    [ 'Princess Cut<br>Diamonds',      'princess-shape-natural-diamonds',   'princess-cut-product-2.png' ],
    [ 'Marquise Shape<br>Diamonds',    'marquise-shape-natural-diamonds',   'Maquesh-Diamond.png' ],
    [ 'Pear Shape<br>Diamonds',        'pear-shape-natural-diamonds',       'pear-shape.png' ],
    [ 'Baguette Cut<br>Diamonds',      'baguette-shape-natural-diamond',    'baggutu-shape-diamond-.png' ],
    [ 'Emerald Cut<br>Diamonds',       'emerald-cut-natural-diamonds',      'emerald-shape.png' ],
    [ 'Old European<br>Cut Diamonds',  'old-european-cut-natural-diamonds', 'natural-old-cut-diamonds.jpg' ],
];

// ACF repeater "shape_cards" (defaults above are used when it is empty)
$acf_cards = function_exists( 'get_field' ) ? get_field( 'shape_cards' ) : [];
$items     = [];
if ( ! empty( $acf_cards ) && is_array( $acf_cards ) ) {
    foreach ( $acf_cards as $row ) {
        $items[] = [
            'title' => isset( $row['card_title'] ) ? $row['card_title'] : '',
            'url'   => ! empty( $row['card_link'] ) ? $row['card_link'] : '#',
            'src'   => ! empty( $row['card_image']['url'] ) ? $row['card_image']['url'] : '',
        ];
    }
} else {
    foreach ( $cards as $card ) {
        list( $title, $slug, $file ) = $card;
        $local = get_template_directory() . '/rrp-img/' . $slug . '/' . $file;
        $src   = file_exists( $local ) ? get_template_directory_uri() . '/rrp-img/' . $slug . '/' . $file : '';
        if ( ! $src && function_exists( 'wc_get_product' ) ) {
            $p = get_page_by_path( $slug, OBJECT, 'product' );
            if ( $p ) {
                $src = (string) wp_get_attachment_image_url( get_post_thumbnail_id( $p->ID ), 'woocommerce_thumbnail' );
            }
        }
        $items[] = [ 'title' => $title, 'url' => home_url( '/product/' . $slug . '/' ), 'src' => $src ];
    }
}

// Heading + description: normal WP page title and page content.
$page_title = get_the_title();
$page_desc  = '';
while ( have_posts() ) {
    the_post();
    $page_desc = apply_filters( 'the_content', get_the_content() );
}
?>
<main id="main" class="fancy-shapes">
    <div class="banner_section">
        <div class="page_heading mb_30">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                        <li class="breadcrumb-item"><?php echo esc_html( get_the_title() ); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <h1 class="fancy-shapes__title"><?php echo esc_html( $page_title ); ?></h1>
        <?php if ( trim( wp_strip_all_tags( $page_desc ) ) !== '' ) : ?>
        <div class="fancy-shapes__desc"><?php echo $page_desc; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
        <?php endif; ?>

        <div class="fancy-shapes__grid">
            <?php foreach ( $items as $item ) :
                $title = $item['title']; $url = $item['url']; $src = $item['src'];
                ?>
                <div class="fancy-shapes__card">
                    <div class="fancy-shapes__txt">
                        <h2><?php echo wp_kses( $title, [ 'br' => [] ] ); ?></h2>
                        <a class="fancy-shapes__btn" href="<?php echo esc_url( $url ); ?>">Shop Now</a>
                    </div>
                    <?php if ( $src ) : ?>
                        <img class="fancy-shapes__img" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( str_replace( '<br>', ' ', $title ) ) ); ?>" loading="lazy">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>
