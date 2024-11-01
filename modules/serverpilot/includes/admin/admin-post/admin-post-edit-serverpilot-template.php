<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_edit_serverpilot_template', 'wpcs_handle_edit_serverpilot_template' );
add_action( 'admin_post_handle_edit_serverpilot_template', 'wpcs_handle_edit_serverpilot_template' );

function wpcs_handle_edit_serverpilot_template() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_name'] ) ) {
		$server_name = $_POST['wpcs_serverpilot_template_name'];
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
	if ( isset( $_POST['wpcs_serverpilot_template_plan'] ) ) {
		$server_plan = $_POST['wpcs_serverpilot_template_plan'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_enable_backups'] ) ) {
		$server_backups = $_POST['wpcs_serverpilot_template_enable_backups'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_serverpilot_template_autossl'] ) ) {
		$server_autossl = $_POST['wpcs_serverpilot_template_autossl'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_serverpilot_template_site_monitor'] ) ) {
		$server_monitor_enabled = $_POST['wpcs_serverpilot_template_site_monitor'];
	}

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
		$module					= strtolower( str_replace( " ", "_", $server_module ) );
		$server_image			= call_user_func("wpcs_{$module}_os_list", $server_type_name );
		
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

			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
			$template_data	= get_option( 'wpcs_template_data_backup' );
			
			if ( !empty($module_data) ) {
				foreach ( $module_data['ServerPilot']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$module_data['ServerPilot']['templates'][$key]=$droplet_data;
					}	
				}
			}
				
			if ( !empty($template_data) ) {
				foreach ( $template_data['ServerPilot']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$template_data['ServerPilot']['templates'][$key]=$droplet_data;
					}	
				}
			}
	
			// Update the Module List
			update_option( 'wpcs_module_list', $module_data );
			
			// Update the Template Backup
			update_option( 'wpcs_template_data_backup', $template_data );
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_serverpilot_confirm_template_delete',
        'code'    => 'settings_updated',
        'message' => 'The ServerPilot Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-serverpilot' ); exit;
}