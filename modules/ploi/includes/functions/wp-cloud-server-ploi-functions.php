<?php
/**
 * WP Cloud Server - Ploi Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
*  Return the status of the API connection
*
*  @since  1.0.0
*/
function wpcs_ploi_module_api_connected() {
	return WPCS_Ploi()->settings->wpcs_ploi_module_api_connected();
}

/**
 *  Check if API settings are saved to database
 *
 *  @since  1.0.0
 *  @return boolean  true if settings exist, false otherwise
 */
function wpcs_ploi_check_api_settings_existance() {
	$api_token = get_option( 'wpcs_ploi_api_token' );
	if ( empty( $api_token )  ) {
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
function wpcs_ploi_set_transient( $key = null, $value = null, $expiration = 900 ) {
	$transient_keys = get_option( 'wpcs_ploi_transient_keys' );
	if ( set_transient( $key, $value, $expiration ) ) {
		$transient_keys[ $key ] = true;
		update_option( 'wpcs_ploi_transient_keys', $transient_keys );
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
function wpcs_ploi_delete_transient( $key = null ) {
	$transient_keys = get_option( 'wpcs_ploi_transient_keys' );
	$delete = delete_transient( $key );
	if ( ! $delete ) {
		delete_transient( 'wpcs_ploi_api_response_' . md5( $key ) );
	}
	if ( $delete ) {
		unset( $transient_keys[ $key ] );
		update_option( 'wpcs_ploi_transient_keys', $transient_keys );
		return true;
	}
	return false;
}

/**
 *  Purge our transient/endpoint response cache
 *
 *  @since  1.0.0
 */
function wpcs_ploi_purge_cache() {
	$transient_keys = get_option( 'wpcs_ploi_transient_keys', array() );
	foreach ( $transient_keys as $transient_key => $value ) {
		$deleted = delete_transient( $transient_key );
		if ( $deleted ) {
			unset( $transient_keys[ $transient_key ] );
		}
	}
	update_option( 'wpcs_ploi_transient_keys', $transient_keys );
}

/**
 *  Reset API Settings
 *
 *  @since  1.0.0
 */
function wpcs_ploi_reset_api_settings() {
	if ( current_user_can( 'manage_options' ) ) {
		delete_option( 'wpcs_ploi_api_token' );
		wpcs_ploi_purge_cache();
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
function wpcs_ploi_api_response_data( $model, $body, $data, $app_data, $function = null ) {
		
	$response_array = get_option( 'wpcs_ploi_api_last_response' );
		
	if ( isset( $function ) ) {
		$source = $function;
	} else {
		$source = $model;	
	}
		
	$response_array[ $source ][ 'body' ]  		= $body;
	$response_array[ $source ][ 'data' ]  		= $data;
	$response_array[ $source ][ 'app_data' ]  	= $app_data;
		
	update_option( 'wpcs_ploi_api_last_response', $response_array );
		
}

/**
 *  Log an event for display in the log tab
 *
 *  @since  1.0.0
 */
function wpcs_ploi_log_event( $new_event, $new_status, $new_desc ) {
	// Sanitize the new event details
	$event	= sanitize_text_field( $new_event );
	$status = sanitize_text_field( $new_status );
	$desc	= sanitize_text_field( $new_desc );
		
	$log_count = 0;
	$logged_data = get_option( 'wpcs_ploi_logged_data', array() );
		
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
		
	update_option( 'wpcs_ploi_logged_data', $logged_data );
	
	// Executes after Ploi log event has been performed
	do_action( 'wpcs_ploi_after_log_event', $logged_data );
		
}