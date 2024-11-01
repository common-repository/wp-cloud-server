<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_edit_vultr_template', 'wpcs_handle_edit_vultr_template' );
add_action( 'admin_post_handle_edit_vultr_template', 'wpcs_handle_edit_vultr_template' );

function wpcs_handle_edit_vultr_template() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_name'] ) ) {
		$server_name = $_POST['wpcs_vultr_template_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_type'] ) ) {
		$server_type = $_POST['wpcs_vultr_template_type'];
	}

	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_app'] ) ) {
		$server_app = $_POST['wpcs_vultr_template_app'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_host_name'] ) ) {
		$server_host_name = $_POST['wpcs_vultr_template_host_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_region'] ) ) {
		$server_region = $_POST['wpcs_vultr_template_region'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_size'] ) ) {
		$server_size = $_POST['wpcs_vultr_template_size'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_ssh_key'] ) ) {
		$server_ssh_key = $_POST['wpcs_vultr_template_ssh_key'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_startup_script_name'] ) ) {
		$server_startup_script = $_POST['wpcs_vultr_template_startup_script_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_enable_backups'] ) ) {
		$server_backups = $_POST['wpcs_vultr_template_enable_backups'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_vultr_template_site_counter'] ) ) {
		$server_site_counter = $_POST['wpcs_vultr_template_site_counter'];
	}
	
		$server_module					= 'Vultr';

		$server_host_name_explode		= explode( '|', $server_host_name );
		$server_host_name				= $server_host_name_explode[0];
		$server_host_name_label			= isset( $server_host_name_explode[1] ) ? $server_host_name_explode[1] : '';
		
		$server_size_explode			= explode( '|', $server_size );
		$server_size					= $server_size_explode[0];
		$server_size_name				= isset( $server_size_explode[1] ) ? $server_size_explode[1] : '';
		
		$server_region_explode			= explode( '|', $server_region );
		$server_region					= $server_region_explode[0];
		$server_region_name				= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
		
		$server_type_explode			= explode( '|', $server_type );
		$server_type					= $server_type_explode[0];
		$server_type_name				= isset( $server_type_explode[1] ) ? $server_type_explode[1] : '';

		$server_app_explode				= explode( '|', $server_app );
		$server_app						= $server_app_explode[0];
		$server_app_name				= isset( $server_app_explode[1] ) ? $server_app_explode[1] : '';
		
		$server_region					= ( 'userselected' == $server_region_name ) ? 'userselected' : $server_region ;
		$server_module_lc				= strtolower( str_replace( " ", "_", $server_module ) );

		$server_enable_backups			= ( isset( $server_backups ) && $server_backups ) ? 'yes' : 'no';

		// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"				=>  $server_name,
			"host_name"			=>  $server_host_name,
			"host_name_label"	=>	$server_host_name_label,
			"slug"				=>  sanitize_title( $server_name ),
			"region"			=>	$server_region,
			"region_name"		=>  $server_region_name,
			"size"				=>	$server_size,
			"size_name"			=>	$server_size_name,
			"image"				=> 	$server_type,
			"image_name"		=>	$server_type_name,
			"app"				=> 	$server_app,
			"app_name"			=>	$server_app_name,
			"ssh_key_name"		=>	$server_ssh_key,
			"user_data"			=>  $server_startup_script,
			"backups"			=>  $server_enable_backups,
			"template_name"		=>  "{$server_module_lc}_template",
			"module"			=>  $server_module,
			"site_counter"		=>	$server_site_counter,
		);

			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
			$template_data	= get_option( 'wpcs_template_data_backup' );
			
			if ( !empty($module_data) ) {
				foreach ( $module_data[$server_module]['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$module_data[$server_module]['templates'][$key]=$droplet_data;
					}	
				}
			}
				
			if ( !empty($template_data) ) {
				foreach ( $template_data[$server_module]['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$template_data[$server_module]['templates'][$key]=$droplet_data;
					}	
				}
			}
	
			// Update the Module List
			update_option( 'wpcs_module_list', $module_data );
			
			// Update the Template Backup
			update_option( 'wpcs_template_data_backup', $template_data );
		
			$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_vultr_confirm_template_delete',
        'code'    => 'settings_updated',
        'message' => 'The Vultr Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-vultr' ); exit;
}