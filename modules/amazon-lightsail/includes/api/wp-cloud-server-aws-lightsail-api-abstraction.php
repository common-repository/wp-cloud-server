<?php
/**
 * WP Cloud Server - AWS Lightsail Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
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
function wpcs_aws_lightsail_regions_array() {
	
	$location = array(
			"us-east-2"			=> 	"Ohio",
			"us-east-1"			=>	"Virginia",
			"us-west-2"			=> 	"Oregon",
			"ap-south-1"		=>	"Mumbai",
			"ap-northeast-2"	=> 	"Seoul",
			"ap-southeast-1"	=>	"Singapore",
			"ap-southeast-2"	=> 	"Sydney",
			"ap-northeast-1"	=>	"Tokyo",
			"ca-central-1"		=> 	"Canada",
			"eu-central-1"		=>	"Frankfurt",
			"eu-north-1"		=> 	"Stockholm",
			"eu-west-1"			=> 	"Ireland",
			"eu-west-2"			=>	"London",
			"eu-west-3"			=> 	"Paris",
		);
	
	return $location;
	
}

/**
 * Retrieves a list of Regions
 *
 * @since  1.0
 *
 * @return regions List of available regions
 */
function wpcs_aws_lightsail_region_map( $region_id ) {
	
	$location_list = array(
			"us-east-2"			=> 	"Ohio",
			"us-east-1"			=>	"Virginia",
			"us-west-2"			=> 	"Oregon",
			"ap-south-1"		=>	"Mumbai",
			"ap-northeast-2"	=> 	"Seoul",
			"ap-southeast-1"	=>	"Singapore",
			"ap-southeast-2"	=> 	"Sydney",
			"ap-northeast-1"	=>	"Tokyo",
			"ca-central-1"		=> 	"Montreal",
			"eu-central-1"		=>	"Frankfurt",
			"eu-north-1"		=> 	"Stockholm",
			"eu-west-1"			=> 	"Ireland",
			"eu-west-2"			=>	"London",
			"eu-west-3"			=> 	"Paris",
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
function wpcs_aws_lightsail_os_map( $image ) {
	
	$image_list = array(
			"Amazon Linux"		=> 	"amazon_linux",
			"Ubuntu 20.04 x64"	=> 	"ubuntu_20_04",
			"Ubuntu 18.04 x64"	=> 	"ubuntu_18_04",
			"Ubuntu 16.04 x64"	=>	"ubuntu_16_04_2",
			"CentOS 7 x64"		=>	"centos_7_1901_01",
			"Debian 9 x64"		=>	"debian_9_5",
			"openSUSE 15.1"		=>	"opensuse_15_1",
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
function wpcs_aws_lightsail_regions_list() {

	$regions = get_option( 'wpcs_aws_lightsail_api_data' );

	if ( !isset( $regions['regions'] ) ) {
		// Create instance of the AWS Lightsail API
		$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
		$api_data = array(
        	'includeAvailabilityZones' => true,
        	'includeRelationalDatabaseAvailabilityZones' => false,
    	);

		$regions = $api->call_api( 'GetRegions', $api_data, false, 0, 'POST', false, 'get_regions' );
	}

	update_option( 'wpcs_get_blueprints', $regions );
	
	if ( !empty( $regions ) ) {
		foreach ( $regions['regions'] as $region ) {
			if ( isset( $region['name'] ) ) {
				$regions_list[$region['name']]['name'] = wpcs_aws_lightsail_region_map( $region['name'] );
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
function wpcs_aws_lightsail_plans_list() {
	
	$plans = get_option( 'wpcs_aws_lightsail_api_data' );

	if ( !isset( $plans['bundles'] ) ) {
		// Create instance of the AWS Lightsail API
		$api = new WP_Cloud_Server_AWS_Lightsail_API();

		$plans = $api->call_api( 'GetBundles', null, false, 900, 'POST', false, 'aws-lightsail_plan_list' );
	}
	
	if ( !empty( $plans['bundles'] ) ) {
		foreach ( $plans['bundles'] as $plan ) {
			if ( isset( $plan['name'] ) && ( 'LINUX_UNIX' == $plan['supportedPlatforms'][0] ) ) {
				$platform  	= ( 'LINUX_UNIX' == $plan['supportedPlatforms'][0] ) ? 'Linux' : 'Windows' ;
				$price  	= ( '3.5' == $plan['price'] ) ? '3.50' : $plan['price'] ;
				$plan_name 	= "{$plan['cpuCount']} CPU, {$plan['ramSizeInGb']}GB, {$plan['diskSizeInGb']}GB SSD ({$platform}) (\${$price}/month)";
				$plan_list[$plan['bundleId']]['id']		= $plan['bundleId'];
				$plan_list[$plan['bundleId']]['name']	= $plan_name;
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
function wpcs_aws_lightsail_os_list( $image = null, $return_label = true ) {
	
	$image		= wpcs_aws_lightsail_os_map( $image );
	
	$blueprints = get_option( 'wpcs_aws_lightsail_api_data' );

	if ( !isset( $blueprints['blueprints'] ) ) {
		// Create instance of the AWS Lightsail API
		$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
		$api_data = array(
			'includeInactive' => false,
		);

		$blueprints = $api->call_api( 'GetBlueprints', $api_data, false, 900, 'POST', false, 'aws_lightsail_blueprints' );
	}
	
	if ( !empty( $image ) ) { 
		if ( !empty( $blueprints['blueprints'] ) ) {
			foreach ( $blueprints['blueprints'] as $blueprint ) {
				if ( ( $blueprint['blueprintId'] == $image ) || ( $blueprint['name'] == $image ) ) {
					$value = ( ! $return_label ) ? $blueprint['name'] : $blueprint['blueprintId'] ;
					return $value;
				}
			}
		}
	}
	 
	if ( !empty( $blueprints['blueprints'] ) ) {
		foreach ( $blueprints['blueprints'] as $blueprint ) {
			if ( 'LINUX_UNIX' == $blueprint['platform'] ) {
				$images[$blueprint['type']][$blueprint['blueprintId']] = array( 'name' => "{$blueprint['name']} {$blueprint['version']}" ) ;
			}
		}
	}	
	
	//if ( !empty( $blueprints ) ) {
	//	$blueprints = array(
	//		'amazon_linux'		=>	array( 'name' => 'Amazon Linux'),		
	//		'ubuntu_18_04'		=>	array( 'name' => 'Ubuntu 18.04 x64'),
	//		'ubuntu_16_04_2'	=>	array( 'name' => 'Ubuntu 16.04 x64'),
	//		'centos_7_1901_01'	=>	array( 'name' => 'CentOS 7 x64'),
	//		'debian_9_5'		=>	array( 'name' => 'Debian 9 x64'),
	//		'opensuse_15_1'		=>	array( 'name' => 'openSUSE 15.1'),
	//);
	//}
	
	return ( isset( $images ) ) ? $images : false;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_aws_lightsail_managed_os_list( $image = null, $return_label = true ) {
	
	$image		= wpcs_aws_lightsail_os_map( $image );
	
	$class_name	= "WP_Cloud_Server_AWS_Lightsail_API";
	
	// Create instance of the AWS Lightsail API
	$api		= new $class_name();
	
	$api_data = array(
        'includeInactive' => false,
    );
	
	$blueprints = get_option( 'wpcs_aws_lightsail_api_data' );
	
	if ( isset( $blueprints ) ) {
		foreach ( $blueprints['blueprints'] as $blueprint ) {
			if ( ( $blueprint['blueprintId'] == $image ) || ( $blueprint['name'] == $image ) ) {
				$value = ( ! $return_label ) ? $blueprint['name'] : $blueprint['blueprintId'] ;
				return $value;
			}
		}
		
	}
	
	$blueprints = array(		
		'ubuntu_20_04'		=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'ubuntu_18_04'		=>	array( 'name' => 'Ubuntu 18.04 x64'),
	);
	
	return $blueprints;
}

/**
 * Retrieves a list of Available Plans by Region
 *
 * @since  1.0
 *
 * @return regions List of available plans by regions
 */
function wpcs_aws_lightsail_availability_list( $region=null ) {
	
	$class_name = "WP_Cloud_Server_AWS_Lightsail_API";
	
	// Create instance of the AWS Lightsail API
	$api = new $class_name();
	
	$plans = get_option( 'wpcs_aws_lightsail_api_data' );
	
	update_option( 'lightsail_bundles', $plans );
	
	if ( !empty( $plans ) ) {
		foreach ( $plans['bundles'] as $plan ) {
			if ( isset( $plan['name'] ) && ( 'LINUX_UNIX' == $plan['supportedPlatforms'][0] ) ) {
				$platform  	= ( 'LINUX_UNIX' == $plan['supportedPlatforms'][0] ) ? 'Linux' : 'Windows' ;
				$price  	= ( '3.5' == $plan['price'] ) ? '3.50' : $plan['price'] ;
				$plan_name 	= "{$plan['cpuCount']}vCPU, {$plan['ramSizeInGb']}GB, {$plan['diskSizeInGb']}GB SSD ({$platform})";
				$plan_cost	= "(\${$price} per month)";
				$available[ 'Server Bundle' ][ $plan['bundleId'] ] = array( 
													'name' 	=> $plan_name,
													'cost'	=> $plan_cost,
													'cpu' 	=> $plan['cpuCount'],
													'disk' 	=> $plan['diskSizeInGb'],
													'ram' 	=> $plan['ramSizeInGb'],
												);
			}
		}
	}
	
	return $available;
}

/**
 * AWS Lightsail API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_aws_lightsail_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	
	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	if ( 'check_data_centers' == $function ) {
		return false;
	}
	
	$script_id = null;
	
	$model = ( 'droplets' == $model ) ? 'server/create' : $model;
	
    // Set-up the data for the new AWS Lightsail Server
    $app_data = array(
        "instanceNames"		=>	array( $api_data["name"] ),
        //"availabilityZone"	=>	$api_data["region"],
        "bundleId"			=>	$api_data["size"],
        "blueprintId"		=> 	$api_data["image"],
    );
	
	if ( isset( $api_data['user_data'] ) ) {
		$app_data["userData"] = $api_data['user_data'];	
	}

	if ( isset( $api_data['custom_settings']['ssh_key'] ) ) {
		$app_data["keyPairName"] = $api_data['custom_settings']['ssh_key'];	
	}
	
	// Check for Automatic Snapshots
	if ( $api_data['backups'] ) {
		$app_data["addOns"][]= array(
			"addOnType" => 'AutoSnapshot',
		);
	}
	
	update_option( 'wpcs_aws_lightsail_server_config', $app_data );

    // Send the API POST request to create the new 'server'
    $response = wpcs_aws_lightsail_call_api_create_server( $app_data, false, $api_data["region"] );
		
    update_option( 'aws_lightsail_create_server_api_response', $response );
	
	return $response;

}

/**
 * Retrieves a list of Region Names
 *
 * @since  1.0
 *
 * @return regions List of all region names
 */
function wpcs_aws_lightsail_cloud_regions() {
	
	$regions = wpcs_aws_lightsail_regions_list();
		
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
function wpcs_aws_lightsail_api_response_valid( $response ) {

	return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_aws_lightsail_server_complete( $server_sub_id, $queued_server, $host_name, $region = null ) {
	
	$response = ( is_array( $queued_server ) ) ? $queued_server['response'] : $queued_server;
	
	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	$status = "pending";
	$x = 1;
	
	$app_data = array(
        'instanceName' => $host_name,
    );
			
	// Wait for the Server to Complete
	while ( ( "running" !== $status ) && ( $x <= 2500 ) ) {
		$actions = $api->call_api( 'GetInstanceState', $app_data, false, 0, 'POST', false, 'server_status', $region );
		if ( isset( $actions['state']['name'] ) ) {
			$status = $actions['state']['name'];
		}
   	 	$x++;
		$debug['counter']['status'] = $status;
		$debug['counter']['count'] = $x;
	}
	
	// Attach Static IP
	if ( 'running' == $status ) {
		
		if ( isset( $queued_server['static_ip'] ) && $queued_server['static_ip'] ) {
		
			$staticip = call_user_func("wpcs_aws_lightsail_attach_static_ip", $host_name, $region );
		
			$debug['staticip'] = $staticip;
		}
		
		if ( isset( $queued_server['open_port'] ) &&  $queued_server['open_port'] ) {
			
			$app_data = array(
        		'instanceName' 	=> $host_name,
				'portInfo'		=> array(
					'fromPort'		=> 34210,
					'protocol'		=> 'tcp',
				),
    		);
	
			$ports = $api->call_api( 'OpenInstancePublicPorts', $app_data, false, 0, 'POST', false, 'update_ports', $region );
		
			$debug['ports'] = $ports;
		}
	}
			
	// Update Log with new website creation
	if ( ! $response || isset( $response['operations']['errorCode'] ) ) {
		$status	= 'Failed';
		$api_data = get_option( 'wpcs_aws_lightsail_api_last_response' );
		$error = $response['operations']['errorDetails'];
		$message = 'An Error Occurred ( ' . $error . ' )';
	} else {
		$status = 'Success';
		$message = 'New Server Created ( ' . $host_name . ' )';
	}
				
	wpcs_aws_lightsail_log_event( 'AWS Lightsail', $status, $message );
	
	$server_info = wpcs_aws_lightsail_call_api_server_info( $host_name, $region, false );
	
	// Save the server details for future use
	if ( isset( $server_info['instance'] ) && is_array( $server_info['instance'] ) ) {
		$server_data = array(
			"id"			=>	$server_info['instance']['name'],
			"name"			=>	$server_info['instance']['name'],
			"location"		=>	$server_info['instance']['location']['regionName'],
			"slug"			=>	sanitize_title($server_info['instance']['name']),
			"ram"			=>	$server_info['instance']['hardware']['ramSizeInGb'],
			"vcpus"			=>	$server_info['instance']['hardware']['cpuCount'],
			"disk"			=>	$server_info['instance']['hardware']['disks'][0]['sizeInGb'],
			"size"			=>	$server_info['instance']['bundleId'],
			"os"			=> 	$server_info['instance']['blueprintName'],
			"ip_address"	=>	$server_info['instance']['publicIpAddress'],
			"state"			=>	$server_info['instance']['state']['name'],
			"completed"		=>	true,
		);
	}
	
	$debug['info'] = $server_info;
	$debug['data'] = $server_data;
	
	update_option( 'wpcs_aws_lightsail_static_ip_and_port_info', $debug );
	
	return ( isset( $server_data ) ) ? $server_data : $server_data['completed'] = false;
}

/**
 * Call to API for Health Check
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_call_api_health_check() {

	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	$api_data 	= array(
		'includeAvailabilityZones'						=> false,
        'includeRelationalDatabaseAvailabilityZones' 	=> false,
		);

	$api_response = $api->call_api( 'GetRegions', $api_data, false, 0, 'POST', true, 'api_health' );

	update_option( 'aws_api_health', $api_response );
	
	return ( isset( $api_response ) && ( $api_response ) ) ? $api_response : false;

}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_call_api_list_servers( $enable_response = false ) {

	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	$api_response = array();
	
	$api_data = array(
		'includeAvailabilityZones'						=> false,
        'includeRelationalDatabaseAvailabilityZones' 	=> false,
	);

	$api_regions		= get_option( 'wpcs_aws_lightsail_api_data', array() );

	//$api_regions = $api->call_api( 'GetRegions', $api_data, false, 0, 'POST', false, 'api_health' );
	
	if ( !empty($api_regions) ) {
		$server_exist = false;
		//update_option( 'lightsail_list_servers_regions', $api_regions );
		foreach ( $api_regions['regions'] as $key => $zone ) {
			$api_response = $api->call_api( 'GetInstances', null, false, 0, 'POST', $enable_response, 'get_servers', $zone['name'] );
			//update_option( 'lightsail_list_servers_instances', $api_response );
			if ( !empty( $api_response['instances'] ) ) {
				$server_exist = true;
				$server_list[$zone['name']] = $api_response['instances'];
			}
		}
	
	return ( $server_exist ) ? $server_list : false;

	}

	return false;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_call_api_server_info( $host_name, $region = null, $enable_response = false ) {

	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	$app_data = array(
        'instanceName' => $host_name,
    );

	$api_response = $api->call_api( "GetInstance", $app_data, false, 0, 'POST', $enable_response, 'server_status', $region );

	return $api_response;

}

/**
 * Call to API to get Server Info
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_check_bundle_type( $api_data, $region ) {
	
	$api_data['bundleId'] = ( 'ap-south-1' == $region ) ? str_replace( "_0", "_1", $api_data['bundleId'] ) : $api_data['bundleId'] ;
	$api_data['bundleId'] = ( 'ap-southeast-2' == $region ) ? str_replace( "_0", "_2", $api_data['bundleId'] ) : $api_data['bundleId'] ;

	return $api_data;

}

/**
 * Call to API to Create New Server
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_call_api_create_server( $api_data, $enable_response = false, $region ) {

	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	$app_data = array(
        'includeAvailabilityZones' => true,
        'includeRelationalDatabaseAvailabilityZones' => false,
    );
	
	$api_response = $api->call_api( 'GetRegions', $app_data, false, 900, 'POST', false, 'aws_lightsail_region_list', $region );
	
	update_option( 'wpcs_get_regions', $api_response );
	
	foreach ( $api_response['regions'] as $zone ) {
		if ( $zone['name'] == $region ) {
			$api_data['availabilityZone'] = $zone['availabilityZones'][0]['zoneName'];	
		}
	}
	
	$api_data = wpcs_aws_lightsail_check_bundle_type( $api_data, $region );

	$api_response = $api->call_api( 'CreateInstances', $api_data, false, 0, 'POST', $enable_response, 'server_creation', $region );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_aws_lightsail_call_api_update_server( $model = 'RebootInstance', $post = 'POST', $api_data = null,  $enable_response = false, $region ) {

	$api = new WP_Cloud_Server_AWS_Lightsail_API();

	$api_response = $api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server', $region );
	
	update_option( 'wpcs_aws_lightsail_update_server_api_last_response', $api_response );

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
function wpcs_aws_lightsail_ssh_key( $ssh_key_name, $region = null ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}
	
	$api = new WP_Cloud_Server_AWS_Lightsail_API();

	// Retrieve the SSH Key data
	$ssh_key_list = get_option( 'wpcs_serverpilots_ssh_keys');

	if ( !empty( $ssh_key_list ) ) {
		foreach ( $ssh_key_list as $key => $ssh_key ) {
			if ( $ssh_key_name == $ssh_key['name'] ) {
				// Set-up the data for the 
				$ssh_key_data = array(
					"label"		=>  str_replace( " ", "_", $ssh_key['name'] ),
					"ssh_key"	=>  $ssh_key['public_key'], 
				);
			}
		}
	} 
			
	// Retrieve list of existing DigitalOcean SSH Keys
	$ssh_keys = $api->call_api( 'GetKeyPairs', null, false, 900, 'POST', false, 'list_ssh_keys', $region );
	
	$debug['get'] = $ssh_keys;

	if ( !empty( $ssh_keys ) ) {
		foreach ( $ssh_keys['keyPairs'] as $key => $ssh_key ) {
			if ( $ssh_key_data['label'] == $ssh_key['name'] ) {
				return $ssh_key['name'];
			}
		}
	}
	
	$api_data = array(
			"keyPairName"		=>  $ssh_key_data['label'],
			"publicKeyBase64"	=>  $ssh_key_data['ssh_key'], 
	);

	// SSH Key is NOT known to AWS Lightsail so we need to add it
	$ssh_key = $api->call_api( 'ImportKeyPair', $api_data, false, 900, 'POST', false, 'add_ssh_key', $region );
	
	$debug['add'] = $ssh_key;
	
	update_option( 'wpcs_aws_lightsail_ssh_key_api_response', $debug );

	if ( isset($ssh_key['operation']['resourceName']) ) {
		return $ssh_key['operation']['resourceName'];
	}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return false;
    
}

/**
 * Get Static IP for Instance
 *
 * @since  3.0.3
 *
 * @return regions List of all region names
 */
function wpcs_aws_lightsail_get_static_ip( $instance_name, $region ) {
	
	$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
	// Allocate Static IP with Instance Name
	$api_data = array(
		"staticIpName"	=>  "{$instance_name}_static_ip", 
	);
	
	$debug['name'] = $instance_name;
	$debug['region'] = $region;
	
	$response = $api->call_api( 'AllocateStaticIp', $api_data, false, 900, 'POST', false, 'allocate_static_ip', $region );
	
	$debug['allocate'] = $response;
	
	//if ( 'Failed' == $response['operations'][0]['status'] ) {
		//return false;
	//}
		
	// Get Static IP
	$api_data = array(	
   		"staticIpName"	=> "{$instance_name}_static_ip",
	);
	
	$response = $api->call_api( 'GetStaticIp', $api_data, false, 900, 'POST', false, 'get_static_ip', $region );
	
	$debug['get'] = $response;
	update_option( 'wpcs_aws_lightsail_static_ip_debug', $debug );
			
	return ( isset( $response['staticIp']['ipAddress'] ) ) ? $response['staticIp']['ipAddress'] : false;
}

/**
 * Attach Static IP for Instance
 *
 * @since  3.0.3
 *
 * @return regions List of all region names
 */
function wpcs_aws_lightsail_attach_static_ip( $instance_name, $region ) {
	
	$debug	= get_option( 'wpcs_aws_lightsail_static_ip_debug', array() );
	
	$api	= new WP_Cloud_Server_AWS_Lightsail_API();
		
	// Attach Static IP to Instance
	$api_data = array(	
   		"instanceName"	=> $instance_name,
   		"staticIpName"	=> "{$instance_name}_static_ip",
	);
	
	$response = $api->call_api( 'AttachStaticIp', $api_data, false, 900, 'POST', false, 'attach_static_ip', $region );
	
	$debug['attach'] = $response;
	
	//if ( 'Failed' == $response['operations'][0]['status'] ) {
		//return false;
	//}
	
	update_option( 'wpcs_aws_lightsail_static_ip_debug', $debug );
			
	return true;
}

/**
 * AWS Lightsail Cloud Server Action Function
 *
 *
 * @since  3.0.3
 *
 * @return response The response from the DigitalOcean API call
 */
function wpcs_aws_lightsail_cloud_server_action( $action, $server_id, $server_region, $enable_response = false ) {

	if ( 'DeleteInstance' == $action ) {

		$request	= 'POST';
		$api_data	= array( "instanceName"	=> $server_id, "forceDeleteAddOns" => true );

	} else {

		$request	= 'POST';
		$api_data	= array( "instanceName"	=> $server_id );

	}

	// Delete the Instance API Data to Force update
	$data = get_option( 'wpcs_aws_lightsail_api_data' );
	if ( isset( $data['instances'] ) ) {
		unset( $data['instances'] );
		update_option( 'wpcs_aws_lightsail_api_data', $data );
	}

	$api_response = WPCS_AWS_Lightsail()->api->call_api( $action, $api_data, false, 0, $request, false, 'server_action', $server_region );
	
	update_option( 'wpcs_linode_model', $server_id);
	
	return $api_response;
	
}

/**
 * AWS Lightsail Cloud Server Server List
 *
 * @since  3.0.6
 *
 * @return response The response from the AWS Lightsail API call
 */
function wpcs_aws_lightsail_api_server_list( $enable_response = false ) {

	$api_data = array(
		'includeAvailabilityZones'						=> false,
		'includeRelationalDatabaseAvailabilityZones' 	=> false,
	);

	$data = get_option( 'wpcs_aws_lightsail_api_data' );

	if ( isset( $data['regions'] ) && !isset( $data['instances'] ) ) {
		foreach ( $data['regions'] as $zone ) {
			$instances = WPCS_AWS_Lightsail()->api->call_api( 'GetInstances', $api_data, false, 0, 'POST', false, 'get_servers', $zone['name'] );
			if ( !empty( $instances['instances'] ) ) {
				$data['instances'][$zone['name']]	= $instances['instances'];
			}
		}
		update_option( 'wpcs_aws_lightsail_api_data', $data );
	}

	return ( isset( $data['instances'] ) ) ? $data['instances'] : false;
	
}