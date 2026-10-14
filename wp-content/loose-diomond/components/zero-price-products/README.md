# Zero Price Products

wp-admin tool (menu: **Zero Price Products**) that lists all WooCommerce products whose `_price` is 0, empty or missing, and deletes them permanently — single (trash icon) or bulk (checkbox + "Delete Selected", sequential with progress + cancel).

- Loader: `index.php` (required from `functions.php`)
- API: `api/routes.php`
  - `GET  /wp-json/lgd/v1/zero-price-products`
  - `POST /wp-json/lgd/v1/zero-price-products/delete` `{ product_id }`
  - `POST /wp-json/lgd/v1/zero-price-products/delete-bulk` `{ product_ids: [] }`
- React: `react/` → built to `out/zero-price-products` via `vites/vite.zero-price-products.config.js`

Safety: requires `manage_woocommerce`; price is re-checked server-side before delete; variations are skipped in listing (variable parents delete their children). Inventory rows are not modified.
