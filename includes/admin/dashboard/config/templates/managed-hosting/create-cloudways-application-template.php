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

// Capture the Cloudway Settings
$app_label	        		= get_option( 'wpcs_cloudways_app_label' );
$app_server_id	        	= get_option( 'wpcs_cloudways_app_server_id' );
$app_application	       	= get_option( 'wpcs_cloudways_app_application' );
$app_project	        	= get_option( 'wpcs_cloudways_app_project' );

$debug['app_label']			= $app_label;
$debug['app_server_id']		= $app_server_id;
$debug['app_application']	= $app_application;		
$debug['app_project']		= $app_project;

if ( '' !== $app_label ) {
	
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

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'Cloudways' ][ 'templates' ][] = $app_data;

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_app_label' );
	delete_option( 'wpcs_cloudways_app_server_id' );
	delete_option( 'wpcs_cloudways_app_application' );
	delete_option( 'wpcs_cloudways_app_project' );
//}