<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_aws_lightsail_server_action', 'wpcs_handle_aws_lightsail_server_actions' );
add_action( 'admin_post_handle_aws_lightsail_server_action', 'wpcs_handle_aws_lightsail_server_actions' );

function wpcs_handle_aws_lightsail_server_actions() {
	
	// Read in the Server Action
	if ( isset( $_POST['wpcs_aws_lightsail_server_action'] ) ) {
		$action = $_POST['wpcs_aws_lightsail_server_action'];
	}

	// Read in the Server Id
	if ( isset( $_POST['wpcs_aws_lightsail_server_id'] ) ) {
		$server_id = $_POST['wpcs_aws_lightsail_server_id'];
	}
	
	// Read in the Server Location
	if ( isset( $_POST['wpcs_aws_lightsail_server_region'] ) ) {
		$server_region = $_POST['wpcs_aws_lightsail_server_region'];
	}
	
	// Read in the Nonce
	if ( isset( $_POST['wpcs_handle_aws_lightsail_server_action_nonce'] ) ) {
		$nonce = $_POST['wpcs_handle_aws_lightsail_server_action_nonce'];
	}
	
	// Send the aws_lightsail API Command
	if ( isset( $action ) && isset( $server_id ) && wp_verify_nonce( $nonce, 'handle_aws_lightsail_server_action_nonce')) {
		$response[] = wpcs_aws_lightsail_cloud_server_action( $action, $server_id, $server_region, false  );
		update_option( 'wpcs_aws_lightsail_server_action_response', $response );
		
		$feedback = get_option( 'wpcs_setting_errors', array());
	
		$feedback[] = array(
        	'setting' => 'wpcs_aws_lightsail_server_action',
        	'code'    => 'settings_updated',
        	'message' => 'The AWS Lightsail Server was Successfully Updated',
        	'type'    => 'success',
			'status'  => 'new',
   	 		);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );
	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-aws-lightsail'  ); exit;
}