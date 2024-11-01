<?php

/**
 * The Easy Digital Downloads functionality of the Plugin.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Cart_EDD {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Hook into EDD actions.
		add_action( 'admin_init', array( $this, 'wpcs_edd_checkout_setting_sections_and_fields' ) );
		add_action( 'edd_complete_purchase', array( $this, 'wpcs_purchase_complete_create_service' ) );
		//add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_before_gateway', array( $this, 'wpcs_purchase_checkout_before_gateway' ), 10, 3 );
		add_action( 'edd_meta_box_settings_fields', array( $this, 'wpcs_add_hosting_metabox' ) );
		add_action( 'edd_metabox_fields_save', array( $this, 'wpcs_save_metabox' ) );
		add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_edd_purchase_client_details_list' ), 10, 2 );

		// Create the Meta boxes for the EDD Download page
		add_action( 'save_post', array( $this, 'wpcs_edd_meta_save' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpcs_edd_custom_meta_box' ) );
		
		// AJAX scripts for Module selection on EDD Download Page
		add_action( 'wp_ajax_display_metaboxes', array( $this, 'wpcs_edd_ajax_display_metaboxes' ) );
		add_action( 'wp_ajax_select_server', array( $this, 'wpcs_edd_ajax_select_server' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_edd_ajax_load_scripts' ) );

		// Indicate went EDD plugin is active
		add_action( 'plugins_loaded', array( $this, 'wpcs_is_edd_active' ) );

		// Remove the Download when Cloud Hosting Plan only
		add_filter( 'edd_receipt_show_download_files', array( $this, 'wpcs_receipt' ), 10, 2 );
		add_filter( 'edd_email_receipt_download_title', array( $this, 'wpcs_email_receipt' ), 10, 3 );
		add_filter( 'edd_receipt_no_files_found_text', array( $this, 'wpcs_no_files_found_text' ), 10, 2 );
		add_filter( 'edd_payment_meta', array( $this, 'wpcs_edd_store_custom_fields') );

	}
	
	
	/**
 	 * Store the custom field data into EDD's payment meta
 	 */
	public function wpcs_edd_store_custom_fields( $payment_meta ) {

		if ( 0 !== did_action('edd_pre_process_purchase') ) {
			$payment_meta['site_label']			= isset( $_POST['edd_site_label'] ) ? sanitize_text_field( $_POST['edd_site_label'] ) : '';
			$payment_meta['domain_name']		= isset( $_POST['edd_domain_name'] ) ? sanitize_text_field( $_POST['edd_domain_name'] ) : '';
			$payment_meta['server_location']	= isset( $_POST['edd_server_location'] ) ? sanitize_text_field( $_POST['edd_server_location'] ) : '';
			$payment_meta['user_name']			= isset( $_POST['edd_user_name'] ) ? sanitize_text_field( $_POST['edd_user_name'] ) : '';
			$payment_meta['user_password']		= isset( $_POST['edd_user_password'] ) ? sanitize_text_field( $_POST['edd_user_password'] ) : '';
			$payment_meta['host_name']			= isset( $_POST['edd_host_name'] ) ? sanitize_text_field( $_POST['edd_host_name'] ) : '';
		}

		return $payment_meta;
	}
		
	/**
	 *  WPCS Purchase Form Custom Fields
	 * 
	 * 	Hooks into the EDD edd_purchase_form_user_info_fields action to add 'Company Details' to checkout.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_purchase_form_custom_fields() {

		// Retrieve the Cart Contents
		$cart = edd_get_cart_contents();

		$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $cart[0]['id'], '_wpcs_cloud_hosting_enabled', true );

		if ( $wpcs_cloud_hosting_enabled == false ) {
			return;
		}
		?>
		<legend><?php _e( 'Company Details (If Applicable)', 'wp-cloud-server'); ?></legend>
		<p>
			<label class="edd-label" for="edd_user_company"><?php _e( 'Company', 'wp-cloud-server'); ?></label>
            <input class="edd-input required" type="text" name="edd_user_company" id="edd_user_company" placeholder="<?php esc_attr_e( 'Company', 'wp-cloud-server'); ?>" value=""/>
		</p>
		<p>
			<label class="edd-label" for="edd_user_phone"><?php _e( 'Phone', 'wp-cloud-server'); ?></label>
            <input class="edd-input" type="text" name="edd_user_phone" id="edd_user_phone" placeholder="<?php esc_attr_e( 'Phone', 'wp-cloud-server'); ?>" value=""/>
		</p>
		<?php
	}
		
    /**
	 *  WPCS Purchase Checkout Before Gateway
	 * 
	 *	Hooks into the EDD edd_checkout_before_gateway action to collect data.
	 * 
	 *  @since 1.0.0
	 */
	public function wpcs_purchase_checkout_before_gateway( $post, $user_info, $valid_data ) {

		// Data provided from the EDD $valid_data array
		$user_data['user_first'] 			= isset( $valid_data['user']['user_first'] ) ? sanitize_text_field( $valid_data['user']['user_first'] ) : '';			
		$user_data['user_last'] 			= isset( $valid_data['user']['user_last'] ) ? sanitize_text_field( $valid_data['user']['user_last'] ) : '';
		$user_data['user_id'] 				= isset( $valid_data['user']['user_id'] ) ? sanitize_text_field( $valid_data['user']['user_id'] ) : '';
		$user_data['user_login'] 			= isset( $valid_data['user']['user_login'] ) ? sanitize_text_field( $valid_data['user']['user_login'] ) : '';			
		$user_data['user_email'] 			= isset( $valid_data['user']['user_email'] ) ? sanitize_text_field( $valid_data['user']['user_email'] ) : '';			
		$user_data['user_pass'] 			= isset( $valid_data['user']['user_pass'] ) ? sanitize_text_field( $valid_data['user']['user_pass'] ) : '';

		// Data provided from custom checkout fields specific to hosting
		$user_data['user_company'] 			= isset( $post['edd_user_company'] ) ? sanitize_text_field( $post['edd_user_company'] ) : '';
		$user_data['user_phone'] 			= isset( $post['edd_user_phone'] ) ? sanitize_text_field( $post['edd_user_phone'] ) : '';
		$user_data['user_name'] 			= isset( $post['edd_user_name'] ) ? sanitize_user( $post['edd_user_name'] ) : '';
		$user_data['user_password'] 		= isset( $post['edd_user_password'] ) ? sanitize_text_field( $post['edd_user_password'] ) : '';
		$user_data['user_confirm_password'] = isset( $post['edd_user_confirm_password'] ) ? sanitize_text_field( $post['edd_user_confirm_password'] ) : '';
		$user_data['domain_name'] 			= isset( $post['edd_domain_name'] ) ? esc_url( $post['edd_domain_name'] ) : '';
		$user_data['host_name'] 			= isset( $post['edd_host_name'] ) ? sanitize_text_field( $post['edd_host_name'] ) : '';
		$user_data['server_location'] 		= isset( $post['edd_server_location'] ) ? sanitize_text_field( $post['edd_server_location'] ) : '';
		$user_data['site_url'] 				= isset( $post['edd_site_url'] ) ? esc_url( $post['edd_site_url'] ) : '';
		$user_data['site_name'] 			= isset( $post['edd_site_name'] ) ? sanitize_text_field( $post['edd_site_name'] ) : '';
		$user_data['site_desc'] 			= isset( $post['edd_site_desc'] ) ? sanitize_text_field( $post['edd_site_desc'] ) : '';

		// Generate a site label as a reference e.g. for use as ServerPilot App Name
		$user_data['site_label'] 			= isset( $post['edd_site_label'] ) ? wpcs_sanitize_site_label( $post['edd_site_label'] ) : '';
			
		// Store the User Data for later use by the modules
		update_option( 'wpcs_new_account_info', $user_data );

    }

    /**
	 *  WPCS Custom Meta Box
	 * 
	 * 	Hooks wpcs_edd_custom_meta_box into add_meta_boxes action
	 *
	 *  @since 1.0.0
	 */
	function wpcs_edd_custom_meta_box() {

		$edd_post_type = 'download';
		$archive_title = ucfirst( rtrim( $edd_post_type, "s"));
		
		add_meta_box( 'wpcs_edd_hosting_meta_box', __( 'WP Cloud Server - Hosting Plan Settings', 'wp-cloud-server' ), array( $this, 'wpcs_edd_meta_callback' ), $edd_post_type );

	}
		
    /**
	 *  Meta Callback for EDD Download Page
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_meta_callback( $post ) {

		wp_nonce_field( basename( __FILE__ ), 'wpcs_nonce' );
			
		// Save the current EDD download Id
		update_option( 'wpcs_edd_download_id', $post->ID );

		$wpcs_stored_meta = get_post_meta( $post->ID );
		$modules_list = get_option( 'wpcs_module_list' );
			
		$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $post->ID, '_wpcs_cloud_hosting_enabled', true );

		if ( $wpcs_cloud_hosting_enabled == false ) {
			?>
			<script>
				(function($){
					$( "#wpcs_edd_hosting_meta_box" ).hide();
				})(jQuery);
			</script>
			<?php
		}

		?>

		<p><?php _e( 'Select a Module and Server to be used for this Plan;', 'wp-cloud-server' ); ?></p>

		<p id="module_value" class="module_value">  
    		<label style="width: 150px; display:inline-block;" for="custom_field1"><?php _e( 'Module:', 'wp-cloud-server'); ?></label>
    		<select style="width: 300px; display:inline-block;" name="custom_field1" id="custom_field1">
				<option value='No Module'><?php _e( 'No Module Selected', 'wp-cloud-server'); ?></option>
				<?php
				$selected_module = ( isset( $wpcs_stored_meta['custom_field1'][0] ) ) ? $wpcs_stored_meta['custom_field1'][0] : '' ;
				foreach ( $modules_list as $key => $module ) {
					if ( ( ( 'cloud_portal' == $modules_list[$key]['module_type'] ) || ( 'cloud_provider' == $modules_list[$key]['module_type'] ) || ( 'managed_server' == $modules_list[$key]['module_type'] ) ) && ( wpcs_check_cloud_provider( $key, null, false ) ) ){
					?>
        			<option value='<?php echo $key ?>' <?php selected( $selected_module, $key ); ?>><?php echo $key ?></option>
					<?php
					}
				}
				?>
    		</select>  
		</p>

		<p id="server_value" class="server_value">
    		<label style="width: 150px; display:inline-block;" for="custom_field2"><?php _e( 'Server/Template:', 'wp-cloud-server' ); ?></label>  
    			<select style="width: 300px; display:inline-block;" name="custom_field2" id="custom_field2">
					<!-- option tags autofilled by JavaScript script (select-module.js) -->	
    			</select>  
		</p>

		<p><?php _e( 'Enter a Plan Name (Leave Blank to use Page Title);', 'wp-cloud-server' ); ?></p>
			
		<p>    
			<label style="width: 150px; display:inline-block;" for="custom_field3" class="d4p-row-title"><?php _e( 'Plan Name:', 'wp-cloud-server' ); ?></label>
			<input style="width: 300px; display:inline-block;" type="text" name="custom_field3" id="custom_field3" value="<?php if ( isset ( $wpcs_stored_meta['custom_field3'] ) ) echo $wpcs_stored_meta['custom_field3'][0]; ?>" />
		</p>
	<?php  
	}
		
    /**
	 *  Save the meta data from EDD Hosting Plan Page
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_meta_save( $post_id ) {
		
		if ( ! isset( $_POST['post_type']) || 'download' !== $_POST['post_type'] ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;

		// Checks save status
		$is_autosave 	= wp_is_post_autosave( $post_id );
		$is_revision 	= wp_is_post_revision( $post_id );
		$nonce 			= ( isset( $_POST['wpcs_nonce'] ) ) ? sanitize_text_field( $_POST['wpcs_nonce'] ) : '';
		$custom_field1 	= ( isset( $_POST['custom_field1'] ) ) ? sanitize_text_field( $_POST['custom_field1'] ) : '';
		$custom_field2 	= ( isset( $_POST['custom_field2'] ) ) ? sanitize_text_field( $_POST['custom_field2'] ) : '';
		$custom_field3 	= ( empty( $_POST['custom_field3'] ) ) ? esc_html( get_the_title( $post_id ) ) : sanitize_text_field( $_POST['custom_field3'] ) ;
		$is_valid_nonce = ( wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) ? 'true' : 'false';
		 
		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Exits if Fields not valid data
		if ( ! $custom_field1 || ! $custom_field2 ) {
			return;
		}

		// Check if the User has selected a Module and Server
		if ( ( 'No Module' == $custom_field1 ) || ( 'No Servers' == $custom_field2 ) ) {

			// Disable the Cloud Hosting Service
			update_post_meta( $post_id, '_wpcs_cloud_hosting_enabled', false );

			// Clear any outdated dismissed Admin Notices
			update_option( 'wpcs_dismissed_hosting_plan_save_failed_notice', false );

			// Update Option to Trigger Admin Notice
			update_option( 'wpcs_hosting_plan_save_failed', true );

		}

		if ( isset( $custom_field1, $custom_field2 ) ) {

			// Sanitize the $_POST Custom Fields
			//$custom_field1 = sanitize_text_field( $_POST['custom_field1'] );
			//$custom_field2 = sanitize_text_field( $_POST['custom_field2'] );
			
			// If Plan Name not entered then use EDD Download Page Title
			$custom_field3 	= ( '' !== $_POST['custom_field3'] ) ? sanitize_text_field( $_POST['custom_field3'] ) : esc_html( get_the_title( $post_id ) );
		 
			$module_updated = update_post_meta( $post_id, 'custom_field1', $custom_field1 );
			$server_updated = update_post_meta( $post_id, 'custom_field2', $custom_field2 );
			$plan_updated 	= update_post_meta( $post_id, 'custom_field3', $custom_field3 );
		}
	}
	
    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_purchase_client_details_list( $payment_meta, $user_info ) {

        $domain_name 	= ( isset( $payment_meta['domain_name'] ) && ( '' !== $payment_meta['domain_name'] ) ) ? sanitize_text_field( $payment_meta['domain_name'] ) : false;
		$user_name 		= ( isset( $payment_meta['user_name'] ) && ( '' !== $payment_meta['user_name'] ) ) ? sanitize_text_field( $payment_meta['user_name'] ) : false;
		$user_password 	= ( isset( $payment_meta['user_password'] ) && ( '' !== $payment_meta['user_password'] ) )	? sanitize_text_field( $payment_meta['user_password'] ) : false;
		$host_name 		= ( isset( $payment_meta['host_name'] ) && ( '' !== $payment_meta['host_name'] ) )	? sanitize_text_field( $payment_meta['host_name'] ) : false;
		
		if ( $domain_name || $user_name || $user_password || $host_name ) {
        ?>
		<h3 style="padding-left: 0; " class="hndle"><span>Website Details</span></h3>
		<?php if ( $domain_name ) { ?>
        	<li><?php echo __( 'Domain Name:', 'wp-cloud-server' ) . ' ' . $domain_name; ?></li>
		<?php } ?>
		<?php if ( $user_name ) { ?>
		<li><?php echo __( 'Username:', 'wp-cloud-server' ) . ' ' . $user_name; ?></li>
		<?php } ?>
		<?php if ( $user_password ) { ?>
		<li><?php echo __( 'User Password:', 'wp-cloud-server' ) . ' ' . $user_password; ?></li>
		<?php } ?>
		<?php if ( $host_name ) { ?>
		<li><?php echo __( 'Hostname:', 'wp-cloud-server' ) . ' ' . $host_name; ?></li>
		<?php }
		}     
    }

    /**
	 *  WPCS Purchase Complete Create Service
	 * 
	 * 	Hooks edd_complete_purchase action
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_purchase_complete_create_service( $payment_id ) {

		// New Account Information
		$new_account_info	= get_option( 'wpcs_new_account_info' );
            
        // Basic payment meta from EDD
        $payment_meta		= edd_get_payment_meta( $payment_id );
 
        // Cart details from EDD
		$cart_items			= edd_get_payment_meta_cart_details( $payment_id );
				
		// Retrieve the product_id
		$product_id			= $cart_items[0]['id'];

		// Exit from Create Hosting Plan if NOT Enabled
		$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $product_id, '_wpcs_cloud_hosting_enabled', true );

		if ( $wpcs_cloud_hosting_enabled == false ) {
			return;
		}

		// Retrieve hosting plan details for product
		$module_name 	= get_post_meta( $product_id, 'custom_field1', true );
		$server_name 	= get_post_meta( $product_id, 'custom_field2', true );
		$plan_name		= get_post_meta( $product_id, 'custom_field3', true );
		
		// Retrieve current user if logged in via EDD
		$user_id		= $new_account_info['user_id'];
		
		// Retrieve the current user if not already set but is logged in
		$user_id		= ( isset( $user_id ) ) ? $user_id : get_current_user_id();
		
		// Finally retrieve current user from email or user login, then create new account
		$user_email		= sanitize_user( $new_account_info['user_email'] );
		$select_user	= ( '' == $new_account_info['user_login'] ) ? $user_email : $new_account_info['user_login'];
		$user_id		= ( isset( $user_id ) ) ? $user_id : username_exists( $select_user );
		
		if ( !$user_id and email_exists($user_email) == false ) {
			
			// Generate Password and set User Name to their email address
			$user_pwd		= wp_generate_password( 10, true, false );		
			$user_name		= $user_email;
			
			$userdata = array(
    			'user_pass'             => $user_pwd,
    			'user_login'            => $user_name,
    			'user_url'              => $new_account_info['domain_name'],
    			'user_email'            => $user_email,
    			'first_name'            => $new_account_info['user_first'],
    			'last_name'             => $new_account_info['user_last'],
			);
			
        	$user_id = wp_insert_user( $userdata );
			
			$to = $user_email;
			$subject = 'WordPress - New Log-in Details!';
			$body  = __( "Dear", "wp-cloud-server" ) . ' ' . $new_account_info['user_first'] . ",\n\n";
			$body .= __( "Below are your login details:", "wp-cloud-server" ) . "\n\n";
			$body .= __( "Your Username:", "wp-cloud-server" ) . ' ' . $user_name . "\n\n";
			$body .= __( "Your Password:", "wp-cloud-server" ) . ' ' . $user_pwd . "\n\n";
			$body .= __( "Login:", "wp-cloud-server" ) . ' ' . wp_login_url() . "\r\n";			
			wp_mail( $to, $subject, $body );
			
			$details = array(
        		'user_login'    => $user_name,
        		'user_password' => $user_pwd,
        		'remember'      => true
    		);
 
   			$user = wp_signon( $details, false );
			
			$customer    = new EDD_Customer( $user_email );
			$customer->update( array( 'user_id' => $user_id ) );
		}

		// Define the arguments for the create service action hook
		$data = array(
			'module_name'				=> 	$module_name,
			'plan_name'					=>	$plan_name,
			'server_name'				=> 	$server_name,
			'host_name'					=> 	$new_account_info['host_name'],
			'server_location'			=> 	$new_account_info['server_location'],
			'domain_name'				=> 	$new_account_info['domain_name'],
			'site_label'				=> 	$new_account_info['site_label'],
			'site_url'					=> 	$new_account_info['site_url'],
			'site_name'					=> 	$new_account_info['site_name'],
			'site_desc'					=> 	$new_account_info['site_desc'],
			'user_id'					=> 	$user_id,
			'user_first'				=> 	$new_account_info['user_first'],
			'user_last'					=> 	$new_account_info['user_last'],
			'user_email'				=> 	$new_account_info['user_email'],
			'user_phone'				=> 	$new_account_info['user_phone'],
			'user_login'				=> 	$new_account_info['user_login'],				
			'user_pass'					=> 	$new_account_info['user_pass'],
			'user_name'					=> 	$new_account_info['user_name'],
			'user_password'				=> 	$new_account_info['user_password'],
			'user_confirm_password'		=> 	$new_account_info['user_confirm_password'],
			'user_company'				=> 	$new_account_info['user_company'],
			'product_id'				=> 	$product_id,
    		'cart_items'				=> 	$cart_items,
			'payment_meta'				=> 	$payment_meta,
			'new_account_info'			=>  $new_account_info,
		);
			
		update_option( 'wpcs_create_hosting_plan_data', $data );
		
		// Executes before the create service functionality
		do_action( 'wpcs_before_purchase_complete_create_service', $data );

		// Executes the create service functionality depending on the module
		do_action( 'wpcs_edd_purchase_complete_create_service', $module_name, $data );
		
		// Executes after the create service functionality
		do_action( 'wpcs_after_purchase_complete_create_service', $data );

    }
		
	/**
	 *  Get EDD Plan Details
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_edd_get_plan_module() {

        $cart_details = edd_get_cart_content_details();
		$module_name = get_post_meta( $cart_details[0]['id'], 'custom_field1', true );
		return $module_name;
            
    }
		
	/**
	 *  Load the scripts for the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_ajax_load_scripts() {

		// Load the JavaScript for the EDD Meta Boxes & set-up the related Ajax script
		$display_metaboxes_args = array(
			'ajaxurl'	 					=> admin_url( 'admin-ajax.php' ),
			'ajax_display_metaboxes_nonce' 	=> wp_create_nonce( 'display_metaboxes_nonce' ),
		);

		wp_enqueue_script( 'display-metaboxes', WPCS_PLUGIN_URL . 'includes/admin/assets/js/display-metaboxes.min.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'display-metaboxes', 'wpcs_display_metaboxes_script', $display_metaboxes_args );

		// Load the JavaScript for the Server Select Dropdown & set-up the related Ajax script
		$select_server_args = array(
			'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
			'ajax_select_server_nonce' 	=> wp_create_nonce( 'select_server_nonce' ),
		);
			
		wp_enqueue_script( 'select-server', WPCS_PLUGIN_URL . 'includes/admin/assets/js/select-server.min.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'select-server', 'wpcs_select_server_script', $select_server_args );

	}
		
	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_ajax_display_metaboxes() {

		// Check the nonce for display metaboxes
		check_ajax_referer( 'display_metaboxes_nonce', 'metaboxes_nonce' );

		if ( empty( $_POST['module'] ) && empty( $_POST['server'] ) ) {
			return;
		}

		// Initial Conditions
		$shared_servers_exist = false;
        
        $module             = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : "";
        $selected_server    = isset( $_POST['server'] ) ? sanitize_text_field( $_POST['server'] ) : "";
        
        $modules_list		= get_option( 'wpcs_module_list' );

		$servers_list		= ( isset( $modules_list[ $module ]['servers'] ) ) ? $modules_list[ $module ]['servers'] : false;
		$template_list		= ( isset( $modules_list[ $module ]['templates'] ) ) ? $modules_list[ $module ]['templates'] : false;
		
        $servers			= ( 'No Module' == $module ) ? false : $servers_list ;
        $templates			= ( 'No Module' == $module ) ? false : $template_list ;
			
        $post_id			= get_option( 'wpcs_edd_download_id' );
		//$post_id			= get_option('wc_download_id');
		$post_meta			= get_post_meta( $post_id, 'custom_field2', true );
		
		// Determine if Shared Hosting Servers Exist
		$server_count = 0;
		if ( $servers && ( 'ServerPilot' == $module  ) ) {
			foreach ( $servers as $key => $server ){
				if ( 'Shared' == $server['hosting_type'] ) {
					$shared_servers_exist = true;
					$server_count++;
				}
			}
		}
			
        if ( $shared_servers_exist || $templates ) {    
			
			if ( $servers && ( 'ServerPilot' == $module  ) ) {
				$server_list[] = '<optgroup label="Shared Hosting Server(s)">';
            	foreach ( $servers as $key => $server ){
					if ( ! array_key_exists('slug', $server) ) {
    					$server['slug'] = sanitize_title( $server['name'] );
					}
					if ( 'Shared' == $server['hosting_type'] ) {
                		$selected_option = selected( $post_meta , $server['slug'], false );
						$server_list[] = '<option value="' . $server['slug'] . '"' . $selected_option . '>' . $server['name'] . '</option>';
					}
				}
				if ( $server_count > 1 ) {
					$selected_option = selected( $post_meta , 'server-selected-by-region', false );
					$server_list[] = '<option value="server-selected-by-region"' . $selected_option . '>All Servers (Selected at Checkout)</option>';
				}
			}
			
			if ( $templates ) {
				$server_list[] = '<optgroup label="Dedicated Server Template(s)">';
            	foreach ( $templates as $key => $template ){
                	$selected_option = selected( $post_meta , $template['name'], false );
					//$server_list[] = '<option value="' . $post_meta . '"' . $selected_option . '>' . $template['name'] . '</option>';
                	$server_list[] = '<option value="' . $template['name'] . '"' . $selected_option . '>' . $template['name'] . '</option>';
				}
			}
			
            $response = json_encode( $server_list );
			update_option( 'server_list', $server_list );
		} else {
			$server_list[] = '<option value="No Servers">No Servers/Templates Available</option>';
            $response = json_encode( $server_list );
        }

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
		
	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_ajax_select_server() {

		// Check the nonce for the module notice data
		check_ajax_referer( 'select_server_nonce', 'server_nonce' );

		if ( empty( $_POST['module'] ) ) {
			return;
		}

		// Initial Conditions
		$shared_servers_exist = false;

	    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : "";
			
		// Retrieve Servers and Templates
		$modules_list = get_option( 'wpcs_module_list' );
		$servers	= ( 'No Module' == $module ) ? false : $modules_list[ $module ]['servers'] ;
        $templates	= ( 'No Module' == $module ) ? false : $modules_list[ $module ]['templates'] ;
		$results    = array();
			
		$post_id = get_option( 'wpcs_edd_download_id' );

		// Determine if Shared Hosting Servers Exist
		$server_count = 0;
		if ( $servers && ( 'ServerPilot' == $module  ) ) {
			foreach ( $servers as $key => $server ){
				if ( 'Shared' == $server['hosting_type'] ) {
					$shared_servers_exist = true;
					$server_count++;
				}
			}
		}
			
        if ( $shared_servers_exist || $templates ) {
			if ( $servers && ( 'ServerPilot' == $module  ) ) {
				$server_list[] = '<optgroup label="Shared Hosting Server(s)">';
            	foreach ( $servers as $key => $server ){
					if ( ! array_key_exists('slug', $server) ) {
    					$server['slug'] = sanitize_title( $server['name'] );
					}
					if ( 'Shared' == $server['hosting_type'] ) {
						$server_list[] = '<option value="' . $server['slug'] . '">' . $server['name'] . '</option>';
					}
				}
				if ( $server_count > 1 ) {
					$server_list[] = '<option value="server-selected-by-region">All Servers (Selected at Checkout)</option>';
				}
			}
			
			if ( $templates ) {
				$server_list[] = '<optgroup label="Dedicated Server Template(s)">';
            	foreach ( $templates as $key => $template ){
                	$server_list[] = '<option value="' . $template['name'] . '">' . $template['name'] . '</option>';
				}
			}
			
            $response = json_encode( $server_list );
		} else {
			$server_list[] = '<option value="No Servers">No Servers/Templates Available</option>';
            $response = json_encode( $server_list );
        }

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
    
    /**
     *  Test if EDD Plugin is Active.
     *
     *  @since 1.0.0
     */
    public static function wpcs_is_edd_active() {
	
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
        return is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php');
	}
	
	/**
	 * Add Metabox if per download email attachments are enabled
	 *
	 * @since 1.2.1
	 */
	public function wpcs_add_hosting_metabox( $post_id ) {

		$checked = (boolean) get_post_meta( $post_id, '_wpcs_cloud_hosting_enabled', true );
		?>
		<p><strong><?php apply_filters( 'wpcs_cloud_hosting_header', printf( __( 'WP Cloud Server Hosting Plan:', 'wp-cloud-server' ), edd_get_label_singular() ) ); ?></strong></p>
		<p>
		<label for="wpcs_cloud_hosting_enabled">
			<input type="checkbox" name="_wpcs_cloud_hosting_enabled" id="wpcs_cloud_hosting_enabled" value="1" <?php checked( true, $checked, true ); ?>/>
			<?php apply_filters( 'wpcs_cloud_hosting_header', printf( __( 'Use as Cloud Hosting Plan', 'wp-cloud-server' ), strtolower( edd_get_label_singular() ) ) ); ?>
		</label>
		</p>
		<?php
	}

	/**
	 * Add to save function
	 * @param  $fields Array of fields
	 *
	 * @since 1.2.1
	 * @return array
	 */
	public function wpcs_save_metabox( $fields ) {

		$fields[] = '_wpcs_cloud_hosting_enabled';

		return $fields;
	}

	/**
	 * Prevent receipt from listing download files
	 * @param $enabled default true
	 * @param int  $item_id ID of download
	 * @since 1.0
	 * @return boolean
	 */
	public function wpcs_receipt( $enabled, $item_id ) {

		if ( $this->is_cloud_hosting( $item_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Modify email template to remove dash if the item is a Cloud Hosting Plan
	 *
	 * @since 1.0
	*/
	public function wpcs_email_receipt( $title, $item_id, $price_id ) {

		if ( $this->is_cloud_hosting( $item_id ) ) {

			$title = get_the_title( $item_id );

			if( $price_id !== false ) {
				$title .= "&nbsp;" . edd_get_price_option_name( $item_id, $price_id );
			}
		}

		return $title;
	}

	/**
	 * Is Cloud Hosting
	 * @param  int  $item_id ID of download
	 * @return boolean true if service, false otherwise
	 * @return boolean
	 */
	public function is_cloud_hosting( $item_id ) {
		global $edd_receipt_args, $edd_options;

		// get array of service categories
		$service_categories = isset( $edd_options['wpcs_cloud_hosting_categories'] ) ? $edd_options['wpcs_cloud_hosting_categories'] : '';

		$term_ids = array();

		if ( $service_categories ) {
			foreach ( $service_categories as $term_id => $term_name ) {
				$term_ids[] = $term_id;
			}
		}

		$is_cloud_hosting = get_post_meta( $item_id, '_wpcs_cloud_hosting_enabled', true );

		// get payment
		$payment   = get_post( $edd_receipt_args['id'] );
		$meta      = isset( $payment ) ? edd_get_payment_meta( $payment->ID ) : '';
		$cart      = isset( $payment ) ? edd_get_payment_meta_cart_details( $payment->ID, true ) : '';

		if ( $cart ) {
			foreach ( $cart as $key => $item ) {
				$price_id = edd_get_cart_item_price_id( $item );

				$download_files = edd_get_download_files( $item_id, $price_id );

				// if the service has a file attached, we still want to show it
				if ( $download_files ) {
					return false;
				}
			}
		}

		// check if download has meta key or has a cloud hosting plan assigned to it
		if ( $is_cloud_hosting || ( ! empty( $term_ids ) && has_term( $term_ids, 'download_category', $item_id ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get terms
	 * @return array
	 * @since  1.0
	 */
	public function wpcs_get_terms() {

		$args = array(
			'hide_empty'     => false,
			'hierarchical'	=> false
		);

		$terms = get_terms( 'download_category', apply_filters( 'wpcs_get_terms', $args ) );

		$terms_array = array();

		foreach ( $terms as $term ) {
			$term_id = $term->term_id;
			$term_name = $term->name;

			$terms_array[$term_id] = $term_name;
		}

		if ( $terms ) {
			return $terms_array;
		}
			

		return false;
	}

	/**
	 * Remove "No downloadable files found." text for download services without a file
	 *
	 * @since  1.2.1
	 *
	 * @param  string $text The text that should appear when no downloadable files are found
	 * @param  int $item_id The ID of the download
	 *
	 * @return string $text The text that should appear when no downloadable files are found
	 */
	public function wpcs_no_files_found_text( $text, $item_id ) {

		// Remove the text for download services without a file
		if ( $this->is_cloud_hosting( $item_id ) ) {
				$text = '';
		}

		return $text;
	}

	/**
	 * Settings
	 *
	 * @since 1.2.1
	 */
	public function settings( $settings ) {

		$new_settings = array(
			array(
				'id' => 'wpcs_cloud_hosting_header',
				'name' => '<strong>' . __( 'WP Cloud Server Hosting Plan', 'wp-cloud-server' ) . '</strong>',
				'type' => 'header'
			),
			array(
				'id' => 'wpcs_cloud_hosting_categories',
				'name' => __( 'Cloud Hosting Categories', 'wp-cloud-server' ),
				'desc' => __( 'Select the categories that contain "Cloud Hosting Plans"', 'wp-cloud-server' ),
				'type' => 'multicheck',
				'options' => $this->wpcs_get_terms()
			),
		);
  
		return array_merge( $settings, $new_settings );
	}
	
		/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_edd_checkout_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_edd_checkout_settings_hostname' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname_label', $args );
		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname' );
		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname_suffix' );
		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname_domain' );
		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname_protocol' );
		register_setting( 'wpcs_edd_checkout_settings', 'wpcs_edd_checkout_settings_hostname_port' );

		add_settings_section(
			'wpcs_edd_checkout_settings',
			esc_attr__( 'Configure Host Name', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_edd_checkout_settings' ),
			'wpcs_edd_checkout_settings'
		);
		
		add_settings_field(
			'wpcs_edd_checkout_settings_hostname_label',
			esc_attr__( 'Label:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname_label' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);

		add_settings_field(
			'wpcs_edd_checkout_settings_hostname',
			esc_attr__( 'Host Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);
		
		add_settings_field(
			'wpcs_edd_checkout_settings_hostname_suffix',
			esc_attr__( 'Host Name Suffix:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname_suffix' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);
		
		add_settings_field(
			'wpcs_edd_checkout_settings_hostname_domain',
			esc_attr__( 'Domain Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname_domain' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);
		
		add_settings_field(
			'wpcs_edd_checkout_settings_hostname_protocol',
			esc_attr__( 'Protocol:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname_protocol' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);
		
		add_settings_field(
			'wpcs_edd_checkout_settings_hostname_port',
			esc_attr__( 'Port:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_edd_checkout_settings_hostname_port' ),
			'wpcs_edd_checkout_settings',
			'wpcs_edd_checkout_settings'
		);
		


	}
		
	/**
	 *  DigitalOcean API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_edd_checkout_settings(){
		echo '<p>';
		echo wp_kses( 'Configure Host Name Setting.', 'wp-cloud-server' );
		echo '</p>';
	}
	
	/**
	 *  DigitalOcean API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname_label() {
		echo '<input class="w-400" type="text" placeholder="Hostname Label"  id="wpcs_edd_checkout_settings_hostname_label" name="wpcs_edd_checkout_settings_hostname_label" value="" />';
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';
	}

	/**
	 *  DigitalOcean API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname() {
		echo '<input class="w-400" type="text" placeholder="hostname"  id="wpcs_edd_checkout_settings_hostname" name="wpcs_edd_checkout_settings_hostname" value="" />';
	}
	
	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname_suffix() {
		?>
		<select class="w-400" name="wpcs_edd_checkout_settings_hostname_suffix" id="wpcs_edd_checkout_settings_hostname_suffix">
            <option value="counter_suffix"><?php _e( 'Integer Counter (e.g. hostname023)', 'wp-cloud-server' ); ?></option>
			<option value="no_suffix"><?php _e( '-- No Suffix --', 'wp-cloud-server' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname_domain() {
		echo '<input class="w-400" type="text" placeholder="example.com"  id="wpcs_edd_checkout_settings_hostname_domain" name="wpcs_edd_checkout_settings_hostname_domain" value="" />';
	}
	
	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname_protocol() {
		?>
		<select class="w-400" name="wpcs_edd_checkout_settings_hostname_protocol" id="wpcs_edd_checkout_settings_hostname_protocol" >
            <option value="https"><?php _e( 'HTTPS', 'wp-cloud-server' ); ?></option>
			<option value="http"><?php _e( 'HTTP', 'wp-cloud-server' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_edd_checkout_settings_hostname_port() {
		echo '<input class="w-400" type="text" placeholder="8443"  id="wpcs_edd_checkout_settings_hostname_port" name="wpcs_edd_checkout_settings_hostname_port" value="" />';
	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_edd_checkout_settings_hostname( $name ) {
		
		$name	= sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_edd_checkout_settings_hostname_label', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output		= $name;
				$type		= 'updated';
				$message	= __( 'The New Hostname was Successfully Saved.', 'wp-cloud-server' );

			} else {
				
				$type		= 'error';
				$message	= __( 'Please enter a Valid Name!', 'wp-cloud-server' );
			}

			add_settings_error(
				'wpcs_edd_checkout_settings_hostname_label',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}

}