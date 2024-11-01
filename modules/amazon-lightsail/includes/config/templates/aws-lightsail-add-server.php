<?php

/**
 * Provide a Admin Area Add Server Page for the Linode Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

function wpcs_aws_lightsail_add_server_template (  $tabs_content, $page_content, $page_id  ) {
	
	if ( 'aws-lightsail-add-server' !== $tabs_content ) {
		return;
	}

	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

	$module_name =  WP_Cloud_Server_AWS_Lightsail_Settings::wpcs_aws_lightsail_module_name();

	$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
	$api_status		= wpcs_check_cloud_provider_api('AWS Lightsail');
	$attributes		= ( $api_status ) ? '' : 'disabled';
	$sp_response	= '';
	$server_script	= '';
	$server_info	= '';
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_aws_lightsail_create_server' );
			wpcs_do_settings_sections( 'wpcs_aws_lightsail_create_server' );
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	$debug_data = array(
		"name"				=>	get_option( 'wpcs_aws_lightsail_server_name' ),
		"region"			=>	get_option( 'wpcs_aws_lightsail_server_region' ),
		"size"				=>	get_option( 'wpcs_aws_lightsail_server_size' ),
		"image"				=> 	get_option( 'wpcs_aws_lightsail_server_type' ),
		"ssh_key"			=> 	get_option( 'wpcs_aws_lightsail_server_ssh_key' ),
		"script_name"		=> 	get_option( 'wpcs_aws_lightsail_server_startup_script_name' ),	
		"backups"			=> 	get_option( 'wpcs_aws_lightsail_server_enable_backups' ),
	);

	if ( get_option( 'wpcs_aws_lightsail_server_name' ) ) {

		$server_type		= get_option( 'wpcs_aws_lightsail_server_type' );
		$server_name		= get_option( 'wpcs_aws_lightsail_server_name' );	
		$server_region		= get_option( 'wpcs_aws_lightsail_server_region' );
		$server_size		= get_option( 'wpcs_aws_lightsail_server_size' );
		$server_ssh_key		= get_option( 'wpcs_aws_lightsail_server_ssh_key' );
		$server_script_name	= get_option( 'wpcs_aws_lightsail_server_startup_script_name' );
		$server_backups		= get_option( 'wpcs_aws_lightsail_server_enable_backups' );

		$domain_name	    = get_option( 'wpcs_aws_lightsail_server_variable_domain_name','' );
		$wp_site_title		= get_option( 'wpcs_aws_lightsail_server_variable_wp_site_title','' );
		$wp_db_user			= get_option( 'wpcs_aws_lightsail_server_variable_wp_db_user','' );
		$wp_database		= get_option( 'wpcs_aws_lightsail_server_variable_wp_database','' );
		$admin_user			= get_option( 'wpcs_aws_lightsail_server_variable_admin_user','' );
		$admin_passwd		= get_option( 'wpcs_aws_lightsail_server_variable_admin_passwd','' );
		$admin_email		= get_option( 'wpcs_aws_lightsail_server_variable_admin_email','' );
		
		$server_pwd		= wp_generate_password( 12, false );
					
		// Set-up the data for the new AWS Lightsail Server
		$app_data = array(
			"instanceNames"		=>	array( $server_name ),
			"bundleId"			=>	$server_size,
			"blueprintId"		=> 	$server_type,
		);
		
		// Use User Meta if provided in template
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
		
				$app_data["userData"] 	= $script;
				$debug['server_script']	= $script;
		
				update_option( 'wpcs_updated_script', $script );

			}
		}

		// Check if SSH Key saved with provider
		$ssh_key_id	= call_user_func("wpcs_aws_lightsail_ssh_key", $server_ssh_key, $server_region );

		if ( !empty( $ssh_key_id ) ) {
			$app_data["keyPairName"] = $ssh_key_id;	
		}

		// Check for Automatic Snapshots
		if ( $server_backups ) {
			$app_data["addOns"][]= array(
				"addOnType" => 'AutoSnapshot',
			);
		}

		// Send the API POST request to create the new 'server'
		$response = wpcs_aws_lightsail_call_api_create_server( $app_data, true, $server_region );
		
		update_option( 'aws_lightsail_create_server_api_response', $response );
			
		// Set-up Hosting Type (for future use)
		$app_data['hosting_type'] = 'In-House';

		// Retrieve the Active Module List
		$module_data = get_option( 'wpcs_module_list' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'AWS Lightsail' ][ 'servers' ][] = $app_data;

		// Update the Active Module List
		update_option( 'wpcs_module_list', $module_data );

		// Log the creation of the new DigitalOcean Droplet
		wpcs_aws_lightsail_log_event( 'AWS Lightsail', 'Success', 'New Server Created (' . $server_name . ')' );

		// Delete the Server API Data to Force update
		$api_data = get_option( 'wpcs_aws_lightsail_api_data' );
		if ( isset( $api_data['instances'] ) ) {
			unset( $api_data['instances'] );
			update_option( 'wpcs_aws_lightsail_api_data', $api_data );
		}		
	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_aws_lightsail_server_type' );
	delete_option( 'wpcs_aws_lightsail_server_name' );	
	delete_option( 'wpcs_aws_lightsail_server_region' );
	delete_option( 'wpcs_aws_lightsail_server_size' );
	delete_option( 'wpcs_aws_lightsail_server_ssh_key' );
	delete_option( 'wpcs_aws_lightsail_server_startup_script_name' );
	delete_option( 'wpcs_aws_lightsail_server_enable_backups' );

	delete_option( 'wpcs_aws_lightsail_server_variable_domain_name' );
	delete_option( 'wpcs_aws_lightsail_server_variable_wp_site_title' );
	delete_option( 'wpcs_aws_lightsail_server_variable_wp_db_user' );
	delete_option( 'wpcs_aws_lightsail_server_variable_wp_database' );
	delete_option( 'wpcs_aws_lightsail_server_variable_admin_user' );
	delete_option( 'wpcs_aws_lightsail_server_variable_admin_passwd' );
	delete_option( 'wpcs_aws_lightsail_server_variable_admin_email' );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_aws_lightsail_add_server_template', 10, 3 );