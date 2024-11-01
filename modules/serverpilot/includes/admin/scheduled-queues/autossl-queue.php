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
 *  Activates the SSL Queue.
 *
 *  @since 1.1.0
 */
function wpcs_sp_module_activate_ssl_queue() {

	// Make sure this event hasn't been scheduled
	if( !wp_next_scheduled( 'wpcs_sp_module_run_ssl_queue' ) ) {
		// Schedule the event
		wp_schedule_event( time(), 'twicedaily', 'wpcs_sp_module_run_ssl_queue' );
		wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'SSL Activation Queue Started' );
	}

}
add_action( 'wpcs_serverpilot_module_activate', 'wpcs_sp_module_activate_ssl_queue' );
	
/**
 *  Run the SSL Queue.
 *
 *  @since 1.1.0
 */
function wpcs_sp_module_run_ssl_queue() {

	$api = new WP_Cloud_Server_ServerPilot_API();
		
	$ssl_queue = get_option( 'wpcs_sp_api_ssl_queue' );
		
	if ( ! empty( $ssl_queue ) ) {
		wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'Running SSL Activation Queue' );
		foreach ( $ssl_queue as $ssl_site ) {
					
			wpcs_sp_api_enable_ssl( $api, $ssl_site['app_id'], $ssl_site['domain'] );
					
		}	
	}

}
add_action( 'wpcs_sp_module_run_ssl_queue', 'wpcs_sp_module_run_ssl_queue' );