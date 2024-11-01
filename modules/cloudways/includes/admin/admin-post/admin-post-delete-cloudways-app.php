<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_delete_cloudways_app', 'wpcs_handle_delete_cloudways_app' );
add_action( 'admin_post_handle_delete_cloudways_app', 'wpcs_handle_delete_cloudways_app' );

function wpcs_handle_delete_cloudways_app() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_app_delete'] ) ) {
		$wpcs_confirm_cloudways_app_delete = $_POST['wpcs_cloudways_confirm_app_delete'];
		update_option( 'wpcs_cloudways_confirm_app_delete', $wpcs_confirm_cloudways_app_delete );
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_app_id'] ) ) {
		$wpcs_confirm_cloudways_app_id = $_POST['wpcs_cloudways_confirm_app_id'];
		update_option( 'wpcs_cloudways_confirm_app_id', $wpcs_confirm_cloudways_app_id );
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_app_server_id'] ) ) {
		$wpcs_cloudways_confirm_app_server_id = $_POST['wpcs_cloudways_confirm_app_server_id'];
		update_option( 'wpcs_cloudways_confirm_app_server_id', $wpcs_cloudways_confirm_app_server_id );
	}
	
	if ( 'true' == $wpcs_confirm_cloudways_app_delete ) {
		$response[] = wpcs_cloudways_api_delete_application( $wpcs_cloudways_confirm_app_server_id, $wpcs_confirm_cloudways_app_id, true );
		update_option( 'wpcs_cloudways_confirm_app_response', $response );
	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_cloudways_confirm_app_delete',
        'code'    => 'settings_updated',
        'message' => 'The Cloudways Application was Successfully Deleted',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );

	// Delete the saved settings ready for next new server
	//delete_option( 'wpcs_cloudways_confirm_app_delete');
	//delete_option( 'wpcs_cloudways_confirm_app_id');
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-managed-hosting'  ); exit;
}