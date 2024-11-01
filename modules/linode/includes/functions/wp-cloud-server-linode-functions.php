<?php
/**
 * WP Cloud Server - Linode Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_linode_check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show Linode API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_linode_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_linode_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_linode_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="linode_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - Linode: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-linode' ), '<a href="?page=wp-cloud-server-admin-menu&tab=linode&submenu=servers">' . __( 'Linode Server Information', 'wp-cloud-server-linode' ) . '</a>' ); ?>
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
function wpcs_linode_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed();
		
	$plugin_activated = is_wpcs_plugin_active();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_linode_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_linode_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function wpcs_linode_is_parent_plugin_installed() {

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
function wpcs_linode_is_parent_plugin_active() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_linode_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_linode_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="linode_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Linode Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-linode' ); ?>
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
function wpcs_linode_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_linode_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="linode_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Linode Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-linode' ); ?>
			</p>
    	</div>
	<?php
	}

}

function wpcs_linode_module_api_connected() {
	return WPCS_Linode()->settings->wpcs_linode_module_api_connected();
}

function wpcs_linode_update_module_status( $module_name, $new_status ) {
	return WPCS_Linode()->settings->wpcs_linode_update_module_status( $module_name, $new_status );
}