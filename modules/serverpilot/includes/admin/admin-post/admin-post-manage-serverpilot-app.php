<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_serverpilot_app_action', 'wpcs_handle_serverpilot_app_actions' );
add_action( 'admin_post_handle_serverpilot_app_action', 'wpcs_handle_serverpilot_app_actions' );

function wpcs_handle_serverpilot_app_actions() {
	
	// Read in the App Action
	if ( isset( $_POST['wpcs_serverpilot_app_action'] ) ) {
		$action = $_POST['wpcs_serverpilot_app_action'];
	}

	// Read in the App Id
	if ( isset( $_POST['wpcs_serverpilot_app_id'] ) ) {
		$app_id = $_POST['wpcs_serverpilot_app_id'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_serverpilot_app_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_serverpilot_app_action_nonce'];
	}
	
	// Send the ServerPilot API Command
	if ( isset( $action ) && isset( $app_id ) && wp_verify_nonce( $nonce, 'handle_serverpilot_app_action_nonce')) {
		$response[] = wpcs_serverpilot_cloud_app_action( $action, $app_id, false  );
		update_option( 'wpcs_serverpilot_app_action_response', $response );
		
		$feedback = get_option( 'wpcs_setting_errors', array());
	
		$feedback[] = array(
        	'setting' => 'wpcs_serverpilot_app_action',
        	'code'    => 'settings_updated',
        	'message' => 'The ServerPilot App was Successfully Updated',
        	'type'    => 'success',
			'status'  => 'new',
   	 		);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-serverpilot'  ); exit;
}