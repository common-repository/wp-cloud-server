<?php
/**
 * The DigitalOcean Functions
 *
 * @author     Gary Jordan <gary@designedforpixels.com>
 * @since      1.2.0
 *
 * @package    WP_Cloud_Server
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * DigitalOcean Cloud Server API Call Function
 *
 * Allows access to the DigitalOcean API. Used as part of the Add-on Module Framework.
 *
 * @since  1.2.0
 *
 * @return response The response from the DigitalOcean API call
 */
function wpcs_digitalocean_cloud_server_api( $server_data, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	
	// Create instance of the DigitalOcean API
	$api = new WP_Cloud_Server_DigitalOcean_API();
	
	$api_response = $api->call_api( $model, $api_data, $cache, $cache_lifetime, $request, $enable_response, $function );
	
	if ( 'check_data_centers' == $function ) {	
	
		foreach ( $api_response['regions'] as $region ) {
			for ($x = 1; $x <= 3; $x++) {
				$region_slug = "{$server_data['region']}{$x}";
				if ( ( $region['available'] == 1 ) && ( $region_slug == $region['slug'] ) ) {
					if ( in_array( $server_data['size'], $region['sizes'] ) ) {
						$server_region['region'] = $region['slug'];
						return $server_region;
					}	
				}
			}
		}
		
	}
	
	return $api_response;
	
}

/**
 * Get a List of DigitalOcean Regions
 *
 * Retrieves a list of DigitalOcean regions by slug.
 *
 * @since  1.2.0
 *
 * @return  regions     List of all regions
 */
function wpcs_digitalocean_cloud_regions() {
	
	return $regions = array(
		'ams' => 'Amsterdam',
		'blr' => 'Bangalore',
		'fra' => 'Frankfurt',
		'lon' => 'London',
		'nyc' => 'New York',
		'sfo' => 'San Francisco',
		'sgp' => 'Singapore',
		'tor' => 'Toronto',
		);
}

/**
 * Get a List of DigitalOcean Regions
 *
 * Retrieves a list of DigitalOcean regions by slug.
 *
 * @since  1.2.0
 *
 * @return  regions     List of all regions
 */
function wpcs_digitalocean_regions_list() {
	
	return $regions = array(
		'ams' =>	array( 'name' => 'Amsterdam'),
		'blr' =>	array( 'name' => 'Bangalore'),
		'fra' =>	array( 'name' => 'Frankfurt'),
		'lon' =>	array( 'name' => 'London'),
		'nyc' =>	array( 'name' => 'New York'),
		'sfo' =>	array( 'name' => 'San Francisco'),
		'sgp' =>	array( 'name' => 'Singapore'),
		'tor' =>	array( 'name' => 'Toronto'),
		);
}

/**
 * Get a List of DigitalOcean Plans
 *
 * Retrieves a list of DigitalOcean Plans by slug.
 *
 * @since  1.2.0
 *
 * @return  plans     List of all plans
 */
function wpcs_digitalocean_plans_list() {
	
	return $plans = array(
		'Basic'							=> array(
			's-1vcpu-1gb'		=>	array( 'name' => '1 CPU, 1GB, 25GB SSD', 'cost' => '($5/month)'),
			's-1vcpu-2gb'		=>	array( 'name' => '1 CPU, 2GB, 50GB SSD', 'cost' => '($10/month)'),
			's-1vcpu-3gb'		=>	array( 'name' => '1 CPU, 3GB, 60GB SSD', 'cost' => '($15/month)'),
			's-2vcpu-2gb'		=>	array( 'name' => '2 CPUs, 2GB, 60GB SSD', 'cost' => '($15/month)'),
			's-3vcpu-1gb'		=>	array( 'name' => '3 CPUs, 1GB, 60GB SSD', 'cost' => '($15/month)'),
			's-2vcpu-4gb'		=>	array( 'name' => '2 CPUs, 4GB, 80GB SSD', 'cost' => '($20/month)'),
			's-4vcpu-8gb'		=>	array( 'name' => '4 CPUs, 8GB, 160GB SSD', 'cost' => '($40/month)'),
			's-8vcpu-16gb'		=>	array( 'name' => '8 CPUs, 16GB, 320GB SSD', 'cost' => '($80/month)'),
		),
		'General Purpose (1 x SSD)'		=> array(
			'g-2vcpu-8gb'		=>	array( 'name' => '2 CPUs, 8GB, 25GB SSD', 'cost' => '($60/month)'),
			'g-4vcpu-16gb'		=>	array( 'name' => '4 CPUs, 16GB, 50GB SSD', 'cost' => '($120/month)'),
			'g-8vcpu-32gb' 		=>	array( 'name' => '8 CPUs, 32GB, 100GB SSD', 'cost' => '($240/month)'),
			'g-16vcpu-64gb' 	=>	array( 'name' => '16 CPUs, 64GB, 200GB SSD', 'cost' => '($480/month)'),
			'g-32vcpu-128gb' 	=>	array( 'name' => '32 CPUs, 128GB, 400GB SSD', 'cost' => '($960/month)'),
			'g-40vcpu-160gb' 	=>	array( 'name' => '40 CPUs, 160GB, 500GB SSD', 'cost' => '($1200/month)'),
		),
		'CPU-Optimized (1 x SSD)'		=> array(
			'c-2'				=>	array( 'name' => '2 CPUs, 4GB, 25GB SSD', 'cost' => '($40/month)'),
			'c-4'				=>	array( 'name' => '4 CPUs, 8GB, 50GB SSD', 'cost' => '($80/month)'),
			'c-8'				=>	array( 'name' => '8 CPUs, 16GB, 100GB SSD', 'cost' => '($160/month)'),
			'c-16' 				=>	array( 'name' => '16 CPUs, 32GB, 200GB SSD', 'cost' => '($320/month)'),
			'c-32' 				=>	array( 'name' => '32 CPUs, 64GB, 400GB SSD', 'cost' => '($640/month)'),
		),
		'CPU-Optimized (2 x SSD)'		=> array(
			'c2-2vcpu-4gb'		=>	array( 'name' => '2 CPUs, 4GB, 50GB SSD', 'cost' => '($45/month)'),
			'c2-4vpcu-8gb'		=>	array( 'name' => '4 CPUs, 8GB, 100GB SSD', 'cost' => '($90/month)'),
			'c2-8vpcu-16gb'		=>	array( 'name' => '8 CPUs, 16GB, 200GB SSD', 'cost' => '($180/month)'),
			'c2-16vcpu-32gb'	=>	array( 'name' => '16 CPUs, 32GB, 400GB SSD', 'cost' => '($360/month)'),
			'c2-32vpcu-64gb' 	=>	array( 'name' => '32 CPUs, 64GB, 800GB SSD', 'cost' => '($720/month)'),
		),
		'Memory-Optimized (1 x SSD)'	=> array(
			'm-2vcpu-16gb' 		=>	array( 'name' => '2 CPUs, 16GB, 50GB SSD', 'cost' => '($90/month)'),
			'm-4vcpu-32gb'		=>	array( 'name' => '4 CPUs, 32GB, 100GB SSD', 'cost' => '($180/month)'),
			'm-8vcpu-64gb' 		=>	array( 'name' => '8 CPUs, 64GB, 200GB SSD', 'cost' => '($360/month)'),
			'm-16vcpu-128gb' 	=>	array( 'name' => '16 CPUs, 128GB, 400GB SSD', 'cost' => '($720/month)'),
			'm-24vcpu-192gb' 	=>	array( 'name' => '24 CPUs, 192GB, 600GB SSD', 'cost' => '($1080/month)'),
			'm-32vcpu-256gb' 	=>	array( 'name' => '32 CPUs, 256GB, 800GB SSD', 'cost' => '($1440/month)'),
		), 
		);
}

/**
 * Get a List of DigitalOcean Plans
 *
 * Retrieves the Availability of DigitalOcean Plans by slug.
 *
 * @since  1.2.0
 *
 * @return  plans     List of all plans
 */
function wpcs_digitalocean_availability_list( $region=null ) {
	
	return $plans = array(
		'Basic'							=> array(
			's-1vcpu-1gb'		=>	array( 'name' => '1 CPU, 1GB, 25GB SSD', 'cost' 	=> '($5/month)'),
			's-1vcpu-2gb'		=>	array( 'name' => '1 CPU, 2GB, 50GB SSD', 'cost' 	=> '($10/month)'),
			's-1vcpu-3gb'		=>	array( 'name' => '1 CPU, 3GB, 60GB SSD', 'cost' 	=> '($15/month)'),
			's-2vcpu-2gb'		=>	array( 'name' => '2 CPUs, 2GB, 60GB SSD', 'cost' 	=> '($15/month)'),
			's-3vcpu-1gb'		=>	array( 'name' => '3 CPUs, 1GB, 60GB SSD', 'cost' 	=> '($15/month)'),
			's-2vcpu-4gb'		=>	array( 'name' => '2 CPUs, 4GB, 80GB SSD', 'cost' 	=> '($20/month)'),
			's-4vcpu-8gb'		=>	array( 'name' => '4 CPUs, 8GB, 160GB SSD', 'cost' 	=> '($40/month)'),
			's-8vcpu-16gb'		=>	array( 'name' => '8 CPUs, 16GB, 320GB SSD', 'cost' 	=> '($80/month)'),
		),
		'General Purpose (1 x SSD)'		=> array(
			'g-2vcpu-8gb'		=>	array( 'name' => '2 CPUs, 8GB, 25GB SSD', 'cost' => '($60/month)'),
			'g-4vcpu-16gb'		=>	array( 'name' => '4 CPUs, 16GB, 50GB SSD', 'cost' => '($120/month)'),
			'g-8vcpu-32gb' 		=>	array( 'name' => '8 CPUs, 32GB, 100GB SSD', 'cost' => '($240/month)'),
			'g-16vcpu-64gb' 	=>	array( 'name' => '16 CPUs, 64GB, 200GB SSD', 'cost' => '($480/month)'),
			'g-32vcpu-128gb' 	=>	array( 'name' => '32 CPUs, 128GB, 400GB SSD', 'cost' => '($960/month)'),
			'g-40vcpu-160gb' 	=>	array( 'name' => '40 CPUs, 160GB, 500GB SSD', 'cost' => '($1200/month)'),
		),
		'CPU-Optimized (1 x SSD)'		=> array(
			'c-2'				=>	array( 'name' => '2 CPUs, 4GB, 25GB SSD', 'cost' => '($40/month)'),
			'c-4'				=>	array( 'name' => '4 CPUs, 8GB, 50GB SSD', 'cost' => '($80/month)'),
			'c-8'				=>	array( 'name' => '8 CPUs, 16GB, 100GB SSD', 'cost' => '($160/month)'),
			'c-16' 				=>	array( 'name' => '16 CPUs, 32GB, 200GB SSD', 'cost' => '($320/month)'),
			'c-32' 				=>	array( 'name' => '32 CPUs, 64GB, 400GB SSD', 'cost' => '($640/month)'),
		),
		'CPU-Optimized (2 x SSD)'		=> array(
			'c2-2vcpu-4gb'		=>	array( 'name' => '2 CPUs, 4GB, 50GB SSD', 'cost' => '($45/month)'),
			'c2-4vpcu-8gb'		=>	array( 'name' => '4 CPUs, 8GB, 100GB SSD', 'cost' => '($90/month)'),
			'c2-8vpcu-16gb'		=>	array( 'name' => '8 CPUs, 16GB, 200GB SSD', 'cost' => '($180/month)'),
			'c2-16vcpu-32gb'	=>	array( 'name' => '16 CPUs, 32GB, 400GB SSD', 'cost' => '($360/month)'),
			'c2-32vpcu-64gb' 	=>	array( 'name' => '32 CPUs, 64GB, 800GB SSD', 'cost' => '($720/month)'),
		),
		'Memory-Optimized (1 x SSD)'	=> array(
			'm-2vcpu-16gb' 		=>	array( 'name' => '2 CPUs, 16GB, 50GB SSD', 'cost' => '($90/month)'),
			'm-4vcpu-32gb'		=>	array( 'name' => '4 CPUs, 32GB, 100GB SSD', 'cost' => '($180/month)'),
			'm-8vcpu-64gb' 		=>	array( 'name' => '8 CPUs, 64GB, 200GB SSD', 'cost' => '($360/month)'),
			'm-16vcpu-128gb' 	=>	array( 'name' => '16 CPUs, 128GB, 400GB SSD', 'cost' => '($720/month)'),
			'm-24vcpu-192gb' 	=>	array( 'name' => '24 CPUs, 192GB, 600GB SSD', 'cost' => '($1080/month)'),
			'm-32vcpu-256gb' 	=>	array( 'name' => '32 CPUs, 256GB, 800GB SSD', 'cost' => '($1440/month)'),
		),
	);
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
function wpcs_digitalocean_os_list( $image = null ) {
	
	$plans = array(
		'ubuntu-20-04-x64'	=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'ubuntu-18-04-x64'	=>	array( 'name' => 'Ubuntu 18.04 x64'),
		'ubuntu-16-04-x64'	=>	array( 'name' => 'Ubuntu 16.04 x64'),
		'centos-8-x64'		=>	array( 'name' => 'CentOS 8 x64'),
		'centos-7-x64'		=>	array( 'name' => 'CentOS 7 x64'),
		'debian-10-x64'		=>	array( 'name' => 'Debian 10 x64'),
		'debian-9-x64'		=>	array( 'name' => 'Debian 9 x64'),
		'fedora-32-x64'		=>	array( 'name' => 'Fedora 32 x64'),
		'fedora-31-x64'		=>	array( 'name' => 'Fedora 31 x64'),
		);
	
	if ( isset( $image ) ) {
		foreach ( $plans as $key => $plan ) {
			if ( $plan['name'] == $image ) {
				return $key;
			}
		}
		
	}
	
	return $plans;
    
}

/**
 * Get a List of DigitalOcean OS Images for Managed Servers e.g. ServerPilot
 *
 * Retrieves a List of DigitalOcean OS Images.
 *
 * @since  1.2.0
 *
 * @return  os_images     List of all OS Images
 */
function wpcs_digitalocean_managed_os_list( $image = null ) {
	
	$plans = array(
		'ubuntu-20-04-x64'	=>	array( 'name' => 'Ubuntu 20.04 x64'),
		'ubuntu-18-04-x64'	=>	array( 'name' => 'Ubuntu 18.04 x64'),
	);
	
	if ( isset( $image ) ) {
		foreach ( $plans as $key => $plan ) {
			if ( $plan['name'] == $image ) {
				return $key;
			}
		}
		
	}
	
	return $plans;
    
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
function wpcs_digitalocean_ssh_key( $ssh_key_name, $server_region = null ) {

	if ( 'no-ssh-key' == $ssh_key_name ) {
		return false;
	}

	// Retrieve the SSH Key data
	$ssh_key_list = get_option( 'wpcs_serverpilots_ssh_keys');

	if ( !empty( $ssh_key_list ) ) {
		foreach ( $ssh_key_list as $key => $ssh_key ) {
			if ( $ssh_key_name == $ssh_key['name'] ) {
				// Set-up the data for the 
				$ssh_key_data = array(
					"name"			=>  $ssh_key['name'],
					"public_key"	=>  $ssh_key['public_key'], 
				);
			}
		}
	}
			
	// Retrieve list of existing DigitalOcean SSH Keys
	$ssh_keys = call_user_func( "wpcs_digitalocean_cloud_server_api", null, 'account/keys', null, false, 0, 'GET', false, 'list_ssh_keys' );
	
	update_option( 'wpcs_ssh_key_api_response', $ssh_keys );

	if ( !empty( $ssh_keys['ssh_keys'] ) ) {
		foreach ( $ssh_keys['ssh_keys'] as $key => $ssh_key ) {
			if ( $ssh_key_data['name'] == $ssh_key['name'] ) {
				return $ssh_key_id = $ssh_key['id'];
			}
		}
	}

	// SSH Key is NOT known to DigitalOcean so we need to add it
	$ssh_key = call_user_func( "wpcs_digitalocean_cloud_server_api", null, 'account/keys', $ssh_key_data, false, 0, 'POST', false, 'add_ssh_key' );

	if ( isset($ssh_key['ssh_key']['id']) ) {
		return $ssh_key['ssh_key']['id'];
	}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return false;
    
}

/**
 * Waits for the DigitalOcean API Action to Complete
 *
 * Returns Status Response or Droplet Data.
 *
 * @since  1.2.0
 *
 * @return  droplet_data     Droplet Data
 */
function wpcs_digitalocean_server_complete( $server_id, $queued_server, $host_name, $server_location ) {
		
	$status = "in-progress";
	$x		= 1;
			
	// Wait for the Server to Complete
	while( ( "in-progress" == $status ) && ( $x <= 1000 ) ) {
		$actions = WPCS_DigitalOcean()->api->call_api( 'droplets/' . $server_id . '/actions', null, false, 0, 'GET', false, 'get_action' );
		$status  = isset( $actions['actions'][0]['status'] ) ? $actions['actions'][0]['status'] : "in-progress" ;
   	 	$x++;
	}
			
	// Read Server Information
	$response = WPCS_DigitalOcean()->api->call_api( 'droplets/' . $server_id, null, false, 0, 'GET', false, 'get_droplet' );
	$droplet  = $response['droplet'];
	
	// Save the server details for future use
	$droplet_data = array(
		"id"			=>	$droplet['id'],
		"name"			=>	$droplet['name'],
		"location"		=>	$droplet['region']['name'],
		"slug"			=>	sanitize_title($droplet['name']),
		"ram"			=>	$droplet['memory'],
		"vcpus"			=>	$droplet['vcpus'],
		"disk"			=>	$droplet['disk'],
		"size"			=>	$droplet['size']['slug'],
		"os"			=> 	$droplet['image']['slug'],
		"ip_address"	=>	$droplet['networks']['v4'][0]['ip_address'],
		"completed"		=>	true,
	);
	
	return $droplet_data;
}
	
/**
 * Set DigitalOcean API Connected.
 *
 * @since  2.0.0
 *
 */
function wpcs_digitalocean_set_api_connected() {
	
	WP_Cloud_Server_DigitalOcean_Settings::wpcs_digitalocean_module_set_api_connected();
    
}

/**
 * DigitalOcean Cloud Server Action Function
 *
 * Allows access to the DigitalOcean API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.3
 *
 * @return response The response from the DigitalOcean API call
 */
function wpcs_digitalocean_cloud_server_action( $action, $server_id, $enable_response = false ) {

	if ( 'delete' == $action ) {

		$request	= 'DELETE';
		$model		= 'droplets/' . $server_id;
		$api_data	= null;

	} else {

		$request	= 'POST';
		$model		= 'droplets/' . $server_id . '/actions';
		$api_data	= array( "type" => $action);

	}

	// Delete the Droplet API Data to Force update
	$data = get_option( 'wpcs_digitalocean_api_data' );
	if ( isset( $data['droplets'] ) ) {
		unset( $data['droplets'] );
		update_option( 'wpcs_digitalocean_api_data', $data );
	}	

	$api_response = WPCS_DigitalOcean()->api->call_api( $model, $api_data, false, 0, $request, false, 'server_action' );
	
	return $api_response;
	
}

/**
 * Retrieves a list of Snapshots
 *
 * @since  1.0
 *
 * @return snapshot List of available snapshots
 */
function wpcs_digitalocean_call_api_list_snapshots( $server_id ) {
	
	$snapshots	= WPCS_DigitalOcean()->api->call_api( "droplets/{$server_id}/snapshots", null, false, 900, 'GET', false, 'digitalocean_snapshot_list' );
	
	return ( isset( $snapshots['snapshots'] ) ) ? $snapshots : false;
}

/**
 * Retrieves a list of Backups
 *
 * @since  3.0.6
 *
 * @return backups List of available backups
 */
function wpcs_digitalocean_call_api_list_backups( $server_id ) {
	
	// Call the DigitalOcean API
	$backups = WPCS_DigitalOcean()->api->call_api( "droplets/{$server_id}/backups", null, false, 900, 'GET', false, 'digitalocean_backup_list' );
	
	return ( isset( $backups['backups'] ) ) ? $backups : false;
}

/**
 * Retrieves a list of Volumes
 *
 * @since  3.0.6
 *
 * @return volumes List of available volumes
 */
function wpcs_digitalocean_call_api_list_volumes() {

	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['volumes'] ) ) {
		$volumes = WPCS_DigitalOcean()->api->call_api( "volumes", null, false, 900, 'GET', false, 'digitalocean_volumes_list' );
		if ( isset( $volumes['databases'] ) ) {
			$data['volumes'] = $volumes['volumes'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( is_array( $data['volumes'] ) ) ? $data['volumes'] : false;
}

/**
 * Retrieves a list of Database Clusters
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_databases() {
	
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['databases'] ) ) {
		$databases = WPCS_DigitalOcean()->api->call_api( "databases", null, false, 900, 'GET', false, 'digitalocean_databases_list' );
		if ( isset( $databases['databases'] ) ) {
			$data['databases'] = $databases['databases'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['databases'] ) ) ? $data['databases'] : false;
}

/**
 * Retrieves a list of Database Clusters
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_images() {
		
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['images'] ) ) {
		$images = WPCS_DigitalOcean()->api->call_api( "images?private=true", null, false, 900, 'GET', false, 'digitalocean_images_list' );
		if ( isset( $images['images'] ) ) {
			$data['images'] = $images['images'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['images'] ) ) ? $data['images'] : false;
}

/**
 * Retrieves a list of Database Clusters
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_domains() {
	
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['domains'] ) ) {
		$domains = WPCS_DigitalOcean()->api->call_api( "domains", null, false, 900, 'GET', false, 'digitalocean_domains_list' );
		if ( isset( $domains['domains'] ) ) {
			$data['domains'] = $domains['domains'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['domains'] ) ) ? $data['domains'] : false;
}

/**
 * Retrieves a list of Database Clusters
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_firewalls() {
	
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['firewalls'] ) ) {
		$firewalls = WPCS_DigitalOcean()->api->call_api( "firewalls", null, false, 900, 'GET', false, 'digitalocean_firewalls_list' );
		if ( isset( $firewalls['firewalls'] ) ) {
			$data['firewalls'] = $firewalls['firewalls'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['firewalls'] ) ) ? $data['firewalls'] : false;
}

/**
 * Retrieves a list of Database Clusters
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_floating_ips() {
	
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['floating_ips'] ) ) {
		$floating_ips = WPCS_DigitalOcean()->api->call_api( "floating_ips", null, false, 900, 'GET', false, 'digitalocean_floating_ips_list' );
		if ( isset( $floating_ips['floating_ips'] ) ) {
			$data['floating_ips'] = $floating_ips['floating_ips'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['floating_ips'] ) ) ? $data['floating_ips'] : false;
}

/**
 * Retrieves a list of VPCs
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_vpcs() {
	
	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['vpcs'] ) ) {
		$vpcs = WPCS_DigitalOcean()->api->call_api( "vpcs", null, false, 900, 'GET', false, 'digitalocean_vpcs_list' );
		if ( isset( $vpcs['floating_ips'] ) ) {
			$data['vpcs'] = $vpcs['vpcs'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( isset( $data['vpcs'] ) ) ? $data['vpcs'] : false;
}

/**
 * Retrieves a list of VPCs
 *
 * @since  3.0.6
 *
 * @return databases List of available databases
 */
function wpcs_digitalocean_call_api_list_load_balancers() {

	$data = get_option( 'wpcs_digitalocean_api_data' );

	if ( !isset( $data['load_balancers'] ) ) {
		$load_balancers = WPCS_DigitalOcean()->api->call_api( "load_balancers", null, false, 900, 'GET', false, 'digitalocean_load_balancers_list' );
		if ( isset( $load_balancers['load_balancers'] ) ) {
			$data['load_balancers'] = $load_balancers['load_balancers'];
			update_option( 'wpcs_digitalocean_api_data', $data );
		}
	}
	
	return ( is_array( $data['load_balancers'] ) ) ? $data['load_balancers'] : false;
}