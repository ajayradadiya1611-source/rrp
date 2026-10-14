# thankyou-question

```
thankyou-question/
├── index.php        ← the ONLY file functions.php needs to require
├── api/
│   └── routes.php   ← registers POST /wp-json/ls/v1/thankyou-question (real move, not shared elsewhere)
└── react/
    ├── index.html
    ├── package.json
    ├── dist/         ← created by `npm run build`
    └── src/
        ├── main.jsx
        ├── App.jsx
        ├── App.scss                       (was react/thankyou-question/styles.scss)
        └── components/
            └── ThankYouQuestion/
                ├── index.jsx               (was react/thankyou-question/App.jsx)
                └── icons.jsx               (was react/thankyou-question/icons.jsx — not currently imported anywhere, moved as-is)
```

## What it does

Renders the short post-purchase question form on the WooCommerce
order-received ("Thank You") page. On submit, posts to
`/wp-json/ls/v1/thankyou-question`, which saves into the existing
`{$wpdb->prefix}ls_contact_submissions` table (`form_type =
'thankyou_question'`) via `ls_store_submission()` — defined in
`inc/admin-contact-form.php`, left there since it's shared with other
forms, not duplicated here.

Enqueue is automatic — `lgd_enqueue_thankyou_question()` is hooked to
`wp_enqueue_scripts` and only fires on `is_wc_endpoint_url('order-received')`
for a valid order, exactly as the original `loose_diomond_scripts_thankyou_question()`
did. `window.LS_THANKYOU_QUESTION` (`orderId`, `apiBaseUrl`, `nonce`,
`alreadyAnswered`) is localized the same way.

## 1. functions.php — one line

```php
require_once get_template_directory() . '/components/thankyou-question/index.php';
```

Remove the old:
```php
include('inc/admin-contact-form.php');       // keep — still shared
include('inc/ls-thankyou-question-api.php'); // remove — moved into this module's api/routes.php
```

## 2. Template integration

Nothing to do — the mount div (`#ls-thankyou-question-root`) is
whatever the order-received template already renders. If no template
currently prints it, call `lgd_render_thankyou_question()` wherever it
should appear.

## 3. Build the React app

```bash
cd react
npm install
npm run build
```

Output goes to `out/thankyou-question/` at the theme root, via
`vites/vite.thankyou-question.config.js` (already present — no changes
needed there).
