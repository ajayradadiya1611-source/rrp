# ring-extra-features (React front-end, fully fixed groups & options, priced per-product via ACF)

```
ring-extra-features/
├── index.php                          ← the ONLY file functions.php needs to require
├── inc/
│   ├── group-definitions.php          (FIXED groups AND options — edit here to rename/add/remove any of them)
│   ├── acf-fields.php                 (registers the ACF price fields on Product, in code)
│   └── class-ring-extra-features.php  (price lookup, cart hooks, and the mount-point markup)
├── front/
│   └── images/                        (one .svg per option key)
└── react/                             (the button + modal, as a React app)
    ├── package.json
    ├── index.html
    └── src/
        ├── main.jsx                   (finds every mount point on the page, mounts one instance per)
        ├── index.css
        └── components/RingExtraFeatures/
            ├── index.jsx              (button, hidden input, modal — all state local to this instance)
            └── OptionCard.jsx
```

## Why React

The button + modal used to be vanilla jQuery keyed off page-wide element
`id`s. If more than one copy of it ever ended up in the same page's DOM —
a quick-view popup, an upsell/related-product widget, anything reusing
this same render call — every copy shared those same ids, and jQuery's
`$('#id')` selectors could only ever bind to one of them while a
`.find()` call could pick up cards belonging to a *different* instance
than the one whose button was actually clicked.

Rewriting it in React removes the whole failure class rather than working
around it: `main.jsx` finds every `.ls-ref-mount` point on the page and
gives each one its own independent React root with its own component
state (`useState` inside `RingExtraFeatures`). There is no page-wide
selector anywhere in this app — one instance's clicks can never affect
another's cards, no matter how many end up on one page.

## The five groups, all fully fixed

- **Band Width** — 1.60mm, 1.80mm, 2.00mm, 2.20mm, 2.50mm, 3.00mm, 3.50mm.
- **Band Style** — Euro Shank, Tapered, Reverse Tapered.
- **Prongs** — Pave Prongs, 6 Prongs (Round), Double Prongs.
- **East-West** — East West.
- **Two Tone** — Two Tone.

Each group shows a fixed one-line **subtitle** under its heading, and
each option shows a small **"?" tooltip** (Bootstrap's
`data-bs-toggle="tooltip"` pattern, matching the rest of the site) with
its own fixed descriptive text. Both are fixed copy in
`group-definitions.php` — not something ACF or an admin controls; only
the **price** per option is per-product.

## How data gets from PHP to React

`LGD_Ring_Extra_Features::render_button_and_modal( $product_id, $preselected_raw_json )`
no longer server-renders the button/modal HTML. It outputs one small
mount `<div class="ls-ref-mount">` with the product's available
groups/prices (from `get_available_groups_for_product()`) serialized
into a `data-groups` JSON attribute, plus `data-preselected`,
`data-currency`, and `data-image-base`. `main.jsx` reads those
attributes and renders `<RingExtraFeatures>` into that div.

The component renders its own `<input type="hidden" name="extra_features">`
as part of its output — as long as the mount div sits inside a `<form>`
(which it always does, same position as before), that hidden input
posts along with the rest of the form exactly like it did in the
vanilla-JS version. Nothing about the server-side flow changed: same
form field name, same JSON shape, same session carry-over, same
server-side re-verification in `ls_rb_addtobag()` / `capture_cart_item_data()`.

## 1. functions.php — one line

```php
require_once get_template_directory() . '/components/ring-extra-features/index.php';
```

Two more small, tagged edits are needed — see
`modified-theme-files/functions.php` and
`modified-theme-files/page-ring-preview.php` in this zip (search
`LGD EXTRA FEATURES`) — unchanged from before, since none of this
touched the server-side verification/session logic.

## 2. Build the React app

This theme builds every component's React app from the **theme root**,
using the root `package.json` and the shared `vites/` folder —
`vites/vite.ring-extra-features.config.js` (included in this zip) is
already wired into the root build script. From the theme root:

```bash
npm run build
```

or just this component on its own while developing:

```bash
vite build --config vites/vite.ring-extra-features.config.js
```

Outputs to `out/ring-extra-features/index.js` (+ `assets/index.css`).
No separate `npm install` inside `react/` — `react` and `react-dom` are
already root dependencies.

## 3. Normal single product pages

Unchanged from before: this theme's `single-product.php` is a fully
custom template that never calls WooCommerce's standard add-to-cart
hooks, so this is wired via tagged edits directly in
`modified-theme-files/single-product.php` and `functions.php` (search
`LGD EXTRA FEATURES`) — the simple-product form, the variable-product
form's real "Add To Bag" branch, and its "Select This Setting" branch
(captured into `$_SESSION` there, since that step doesn't add to cart
yet — see `handle_ring_builder_setting()`).

## 4. Where it shows up

Ring Preview page and any normal single product page — "Add Extra
Feature" button, only appearing when at least one group has at least
one available option for that product. On Add To Bag, the selection is
re-priced from that product's own ACF data, and the chosen upgrades
show up as extra line-item detail in cart, checkout, order emails, and
the WooCommerce order screen.

## Customizing artwork

Each option looks for `front/images/{option_key}.svg` — replace those
files with real line art. Missing images just hide the broken-image
icon rather than showing one.

Option keys, for reference: `band_1_60mm`, `band_1_80mm`,
`band_2_00mm`, `band_2_20mm`, `band_2_50mm`, `band_3_00mm`,
`band_3_50mm`, `euro_shank`, `tapered`, `reverse_tapered`,
`pave_prongs`, `six_prongs_round`, `double_prongs`, `east_west`,
`two_tone`.
