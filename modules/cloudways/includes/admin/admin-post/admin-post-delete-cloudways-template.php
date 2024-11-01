<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_delete_cloudways_template', 'wpcs_handle_delete_cloudways_template' );
add_action( 'admin_post_handle_delete_cloudways_template', 'wpcs_handle_delete_cloudways_template' );

function wpcs_handle_delete_cloudways_template() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_template_delete'] ) ) {
		$wpcs_confirm_cloudways_template_delete = $_POST['wpcs_cloudways_confirm_template_delete'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_cloudways_confirm_template_id'] ) ) {
		$wpcs_confirm_cloudways_template_id = $_POST['wpcs_cloudways_confirm_template_id'];
	}
	
	// Delete Template if Confirmed Received
	if ( 'true' == $wpcs_confirm_cloudways_template_delete ) {
		$confirm = wpcs_delete_template( 'Cloudways', $wpcs_confirm_cloudways_template_id );
	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_cloudways_confirm_template_delete',
        'code'    => 'settings_updated',
        'message' => 'The Template was Successfully Deleted',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-managed-hosting'  ); exit;
}