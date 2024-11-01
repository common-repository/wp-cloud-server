<?php

/**
 * Provide a Admin Area Create Cloud Server Page
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.1.3
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$api_status		= wpcs_check_cloud_provider_api();
$attributes		= ( $api_status ) ? '' : 'disabled';
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$sp_response	= '';
$server_script	= null;

?>
<div class="content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpcs_cloudways_create_application' );
		wpcs_do_settings_sections( 'wpcs_cloudways_create_application' );
		wpcs_submit_button( 'Create Application', 'secondary', 'create_server', null, $attributes );
		?>
	</form>
</div>
<?php
	
$api_run = get_option( 'wpcs_cloudways_api', true );

// Capture the Cloudway Settings
$app_label	        		= get_option( 'wpcs_cloudways_app_label' );
$app_server_id	        	= get_option( 'wpcs_cloudways_app_server_id' );
$app_application	       	= get_option( 'wpcs_cloudways_app_application' );
$app_project	        	= get_option( 'wpcs_cloudways_app_project' );

$debug['app_label']			= $app_label;
$debug['app_server_id']		= $app_server_id;
$debug['app_application']	= $app_application;		
$debug['app_project']		= $app_project;

update_option('cloudways_application', $debug);

if ( '' !== $app_label ) {
		
	update_option( 'wpcs_cloudways_api', false );
	
	$app_explode			= explode( '|', $app_application );
	$app_application		= $app_explode[0];
	$app_version			= isset($app_explode[1]) ? $app_explode[1] : '';
		
	// Set-up the data for the new Droplet
	$app_data = array(
		"server_id"			=>	$app_server_id,
		"application"		=> 	$app_application,
		"app_version"		=>	$app_version,
		"app_label"			=>	$app_label,
		"project_name"		=>	$app_project,
	);

	$api = new WP_Cloud_Server_Cloudways_API();
			
	// Read Server Information
	$servers = $api->call_api( 'app', $app_data, false, 0, 'POST', false, 'create_cloudways_app' );

	// Log the creation of the new DigitalOcean Droplet
	//call_user_func("wpcs_{$server_module}_log_event", $server_cloud_provider, 'Success', 'New Server Created ('. $server_name .')' );
}		
	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_app_label' );
	delete_option( 'wpcs_cloudways_app_server_id' );
	delete_option( 'wpcs_cloudways_app_application' );
	delete_option( 'wpcs_cloudways_app_project' );
//}