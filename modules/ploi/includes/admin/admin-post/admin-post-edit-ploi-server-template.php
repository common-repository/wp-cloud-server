<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_ploi_edit_server_template', 'wpcs_handle_ploi_edit_server_template' );
add_action( 'admin_post_handle_ploi_edit_server_template', 'wpcs_handle_ploi_edit_server_template' );

function wpcs_handle_ploi_edit_server_template() {
	
	// Save the Ploi Template Name
	$wpcs_ploi_edit_server_template_name			= isset( $_POST['wpcs_ploi_edit_server_template_name'] ) ? $_POST['wpcs_ploi_edit_server_template_name'] : 'none';

	// Save the Ploi Template Credentials
	$wpcs_ploi_edit_server_template_credentials		= isset( $_POST['wpcs_ploi_edit_server_template_credentials'] ) ? $_POST['wpcs_ploi_edit_server_template_credentials'] : 'none';

	// Save the Ploi Template Root Domain
	$wpcs_ploi_edit_server_template_root_domain		= isset( $_POST['wpcs_ploi_edit_server_template_root_domain'] ) ? $_POST['wpcs_ploi_edit_server_template_root_domain'] : 'none';

	// Save the Ploi Template Size
	$wpcs_ploi_edit_server_template_size			= isset( $_POST['wpcs_ploi_edit_server_template_size'] ) ? $_POST['wpcs_ploi_edit_server_template_size'] : 'none';

	// Save the Ploi Template Database
	$wpcs_ploi_edit_server_template_database		= isset( $_POST['wpcs_ploi_edit_server_template_database'] ) ? $_POST['wpcs_ploi_edit_server_template_database'] : 'none';

	// Save the Ploi Template PHP version
	$wpcs_ploi_edit_server_template_php_version		= isset( $_POST['wpcs_ploi_edit_server_template_php_version'] ) ? $_POST['wpcs_ploi_edit_server_template_php_version'] : 'none';

	// Save the Ploi Template Region
	$wpcs_ploi_edit_server_template_region			= isset( $_POST['wpcs_ploi_edit_server_template_region'] ) ? $_POST['wpcs_ploi_edit_server_template_region'] : 'none';

	// Save the Ploi Template Type
	$wpcs_ploi_edit_server_template_type			= isset( $_POST['wpcs_ploi_edit_server_template_type'] ) ? $_POST['wpcs_ploi_edit_server_template_type'] : 'none';

	// Save the Ploi Template Webserver
	$wpcs_ploi_edit_server_template_webserver		= isset( $_POST['wpcs_ploi_edit_server_template_webserver'] ) ? $_POST['wpcs_ploi_edit_server_template_webserver'] : 'none';

	// Save the Ploi Template Install App
	$wpcs_ploi_edit_server_template_install_app		= isset( $_POST['wpcs_ploi_edit_server_template_install_app'] ) ? $_POST['wpcs_ploi_edit_server_template_install_app'] : 'none';
	
	// Save the Ploi Template Site Counter
	$wpcs_ploi_edit_server_template_site_counter	= isset( $_POST['wpcs_ploi_edit_server_template_site_counter'] ) ? $_POST['wpcs_ploi_edit_server_template_site_counter'] : 'none';

	$server_name					= $wpcs_ploi_edit_server_template_name;
	$server_credentials				= $wpcs_ploi_edit_server_template_credentials;
	$server_host_name				= $wpcs_ploi_edit_server_template_root_domain;
	$server_size					= $wpcs_ploi_edit_server_template_size;
	$server_database				= $wpcs_ploi_edit_server_template_database;
	$server_php_version				= $wpcs_ploi_edit_server_template_php_version;
	$server_region					= $wpcs_ploi_edit_server_template_region;
	$server_type					= $wpcs_ploi_edit_server_template_type;
	$server_webserver			 	= $wpcs_ploi_edit_server_template_webserver;
	$server_install_app				= $wpcs_ploi_edit_server_template_install_app;
	$server_site_counter			= $wpcs_ploi_edit_server_template_site_counter;

	$server_host_name_explode		= explode( '|', $server_host_name );
	$server_host_name_name			= $server_host_name_explode[0];
	$server_host_name				= isset( $server_host_name_explode[1] ) ? $server_host_name_explode[1] : '';

	$server_credentials_explode		= explode( '|', $server_credentials );
	$server_credentials_name		= $server_credentials_explode[0];
	$server_credentials				= isset( $server_credentials_explode[1] ) ? $server_credentials_explode[1] : '';
	
	$server_size_explode			= explode( '|', $server_size );
	$server_size_name				= $server_size_explode[0];
	$server_size					= isset( $server_size_explode[1] ) ? $server_size_explode[1] : '';

	$server_database_explode		= explode( '|', $server_database );
	$server_database_name			= $server_database_explode[0];
	$server_database				= isset( $server_database_explode[1] ) ? $server_database_explode[1] : '';
	
	$server_region_explode			= explode( '|', $server_region );
	$server_region_name				= $server_region_explode[0];
	$server_region					= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
	
	$server_type_explode			= explode( '|', $server_type );
	$server_type_name				= $server_type_explode[0];
	$server_type					= isset( $server_type_explode[1] ) ? $server_type_explode[1] : '';

	$server_install_app_explode		= explode( '|', $server_install_app );
	$server_install_app				= $server_install_app_explode[0];
	$server_install_app_type		= isset( $server_install_app_explode[1] ) ? $server_install_app_explode[1] : '';
	$server_install_app_name		= isset( $server_install_app_explode[2] ) ? $server_install_app_explode[2] : '';

	// Set-up the data for the new Droplet
	$droplet_data = array(
		"name"					=>  $server_name,
		"slug"					=>  sanitize_title( $server_name ),
		"host_name"				=>  $server_host_name,
		"host_name_label"		=>	$server_host_name_name,
		"region"				=>	$server_region,
		"region_name"			=>	$server_region_name,
		"size"					=>	$server_size,
		"size_name"				=>	$server_size_name,
		"image"					=>	$server_type,
		"image_name"			=>	$server_type_name,
		"backups"				=>	'',
		"template_name"			=>  'ploi_server_template',
		"hosting_type"			=>	'Shared',
		"module"				=>  'Ploi',
		"plan"					=>	'',
		"autossl"				=>	'',
		"monitor_enabled"		=>	false,
		"ssh_key"				=>	'',
		"user_data"				=>  '',
		"site_counter"			=>  $server_site_counter,
		"web_app"				=>  $server_install_app,
		"web_app_name"			=>  $server_install_app_name,
		"web_app_type"			=>  $server_install_app_type,
		"default_app"			=>  '',
		"database"				=>  $server_database,
		"database_name"			=>  $server_database_name,
		"php_version"			=>  $server_php_version,
		"webserver"				=>  $server_webserver,
		"credentials"			=>  $server_credentials,
		"credentials_name"		=>  $server_credentials_name,
		"system_user"			=>	'ploi',
		"system_user_name"		=>	'',
		"system_user_password"	=>	'',
		"project_directory"		=>  '/',
		"web_directory"			=>  '/public',
		"custom_settings"		=>	array(
										"DCID"		=>	$server_region,
										"VPSPLANID"	=>	$server_size,
										"OSID"		=> 	$server_type,
									), 
	);

			// Retrieve the Active Module List
			$module_data	= get_option( 'wpcs_module_list' );
			$template_data	= get_option( 'wpcs_template_data_backup' );
			
			if ( !empty($module_data) ) {
				foreach ( $module_data['Ploi']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$module_data['Ploi']['templates'][$key]=$droplet_data;
					}	
				}
			}
				
			if ( !empty($template_data) ) {
				foreach ( $template_data['Ploi']['templates'] as $key => $templates ) {
					if ( $server_name == $templates['name'] ) {
						$template_data['Ploi']['templates'][$key]=$droplet_data;
					}	
				}
			}
	
			// Update the Module List
			update_option( 'wpcs_module_list', $module_data );
			
			// Update the Template Backup
			update_option( 'wpcs_template_data_backup', $template_data );
		
			$feedback	= get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_ploi_edit_server_template_name',
        'code'    => 'settings_updated',
        'message' => 'The Ploi Server Template was Successfully Updated',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-ploi' ); exit;
}