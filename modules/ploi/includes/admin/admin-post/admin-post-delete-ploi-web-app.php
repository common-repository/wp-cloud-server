<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_delete_ploi_web_apps', 'wpcs_handle_delete_ploi_web_apps' );
add_action( 'admin_post_handle_delete_ploi_web_apps', 'wpcs_handle_delete_ploi_web_apps' );

function wpcs_handle_delete_ploi_web_apps() {
	
	// Confirm deletion of Ploi Web App
	if ( isset( $_POST['wpcs_ploi_confirm_web_apps_delete'] ) ) {
		$wpcs_confirm_ploi_web_apps_delete = $_POST['wpcs_ploi_confirm_web_apps_delete'];
		update_option( 'wpcs_ploi_confirm_web_apps_delete', $wpcs_confirm_ploi_web_apps_delete );
	}
	
	// Save the Ploi Web Apps Id
	if ( isset( $_POST['wpcs_ploi_confirm_web_apps_id'] ) ) {
		$wpcs_confirm_ploi_web_apps_id = $_POST['wpcs_ploi_confirm_web_apps_id'];
		update_option( 'wpcs_ploi_confirm_web_apps_id', $wpcs_confirm_ploi_web_apps_id );
	}

	// Save the Ploi Web Apps Server Id
	if ( isset( $_POST['wpcs_ploi_confirm_web_apps_server_id'] ) ) {
		$wpcs_ploi_confirm_web_apps_server_id = $_POST['wpcs_ploi_confirm_web_apps_server_id'];
		update_option( 'wpcs_ploi_confirm_web_apps_server_id', $wpcs_ploi_confirm_web_apps_server_id );
	}
	
	if ( 'true' == $wpcs_confirm_ploi_web_apps_delete ) {
		$response[] = wpcs_ploi_delete_site_application( $wpcs_ploi_confirm_web_apps_server_id, $wpcs_confirm_ploi_web_apps_id, true );
		update_option( 'wpcs_ploi_confirm_web_apps_response', $response );

		// Delete the Droplet API Data to Force update
		$data = get_option( 'wpcs_ploi_api_data' );
		if ( isset( $data['sites/list'] ) ) {
			unset( $data['sites/list'] );
			update_option( 'wpcs_ploi_api_data', $data );
		}
	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_ploi_confirm_web_apps_delete',
        'code'    => 'settings_updated',
        'message' => 'The Ploi Server was Successfully Deleted',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-ploi'  ); exit;
}