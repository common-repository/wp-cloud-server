<?php

function wpcs_vultr_create_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-create-template' !== $tabs_content ) {
		return;
	}

$api_status		= wpcs_check_cloud_provider_api('Vultr');
$attributes		= ( $api_status ) ? '' : 'disabled';

//if ( wp_verify_nonce( $nonce, 'do_add_template_nonce' ) && wpcs_check_cloud_provider() ) {

	$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
	$sp_response = '';
	$server_script = '';
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_vultr_create_template' );
			wpcs_do_settings_sections( 'wpcs_vultr_create_template' );
			?>
			<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Admin SSH Key:', 'wp-cloud-server' ); ?></th>
					<td>
						<?php
						$ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
						?>
						<select  class="w-400" name="wpcs_vultr_template_ssh_key" id="wpcs_vultr_template_ssh_key">
							<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
							<?php if ( !empty( $ssh_keys ) ) { ?>
								<optgroup label="User SSH Keys">
								<?php foreach ( $ssh_keys as $key => $ssh_key ) {
									echo "<option value='{$ssh_key['name']}'>{$ssh_key['name']}</option>";
								} ?>
								</optgroup>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Startup Script:', 'wp-cloud-server' ); ?></th>
					<td>
						<?php
						$startup_scripts = get_option( 'wpcs_startup_scripts' );
						?>
						<select class="w-400" name="wpcs_vultr_template_startup_script_name" id="wpcs_vultr_template_startup_script_name">
							<option value="no-startup-script"><?php esc_html_e( '-- No Startup Script --', 'wp-cloud-server' ); ?></option>
							<?php
							if ( !empty( $startup_scripts ) ) { ?>
								<optgroup label="User Startup Scripts">
								
								<?php foreach ( $startup_scripts as $key => $script ) {
									echo "<option value='{$script['name']}'>{$script['name']}</option>";
								} ?>
								</optgroup>
							<?php	
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Server Backups:', 'wp-cloud-server' ); ?></th>
					<td>
						<input type='checkbox' id='wpcs_vultr_template_enable_backups' name='wpcs_vultr_template_enable_backups' value='1'>
					</td>
				</tr>
			</tbody>
		</table>
		<hr>
		<?php
		wpcs_submit_button( 'Create Template', 'secondary', 'create_do_template', null, $attributes );
		?>
		</form>
	</div>

	<?php

	$debug_data = array(
		"name"		=>	get_option( 'wpcs_vultr_template_name' ),
		"host_name"	=>	get_option( 'wpcs_vultr_template_host_name' ),
		"region"	=>	get_option( 'wpcs_vultr_template_region' ),
		"size"		=>	get_option( 'wpcs_vultr_template_size' ),
		"image"		=> 	get_option( 'wpcs_vultr_template_type' ),
		"app"		=>	get_option( 'wpcs_vultr_template_app' ),
		"ssh_key"	=> 	get_option( 'wpcs_vultr_template_ssh_key' ),
		"user_data"	=>  get_option( 'wpcs_vultr_template_startup_script_name' ),
		"backups"	=>	get_option( 'wpcs_vultr_template_enable_backups' ),
	);

	if ( get_option( 'wpcs_vultr_template_name' ) ) {

		$server_module					= 'Vultr';

		$server_type					= get_option( 'wpcs_vultr_template_type' );
		$server_app	        			= get_option( 'wpcs_vultr_template_app' );
		$server_name					= get_option( 'wpcs_vultr_template_name' );
		$server_host_name				= get_option( 'wpcs_vultr_template_host_name' );
		$server_region					= get_option( 'wpcs_vultr_template_region' );
		$server_size					= get_option( 'wpcs_vultr_template_size' );
		$server_ssh_key					= get_option( 'wpcs_vultr_template_ssh_key' );
		$server_startup_script			= get_option( 'wpcs_vultr_template_startup_script_name' );
		$server_backups	        		= get_option( 'wpcs_vultr_template_enable_backups' );

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

		$server_enable_backups			= ( $server_backups ) ? 'yes' : 'no';

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

		wpcs_add_settings_error(
			'wpcs_vultr_template_name',
			esc_attr( 'settings_error' ),
			__( 'The Vultr Template was updated.', 'wp-cloud-server-vultr' ),
			'updated'
		);
		
		//echo '<script type="text/javascript"> window.location.href =  window.location.href; </script>';

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_vultr_template_type');
	delete_option( 'wpcs_vultr_template_app');
	delete_option( 'wpcs_vultr_template_name');
	delete_option( 'wpcs_vultr_template_host_name');
	delete_option( 'wpcs_vultr_template_region' );
	delete_option( 'wpcs_vultr_template_size' );
	delete_option( 'wpcs_vultr_template_ssh_key' );
	delete_option( 'wpcs_vultr_template_startup_script_name' );
	delete_option( 'wpcs_vultr_template_enable_backups' );
	
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_create_template', 10, 3 );