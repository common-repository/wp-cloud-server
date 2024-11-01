<?php

function wpcs_upcloud_create_server_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'upcloud-create-server' !== $tabs_content ) {
		return;
	}

$api_status		= wpcs_check_cloud_provider_api( 'UpCloud');
$attributes		= ( $api_status ) ? '' : 'disabled';
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$sp_response	= '';
$server_script	= null;

?>

<div class="content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpcs_upcloud_create_server' );
		wpcs_do_settings_sections( 'wpcs_upcloud_create_server' );
		?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'SSH Key:', 'wp-cloud-server' ); ?></th>
						<td>
							<?php
							$ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
							?>
							<select style="width: 25rem;" name="wpcs_upcloud_server_ssh_key" id="wpcs_upcloud_server_ssh_key">
								<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
								<?php if ( !empty( $ssh_keys ) ) { ?>
									<optgroup label="Select SSH Key">
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
							<select style="width: 25rem;" name="wpcs_upcloud_server_startup_script_name" id="wpcs_upcloud_server_startup_script_name">
								<option value="no-startup-script"><?php esc_html_e( '-- No Startup Script --', 'wp-cloud-server' ); ?></option>
								<?php
								if ( !empty( $startup_scripts ) ) { ?>
									<optgroup label="Select Startup Script">
									
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
							<input type='checkbox' id='wpcs_upcloud_server_enable_backups' name='wpcs_upcloud_server_enable_backups' value='1'>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
			//wpcs_create_server_script_variable_settings( 'upcloud' );
			?>
			<hr>
			<?php
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_upcloud_server_name' ) ) {
		
		global $wp_settings_errors;

		$server_cloud_provider				= "UpCloud";

		// Capture the DigitalOcean Settings
		$server_type				= get_option( 'wpcs_upcloud_server_type' );
		$server_name				= get_option( 'wpcs_upcloud_server_name' );
		$server_region				= get_option( 'wpcs_upcloud_server_region' );
		$server_size				= get_option( 'wpcs_upcloud_server_size' );
		$server_ssh_key_name		= get_option( 'wpcs_upcloud_server_ssh_key' );
		$server_startup_script_name	= get_option( 'wpcs_upcloud_server_startup_script_name' );
		$server_backups				= get_option( 'wpcs_upcloud_server_enable_backups' );

		$domain_name	    		= get_option( 'wpcs_upcloud_server_variable_domain_name','' );
		$wp_site_title				= get_option( 'wpcs_upcloud_server_variable_wp_site_title','' );
		$wp_db_user					= get_option( 'wpcs_upcloud_server_variable_wp_db_user','' );
		$wp_database				= get_option( 'wpcs_upcloud_server_variable_wp_database','' );
		$admin_user					= get_option( 'wpcs_upcloud_server_variable_admin_user','' );
		$admin_passwd				= get_option( 'wpcs_upcloud_server_variable_admin_passwd','' );
		$admin_email				= get_option( 'wpcs_upcloud_server_variable_admin_email','' );

		$debug = array(
				"server_type"		=> $server_type,
				"server_name"		=> $server_name,
				"server_region"		=> $server_region,
				"server_size"		=> $server_size,
		);
		
		$server_module				= strtolower( str_replace( " ", "_", $server_cloud_provider ) );
		
		$server_enable_backups		= ( $server_backups ) ? '0100,dailies' : 'no';

		// Create Server Data Array
		$server = array(
			"name"			=>	$server_name,
			"slug"			=>	sanitize_title( $server_name ),
			"region"		=>	$server_region,
			"size"			=>	$server_size,
			"image"			=> 	$server_type,
			"module"		=>	$server_cloud_provider,
			"ssh_key"		=>	$server_ssh_key_name,
			"user_data"		=>	$server_startup_script_name,
			"backups"		=> 	$server_enable_backups,
			"hosting_type"	=>	'dedicated',
		);
		
		// Set-up the API Data for the New Server
		$api_data = array(
			"region"		=>	$server_region,
			"name"			=>	$server_name,
			"size"			=>	$server_size,
			"image"			=> 	$server_type,
		);
		
		// Check and Select the Available Region
		$api_response  = call_user_func("wpcs_{$server_module}_cloud_server_api", $api_data, 'regions', null, false, 900, 'GET', false, 'check_data_centers' );
						
		$server_region = ( isset( $api_response['region'] ) ) ? $api_response['region'] : $server_region;
		
		$debug['server_region_available'] = $server_region;

		// Set-up the data for the new Droplet
		$app_data = array(
			"name"			=>	$server_name,
			"region"		=>	$server_region,
			"size"			=>	$server_size,
			"image"			=> 	$server_type,
			"simple_backup"	=>	$server_enable_backups,
			"password"		=>	'email',
		);
		
		$startup_scripts				= get_option( 'wpcs_startup_scripts' );

		if ( is_array( $startup_scripts ) && ( 'no-startup-script' !== $server_startup_script_name ) ) {
			foreach ( $startup_scripts as $key => $script ) {
				if ( $server_startup_script_name == $script['name'] ) {
					$server_startup_script	= $script['startup_script'];
					$server_script_type		= $script['type'];
					$server_script_repos	= ( isset( $script['github_repos'] ) ) ? $script['github_repos'] : '';
					$server_script_file		= ( isset( $script['github_file'] ) ) ? $script['github_file'] : '';
				}	
			}

			if ( !$server_startup_script && 'git' == $server_script_type ) {

				if ( function_exists('wpcs_github_call_api_get_file') ) {
					$server_startup_script = wpcs_github_call_api_get_file( $server_script_repos, $server_script_file );
				}

			}

			if ( $server_startup_script ) {

				$placeholder = array(
					"{{domain_name}}",
					"{{wp_site_title}}",
					"{{wp_db_user}}",
					"{{wp_database}}",
					"{{admin_user}}",
					"{{admin_passwd}}",
					"{{admin_email}}",
				);

				$values = array(
					$domain_name,
					$wp_site_title,
					$wp_db_user,
					$wp_database,
					$admin_user,
					$admin_passwd,
					$admin_email,
				);
			
				$script	= str_replace( $placeholder, $values, $server_startup_script );
		
				$app_data['custom_settings']['script_name']	= $server_startup_script_name;
				$app_data["user_data"] 						= $script;
				$debug['server_script']						= $script;
		
				update_option( 'wpcs_updated_script', $script );

			}
		}
		
		// Check if SSH Key saved with provider
		$ssh_key_id	= false;
		
		$ssh_public_key		= wpcs_upcloud_ssh_key( $server_ssh_key_name );
		
		$debug['server_ssh_key_name']	= $server_ssh_key_name;
		
		// Use SSH Key if available or generate root password
		if ( isset( $ssh_public_key['ssh_key'] ) ) {
			$app_data["ssh_key"][] = $ssh_public_key['ssh_key'];
		}
		
		$debug['app_data'] = $app_data;

		// Send the API POST request to create the new server
		$response =  wpcs_upcloud_call_api_create_server( $app_data );
		
		$debug['droplet_response']	= $response;
		update_option( 'wpcs_droplet_info', $debug );
		
		$root_password = ( isset( $response['server']['password'] ) ) ? $response['server']['password'] : false;
		
		if ( $root_password ) {
			
			$to = get_option('admin_email');
			$subject = 'New Cloud Server - Login Details';
			$body  = __( "Dear Admin", "wp-cloud-server" ) . ",\n\n";
			$body .= __( "Your new server is ready to go. The login details are;", "wp-cloud-server" ) . "\n\n";
			$body .= __( "Username: root", "wp-cloud-server" ) . "\n";
			$body .= __( "Password: ", "wp-cloud-server" ) . ' ' . $root_password . "\n\n";
			$body .= __( "Thank you.", "wp-cloud-server" ) . "\r\n";			
			wp_mail( $to, $subject, $body );
			
		}

		// Delete the Server API Data to Force update
		$api_data = get_option( 'wpcs_upcloud_api_data' );
		if ( isset( $api_data['server'] ) ) {
			unset( $api_data['server'] );
			update_option( 'wpcs_upcloud_api_data', $api_data );
		}		

		// Log the creation of the new DigitalOcean Droplet
		call_user_func("wpcs_{$server_module}_log_event", $server_cloud_provider, 'Success', 'New Server Created ('. $server_name .')' );
			
		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_upcloud_server_type' );
		delete_option( 'wpcs_upcloud_server_name' );	
		delete_option( 'wpcs_upcloud_server_region' );
		delete_option( 'wpcs_upcloud_server_size' );
		delete_option( 'wpcs_upcloud_server_ssh_key' );
		delete_option( 'wpcs_upcloud_server_startup_script_name' );
		delete_option( 'wpcs_upcloud_server_enable_backups' );

		delete_option( 'wpcs_upcloud_server_variable_domain_name' );
		delete_option( 'wpcs_upcloud_server_variable_wp_site_title' );
		delete_option( 'wpcs_upcloud_server_variable_wp_db_user' );
		delete_option( 'wpcs_upcloud_server_variable_wp_database' );
		delete_option( 'wpcs_upcloud_server_variable_admin_user' );
		delete_option( 'wpcs_upcloud_server_variable_admin_passwd' );
		delete_option( 'wpcs_upcloud_server_variable_admin_email' );
}
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_upcloud_create_server_template', 10, 3 );