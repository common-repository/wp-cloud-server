<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_delete_cloudways_server', 'wpcs_handle_delete_cloudways_server' );
add_action( 'admin_post_handle_delete_cloudways_server', 'wpcs_handle_delete_cloudways_server' );

function wpcs_handle_delete_cloudways_server() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_server_delete'] ) ) {
		$wpcs_confirm_cloudways_server_delete = $_POST['wpcs_cloudways_confirm_server_delete'];
		update_option( 'wpcs_cloudways_confirm_server_delete', $wpcs_confirm_cloudways_server_delete );
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_server_id'] ) ) {
		$wpcs_confirm_cloudways_server_id = $_POST['wpcs_cloudways_confirm_server_id'];
		update_option( 'wpcs_cloudways_confirm_server_id', $wpcs_confirm_cloudways_server_id );
	}
	
	if ( 'true' == $wpcs_confirm_cloudways_server_delete ) {
		$response[] = wpcs_cloudways_api_delete_server( $wpcs_confirm_cloudways_server_id, true );
		update_option( 'wpcs_cloudways_confirm_server_response', $response );
	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_cloudways_confirm_server_delete',
        'code'    => 'settings_updated',
        'message' => 'The Cloudways Server was Successfully Deleted',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );

	// Delete the saved settings ready for next new server
	//delete_option( 'wpcs_cloudways_confirm_server_delete');
	//delete_option( 'wpcs_cloudways_confirm_server_id');
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-managed-hosting'  ); exit;
}