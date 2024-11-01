<?php
/**
 * WP Cloud Server - Vultr Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show Vultr API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_vultr_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_vultr_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_vultr_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="vultr_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - Vultr: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-vultr' ), '<a href="?page=wp-cloud-server-admin-menu&tab=vultr&submenu=servers">' . __( 'Vultr Server Information', 'wp-cloud-server-vultr' ) . '</a>' ); ?>
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
function wpcs_vultr_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed();
		
	$plugin_activated = is_wpcs_plugin_active();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_vultr_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_vultr_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function is_wpcs_plugin_installed() {

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
function is_wpcs_plugin_active() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_vultr_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_vultr_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="vultr_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Vultr Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-vultr' ); ?>
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
function wpcs_vultr_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_vultr_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="vultr_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Vultr Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-vultr' ); ?>
			</p>
    	</div>
	<?php
	}

}

function wpcs_vultr_module_api_connected() {
	return WPCS_Vultr()->settings->wpcs_vultr_module_api_connected();
}

function wpcs_vultr_update_module_status( $module_name, $new_status ) {
	return WPCS_Vultr()->settings->wpcs_vultr_update_module_status( $module_name, $new_status );
}

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function check_vultr_pro_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server-vultr-pro/wp-cloud-server-vultr-pro.php');

	return $active;

}

/**
 * Return API Data from Background Process or direct via Vultr API
 *
 * @since  1.0
 *
 * @return images List of available OS images
 */
function wpcs_vultr_return_api_data( $request, $index, $feedback=false, $label='vultr_data', $response=false ) {

	// Create instance of the Vultr API
	$api	= new WP_Cloud_Server_Vultr_API();
	$plans	= get_option( 'wpcs_vultr_api_data' );
	
	if ( !isset( $plans[$index] ) ) {
		$plans[$index] = $api->call_api( $request, null, false, 900, 'GET', $response, $label );
	}
}