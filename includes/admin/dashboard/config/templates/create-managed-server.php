<?php
/**
 * Provide a Admin Area Create Server Page
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce			= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$module_name	= 'ServerPilot';

$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
$attributes		= ( $api_status ) ? '' : 'disabled';

	// Local Variables
	$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
	$sp_response	= '';
	$server_script	= null;

	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_serverpilot_create_server' );
			wpcs_do_settings_sections( 'wpcs_serverpilot_create_server' );
			?>
        			<h2>ServerPilot Settings</h2>
        			<div>
						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row">ServerPilot Plan:</th>
									<td>
										<select class="w-400" name="wpcs_serverpilot_server_plan" id="wpcs_serverpilot_server_plan">
											<optgroup label="Select ServerPilot Plan">
            									<option value="economy"><?php esc_html_e( 'Economy ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
            									<option value="business"><?php esc_html_e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
												<option value="first_class"><?php esc_html_e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
											</optgroup>
										</select>
									</td>
								</tr>
								<tr>
									<th scope="row">Use for Shared Hosting:</th>
									<td>
										<input type='checkbox' id='wpcs_serverpilot_server_shared_hosting' name='wpcs_serverpilot_server_shared_hosting' value='1'>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			<?php
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_serverpilot_server_name' ) ) {
		
		global $wp_settings_errors;
		
		// Retrieve the Active Module List
		$module_data = get_option( 'wpcs_module_list' );

		// Capture the DigitalOcean Settings
		$server_type	        			= get_option( 'wpcs_serverpilot_server_type' );
		$server_name	       				= get_option( 'wpcs_serverpilot_server_name' );
		$server_cloud_provider	       		= get_option( 'wpcs_serverpilot_server_module' );
		$server_region	        			= get_option( 'wpcs_serverpilot_server_region' );
		$server_size	        			= get_option( 'wpcs_serverpilot_server_size' );
		$server_backups	        			= get_option( 'wpcs_serverpilot_server_enable_backups' );
		
		// Capture the ServerPilot Settings
		$serverpilot_autossl 				= get_option( 'wpcs_serverpilot_server_autossl' );
		$serverpilot_plan 	    			= get_option( 'wpcs_serverpilot_server_plan' );
		$serverpilot_shared_hosting 		= get_option( 'wpcs_serverpilot_server_shared_hosting' );
		$serverpilot_ssh_key_name	    	= get_option( 'wpcs_serverpilot_server_ssh_key' );
		
		// Determine the ServerPilot SSH Key
		$serverpilot_ssh_keys				= get_option( 'wpcs_serverpilots_ssh_keys' );
		$serverpilot_ssh_public_key			= ( ! empty( $serverpilot_ssh_keys[$serverpilot_ssh_key_name]['public_key'] ) ) ? $serverpilot_ssh_keys[$serverpilot_ssh_key_name]['public_key'] : 'no_ssh_key';
		
		// Extract the Setting Values
		$server_size_explode				= explode( '|', $server_size );
		$server_size_name					= $server_size_explode[0];
		$server_size						= $server_size_explode[1];
		
		$server_region_explode				= explode( '|', $server_region );
		$server_region_name					= $server_region_explode[0];
		$server_region						= $server_region_explode[1];

		$server_type_explode				= explode( '|', $server_type );
		$server_type_name					= $server_type_explode[0];
		$server_type						= $server_type_explode[1];
		
		$server_enable_backups				= ( $server_backups ) ? true : false;
		
		$server_module						= strtolower( str_replace( " ", "_", $server_cloud_provider ) );
		
		// Need to Retrieve the Server Image
		$server_image						= call_user_func("wpcs_{$server_module}_os_list", $server_type_name );

		// Create Server Data Array
		$server = array(
				"name"						=>	$server_name,
				"slug"						=>	sanitize_title( $server_name ),
				"region"					=>	$server_region_name,
				"size"						=>	$server_size_name,
				"image"						=> 	$server_image,
				"module"					=>	$module_name,
				//"ssh_key"					=>	$server_ssh_key_name,
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

		$serverpilot_api	= new WP_Cloud_Server_ServerPilot_API();
		$data				= array( 'name' => $server_name, 'plan' => $serverpilot_plan );
		$sp_response		= $serverpilot_api->call_api( 'servers', $data, false, 900, 'POST', false, 'connect_server' );
			
			$server_id			= $sp_response['data']['id'];
			$server_apikey		= $sp_response['data']['apikey'];
			$action_id			= $sp_response['actionid'];
		
			$api_account_id		= get_option( 'wpcs_sp_api_account_id' );
			$api_key			= get_option( 'wpcs_sp_api_key' );
			$sys_user_password 	= wp_generate_password( 10, false, false );
			$ssh_key			= $serverpilot_ssh_public_key;
			$server_script		= <<<EOF
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
apt-get -y install wget ca-certificates curl jq util-linux && \
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

			// Add to the Server Data for Shared Hosting Settings
			if ( $serverpilot_shared_hosting == "1" ) {
		
				// Retrieve the Shared Hosting Server List
				$shared_server_data = get_option( 'wpcs_app_server_list', array() );

				$server_data = $server;
			
				// Save the New Server Details
				$server_data["autossl"]			=	$serverpilot_autossl;
				$server_data["monitor_enabled"]	=	false;
				$server_data["location_name"]	=	$server_region_name;
				$server_data["location_slug"]	=	sanitize_title($server_region_name);
				$server_data["hosting_type"]	=	'Shared';		
				$server_data["max_sites"]		=	1000;
				$server_data["site_counter"]	=	0;
				
				$shared_server_data[ $server_region_name ][] = $server_data;

				// Update the App Server List
				update_option( 'wpcs_app_server_list', $shared_server_data );
				
				// Add the New Server Data to the Module List Array
				$module_data[ 'ServerPilot' ][ 'servers' ][] = $server_data;
			
			}

		// Set-up the data for the new Droplet
		$app_data = array(
			"name"			=>	$server_name,
			"region"		=>	$server_region,
			"size"			=>	$server_size,
			"image"			=> 	$server_image,
			//"ssh_keys"		=>	$server_ssh_key_id,
			"user_data"		=>  $server_script,
			"backups"		=>	$server_enable_backups,
		);
						
		$random_number = wp_rand( 1000, 9000 );
						
		// Read in any Custom Settings from Template
		if ( isset( $server_size ) ) {

			$app_data['custom_settings']['OSID']		= $server_image;
			$app_data['custom_settings']['DCID']		= $server_region;
			$app_data['custom_settings']['VPSPLANID']	= $server_size;			
			$app_data['custom_settings']['label']		= $server['name'];
			$app_data['custom_settings']['script_name'] = "serverpilot-installer-{$random_number}";

			$debug['app_data']	= $app_data;
		}

		// Send the API POST request to create the new server
		$response	 =  call_user_func("wpcs_{$server_module}_cloud_server_api", null, 'droplets', $app_data, false, 0, 'POST', false, 'site_creation' );
		
		$debug['droplet_response']	= $response;
		
		update_option( 'wpcs_manage_serverpilot_server_debug', $debug );
		
		// Wait for Server Creation to Complete and read back data
		// $server		 =  call_user_func("wpcs_{$server_module}_server_complete", $response );

		// Update Module List Option with New Module List
		update_option( 'wpcs_module_list', $module_data );

		// Log the creation of the new DigitalOcean Droplet
		call_user_func("wpcs_{$server_module}_log_event", $module_name, 'Success', 'New Server Connected to ServerPilot ( ' . $server_name . ' )' );

		call_user_func("wpcs_serverpilot_log_event", 'ServerPilot', 'Success', 'New Server Connected (' . $server_name . ')' );
			
		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_serverpilot_server_autossl' );
		delete_option( 'wpcs_serverpilot_server_plan' );
		delete_option( 'wpcs_serverpilot_server_shared_hosting' );
		delete_option( 'wpcs_serverpilot_server_module' );
		delete_option( 'wpcs_serverpilot_server_type' );
		delete_option( 'wpcs_serverpilot_server_name' );	
		delete_option( 'wpcs_serverpilot_server_region' );
		delete_option( 'wpcs_serverpilot_server_size' );
		delete_option( 'wpcs_serverpilot_server_ssh_key' );

	}