<?php

/**
 * WP Cloud Server - RunCloud Module Activator Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_RunCloud_Activator {

	/**
	 * Provides Hook for RunCloud Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_runcloud_module_activate' );
		
	}
}