<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wpcs_delete_plugin() {
	
	// Set Plugin Inactive
	update_option( 'wpcs_plugin_active', false );
	
	$delete_data_confirmed = get_option( 'wpcs_uninstall_data_confirmed' );
	
	if ( $delete_data_confirmed ) {

		// Delete the main Module List
		delete_option( 'wpcs_module_list' );
		
		// Delete Templates
		delete_option( 'wpcs_template_data_backup' );

		// Delete the Set-up Wizard Options
		delete_option( 'wpcs_setup_wizard_complete' );
		delete_option( 'wpcs_setup_wizard_complete_confirmed' );
		delete_option( 'wpcs_skip_setup_wizard' );
		delete_option( 'wpcs_setup_digitalocean_api_key' );
		delete_option( 'wpcs_setup_serverpilot_client_id' );
		delete_option( 'wpcs_setup_serverpilot_api_key' );
		delete_option( 'wpcs_setup_serverpilot_ssh_key_name' );
		delete_option( 'wpcs_setup_serverpilot_ssh_key' );
		delete_option( 'wpcs_serverpilots_ssh_keys' );

		//delete_option( 'wpcs_wp_cli_ssh_key' );
		delete_option( 'wpcs_enable_debug_mode' );
		delete_option( 'wpcs_module_config' );
		delete_option( 'wpcs_config' );
	
		// Delete the DigitalOcean Module Options

		delete_option( 'wpcs_digitalocean_api_token' );
		delete_option( 'wpcs_dismissed_digitalocean_module_setup_notice' );
		delete_option( 'wpcs_digitalocean_server_name' );
		delete_option( 'wpcs_digitalocean_server_type' );	
		delete_option( 'wpcs_digitalocean_server_region' );
		delete_option( 'wpcs_digitalocean_server_size' );
		delete_option( 'wpcs_digitalocean_template_name' );
		delete_option( 'wpcs_digitalocean_template_type' );	
		delete_option( 'wpcs_digitalocean_template_region' );
		delete_option( 'wpcs_digitalocean_template_size' );
		delete_option( 'wpcs_digitalocean_api_token' );
		delete_option( 'wpcs_digitalocean_logged_data' );
		delete_option( 'wpcs_digitalocean_api_last_response' );
	

		delete_option( 'wpcs_sp_api_account_id' );
		delete_option( 'wpcs_sp_api_key' );
		delete_option( 'wpcs_serverpilot_logged_data' );
		delete_option( 'wpcs_sp_api_site_creation_queue' );
		delete_option( 'wpcs_sp_api_ssl_queue' );
		delete_option( 'wpcs_sp_api_sysuser_creation' );
		delete_option( 'wpcs_sp_new_site_created' );
		delete_option( 'wpcs_sp_api_last_response' );
		delete_option( 'wpcs_app_server_list' );
	
		// Delete the Log Files
		delete_option( 'wpcs_digitalocean_log_event' );
		delete_option( 'wpcs_serverpilot_log_event' );
		
		delete_option( 'wpcs_cloud_server_client_info' );
		
	}
		
	// Data to be delete on uninstall
	
	delete_option( 'wpcs_current_page' );
	delete_transient( 'wpcs_digitalocean_api_health' );
	
	delete_option( 'wpcs_digitalocean_api_health_check_failed' );
	delete_option( 'wpcs_dismissed_digitalocean_api_notice' );
	delete_option( 'wpcs_digitalocean_api_last_health_response' );
	delete_option( 'wpcs_digitalocean_server_attached' );
	
	// Delete the ServerPilot Module Options
	delete_transient( 'wpcs_sp_api_health' );
	
	delete_option( 'wpcs_sp_api_last_health_response' );
	delete_option( 'wpcs_sp_api_health_check_failed' );
	delete_option( 'wpcs_dismissed_sp_module_setup_notice' );
		
	// Clear the SSL and Site Creation Queues
	wp_clear_scheduled_hook( 'wpcs_sp_module_run_ssl_queue' );
	wp_clear_scheduled_hook( 'wpcs_sp_module_run_site_creation_queue' );

}

wpcs_delete_plugin();