<?php

/**
 * WP Cloud Server - AWS Lightsail Module Deactivate Function
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Ensure that the plugin deactivation disables the module.
*
* @since    1.0.0
*/
function wpcs_aws_lightsail_deactivate_plugin() {

	$module_data = get_option( 'wpcs_module_list' );

	unset( $module_data[  'AWS Lightsail' ] );

	update_option( 'wpcs_module_list', $module_data );
	
	$config			= get_option( 'wpcs_config' );
	$module_config	= get_option( 'wpcs_module_config' );

	unset( $module_config[ 'wp-cloud-server-cloud-servers' ][ 'AWS Lightsail'] );
	unset( $module_config[ 'wp-cloud-server-admin-menu' ][ 'AWS Lightsail'] );
	
	update_option( 'wpcs_module_config', $module_config );
	update_option( 'wpcs_config', $config );

}

register_deactivation_hook( WPCS_AWS_LIGHTSAIL_PLUGIN_FILE, 'wpcs_aws_lightsail_deactivate_plugin' );