<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_edit_cloudways_template', 'wpcs_handle_edit_cloudways_template' );
add_action( 'admin_post_handle_edit_cloudways_template', 'wpcs_handle_edit_cloudways_template' );

function wpcs_handle_edit_cloudways_template() {
	
		// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_edit_template_name'] ) ) {
		$server_name = $_POST['wpcs_cloudways_edit_template_name'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_edit_template_host_name'] ) ) {
		$server_host_name = $_POST['wpcs_cloudways_edit_template_host_name'];
	}
	
	// Save the ServerPilot Template Size
	if ( isset( $_POST['wpcs_cloudways_edit_template_providers'] ) ) {
		$server_providers = $_POST['wpcs_cloudways_edit_template_providers'];
	}
	
	// Save the ServerPilot Template Type
	if ( isset( $_POST['wpcs_cloudways_edit_template_region'] ) ) {
		$server_region = $_POST['wpcs_cloudways_edit_template_region'];
	}
	
	// Save the ServerPilot Template Module
	if ( isset( $_POST['wpcs_cloudways_edit_template_size'] ) ) {
		$server_size = $_POST['wpcs_cloudways_edit_template_size'];
	}
	
	// Save the ServerPilot Template Plan
	if ( isset( $_POST['wpcs_cloudways_edit_template_app_name'] ) ) {
		$server_app_name = $_POST['wpcs_cloudways_edit_template_app_name'];
	}
	
	// Save the ServerPilot Template AutoSSL
	if ( isset( $_POST['wpcs_cloudways_edit_template_app'] ) ) {
		$server_app = $_POST['wpcs_cloudways_edit_template_app'];
	}
	
	// Save the ServerPilot Template Enable Backups
	if ( isset( $_POST['wpcs_cloudways_edit_template_project'] ) ) {
		$server_project = $_POST['wpcs_cloudways_edit_template_project'];
	}
	
	// Save the ServerPilot Template Enable Backups
	if ( isset( $_POST['wpcs_cloudways_edit_template_site_counter'] ) ) {
		$server_site_counter = $_POST['wpcs_cloudways_edit_template_site_counter'];
	}
	
	// Save the ServerPilot Template Database Volume Size
	if ( isset( $_POST['wpcs_cloudways_edit_template_db_volume_size'] ) ) {
		$server_db_volume_size = $_POST['wpcs_cloudways_edit_template_db_volume_size'];
	}
	
	// Save the ServerPilot Template Data Volume Size
	if ( isset( $_POST['wpcs_cloudways_edit_template_data_volume_size'] ) ) {
		$server_data_volume_size = $_POST['wpcs_cloudways_edit_template_data_volume_size'];
	}
	
	if ( isset( $_POST['wpcs_cloudways_edit_template_send_email'] ) ) {
		$server_send_email = $_POST['wpcs_cloudways_edit_template_send_email'];
	}
	
	$server_module = 'Cloudways';
	
		$server_host_name_explode		= explode( '|', $server_host_name );
		$server_host_name				= $server_host_name_explode[0];
		$server_host_name_label			= isset( $server_host_name_explode[1] ) ? $server_host_name_explode[1] : '';
		
		$server_region_explode			= explode( '|', $server_region );
		$server_region					= $server_region_explode[0];
		$server_region_name				= isset($server_region_explode[1]) ? $server_region_explode[1] : '';
		
		$server_providers_explode		= explode( '|', $server_providers );
		$server_provider				= $server_providers_explode[0];
		$server_providers_name			= isset($server_providers_explode[1]) ? $server_providers_explode[1] : '';
	
		$server_app_explode				= explode( '|', $server_app );
		$server_app_application			= $server_app_explode[0];
		$server_app_version				= isset($server_app_explode[1]) ? $server_app_explode[1] : '';
		$server_app_label				= isset($server_app_explode[2]) ? $server_app_explode[2] : '';
	
		$server_send_email				= isset($server_send_email) ? true : false;
		
		$server_module_lc				= strtolower( str_replace( " ", "_", $server_module ) );
		
		// Set-up the data for the new Droplet
		$app_data = array(
			"name"				=>	$server_name,
			"host_name"			=>  $server_host_name,
			"host_name_label"	=>	$server_host_name_label,
			"cloud"				=>	$server_provider,
			"cloud_name"		=>	$server_providers_name,
			"region"			=>	$server_region,
			"region_name"		=>	$server_region_name,
			"size"				=>	$server_size,
			"image"				=> 	$server_app_application,
			"app_version"		=>	$server_app_version,
			"app_name"			=>	$server_app_name,
			"server_label"		=>	$server_name,
			"app_label"			=>	$server_app_label,
			"project_name"		=>	$server_project,
			"db_volume_size"	=>	( ( 'amazon' == $server_provider ) || ( 'gce' == $server_provider ) ) ? $server_db_volume_size : null,
			"data_volume_size"	=>	( ( 'amazon' == $server_provider ) || ( 'gce' == $server_provider ) ) ? $server_data_volume_size : null,
			"series"			=>	null,
			"template_name"		=>  "{$server_module_lc}_template",
			"module"			=>  $server_module,
			"send_email"		=>	$server_send_email,
			"site_counter"		=>	$server_site_counter,
		);

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
		
		if ( !empty($module_data) ) {
			foreach ( $module_data['Cloudways']['templates'] as $key => $templates ) {
				if ( $server_name == $templates['server_label'] ) {
					$module_data['Cloudways']['templates'][$key]=$app_data;
				}	
			}
		}
			
		if ( !empty($template_data) ) {
			foreach ( $template_data['Cloudways']['templates'] as $key => $templates ) {
				if ( $server_name == $templates['server_label'] ) {
					$template_data['Cloudways']['templates'][$key]=$app_data;
				}	
			}
		}

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );
			
		update_option( 'dotemplate_data', $module_data );
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_cloudways_edit_template_name',
        'code'    => 'settings_updated',
        'message' => 'The Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-managed-hosting'  ); exit;
}