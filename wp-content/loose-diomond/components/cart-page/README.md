# Cart Page (React)

Replaces the jQuery + PHP markup in `woocommerce/cart/cart.php` with a React app.

## Install
1. Copy `woocommerce/cart/cart.php`, `components/cart-page/`, `out/cart-page/` into the theme
   (and the patched `components/ring-extra-features/react/src/main.jsx`, then rebuild that app).
2. In `functions.php`, next to the other components:
   require_once get_template_directory() . '/components/cart-page/index.php';

`out/cart-page/` is already built. To rebuild after edits:
   cd components/cart-page/react && npm install && npm run build

## How it works
- `cart.php` prints `#ls-cart-root` + `window.LS_CART_INITIAL` (first paint, no AJAX).
- `api/cart-data.php` → `lgd_cart_page_get_data()` builds all state (prices as wc_price HTML).
- After every action the app re-fetches `lgd_cart_page_data` — no page reloads.
- Reused handlers: custom_update_cart_qty, custom_apply_coupon, ls_save_engraving,
  ls_update_ringsize, ls_update_settingsize, ls_ref_remove_upgrade, ls_change_rb.
- New handlers: lgd_cart_page_data, lgd_cart_remove_item, lgd_cart_remove_coupon.
- Modals (engraving, unboxing video) are React — no Bootstrap JS needed.
- "Edit upgrades" widget: ring-extra-features main.jsx now exposes `window.lsRefMountAll`
  and listens for `ls:ref-mount` so it mounts into React-rendered divs.
- Emptying the cart reloads the page so WooCommerce shows cart-empty.php.
