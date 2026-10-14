<?php
global $oc_common;

$oc_common['notification_email']=SUPPORT_EMAIL;
$oc_common['inventory_url'] = get_site_url().'/lab-diamonds/';
$oc_common['white_inventory_url'] = get_site_url().'/lab-diamonds/';
$oc_common['color_inventory_url'] = get_site_url().'/lab-created-colored-diamonds/';
global $select_diamond;
$select_diamond = $oc_common['inventory_url'].'?type=ringbuilder';
global $rb_setting_size;
$rb_setting_size = array('3.00','3.25','3.50','3.75','4.00','4.25','4.50','4.75','5.00','5.25','5.50','5.75','6.00','6.25','6.50','6.75','7.00','7.25','7.50','7.75','8.00','8.25','8.50','8.75','9.00','9.25','9.50','9.75','10.00','10.25','10.50','10.75','11.00','11.25','11.50','11.75','12.00');
global $ring_preview, $ringdmnd_incart_msg, $ring_setting,$select_cdiamond,$ring_incart_msg;
// Define if not set
if (!isset($ring_preview)) $ring_preview = get_site_url() . '/ring-preview'; // example
if (!isset($select_cdiamond)) $select_cdiamond = get_site_url() . '/lab-created-colored-diamonds/?type=ringbuilder_c'; // example
if (!isset($ring_incart_msg)) $ring_incart_msg = 'Ring setting you have selected is already in your Cart';
if (!isset($ringdmnd_incart_msg)) $ringdmnd_incart_msg = 'This diamond is already in your cart.';
if (!isset($ring_setting)) $ring_setting = get_site_url() . '/build-a-ring/'; // example

global $oc_cut,$oc_color,$oc_clarity,$oc_certificate;

$oc_cut = array(
    1 => "Good",
    2 => "Very Good",
    3 => "Excellent",
    4 => "Ideal"
);
$oc_color = [
    1  => "yellow",
    2  => "pink",
    3  => "purple",
    4  => "red",
    5  => "blue",
    6  => "green",
    7  => "orange",
    8  => "brown",
    9  => "black",
    10 => "gray",
];
$oc_clarity = [
    1  => "I2",
    2  => "I1",
    3  => "SI3",
    4  => "SI2",
    5  => "SI1",
    6  => "VS2",
    7  => "VS1",
    8  => "VVS2",
    9  => "VVS1",
    10 => "IF",
];
$oc_certificate = [
	1  => "IGI",
	2  => "GIA",
	3  => "GCAL",
] ;

global $inv_color_c, $inv_intensity_c, $inv_color_slug,  $inv_shape_imgids_c;
$inv_color_slug = 'lab-created-colored-diamonds';

$inv_color_c = array(
    'yellow' => 1,
    'pink'   => 2,
    'purple' => 3,
    'red'    => 4,
    'blue'   => 5,
    'green'  => 6,
    'orange' => 7,
    'brown'  => 8,
    'black'  => 9,
    'gray'   => 10,
);

$inv_intensity_c = array(
    'Fancy Light'   => 1,
    'Fancy'         => 2,
    'Fancy Intense' => 3,
    'Fancy Vivid'   => 4,
    'Fancy Deep'    => 5,
    'Fancy Dark'    => 6,
);

$inv_clarity = array(
    'I2'   => 1,
    'I1'   => 2,
    'SI3'  => 3,
    'SI2'  => 4,
    'SI1'  => 5,
    'VS2'  => 6,
    'VS1'  => 7,
    'VVS2' => 8,
    'VVS1' => 9,
    'IF'   => 10,
    'FL'   => 11,
);

$inv_shape_imgids_c = array(
    'round'    => array(
        'yellow' => 184753,
        'pink'   => 184750,
        'purple' => 184751,
        'red'    => 184752,
        'blue'   => 184755,
        'green'  => 184748,
        'orange' => 184749,
        'brown'  => 184746,
        'black'  => 184754,
        'gray'   => 184747,
    ),
    'princess' => array(
        'yellow' => 184733,
        'pink'   => 184730,
        'purple' => 184731,
        'red'    => 184732,
        'blue'   => 184725,
        'green'  => 184728,
        'orange' => 184729,
        'brown'  => 184726,
        'black'  => 184734,
        'gray'   => 184727,
    ),
    'cushion'  => array( //done
        'yellow' => 184669,
        'pink'   => 184666,
        'purple' => 184667,
        'red'    => 184668,
        'blue'   => 184671,
        'green'  => 184674,
        'orange' => 184665,
        'brown'  => 184672,
        'black'  => 184670,
        'gray'   => 184673,
    ),
    'oval'     => array(
        'yellow' => 184713,
        'pink'   => 184710,
        'purple' => 184711,
        'red'    => 184712,
        'blue'   => 184705,
        'green'  => 184708,
        'orange' => 184709,
        'brown'  => 184706,
        'black'  => 184714,
        'gray'   => 184707,
    ),
    'emerald'  => array( //done
        'yellow' => 184680,
        'pink'   => 184677,
        'purple' => 184678,
        'red'    => 184679,
        'blue'   => 184682,
        'green'  => 184675,
        'orange' => 184676,
        'brown'  => 184683,
        'black'  => 184681,
        'gray'   => 184684,
    ),
    'pear'     => array(
        'yellow' => 184724,
        'pink'   => 184721,
        'purple' => 184722,
        'red'    => 184723,
        'blue'   => 184716,
        'green'  => 184719,
        'orange' => 184720,
        'brown'  => 184717,
        'black'  => 184715,
        'gray'   => 184718,
    ),
    'asscher'  => array( //done
        'yellow' => 184661,
        'pink'   => 184658,
        'purple' => 184659,
        'red'    => 184660,
        'blue'   => 184663,
        'green'  => 184656,
        'orange' => 184657,
        'brown'  => 184664,
        'black'  => 184662,
        'gray'   => 184655,
    ),
    'radiant'  => array(
        'yellow' => 184744,
        'pink'   => 184741,
        'purple' => 184742,
        'red'    => 184743,
        'blue'   => 184735,
        'green'  => 184738,
        'orange' => 184739,
        'brown'  => 184736,
        'black'  => 184745,
        'gray'   => 184737,
    ),
    'marquise' => array(
        'yellow' => 184702,
        'pink'   => 184699,
        'purple' => 184700,
        'red'    => 184701,
        'blue'   => 184704,
        'green'  => 184697,
        'orange' => 184698,
        'brown'  => 184695,
        'black'  => 184703,
        'gray'   => 184696,
    ),
    'heart'    => array(
        'yellow' => 184690,
        'pink'   => 184687,
        'purple' => 184688,
        'red'    => 184689,
        'blue'   => 184692,
        'green'  => 184685,
        'orange' => 184686,
        'brown'  => 184693,
        'black'  => 184691,
        'gray'   => 184694,
    ),
);


// Globals and arrays from oldaddtocart.php
global $inv_cut, $inv_color, $inv_clarity, $inv_shape, $inv_dtype;
$inv_shape = array(
    'Round'    => 1,
    'Princess' => 2,
    'Cushion'  => 3,
    'Oval'     => 4,
    'Emerald'  => 5,
    'Pear'    => 6,
    'Asscher'  => 7,
    'Radiant'  => 8,
    'Marquise' => 9,
    'Heart'    => 10,
    'Rose Cut' => 11,  
    'Old European' => 12,
    'Half Moon'  => 13,
    'Baguette Trapezoid' => 14,
    'Baguette'  => 15,
    'Hexagon' => 16,
    'Kite'  => 17,
    'Old Mine'  => 18,   
    'Trapezoid' => 19,
    'Triangular'    => 20,
    );

// Named "Cut Style" sub-shapes (see constants/cutStyles.js on the React
// side) are stored in the inventory `shape` column under their own
// distinct value (e.g. 'rectangular_radiant') instead of the base shape
// ('radiant'). For ring-builder validation / display we still need to
// resolve them back to their base shape so a Radiant setting accepts a
// diamond whose shape is one of these Radiant sub-cuts.
global $inv_subshape_to_base;
$inv_subshape_to_base = array(
    'cushion_brilliant' => 'Cushion',
    'cushion_modified' => 'Cushion',
    'antique_cushion' => 'Cushion',
    'old_mine_cushion' => 'Cushion',
    'square_cushion' => 'Cushion',
    'oval_step' => 'Oval',
    'square_emerald' => 'Emerald',
    'pear_step' => 'Pear',
    'rectangular_radiant' => 'Radiant',
    'radiant_modified'    => 'Radiant',
    'square_radiant'      => 'Radiant',
    'dutch_marquise' => 'Marquise',
    'eurocut' => 'Old European',
    'trapez_step' => 'Trapezoid',
    'trapez_brilliant' => 'Trapezoid',

    // Criss cut sub-cuts (from constants/cutStyles.js)
    'criss_cut_radiant' => 'Radiant',
    'criss_cut_cushion' => 'Cushion',
    'criss_cut_asscher' => 'Asscher',

    // Named fancy cuts -> ring-builder base shape
    'portuguese' => 'Round',
    'square'     => 'Princess',
    'carre'      => 'Princess',
    'flanders'   => 'Cushion',
    'ashoka'     => 'Cushion',
    'capsule'    => 'Cushion',
    'moval'      => 'Oval',
    'crisscut'   => 'Radiant',
    'criss_cut'  => 'Radiant',
    );

/**
 * Base shape -> exact /build-a-ring/?shape= slug (must match the
 * build-a-ring filter values exactly).
 */
global $ls_ring_shape_slugs;
$ls_ring_shape_slugs = array(
    'Round'    => 'round-brilliant-cut',
    'Princess' => 'princess-cut',
    'Cushion'  => 'cushion-cut',
    'Oval'     => 'oval-cut',
    'Emerald'  => 'emerald-cut',
    'Asscher'  => 'asscher-cut',
    'Pear'     => 'pear-cut',
    'Radiant'  => 'radiant-cut',
    'Marquise' => 'marquise-cut',
    'Heart'    => 'heart-cut',
);

/**
 * Ring builder shape groups: a setting of the base shape also accepts
 * these named cuts (they are top-level shapes in the diamond list).
 * Mirrors RING_SHAPE_GROUPS in inventory-app/react/src/lsFunction.js.
 */
global $ls_ring_shape_groups;
$ls_ring_shape_groups = array(
    'round'    => array('round', 'portuguese'),
    'princess' => array('princess', 'square', 'carre'),
    'cushion'  => array('cushion', 'flanders', 'ashoka', 'capsule'),
    'oval'     => array('oval', 'moval'),
    'radiant'  => array('radiant', 'criss_cut'),
);

/** Any shape in a group -> whole group, else array($shape). */
function ls_get_ring_shape_group($shape) {
    global $ls_ring_shape_groups;
    $key = ls_normalize_shape_key($shape);
    if ($key === 'crisscut') $key = 'criss_cut';
    foreach ((array) $ls_ring_shape_groups as $group) {
        if (in_array($key, $group, true)) return $group;
    }
    return $key !== '' ? array($key) : array();
}

/** True when $shape is a named top-level cut inside a group (not a base). */
function ls_is_ring_group_cut($shape) {
    global $ls_ring_shape_groups;
    $key = ls_normalize_shape_key($shape);
    if ($key === 'crisscut') $key = 'criss_cut';
    foreach ((array) $ls_ring_shape_groups as $base => $group) {
        if ($key !== $base && in_array($key, $group, true)) return true;
    }
    return false;
}

/**
 * Normalise a raw inventory `shape` value into a lookup key:
 * lowercase, spaces/hyphens -> underscore, accents stripped
 * ("Carré" -> "carre", "Criss-Cut" -> "criss_cut").
 */
function ls_normalize_shape_key($shape) {
    $key = strtolower(trim((string) $shape));
    if (function_exists('remove_accents')) {
        $key = remove_accents($key);
    }
    $key = preg_replace('/[\s\-]+/', '_', $key);
    return trim($key, '_');
}

/**
 * Resolve any inventory shape (base shape or named sub-cut such as
 * Portuguese, Carre, Flanders, Ashoka, Capsule, Moval, Crisscut) to its
 * base $inv_shape name ("Round", "Princess", ...). Returns '' if unknown.
 */
function ls_get_base_shape_name($shape) {
    global $inv_shape, $inv_subshape_to_base;

    $key = ls_normalize_shape_key($shape);
    if ($key === '') return '';

    $candidates = array($key);
    // "portuguese_cut" / "flanders_cut" -> "portuguese" / "flanders"
    if (substr($key, -4) === '_cut') {
        $candidates[] = substr($key, 0, -4);
    }

    foreach ($candidates as $k) {
        if (!empty($inv_subshape_to_base) && isset($inv_subshape_to_base[$k])) {
            return $inv_subshape_to_base[$k];
        }
        foreach ((array) $inv_shape as $name => $id) {
            if (ls_normalize_shape_key($name) === $k) {
                return $name;
            }
        }
    }
    return '';
}

/**
 * Resolve a diamond's `shape` column value to its $inv_shape id, treating
 * any Radiant sub-shape (rectangular_radiant, radiant_modified,
 * square_radiant) as plain 'Radiant'.
 */
function ls_resolve_dp_shape_id($shape) {
    global $inv_shape;

    $base = ls_get_base_shape_name($shape);
    return ($base !== '' && isset($inv_shape[$base])) ? $inv_shape[$base] : null;
}

$inv_cut = array(
        'Good'      => 1,
        'Very Good' => 2,
        'Excellent' => 3,
        'Ideal'     => 4,
    );
$inv_dtype = array(
        'CVD'      => 0,
        'HPHT' => 1,
    );
$inv_color = array(
        'M' => 1,
        'L' => 2,
        'K' => 3,
        'J' => 4,
        'I' => 5,
        'H' => 6,
        'G' => 7,
        'F' => 8,
        'E' => 9,
        'D' => 10,
    );

$inv_clarity = array(
        'I2'   => 1,
        'I1'   => 2,
        'SI3'  => 3,
        'SI2'  => 4,
        'SI1'  => 5,
        'VS2'  => 6,
        'VS1'  => 7,
        'VVS2' => 8,
        'VVS1' => 9,
        'IF'   => 10,
        'FL'   => 11,
    );

$inv_shape_imgids = array(
    'round'    => 184638,
    'princess' => 184646,
    'cushion'  => 184640,
    'oval'     => 184644,
    'emerald'  => 184641,
    'pear'     => 184645,
    'asscher'  => 184639,
    'radiant'  => 184647,
    'marquise' => 184643,
    'heart'    => 184642,
    'rose cut' => 2292192,
    'rose cut cushion' => 1091659,
    'rose cut cushion' => 1091659,
    'rose cut pear' => 1091662,  
    'rose cut oval' => 1091661,
    'rose cut marquise' => 1091660,
    'rose cushion' => 1091659,
    'rose pear' => 1091662,  
    'rose oval' => 1091661,
    'rose marquise' => 1091660,
    'old mine' => 2340481,
    'old european' => 1091692,
    'half moon'  => 1091653,
    'baguette trapezoid' => 1091652,
    'baguette'  => 1091651,
    'hexagon' => 1091654,
    'kite'  => 1091655,
    'rectangular modified'  => 1091658,  
    'trapezoid' => 1091663,
    'triangular'    => 1091664,
    'ashoka' => 2330668,
    'briolette' => 2330669,
    'buddha' => 2330670,
    'bullet' => 2330671,
    'butterfly'  => 2330672,
    'pear step' => 2330674,
    'pentagonal'  => 2330675,
    'cadi step' => 2330676,
    'cadillac'  => 2330677,
    'calf head'  => 2330678,  
    'capsule' => 2330679,
    'carre'    => 2330680,
    'flanders'  => 2330681,  
    'flower' => 2330682,
    'heart and arrow'    => 2330683,
    'horsehead'  => 2330684,  
    'lily' => 2330685,
    'lozenge'    => 2330686,
    'moval'  => 2330687,  
    'octagonal' => 2330688,
    'triangular'    => 1091664,
    'old european' => 2330689,
    'oval step'    => 2330690,
    'tapered bullet'  => 2330701,  
    'portuguese' => 2330703,
    'triangular'    => 1091664,
    'shield' => 2330704,
    'star'    => 2330705,
    'trilliant'  => 2330712,  
    'criss cut cushion' => 2330714,
    'cushion modified'    => 2330715,
    'old mine cushion' => 2330716,
    'square cushion'    => 2330717,
    'cushion brilliant'  => 2330718,  
    'antique cushion' => 2330719,
    'square emerald'    => 2330722,
    'radiant modified' => 2330723,
    'square radiant'    => 2330724,
    'criss cut radiant'  => 2330725, 
    'square'    => 2330728,
    'dutch marquise'    => 2330731,  
    'heptagonal' => 2336439
);

global $shape_png_white;
$shape_png_white = array(
    'round' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/round.png',
    'princess' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/princess.png',

    'cushion' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cushion.png',
    'cushion_brilliant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cushion_brilliant.png',
    'cushion_modified' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cushion_modified.png',
    'antique_cushion' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/antique_cushion.png',
    'old_mine_cushion' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/old_mine_cushion.png',
    'square_cushion' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/square_cushion.png',

    'oval' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/oval.png',
    'oval_step' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/oval_step.png',

    'emerald' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/emerald.png',
    'square_emerald' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/square_emerald.png',

    'pear' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/pear.png',
    'pear_step' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/pear_step.png',

    'asscher' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/asscher.png',

    'radiant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/radiant.png',
    'rectangular_radiant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/rectangular_radiant.png',
    'radiant_modified' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/radiant_modified.png',
    'square_radiant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/radiant.png',
  
    'marquise' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/marquise.png',
    'dutch_marquise' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/dutch_marquise.png',

    'heart' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/heart.png',
    'rose_cut' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/rose_cut.png',

    'old_european' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/old_european.png',
    'eurocut' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/eurocut.png',

    'half_moon' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/half_moon.png',
    'baguette_trapezoid' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/baguette_trapezoid.png',
    'baguette' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/baguette.png',
    'hexagon' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/hexagon.png',
    'kite' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/kite.png',
    'old_mine' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/old_mine.png',

    'trapezoid' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/trapezoid.png',
    'trapez_step' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/trapez_step.png',
    'trapez_brilliant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/trapez_brilliant.png',

    'triangular' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/triangular.png',

    // New shapes
    'trilliant' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/trilliant.png',
    'heptagonal' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/heptagonal.png',
    'pentagonal' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/pentagonal.png',
    'shield' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/shield.png',
    'horsehead' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/horsehead.png',
    'capsule' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/capsule.png',
    'octagonal' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/octagonal.png',
    'star' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/star.png',
    'square' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/square.png',
    'lozenge' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/lozenge.png',
    'briolette' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/briolette.png',
    'calf_head' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/calf_head.png',
    'flanders' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/flanders.png',
    'carre' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/carre.png',
    'bullet' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/bullet.png',
    'tapered_bullet' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/tapered_bullet.png',
    'portuguese' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/portuguese.png',
    'ashoka' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/ashoka.png',
    'cadillac' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cadillac.png',
    'lily' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/lily.png',
    'cadi_step' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/cadi_step.png',
    'butterfly' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/butterfly.png',
    'buddha' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/buddha.png',
    'moval' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/moval.png',
    'flower' => get_template_directory_uri() . '/assets/image/ls-inventory/shape-diamond/png/flower.png',
);

function lsFunction($cut, $color, $clarity,$dtype,$intensity) {
    $desc = [
        'cut'     => '',
        'color'   => '',
        'clarity' => '',
        'dtype' => '',
        'intensity'=>''
    ];

    switch ($cut) {
        case 'Good':
            $desc['cut'] = 'Quality cut with good proportions to return most light that enters.';
            break;
        case 'Excellent':
            $desc['cut'] = 'High-quality cut with near-perfect proportions that return nearly all light that enters.';
            break;
        case 'Very Good':
            $desc['cut'] = 'High-quality cut with good proportions that return almost all light that enters.';
            break;
        case 'Ideal':
            $desc['cut'] = 'Perfectly-proportioned to return all light that enters for maximum fire. Only 5% of mined diamonds meet this standard.';
            break;
    }

    switch ($color) {
        case 'M':
            $desc['color'] = "Have a definite yellow tint that's visible to the naked eye. Like K and L diamonds, M color diamonds offer fantastic value for money.";
            break;
        case 'L':
            $desc['color'] = 'Color is most noticeable when viewed from the side, have a slightly yellow tone.';
            break;
        case 'K':
            $desc['color'] = 'Color noticeable with the naked eye. There are several color grades that are lower than K.';
            break;
        case 'J':
            $desc['color'] = 'Color noticeable when seen side-by-side with diamonds of better grades. Good value.';
            break;
        case 'I':
            $desc['color'] = 'Slight color noticeable when seen side-by-side with diamonds of better grades. Great value.';
            break;
        case 'H':
            $desc['color'] = 'Nearly colorless. Slight color may be noticeable when seen side-by-side with diamonds of better grades. Excellent value.';
            break;
        case 'G':
            $desc['color'] = 'Nearly colorless. Gemologists will be able to detect some color, but appears colorless to non-experts. Excellent value.';
            break;
        case 'F':
            $desc['color'] = 'Almost colorless, with slight color visible to expert gemologists. Still considered a very high quality grade.';
            break;
        case 'E':
            $desc['color'] = 'Almost completely colorless. So close to perfectly colorless, only expert gemologists may be able to detect any color.';
            break;
        case 'D':
            $desc['color'] = 'Completely colorless, displaying the most desirable icy white brilliance. Very rare in nature and the most expensive color.';
            break;
    }

    switch ($clarity) {
        case 'I2':
            $desc['clarity'] = 'Obvious inclusions which can be easily seen using a 10x loupe (and even with the naked eyes).';
            break;
        case 'I1':
            $desc['clarity'] = 'Included. Inclusions are very easy to see under 10x magnification and may be seen with the naked eye.';
            break;
        case 'SI3':
            $desc['clarity'] = 'Refer to diamonds that cannot be graded SI2 but do not have as many visible flaws as the stones graded I.';
            break;
        case 'SI2':
            $desc['clarity'] = 'Slightly included with more inclusions than SI1. Easily seen by an expert under 10x magnification but difficult to see with the naked eye.';
            break;
        case 'SI1':
            $desc['clarity'] = 'Slightly included. Easily seen by an expert under 10x magnification but difficult to see with the naked eye.';
            break;
        case 'VS2':
            $desc['clarity'] = 'Very slightly included. Easier for an expert to see inclusions under 10x magnification, and generally not visible to the naked eye; slightly more inclusions than VS1.';
            break;
        case 'VS1':
            $desc['clarity'] = 'Very slightly included. Easier for an expert to see inclusions under 10x magnification, and generally not visible to the naked eye.';
            break;
        case 'VVS2':
            $desc['clarity'] = 'Very, very slightly included. Extremely small inclusions are difficult for an expert to see under 10x magnification and invisible to the naked eye; slightly more inclusions than VVS1.';
            break;
        case 'VVS1':
            $desc['clarity'] = 'Very, very slightly included. Extremely small inclusions are difficult for an expert to see under 10x magnification and invisible to the naked eye.';
            break;
        case 'IF':
            $desc['clarity'] = 'Internally flawless. The highest clarity grade, with no flaws or inclusions. Very rare and expensive.';
            break;
        case 'FL':
            $desc['clarity'] = 'Flawless Diamonds. The highest grade of diamond clarity, with no visible inclusions or blemishes. Very rare and expensive.';
            break;
    }
    switch ($dtype) {
        case 'HPHT':
            $desc['dtype'] = "Diamonds are created by replicating the Earth's natural high-pressure, high-temperature conditions, resulting in diamonds that are chemically and optically identical to mined diamonds.";
            break;
        case 'CVD':
            $desc['dtype'] = 'Diamonds are grown layer by layer from carbon-rich gases in a controlled environment, producing high-quality diamonds with exceptional brilliance and purity.';
            break;
    }
    switch ($intensity) {
        case "1":
            $desc["intensity"] = "A light form of intensity that can be seen with the naked eye. Fancy Light diamonds allow for maximum light performance, while also showing significant color.";
            break;
          case "2":
            $desc["intensity"] = "The “Fancy” intensity indicates the diamond has significant color. The shade of the color is neither light nor too intense.";
            break;
          case "3":
            $desc["intensity"] = "Fancy Intense indicates very significant color. The color intensity can be clearer seen, as the color saturation is much more intense than the “Fancy” intensity grade.";
            break;
          case "4":
            $desc["intensity"] = "As the name indicated Fancy Vivid color diamonds have very strong color saturation. The Fancy Vivid is often the most expensive intensity grade.";
            break;
          case "5":
            $desc["intensity"] = "On of the darkest color tones available. Fancy Deep diamonds have almost full color saturation.";
            break;
          case "6":
            $desc["intensity"] = "The most intense color tone available. Fancy Dark diamond have full color saturation with minimal room for additional light properties. ";
            break;
          default:
            $desc["intensity"] = "";
    }

    return $desc;
}