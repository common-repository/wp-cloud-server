<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_cloudways_create_app', 'wpcs_cloudways_create_app' );
add_action( 'admin_post_cloudways_create_app', 'wpcs_cloudways_create_app' );

function wpcs_cloudways_create_app() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_create_app_name'] ) ) {
		update_option( 'wpcs_cloudways_create_app_name', $_POST['wpcs_cloudways_create_app_name'] );
	}
	
	// Save the ServerPilot Template Region
	if ( isset( $_POST['wpcs_cloudways_create_app_server'] ) ) {
		update_option( 'wpcs_cloudways_create_app_server', $_POST['wpcs_cloudways_create_app_server'] );
	}
	
	// Save the ServerPilot Template Size
	if ( isset( $_POST['wpcs_cloudways_create_app_application'] ) ) {
		update_option( 'wpcs_cloudways_create_app_application', $_POST['wpcs_cloudways_create_app_application'] );
	}
	
	// Save the ServerPilot Template Type
	if ( isset( $_POST['wpcs_cloudways_create_app_project'] ) ) {
		update_option( 'wpcs_cloudways_create_app_project', $_POST['wpcs_cloudways_create_app_project'] );
	}
	
		
	
	$debug_data = array(
		"app_label"			=>	get_option( 'wpcs_cloudways_create_app_name' ),
		"server_id"			=>	get_option( 'wpcs_cloudways_create_app_server' ),
		"application"		=>	get_option( 'wpcs_cloudways_create_app_application' ),
		"project"			=> 	get_option( 'wpcs_cloudways_create_app_project' ),
	);
	
	update_option( 'wpcs_cloudways_create_app_debug', $debug_data );
	
	$app_explode			= explode( '|', get_option( 'wpcs_cloudways_create_app_application'  ) );
	$app_application		= $app_explode[0];
	$app_version			= isset($app_explode[1]) ? $app_explode[1] : '';
	
	$app_data = array(
				"server_id"			=>	get_option( 'wpcs_cloudways_create_app_server' ),
				"application"		=> 	$app_application,
				"app_version"		=>	$app_version,
				"app_label"			=>	get_option( 'wpcs_cloudways_create_app_name' ),
				"project_name"		=>	get_option( 'wpcs_cloudways_create_app_project' ),
			);
									  
	$app_queue		= get_option( 'wpcs_cloudways_create_app_queue' );
									  
	$app_queue[] 	= $app_data;								  
									  
	update_option( 'wpcs_cloudways_create_app_queue', $app_queue );
	

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_cloudways_create_app_name');
	delete_option( 'wpcs_cloudways_create_app_server');
	delete_option( 'wpcs_cloudways_create_app_application' );
	delete_option( 'wpcs_cloudways_create_app_project' );
	
	$url = admin_url();
	wp_redirect( $url . 'index.php/test-shortcode/'  ); exit;
}