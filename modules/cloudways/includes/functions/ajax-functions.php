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
	function wpcs_cloudways_ajax_load_scripts() {
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_tabs_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_cloudways_dashboard_tabs_nonce' => wp_create_nonce( 'cloudways_dashboard_ui_tabs_nonce' ),
		);

		wp_enqueue_script( 'cloudways_dashboard-tabs-update', WPCS_CLOUDWAYS_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'cloudways_dashboard-tabs-update', 'wpcs_cloudways_dashboard_tabs_ajax_script', $dashboard_tabs_args );

		$select_cloudways_provider_args = array(
			'ajaxurl'	 							=> admin_url( 'admin-ajax.php' ),
			'ajax_select_cloudways_provider_nonce' 	=> wp_create_nonce( 'select_cloudways_provider_nonce' ),
		);
				
		wp_enqueue_script( 'select-cloudways-provider', WPCS_CLOUDWAYS_PLUGIN_URL . 'includes/admin/assets/js/cloud-provider.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'select-cloudways-provider', 'wpcs_select_cloudways_provider_script', $select_cloudways_provider_args );
		
		$select_cloudways_template_provider_args = array(
			'ajaxurl'	 									=> admin_url( 'admin-ajax.php' ),
			'ajax_select_cloudways_template_provider_nonce'	=> wp_create_nonce( 'select_cloudways_template_provider_nonce' ),
		);
				
		wp_enqueue_script( 'select-cloudways-template-provider', WPCS_CLOUDWAYS_PLUGIN_URL . 'includes/admin/assets/js/cloudways-template.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'select-cloudways-template-provider', 'wpcs_select_cloudways_template_provider_script', $select_cloudways_template_provider_args );
		
		$select_cloudways_edit_template_provider_args = array(
			'ajaxurl'	 											=> admin_url( 'admin-ajax.php' ),
			'ajax_select_cloudways_edit_template_provider_nonce'	=> wp_create_nonce( 'select_cloudways_edit_template_provider_nonce' ),
		);
				
		wp_enqueue_script( 'select-cloudways-edit-template-provider', WPCS_CLOUDWAYS_PLUGIN_URL . 'includes/admin/assets/js/edit-cloudways-template.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'select-cloudways-edit-template-provider', 'wpcs_select_cloudways_edit_template_provider_script', $select_cloudways_edit_template_provider_args );



	}
	add_action( 'admin_enqueue_scripts', 'wpcs_cloudways_ajax_load_scripts' );
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_cloudways_dashboard_tabs() {

		// Check the nonce for the admin notice data
		check_ajax_referer( 'cloudways_dashboard_ui_tabs_nonce', 'cloudways_dashboard_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['cloudways_dashboard_tabs_type'] ) ) {
			$position	= $_POST['cloudways_dashboard_tabs_type'];
			$tab_id		= $_POST['cloudways_dashboard_tabs_id'];
			update_option( "wpcs_{$tab_id}_current_tab", $position );
		}
			
	}
	add_action( 'wp_ajax_cloudways_dashboard_tabs', 'wpcs_ajax_process_cloudways_dashboard_tabs' );

	
/**
 *  Handle the Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_cloudways_ajax_select_cloudways_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_cloudways_provider_nonce', 'select_cloudways_provider_nonce' );

	$cloud_provider	= isset( $_POST['cloudways_provider'] ) ? sanitize_text_field( $_POST['cloudways_provider'] ) : '';
		
	if ( empty( $cloud_provider ) ) {
		return;	
	}
		
	//$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
	
	$debug['provider'] = $cloud_provider;
			
	// generate the response
	$regions = call_user_func("wpcs_cloudways_regions_list");
	
	$debug['regions'] = $regions;
			
	if ( $regions ) {
        foreach ( $regions[$cloud_provider] as $key => $region ) {
			$region_id		= $region['id'];
			$region_name	= $region['name'];
            $region_list[] = "<option value='{$region_id}'>{$region_name}</option>";
		}
	}
		
	$plans = call_user_func("wpcs_cloudways_plans_list");
	
	$debug['plans'] = $plans;
	
	update_option( 'cloudways_regions_list', $debug );
		
	if ( $plans ) {
		foreach ( $plans[$cloud_provider] as $key => $plan ) {
			foreach ( $plan as $index => $item ) {
               	$plan_list[] = "<option value='{$item}'>{$item}</option>";
			}
		}
	}
		
	$data = array( $region_list, $plan_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_cloudways_provider', 'wpcs_cloudways_ajax_select_cloudways_provider' );

/**
 *  Handle the Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_cloudways_ajax_select_cloudways_template_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_cloudways_template_provider_nonce', 'select_cloudways_template_provider_nonce' );

	$cloud_provider	= isset( $_POST['cloudways_template_provider'] ) ? sanitize_text_field( $_POST['cloudways_template_provider'] ) : '';
		
	if ( empty( $cloud_provider ) ) {
		return;	
	}
	
	$cloud_provider_explode	= explode( '|', $cloud_provider );
	$cloud_provider		= $cloud_provider_explode[0];
		
	//$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
	
	$debug['provider'] = $cloud_provider;
			
	// generate the response
	$regions = call_user_func("wpcs_cloudways_regions_list");
	
	$debug['regions'] = $regions;
			
	if ( $regions ) {
        foreach ( $regions[$cloud_provider] as $key => $region ) {
			$region_id		= $region['id'];
			$region_name	= $region['name'];
            $region_list[] = "<option value='{$region_id}|{$region_name}'>{$region_name}</option>";
		}
	}
	
	$debug['regions-list'] = $region_list;
		
	$plans = call_user_func("wpcs_cloudways_plans_list");
	
	$debug['plans'] = $plans;
		
	if ( $plans ) {
		foreach ( $plans[$cloud_provider] as $key => $plan ) {
			foreach ( $plan as $index => $item ) {
               	$plan_list[] = "<option value='{$item}'>{$item}</option>";
			}
		}
	}
	
	$debug['plan-list'] = $plan_list;
	
	update_option( 'cloudways_regions_list', $debug );
		
	$data = array( $region_list, $plan_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_cloudways_template_provider', 'wpcs_cloudways_ajax_select_cloudways_template_provider' );

/**
 *  Handle the Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_cloudways_ajax_select_cloudways_edit_template_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_cloudways_edit_template_provider_nonce', 'select_cloudways_edit_template_provider_nonce' );

	$cloud_provider	= isset( $_POST['cloudways_edit_template_provider'] ) ? sanitize_text_field( $_POST['cloudways_edit_template_provider'] ) : '';
		
	if ( empty( $cloud_provider ) ) {
		return;	
	}
	
	$cloud_provider_explode	= explode( '|', $cloud_provider );
	$cloud_provider		= $cloud_provider_explode[0];
		
	//$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
	
	$debug['provider'] = $cloud_provider;
			
	// generate the response
	$regions = call_user_func("wpcs_cloudways_regions_list");
	
	$debug['regions'] = $regions;
			
	if ( $regions ) {
        foreach ( $regions[$cloud_provider] as $key => $region ) {
			$region_id		= $region['id'];
			$region_name	= $region['name'];
            $region_list[] = "<option value='{$region_id}|{$region_name}'>{$region_name}</option>";
		}
	}
		
	$plans = call_user_func("wpcs_cloudways_plans_list");
	
	$debug['plans'] = $plans;
	
	update_option( 'cloudways_regions_list', $debug );
		
	if ( $plans ) {
		foreach ( $plans[$cloud_provider] as $key => $plan ) {
			foreach ( $plan as $index => $item ) {
               	$plan_list[] = "<option value='{$item}'>{$item}</option>";
			}
		}
	}
		
	$data = array( $region_list, $plan_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_cloudways_edit_template_provider', 'wpcs_cloudways_ajax_select_cloudways_edit_template_provider' );