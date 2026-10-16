<?php

global $is_festival_banner, $is_discount_enabled, $discount_object, $discount_coupon_code, $direct_discount, $inventory_banner, $inventory_banner_mob;

$is_festival_banner    = get_field('enable_festival_banner', 'option');
$is_discount_enabled   = get_field('enable_festival_discount', 'option');
$discount_object       = get_field('festival_coupon_code', 'option');
$discount_coupon_code  = !empty($discount_object) && isset($discount_object->post_title) ? $discount_object->post_title : '';
$direct_discount       = !empty(get_field('festival_coupon_discount', 'option')) ? get_field('festival_coupon_discount', 'option') : 0;
$inventory_banner      = get_field('festival_inventory_banner', 'option');
$inventory_banner_mob  = get_field('festival_inventory_banner_mob', 'option');



add_filter( 'woocommerce_package_rates', 'ls_shpng_woocommerce_package_rates', 100, 2 );
function ls_shpng_woocommerce_package_rates( $rates, $package ) { 
    
    $sub_total = WC()->cart->get_subtotal();  

    // //if cart has only normal product and subtotal is less then 500 only show below order 500 method
    // if($sub_total < 500){
    //     unset( $rates['free_shipping:1'] );
    //     //unset( $rates['flat_rate:5'] );
    // }
    // else{
    //     unset( $rates['flat_rate:3'] );
    // }  
    // return $rates;
    $is_free_shipping = false;
    $applied_coupons = WC()->cart->get_applied_coupons();
       foreach( $applied_coupons as $coupon_code ){
       $coupon = new WC_Coupon($coupon_code);
        if($coupon->get_free_shipping()){   //if coupon has free shipping option on
           $is_free_shipping = true;

        }
    }
    if(!$is_free_shipping){
       if($sub_total <= 500){

        $items_count = count(WC()->cart->get_cart());
        $items = WC()->cart->get_cart();

            $cnt_cp = 0;

             $arr_custom_product = array('849973','849972','849971','849970','849969','846211','833062','832621','832619');
            if($items_count >= 1){
                foreach($items as $item => $values) { 
                    
                    $product_id = $values['product_id'];
                    
                    if(in_array($product_id, $arr_custom_product)){
                        $cnt_cp++ ;
                    }
                }
            }

            if($cnt_cp==$items_count){
               unset( $rates['flat_rate:3'] );
             }else{
                unset( $rates['free_shipping:5'] );
            }
        }
        else{
            unset( $rates['flat_rate:3']);
        }
    }
  
    return $rates; 
}