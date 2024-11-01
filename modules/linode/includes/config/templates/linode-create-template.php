<?php
/**
 * Provide a Admin Area Add Template Page for the Linode Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

function wpcs_linode_create_template_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-create-template' !== $tabs_content ) {
		return;
	}

	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

	$api_status		= wpcs_check_cloud_provider_api('Linode');
	$debug_enabled 	= get_option( 'wpcs_enable_debug_mode' );
	$attributes		= ( $api_status ) ? '' : 'disabled';
	$server_module	= 'Linode';
	$sp_response	= '';
	$server_script	= '';
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_linode_create_template' );
			wpcs_do_settings_sections( 'wpcs_linode_create_template' );
			?>
			<hr>
			<?php
			wpcs_submit_button( 'Create Template', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	$debug_data = array(
		"name"			=> get_option( 'wpcs_linode_template_name' ),
		"host_name"		=> get_option( 'wpcs_linode_template_host_name' ),
		"region"		=> get_option( 'wpcs_linode_template_region' ),
		"size"			=> get_option( 'wpcs_linode_template_size' ),
		"image"			=> get_option( 'wpcs_linode_template_type' ),
		"ssh_key"		=> get_option( 'wpcs_linode_template_ssh_key' ),
		"script_name"	=> get_option( 'wpcs_linode_template_startup_script_name' ),
		"backups"		=> get_option( 'wpcs_linode_template_enable_backups' ),
	);

	if ( get_option( 'wpcs_linode_template_name' ) ) {

		$server_type				= get_option( 'wpcs_linode_template_type' );
		$server_name				= get_option( 'wpcs_linode_template_name' );
		$server_host_name			= get_option( 'wpcs_linode_template_host_name' );	
		$server_region				= get_option( 'wpcs_linode_template_region' );
		$server_size				= get_option( 'wpcs_linode_template_size' );
		$server_ssh_key				= get_option( 'wpcs_linode_template_ssh_key' );
		$server_startup_script		= get_option( 'wpcs_linode_template_startup_script_name' );
		$server_backups				= get_option( 'wpcs_linode_template_enable_backups' );

		$server_host_name_explode	= explode( '|', $server_host_name );
		$server_host_name			= $server_host_name_explode[0];
		$server_host_name_label		= isset( $server_host_name_explode[1] ) ? $server_host_name_explode[1] : '';
		
		$server_size_explode		= explode( '|', $server_size );
		$server_size				= $server_size_explode[0];
		$server_size_name			= isset( $server_size_explode[1] ) ? $server_size_explode[1] : '';
		
		$server_region_explode		= explode( '|', $server_region );
		$server_region				= $server_region_explode[0];
		$server_region_name			= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
		
		$server_type_explode		= explode( '|', $server_type );
		$server_type				= $server_type_explode[0];
		$server_type_name			= isset( $server_type_explode[1] ) ? $server_type_explode[1] : '';
		
		$server_region				= ( 'userselected' == $server_region_name ) ? 'userselected' : $server_region ;
		$server_module_lc			= strtolower( str_replace( " ", "_", $server_module ) );

		$server_enable_backups		= ( isset( $server_backups ) && $server_backups ) ? true : false;

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
				"backups"			=>	$server_enable_backups,
				"template_name"		=>  "{$server_module_lc}_template",
				"module"			=>  $server_module,
				"site_counter"		=>	0,
		);

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'Linode' ][ 'templates' ][] = $droplet_data;
		$template_data[ 'Linode' ][ 'templates' ][] = $droplet_data;

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );
			
	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_linode_template_type');
	delete_option( 'wpcs_linode_template_name');
	delete_option( 'wpcs_linode_template_region' );
	delete_option( 'wpcs_linode_template_size' );
	delete_option( 'wpcs_linode_template_host_name' );	
	delete_option( 'wpcs_linode_template_ssh_key' );
	delete_option( 'wpcs_linode_template_startup_script_name' );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_create_template_template', 10, 3 );