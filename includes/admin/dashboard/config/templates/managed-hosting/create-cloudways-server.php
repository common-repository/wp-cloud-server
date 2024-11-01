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
		settings_fields( 'wpcs_cloudways_create_server' );
		wpcs_do_settings_sections( 'wpcs_cloudways_create_server' );
		wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
		?>
	</form>
</div>
<?php
	
$api_run = get_option( 'wpcs_cloudways_api', true );

// Capture the Cloudway Settings
$server_name	        			= get_option( 'wpcs_cloudways_server_name' );
$server_type	        			= get_option( 'wpcs_cloudways_server_type' );
$server_providers	       			= get_option( 'wpcs_cloudways_server_providers' );
$server_region	        			= get_option( 'wpcs_cloudways_server_region' );
$server_size	        			= get_option( 'wpcs_cloudways_server_size' );
$server_app	        				= get_option( 'wpcs_cloudways_server_app' );
$server_app_name	        		= get_option( 'wpcs_cloudways_server_app_name' );

$debug['server_name']				= $server_name;
$debug['server_type']				= $server_type;
$debug['server_providers']			= $server_providers;		
$debug['server_region']				= $server_region;
$debug['server_size']				= $server_size;
$debug['server_app_name']			= $server_app_name;
//$debug['server_app_application']	= $server_app_application;
//$debug['server_app_version']		= $server_app_version;

update_option('cloudways_trigger', $debug);

if ( $api_run && ( '' !== $server_name ) ) {
		
	update_option( 'wpcs_cloudways_api', false );
	
	$server_app_explode					= explode( '|', $server_app );
	$server_app_application				= $server_app_explode[0];
	$server_app_version					= isset($server_app_explode[1]) ? $server_app_explode[1] : '';
		
	// Set-up the data for the new Droplet
	$app_data = array(
		"cloud"				=>	$server_providers,
		"region"			=>	$server_region,
		"instance_type"		=>	$server_size,
		"application"		=> 	$server_app_application,
		"app_version"		=>	$server_app_version,
		"server_label"		=>	$server_name,
		"app_label"			=>	$server_app_name,
		"project_name"		=>	null,
		"db_volume_size"	=>	null,
		"data_volume_size"	=>	null,
		"series"			=>	null,
	);

	$api = new WP_Cloud_Server_Cloudways_API();
			
	// Read Server Information
	//$servers = $api->call_api( 'server', $app_data, false, 0, 'POST', false, 'create_cloudways_server' );

	// Log the creation of the new DigitalOcean Droplet
	//call_user_func("wpcs_{$server_module}_log_event", $server_cloud_provider, 'Success', 'New Server Created ('. $server_name .')' );
			
	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_server_type' );
	delete_option( 'wpcs_cloudways_server_name' );
	delete_option( 'wpcs_cloudways_server_providers' );
	delete_option( 'wpcs_cloudways_server_region' );
	delete_option( 'wpcs_cloudways_server_size' );
	delete_option( 'wpcs_cloudways_server_app' );
	delete_option( 'wpcs_cloudways_server_app_name' );
}