<?php
/**
 * Provide a Admin Area Create Cloud Server Page
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$api_status		= wpcs_check_cloud_provider_api( 'DigitalOcean' );
$attributes		= ( $api_status ) ? '' : 'disabled';
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$sp_response	= '';
$server_script	= null;

?>

<div class="content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpcs_digitalocean_create_server' );
		wpcs_do_settings_sections( 'wpcs_digitalocean_create_server' );
		?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'SSH Key:', 'wp-cloud-server' ); ?></th>
						<td>
							<?php
							$ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
							?>
							<select style="width: 25rem;" name="wpcs_digitalocean_server_ssh_key" id="wpcs_digitalocean_server_ssh_key">
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
							<select style="width: 25rem;" name="wpcs_digitalocean_server_startup_script_name" id="wpcs_digitalocean_server_startup_script_name">
								<option value="no-startup-script"><?php esc_html_e( '-- No Startup Script --', 'wp-cloud-server' ); ?></option>
								<?php
								if ( !empty( $startup_scripts ) ) { ?>
									<optgroup label="User Startup Scripts">
									<?php foreach ( $startup_scripts as $key => $script ) {
										$script_type = ( isset( $script['type'] ) ) ? $script['type'] : '';
										if ( 'git' !== $script_type) {
            								echo "<option value='{$script['name']}'>{$script['name']}</option>";
										}
									}
									foreach ( $startup_scripts as $key => $script ) {
										$script_type = ( isset( $script['type'] ) ) ? $script['type'] : '';
										if ( 'git' == $script_type) {
            								echo "<option value='{$script['name']}'>{$script['name']}</option>";
										}
									}
									?>
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
							<input type='checkbox' id='wpcs_digitalocean_server_enable_backups' name='wpcs_digitalocean_server_enable_backups' value='1'>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
			//wpcs_create_server_script_variable_settings( 'digitalocean' );
			?>
			<hr>
			<?php
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_digitalocean_server_name' ) ) {
		
		global $wp_settings_errors;
		
		$server_cloud_provider	       		= 'DigitalOcean';

		// Capture the DigitalOcean Settings
		$server_type	        			= get_option( 'wpcs_digitalocean_server_type' );
		$server_name	       				= get_option( 'wpcs_digitalocean_server_name' );
		$server_region	        			= get_option( 'wpcs_digitalocean_server_region' );
		$server_size	        			= get_option( 'wpcs_digitalocean_server_size' );
		$server_ssh_key_name	        	= get_option( 'wpcs_digitalocean_server_ssh_key' );
		$server_startup_script_name	        = get_option( 'wpcs_digitalocean_server_startup_script_name' );
		$server_backups	        			= get_option( 'wpcs_digitalocean_server_enable_backups' );

		$domain_name	        			= get_option( 'wpcs_digitalocean_server_variable_domain_name','' );
		$wp_site_title	        			= get_option( 'wpcs_digitalocean_server_variable_wp_site_title','' );
		$wp_db_user	        				= get_option( 'wpcs_digitalocean_server_variable_wp_db_user','' );
		$wp_database	        			= get_option( 'wpcs_digitalocean_server_variable_wp_database','' );
		$admin_user	        				= get_option( 'wpcs_digitalocean_server_variable_admin_user','' );
		$admin_passwd	        			= get_option( 'wpcs_digitalocean_server_variable_admin_passwd','' );
		$admin_email	        			= get_option( 'wpcs_digitalocean_server_variable_admin_email','' );
		
		// Extract the Setting Values
		$server_size_explode				= explode( '|', $server_size );
		$server_size_name					= $server_size_explode[0];
		$server_size						= $server_size_explode[1];
		
		$debug['server_size']				= $server_size;
		
		$server_region_explode				= explode( '|', $server_region );
		$server_region_name					= $server_region_explode[0];
		$server_region						= $server_region_explode[1];
		
		$debug['server_region']				= $server_size;

		$server_type_explode				= explode( '|', $server_type );
		$server_type_name					= $server_type_explode[0];
		$server_type						= $server_type_explode[1];
		
		$debug['server_type']				= $server_type;
		$debug['server_type_name']			= $server_type_name;
		
		$server_enable_backups				= ( $server_backups ) ? true : false;
		
		$server_module						= strtolower( str_replace( " ", "_", $server_cloud_provider ) );
		
		// Need to Retrieve the Server Image
		$server_image						= call_user_func("wpcs_{$server_module}_os_list", $server_type_name );
		
		$debug['server_image']				= $server_image;

		// Create Server Data Array
		$server = array(
			"name"			=>	$server_name,
			"slug"			=>	sanitize_title( $server_name ),
			"region"		=>	$server_region_name,
			"size"			=>	$server_size_name,
			"image"			=> 	$server_image,
			"module"		=>	$server_cloud_provider,
			"ssh_key"		=>	$server_ssh_key_name,
			"backups"		=>	$server_enable_backups,
			"user_data"		=>	$server_startup_script_name,
			"hosting_type"	=>	'dedicated',
		);
		
		// Set-up the API Data for the New Server
		$api_data = array(
			"region"		=>	$server_region,
			"size"			=>	$server_size,
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
			"image"			=> 	$server_image,
			"backups"		=>	$server_enable_backups,
		);

		// Check if SSH Key saved with provider
		$ssh_key_id	= call_user_func("wpcs_{$server_module}_ssh_key", $server_ssh_key_name, $server_region );
		
		$debug['ssh_key_id']			= $ssh_key_id;
		$debug['server_ssh_key_name']	= $server_ssh_key_name;
		
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
		
		// Use SSH Key if available or generate root password
		if ( $ssh_key_id ) {
			$app_data["ssh_keys"][]						= $ssh_key_id;
			$app_data['custom_settings']['SSHKEYID']	= $ssh_key_id;
			$app_data['custom_settings']['ssh_key']		= $ssh_key_id;
			
			// Linode requires a root password even if an SSH Key is added
			if ( 'Linode' == $server_cloud_provider ) {
				$root_password								= wp_generate_password( 20, false, false );
				$app_data['custom_settings']['root_pass']	= $root_password;
			}
		} else {
			$root_password								= wp_generate_password( 20, false, false );
			// This needs testing for DO only
			$app_data['custom_settings']['root_pass']	= $root_password;
		}
		
		if ( isset( $root_password ) ) {
			
			$to = get_option('admin_email');
			$subject = 'New Cloud Server - Login Details';
			$body  = __( "Dear Admin", "wp-cloud-server" ) . ",\n\n";
			$body .= __( "Your new server is ready to go. The login details are;", "wp-cloud-server" ) . "\n\n";
			$body .= __( "Username: root", "wp-cloud-server" ) . "\n";
			$body .= __( "Password: ", "wp-cloud-server" ) . ' ' . $root_password . "\n\n";
			$body .= __( "Thank you.", "wp-cloud-server" ) . "\r\n";			
			wp_mail( $to, $subject, $body );
			
		}
						
		// Read in any Custom Settings from Template
		if ( isset( $server_size ) ) {
			$app_data['custom_settings']['OSID']		= $server_image;
			$app_data['custom_settings']['DCID']		= $server_region;
			$app_data['custom_settings']['VPSPLANID']	= $server_size;			
			$app_data['custom_settings']['label']		= $server['name'];
			$app_data['custom_settings']['hostname']	= $server['name'];
		}
		
		$debug['app_data'] = $app_data;

		// Send the API POST request to create the new server
		$response =  call_user_func("wpcs_{$server_module}_cloud_server_api", null, 'droplets', $app_data, false, 0, 'POST', false, 'site_creation' );
		
		$debug['droplet_response']	= $response;
		
		update_option( 'wpcs_droplet_info', $debug );

		// Delete the Droplet API Data to Force update
		$api_data = get_option( 'wpcs_digitalocean_api_data' );
		if ( isset( $api_data['droplets'] ) ) {
			unset( $api_data['droplets'] );
			update_option( 'wpcs_digitalocean_api_data', $api_data );
		}

		// Log the creation of the new DigitalOcean Droplet
		call_user_func("wpcs_{$server_module}_log_event", $server_cloud_provider, 'Success', 'New Server Created ('. $server_name .')' );
			
		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_digitalocean_server_cloud_provider' );
		delete_option( 'wpcs_digitalocean_server_type' );
		delete_option( 'wpcs_digitalocean_server_name' );	
		delete_option( 'wpcs_digitalocean_server_region' );
		delete_option( 'wpcs_digitalocean_server_size' );
		delete_option( 'wpcs_digitalocean_server_ssh_key' );
		delete_option( 'wpcs_digitalocean_server_enable_backups' );
		delete_option( 'wpcs_digitalocean_server_startup_script_name' );

		delete_option( 'wpcs_digitalocean_server_variable_domain_name' );
		delete_option( 'wpcs_digitalocean_server_variable_wp_site_title' );
		delete_option( 'wpcs_digitalocean_server_variable_wp_db_user' );
		delete_option( 'wpcs_digitalocean_server_variable_wp_database' );
		delete_option( 'wpcs_digitalocean_server_variable_admin_user' );
		delete_option( 'wpcs_digitalocean_server_variable_admin_passwd' );
		delete_option( 'wpcs_digitalocean_server_variable_admin_email' );
}