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
function wpcs_digitalocean_ajax_load_scripts() {
		
	// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
	$dashboard_tabs_args = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_digitalocean_dashboard_tabs_nonce' => wp_create_nonce( 'digitalocean_dashboard_ui_tabs_nonce' ),
	);

	wp_enqueue_script( 'digitalocean_dashboard-tabs-update', WPCS_DIGITALOCEAN_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'digitalocean_dashboard-tabs-update', 'wpcs_digitalocean_dashboard_tabs_ajax_script', $dashboard_tabs_args );

}
add_action( 'admin_enqueue_scripts', 'wpcs_digitalocean_ajax_load_scripts' );
	
/**
 *  Create the Option for the Dashboard Update
 *
 *  @since  2.0.0
 */			
function wpcs_ajax_process_digitalocean_dashboard_tabs() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'digitalocean_dashboard_ui_tabs_nonce', 'digitalocean_dashboard_tabs_nonce' );

	// Pick up the notice "admin_type" - passed via the "data-tab" attribute
	if ( isset( $_POST['digitalocean_dashboard_tabs_type'] ) ) {
		$position	= $_POST['digitalocean_dashboard_tabs_type'];
		$tab_id		= $_POST['digitalocean_dashboard_tabs_id'];
		update_option( "wpcs_{$tab_id}_current_tab", $position );
	} 		
}
add_action( 'wp_ajax_digitalocean_dashboard_tabs', 'wpcs_ajax_process_digitalocean_dashboard_tabs' );