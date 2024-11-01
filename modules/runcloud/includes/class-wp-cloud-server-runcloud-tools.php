<?php

/**
 * WP Cloud Server - RunCloud Module Tools Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Check if API settings are saved to database
 *
 *  @since  1.0.0
 *  @return boolean  true if settings exist, false otherwise
 */
function wpcs_runcloud_check_api_settings_existance() {
	$api_token = get_option( 'wpcs_runcloud_api_token' );
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
function wpcs_runcloud_set_transient( $key = null, $value = null, $expiration = 900 ) {
	$transient_keys = get_option( 'wpcs_runcloud_transient_keys' );
	if ( set_transient( $key, $value, $expiration ) ) {
		$transient_keys[ $key ] = true;
		update_option( 'wpcs_runcloud_transient_keys', $transient_keys );
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
function wpcs_runcloud_delete_transient( $key = null ) {
	$transient_keys = get_option( 'wpcs_runcloud_transient_keys' );
	$delete = delete_transient( $key );
	if ( ! $delete ) {
		delete_transient( 'wpcs_runcloud_api_response_' . md5( $key ) );
	}
	if ( $delete ) {
		unset( $transient_keys[ $key ] );
		update_option( 'wpcs_runcloud_transient_keys', $transient_keys );
		return true;
	}
	return false;
}

/**
 *  Purge our transient/endpoint response cache
 *
 *  @since  1.0.0
 */
function wpcs_runcloud_purge_cache() {
	$transient_keys = get_option( 'wpcs_runcloud_transient_keys', array() );
	foreach ( $transient_keys as $transient_key => $value ) {
		$deleted = delete_transient( $transient_key );
		if ( $deleted ) {
			unset( $transient_keys[ $transient_key ] );
		}
	}
	update_option( 'wpcs_runcloud_transient_keys', $transient_keys );
}

/**
 *  Reset API Settings
 *
 *  @since  1.0.0
 */
function wpcs_runcloud_reset_api_settings() {
	if ( current_user_can( 'manage_options' ) ) {
		delete_option( 'wpcs_runcloud_api_token' );
		wpcs_runcloud_purge_cache();
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
function wpcs_runcloud_api_response_data( $model, $body, $data, $app_data, $function = null ) {
		
	$response_array = get_option( 'wpcs_runcloud_api_last_response' );
		
	if ( isset( $function ) ) {
		$source = $function;
	} else {
		$source = $model;	
	}
		
	$response_array[ $source ][ 'body' ]  		= $body;
	$response_array[ $source ][ 'data' ]  		= $data;
	$response_array[ $source ][ 'app_data' ]  	= $app_data;
		
	update_option( 'wpcs_runcloud_api_last_response', $response_array );
		
}

/**
 *  Log an event for display in the log tab
 *
 *  @since  1.0.0
 */
function wpcs_runcloud_log_event( $new_event, $new_status, $new_desc ) {
	// Sanitize the new event details
	$event	= sanitize_text_field( $new_event );
	$status = sanitize_text_field( $new_status );
	$desc	= sanitize_text_field( $new_desc );
		
	$log_count = 0;
	$logged_data = get_option( 'wpcs_runcloud_logged_data', array() );
		
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
		
	update_option( 'wpcs_runcloud_logged_data', $logged_data );
	
	// Executes after RunCloud log event has been performed
	do_action( 'wpcs_runcloud_after_log_event', $logged_data );
		
}

function wpcs_runcloud_plugin_activation_link($plugin) {
	// the plugin might be located in the plugin folder directly
	if (strpos($plugin, '/')) {
    	$plugin = str_replace('/', '%2F', $plugin);
	}
	$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin);
	// change the plugin request to the plugin to pass the nonce check
	$_REQUEST['plugin'] = $plugin;
	$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);
	return $activateUrl;
}