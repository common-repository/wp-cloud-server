<?php
/**
 * WP Cloud Server - Cloudways Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a list of Providers
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_providers_list( $provider = null ) {

	$list = array();

	$regions = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $regions['providers'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$regions	= $api->call_api( 'providers', null, false, 900, 'GET', false, 'cloudways_zone_list' );
	}

	if ( isset( $regions['providers'] ) && is_array( $regions['providers'] ) ) {
		foreach ( $regions['providers'] as $key => $region ) {
			$list[ $region['id'] ] = array( 'name' => $region['name'] );
		}
	}
	
	if ( !empty( $provider ) ) {
		return $list[$provider]['name'];
	}
	
	return $list;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_regions_list() {

	$list = array();
	
	$regions = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $regions['regions'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$regions	= $api->call_api( 'regions', null, false, 0, 'GET', false, 'cloudways_zone_list' );
	}
	
	if ( isset( $regions['regions'] ) && is_array( $regions['regions'] ) ) {
		foreach ( $regions['regions'] as $key => $region ) {
			foreach ( $region as $value ) {
				$list[ $key ][] = array( 'id' => $value['id'], 'name' => $value['name'] );
			}
		}
	}
	
	return $list;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_regions_array() {

	$list = array();
	
	$regions = get_option( 'wpcs_cloudways_api_data' );
	
	if ( isset( $regions['regions'] ) && is_array( $regions['regions'] ) ) {
		foreach ( $regions['regions'] as $key => $region ) {
			foreach ( $region as $value ) {
				$list[ $key ] = $value['name'];
			}
		}
	}
	
	return $list;
}

/**
 * Maps Cloudways App Name to Name
 *
 * @since  3.0.3
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_app_map( $appname=null ) {
	
	$app_map = array(
		"wordpress"			=>	"WordPress",
		"woocommerce"		=>	"WooCommerce",
		"wordpressmu"		=>	"WordPress Multisite",
		"wordpressdefault"	=>	"WordPress (Default)",
		"magento"			=>	"Magento",
		"drupal"			=>	"Drupal",
		"opencart"			=>	"OpenCart",
		"phplaravel"		=>	"Laravel",
		"joomla"			=>	"Joomla",
		"prestashop"		=>	"PrestaShop",
		"restropress"		=>	"Restaurant Toolkit",
		"phpstack"			=>	"PHP Stack",
	);

	return ( !empty( $appname ) ) ? $app_map[$appname] : $app_map;
}

/**
 * Retrieves a list of Applications
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_apps_list() {
	
	$app_map = array(
		"wordpress"			=>	"WordPress",
		"woocommerce"		=>	"WooCommerce",
		"wordpressmu"		=>	"WordPress Multisite",
		"wordpressdefault"	=>	"WordPress (Default)",
		"magento"			=>	"Magento",
		"drupal"			=>	"Drupal",
		"opencart"			=>	"OpenCart",
		"phplaravel"		=>	"Laravel",
		"joomla"			=>	"Joomla",
		"prestashop"		=>	"PrestaShop",
		"restropress"		=>	"Restaurant Toolkit",
		"phpstack"			=>	"PHP Stack",
	);
	
	$list = array();

	$apps = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $apps['apps'] ) ) {
		// Create instance of the Cloudways API
		$api	= new WP_Cloud_Server_Cloudways_API();
		$apps	= $api->call_api( 'apps', null, false, 0, 'GET', false, 'cloudways_zone_list' );
	}
	
	update_option('cloudways_app_data', $apps );

	if ( isset( $apps['apps'] ) && is_array( $apps['apps'] ) ) {
		foreach ( $apps['apps'] as $key => $app ) {
			foreach ( $app['versions'] as $index => $value ) {
				$app_name = ( array_key_exists( $value['application'], $app_map ) ) ? $app_map[ $value['application'] ] : $value['application'];
				$list[$key][] = array( 'app_version' => $value['app_version'], 'application' => $value['application'], 'label' => $app_name );
			}
		}
	}
	
	update_option('cloudways_api_values', $list );
	
	return $list;
}

/**
 * Retrieves a list of Applications
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_cloudways_packages_list() {
	
	$app_map = array();
	
	$list = array();

	$packages = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $packages['packages'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$packages	= $api->call_api( 'packages', null, false, 0, 'GET', false, 'cloudways_zone_list' );
	}
	
	update_option('cloudways_packages_data', $packages );

	//if ( !empty( $packagess ) ) {
	//	foreach ( $packages['packages'] as $key => $app ) {
	//		foreach ( $app['versions'] as $index => $value ) {
	//			$list[$key][] = array( 'app_version' => $value['app_version'], 'application' => $value['application'] );
	//		}
	//	}
	//}
	
	//update_option('cloudways_api_values', $list );
	
	return $list;
}

/**
 * Retrieves a list of Available Plans by Zone
 *
 * @since  1.0
 *
 * @return regions List of available plans by zone
 */
function wpcs_cloudways_availability_list( $region ) {
	
	// Create instance of the Cloudways API
	//$api = new WP_Cloud_Server_Cloudways_API();
	
	$model = ( 'userselected' == $region ) ? "plan" : "plan" ;
	
	$api_data = get_option( 'wpcs_cloudways_api_data' );
	
	//$available_plans = $api->call_api( 'plan', null, false, 900, 'GET', false, 'cloudways_plan_availability_list' );
	//$available_plans = get_option( 'wpcs_cloudways_api_data' );
	
	//$available_plans = ( 'userselected' == $region ) ? array_keys($available_plans) : $available_plans ;
	
	//$plans = $api->call_api( "plan", null, false, 900, 'GET', false, 'cloudways_plan_list' );
	$plans = get_option( 'wpcs_cloudways_api_data' );
	
	//if ( is_array( $plans ) ) {
	foreach ( $plans['plans']['plan'] as $key => $plan ) {
			
		$ram  		= wpcs_cloudways_convert_mb_to_gb($plan['memory_amount']);
		//$cost 	= str_replace('.00', '', $plan['price_per_month']);
		$cost		= 0;
		$plan_name 	= "{$plan['core_number']} CPU, {$ram}GB, {$plan['storage_size']}GB SSD";
		$plan_cost	= "(\${$cost}/month)";
			
		$available['server'][$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
	}
	//}

	update_option( 'wpcs_cloudways', $plans );
	
	return $available;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_cloudways_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_Cloudways_API";

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
	
	// Create instance of the Cloudways API
	//$api = new $class_name();
	
	//$plans = $api->call_api( 'storage/template', null, false, 900, 'GET', false, 'cloudways_os_list' );
	$plans = get_option( 'wpcs_cloudways_api_data' );
	
	update_option( 'cloudways_os_list', $image );
	
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
function wpcs_cloudways_plans_list() {

	$list = array();
	
	$plans = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $plans['sizes'] ) ) {
		// Create instance of the Cloudways API
		$api	= new WP_Cloud_Server_Cloudways_API();
		$plans	= $api->call_api( 'server_sizes', null, false, 0, 'GET', false, 'cloudways_plan_list' );
	}

	if ( isset( $plans['sizes'] ) && is_array( $plans['sizes'] ) ) {
		foreach ( $plans['sizes'] as $key => $plan ) {
				$list[ $key ][] = $plan;
		}
	}
	
	return $list;
}

/**
 * Retrieves a list of Servers
 *
 * @since  1.0.0
 *
 * @return plans List of available plans
 */
function wpcs_cloudways_server_list() {
	 
	$server_list = array();
	
	$servers = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $servers['servers'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$servers	= $api->call_api( 'server', null, false, 900, 'GET', false, 'cloudways_server_list' );
	}
	 
	if ( isset( $servers['servers'] ) && is_array( $servers['servers'] ) ) {
		foreach ( $servers['servers'] as $key => $server ) {
			$server_list[$server['id']] = $server['label'];
		}
	}
	
	return $server_list;
}

/**
 * Retrieves a list of Servers
 *
 * @since  1.0.0
 *
 * @return plans List of available plans
 */
function wpcs_cloudways_project_list() {
	 
	$project_list = array();
	
	$projects = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $projects['projects'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$projects	= $api->call_api( 'project', null, false, 900, 'GET', false, 'cloudways_project_list' );
	}
	 
	if ( !empty( $projects['projects'] ) ) {
		foreach ( $projects['projects'] as $key => $project ) {
			$project_list[$project['id']] = $project['name'];
		}
	}
	
	return $project_list;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_cloudways_managed_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_Cloudways_API";
	
	// Create instance of the Cloudways API
	$api = new $class_name();
	
	$plans = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'cloudways_os_list' );
	
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
 * Cloudways API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_cloudways_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	// Exit if checking DigitalOcean Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$api = new WP_Cloud_Server_Cloudways_API();
	
	$model = ( 'droplets' == $model ) ? 'server' : $model;
	
	if ( isset( $api_data ) && ( 'POST' == $request ) ) {
		$app_data = $api_data['custom_settings'];	
	}
	
	if ( isset( $api_data['user_data'] )  ) {
		$app_data['user_data'] = $api_data['user_data'];	
	}
	
	update_option( 'wpcs_cloudways_sp_install_script', $app_data );

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
function wpcs_cloudways_cloud_regions() {
	
	$regions = wpcs_cloudways_regions_list();
		
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
function wpcs_cloudways_api_response_not_valid( $response ) {

	return ( $response ) ? false : true;
	//return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Delete Cloudways Application from Server
 *
 * @since  3.0.3
 *
 * @return server_data Server Data
 */
function wpcs_cloudways_api_delete_application( $server_id, $app_id, $enable_response = false ) {
	
	$api = new WP_Cloud_Server_Cloudways_API();
	
	$app_data = array(
				"server_id"		=>	intval($server_id),
				"app_id"		=>	intval($app_id),
			);

	$response		= $api->call_api( 'app/' . $app_id, $app_data, false, 0, 'DELETE', $enable_response, 'delete_app_test' );
	
	return $response;

}

/**
 * Delete Cloudways Server
 *
 * @since  3.0.3
 *
 * @return Response from API
 */
function wpcs_cloudways_api_delete_server( $server_id, $enable_response = false ) {
	
	$api = new WP_Cloud_Server_Cloudways_API();
	
	$app_data = array(
					"serverId"		=>	intval($server_id),
				  );

	$response = $api->call_api( 'server/' . $server_id, $app_data, false, 0, 'DELETE', $enable_response, 'delete_server' );
	
	return $response;

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_cloudways_server_complete( $server_sub_id, $queued_server, $host_name, $server_location ) {
	
	$api		= new WP_Cloud_Server_Cloudways_API();
	
	$operation	= $queued_server['response']['server']['operations'][0]['id'];
	$status		= $queued_server['response']['server']['operations'][0]['is_completed'];
	$x			= 1;
	
	$debug['queue_server'] = $queued_server;
			
	// Wait for the Server to Complete
	while( ( "1" !== $status ) && ( $x <= 30 ) ) {
		$actions = $api->call_api( 'operation/' . $operation, null, false, 0, 'GET', false, 'server_status' );
		if ( $actions ) {
			$status		= $actions['operation']['is_completed'];
			if ( "1" == $status ) {
				$debug['action'] = $actions;
			}
   	 		$x++;
		}
	}
	
	$debug['counter']['status']	= $status;
	$debug['counter']['count']	= $x;
	
	if ( "1" == $status ) {
			
		// Wait 1 second to avoid Cloudways Rate Limit
		sleep(1);
			
		// Read Server Information
		$servers = $api->call_api( 'server', null, false, 0, 'GET', false, 'get_server' );
			
		foreach ( $servers['servers'] as $key => $server_info ) {
			if ( $server_sub_id == $server_info['id'] ) {
				$server = $server_info;
			}
		}
	
		$debug['server_info'] = $server;
	
		$response = $queued_server['response'];
	
		// Update Log with new website creation
		if ( ! $response ) {
			$result		= 'Failed';
			$api_data	= get_option( 'wpcs_cloudways_api_last_response' );
			$error		= $api_data['server_id'][0]['message'];
			$message	= 'An Error Occurred ( ' . $error . ' )';
		} else {
			$result		= 'Success';
			$message	= 'New Server Created ( ' . $host_name . ' )';
		}
					
		wpcs_cloudways_log_event( 'Cloudways', $result, $message );
	
		update_option('wpcs_cloudways_server_complete', $debug );
		
	}
	
		// Save the server details for future use
		$server_data = array(
			"id"			=>	( isset( $server['id'] ) ) ? $server['id'] : '',
			"name"			=>	( isset( $server['label'] ) ) ? $server['label'] : '',
			"location"		=>	( isset( $server['region'] ) ) ? $server['region'] : '',
			"slug"			=>	( isset( $server['label'] ) ) ? sanitize_title($server['label']) : '',
			"ram"			=>	'',
			"vcpus"			=>	'',
			"disk"			=>	( isset( $server['volume_size'] ) ) ? $server['volume_size'] : '',
			"size"			=>	( isset( $server['instance_type'] ) ) ? $server['instance_type'] : '',
			"os"			=> 	( isset( $server['platform'] ) ) ? $server['platform'] : '',
			"ip_address"	=>	( isset( $server['public_ip'] ) ) ? $server['public_ip'] : '',
			"domain_name"	=>	( isset( $server['apps'][0]['app_fqdn'] ) ) ? $server['apps'][0]['app_fqdn'] : '',
			"app_user"		=>	( isset( $server['apps'][0]['app_user'] ) ) ? $server['apps'][0]['app_user'] : '',
			"app_password"	=>	( isset( $server['apps'][0]['app_password'] ) ) ? $server['apps'][0]['app_password'] : '',
			"completed"		=>	( "1" == $status ) ? true : false,
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
function wpcs_cloudways_call_api_health_check( ) {

	$api = new WP_Cloud_Server_Cloudways_API();

	$api_response = wpcs_cloudways_get_access_token( $api->wpcs_cloudways_get_api_url() );

	return $api_response;

}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_cloudways_call_api_list_servers( $enable_response = false ) {

	$api = new WP_Cloud_Server_Cloudways_API();

	$api_response = $api->call_api( 'server', null, false, 0, 'GET', $enable_response, 'get_servers' );

	foreach ( $api_response['servers']['server'] as $key => $server ) {
			$server_list[$key] =  $server;
	}

	return $server_list;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_cloudways_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Cloudways_API();

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
function wpcs_cloudways_call_api_create_server( $api_data = null, $enable_response = false ) {

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

	$api = new WP_Cloud_Server_Cloudways_API();

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
function wpcs_cloudways_call_api_update_server( $model = 'linode/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api = new WP_Cloud_Server_Cloudways_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_cloudways_update_server_api_last_response', $api_response );

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
function wpcs_cloudways_ssh_key( $ssh_key_name ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$api = new WP_Cloud_Server_Cloudways_API();

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

function wpcs_cloudways_convert_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

function wpcs_cloudways_get_access_token( $api_base_url ) {
	
	// Check if the health is temporarily cached.
	$health			= get_transient( 'wpcs_cloudways_api_health' );
	$access_token	= get_option( 'wpcs_cloudways_api_access_token', false );

	if ( ( 'ok' === $health ) && ( $access_token ) ) {
		return $access_token;
	}

	$body = [
		'api_key'  	=> get_option( 'wpcs_cloudways_api_key', '' ),
		'email'		=> get_option( 'wpcs_cloudways_email', '' ),
	];

	$body = wp_json_encode( $body );

	$args = [
		'body'        => $body,
		'headers'     => [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer',
		],
	];

	$response = wp_safe_remote_post( trailingslashit( $api_base_url ) . 'oauth/access_token' , $args );
	
			// WP couldn't make the call for some reason, return false as a error.
		if ( is_wp_error( $response ) ) {
			return false;
		}
					
		// Get request response body and endcode the JSON data.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

	$access_token = ( isset( $data['access_token'] )) ? $data['access_token'] : false;
	
	update_option( 'wpcs_cloudways_api_access_token', $access_token );

	return $access_token;
}