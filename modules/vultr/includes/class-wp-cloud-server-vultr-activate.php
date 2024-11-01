<?php

/**
 * WP Cloud Server - Vultr Module Activator Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Vultr_Activator {

	/**
	 * Provides Hook for Vultr Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_vultr_module_activate' );
		
	}
}