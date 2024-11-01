<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_delete_aws_lightsail_template', 'wpcs_handle_delete_aws_lightsail_template' );
add_action( 'admin_post_handle_delete_aws_lightsail_template', 'wpcs_handle_delete_aws_lightsail_template' );

function wpcs_handle_delete_aws_lightsail_template() {
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_aws_lightsail_confirm_template_delete'] ) ) {
		$wpcs_confirm_aws_lightsail_template_delete = $_POST['wpcs_aws_lightsail_confirm_template_delete'];
	}
	
	// Save the ServerPilot Template Name
	if ( isset( $_POST['wpcs_aws_lightsail_confirm_template_id'] ) ) {
		$wpcs_confirm_aws_lightsail_template_id = $_POST['wpcs_aws_lightsail_confirm_template_id'];
	}
	
	// Delete Template if Confirmed Received
	if ( 'true' == $wpcs_confirm_aws_lightsail_template_delete ) {
		$confirm = wpcs_delete_template( 'AWS Lightsail', $wpcs_confirm_aws_lightsail_template_id );
	}
		
	$feedback = get_option( 'wpcs_setting_errors', array());
	
	$feedback[] = array(
        'setting' => 'wpcs_aws_lightsail_confirm_template_delete',
        'code'    => 'settings_updated',
        'message' => 'The Template was Successfully Deleted',
        'type'    => 'success',
		'status'  => 'new',
    );
	
	// Update the feedback array
	update_option( 'wpcs_setting_errors', $feedback );
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-servers-aws-lightsail'  ); exit;
}