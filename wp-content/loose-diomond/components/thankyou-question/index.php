<?php
/**
 * Thank You Question — module loader
 *
 * Renders the short post-purchase question form shown on the
 * WooCommerce order-received ("Thank You") page (moved out of
 * react/thankyou-question/). Answers are saved into the existing
 * `{$wpdb->prefix}ls_contact_submissions` table (form_type =
 * 'thankyou_question') so they show up on the "Forms" admin page
 * alongside other form submissions.
 *
 * This is the ONLY file functions.php needs to require for this
 * feature. It wires up:
 *   - api/routes.php — registers POST /wp-json/ls/v1/thankyou-question
 *     (moved from inc/ls-thankyou-question-api.php; not shared with
 *     anything else, so it's a real move, not just documentation —
 *     unlike components/inventory-listing's api/routes.php)
 *   - the frontend script/style enqueue + window.LS_THANKYOU_QUESTION
 *     localization for the built React app (out/thankyou-question),
 *     hooked to wp_enqueue_scripts, gated on is_wc_endpoint_url('order-received')
 *   - a template helper, lgd_render_thankyou_question(), that prints
 *     the mount point (not currently called from any template in this
 *     export — the original react/thankyou-question/index.jsx mounted
 *     into #ls-thankyou-question-root wherever that div already
 *     exists in the WooCommerce order-received template output)
 *
 * Usage in functions.php:
 *   require_once get_template_directory() . '/components/thankyou-question/index.php';
 *
 * Relies on ls_store_submission(), defined in inc/admin-contact-form.php
 * (shared — not duplicated here).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // no direct access
}

// --- API: registers /wp-json/ls/v1/thankyou-question -------------------
require_once __DIR__ . '/api/routes.php';

// --- Frontend enqueue + mount helper ------------------------------------

/**
 * Print the mount point for the question form. The script/style
 * enqueue and window.LS_THANKYOU_QUESTION localization happen
 * separately, automatically, via the wp_enqueue_scripts hook below —
 * this just prints the div.
 */
function lgd_render_thankyou_question() {
    echo '<div id="ls-thankyou-question-root"></div>';
}

add_action( 'wp_enqueue_scripts', 'lgd_enqueue_thankyou_question' );

function lgd_enqueue_thankyou_question() {

    if ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-received' ) ) {
        return;
    }

    global $wp;
    $order_id = absint( $wp->query_vars['order-received'] ?? 0 );

    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $dist = get_template_directory() . '/out/thankyou-question';
    $uri  = get_template_directory_uri() . '/out/thankyou-question';

    if ( ! file_exists( $dist . '/index.js' ) ) {
        return; // not built yet — run `npm run build` in components/thankyou-question/react/
    }

    wp_enqueue_script(
        'ls-thankyou-question',
        $uri . '/index.js',
        array(),
        filemtime( $dist . '/index.js' ),
        true
    );

    // Loaded as an ES module so its bundled dependencies (React, etc.)
    // get their own per-file scope — avoids a top-level `const`
    // collision if another bundled-React script ever loads on the
    // same page (same reasoning as the other components/* modules).
    add_filter( 'script_loader_tag', function ( $tag, $handle ) {
        if ( $handle !== 'ls-thankyou-question' ) {
            return $tag;
        }
        return str_replace( ' src', ' type="module" src', preg_replace( '/\stype=["\'][^"\']*["\']/', '', $tag, 1 ) );
    }, 10, 2 );

    $css_file = $dist . '/assets/index.css';
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            'ls-thankyou-question-style',
            $uri . '/assets/index.css',
            array(),
            filemtime( $css_file )
        );
    }

    wp_localize_script( 'ls-thankyou-question', 'LS_THANKYOU_QUESTION', array(
        'orderId'         => $order_id,
        'apiBaseUrl'      => esc_url_raw( rest_url( 'ls/v1' ) ),
        'nonce'           => wp_create_nonce( 'wp_rest' ),
        'alreadyAnswered' => (bool) $order->get_meta( '_ls_thankyou_question_answer', true ),
    ) );
}
