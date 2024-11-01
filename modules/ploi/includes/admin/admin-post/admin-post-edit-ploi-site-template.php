<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_edit_ploi_site_template', 'wpcs_handle_edit_ploi_site_template' );
add_action( 'admin_post_handle_edit_ploi_site_template', 'wpcs_handle_edit_ploi_site_template' );

function wpcs_handle_edit_ploi_site_template() {
	
	// Save the Ploi Template Name
	$wpcs_ploi_site_template_name					= isset( $_POST['wpcs_ploi_site_template_name'] ) ? $_POST['wpcs_ploi_site_template_name'] : 'none';

	// Save the Ploi Template Root Domain
	$wpcs_ploi_site_template_root_domain			= isset( $_POST['wpcs_ploi_site_template_root_domain'] ) ? $_POST['wpcs_ploi_site_template_root_domain'] : 'none';

	// Save the Ploi Template Server Id
	$wpcs_ploi_site_template_server_id				= isset( $_POST['wpcs_ploi_site_template_server_id'] ) ? $_POST['wpcs_ploi_site_template_server_id'] : 'none';

	// Save the Ploi Template Project Directory
	$wpcs_ploi_site_template_project_directory		= isset( $_POST['wpcs_ploi_site_template_project_directory'] ) ? $_POST['wpcs_ploi_site_template_project_directory'] : 'none';

	// Save the Ploi Template Web Projectory
	$wpcs_ploi_site_template_web_directory			= isset( $_POST['wpcs_ploi_site_template_web_directory'] ) ? $_POST['wpcs_ploi_site_template_web_directory'] : 'none';

	// Save the Ploi Template System User
	$wpcs_ploi_site_template_system_user			= isset( $_POST['wpcs_ploi_site_template_system_user'] ) ? $_POST['wpcs_ploi_site_template_system_user'] : 'none';

	// Save the Ploi Template Web Template
	$wpcs_ploi_site_template_web_template			= isset( $_POST['wpcs_ploi_site_template_web_template'] ) ? $_POST['wpcs_ploi_site_template_web_template'] : 'none';

	// Save the Ploi Template Install App
	$wpcs_ploi_site_template_install_app			= isset( $_POST['wpcs_ploi_site_template_install_app'] ) ? $_POST['wpcs_ploi_site_template_install_app'] : 'none';

	// Save the Ploi Template Enable SSL
	$wpcs_ploi_site_template_enable_ssl				= isset( $_POST['wpcs_ploi_site_template_enable_ssl'] ) ? $_POST['wpcs_ploi_site_template_enable_ssl'] : false;

	// Save the Ploi Template Site Counter
	$wpcs_ploi_site_template_site_counter			= isset( $_POST['wpcs_ploi_site_template_site_counter'] ) ? $_POST['wpcs_ploi_site_template_site_counter'] : 'none';

	$debug_data = array(
		"name"					=>	$wpcs_ploi_site_template_name,
		"root_domain"			=>	$wpcs_ploi_site_template_root_domain,
		"server_id"				=>	$wpcs_ploi_site_template_server_id,
		"project_directory"		=>	$wpcs_ploi_site_template_project_directory,
		"web_directory"			=> 	$wpcs_ploi_site_template_web_directory,
		"system_user"			=> 	$wpcs_ploi_site_template_system_user,
		"web_template"			=>	$wpcs_ploi_site_template_web_template,
		"install_app"			=>	$wpcs_ploi_site_template_install_app,
		"enable_ssl"			=>	$wpcs_ploi_site_template_enable_ssl,
		"site_counter"			=>	$wpcs_ploi_site_template_site_counter,
	);
	
	update_option( 'wpcs_managed_site_template_debug', $debug_data );
	
	$site_name				= $wpcs_ploi_site_template_name;
	$site_server_id			= $wpcs_ploi_site_template_server_id;
	
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	if ( '' == $site_name ) {
		$feedback[] = array(
        	'setting' => 'wpcs_ploi_site_template_name',
        	'code'    => 'settings_error',
        	'message' => 'Please enter a valid Site Template Name',
        	'type'    => 'danger',
			'status'  => 'new',
    	);
	}
		
	if ( isset( $site_name ) && ( '' !== $site_name ) && ( '' !== $site_server_id ) ) {
		
		$site_root_domain				= $wpcs_ploi_site_template_root_domain;
		$site_project_directory			= $wpcs_ploi_site_template_project_directory;
		$site_web_directory				= $wpcs_ploi_site_template_web_directory;
		$site_system_user				= $wpcs_ploi_site_template_system_user;
		$site_web_template				= $wpcs_ploi_site_template_web_template;
		$site_install_app				= $wpcs_ploi_site_template_install_app;
		$site_enable_ssl				= $wpcs_ploi_site_template_enable_ssl;
		$site_counter					= $wpcs_ploi_site_template_site_counter;

		$site_root_domain_explode		= explode( '|', $site_root_domain );
		$site_root_domain_name			= $site_root_domain_explode[0];
		$site_root_domain				= isset( $site_root_domain_explode[1] ) ? $site_root_domain_explode[1] : '';
		
		$site_install_app_explode		= explode( '|', $site_install_app );
		$site_install_app				= $site_install_app_explode[0];
		$site_install_app_type			= isset( $site_install_app_explode[1] ) ? $site_install_app_explode[1] : '';
		$site_install_app_name			= isset( $site_install_app_explode[2] ) ? $site_install_app_explode[2] : '';
		
		$site_server_id_explode			= explode( '|', $site_server_id );
		$site_server_id_name			= $site_server_id_explode[0];
		$site_server_id					= isset( $site_server_id_explode[1] ) ? $site_server_id_explode[1] : '';

		$site_web_template_explode		= explode( '|', $site_web_template );
		$site_web_template_label		= $site_web_template_explode[0];
		$site_web_template				= isset( $site_web_template_explode[1] ) ? $site_web_template_explode[1] : '';

		// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"					=>  $site_name,
			"slug"					=>  sanitize_title( $site_name ),
			"host_name"				=>  $site_root_domain,
			"host_name_label"		=>	$site_root_domain_name,
			"region"				=>  '',
			"region_name"			=>  '',
			"size"					=>  '',
			"size_name"				=>  '',
			"image"					=>  '',
			"image_name"			=>  '',
			"backups"				=>	'',
			"template_name"			=>  'ploi_site_template',
			"hosting_web_template"	=>	'Shared',
			"module"				=>  'Ploi',
			"plan"					=>	'',
			"autossl"				=>	'',
			"monitor_enabled"		=>	false,
			"ssh_key"				=>	'',
			"user_data"				=>  '',
			"site_counter"			=>  $site_counter,
			"web_app"				=>  $site_install_app,
			"web_app_name"			=>  $site_install_app_name,
			"web_app_type"			=>  $site_install_app_type,
			"default_app"			=>  '',
			"database"				=>  '',
			"php_version"			=>  '',
			"webserver"				=>  '',
			"root_domain"			=>  $site_root_domain,
			"root_domain_name"		=>  $site_root_domain_name,
			"system_user"			=>	$site_system_user,
			"system_user_name"		=>	'',
			"system_user_password"	=>	'',
			"project_directory"		=>  $site_project_directory,
			"web_directory"			=>  $site_web_directory,
			"web_template"			=>	$site_web_template,
			"web_template_label"	=>	$site_web_template_label,
			"server_id"				=>	$site_server_id,
			"server_name"			=>  $site_server_id_name,
			"enable_ssl"			=>  $site_enable_ssl,
			"custom_settings"		=>	array(
											"DCID"		=>	'',
											"VPSPLANID"		=>	'',
											"OSID"		=>	'',
										), 
		);

			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
			$template_data	= get_option( 'wpcs_template_data_backup' );
			
			if ( !empty($module_data) ) {
				foreach ( $module_data['Ploi']['templates'] as $key => $templates ) {
					if ( $site_name == $templates['name'] ) {
						$module_data['Ploi']['templates'][$key]=$droplet_data;
					}	
				}
			}
				
			if ( !empty($template_data) ) {
				foreach ( $template_data['Ploi']['templates'] as $key => $templates ) {
					if ( $site_name == $templates['name'] ) {
						$template_data['Ploi']['templates'][$key]=$droplet_data;
					}	
				}
			}
	
			// Update the Module List
			update_option( 'wpcs_module_list', $module_data );
			
			// Update the Template Backup
			update_option( 'wpcs_template_data_backup', $template_data );

	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_ploi_site_template_name',
        'code'    => 'settings_updated',
        'message' => 'The Ploi Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-ploi' ); exit;
}