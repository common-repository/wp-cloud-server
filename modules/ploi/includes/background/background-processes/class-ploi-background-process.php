<?php

class WP_Ploi_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'ploi_process';

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

		static $server_list;

		$api	= new WP_Cloud_Server_Ploi_API();
		$data	= get_option( 'wpcs_ploi_api_data', array() );

		switch ($item) {
			case 'server_list':

				/* Ploi API Data */
				$servers = $api->call_api( 'servers', null, false, 900, 'GET', false, 'ploi_server_list' );
				
				if ( isset( $servers['data'] ) ) {
					foreach ( $servers['data'] as $key => $server ) {
						if ( isset( $server['id'] ) ) {
							//$data['servers'][$server['id']] = $server;
							$server_list[$server['id']] = $server['name'];
						}
					}
					$data['servers']['data'] = $servers['data'];
				}

				break;

			case 'credentials':

				if ( isset( $server_list ) && is_array( $server_list ) ) {

					foreach ( $server_list as $server_id => $server_name ) {

						/* Ploi API Data */
						$web_apps = $api->call_api( "user/server-providers", null, false, 900, 'GET', false, 'ploi_server_list' );
						
						$data['credentials'] = $web_apps['data'];
					}
				}

				break;

			case 'sys_users':

				if ( isset( $server_list ) && is_array( $server_list ) ) {
	
					foreach ( $server_list as $server_id => $server_name ) {
	
						/* Ploi API Data */
						$sys_users = $api->call_api( "servers/{$server_id}/users", null, false, 900, 'GET', false, 'ploi_server_list' );
							
						if ( isset( $sys_users['data'] ) ) {
							foreach ( $sys_users['data'] as $key => $sys_user ) {
								if ( isset( $sys_user['id'] ) ) {
									$data['sys_users']['data'][$sys_user['id']] = $sys_user;
								}
							}
						}
					}
				}
	
				break;

				case 'sites':

					if ( isset( $server_list ) && is_array( $server_list ) ) {
		
						foreach ( $server_list as $server_id => $server_name ) {
		
							/* Ploi API Data */
							$sites = $api->call_api( "servers/{$server_id}/sites", null, false, 900, 'GET', false, 'ploi_sites_list' );
								
							if ( isset( $sites['data'] ) ) {
								foreach ( $sites['data'] as $key => $site ) {
									if ( isset( $site['id'] ) ) {
										$data['sites/list'][$server_id]['data'] = $site;
									}
								}
							}
						}
					}
		
					break;
		}
			$data['server_list'] = $server_list;
			update_option( 'wpcs_ploi_api_data', $data );

			//error_log( "Run the API for {$item}" );

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