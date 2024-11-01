<?php

/**
 * The Easy Digital Downloads functionality for the DigitalOcean Module.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_DigitalOcean_Cart_EDD {
		
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
		add_action( 'wpcs_edd_purchase_complete_create_service', array( $this, 'wpcs_digitalocean_purchase_complete_create_service' ), 10, 2 );
		add_action( 'wpcs_wc_purchase_complete_create_service', array( $this, 'wpcs_digitalocean_purchase_complete_create_service' ), 10, 2 );

		// Add Custom Checkout Fields for the EDD checkout
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_digitalocean_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'wpcs_digitalocean_purchase_form_error_checks' ), 10, 2 );
		//add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_digitalocean_purchase_client_details_list' ), 10, 2 );
		
		// Hook into Easy Digital Downloads filters.
		add_filter( 'edd_purchase_form_required_fields', array( $this, 'wpcs_digitalocean_purchase_required_checkout_fields' ) );

		self::$api = new WP_Cloud_Server_DigitalOcean_API();

	}
	
	/**
	 *  EDD Purchase Form Custom Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_purchase_form_custom_fields() {
		
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
		foreach ( $module_data[ $server_name['custom_field1'][0] ]['templates'] as $server ) {
			if ( $server_name['custom_field2'][0] == $server['name'] ) {
			
				$droplet_data = array(
					"name"			=>	$server['name'],
					"host_name"		=>	( isset( $server['host_name'] ) ) ? $server['host_name'] : '',
    				"region"		=>	$server['region'],
					"size"			=>	$server['size'],
					"image"			=> 	$server['image'],
				);
					
			}
		}
		
				if ( ( 'DigitalOcean' == $plan_module ) && ( ( '[Customer Input]' == $droplet_data['host_name'] ) || ( 'userselected' == $droplet_data['region'] ) ) ) {
                
					if ( '[Customer Input]' == $droplet_data['host_name'] ) {
						?>
			<legend class="website-details"><?php _e( 'New Server Details', 'wp-cloud-server' ); ?></legend>

        	<p>
				<label class="edd-label" for="edd_host_name"><?php _e( 'Host Name', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_host_name" id="edd_host_name" placeholder="<?php esc_attr_e( 'Host Name', 'wp-cloud-server' ); ?>" value=""/>
			</p>

			<?php
            }
            
            if ( 'userselected' == $droplet_data['region'] ) { ?>
			<p>
				<label class="edd-label" for="edd_server_location"><?php _e( 'Website Location', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
				<select name="edd_server_location" id="edd_server_location">
            		<option value="ams"><?php _e( 'Amsterdam', 'wp-cloud-server' ); ?></option>
            		<option value="blr"><?php _e( 'Bangalore', 'wp-cloud-server' ); ?></option>
            		<option value="fra"><?php _e( 'Frankfurt', 'wp-cloud-server' ); ?></option>
            		<option value="lon"><?php _e( 'London', 'wp-cloud-server' ); ?></option>
            		<option value="nyc"><?php _e( 'New York', 'wp-cloud-server' ); ?></option>
            		<option value="sfo"><?php _e( 'San Francisco', 'wp-cloud-server' ); ?></option>
            		<option value="sgp"><?php _e( 'Singapore', 'wp-cloud-server' ); ?></option>
            		<option value="tor"><?php _e( 'Toronto', 'wp-cloud-server' ); ?></option>
				</select>

			</p>
<?php } ?>
<!--
        	<p>
				<label class="edd-label" for="edd_domain_name"><?php _e( 'Domain Name', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_domain_name" id="edd_domain_name" placeholder="<?php esc_attr_e( 'Domain Name', 'wp-cloud-server' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_name"><?php _e( 'Username', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_user_name" id="edd_user_name" placeholder="<?php esc_attr_e( 'Username', 'wp-cloud-server' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_password"><?php _e( 'Password', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_password" id="edd_user_password" placeholder="<?php esc_attr_e( 'Password', 'wp-cloud-server' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_confirm_password"><?php _e( 'Confirm Password', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_confirm_password" id="edd_user_confirm_password" placeholder="<?php esc_attr_e('Confirm Password', 'wp-cloud-server'); ?>" value=""/>
			</p>
-->
        <?php
		}

	}
	
	/**
	 *  EDD Purchase Form Required Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_purchase_required_checkout_fields( $required_fields ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'DigitalOcean' == $plan_module ) {
			
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
	public function wpcs_digitalocean_purchase_form_error_checks( $valid_data, $data ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'DigitalOcean' == $plan_module ) {
			
        	//if ( ! isset( $data['edd_host_name'] ) || $data['edd_host_name'] == '' ) {
            	// check for a valid host name
            //	edd_set_error( 'invalid_host_name', __( 'You must provide a valid host name.', 'wp-cloud-server' ) );
        	//}
			
        	//if ( ! isset( $data['edd_domain_name'] ) || $data['edd_domain_name'] == '' ) {
            	// check for a valid domain name
            //	edd_set_error( 'invalid_domain_name', __( 'You must provide a valid domain name.', 'wp-cloud-server' ) );
        	//}

        	//if ( ! isset( $data['edd_user_name'] ) || $data['edd_user_name'] == '' ) {
            	// check for a valid user name
            //	edd_set_error( 'invalid_user_name', __( 'You must provide a valid user name.', 'wp-cloud-server' ) );
        	//}
			
        	//if ( ! isset( $data['edd_user_password'] ) || $data['edd_user_password'] == '' ) {
            	// check for a valid password
            //	edd_set_error( 'invalid_password', __( 'You must provide a valid password.', 'wp-cloud-server' ) );
        	//}
			
			//if ( ! isset( $data['edd_user_confirm_password'] ) || $data['edd_user_confirm_password'] == '' ) {
            	// check for a valid password confirmation
            //	edd_set_error( 'invalid_confirm_password', __( 'You must provide a valid password.', 'wp-cloud-server' ) );
        	//}
			
		}
        
    }

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_purchase_client_details_list( $payment_meta, $user_info ) {

		$host_name 		= isset( $payment_meta['host_name'] )		? sanitize_text_field( $payment_meta['host_name'] ) : 'none';
        $domain_name 	= isset( $payment_meta['domain_name'] ) 	? sanitize_text_field( $payment_meta['domain_name'] ) : 'none';
		$user_name 		= isset( $payment_meta['user_name'] ) 		? sanitize_text_field( $payment_meta['user_name'] ) : 'none';
		$user_password 	= isset( $payment_meta['user_password'] )	? sanitize_text_field( $payment_meta['user_password'] ) : 'none';
        ?>
		<li><?php echo __( 'Host Name:', 'wp-cloud-server' ) . ' ' . $host_name; ?></li>
        <li><?php echo __( 'Domain Name:', 'wp-cloud-server' ) . ' ' . $domain_name; ?></li>
		<li><?php echo __( 'Username:', 'wp-cloud-server' ) . ' ' . $user_name; ?></li>
		<li><?php echo __( 'User Password:', 'wp-cloud-server' ) . ' ' . $user_password; ?></li>
		<?php
		     
    }

	/**
	 *  EDD Purchase Complete Create ServerPilot Service
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_purchase_complete_create_service( $module_name, $data ) {

		if ( 'DigitalOcean' == $module_name ) {

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
			
			$user_meta = null;
			
			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
            $template_data 	= get_option( 'wpcs_template_data_backup' );

			// Retrieve the correct cloud server template data
			foreach ( $module_data[ $module_name ]['templates'] as $server ) {
				if ( $server_name == $server['name'] ) {
					
					// The Region could be user selectable
					if ( 'userselected' == $server['region'] ) {
						$server_region = $server_location;
					} else {
						$server_region = $server['region'];
					}
					
					$droplet_data = array(
						"name"			=>	$host_name,
    					"region"		=>	$server_region,
						"size"			=>	$server['size'],
						"image"			=> 	$server['image'],
						"user_data"		=>	$user_meta,
						"backups"		=>	$server['backups'],
					);
					
					$server_data = array(
						"plan_name"		=>	$plan_name,
						"module"		=>	$module_name,
						"host_name"		=>	$host_name,
						"server_name"	=>	$site_label,
    					"region_name"	=>	$server_region,
						"size_name"		=>	$server['size_name'],
						"image_name"	=> 	$server['image_name'],
						"ssh_key_name"	=> 	$server['ssh_key_name'],						
					);

					$server_ssh_key_name		= $server['ssh_key_name'];
					$server_startup_script_name = $server['user_data'];
					$site_counter				= $server['site_counter'];
					$host_name_config			= $server['host_name'];
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
								
								$droplet_data["name"] = $host_name_only;
					
								//foreach ( $module_data[ $module_name ]['templates'] as $key => $server ) {
								//	if ( $server_name == $server['name'] ) {
								//		$module_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
								//		$template_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
								//		update_option( 'wpcs_module_list', $module_data );
								//		update_option( 'wpcs_template_data_backup', $template_data );
								//	}
								//}
								$host_names[$host_name['label']]['count'] = $tmp_host_name_counter; 
								update_option( 'wpcs_host_names', $host_names );
							}
						}
					}
				}					
			} 
			
			// Increment the template site counter - this needs moving to completion queue
			foreach ( $module_data[ $module_name ]['templates'] as $key => $server ) {
				if ( $server_name == $server['name'] ) {
					// Retrieve the current template site counter
					$site_counter = $module_data[ $module_name ]['templates'][$key]['site_counter'];

					// Increment the site counter
					++$site_counter;

					// Save the new counter value and update module and template data
					$module_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
					$template_data[ $module_name ]['templates'][$key]['site_counter'] = $site_counter;
					update_option( 'wpcs_module_list', $module_data );
					update_option( 'wpcs_template_data_backup', $template_data );
				}
			}

            		
			// Need to check the availability of the region and select available datacentre
			$regions = self::$api->call_api( 'regions', null, false, 900, 'GET', false, 'get_regions' );
		
			foreach ( $regions['regions'] as $region ) {
				$regions_test['regions'] = $regions['regions'];
				for ($x = 1; $x <= 3; $x++) {
					$region_slug = "{$droplet_data['region']}{$x}";
					$regions_test['data'][] = $region_slug;
					if ( ( $region['available'] == 1 ) && ( $region_slug == $region['slug'] ) ) {
						if ( in_array( $droplet_data['size'], $region['sizes'] ) ) {
							$droplet_data['region'] = $region['slug'];
							$regions_test['result'] = $region['slug'];
						}	
					}
				}
				update_option( 'regions_test_data', $regions_test );
			}

			$server_module		= strtolower( $module_name );

			// Check if SSH Key saved with provider
			$ssh_key_id			= call_user_func("wpcs_{$server_module}_ssh_key", $server_ssh_key_name );

			// Retrieve any startup scripts selected
			$startup_scripts	= get_option( 'wpcs_startup_scripts' );

			if ( !empty( $startup_scripts ) ) {
				foreach ( $startup_scripts as $key => $script ) {
					if ( $server_startup_script_name == $script['name'] ) {
						$server_script = $script['startup_script'];
					}	
				}
			}
			
			// Use SSH Key if available or generate root password
			if ( $ssh_key_id ) {
				$droplet_data["ssh_keys"][]	= $ssh_key_id;
			} else {
				
				if ( !isset( $server_script ) ) {
					$root_password	= wp_generate_password( 20, true, false );
					$server_script 	= password_install_script( $root_password );
				}
				
				$to = $user_email;
				$subject = 'WP Cloud Server - New DigitalOcean Server';
				$body  = __( "Dear", "wp-cloud-server" ) . ' ' . $user_name . "\n\n";
				$body .= __( "Your new server is ready to go. The login details are;", "wp-cloud-server" ) . "\n\n";
				$body .= __( "Username: root", "wp-cloud-server" ) . "\n";
				$body .= __( "Password: ", "wp-cloud-server" ) . ' ' . $root_password . "\n\n";
				$body .= __( "Thank you.", "wp-cloud-server" ) . "\r\n";			
				wp_mail( $to, $subject, $body );
			}
			
			if ( isset( $server_script ) ) {
				$droplet_data["user_data"]	= $server_script;
			}
			
			update_option( 'wpcs_droplet_data_for_server', $droplet_data );
			
			// Send the API POST request to create the new 'Droplet'
			$response		= self::$api->call_api( 'droplets', $droplet_data, false, 0, 'POST', false, 'create_digitalocean_server' );
            
            $server_sub_id	= $response['droplet']['id'];

			// Retrieve existing queue state
			$server_queue	= get_option( 'wpcs_server_complete_queue' );
			
			// Add server to queue for completion
			$server_queue[] = array(
				'SUBID'				=> $server_sub_id,
				'user_id'			=> $user_id,
				'response'			=> $response,
				'domain_name'		=> $domain_name,
				'host_name'			=> $droplet_data["name"],
				'host_name_domain'	=> $tmp_host_name_domain,
				'fqdn'				=> $host_name_fqdn,				
				'protocol'			=> $tmp_host_name_protocol,				
				'port'				=> $tmp_host_name_port,				
				'site_label'		=> $site_label,
				'user_meta'			=> $user_meta,
				'backups'			=> $droplet_data["backups"],
				'plan_name'			=> $plan_name,
				'module'			=> $module_name,
				'ssh_key'			=> $server_ssh_key_name,
				'location'			=> $server_location,
				'region_name'		=> $server_data['region_name'],
				'size_name'			=> $server_data['size_name'],
				'image_name'		=> $server_data['image_name'],
			);
			
			$debug['server_queue'] = $server_queue;
			
			// Send new server details to completion queue after checking valid response
			if ( isset( $server_sub_id ) ) {
				update_option( 'wpcs_server_complete_queue', $server_queue );
			}
			
			update_option( 'wpcs_item_to_queue', $debug );
		} 
    } 
}