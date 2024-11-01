<?php
/**
 * The Easy Digital Downloads functionality for the ServerPilot Module.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_ServerPilot_Cart_EDD {
		
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

		add_action( 'wpcs_edd_purchase_complete_create_service', array( $this, 'wpcs_sp_purchase_complete_create_service' ), 10, 2 );
		add_action( 'wpcs_wc_purchase_complete_create_service', array( $this, 'wpcs_sp_purchase_complete_create_service' ), 10, 2 );
		add_action( 'edd_purchase_form_user_info_fields', array( $this, 'wpcs_sp_purchase_form_custom_fields' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'wpcs_sp_purchase_form_error_checks' ), 10, 2 );
		//add_action( 'edd_payment_personal_details_list', array( $this, 'wpcs_sp_purchase_client_details_list' ), 10, 2 );
		add_action( 'edd_add_email_tags', array( $this, 'wpcs_serverpilot_edd_add_email_tag' ) );
		add_action( 'edd_payment_meta', array( $this, 'wpcs_serverpilot_edd_store_custom_fields' ) );
		
		add_filter( 'edd_purchase_form_required_fields', array( $this, 'wpcs_sp_purchase_required_checkout_fields' ) );		

		self::$api = new WP_Cloud_Server_ServerPilot_API();

	}
	
	/**
	 *  EDD Purchase Form Custom Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_purchase_form_custom_fields() {
		
		// Retrieve the Cart Contents
		$cart = edd_get_cart_contents();

		$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $cart[0]['id'], '_wpcs_cloud_hosting_enabled', true );
		
		// If Cloud Hosting is not enabled then lets exit now
		if ( $wpcs_cloud_hosting_enabled == false ) {
			return;
		}

		$droplet_data = 'no data';

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

		$templates_array = $module_data[ $server_name['custom_field1'][0] ]['templates'];

		// Retrieve the correct template data
		if ( is_array( $templates_array ) ) {
			foreach ( $templates_array as $server ) {
				if ( $server_name['custom_field2'][0] == $server['name'] ) {
				
					if ( ! array_key_exists('slug', $server) ) {
    						$server['slug'] = sanitize_title( $server['name'] );
					}
			
					$droplet_data = array(
						"name"			=>	$server['name'],
						"slug"			=>	$server['slug'],					
    					"region"		=>	$server['region'],
						"size"			=>	$server['size'],
						"image"			=> 	$server['image'],
						"module"		=>  $server['module'],
					);
					
				}
			}
		}
		
		if ( 'ServerPilot' == $plan_module ) {
		?>
			<legend class="website-details"><?php _e( 'Website Details', 'wp-cloud-server' ); ?></legend>
			<p>
				<label class="edd-label" for="edd_site_label"><?php _e( 'Site Label', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_site_label" id="edd_site_label" placeholder="<?php esc_attr_e( 'site-label', 'wp-cloud-server' ); ?>" value=""/>
			</p>
        	<p>
				<label class="edd-label" for="edd_domain_name"><?php _e( 'Domain Name', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
            	<input class="edd-input required" type="text" name="edd_domain_name" id="edd_domain_name" placeholder="<?php esc_attr_e( 'Domain Name', 'wp-cloud-server' ); ?>" value=""/>
			</p>
			<?php
			if ( ( is_array( $droplet_data ) ) && ( 'userselected' == $droplet_data['region'] ) ) {
				
				// Read in the regions array dependent on the Cloud Provider
				$regions = call_user_func( "wpcs_{$droplet_data['module']}_cloud_regions" );
?>
				<p>
					<label class="edd-label" for="edd_server_location"><?php _e( 'Website Location', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
					<select name="edd_server_location" id="edd_server_location">
						
						<?php foreach ( $regions as $slug => $region ) { ?>
								<option value="<?php echo $slug; ?>"><?php echo $region; ?></option>
						<?php } ?>
						
					</select>
				</p>
			<?php } ?>
			<?php
			// Displays location dropdown for ServerPilot Shared App Servers
			$shared_servers = get_option( 'wpcs_app_server_list' );
			if ( 'server-selected-by-region' == $server_name['custom_field2'][0] )  {
				if ( count( $shared_servers ) != 1 )  {
				?>
					<p>
						<label class="edd-label" for="edd_server_location"><?php _e( 'Website Location', 'wp-cloud-server' ); ?><span class="edd-required-indicator">*</span></label>
							<select name="edd_server_location" id="edd_server_location">
							<?php
							foreach ( $shared_servers as $location => $shared_server ) {
							?>
								<option value="<?php echo $shared_servers[$location][0]['location_name']; ?>"><?php echo $shared_servers[$location][0]['location_name']; ?></option>
							<?php } ?>
							</select>
					</p>
				<?php } else { 
					$location = array_keys($shared_servers);
				?>
					<input type="hidden" id="edd_server_location" name="edd_server_location" value="<?php echo $location[0]; ?>">

				<?php } ?>
			<?php } ?>
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
        <?php
		}

	}
	
	/**
	 *  EDD Purchase Form Required Fields
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_purchase_required_checkout_fields( $required_fields ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'ServerPilot' == $plan_module ) {
			
			$required_fields['edd_site_label'] = array(
				'error_id' => 'invalid_site_label',
				'error_message' => 'Please enter a site label',
			);
			
			$required_fields['edd_domain_name'] = array(
				'error_id' => 'invalid_domain_name',
				'error_message' => 'Please enter a domain name',
			);
			
			$required_fields['edd_user_name'] = array(
				'error_id' => 'invalid_user_name',
				'error_message' => 'Please enter a user name',
			);
	
			$required_fields['edd_user_password'] = array(
				'error_id' => 'invalid_password',
				'error_message' => 'Please enter a password',
			);
			
			$required_fields['edd_user_confirm_password'] = array(
				'error_id' => 'invalid_confirm_password',
				'error_message' => 'Please repeat your password',
			);
		}

		return $required_fields;           
        
    }

    /**
	 *  EDD Purchase Form Error Checks
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_purchase_form_error_checks( $valid_data, $data ) {
		
		// Retrieve the module for the plan in the checkout cart
		$plan_module = WP_Cloud_Server_Cart_EDD::wpcs_edd_get_plan_module();
		
		if ( 'ServerPilot' == $plan_module ) {
			if ( !isset( $data['edd_site_label'] ) || $data['edd_site_label'] == '' ) {
            	// check for a valid site label
            	edd_set_error( 'invalid_site_label', __( 'You must provide a valid site label.', 'wp-cloud-server' ) );
        	}
			
        	if ( !isset( $data['edd_domain_name'] ) || $data['edd_domain_name'] == '' ) {
            	// check for a valid domain name
            	edd_set_error( 'invalid_domain_name', __( 'You must provide a valid domain name.', 'wp-cloud-server' ) );
        	}

        	if ( !isset( $data['edd_user_name'] ) || $data['edd_user_name'] == '' ) {
            	// check for a valid user name
            	edd_set_error( 'invalid_user_name', __( 'You must provide a valid user name.', 'wp-cloud-server' ) );
        	}
			
        	if ( !isset( $data['edd_user_password'] ) || $data['edd_user_password'] == '' ) {
            	// check for a valid password
            	edd_set_error( 'invalid_password', __( 'You must provide a valid password.', 'wp-cloud-server' ) );
        	}
			
			if ( !isset( $data['edd_user_confirm_password'] ) || $data['edd_user_confirm_password'] == '' ) {
            	// check for a valid password confirmation
            	edd_set_error( 'invalid_confirm_password', __( 'You must provide a valid password.', 'wp-cloud-server' ) );
        	}
			
		}
        
    }

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_purchase_client_details_list( $payment_meta, $user_info ) {

        $site_label		= isset( $payment_meta['site_label'] ) ? sanitize_text_field( $payment_meta['site_label'] ) : 'none';
        $domain_name	= isset( $payment_meta['domain_name'] ) ? sanitize_text_field( $payment_meta['domain_name'] ) : 'none';
		$user_name		= isset( $payment_meta['user_name'] ) ? sanitize_text_field( $payment_meta['user_name'] ) : 'none';
		$user_password	= isset( $payment_meta['user_password'] ) ? sanitize_text_field( $payment_meta['user_password'] ) : 'none';
        ?>
        <li><?php echo __( 'Site Label:', 'wp-cloud-server' ) . ' ' . $site_label; ?></li>
        <li><?php echo __( 'Domain Name:', 'wp-cloud-server' ) . ' ' . $domain_name; ?></li>
		<li><?php echo __( 'Username:', 'wp-cloud-server' ) . ' ' . $user_name; ?></li>
		<li><?php echo __( 'User Password:', 'wp-cloud-server' ) . ' ' . $user_password; ?></li>
		<?php
		     
	}

    /**
	 *  EDD Purchase Client Details List
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_store_custom_fields( $payment_meta ) {

		if ( 0 !== did_action('edd_pre_process_purchase') ) {
			$payment_meta['site_label']			= isset( $_POST['edd_site_label'] ) ? sanitize_text_field( $_POST['edd_site_label'] ) : '';
			$payment_meta['domain_name']		= isset( $_POST['edd_domain_name'] ) ? sanitize_text_field( $_POST['edd_domain_name'] ) : '';
			$payment_meta['server_location']	= isset( $_POST['edd_server_location'] ) ? sanitize_text_field( $_POST['edd_server_location'] ) : '';
			$payment_meta['user_name']			= isset( $_POST['edd_user_name'] ) ? sanitize_text_field( $_POST['edd_user_name'] ) : '';
			$payment_meta['user_password']		= isset( $_POST['edd_user_password'] ) ? sanitize_text_field( $_POST['edd_user_password'] ) : '';
		}

		return $payment_meta;
	}
	
	/**
	 *  EDD Email Tags
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_add_email_tag() {

		edd_add_email_tag( 'site_label', 'New website site label', array($this, 'wpcs_serverpilot_edd_email_tag_site_label') );
		edd_add_email_tag( 'domain_name', 'New website domain name', array($this, 'wpcs_serverpilot_edd_email_tag_domain_name') );
		edd_add_email_tag( 'server_location', 'New website server location', array($this, 'wpcs_serverpilot_edd_email_tag_server_location') );
		edd_add_email_tag( 'user_name', 'New website user name', array($this, 'wpcs_serverpilot_edd_email_tag_user_name') );
		edd_add_email_tag( 'user_password', 'New website user password', array($this, 'wpcs_serverpilot_edd_email_tag_user_password') );
	}
	
    /**
	 *  EDD Email Tag - Site Label
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_email_tag_site_label( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['site_label'];
	}

    /**
	 *  EDD Email Tag - Domain Name
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_email_tag_domain_name( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['domain_name'];
	}

    /**
	 *  EDD Email Tag - Server Location
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_email_tag_server_location( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['server_location'];
	}

    /**
	 *  EDD Email Tag - User Name
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_email_tag_user_name( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['user_name'];
	}

    /**
	 *  EDD Email Tag - User Password
	 *
	 *  @since 1.2.1
	 */
	public function wpcs_serverpilot_edd_email_tag_user_password( $payment_id ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		return $payment_data['user_password'];
	}

	/**
	 *  EDD Purchase Complete Create ServerPilot Service
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_purchase_complete_create_service( $module_name, $data ) {
		
		// Create instance of the DigitalOcean API
		$digitalocean_api = new WP_Cloud_Server_DigitalOcean_API();
		
		$sp_debug[]=1;
		$sp_debug[]=$module_name;
		$sp_debug[]=$data;

		if ( 'ServerPilot' == $module_name ) {

			// Retrieve the user entered account details
			$domain_name 		= wpcs_sanitize_domain_strip_http( $data['domain_name'] );
			$plan_name 			= $data['plan_name'];
			$user_name 			= $data['user_name'];
			$user_pass 			= $data['user_password'];
			$user_email 		= $data['user_email'];
			$user_id			= $data['user_id'];
			$server_name		= $data['server_name'];
			$server_location	= $data['server_location'];
			$site_label			= $data['site_label'];
			
			$sp_debug[]=2;
			$sp_debug[]=$server_location;
			$sp_debug[]=$server_name;
			
			// Retrieve the Active Module List
			$module_data		= get_option( 'wpcs_module_list' );
			$check_servers		= ( array_key_exists( 'servers', $module_data[$module_name] ) && ( is_array($module_data[$module_name]['servers'] ) ) );
			$check_templates	= ( array_key_exists( 'templates', $module_data[$module_name] ) && ( is_array($module_data[$module_name]['templates'] ) ) );
			
			if ( $check_servers && ! $check_templates ) {
				$servers		= $module_data[ $module_name ]['servers'];
			}
			
			if ( $check_templates && ! $check_servers ) {
				$servers		= $module_data[ $module_name ]['templates'];
			}
			
			if ( $check_servers && $check_templates ) {
				$templates		= $module_data[ $module_name ]['templates'];
				$servers		= $module_data[ $module_name ]['servers'];
				$servers		= array_merge( $templates, $servers );
			}
			
			// Retrieve the Shared Server List
			$shared_servers = get_option( 'wpcs_app_server_list' );
						
			// If shared server location is user selected then determine server name
			if ( 'server-selected-by-region' == $server_name ) {
				foreach ( $shared_servers[$server_location] as $shared_server ) {
					if ( $server_location == $shared_server['location_name'] ) {
						$server_name = $shared_server['name'];
					}
				}
			}
			
			$sp_debug[]=3;
			$sp_debug[]=$server_name;
			$sp_debug[]=4;
			$sp_debug[]=$servers;
			
			update_option('test_framework_serverpilot_edd', $sp_debug );
			
			update_option( 'wpcs_serverpilot_edd_servers', $servers );
			
			// Retrieve the correct server data for the application 
			foreach ( $servers as $key => $server ) {
				
				if ( $server_name == $server['name'] ) {

					$server_autossl			= $server['autossl'];
					$server_monitor_enabled	= $server['monitor_enabled'];
					
					if ( ( array_key_exists( 'template_name', $server ) ) && ( 'serverpilot_template' == $server['template_name'] ) ) {
						
						// Because this uses a template, the server_name will be the site_label
						$vps_server_name	= $site_label;
						$server_module		= strtolower( str_replace( " ", "_", $server['module'] ) );
						$server_image		= $server['image'];
						$server_size		= $server['size'];
						$server_backups		= ( isset( $server['backups'] ) && $server['backups'] ) ? true : false;
						$server_custom		= ( isset( $server['custom_settings'] ) ) ? $server['custom_settings'] : null;
						
						// The Region could be user selectable
						if ( 'userselected' == $server['region'] ) {
							$server['region'] = $server_location;
						} 
						
						// Need to check the availability of the region and select available data center
						$api_response 		= call_user_func("wpcs_{$server_module}_cloud_server_api", $server, 'regions', null, false, 900, 'GET', false, 'check_data_centers' );
						
						$server_region		= ( isset( $api_response['region'] ) ) ? $api_response['region'] : $server['region'];
						
						// Inform ServerPilot API that a New Server is being connected
						$data				= array( 'name' => $vps_server_name );
						$sp_response		= self::$api->call_api( 'servers', $data, false, 900, 'POST', false, 'connect_server' );
						$server_id			= $sp_response['data']['id'];
						$server_apikey		= $sp_response['data']['apikey'];
						$action_id			= $sp_response['actionid'];
						
						// Retrieve data for embedding in install script
						$api_account_id 	= get_option( 'wpcs_sp_api_account_id' );
						$api_key			= get_option( 'wpcs_sp_api_key' );
						$sys_user_password	= wp_generate_password( 10, false, false );
						$ssh_key			= $server['ssh_key'];
						$ssh_key			= ( ! empty( $ssh_key ) ) ? $ssh_key : 'no_ssh_key';
						
						$server_script = <<<EOF
#!/bin/bash
export MODULE=$server_module
export SERVERID=$server_id
export SERVERAPIKEY=$server_apikey
export CLIENTID=$api_account_id
export APIKEY=$api_key
export ACTIONID=$action_id
export SYSUSERPWD=$sys_user_password
export SSHKEY='$ssh_key'
export DEBIAN_FRONTEND=noninteractive
UPGRADE_ATTEMPT_COUNT=100
UPGRADE_STATE=1
for i in `seq 1 \$UPGRADE_ATTEMPT_COUNT`;
do
    if [ "\$UPGRADE_STATE" -eq "1" ]; then
        apt-get -y update
        if [ "`echo $?`" -eq "0" ]; then
            echo "package list updated."
            UPGRADE_STATE=2;
        fi
    fi
	if [ "\$UPGRADE_STATE" -eq "2" ]; then
        break
    fi

    sleep 5
done
if [ "\$MODULE" = "digitalocean" ]
then
sudo sed -i 's/^root:.*$/root:*:16231:0:99999:7:::/' /etc/shadow
fi
sudo apt-get -y install wget ca-certificates curl jq util-linux && \
sudo wget -nv -O serverpilot-installer https://download.serverpilot.io/serverpilot-installer && \
sudo sh serverpilot-installer \
--server-id=\$SERVERID \
--server-apikey=\$SERVERAPIKEY
APIRESPONSE=$( curl https://api.serverpilot.io/v1/actions/\$ACTIONID -u \$CLIENTID:\$APIKEY |  jq -r '.data.status' )
logger \$APIRESPONSE
while [ \$APIRESPONSE != 'success' ]
do
APIRESPONSE=$( curl https://api.serverpilot.io/v1/actions/\$ACTIONID -u \$CLIENTID:\$APIKEY |  jq -r '.data.status' )
logger \$APIRESPONSE
done
logger \$APIRESPONSE
RESPONSE=$( curl https://api.serverpilot.io/v1/sysusers \
   -u \$CLIENTID:\$APIKEY \
   -H "Content-Type: application/json" \
   -d "{\"serverid\": \"\${SERVERID}\", \"name\": \"wpcssysuser\", \"password\": \"\${SYSUSERPWD}\"}" |  jq -r '.actionid' )
APIRESPONSE=$( curl https://api.serverpilot.io/v1/actions/\$RESPONSE -u \$CLIENTID:\$APIKEY |  jq -r '.data.status' )
logger \$APIRESPONSE
while [ \$APIRESPONSE != 'success' ]
do
APIRESPONSE=$( curl https://api.serverpilot.io/v1/actions/\$RESPONSE -u \$CLIENTID:\$APIKEY |  jq -r '.data.status' )
logger \$APIRESPONSE
done
if [ "\${SSHKEY}" != 'no_ssh_key' ]
then
(umask 077 && echo "\${SSHKEY}" >> /srv/users/wpcssysuser/.ssh/authorized_keys)
fi
EOF;

						// Set-up the data for the new Droplet
						$app_data = array(
							"name"			=>	$vps_server_name,
							"region"		=>	$server_region,
							"size"			=>	$server_size,
							"image"			=> 	$server_image,
							"backups"		=>	$server_backups,
							"user_data"		=>  $server_script,
						);
						
						// Read in any Custom Settings from Template
						if ( isset( $server_custom ) ) {
							$app_data['custom_settings']			= $server_custom;
							$app_data['custom_settings']['DCID']	= $server_region;
							$app_data['custom_settings']['label']	= $vps_server_name;
							
							$random_number = wp_rand( 1000, 9000 );
							$app_data['custom_settings']['script_name'] = "serverpilot-installer-{$random_number}";
						}
						
						update_option( 'wpcs_serverpilot_app_data', $app_data );

						// Send the API POST request to create the new server
						$response =  call_user_func("wpcs_{$server_module}_cloud_server_api", null, 'droplets', $app_data, false, 0, 'POST', true, 'site_creation' );

						// Log the creation of the new server
						wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'New ServerPilot Server Created (' . $vps_server_name . ')' );
						
						// Add the new server details to the activation queue parameters
						$activation_queue[ $vps_server_name ] = array( 	'action_id' 		=> 	$action_id,
												   	 					'server_id' 		=> 	$server_id,
																	  	'module_name' 		=> 	$module_name,
																	  	'plan_name' 		=> 	$plan_name,
																  		'domain_name' 		=> 	$domain_name,
																		'user_name' 		=> 	$user_name,
																		'user_pass' 		=> 	$user_pass,
																		'user_email' 		=> 	$user_email,
																	  	'user_id'			=>	$user_id,
																		'server_name'		=> 	$vps_server_name,
																		'site_label' 		=> 	$site_label,
																	  	'autossl'			=>	$server_autossl,
																	  	'monitor_enabled'	=> 	$server_monitor_enabled,
																 );
						
						// Send new serverpilot server to the activation queue to wait for install to complete
						update_option( 'wpcs_sp_api_site_creation_queue', $activation_queue );
						
					} else {
					
						// Start of the Create New App Function
						
						$sysuserid = null;
						
						$server_autossl			= $server['autossl'];
						$server_monitor_enabled	= $server['monitor_enabled'];
					
						$new_server = self::$api->call_api( 'servers', null, false, 900, 'GET', false, 'server_info' );
						$servers = $new_server['data'];
						update_option( 'new_server', $servers );
						
						foreach ( $servers as $server ) {
				
							if ( $server_name == $server['name'] ) {
							
								$server_id = $server['id'];
					
								$new_sysuser = self::$api->call_api( 'sysusers', null, false, 0, 'GET', false, 'fetch_sysuser' );
								$sysusers = $new_sysuser['data'];
						
								if ( ! empty( $sysusers ) ) {
									foreach ( $sysusers as $sysuser ) {
										if ( $sysuser['serverid'] == $server_id ) {
											$sysuserid = $sysuser['id'];
										}
									}
								}
							}
						}
						
						if ( !isset( $sysuserid ) ) {
						
							// We need to create a new ServerPilot System User if none exist
							$user_data = array(
								"name"		=>	"wpcssysuser",
								"serverid"	=>	$server_id,
								"password"	=>	wp_generate_password( 10, true, false )				
							);
						
							// Send the API POST request to create 'sysuser'
							$response = self::$api->call_api( 'sysusers', $user_data, false, 0, 'POST', false, 'create_sysuser' );
							update_option( 'wpcs_sp_api_sysuser_creation', $response );
						
							// Log the creation of the new ServerPilot Sys User
							wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'New System User Created (' . $user_data['name'] . ')' );
						
							$sysuserid = $response['data']['id'];
						}

						// Obtain Key for desired PHP Runtime Version (ServerPilot occasionally changes the availability!)
						if ( is_array( $server['available_runtimes'] ) ) {
							$key = ( count( $server['available_runtimes'] ) - 1 );
							$php_version = $server['available_runtimes'][ $key ];
						} else {
							$php_version = 'php7.4';
						}
					
						// Populate Information for creating the new app. This is required by the ServerPilot API
						$app_data = array(
							"name"		=>	$site_label,
    						"sysuserid" =>	$sysuserid,
							"runtime"	=>	$php_version,
							"domains"	=> 	array( $domain_name, "www." . $domain_name ),
							"wordpress"	=>	array(
												"site_title"		=>	"My WordPress Site",
												"admin_user"		=>	$user_name,
												"admin_password"	=>	$user_pass,
												"admin_email"		=>	$user_email,								
											)
    					);
			
						// Send the API POST request to create the new 'app'
						$response = self::$api->call_api( 'apps', $app_data, false, 0, 'POST', true, 'site_creation' );
			
						// Update Log with new website creation
						$api_data = get_option( 'wpcs_sp_api_last_response' );
						if ( ! $response || $response['response']['code'] !== 200 ) {
							$status		= 'Failed';
							$error		= $api_data['site_creation']['data']['error']['message'];
							$message 	= 'An Error Occurred (' . $error . ')';
						} else {
							$status 	= 'Success';
							$message 	= 'New Website Created (' . $domain_name . ')';
							$app_id 	= $api_data['site_creation']['data']['data']['id'];
				
							// Enable SSL if AutoSSL Enabled
							if ( '1' == $server_autossl ) {
								wpcs_sp_api_enable_ssl( self::$api, $app_id, $domain_name );
							}
							
							$data = array(
								"plan_name"			=>	$plan_name,
								"app_id"			=>	$app_id,
								"module"			=>	$module_name,
								"host_name"			=>	'',
								"host_name_domain"	=>	'',
								"fqdn"				=>	'',
								"protocol"			=>	'',
								"port"				=>	'',
								"server_name"		=>	$site_label,
								"region_name"		=>	'',
								"size_name"			=>	'',
								"image_name"		=> 	'',
								"ssh_key_name"		=> 	'',
								"user_data"			=>	'',
								"ip_address"		=>	$server['lastaddress'],
								"php_version"		=>	$php_version,
								"autossl"			=> 	$server_autossl,
								"monitor_enabled"	=> 	$server_monitor_enabled,
								"domain_name"		=>	$domain_name,
								"site_label"		=>	$site_label,
								"user_name"			=>	$user_name,
							);
							
							// End of provider specific function
							
							$get_user_meta		= get_user_meta( $user_id );
							
							$data['user_id']	= $user_id;
							$data['nickname']	= $get_user_meta['nickname'][0];
							$data['first_name']	= $get_user_meta['first_name'][0];
							$data['last_name']	= $get_user_meta['last_name'][0];
							$data['full_name']	= "{$get_user_meta['first_name'][0]} {$get_user_meta['last_name'][0]}";
							
							// Save Server Data for display in control panel
							$client_data		= get_option( 'wpcs_cloud_server_client_info' );
							$client_data		= ( is_array( $client_data ) ) ? $client_data : array();
							$client_data[$module_name][]	= $data;
							update_option( 'wpcs_cloud_server_client_info', $client_data );
				
							// Reset the dismissed site creation option and set new site created option
							update_option( 'wpcs_dismissed_sp_site_creation_notice', FALSE );
							update_option( 'wpcs_sp_new_site_created', TRUE );
				
							// Executes after the create service functionality
							do_action( 'wpcs_after_serverpilot_site_completion', $data );
						}
				
						wpcs_serverpilot_log_event( 'ServerPilot', $status, $message );
			
						$debug['app_data'] = $app_data;
						//update_option( 'sp_debug', $sp_debug );
						update_option( 'wpcs_sp_new_site_data', $debug );
			
						// End of the Create New App Function
					}
				}
			}
		} 
	} 
}