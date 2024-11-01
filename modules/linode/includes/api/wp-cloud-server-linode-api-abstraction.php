<?php
/**
 * WP Cloud Server - Linode Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
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
function wpcs_linode_region_map( $region_id ) {
	
	$location_list = array(
			"us-east"			=> 	"Newark",
			"us-central"		=>	"Dallas",
			"us-west"			=> 	"Fremont",
			"us-southeast"		=>	"Atlanta",
			"ca-central"		=> 	"Toronto",
			"eu-west"			=>	"London",
			"eu-central"		=> 	"Frankfurt",
			"ap-south"			=>	"Singapore",
			"ap-northeast"		=> 	"Tokyo",
			"ap-west"			=>	"Mumbai",
			"ap-southeast"		=> 	"Sydney",
		);
	
	$location = $location_list[ $region_id ];
	
	return $location;
	
}

/**
 * Retrieves a list of Regions
 *
 * @since  1.0
 *
 * @return regions List of available regions
 */
function wpcs_linode_memory_map( $value ) {
	
	$size_list = array(
			"1024"		=> 	"1",
			"2048"		=>	"2",
			"4096"		=> 	"4",
			"8192"		=>	"8",
			"16384"		=> 	"16",
			"25600"		=>	"25",
			"32768"		=>	"32",
			"51200"		=>	"50",
			"65536"		=> 	"64",
			"81920"		=>	"80",
			"98304"		=>	"96",
			"131072"	=> 	"128",
			"163840"	=>	"160",
			"327680"	=>	"320",
			"512000"	=>	"500",
			"655360"	=> 	"640",
			"819200"	=>	"800",
			"983040"	=>	"960",
			"1310720"	=> 	"1280",
	);
	
	$size = ( array_key_exists( $value, $size_list ) ) ? $size_list[ $value ] : $value ;
	
	return $size;
	
}

/**
 * Retrieves a list of Regions
 *
 * @since  1.0
 *
 * @return regions List of available regions
 */
function wpcs_linode_os_map( $image ) {
	
	$image_list = array(
			"Ubuntu 20.04 x64"	=> 	"Ubuntu 20.04 LTS",
			"Ubuntu 18.04 x64"	=> 	"Ubuntu 18.04 LTS",
			"Ubuntu 16.04 x64"	=>	"Ubuntu 16.04 LTS",
			"Debian 10 x64"		=>	"Debian 10",
			"Debian 9 x64"		=>	"Debian 9",
			"CentOS 8 x64"		=>	"CentOS 8",
			"CentOS 7 x64"		=>	"CentOS 7",
			"Fedora 32 x64"		=>	"Fedora 32",
			"Fedora 31 x64"		=>	"Fedora 31",
		);
	
	$os_image = ( array_key_exists( $image, $image_list ) ) ? $image_list[ $image ] : $image ;
	
	return $os_image;
	
}

/**
 * Retrieves a list of Regions
 *
 * @since  1.0
 *
 * @return regions List of available regions
 */
function wpcs_linode_regions_list() {

	$regions = get_option( 'wpcs_linode_api_data' );

	if ( !isset( $regions['regions'] ) ) {
		$api = new WP_Cloud_Server_Linode_API;
		$regions['regions'] = $api->call_api( 'regions', null, false, 900, 'GET', false, 'linode_region_list' );
	}
	
	if ( isset( $regions['regions'] ) ) {
		foreach ( $regions['regions'] as $region ) {
			if ( isset( $region['country'] ) ) {
				$regions_list[$region['id']]['name'] = wpcs_linode_region_map( $region['id'] );
				$regions_list[$region['id']]['country'] = $region['country'];
				$regions_list[$region['id']]['id'] = $region['id'];
			}
		}
		return $regions_list;
	}
	
	return $regions;
}

/**
 * Retrieves a list of Plans
 *
 * @since  1.0
 *
 * @return plans List of available plans
 */
function wpcs_linode_plans_list() {

	$plans = get_option( 'wpcs_linode_api_data' );

	if ( !isset( $plans['types'] ) ) {
		// Create instance of the Linode API
		$api	= new WP_Cloud_Server_Linode_API;
		$plans['types'] = $api->call_api( 'linode/types', null, false, 900, 'GET', false, 'linode_plan_list' );
	}
	
	if ( isset( $plans['types'] ) ) {
		foreach ( $plans['types'] as $plan ) {
			if ( isset( $plan['label'] ) ) {
				$plan_list[$plan['id']]['id']		= $plan['id'];
				$plan_list[$plan['id']]['label']	= $plan['label'];
			}
		}
		return $plan_list;
	}
	
	return $plans;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_linode_os_list( $image = null, $return_label = false ) {
	
	$image	= wpcs_linode_os_map( $image );

	$plans	= get_option( 'wpcs_linode_api_data' );

	if ( !isset( $plans['images']['data'] ) ) {
		// Create instance of the Linode API
		$api	= new WP_Cloud_Server_Linode_API;
		$plans['images'] = $api->call_api( 'images', null, false, 900, 'GET', false, 'linode_os_list' );
	}
	
	if ( isset( $plans ) && is_array( $plans ) ) {
		foreach ( $plans['images']['data'] as $plan ) {
			if ( ( $plan['label'] == $image ) || ( $plan['id'] == $image ) ) {
				$value = ( ! $return_label ) ? $plan['id'] : $plan['label'] ;
				return $value;
			}
		}
	}
	
	$plans = array(
		'linode/ubuntu20.04'	=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'linode/ubuntu18.04'	=>	array( 'name' => 'Ubuntu 18.04 x64'),
		'linode/ubuntu16.04'	=>	array( 'name' => 'Ubuntu 16.04 x64'),
		'linode/centos8'		=>	array( 'name' => 'CentOS 8 x64'),
		'linode/centos7'		=>	array( 'name' => 'CentOS 7 x64'),
		'linode/debian10'		=>	array( 'name' => 'Debian 10 x64'),
		'linode/debian9'		=>	array( 'name' => 'Debian 9 x64'),
		'linode/fedora32'		=>	array( 'name' => 'Fedora 32 x64'),
		'linode/fedora31'		=>	array( 'name' => 'Fedora 31 x64'),
	);
	
	return $plans;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_linode_managed_os_list( $image = null, $return_label = false ) {
	
	$image		= wpcs_linode_os_map( $image );
	
	//$class_name	= "WP_Cloud_Server_Linode_API";
	
	// Create instance of the Linode API
	//$api		= new $class_name();
	
	//$plans = $api->call_api( 'images', null, false, 900, 'GET', false, 'linode_os_list' );

	$plans = get_option( 'wpcs_linode_api_data' );
	
	if ( isset( $plans ) ) {
		foreach ( $plans['images']['data'] as $plan ) {
			if ( ( $plan['label'] == $image ) || ( $plan['id'] == $image ) ) {
				$value = ( ! $return_label ) ? $plan['id'] : $plan['label'] ;
				return $value;
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
function wpcs_linode_availability_list( $region=null ) {
	
	$class_name = "WP_Cloud_Server_Linode_API";
	
	// Create instance of the Linode API
	$api = new $class_name();

	$plans = get_option( 'wpcs_linode_api_data' );

	if ( !isset( $plans['types']['data'] ) ) {

		$plans['types'] = $api->call_api( 'linode/types', null, false, 900, 'GET', false, 'linode_plan_list' );

	}
	
	if ( !empty( $plans ) ) {
		foreach ( $plans['types']['data'] as $plan ) {

			$memory		= wpcs_linode_memory_map( $plan['memory'] );
			$disk		= wpcs_linode_memory_map( $plan['disk'] );
			$plan_name 	= "{$plan['label']} : {$plan['vcpus']} vCPU, {$memory}GB, {$disk}GB SSD";
			$plan_cost	= "(\${$plan['price']['monthly']} per month)";

			if ( isset( $plan['label'] ) ) {
				$available[ $plan['class'] ][ $plan['id'] ] = array( 'name' => $plan_name, 'cost' => $plan_cost );
			}
		}
		return $available;
	}
	
	return false;
}

/**
 * Linode API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_linode_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	// Exit if checking Linode Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$script_id = null;
	
	$api = new WP_Cloud_Server_Linode_API();
	
	$model = ( 'droplets' == $model ) ? 'server/create' : $model;
	
	if ( isset( $api_data['user_data'] ) ) {
		
		$label = $api_data['name'];
		
		$script_name = "{$label}-config-script";
		
		// This strips out spurious ^m from the WP Classic Editor
		$file = str_ireplace("\x0D", "", $api_data['user_data']);
				
		$script_data = array(
			"label"			=>	$script_name,
			"description"	=>	"Cloud Server Config Script",
			"images"		=>	array(
									"linode/ubuntu20.04",
									"linode/ubuntu18.04",
									"linode/ubuntu16.04lts",
								),
			"is_public"		=> 	false,
      		"rev_note"		=>	"Cloud Server Config Script",
			"script"		=>	$file,
		);
				
		$new_script	= $api->call_api( 'linode/stackscripts', $script_data, false, 0, 'POST', false, 'install_script' );
		$script_id	= $new_script['id'];
								  
		$debug['script_id'] = $script_id;
			
	}
	
    // Set-up the data for the new Linode Server
    $app_data = array(
        "label"				=>	$api_data["name"],
        "region"			=>	$api_data["region"],
        "type"				=>	$api_data["size"],
        "image"				=> 	$api_data["image"],
    );
	
	if ( isset( $script_id ) ) {
		$app_data["stackscript_id"] = $script_id;	
	}

	if ( isset( $api_data['backups'] ) && $api_data['backups'] ) {
		$app_data['backups_enabled'] = true;
	}
	
	update_option( 'wpcs_linode_sp_install_script', $api_data );

	if ( isset( $api_data['custom_settings']['ssh_key'] ) ) {
		$app_data["authorized_keys"][] = $api_data['custom_settings']['ssh_key'];	
	}
	
	if ( isset( $api_data['custom_settings']['root_pass'] ) ) {
		$app_data["root_pass"] = $api_data['custom_settings']['root_pass'];	
	} else {
		$app_data["root_pass"] = wp_generate_password( 20, false, false );
	}
	
	update_option( 'wpcs_linode_server_config', $app_data );

    // Send the API POST request to create the new 'server'
    $response = wpcs_linode_call_api_create_server( $app_data, false );
		
    update_option( 'linode_create_server_api_response', $response );
	
	return $response;

}

/**
 * Retrieves a list of Region Names
 *
 * @since  1.0
 *
 * @return regions List of all region names
 */
function wpcs_linode_cloud_regions() {
	
	$regions = wpcs_linode_regions_list();
		
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
function wpcs_linode_api_response_valid( $response ) {

	return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_linode_server_complete( $server_sub_id, $queued_server, $host_name, $server_location ) {
	
	$api = new WP_Cloud_Server_Linode_API();
	
	$server_info = wpcs_linode_call_api_server_info( $server_sub_id, false );
	$status = $server_info['status'];
	
	$response = $queued_server['response'];
			
	// Update Log with new website creation
	if ( ! $response || isset( $response['errors'] ) ) {
		$status	= 'Failed';
		$api_data = get_option( 'wpcs_linode_api_last_response' );
		$error = $response['errors']['reason'];
		$message = 'An Error Occurred ( ' . $error . ' )';
	} else {
		$status = 'Success';
		$message = 'New Server Created ( ' . $server_info['label'] . ' )';
				
		$d = new DateTime( $server_info['created'] );
	}
					
	wpcs_linode_log_event( 'Linode', $status, $message );
	
	// Save the server details for future use
	$server_data = array(
			"id"			=>	$server_info['id'],
			"name"			=>	$server_info['label'],
			"location"		=>	$server_info['region'],
			"slug"			=>	sanitize_title($server_info['label']),
			"ram"			=>	$server_info['specs']['memory'],
			"vcpus"			=>	$server_info['specs']['vcpus'],
			"disk"			=>	$server_info['specs']['disk'],
			"size"			=>	$server_info['type'],
			"os"			=> 	$server_info['image'],
			"ip_address"	=>	$server_info['ipv4'],
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
function wpcs_linode_call_api_health_check( ) {

	$api = new WP_Cloud_Server_Linode_API();

	$api_response = $api->call_api( 'account', null, false, 0, 'GET', true, 'api_health' );

	return $api_response;

}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_list_servers( $enable_response = false ) {

	$data = get_option( 'wpcs_linode_api_data' );

	if ( isset( $data['instances'] ) && empty( $data['instances'] ) ) {
		$servers = WPCS_Linode()->api->call_api( "linode/instances", null, false, 900, 'GET', false, 'linode_instances_list' );
		if ( isset( $servers['data'] ) ) {
			$data['instances'] = $servers['data'];
			update_option( 'wpcs_linode_api_data', $data );
		}
	}

	return ( isset( $data['instances'] ) ) ? $data['instances'] : false;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Linode_API();

	$api_response = $api->call_api( "linode/instances/{$server_sub_id}", null, false, 0, 'GET', $enable_response, 'server_status' );

	return $api_response;

}

/**
 * Call to API to Create New Server
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_create_server( $api_data = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_Linode_API();

	$api_response = $api->call_api( 'linode/instances', $api_data, false, 0, 'POST', $enable_response, 'server_creation' );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_update_server( $model = 'linode/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api = new WP_Cloud_Server_Linode_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_linode_update_server_api_last_response', $api_response );

	return $api_response;

}

/**
 * Get a List of Linode OS Images
 *
 * Retrieves a List of Linode OS Images.
 *
 * @since  1.2.0
 *
 * @return  os_images     List of all OS Images
 */
function wpcs_linode_ssh_key( $ssh_key_name ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$api = new WP_Cloud_Server_Linode_API();

	// Retrieve the SSH Key data
	$ssh_key_list = get_option( 'wpcs_serverpilots_ssh_keys');


	$debug['name'] = $ssh_key_name;
	$debug['list'] = $ssh_key_list;

	if ( !empty( $ssh_key_list ) ) {
		foreach ( $ssh_key_list as $key => $ssh_key ) {
			if ( $ssh_key_name == $ssh_key['name'] ) { 
				$ssh_key_data = array(
					"label"		=>  $ssh_key['name'],
					"ssh_key"	=>  $ssh_key['public_key'], 
				);
			}
		}
	} 
			
	// Retrieve list of existing Linode SSH Keys
	$ssh_keys = $api->call_api( 'profile/sshkeys', null, false, 900, 'GET', false, 'list_ssh_keys' );
	//$ssh_keys = call_user_func( "wpcs_linode_cloud_server_api", null, 'profile/sshkeys', null, false, 0, 'GET', false, 'list_ssh_keys' );
	
	$debug['get'] = $ssh_keys;

	if ( !empty( $ssh_keys ) ) {
		foreach ( $ssh_keys['data'] as $key => $ssh_key ) {
			if ( $ssh_key_data['label'] == $ssh_key['label'] ) {
				return $ssh_key_id = $ssh_key['ssh_key'];
			}
		}
	}

	// SSH Key is NOT known to Linode so we need to add it
	//$ssh_key = call_user_func( "wpcs_linode_cloud_server_api", null, 'profile/sshkeys', $ssh_key_data, false, 0, 'POST', false, 'add_ssh_key' );
	$ssh_key = $api->call_api( 'profile/sshkeys', $ssh_key_data, false, 900, 'POST', false, 'add_ssh_key' );
	
	$debug['add'] = $ssh_key;
	
	update_option( 'wpcs_linode_ssh_key_api_response', $debug );

	if ( isset($ssh_key['ssh_key']) ) {
		return $ssh_key['ssh_key'];
	}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return false;
    
}

/**
 * Linode Cloud Server Action Function
 *
 * Allows access to the Linode API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.3
 *
 * @return response The response from the Linode API call
 */
function wpcs_linode_cloud_server_action( $action, $server_id, $enable_response = false ) {

	if ( 'delete' == $action ) {

		$request	= 'DELETE';
		$model		= "linode/instances/{$server_id}";
		$api_data	= null;

	} elseif ( 'shutdown' == $action ) {

		$request	= 'POST';
		$model		= "linode/instances/{$server_id}/{$action}";
		$api_data	= null;

	} else {

		$request	= 'POST';
		$model		= "linode/instances/{$server_id}/{$action}";
		$api_data	= array( 'config_id' => null );

	}

	// Delete the Droplet API Data to Force update
	$data = get_option( 'wpcs_linode_api_data' );
	if ( isset( $data['instances'] ) ) {
		unset( $data['instances'] );
		update_option( 'wpcs_linode_api_data', $data );
	}
	
	$api_response = WPCS_Linode()->api->call_api( $model, $api_data, false, 0, $request, $enable_response, 'server_action' );
	
	return $api_response;
}

/**
 * Call to API to list Volumes
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_list_volumes( $api_data = null, $enable_response = false ) {

	$data = get_option( 'wpcs_linode_api_data' );

	if ( !isset( $data['volumes'] ) ) {
		$volumes = WPCS_Linode()->api->call_api( "volumes", null, false, 900, 'GET', false, 'linode_volume_list' );
		if ( isset( $data['volumes'] ) ) {
			$data['volumes'] = $volumes['data'];
			update_option( 'wpcs_linode_api_data', $data );
		}
	}

	return ( isset( $data['volumes'] ) ) ? $data['volumes'] : false;

}

/**
 * Call to API to list Domains
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_list_domains( $api_data = null, $enable_response = false ) {

	$data = get_option( 'wpcs_linode_api_data' );

	if ( !isset( $data['domains'] ) ) {
		$domains = WPCS_Linode()->api->call_api( "domains", null, false, 900, 'GET', false, 'linode_domains_list' );
		if ( isset( $data['domains'] ) ) {
			$data['domains'] = $domains['domains'];
			update_option( 'wpcs_linode_api_data', $data );
		}
	}

	return ( isset( $data['domains'] ) ) ? $data['domains'] : false;

}

/**
 * Call to API to list Linode Backups
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_linode_call_api_list_backups( $server_id ) {

	$api_response = WPCS_Linode()->api->call_api( "instances/{$server_id}/backups", null, false, 0, 'GET', false, 'backup_list' );

	return $api_response;

}