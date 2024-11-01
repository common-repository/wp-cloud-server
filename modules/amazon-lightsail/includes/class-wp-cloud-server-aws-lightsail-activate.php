<?php

/**
 * WP Cloud Server - AWS Lightsail Module Activator Class
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_AWS_Lightsail_Activator {

	/**
	 * Provides Hook for AWS Lightsail Activation functionality
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		do_action( 'wpcs_aws_lightsail_module_activate' );
		
	}
}