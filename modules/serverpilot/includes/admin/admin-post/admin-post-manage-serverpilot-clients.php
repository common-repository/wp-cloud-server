<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_serverpilot_client_action', 'wpcs_handle_serverpilot_client_actions' );
add_action( 'admin_post_handle_serverpilot_client_action', 'wpcs_handle_serverpilot_client_actions' );

function wpcs_handle_serverpilot_client_actions() {
	
	// Read in the Server Action
	if ( isset( $_POST['wpcs_serverpilot_client_action'] ) ) {
		$action = $_POST['wpcs_serverpilot_client_action'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_serverpilot_client_user_id'] ) ) {
		$user_id = $_POST['wpcs_serverpilot_client_user_id'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_serverpilot_client_host_name'] ) ) {
		$host_name = $_POST['wpcs_serverpilot_client_host_name'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_serverpilot_client_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_serverpilot_client_action_nonce'];
	}
	
	// Delete the Client Info
	if ( isset( $action ) && isset( $user_id ) && isset( $host_name ) && wp_verify_nonce( $nonce, 'handle_serverpilot_client_action_nonce')) {

		$clients_data	= get_option( 'wpcs_cloud_server_client_info', array() );
	
		foreach ( $clients_data['ServerPilot'] as $key => $client ) {
						
			if ( ( $host_name == $client['host_name'] ) && ( $user_id == $client['user_id'] ) ) {

				unset($clients_data['ServerPilot'][$key]);
				update_option( 'wpcs_cloud_server_client_info', $clients_data );
		
				$feedback = get_option( 'wpcs_setting_errors', array());
	
				$feedback[] = array(
        			'setting' => 'wpcs_serverpilot_client_action',
        			'code'    => 'settings_updated',
        			'message' => 'The ServerPilot Client Details were Successfully Updated',
        			'type'    => 'success',
					'status'  => 'new',
   	 			);
	
				// Update the feedback array
				update_option( 'wpcs_setting_errors', $feedback );
			}
		}
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-serverpilot' ); exit;
}