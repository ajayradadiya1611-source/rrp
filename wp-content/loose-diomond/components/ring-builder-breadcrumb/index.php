<?php
/**
 * Ring Builder Breadcrumb — module loader
 *
 * Renders the 3-step "Choose a Diamond / Choose a Setting / Complete
 * Ring" breadcrumb nav shown at the top of the ring-builder flow
 * (moved out of react/inventory/App4.jsx). Step order (diamond-first
 * vs setting-first) and each step's selected-item summary come from
 * `window.LS_RING_BUILDER`, read client-side exactly as App4.jsx did.
 *
 * This is the ONLY file functions.php needs to require for this
 * feature. It wires up:
 *   - api/routes.php (no new routes — see that file for why)
 *   - the frontend script/style enqueue for the built React app
 *     (out/ring-builder-breadcrumb), hooked to wp_enqueue_scripts and
 *     gated on the same condition the ring-builder breadcrumb div is
 *     printed under in inc/diamond-detail-render.php ($_GET['type']
 *     === 'ringbuilder')
 *   - a template helper, lgd_render_ring_builder_breadcrumb(), that
 *     prints the mount point
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/ring-builder-breadcrumb/index.php';
 *
 * This replaces the `<div id="ls-inventory-root4"></div>` previously
 * printed directly in inc/diamond-detail-render.php (which relied on
 * the big shared react/inventory bundle) — that template now calls
 * lgd_render_ring_builder_breadcrumb() instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API (no new routes — see api/routes.php) --------------------------
require_once __DIR__ . '/api/routes.php';

// --- Frontend enqueue + mount helper ------------------------------------

/**
 * Print the mount point for the ring-builder breadcrumb. The script/
 * style enqueue happens separately, automatically, via the
 * wp_enqueue_scripts hook below — this just prints the div. Call it
 * from any template that needs the breadcrumb, in place of the old
 * bare `<div id="ls-inventory-root4"></div>`.
 */
function lgd_render_ring_builder_breadcrumb() {
    echo '<div id="ring-builder-breadcrumb-root"></div>';
}

add_action( 'wp_enqueue_scripts', 'lgd_enqueue_ring_builder_breadcrumb' );

function lgd_enqueue_ring_builder_breadcrumb() {

    if (
        ! is_page_template( 'page-ring-preview.php' ) &&
        ! is_page_template( 'page-inventory.php' ) &&
        ! is_page_template( 'page-inventory2.php' ) &&
        ! is_page_template( 'page-shape-products.php' ) &&
        ! is_product_category() &&
        ! is_product() &&
        ! get_query_var( 'diamond_sku' )   // /diamond/{sku}/ detail pages
    ) {
        return;
    }
  

    $dist = get_template_directory() . '/out/ring-builder-breadcrumb';
    $uri  = get_template_directory_uri() . '/out/ring-builder-breadcrumb';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build` in components/ring-builder-breadcrumb/react/
    }

    wp_enqueue_script(
        'lgd-ring-builder-breadcrumb',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    // Loaded as an ES module so its bundled dependencies (React, etc.)
    // get their own per-file scope. The ring-builder page also loads
    // the older shared 'loose-diomond-inventory' bundle (is_product()
    // is one of its trigger conditions in inc/script-load.php), which
    // bundles its own copy of React too — two classic <script> tags
    // that each independently minify a top-level `const` for their
    // bundled React import will collide ("Identifier '...' has
    // already been declared") since classic scripts share the page's
    // top-level lexical scope. type="module" avoids that.
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( $handle !== 'lgd-ring-builder-breadcrumb' ) {
            return $tag;
        }
        return str_replace( ' src', ' type="module" src', preg_replace( '/\stype=["\'][^"\']*["\']/', '', $tag, 1 ) );
    }, 10, 2 );

    // Reads window.LS_RING_BUILDER (setting, diamond, started_with,
    // urls, current_page, admin_ajax_url, is_diamond_sku) — make sure
    // that's still localized before this runs, same as before.

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'lgd-ring-builder-breadcrumb',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }
}
