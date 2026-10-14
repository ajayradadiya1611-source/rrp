# Inventory Admin

React wp-admin screens for **Diamonds** (`ls_inventory`) and **Colored Diamonds** (`ls_inventory_c`) — list, search, add/edit, delete, stock status.

```
inventory-admin/
├── index.php          # loader: admin menu pages + enqueue (out/inventory-admin)
├── api/routes.php     # REST API — ls/v1/inventory-admin/{white|color}
└── react/
    ├── index.html
    ├── package.json
    └── src/
        ├── main.jsx   # mounts #ls-inventory-admin-root (data-type white|color)
        ├── App.jsx
        ├── App.scss
        ├── api.js
        └── components/InventoryAdmin/
            ├── InventoryList.jsx
            └── InventoryForm.jsx
```

Build: `vite build --config vites/vite.inventory-admin.config.js` → `out/inventory-admin/`.
Loaded from `inc-admin/admin-page.php`.
