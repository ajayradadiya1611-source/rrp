<?php


// Function to add product to WC from inventory
function add_product_to_wc($iid, $categories = array()){
    global $wpdb;
    
    $r = $wpdb->get_row("SELECT * from ls_inventory where id=$iid");
    if (!$r) {
        return false; // Inventory item not found
    }
    $shape = $shape_img = $shape_title = $r->shape;
    $shape_cut_ttl = $r->shape_title;
    $carat = $r->carat;
    $color = $r->color;
    $clarity = $r->clarity;
    $sku = $r->sku;
    if(WEBSITE_TYPE=='WHOLESALE'){
        $price = (int)$r->price;
        $org_price = (int)$r->org_price;
    }else{
        $price = (int) round(($r->price / (100 - 30)) * 100);
        $org_price = (int) round(($r->price / (100 - 30)) * 100);
    }
    if ($price <= 0) {
        return false;
    }
    $byskuid = wc_get_product_id_by_sku($sku);
    if ($byskuid) {
        wp_set_object_terms($byskuid, 'white-diamond', 'product_tag', true);
        if($r->sale==1){
            wp_set_object_terms($byskuid, 'sale', 'product_tag', true); 
            wp_set_object_terms($byskuid, array('ready-to-ship', 'ready-to-ship-lab-diamonds'), 'product_cat', true);   
        }
        /*if (!empty($categories)) {
            wp_set_object_terms($byskuid, $categories, 'product_cat', true);
        }*/
        // Update inventory table
        $pid_update = "UPDATE `ls_inventory` SET `pid`='$byskuid' WHERE `id`='$iid' ";
        $wpdb->query($pid_update); 
        return $byskuid;
    }
    if(($clarity != "-" && $clarity != "") &&  ($color != "-" && $color != "")){
        $num_color = get_attrkey_val('color', $color);
        $num_clarity = get_attrkey_val('clarity', $clarity);
    }
    // Build product name using the full record
    $pname = ls_build_pname($r);
    $pname = $pname . ' Lab Grown Diamond';
    $p_title = $pname.' '.$sku;
    $p_slug = ls_fixForUri_iid($p_title);
    // Create product using WooCommerce API
    $product = new WC_Product_Simple();
    $product->set_name($pname);
    $product->set_slug($p_slug);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_sku($sku);
    $product->set_manage_stock(true);
    $product->set_stock_status('instock');
    $product->set_stock_quantity(1);
    $product->set_sold_individually(true);
    if (!isset($price) || $price === '' || (float)$price <= 0) {
    }
    // Set prices
    if(isset($org_price) && $org_price!='' && $org_price!=0){
        $product->set_regular_price($org_price);
        $product->set_sale_price($price);
        $product->set_price($price);
    } else {
        $product->set_regular_price($price);
        $product->set_price($price);
    }
    // Set featured image
    global $inv_shape_imgids;
    $shape_img = str_replace("_", " ", $shape_img);
    $thumbnail_id = $inv_shape_imgids[strtolower($shape_img)] ?? null;
    if($thumbnail_id == '' || $thumbnail_id== null){
        $thumbnail_id = $inv_shape_imgids[strtolower($shape_cut_ttl)] ?? null;
    }
    if ($thumbnail_id) {
        $product->set_image_id($thumbnail_id);
    }
    // Save the product
    $post_id = $product->save();
    // Set product type
    wp_set_object_terms($post_id, 'simple', 'product_type');
    // Add custom meta
    update_post_meta($post_id, '_aj_diamond', $r);
    update_post_meta($post_id, 'c_type', 1);
    update_post_meta($post_id, '_invid', $iid);
    update_post_meta($post_id, 'rank_math_robots', array(
        'noindex',
        'nofollow'
    ));
    wp_set_object_terms($post_id, 'white-diamond', 'product_tag', true);
    if($r->sale==1){
        wp_set_object_terms($post_id, 'sale', 'product_tag', true); 
        wp_set_object_terms($post_id, array('ready-to-ship', 'ready-to-ship-lab-diamonds'), 'product_cat', true);     
    }
    // Update inventory table
    $pid_update = "UPDATE `ls_inventory` SET `pid`='$post_id' WHERE `id`='$iid' ";
    $wpdb->query($pid_update); 
    return $post_id;
}
// Helper function, assuming it's defined elsewhere
// Function to retrive attribute name value from numaric
if (!function_exists('get_attrkey_val')) {
    function get_attrkey_val($type, $val){
        global ${'inv_' . $type};
        $array_to_fetch = ${'inv_' . $type};
        $key_value = $val;

        foreach ($array_to_fetch as $key => $value) {
            if($value == $val){
                $key_value = $key;
            }
        }

        return $key_value;
    }
}

if (!function_exists('ls_fixForUri_iid')) {
    function ls_fixForUri_iid($str) {
        return sanitize_title($str);
    }
}

/**
 * Shape -> dedicated ring-setting page URL map.
 *
 * IMPORTANT: Replace the placeholder URLs below with your real,
 * dedicated per-shape ring-setting page URLs. Keys must be lowercase
 * and match how `$shape` comes out of `ls_inventory` (e.g. "Round",
 * "Oval", "Cushion" etc — the function lowercases before lookup).
 *
 * If a shape isn't found in this map, it falls back to the global
 * $ring_setting URL (your existing generic setting page).
 */
if (!function_exists('ls_get_ring_setting_url_by_shape')) {
    /**
     * Any inventory shape (base or named cut) -> build-a-ring URL with the
     * exact shape slug, e.g.
     *   Round / Portuguese          -> /build-a-ring/?shape=round-brilliant-cut
     *   Princess / Square / Carre   -> /build-a-ring/?shape=princess-cut
     *   Cushion / Flanders / Ashoka / Capsule -> /build-a-ring/?shape=cushion-cut
     *   Oval / Moval                -> /build-a-ring/?shape=oval-cut
     *   Radiant / Crisscut          -> /build-a-ring/?shape=radiant-cut
     */
    function ls_get_ring_setting_url_by_shape($shape) {
        global $ring_setting, $ls_ring_shape_slugs;

        $base_url = home_url('/build-a-ring/');
        $base     = function_exists('ls_get_base_shape_name') ? ls_get_base_shape_name($shape) : ucfirst(strtolower(trim((string) $shape)));

        if ($base !== '' && isset($ls_ring_shape_slugs[$base])) {
            // Build manually so the slug stays exact (no encoding changes).
            return $base_url . '?shape=' . $ls_ring_shape_slugs[$base];
        }

        // Shape has no standard setting -> generic setting page
        return $ring_setting ?? $base_url;
    }
}

add_action('rest_api_init', function () {
    register_rest_route('ls/v1', '/add-to-cart', [
        'methods'  => 'POST',
        'callback' => 'ls_rest_add_to_cart',
        'permission_callback' => '__return_true', // allow guests
    ]);
});

// Also expose the same functionality via admin-ajax.php for Ajax calls from the frontend React app
add_action('wp_ajax_ls_add_to_cart', 'ls_ajax_add_to_cart');
add_action('wp_ajax_nopriv_ls_add_to_cart', 'ls_ajax_add_to_cart');

function ls_ajax_add_to_cart() {
    // Verify nonce sent from the frontend
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'ls_add_to_cart_nonce') ) {
        //wp_send_json_error('Invalid nonce', 403);
    }

    // Build a WP_REST_Request from the POST data so we can reuse the existing REST handler
    $request = new WP_REST_Request('POST');
    foreach ($_POST as $k => $v) {
        if ($k === 'nonce' || $k === 'action') continue;
        $request->set_param($k, $v);
    }

    $result = ls_rest_add_to_cart($request);

    if ( is_wp_error($result) ) {
        wp_send_json_error($result->get_error_message(), 500);
    }

    wp_send_json_success($result);
}

function ls_rest_add_to_cart(WP_REST_Request $request) {

    if ( ! class_exists('WooCommerce') ) {
        return new WP_Error('woocommerce_missing', 'WooCommerce not active', ['status' => 500]);
    }

    // 🔥 REQUIRED FOR REST
    if ( null === WC()->session ) {
        WC()->initialize_session();
    }

    if ( null === WC()->cart ) {
        WC()->initialize_cart();
    }

    // 🔥 REQUIRED FOR REST — Initialize Customer
    if ( null === WC()->customer ) {
        WC()->customer = new WC_Customer( get_current_user_id(), true );
    }

    if ( ! class_exists('WC_Cart') ) {
        return new WP_Error('no_cart', 'WooCommerce cart not available', ['status' => 500]);
    }

    $status = $is_ringbuilder = false;
    global $wpdb, $woocommerce, $ring_preview, $ringdmnd_incart_msg, $ring_setting, $inv_shape; //Ring builder

    $pid = absint($request->get_param('product_id'));
    $iid = absint($request->get_param('iid'));
    $quantity = absint($request->get_param('quantity')) ?: 1;

    //if($pid==0 && $iid > 0)
    //{
        $pid = add_product_to_wc($iid);
   // }

    //Ring builder
    $is_ringbuilder_process = $request->get_param('type');

    $cart_url = wc_get_cart_url();
    $content = '';

    if($pid!=''){
        global $product; 
        $table = 'ls_inventory';
        $record = $wpdb->get_row("SELECT * FROM ".$table." WHERE `id` = ". $iid ."");

        $shape = $record->shape;

        $price_in_inv = $record->price;
        $product_l = wc_get_product( $pid );
        $price_in_wc = $price_sale_wc = $product_l->get_sale_price();
        if ($price_in_inv != $price_in_wc) {
            $org_price = $record->org_price;

            if (WEBSITE_TYPE == 'WHOLESALE') {
                if (!empty($org_price) && (float) $org_price > (float) $price_in_inv) {
                    $product_l->set_regular_price($org_price);
                    $product_l->set_sale_price($price_in_inv);
                } else {
                    $product_l->set_regular_price($price_in_inv);
                    $product_l->set_sale_price('');
                }
            } else {
                $rprice = round(($record->price / (100 - 30)) * 100);
                $product_l->set_regular_price($rprice);
                $product_l->set_sale_price('');
            }

            $product_l->save(); // syncs _price and clears transients
        }

        if (!$product_l || (float) $product_l->get_regular_price() <= 0) {
            return new WP_Error(
                'invalid_product_price',
                'Product price is 0 or product not found',
                ['status' => 500]
            );
        }
        if($price_sale_wc==''){
            $price_in_wc = $product_l->get_regular_price();
        }

        if ($record->status == 0 ) {
            $product_l->set_manage_stock(true);
            $product_l->set_stock_status('instock');
            $product_l->set_stock_quantity(1);
            $product_l->set_sold_individually(true);
            $product_l->save();
        }
        
        
        $dp_shape_id = ls_resolve_dp_shape_id($shape);

        if($is_ringbuilder_process!='' && $is_ringbuilder_process=='ringbuilder'){ //Ring builder

            $is_ringbuilder = true;

            $sp_id = $sp_vid = $stng_dmnd_shape = $setting_dshape_name = null;

            $ls_rbsetting_session = isset($_SESSION['ls_ring_builder']['setting'])
                ? $_SESSION['ls_ring_builder']['setting']
                : array();

            if(!empty($ls_rbsetting_session)){

                $sp_vid = $ls_rbsetting_session['sp_vid'];//main product id for validation
                $sp_id  = $ls_rbsetting_session['sp_id']; 

                $stng_dmnd_shape = get_post_meta($sp_id, 's_dtype', true);
                $setting_dshape_name = array_search($stng_dmnd_shape, $inv_shape);
            }

            $is_setting_product = is_setting_product($sp_id);

            $diamond_already_in_cart = false;
            $diamond_in_ring_in_cart = false;
            
            foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {

                $_product = $values['data'];

                //cart validation
                if( $pid == $_product->id ) {
                    $diamond_already_in_cart = true;
                    if ( ls_is_diamond_in_cart_ring( $pid ) ) {
                        $diamond_in_ring_in_cart = true; // already set in a ring
                    }
                }

            }

            // Whether the diamond is already sitting in the cart (e.g.
            // added earlier via "Add To Bag") or not, clicking
            // "Add To Ring" should select it for the ring builder
            // either way — this block only stores the selection in
            // session, it never adds to the WooCommerce cart itself,
            // so there's no risk of double-adding.
            if(!$diamond_in_ring_in_cart){
               // echo "dp_shape_id ".$dp_shape_id." stng_dmnd_shape ".$stng_dmnd_shape;
                //Validation for Diamnod shape, if selected setting product thene need diamond shape same as setting product
                if(!empty($ls_rbsetting_session) && $dp_shape_id!=$stng_dmnd_shape){
                    $content .= 'Please Choose '.$setting_dshape_name.' shape Diamond';
                }
                else{

                    unset($_SESSION['ls_ring_builder']['diamond']); //unset diamond session before store new value

                    $diamond_data = array( 'd_pid'=>$pid, 'd_shape'=>$dp_shape_id );
                    $_SESSION['ls_ring_builder']['diamond'] = $diamond_data; //store data in session

                    // Remember which item was chosen first so the breadcrumb can
                    // keep showing it first, regardless of selection state later.
                    if (empty($_SESSION['ls_ring_builder']['started_with'])) {
                        $_SESSION['ls_ring_builder']['started_with'] = 'diamond';
                    }

                    $status = true;

                    // Loose diamond in cart: selection continues silently; it will be
                    // replaced by the ring on final Add To Bag (ls_rb_addtobag).

                    if( $sp_id!='' && $sp_vid!='' && $is_setting_product['is_setting'] ) {
                        // Setting was already selected -> go straight to preview
                        $redirect_url = $ring_preview;
                    } else {
                        // No setting selected yet -> send to the shape-specific setting page
                        $redirect_url = ls_get_ring_setting_url_by_shape($shape);
                    }
                }
                
            }
            else{
                // Diamond is already set in a ring in the cart -> block
                $content .= $ringdmnd_incart_msg;
                $redirect_url = $cart_url;
            }

        }
        else{
            //check product is already in cart
            $already_in_cart = false;

            foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {

                $_product = $values['data'];

                if( $pid == $_product->id ) {
                    $already_in_cart = true;
                }
            }

            if(!$already_in_cart){
                
                $cart_item_data = array();
                $cart_item_data['diamond_data']=$record;
                $cart_item_data = ls_prepare_cart_item_data($cart_item_data, $pid);
                WC()->cart->add_to_cart( $pid, $quantity, 0, array(), $cart_item_data );
                $status = true;
            }
            else{
                $content = 'This Product is already in your Cart';
            }

            $redirect_url = $cart_url;

        }
    }
    
    return [
        'status' => $status,
        'redirect' => $redirect_url ?? $cart_url,
        'content' => $content,
        'pid' => $pid,
        'is_ringbuilder' => $is_ringbuilder,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_url' => wc_get_cart_url(),
    ];
}