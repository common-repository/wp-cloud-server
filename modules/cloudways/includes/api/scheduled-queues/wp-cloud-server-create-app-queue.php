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
function wpcs_cloudways_create_app_queue() {

	// Make sure this event hasn't been scheduled
	if( !wp_next_scheduled( 'wpcs_run_cloudways_create_app_queue' ) ) {
		// Schedule the event
		wp_schedule_event( time(), 'one_minute', 'wpcs_run_cloudways_create_app_queue' );
	}

}
add_action( 'wpcs_plugin_activated', 'wpcs_cloudways_create_app_queue' );
	
/**
 *  Run the Server Completion Queue.
 *
 *  @since 2.1.3
 */
function wpcs_run_cloudways_create_app_queue() {
	
	// Create instance of the Cloudways API
	$api = new WP_Cloud_Server_Cloudways_API;
		
	$api_data = get_option( 'wpcs_cloudways_create_app_queue', array() );
	
	/* Cloudways API Data */
	
	if ( !empty( $api_data ) ) {
		foreach ( $api_data as $key => $app ) {
	
			$app_data = array(
				"server_id"		=>	$app['server_id'],
				"application"	=> 	$app['application'],
				"app_version"	=>	$app['app_version'],
				"app_label"		=>	$app['app_label'],
			);

			if ( !empty( $app['new_project_name'] ) ) {
				$app_data["project_name"] = $app['new_project_name'];
			}

			if ( empty( $app['operation_id'] ) ) {
			
				// Create Application
				$response = $api->call_api( 'app', $app_data, false, 0, 'POST', false, 'create_cloudways_application' );
				
				if ( !empty( $response ) ) {
					$api_data[ $key ]['operation_id']	= $response["operation_id"];
					$api_data[ $key ]['stage']			= 'Waiting App Completion';
				}

			} else {

				$app_id = array(
					"id"	=>	$app['operation_id'],
				);

				$status = $api->call_api( 'operation/' . $app['operation_id'], $app_id, false, 0, 'GET', false, 'cloudways_application_status' );

				if ( isset($status['operation']['is_completed']) && $status['operation']['is_completed'] ) {

					if ( 'no_project' !== $app['selected_project_id'] ) {

						$project_data = array(
							"id"		=>	$app['selected_project_id'],
							"name"		=>	$app['selected_project_name'],
							"app_ids"	=>	$status['operation']['app_id'],
						);

						$update = $api->call_api( "project/" . $app['selected_project_id'], $project_data, false, 0, 'PUT', false, 'cloudways_project' );
					
					}

					unset($api_data[ $key ]);
				}

			}
			
		}
		update_option( 'wpcs_cloudways_create_app_queue', $api_data );
	}

}
add_action( 'wpcs_run_cloudways_create_app_queue', 'wpcs_run_cloudways_create_app_queue' );