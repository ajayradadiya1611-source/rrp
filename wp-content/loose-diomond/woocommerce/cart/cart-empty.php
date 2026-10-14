<?php
defined( 'ABSPATH' ) || exit;

// do_action( 'woocommerce_cart_is_empty' );
?>


    
        <div class=" text-center cart-empty">

            <h2 class=" mb-2">Your bag is currently empty</h2>
            <p class="">
                You have not added any items in your shopping bag
            </p>

            <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
                <a href="<?php echo esc_url( get_site_url().'/inventory' ); ?>"
                   class="btn btn-dark  text-uppercase active">
                    Return to shop
                </a>
            <?php endif; ?>

        </div>
 
