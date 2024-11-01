<?php

class AWS_Lightsail_Async_Request extends WP_Cloud_Server_Async_Request {

	/**
	 * @var string
	 */
	protected $action = 'aws_lightsail_request';

	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	protected function handle() {

		//$message = $this->get_message( $_POST['name'] );

		$api	= new WP_Cloud_Server_AWS_Lightsail_API();
		
		//delete_option( 'wpcs_aws_lightsail_api_data');
	
		$data	= get_option( 'wpcs_aws_lightsail_api_data', array() );

		$api_data = array(
			'includeAvailabilityZones'						=> false,
			'includeRelationalDatabaseAvailabilityZones' 	=> false,
		);

		/* RunCloud API Data */
		$regions = $api->call_api( 'GetRegions', $api_data, false, 0, 'POST', false, 'get_regions' );
				
		if ( isset( $regions['regions'] ) ) {
			$data['regions'] = $regions['regions'];
		}
			
		foreach ( $regions['regions'] as $key => $zone ) {
			$instances = $api->call_api( 'GetInstances', $api_data, false, 0, 'POST', false, 'get_servers', $zone['name'] );
			//update_option( 'lightsail_list_servers_instances', $api_response );
			if ( !empty( $instances['instances'] ) ) {
				$data['instances'][$zone['name']] = $instances['instances'];
			} else {
				$data['instances'][$zone['name']] = array();
			}
		}

		update_option( 'wpcs_aws_lightsail_api_data', $data );

		//error_log( "AWS Async Request" );
	}
}