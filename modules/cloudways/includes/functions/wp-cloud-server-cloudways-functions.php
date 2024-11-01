<?php
/**
 * WP Cloud Server - Cloudways Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_cloudways_check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show Cloudways API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_cloudways_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_cloudways_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_cloudways_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="cloudways_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - Cloudways: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-cloudways' ), '<a href="?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=servers">' . __( 'Cloudways Server Information', 'wp-cloud-server-cloudways' ) . '</a>' ); ?>
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
function wpcs_cloudways_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed_cloudways();
		
	$plugin_activated = is_wpcs_plugin_active_cloudways();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_cloudways_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_cloudways_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function is_wpcs_plugin_installed_cloudways() {

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
function is_wpcs_plugin_active_cloudways() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_cloudways_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_cloudways_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="cloudways_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Cloudways Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-cloudways' ); ?>
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
function wpcs_cloudways_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_cloudways_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="cloudways_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - Cloudways Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-cloudways' ); ?>
			</p>
    	</div>
	<?php
	}

}

/**
 * Retrieves a template part
 *
 * @since v1.5
 *
 * Taken from bbPress
 *
 * @param string $slug
 * @param string $name Optional. Default null
 *
 * @uses  rcp_locate_template()
 * @uses  load_template()
 * @uses  get_template_part()
 */
function wpcs_get_template_part( $slug, $name = null, $load = true ) {
	// Execute code for this part
	do_action( 'get_template_part_' . $slug, $slug, $name );
 
	// Setup possible parts
	$templates = array();
	if ( isset( $name ) )
		$templates[] = $slug . '-' . $name . '.php';
	$templates[] = $slug . '.php';
 
	// Allow template parts to be filtered
	$templates = apply_filters( 'wpcs_get_template_part', $templates, $slug, $name );
 
	// Return the part that is found
	return wpcs_locate_template( $templates, $load, false );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * Taken from bbPress
 *
 * @since v1.5
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true.
 *                            Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function wpcs_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;
 
	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {
 
		// Continue if template is empty
		if ( empty( $template_name ) )
			continue;
 
		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );
 
		// Check theme compatibility last
		if ( file_exists( trailingslashit( wpcs_get_templates_dir() ) . $template_name ) ) {
			$located = trailingslashit( wpcs_get_templates_dir() ) . $template_name;
			break;
		}
	}
 
	if ( ( true == $load ) && ! empty( $located ) )
		load_template( $located, $require_once );
 
	return $located;
}

/**
 * Returns the path to the EDD templates directory
 *
 * @since 1.2
 * @return string
 */
function wpcs_get_templates_dir() {
	return WPCS_CLOUDWAYS_PLUGIN_DIR . 'templates';
}

function wpcs_cloudways_module_api_connected() {
	return WPCS_Cloudways()->settings->wpcs_cloudways_module_api_connected();
}

function wpcs_uninstall_cloudways_plugin() {
	
	$delete_data_confirmed = get_option( 'wpcs_uninstall_data_confirmed' );
	
	delete_option( 'wpcs_cloudways_server_complete_queue' );
	delete_transient( 'wpcs_cloudways_api_health' );
	
	// Clear the Server Completed Queue
	wp_clear_scheduled_hook( 'wpcs_cloudways_run_server_completed_queue' );
	
	if ( $delete_data_confirmed ) {
	
		// Delete the Log Files
		delete_option( 'wpcs_cloudways_log_event' );
		
	}

}