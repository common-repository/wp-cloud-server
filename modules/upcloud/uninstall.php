<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wpcs_uninstall_upcloud_plugin() {
	
	$delete_data_confirmed = get_option( 'wpcs_uninstall_data_confirmed' );
	
	delete_option( 'wpcs_upcloud_server_complete_queue' );
	
	// Clear the Server Completed Queue
	wp_clear_scheduled_hook( 'wpcs_upcloud_run_server_completed_queue' );
	
	if ( $delete_data_confirmed ) {
	
		// Delete the Log Files
		delete_option( 'wpcs_upcloud_log_event' );
		
	}

}

wpcs_uninstall_upcloud_plugin();