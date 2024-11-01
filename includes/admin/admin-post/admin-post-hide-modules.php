<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_hide_module_action', 'wpcs_handle_hide_module_action' );
add_action( 'admin_post_handle_hide_module_action', 'wpcs_handle_hide_module_action' );

function wpcs_handle_hide_module_action() {
	
	// Read in the App Action
	if ( isset( $_POST['wpcs_hide_module_action'] ) ) {
		$action = $_POST['wpcs_hide_module_action'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_hide_module_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_hide_module_action_nonce'];
	}
	
	// Send the ServerPilot API Command
	if ( isset( $action ) && wp_verify_nonce( $nonce, 'handle_hide_module_action_nonce')) {
		$response = ( 'true' == $action ) ? true : false;
		update_option( 'wpcs_hide_inactive_modules', $response );
		
		$feedback = get_option( 'wpcs_setting_errors', array());
		
		$message = ( $response ) ? 'Your Inactive Modules have been Hidden' : 'Your Inactive Modules are Visible';
	
		$feedback[] = array(
        	'setting' => 'wpcs_hide_module_action',
        	'code'    => 'settings_updated',
        	'message' => $message,
        	'type'    => 'success',
			'status'  => 'new',
   	 		);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-admin-menu'  ); exit;
}