<?php

function wpcs_cloudways_create_server_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-create-server' !== $tabs_content ) {
		return;
	}

$api_status		= wpcs_check_cloud_provider_api('Cloudways', null, false);
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
$server_name	        			= get_option( 'wpcs_cloudways_server_name', '' );
$server_app	        				= get_option( 'wpcs_cloudways_server_app' );
$server_app_name	        		= get_option( 'wpcs_cloudways_server_app_name' );
$server_providers	       			= get_option( 'wpcs_cloudways_server_providers' );
$server_region	        			= get_option( 'wpcs_cloudways_server_region' );
$server_size	        			= get_option( 'wpcs_cloudways_server_size' );
$server_project	        			= get_option( 'wpcs_cloudways_server_project' );
$server_db_volume_size	        	= get_option( 'wpcs_cloudways_server_db_volume_size' );
$server_data_volume_size	        = get_option( 'wpcs_cloudways_server_data_volume_size' );

$debug['server_name']				= $server_name;
$debug['server_project']			= $server_project;
$debug['server_providers']			= $server_providers;		
$debug['server_region']				= $server_region;
$debug['server_size']				= $server_size;
$debug['server_app_name']			= $server_app_name;

update_option('cloudways_trigger', $debug);

if ( '' !== $server_name ) {
		
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
		"db_volume_size"	=>	( ( 'amazon' == $server_providers ) || ( 'gce' == $server_providers ) ) ? $server_db_volume_size : null,
		"data_volume_size"	=>	( ( 'amazon' == $server_providers ) || ( 'gce' == $server_providers ) ) ? $server_data_volume_size : null,
		"series"			=>	null,
	);

	$api = new WP_Cloud_Server_Cloudways_API();
			
	// Create Cloudways Server
	$servers = $api->call_api( 'server', $app_data, false, 0, 'POST', false, 'create_cloudways_server' );

	// Add Server to Project

			
	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_server_project' );
	delete_option( 'wpcs_cloudways_server_name' );
	delete_option( 'wpcs_cloudways_server_providers' );
	delete_option( 'wpcs_cloudways_server_region' );
	delete_option( 'wpcs_cloudways_server_size' );
	delete_option( 'wpcs_cloudways_server_app' );
	delete_option( 'wpcs_cloudways_server_app_name' );
}
	
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_create_server_template', 10, 3 );