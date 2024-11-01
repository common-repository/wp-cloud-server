<?php
/**
 * WP Cloud Server - Ploi Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Performs Server API Calls
 *
 * @since  1.0
 *
 * @return data Server API Data
 */
function wpcs_ploi_api_user_request( $request, $args = null ) {

	$provider 			= ( isset( $args['provider'] ) ) ? $args['provider'] : null;
	$command 			= wpcs_ploi_api_definition( 'User', $request );
	$command['request'] = str_replace( "{{provider}}", $provider, $command['request'] );

	if ( is_array( $command ) ) {

		$request	= $command['request'];
		$query		= $command['query'];

		$api_data	= ( isset( $args['api_data'] ) ) ? $args['api_data'] : null;

		$stored_data		= get_option( 'wpcs_ploi_api_data' );
		$stored_data		= ( isset( $data[$request]['data'] ) ) ? $data[$request]['data'] : false;

		if ( !$stored_data ) {
			// Fetch the server data using the API
			$response		= WPCS_Ploi()->api->call_api( $request, $api_data, false, 900, $query, false, 'ploi_credentials_list' );;
			$new_data[$request]	= $response;

			update_option( 'wpcs_ploi_api_data', $new_data);

			if ( isset( $new_data[$request]['data'] ) ) {
				$stored_data = $new_data[$request]['data'];
			}
		}
	}
	
	return ( $stored_data ) ? $stored_data : false;
}

/**
 * Performs Server API Calls
 *
 * @since  1.0
 *
 * @return data Server API Data
 */
function wpcs_ploi_api_server_request( $request, $args = null ) {

	$server_id			= ( isset( $args['server_id'] ) ) ? $args['server_id'] : null;
	$command			= wpcs_ploi_api_definition( 'Servers', $request );
	$command['request']	= isset( $server_id ) ? str_replace( "{{server_id}}", $server_id, $command['request'] ) : $command['request'];

	if ( is_array( $command ) ) {

		$request	= $command['request'];
		$query		= $command['query'];

		$api_data	= ( isset( $args['api_data'] ) ) ? $args['api_data'] : null;

		$response	= WPCS_Ploi()->api->call_api( $request, $api_data, false, 900, $query, false, 'ploi_server_request' );
	
		if ( isset( $response['data'] ) ) {
			$data = $response['data'];
		}

	}
	
	return ( isset( $data ) ) ? $data : false;
}

/**
 * Performs Sites API Calls
 *
 * @since  1.0
 *
 * @return data Site API Data
 */
function wpcs_ploi_api_sites_request( $model, $args = null) {

	$server_id			= ( isset( $args['server_id'] ) ) ? $args['server_id'] : null;
	$site_id			= ( isset( $args['site_id'] ) ) ? $args['site_id'] : null;

	$command			= wpcs_ploi_api_definition( 'Sites', $model );
	$command['request']	= isset( $server_id ) ? str_replace( "{{server_id}}", $server_id, $command['request'] ) : $command['request'];
	$command['request']	= isset( $site_id ) ? str_replace( "{{site_id}}", $site_id, $command['request'] ) : $command['request'];

	if ( is_array( $command ) ) {

		$request	= $command['request'];
		$query		= $command['query'];

		$api_data	= ( isset( $args['api_data'] ) ) ? $args['api_data'] : null;

		if ( $query !== 'GET') {
			$response = WPCS_Ploi()->api->call_api( $request, $api_data, false, 0, $query, false, 'ploi_site_request' );
			return ( isset( $response['data'] ) ) ? $response['data'] : $response;
		}

		$data	= get_option( 'wpcs_ploi_api_data' );
		//unset($data['sites/list']);
		$test	= ( isset( $data[$model]['data'] ) ) ? true : false;

		if ( !$test ) {
			// Fetch the server data using the API
			$response		= WPCS_Ploi()->api->call_api( $request, $api_data, false, 0, $query, false, 'ploi_site_list' );	
			$data[$model][$server_id]	= $response;
			update_option( 'wpcs_ploi_api_data', $data);

		}
	}
	
	return ( isset( $data[$model][$server_id]['data'] ) ) ? $data[$model][$server_id]['data'] : false;
}

/**
 * Performs Sites API Calls
 *
 * @since  1.0
 *
 * @return data Site API Data
 */
function wpcs_ploi_api_web_templates_request( $request, $args = null) {

	$command = wpcs_ploi_api_definition( 'Templates', $request );

	if ( is_array( $command ) ) {

		$request	= $command['request'];
		$query		= $command['query'];

		$api_data	= ( isset( $args['api_data'] ) ) ? $args['api_data'] : null;

		$response	= WPCS_Ploi()->api->call_api( $request, $api_data, false, 900, $query, false, 'ploi_sites_request' );
	
		if ( isset( $response['data'] ) ) {
			$data = $response['data'];
		}

	}
	
	return ( isset( $data ) ) ? $data : false;
}

/**
 * Performs Sites API Calls
 *
 * @since  1.0
 *
 * @return data Site API Data
 */
function wpcs_ploi_api_system_users_request( $request, $args = null ) {

	$server_id			= ( isset( $args['server_id'] ) ) ? $args['server_id'] : null;

	$command			= wpcs_ploi_api_definition( 'SystemUsers', $request );
	$command['request']	= isset( $server_id ) ? str_replace( "{{server_id}}", $server_id, $command['request'] ) : $command['request'];
	
	if ( is_array( $command ) ) {

		$request	= $command['request'];
		$query		= $command['query'];

		$api_data	= ( isset( $args['api_data'] ) ) ? $args['api_data'] : null;

		$response	= WPCS_Ploi()->api->call_api( $request, $api_data, false, 900, $query, false, 'ploi_system_users_request' );
	
		if ( isset( $response['data'] ) ) {
			$data = $response['data'];
		}

	}
	
	return ( isset( $data ) ) ? $data : false;
}

/**
 * Retrieves a list of Credentials
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_ploi_credentials_list() {
	
	$class_name = "WP_Cloud_Server_Ploi_API";
	
	// Create instance of the Ploi API
	$api = new $class_name();

	$credentials = wpcs_ploi_api_user_request( 'user/server-providers' );
	
	//$credentials = $api->call_api( 'user/server-providers', null, false, 900, 'GET', false, 'ploi_credentials_list' );
	
	if ( isset( $credentials ) ) {
		foreach ( $credentials as $key => $credential ) {
			$data[$credential['label']] = $credential['name'];
		}
	}
	
	return ( isset( $data ) ) ? $data : false;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_ploi_size_list( $selected_region = null ) {
	
	return [
            '1GB' => 1,
            '2GB' => 2,
            '4GB' => 4,
            '8GB' => 8,
            '16GB' => 16,
            'm-16GB' => 'm16',
            '32GB' => 32,
            'm-32GB' => 'm32',
            '64GB' => 64,
            'm-64GB' => 'm-64',
        ];
}

/**
 * Retrieves a list of Database types.
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_ploi_database_list() {
	
	return [
			'-- No Database Installed --' => 'none',
            'MySQL' => 'mysql',
            'MariaDB' => 'mariadb',
            'PostGreSQL' => 'postgresql',
        ];
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_ploi_regions_list( $selected_region = null ) {
	
	$class_name = "WP_Cloud_Server_Ploi_API";
	
	// Create instance of the Ploi API
	$api = new $class_name();
	
	$regions = wpcs_ploi_api_user_request( 'user/server-providers' );

	if ( isset( $selected_region ) ) {
		foreach ( $regions['provider']['regions'] as $key => $region ) {
			if ( $selected_region == $key ) {
				return $region;
			}
		}
	}
	
	return $regions['provider']['regions'];
}

/**
 * Retrieves a list of Available Plans by Zone
 *
 * @since  1.0
 *
 * @return regions List of available plans by zone
 */
function wpcs_ploi_availability_list( $region ) {
	
	// Create instance of the Ploi API
	$api = new WP_Cloud_Server_Ploi_API();
	
	$model = ( 'userselected' == $region ) ? "plan" : "plan" ;
	
	$available_plans = $api->call_api( $model, null, false, 900, 'GET', false, 'ploi_plan_availability_list' );
	
	$available_plans = ( 'userselected' == $region ) ? array_keys($available_plans) : $available_plans ;
	
	$plans = $api->call_api( "plan", null, false, 900, 'GET', false, 'ploi_plan_list' );
	
	//if ( is_array( $plans ) ) {
	foreach ( $plans['plans']['plan'] as $key => $plan ) {
			
		$ram  		= wpcs_ploi_convert_mb_to_gb($plan['memory_amount']);
		//$cost 	= str_replace('.00', '', $plan['price_per_month']);
		$cost		= 0;
		$plan_name 	= "{$plan['core_number']} CPU, {$ram}GB, {$plan['storage_size']}GB SSD";
		$plan_cost	= "(\${$cost}/month)";
			
		$available['server'][$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
	}
	//}

	update_option( 'wpcs_ploi', $plans );
	
	return $available;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_ploi_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_Ploi_API";

	$images = array(
		"Ubuntu 20.04 x64"							=>	"Ubuntu Server 20.04 LTS (Focal Fossa)",
		"Ubuntu 18.04 x64"							=>	"Ubuntu Server 18.04 LTS (Bionic Beaver)",
		"Ubuntu Server 20.04 LTS (Focal Fossa)"		=>	"Ubuntu Server 20.04 LTS (Focal Fossa)",
		"Ubuntu Server 18.04 LTS (Bionic Beaver)"	=>	"Ubuntu Server 18.04 LTS (Bionic Beaver)",
		"Ubuntu Server 16.04 LTS (Xenial Xerus)"	=>	"Ubuntu Server 16.04 LTS (Xenial Xerus)",
		"Debian GNU/Linux 10 (Buster)"				=>	"Debian GNU/Linux 10 (Buster)",
		"Debian GNU/Linux 9 (Stretch)"				=>	"Debian GNU/Linux 9 (Stretch)",
		"CentOS 8"									=>	"CentOS 8",
		"CentOS 7"									=>	"CentOS 7",
		"CentOS 6.10"								=>	"CentOS 6.10",
		"Plesk Obsidian"							=>	"Plesk Obsidian",
		"Windows Server 2016 Datacenter"			=>	"Windows Server 2016 Datacenter",
		"Windows Server 2016 Standard"				=>	"Windows Server 2016 Standard",
		"Windows Server 2019 Datacenter"			=>	"Windows Server 2019 Datacenter",
		"Windows Server 2019 Standard"				=>	"Windows Server 2019 Standard",
	);
	
	// Create instance of the Ploi API
	$api = new $class_name();
	
	$plans = $api->call_api( 'storage/template', null, false, 900, 'GET', false, 'ploi_os_list' );
	
	update_option( 'ploi_os_list', $image );
	
	if ( isset( $image ) ) {
		foreach ( $plans['storages']['storage'] as $key => $plan ) {
			if ( $plan['title'] == $images[$image] ) {
				return $plan['uuid'];
			}
		}
	}
	
	foreach ( $plans['storages']['storage'] as $key => $plan ) {
		if ( !in_array( $plan['title'], ['Custom','Snapshot', 'Backup','Application'], true ) ) {
			$plan_list[$plan['uuid']] =  array( 'name' => $plan['title'] );
		}
	}
	
	return $plan_list;
}

/**
 * Retrieves a list of Plans
 *
 * @since  1.0
 *
 * @return plans List of available plans
 */
function wpcs_ploi_plans_list() {
	
	$class_name = "WP_Cloud_Server_Ploi_API";
	
	// Create instance of the Ploi API
	$api = new $class_name();
	
	$plans = $api->call_api( 'plan', null, false, 900, 'GET', false, 'ploi_plan_list' );
	
	return $plans;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_ploi_managed_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_Ploi_API";
	
	// Create instance of the Ploi API
	$api = new $class_name();
	
	$plans = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'ploi_os_list' );
	
	if ( isset( $image ) ) {
		foreach ( $plans as $plan ) {
			if ( $plan['name'] == $image ) {
				return $plan['OSID'];
			}
		}	
	}
	
	$plans = array(
		'ubuntu-20-04-x64'	=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'ubuntu-18-04-x64'	=>	array( 'name' => 'Ubuntu 18.04 x64'),
	);
	
	return $plans;
}

/**
 * Ploi API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_ploi_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	// Exit if checking DigitalOcean Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$api = new WP_Cloud_Server_Ploi_API();
	
	$model = ( 'droplets' == $model ) ? 'server' : $model;
	
	if ( isset( $api_data ) && ( 'POST' == $request ) ) {
		$app_data = $api_data['custom_settings'];	
	}
	
	if ( isset( $api_data['user_data'] )  ) {
		$app_data['user_data'] = $api_data['user_data'];	
	}
	
	update_option( 'wpcs_ploi_sp_install_script', $app_data );

	$host_name	= ( isset( $api_data['hostname']) ) ? $app_data['hostname'] : $app_data['label'];

	$server_data = array( "server" => array(
        "zone"				=>	$app_data['DCID'],
        "title"				=>	$app_data['label'],
        "hostname"			=>	$host_name,
		"plan"				=>	$app_data['VPSPLANID'],
		"user_data"			=>	$app_data['user_data'],
        "storage_devices"	=>	array(
            	"storage_device" =>	array(
                    	"action"	=>	"clone",
                    	"storage"	=>	$app_data['OSID'],
                    	"title"		=>	$app_data['label'],
                    	"size"		=>	25,
                    	"tier"		=>	"maxiops"
                	)
			)
       	)
	);
	
	$api_response = $api->call_api( $model, $server_data, $cache, $cache_lifetime, $request, $enable_response, $function );
	
	return $api_response;

}

/**
 * Retrieves a list of Region Names
 *
 * @since  1.0
 *
 * @return regions List of all region names
 */
function wpcs_ploi_cloud_regions() {
	
	$regions = wpcs_ploi_regions_list();
		
	if ( !empty( $regions ) ) {
		foreach ( $regions as $region ) {
			$list[ $region['DCID'] ] = $region['name'];
		}
	}
	return $list;
}

/**
 * Returns API Response Valid Status
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_ploi_api_response_valid( $response ) {

	return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_ploi_server_complete( $queued_server, $response, $host_name, $server_location ) {

	if ( '' == $queued_server['SUBID'] ) {

		$status				= 'Error';
		$message			= 'Error Creating Server ( ' . $host_name . ' )';

		wpcs_ploi_log_event( 'Ploi', $status, $message );

		return false;

	}

	$complete = false;

	$x					= 1;
	$args['server_id']	= $queued_server['SUBID'];
	$status				= "in-progress";
			
	// Wait for the Server to Complete
	while( ( "active" !== $status ) && ( $x <= 20 ) ) {

		$actions		= wpcs_ploi_api_server_request( 'servers/get', $args );
		$status			= $actions['status'];

   	 	$x++;
		$debug['counter']['status']	= $status;
		$debug['counter']['count']	= $x;
	}
			
	// Wait 1 second
	sleep(1);
			
	// Read Server Information
	$server = wpcs_ploi_api_server_request( 'servers/get', $args );

	// Send Create Site Request to Ploi API
	if ( 'active' == $server['status'] ) {

		$template_type = $queued_server['template_name'];

		if ( 'ploi_server_template' == $template_type ) {

			$status				= 'Success';
			$message			= 'New Server Created ( ' . $host_name . ' )';

			wpcs_ploi_log_event( 'Ploi', $status, $message );

		}

		$web_app_on_server	= $queued_server['web_app_on_server'];
		$server_id			= $server['id'];

		$complete			= ( ( 'ploi_site_template' == $template_type ) || ( $web_app_on_server ) ) ? false : true;

		// If template is for a site only then we create it here
		if ( ( 'ploi_site_template' == $template_type ) || ( $web_app_on_server ) ) {

			$domain_name				= $queued_server['domain_name'];
			$project_directory			= $queued_server['project_root'];
			$web_directory				= $queued_server['web_directory'];
			$system_user				= $queued_server['system_user'];
			$web_template				= $queued_server['webserver_template'];
			$web_application 			= $queued_server['web_app'];
			$web_application_type		= $queued_server['web_app_type'];
			$enable_ssl					= $queued_server['enable_ssl'];

			$args['api_data'] = array(
				"root_domain"			=> $domain_name,
				"project_root"			=> $project_directory,
				"web_directory"			=> $web_directory,
				"system_user"			=> $system_user,
				"webserver_template"	=> $web_template,
			);

			$args['server_id'] 			= $server_id;
			$response					= wpcs_ploi_api_sites_request( 'sites/create', $args );
			$site_id					= $response['id'];
			$args['site_id'] 			= $site_id;

			unset( $args['api_data'] );

			// Wait for the GIT install to Complete
			$x							= 1;
			$status						= 'installng-repository';

			while ( ( "active" !== $status ) && ( $x <= 30 ) ) {

				$actions		= wpcs_ploi_api_sites_request( 'sites/get', $args );
				$status			= $actions['status'];

   	 			$x++;
				$debug['sitecounter']['status']	= $status;
				$debug['sitecounter']['count']	= $x;
			}

			$debug['site_response']		= $actions;

			$status						= 'Success';
			$message					= 'New Site Created ( ' . $domain_name . ' )';

			wpcs_ploi_log_event( 'Ploi', $status, $message );

			if ( ( 'no-application' !== $web_application ) && ( 'app' == $web_application_type ) ) {

				$args['site_id'] 		= $site_id;
				$args['api_data']		= ( 'wordpress' == $web_application ) ? array( 'create_database' => true ) : null;
				$response				= wpcs_ploi_api_sites_request( "sites/install/{$web_application}", $args );

				$status					= 'Success';
				$message				= 'WordPress Installed ( ' . $domain_name . ' )';

				wpcs_ploi_log_event( 'Ploi', $status, $message );

				$complete					= ( $enable_ssl ) ? false : true;
			}

			if ( 'git' == $web_application_type ) {

				$owner					= wpcs_github_repo_owner();
				$args['site_id'] 		= $site_id;

				// Wait 1 second
				sleep(1);

				// Install the GitHub Repository
				$args['api_data']			= array(
						"provider"			=> 'github',
						"branch"			=> 'main',
						"name"				=> "{$owner}/{$web_application}",
				);

				$response					= wpcs_ploi_api_sites_request( "sites/install/git", $args );

				$debug['git_response']		= $response;
				$debug['git_args']			= $args;

				unset( $args['api_data'] );

				// Wait for the GIT install to Complete
				$x							= 1;
				$status						= 'installng-repository';

				while ( ( "active" !== $status ) && ( $x <= 30 ) ) {

					$actions		= wpcs_ploi_api_sites_request( 'sites/get', $args );
					$status			= $actions['status'];

   	 				$x++;
					$debug['gitcounter']['status']	= $status;
					$debug['gitcounter']['count']	= $x;
				}

				// Wait 1 second
				sleep(1);

				// Update the deploy script
				$args['api_data']		= array(
					"deploy_script"		=> "cd /home/ploi/{$domain_name}",
				);

				$actions		= wpcs_ploi_api_sites_request( 'sites/install/git/deploy/update/script', $args );				

				$debug['script_response']	= $actions;
				$debug['script_args']		= $args;

				// Wait 1 second
				sleep(1);

				// Deploys the site, remember that a deploy will not be done if there is no repository installed. 
				unset( $args['api_data'] );

				$response					= wpcs_ploi_api_sites_request( "sites/install/git/deploy", $args );

				$debug['deploy_response']	= $response;
				$debug['deploy_args']		= $args;

				$complete					= ( $enable_ssl ) ? false : true;
			}

			// Wait for the Site Deploy to Complete
			$x							= 1;
			$status						= 'deploying';
			
			while ( ( "active" !== $status ) && ( $x <= 30 ) ) {
			
				$actions		= wpcs_ploi_api_sites_request( 'sites/get', $args );
				$status			= $actions['status'];
			
				$x++;
				$debug['deploycounter']['status']	= $status;
				$debug['deploycounter']['count']	= $x;
			}
			
			$debug['deploy_site_response']		= $actions;

			// Install a Let's Encrypt SSL Certifcate if enabled
			if ( $enable_ssl ) {

				$args['api_data']	= array(
					"certificate"	=> $domain_name,
					"type"			=> 'letsencrypt',
				);

				$response			= wpcs_ploi_api_sites_request( "sites/certificates/create", $args );

				$debug['ssl_response']	= $response;
				$debug['ssl_args']		= $args;

				$complete = true;
			}

			update_option( 'wpcs_complete_server_queue_debug', $debug );
		}
	}
	
	// Save the server details for future use
	$server_data = array(
		"id"			=>	$server['id'],
		"name"			=>	$server['name'],
		"status"		=>	$server['status'],
		"domain_name"	=>	( isset( $domain_name ) ) ? $domain_name : '',
		"location"		=>	$server_location,
		"slug"			=>	sanitize_title($server['id']),
		"ram"			=>	'',
		"vcpus"			=>	'',
		"disk"			=>	'',
		"size"			=>	'',
		"os"			=> 	'',
		"server_type"	=>	$server['type'],
		"ip_address"	=>	$server['ip_address'],
		"php_version"	=>	$server['php_version'],
		"mysql_version"	=>	$server['mysql_version'],
		"sites_count"	=>	$server['sites_count'],
		"created_at"	=>	$server['created_at'],
		"monitoring"	=>  $server['monitoring'],
		"completed"		=>	$complete,
	);
	
	return $server_data;
}

/**
 * Call to API for Health Check
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_ploi_call_api_health_check( ) {

	$api = new WP_Cloud_Server_Ploi_API();

	$api_response = $api->call_api( 'user', null, false, 0, 'GET', true, 'api_health' );

	return $api_response;

}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_ploi_call_api_list_servers( $enable_response = false ) {

	$api = new WP_Cloud_Server_Ploi_API();

	$api_response = $api->call_api( 'servers', null, false, 0, 'GET', $enable_response, 'get_servers' );

	if ( isset( $api_response['data'] ) ) {
		foreach ( $api_response['data'] as $key => $server ) {
			$server_list[$key] =  $server;
		}
	}

	return ( isset( $api_response['data'] ) && isset( $server_list ) ) ? $server_list : false;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_ploi_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Ploi_API();

	$api_response = $api->call_api( 'server/list?SUBID=' . $server_sub_id, null, false, 0, 'GET', $enable_response, 'server_status' );

	return $api_response;

}

/**
 * Call to API to Create New Server
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_ploi_call_api_create_server( $api_data = null, $enable_response = false ) {

	$server_data = array( "server" => array(
        "zone"				=>	$api_data['region'],
        "title"				=>	$api_data['name'],
        "hostname"			=>	$api_data['name'],
        "plan"				=>	$api_data['size'],
        "storage_devices"	=>	array(
            	"storage_device" =>	array(
                    	"action"	=>	"clone",
                    	"storage"	=>	$api_data['image'],
                    	"title"		=>	$api_data['name'],
                    	"size"		=>	25,
                    	"tier"		=>	"maxiops"
                	)
			)
       	)
	);

	update_option( 'wpcs_create_server_data', $server_data );

	$api = new WP_Cloud_Server_Ploi_API();

	//$api_response = $api->call_api( 'server', $server_data, false, 0, 'POST', $enable_response, 'server_creation' );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_ploi_call_api_update_server( $model = 'linode/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api = new WP_Cloud_Server_Ploi_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_ploi_update_server_api_last_response', $api_response );

	return $api_response;

}

/**
 * Get a List of DigitalOcean OS Images
 *
 * Retrieves a List of DigitalOcean OS Images.
 *
 * @since  1.2.0
 *
 * @return  os_images     List of all OS Images
 */
function wpcs_ploi_ssh_key( $ssh_key_name ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$api = new WP_Cloud_Server_Ploi_API();

	// Retrieve the SSH Key data
	$ssh_key_list = get_option( 'wpcs_serverpilots_ssh_keys');

	if ( !empty( $ssh_key_list ) ) {
		foreach ( $ssh_key_list as $key => $ssh_key ) {
			if ( $ssh_key_name == $ssh_key['name'] ) {
				// Set-up the data for the 
				$ssh_key_data = array(
					"name"		=>  $ssh_key['name'],
					"ssh_key"	=>  $ssh_key['public_key'], 
				);
			}
		}
	} 
			
	// Retrieve list of existing DigitalOcean SSH Keys
	$ssh_keys = $api->call_api( 'sshkey/list', null, false, 900, 'GET', false, 'list_ssh_keys' );
	//$ssh_keys = call_user_func( "wpcs_linode_cloud_server_api", null, 'profile/sshkeys', null, false, 0, 'GET', false, 'list_ssh_keys' );
	
	$debug['get'] = $ssh_keys;

	if ( !empty( $ssh_keys ) ) {
		foreach ( $ssh_keys as $key => $ssh_key ) {
			if ( $ssh_key_data['name'] == $ssh_key['name'] ) {
				return $ssh_key_id = $ssh_key['SSHKEYID'];
			}
		}
	}

	// SSH Key is NOT known to Linode so we need to add it
	//$ssh_key = call_user_func( "wpcs_linode_cloud_server_api", null, 'profile/sshkeys', $ssh_key_data, false, 0, 'POST', false, 'add_ssh_key' );
	$ssh_key = $api->call_api( 'sshkey/create', $ssh_key_data, false, 900, 'POST', false, 'add_ssh_key' );
	
	$debug['add'] = $ssh_key;
	
	update_option( 'wpcs_linode_ssh_key_api_response', $debug );

	if ( isset($ssh_key['SSHKEYID']) ) {
		return $ssh_key['SSHKEYID'];
	}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return false;
    
}

function wpcs_ploi_convert_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

// Data Mapping Functions

function wpcs_ploi_server_type_map( $type ) {
	$server_type = array(
		'server'			=> 'Server',
		'load-balancer'		=> 'Load Balancer',
		'database-server'	=> 'Database',
		'redis-server'		=> 'Redis',
	);

	return $server_type[ $type ];
}

function wpcs_ploi_server_status_map( $status ) {
	$server_status = array(
		'active'				=> '<span style="color: green;">Active</span>',
		'building'				=> '<span style="color: green;">Building</span>',
		'building-failed'		=> '<span style="color: red;">Building Failed</span>',
		'created'				=> '<span style="color: green;">Created</span>',
		'destroying'			=> '<span style="color: red;">Destroying</span>',
		'deploy-failed'			=> '<span style="color: red;">Deploy Failed</span>',
		'unreachable'			=> '<span style="color: red;">Unreachable</span>',
		'rebooting'				=> '<span style="color: yellow;">Rebooting</span>',
		'testing-connection'	=> '<span style="color: yellow;">Testing Connection</span>',
	);

	return $server_status[ $status ];
}

function wpcs_ploi_server_project_map( $project ) {
	$project_type = array(
		'no-application'	=> 'No Appication',
		'wordpress'			=> 'WordPress',
		'nextcloud'			=> 'Nextcloud',
	);

	return ( array_key_exists( $project, $project_type ) ) ? $project_type[ $project ] : 'None';
}

/**
 * Ploi Cloud Server Action Function
 *
 * Allows access to the Ploi. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.3
 *
 * @return response The response from the Linode API call
 */
function wpcs_ploi_cloud_server_action( $action, $server_id, $enable_response = false ) {

	if ( 'delete' == $action ) {

		$request			= 'DELETE';
		$model				= "servers/delete";
		$args['server_id']	= $server_id;

	} elseif ( 'shutdown' == $action ) {

		$request	= 'POST';
		$model		= "linode/instances/{$server_id}/{$action}";
		$api_data	= null;

	} else {

		return false;

	}

	// Delete the Droplet API Data to Force update
	$data = get_option( 'wpcs_ploi_api_data' );
	if ( isset( $data['servers'] ) ) {
		unset( $data['servers'] );
		update_option( 'wpcs_ploi_api_data', $data );
	}
	
	$api_response	= wpcs_ploi_api_server_request( $model, $args );
	
	return $api_response;
}

/**
 * Call to API to Delete Web App
 *
 * @since  3.0.3
 *
 * @return api_response 	API response
 */
function wpcs_ploi_delete_site_application( $server_id, $site_id, $enable_response = false ) {

	$request			= 'DELETE';
	$model				= "sites/delete";

	$args['server_id']	= $server_id;
	$args['site_id']	= $site_id;

	$api_response	= wpcs_ploi_api_sites_request( $model, $args);
	
	return $api_response;

}