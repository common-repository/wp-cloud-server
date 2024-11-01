<?php
/**
 * The Activation functionality for the DigitalOcean Module.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_DigitalOcean_Activator {

	/**
	 * Provides Hook for DigitalOcean Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_digitalocean_module_activate' );
		
	}
}