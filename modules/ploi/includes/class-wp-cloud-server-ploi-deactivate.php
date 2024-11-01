<?php

/**
 * WP Cloud Server - Ploi Module Deactivate Function
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Ensure that the plugin deactivation disables the module and updates the menus.
*
* @since    1.0.0
*/
function wpcs_ploi_deactivate_plugin() {

	// Set the Ploi Module Deactivated

	$module_data = get_option( 'wpcs_module_list' );

	unset( $module_data[ 'Ploi' ] );

	update_option( 'wpcs_module_list', $module_data );
		
	$config					= get_option( 'wpcs_config' );
	$module_config			= get_option( 'wpcs_module_config' );

	unset( $module_config[ 'wp-cloud-server-cloud-servers' ]['Ploi'] );
	unset( $module_config[ 'wp-cloud-server-admin-menu' ]['Ploi'] );
	
	
	update_option( 'wpcs_module_config', $module_config );
	update_option( 'wpcs_config', $config );	

}

register_deactivation_hook( WPCS_PLOI_PLUGIN_FILE, 'wpcs_ploi_deactivate_plugin' );