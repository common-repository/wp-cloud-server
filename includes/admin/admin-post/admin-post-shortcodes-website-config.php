<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_post_nopriv_handle_shortcode_website_config', 'wpcs_handle_shortcodes_website_config' );
add_action( 'admin_post_handle_shortcode_website_config', 'wpcs_handle_shortcodes_website_config' );

function wpcs_handle_shortcodes_website_config() {
	
	global $custom_notices;
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_plan_name'] ) ) {
		$plan_name = $_POST['wpcs_website_shortcode_plan_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_domain_name'] ) ) {
		$domain_name = $_POST['wpcs_website_shortcode_domain_name'];
	}
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_ip_address'] ) ) {
		$ip_address = $_POST['wpcs_website_shortcode_ip_address'];
	}	
	
	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_php_version'] ) ) {
		$php_version = $_POST['wpcs_website_shortcode_php_version'];
	}

	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_nonce'] ) ) {
		$nonce = $_POST['wpcs_website_shortcode_nonce'];
	}

	// Save the ServerPilot Template Site Monitor
	if ( isset( $_POST['wpcs_website_shortcode_action'] ) ) {
		$action = $_POST['wpcs_website_shortcode_action'];
	}

	if ( $action == "update") {
	
		$data['contents']	= array(
		
			'plan_name'		=> isset( $plan_name ) ? $plan_name : false,
			'domain_name'	=> isset( $domain_name ) ? $domain_name : false,
			'ip_address'	=> isset( $ip_address ) ? $ip_address : false,
			'php_version'	=> isset( $php_version ) ? $php_version : false,
				
		);
		
		$data['fields']		= array(
				
			'plan_name'		=> 'Plan Name', 
			'domain_name'	=> 'Domain Name',
			'ip_address'	=> 'IP Address',		
			'php_version'	=> 'PHP Version',
				
		);
			
		update_option( 'wpcs_website_shortcodes_enabled_data', $data );
		
		$feedback[] = array(
        	'setting' => 'wpcs_website_shortcode_plan_name',
        	'code'    => 'settings_updated',
        	'message' => 'The New Website Shortcode Settings have been Saved',
        	'type'    => 'success',
			'status'  => 'new',
    	);
	
		// Update the feedback array
		update_option( 'wpcs_setting_errors', $feedback );

	}
	
	$url = admin_url();
	wp_redirect( $url . 'admin.php?page=wp-cloud-server-general-settings'  ); exit;
}