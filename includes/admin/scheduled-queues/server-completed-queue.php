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
function wpcs_activate_server_completed_queue() {

	// Make sure this event hasn't been scheduled
	if( !wp_next_scheduled( 'wpcs_run_server_completed_queue' ) ) {
		// Schedule the event
		wp_schedule_event( time(), 'one_minute', 'wpcs_run_server_completed_queue' );
	}

}
add_action( 'wpcs_plugin_activated', 'wpcs_activate_server_completed_queue' );
	
/**
 *  Run the Server Completion Queue.
 *
 *  @since 2.1.3
 */
function wpcs_run_server_completed_queue() {
		
	$server_queue	= get_option( 'wpcs_server_complete_queue', array() );
		
	if ( ! empty( $server_queue ) ) {
			
		foreach ( $server_queue as $key => $queued_server ) {
			
			$server_sub_id		= $queued_server['SUBID'];
			$response			= $queued_server['response'];
			$user_id			= $queued_server['user_id'];
			$domain_name		= $queued_server['domain_name'];
			$host_name			= $queued_server['host_name'];
			$host_name_domain	= $queued_server['host_name_domain'];
			$host_name_fqdn		= $queued_server['fqdn'];
			$host_name_protocol	= $queued_server['protocol'];
			$host_name_port		= $queued_server['port'];
			$site_label			= $queued_server['site_label'];
			$user_meta			= $queued_server['user_meta'];
			$plan_name			= $queued_server['plan_name'];
			$module_name		= $queued_server['module'];
			$ssh_key_name		= $queued_server['ssh_key'];
			$server_location	= $queued_server['location'];
			$server_backup		= $queued_server['backups'];
			$region_name		= $queued_server['region_name'];
			$size_name			= $queued_server['size_name'];
			$image_name			= $queued_server['image_name'];
				
			$server_module		= strtolower( str_replace( " ", "_", $module_name ) );
				
			// Run Cloud Provider completion function
			$server	= call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $queued_server, $host_name, $server_location );
			
			$debug['server'] = $server;
				
			if ( is_array($server) && ( $server['completed'] ) ) { 
					
				$data = array(
					    "plan_name"			=>	$plan_name,
						"module"			=>	$module_name,
						"host_name"			=>	$host_name,
						"host_name_domain"	=>	$host_name_domain,
						"fqdn"				=>	$host_name_fqdn,
						"protocol"			=>	$host_name_protocol,
						"port"				=>	$host_name_port,
						"server_name"		=>	$site_label,
    					"region_name"		=>	$region_name,
						"size_name"			=>	$size_name,
						"image_name"		=> 	$image_name,
						"ssh_key_name"		=> 	$ssh_key_name,
						"user_data"			=>	$user_meta,
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
				$client_data[$module_name][] = $data;

				update_option( 'wpcs_cloud_server_client_info', $client_data );
					
				// Remove the server from the completion queue
				unset( $server_queue[ $key ] );
				update_option( 'wpcs_server_complete_queue', $server_queue );
					
				$debug['client_data'] = $data;
			}
			
			update_option( 'wpcs_server_complete_queue_debug', $debug );
		}
	}
}
add_action( 'wpcs_run_server_completed_queue', 'wpcs_run_server_completed_queue' );