<?php

/**
 * WP Cloud Server - Cloudways Module Deactivate Function
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Ensure that the plugin deactivation disables the module and updates the menus.
*
* @since    1.0.0
*/
function wpcs_cloudways_deactivate_plugin() {

	// Set the Cloudways Module Deactivated

	$module_data = get_option( 'wpcs_module_list' );

	unset( $module_data[ 'Cloudways' ] );

	update_option( 'wpcs_module_list', $module_data );
		
	$config					= get_option( 'wpcs_config' );
	$module_config			= get_option( 'wpcs_module_config' );

	unset( $module_config[ 'wp-cloud-server-cloud-servers' ]['Cloudways'] );
	unset( $module_config[ 'wp-cloud-server-admin-menu' ]['Cloudways'] );
	
	
	update_option( 'wpcs_module_config', $module_config );
	update_option( 'wpcs_config', $config );	

}

register_deactivation_hook( WPCS_CLOUDWAYS_PLUGIN_FILE, 'wpcs_cloudways_deactivate_plugin' );