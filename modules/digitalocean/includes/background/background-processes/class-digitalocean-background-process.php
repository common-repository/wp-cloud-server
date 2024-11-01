<?php

class DigitalOcean_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'digitalocean_background_process';

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

		$api = new WP_Cloud_Server_DigitalOcean_API();
	
		$data	= get_option( 'wpcs_digitalocean_api_data', array() );

		switch ($item) {
			case 'droplets':
				
					$droplets = call_user_func( "wpcs_digitalocean_cloud_server_api", 'DigitalOcean', 'droplets', null, false, 900, 'GET', false );
					if ( !empty( $droplets['droplets'] ) ) {
						$data['droplets']			= $droplets['droplets']; 
					}
	
				break;

			case 'sshkeys':

				$ssh_keys = call_user_func( "wpcs_digitalocean_cloud_server_api", null, 'account/keys', null, false, 0, 'GET', false, 'list_ssh_keys' );
					if ( !empty( $ssh_keys['ssh_keys'] ) ) {
						$data['ssh_keys'] = $ssh_keys['ssh_keys']; 
					}
				
				break;

			case 'volumes':

				$volumes = call_user_func( "wpcs_digitalocean_cloud_server_api", null, 'volumes', null, false, 0, 'GET', false, 'list_volumes' );
				if ( !empty( $volumes['volumes'] ) ) {
					$data['volumes'] = $volumes['volumes']; 
				}
					
				break;

		}

		update_option( 'wpcs_digitalocean_api_data', $data );

		//error_log( "Run the DigitalOcean API for {$item}" );

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