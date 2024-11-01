<?php
/**
 * WP Cloud Server - UpCloud Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_UpCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_upcloud_check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show UpCloud API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_upcloud_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_upcloud_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_upcloud_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="upcloud_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - UpCloud: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-upcloud' ), '<a href="?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=servers">' . __( 'UpCloud Server Information', 'wp-cloud-server-upcloud' ) . '</a>' ); ?>
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
function wpcs_upcloud_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed_upcloud();
		
	$plugin_activated = is_wpcs_plugin_active_upcloud();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_upcloud_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_upcloud_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function is_wpcs_plugin_installed_upcloud() {

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
function is_wpcs_plugin_active_upcloud() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_upcloud_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_upcloud_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="upcloud_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - UpCloud Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-upcloud' ); ?>
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
function wpcs_upcloud_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_upcloud_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="upcloud_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - UpCloud Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-upcloud' ); ?>
			</p>
    	</div>
	<?php
	}

}

function wpcs_upcloud_module_api_connected() {
	return WPCS_UpCloud()->settings->wpcs_upcloud_module_api_connected();
}

function wpcs_upcloud_update_module_status( $module_name, $new_status ) {
	return WPCS_UpCloud()->settings->wpcs_upcloud_update_module_status( $module_name, $new_status );
}