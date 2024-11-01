<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_linode_server_action', 'wpcs_handle_linode_server_actions' );
add_action( 'admin_post_handle_linode_server_action', 'wpcs_handle_linode_server_actions' );

function wpcs_handle_linode_server_actions() {
	
	// Read in the Server Action
	if ( isset( $_POST['wpcs_linode_server_action'] ) ) {
		$action = $_POST['wpcs_linode_server_action'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_linode_server_id'] ) ) {
		$server_id = $_POST['wpcs_linode_server_id'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_linode_server_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_linode_server_action_nonce'];
	}
	
	// Send the linode API Command
	if ( isset( $action ) && isset( $server_id ) && wp_verify_nonce( $nonce, 'handle_linode_server_action_nonce')) {
		$response[] = wpcs_linode_cloud_server_action( $action, $server_id, false  );
		update_option( 'wpcs_linode_server_action_response', $response );
		
		$feedback = get_option( 'wpcs_setting_errors', array());
	
		$feedback[] = array(
        	'setting' => 'wpcs_linode_server_action',
        	'code'    => 'settings_updated',
        	'message' => 'The linode Server was Successfully Updated',
        	'type'    => 'success',
			'status'  => 'new',
   	 		);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-linode'  ); exit;
}