<?php
/**
 * WP Cloud Server - RunCloud Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_runcloud_check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show RunCloud API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_runcloud_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_runcloud_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_runcloud_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="runcloud_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - RunCloud: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-runcloud' ), '<a href="?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=servers">' . __( 'RunCloud Server Information', 'wp-cloud-server-runcloud' ) . '</a>' ); ?>
			</p>
    	</div>
	<?php
	}

}

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_runcloud_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed_runcloud();
		
	$plugin_activated = is_wpcs_plugin_active_runcloud();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_runcloud_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_runcloud_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function is_wpcs_plugin_installed_runcloud() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // Get all the plugins installed
	$installedPlugins = get_plugins();

	return ( array_key_exists( "wp-cloud-server/wp-cloud-server.php", $installedPlugins ) );
	
}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function is_wpcs_plugin_active_runcloud() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_runcloud_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_runcloud_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="runcloud_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - RunCloud Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-runcloud' ); ?>
			</p>
    	</div>
	<?php
	}

}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_runcloud_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_runcloud_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="runcloud_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - RunCloud Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-runcloud' ); ?>
			</p>
    	</div>
	<?php
	}
}

function wpcs_runcloud_module_api_connected() {
	return WPCS_RunCloud()->settings->wpcs_runcloud_module_api_connected();
}

function wpcs_runcloud_update_module_status( $module_name, $new_status ) {
	return WPCS_RunCloud()->settings->wpcs_runcloud_update_module_status( $module_name, $new_status );
}

function wpcs_uninstall_runcloud_plugin() {
	
	$delete_data_confirmed = get_option( 'wpcs_uninstall_data_confirmed' );
	
	delete_option( 'wpcs_runcloud_server_complete_queue' );
	
	// Clear the Server Completed Queue
	wp_clear_scheduled_hook( 'wpcs_runcloud_run_server_completed_queue' );
	
	if ( $delete_data_confirmed ) {
	
		// Delete the Log Files
		delete_option( 'wpcs_runcloud_log_event' );
		
	}

}