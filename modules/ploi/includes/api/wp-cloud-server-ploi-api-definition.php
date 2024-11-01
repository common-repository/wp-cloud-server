<?php
/**
 * WP Cloud Server - Ploi API Definition
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a list of Credentials
 *
 * @since  1.0
 *
 * @return regions List of available zones
 */
function wpcs_ploi_api_definition( $function, $request ) {

	switch ($function) {

		case "User":

			$api_command = array( 
		
					"user/info"							=> array( "request" => "user",									"query" => "GET"),
					"server-providers/list"				=> array( "request" => "user/server-providers", 				"query" => "GET"),
					"server-providers/list/provider"	=> array( "request" => "user/server-providers/{{provider}}",	"query" => "GET"),
		
			);

			break;

		case "Servers":

			$api_command = array( 
		
					"servers/list"			=> array( "request" => "servers",								"query" => "GET"),
					"servers/get"			=> array( "request" => "servers/{{server_id}}", 				"query" => "GET"),
					"servers/create"		=> array( "request" => "servers",								"query" => "POST"),
					"servers/custom"		=> array( "request" => "servers/custom",						"query" => "POST"),
					"servers/delete"		=> array( "request" => "servers/{{server_id}}", 				"query" => "DELETE"),
					"servers/logs"			=> array( "request" => "servers/{{server_id}}/logs",			"query" => "GET"),
					"servers/monitor"		=> array( "request" => "servers/{{server_id}}/monitor",			"query" => "GET"),
					"servers/restart"		=> array( "request" => "servers/{{server_id}}/restart", 		"query" => "POST"),
		
			);
				
			break;

		case "Databases":

			$api_command = array( 
			
					"List databases"		=> array( "request" => "servers",								"query" => "GET"),
					"Create database"		=> array( "request" => "servers/{{server_id}}", 				"query" => "GET"),
					"Get database"			=> array( "request" => "servers",								"query" => "POST"),
					"Delete database"		=> array( "request" => "servers/custom",						"query" => "POST"),
					"Acknowledge database"	=> array( "request" => "servers/{{server_id}}", 				"query" => "DELETE"),
					"Forget database"		=> array( "request" => "servers/{{server_id}}/logs",			"query" => "GET"),
			
			);
					
			break;

		case "Sites":

			$api_command = array( 
				
					"sites/list"								=> array( "request" => "servers/{{server_id}}/sites",							"query" => "GET"),
					"sites/create"								=> array( "request" => "servers/{{server_id}}/sites", 							"query" => "POST"),
					"sites/get"									=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}",				"query" => "GET"),
					"sites/delete"								=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}",				"query" => "DELETE"),
					"sites/log"									=> array( "request" => "servers/{{server_id}}", 								"query" => "GET"),
					"sites/log/get"								=> array( "request" => "servers/{{server_id}}/logs",							"query" => "GET"),
					"sites/install/wordpress"					=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/wordpress",		"query" => "POST"),
					"sites/install/nextcloud"					=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/nextcloud",		"query" => "POST"),
					"sites/install/git"							=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/repository",	"query" => "POST"),
					"sites/install/git/deploy"					=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/deploy",		"query" => "POST"),		
					"sites/install/git/deploy/get/script"		=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/deploy/script",	"query" => "GET"),
					"sites/install/git/deploy/update/script"	=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/deploy/script",	"query" => "PATCH"),								
					"sites/certificates/create"					=> array( "request" => "servers/{{server_id}}/sites/{{site_id}}/certificates",	"query" => "POST"),

				);
						
			break;

		case "Templates":

			$api_command = array( 
					
					"templates/list"			=> array( "request" => "webserver-templates",									"query" => "GET"),
					"templates/get"				=> array( "request" => "webserver-templates/{id}", 								"query" => "GET"),
									
			);

			break;

		case "SystemUsers":

			$api_command = array( 
						
					"system-users/list"			=> array( "request" => "servers/{{server_id}}/system-users",					"query" => "GET"),
					"system-users/get"			=> array( "request" => "servers/{{server_id}}/system-users/{{system_user}}", 	"query" => "GET"),
										
			);
							
			break;

		default:

			return false;
	}

	if ( array_key_exists( $request, $api_command ) ) {

		return $api_command[$request];

	}

	return false;
	
}