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
function wpcs_serverpilot_ajax_load_scripts() {
		
	// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
	$dashboard_tabs_args = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_serverpilot_dashboard_tabs_nonce' => wp_create_nonce( 'serverpilot_dashboard_ui_tabs_nonce' ),
	);

	wp_enqueue_script( 'serverpilot_dashboard-tabs-update', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'serverpilot_dashboard-tabs-update', 'wpcs_serverpilot_dashboard_tabs_ajax_script', $dashboard_tabs_args );
	
	// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
	$dashboard_app_tabs_args = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_serverpilot_dashboard_app_tabs_nonce' => wp_create_nonce( 'serverpilot_dashboard_ui_app_tabs_nonce' ),
	);

	wp_enqueue_script( 'serverpilot_dashboard-app-tabs-update', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/dashboard-app-tab.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'serverpilot_dashboard-app-tabs-update', 'wpcs_serverpilot_dashboard_app_tabs_ajax_script', $dashboard_app_tabs_args );

}
add_action( 'admin_enqueue_scripts', 'wpcs_serverpilot_ajax_load_scripts' );
	
/**
 *  Create the Option for the Dashboard Update
 *
 *  @since  2.0.0
 */			
function wpcs_ajax_process_serverpilot_dashboard_tabs() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'serverpilot_dashboard_ui_tabs_nonce', 'serverpilot_dashboard_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['serverpilot_dashboard_tabs_type'] ) ) {
			$position	= $_POST['serverpilot_dashboard_tabs_type'];
			$tab_id		= $_POST['serverpilot_dashboard_tabs_id'];
			update_option( "wpcs_{$tab_id}_current_tab", $position );
		}
			
	}
add_action( 'wp_ajax_serverpilot_dashboard_tabs', 'wpcs_ajax_process_serverpilot_dashboard_tabs' );

/**
 *  Create the Option for the Dashboard Update
 *
 *  @since  2.0.0
 */			
function wpcs_ajax_process_serverpilot_dashboard_app_tabs() {

	// Check the nonce for the admin notice data
	check_ajax_referer( 'serverpilot_dashboard_ui_app_tabs_nonce', 'serverpilot_dashboard_app_tabs_nonce' );

	// Pick up the notice "admin_type" - passed via the "data-tab" attribute
	if ( isset( $_POST['serverpilot_dashboard_app_tabs_type'] ) ) {
		$position = $_POST['serverpilot_dashboard_app_tabs_type'];
		update_option( 'wpcs_serverpilot_current_tab', $position );
	} else {
		update_option( 'wpcs_serverpilot_current_tab', 'No Data' );
	}
			
}
add_action( 'wp_ajax_serverpilot_dashboard_app_tabs', 'wpcs_ajax_process_serverpilot_dashboard_app_tabs' );

/**
 *  Load the scripts for the Template Select Options
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_scripts() {

	// Load the JavaScript for the ServerPilot Template Select Options
		
	$select_vultr_plan_args = array(
		'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
		'ajax_select_vultr_plan_nonce' => wp_create_nonce( 'select_vultr_plan_nonce' ),
	);
			
	wp_enqueue_script( 'select_vultr_plan', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/vultr-region.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'select_vultr_plan', 'wpcs_select_vultr_plan_script', $select_vultr_plan_args );
		
	$select_cloud_provider_args = array(
		'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
		'ajax_select_cloud_provider_nonce' => wp_create_nonce( 'select_cloud_provider_nonce' ),
	);
			
	wp_enqueue_script( 'select-cloud-provider', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/cloud-provider.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'select-cloud-provider', 'wpcs_select_cloud_provider_script', $select_cloud_provider_args );
	
	$edit_cloud_provider_args = array(
		'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
		'ajax_edit_cloud_provider_nonce' => wp_create_nonce( 'edit_cloud_provider_nonce' ),
	);
			
	wp_enqueue_script( 'edit-cloud-provider', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/edit-cloud-provider.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'edit-cloud-provider', 'wpcs_edit_cloud_provider_script', $edit_cloud_provider_args );
		
	// Load the JavaScript for the ServerPilot Server Select Options
		
	$select_vultr_server_plan_args = array(
		'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
		'ajax_select_vultr_server_plan_nonce' => wp_create_nonce( 'select_vultr_server_plan_nonce' ),
	);
			
	wp_enqueue_script( 'select_vultr_server_plan', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/vultr-server-region.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'select_vultr_server_plan', 'wpcs_select_vultr_server_plan_script', $select_vultr_server_plan_args );
		
	$select_server_cloud_provider_args = array(
		'ajaxurl'	 				=> admin_url( 'admin-ajax.php' ),
		'ajax_select_server_cloud_provider_nonce' => wp_create_nonce( 'select_server_cloud_provider_nonce' ),
	);
			
	wp_enqueue_script( 'select-server-cloud-provider', WPCS_SERVERPILOT_PLUGIN_URL . 'includes/admin/assets/js/server-cloud-provider.js', array( 'jquery' ), '1.0.0', false );
	wp_localize_script( 'select-server-cloud-provider', 'wpcs_select_server_cloud_provider_script', $select_server_cloud_provider_args );

}
add_action( 'admin_enqueue_scripts', 'wpcs_serverpilot_ajax_scripts' );
	
/**
 *  Handle the Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_select_cloud_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_cloud_provider_nonce', 'select_cloud_provider_nonce' );

	$cloud_provider_raw	= isset( $_POST['cloud_provider'] ) ? sanitize_text_field( $_POST['cloud_provider'] ) : '';
		
	if ( empty( $cloud_provider_raw ) ) {
		return;	
	}
		
	$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
			
	// generate the response
	$regions = call_user_func("wpcs_{$cloud_provider}_regions_list");
			
	if ( $regions ) {
        foreach ( $regions as $key => $region ){
			$region_dcid[]  = $key; 
			$region_name = $region['name'];
            $region_list[] = "<option value='{$region_name}|{$key}'>{$region_name}</option>";
		}
	}
	$region_list[] = '<option value="userselected">No Region (User Selectable)</option>';
		
	$plans = call_user_func("wpcs_{$cloud_provider}_availability_list", $region_dcid[0]);
		
	if ( $plans ) {
		$plan_list = "";
		foreach ( $plans as $key => $type ){
			$plan_list .= "<optgroup label='{$key}'>";
            foreach ( $type as $key => $plan ){
				$change	 = array(".00 ", " BW", ",");
				$replace = array("", "", ", ");
				$plan_name = str_replace($change, $replace, $plan['name']);
               	$plan_list .= '<option value="' . $plan['name'] . '|' . $key . '">' . $plan_name . ' ' . $plan['cost'] . '</option>';
			}
			$plan_list .= '</optgroup>';
		}
	}
		
	$blueprints = call_user_func("wpcs_{$cloud_provider}_managed_os_list");
			
	if ( $blueprints ) {
        foreach ( $blueprints as $key => $blueprint ){ 
			$change	 = array(".00 ", " BW", ",");
			$replace = array("", "", ", ");
			$plan_name = str_replace($change, $replace, $blueprint['name']);
            $image_list[] = '<option value="' . $blueprint['name'] . '|' . $blueprint['name'] . '">' . $blueprint['name'] . '</option>';
		}
	}
		
	$data = array( $region_list, $plan_list, $image_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_cloud_provider', 'wpcs_serverpilot_ajax_select_cloud_provider' );
	
/**
 *  Handle the Vultr Plan Dropdown Module List
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_select_vultr_plan() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_vultr_plan_nonce', 'select_vultr_plan_nonce' );

	// 
	$cloud_provider	= sanitize_text_field( $_POST['cloud_provider'] );
	$region		= sanitize_text_field( $_POST['plan_size'] );
		
	$region_explode	= explode( '|', $region );
	$region = ('userselected' == $region ) ? $region_explode[0] : $region_explode[1] ;
	
	$cloud_provider	= strtolower( str_replace( " ", "_", $cloud_provider ) );
		
	// generate the response
	$plans = call_user_func("wpcs_{$cloud_provider}_availability_list", $region );
		
	if ( $plans ) {
		$plan_list = "";
		foreach ( $plans as $title => $type ){
			$plan_list .= "<optgroup label='{title}'>";
            foreach ( $type as $key => $plan ){
				$change	 = array(".00 ", " BW", ",");
				$replace = array("", "", ", ");
				$plan_name = str_replace($change, $replace, $plan['name']);
               	$plan_list .= '<option value="' . $plan['name'] . '|' . $key . '">' . $plan_name . ' ' . $plan['cost'] . '</option>';
			}
			$plan_list .= '</optgroup>';
		}
	} else {
		$plan_list .= "<option value='no-server'>-- No Servers Available --</option>";
	}

    $response = json_encode( $plan_list );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_vultr_plan', 'wpcs_serverpilot_ajax_select_vultr_plan' );
	
/**
 *  Handle the Server Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_select_server_cloud_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_server_cloud_provider_nonce', 'select_server_cloud_provider_nonce' );

	$cloud_provider_raw	= isset( $_POST['cloud_provider'] ) ? sanitize_text_field( $_POST['cloud_provider'] ) : '';
		
	if ( empty( $cloud_provider_raw ) ) {
		return;	
	}
		
	$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
		
	if ( empty( $cloud_provider ) ) {
		return;	
	}
			
	// generate the response
	$regions = call_user_func("wpcs_{$cloud_provider}_regions_list");
			
	if ( $regions ) {
        foreach ( $regions as $key => $region ){
			$region_dcid[]  = $key; 
			$region_name = $region['name'];
            $region_list[] = "<option value='{$region_name}|{$key}'>{$region_name}</option>";
		}
	}
		
	$plans = call_user_func("wpcs_{$cloud_provider}_availability_list", $region_dcid[0]);
		
	if ( $plans ) {
		$plan_list = "";
		foreach ( $plans as $key => $type ){
			$plan_list .= "<optgroup label='{$key}'>";
            foreach ( $type as $key => $plan ){
				$change	 = array(".00 ", " BW", ",");
				$replace = array("", "", ", ");
				$plan_name = str_replace($change, $replace, $plan['name']);
               	$plan_list .= '<option value="' . $plan['name'] . '|' . $key . '">' . $plan_name . ' ' . $plan['cost'] . '</option>';
			}
			$plan_list .= '</optgroup>';
		}
	}
		
	$blueprints = call_user_func("wpcs_{$cloud_provider}_managed_os_list");
			
	if ( $blueprints ) {
         foreach ( $blueprints as $key => $blueprint ){ 
			$change	 = array(".00 ", " BW", ",");
			$replace = array("", "", ", ");
			$plan_name = str_replace($change, $replace, $blueprint['name']);
            $image_list[] = '<option value="' . $blueprint['name'] . '|' . $blueprint['name'] . '">' . $blueprint['name'] . '</option>';
		}
	}
		
	$data = array( $region_list, $plan_list, $image_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_server_cloud_provider', 'wpcs_serverpilot_ajax_select_server_cloud_provider');
	
/**
 *  Handle the Server Plan Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_select_vultr_server_plan() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'select_vultr_server_plan_nonce', 'select_vultr_server_plan_nonce' );

	$cloud_provider	= isset( $_POST['cloud_provider'] ) ? sanitize_text_field( $_POST['cloud_provider'] ) : '';
	$region			= isset( $_POST['plan_size'] ) ? sanitize_text_field( $_POST['plan_size'] ) : '';
	
	$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider ) );
		
	$region_explode	= explode( '|', $region );
	$region			= $region_explode[1];
		
	// generate the response
	//$plans = call_user_func("wpcs_{$cloud_provider}_plans_list");
	$plans = call_user_func("wpcs_{$cloud_provider}_availability_list", $region );
		
	if ( $plans ) {
		$plan_list = "";
		foreach ( $plans as $key => $type ){
			$plan_list .= "<optgroup label='{$key}'>";
            foreach ( $type as $key => $plan ){
				$change	 = array(".00 ", " BW", ",");
				$replace = array("", "", ", ");
				$plan_name = str_replace($change, $replace, $plan['name']);
               	$plan_list .= '<option value="' . $plan['name'] . '|' . $key . '">' . $plan_name . ' ' . $plan['cost'] . '</option>';
			}
			$plan_list .= '</optgroup>';
		}
	}

    $response = json_encode( $plan_list );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_select_vultr_server_plan', 'wpcs_serverpilot_ajax_select_vultr_server_plan' );

/**
 *  Handle the Edit Cloud Provider Dropdown List
 *
 *  @since 1.2.0
 */
function wpcs_serverpilot_ajax_edit_cloud_provider() {

	// Check the nonce for the cloud provider setting
	check_ajax_referer( 'edit_cloud_provider_nonce', 'edit_cloud_provider_nonce' );

	$cloud_provider_raw	= isset( $_POST['cloud_provider'] ) ? sanitize_text_field( $_POST['cloud_provider'] ) : '';
		
	if ( empty( $cloud_provider_raw ) ) {
		return;	
	}
		
	$cloud_provider = strtolower( str_replace( " ", "_", $cloud_provider_raw ) );
			
	// generate the response
	$regions = call_user_func("wpcs_{$cloud_provider}_regions_list");
			
	if ( $regions ) {
        foreach ( $regions as $key => $region ){
			$region_dcid[]  = $key; 
			$region_name = $region['name'];
            $region_list[] = "<option value='{$region_name}|{$key}'>{$region_name}</option>";
		}
	}
	$region_list[] = '<option value="userselected">No Region (User Selectable)</option>';
		
	$plans = call_user_func("wpcs_{$cloud_provider}_availability_list", $region_dcid[0]);
		
	if ( $plans ) {
		$plan_list = "";
		foreach ( $plans as $key => $type ){
			$plan_list .= "<optgroup label='{$key}'>";
            foreach ( $type as $key => $plan ){
				$change	 = array(".00 ", " BW", ",");
				$replace = array("", "", ", ");
				$plan_name = str_replace($change, $replace, $plan['name']);
               	$plan_list .= '<option value="' . $plan['name'] . '|' . $key . '">' . $plan_name . ' ' . $plan['cost'] . '</option>';
			}
			$plan_list .= '</optgroup>';
		}
	}
		
	$blueprints = call_user_func("wpcs_{$cloud_provider}_managed_os_list");
			
	if ( $blueprints ) {
        foreach ( $blueprints as $key => $blueprint ){ 
			$change	 = array(".00 ", " BW", ",");
			$replace = array("", "", ", ");
			$plan_name = str_replace($change, $replace, $blueprint['name']);
            $image_list[] = '<option value="' . $blueprint['name'] . '|' . $blueprint['name'] . '">' . $blueprint['name'] . '</option>';
		}
	}
		
	$data = array( $region_list, $plan_list, $image_list );

	$response = json_encode( $data );

    // response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
	exit;
				
}
add_action( 'wp_ajax_edit_cloud_provider', 'wpcs_serverpilot_ajax_edit_cloud_provider' );