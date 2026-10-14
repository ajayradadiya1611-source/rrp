<?php
/**
 * Inventory Admin — REST API (loaded via ../index.php)
 *
 * REST API backing the React admin replacement for:
 *   - inc-admin/ls-inventory (white diamonds, table: ls_inventory)
 *   - inc-admin/ls-inventory-color (colored diamonds, table: ls_inventory_c)
 *
 * Namespace: ls/v1
 *   GET    /ls/v1/inventory-admin/{type}              list (search, lot filter, sort, pagination)
 *   GET    /ls/v1/inventory-admin/{type}/{id}          single row
 *   POST   /ls/v1/inventory-admin/{type}               create
 *   PUT    /ls/v1/inventory-admin/{type}/{id}           update
 *   DELETE /ls/v1/inventory-admin/{type}/{id}           delete (also deletes the linked WC product)
 *   POST   /ls/v1/inventory-admin/{type}/{id}/status    change stock status (In Stock / Out of Stock)
 *   GET    /ls/v1/inventory-admin/{type}/meta           lookup lists for form dropdowns + lot numbers
 *
 * {type} is "white" (ls_inventory) or "color" (ls_inventory_c).
 *
 * Auth: uses WP cookie auth + nonce (same-origin admin app) via permission_callback below.
 * If the React app is served from a different origin, swap the permission_callback for an
 * application-password / JWT check and add the CORS block used in inc/ls-inventory-api.php.
 */

if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------------------------
 * Helpers (mirrors logic in inc-admin/ls-inventory/mst_diamonds.php)
 * ---------------------------------------------------------------------- */

function ls_admin_table_for_type($type) {
	return $type === 'color' ? 'ls_inventory_c' : 'ls_inventory';
}

function ls_admin_set_certificate_type($certificate) {
	$type = 0;
	if ($certificate != '') {
		if (strpos($certificate, 'igi') !== false) $type = 1;
		elseif (strpos($certificate, 'gia') !== false) $type = 2;
	}
	return $type;
}

function ls_admin_count_org_price($price, $per = 40) {
	$count_price = 0;
	if ($price != '' || $price != 0) {
		$count_price = ($price * $per) / 100;
		$count_price = $price + $count_price;
	}
	return round($count_price);
}

// Same shape-normalization rules as the legacy add/edit form.
function ls_admin_normalize_shape($shape) {
	$arr_rose = array("rose", "Rose", "Rose Pear", "Rose Oval", "Rose Marquise", "Rose Cushion");
	$arr_oeu  = array('old european cut', 'eurocut', 'oeu', 'old euro');
	$arr_omc  = array('old miner ', 'old mine', 'omc');

	$normalized = $shape;

	foreach ($arr_rose as $ar) {
		if (stripos($shape, $ar) !== false) { $normalized = "Rose Cut"; }
	}
	foreach ($arr_oeu as $ar) {
		if (stripos(strtolower($shape), $ar) !== false) { $normalized = "Old European"; }
	}
	foreach ($arr_omc as $ar) {
		if (stripos(strtolower($shape), $ar) !== false) { $normalized = "Old Mine"; }
	}

	return $normalized;
}

function ls_admin_build_product_name($shape_title, $carat, $color, $clarity) {
	if (str_contains($shape_title, 'Cut')) {
		$pname = $shape_title . ' ' . $carat . ' CARAT ' . $color . ' COLOR ' . $clarity . ' Clarity';
	} else {
		$pname = $shape_title . ' Cut ' . $carat . ' CARAT ' . $color . ' COLOR ' . $clarity . ' Clarity';
	}
	return $pname . ' Lab Grown Diamond';
}

/* -------------------------------------------------------------------------
 * Route registration
 * ---------------------------------------------------------------------- */

add_action('rest_api_init', function () {

	$permission = function () {
		return current_user_can('edit_products');
	};

	register_rest_route('ls/v1', '/inventory-admin/(?P<type>white|color)/meta', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => 'ls_admin_inventory_meta',
		'permission_callback' => $permission,
	]);

	register_rest_route('ls/v1', '/inventory-admin/(?P<type>white|color)', [
		[
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'ls_admin_inventory_list',
			'permission_callback' => $permission,
		],
		[
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => 'ls_admin_inventory_create',
			'permission_callback' => $permission,
		],
	]);

	register_rest_route('ls/v1', '/inventory-admin/(?P<type>white|color)/(?P<id>\d+)', [
		[
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'ls_admin_inventory_get',
			'permission_callback' => $permission,
		],
		[
			'methods'  => WP_REST_Server::EDITABLE, // PUT/PATCH
			'callback' => 'ls_admin_inventory_update',
			'permission_callback' => $permission,
		],
		[
			'methods'  => WP_REST_Server::DELETABLE,
			'callback' => 'ls_admin_inventory_delete',
			'permission_callback' => $permission,
		],
	]);

	register_rest_route('ls/v1', '/inventory-admin/(?P<type>white|color)/(?P<id>\d+)/status', [
		'methods'  => WP_REST_Server::CREATABLE,
		'callback' => 'ls_admin_inventory_change_status',
		'permission_callback' => $permission,
	]);
});

/* -------------------------------------------------------------------------
 * Meta: dropdown option lists + available lot numbers
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_meta(WP_REST_Request $request) {
	global $wpdb;
	$type  = $request['type'];
	$table = ls_admin_table_for_type($type);

	$common = [
		'cut'    => ['-' => 0, 'Good' => 1, 'Very Good' => 2, 'Excellent' => 3, 'Ideal' => 4],
		'clarity'=> ['I2' => 1, 'I1' => 2, 'SI3' => 3, 'SI2' => 4, 'SI1' => 5, 'VS2' => 6, 'VS1' => 7, 'VVS2' => 8, 'VVS1' => 9, 'IF' => 10, 'FL' => 11],
		'status' => ['In Stock' => 0, 'Out Of Stock' => 1],
		'sale'   => ['No' => 0, 'Yes' => 1],
		'shipping' => ['None' => 0, 'Express' => 1, 'Overnight' => 2, 'Express + Overnight' => 3],
		// NOTE: grading scales below (polish/symmetry/fluorescence/culet/girdle_thickness)
		// aren't documented anywhere in the legacy code — these are the standard GIA/IGI
		// scales mapped to sequential ints the same way `cut` already is. Confirm the
		// numeric codes match whatever your sheet-import / vendor feed already writes
		// into these columns before relying on them.
		'polish'    => ['-' => 0, 'Good' => 1, 'Very Good' => 2, 'Excellent' => 3, 'Ideal' => 4],
		'symmetry'  => ['-' => 0, 'Good' => 1, 'Very Good' => 2, 'Excellent' => 3, 'Ideal' => 4],
		'fluorescence' => ['None' => 0, 'Faint' => 1, 'Medium' => 2, 'Strong' => 3, 'Very Strong' => 4],
		'culet' => ['None' => 0, 'Very Small' => 1, 'Small' => 2, 'Medium' => 3, 'Slightly Large' => 4, 'Large' => 5, 'Very Large' => 6],
		'girdle_thickness' => ['Extremely Thin' => 0, 'Very Thin' => 1, 'Thin' => 2, 'Medium' => 3, 'Slightly Thick' => 4, 'Thick' => 5, 'Very Thick' => 6, 'Extremely Thick' => 7],
		'is_media' => ['No' => 0, 'Yes' => 1],
	];

	if ($type === 'color') {
		global $inv_color_c, $inv_intensity_c;
		$common['color']     = is_array($inv_color_c) ? $inv_color_c : ['yellow'=>1,'pink'=>2,'purple'=>3,'red'=>4,'blue'=>5,'green'=>6,'orange'=>7,'brown'=>8,'black'=>9,'gray'=>10];
		$common['intensity'] = is_array($inv_intensity_c) ? $inv_intensity_c : ['Fancy Light'=>1,'Fancy'=>2,'Fancy Intense'=>3,'Fancy Vivid'=>4,'Fancy Deep'=>5,'Fancy Dark'=>6];
	} else {
		$common['color'] = ['M'=>1,'L'=>2,'K'=>3,'J'=>4,'I'=>5,'H'=>6,'G'=>7,'F'=>8,'E'=>9,'D'=>10];
		$common['dtype'] = ['HPHT' => 'HPHT', 'CVD' => 'CVD'];
		$common['ha']    = ['No' => 0, 'Yes' => 1];
	}

	$lot_numbers = $wpdb->get_col("SELECT DISTINCT `lot_number` FROM `{$table}` WHERE status = 0 AND lot_number != 0 AND lot_number IS NOT NULL ORDER BY lot_number");

	// lot numbers available to assign on "add" (from the lot list table, mirrors legacy form)
	$available_lots = $wpdb->get_col("SELECT `lot_no` FROM `ls_inventory_lot` ORDER BY lid DESC");

	return rest_ensure_response([
		'options'         => $common,
		'lot_numbers'     => array_values($lot_numbers),
		'available_lots'  => array_values($available_lots),
	]);
}

/* -------------------------------------------------------------------------
 * List (search + lot filter + sort + pagination) — mirrors phili_List_Table::prepare_items
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_list(WP_REST_Request $request) {
	global $wpdb;
	$type  = $request['type'];
	$table = ls_admin_table_for_type($type);

	$per_page = max(1, min(200, (int) $request->get_param('per_page') ?: 50));
	$page     = max(1, (int) $request->get_param('page') ?: 1);
	$search   = trim((string) $request->get_param('search'));
	$lotnum   = (string) $request->get_param('lot_number');
	$orderby  = (string) $request->get_param('orderby') ?: 'id';
	$order    = strtolower((string) $request->get_param('order')) === 'desc' ? 'DESC' : 'ASC';

	$sortable = ['id','sku','shape','price','sale_price','org_price','carat','cut','color','clarity','video','certificate','sale','status','dtype','av_table','depth','lwratio','vendor_name','stock_num','polish','symmetry','fluorescence','delivery_days','created_at','updated_at'];
	if (!in_array($orderby, $sortable, true)) $orderby = 'id';

	$where = '1=1';
	$params = [];

	if ($search !== '') {
		$search_keyword = str_replace(' ', '', str_replace('|', '&', $search));
		$replace_lg = ["LG", "LG-", "lg", "lg-", "Lg", "Lg-", "lG", "lG-"];
		$ls_sku = str_replace($replace_lg, '', $search_keyword);

		$where .= " AND ( (sku = %s OR sku = %s OR sku = %s) OR (shape LIKE %s) OR (certificate LIKE %s) )";
		$params[] = $ls_sku;
		$params[] = 'LG' . $ls_sku;
		$params[] = 'LG-' . $ls_sku;
		$params[] = '%' . $wpdb->esc_like($search_keyword) . '%';
		$params[] = '%' . $wpdb->esc_like($search_keyword) . '%';
	}

	if ($lotnum !== '' && $lotnum !== 'all') {
		$where .= " AND (lot_number = %s)";
		$params[] = $lotnum;
	}

	$count_sql = "SELECT COUNT(id) FROM `{$table}` WHERE {$where}";
	$total = (int) ($params ? $wpdb->get_var($wpdb->prepare($count_sql, $params)) : $wpdb->get_var($count_sql));

	$offset = ($page - 1) * $per_page;
	$data_sql = "SELECT * FROM `{$table}` WHERE {$where} ORDER BY `{$orderby}` {$order} LIMIT %d OFFSET %d";
	$data_params = array_merge($params, [$per_page, $offset]);
	$rows = $wpdb->get_results($wpdb->prepare($data_sql, $data_params), ARRAY_A);

	return rest_ensure_response([
		'items'       => $rows,
		'total'       => $total,
		'page'        => $page,
		'per_page'    => $per_page,
		'total_pages' => (int) ceil($total / $per_page),
	]);
}

function ls_admin_inventory_get(WP_REST_Request $request) {
	global $wpdb;
	$table = ls_admin_table_for_type($request['type']);
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$table}` WHERE id = %d", (int) $request['id']), ARRAY_A);

	if (!$row) {
		return new WP_Error('not_found', 'Diamond not found', ['status' => 404]);
	}
	return rest_ensure_response($row);
}

/* -------------------------------------------------------------------------
 * Create — mirrors the $_GET['action']=='add' branch
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_create(WP_REST_Request $request) {
	global $wpdb;
	$type  = $request['type'];
	$table = ls_admin_table_for_type($type);
	$body  = $request->get_json_params();

	$lot_number = trim((string) ($body['lot_number'] ?? ''));
	if ($lot_number === '') {
		return new WP_Error('missing_lot_number', 'Lot Number is required', ['status' => 400]);
	}

	$sku          = trim((string) ($body['sku'] ?? ''));
	$shape_input  = trim((string) ($body['shape'] ?? ''));
	$shape        = ls_admin_normalize_shape($shape_input);
	$shape_title  = ($type === 'color') ? trim((string) ($body['shape_title'] ?? $shape_input)) : $shape_input;

	$price     = trim((string) ($body['price'] ?? ''));
	$org_price = trim((string) ($body['org_price'] ?? ''));
	$sale_price = trim((string) ($body['sale_price'] ?? ''));
	$rap       = trim((string) ($body['rap'] ?? ''));
	$carat     = trim((string) ($body['carat'] ?? ''));
	$cut       = $body['cut'] ?? '';
	$color     = $body['color'] ?? '';
	$clarity   = $body['clarity'] ?? '';
	$video     = trim((string) ($body['video'] ?? ''));
	$certificate = trim((string) ($body['certificate'] ?? ''));
	$certificate_type = trim((string) ($body['certificate_type'] ?? ''));;
	$mm        = trim((string) ($body['mm'] ?? ''));
	$note      = $body['note'] ?? '';
	$status    = $body['status'] ?? 0;
	$sale      = $body['sale'] ?? 0;
	$av_table  = $body['av_table'] ?? '';
	$depth     = $body['depth'] ?? '';
	$lwratio   = $body['lwratio'] ?? '';
	$shipping  = intval($body['shipping'] ?? 0);

	// Vendor / sourcing
	$sheet_provider = trim((string) ($body['sheet_provider'] ?? ''));
	$vendor_id      = trim((string) ($body['vendor_id'] ?? ''));
	$vendor_name    = trim((string) ($body['vendor_name'] ?? ''));
	$vendor_city    = trim((string) ($body['vendor_city'] ?? ''));
	$vendor_country = trim((string) ($body['vendor_country'] ?? ''));
	$stock_num      = trim((string) ($body['stock_num'] ?? ''));

	// Advanced grading
	$polish           = $body['polish'] ?? 0;
	$symmetry         = $body['symmetry'] ?? 0;
	$fluorescence     = $body['fluorescence'] ?? 0;
	$culet            = $body['culet'] ?? 0;
	$girdle_thickness = $body['girdle_thickness'] ?? 0;
	$crown_angle      = $body['crown_angle'] ?? null;
	$pavilion_angle   = $body['pavilion_angle'] ?? null;

	// Physical measurements
	$length = $body['length'] ?? null;
	$width  = $body['width'] ?? null;
	$height = $body['height'] ?? null;

	// Media / logistics
	$image         = trim((string) ($body['image'] ?? ''));
	$delivery_days = $body['delivery_days'] ?? null;
	$is_media      = $body['is_media'] ?? 0;

	if ($org_price === '') {
		$org_price = ($shape === 'ROUND') ? ls_admin_count_org_price($price, 30) : ls_admin_count_org_price($price);
	} else {
		$org_price = round((float) $org_price);
	}

	$insert = [
		'pid' => 0, 'sku' => $sku, 'shape' => $shape, 'price' => $price, 'org_price' => $org_price,
		'sale_price' => $sale_price !== '' ? $sale_price : $price, 'rap' => $rap,
		'carat' => $carat, 'cut' => $cut, 'color' => $color, 'clarity' => $clarity, 'video' => $video,
		'certificate' => $certificate, 'certificate_type' => $certificate_type, 'mm' => $mm,
		'lot_number' => $lot_number, 'note' => $note, 'status' => 0, 'shape_title' => $shape_title,
		'sale' => $sale, 'av_table' => $av_table, 'depth' => $depth, 'lwratio' => $lwratio,
		'shipping' => $shipping,
		'sheet_provider' => $sheet_provider, 'vendor_id' => $vendor_id, 'vendor_name' => $vendor_name,
		'vendor_city' => $vendor_city, 'vendor_country' => $vendor_country, 'stock_num' => $stock_num,
		'polish' => $polish, 'symmetry' => $symmetry, 'fluorescence' => $fluorescence,
		'culet' => $culet, 'girdle_thickness' => $girdle_thickness,
		'crown_angle' => $crown_angle, 'pavilion_angle' => $pavilion_angle,
		'length' => $length, 'width' => $width, 'height' => $height,
		'image' => $image, 'delivery_days' => $delivery_days, 'is_media' => $is_media,
	];

	if ($type === 'white') {
		$insert['dtype'] = $body['dtype'] ?? '';
		$insert['ha']    = $body['ha'] ?? 0;
	} else {
		$insert['intensity'] = $body['intensity'] ?? '';
	}

	$ok = $wpdb->insert($table, $insert);
	if ($ok === false) {
		return new WP_Error('db_error', $wpdb->last_error, ['status' => 500]);
	}

	// Keep the lot's last-updated timestamp in sync (legacy behaviour).
	$wpdb->update('ls_inventory_lot', ['last_udate' => current_time('mysql')], ['lot_no' => $lot_number]);

	$new_id = $wpdb->insert_id;
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$table}` WHERE id = %d", $new_id), ARRAY_A);
	return rest_ensure_response($row);
}

/* -------------------------------------------------------------------------
 * Update — mirrors the isset($_POST['update']) branch, incl. WC product meta sync
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_update(WP_REST_Request $request) {
	global $wpdb;
	$type  = $request['type'];
	$table = ls_admin_table_for_type($type);
	$id    = (int) $request['id'];
	$body  = $request->get_json_params();

	$existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$table}` WHERE id = %d", $id), ARRAY_A);
	if (!$existing) {
		return new WP_Error('not_found', 'Diamond not found', ['status' => 404]);
	}

	$pid = (int) $existing['pid'];

	$shape_input = trim((string) ($body['shape'] ?? $existing['shape']));
	$shape       = ls_admin_normalize_shape($shape_input);
	$shape_title = ($type === 'color') ? trim((string) ($body['shape_title'] ?? $shape_input)) : $shape_input;

	$price     = $body['price'] ?? $existing['price'];
	$org_price = $body['org_price'] ?? '';
	$certificate = trim((string) ($body['certificate'] ?? $existing['certificate']));
	$certificate_type = trim((string) ($body['certificate_type'] ?? $existing['certificate_type']));

	if ($org_price === '') {
		$org_price = ($shape === 'ROUND') ? ls_admin_count_org_price($price, 30) : ls_admin_count_org_price($price);
	} else {
		$org_price = round((float) $org_price);
	}

	$update = [
		'sku' => $body['sku'] ?? $existing['sku'],
		'shape' => $shape,
		'price' => $price,
		'org_price' => $org_price,
		'sale_price' => $body['sale_price'] ?? $existing['sale_price'] ?? $price,
		'rap' => $body['rap'] ?? $existing['rap'] ?? '',
		'carat' => $body['carat'] ?? $existing['carat'],
		'cut' => $body['cut'] ?? $existing['cut'],
		'color' => $body['color'] ?? $existing['color'],
		'clarity' => $body['clarity'] ?? $existing['clarity'],
		'video' => $body['video'] ?? $existing['video'],
		'certificate' => $certificate,
		'certificate_type' => $certificate_type,
		'mm' => $body['mm'] ?? $existing['mm'],
		'note' => $body['note'] ?? $existing['note'],
		'status' => $body['status'] ?? $existing['status'],
		'shape_title' => $shape_title,
		'sale' => $body['sale'] ?? $existing['sale'],
		'av_table' => $body['av_table'] ?? $existing['av_table'],
		'depth' => $body['depth'] ?? $existing['depth'],
		'lwratio' => $body['lwratio'] ?? $existing['lwratio'],
		'shipping' => intval($body['shipping'] ?? $existing['shipping']),
		'sheet_provider' => $body['sheet_provider'] ?? ($existing['sheet_provider'] ?? ''),
		'vendor_id' => $body['vendor_id'] ?? ($existing['vendor_id'] ?? ''),
		'vendor_name' => $body['vendor_name'] ?? ($existing['vendor_name'] ?? ''),
		'vendor_city' => $body['vendor_city'] ?? ($existing['vendor_city'] ?? ''),
		'vendor_country' => $body['vendor_country'] ?? ($existing['vendor_country'] ?? ''),
		'stock_num' => $body['stock_num'] ?? ($existing['stock_num'] ?? ''),
		'polish' => $body['polish'] ?? ($existing['polish'] ?? 0),
		'symmetry' => $body['symmetry'] ?? ($existing['symmetry'] ?? 0),
		'fluorescence' => $body['fluorescence'] ?? ($existing['fluorescence'] ?? 0),
		'culet' => $body['culet'] ?? ($existing['culet'] ?? 0),
		'girdle_thickness' => $body['girdle_thickness'] ?? ($existing['girdle_thickness'] ?? 0),
		'crown_angle' => $body['crown_angle'] ?? ($existing['crown_angle'] ?? null),
		'pavilion_angle' => $body['pavilion_angle'] ?? ($existing['pavilion_angle'] ?? null),
		'length' => $body['length'] ?? ($existing['length'] ?? null),
		'width' => $body['width'] ?? ($existing['width'] ?? null),
		'height' => $body['height'] ?? ($existing['height'] ?? null),
		'image' => $body['image'] ?? ($existing['image'] ?? ''),
		'delivery_days' => $body['delivery_days'] ?? ($existing['delivery_days'] ?? null),
		'is_media' => $body['is_media'] ?? ($existing['is_media'] ?? 0),
	];

	if ($type === 'white') {
		$update['dtype'] = $body['dtype'] ?? ($existing['dtype'] ?? '');
		$update['ha']    = $body['ha'] ?? ($existing['ha'] ?? 0);
	} else {
		$update['intensity'] = $body['intensity'] ?? ($existing['intensity'] ?? '');
	}

	$ok = $wpdb->update($table, $update, ['id' => $id]);
	if ($ok === false) {
		return new WP_Error('db_error', $wpdb->last_error, ['status' => 500]);
	}

	// Sync linked WooCommerce product meta, same as legacy admin form.
	if ($pid) {
		$stock = 'instock';
		$stock_qty = 1;
		if ((int) $update['status'] === 1) {
			$stock = 'outofstock';
			$stock_qty = 0;
		}
		update_post_meta($pid, '_stock_status', $stock);
		update_post_meta($pid, '_stock', $stock_qty);
		update_post_meta($pid, '_sku', $update['sku']);
		update_post_meta($pid, '_price', $update['price']);

		if ($update['org_price'] !== '' && (float) $update['org_price'] !== 0.0) {
			update_post_meta($pid, '_regular_price', $update['org_price']);
			update_post_meta($pid, '_sale_price', $update['price']);
		} else {
			update_post_meta($pid, '_regular_price', $update['price']);
		}
	}

	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$table}` WHERE id = %d", $id), ARRAY_A);
	return rest_ensure_response($row);
}

/* -------------------------------------------------------------------------
 * Delete — mirrors case 'dodeletesingle': deletes the WC product + inventory row
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_delete(WP_REST_Request $request) {
	global $wpdb;
	$table = ls_admin_table_for_type($request['type']);
	$id = (int) $request['id'];

	$row = $wpdb->get_row($wpdb->prepare("SELECT pid FROM `{$table}` WHERE id = %d", $id));
	if (!$row) {
		return new WP_Error('not_found', 'Diamond not found', ['status' => 404]);
	}

	if ($row->pid) {
		wp_delete_post((int) $row->pid, true);
	}
	$wpdb->delete($table, ['id' => $id]);

	return rest_ensure_response(['deleted' => true, 'id' => $id]);
}

/* -------------------------------------------------------------------------
 * Inline stock-status toggle — mirrors ls_change_inv_pstatus AJAX handler
 * ---------------------------------------------------------------------- */

function ls_admin_inventory_change_status(WP_REST_Request $request) {
	global $wpdb;
	$table = ls_admin_table_for_type($request['type']);
	$id = (int) $request['id'];
	$body = $request->get_json_params();
	$p_status = absint($body['status'] ?? 0);

	$row = $wpdb->get_row($wpdb->prepare("SELECT pid FROM `{$table}` WHERE id = %d", $id));
	if (!$row) {
		return new WP_Error('not_found', 'Diamond not found', ['status' => 404]);
	}

	$updated = $wpdb->update($table, ['status' => $p_status], ['id' => $id]);
	if ($updated === false) {
		return new WP_Error('db_error', $wpdb->last_error, ['status' => 500]);
	}

	if ($row->pid) {
		$stock = $p_status == 1 ? 'outofstock' : 'instock';
		$stock_qty = $p_status == 1 ? 0 : 1;
		update_post_meta((int) $row->pid, '_stock_status', $stock);
		update_post_meta((int) $row->pid, '_stock', $stock_qty);
	}

	return rest_ensure_response(['status' => true, 'msg' => 'Status Updated']);
}
