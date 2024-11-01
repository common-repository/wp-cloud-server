<?php

/**
 * WP Cloud Server - Ploi Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_License_Settings {

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_create_license_setting_sections_and_fields' ) );

	}
	
	/**
	 *  Create Ploi License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	public static function wpcs_ploi_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_ploi_license_settings', 'wpcs_ploi_module_license_key', 'wpcs_sanitize_license' );
		register_setting('wpcs_ploi_license_settings', 'wpcs_ploi_module_license_activate' );
	}

	function wpcs_sanitize_license( $new ) {
		$old = get_option( 'wpcs_ploi_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_ploi_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
}