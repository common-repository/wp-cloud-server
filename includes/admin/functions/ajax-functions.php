<?php

/**
 * Ajax Functions.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
		
/**
 *  Load the JS Scripts for Handling Admin and Module notices
 *
 *  @since  1.0.0
 */		
function wpcs_ajax_load_scripts() {

	// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
	$dashboard_tabs_args = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_settings_dashboard_tabs_nonce' => wp_create_nonce( 'settings_dashboard_ui_tabs_nonce' ),
	);
	
	wp_enqueue_script( 'settings_dashboard-tabs-update', WPCS_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'settings_dashboard-tabs-update', 'wpcs_settings_dashboard_tabs_ajax_script', $dashboard_tabs_args );
	

	// Load the JavaScript for the dashboard & set-up the related Ajax script
	$dashboard_args = array(
		'ajaxurl'	 			=> admin_url( 'admin-ajax.php' ),
		'ajax_dashboard_nonce' 	=> wp_create_nonce( 'dashboard_ui_nonce' ),
	);

	wp_enqueue_script( 'dashboard-update', WPCS_PLUGIN_URL . 'includes/admin/assets/js/dashboard.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'dashboard-update', 'wpcs_dashboard_ajax_script', $dashboard_args );
		
	// Load the JavaScript for the Admin Notices & set-up the related Ajax script
	$admin_args = array(
		'ajaxurl'	 			=> admin_url( 'admin-ajax.php' ),
		'ajax_admin_nonce' 		=> wp_create_nonce( 'admin_notices_nonce' ),
	);

	wp_enqueue_script( 'admin-notices', WPCS_PLUGIN_URL . 'includes/admin/assets/js/admin-notices.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'admin-notices', 'wpcs_admin_ajax_script', $admin_args );
			
	// Load the JavaScript for the Module Notices & set-up the related Ajax script
	$module_args = array(
		'ajaxurl' 				=> admin_url( 'admin-ajax.php' ),
		'ajax_module_nonce' 	=> wp_create_nonce( 'module_notices_nonce' ),
	);

	wp_enqueue_script( 'module-notices', WPCS_PLUGIN_URL . 'includes/admin/assets/js/module-notices.min.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'module-notices', 'wpcs_module_ajax_script', $module_args );

}
add_action( 'admin_enqueue_scripts', 'wpcs_ajax_load_scripts' );
	
/**
 *  Create the Option for the Dashboard Update
 *
 *  @since  2.0.0
 */			
function wpcs_ajax_process_dashboard_update() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'dashboard_ui_nonce', 'dashboard_nonce' );

	// Pick up the notice "admin_type" - passed via the "data-notice" attribute
	if ( isset( $_POST['dashboard_type'] ) ) {
		$position = $_POST['dashboard_type'];
		update_option( 'wpcs_current_page', $position );
	} else {
		update_option( 'wpcs_current_page', 'No Data' );
	}		
}
add_action( 'wp_ajax_dashboard_update', 'wpcs_ajax_process_dashboard_update' );

/**
 *  Create the Option for the Dashboard Tabs Update
 *
 *  @since  3.0.6
 */			
function wpcs_ajax_process_dashboard_tabs_update() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'settings_dashboard_ui_tabs_nonce', 'settings_dashboard_tabs_nonce' );

	// Pick up the notice "admin_type" - passed via the "data-tab" attribute
	if ( isset( $_POST['settings_dashboard_tabs_type'] ) ) {
		$position	= $_POST['settings_dashboard_tabs_type'];
		$tab_id		= $_POST['settings_dashboard_tabs_id'];
		update_option( "wpcs_{$tab_id}_current_tab", $position );
	} 		
}
add_action( 'wp_ajax_settings_dashboard_tabs', 'wpcs_ajax_process_dashboard_tabs_update' );
		
/**
 *  Create the Option for the Dismissible Admin Notices
 *
 *  @since  1.0.0
 */			
function wpcs_ajax_process_admin_dismiss() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'admin_notices_nonce', 'admin_nonce' );

	// Pick up the notice "admin_type" - passed via the "data-notice" attribute
	if ( isset( $_POST['admin_type'] ) ) {
		$type				= sanitize_key( $_POST['admin_type'] );
		$dismissed			= get_option( 'wpcs_dismissed_admin_notices', array() );
		$dismissed[]		= $type;
		update_option( 'wpcs_dismissed_admin_notices', $dismissed );
	}
			
}
add_action( 'wp_ajax_admin_dismiss', 'wpcs_ajax_process_admin_dismiss' );
	
/**
 *  Create the Option for the Dismissible Module Notices
 *
 *  @since  1.0.0
 */		
function wpcs_ajax_process_module_dismiss() {

	// Check the nonce for the module notice data
	check_ajax_referer( 'module_notices_nonce', 'module_nonce' );

	// Pick up the notice "module_type" - passed via the "data-spnotice" attribute
	if ( isset( $_POST['module_type'] ) ) {
		$type = sanitize_key( $_POST['module_type'] );
		update_option( 'wpcs_dismissed_' . $type, true );
	}		
}
add_action( 'wp_ajax_module_dismiss', 'wpcs_ajax_process_module_dismiss' );