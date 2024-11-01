<?php

/**
 * WP Cloud Server - Linode Module Activator Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Linode_Activator {

	/**
	 * Provides Hook for Linode Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_linode_module_activate' );
		
	}
}