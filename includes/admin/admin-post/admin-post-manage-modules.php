<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_serverpilot_module_action', 'wpcs_handle_serverpilot_module_action' );
add_action( 'admin_post_handle_serverpilot_module_action', 'wpcs_handle_serverpilot_module_action' );

function wpcs_handle_serverpilot_module_action() {
	
	// Read in the App Action
	if ( isset( $_POST['wpcs_serverpilot_module_action'] ) ) {
		$action = $_POST['wpcs_serverpilot_module_action'];
	}

	// Read in the App Id
	if ( isset( $_POST['wpcs_serverpilot_module_id'] ) ) {
		$module_id = $_POST['wpcs_serverpilot_module_id'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_serverpilot_module_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_serverpilot_module_action_nonce'];
	}
	
	// Send the ServerPilot API Command
	if ( isset( $action ) && isset( $module_id ) && wp_verify_nonce( $nonce, 'handle_serverpilot_module_action_nonce')) {
		$response['action'] = $action;
		$response['module'] = $module_id;
		update_option( 'wpcs_module_action_response', $response );
		
		do_action( 'wpcs_update_module_status', $module_id, $action );
		
		$feedback = get_option( 'wpcs_setting_errors', array());
	
		$feedback[] = array(
        	'setting' => 'wpcs_serverpilot_module_action',
        	'code'    => 'settings_updated',
        	'message' => 'The Modules status was Successfully Updated',
        	'type'    => 'success',
			'status'  => 'new',
   	 		);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-admin-menu'  ); exit;
}