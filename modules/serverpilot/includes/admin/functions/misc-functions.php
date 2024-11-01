<?php
/**
 * The Server Tools functionality for the ServerPilot Module.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Check if API settings are saved to database
 *
 *  @since  1.0.0
 *  @return boolean  true if settings exist, false otherwise
 */
function wpcs_sp_check_api_settings_existance() {
		
	$api_account_id = get_option( 'wpcs_sp_api_account_id' );
	$api_key = get_option( 'wpcs_sp_api_key' );
	if ( empty( $api_account_id ) || empty( $api_key )  ) {
		return false;
	}
	return true;
}
/**
 *  Save endpoint response to transient and store transient name to option,
 *  because we need those when resetting plugin
 *
 *  @since 1.0.0
 *  @param string  $key        key/name for transient
 *  @param mixed   $value      value to save
 *  @param integer $expiration how long this transient should exist, in seconds
 */
function wpcs_sp_set_transient( $key = null, $value = null, $expiration = 900 ) {
	$transient_keys = get_option( 'wpcs_sp_transient_keys' );
	if ( set_transient( $key, $value, $expiration ) ) {
		$transient_keys[ $key ] = true;
		update_option( 'wpcs_sp_transient_keys', $transient_keys );
		return true;
	}
	return false;
}
/**
 *  Remove endpoint response from transient cache
 *
 *  @since 1.0.0
 *  @param string  $key  key/name for transient
 */
function wpcs_sp_delete_transient( $key = null ) {
	$transient_keys = get_option( 'wpcs_sp_transient_keys' );
	$delete = delete_transient( $key );
	if ( ! $delete ) {
		delete_transient( 'wpcs_sp_api_response_' . md5( $key ) );
	}
	if ( $delete ) {
		unset( $transient_keys[ $key ] );
		update_option( 'wpcs_sp_transient_keys', $transient_keys );
		return true;
	}
	return false;
}
/**
 *  Purge our transient/endpoint response cache
 *
 *  @since  1.0.0
 */
function wpcs_sp_purge_cache() {
	$transient_keys = get_option( 'wpcs_sp_transient_keys', array() );
	foreach ( $transient_keys as $transient_key => $value ) {
		$deleted = delete_transient( $transient_key );
		if ( $deleted ) {
			unset( $transient_keys[ $transient_key ] );
		}
	}
	update_option( 'wpcs_sp_transient_keys', $transient_keys );
}
/**
 *  Reset API Settings
 *
 *  @since  1.0.0
 */
function wpcs_sp_reset_api_settings() {
	if ( current_user_can( 'manage_options' ) ) {
		delete_option( 'wpcs_sp_api_account_id' );
		delete_option( 'wpcs_sp_api_key' );
		wpcs_sp_purge_cache();
	}
}		
	
/**
 *  Save response for endpoint to cache
 *
 *  @since 1.0.0
 *  @param string  $endpoint 	For what endpoint to cache response
 *  @param string  $response   	Response data to cache
 *  @param integer $source		How long the response should be cached, defaults to 15 minutes
 */
function wpcs_sp_api_response_data( $endpoint, $body, $data, $app_data, $function=null ) {
		
	$response_array = get_option( 'wpcs_sp_api_last_response' );
		
	if ( isset( $function ) ) {
		$source = $function;
	} else {
		$source = $endpoint;	
	}
		
	$response_array[ $source ][ 'body' ]  		= $body;
	$response_array[ $source ][ 'data' ]  		= $data;
	$response_array[ $source ][ 'app_data' ]  	= $app_data;
	$response_array[ $source ][ 'endpoint' ]  	= $endpoint;
		
	update_option( 'wpcs_sp_api_last_response', $response_array );
		
}

/**
 *  Enable SSL
 *
 *  @since 1.1.0
 *  @param string  $endpoint 	For what endpoint to cache response
 *  @param string  $response   	Response data to cache
 *  @param integer $source		How long the response should be cached, defaults to 15 minutes
 */
function wpcs_sp_api_enable_ssl( $api, $app_id, $domain ) {
	
	$ssl_queue = get_option( 'wpcs_sp_api_ssl_queue' );
	
	$server = $api->call_api( 'apps', null, false, 900, 'GET', false, 'ssl_status', $app_id  );
	
	if ( ( isset($server['data']['ssl']) && $server['data']['ssl'] === null ) && ( $server['data']['autossl']['available'] == 1 ) )  {
		
		$app_data = array( "auto"	=>	true );
		$response = $api->call_api( 'apps', $app_data, false, 900, 'POST', false, 'activate_ssl', $app_id  );
		
		if ( 1 == $response['data']['auto'] ) {
			wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'SSL Certificate was installed (' . $domain . ')' );
			$ssl_queue = ( is_array( $ssl_queue ) ) ? $ssl_queue : array();
			$key = array_search( $domain, $ssl_queue );
			if ( array_key_exists( $domain, $ssl_queue ) ) {
				unset( $ssl_queue[ $domain ] );
			}
		} else {
			wpcs_serverpilot_log_event( 'ServerPilot', 'SSL Failed', 'SSL Certificate installation failed (' . $domain . ')' );	
		}
		
	} else {
		
		$ssl_queue = ( is_array( $ssl_queue ) ) ? $ssl_queue : array();
		if ( ! array_key_exists( $domain, $ssl_queue ) ) {
			$site['app_id'] = $app_id;
			$site['domain'] = $domain;
			$ssl_queue[ $domain ] = $site;
			wpcs_serverpilot_log_event( 'ServerPilot', 'SSL Queued', 'Site Added to SSL Queue (' . $domain . ')' );
		}
	}
	
	update_option( 'wpcs_sp_api_ssl_queue', $ssl_queue );
}
	
/**
 *  SSL Queue Status
 *
 *  @since 1.1.0
 *  @param string  $endpoint 	For what endpoint to cache response
 *  @param string  $response   	Response data to cache
 *  @param integer $source		How long the response should be cached, defaults to 15 minutes
 */
function wpcs_sp_api_ssl_status( $app_id, $domain=null ) {
	
	$domain = preg_replace( '/^www./', '', $domain );
	
	$ssl_queue = get_option( 'wpcs_sp_api_ssl_queue' );
	
	$ssl_queue = ( is_array( $ssl_queue ) ) ? $ssl_queue : array();
	
	if ( array_key_exists( $domain, $ssl_queue ) ) {
		$ssl_status = '<span style="color: orange;">SSL Queued</span>';
		return $ssl_status;
	}
	$server = WPCS_ServerPilot()->api->call_api( 'apps', null, false, 900, 'GET', false, 'ssl_status', $app_id  );
	$ssl_status = ( isset( $server['data']['ssl'] ) ) ? '<span style="color: green;">SSL Enabled</span>' : '<span style="color: gray;">Not Enabled</span>';
	return $ssl_status;
		
}

/**
 *  Log an event for display in the log tab
 *
 *  @since  1.0.0
 */
function wpcs_serverpilot_log_event( $new_event, $new_status, $new_desc ) {
	// Sanitize the new event details
	$event	= sanitize_text_field( $new_event );
	$status = sanitize_text_field( $new_status );
	$desc	= sanitize_text_field( $new_desc );
		
	$log_count = 0;
	$logged_data = get_option( 'wpcs_serverpilot_logged_data', array() );
		
	if ( is_array( $logged_data ) ) {
		$log_count = count( $logged_data );
	}
		
	// Limit Log to 20 entries
	if ( $log_count >= 20 ) {
		array_shift( $logged_data );	
	}
		
	// Create Date and Time Stamp
	$date = date("D j M Y G:i:s");
	$data = array(
			'date'			=> $date,
			'event'  		=> $event,
			'status'		=> $status,
			'description'  	=> $desc
		);
		
	// Add new logged event to array
	array_push( $logged_data, $data );
		
	update_option( 'wpcs_serverpilot_logged_data', $logged_data );
	
	// Executes after a log event has been performed
	do_action( 'wpcs_after_log_event', $data );
		
}

function wpcs_serverpilot_update_module_status( $module_name, $new_status ) {
	return WPCS_ServerPilot()->settings->wpcs_serverpilot_update_module_status( $module_name, $new_status );
}