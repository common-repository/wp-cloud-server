<?php

class UpCloud_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'upcloud_background_process';

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

		$api = new WP_Cloud_Server_UpCloud_API();
	
		$data	= get_option( 'wpcs_upcloud_api_data', array() );

		switch ($item) {
			case 'zone':
				
				$zones = $api->call_api( 'zone', null, false, 900, 'GET', false, 'upcloud_zone_list' );
				if ( !empty( $zones ) ) {
					$data = array_merge( $data, $zones);
				}

			break;

			case 'plans':
				
				$plans = $api->call_api( 'plan', null, false, 900, 'GET', false, 'upcloud_plan_list' );
				if ( !empty( $plans ) ) {
					$data = array_merge( $data, $plans);
				}

			break;

			case 'storage':
				
				$storage = $api->call_api( 'storage/template', null, false, 900, 'GET', false, 'upcloud_os_list' );
				if ( !empty( $storage ) ) {
					$data = array_merge( $data, $storage);
				}

			break;

			case 'servers':
				
				$servers = $api->call_api( 'server', null, false, 900, 'GET', false, 'get_servers' );
				if ( !empty( $servers ) ) {
					$data = array_merge( $data, $servers);
				}

			break;

			case 'prices':
				
				$prices = $api->call_api( 'price', null, false, 900, 'GET', false, 'get_prices' );
				if ( !empty( $prices ) ) {
					$data = array_merge( $data, $prices);
				}

			break;

		}

		update_option( 'wpcs_upcloud_api_data', $data );

		//error_log( "Run the UpCloud API for {$item}" );

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