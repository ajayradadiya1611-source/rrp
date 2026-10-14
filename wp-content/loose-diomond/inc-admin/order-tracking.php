<?php
add_action('woocommerce_admin_order_data_after_shipping_address', function($order) {
   $tracking_numbers = $order->get_meta('_tracking_numbers');

    // Backwards compatibility: if old single meta exists, convert to array for display
    if (empty($tracking_numbers)) {
        $old_number = $order->get_meta('_tracking_numbers');
        $old_carrier = $order->get_meta('_carrier');
        if ($old_number || $old_carrier) {
            $tracking_numbers = array(array('carrier' => $old_carrier, 'number' => $old_number));
        }
    }
    if (!is_array($tracking_numbers)) $tracking_numbers = array();
    ?>
    <p><strong>Shipment Tracking</strong></p>

    <div id="tracking-list">
        <?php foreach ($tracking_numbers as $idx => $t):
            $carrier = isset($t['carrier']) ? $t['carrier'] : '';
            $number = isset($t['number']) ? $t['number'] : '';
        ?>
        <div class="tracking-row" style="margin-bottom:6px;">
            <select name="tracking_numbers[<?php echo $idx; ?>][carrier]">
                <option value="" <?php selected($carrier, ''); ?>>-- Select Carrier --</option>
                <option value="FedEx" <?php selected($carrier, 'FedEx'); ?>>FedEx</option>
                <option value="UPS" <?php selected($carrier, 'UPS'); ?>>UPS</option>
                <option value="USPS" <?php selected($carrier, 'USPS'); ?>>USPS</option>
                <option value="Aramex" <?php selected($carrier, 'Aramex'); ?>>Aramex</option>
                <option value="Au post" <?php selected($carrier, 'Au post'); ?>>Au post</option>
            </select>
            <input type="text" name="tracking_numbers[<?php echo $idx; ?>][number]" value="<?php echo esc_attr($number); ?>" placeholder="Tracking number" />
            <button class="remove-tracking button">Remove</button>
        </div>
        <?php endforeach; ?>
    </div>

    <p>
        <button id="add-tracking" class="button">Add Tracking</button>
    </p>

    <div id="tracking-template" style="display:none;">
        <div class="tracking-row" style="margin-bottom:6px;">
            <select name="__name__[carrier]">
                <option value="">-- Select Carrier --</option>
                <option value="FedEx">FedEx</option>
                <option value="UPS">UPS</option>
                <option value="USPS">USPS</option>
                <option value="Aramex">Aramex</option>
                <option value="Au post">Au post</option>
            </select>
            <input type="text" name="__name__[number]" placeholder="Tracking number" />
            <button class="remove-tracking button">Remove</button>
        </div>
    </div>

    <script type="text/javascript">
        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                var list = document.getElementById('tracking-list');
                var addBtn = document.getElementById('add-tracking');
                var template = document.getElementById('tracking-template').innerHTML;

                function reindex(){
                    var rows = list.querySelectorAll('.tracking-row');
                    rows.forEach(function(row, i){
                        var inputs = row.querySelectorAll('select, input');
                        inputs.forEach(function(inp){
                            var name = inp.getAttribute('name');
                            if (!name) return;
                            var newName = name.replace(/tracking_numbers\[.*?\]/, 'tracking_numbers['+i+']');
                            if (newName === name) {
                                // handle template-style __name__
                                newName = name.replace('__name__', 'tracking_numbers['+i+']');
                            }
                            inp.setAttribute('name', newName);
                        });
                    });
                }

                function bindRemove(btn){
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        var row = btn.closest('.tracking-row');
                        if (row) { row.parentNode.removeChild(row); reindex(); }
                    });
                }

                // bind existing remove buttons
                Array.prototype.forEach.call(document.querySelectorAll('.remove-tracking'), bindRemove);

                addBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    var idx = list.querySelectorAll('.tracking-row').length;
                    var html = template.replace(/__name__/g, 'tracking_numbers['+idx+']');
                    var tmp = document.createElement('div');
                    tmp.innerHTML = html;
                    var node = tmp.firstElementChild;
                    list.appendChild(node);
                    bindRemove(node.querySelector('.remove-tracking'));
                });
            });
        })();
    </script>
    <?php
});
add_action('woocommerce_process_shop_order_meta', function($order_id) {

    $order = wc_get_order($order_id);

    // Save multiple tracking numbers
    if (isset($_POST['tracking_numbers']) && is_array($_POST['tracking_numbers'])) {
        $clean = array();

        foreach ($_POST['tracking_numbers'] as $t) {
            if (!is_array($t)) continue;

            $carrier = isset($t['carrier']) ? sanitize_text_field($t['carrier']) : '';
            $number  = isset($t['number']) ? sanitize_text_field($t['number']) : '';

            if ($carrier || $number) {
                $clean[] = array(
                    'carrier' => $carrier,
                    'number'  => $number
                );
            }
        }

        if (!empty($clean)) {
            $order->update_meta_data('_tracking_numbers', $clean);
        } else {
            $order->delete_meta_data('_tracking_numbers');
        }
    } else {
        $order->delete_meta_data('_tracking_numbers');
    }

    // Backward compatibility
    if (isset($_POST['tracking_number'])) {
        $order->update_meta_data('_tracking_number', sanitize_text_field($_POST['tracking_number']));
    }

    if (isset($_POST['carrier'])) {
        $order->update_meta_data('_carrier', sanitize_text_field($_POST['carrier']));
    }

    // IMPORTANT: save changes
    $order->save();
});

function ld_get_order_tracking_numbers($order) {
    if (! $order) return array();

    $tracking_numbers = $order->get_meta('_tracking_numbers');

    if (empty($tracking_numbers)) {
        $old_number = $order->get_meta('_tracking_number');
        $old_carrier = $order->get_meta('_carrier');
        if ($old_number || $old_carrier) {
            $tracking_numbers = array(array('carrier' => $old_carrier, 'number' => $old_number));
        }
    }

    return is_array($tracking_numbers) ? $tracking_numbers : array();
}

function ld_add_tracking_admin_order_column($columns) {
    $new_columns = array();

    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;

        if ('order_status' === $key) {
            $new_columns['ld_tracking'] = __('Tracking', 'loose-diomond');
        }
    }

    if (! isset($new_columns['ld_tracking'])) {
        $new_columns['ld_tracking'] = __('Tracking', 'loose-diomond');
    }

    return $new_columns;
}

add_filter('manage_edit-shop_order_columns', 'ld_add_tracking_admin_order_column', 20);
add_filter('manage_woocommerce_page_wc-orders_columns', 'ld_add_tracking_admin_order_column', 20);

function ld_render_tracking_admin_order_column($order) {
    $tracking_numbers = ld_get_order_tracking_numbers($order);

    if (empty($tracking_numbers)) {
        echo '&mdash;';
        return;
    }

    foreach ($tracking_numbers as $t) {
        $carrier = isset($t['carrier']) ? $t['carrier'] : '';
        $number = isset($t['number']) ? $t['number'] : '';

        if (!$carrier && !$number) continue;

        $link = get_tracking_link($carrier, $number);
        $label = trim($carrier . ' ' . $number);

        if ($link && '#' !== $link) {
            echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_html($label) . '</a><br />';
        } else {
            echo esc_html($label) . '<br />';
        }
    }
}

add_action('manage_shop_order_posts_custom_column', function($column, $post_id) {
    if ('ld_tracking' !== $column) return;

    ld_render_tracking_admin_order_column(wc_get_order($post_id));
}, 20, 2);

add_action('manage_woocommerce_page_wc-orders_custom_column', function($column, $order) {
    if ('ld_tracking' !== $column) return;

    ld_render_tracking_admin_order_column($order);
}, 20, 2);

add_action('woocommerce_order_details_before_order_table', function($order) {
    $tracking_numbers = $order->get_meta('_tracking_numbers');

    // Backwards compatibility
    if (empty($tracking_numbers)) {
        $old_number = $order->get_meta('_tracking_numbers');
        $old_carrier = $order->get_meta('_carrier');
        if ($old_number || $old_carrier) {
            $tracking_numbers = array(array('carrier' => $old_carrier, 'number' => $old_number));
        }
    }

    if (!empty($tracking_numbers) && is_array($tracking_numbers)) {
        echo "<p><strong>Tracking:</strong></p><ul>";
        foreach ($tracking_numbers as $t) {
            $carrier = isset($t['carrier']) ? $t['carrier'] : '';
            $number = isset($t['number']) ? $t['number'] : '';
            if (!$carrier && !$number) continue;
            $link = get_tracking_link($carrier, $number);
            $safe_carrier = esc_html($carrier);
            $safe_number = esc_html($number);
            $safe_link = esc_url($link);
            echo '<li>' . $safe_carrier . ' - <a class="tracking-link" href="' . $safe_link . '" target="_blank" rel="noopener noreferrer">' . $safe_number . '</a></li>';
        }
        echo "</ul>";
    }
});

/**
 * Add a "Track" action to orders displayed in My Account (orders list).
 * Supports both the common WooCommerce filter and the newer wc_get_account_orders_actions filter.
 */
function ld_add_tracking_to_account_orders_actions($actions, $order) {
    if (! $order) return $actions;

    $tracking_numbers = $order->get_meta('_tracking_numbers');

    // Backwards compatibility
    if (empty($tracking_numbers)) {
        $old_number = $order->get_meta('_tracking_number');
        $old_carrier = $order->get_meta('_carrier');
        if ($old_number || $old_carrier) {
            $tracking_numbers = array(array('carrier' => $old_carrier, 'number' => $old_number));
        }
    }

    if (!empty($tracking_numbers) && is_array($tracking_numbers)) {
        // Use the first tracking entry for the quick action
        $t = $tracking_numbers[0];
        $carrier = isset($t['carrier']) ? $t['carrier'] : '';
        $number = isset($t['number']) ? $t['number'] : '';
        if ($carrier || $number) {
            $link = get_tracking_link($carrier, $number);
            if ($link && $link !== '#') {
                $actions['tracking'] = array(
                    'url'  => esc_url($link),
                    'name' => __('Track', 'loose-diomond'),
                );
            }
        }
    }

    return $actions;
}

add_filter('woocommerce_my_account_my_orders_actions', 'ld_add_tracking_to_account_orders_actions', 10, 2);
add_filter('wc_get_account_orders_actions', 'ld_add_tracking_to_account_orders_actions', 10, 2);
// Ensure the frontend My Account 'Track' action opens in a new tab
add_action('wp_footer', function() {
    if (is_admin()) return;
    if (!function_exists('is_account_page') || !is_account_page()) return;
    ?>
    <script type="text/javascript">
        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                var els = document.querySelectorAll('a.tracking');
                els.forEach(function(a){
                    a.setAttribute('target', '_blank');
                    a.setAttribute('rel', 'noopener noreferrer');
                });
            });
        })();
    </script>
    <?php
});
function get_tracking_link($carrier, $tracking_number) {
    switch ($carrier) {
        case 'FedEx':
            return "https://www.fedex.com/fedextrack/?tracknumbers=$tracking_number";
        case 'UPS':
            return "https://www.ups.com/track?tracknum=$tracking_number";
        case 'USPS':
            return "https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=$tracking_number";
        case 'Aramex':
            return "https://www.aramex.com/track/results?ShipmentNumber=$tracking_number";
        case 'Au post':
            return "https://auspost.com.au/mypost/track/search?ShipmentNumber=$tracking_number";
            
    }
    return '#';
}
