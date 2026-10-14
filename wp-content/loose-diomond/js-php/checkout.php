<?php
add_action('wp_footer', function () {
    if ( is_checkout() ) {
        ?>
        <script>
            console.log('Checkout page');
        </script>
        <?php
    }
});