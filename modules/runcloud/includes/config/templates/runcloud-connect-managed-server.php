<?php
/**
 * Create Servers for connection to RunCloud
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_runcloud_connect_managed_server_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-connect-managed-server' !== $tabs_content ) {
		return;
	}

	$nonce	= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

	// Local Variables
	$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
	$sp_response	= '';
	$server_script	= '';

	// Check API Status
	$api_status		= wpcs_check_cloud_provider_api('RunCloud');
	$attributes		= ( $api_status ) ? '' : 'disabled';

	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_serverpilot_create_server' );
			wpcs_do_settings_sections( 'wpcs_serverpilot_create_server' );
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	$server_name = get_option( 'wpcs_serverpilot_server_name' );

	if ( '' !== $server_name ) {
		
		global $wp_settings_errors;

		// Capture the DigitalOcean Settings
		$server_type	        	= get_option( 'wpcs_serverpilot_server_type' );
		$server_cloud_provider	    = get_option( 'wpcs_serverpilot_server_module' );
		$server_region	        	= get_option( 'wpcs_serverpilot_server_region' );
    	$server_size	        	= get_option( 'wpcs_serverpilot_server_size' );
		$server_ssh_key	        	= get_option( 'wpcs_serverpilot_server_ssh_key' );
		$server_backups	    		= get_option( 'wpcs_serverpilot_server_enable_backups' );
		
		$server_portal				= "RunCloud";
    
    	// Set Module Name
    	$module_name                = $server_cloud_provider;
		
		// Set Enable Backup Setting
		$server_enable_backups		= ( $server_backups ) ? true : false;
				
		// Extract the Setting Values
		$server_size_explode		= explode( '|', $server_size );
		$server_size_name			= $server_size_explode[0];
		$server_size				= isset($server_size_explode[1] ) ? $server_size_explode[1] : '';
		
		$server_region_explode		= explode( '|', $server_region );
		$server_region_name			= $server_region_explode[0];
		$server_region				= isset($server_region_explode[1] ) ? $server_region_explode[1] : '';

		$server_type_explode		= explode( '|', $server_type );
		$server_type_name			= $server_type_explode[0];
		$server_type				= isset($server_type_explode[1] ) ? $server_type_explode[1] : '';
		
		$server_module				= strtolower( str_replace( " ", "_", $server_cloud_provider ) );
	
		if ( '' == $server_module ) { 
			return;	
		}
		
		// Determine the ServerPilot SSH Key
		$server_ssh_keys				= get_option( 'wpcs_serverpilots_ssh_keys' );
		$server_ssh_public_key			= ( ! empty( $serverpilot_ssh_keys[$server_ssh_key]['public_key'] ) ) ? $serverpilot_ssh_keys[$server_ssh_key]['public_key'] : 'no_ssh_key';
		
		// Check if SSH Key saved with provider
		$ssh_key_id	= call_user_func("wpcs_{$server_module}_ssh_key", $server_ssh_key, $server_region );
		
		// Need to Retrieve the Server Image
		$server_image				= call_user_func("wpcs_{$server_module}_os_list", $server_type_name );

		// Create Server Data Array
		$server = array(
			"name"						=>	$server_name,
			"slug"						=>	sanitize_title( $server_name ),
			"region"					=>	$server_region_name,
			"size"						=>	$server_size_name,
			"image"						=> 	$server_image,
			"module"					=>	$module_name,
			"ssh_key"					=>	$ssh_key_id,
			"backups"					=>	$server_enable_backups,
			"hosting_type"				=>	'dedicated',
		);
		
		// Set-up the API Data for the New Server
		$api_data = array(
			"region"					=>	$server_region,
			"size"						=>	$server_size,
		);
		
		// Check and Select the Available Region
		$api_response  = call_user_func("wpcs_{$server_module}_cloud_server_api", $api_data, 'regions', null, false, 900, 'GET', false, 'check_data_centers' );
						
    	$server_region = ( isset( $api_response['region'] ) ) ? $api_response['region'] : $server_region;
		
		if ( 'aws_lightsail' == $server_module ) {
			$ip_address = call_user_func("wpcs_{$server_module}_get_static_ip", $server_name, $server_region );
		}
		$ip_address	= ( isset( $ip_address ) && $ip_address ) ? $ip_address : 'not_required';
		
		// Set-up the ethernet port for the script (Vultr uses ens3 instead of eth0)
		$network	= ( 'vultr' == $server_module ) ? 'ens3' : 'eth0';
    
    	if ( 'RunCloud' == $server_portal ) {

			$api_key			= get_option( 'wpcs_runcloud_api_key' );
			$api_secret 		= get_option( 'wpcs_runcloud_api_secret' );
			$server_script		= <<<EOF
#!/bin/bash
export APISECRET=$api_secret
export APIKEY=$api_key
export SERVERNAME=$server_name
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

    	}

		// Set-up the data for the new Droplet
		$app_data = array(
			"name"			=>	$server_name,
			"region"		=>	$server_region,
			"size"			=>	$server_size,
			"image"			=> 	$server_image,
			"backups"		=>	$server_enable_backups,
			"user_data"		=>  $server_script,
		);
			
		// Read in any Custom Settings from Template
		if ( isset( $server_size ) ) {
			$app_data['custom_settings']['OSID']		= $server_image;
			$app_data['custom_settings']['DCID']		= $server_region;
			$app_data['custom_settings']['VPSPLANID']	= $server_size;			
			$app_data['custom_settings']['label']		= $server['name'];
			
			$random_number = wp_rand( 1000, 9000 );
			$app_data['custom_settings']['script_name'] = "runcloud-installer-{$random_number}";
		}
		
		$debug['app_data'] = $app_data;
		
		// Use SSH Key if available or generate root password
		if ( $ssh_key_id ) {
			$app_data["ssh_keys"][]						= $ssh_key_id;
			$app_data['custom_settings']['SSHKEYID']	= $ssh_key_id;
			$app_data['custom_settings']['ssh_key']		= $ssh_key_id;
		}

		// Send the API POST request to create the new server
		$response = call_user_func("wpcs_{$server_module}_cloud_server_api", null, 'droplets', $app_data, false, 0, 'POST', false, 'site_creation' );
		
		update_option( 'wpcs_manage_server_debug', $debug );
		
		if ( 'aws_lightsail' == $server_module ) {

			$server_sub_id = $response['operations'][0]['id'];
			
			$server_data = array(
        		'response' 	=> $response,
        		'static_ip' => true,
        		'open_port' => true,
			);
		
			// Wait for server to be running
			$status = call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $server_data, $server_name, $server_region );
		
		}
		
		// Retrieve the Active Module List
		$module_data = get_option( 'wpcs_module_list' );

		// Add the New Server Data to the Module List Array
		$module_data[ 'RunCloud' ][ 'servers' ][] = $server;

		// Update Module List Option with New Module List
		update_option( 'wpcs_module_list', $module_data );

		// Log the creation of the new DigitalOcean Droplet
		call_user_func("wpcs_{$server_module}_log_event", $module_name, 'Success', 'New Server Connected to RunCloud ( ' . $server_name . ' )' );
		
		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_serverpilot_server_module' );
		delete_option( 'wpcs_serverpilot_server_type' );
		delete_option( 'wpcs_serverpilot_server_name' );	
		delete_option( 'wpcs_serverpilot_server_region' );
		delete_option( 'wpcs_serverpilot_server_size' );
	}

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_connect_managed_server_template', 10, 3 );