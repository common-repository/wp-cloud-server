<?php

function wpcs_cloudways_create_application_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-create-application' !== $tabs_content ) {
		return;
	}

$api_status		= wpcs_check_cloud_provider_api('Cloudways', null, false);
$attributes		= ( $api_status ) ? '' : 'disabled';

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
$app_label	        		= get_option( 'wpcs_cloudways_app_label', '' );
$app_server_id	        	= get_option( 'wpcs_cloudways_app_server_id' );
$app_application	       	= get_option( 'wpcs_cloudways_app_application' );
$app_project	        	= get_option( 'wpcs_cloudways_app_project' );
$app_new_project	        = get_option( 'wpcs_cloudways_app_new_project' );

$debug['app_label']			= $app_label;
$debug['app_server_id']		= $app_server_id;
$debug['app_application']	= $app_application;		
$debug['app_project']		= $app_project;

if ( '' !== $app_label ) {
	
	$app_explode			= explode( '|', $app_application );
	$app_application		= $app_explode[0];
	$app_version			= isset($app_explode[1]) ? $app_explode[1] : '';

	$app_project_explode	= explode( '|', $app_project );
	$app_project_id			= $app_project_explode[0];
	$app_project_name		= isset($app_project_explode[1]) ? $app_project_explode[1] : '';
	
	$app_queue				= get_option( 'wpcs_cloudways_create_app_queue', array() );
		
	// Set-up the data for the new Droplet
	$app_queue[] = array(
		"server_id"					=>	$app_server_id,
		"application"				=> 	$app_application,
		"app_version"				=>	$app_version,
		"app_label"					=>	$app_label,
		"new_project_name"			=>	( isset( $app_new_project ) ) ? $app_new_project : null,
		"selected_project_name"		=>	$app_project_name,
		"selected_project_id"		=>	$app_project_id,
		"operation_id"				=>	null,
		"stage"						=>	'Added to Queue',
		"response"					=>	'',
	);

	// Add App to App Queue
	update_option( 'wpcs_cloudways_create_app_queue', $app_queue );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_app_label' );
	delete_option( 'wpcs_cloudways_app_server_id' );
	delete_option( 'wpcs_cloudways_app_application' );
	delete_option( 'wpcs_cloudways_app_project' );
	delete_option( 'wpcs_cloudways_app_new_project' );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_create_application_template', 10, 3 );