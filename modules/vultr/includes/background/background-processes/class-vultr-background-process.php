<?php

class Vultr_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'vultr_background_process';

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

		$api = new WP_Cloud_Server_Vultr_API();
	
		$data	= get_option( 'wpcs_vultr_api_data', array() );

		switch ($item) {
			case 'regions':
				
				$regions = $api->call_api( 'regions/list', null, false, 900, 'GET', false, 'vultr_region_list' );
				if ( !empty( $regions ) ) {
					$data['regions'] = $regions; $api->call_api( 'plans/list', null, false, 900, 'GET', false, 'vultr_plan_list' );
				}

			break;

			case 'plans':
				
				$plans = $api->call_api( 'plans/list', null, false, 900, 'GET', false, 'vultr_plan_list' );
				if ( !empty( $plans ) ) {
					$data['plans'] = $plans;
				}

			break;

			case 'images':
				
				$images = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'vultr_os_list' );
				if ( !empty( $images ) ) {
					$data['images'] = $images;
				}

			break;

			case 'servers':
				
				$servers = $api->call_api( 'server/list', null, false, 0, 'GET', false, 'get_server' );
				if ( !empty( $servers ) ) {
					$data['servers'] = $servers;
					$server_list = $servers;		
				}

			break;

			case 'sshkey':
				
				$ssh_keys = $api->call_api( 'sshkey/list', null, false, 900, 'GET', false, 'list_ssh_keys' );
				if ( !empty( $ssh_keys ) ) {
					$data['sshkeys'] = $ssh_keys;
				}

			break;

		}

		update_option( 'wpcs_vultr_api_data', $data );

		//error_log( "Run the Vultr API for {$item}" );

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