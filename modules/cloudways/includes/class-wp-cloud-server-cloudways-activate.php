<?php

/**
 * WP Cloud Server - Cloudways Module Activator Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Cloudways_Activator {

	/**
	 * Provides Hook for Cloudways Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_cloudways_module_activate' );
		
	}
}