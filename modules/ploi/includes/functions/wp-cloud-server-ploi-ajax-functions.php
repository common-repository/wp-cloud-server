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
	 *  Load the scripts for the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_ploi_ajax_load_scripts() {

		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_tabs_args = array(
			'ajaxurl'									=> admin_url( 'admin-ajax.php' ),
			'ajax_ploi_dashboard_tabs_nonce'			=> wp_create_nonce( 'ploi_dashboard_ui_tabs_nonce' ),
		);
		
		wp_enqueue_script( 'ploi_dashboard-tabs-update', WPCS_PLOI_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'ploi_dashboard-tabs-update', 'wpcs_ploi_dashboard_tabs_ajax_script', $dashboard_tabs_args );
		

		// Load the JavaScript for the Server Select Dropdown & set-up the related Ajax script
		$select_server_args = array(
			'ajaxurl'	 								=> admin_url( 'admin-ajax.php' ),
			'ajax_ploi_select_server_credentials_nonce'	=> wp_create_nonce( 'ploi_select_server_credentials_nonce' ),
		);
			
		wp_enqueue_script( 'ploi-select-server-credentials', WPCS_PLOI_PLUGIN_URL . 'includes/admin/assets/js/select-server-credentials.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'ploi-select-server-credentials', 'wpcs_ploi_select_server_credentials_ajax_script', $select_server_args );

		// Load the JavaScript for the Server Select Dropdown & set-up the related Ajax script
		$select_server_template_args = array(
			'ajaxurl'	 								=> admin_url( 'admin-ajax.php' ),
			'ajax_ploi_select_server_template_credentials_nonce'	=> wp_create_nonce( 'ploi_select_server_template_credentials_nonce' ),
		);
					
		wp_enqueue_script( 'ploi-select-server-template-credentials', WPCS_PLOI_PLUGIN_URL . 'includes/admin/assets/js/select-server-template-credentials.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'ploi-select-server-template-credentials', 'wpcs_ploi_select_server_template_credentials_ajax_script', $select_server_template_args );

		// Load the JavaScript for the Server Select Dropdown & set-up the related Ajax script
		$select_edit_server_template_args = array(
			'ajaxurl'	 								=> admin_url( 'admin-ajax.php' ),
			'ajax_ploi_select_edit_server_template_nonce'	=> wp_create_nonce( 'ploi_select_edit_server_template_nonce' ),
		);
					
		wp_enqueue_script( 'ploi-select-edit-server-template', WPCS_PLOI_PLUGIN_URL . 'includes/admin/assets/js/select-edit-server-template.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'ploi-select-edit-server-template', 'wpcs_ploi_select_edit_server_template_ajax_script', $select_edit_server_template_args );

	}
add_action( 'admin_enqueue_scripts', 'wpcs_ploi_ajax_load_scripts' );
		
	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_ploi_ajax_select_server_credentials() {

		// Check the nonce for the module notice data
		//check_ajax_referer( 'ajax_ploi_select_server_credentials_nonce', 'ploi_select_server_credentials_nonce' );

		if ( empty( $_POST['server_credentials'] ) ) {
			return;
		}

	    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $credentials = isset( $_POST['server_credentials'] ) ? sanitize_text_field( $_POST['server_credentials'] ) : "";
			
		// Retrieve Servers and Templates
		$args['provider'] = $credentials;

		$list = wpcs_ploi_api_user_request( 'server-providers/list/provider', $args );

		if ( is_array( $list ) ) {
			foreach ( $list['provider']['regions'] as $key => $plan ) {
				$region_list[]	= "<option value='{$plan['id']}'>{$plan['name']}</option>";
			}
		} else {
				$region_list[]	= "<option value='no_value'>-- No Regions Available --</option>";
		}

		if ( is_array( $list ) ) {
			foreach ( $list['provider']['plans'] as $key => $plan ) {
				$plan_list[]	= "<option value='{$plan['id']}'>{$plan['description']}</option>";
			}
		} else {
				$plan_list[]	= "<option value='no_value'>-- No Plans Available --</option>";
		}
		
		$data = array( $plan_list, $region_list );

		$response = json_encode( $data );

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
add_action( 'wp_ajax_ploi_select_server_credentials', 'wpcs_ploi_ajax_select_server_credentials' );


	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_ploi_ajax_select_server_template_credentials() {

		// Check the nonce for the module notice data
		//check_ajax_referer( 'ajax_ploi_select_server_template_credentials_nonce', 'ploi_select_server_template_credentials_nonce' );

		if ( empty( $_POST['server_template_credentials'] ) ) {
			return;
		}

	    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $credentials = isset( $_POST['server_template_credentials'] ) ? sanitize_text_field( $_POST['server_template_credentials'] ) : "";

		$credentials_explode	= explode( '|', $credentials );

		if ( isset( $credentials_explode[1] ) ) {
			$args['provider'] = isset( $credentials_explode[1] ) ? $credentials_explode[1] : false;
			$list = wpcs_ploi_api_user_request( 'server-providers/list/provider', $args );
		}

		if ( isset( $list['provider']['regions'] ) ) {
			foreach ( $list['provider']['regions'] as $key => $plan ) {
				$region_list[]	= "<option value='{$plan['name']}|{$plan['id']}'>{$plan['name']}</option>";
			}
		} else {
				$region_list[]	= "<option value='no_value'>-- No Regions Available --</option>";
		}

		if ( isset( $list['provider']['plans'] ) ) {
			foreach ( $list['provider']['plans'] as $key => $plan ) {
				$plan_list[]	= "<option value='{$plan['name']}|{$plan['id']}'>{$plan['description']}</option>";
			}
		} else {
				$plan_list[]	= "<option value='no_value'>-- No Plans Available --</option>";
		}
		
		$data = array( $plan_list, $region_list );

		$response = json_encode( $data );

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
add_action( 'wp_ajax_ploi_select_server_template_credentials', 'wpcs_ploi_ajax_select_server_template_credentials' );

	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_ploi_ajax_select_edit_server_template() {

		// Check the nonce for the module notice data
		//check_ajax_referer( 'ajax_ploi_select_server_template_credentials_nonce', 'ploi_select_server_template_credentials_nonce' );

		if ( empty( $_POST['edit_server_template'] ) ) {
			return;
		}

	    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $credentials = isset( $_POST['edit_server_template'] ) ? sanitize_text_field( $_POST['edit_server_template'] ) : "";

		$credentials_explode	= explode( '|', $credentials );
		$args['provider']		= $credentials_explode[1];

		update_option( 'wpcseditserver', $args['provider']);

		$list = wpcs_ploi_api_user_request( 'server-providers/list/provider', $args );

		if ( is_array( $list ) ) {
			foreach ( $list['provider']['regions'] as $key => $plan ) {
				$region_list[]	= "<option value='{$plan['name']}|{$plan['id']}'>{$plan['name']}</option>";
			}
		} else {
				$region_list[]	= "<option value='no_value'>-- No Regions Available --</option>";
		}

		if ( is_array( $list ) ) {
			foreach ( $list['provider']['plans'] as $key => $plan ) {
				$plan_list[]	= "<option value='{$plan['name']}|{$plan['id']}'>{$plan['description']}</option>";
			}
		} else {
				$plan_list[]	= "<option value='no_value'>-- No Plans Available --</option>";
		}
		
		$data = array( $plan_list, $region_list );

		$response = json_encode( $data );

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
add_action( 'wp_ajax_ploi_select_edit_server_template', 'wpcs_ploi_ajax_select_edit_server_template' );