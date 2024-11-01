<?php
/**
 * Scheduled Queues.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.1.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Activates the Site Creation Queue.
 *
 *  @since 1.1.0
 */
function wpcs_sp_module_activate_site_creation_queue() {

	// Make sure this event hasn't been scheduled
	if( !wp_next_scheduled( 'wpcs_sp_module_run_site_creation_queue' ) ) {
		// Schedule the event
		wp_schedule_event( time(), 'five_minutes', 'wpcs_sp_module_run_site_creation_queue' );
		wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'Site Creation Queue Started' );
	}

}
add_action( 'wpcs_serverpilot_module_activate', 'wpcs_sp_module_activate_site_creation_queue' );
	
/**
 *  Run the Site Creation Queue.
 *
 *  @since 1.1.0
 */
function wpcs_sp_module_run_site_creation_queue() {
		
	// Create instance of the DigitalOcean API
	$digitalocean_api = new WP_Cloud_Server_DigitalOcean_API();

	$api = new WP_Cloud_Server_ServerPilot_API();
		
	$site_creation_queue = get_option( 'wpcs_sp_api_site_creation_queue' );
		
	if ( ! empty( $site_creation_queue ) ) {

		foreach ( $site_creation_queue as $site ) {
				
			$data			= $site;
			$action_id 		= $site['action_id'];
			$server_id 		= $site['server_id'];
			$module_name 	= $site['module_name'];
			$plan_name 		= $site['plan_name'];
			$domain_name 	= $site['domain_name'];
			$user_name 		= $site['user_name'];
			$user_pass 		= $site['user_pass'];
			$user_email 	= $site['user_email'];
			$user_id		= $site['user_id'];
			$server_name 	= $site['server_name'];
			$site_label 	= $site['site_label'];
			$server_autossl = $site['autossl'];
				
				
			// Retrieve Status of the Server Install Action
			$sp_action_response = $api->call_api( 'actions/' . $action_id, null, false, 900, 'GET', false, 'check_server' );
			$status = (isset($sp_action_response['data']['status'])) ? $sp_action_response['data']['status'] : false;
				
			$debug['action_response'] = $status;
				
			if ( 'success' == $status ) {
					
				// Start of the Create New App Functionality
					
				// Get the New Server Information
				$new_server = $api->call_api( 'servers', null, false, 900, 'GET', false, 'server_info', $server_id );
				$server = $new_server['data'];
					
				// Get the Sys User Information
				$new_sysuser = $api->call_api( 'sysusers', null, false, 0, 'GET', false, 'fetch_sysuser' );
				$sysusers = $new_sysuser['data'];
					
				// Test if Sys User exists for New Server
				if ( ! empty( $sysusers ) ) {
					foreach ( $sysusers as $sysuser ) {
						if ( $sysuser['serverid'] == $server_id ) {
								$sysuserid = $sysuser['id'];
						}
					}
				}
						
				if ( ! isset( $sysuserid ) ) {
						
					// We need to create a new ServerPilot System User
					$user_data = array(
						"name"		=>	"wpcssysuser",
						"serverid"	=>	$server['id'],
						"password"	=>	wp_generate_password( 10, true, false )				
					);
						
					// Send the API POST request to create 'sysuser'
					$response = $api->call_api( 'sysusers', $user_data, false, 0, 'POST', false, 'create_sysuser' );
					update_option( 'wpcs_sp_api_sysuser_creation', $response );
						
					// Log the creation of the new ServerPilot Sys User
					wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'New System User Created (' . $user_data['name'] . ')' );
						
					$sysuserid = $response['data']['id'];
					
				} // end of Create Sys User

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
						"admin_user"		=>	"admin",
						"admin_password"	=>	$user_pass,
						"admin_email"		=>	$user_email,								
					)
    			);
					
				update_option( 'wpcs_sp_api_app_data', $app_data );
			
				// Send the API POST request to create the new 'app'
				$response	= $api->call_api( 'apps', $app_data, false, 0, 'POST', true, 'serverpilot_app_creation' );
				//$action_id	= $response['actionid'];
			
				// Update Log with new website creation
				$api_data = get_option( 'wpcs_sp_api_last_response' );
				if ( ! $response || $response['response']['code'] !== 200 ) {
					$status		= 'Failed';
					$error	 	= $api_data['serverpilot_app_creation']['data']['error']['message'];
					$message 	= 'An Error Occurred Creating New Website (' . $error . ')';
				} else {
					$status	 	= 'Success';
					$message	= 'New Website Created (' . $domain_name . ')';
					$app  		= $api_data['serverpilot_app_creation']['data']['data'];
				
					// Enable SSL if AutoSSL Enabled
					if ( '1' == $server_autossl ) {
						wpcs_sp_api_enable_ssl( $api, $app['id'], $domain_name );
					}

					$data = array(
						"plan_name"			=>	$plan_name,
						"app_id"			=>	$app['id'],
						"app_name"			=>	$app['name'],
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
						"domain_name"		=>  $domain_name,
						"ip_address"		=>	$server['lastaddress'],
						"php_version"		=>	$php_version,
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
					
					// Remove the Website from the Activation Queue
					unset( $site_creation_queue[ $server_name ] );
					update_option( 'wpcs_sp_api_site_creation_queue', $site_creation_queue );
				
					// Reset the dismissed site creation option and set new site created option
					update_option( 'wpcs_dismissed_sp_site_creation_notice', FALSE );
					update_option( 'wpcs_sp_new_site_created', TRUE );
						
					// Executes after the create service functionality
					do_action( 'wpcs_after_serverpilot_site_completion', $data );
				}
				
				wpcs_serverpilot_log_event( 'ServerPilot', $status, $message );

				$debug['app_data'] = $app_data;
				update_option( 'wpcs_sp_create_site_queue', $debug );
			
				// End of the Create New App Function
					
			}
					
		} // end of site_creation foreach loop
			
		wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'Site Creation Queue Completed' );
			
	} // end of site_creation if statement		
}
add_action( 'wpcs_sp_module_run_site_creation_queue', 'wpcs_sp_module_run_site_creation_queue' );