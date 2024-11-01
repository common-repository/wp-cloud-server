<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_cloudways_client_action', 'wpcs_handle_cloudways_client_actions' );
add_action( 'admin_post_handle_cloudways_client_action', 'wpcs_handle_cloudways_client_actions' );

function wpcs_handle_cloudways_client_actions() {
	
	// Read in the Server Action
	if ( isset( $_POST['wpcs_cloudways_client_action'] ) ) {
		$action = $_POST['wpcs_cloudways_client_action'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_cloudways_client_user_id'] ) ) {
		$user_id = $_POST['wpcs_cloudways_client_user_id'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_cloudways_client_host_name'] ) ) {
		$host_name = $_POST['wpcs_cloudways_client_host_name'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_cloudways_client_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_cloudways_client_action_nonce'];
	}
	
	// Delete the Client Info
	if ( isset( $action ) && isset( $user_id ) && isset( $host_name ) && wp_verify_nonce( $nonce, 'handle_cloudways_client_action_nonce')) {

		$clients_data	= get_option( 'wpcs_cloud_server_client_info', array() );
	
		foreach ( $clients_data['Cloudways'] as $key => $client ) {
						
			if ( ( $host_name == $client['host_name'] ) && ( $user_id == $client['user_id'] ) ) {

				unset($clients_data['Cloudways'][$key]);
				update_option( 'wpcs_cloud_server_client_info', $clients_data );
		
				$feedback = get_option( 'wpcs_setting_errors', array());
	
				$feedback[] = array(
        			'setting' => 'wpcs_cloudways_client_action',
        			'code'    => 'settings_updated',
        			'message' => 'The Cloudways Client Details were Successfully Updated',
        			'type'    => 'success',
					'status'  => 'new',
   	 			);
	
				// Update the feedback array
				update_option( 'wpcs_setting_errors', $feedback );
			}
		}
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-cloudways'  ); exit;
}