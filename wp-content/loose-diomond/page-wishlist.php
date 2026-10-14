<?php
/*
Template Name: Wishlist
Description: Wishlist page that reads server-side cookie `ld_wishlist` and renders saved items.
*/
global $text_change;
get_header();

// Read wishlist cookie and decode entries server-side
$raw = isset($_COOKIE['ld_wishlist']) ? $_COOKIE['ld_wishlist'] : '';
$wishlist = array();
if ($raw) {
    // Cookie value was written with encodeURIComponent(JSON.stringify(...)) in JS
    $decoded = rawurldecode($raw);
       // Decode URL encoding
    $decoded = urldecode($_COOKIE['ld_wishlist']);
    // Remove escape slashes
    $json = stripslashes($decoded);

    // Decode JSON
    $parsed = json_decode($json, true);
    if (is_array($parsed)) {
      foreach ($parsed as $entry) {
        if (is_array($entry) && isset($entry['id'])) {
          $rec = array(
            'id' => $entry['id'],
            'type' => (isset($entry['type']) ? $entry['type'] : ''),
          );
          // preserve variation id if present (possible keys: variationId or variation_id)
          if (isset($entry['variationId'])) $rec['variationId'] = $entry['variationId'];
          if (isset($entry['variation_id'])) $rec['variation_id'] = $entry['variation_id'];
          // preserve any meta object
          if (isset($entry['meta'])) $rec['meta'] = $entry['meta'];
          $wishlist[] = $rec;
        } else {
          $wishlist[] = array('id' => $entry, 'type' => '');
        }
      }
    }
}

// Resolve API base (fallback to WP REST namespace)
$api_base = home_url('/wp-json/ls/v1');

// Fetch item details server-side where possible.
// Prefer selecting from inventory tables when `type` indicates local inventory,
// otherwise fall back to WooCommerce product data. Do NOT include or rely
// on inc/ls-addtocart-api.php or inc/ls-addtocart-c-api.php.
$items = array();
global $wpdb;

foreach ( $wishlist as $entry ) {
    $iid  = intval( $entry['id'] );
    $type = isset( $entry['type'] ) ? $entry['type'] : '';
    $data = null;

    // ─── WooCommerce Product ──────────────────────────────────────────────────
    if ( ! $data && function_exists( 'wc_get_product' ) ) {
        $product = wc_get_product( $iid );

        if ( $product ) {

            // Default value
            $is_move_to_cart = false;

            // Check if product belongs to restricted categories
            $restricted_categories = array( 'engagement-rings', 'wedding-rings' );

            $has_restricted_category = has_term(
                $restricted_categories,
                'product_cat',
                $iid
            );

            // Allow direct cart move only for variation products
            // AND not in restricted categories
            if (
                ( isset( $entry['variationId'] ) || isset( $entry['variation_id'] ) ) &&
                ! $has_restricted_category
            ) {
                $is_move_to_cart = true;
            }

            // Resolve price / sale price
            $org_price = null;
            $price     = (int) $product->get_price();

            if ( $product->is_on_sale() ) {
                if ( $product->is_type( 'simple' ) ) {
                    $org_price = (int) $product->get_regular_price();
                    $price     = (int) $product->get_sale_price();
                } elseif ( $product->is_type( 'variable' ) ) {
                    $org_price = (int) $product->get_variation_regular_price();
                    $price     = (int) $product->get_variation_sale_price();
                }
            }

            $img = wp_get_attachment_image_url( $product->get_image_id(), 'full' );

            $data = array(
                'is_move_to_cart' => $is_move_to_cart,
                'id'              => $iid,
                'pid'             => $iid,
                'title'           => $product->get_name() . ' ' . $product->get_sku(),
                'price'           => $price,
                'org_price'       => $org_price,
                'sku'             => $product->get_sku(),
                'image'           => $img ?: get_stylesheet_directory_uri() . '/assets/image/placeholder-84x84.png',
                'imageclass'      => 'w-img-big',
                'viewmore'        => get_permalink( $iid ),
                'viewmore_text'   => 'View Product',
                'stock_status'    => $product->is_in_stock() ? 'in_stock' : 'out_of_stock',
                'stock_qty'       => $product->get_stock_quantity(),
            );
        }
    }

    // ─── Merge & collect ─────────────────────────────────────────────────────
    $items[] = array_merge( $entry, array( 'data' => $data ) );
}
?>

<div id="ls-wishlist-page" > 
  <div class="banner_section">
    <div class="py_60">
      <div class="container-fluid">
       
        <?php if (empty($items)) : ?>
         <div class="container-fluid d-flex flex-column align-items-center mt-4 wishlist-empty"><h4 class="text-uppercase fw-700">Your wishlist is empty.</h4><p class="text-center">Add items that you like to your wishlist. Review them anytime and easily move them to the bag</p><img alt="empty_wishlist" width="300" height="300" decoding="async" data-nimg="1" class="img-fluid" src="<?php echo get_stylesheet_directory_uri() . '/aj_custom/whishlist/empty-wishlist.png';?>"><a href="/lab-diamonds/" class="btn common_black_btn outline-btn py-2 px-4 fw-400 text-uppercase ">CONTINUE SHOPPING</a></div>


        <?php else : ?>
           <h1 class="mb-4">My Wishlist</h1>
          <div class="row">
            <?php foreach ($items as $entry): 

              $data = $entry['data']; 
              $id = esc_attr($entry['id']); 
              $type = esc_html($entry['type']); 
              
              $title = $data && !empty($data['title']) ? esc_html($data['title']) : ($data && !empty($data['shape_title']) ? esc_html($data['shape_title']) : "Item #{$id}"); 
              $image = $data && !empty($data['image']) ? esc_url($data['image']) : esc_url(get_stylesheet_directory_uri() . '/assets/image/placeholder-84x84.png'); 
              $price = $data && !empty($data['price']) ? esc_html('$' . $data['price']) : ''; 
              $org_price = $data && !empty($data['org_price']) ? esc_html('$' . $data['org_price']) : null; 
              $sku = $data && !empty($data['sku']) ? esc_html($data['sku']) : ''; 
              $product_url = (isset($data['viewmore']) && $data['viewmore']) ? esc_url($data['viewmore']) : '#';
              $is_cart_available = isset($data['is_move_to_cart']) ? $data['is_move_to_cart'] : false;
              $product_url_text = $is_cart_available ? 'MOVE TO CART' : $data['viewmore_text'];
            ?>
               <div class="col-md-3 col-6 mb-4">
                <div class="wishlist-card ">
                  <!-- Remove -->
                  <?php $remove_attrs = ' data-id="' . esc_attr($id) . '"';
                        if (isset($entry['variationId'])) $remove_attrs .= ' data-variation-id="' . esc_attr($entry['variationId']) . '"';
                        if (isset($entry['variation_id'])) $remove_attrs .= ' data-variation-id="' . esc_attr($entry['variation_id']) . '"';
                  ?>
                  <button class="remove-btn ls-wish-remove" <?php echo $remove_attrs; ?>>
                    <img src="<?php echo get_stylesheet_directory_uri() . '/aj_custom/whishlist/close-icon.svg';?>">
                  </button>

                  <?php
                  if($data['express_shipping']==true){
                    echo '<img src="'.get_template_directory_uri().'/aj_custom/inventory/delivery.svg" class="whi_express"/>';
                  }
                  if($data['is_overnight']==true){
                    echo '<img src="'.get_template_directory_uri().'/aj_custom/inventory/space-rocket.svg" class="whi_express"/>';
                  }
                  
                  ?>
                  <!-- Product Image -->
                  <div class="product-img-wish">
                    <a href="<?php echo $product_url;?>">
                      <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" class="img-fluid <?php echo $data['imageclass'];?>">
                      
                    </a>
                    <?php if(isset($data['stock_status']) && $data['stock_status'] != 'in_stock'): ?>
                        <div class="stock-status">
                          
                            <span class="text-danger">Out of Stock</span>
                        </div>
                      <?php endif; ?>
                  </div>
                  <!-- Product Info -->
                  <div class="product-content">
                    <h6 class="product-title">
                      <?php echo html_entity_decode($title); ?>
                    </h6>
                    <?php if($price): ?>
                      <div class="ls_prod_price">
                        
                        <span class="ls_prod_sale_price">
                          <?php echo $price; ?>
                        </span>
                        <?php if($org_price): ?>
                          <span class="ls_prod_rglr_price me-1">
                            <?php echo $org_price; ?>
                          </span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <!-- Action Button -->
                  <?php if ($product_url) : ?>
                    <?php
                      // expose variation and meta as data attributes if present in the wishlist entry
                      $data_attrs = '';
                      if (isset($entry['variationId'])) {
                        $data_attrs .= ' data-variation-id="' . esc_attr($entry['variationId']) . '"';
                      } elseif (isset($entry['variation_id'])) {
                        $data_attrs .= ' data-variation-id="' . esc_attr($entry['variation_id']) . '"';
                      }
                      if (isset($entry['meta']) && is_array($entry['meta'])) {
                        // store selected attributes JSON-encoded and URL-encoded to be safe in HTML
                        $meta_json = rawurlencode( wp_json_encode( $entry['meta'] ) );
                        $data_attrs .= ' data-meta="' . esc_attr( $meta_json ) . '"';
                      }
                    ?>
                    <a href="<?php echo $product_url; ?>" class="btn btn-dark w-100 rounded-0 wishlist-btn <?php echo $is_cart_available ? 'wishlist-move-to-cart' : ''; ?>" data-id="<?php echo $id; ?>"<?php echo $data_attrs; ?>>
                      <?php echo $product_url_text; ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
  .wishlist-card{
    border:1px solid #ddd;
    background:#fff;
    position:relative;
    display:flex;
    flex-direction:column;
    height:100%;
    border: 1px solid rgba(0, 0, 0, 0.1019607843);
    border-radius: 5px;
  }

  .product-img-wish{
    background:#f3f3f3;
    padding:0;
    text-align:center;
  }

  .product-img-wish img{
   /* max-height: 265px;
    min-height: 265px;*/
    object-fit:contain;
  }

  .product-content{
    padding:15px;
    min-height: 100px;
    max-height: 100px;
  }

  .product-title{
    font-size:14px;
    line-height:1.5;
    margin-bottom: 10px;
    min-height: 42px;
  }

  .ls_prod_rglr_price {
    font-size: 13px;
    color: var(--black-secondary);
    text-decoration: line-through;
  }

  .ls_prod_sale_price {
    font-size: 15px;
    font-weight: 500;
  }

  .wishlist-btn {
    font-size:13px;
    letter-spacing:1px;
    padding:12px;
    border-bottom-left-radius: 5px !important;
    border-bottom-right-radius: 5px !important;
  }

  .remove-btn{
    position:absolute;
    top:10px;
    right:12px;
    border:none;
    background:none;
    font-size:22px;
    cursor:pointer;
    max-width: 10px;
  }
</style>

<script>
// Minimal client-side helper to remove an item and reload the page
(function(){
  function readCookie(name){
    var m = document.cookie.split('; ').find(function(c){ return c.indexOf(name+'=')===0; });
    return m ? decodeURIComponent(m.split('=')[1]||'') : null;
  }
  function writeCookie(name, value, days){
    var d = new Date(); d.setTime(d.getTime() + (days||365)*24*60*60*1000);
    document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/';
  }
  function parseWishlist(){
    var raw = readCookie('ld_wishlist');
    if(!raw) return [];
    try{ var parsed = JSON.parse(raw); return Array.isArray(parsed) ? parsed : []; }catch(e){ return []; }
  }
  function writeWishlist(arr){ writeCookie('ld_wishlist', JSON.stringify(arr), 365); }

  document.addEventListener('click', function(e){
    var btn = e.target.closest('.ls-wish-remove');
    if (!btn) return;
    e.preventDefault();
    var id = btn.getAttribute('data-id');
    if (!id) return;
    var variation_id = btn.getAttribute('data-variation-id') || '';
    var list = parseWishlist();
    var next = list.filter(function(x){
      try {
        var obj = (typeof x === 'object') ? x : {id: x};
        if (String(obj.id) !== String(id)) return true; // keep other ids
        // if variation specified on button, only remove entries that match variation too
        if (variation_id) {
          var objVar = obj.variationId || obj.variation_id || (obj.meta && obj.meta.selected && obj.meta.selected.variation_id) || '';
          return String(objVar) !== String(variation_id);
        }
        // no variation specified, remove all entries matching id
        return false;
      } catch(err){ return true; }
    });
    writeWishlist(next);
    location.reload();
  });
})();
</script>

<script>
(function(){
  var wishlistMoveToCart = document.querySelectorAll('.wishlist-move-to-cart');
  wishlistMoveToCart.forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      var product_id = btn.getAttribute('data-id');
      var variation_id = btn.getAttribute('data-variation-id') || '';
      var meta_raw = btn.getAttribute('data-meta') || '';
      var variation_attrs = '';
      console.log(meta_raw);
      if (meta_raw) {
        try {
          var meta = JSON.parse(decodeURIComponent(meta_raw));
          // meta.selected may contain attribute selections from single-product page
          if (meta && meta.selected) {
            variation_attrs = JSON.stringify(meta.selected);
          }
        } catch (err) {
          variation_attrs = '';
        }
      }

      var body = new URLSearchParams({ action: 'wishlist_move_to_cart', product_id: product_id });
      if (variation_id) body.append('variation_id', variation_id);
      if (variation_attrs) body.append('variation_attrs', variation_attrs);

      fetch("<?php echo  admin_url( 'admin-ajax.php' );?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (typeof wc_cart_fragments_params !== 'undefined') {
            jQuery(document.body).trigger('wc_fragment_refresh');
          }
          window.location.href = "/cart";
          btn.closest('.col-md-3').remove();
        } else {
          alert(data.message || 'Unable to add product to cart');
        }
      });
    });
  });
})();
</script>

<?php
get_footer();
?>
