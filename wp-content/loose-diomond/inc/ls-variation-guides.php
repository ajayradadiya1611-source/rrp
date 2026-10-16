<?php
/**
 * Single product: "Guide" (size) and "Chart" (purity/clarity) links next to
 * the variation labels, each opening a Bootstrap modal.
 *
 *   ls_variation_guide_link( $attr_name )   -> link html ('' when not needed)
 *   ls_variation_guide_modals()             -> prints the modals in wp_footer
 *
 * The "View & download complete guide" link is shown only when a URL is set:
 *   add_filter( 'ls_size_guide_url', fn() => '/wp-content/uploads/size-guide.pdf' );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ls_variation_guide_link( $attr_name ) {
    $name = strtolower( (string) $attr_name );

    if ( strpos( $name, 'size' ) !== false ) {
        return '<a href="#" class="ls-guide-link" data-bs-toggle="modal" data-bs-target="#lsSizeGuideModal">Guide</a>';
    }
    if ( strpos( $name, 'purity' ) !== false || strpos( $name, 'clarity' ) !== false ) {
        return '<a href="#" class="ls-guide-link" data-bs-toggle="modal" data-bs-target="#lsClarityChartModal">Chart</a>';
    }
    return '';
}

/** Round-brilliant top view (girdle, table octagon, star + upper-girdle facets) with inclusion marks. */
function ls_clarity_svg( $marks, $size = 1.6 ) {
    $cx = 50;
    $cy = 50;
    $pt = function ( $deg, $r ) use ( $cx, $cy ) {
        return [ $cx + $r * cos( deg2rad( $deg - 90 ) ), $cy + $r * sin( deg2rad( $deg - 90 ) ) ];
    };
    $d = '';
    for ( $k = 0; $k < 8; $k++ ) {
        $a  = $k * 45;
        $t1 = $pt( $a, 21 );          // table vertex
        $t2 = $pt( $a + 45, 21 );     // next table vertex
        $st = $pt( $a + 22.5, 33 );   // star point
        $g0 = $pt( $a, 48 );          // girdle straight out from the table vertex
        $gl = $pt( $a + 11.25, 48 );
        $gr = $pt( $a + 33.75, 48 );
        $d .= sprintf( 'M%.1f %.1f L%.1f %.1f L%.1f %.1f ', $t1[0], $t1[1], $t2[0], $t2[1], $st[0], $st[1] );   // table edge + star
        $d .= sprintf( 'M%.1f %.1f L%.1f %.1f ', $t1[0], $t1[1], $st[0], $st[1] );
        $d .= sprintf( 'M%.1f %.1f L%.1f %.1f ', $t1[0], $t1[1], $g0[0], $g0[1] );                              // kite edge
        $d .= sprintf( 'M%.1f %.1f L%.1f %.1f M%.1f %.1f L%.1f %.1f ', $st[0], $st[1], $gl[0], $gl[1], $st[0], $st[1], $gr[0], $gr[1] );
    }
    $svg  = '<svg viewBox="0 0 100 100" width="100" height="100" aria-hidden="true" fill="none" stroke="#2d2926" stroke-width="0.9" stroke-linejoin="round">';
    $svg .= '<circle cx="50" cy="50" r="48"/><path d="' . $d . '"/>';

    // deterministic inclusion marks: dots for higher grades, short strokes for included grades
    $seed = 11;
    $svg .= '<g fill="#2d2926" stroke="none">';
    for ( $i = 0; $i < $marks; $i++ ) {
        $seed = ( $seed * 1103515245 + 12345 ) & 0x7fffffff;
        $ang  = ( $seed % 3600 ) / 10;
        $seed = ( $seed * 1103515245 + 12345 ) & 0x7fffffff;
        $rad  = 5 + ( $seed % 3600 ) / 100;
        $x    = $cx + $rad * cos( deg2rad( $ang ) );
        $y    = $cy + $rad * sin( deg2rad( $ang ) );
        $rx   = $marks > 20 ? $size * 1.4 : $size * 0.6;
        $ry   = $size * 0.55;
        $svg .= sprintf( '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" transform="rotate(%d %.1f %.1f)"/>', $x, $y, $rx, $ry, $seed % 180, $x, $y );
    }
    $svg .= '</g></svg>';
    return $svg;
}

function ls_variation_guide_modals() {
    static $registered = false;
    if ( $registered ) {
        return;
    }
    $registered = true;

    add_action( 'wp_footer', function () {
        $guide_url = apply_filters( 'ls_size_guide_url', '' );
        $sizes = [
            [ '0.90 - 1.00 MM', '0.004', '000-00' ],
            [ '1.00 - 1.10 MM', '0.005', '00-0' ],
            [ '1.10 - 1.15 MM', '0.006', '0-1' ],
            [ '1.15 - 1.20 MM', '0.007', '1 - 1.5' ],
            [ '1.20 - 1.25 MM', '0.008', '1.5 - 2' ],
        ];
        // [ label(s), marks, group ]
        $grades = [
            [ 'FL',        0,  'FLAWLESS',                 1 ],
            [ 'IF',        0,  'INTERNALLY FLAWLESS',      1 ],
            [ 'VVS1, VVS2', 3, 'VERY VERY SLIGHTLY INCLUDED', 1 ],
            [ 'VS1, VS2',  5,  'VERY SLIGHTLY INCLUDED',   1 ],
            [ 'SI1',       10, '',                         0 ],
            [ 'SI2, SI3',  14, 'SLIGHTLY INCLUDED',        2 ],
            [ 'I1, I2',    22, '',                         0 ],
            [ 'I3',        36, 'INCLUDED',                 2 ],
        ];
        ?>
        <!-- Size guide modal -->
        <div class="modal fade ls-guide-modal" id="lsSizeGuideModal" tabindex="-1" aria-labelledby="lsSizeGuideTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h2 class="modal-title" id="lsSizeGuideTitle">SIZE GUIDE</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="ls-guide-table">
                            <thead>
                                <tr><th>SIZE (MM)</th><th>SIZE (Carat)</th><th>SIEVE (SIZE)</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $sizes as $row ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $row[0] ); ?></td>
                                        <td><?php echo esc_html( $row[1] ); ?></td>
                                        <td><?php echo esc_html( $row[2] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if ( $guide_url ) : ?>
                            <a class="ls-guide-download" href="<?php echo esc_url( $guide_url ); ?>" target="_blank" rel="noopener">
                                <i class="fa-solid fa-download"></i> View &amp; download complete guide
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clarity chart modal -->
        <div class="modal fade ls-guide-modal ls-guide-modal--wide" id="lsClarityChartModal" tabindex="-1" aria-labelledby="lsClarityChartTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h2 class="modal-title" id="lsClarityChartTitle">DIAMOND CLARITY CHART</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="ls-clarity">
                            <?php foreach ( $grades as $g ) : ?>
                                <div class="ls-clarity__item">
                                    <span class="ls-clarity__grade"><?php echo esc_html( $g[0] ); ?></span>
                                    <?php echo ls_clarity_svg( $g[1] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="ls-clarity ls-clarity--groups">
                            <div class="ls-clarity__group"><span></span><p>FLAWLESS</p></div>
                            <div class="ls-clarity__group"><span></span><p>INTERNALLY FLAWLESS</p></div>
                            <div class="ls-clarity__group"><span></span><p>VERY VERY SLIGHTLY INCLUDED</p></div>
                            <div class="ls-clarity__group"><span></span><p>VERY SLIGHTLY INCLUDED</p></div>
                            <div class="ls-clarity__group ls-clarity__group--2"><span></span><p>SLIGHTLY INCLUDED</p></div>
                            <div class="ls-clarity__group ls-clarity__group--2"><span></span><p>INCLUDED</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } );
}
