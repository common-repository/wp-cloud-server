<?php

/**
 * Provide a Admin Area Add Template Page for the Digitalocean Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$module_name	=  WP_Cloud_Server_ServerPilot_Settings::wpcs_serverpilot_module_name();
$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
$attributes		= ( $api_status ) ? '' : 'disabled';
	
	$api	= new WP_Cloud_Server_ServerPilot_API();
	
	$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
	$sp_response = '';
	$server_script = '';

	$servers = $api->call_api( 'servers', null, false, 900, 'GET' );

	if ( empty( $servers['data'] ) ) {
		
		}
		?>

		<div class="content">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wpcs_serverpilot_create_app' );
				wpcs_do_settings_sections( 'wpcs_serverpilot_create_app' );
				wpcs_submit_button( 'Create New Website', 'secondary', 'create_app', null, $attributes );
				?>
			</form>
		</div>

		<?php

		$debug_data = array(
			"name"				=>	get_option( 'wpcs_serverpilot_create_app_name' ),
			"domain"			=>	get_option( 'wpcs_serverpilot_create_app_domain' ),
			"server"			=>	get_option( 'wpcs_serverpilot_create_app_server' ),
			"runtime"			=>	get_option( 'wpcs_serverpilot_create_app_runtime' ),
			"site_title"		=>	get_option( 'wpcs_serverpilot_create_app_site_title' ),
			"admin_user"		=>	get_option( 'wpcs_serverpilot_create_app_admin_user' ),
			"password"			=>	get_option( 'wpcs_serverpilot_create_app_password' ),
			"email"				=>	get_option( 'wpcs_serverpilot_create_app_email' ),
			"autossl"			=>	get_option( 'wpcs_serverpilot_create_app_autossl' ),
			"monitor_enabled"	=>	get_option( 'wpcs_serverpilot_create_app_site_monitor' ),
		);

		if ( get_option( 'wpcs_serverpilot_create_app_name' ) ) {

			$app_name 		= get_option( 'wpcs_serverpilot_create_app_name' );
			$app_domain		= get_option( 'wpcs_serverpilot_create_app_domain' );
			$app_server		= get_option( 'wpcs_serverpilot_create_app_server' );
			$app_runtime	= get_option( 'wpcs_serverpilot_create_app_runtime' );
			$app_site_title = get_option( 'wpcs_serverpilot_create_app_site_title' );
			$app_admin_user = get_option( 'wpcs_serverpilot_create_app_admin_user' );
			$app_password	= get_option( 'wpcs_serverpilot_create_app_password' );
			$app_email		= get_option( 'wpcs_serverpilot_create_app_email' );
			$app_autossl	= get_option( 'wpcs_serverpilot_create_app_autossl' );
			$app_monitor	= get_option( 'wpcs_serverpilot_create_app_site_monitor' );
		
			$sysuserid = null;
					
			$new_server = $api->call_api( 'servers', null, false, 900, 'GET', false, 'server_info' );
			$servers = $new_server['data'];
						
			foreach ( $servers as $server ) {
				
				if ( $app_server == $server['name'] ) {
							
					$server_id = $server['id'];
					
					$new_sysuser = $api->call_api( 'sysusers', null, false, 0, 'GET', false, 'fetch_sysuser' );
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
				$response = $api->call_api( 'sysusers', $user_data, false, 0, 'POST', false, 'create_sysuser' );
				update_option( 'wpcs_sp_api_sysuser_creation', $response );
						
				// Log the creation of the new ServerPilot Sys User
				wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'New System User Added (' . $user_data['name'] . ')' );
						
				$sysuserid = $response['data']['id'];
			}
					
			// Populate Information for creating the new app. This is required by the ServerPilot API
			$app_data = array(
				"name"		=>	$app_name,
    			"sysuserid" =>	$sysuserid,
				"runtime"	=>	$app_runtime,
				"domains"	=> 	array( $app_domain, "www." . $app_domain ),
				"wordpress"	=>	array(
									"site_title"		=>	$app_site_title,
									"admin_user"		=>	$app_admin_user,
									"admin_password"	=>	$app_password,
									"admin_email"		=>	$app_email,								
								)
    		);
			
			// Send the API POST request to create the new 'app'
			$response = $api->call_api( 'apps', $app_data, false, 0, 'POST', true, 'site_creation' );
			
			// Update Log with new website creation
			$api_data = get_option( 'wpcs_sp_api_last_response' );
			if ( ! $response || $response['response']['code'] !== 200 ) {
				$status	= 'Failed';
				$error = $api_data['site_creation']['data']['error']['message'];
				$message = 'An Error Occurred ( ' . $error . ' )';
			} else {
				$status = 'Success';
				$message = 'New Website Created ( ' . $app_domain . ' )';
				$app_id = $api_data['site_creation']['data']['data']['id'];
			
				// Enable SSL Queuing for this App
				if ( $app_autossl == "1" ) {
					wpcs_sp_api_enable_ssl( $api, $app_id, $app_domain );	
				}
			
				// Package Data
				$data = array(
					"site_label"	=>	$app_name,
					"domain_name"	=> 	$app_domain,
    			);
			
				// Executes after the create service functionality
				do_action( 'wpcs_after_serverpilot_app_creation', $data );
				
				// Save Website Data for the User_Id
				// add_user_meta( $data['user_id'], '_web_info', $data);
				
				// Reset the dismissed site creation option and set new site created option
				update_option( 'wpcs_dismissed_sp_site_creation_notice', FALSE );
				update_option( 'wpcs_sp_new_site_created', TRUE );
			
				$data = array(
					'autossl'			=> 	$app_autossl,
					'monitor_enabled'	=> 	$app_monitor,
					'domain_name'		=>	$app_domain,
					'site_label'		=>	$app_name,
				);
			
				// Executes after the create service functionality
				do_action( 'wpcs_after_serverpilot_site_completion', $data );
			}		
			wpcs_serverpilot_log_event( 'ServerPilot', $status, $message );
		}

		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_serverpilot_create_app_name' );
		delete_option( 'wpcs_serverpilot_create_app_domain' );
		delete_option( 'wpcs_serverpilot_create_app_site_title' );
		delete_option( 'wpcs_serverpilot_create_app_admin_user' );
		delete_option( 'wpcs_serverpilot_create_app_password' );
		delete_option( 'wpcs_serverpilot_create_app_email' );
		delete_option( 'wpcs_serverpilot_create_app_autossl' );
		delete_option( 'wpcs_serverpilot_create_app_site_monitor' );