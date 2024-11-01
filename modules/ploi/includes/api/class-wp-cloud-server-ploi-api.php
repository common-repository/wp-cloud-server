<?php

/**
 * WP Cloud Server - Ploi Module API Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_API {

	/**
	 *  CloudServer API base url
	 *
	 *  @var string
	 */
	private static $api_base_url = 'https://ploi.io/api/';

	/**
	 *  Holder for API key from settings
	 *
	 *  @var string
	 */
	private static $api_key;

	/**
	 *  Set Variables and Action Hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		self::$api_key		= get_option( 'wpcs_ploi_api_key' );

	}

	/**
	 *  Check that we can connect to API
	 *
	 *  @since  1.0.0
	 *  @return boolean  true when API connection good, false if not
	 */
	public function wpcs_ploi_check_api_health() {

		// Check if the health is temporarily cached.
		$health = get_transient( 'wpcs_ploi_api_health' );

		if ( 'ok' === $health ) {
			return true;
		}
	
		/**
		 *  Make API call to check if credentials work
		 */
		// $api_response = self::call_api( 'account/info', null, false, 0, 'GET', true, 'api_health' );
		$api_response = wpcs_ploi_call_api_health_check();
			
		update_option( 'wpcs_ploi_api_last_health_response', $api_response );

		// if ( ! $api_response || $api_response['response']['http_code'] !== 200 ) {
		if ( wpcs_ploi_api_response_valid( $api_response ) ) {

			// Can't connect. Not healthy.
			delete_transient( 'wpcs_ploi_api_health' );
			update_option('wpcs_dismissed_ploi_api_notice', FALSE );

			// Update the log event once for current failure
			if ( ! get_option( 'wpcs_ploi_api_health_check_failed', false ) ) {
				wpcs_ploi_log_event( 'Ploi', 'Failed', 'The Ploi API Health Check Failed!' );
				update_option( 'wpcs_ploi_api_health_check_failed', true );
			}
			return false;
		}
			
		// Update Ploi Module Data
		WP_Cloud_Server_Ploi_Settings::wpcs_ploi_update_servers();

		// API connection is healthy. Cache result for three minutes
		wpcs_ploi_set_transient( 'wpcs_ploi_api_health', 'ok', 60 );
		
		if ( get_option( 'wpcs_ploi_api_health_check_failed' ) ) {
			wpcs_ploi_log_event( 'Ploi', 'Success', 'The Ploi API Health Check Completed Successfully' );

			// Clear the latched failed indication now that API is ok
			update_option( 'wpcs_ploi_api_health_check_failed', false );
		}
		
		return true;

	}

	/**
	 *  Make Call to API
	 *
	 *  @since  1.0.0
	 *  @param  string  $endpoint       	Which required endpoint for current api call
	 *  @param  mixed 	$data 				Data that we send to API as a body, null if no data and make get request
	 *  @param  boolean $cache          	true if response should be cached, defaults to false
	 *  @param  integer $cache_lifetime 	Set cache lifetime, defaults to 15 minutes
	 *  @param  string	$request			Request type, such as GET, POST, etc.
	 * 	@param	boolean	$enable_response	Determines what data is returned from API call
	 *  @return mixed                  		Payload from API response if call succesful, false if some error happened
	 */
	public function call_api( $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {

		$module_list = get_option( 'wpcs_module_list' );

		if ( ( !isset( $module_list[ 'Ploi' ]['status'] ) ) || ( 'active' !== $module_list[ 'Ploi' ]['status'] ) ) {
			return false;
		}

		if ( 'POST' === $request || 'DELETE' === $request || 'GET' === $request || 'PATCH' === $request ) {
			$cache = false;
		}

		// Check that there's no response cached for this endpoint.
		if ( $cache ) {
			$data_from_cache = self::$instance->get_api_response_cache( $model );

			if ( false !== $data_from_cache ) {
				return $data_from_cache;
			}
		}

		$args = array(
			'headers'	=> array(
				'Content-Type'	=> 'application/json',
				'Accept'		=> 'application/json',
				'Authorization' => 'Bearer ' . self::$api_key,
			),
			'reject_unsafe_urls' => false, 
			//'sslverify'	=> false,
		);

		if ( 'POST' === $request ) {
			$args['method'] = 'POST';
			$args['body'] = json_encode( $api_data );
			$response = wp_safe_remote_post( trailingslashit( self::$api_base_url ) . $model , $args );
		} else if ( 'PUT' === $request ) {
			$args['body'] = json_encode( $api_data );
			$response = wp_safe_remote_post( trailingslashit( self::$api_base_url ) . $model , $args );
		} else if ( 'GET' === $request ) {
			$args['body'] = $api_data;
			$args['method'] = 'GET';
			$response = wp_safe_remote_get( trailingslashit( self::$api_base_url ) . $model , $args );
		} else if ( 'PATCH' === $request ) {
			$args['body'] = json_encode( $api_data );
			$args['method'] = 'PATCH';
			$response = wp_safe_remote_request( trailingslashit( self::$api_base_url ) . $model , $args );
			update_option( 'wpcs_ploi_api_patch_response', $response );
		} else if ( 'DELETE' === $request ) {
			$args['method'] = 'DELETE';
			$response = wp_safe_remote_request( trailingslashit( self::$api_base_url ) . $model , $args );
		}
		
		// WP couldn't make the call for some reason, return false as a error.
		if ( is_wp_error( $response ) ) {
			return false;
		}
			
		// Get request response body and endcode the JSON data.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Test the API response code is 200-299 which proves succesful request was made.
		$response_code = (int) $response['response']['code'];
			
		// if ( ( $response_code <= 200 && $response_code > 300 ) || $response_code > 300 ) {
		if ( ( $response_code < 200 ) || $response_code > 300 ) {
			// Save the last API error response for debugging
			$api_response['ploi_api_error'] = $response;
			update_option( 'wpcs_ploi_api_last_response', $api_response );
			//wpcs_Ploi_api_response_data( $model, $body, $data, $api_data, $function );
			return false;
		}
		
		$api_response = get_option( 'wpcs_ploi_api_last_response' );
		
		$api_response[$function] = $response;
			
		// Save the last API response for debugging
		update_option( 'wpcs_ploi_api_last_response', $api_response );
		//wpcs_Ploi_api_response_data( $model, $body, $data, $api_data, $function );

		// If response should be cached, cache it.
		if ( $cache ) {
			self::set_api_response_cache( $model, $data, $cache_lifetime );
		}

		// Determine the format of the returned API response
		if ( $enable_response ) {
			return $response;
		} else {
			return $data;
		}
	}

	/**
	 *  Get response for endpoint from cache
	 *
	 *  @since  1.0.0
	 *  @param  string  $endpoint For what endpoint we want response
	 *  @return mixed            	String if there's cached response, false if not
	 */
	private function get_do_api_response_cache( $model ) {
		return get_transient( 'wpcs_ploi_api_response_' . md5( $model ) );
	}

	/**
	 *  Save response for endpoint to cache
	 *
	 *  @since 1.0.0
	 *  @param string  $endpoint For what endpoint to cache response
	 *  @param string  $data     Response data to cache
	 *  @param integer $lifetime How long the response should be cached, defaults to 15 minutes
	 */
	private function set_do_api_response_cache( $model = null, $data = null, $lifetime = 900 ) {
		if ( ! $model || ! $data ) {
			return;
		}

		wpcs_ploi_set_transient( 'wpcs_ploi_api_response_' . md5( $model ), $data, $lifetime );
	}
}