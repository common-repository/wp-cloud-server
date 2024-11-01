<?php

class AWS_Lightsail_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'aws_lightsail_background_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {

		static $regions;
		static $server_list;

		$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
		$data	= get_option( 'wpcs_aws_lightsail_api_data', array() );

		$api_data = array(
			'includeAvailabilityZones'						=> false,
			'includeRelationalDatabaseAvailabilityZones' 	=> false,
		);

		switch ($item) {
			case 'regions':

				/* RunCloud API Data */
				$regions = $api->call_api( 'GetRegions', $api_data, false, 0, 'POST', false, 'get_regions' );
				
				if ( isset( $regions['regions'] ) ) {
					$data['regions'] = $regions['regions'];
				}

				break;

			case 'instances':
				
				if ( isset( $regions['regions'] ) ) {
					foreach ( $regions['regions'] as $zone ) {
						$instances = $api->call_api( 'GetInstances', $api_data, false, 0, 'POST', false, 'get_servers', $zone['name'] );
						//update_option( 'lightsail_list_servers_instances', $api_response );
						if ( !empty( $instances['instances'] ) ) {
							$data['instances'][$zone['name']]	= $instances['instances'];
							$server_list[$zone['name']] 		= $instances['instances']; 
						}
					}
				}
	
				break;

			case 'bundles':
					
				$bundles = $api->call_api( 'GetBundles', null, false, 900, 'POST', false, 'aws-lightsail_plan_list' );
					if ( !empty( $bundles['bundles'] ) ) {
						$data['bundles'] = $bundles['bundles']; 
					}
		
				break;

			case 'blueprints':

				$api_data = array(
					'includeInactive' => false,
				);
					
				$blueprints = $api->call_api( 'GetBlueprints', $api_data, false, 900, 'POST', false, 'aws_lightsail_blueprints' );
					if ( !empty( $blueprints['blueprints'] ) ) {
						$data['blueprints'] = $blueprints['blueprints']; 
					}
			
				break;

			case 'sshkeys':

				if ( isset( $regions['regions'] ) ) {
					foreach ( $regions['regions'] as $zone ) {
						$keypairs = $api->call_api( 'GetKeyPairs', null, false, 900, 'POST', false, 'list_ssh_keys', $zone['name'] );
						if ( !empty( $keypairs['keyPairs'] ) ) {
							$data['keyPairs'] = $keypairs['keyPairs']; 
						}
					}
				}
				
				break;

		}

		update_option( 'wpcs_aws_lightsail_api_data', $data );

		//error_log( "Run the AWS API for {$item}" );

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}