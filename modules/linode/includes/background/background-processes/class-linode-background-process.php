<?php

class Linode_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'linode_background_process';

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

		$api = new WP_Cloud_Server_Linode_API();
	
		$data	= get_option( 'wpcs_linode_api_data', array() );

		switch ($item) {
			case 'regions':
				
				$instances = $api->call_api( 'regions', null, false, 900, 'GET', false, 'linode_region_list' );
				if ( !empty( $instances['data'] ) ) {
					$data['regions'] = $instances['data'];
				}

			break;

			case 'types':
				
				$types = $api->call_api( 'linode/types', null, false, 900, 'GET', false, 'linode_plan_list' );
				if ( !empty( $types['data'] ) ) {
					$data['types'] = $types['data'];
				}

			break;

			case 'images':
				
				$images = $api->call_api( 'images', null, false, 900, 'GET', false, 'linode_os_list' );
				if ( !empty( $images['data'] ) ) {
					$data['images'] = $images['data'];
				}

			break;

			case 'instances':
				
					$instances = $api->call_api( 'linode/instances', null, false, 0, 'GET', false, 'get_servers' );
					if ( !empty( $instances['data'] ) ) {
						$data['instances'] = $instances['data']; 
					}
	
				break;

		}

		update_option( 'wpcs_linode_api_data', $data );

		//error_log( "Run the Linode API for {$item}" );

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