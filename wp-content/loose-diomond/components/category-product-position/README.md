# Category Product Position

wp-admin tool (menu: **Product Position**) to set the order of products per WooCommerce category. That order drives the frontend **Sort by : Best Seller** option.

- Loader: `index.php` (required from `functions.php`)
- API: `api/routes.php`
  - `GET  /wp-json/lgd/v1/product-position/categories`
  - `GET  /wp-json/lgd/v1/product-position/products?term_id=`
  - `POST /wp-json/lgd/v1/product-position/save` `{ term_id, product_ids: [] }`
  - `POST /wp-json/lgd/v1/product-position/reset` `{ term_id }`
- React: `react/` → built to `out/category-product-position` via `vites/vite.category-product-position.config.js`

## How it works
- Order is stored as term meta `_lgd_product_positions` (array of product IDs) on each `product_cat`.
- Admin: select category → drag rows, use ↑ ↓ ⤒ ⤓, or type a position number + Enter → **Save**. "Auto-sort by sales" pre-fills by WooCommerce `total_sales`.
- Frontend: `custom/v1/get-products?orderby=best_seller` (inc/ls-category-product-get-api.php) orders by saved position first, then `total_sales` DESC, then newest. On search pages (no category) it's `total_sales` only.
- "Best Seller" is the default sort in `inventory-listing`; Price Low→High / High→Low still work.
- Products deleted permanently are removed from saved orders automatically. New products added to a category later show after the positioned ones until you save again.
