<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>
<p>
<?php
echo wp_kses_post(
	/**
	 * Filter to modify order detiails status text.
	 *
	 * @param string $order_status The order status text.
	 *
	 * @since 10.1.0
	 */
	apply_filters(
		'woocommerce_order_details_status',
		sprintf(
		/* translators: 1: order number 2: order date 3: order status */
			esc_html__( 'Order %1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
			'<mark class="order-number">#' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		),
		$order
	)
);

?>
</p>

<?php if ( $notes ) : ?>
	<h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>


<?php     

$order_id = $order->get_id();
$order    = wc_get_order($order_id);
 
if ($order_id > '1921867') {
    $order_status              = $order->get_status();
    $order_has_item_need_tomade = false;

    $valid_statuses = [
        'order-received', 'order-confirmed', 'cad-3d-design', 'crafting',
        'quality-check', 'dispatched', 'shipped', 'shipped-shiv', 'delivered'
    ];

    if (in_array($order_status, $valid_statuses)) {

        // ── 1. Build steps array ──────────────────────────────────────────────
        $steps = [
            ['key' => 'order-received',  'label' => 'Order Received'],
            ['key' => 'order-confirmed', 'label' => 'Order Confirmed'],
        ];

        if ($order_has_item_need_tomade) {
            $steps[] = ['key' => 'cad-3d-design', 'label' => 'CAD & 3D Design'];
            $steps[] = ['key' => 'crafting',       'label' => 'Crafting'];
            $steps[] = ['key' => 'quality-check',  'label' => 'Quality Check (QC)'];
        }

        $steps[] = ['key' => 'dispatched', 'label' => 'Dispatched from Factory'];
        $steps[] = ['key' => 'shipped',    'label' => 'Shipped'];
        $steps[] = ['key' => 'delivered',  'label' => 'Delivered'];

        // ── 2. Map current status → step index ───────────────────────────────
        if ($order_has_item_need_tomade) {
            $status_map = [
                'order-received'  => 0,
                'order-confirmed' => 1,
                'cad-3d-design'   => 2,
                'crafting'        => 3,
                'quality-check'   => 4,
                'dispatched'      => 5,
                'shipped'         => 6,
                'shipped-shiv'    => 6,
                'delivered'       => 7,
            ];
        } else {
            $status_map = [
                'order-received'  => 0,
                'order-confirmed' => 1,
                'dispatched'      => 2,
                'shipped'         => 3,
                'shipped-shiv'    => 3,
                'delivered'       => 4,
            ];
        }

        $current_index = $status_map[$order_status]+1 ?? 1;
        $total_steps   = count($steps);
        ?>

        <section class="lswc_order_status py-4">
            <h3 class="mb-4">Order Status</h3>

            <div class="ot-container">
                <?php foreach ($steps as $index => $step):

                    // ── Circle state ─────────────────────────────────────────
                    if ($index < $current_index) {
                        $state = 'done';
                    } elseif ($index === $current_index) {
                        $state = 'active';
                    } else {
                        $state = 'pending';
                    }

                    // ── Connector line class (not needed on last step) ────────
                    $line_class = '';
                    if ($index < $total_steps - 1) {
                        $line_class = ($index + 1 <= $current_index) ? 'line-done' : 'line-pending';
                    }

                    // ── Date from order meta (optional) ──────────────────────
                    // Save dates with: update_post_meta($order_id, 'status_date_order-received', '30 Mar, 2026');
                    $date      = get_post_meta($order_id, 'status_date_' . $step['key'], true);
                    $date_done = ($date && $index <= $current_index) ? 'done' : '';
                ?>
                <div class="ot-step <?php echo esc_attr($line_class); ?>">

                    <div class="ot-circle <?php echo esc_attr($state); ?>">
                        <?php if ($state === 'done'): ?>
                            <span class="check-icon">&#10003;</span>
                        <?php else: ?>
                            <?php echo $index + 1; ?>
                        <?php endif; ?>
                    </div>

                    <div class="ot-label <?php echo esc_attr($state); ?>">
                        <?php echo esc_html($step['label']); ?>
                    </div>

                    <div class="ot-date <?php echo esc_attr($date_done); ?>">
                        <?php echo esc_html($date); ?>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <?php
            $custom_order_note = get_post_meta($order_id, 'custom_order_note', true);
            if ($custom_order_note): ?>
                <div class="alert alert-info d-flex align-items-start gap-2 mt-4" role="alert">
                    <i class="fa-solid fa-circle-info mt-1"></i>
                    <div><?php echo wp_kses_post($custom_order_note); ?></div>
                </div>
            <?php endif; ?>

        </section>
        <?php
    }
}
?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
