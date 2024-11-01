<?php

/**
 * WP Cloud Server - AWS Lightsail Module API Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_AWS_Lightsail_API {

	/**
	 *  CloudServer API base url
	 *
	 *  @var string
	 */
	private static $api_base_url = 'https://api.aws-lightsail.com/v4';

	/**
	 *  Holder for API key from settings
	 *
	 *  @var string
	 */
	private static $api_token;
	
	/**
	 *  Holder for API key from settings
	 *
	 *  @var string
	 */
	private static $api_secret_key;

	/**
	 *  Set Variables and Action Hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		self::$api_token		= get_option( 'wpcs_aws_lightsail_api_token' );
		self::$api_secret_key	= get_option( 'wpcs_aws_lightsail_api_secret_key' );

	}

	/**
	 *  Check that we can connect to API
	 *
	 *  @since  1.0.0
	 *  @return boolean  true when API connection good, false if not
	 */
	public function wpcs_aws_lightsail_check_api_health() {

		// Check if the health is temporarily cached.
		$health = get_transient( 'wpcs_aws_lightsail_api_health' );

		if ( 'ok' === $health ) {
			return true;
		}
	
		/**
		 *  Make API call to check if credentials work
		 */
		// $api_response = self::call_api( 'account/info', null, false, 0, 'GET', true, 'api_health' );
		$api_response = wpcs_aws_lightsail_call_api_health_check();
			
		update_option( 'wpcs_aws_lightsail_api_last_health_response', $api_response );

		// if ( ! $api_response || $api_response['response']['http_code'] !== 200 ) {
		if ( wpcs_aws_lightsail_api_response_valid( $api_response ) ) {

			// Can't connect. Not healthy.
			delete_transient( 'wpcs_aws_lightsail_api_health' );
			update_option('wpcs_dismissed_aws_lightsail_api_notice', FALSE );

			// Update the log event once for current failure
			if ( ! get_option( 'wpcs_aws_lightsail_api_health_check_failed', false ) ) {
				wpcs_log_event(  'AWS Lightsail', 'Failed', 'The AWS Lightsail API Health Check Failed!' );
				update_option( 'wpcs_aws_lightsail_api_health_check_failed', true );
			}
			return false;
		}
			
		// Update AWS Lightsail Module Data
		WP_Cloud_Server_AWS_Lightsail_Settings::wpcs_aws_lightsail_update_servers();

		// API connection is healthy. Cache result for three minutes
		wpcs_aws_lightsail_set_transient( 'wpcs_aws_lightsail_api_health', 'ok', 60 );
		
		if ( get_option( 'wpcs_aws_lightsail_api_health_check_failed' ) ) {
			wpcs_aws_lightsail_log_event(  'AWS Lightsail', 'Success', 'The AWS Lightsail API Health Check Completed Successfully' );

			// Clear the latched failed indication now that API is ok
			update_option( 'wpcs_aws_lightsail_api_health_check_failed', false );
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
	public function call_api( $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'POST', $enable_response = false, $function = null, $region = 'eu-west-2' ) {

		$module_list = get_option( 'wpcs_module_list' );

		if ( ( !isset( $module_list[ 'AWS Lightsail' ]['status'] ) ) || ( 'active' !== $module_list[ 'AWS Lightsail' ]['status'] ) ) {
			return false;
		}

		if ( 'POST' === $request || 'DELETE' === $request || 'GET' === $request ) {
			$cache = false;
		}

		// Check that there's no response cached for this endpoint.
		// if ( $cache ) {
		//		$data_from_cache = self::$instance->get_api_response_cache( $model );

		//	if ( false !== $data_from_cache ) {
		//		return $data_from_cache;
		//	}
		//}

		$aws_lightsail_api_token	= get_option( 'wpcs_aws_lightsail_api_token' );
		$aws_lightsail_secret_key	= get_option( 'wpcs_aws_lightsail_api_secret_key' );

		if ( ( '' == $aws_lightsail_api_token ) || ( '' == $aws_lightsail_secret_key )) {
			return false;
		}

		$response = null;

		$service				= "lightsail";
		$host 					= "lightsail.$region.amazonaws.com";
		$region					= $region;
		$endpoint				= "https://lightsail.$region.amazonaws.com/";
		$content_type			= "application/x-amz-json-1.1";
		$amz_target				= "Lightsail_20161128.$model";
		
		$debug[] = $host;
		$debug[] = $region;
		$debug[] = $endpoint;
		$debug[] = json_encode( $api_data );
		
		update_option( 'wpcs_lightsail_debug', $debug );

		$access_key				= $aws_lightsail_api_token;
		$secret_key				= $aws_lightsail_secret_key;	

		$date					= new DateTime();
		$value					= $date->getTimestamp();
		$amz_date				= gmdate('Ymd\THis\Z', $value);
		$datestamp 				= substr($amz_date, 0, 8);

		$canonical_uri			= '/';
		$canonical_querystring	= '';
		$canonical_headers		= 'content-type:' . $content_type . "\n" . 'host:' . $host . "\n" . 'x-amz-date:' . $amz_date . "\n" . 'x-amz-target:' . $amz_target . "\n";

		$signed_headers			= 'content-type;host;x-amz-date;x-amz-target';
		
		if ( !empty( $api_data ) ) {
			$api_data	= json_encode( $api_data );
		} else {
			$api_data['pageToken'] = '';
			$api_data	= json_encode( $api_data );
		}

		$payload_hash 			= hash('sha256', $api_data);

		$canonical_request 		= $request . "\n" . $canonical_uri . "\n" . $canonical_querystring . "\n" . $canonical_headers . "\n" . $signed_headers . "\n" . $payload_hash;

		$algorithm				= 'AWS4-HMAC-SHA256';
		$credential_scope		= $datestamp . '/' . $region . '/' . $service . '/' . 'aws4_request';
		$hash					= hash('sha256', $canonical_request);
		$string_to_sign			= "{$algorithm}\n{$amz_date}\n{$credential_scope}\n{$hash}";
	
		$kDate 					= hash_hmac( 'sha256', $datestamp, "AWS4{$secret_key}", true );
    	$kRegion 				= hash_hmac( 'sha256', $region, $kDate, true );
    	$kService 				= hash_hmac( 'sha256', $service, $kRegion, true );
    	$kSigning 				= hash_hmac( 'sha256', 'aws4_request', $kService, true );

		$signature 				= hash_hmac('sha256', $string_to_sign, "$kSigning");

		$authorization_header 	= $algorithm . ' ' . 'Credential=' . $access_key . '/' . $credential_scope . ', ' .  'SignedHeaders=' . $signed_headers . ', ' . 'Signature=' . $signature;
	
		$args = array(
			'headers'	=> array(
				'Content-Type'	=> 	$content_type,
           		'X-Amz-Date'	=>	$amz_date,
           		'X-Amz-Target'	=>	$amz_target,
				'Authorization' => 	$authorization_header,
			), 
		);

		if ( 'POST' === $request ) {
			$args['method']	= 'POST';
			$args['body']	= $api_data;
			$response = wp_safe_remote_post( trailingslashit( $endpoint ) , $args );
		} else if ( 'PUT' === $request ) {
			$args['body']	= $api_data;
			$response		= wp_safe_remote_post( trailingslashit( $endpoint ) , $args );
		} else if ( 'GET' === $request ) {
			$args['body']	= $api_data;
			$args['method'] = 'GET';
			$response = wp_safe_remote_get( trailingslashit( $endpoint ) , $args );
		} else if ( 'DELETE' === $request ) {
			$args['method'] = 'DELETE';
			$response = wp_safe_remote_request( trailingslashit( $endpoint ) , $args );
		}

		// WP couldn't make the call for some reason, return false as a error.
		if ( is_wp_error( $response ) ) {
			return false;
		}
			
		// Get request response body and endcode the JSON data.
		$body			= wp_remote_retrieve_body( $response );
		$data			= json_decode( $body, true );

		// Test the API response code is 200-299 which proves succesful request was made.
		$response_code	= (int) ( isset( $response['response']['code'] ) ) ? $response['response']['code'] : '';
			
		// if ( ( $response_code <= 200 && $response_code > 300 ) || $response_code > 300 ) {
		if ( ( $response_code < 200 ) || $response_code > 300 ) {
			// Save the last API error response for debugging
			update_option( 'wpcs_aws_lightsail_api_last_response', $body );
			//wpcs_aws_lightsail_api_response_data( $model, $body, $data, $api_data, $function );
			//return false;
		}
			
		// Save the last API response for debugging
		update_option( 'wpcs_aws_lightsail_api_last_response', $response );
		//wpcs_aws_lightsail_api_response_data( $model, $body, $data, $api_data, $function );

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
		return get_transient( 'wpcs_aws_lightsail_api_response_' . md5( $model ) );
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

		wpcs_aws_lightsail_set_transient( 'wpcs_aws_lightsail_api_response_' . md5( $model ), $data, $lifetime );
	}
	

	/**
	 *  Save response for endpoint to cache
	 *
	 *  @since 1.0.0
	 *  @param string  $endpoint For what endpoint to cache response
	 *  @param string  $data     Response data to cache
	 *  @param integer $lifetime How long the response should be cached, defaults to 15 minutes curl_setopt($ch, CURLOPT_POST, 1);
	 */
	private function remote_get( $url, $args ) {
			
		$ch = curl_init();
		$headers = [ 'API-Key:' . self::$api_token ];
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 25);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);	
		curl_setopt($ch, CURLOPT_URL, $url);

		$body = curl_exec($ch);
		$data = curl_getinfo($ch);
		curl_close($ch);
			
		update_option( 'wpcs_aws_lightsail_api_response', $data );
			
		$response['response'] = $data;
		$response['body'] = $body;
			
		return $response;
			
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
		$headers = [ 'API-Key:' . self::$api_token ];
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
			
		update_option( 'wpcs_aws_lightsail_api_response', $data );
			
		$response['response'] = $data;
		$response['body'] = $body;
			
		return $response;
			
	}

}