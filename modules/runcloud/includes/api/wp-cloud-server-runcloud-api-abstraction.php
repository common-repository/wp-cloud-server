<?php
/**
 * WP Cloud Server - RunCloud Cloud Provider Config Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
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
function wpcs_runcloud_php_version_list() {
	
	return $php_version = array(
		"php80rc"		=>	"PHP 8.0",
		"php74rc"		=>	"PHP 7.4",
		"php73rc"		=>	"PHP 7.3",
		"php72rc"		=>	"PHP 7.2",
	);
}

/**
 * Retrieves a list of Web Applications
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_application_list() {
	
	return $application = array(
		"wordpress"	=>		"WordPress",
	);
}

/**
 * Retrieves a list of Web Applications
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_github_add_deploy_key( $key, $repo ) {
	
	if ( function_exists('wpcs_github_call_api_add_deploy_key') ) {
		$repos = wpcs_github_call_api_add_deploy_key( $key, $repo );
	}
	
	return ( isset( $repos ) && is_array( $repos ) ) ? $repos : false;
}

/**
 * Retrieves a list of Web Applications
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_github_add_webhook( $url, $repo ) {
	
	if ( function_exists('wpcs_github_call_api_add_webhook') ) {
		$repos = wpcs_github_call_api_add_webhook( $url, $repo );
	}
	
	return ( isset( $repos ) && is_array( $repos ) ) ? $repos : false;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_server_list( $force = false ) {

	if ( $force ) {
		// Create instance of the RunCloud API
		$api = new WP_Cloud_Server_RunCloud_API;
		$servers['servers'] = $api->call_api( "servers", null, false, 900, 'GET', false, 'runcloud_server_list' );
	} else {
		$servers = get_option( 'wpcs_runcloud_api_data' );
	}

	$server_list = ( is_array( $servers['servers'] ) ) ? $servers['servers'] : array();
	
	return $server_list;
}

/**
 * Retrieves a Server ID
 *
 * @since  3.0.1
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_server_id( $server_name, $force = false ) {
	
	$server_list = array();
	
	$servers = wpcs_runcloud_server_list( $force );
	
	if ( isset( $servers['data'] ) && is_array( $servers['data'] ) ) {
		foreach ( $servers['data'] as $key => $server ) {
			if ( isset( $server['name'] ) ) {
				$server_list[$server['name']] = $server['id'];
			}
		}
	}
	
	return $server_id = ( array_key_exists( $server_name, $server_list ) ) ? $server_list[$server_name] : false;
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_web_app_list() {
	
	$web_apps	= false;
	$servers	= wpcs_runcloud_server_list();
	
	if ( isset( $servers['data'] ) && is_array( $servers['data'] ) ) {
		foreach ( $servers['data'] as $key => $server ) {
			if ( isset( $server['name'] ) ) {
				$server_list[$server['id']] = $server['name'];
			}
		}
	}
	
	$class_name = "WP_Cloud_Server_RunCloud_API";
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	if ( isset( $server_list ) && is_array( $server_list ) ) {
		foreach ( $server_list as $key => $server ) {
			if ( isset( $server ) ) {
				$apps = $api->call_api( "servers/{$key}/webapps", null, false, 900, 'GET', false, 'runcloud_web_app_list' );
				if ( isset( $apps['data'] ) && is_array( $apps['data'] ) ) {
					$web_apps[$server] = $apps['data'];
				}
			}
		}
	}
	
	update_option( 'wpcs_web_apps', $web_apps );
	
	return $web_apps;
}

/**
 * Retrieves a list of System Users
 *
 * @since  1.0
 *
 * @return System User List
 */
function wpcs_runcloud_sys_user_list( $user = null, $server_name = null ) {
	
	$server_id	= false;
	$servers	= wpcs_runcloud_server_list();
	
	if ( !empty( $servers['data'] ) ) {
		foreach ( $servers['data'] as $key => $server ) {
			if ( ( !empty( $user ) && ( $server['name'] == $server_name ) ) || ( empty( $user ) && empty( $server_name ) ) )  {
				$server_id[] = $server['id'];
			}
		}
	}
	
	$test['id'] = $server_id;

	if ( $server_id ) {
	
		$class_name = "WP_Cloud_Server_RunCloud_API";
	
		// Create instance of the RunCloud API
		$api = new $class_name();
		
		foreach ( $server_id as $key => $id ) {
			$data = $api->call_api( "servers/{$id}/users", null, false, 900, 'GET', false, 'runcloud_sys_user_list' );
			$sys_users[] = ( isset( $data['data'] ) ) ? $data['data'] : array();
		}
		
		$test['sys_users'] = $sys_users;
		
		if ( !empty( $sys_users ) && !empty( $user ) && !empty( $server_name ) ) {
			foreach ( $sys_users as $key => $sys_user ) {
				foreach ( $sys_user as $key => $item ) {
					if ( $user == $item['username'] ) {
						$sys_user_id = $item['id'];
					}
				}
			}
		}
	}
	
	$sys_users_list = ( isset($sys_users) ) ? $sys_users : array();
	$sys_user_id	= ( isset($sys_user_id) ) ? $sys_user_id : false;
	
	if ( !empty( $user ) ) {
		return $sys_user_id;
	} else {
		return $sys_users_list;
	}
}

/**
 * Retrieves a list of Zones
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_runcloud_regions_array() {
	
	$class_name = "WP_Cloud_Server_RunCloud_API";
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	$regions = $api->call_api( 'zone', null, false, 900, 'GET', false, 'runcloud_zone_list' );

	if ( !empty( $regions ) ) {
		foreach ( $regions['zones']['zone'] as $key => $region ) {
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
function wpcs_runcloud_regions_list() {
	
	$class_name = "WP_Cloud_Server_RunCloud_API";
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	$regions = $api->call_api( 'zone', null, false, 900, 'GET', false, 'runcloud_zone_list' );

	if ( !empty( $regions ) ) {
		foreach ( $regions['zones']['zone'] as $key => $region ) {
			$list[ $region['id'] ] = array( 'name' => $region['description'] );
		}
	}
	
	return $list;
}

/**
 * Retrieves a list of Available Plans by Zone
 *
 * @since  1.0
 *
 * @return regions List of available plans by zone
 */
function wpcs_runcloud_availability_list( $region ) {
	
	// Create instance of the RunCloud API
	$api = new WP_Cloud_Server_RunCloud_API();
	
	$available_plans = $api->call_api( 'plan', null, false, 900, 'GET', false, 'runcloud_plan_availability_list' );
	
	$available_plans = ( 'userselected' == $region ) ? array_keys($available_plans) : $available_plans ;
	
	$plans = $api->call_api( "plan", null, false, 900, 'GET', false, 'runcloud_plan_list' );
	
	foreach ( $plans['plans']['plan'] as $key => $plan ) {
			
		$ram  		= wpcs_runcloud_convert_mb_to_gb($plan['memory_amount']);
		$cost		= 0;
		$plan_name 	= "{$plan['core_number']} CPU, {$ram}GB, {$plan['storage_size']}GB SSD";
		$plan_cost	= "(\${$cost}/month)";
			
		$available['server'][$plan['name']] = array( 'name' => $plan_name, 'cost' => $plan_cost, 'storage' => $plan['storage_size'] );
	}
	
	return $available;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_runcloud_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_RunCloud_API";

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
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	$plans = $api->call_api( 'storage/template', null, false, 900, 'GET', false, 'runcloud_os_list' );
	
	update_option( 'runcloud_os_list', $image );
	
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
function wpcs_runcloud_plans_list() {
	
	$class_name = "WP_Cloud_Server_RunCloud_API";
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	$plans = $api->call_api( 'plan', null, false, 900, 'GET', false, 'runcloud_plan_list' );
	
	return $plans;
}

/**
 * Retrieves a list of OS Images
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_runcloud_managed_os_list( $image = null ) {
	
	$class_name = "WP_Cloud_Server_RunCloud_API";
	
	// Create instance of the RunCloud API
	$api = new $class_name();
	
	$plans = $api->call_api( 'os/list', null, false, 900, 'GET', false, 'runcloud_os_list' );
	
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
 * RunCloud API Interface for integrating with Modules
 *
 * @since  1.0
 *
 * @return api_response Response from API call
 */
function wpcs_runcloud_cloud_server_api( $module_name, $model, $api_data = null, $cache = false, $cache_lifetime = 900, $request = 'GET', $enable_response = false, $function = null ) {
	// Exit if checking DigitalOcean Data Centers
	if ( 'check_data_centers' == $function ) {
		return null;
	}
	
	$api = new WP_Cloud_Server_RunCloud_API();
	
	$model = ( 'droplets' == $model ) ? 'server' : $model;
	
	if ( isset( $api_data ) && ( 'POST' == $request ) ) {
		$app_data = $api_data['custom_settings'];	
	}
	
	if ( isset( $api_data['user_data'] )  ) {
		$app_data['user_data'] = $api_data['user_data'];	
	}
	
	update_option( 'wpcs_runcloud_sp_install_script', $app_data );

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
function wpcs_runcloud_cloud_regions() {
	
	$regions = wpcs_runcloud_regions_list();
		
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
function wpcs_runcloud_api_response_valid( $response ) {

	return ( ! $response || $response['response']['code'] !== 200 );

}

/**
 * Waits for API Server Action to Complete
 *
 * @since  1.0
 *
 * @return server_data Server Data
 */
function wpcs_runcloud_server_complete( $server_sub_id, $queued_server, $host_name, $server_region ) {
	
	if ( !isset( $queued_server['server_module'] ) ) {
		return false;
	}
	
	$server_module = $queued_server['server_module'];
	
	if ( 'aws_lightsail' == $server_module ) {

		//$server_sub_id = $response['operations'][0]['id'];

		// Wait for server to be running
		$status = call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $queued_server, $host_name, $server_region );
		
		// Attach Static IP
		if ( 'running' == $status['state'] ) {
					
			$staticip = call_user_func("wpcs_{$server_module}_attach_static_ip", $host_name, $server_region );
			
			$app_data = array(
        		'instanceName' 	=> $host_name,
				'portInfo'		=> array(
						'fromPort'		=> 34210,
						'protocol'		=> 'tcp',
						),
    			);
			
			$api = new WP_Cloud_Server_AWS_Lightsail_API();
	
			$ports = $api->call_api( 'OpenInstancePublicPorts', $app_data, false, 0, 'POST', false, 'update_ports', $server_region );
			
			$debug['setup_server'] = $ports;
			$debug['aws_id'] 	= $server_sub_id;
		}
	}
	
	$api = new WP_Cloud_Server_RunCloud_API();	

	$server_complete	= false;
	$server_web_app		= false;
	$server_sub_id		= false;
	$app_installed		= false;
	
	$servers			= wpcs_runcloud_server_list();
	
	$debug['servers'] 	= $servers;
	
	foreach ( $servers as $key => $server ) {
		if ( $queued_server['host_name'] == $server['name'] ) {
			$server_default_app = $queued_server['default_app'];
			$server_web_app		= $queued_server['web_app'];
			$server_sub_id		= $server['id'];
			$server_name		= $server['name'];
			$server_complete	= true;
		}
	}
	
	$debug['complete'] 		= $server_complete;
	$debug['web_app']		= $server_web_app;
	$debug['id'] 			= $server_sub_id;

	$system_user_name		= $queued_server['system_user_name'];
	$system_user_password	= $queued_server['system_user_password'];

	// Create System User if required
	if ( ( 'runcloud' !== $system_user_name ) && !empty( $system_user_password ) ) {

		$app_data = array(
			'username'	=>	$system_user_name,
			'password'	=>	$system_user_password,
		);

		// Send the API POST request to create the new 'app'
		$response = $api->call_api( "servers/{$server_sub_id}/users", $app_data, false, 0, 'POST', false, 'runcloud_new_system_user' );

	}

	if ( $server_web_app && $server_complete ) {
		$app_data = array(
				"name"							=> "{$host_name}-{$server_web_app}",
  				"domainName"					=> "{$host_name}.{$queued_server['host_name_domain']}",
 	 			"user"							=> wpcs_runcloud_sys_user_list( $system_user_name, $server_name ),
  				"publicPath"					=> "/public",
  				"phpVersion"					=> "php74rc",
  				"stack"							=> "hybrid",
  				"stackMode"						=> "production",
  				"clickjackingProtection"		=> true,
  				"xssProtection"					=> true,
  				"mimeSniffingProtection"		=> true,
 				"processManager"				=> "ondemand",
  				"processManagerMaxChildren"		=> 50,
  				"processManagerMaxRequests"		=> 500,
  				"openBasedir"					=> "",
  				"timezone"						=> "UTC",
  				"disableFunctions"				=> "getmyuid,passthru,leak,listen,diskfreespace,tmpfile,link,ignore_user_abort,shell_exec,dl,set_time_limit,exec,system,highlight_file,source,show_source,fpassthru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix,_getppid,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_open,proc_close,proc_nice,proc_terminate,escapeshellcmd,ini_alter,popen,pcntl_exec,socket_accept,socket_bind,socket_clear_error,socket_close,socket_connect,symlink,posix_geteuid,ini_alter,socket_listen,socket_create_listen,socket_read,socket_create_pair,stream_socket_server",
  				"maxExecutionTime"				=> 30,
  				"maxInputTime"					=> 60,
  				"maxInputVars"					=> 1000,
  				"memoryLimit"					=> 256,
  				"postMaxSize"					=> 256,
  				"uploadMaxFilesize"				=> 256,
  				"sessionGcMaxlifetime"			=> 1440,
  				"allowUrlFopen"					=> true,
		);
	
		update_option( 'wpcs_website_install_data', $app_data );
		
		// Send the API POST request to create the new 'app'
		$response = $api->call_api( "servers/{$server_sub_id}/webapps/custom", $app_data, false, 0, 'POST', false, 'runcloud_new_site' );
		
		$debug['response'] = $response;
	
		$web_apps = wpcs_runcloud_web_app_list();
		
		foreach ( $web_apps as $key => $web_app ) {
			foreach ( $web_app as $index => $app ) {
				if ( ( $host_name == $key ) && ( $app_data['name'] == $app['name'] ) ) {
					$app_installed	= true;
				}
			}
		}
			
		$debug['webapp'] = $app_installed;
	
		$webapp_name	= isset( $response['name'] ) ? $response['name'] : '';
		$webapp_id		= isset( $response['id'] ) ? $response['id'] : '';
	
		$application_data = array(
			"name"	=> $server_web_app,
		);
	
		$response = $api->call_api( "servers/{$server_sub_id}/webapps/{$webapp_id}/installer", $application_data, false, 0, 'POST', false, 'runcloud_application' );
			
		$debug['installer'] = $response;
			
		// Set as default web application on this server
		if ( '1' == $server_default_app ) {
			$response = $api->call_api( "servers/{$server_sub_id}/webapps/{$webapp_id}/default", null, false, 0, 'POST', false, 'runcloud_application' );
			$debug['default'] = $response;
		}
		
	}
	
	update_option( 'runcloud_create_app_debug', $debug );
					
	// Save the server details for future use
	$server_data = array(
			"id"			=>	'',
			"name"			=>	'',
			"location"		=>	'',
			"slug"			=>	'',
			"ram"			=>	'',
			"vcpus"			=>	'',
			"disk"			=>	'',
			"size"			=>	'',
			"os"			=> 	'',
			"ip_address"	=>	'',
			"completed"		=>	$app_installed,
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
function wpcs_runcloud_call_api_health_check( ) {

	$api = new WP_Cloud_Server_RunCloud_API();

	$api_response = $api->call_api( 'ping', null, false, 0, 'GET', true, 'api_health' );

	return $api_response;

}

/**
 * Call to API to List Servers
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_runcloud_call_api_list_servers( $enable_response = false ) {

	$api = new WP_Cloud_Server_RunCloud_API();

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
function wpcs_runcloud_call_api_server_info( $server_sub_id = null, $enable_response = false ) {

	$api_response = WPCS_RunCloud()->api->call_api( 'server/list?SUBID=' . $server_sub_id, null, false, 0, 'GET', $enable_response, 'server_status' );

	return $api_response;

}

/**
 * Call to API to Create New Server
 *
 * @since  1.0
 *
 * @return api_response 	API response
 */
function wpcs_runcloud_call_api_create_server( $api_data = null, $enable_response = false ) {

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

	$api_response = WPCS_RunCloud()->api->call_api( 'server', $server_data, false, 0, 'POST', $enable_response, 'server_creation' );

	return $api_response;

}

/**
 * Call to API to Update Server Status
 *
 * @since  2.1.1
 *
 * @return api_response 	API response
 */
function wpcs_runcloud_call_api_update_server( $model = 'linode/instances', $post = 'POST', $api_data = null,  $enable_response = false ) {

	$api_response = WPCS_RunCloud()->api->call_api( $model, $api_data, false, 0, $post, $enable_response, 'update_server' );
	
	update_option( 'wpcs_runcloud_update_server_api_last_response', $api_response );

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
function wpcs_runcloud_ssh_key( $ssh_key_name ) {

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
					"name"		=>  $ssh_key['name'],
					"ssh_key"	=>  $ssh_key['public_key'], 
				);
			}
		}
	} 
			
	// Retrieve list of existing DigitalOcean SSH Keys
	$ssh_keys = WPCS_RunCloud()->api->call_api( 'sshkey/list', null, false, 900, 'GET', false, 'list_ssh_keys' );
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
	$ssh_key = WPCS_RunCloud()->api->call_api( 'sshkey/create', $ssh_key_data, false, 900, 'POST', false, 'add_ssh_key' );
	
	$debug['add'] = $ssh_key;
	
	update_option( 'wpcs_linode_ssh_key_api_response', $debug );

	if ( isset($ssh_key['SSHKEYID']) ) {
		return $ssh_key['SSHKEYID'];
	}
	
	// If we get here that the SSH Key retrieval process failed, so return FALSE
	return false;
    
}

function wpcs_runcloud_convert_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

/**
 * RunCloud Cloud Server Action Function
 *
 * Allows access to the RunCloud API. Used as part of the Add-on Module Framework.
 *
 * @since  3.0.6
 *
 * @return response The response from the UpCloud API call
 */
function wpcs_runcloud_cloud_server_action( $action, $server_id, $enable_response = false ) {
	
	if ( 'delete' == $action ) {

		$request	= 'DELETE';
	
		$api_response = WPCS_RunCloud()->api->call_api( 'servers/' . $server_id, null, false, 0, $request, $enable_response, 'server_action' );

		// Delete the Instance API Data to Force update
		$data = get_option( 'wpcs_runcloud_api_data' );
		if ( isset( $data['servers'] ) ) {
			unset( $data['servers'] );
			update_option( 'wpcs_runcloud_api_data', $data );
		}
		
	}
	
	return ( isset( $api_response ) ) ? $api_response : false;
}

/**
 * Call to API to Delete Web App
 *
 * @since  3.0.3
 *
 * @return api_response 	API response
 */
function wpcs_runcloud_api_delete_web_apps( $server_id, $web_app_id, $enable_response = false ) {

	$api_response = WPCS_RunCloud()->api->call_api( "servers/{$server_id}/webapps/{$web_app_id}", null, false, 0, 'DELETE', $enable_response, 'delete_web_app' );

	return $api_response;

}

/**
 * Call to RunCloud API to List Servers
 *
 * @since  3.0.6
 *
 * @return api_response 	API response
 */
function wpcs_runcloud_call_api_data_servers( $enable_response = false ) {

	$data = get_option( 'wpcs_runcloud_api_data' );

	if ( !isset( $data['servers'] ) ) {
		$servers = WPCS_RunCloud()->api->call_api( "servers", null, false, 900, 'GET', false, 'runcloud_server_list' );
		if ( isset( $servers['data'] ) ) {
			$data['servers'] = $servers;
			update_option( 'wpcs_runcloud_api_data', $data );
		}
	}

	return ( isset( $data['servers']['data'] ) ) ? $data['servers']['data'] : false;

}