<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_edit_runcloud_template', 'wpcs_handle_edit_runcloud_template' );
add_action( 'admin_post_handle_edit_runcloud_template', 'wpcs_handle_edit_runcloud_template' );

function wpcs_handle_edit_runcloud_template() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_name'] ) ) {
		$server_name = $_POST['wpcs_serverpilot_template_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_host_name'] ) ) {
		$server_host_name = $_POST['wpcs_serverpilot_template_host_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_type'] ) ) {
		$server_type = $_POST['wpcs_serverpilot_template_type'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_module'] ) ) {
		$server_module = $_POST['wpcs_serverpilot_template_module'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_region'] ) ) {
		$server_region = $_POST['wpcs_serverpilot_template_region'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_size'] ) ) {
		$server_size = $_POST['wpcs_serverpilot_template_size'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_ssh_key'] ) ) {
		$server_ssh_key = $_POST['wpcs_serverpilot_template_ssh_key'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_default_app'] ) ) {
		$server_default_app = $_POST['wpcs_serverpilot_template_default_app'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_enable_backups'] ) ) {
		$server_backups = $_POST['wpcs_serverpilot_template_enable_backups'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_install_app'] ) ) {
		$server_web_app = $_POST['wpcs_serverpilot_template_install_app'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_serverpilot_runcloud_site_monitor'] ) ) {
		$server_monitor_enabled = $_POST['wpcs_serverpilot_template_site_monitor'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_site_counter'] ) ) {
		$server_site_counter = $_POST['wpcs_serverpilot_template_site_counter'];
	}

	// Save the ServerPilot Template Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user'] ) ) {
		$system_user = $_POST['wpcs_serverpilot_template_system_user'];
	}

	// Save the ServerPilot Template Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user_password'] ) ) {
		$system_user_password = $_POST['wpcs_serverpilot_template_system_user_password'];
	}
	
	// Save the ServerPilot Default Application
	if ( isset( $_POST['wpcs_serverpilot_template_system_user_name'] ) ) {
		$system_user_name = $_POST['wpcs_serverpilot_template_system_user_name'];
	}
	
		$server_web_app				= ( $server_web_app ) ? $server_web_app : false;
		$server_default_app			= ( isset( $server_default_app ) && $server_default_app ) ? true : false;
		$server_monitor_enabled		= ( isset( $server_monitor_enabled ) && $server_monitor_enabled ) ? true : false;
			
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
		$server_type				= $server_type_explode[1];
		
		// Need to retrieve the image value depending on the cloud provider
		$module						= strtolower( str_replace( " ", "_", $server_module ) );
		$server_image				= call_user_func("wpcs_{$module}_os_list", $server_type_name );
		
		$server_region				= ( 'userselected' == $server_region_name ) ? $server_region_name : $server_region;
		
		$server_enable_backups		= ( isset( $server_backups ) && $server_backups ) ? true : false;

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
			"image"					=>	$server_image,
			"image_name"			=>	$server_type_name,
			"backups"				=>	$server_enable_backups,
			"template_name"			=>  'runcloud_template',
			"hosting_type"			=>	'Shared',
			"module"				=>  $server_module,
			"plan"					=>	'',
			"autossl"				=>	'',
			"monitor_enabled"		=>	$server_monitor_enabled,
			"ssh_key"				=>	$server_ssh_key,
			"user_data"				=>  '',
			"site_counter"			=>  $server_site_counter,
			"web_app"				=>  $server_web_app,
			"default_app"			=>  $server_default_app,
			"system_user"			=>	$system_user,
			"system_user_name"		=>	$system_user_name,
			"system_user_password"	=>	$system_user_password,
			"custom_settings"		=>	array(
										"DCID"		=>	$server_region,
										"VPSPLANID"	=>	$server_size,
										"OSID"		=> 	$server_image,
									), 
		);

			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
			$template_data	= get_option( 'wpcs_template_data_backup' );
			
			if ( !empty($module_data) ) {
				foreach ( $module_data['RunCloud']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$module_data['RunCloud']['templates'][$key]=$droplet_data;
					}	
				}
			}
				
			if ( !empty($template_data) ) {
				foreach ( $template_data['RunCloud']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$template_data['RunCloud']['templates'][$key]=$droplet_data;
					}	
				}
			}
	
			// Update the Module List
			update_option( 'wpcs_module_list', $module_data );
			
			// Update the Template Backup
			update_option( 'wpcs_template_data_backup', $template_data );
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_runcloud_template_name',
        'code'    => 'settings_updated',
        'message' => 'The RunCloud Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-runcloud' ); exit;
}