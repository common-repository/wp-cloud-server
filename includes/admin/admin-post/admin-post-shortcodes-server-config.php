<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_shortcodes_server_config', 'wpcs_handle_shortcodes_server_config' );
add_action( 'admin_post_handle_shortcodes_server_config', 'wpcs_handle_shortcodes_server_config' );

function wpcs_handle_shortcodes_server_config() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_plan_name'] ) ) {
		$plan_name = $_POST['wpcs_server_shortcodes_plan_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_host_name'] ) ) {
		$host_name = $_POST['wpcs_server_shortcodes_host_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_host_name_fqdn'] ) ) {
		$host_name_fqdn = $_POST['wpcs_server_shortcodes_host_name_fqdn'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_region_name'] ) ) {
		$region_name = $_POST['wpcs_server_shortcodes_region_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_size_name'] ) ) {
		$size_name = $_POST['wpcs_server_shortcodes_size_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_image_name'] ) ) {
		$image_name = $_POST['wpcs_server_shortcodes_image_name'];
	}	
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_login_url'] ) ) {
		$login_url = $_POST['wpcs_server_shortcodes_login_url'];
	}

	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_nonce'] ) ) {
		$nonce = $_POST['wpcs_server_shortcodes_nonce'];
	}

	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_server_shortcodes_action'] ) ) {
		$action = $_POST['wpcs_server_shortcodes_action'];
	}

	if ( $action == "update") {
	
		$data['contents']	= array(
		
			'plan_name'		=> isset( $plan_name ) ? $plan_name : false, 
			'host_name'		=> isset( $host_name ) ? $host_name : false,
			'fqdn'			=> isset( $host_name_fqdn ) ? $host_name_fqdn : false,
			'region_name'	=> isset( $region_name ) ? $region_name : false,
			'size_name'		=> isset( $size_name ) ? $size_name : false,
			'image_name'	=> isset( $image_name ) ? $image_name : false,
			'login_url'		=> isset( $login_url ) ? $login_url : false,
	
		);

		$data['fields']		= array(
	
			'plan_name'		=> 'Plan Name', 
			'host_name'		=> 'Host Name',
			'fqdn'			=> 'Host Name (FQDN)',		
			'region_name'	=> 'Region',
			'size_name'		=> 'Size', 
			'image_name'	=> 'Image',
			'login_url'		=> 'Login URL',
	
		);

		update_option( 'wpcs_server_shortcodes_enabled_data', $data );
		
		$feedback[] = array(
        	'setting' => 'wpcs_server_shortcodes_plan_name',
        	'code'    => 'settings_updated',
        	'message' => 'The New Server Shortcode Settings have been Saved',
        	'type'    => 'success',
			'status'  => 'new',
    	);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );

	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-general-settings'  ); exit;
}