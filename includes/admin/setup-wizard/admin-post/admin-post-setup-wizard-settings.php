<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_setup_wizard_settings_action', 'handle_setup_wizard_settings_action' );
add_action( 'admin_post_handle_setup_wizard_settings_action', 'wpcs_handle_setup_wizard_settings_action' );

function wpcs_handle_setup_wizard_settings_action() {
	
	global $custom_notices;
	
	// Save the DigitalOcean API Key
	if ( isset( $_POST['wpcs_setup_digitalocean_api_key'] ) ) {
		$digitalocean_api_key = $_POST['wpcs_setup_digitalocean_api_key'];
	}

	// Save the Vultr API Key
	if ( isset( $_POST['wpcs_setup_vultr_api_key'] ) ) {
		$vultr_api_key = $_POST['wpcs_setup_vultr_api_key'];
	}

	// Save the Linode API Key
	if ( isset( $_POST['wpcs_setup_linode_api_key'] ) ) {
		$linode_api_key = $_POST['wpcs_setup_linode_api_key'];
	}

	// Save the UpCloud Username
	if ( isset( $_POST['wpcs_setup_upcloud_username'] ) ) {
		$upcloud_username = $_POST['wpcs_setup_upcloud_username'];
	}

	// Save the UpCloud Password
	if ( isset( $_POST['wpcs_setup_upcloud_password'] ) ) {
		$upcloud_password = $_POST['wpcs_setup_upcloud_password'];
	}

	// Save the Amazon Lightsail Access Key
	if ( isset( $_POST['wpcs_setup_aws_lightsail_access_key'] ) ) {
		$aws_lightsail_access_key = $_POST['wpcs_setup_aws_lightsail_access_key'];
	}

	// Save the Amazon Lightsail Secret Key
	if ( isset( $_POST['wpcs_setup_aws_lightsail_secret_key'] ) ) {
		$aws_lightsail_secret_key = $_POST['wpcs_setup_aws_lightsail_secret_key'];
	}

	// Save the ServerPilot Client ID
	if ( isset( $_POST['wpcs_setup_serverpilot_client_id'] ) ) {
		$serverpilot_client_id = $_POST['wpcs_setup_serverpilot_client_id'];
	}

	// Save the ServerPilot API Key
	if ( isset( $_POST['wpcs_setup_serverpilot_api_key'] ) ) {
		$serverpilot_api_key = $_POST['wpcs_setup_serverpilot_api_key'];
	}

	// Save the RunCloud API Key
	if ( isset( $_POST['wpcs_setup_runcloud_api_key'] ) ) {
		$runcloud_api_key = $_POST['wpcs_setup_runcloud_api_key'];
	}

	// Save the RunCloud API Secret
	if ( isset( $_POST['wpcs_setup_runcloud_api_secret'] ) ) {
		$runcloud_api_secret = $_POST['wpcs_setup_runcloud_api_secret'];
	}

	// Save the Ploi API Key
	if ( isset( $_POST['wpcs_setup_ploi_api_key'] ) ) {
		$ploi_api_key = $_POST['wpcs_setup_ploi_api_key'];
	}

	// Save the Cloudways Email
	if ( isset( $_POST['wpcs_setup_cloudways_email'] ) ) {
		$cloudways_email = $_POST['wpcs_setup_cloudways_email'];
	}

	// Save the Cloudways API Key
	if ( isset( $_POST['wpcs_setup_cloudways_api_key'] ) ) {
		$cloudways_api_key = $_POST['wpcs_setup_cloudways_api_key'];
	}

	// Save the SSH Key Name
	if ( isset( $_POST['wpcs_setup_serverpilot_ssh_key_name'] ) ) {
		$serverpilot_ssh_key_name = $_POST['wpcs_setup_serverpilot_ssh_key_name'];
	}

	// Save the SSH Key
	if ( isset( $_POST['wpcs_setup_serverpilot_ssh_key'] ) ) {
		$serverpilot_ssh_key = $_POST['wpcs_setup_serverpilot_ssh_key'];
	}

	// Save Setup Wizard Complete Comnfirmation
	if ( isset( $_POST['wpcs_setup_wizard_complete'] ) ) {
		$setup_wizard_complete = $_POST['wpcs_setup_wizard_complete'];
	}

	// Save Setup Wizard Complete Comnfirmation
	if ( isset( $_POST['wpcs_setup_wizard_complete'] ) ) {
		$setup_wizard_complete = $_POST['wpcs_setup_wizard_complete'];
	}

	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_setup_wizard_settings_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_setup_wizard_settings_action_nonce'];
	}

	update_option( 'wpcs_setup_wizard_complete', $setup_wizard_complete );

	$setup_wizard_complete_confirmed = get_option( 'wpcs_setup_wizard_complete_confirmed', 'false' );
	
	if ( ( 'true' == $setup_wizard_complete ) && ( current_user_can( 'manage_options' ) ) && ( 'false' == $setup_wizard_complete_confirmed ) ) {

		$modules = get_option( 'wpcs_module_list' );

		if ( '' !== $digitalocean_api_key ) {
			update_option( 'wpcs_digitalocean_api_token', $digitalocean_api_key );
			$modules['DigitalOcean']['api_connected'] = '1';
			$modules['DigitalOcean']['status'] = 'active';
		}

		if ( '' !== $vultr_api_key ) {
			update_option( 'wpcs_vultr_api_token', $vultr_api_key );
			$modules['Vultr']['api_connected'] = '1';
			$modules['Vultr']['status'] = 'active';
		}

		if ( '' !== $linode_api_key ) {
			update_option( 'wpcs_linode_api_token', $linode_api_key );
			$modules['Linode']['api_connected'] = '1';
			$modules['Linode']['status'] = 'active';
		}

		if ( ( '' !== $upcloud_username ) && ( '' !== $upcloud_password ) ) {
			update_option( 'wpcs_upcloud_user_name', $upcloud_username );
			update_option( 'wpcs_upcloud_password', $upcloud_password );
			$modules['UpCloud']['api_connected'] = '1';
			$modules['UpCloud']['status'] = 'active';
		}

		if ( ( '' !== $aws_lightsail_access_key ) && ( '' !== $aws_lightsail_secret_key ) ) {
			update_option( 'wpcs_aws_lightsail_api_token', $aws_lightsail_access_key );
			update_option( 'wpcs_aws_lightsail_api_secret_key', $aws_lightsail_secret_key );
			$modules['AWS Lightsail']['api_connected'] = '1';
			$modules['AWS Lightsail']['status'] = 'active';
		}
		
		if ( ( '' !== $serverpilot_api_key ) && ( '' !== $serverpilot_client_id ) ) {
			update_option( 'wpcs_sp_api_account_id', $serverpilot_client_id );
			update_option( 'wpcs_sp_api_key', $serverpilot_api_key );
			$modules['ServerPilot']['api_connected'] = '1';
			$modules['ServerPilot']['status'] = 'active';
		}

		if ( ( '' !== $runcloud_api_key ) && ( '' !== $runcloud_api_secret ) ) {
			update_option( 'wpcs_runcloud_api_key', $runcloud_api_key );
			update_option( 'wpcs_runcloud_api_secret', $runcloud_api_secret );
			$modules['RunCloud']['api_connected'] = '1';
			$modules['RunCloud']['status'] = 'active';
		}

		if ( ( '' !== $ploi_api_key ) ) {
			update_option( 'wpcs_ploi_api_key', $ploi_api_key );
			$modules['Ploi']['api_connected'] = '1';
			$modules['Ploi']['status'] = 'active';
		}

		if ( ( '' !== $cloudways_email ) && ( '' !== $cloudways_api_key ) ) {
			update_option( 'wpcs_cloudways_email', $cloudways_email );
			update_option( 'wpcs_cloudways_api_key', $cloudways_api_key );
			$modules['Cloudways']['api_connected'] = '1';
			$modules['Cloudways']['status'] = 'active';
		}
		
		if ( ( '' !== $serverpilot_ssh_key_name ) && ( '' !== $serverpilot_ssh_key ) ) {
			
			// Set-up the data for sending the SSH Key to DigitalOcean
			$ssh_key_data = array(
				"name"			=>  $serverpilot_ssh_key_name,
				"public_key"	=>  $serverpilot_ssh_key, 
			);
			
			// Retrieve the Active Module List
			$ssh_keys		= get_option( 'wpcs_serverpilots_ssh_keys' );
		
			$content		= explode(' ', $ssh_key_data['public_key'], 3);
			$fingerprint	= join(':', str_split(md5(base64_decode($content[1])), 2)) . "\n\n";
		
			$ssh_key_data['fingerprint'] = $fingerprint;
		
			// Save the VPS Template for use with a Plan
			$ssh_keys[ $ssh_key_data['name'] ] = $ssh_key_data;
		
			update_option( 'wpcs_serverpilots_ssh_keys', $ssh_keys );
		
		}
		update_option( 'wpcs_module_list', $modules );
		update_option( 'wpcs_setup_wizard_complete_confirmed', 'true' );

				// Update the Module Config Settings
				do_action( 'wpcs_update_module_config', $modules, '', '' );
		
				// Update the Main Config Settings
				do_action( 'wpcs_update_config', $modules, '', '' );
	}
	
	$feedback[] = array(
        'setting' => 'wpcs_serverpilot_template_name',
        'code'    => 'settings_updated',
        'message' => 'The New Module API Settings were Successfully Saved',
        'type'    => 'success',
		'status'  => 'new',
    );

	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-admin-menu'  ); exit;
}