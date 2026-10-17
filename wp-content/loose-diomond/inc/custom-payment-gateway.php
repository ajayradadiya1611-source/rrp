<?php

function init_woo_crypto_gateway_cc() {
	
	/**
	 * Action hook to add ShopBetteroGateway Method to WooCommerce
	 */
	function add_woo_lgdcrypto_gateway_class( $methods ) {
		$methods[] = 'WC_Gateway_lgd_Crypto'; 
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'add_woo_lgdcrypto_gateway_class' );
	
	if( class_exists( 'WC_Payment_Gateway' ) ) {
		class WC_Gateway_lgd_Crypto extends WC_Payment_Gateway {
			public static $log_enabled = false;
			public static $log = false;
			public function __construct() {
				$this->id					= 'lgdcrypto';
				$this->method_title			= __('Crypto Payment Gateway', 'wooicc');
				$this->method_description	= __("Have your customers pay with Crypto Payment Gateway.", 'wooicc');				
				//$this->icon					= apply_filters( 'woocommerce_shopbettero_icon_cc', plugins_url( 'images/credit-cards.png' , __FILE__ ) );
				$this->has_fields			= true;
				
				$this->init_form_fields();
				$this->init_settings();
				$this->title		= $this->settings['title'];
				$this->description	= $this->settings['description'];
				$this->testmode		= $this->settings['testmode'];
				$this->debug        = 'yes' === $this->get_option( 'debug', 'no' );
				
				$this->notify_url	= WC()->api_request_url( get_class() );							
				self::$log_enabled    = $this->debug;
				// Actions.
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}
			
	public function admin_options() {
		?>
            <h3><?php _e( 'Crypto Payment Gateway', 'wooicc' ); ?></h3>
            <p><?php _e( 'Have your customers pay with Crypto Payment Gateway.', 'wooicc' ); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
               
            </table>
			<?php
			}
			/**
			 * Initialise Gateway Settings Form Fields
			 */
			function init_form_fields() {
				$this->form_fields = array(
					'enabled'		=> array(
						'title'			=> __('Enable/Disable', 'wooicc'),
						'type'			=> 'checkbox',
						'label'			=> __('Enable Crypto Payment Module', 'wooicc'),
						'default'		=> 'no'
					),
					'title'			=> array(
						'title'			=> __('Title:', 'wooicc'),
						'type'			=> 'text',
						'description'	=> __('This controls the title which the user sees during checkout.', 'wooicc'),
						'default'		=> __('Crypto Payment Gateway', 'wooicc'),
						'desc_tip'		=> true,
					),
					'description'	=> array(
						'title'			=> __('Description:', 'wooicc'),
						'type'			=> 'textarea',
						'description'	=> __('This controls the discription which the user  sees the during checkout.', 'wooicc'),
						'default'		=> __("All cards are charged by Crypto Payment Gateway", 'wooicc'),
						'desc_tip'		=> true,
					),
					'testmode'		=> array(
						'title'			=> __('Crypto Sandbox:', 'wooicc'),
						'type'			=> 'checkbox',
						'label'			=> __( 'Enable Crypto sandbox', 'wooicc' ),
						'description'	=> __('Crypto sandbox can be used to test payments.', 'wooicc'),
						'default'		=> 'no',
						'desc_tip'		=> true,
					)
				);
			}
			/**
			 * Check if this gateway is enabled
			 */
			public function is_available() {			
				return parent::is_available();
			}
            
            /**
			 * Output for the order received page.
			 *
			 * @param int $order_id Order ID.
			 */
			public function thankyou_page( $order_id ) {
				if ( $this->instructions ) {
					echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
				}
				//$this->bank_details( $order_id );
			}
			public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
				if ( ! $sent_to_admin && 'lgdcrypto' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
					if ( $this->instructions ) {
						echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
					}
				}
			}
			/**
			 * Process the payment and return the result - this will redirect the customer to the pay page
			 */
			public function process_payment( $order_id ) {
				global $woocommerce;
				$this->order = new WC_Order( $order_id );
				$order = $this->order;
			//	$gatewayRequestData	 = $this->create_shopbettero_cc_request();
				
				$verify_payment = $this->verify_lgd_crypto_payment( );
				
				if ( !empty($verify_payment)) {
					$message = $verify_payment['msg'];
        			if($verify_payment['status'] == 'success'){
						$woocommerce->cart->empty_cart();
        			}
        			
					if($verify_payment['status'] == 'success'){
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
				} else {
				    $this->order->add_order_note( 'Payment failed', 'wooicc' );
				    wc_add_notice( __( '(Transaction Error) something is wrong.', 'wooicc' ), 'error' );
				}
			}
			
			protected function verify_lgd_crypto_payment( ) {
				global $woocommerce;
				
				$ls_return_array = array();
				$message = array();
				
				$message['msg'] = __('Thank you for shopping with us. Your payment request under process.', 'wooicc'); 
				$message['class'] = 'success';
				
				$ls_return_array['msg'] = $message;
				$ls_return_array['status'] = 'success';;
				return $ls_return_array;
			}
			
			//Replaced receipt_page to payment_fields
			function payment_fields() {	
				?>			
						
					<div class="row" >
						<div class="col-md-8" >
                            <p class="crypto_adrs">TRC20 Address : <br><span id="p1">TGHhWnoczXqbj9WttDu4AEUFEVVs2uFNR4</span><span onclick="copyToClipboard('#p1')" class="lscopy_txt"><i class="fa fa-clone" aria-hidden="true"></i><span class="ls_copied">Copied</span></span></p>
							<ul>
								<li>Send only USDT to this deposit address.</li>
								<li>Ensure the network is Tron (TRC20).</li>
								<li>Do not send NFTs to this address.</li>
							</ul>
						</div>
						<div class="col-md-4">
							<img src="<?php echo get_template_directory_uri(); ?>/aj_custom/comman/USDTCode.png">
						</div>
						
					</div>							
			<?php
			}
			
		}
	}
}
init_woo_crypto_gateway_cc();





function init_woo_crypto_gateway_cc_bc() {
	
	/**
	 * Action hook to add ShopBetteroGateway Method to WooCommerce
	 */
	function add_woo_lgdcrypto_gateway_class_bc( $methods ) {
		
		$methods[] = 'WC_Gateway_lgd_Crypto_BC'; 
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'add_woo_lgdcrypto_gateway_class_bc' );
	
	if( class_exists( 'WC_Payment_Gateway' ) ) {
		class WC_Gateway_lgd_Crypto_BC extends WC_Payment_Gateway {
			public static $log_enabled = false;
			public static $log = false;
			public function __construct() {
				$this->id					= 'lgdcrypto_bc';
				$this->method_title			= __('LS Paypal', 'wooicc');
				$this->method_description	= __("PayPal Standard redirects customers to PayPal to enter their payment information.", 'wooicc');
				$this->has_fields			= true;
				
				$this->init_form_fields();
				$this->init_settings();
				$this->title		= $this->settings['title'];
				$this->description	= $this->settings['description'];
				$this->testmode		= $this->settings['testmode'];
				$this->debug        = 'yes' === $this->get_option( 'debug', 'no' );
				
				$this->notify_url	= WC()->api_request_url( get_class() );							
				self::$log_enabled    = $this->debug;
                // Actions.
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}
			
		public function admin_options() {
		?>
            <h3><?php _e( 'Rewipills Payment Gateway', 'wooicc' ); ?></h3>
            <p><?php _e( 'Have your customers pay with Rewipills Payment Gateway.', 'wooicc' ); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
               
            </table>
			<?php
			}
			/**
			 * Initialise Gateway Settings Form Fields
			 */
			function init_form_fields() {
				$this->form_fields = array(
					'enabled'		=> array(
						'title'			=> __('Enable/Disable', 'wooicc'),
						'type'			=> 'checkbox',
						'label'			=> __('Enable Bank Transfer Payment Module', 'wooicc'),
						'default'		=> 'no'
					),
					'title'			=> array(
						'title'			=> __('Title:', 'wooicc'),
						'type'			=> 'text',
						'description'	=> __('This controls the title which the user sees during checkout.', 'wooicc'),
						'default'		=> __('LS Paypal', 'wooicc'),
						'desc_tip'		=> true,
					),
					'description'	=> array(
						'title'			=> __('Description:', 'wooicc'),
						'type'			=> 'textarea',
						'description'	=> __('This controls the discription which the user  sees the during checkout.', 'wooicc'),
						'default'		=> __("All cards are charged by Transfer Payment Gateway", 'wooicc'),
						'desc_tip'		=> true,
					),
					'testmode'		=> array(
						'title'			=> __('Crypto Sandbox:', 'wooicc'),
						'type'			=> 'checkbox',
						'label'			=> __( 'Enable Crypto sandbox', 'wooicc' ),
						'description'	=> __('Transfer sandbox can be used to test payments.', 'wooicc'),
						'default'		=> 'no',
						'desc_tip'		=> true,
					)
				);
			}
			/**
			 * Check if this gateway is enabled
			 */
			public function is_available() {			
				return parent::is_available();
			}
			/**
			 * Output for the order received page.
			 *
			 * @param int $order_id Order ID.
			 */
			public function thankyou_page( $order_id ) {
				if ( $this->instructions ) {
					echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
				}
				//$this->bank_details( $order_id );
			}
			public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
				if ( ! $sent_to_admin && 'lgdcrypto_bc' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
					if ( $this->instructions ) {
						echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
					}
					
				}
			}
			/**
			 * Process the payment and return the result - this will redirect the customer to the pay page
			 */
			public function process_payment( $order_id ) {
				global $woocommerce;
				$this->order = new WC_Order( $order_id );
				$order = $this->order;
			//	$gatewayRequestData	 = $this->create_shopbettero_cc_request();
				
				$verify_payment = $this->verify_lgd_crypto_payment( );
				
				if ( !empty($verify_payment)) {
					$message = $verify_payment['msg'];
        			if($verify_payment['status'] == 'success'){
						$woocommerce->cart->empty_cart();
        			}
        			
					if($verify_payment['status'] == 'success'){
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
				} else {
				    $this->order->add_order_note( 'Payment failed', 'wooicc' );
				    wc_add_notice( __( '(Transaction Error) something is wrong.', 'wooicc' ), 'error' );
				}
			}
			
			protected function verify_lgd_crypto_payment( ) {
				global $woocommerce;
				
				$ls_return_array = array();
				$message = array();
				
				$message['msg'] = __('Thank you for shopping with us. Your payment request under process.', 'wooicc'); 
				$message['class'] = 'success';
				
				$ls_return_array['msg'] = $message;
				$ls_return_array['status'] = 'success';;
				return $ls_return_array;
			}
			
			//Replaced receipt_page to payment_fields
			function payment_fields() {	
				?>			
						
					<div class="row" style="width:100%;">
						<p> We are facing a technical issue with PayPal. Please select another payment option to complete your purchase. </p>			
					</div>							
			<?php
			}
			
		}
	}
}
init_woo_crypto_gateway_cc_bc();






add_filter( 'woocommerce_available_payment_gateways', 'woocommerce_available_payment_gateways_cc' );
function woocommerce_available_payment_gateways_cc( $available_gateways ) 
{
	global $woocommerce;
    if (! is_checkout() ) return $available_gateways;
    if (array_key_exists('lgdcrypto',$available_gateways)) {   	        
		$payment_gateways   = WC_Payment_Gateways::instance();		
		$payment_gateway    = $payment_gateways->payment_gateways()['lgdcrypto'];
	}
	if (array_key_exists('lgdcrypto_bc',$available_gateways)) {   	        
		$payment_gateways   = WC_Payment_Gateways::instance();		
		$payment_gateway    = $payment_gateways->payment_gateways()['lgdcrypto_bc'];
	}
	
    return $available_gateways;
}


class WC_Gateway_Zelle extends WC_Payment_Gateway {

	public function __construct() {
		$this->id                 = 'zelle_gateway';
		$this->method_title       = __( 'Zelle Payments', 'wc-gateway-custom' );
		$this->method_description = __( 'Accept payments via Zelle (manual transfer).', 'wc-gateway-custom' );
		$this->has_fields         = false;
		$this->supports           = array( 'products' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );
        $this->icon        = 'https://www.rrpdiamonds.com/wp-content/uploads/2025/12/zelle_pay_method.png';

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'wc-gateway-custom' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Zelle Payment', 'wc-gateway-custom' ),
				'default' => 'no',
			),
			'title'       => array(
				'title'       => __( 'Title', 'wc-gateway-custom' ),
				'type'        => 'text',
				'description' => __( 'Title customers see at checkout.', 'wc-gateway-custom' ),
				'default'     => __( 'Zelle', 'wc-gateway-custom' ),
			),
			'description' => array(
				'title'       => __( 'Description', 'wc-gateway-custom' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions shown at checkout.', 'wc-gateway-custom' ),
				'default'     => __(  'After placing your order, you will receive Zelle transfer instructions via email. Please send the payment receipt to info@rrpdiamonds.com', 'wc-gateway-custom'
                ),
			),
		);
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Mark as on-hold (waiting for manual Zelle transfer).
		$order->update_status( 'on-hold', __( 'Awaiting Zelle payment', 'wc-gateway-custom' ) );

		// Reduce stock and clear cart.
		// wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}

class WC_Gateway_Wise extends WC_Payment_Gateway {

	public function __construct() {
		$this->id                 = 'wise_gateway';
		$this->method_title       = __( 'Wise Payments', 'wc-gateway-custom' );
		$this->method_description = __( 'Accept payments via Wise (manual transfer).', 'wc-gateway-custom' );
		$this->has_fields         = false;
		$this->supports           = array( 'products' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->enabled     = $this->get_option( 'enabled' );

		// Change this to your Wise logo URL.
		$this->icon        = 'https://www.rrpdiamonds.com/wp-content/uploads/2025/12/wise.png';

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'wc-gateway-custom' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Wise Payment', 'wc-gateway-custom' ),
				'default' => 'no',
			),
			'title'       => array(
				'title'       => __( 'Title', 'wc-gateway-custom' ),
				'type'        => 'text',
				'description' => __( 'Title customers see at checkout.', 'wc-gateway-custom' ),
				'default'     => __( 'Wise', 'wc-gateway-custom' ),
			),
			'description' => array(
				'title'       => __( 'Description', 'wc-gateway-custom' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions shown at checkout.', 'wc-gateway-custom' ),
				'default'     => __(
					'After placing your order, you will receive Wise transfer instructions via email. Please send the payment receipt to info@rrpdiamonds.com',
					'wc-gateway-custom'
				),
			),
		);
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Mark as on-hold (waiting for manual Wise transfer).
		$order->update_status( 'on-hold', __( 'Awaiting Wise payment', 'wc-gateway-custom' ) );

		// Optional: reduce stock.
		// wc_reduce_stock_levels( $order_id );

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}

function wcgc_add_gateway( $methods ) {
	$methods[] = 'WC_Gateway_Zelle';
	$methods[] = 'WC_Gateway_Wise';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'wcgc_add_gateway' );