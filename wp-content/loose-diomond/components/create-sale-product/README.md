# create-sale-product

```
create-sale-product/
├── index.php        ← the ONLY file functions.php needs to require
├── api/
│   └── routes.php   ← REST API (registers /wp-json/lgd/v1/*)
└── react/
    ├── index.html
    ├── package.json
    ├── dist/         ← created by `npm run build`
    └── src/
        ├── main.jsx
        ├── App.jsx
        ├── App.scss
        ├── api.js
        └── components/
            └── CreateSaleProduct/
                ├── index.jsx
                ├── SaleProductPanel.jsx   (shared UI/logic)
                ├── ColorSaleProduct.jsx   (type: color-diamond → ls_inventory_c)
                └── WhiteSaleProduct.jsx   (type: white-diamond → ls_inventory)
```

## What it does

Lists every inventory row flagged `sale = 1` in `ls_inventory` (white
diamonds) and `ls_inventory_c` (color diamonds). For each row it checks
whether a WooCommerce product already exists for it (`pid` set **and**
`wc_get_product()` actually resolves it — a stale `pid` pointing at a
deleted product is treated as "not created"). Admin can create the
missing product for a single row or a multi-select batch.

Product creation does **not** duplicate any logic — it calls the same
helpers the storefront "Add To Cart" flow already uses to lazily create
a product the first time a diamond is added to cart:

- `add_product_to_wc()`   — `inc/ls-addtocart-api.php`   (white / `ls_inventory`)
- `add_product_to_wc_c()` — `inc/ls-addtocart-c-api.php` (color / `ls_inventory_c`)

Both already handle: skipping creation and linking back via SKU if a
product with that SKU exists, building the `WC_Product_Simple` with
price/stock/image/tags/meta, and writing the new post ID back onto the
inventory row's `pid` column.

## 1. functions.php — one line

```php
require_once get_template_directory() . '/components/create-sale-product/index.php';
```

`index.php` internally requires `api/routes.php` and hooks up the
wp-admin page + script enqueue.

## 2. Build the React app

```bash
cd components/create-sale-product/react
npm install
npm run build
```

Outputs to `components/create-sale-product/react/dist/index.js`, using
`vites/vite.create-sale-product.config.js` at the theme root (same
build pattern as `vite.delete-out-of-stock-inventory.config.js`). Also
wired into the root `package.json` `build` script.

## 3. Where it shows up

A new wp-admin menu item, "Create Sale Product" (capability:
`manage_woocommerce`), renders the React app.

## API

- `GET  /wp-json/lgd/v1/sale-inventory?type=color-diamond|white-diamond`
  → lists `sale = 1` rows for that table, each with `pid_status`:
    `created` (pid set and product live) or `not-created` (pid empty,
    or pid set but the product no longer exists).

- `POST /wp-json/lgd/v1/create-sale-product`
  body: `{ "type": "color-diamond"|"white-diamond", "inventory_id": 123 }`
  → creates the product for that inventory row if it doesn't already
    have a live one; returns `{ inventory_id, product_id, created,
    already_existed }`.

- `POST /wp-json/lgd/v1/create-sale-product-bulk`
  body: `{ "type": "color-diamond"|"white-diamond", "inventory_ids": [123, 456] }`
  → same, applied to a list; one failure doesn't stop the rest.

Color and white diamond stay fully separate code paths end to end:
separate React component, separate table, separate creation helper —
same shared REST namespace (`lgd/v1`) only.
