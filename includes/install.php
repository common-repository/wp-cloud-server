<?php
/**
 * Install Function
 *
 * @package     WPCS
 * @copyright   Copyright (c) 2020, DesignedforPixels
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * @since 2.1.3
 */
function wpcs_install( $network_wide = false ) {

		do_action( 'wpcs_run_install' );

}
register_activation_hook( WPCS_PLUGIN_DIR, 'wpcs_install' );

/**
 * Run the WPCS Install process
 *
 * @since  2.1.3
 * @return void
 */
function wpcs_run_install() {


}

/**
 * Post-installation
 *
 * @since 2.1.3
 * @return void
 */
function wpcs_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	do_action( 'wpcs_plugin_activated');

}
add_action( 'admin_init', 'wpcs_after_install' );