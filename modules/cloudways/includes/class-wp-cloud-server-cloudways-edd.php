<?php

/**
 * WP Cloud Server - Cloudways Module EDD Cart Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Cloudways_Cart_EDD {
		
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
		add_action( 'wpcs_edd_purchase_complete_create_service', array( $this, 'wpcs_cloudways_purchase_complete_create_service' ), 10, 2 );
		add_action( 'wpcs_wc_purchase_complete_create_service', array( $this, 'wpcs_cloudways_purchase_complete_create_service' ), 10, 2 );

		// Add Custom Checkout Fields for the EDD checkout
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_cloudways_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'wpcs_cloudways_purchase_form_error_checks' ), 10, 2 );
		//add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_cloudways_purchase_client_details_list' ), 10, 2 );
		
		// Hook into Easy Digital Downloads filters.
		add_filter( 'edd_purchase_form_required_fields', array( $this, 'wpcs_cloudways_purchase_required_checkout_fields' ) );

	}
	
	/**
	 *  EDD Purchase Form Custom Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_purchase_form_custom_fields() {

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
			if ( $server_name['custom_field2'][0] == $server['server_label'] ) {
			
				$droplet_data = array(
					"name"			=>	$server['server_label'],
    				"region"		=>	$server['region'],
					"cloud"			=>	$server['cloud'],
				);
				
				if ( ( 'Cloudways' == $plan_module ) && ( ( 'userselected' == $droplet_data['region'] ) || ( '[Customer Input]' == $server['host_name'] ) ) ) {
					
					?>
						<legend>Website Information</legend>

					<?php
				
					if ( 'userselected' == $droplet_data['region'] ) {
					
						$plans	= wpcs_cloudways_plans_list();
			
						foreach ( $plans as $plan ) {
							if ( $server['size'] == $plan['name'] ) {
								$location_list = $plan['available_locations'];
							}
						}
			
						$regions = wpcs_cloudways_regions_list();
			
						foreach ( $regions as $region ) {
							foreach ( $location_list as $location ) {
								if ( $region['DCID'] == $location ) {
									$available[] = $region;
								}
							}
						}		
			
						?>

						<p>
						<label class="edd-label" for="edd_server_location"><?php esc_html_e( 'Website Location', 'wp-cloud-server-cloudways' ); ?><span class="edd-required-indicator">*</span></label>
						<select name="edd_server_location" id="edd_server_location">
							<?php foreach ( $available as $region ) { ?>
            				<option value="<?php echo $region['DCID']; ?>" ><?php echo $region['name']; ?></option>
							<?php } ?>
						</select>

						</p>
			<?php }
					
					
				if ( '[Customer Input]' == $server['host_name'] ) {
					
					?>

			<p>
				<label class="edd-label" for="edd_host_name"><?php esc_html_e( 'Hostname', 'wp-cloud-server-cloudways' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_host_name" id="edd_host_name" placeholder="<?php esc_attr_e( 'Hostname', 'wp-cloud-server-cloudways' ); ?>" value=""/>
			</p>

		<?php
				}

			

?>
			<!--
			<p>
				<label class="edd-label" for="edd_user_name"><?php esc_html_e( 'Username', 'wp-cloud-server-cloudways' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_user_name" id="edd_user_name" placeholder="<?php esc_attr_e( 'Username', 'wp-cloud-server-cloudways' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_password"><?php esc_html_e( 'Password', 'wp-cloud-server-cloudways' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_password" id="edd_user_password" placeholder="<?php esc_attr_e( 'Password', 'wp-cloud-server-cloudways' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_confirm_password"><?php esc_html_e( 'Confirm Password', 'wp-cloud-server-cloudways' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_confirm_password" id="edd_user_confirm_password" placeholder="<?php esc_attr_e('Confirm Password', 'wp-cloud-server-cloudways'); ?>" value=""/>
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
	public function wpcs_cloudways_purchase_required_checkout_fields( $required_fields ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'Cloudways' == $plan_module ) {
			
			$required_fields['edd_host_name'] = array(
				'error_id' => 'invalid_host_name',
				'error_message' => 'Please enter a valid hostname',
			);
			
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
	public function wpcs_cloudways_purchase_form_error_checks( $valid_data, $data ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'Cloudways' == $plan_module ) {
			
			if ( ! isset( $data['edd_host_name'] ) || $data['edd_host_name'] == '' ) {
            	// check for a valid host name
            	edd_set_error( 'invalid_host_name', __( 'You must provide a valid host name.', 'wp-cloud-server-cloudways' ) );
        	}
			
        	//if ( ! isset( $data['edd_domain_name'] ) || $data['edd_domain_name'] == '' ) {
            	// check for a valid domain name
            // 	edd_set_error( 'invalid_domain_name', __( 'You must provide a valid domain name.', 'wp-cloud-server-cloudways' ) );
        	//}

        	//if ( ! isset( $data['edd_user_name'] ) || $data['edd_user_name'] == '' ) {
            	// check for a valid user name
            //	edd_set_error( 'invalid_user_name', __( 'You must provide a valid user name.', 'wp-cloud-server-cloudways' ) );
        	//}
			
        	//if ( ! isset( $data['edd_user_password'] ) || $data['edd_user_password'] == '' ) {
            	// check for a valid password
            //	edd_set_error( 'invalid_password', __( 'You must provide a valid password.', 'wp-cloud-server-cloudways' ) );
        	//}
			
			//if ( ! isset( $data['edd_user_confirm_password'] ) || $data['edd_user_confirm_password'] == '' ) {
            	// check for a valid password confirmation
            //	edd_set_error( 'invalid_confirm_password', __( 'You must provide a valid password.', 'wp-cloud-server-cloudways' ) );
        	//}
			
		}
        
    }

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_purchase_client_details_list( $payment_meta, $user_info ) {

			$host_name 		= isset( $payment_meta['host_name'] ) 		? sanitize_text_field( $payment_meta['host_name'] ) : 'none';
       	 	$domain_name 	= isset( $payment_meta['domain_name'] ) 	? sanitize_text_field( $payment_meta['domain_name'] ) : 'none';
			$user_name 		= isset( $payment_meta['user_name'] ) 		? sanitize_text_field( $payment_meta['user_name'] ) : 'none';
			$user_password 	= isset( $payment_meta['user_password'] )	? sanitize_text_field( $payment_meta['user_password'] ) : 'none';
        	?>
        	<li><?php echo __( 'Host Namecw:', 'wp-cloud-server-cloudways' ) . ' ' . $host_name; ?></li>
        	<li><?php echo __( 'Domain Namecw:', 'wp-cloud-server-cloudways' ) . ' ' . $domain_name; ?></li>
			<li><?php echo __( 'Usernamecw:', 'wp-cloud-server-cloudways' ) . ' ' . $user_name; ?></li>
			<li><?php echo __( 'User Passwordcw:', 'wp-cloud-server-cloudways' ) . ' ' . $user_password; ?></li>
			<?php
		     
    }

	/**
	 *  EDD Purchase Complete Create ServerPilot Service
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_purchase_complete_create_service( $module_name, $data ) {

		if ( 'Cloudways' == $module_name ) {
			
			$api = new WP_Cloud_Server_Cloudways_API();

			// Retrieve the user entered account details
			$domain_name		= wpcs_sanitize_domain_strip_http( $data['domain_name'] );
			$user_name			= $data['user_name'];
			$user_pass			= $data['user_password'];
			$user_email			= $data['user_email'];
			$host_name			= $data['host_name'];
			$server_name		= $data['server_name'];
			$server_location	= $data['server_location'];
			$site_label			= $data['site_label'];
			$user_id			= $data['user_id'];
			$plan_name			= $data['plan_name'];
			
			$user_meta			= null;
			
			// Retrieve the Active Module List
			$module_data		= get_option( 'wpcs_module_list' );
			$template_data		= get_option( 'wpcs_template_data_backup' );

			// Retrieve the correct cloud server template data
			foreach ( $module_data[ $module_name ]['templates'] as $server ) {
				if ( $server_name == $server['name'] ) {
					
					// Set-up the data
					$server_region			= ( 'userselected' == $server['region'] ) ? $server_location : $server['region'];
					$site_counter			= $server['site_counter'];
					$host_name_config		= $server['host_name'];
					$region_name			= $server['region_name'];
					$size_name				= $server['size'];
					$image_name				= "{$server['app_label']} {$server['app_version']}";

					$cloud_provider			= $server['cloud'];
					$application			= $server['image'];
					$app_version			= $server['app_version'];
					$app_name				= $server['app_name'];
					$project_name			= $server['project_name'];
					$db_volume_size			= $server['db_volume_size'];
					$data_volume_size		= $server['data_volume_size'];
					$send_email				= isset( $server['send_email'] ) ? $server['send_email'] : false;
				}
			}
			
			if ( '[Customer Input]' !== $host_name_config ) {
			
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
								++$site_counter;
								++$tmp_host_name_counter;
								$host_name_only	= "{$tmp_host_name}{$tmp_host_name_counter}";
								$host_name_fqdn = "{$host_name_only}.{$tmp_host_name_domain}";
					
								foreach ( $module_data[ $module_name ]['templates'] as $key => $server ) {
									if ( $server_name == $server['name'] ) {
										$module_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
										$template_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
										update_option( 'wpcs_module_list', $module_data );
										update_option( 'wpcs_template_data_backup', $template_data );
									}
								}
								$host_names[$host_name['label']]['count'] = $tmp_host_name_counter; 
								update_option( 'wpcs_host_names', $host_names );
							}
						}
					}
				}					
			} else {

				// Hostname has been entered at checkout
				$host_name_only = $host_name;
			}

			// Set-up the data for the new server
			$app_data = array(
							"cloud"				=>	$cloud_provider,
							"region"			=>	$server_region,
							"instance_type"		=>	$size_name,
							"application"		=> 	$application,
							"app_version"		=>	$app_version,
							"server_label"		=>	$host_name_only,
							"app_label"			=>	$app_name,
							"project_name"		=>	null,
							"db_volume_size"	=>	$db_volume_size,
							"data_volume_size"	=>	$data_volume_size,
							"series"			=>	null,
						);
			
			update_option( 'wpcs_cloudways_create_server_data', $app_data );

			// Send the API POST request to create the new 'server'
			$response		= $api->call_api( 'server', $app_data, false, 0, 'POST', false, 'create_cloudways_server' );
			
			update_option( 'wpcs_cloudways_create_server_api_response', $response );
			$server_sub_id	= $response['server']['id'];
			
			// Retrieve existing queue state
			$server_queue	= get_option( 'wpcs_cloudways_server_complete_queue' );
			
			// Add server to queue for completion
			$server_queue[] = array(
				'SUBID'				=> $server_sub_id,
				'user_id'			=> $user_id,
				'user_email'		=> $user_email,
				'send_email'		=> $send_email,
				'response'			=> $response,
				'domain_name'		=> $domain_name,
				'host_name'			=> $host_name_only,
				'host_name_domain'	=> isset( $tmp_host_name_domain ) ? $tmp_host_name_domain : '',
				'fqdn'				=> $response['server']['server_fqdn'],				
				'protocol'			=> isset( $tmp_host_name_protocol ) ? $tmp_host_name_protocol : '',				
				'port'				=> isset( $tmp_host_name_port ) ? $tmp_host_name_port : '',				
				'site_label'		=> $site_label,
				'user_meta'			=> $user_meta,
				'plan_name'			=> $plan_name,
				'module'			=> $module_name,
				'ssh_key'			=> isset( $server_ssh_key_name ) ? isset( $server_ssh_key_name ) : '',
				'location'			=> $server_region,
				'ip_address'		=> $response['server']['public_ip'],
				'php_version'		=> '',
				'region_name'		=> $region_name,
				'size_name'			=> $size_name,
				'image_name'		=> $image_name,
			);
			
			$debug['server_queue'] = $server_queue;
			
			// Send new server details to completion queue after checking valid response
			if ( isset( $server_sub_id ) ) {
				update_option( 'wpcs_cloudways_server_complete_queue', $server_queue );
			}
			update_option( 'wpcs_item_to_queue', $debug );
		} 
    }  
}