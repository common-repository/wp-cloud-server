<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_runcloud_managed_server_template', 'wpcs_runcloud_managed_server_template_options' );
add_action( 'admin_post_runcloud_managed_server_template', 'wpcs_runcloud_managed_server_template_options' );

function wpcs_runcloud_managed_server_template_options() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_name'] ) ) {
		$wpcs_serverpilot_template_name = $_POST['wpcs_serverpilot_template_name'];
	}
	
	// Save the ServerPilot Hostname
	if ( isset( $_POST['wpcs_serverpilot_template_host_name'] ) ) {
		update_option( 'wpcs_serverpilot_template_host_name', $_POST['wpcs_serverpilot_template_host_name'] );
	}
	
	// Save the ServerPilot Template Region
	if ( isset( $_POST['wpcs_serverpilot_template_region'] ) ) {
		update_option( 'wpcs_serverpilot_template_region', $_POST['wpcs_serverpilot_template_region'] );
	}
	
	// Save the ServerPilot Template Size
	if ( isset( $_POST['wpcs_serverpilot_template_size'] ) ) {
		update_option( 'wpcs_serverpilot_template_size', $_POST['wpcs_serverpilot_template_size'] );
	}
	
	// Save the ServerPilot Template Type
	if ( isset( $_POST['wpcs_serverpilot_template_type'] ) ) {
		update_option( 'wpcs_serverpilot_template_type', $_POST['wpcs_serverpilot_template_type'] );
	}
	
	// Save the ServerPilot Template Module
	if ( isset( $_POST['wpcs_serverpilot_template_module'] ) ) {
		update_option( 'wpcs_serverpilot_template_module', $_POST['wpcs_serverpilot_template_module'] );
	}
	
	// Save the ServerPilot Template Enable Backups
	if ( isset( $_POST['wpcs_serverpilot_template_enable_backups'] ) ) {
		update_option( 'wpcs_serverpilot_template_enable_backups', $_POST['wpcs_serverpilot_template_enable_backups'] );
	}
	
	// Save the ServerPilot Template SSH Key
	if ( isset( $_POST['wpcs_serverpilot_template_ssh_key'] ) ) {
		update_option( 'wpcs_serverpilot_template_ssh_key', $_POST['wpcs_serverpilot_template_ssh_key'] );
	}
	
	// Save the ServerPilot Template Application
	if ( isset( $_POST['wpcs_serverpilot_template_install_app'] ) ) {
		update_option( 'wpcs_serverpilot_template_install_app', $_POST['wpcs_serverpilot_template_install_app'] );
	}
	
	// Save the ServerPilot Default Application
	if ( isset( $_POST['wpcs_serverpilot_template_default_app'] ) ) {
		update_option( 'wpcs_serverpilot_template_default_app', $_POST['wpcs_serverpilot_template_default_app'] );
	}

	// Save the ServerPilot Template Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user'] ) ) {
		update_option( 'wpcs_serverpilot_template_system_user', $_POST['wpcs_serverpilot_template_system_user'] );
	}

	// Save the ServerPilot Template Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user_password'] ) ) {
		update_option( 'wpcs_serverpilot_template_system_user_password', $_POST['wpcs_serverpilot_template_system_user_password'] );
	}
	
	// Save the ServerPilot Default Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user_name'] ) ) {
		update_option( 'wpcs_serverpilot_template_system_user_name', $_POST['wpcs_serverpilot_template_system_user_name'] );
	}
	
	$debug_data = array(
		"name"					=>	$wpcs_serverpilot_template_name,
		"hostname"				=>	get_option( 'wpcs_serverpilot_template_host_name' ),
		"region"				=>	get_option( 'wpcs_serverpilot_template_region' ),
		"size"					=>	get_option( 'wpcs_serverpilot_template_size' ),
		"image"					=> 	get_option( 'wpcs_serverpilot_template_type' ),
		"module"				=> 	get_option( 'wpcs_serverpilot_template_module' ),
		"ssh_key"				=>	get_option( 'wpcs_serverpilot_template_ssh_key' ),
		"backups"				=>	get_option( 'wpcs_serverpilot_template_enable_backups' ),
		"web_app"				=>	get_option( 'wpcs_serverpilot_template_install_app' ),
		"default_app"			=>	get_option( 'wpcs_serverpilot_template_default_app' ),
		"system_user"			=>	get_option( 'wpcs_serverpilot_template_system_user' ),
		"system_user_name"		=>	get_option( 'wpcs_serverpilot_template_system_user_name' ),
		"system_user_password"	=>	get_option( 'wpcs_serverpilot_template_system_user_password' ),
	);
	
	update_option( 'wpcs_managed_template_debug', $debug_data );
	
	$server_name			= $wpcs_serverpilot_template_name;
	$server_size			= get_option( 'wpcs_serverpilot_template_size' );
	
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	if ( '' == $server_name ) {
		$feedback[] = array(
        	'setting' => 'wpcs_serverpilot_template_name',
        	'code'    => 'settings_error',
        	'message' => 'Please enter a valid Template Name',
        	'type'    => 'danger',
			'status'  => 'new',
    	);
	}
	
	if ( 'no-server' == $server_size ) {	
		$error['wpcs_serverpilot_template_size'] = 'true';
	}
		
	if ( isset( $server_name ) && ( '' !== $server_name ) && ( '' !== $server_size ) ) {
		
		$server_region				= get_option( 'wpcs_serverpilot_template_region' );
		$server_host_name			= get_option( 'wpcs_serverpilot_template_host_name' );
		$server_type				= get_option( 'wpcs_serverpilot_template_type' );
		$server_module				= get_option( 'wpcs_serverpilot_template_module' );
		$server_backups				= get_option( 'wpcs_serverpilot_template_enable_backups' );
		$server_ssh_key				= get_option( 'wpcs_serverpilot_template_ssh_key', '' );
		$server_web_app				= get_option( 'wpcs_serverpilot_template_install_app' );
		$server_default_app			= get_option( 'wpcs_serverpilot_template_default_app' );
		$system_user				= get_option( 'wpcs_serverpilot_template_system_user' );
		$system_user_name			= get_option( 'wpcs_serverpilot_template_system_user_name' );
		$system_user_password		= get_option( 'wpcs_serverpilot_template_system_user_password' );
		
		$server_web_app				= ( $server_web_app ) ? $server_web_app : false;
		$server_default_app			= ( $server_default_app ) ? $server_default_app : false;
			
		$server_host_name_explode	= explode( '|', $server_host_name );
		$server_host_name			= $server_host_name_explode[0];
		$server_host_name_label		= isset( $server_host_name_explode[1] ) ? $server_host_name_explode[1] : '';
		
		$server_size_explode		= explode( '|', $server_size );
		$server_size_name			= $server_size_explode[0];
		$server_size				= isset( $server_size_explode[1] ) ? $server_size_explode[1] : '';
		
		$server_region_explode		= explode( '|', $server_region );
		$server_region_name			= $server_region_explode[0];
		$server_region				= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
		
		$server_type_explode		= explode( '|', $server_type );
		$server_type_name			= $server_type_explode[0];
		//$server_type				= $server_type_explode[1];
		
		$module						= strtolower( str_replace( " ", "_", $server_module ) );
		
		// Need to retrieve the image value depending on the cloud provider
		$server_type				= call_user_func("wpcs_{$module}_os_list", $server_type_name );
		
		$server_region				= ( 'userselected' == $server_region_name ) ? $server_region_name : $server_region;
		
		$server_enable_backups		= ( $server_backups ) ? true : false;

		// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"					=>  $server_name,
			"slug"					=>  sanitize_title( $server_name ),
			"host_name"				=>  $server_host_name,
			"host_name_label"		=>	$server_host_name_label,
			"region"				=>	$server_region,
			"region_name"			=>	$server_region_name,
			"size"					=>	$server_size,
			"size_name"				=>	$server_size_name,
			"image"					=>	$server_type,
			"image_name"			=>	$server_type_name,
			"backups"				=>	$server_enable_backups,
			"template_name"			=>  'runcloud_template',
			"hosting_type"			=>	'Shared',
			"module"				=>  $server_module,
			"plan"					=>	'',
			"autossl"				=>	'',
			"monitor_enabled"		=>	false,
			"ssh_key"				=>	$server_ssh_key,
			"user_data"				=>  '',
			"site_counter"			=>  0,
			"web_app"				=>  $server_web_app,
			"default_app"			=>  $server_default_app,
			"system_user"			=>	$system_user,
			"system_user_name"		=>	$system_user_name,
			"system_user_password"	=>	$system_user_password,
			"custom_settings"		=>	array(
											"DCID"		=>	$server_region,
											"VPSPLANID"	=>	$server_size,
											"OSID"		=> 	$server_type,
										), 
		);
		
		update_option( 'wpcs_tmplte', $droplet_data );

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'RunCloud' ][ 'templates' ][] = $droplet_data;
		
		// Save backup copy of templates
		$template_data[ 'RunCloud' ][ 'templates' ][] = $droplet_data;

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );
			
		update_option( 'sptemplate_data', $module_data );
		
		foreach ( $feedback as $key => $setting ) {
			if ( ( 'wpcs_serverpilot_template_name' == $setting['setting'] ) && ( 'settings_error' == $setting['code'] ) ) {
				unset( $feedback[$key] );
			}
		}
		
		$feedback[] = array(
        	'setting' => 'wpcs_serverpilot_template_name',
        	'code'    => 'settings_updated',
        	'message' => 'The New RunCloud Template has been Saved',
        	'type'    => 'success',
			'status'  => 'new',
    	);
	}
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_serverpilot_template_type');
	delete_option( 'wpcs_serverpilot_template_name');
	delete_option( 'wpcs_serverpilot_template_region' );
	delete_option( 'wpcs_serverpilot_template_size' );
	delete_option( 'wpcs_serverpilot_template_module' );
	delete_option( 'wpcs_serverpilot_template_enable_backups' );
	delete_option( 'wpcs_serverpilot_template_ssh_key' );
	delete_option( 'wpcs_serverpilot_template_install_app' );
	delete_option( 'wpcs_serverpilot_template_default_app' );
	delete_option( 'wpcs_serverpilot_template_system_user' );
	delete_option( 'wpcs_serverpilot_template_system_user_name' );
	delete_option( 'wpcs_serverpilot_template_system_user_password' );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-runcloud'  ); exit;
}