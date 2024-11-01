<?php
/**
 * WP Cloud Server - Vultr Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a list of Regions
 *
 * @since  1.0
 *
 * @return regions List of available regions
 */
function wpcs_vultr_regions_list() {
	
	$regions	= get_option( 'wpcs_vultr_api_data' );
		
	if ( !isset( $regions['regions'] ) ) {
		// Create instance of the Vultr API
		$api = new WP_Cloud_Server_Vultr_API();
		$regions['regions'] = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'vultr_os_list' );
	}

	return isset( $regions['regions'] ) ? $regions['regions'] : false;
	
	return $regions;
}

/**
 * Retrieves a list of Plans
 *
 * @since  1.0
 *
 * @return plans List of available plans
 */
function wpcs_vultr_plans_list() {
	
	$plans	= get_option( 'wpcs_vultr_api_data' );
	
	if ( !isset( $plans['plans'] ) ) {
		// Create instance of the Vultr API
		$api	= new WP_Cloud_Server_Vultr_API();
		$plans['plans'] = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'vultr_os_list' );
	}

	return isset( $plans['plans'] ) ? $plans['plans'] : false;
	
	return $plans;
}

/**
 * Retrieves a list of Applications
 *
 * @since  3.0.6
 *
 * @return images List of available applications
 */
function wpcs_vultr_app_list() {

	$plans	= get_option( 'wpcs_vultr_api_data' );
	
	if ( !isset( $plans['apps'] ) ) {
		// Create instance of the Vultr API
		$api	= new WP_Cloud_Server_Vultr_API();
		$plans['apps'] = $api->call_api( 'app/list', null, false, 900, 'GET', false, 'vultr_app_list' );
	}

	if ( $plans && is_array( $plans ) ) {
	
		foreach ( $plans['apps'] as $plan ) {
			//if ( !in_array( $plan['name'], ['Custom','Snapshot', 'Backup','Application'], true ) ) {
			//if ( !in_array( $plan['name'], ['Custom','Snapshot', 'Backup'], true ) ) {
				$plan_list[$plan['APPID']] =  array( 'name' => $plan['name'], 'deploy_name' => $plan['deploy_name'] );
			//}
		}
	}

	$plan_list = ( isset( $plan_list ) ) ? $plan_list : false;
	
	return $plan_list;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_vultr_os_list( $image = null ) {

	$plans	= get_option( 'wpcs_vultr_api_data' );
	
	if ( !isset( $plans['images'] ) ) {
		// Create instance of the Vultr API
		$api	= new WP_Cloud_Server_Vultr_API();
		$plans['images'] = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'vultr_os_list' );
	}
	
	if ( !empty( $plans['images'] ) && isset( $image ) ) {

		foreach ( $plans['images'] as $plan ) {
			if ( $plan['name'] == $image ) {
				return $plan['OSID'];
			}
		}
	}

	if ( !empty( $plans['images'] ) ) {
	
		foreach ( $plans['images'] as $plan ) {
			//if ( !in_array( $plan['name'], ['Custom','Snapshot', 'Backup','Application'], true ) ) {
			if ( !in_array( $plan['name'], ['Custom','Snapshot', 'Backup'], true ) ) {
				$plan_list[$plan['OSID']] =  array( 'name' => $plan['name'] );
			}
		}
	}
	
	$plans = array(
		'Ubuntu 20.04 x64'			=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'Ubuntu 18.04 x64'			=>	array( 'name' => 'Ubuntu 18.04 x64'),
		'Ubuntu 16.04 x64'			=>	array( 'name' => 'Ubuntu 16.04 x64'),
		'CentOS 8 x64'				=>	array( 'name' => 'CentOS 8 x64'),
		'CentOS 7 x64'				=>	array( 'name' => 'CentOS 7 x64'),
		'Debian 10 x64 (buster)'	=>	array( 'name' => 'Debian 10 x64 (buster)'),
		'Debian 9 x64 (stretch)'	=>	array( 'name' => 'Debian 9 x64 (stretch)'),
		'Fedora 32 x64'				=>	array( 'name' => 'Fedora 32 x64'),
		'Fedora 31 x64'				=>	array( 'name' => 'Fedora 31 x64'),
	);

	$plan_list = ( isset( $plan_list ) ) ? $plan_list : false;

	update_option( 'vultr_os_list', $plan_list );
	
	return $plan_list;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_vultr_managed_os_list( $image = null ) {
	
	$plans = get_option( 'wpcs_vultr_api_data' );
	
	if ( !empty( $plans['images'] ) && isset( $image ) ) {
		foreach ( $plans['images'] as $plan ) {
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
 * Retrieves a list of Available Plans by Region
 *
 * @since  1.0
 *
 * @return regions List of available plans by regions
 */
function wpcs_vultr_availability_list( $region=null ) {
	
	// Create instance of the Vultr API
	$api = new WP_Cloud_Server_Vultr_API();
	
	$server_types = array( 
		'SSD'				=> 'Cloud Compute', 
		'HIGHFREQUENCY'		=> 'High Frequency',
		'DEDICATED'			=> 'Dedicated Cloud',
	);

	//foreach ( $server_types as $key => $group ) {
	
		$model = ( empty( $region ) || 'userselected' == $region ) ? "plans/list" : "regions/availability?DCID={$region}" ;
	
		$available_plans = $api->call_api( $model, null, false, 900, 'GET', false, 'vultr_plan_availability_list' );
	
		$available_plans = ( empty( $region ) || 'userselected' == $region ) ? array_keys($available_plans) : $available_plans ;
	
		$plans = get_option( 'wpcs_vultr_api_data' );
		
	//}
	
	if ( !empty( $plans['plans'] ) ) {
		foreach ( $plans['plans'] as $plan ) {
			
			$ram  		= wpcs_convert_mb_to_gb($plan['ram']);
			$cost 		= str_replace('.00', '', $plan['price_per_month']);
			$plan_name 	= "{$plan['vcpu_count']} CPU, {$ram}GB, {$plan['disk']}GB SSD";
			$plan_cost	= "(\${$cost}/month)";
			
			if ( in_array( $plan['VPSPLANID'], $available_plans ) ) {
				$type = $server_types[$plan['plan_type']];
				$available[$type][$plan['VPSPLANID']] = array( 'name' => $plan_name, 'cost' => $plan_cost );
			}
		}
	}

	update_option( 'wpcs_vultr', $plans );
	
	return $available;
}

/**
 * Vultr API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_vultr_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	
	// Exit if checking DigitalOcean Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$api = new WP_Cloud_Server_Vultr_API();
	
	$model = ( 'droplets' == $model ) ? 'server/create' : $model;
	
	if ( isset( $api_data['user_data'] ) ) {
		
		$startup_script_name	= $api_data['custom_settings']['script_name'];
		$startup_script			= $api_data['user_data'];
		$enable_backups			= ( $api_data['backups'] ) ? 'yes' : 'no';
		$startup_script_exists	= false;

		// Retrieve list of scripts to check if script exists already
		$list_scripts = $api->call_api( 'startupscript/list', null, false, 0, 'GET', false, 'list_scripts' );
		foreach ( $list_scripts as $key => $script ) {
			if ( $startup_script_name == $script['name'] ) {
				$app_data['SCRIPTID']	= $script['SCRIPTID'];
				$startup_script_exists	= true;
			}	
		}
					
		if ( ! $startup_script_exists ) {
					
			$script_data = array(
				"name"		=> 	$startup_script_name,
				"script"	=>	$startup_script,
			);
				
			$new_script 		= $api->call_api( 'startupscript/create', $script_data, false, 0, 'POST', false, 'install_script' );
			$script_id			= $new_script['SCRIPTID'];	  
			$debug['script_id']	= $script_id;
		}
	}
	
	if ( isset( $api_data ) && ( 'POST' == $request ) ) {
		$api_data = $api_data['custom_settings'];	
	}
	
	if ( isset( $script_id ) ) {
		$api_data["SCRIPTID"] = $script_id;	
	}
	
	update_option( 'wpcs_vultr_sp_install_script', $api_data );
	
	$api_response = $api->call_api( $model, $api_data, $cache, $cache_lifetime, $request, $enable_response, $function );
	
	return $api_response;

}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_vultr_regions_array() {

	$regions = wpcs_vultr_regions_list();

	if ( !empty( $regions ) ) {
		foreach ( $regions as $key => $region ) {
			$list[ $region['DCID'] ] = $region['name'];
		}
	}
	
	return ( !empty( $list ) ) ? $list : false;
}

/**
 * Retrieves a list of Region Names
 *
 * @since  1.0
 *
 * @return regions List of all region names
 */
function wpcs_vultr_cloud_regions() {
	
	$regions = wpcs_vultr_regions_list();
		
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
function wpcs_vultr_api_response_not_valid( $response ) {

	return ( ! $response || $response['response']['http_code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_vultr_server_complete( $server_sub_id, $queued_server, $host_name, $server_location ) {
	
	$api = new WP_Cloud_Server_Vultr_API();
			
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
			
	// Wait 1 second to avoid Vultr Rate Limit
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
		$api_data	= get_option( 'wpcs_vultr_api_last_response' );
		$error		= $api_data['site_creation']['data']['message'];
		$message	= 'An Error Occurred ( ' . $error . ' )';
	} else {
		$status		= 'Success';
		$message	= 'New Server Created ( ' . $host_name . ' )';
	}
					
	wpcs_vultr_log_event( 'Vultr', $status, $message );
	
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
function wpcs_vultr_call_api_health_check( ) {

	$api = new WP_Cloud_Server_Vultr_API();

	$api_response = $api->call_api( 'account/info', null, false, 0, 'GET', true, 'api_health' );

	return $api_response;

}

/**
 * Call to API to List Vultr Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_servers( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( isset( $data['servers'] ) && empty( $data['servers'] ) ) {
		$servers = WPCS_Vultr()->api->call_api( "server/list", null, false, 0, 'GET', $enable_response, 'vultr_server_list' );
		$data['servers'] = $servers;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['servers'] ) ) ? $data['servers'] : false;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Vultr_API();

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
function wpcs_vultr_call_api_create_server( $api_data = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Vultr_API();

	$api_response = $api->call_api( 'server/create', $api_data, false, 0, 'POST', $enable_response, 'server_creation' );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_update_server( $model = 'linode/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api = new WP_Cloud_Server_Vultr_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_vultr_update_server_api_last_response', $api_response );

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
function wpcs_vultr_ssh_key( $ssh_key_name ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$api = new WP_Cloud_Server_Vultr_API();

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

function wpcs_convert_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

/**
 * Vultr Cloud Server Action Function
 *
 *
 * @since  3.0.3
 *
 * @return response The response from the UpCloud API call
 */
function wpcs_vultr_cloud_server_action( $action, $server_id, $enable_response = false ) {

	if ( 'DeleteInstance' == $action ) {

		$request	= 'POST';
		$api_data	= array( "instanceName"	=> $server_id, "forceDeleteAddOns" => true );

	} else {

		$request	= 'POST';
		$api_data	= array( "instanceName"	=> $server_id );

	}

	// Delete the Instance API Data to Force update
	$data = get_option( 'wpcs_vultr_api_data' );
	if ( isset( $data['servers'] ) ) {
		unset( $data['servers'] );
		update_option( 'wpcs_vultr_api_data', $data );
	}

	$request	= 'POST';
	$api_data	= array( "SUBID"	=> $server_id );
	
	$api_response = WPCS_Vultr()->api->call_api( 'server/' . $action, $api_data, false, 0, $request, $enable_response, 'server_action' );
	
	return $api_response;
}

/**
 * Call to API to get Domain Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_domains( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['domains'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "domains", null, false, 0, 'GET', false, 'vultr_domain_list' );
		$data['domains'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['domains'] ) ) ? $data['domains'] : false;

}

/**
 * Call to API to get Firewall Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_firewall( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['firewalls'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "firewall/rule_list", null, false, 0, 'GET', $enable_response, 'vultr_firewall_list' );
		$data['firewalls'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['firewalls'] ) ) ? $data['firewalls'] : false;

}

/**
 * Call to API to get Network Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_network( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['firewalls'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "network/list", null, false, 0, 'GET', $enable_response, 'vultr_firewall_list' );
		$data['firewalls'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['firewalls'] ) ) ? $data['firewalls'] : false;

}

/**
 * Call to API to get Reserved IP Info
 *
 * @since  3.0.6
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_reserved_ip( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['reservedip'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "reservedip/list", null, false, 0, 'GET', $enable_response, 'vultr_reservedip_list' );
		$data['reservedip'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['reservedip'] ) ) ? $data['reservedip'] : false;

}

/**
 * Call to API to get Backup Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_backups( $server_id = null, $enable_response = false ) {

	$api_data = ( !empty( $server ) ) ? array( 'SUBID' => $server_id ) : null;

	$databases = WPCS_Vultr()->api->call_api( "backup/list", $api_data, false, 0, 'GET', $enable_response, 'vultr_reservedip_list' );
	$data['backups'] = $databases;
	
	return ( is_array( $data['backups'] ) ) ? $data['backups'] : false;
}

/**
 * Call to API to get Snapshot Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_snapshots( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['snapshots'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "snapshot/list", null, false, 0, 'GET', $enable_response, 'vultr_reservedip_list' );
		$data['snapshots'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['snapshots'] ) ) ? $data['snapshots'] : false;

}

/**
 * Call to API to get Snapshot Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_block_storage( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['blocks'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "block/list", null, false, 0, 'GET', $enable_response, 'vultr_reservedip_list' );
		$data['blocks'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['blocks'] ) ) ? $data['blocks'] : false;

}

/**
 * Call to API to get Snapshot Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_vultr_call_api_list_firewall_groups( $enable_response = false ) {

	$data = get_option( 'wpcs_vultr_api_data' );

	if ( !isset( $data['firewalls'] ) ) {
		$databases = WPCS_Vultr()->api->call_api( "firewall/group_list", null, false, 0, 'GET', $enable_response, 'vultr_reservedip_list' );
		$data['firewalls'] = $databases;
		update_option( 'wpcs_vultr_api_data', $data );
	}
	
	return ( is_array( $data['firewalls'] ) ) ? $data['firewalls'] : false;

}