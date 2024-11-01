<?php

class WP_Example_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'example_process';

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

		$api	= new WP_Cloud_Server_RunCloud_API();
		$data	= get_option( 'wpcs_runcloud_api_data', array() );

		switch ($item) {
			case 'server_list':

				/* RunCloud API Data */
				$servers = $api->call_api( 'servers', null, false, 900, 'GET', false, 'runcloud_server_list' );
				
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

			case 'web_apps':

				if ( isset( $server_list ) && is_array( $server_list ) ) {

					foreach ( $server_list as $server_id => $server_name ) {

						/* RunCloud API Data */
						$web_apps = $api->call_api( "servers/{$server_id}/webapps", null, false, 900, 'GET', false, 'runcloud_server_list' );
						
						if ( isset( $web_apps['data'] ) ) {
							foreach ( $web_apps['data'] as $key => $web_app ) {
								if ( isset( $web_app['id'] ) ) {
									$data['web_apps']['data'][$server_id][$web_app['id']] = $web_app;
								}
							}
						}
					}
				}

				break;

				case 'sys_users':

					if ( isset( $server_list ) && is_array( $server_list ) ) {
	
						foreach ( $server_list as $server_id => $server_name ) {
	
							/* RunCloud API Data */
							$sys_users = $api->call_api( "servers/{$server_id}/users", null, false, 900, 'GET', false, 'runcloud_server_list' );
							
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
		}
			$data['server_list'] = $server_list;
			update_option( 'wpcs_runcloud_api_data', $data );

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