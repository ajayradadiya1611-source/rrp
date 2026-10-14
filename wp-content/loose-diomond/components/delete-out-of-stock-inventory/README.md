# delete-out-of-stock-inventory

```
delete-out-of-stock-inventory/
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
            └── DeleteOutOfStockInventory/
                ├── index.jsx
                ├── InventoryPanel.jsx      (shared UI/logic)
                ├── ColorDiamondDelete.jsx  (type: color-diamond → ls_inventory_c)
                └── WhiteDiamondDelete.jsx  (type: white-diamond → ls_inventory)
```

## 1. functions.php — one line

```php
require_once get_template_directory() . '/delete-out-of-stock-inventory/index.php';
```

That's it. `index.php` internally requires `api/routes.php` and hooks up the
wp-admin page + script enqueue.

## 2. Styling

Admin UI is styled with `src/App.scss` (Sass — variables, nesting, a
button mixin). Vite compiles it out of the box as long as the `sass`
devDependency is installed (already in `package.json`).

## 3. Build the React app

```bash
cd delete-out-of-stock-inventory/react
npm install
npm run build
```

Outputs to `delete-out-of-stock-inventory/react/dist/index.js`, using
`vite.delete-out-of-stock-inventory.config.js` at the theme root (same
build pattern as your existing `vite.react-admin.config.js`).

## 4. Where it shows up

A new wp-admin menu item, "Delete OOS Inventory" (capability:
`manage_woocommerce`), renders the React app.

## API

- `GET  /wp-json/lgd/v1/out-of-stock-inventory?type=color-diamond|white-diamond`
  → lists out-of-stock rows for that table.

- `POST /wp-json/lgd/v1/delete-inventory`  body: `{ "type": "color-diamond"|"white-diamond", "product_id": 123 }`
  → deletes the row(s) `WHERE pid = product_id` from the matching table
    (`ls_inventory_c` for color, `ls_inventory` for white), and
    force-deletes (`wp_delete_post($id, true)`) the WooCommerce product —
    permanent, not Trash.

Color and white diamond stay fully separate code paths end to end: separate
React component, separate table, same shared REST namespace only.
