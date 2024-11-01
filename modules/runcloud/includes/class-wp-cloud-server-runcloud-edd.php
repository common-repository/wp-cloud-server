<?php

/**
 * WP Cloud Server - RunCloud Module EDD Cart Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_RunCloud_Cart_EDD {
		
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
		add_action( 'wpcs_edd_purchase_complete_create_service', array( $this, 'wpcs_runcloud_purchase_complete_create_service' ), 10, 2 );
		add_action( 'wpcs_wc_purchase_complete_create_service', array( $this, 'wpcs_runcloud_purchase_complete_create_service' ), 10, 2 );

		// Add Custom Checkout Fields for the EDD checkout
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_runcloud_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'wpcs_runcloud_purchase_form_error_checks' ), 10, 2 );
		//add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_runcloud_purchase_client_details_list' ), 10, 2 );
		
		// Hook into Easy Digital Downloads filters.
		add_filter( 'edd_purchase_form_required_fields', array( $this, 'wpcs_runcloud_purchase_required_checkout_fields' ) );

		self::$api = new WP_Cloud_Server_RunCloud_API();

	}
	
	/**
	 *  EDD Purchase Form Custom Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_purchase_form_custom_fields() {

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
				
				if ( ( 'RunCloud' == $plan_module ) && ( ( 'host_name_field' == $droplet_data['host_name'] ) || ( 'userselected' == $droplet_data['region'] ) ) ) {
			
					if ( 'host_name_field' == $droplet_data['host_name'] ) {
						?>
						<legend class="website-details"><?php esc_html_e( 'New Server Details', 'wp-cloud-server-runcloud' ); ?></legend>
			
        				<p>
						<label class="edd-label" for="edd_host_name"><?php esc_html_e( 'Host Name', 'wp-cloud-server-runcloud' ); ?><span class="edd-required-indicator">*</span></label>
            			<input class="edd-input required" type="text" name="edd_host_name" id="edd_host_name" placeholder="<?php esc_attr_e( 'host-name', 'wp-cloud-server-runcloud' ); ?>" value=""/>
						</p>

						<?php
					}
				
					if ( 'userselected' == $droplet_data['region'] ) {
					
						$plans	= wpcs_runcloud_plans_list();
			
						foreach ( $plans as $plan ) {
							if ( $server['size'] == $plan['name'] ) {
								$location_list = $plan['available_locations'];
							}
						}
			
						$regions = wpcs_runcloud_regions_list();
			
						foreach ( $regions as $region ) {
							foreach ( $location_list as $location ) {
								if ( $region['DCID'] == $location ) {
									$available[] = $region;
								}
							}
						}		
			
						?>

						<p>
						<label class="edd-label" for="edd_server_location"><?php esc_html_e( 'Website Location', 'wp-cloud-server-runcloud' ); ?><span class="edd-required-indicator">*</span></label>
						<select name="edd_server_location" id="edd_server_location">
							<?php foreach ( $available as $region ) { ?>
            				<option value="<?php echo $region['DCID']; ?>" ><?php echo $region['name']; ?></option>
							<?php } ?>
						</select>

						</p>
			<?php } ?>
			<!--
			<p>
				<label class="edd-label" for="edd_user_name"><?php esc_html_e( 'Username', 'wp-cloud-server-runcloud' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_user_name" id="edd_user_name" placeholder="<?php esc_attr_e( 'Username', 'wp-cloud-server-runcloud' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_password"><?php esc_html_e( 'Password', 'wp-cloud-server-runcloud' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_password" id="edd_user_password" placeholder="<?php esc_attr_e( 'Password', 'wp-cloud-server-runcloud' ); ?>" value=""/>
			</p>
			<p>
				<label class="edd-label" for="edd_user_confirm_password"><?php esc_html_e( 'Confirm Password', 'wp-cloud-server-runcloud' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="password" name="edd_user_confirm_password" id="edd_user_confirm_password" placeholder="<?php esc_attr_e('Confirm Password', 'wp-cloud-server-runcloud'); ?>" value=""/>
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
	public function wpcs_runcloud_purchase_required_checkout_fields( $required_fields ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'RunCloud' == $plan_module ) {
			
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
	public function wpcs_runcloud_purchase_form_error_checks( $valid_data, $data ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'RunCloud' == $plan_module ) {
			
			//if ( ! isset( $data['edd_host_name'] ) || $data['edd_host_name'] == '' ) {
            	// check for a valid host name
            //	edd_set_error( 'invalid_host_name', __( 'You must provide a valid host name.', 'wp-cloud-server-runcloud' ) );
        	//}
			
        	//if ( ! isset( $data['edd_domain_name'] ) || $data['edd_domain_name'] == '' ) {
            	// check for a valid domain name
            // 	edd_set_error( 'invalid_domain_name', __( 'You must provide a valid domain name.', 'wp-cloud-server-runcloud' ) );
        	//}

        	//if ( ! isset( $data['edd_user_name'] ) || $data['edd_user_name'] == '' ) {
            	// check for a valid user name
            //	edd_set_error( 'invalid_user_name', __( 'You must provide a valid user name.', 'wp-cloud-server-runcloud' ) );
        	//}
			
        	//if ( ! isset( $data['edd_user_password'] ) || $data['edd_user_password'] == '' ) {
            	// check for a valid password
            //	edd_set_error( 'invalid_password', __( 'You must provide a valid password.', 'wp-cloud-server-runcloud' ) );
        	//}
			
			//if ( ! isset( $data['edd_user_confirm_password'] ) || $data['edd_user_confirm_password'] == '' ) {
            	// check for a valid password confirmation
            //	edd_set_error( 'invalid_confirm_password', __( 'You must provide a valid password.', 'wp-cloud-server-runcloud' ) );
        	//}
			
		}
        
    }

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_purchase_client_details_list( $payment_meta, $user_info ) {

		$host_name 		= isset( $payment_meta['host_name'] ) 		? sanitize_text_field( $payment_meta['host_name'] ) : 'none';
        $domain_name 	= isset( $payment_meta['domain_name'] ) 	? sanitize_text_field( $payment_meta['domain_name'] ) : 'none';
		$user_name 		= isset( $payment_meta['user_name'] ) 		? sanitize_text_field( $payment_meta['user_name'] ) : 'none';
		$user_password 	= isset( $payment_meta['user_password'] )	? sanitize_text_field( $payment_meta['user_password'] ) : 'none';
        ?>
        <li><?php echo __( 'Host Name:', 'wp-cloud-server-runcloud' ) . ' ' . $host_name; ?></li>
        <li><?php echo __( 'Domain Name:', 'wp-cloud-server-runcloud' ) . ' ' . $domain_name; ?></li>
		<li><?php echo __( 'Username:', 'wp-cloud-server-runcloud' ) . ' ' . $user_name; ?></li>
		<li><?php echo __( 'User Password:', 'wp-cloud-server-runcloud' ) . ' ' . $user_password; ?></li>
		<?php
		     
    }

	/**
	 *  EDD Purchase Complete Create ServerPilot Service
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_purchase_complete_create_service( $module_name, $data ) {

		if ( 'RunCloud' == $module_name ) {

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
			$module_data		= get_option( 'wpcs_module_list' );
			$template_data		= get_option( 'wpcs_template_data_backup' );

			// Retrieve the correct cloud server template data
			foreach ( $module_data[ $module_name ]['templates'] as $server ) {
				if ( $server_name == $server['name'] ) {
					$server_size 			= $server['size'];
					$server_image			= $server['image'];
					$server_region			= ( 'userselected' == $server['region'] ) ? $server_location : $server['region'];
					$server_cloud_provider	= $server['module'];
					$user_meta				= $server['user_data'];
					$server_ssh_key 		= $server['ssh_key'];
					$site_counter			= $server['site_counter'];
					$host_name_config		= $server['host_name'];
					$server_web_app			= $server['web_app'];
					$server_default_app	    = $server['default_app'];
					$server_enable_backups	= $server['backups'];
					$system_user_name	    = $server['system_user_name'];
					$system_user_password	= $server['system_user_password'];
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
								$host_name_only	= str_replace( "-", "", $host_name_only );
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
			
			$server_region	= ( isset( $api_response['region'] ) ) ? $api_response['region'] : $server_region;
    
			$server_module	= strtolower( str_replace( " ", "_", $server_cloud_provider ) );
			
			// Set-up the API Data for the New Server
			$api_data = array(
				"region"	=>	$server_region,
				"size"		=>	$server_size,
			);

			// Check and Select the Available Region
			$api_response   = call_user_func("wpcs_{$server_module}_cloud_server_api", $api_data, 'regions', null, false, 900, 'GET', false, 'check_data_centers' );
						
			$server_region	= ( isset( $api_response['region'] ) ) ? $api_response['region'] : $server_region;
			
			if ( 'aws_lightsail' == $server_module ) {
				$ip_address = call_user_func("wpcs_{$server_module}_get_static_ip", $host_name_only, $server_region );
			}
			$ip_address	= ( isset( $ip_address ) && $ip_address ) ? $ip_address : 'not_required';
		
			// Set-up the ethernet port for the script (Vultr uses ens3 instead of eth0)
			$network	= ( 'vultr' == $server_module ) ? 'ens3' : 'eth0';
			
			$api_key		= get_option( 'wpcs_runcloud_api_key' );
			$api_secret 	= get_option( 'wpcs_runcloud_api_secret' );
			$server_script	= <<<EOF
#!/bin/bash
export APISECRET=$api_secret
export APIKEY=$api_key
export SERVERNAME=$host_name_only
export PROVIDER=$server_module
export DEBIAN_FRONTEND=noninteractive
apt-get -y update
apt-get -y install wget ca-certificates curl jq util-linux && \
if [ "\$PROVIDER" = "digitalocean" ]
then
sudo sed -i 's/^root:.*$/root:*:16231:0:99999:7:::/' /etc/shadow
fi
if [ "\$PROVIDER" = "aws_lightsail" ]
then
export IPADDRESS=$ip_address
else
export IPADDRESS=$(ip -f inet -o addr show $network|cut -d\  -f 7 | cut -d/ -f 1 | cut -d$'\\n' -f1)
echo \$IPADDRESS
fi
SERVERID=$(curl --request POST --url https://manage.runcloud.io/api/v2/servers -u \${APIKEY}:\${APISECRET} --header "accept: application/json" --header "content-type: application/json" --data "{\"name\": \"\${SERVERNAME}\", \"ipAddress\": \"\${IPADDRESS}\", \"provider\": \"\${PROVIDER}\"}" | jq -r '.id' )
SCRIPT=$(curl --request GET --url https://manage.runcloud.io/api/v2/servers/\${SERVERID}/installationscript -u \${APIKEY}:\${APISECRET} --header "accept: application/json" --header "content-type: application/json" | jq -r '.script' )
echo "\$SCRIPT" > build.sh
chmod +x build.sh
./build.sh
EOF;
			// Set-up the data for the new Droplet
			$app_data = array(
				"name"			=>	$host_name_only,
				"region"		=>	$server_region,
				"size"			=>	$server_size,
				"image"			=> 	$server_image,
				"user_data"		=>  $server_script,
				"backups"		=>	$server_enable_backups,
			);
						
			// Read in any Custom Settings from Template
			if ( isset( $server_size ) ) {
				$app_data['custom_settings']['OSID']		= $server_image;
				$app_data['custom_settings']['DCID']		= $server_region;
				$app_data['custom_settings']['VPSPLANID']	= $server_size;			
				$app_data['custom_settings']['label']		= $host_name_only;
				
				// Give the installer script a randon name in case script already exists!
				$random_number = wp_rand( 1000, 9000 );
				$app_data['custom_settings']['script_name'] = "runcloud-installer-{$random_number}";
			}
			
			// Determine the SSH Key
			$server_ssh_keys		= get_option( 'wpcs_serverpilots_ssh_keys' );
			$server_ssh_public_key	= ( ! empty( $serverpilot_ssh_keys[$server_ssh_key]['public_key'] ) ) ? $serverpilot_ssh_keys[$server_ssh_key]['public_key'] : 'no_ssh_key';
		
			// Check if SSH Key saved with provider
			$ssh_key_id	= call_user_func("wpcs_{$server_module}_ssh_key", $server_ssh_key, $server_region );
			
			// Use SSH Key if available or generate root password
			if ( $ssh_key_id ) {
				$app_data["ssh_keys"][]	= $ssh_key_id;
			}
			
			$debug['app_data'] = $app_data;

			// Send the API POST request to create the new server
			$response = call_user_func("wpcs_{$server_module}_cloud_server_api", null, 'droplets', $app_data, false, 0, 'POST', false, 'site_creation' );
			
			$server_sub_id = isset( $response['operations'][0]['id'] ) ? $response['operations'][0]['id'] : '';
			
			$debug['site_creation']	= $response;
			$debug['module']		= $server_module;
			
			// Retrieve existing queue state
			$server_queue	= get_option( 'wpcs_server_complete_queue' );
			
			// Add server to queue for completion
			$server_queue[] = array(
				'SUBID'					=> $server_sub_id,
				'user_id'				=> $user_id,
				'response'				=> $response,
				'domain_name'			=> ( '' == $domain_name ) ? $tmp_host_name_domain : $domain_name,
				'host_name'				=> $host_name_only,
				'host_name_domain'		=> isset( $tmp_host_name_domain ) ? $tmp_host_name_domain : '',
				'fqdn'					=> isset( $host_name_fqdn ) ? $host_name_fqdn : '',				
				'protocol'				=> isset( $tmp_host_name_protocol ) ? $tmp_host_name_protocol : '',				
				'port'					=> isset( $tmp_host_name_port ) ? $tmp_host_name_port : '',				
				'site_label'			=> $site_label,
				'user_meta'				=> $user_meta,
				'plan_name'				=> $plan_name,
				'module'				=> $module_name,
				'server_module'			=> $server_module,
				'ssh_key'				=> $server_ssh_key,
				'location'				=> $server_region,
				'web_app'				=> $server_web_app,
				'default_app'			=> $server_default_app,
				'system_user_name'		=> ( !empty( $system_user_name ) ) ? $system_user_name : 'runcloud',
				'system_user_password'	=> $system_user_password,
				'backups'				=> $server_enable_backups,
				'static_ip'				=> true,
				'open_port'				=> true,				
			);
			
			$debug['server_queue'] = $server_queue;
			
			// Send new server details to completion queue after checking valid response
			update_option( 'wpcs_server_complete_queue', $server_queue );
			update_option( 'wpcs_runcloud_edd_debug', $debug );
		} 
    }  
}