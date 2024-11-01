<?php
/**
 * WP Cloud Server - UpCloud Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_UpCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_upcloud_regions_array() {

	$data		= get_option( 'wpcs_upcloud_api_data' );
	$regions	= ( ( isset( $data['zones']['zone'] ) ) && ( is_array( $data['zones']['zone'] ) ) ) ? $data['zones']['zone'] : array();

	if ( !empty( $regions ) ) {
		foreach ( $regions as $key => $region ) {
			$list[ $region['id'] ] = $region['description'];
		}
	}
	
	return ( !empty( $list ) ) ? $list : false;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_upcloud_regions_list() {
	
	$data = get_option( 'wpcs_upcloud_api_data' );
	
	if ( !isset( $data['zones']['zone'] ) ) {
		// Create instance of the UpCloud API
		$api	= new WP_Cloud_Server_UpCloud_API();
		$data	= $api->call_api( 'zone', null, false, 900, 'GET', false, 'upcloud_zone_list' );
	}
	
	$regions = ( ( isset( $data['zones']['zone'] ) ) && ( is_array( $data['zones']['zone'] ) ) ) ? $data['zones']['zone'] : array();

	if ( !empty( $regions ) ) {
		foreach ( $regions as $key => $region ) {
			$list[ $region['id'] ] = array( 'name' => $region['description'] );
		}
	}
	
	return ( !empty( $list ) ) ? $list : false;
}

/**
 * Retrieves a list of Available Plans by Zone
 *
 * @since  1.0
 *
 * @return regions List of available plans by zone
 */
function wpcs_upcloud_availability_list( $region=null ) {
	
	$data	= get_option( 'wpcs_upcloud_api_data' );
	$plans	= ( ( isset( $data['plans']['plan'] ) ) && ( is_array( $data['plans']['plan'] ) ) ) ? $data['plans']['plan'] : array();
	
	if ( !empty( $plans ) ) {
		foreach ( $plans as $key => $plan ) {
			$ram  		= wpcs_upcloud_convert_mb_to_gb($plan['memory_amount']);
			$plan_name 	= "{$plan['core_number']} CPU, {$ram}GB, {$plan['storage_size']}GB SSD";
			$cost		= wpcs_upcloud_pricing( $plan['name'] );
			$plan_cost	= "(\${$cost}/month)";
			$available['server'][$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
		}
	}
	
	return isset( $available ) ? $available : false;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_upcloud_os_list( $image = null ) {
	
	$excludes	= array( 'Windows', 'Plesk' );

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

	$plans = get_option( 'wpcs_upcloud_api_data' );
	
	if ( !isset( $plans['storages']['storage'] ) ) {
		$api	= new WP_Cloud_Server_UpCloud_API();
		$plans	= $api->call_api( 'storage/template', null, false, 900, 'GET', false, 'upcloud_os_list' );
	}
	
	update_option( 'upcloud_os_list', $image );
	
	if ( isset( $image ) ) {
		foreach ( $plans['storages']['storage'] as $key => $plan ) {
			if ( $plan['title'] == $images[$image] ) {
				return $plan['uuid'];
			}
		}
	}
	
	if ( !empty( $plans['storages']['storage'] ) ) {
		foreach ( $plans['storages']['storage'] as $key => $plan ) {
			$string		= explode( ' ', $plan['title'] );
			$string_pt1	= $string[0];
			if ( !in_array( $string_pt1, $excludes ) ) {
			//if ( $plan['name'] ) {
				$plan_list[$plan['uuid']] =  array( 'name' => $plan['title'] );
			}
		}
	}
	
	return ( !empty( $plan_list ) ) ? $plan_list : false;
}
	
/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_upcloud_pricing( $plan = null ) {

	$plans = array(
		"1xCPU-1GB"		=>	"5",
		"1xCPU-2GB"		=>	"10",
		"2xCPU-4GB"		=>	"20",
		"4xCPU-8GB"		=>	"40",
		"6xCPU-16GB"	=>	"80",
		"8xCPU-32GB"	=>	"160",
		"12xCPU-48GB"	=>	"240",
		"16xCPU-64GB"	=>	"320",
		"20xCPU-128GB"	=>	"480",
		"20xCPU-96GB"	=>	"640",
	);
	
	return ( array_key_exists( $plan, $plans ) ) ? $plans[$plan] : '0';
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_upcloud_plan_description( $plan = null ) {

	$plans = array(
		"1xCPU-1GB"		=>	"5",
		"1xCPU-2GB"		=>	"10",
		"2xCPU-4GB"		=>	"20",
		"4xCPU-8GB"		=>	"40",
		"6xCPU-16GB"	=>	"80",
		"8xCPU-32GB"	=>	"160",
		"12xCPU-48GB"	=>	"240",
		"16xCPU-64GB"	=>	"320",
		"20xCPU-96GB"	=>	"480",
		"20xCPU-128GB"	=>	"640",
	);
	
	return ( array_key_exists( $plan, $plans ) ) ? $plans[$plan] : '0';
}

/**
 * Retrieves a list of Plans
 *
 * @since  1.0
 *
 * @return plans List of available plans
 */
function wpcs_upcloud_plans_list() {
	
	$data		= get_option( 'wpcs_upcloud_api_data' );
	
	if ( !isset( $data['plans']['plan'] ) ) {
		$api	= new WP_Cloud_Server_UpCloud_API();
		$data	= $api->call_api( 'plan', null, false, 900, 'GET', false, 'upcloud_plans_list' );
	}
	
	$plans		= ( ( isset( $data['plans']['plan'] ) ) && ( is_array( $data['plans']['plan'] ) ) ) ? $data['plans']['plan'] : array();
	
	foreach ( $plans as $key => $plan ) {
			$ram  		= wpcs_upcloud_convert_mb_to_gb($plan['memory_amount']);
			$plan_name 	= "{$plan['core_number']} CPU, {$ram}GB, {$plan['storage_size']}GB SSD";
			$cost		= wpcs_upcloud_pricing( $plan['name'] );
			$plan_cost	= "(\${$cost}/month)";
			$plan_list[$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
			//$plan_list['Simple'][$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
	}

	//$plans = get_option( 'wpcs_upcloud_api_data' );
	
	//if ( !empty( $plans['plans']['plan'] ) ) {
	//	foreach ( $plans['plans']['plan'] as $key => $plan ) {
	//			$plan_list[$plan['name']] =  array( 'name' => wpcs_upcloud_plan_description( $plan['name'] ) );
	//	}
	//}
	
	return ( !empty( $plan_list ) ) ? $plan_list : false;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_upcloud_managed_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_UpCloud_API";
	
	// Create instance of the UpCloud API
	$api = new WP_Cloud_Server_UpCloud_API();
	
	$plans = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'upcloud_os_list' );
	
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
 * UpCloud API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_upcloud_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	// Exit if checking DigitalOcean Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$api = new WP_Cloud_Server_UpCloud_API();
	
	$model = ( 'droplets' == $model ) ? 'server' : $model;
	
	if ( isset( $api_data ) && ( 'POST' == $request ) ) {
		$app_data = $api_data['custom_settings'];	
	}
	
	if ( isset( $api_data['user_data'] )  ) {
		$app_data['user_data'] = $api_data['user_data'];	
	}
	
	update_option( 'wpcs_upcloud_sp_install_script', $app_data );

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
function wpcs_upcloud_cloud_regions() {
	
	$regions = wpcs_upcloud_regions_list();
		
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
function wpcs_upcloud_api_response_valid( $response ) {

	return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_upcloud_server_complete( $server_sub_id, $queued_server, $host_name, $server_location ) {
	
	$api = new WP_Cloud_Server_UpCloud_API();
			
	$status = "in-progress";
	$x = 1;
			
	// Wait for the Server to Complete
	while( ( "active" !== $status ) && ( $x <= 2000 ) ) {
		$actions = $api->call_api( 'server/list?SUBID=' . $server_sub_id, null, false, 0, 'GET', false, 'server_status' );
		$status = $actions['status'];
   	 	$x++;
		$debug['counter']['status'] = $status;
		$debug['counter']['count'] = $x;
	}
			
	// Wait 1 second to avoid UpCloud Rate Limit
	sleep(1);
			
	// Read Server Information
	$servers = $api->call_api( 'server/list', null, false, 0, 'GET', true, 'get_server' );
			
	foreach ( json_decode( $servers['body'], true ) as $server_info ) {
		if ( $server_sub_id == $server_info['SUBID'] ) {
			$server = $server_info;
		}
	}
	
	// Update Log with new website creation
	if ( ! $queued_server['response'] ) {
		$status		= 'Failed';
		$api_data	= get_option( 'wpcs_upcloud_api_last_response' );
		$error		= $api_data['site_creation']['data']['message'];
		$message	= 'An Error Occurred ( ' . $error . ' )';
	} else {
		$status		= 'Success';
		$message	= 'New Server Created ( ' . $host_name . ' )';
	}
					
	wpcs_upcloud_log_event( 'UpCloud', $status, $message );
	
	// Save the server details for future use
	$server_data = array(
			"id"			=>	$server['SUBID'],
			"name"			=>	$server['label'],
			"location"		=>	$server['location'],
			"slug"			=>	sanitize_title($server['label']),
			"ram"			=>	$server['ram'],
			"vcpus"			=>	$server['vcpu_count'],
			"disk"			=>	$server['disk'],
			"size"			=>	$server['VPSPLANID'],
			"os"			=> 	$server['os'],
			"ip_address"	=>	$server['main_ip'],
			"completed"		=>	true,
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
function wpcs_upcloud_call_api_health_check( ) {

	$api			= new WP_Cloud_Server_UpCloud_API();
	$api_response	= $api->call_api( 'account', null, false, 0, 'GET', true, 'api_health' );

	return $api_response;
}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_upcloud_call_api_list_servers( $enable_response = false ) {
	
	$server_list	= array();
	$api			= new WP_Cloud_Server_UpCloud_API();
	$api_response	= $api->call_api( 'server', null, false, 0, 'GET', $enable_response, 'get_servers' );

	if ( !empty( $api_response['servers']['server'] ) ) {
		foreach ( $api_response['servers']['server'] as $key => $server ) {
			$server_details		= wpcs_upcloud_call_api_server_info( $server['uuid'] );
			$server_list[$key]	= $server_details;
		}
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
function wpcs_upcloud_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api			= new WP_Cloud_Server_UpCloud_API();
	$api_response	= $api->call_api( 'server/' . $server_sub_id, null, false, 0, 'GET', $enable_response, 'server_status' );

	return isset( $api_response['server'] ) ? $api_response['server'] : false;

}

/**
 * Call to API to Create New Server
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_upcloud_call_api_create_server( $api_data = null, $enable_response = false ) {

	$server_data = array( "server" => array(
        "zone"				=>	$api_data['region'],
        "title"				=>	( isset( $api_data['host_name'] ) ) ? $api_data['host_name'] : $api_data['name'],
        "hostname"			=>	( isset( $api_data['host_name'] ) ) ? $api_data['host_name'] : $api_data['name'],
        "plan"				=>	$api_data['size'],
		"password_delivery"	=>	$api_data['password'],
		"simple_backup"		=>	$api_data['simple_backup'],
        "storage_devices"	=>	array(
            						"storage_device" =>	array(
                    					"action"	=>	"clone",
                    					"storage"	=>	$api_data['image'],
                    					"title"		=>	$api_data['name'],
                    					"size"		=>	25,
                    					"tier"		=>	"maxiops"
                					)
								),
		"login_user"		=>	array(
       								"username"		=> "root",
       								"ssh_keys"		=> ( isset( $api_data['ssh_key'] ) ) ? array( "ssh_key"	=> $api_data['ssh_key'] ) : null
       							)
	));

	update_option( 'wpcs_create_server_data', $server_data );

	$api			= new WP_Cloud_Server_UpCloud_API();
	$api_response	= $api->call_api( 'server', $server_data, false, 0, 'POST', $enable_response, 'server_creation' );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_upcloud_call_api_update_server( $model = 'upcloud/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api = new WP_Cloud_Server_UpCloud_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_upcloud_update_server_api_last_response', $api_response );

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
function wpcs_upcloud_ssh_key( $ssh_key_name ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$ssh_key_data = false;
	
	//$api = new WP_Cloud_Server_UpCloud_API();

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
	//$ssh_keys = $api->call_api( 'sshkey/list', null, false, 900, 'GET', false, 'list_ssh_keys' );
	//$ssh_keys = call_user_func( "wpcs_upcloud_cloud_server_api", null, 'profile/sshkeys', null, false, 0, 'GET', false, 'list_ssh_keys' );
	
	//$debug['get'] = $ssh_keys;

	//if ( !empty( $ssh_keys ) ) {
	//	foreach ( $ssh_keys as $key => $ssh_key ) {
	//		if ( $ssh_key_data['name'] == $ssh_key['name'] ) {
	//			return $ssh_key_id = $ssh_key['SSHKEYID'];
	//		}
	//	}
	//}

	// SSH Key is NOT known to UpCloud so we need to add it
	//$ssh_key = call_user_func( "wpcs_upcloud_cloud_server_api", null, 'profile/sshkeys', $ssh_key_data, false, 0, 'POST', false, 'add_ssh_key' );
	//$ssh_key = $api->call_api( 'sshkey/create', $ssh_key_data, false, 900, 'POST', false, 'add_ssh_key' );
	
	//$debug['add'] = $ssh_key;
	
	//update_option( 'wpcs_upcloud_ssh_key_api_response', $debug );

	//if ( isset($ssh_key['SSHKEYID']) ) {
	//	return $ssh_key['SSHKEYID'];
	//}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return $ssh_key_data;
    
}

function wpcs_upcloud_convert_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

/**
 * UpCloud Cloud Server Action Function
 *
 * Allows access to the UpCloud API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.3
 *
 * @return response The response from the UpCloud API call
 */
function wpcs_upcloud_cloud_server_action( $action, $server_id, $enable_response = false ) {
	
	// Create instance of the UpCloud API
	$api = new WP_Cloud_Server_UpCloud_API();

	$request	= ( 'delete' == $action ) ? 'DELETE' : 'POST';
	$model		= ( 'delete' == $action ) ? 'server/' . $server_id . '/?storages=1' : 'server/' . $server_id . '/' . $action;
	$api_data	= ( 'stop' == $action ) ? array( 'stop_server' => array( "stop_type" => "soft", "timeout" => "60" ) ) : null;
	$api_data	= ( 'start' == $action ) ? array( 'server' => array( "host" => null ) ) : $api_data;
	$api_data	= ( 'restart' == $action ) ? array( 'restart_server' => array( "stop_type" => null, "timeout" => "60", "timeout_action" => "destroy" ) ) : $api_data;
	
	$api_response = $api->call_api( $model, $api_data, false, 0, $request, $enable_response, 'server_action' );
	
	return $api_response;
}