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
 *  Activates the Server Completion Queue.
 *
 *  @since 2.1.3
 */
function wpcs_activate_digitalocean_api_queue() {

	// Make sure this event hasn't been scheduled
	if( !wp_next_scheduled( 'wpcs_run_digitalocean_api_queue' ) ) {
		// Schedule the event
		wp_schedule_event( time(), 'thirty_minutes', 'wpcs_run_digitalocean_api_queue' );
	}

}
add_action( 'wpcs_plugin_activated', 'wpcs_activate_digitalocean_api_queue' );
	
/**
 *  Run the Server Completion Queue.
 *
 *  @since 2.1.3
 */
function wpcs_run_digitalocean_api_queue() {
		
	do_action( 'wpcs_execute_digitalocean_api_queue');
}
add_action( 'wpcs_run_digitalocean_api_queue', 'wpcs_run_digitalocean_api_queue' );