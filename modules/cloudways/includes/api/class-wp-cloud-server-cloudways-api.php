<?php

/**
 * WP Cloud Server - Cloudways Module API Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Cloudways_API {

	/**
	 *  CloudServer API base url
	 *
	 *  @var string
	 */
	private static $api_base_url = 'https://api.cloudways.com/api/v1';

	/**
	 *  Holder for API key from settings
	 *
	 *  @var string
	 */
	private static $email;
	
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

		self::$email 	= get_option( 'wpcs_cloudways_email' );
		self::$api_key  = get_option( 'wpcs_cloudways_api_key' );

	}

	/**
	 *  Check that we can connect to API
	 *
	 *  @since  1.0.0
	 *  @return boolean  true when API connection good, false if not
	 */
	public function wpcs_cloudways_get_api_url() {
		return self::$api_base_url;
	}

	/**
	 *  Check that we can connect to API
	 *
	 *  @since  1.0.0
	 *  @return boolean  true when API connection good, false if not
	 */
	public function wpcs_cloudways_check_api_health() {

		// Check if the health is temporarily cached.
		$health = get_transient( 'wpcs_cloudways_api_health' );

		if ( 'ok' === $health ) {
			return true;
		}
	
		// Perform the Cloudways API Health Check
		$api_response = wpcs_cloudways_call_api_health_check();
		update_option( 'wpcs_cloudways_api_last_health_response', $api_response );

		// Check if the API Response is Valid
		if ( wpcs_cloudways_api_response_not_valid( $api_response ) ) {

			// Can't connect. Not healthy.
			delete_transient( 'wpcs_cloudways_api_health' );
			update_option('wpcs_dismissed_cloudways_api_notice', FALSE );

			// Update the API check failed Flag if not already set
			if ( !get_option( 'wpcs_cloudways_api_health_check_failed', false ) ) {
				wpcs_log_event( 'Cloudways', 'Failed', 'The Cloudways API Health Check Failed!' );
				update_option( 'wpcs_cloudways_api_health_check_failed', true );
			}
			return false;
		}
			
		// Update Cloudways Module Data
		WP_Cloud_Server_Cloudways_Settings::wpcs_cloudways_update_servers();

		// API connection is healthy. Cache result for three minutes
		wpcs_cloudways_set_transient( 'wpcs_cloudways_api_health', 'ok', 3000 );
		
		if ( ! get_option( 'wpcs_cloudways_api_health_check_failed' ) ) {
			wpcs_cloudways_log_event( 'Cloudways', 'Success', 'The Cloudways API Health Check Completed Successfully' );

			// Clear the latched failed indication now that API is ok
			update_option( 'wpcs_cloudways_api_health_check_failed', false );
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

		if ( ( !isset( $module_list[ 'Cloudways' ]['status'] ) ) || ( 'active' !== $module_list[ 'Cloudways' ]['status'] ) ) {
			return false;
		}

		if ( 'POST' === $request || 'DELETE' === $request || 'GET' === $request ) {
			$cache = false;
		}

		// Check that there's no response cached for this endpoint.
		if ( $cache ) {
			$data_from_cache = self::$instance->get_api_response_cache( $model );

			if ( false !== $data_from_cache ) {
				return $data_from_cache;
			}
		}
		
		$body = [
    		'api_key'  	=> get_option( 'wpcs_cloudways_api_key' ),
    		'email'		=> get_option( 'wpcs_cloudways_email' ),
		];

		$data = wpcs_cloudways_get_access_token( self::$api_base_url );

		// Failure to obtain token means we exit
		if ( !$data ) {
			//wpcs_cloudways_log_event( 'Cloudways', 'Failure', 'No Access Token! Check API Settings' );
			return false;
		}

		$access_token = ( isset( $data ) ) ? $data : '';

		//update_option( 'wpcs_cloudways_api_access_token', $access_token );

		$args = array(
			'headers'	=> array(
				'Content-Type'	=> 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
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
			$args['method'] = 'PUT';
			$response = wp_safe_remote_request( trailingslashit( self::$api_base_url ) . $model , $args );
		} else if ( 'GET' === $request ) {
			$args['body'] = $api_data;
			$args['method'] = 'GET';
			$response = wp_safe_remote_get( trailingslashit( self::$api_base_url ) . $model , $args );
		} else if ( 'DELETE' === $request ) {
			$args['body'] = json_encode( $api_data );
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
		$api_response = get_option('wpcs_cloudways_api_last_response', array());
			
		// if ( ( $response_code <= 200 && $response_code > 300 ) || $response_code > 300 ) {
		if ( ( $response_code < 200 ) || $response_code > 300 ) {
			// Save the last API error response for debugging
			$api_response[$function] = $response;
			update_option( 'wpcs_cloudways_api_last_response', $api_response );
			//update_option( 'wpcs_cloudways_api_last_response', $response );
			//wpcs_Cloudways_api_response_data( $model, $body, $data, $api_data, $function );
			return false;
		}
		$api_response[$function]['data'] = $data;
		$api_response[$function]['body'] = $body;
		// Save the last API response for debugging
		update_option( 'wpcs_cloudways_api_last_response', $api_response );
		//wpcs_Cloudways_api_response_data( $model, $body, $data, $api_data, $function );

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
		return get_transient( 'wpcs_cloudways_api_response_' . md5( $model ) );
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

		wpcs_cloudways_set_transient( 'wpcs_cloudways_api_response_' . md5( $model ), $data, $lifetime );
	}

	/**
	 *  Save response for endpoint to cache
	 *
	 *  @since 1.0.0
	 *  @param string  $endpoint For what endpoint to cache response
	 *  @param string  $data     Response data to cache
	 *  @param integer $lifetime How long the response should be cached, defaults to 15 minutes curl_setopt($ch, CURLOPT_POST, 1);
	 */
	private function remote_post( $url, $args ) {
			
		$ch = curl_init();
		$headers = [ 'email:' . self::$api_token . '', 'api_key:' . self::$api_token ];
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 25);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args['body']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		curl_setopt($ch, CURLOPT_URL, $url);

		$body = curl_exec($ch);
		$data = curl_getinfo($ch);
		curl_close($ch);
			
		update_option( 'wpcs_vultr_api_response', $data );
			
		$response['response'] = $data;
		$response['body'] = $body;
			
		return $response;
			
	}


}