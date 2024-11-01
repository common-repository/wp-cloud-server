<?php

class Cloudways_Process extends WP_Cloud_Server_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'cloudways_background_process';

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

		$api = new WP_Cloud_Server_Cloudways_API();
	
		$data	= get_option( 'wpcs_cloudways_api_data', array() );

		switch ($item) {
			case 'providers':
				
				$providers = $api->call_api( 'providers', null, false, 0, 'GET', false, 'cloudways_providers_list' );
				if ( !empty( $providers ) ) {
					$data = array_merge( $data, $providers );
				}

			break;

			case 'regions':
				
				$regions = $api->call_api( 'regions', null, false, 0, 'GET', false, 'cloudways_zone_list' );
				if ( !empty( $regions ) ) {
					$data = array_merge( $data, $regions );
				}

			break;

			case 'apps':
				
				$apps = $api->call_api( 'apps', null, false, 0, 'GET', false, 'cloudways_zone_list' );
				if ( !empty( $apps ) ) {
					$data = array_merge( $data, $apps);
				}

			break;

			case 'packages':
				
				$packages = $api->call_api( 'packages', null, false, 0, 'GET', false, 'cloudways_zone_list' );
				if ( !empty( $packages ) ) {
					$data = array_merge( $data, $packages);
				}

			break;

			case 'sizes':
				
				$sizes = $api->call_api( 'server_sizes', null, false, 0, 'GET', false, 'cloudways_plan_list' );
				if ( !empty( $sizes ) ) {
					$data = array_merge( $data, $sizes);
				}

			break;

			case 'servers':
				
				$servers = $api->call_api( 'server', null, false, 900, 'GET', false, 'cloudways_server_list' );
				if ( !empty( $servers ) ) {
					$data = array_merge( $data, $servers);
				}

			break;

			case 'projects':
				
				$projects = $api->call_api( 'project', null, false, 900, 'GET', false, 'cloudways_project_list' );
				if ( !empty( $projects ) ) {
					$data = array_merge( $data, $projects);
				}

			break;

		}

		update_option( 'wpcs_cloudways_api_data', $data );

		//error_log( "Run the Cloudways API for {$item}" );

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