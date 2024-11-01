<?php

/**
 * WP Cloud Server - RunCloud Module Deactivate Function
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Ensure that the plugin deactivation disables the module and updates the menus.
*
* @since    1.0.0
*/
function wpcs_runcloud_deactivate_plugin() {

	// Set the RunCloud Module Deactivated

	$module_data = get_option( 'wpcs_module_list' );

	unset( $module_data[ 'RunCloud' ] );

	update_option( 'wpcs_module_list', $module_data );
		
	$config					= get_option( 'wpcs_config' );
	$module_config			= get_option( 'wpcs_module_config' );

	unset( $module_config[ 'wp-cloud-server-cloud-servers' ]['RunCloud'] );
	unset( $module_config[ 'wp-cloud-server-admin-menu' ]['RunCloud'] );
	
	
	update_option( 'wpcs_module_config', $module_config );
	update_option( 'wpcs_config', $config );	

}

register_deactivation_hook( WPCS_RUNCLOUD_PLUGIN_FILE, 'wpcs_runcloud_deactivate_plugin' );