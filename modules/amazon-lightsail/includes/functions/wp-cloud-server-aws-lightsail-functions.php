<?php
/**
 * WP Cloud Server - AWS Lightsail Module Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Check that the WP Cloud Server plugin is active.
*
* @since    1.0.0
*/
function wpcs_aws_lightsail_check_parent_plugin() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
    $active = is_plugin_active( 'wp-cloud-server/wp-cloud-server.php');

	// Show AWS Lightsail API connectivity issue notice.
	if ( ! $active ) {
		add_action( 'admin_notices', 'wpcs_aws_lightsail_notice_no_parent_plugin' );
	}

	return $active;

}

/**
*  Show info message when new site created
*
*  @since  1.0.0
*/
function wpcs_aws_lightsail_notice_no_parent_plugin() {

	if ( ! get_option('wpcs_dismissed_aws_lightsail_no_parent_plugin_notice', false ) ) {
	?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="linode_no_parent_plugin_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - AWS Lightsail: requires parent plugin! Please visit the %s page for details.', 'wp-cloud-server-linode' ), '<a href="?page=wp-cloud-server-admin-menu&tab=linode&submenu=servers">' . __(  'AWS Lightsail Server Information', 'wp-cloud-server-linode' ) . '</a>' ); ?>
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
function wpcs_aws_lightsail_parent_plugin_status() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$plugin_installed = is_wpcs_plugin_installed();
		
	$plugin_activated = is_wpcs_plugin_active();

	if ( ! $plugin_installed ) {
		$action = 'installed';
	} elseif ( ! $plugin_activated ) {
		$action = 'activated';
	}

	if ( ! get_option('wpcs_dismissed_aws_lightsail_no_parent_plugin_notice', false ) && ( ! $plugin_installed || ! $plugin_activated ) ) {
		add_action( 'admin_notices', "wpcs_aws_lightsail_notice_parent_plugin_not_{$action}" );
	}

	return $plugin_activated;

}

/**
* Checks if a WordPress plugin is installed.
*
*  @since  1.0.0
*/
function wpcs_aws_lightsail_is_parent_plugin_installed() {

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
function wpcs_aws_lightsail_is_parent_plugin_active() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return ( is_plugin_active( 'wp-cloud-server/wp-cloud-server.php') );
	
}

/**
*  Alert user if WP Cloud Server Plugin is not active
*
*  @since  1.0.0
*/
function wpcs_aws_lightsail_notice_parent_plugin_not_installed() {

	if ( ! get_option('wpcs_dismissed_aws_lightsail_plugin_not_installed_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="linode_plugin_not_installed_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - AWS Lightsail Module" requires the "WP Cloud Server" plugin to be Installed & Activated.', 'wp-cloud-server-linode' ); ?>
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
function wpcs_aws_lightsail_notice_parent_plugin_not_activated() {

	if ( ! get_option('wpcs_dismissed_aws_lightsail_plugin_not_activated_notice', false ) ) {
	?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="linode_plugin_not_activated_notice">
			<p>
				<?php echo wp_kses( 'The "WP Cloud Server - AWS Lightsail Module" requires the "WP Cloud Server" plugin to be Activated.', 'wp-cloud-server-linode' ); ?>
			</p>
    	</div>
	<?php
	}

}

function wpcs_aws_lightsail_module_api_connected() {
	return WPCS_AWS_Lightsail()->settings->wpcs_aws_lightsail_module_api_connected();
}