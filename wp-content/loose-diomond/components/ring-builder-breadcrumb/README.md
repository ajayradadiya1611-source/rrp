# ring-builder-breadcrumb

```
ring-builder-breadcrumb/
├── index.php        ← the ONLY file functions.php needs to require
├── api/
│   └── routes.php   ← no new routes — documents the shared endpoint this module calls
└── react/
    ├── index.html
    ├── package.json
    ├── dist/         ← created by `npm run build`
    └── src/
        ├── main.jsx
        ├── App.jsx
        ├── App.scss                (no styles of its own — see below)
        └── components/
            └── RingBuilderBreadcrumb/
                ├── index.jsx           (was react/inventory/App4.jsx)
                └── RingBuilderSteps.jsx (diamond/setting/review step content)
```

## What it does

Renders the 3-step "Choose a Diamond / Choose a Setting / Complete Ring"
breadcrumb nav at the top of the ring-builder flow. Step order
(diamond-first vs setting-first) and each step's selected-item summary
come from `window.LS_RING_BUILDER` (`setting`, `diamond`, `started_with`,
`urls`, `current_page`, `admin_ajax_url`, `is_diamond_sku`), read
client-side exactly as the original `App4.jsx` did. Clearing a step
posts to the existing `ls_rb_clear` AJAX action.

Styling comes entirely from the theme's existing `.ringbuilder_brdcrmb`
CSS, already loaded globally — this module has no CSS of its own.

## 1. functions.php — one line

```php
require_once get_template_directory() . '/components/ring-builder-breadcrumb/index.php';
```

## 2. Template integration

Already wired: `inc/diamond-detail-render.php` calls
`lgd_render_ring_builder_breadcrumb()` where the old bare
`<div id="ls-inventory-root4"></div>` used to sit, right after the
existing `ls_ring_builder_breadcrumb('diamond')` PHP-rendered fallback
call (left untouched — that's a separate system).

## 3. Build the React app

```bash
cd react
npm install
npm run build
```

Output goes to `out/ring-builder-breadcrumb/` at the theme root, via
`vites/vite.ring-builder-breadcrumb.config.js` (already wired into the
root `package.json` `build` script).
