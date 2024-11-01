<?php

/**
 * WP Cloud Server - Ploi Module EDD Cart Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_Cart_EDD {
		
	/**
	 *  Instance of CloudServer_API class
	 *
	 *  @var resource
	 */
	private static $api;

	/**
	 *  Set Variables and add Action Hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		// Hook into Easy Digital Downloads filters.
		add_action( 'wpcs_edd_purchase_complete_create_service', array( $this, 'wpcs_ploi_purchase_complete_create_service' ), 10, 2 );
		add_action( 'wpcs_wc_purchase_complete_create_service', array( $this, 'wpcs_ploi_purchase_complete_create_service' ), 10, 2 );


		// Add Custom Checkout Fields for the EDD checkout
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_ploi_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'wpcs_ploi_purchase_form_error_checks' ), 10, 2 );
		add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_ploi_purchase_client_details_list' ), 10, 2 );
		
		// Hook into Easy Digital Downloads filters.
		add_filter( 'edd_purchase_form_required_fields', array( $this, 'wpcs_ploi_purchase_required_checkout_fields' ) );

		self::$api = new WP_Cloud_Server_Ploi_API();

	}
	
	/**
	 *  EDD Purchase Form Custom Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_purchase_form_custom_fields() {

		// Retrieve the Cart Contents
		$cart = edd_get_cart_contents();

		$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $cart[0]['id'], '_wpcs_cloud_hosting_enabled', true );
		
		if ( $wpcs_cloud_hosting_enabled == false ) {
			return;
		}

		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		// Retrieve the Server Info
		$server_name = get_post_meta( $cart[0]['id'] );
		
		// Retrieve the Active Module List
		$module_data = get_option( 'wpcs_module_list' );

		// If no module selected then lets exit now
		if ( 'No Module' == $server_name['custom_field1'][0] ) {
			return;
		}

		// Retrieve the correct vps template data
		foreach ( $module_data[ $server_name['custom_field1'][0]]['templates'] as $server ) {
			if ( $server_name['custom_field2'][0] == $server['name'] ) {
			
				$droplet_data = array(
					"name"			=>	$server['name'],
					"host_name"		=>	$server['host_name'],
    				"region"		=>	$server['region'],
					"size"			=>	$server['size'],
					"image"			=> 	$server['image'],
				);
				
				if ( ( 'Ploi' == $plan_module ) && ( ( 'host_name_field' == $droplet_data['host_name'] ) || ( 'userselected' == $droplet_data['region'] ) ) ) {
			
					if ( 'host_name_field' == $droplet_data['host_name'] ) {
						?>
						<legend class="website-details"><?php esc_html_e( 'New Server Details', 'wp-cloud-server-ploi' ); ?></legend>
			
        				<p>
						<label class="edd-label" for="edd_host_name"><?php esc_html_e( 'Host Name', 'wp-cloud-server-ploi' ); ?><span class="edd-required-indicator">*</span></label>
            			<input class="edd-input required" type="text" name="edd_host_name" id="edd_host_name" placeholder="<?php esc_attr_e( 'host-name', 'wp-cloud-server-ploi' ); ?>" value=""/>
						</p>

						<?php
					}
				
					if ( 'userselected' == $droplet_data['region'] ) {
					
						$plans	= wpcs_ploi_plans_list();
			
						foreach ( $plans as $plan ) {
							if ( $server['size'] == $plan['name'] ) {
								$location_list = $plan['available_locations'];
							}
						}
			
						$regions = wpcs_ploi_regions_list();
			
						foreach ( $regions as $region ) {
							foreach ( $location_list as $location ) {
								if ( $region['DCID'] == $location ) {
									$available[] = $region;
								}
							}
						}		
			
						?>

						<p>
						<label class="edd-label" for="edd_server_location"><?php esc_html_e( 'Website Location', 'wp-cloud-server-ploi' ); ?><span class="edd-required-indicator">*</span></label>
						<select name="edd_server_location" id="edd_server_location">
							<?php foreach ( $available as $region ) { ?>
            				<option value="<?php echo $region['DCID']; ?>" ><?php echo $region['name']; ?></option>
							<?php } ?>
						</select>

						</p>
			<?php } ?>
			<!--
			<p>
				<label class="edd-label" for="edd_user_name"><?php esc_html_e( 'Username', 'wp-cloud-server-ploi' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_user_name" id="edd_user_name" placeholder="<?php esc_attr_e( 'Username', 'wp-cloud-server-ploi' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_password"><?php esc_html_e( 'Password', 'wp-cloud-server-ploi' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_password" id="edd_user_password" placeholder="<?php esc_attr_e( 'Password', 'wp-cloud-server-ploi' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_confirm_password"><?php esc_html_e( 'Confirm Password', 'wp-cloud-server-ploi' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_confirm_password" id="edd_user_confirm_password" placeholder="<?php esc_attr_e('Confirm Password', 'wp-cloud-server-ploi'); ?>" value=""/>
			</p>
-->
        	<?php
			}					
		}
	}
}
	
	/**
	 *  EDD Purchase Form Required Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_purchase_required_checkout_fields( $required_fields ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'Ploi' == $plan_module ) {
			
			//$required_fields['edd_host_name'] = array(
			//	'error_id' => 'invalid_host_name',
			//	'error_message' => 'Please enter a host name',
			//);
			
			//$required_fields['edd_domain_name'] = array(
			//	'error_id' => 'invalid_domain_name',
			//	'error_message' => 'Please enter a domain name',
			//);
			
			//$required_fields['edd_user_name'] = array(
			//	'error_id' => 'invalid_user_name',
			//	'error_message' => 'Please enter a user name',
			//);
	
			//$required_fields['edd_user_password'] = array(
			//	'error_id' => 'invalid_password',
			//	'error_message' => 'Please enter a password',
			//);
			
			//$required_fields['edd_user_confirm_password'] = array(
			//	'error_id' => 'invalid_confirm_password',
			//	'error_message' => 'Please repeat your password',
			//);
		}

		return $required_fields;           
        
    }

    /**
	 *  EDD Purchase Form Error Checks
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_purchase_form_error_checks( $valid_data, $data ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'Ploi' == $plan_module ) {
			
			//if ( ! isset( $data['edd_host_name'] ) || $data['edd_host_name'] == '' ) {
            	// check for a valid host name
            //	edd_set_error( 'invalid_host_name', __( 'You must provide a valid host name.', 'wp-cloud-server-ploi' ) );
        	//}
			
        	//if ( ! isset( $data['edd_domain_name'] ) || $data['edd_domain_name'] == '' ) {
            	// check for a valid domain name
            // 	edd_set_error( 'invalid_domain_name', __( 'You must provide a valid domain name.', 'wp-cloud-server-ploi' ) );
        	//}

        	//if ( ! isset( $data['edd_user_name'] ) || $data['edd_user_name'] == '' ) {
            	// check for a valid user name
            //	edd_set_error( 'invalid_user_name', __( 'You must provide a valid user name.', 'wp-cloud-server-ploi' ) );
        	//}
			
        	//if ( ! isset( $data['edd_user_password'] ) || $data['edd_user_password'] == '' ) {
            	// check for a valid password
            //	edd_set_error( 'invalid_password', __( 'You must provide a valid password.', 'wp-cloud-server-ploi' ) );
        	//}
			
			//if ( ! isset( $data['edd_user_confirm_password'] ) || $data['edd_user_confirm_password'] == '' ) {
            	// check for a valid password confirmation
            //	edd_set_error( 'invalid_confirm_password', __( 'You must provide a valid password.', 'wp-cloud-server-ploi' ) );
        	//}
			
		}
        
    }

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_purchase_client_details_list( $payment_meta, $user_info ) {

		$host_name 		= isset( $payment_meta['host_name'] ) 		? sanitize_text_field( $payment_meta['host_name'] ) : 'none';
        $domain_name 	= isset( $payment_meta['domain_name'] ) 	? sanitize_text_field( $payment_meta['domain_name'] ) : 'none';
		$user_name 		= isset( $payment_meta['user_name'] ) 		? sanitize_text_field( $payment_meta['user_name'] ) : 'none';
		$user_password 	= isset( $payment_meta['user_password'] )	? sanitize_text_field( $payment_meta['user_password'] ) : 'none';
        ?>
        <li><?php echo __( 'Host Name:', 'wp-cloud-server-ploi' ) . ' ' . $host_name; ?></li>
        <li><?php echo __( 'Domain Name:', 'wp-cloud-server-ploi' ) . ' ' . $domain_name; ?></li>
		<li><?php echo __( 'Username:', 'wp-cloud-server-ploi' ) . ' ' . $user_name; ?></li>
		<li><?php echo __( 'User Password:', 'wp-cloud-server-ploi' ) . ' ' . $user_password; ?></li>
		<?php
		     
    }

	/**
	 *  EDD Purchase Complete Create ServerPilot Service
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_purchase_complete_create_service( $module_name, $data ) {

		if ( 'Ploi' == $module_name ) {

			// Retrieve the user entered account details
			$domain_name			= wpcs_sanitize_domain_strip_http( $data['domain_name'] );
			$user_name				= $data['user_name'];
			$user_pass				= $data['user_password'];
			$user_email				= $data['user_email'];
			$host_name_only			= $data['host_name'];
			$server_name			= $data['server_name'];
			$server_location		= $data['server_location'];
			$site_label				= $data['site_label'];
			$user_id				= $data['user_id'];
			$plan_name				= $data['plan_name'];
			
			$user_meta				= null;

			$ploi_debug['input']	= $data;
			
			// Retrieve the Active Module List
			$module_data			= get_option( 'wpcs_module_list' );
			$template_data			= get_option( 'wpcs_template_data_backup' );

			// Retrieve the correct cloud server template data
			foreach ( $module_data[ $module_name ]['templates'] as $server ) {
				if ( $server_name == $server['name'] ) {

					$template_type 			= $server['template_name'];

					$ploi_debug['template'] = $server;

					// Site Template Information
					$server_id				= isset( $server['server_id'] ) ? $server['server_id'] : '';
					$server_id_name			= isset( $server['server_name'] ) ? $server['server_name'] : '';
					$root_domain			= isset( $server['root_domain'] ) ? $server['root_domain'] : '';
					$system_user			= isset( $server['system_user'] ) ? $server['system_user'] : 'ploi';
					$site_counter			= isset( $server['site_counter'] ) ? $server['site_counter'] : '';
					$host_name_config		= isset( $server['host_name'] ) ? $server['host_name'] : '';
					$web_application		= isset( $server['web_app'] ) ? $server['web_app'] : '';
					$web_application_type	= isset( $server['web_app_type'] ) ? $server['web_app_type'] : '';
					$project_directory		= isset( $server['project_directory'] ) ? $server['project_directory'] : '/';
					$web_directory			= isset( $server['web_directory'] ) ? $server['web_directory'] : '/public';
					$web_template			= isset( $server['web_template'] ) ? $server['web_template'] : null;
					$enable_ssl				= isset( $server['enable_ssl'] ) ? $server['enable_ssl'] : null;

					// Server Template Information
         			$server_size			= isset( $server['size'] ) ? $server['size'] : null; 
         			$server_region			= isset( $server['region'] ) ? $server['region'] : null; 
         			$server_credential		= isset( $server['credentials'] ) ? $server['credentials'] : null; 
         			$server_type			= isset( $server['image'] ) ? $server['image'] : null; 
         			$server_database_type	= isset( $server['database'] ) ? $server['database'] : null; 
         			$server_webserver_type	= isset( $server['webserver'] ) ? $server['webserver'] : null;
         			$server_php_version		= isset( $server['php_version'] ) ? $server['php_version'] : null;

					$web_app_on_server		= ( ( 'ploi_server_template' == $template_type ) && ( 'no-application' !== $web_application ) ) ? true : false;

				}
			}

			// Set-up the Domain Name based on template options
			if ( ( '[Customer Input]' != $host_name_config ) && ( '[Customer Input]' != $root_domain ) ) {
			
				$host_names	= get_option( 'wpcs_host_names' );
			
				if ( !empty( $host_names ) ) {
					foreach ( $host_names as $key => $host_name ) {
						if ( $host_name_config == $host_name['hostname'] ) {
							$tmp_host_name			= $host_name['hostname'];
							$tmp_host_name_suffix	= $host_name['suffix'];
							$tmp_host_name_domain	= $host_name['domain'];
							$tmp_host_name_protocol	= $host_name['protocol'];
							$tmp_host_name_port		= $host_name['port'];
							$tmp_host_name_counter	= $host_name['count'];

							if ( 'counter_suffix' == $tmp_host_name_suffix ) {
								++$tmp_host_name_counter;
								$host_name_only	= "{$tmp_host_name}{$tmp_host_name_counter}";
								$domain_name	= "{$host_name_only}.{$tmp_host_name_domain}";
								$host_names[$host_name['label']]['count'] = $tmp_host_name_counter; 
								update_option( 'wpcs_host_names', $host_names );
							}
						}
					}
				}					
			}

			$ploi_debug['domain'] = $domain_name;

			// Template data is valid so increment the template site count
			++$site_counter;
			foreach ( $module_data[ 'Ploi' ]['templates'] as $key => $server ) {
				if ( $server_name == $server['name'] ) {
					$module_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
					$template_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
					update_option( 'wpcs_module_list', $module_data );
					update_option( 'wpcs_template_data_backup', $template_data );
				}
			}

			// If template is for a server then we create it here
			if ( 'ploi_server_template' == $template_type ) {

				// Set-up the data for the new Droplet
				$args['api_data'] = array(
					"name"				=> $host_name_only, 
         			"plan"				=> $server_size, 
         			"region"			=> $server_region, 
         			"credential"		=> $server_credential, 
         			"type"				=> $server_type, 
         			"database_type"		=> $server_database_type, 
         			"webserver_type"	=> $server_webserver_type,
					"php_version"		=> $server_php_version,
				);

				$ploi_debug['server_args'] = $args;

				// Send Create Server Request to Ploi API
				$api_response			= wpcs_ploi_api_server_request( 'servers/create', $args );
				$server_id				= $api_response['id'];

			}
			
			// Retrieve existing queue state
			$server_queue	= get_option( 'wpcs_ploi_server_complete_queue' );
			
			// Add server to queue for completion
			$server_queue[] = array(
				'SUBID'					=> ( isset( $server_id ) ) ? $server_id : '',
				'server_name'			=> $server_name,
				'user_id'				=> $user_id,
				'response'				=> ( isset( $api_response ) ) ? $api_response : '',
				'domain_name'			=> $domain_name,
				'host_name'				=> $host_name_only,
				'host_name_domain'		=> $tmp_host_name_domain,
				'fqdn'					=> ( isset( $host_name_fqdn ) ) ? $host_name_fqdn : '',			
				'protocol'				=> $tmp_host_name_protocol,				
				'port'					=> $tmp_host_name_port,				
				'site_label'			=> $site_label,
				'user_meta'				=> $user_meta,
				'plan_name'				=> $plan_name,
				'module'				=> $module_name,
				'ssh_key'				=> ( isset( $server_ssh_key_name ) ) ? $server_ssh_key_name : '',
				'location'				=> $server_region,
				'web_app'				=> $web_application,
				'web_app_type'			=> $web_application_type,
				'project_root'			=> $project_directory,
				'web_directory'			=> $web_directory,
				'system_user'			=> $system_user,
				'webserver_template'	=> $web_template,
				'template_name'			=> $template_type,
				'web_app_on_server'		=> $web_app_on_server,
				'enable_ssl'			=> $enable_ssl,
			);

			$ploi_debug['server_queue'] = $server_queue;

			update_option( 'wpcs_ploi_edd_debug', $ploi_debug );
			
			//$debug['server_queue'] = $server_queue;
			
			// Send new server details to completion queue after checking valid response
			if ( isset( $server_id ) ) {
			//	$debug['sent'] = 'Added to Queue';
				update_option( 'wpcs_ploi_server_complete_queue', $server_queue );
			}
			//update_option( 'wpcs_item_to_queue', $debug );
		} 
    }  
}