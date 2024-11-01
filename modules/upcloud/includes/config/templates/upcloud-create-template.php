<?php

function wpcs_upcloud_create_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'upcloud-create-template' !== $tabs_content ) {
		return;
	}

	$api_status		= wpcs_check_cloud_provider_api( 'UpCloud' );
	$attributes		= ( $api_status ) ? '' : 'disabled';

	$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
	$sp_response = '';
	$server_script = '';
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_upcloud_create_template' );
			wpcs_do_settings_sections( 'wpcs_upcloud_create_template' );
			wpcs_submit_button( 'Create Template', 'secondary', 'create_do_template', null, $attributes );
			?>
		</form>
	</div>

	<?php

	$debug_data = array(
		"name"		=>	get_option( 'wpcs_upcloud_template_name' ),
		"host_name"	=>	get_option( 'wpcs_upcloud_template_host_name' ),
		"region"	=>	get_option( 'wpcs_upcloud_template_region' ),
		"size"		=>	get_option( 'wpcs_upcloud_template_size' ),
		"module"	=>	get_option( 'wpcs_upcloud_template_module' ),
		"image"		=> 	get_option( 'wpcs_upcloud_template_type' ),
		"ssh_key"	=> 	get_option( 'wpcs_upcloud_template_ssh_key' ),
		"backups"	=>	get_option( 'wpcs_upcloud_template_enable_backups' ),
	);

	if ( get_option( 'wpcs_upcloud_template_name' ) ) {
		
		$server_module					= 'UpCloud';

		$server_type					= get_option( 'wpcs_upcloud_template_type' );
		$server_name					= get_option( 'wpcs_upcloud_template_name' );
		$server_host_name				= get_option( 'wpcs_upcloud_template_host_name' );
		$server_region					= get_option( 'wpcs_upcloud_template_region' );
		$server_size					= get_option( 'wpcs_upcloud_template_size' );
		$server_ssh_key					= get_option( 'wpcs_upcloud_template_ssh_key' );
		$server_startup_script			= get_option( 'wpcs_upcloud_template_startup_script_name' );
		$server_backups					= get_option( 'wpcs_upcloud_template_enable_backups' );
		
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
		
		$server_region					= ( 'userselected' == $server_region_name ) ? 'userselected' : $server_region ;
		$server_module_lc				= strtolower( str_replace( " ", "_", $server_module ) );

		$server_enable_backups			= ( $server_backups ) ? true : false;


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
			"ssh_key_name"		=>	$server_ssh_key,
			"user_data"			=>  $server_startup_script,
			"backups"			=>  $server_enable_backups,
			"template_name"		=>  "{$server_module_lc}_template",
			"module"			=>  $server_module,
			"site_counter"		=>	0,
		);

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ $server_module ][ 'templates' ][] = $droplet_data;
		
		// Save backup copy of templates
		$template_data[ $server_module ][ 'templates' ][] = $droplet_data;

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );
			
		update_option( 'dotemplate_data', $module_data );
		
		//echo '<script type="text/javascript"> window.location.href =  window.location.href; </script>';

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_upcloud_template_type');
	delete_option( 'wpcs_upcloud_template_name');
	delete_option( 'wpcs_upcloud_template_host_name');
	delete_option( 'wpcs_upcloud_template_module' );
	delete_option( 'wpcs_upcloud_template_region' );
	delete_option( 'wpcs_upcloud_template_size' );
	delete_option( 'wpcs_upcloud_template_ssh_key' );
	delete_option( 'wpcs_upcloud_template_startup_script_name' );
	delete_option( 'wpcs_upcloud_template_enable_backups' );
	
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_upcloud_create_template', 10, 3 );