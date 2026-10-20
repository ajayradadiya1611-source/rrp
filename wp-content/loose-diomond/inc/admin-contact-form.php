<?php

/* ------------------------------------------------------------------
 * Database table creation and storage helpers for contact submissions
 * ------------------------------------------------------------------ */

add_action('admin_init', 'ls_maybe_create_contact_table');
function ls_maybe_create_contact_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ls_contact_submissions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        form_type varchar(50) NOT NULL,
        name varchar(191) DEFAULT '' NOT NULL,
        email varchar(191) DEFAULT '' NOT NULL,
        phone varchar(50) DEFAULT '' NOT NULL,
        message text,
        reference_url varchar(255) DEFAULT '' NOT NULL,
        attachments longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function ls_store_submission($data = [])
{
    if (empty($data) || !is_array($data)) {
        return false;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ls_contact_submissions';

    $row = [
        'form_type'   => isset($data['form_type']) ? sanitize_text_field($data['form_type']) : 'contact',
        'name'        => isset($data['name']) ? sanitize_text_field($data['name']) : '',
        'email'       => isset($data['email']) ? sanitize_email($data['email']) : '',
        'phone'       => isset($data['phone']) ? sanitize_text_field($data['phone']) : '',
        'message'     => isset($data['message']) ? sanitize_textarea_field($data['message']) : '',
        'reference_url'=> isset($data['reference']) ? esc_url_raw($data['reference']) : '',
        'attachments' => isset($data['attachments']) ? maybe_serialize($data['attachments']) : '',
    ];

    $format = ['%s','%s','%s','%s','%s','%s','%s'];
    return (bool)$wpdb->insert($table, $row, $format);
}

/* ------------------------------------------------------------------
 * Admin menu and listing with simple pagination
 * ------------------------------------------------------------------ */
add_action('admin_menu', 'ls_contact_admin_menu');
function ls_contact_admin_menu()
{
    add_menu_page('Forms', 'Forms', 'manage_woocommerce', 'ls-contact-submissions', 'ls_contact_admin_page', 'dashicons-email', 26);
}

function ls_contact_admin_page()
{
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ls_contact_submissions';

    // Preserve current filters/paging across redirects
    $keep_args = function () {
        $args = [];
        if ( isset( $_GET['form_type'] ) ) $args['form_type'] = sanitize_text_field( wp_unslash( $_GET['form_type'] ) );
        if ( isset( $_GET['date_from'] ) ) $args['date_from'] = sanitize_text_field( wp_unslash( $_GET['date_from'] ) );
        if ( isset( $_GET['date_to'] ) ) $args['date_to'] = sanitize_text_field( wp_unslash( $_GET['date_to'] ) );
        if ( isset( $_GET['name'] ) ) $args['name'] = sanitize_text_field( wp_unslash( $_GET['name'] ) );
        if ( isset( $_GET['email'] ) ) $args['email'] = sanitize_email( wp_unslash( $_GET['email'] ) );
        if ( isset( $_GET['paged'] ) ) $args['paged'] = intval( $_GET['paged'] );
        return $args;
    };

    // Handle single deletion requests (GET link)
    if ( isset( $_GET['ls_delete'] ) ) {
        $del_id = intval( $_GET['ls_delete'] );
        $nonce   = isset( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '';
        if ( $del_id && $nonce && wp_verify_nonce( $nonce, 'ls_delete_submission_' . $del_id ) && current_user_can( 'manage_woocommerce' ) ) {
            $deleted = (bool) $wpdb->delete( $table, [ 'id' => $del_id ], [ '%d' ] );

            $redirect_args = $keep_args();
            $redirect_args['deleted'] = $deleted ? '1' : '0';

            wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php?page=ls-contact-submissions' ) ) );
            exit;
        } else {
            wp_die( 'Invalid delete request' );
        }
    }

    // Handle bulk deletion requests (POST form)
    if ( isset( $_POST['ls_bulk_action'] ) && $_POST['ls_bulk_action'] === 'delete' ) {
        if ( ! isset( $_POST['ls_bulk_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['ls_bulk_nonce'] ), 'ls_bulk_delete_submissions' ) ) {
            wp_die( 'Invalid request' );
        }
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'You do not have permission to do this.' );
        }

        $ids = isset( $_POST['submission_ids'] ) && is_array( $_POST['submission_ids'] )
            ? array_map( 'intval', wp_unslash( $_POST['submission_ids'] ) )
            : [];
        $ids = array_filter( array_unique( $ids ) );

        $bulk_deleted = 0;
        if ( ! empty( $ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            $sql = $wpdb->prepare( "DELETE FROM $table WHERE id IN ($placeholders)", $ids );
            $bulk_deleted = $wpdb->query( $sql );
            $bulk_deleted = ( $bulk_deleted === false ) ? 0 : (int) $bulk_deleted;
        }

        $redirect_args = $keep_args();
        $redirect_args['bulk_deleted'] = $bulk_deleted;

        wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php?page=ls-contact-submissions' ) ) );
        exit;
    }

    // Filters
    $form_type = isset($_GET['form_type']) ? sanitize_text_field($_GET['form_type']) : '';
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to   = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
    $name_search = isset($_GET['name']) ? sanitize_text_field($_GET['name']) : '';
    $email_search = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

    $where_clauses = [];
    $params = [];

    if ($form_type && $form_type !== 'all') {
        $where_clauses[] = 'form_type = %s';
        $params[] = $form_type;
    }

    if ($date_from) {
        $where_clauses[] = 'created_at >= %s';
        $params[] = $date_from . ' 00:00:00';
    }

    if ($date_to) {
        $where_clauses[] = 'created_at <= %s';
        $params[] = $date_to . ' 23:59:59';
    }

    if ( $name_search ) {
        $where_clauses[] = 'name LIKE %s';
        $params[] = '%' . $wpdb->esc_like( $name_search ) . '%';
    }

    if ( $email_search ) {
        $where_clauses[] = 'email LIKE %s';
        $params[] = '%' . $wpdb->esc_like( $email_search ) . '%';
    }

    $where_sql = $where_clauses ? implode(' AND ', $where_clauses) : '1=1';

    $per_page = 20;
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($paged - 1) * $per_page;

    // Count total with filters
    $count_sql = "SELECT COUNT(*) FROM $table WHERE $where_sql";
    if (!empty($params)) {
        $prepared_count = call_user_func_array([$wpdb, 'prepare'], array_merge([$count_sql], $params));
        $total = (int)$wpdb->get_var($prepared_count);
    } else {
        $total = (int)$wpdb->get_var($count_sql);
    }

    // Fetch rows with filters and pagination
    $select_sql = "SELECT * FROM $table WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $params2 = array_merge($params, [$per_page, $offset]);
    if (!empty($params)) {
        $prepared_select = call_user_func_array([$wpdb, 'prepare'], array_merge([$select_sql], $params2));
        $rows = $wpdb->get_results($prepared_select);
    } else {
        $rows = $wpdb->get_results($wpdb->prepare($select_sql, $per_page, $offset));
    }

    $base_url = admin_url('admin.php?page=ls-contact-submissions');

    echo '<div class="wrap"><h1>Contact Submissions</h1>';

    // Show deletion notices if present
    if ( isset( $_GET['deleted'] ) ) {
        if ( '1' === sanitize_text_field( wp_unslash( $_GET['deleted'] ) ) ) {
            echo '<div class="updated notice"><p>Submission deleted.</p></div>';
        } else {
            echo '<div class="error notice"><p>Failed to delete submission.</p></div>';
        }
    }
    if ( isset( $_GET['bulk_deleted'] ) ) {
        $bd = intval( $_GET['bulk_deleted'] );
        if ( $bd > 0 ) {
            echo '<div class="updated notice"><p>' . esc_html( $bd ) . ' submission(s) deleted.</p></div>';
        } else {
            echo '<div class="error notice"><p>No submissions were deleted.</p></div>';
        }
    }

    // Filter form
    echo '<form method="get" style="margin-bottom:15px;">';
    echo '<input type="hidden" name="page" value="ls-contact-submissions" />';
    echo '<label style="margin-right:8px;">Form Type:</label>';
    echo '<select name="form_type" style="margin-right:12px;">';
    $types = [ 'all' => 'All' ] + ls_form_type_labels();
    foreach ($types as $key => $label) {
        $sel = ($form_type === $key) ? ' selected' : '';
        echo '<option value="' . esc_attr($key) . '"' . $sel . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<label style="margin-right:8px;">Name:</label>';
    echo '<input type="text" name="name" value="' . esc_attr( $name_search ) . '" style="margin-right:12px;" />';
    echo '<label style="margin-right:8px;">Email:</label>';
    echo '<input type="search" name="email" value="' . esc_attr( $email_search ) . '" style="margin-right:12px;" />';
    echo '<label style="margin-right:8px;">Date from:</label>';
    echo '<input type="date" name="date_from" value="' . esc_attr($date_from) . '" style="margin-right:12px;" />';
    echo '<label style="margin-right:8px;">Date to:</label>';
    echo '<input type="date" name="date_to" value="' . esc_attr($date_to) . '" style="margin-right:12px;" />';
    echo '<input type="submit" class="button" value="Filter" /> ';
    echo '<a class="button" href="' . esc_url($base_url) . '">Reset</a>';
    echo '</form>';

    echo '<p>Total: ' . esc_html($total) . '</p>';

    if ($rows) {
        // Bulk action form wraps the whole table
        echo '<form method="post" id="ls-bulk-form">';
        wp_nonce_field( 'ls_bulk_delete_submissions', 'ls_bulk_nonce' );
        echo '<input type="hidden" name="ls_bulk_action" value="delete" />';

        echo '<div class="tablenav top">';
        echo '<div class="alignleft actions">';
        echo '<button type="submit" class="button" id="ls-bulk-delete-btn" onclick="return confirm(\'Delete the selected submissions? This cannot be undone.\');">Delete Selected</button>';
        echo '</div>';
        echo '</div>';

        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr>';
        echo '<th style="width:20px;"><input type="checkbox" id="ls-select-all" /></th>';
        echo '<th style="width:15px;">ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Attachments</th><th>Message</th><th>Reference</th><th>Submitted</th><th>Form</th><th>Actions</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($rows as $r) {
            $msg = nl2br( esc_html( $r->message ) );

            // Prepare attachments HTML
            $attachments_html = '';
            if ( ! empty( $r->attachments ) ) {
                $atts = maybe_unserialize( $r->attachments );
                if ( ! empty( $atts ) && is_array( $atts ) ) {
                    $uploads = wp_get_upload_dir();
                    $basedir = wp_normalize_path( $uploads['basedir'] );
                    $baseurl = $uploads['baseurl'];
                    foreach ( $atts as $att ) {
                        // If attachment is numeric -> treat as attachment ID
                        if ( is_numeric( $att ) ) {
                            $aid = intval( $att );
                            $url = wp_get_attachment_url( $aid );
                            $label = get_the_title( $aid );
                            if ( ! $label ) $label = basename( $url );
                            $attachments_html .= '<a href="' . esc_url( $url ) . '" target="_blank" style="margin-right:8px;">' . esc_html( $label ) . '</a>';
                            continue;
                        }

                        // Fallback: older stored file paths
                        $norm = wp_normalize_path( $att );
                        if ( strpos( $norm, $basedir ) === 0 ) {
                            $rel = substr( $norm, strlen( $basedir ) );
                            $url = $baseurl . str_replace( '\\', '/', $rel );
                        } else {
                            $url = str_replace( wp_normalize_path( ABSPATH ), site_url( '/' ), $norm );
                        }
                        $attachments_html .= '<a href="' . esc_url( $url ) . '" target="_blank" style="margin-right:8px;">' . esc_html( basename( $att ) ) . '</a>';
                    }
                }
            }

            // Prepare reference URL HTML
            $ref_html = '';
            if ( ! empty( $r->reference_url ) ) {
                $ref_host = wp_parse_url( $r->reference_url, PHP_URL_HOST );
                $ref_label = $ref_host ? $ref_host : $r->reference_url;
                $ref_html = '<a href="' . esc_url( $r->reference_url ) . '" target="_blank" rel="noopener" title="' . esc_attr( $r->reference_url ) . '">' . esc_html( $ref_label ) . '</a>';
            }

            echo '<tr>';
            echo '<td><input type="checkbox" class="ls-row-checkbox" name="submission_ids[]" value="' . esc_attr( $r->id ) . '" /></td>';
            echo '<td>' . esc_html($r->id) . '</td>';

            echo '<td>' . esc_html($r->name) . '</td>';
            echo '<td>' . esc_html($r->email) . '</td>';
            echo '<td>' . esc_html($r->phone) . '</td>';
            echo '<td>' . $attachments_html . '</td>';
            echo '<td>' . $msg . '</td>';
            echo '<td>' . $ref_html . '</td>';
            echo '<td>' . esc_html($r->created_at) . '</td>';
            echo '<td>' . esc_html( ls_form_type_label( $r->form_type ) ) . '</td>';
            // Actions (single delete, still via GET link)
            $del_args = [ 'ls_delete' => $r->id, '_wpnonce' => wp_create_nonce( 'ls_delete_submission_' . $r->id ) ];
            if ( $form_type ) $del_args['form_type'] = $form_type;
            if ( $date_from ) $del_args['date_from'] = $date_from;
            if ( $date_to ) $del_args['date_to'] = $date_to;
            if ( $name_search ) $del_args['name'] = $name_search;
            if ( $email_search ) $del_args['email'] = $email_search;
            if ( isset( $paged ) && $paged > 1 ) $del_args['paged'] = $paged;
            $delete_url = esc_url( add_query_arg( $del_args, $base_url ) );
            echo '<td><a href="' . $delete_url . '" onclick="return confirm(\'Are you sure you want to delete this submission?\');" class="button-link-delete">Delete</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</form>';

        // Select-all checkbox behavior
        echo '<script>
        (function(){
            var selectAll = document.getElementById("ls-select-all");
            if (!selectAll) return;
            selectAll.addEventListener("change", function () {
                var boxes = document.querySelectorAll(".ls-row-checkbox");
                boxes.forEach(function (b) { b.checked = selectAll.checked; });
            });
        })();
        </script>';

        $total_pages = (int)ceil($total / $per_page);

        echo '<div class="tablenav"><div class="tablenav-pages">';

        // build pagination args to preserve filters
        $pagination_args = [];
        if ($form_type) $pagination_args['form_type'] = $form_type;
        if ($date_from) $pagination_args['date_from'] = $date_from;
        if ($date_to) $pagination_args['date_to'] = $date_to;
        if ( $name_search ) $pagination_args['name'] = $name_search;
        if ( $email_search ) $pagination_args['email'] = $email_search;

        if ($paged > 1) {
            $prev_args = array_merge($pagination_args, ['paged' => $paged - 1]);
            $prev = add_query_arg($prev_args, $base_url);
            echo '<a class="prev-page" href="' . esc_url($prev) . '">&laquo; Previous</a> ';
        }

        echo ' Page ' . $paged . ' of ' . $total_pages . ' ';

        if ($paged < $total_pages) {
            $next_args = array_merge($pagination_args, ['paged' => $paged + 1]);
            $next = add_query_arg($next_args, $base_url);
            echo ' <a class="next-page" href="' . esc_url($next) . '">Next &raquo;</a>';
        }

        echo '</div></div>';

    } else {
        echo '<p>No submissions found.</p>';
    }

    echo '</div>';
}


/**
 * Display names for the stored form_type slugs (filter dropdown + list column).
 */
function ls_form_type_labels() {
    return [
        'contact'                => 'Contact Us',
        'consultation'           => 'Consultation Request',
        'ask-us'                 => 'Diamond Search Request',
        'ring-quote'             => 'Ring Quote Request',
        'custom-setting-request' => 'Custom Setting Request',
        'feedback'               => 'Feedback',
        'wholesale'              => 'Wholesale Inquiry',
        'video-request'          => 'Video Request',
    ];
}

function ls_form_type_label( $slug ) {
    $labels = ls_form_type_labels();
    return isset( $labels[ $slug ] ) ? $labels[ $slug ] : ucwords( str_replace( [ '-', '_' ], ' ', (string) $slug ) );
}
