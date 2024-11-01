<?php

class ServerPilot_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'serverpilot_background_process';

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

		$api = new WP_Cloud_Server_ServerPilot_API();
	
		$data	= get_option( 'wpcs_serverpilot_api_data', array() );

		switch ($item) {
			case 'servers':
				
				$servers = $api->call_api( 'servers', null, false, 900, 'GET' );
				if ( !empty( $servers ) ) {
					$data['servers'] = $servers;
				}

			break;

			case 'apps':
				
				$apps = $api->call_api( 'apps', null, false, 900, 'GET' );
				if ( !empty( $apps ) ) {
					$data['apps'] = $apps;
				}

				// Save the SSL Status
				if ( ! empty( $data['apps']['data'] ) ) { 
					foreach ( $data['apps']['data'] as $key => $app ) {
						$data['apps']['data'][$key]['ssl_status'] = wpcs_sp_api_ssl_status( $app['id'], $app['domains'][0] );
					}
				}

			break;

		}

		update_option( 'wpcs_serverpilot_api_data', $data );

		//error_log( "Run the ServerPilot API for {$item}" );

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