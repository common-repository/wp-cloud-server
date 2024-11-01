<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_managed_template_options', 'wpcs_handle_managed_template_options' );
add_action( 'admin_post_handle_managed_template_options', 'wpcs_handle_managed_template_options' );

function wpcs_handle_managed_template_options() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_name'] ) ) {
		$wpcs_serverpilot_template_name = $_POST['wpcs_serverpilot_template_name'];
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
	
	// Save the ServerPilot Template Plan
	if ( isset( $_POST['wpcs_serverpilot_template_plan'] ) ) {
		update_option( 'wpcs_serverpilot_template_plan', $_POST['wpcs_serverpilot_template_plan'] );
	}
	
	// Save the ServerPilot Template AutoSSL
	if ( isset( $_POST['wpcs_serverpilot_template_autossl'] ) ) {
		update_option( 'wpcs_serverpilot_template_autossl', $_POST['wpcs_serverpilot_template_autossl'] );
	}
	
	// Save the ServerPilot Template Enable Backups
	if ( isset( $_POST['wpcs_serverpilot_template_enable_backups'] ) ) {
		update_option( 'wpcs_serverpilot_template_enable_backups', $_POST['wpcs_serverpilot_template_enable_backups'] );
	}
	
	// Save the ServerPilot Template SSH Key
	if ( isset( $_POST['wpcs_serverpilot_template_ssh_key'] ) ) {
		update_option( 'wpcs_serverpilot_template_ssh_key', $_POST['wpcs_serverpilot_template_ssh_key'] );
	}	
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_serverpilot_template_site_monitor'] ) ) {
		update_option( 'wpcs_serverpilot_template_site_monitor', $_POST['wpcs_serverpilot_template_site_monitor'] );
	}	
	
	$debug_data = array(
		"name"				=>	$wpcs_serverpilot_template_name,
		"region"			=>	get_option( 'wpcs_serverpilot_template_region' ),
		"size"				=>	get_option( 'wpcs_serverpilot_template_size' ),
		"image"				=> 	get_option( 'wpcs_serverpilot_template_type' ),
		"module"			=> 	get_option( 'wpcs_serverpilot_template_module' ),
		"plan"				=> 	get_option( 'wpcs_serverpilot_template_plan' ),
		"autossl"			=>	get_option( 'wpcs_serverpilot_template_autossl' ),
		"ssh_key"			=>	get_option( 'wpcs_serverpilot_template_ssh_key' ),
		"backups"			=>	get_option( 'wpcs_serverpilot_template_enable_backups' ),
		"monitor_enabled"	=>	get_option( 'wpcs_serverpilot_template_site_monitor' ),
	);
	
	update_option( 'wpcs_managed_template_debug', $debug_data );
	
	//$server_name			= get_option( 'wpcs_serverpilot_template_name', '' );
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
		
		$server_region			= get_option( 'wpcs_serverpilot_template_region' );
		$server_type			= get_option( 'wpcs_serverpilot_template_type' );
		$server_module			= get_option( 'wpcs_serverpilot_template_module' );
		$server_plan			= get_option( 'wpcs_serverpilot_template_plan' );
		$server_autossl			= get_option( 'wpcs_serverpilot_template_autossl' );
		$server_backups			= get_option( 'wpcs_serverpilot_template_enable_backups' );
		$server_ssh_key			= get_option( 'wpcs_serverpilot_template_ssh_key' );
		$server_monitor_enabled	= get_option( 'wpcs_serverpilot_template_site_monitor' );
		
		$server_size_explode	= explode( '|', $server_size );
		$server_size_name		= $server_size_explode[0];
		$server_size			= isset( $server_size_explode[1] ) ? $server_size_explode[1] : '';
		
		$server_region_explode	= explode( '|', $server_region );
		$server_region_name		= $server_region_explode[0];
		$server_region			= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
		
		$server_type_explode	= explode( '|', $server_type );
		$server_type_name		= $server_type_explode[0];
		$server_type			= $server_type_explode[1];
		
		// Need to retrieve the image value depending on the cloud provider
		$server_image			= call_user_func("wpcs_{$server_module}_os_list", $server_type_name );
		
		$server_region			= ( 'userselected' == $server_region_name ) ? $server_region_name : $server_region;
		
		$server_enable_backups	= ( $server_backups ) ? true : false;

		// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"				=>  $server_name,
			"slug"				=>  sanitize_title( $server_name ),
			"region"			=>	$server_region,
			"region_name"		=>	$server_region_name,
			"size"				=>	$server_size,
			"size_name"			=>	$server_size_name,
			"image"				=>	$server_image,
			"image_name"		=>	$server_type,
			"backups"			=>	$server_enable_backups,
			"template_name"		=>  'serverpilot_template',
			"hosting_type"		=>	'Shared',
			"module"			=>  $server_module,
			"plan"				=>	$server_plan,
			"autossl"			=>	$server_autossl,
			"monitor_enabled"	=>	$server_monitor_enabled,
			"ssh_key"			=>	$server_ssh_key,
			"custom_settings"	=>	array(
										"DCID"		=>	$server_region,
										"VPSPLANID"	=>	$server_size,
										"OSID"		=> 	$server_image,
									), 
		);
		
		update_option( 'wpcs_tmplte', $droplet_data );

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'ServerPilot' ][ 'templates' ][] = $droplet_data;
		
		// Save backup copy of templates
		$template_data[ 'ServerPilot' ][ 'templates' ][] = $droplet_data;

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
        	'message' => 'The New ServerPilot Template has been Saved',
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
	delete_option( 'wpcs_serverpilot_enable_backups' );
	delete_option( 'wpcs_serverpilot_template_plan' );
	delete_option( 'wpcs_serverpilot_template_autossl' );
	delete_option( 'wpcs_serverpilot_template_ssh_key' );
	delete_option( 'wpcs_serverpilot_template_site_monitor' );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-serverpilot'  ); exit;
}

/**
 *  Display Admin & ServerPilot Module Admin Notices
 *
 *  @since  1.0.0
 */
function wpcs_sp_template_admin_notices( $task=null, $item=null, $page=null) {
			
	$type = 'error';
	$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server' );
		
	$output  = "<div class='uk-alert-{$type} wpcs-notice' uk-alert> \n";
    $output .= "<p>{$message}</p>";
    $output .= "</div> \n";
		
	echo $output;
}