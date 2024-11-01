<?php
/**
 * The ServerPilot Functions
 *
 * @author     Gary Jordan <gary@designedforpixels.com>
 * @since      3.0.3
 *
 * @package    WP_Cloud_Server
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ServerPilot Cloud Server Action Function
 *
 * Allows access to the ServerPilot API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.3
 *
 * @return response The response from the ServerPilot API call
 */
function wpcs_serverpilot_cloud_server_action( $action, $server_id, $enable_response = false ) {
	
	// Create instance of the ServerPilot API
	$api = new WP_Cloud_Server_ServerPilot_API();

	if ( 'delete' == $action ) {

		$request		= 'DELETE';
		$api_response	= $api->call_api( 'servers/' . $server_id, null, false, 0, $request, $enable_response, 'server_action' );

	}
	
	return ( isset( $api_response ) ) ? $api_response : false;
}

/**
 * ServerPilot App Action Function
 *
 * Allows access to the ServerPilot API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.5
 *
 * @return response The response from the ServerPilot API call
 */
function wpcs_serverpilot_cloud_app_action( $action, $app_id, $enable_response = false ) {
	
	// Create instance of the ServerPilot API
	$api = new WP_Cloud_Server_ServerPilot_API();

	if ( 'delete' == $action ) {

		$request		= 'DELETE';
		$api_response	= $api->call_api( 'apps/' . $app_id, null, false, 0, $request, $enable_response, 'app_action' );

	}
	
	return ( isset( $api_response ) ) ? $api_response : false;
}

/**
 * Call to ServerPilot API to List Servers
 *
 * @since  3.0.6
 *
 * @return api_response 	API response
 */
function wpcs_serverpilot_call_api_list_servers( $enable_response = false ) {

	$data = get_option( 'wpcs_serverpilot_api_data' );

	if ( !isset( $data['servers']['data'] ) || isset( $data['servers']['data'] ) && empty( $data['servers']['data'] ) ) {
		$servers = WPCS_ServerPilot()->api->call_api( "servers", null, false, 900, 'GET', false, 'serverpilot_server_list' );
		if ( isset( $servers['data'] ) ) {
			$data['servers'] = $servers;
			update_option( 'wpcs_serverpilot_api_data', $data );
		}
	}

	return ( isset( $data['servers']['data'] ) ) ? $data['servers']['data'] : false;

}

/**
 * Call to ServerPilot API to List Apps
 *
 * @since  3.0.6
 *
 * @return api_response 	API response
 */
function wpcs_serverpilot_call_api_list_apps( $enable_response = false ) {

	$data = get_option( 'wpcs_serverpilot_api_data' );

	if ( !isset( $data['apps']['data'] ) || isset( $data['apps']['data'] ) && empty( $data['apps']['data'] ) ) {
		$apps = WPCS_ServerPilot()->api->call_api( "apps", null, false, 900, 'GET', false, 'serverpilot_app_list' );
		if ( isset( $apps['data'] ) ) {
			$data['apps'] = $apps;
			update_option( 'wpcs_serverpilot_api_data', $data );
		}
	}

	return ( isset( $data['apps']['data'] ) ) ? $data['apps']['data'] : false;

}