<?php
/**
 * Admin Notice Functions.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Display Admin & DigitalOcean Module Admin Notices
 *
 *  @since  1.0.0
 */
function wpcs_digitalocean_show_admin_notices() {
		
	$module_data	= get_option( 'wpcs_module_list' );
	$api			= new WP_Cloud_Server_DigitalOcean_API();
		
	//Show DigitalOcean API connectivity issue notice.
	if ( current_user_can( 'manage_options' )  && ( 'active' == $module_data['DigitalOcean']['status'] ) && ! $api->wpcs_digitalocean_check_api_health() ) {
		add_action( 'wpcs_system_notices', array( $this, 'wpcs_digitalocean_notice_no_api_connection' ) );
	}

	// Show site creation notice.
	if ( current_user_can( 'manage_options' ) && get_option('wpcs_digitalocean_new_site_created' )) {
		add_action( 'wpcs_digitalocean_module_notices', array( $this, 'wpcs_digitalocean_notice_site_creation' ) );
	}

}
	
/**
 *  Show API connectivity issue warning
 *
 *  @since  1.0.0
 */
function wpcs_digitalocean_notice_no_api_connection() {

	if ( ! get_option('wpcs_dismissed_digitalocean_api_notice' ) ) {
		?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="digitalocean_api_notice">
    		<p>
    			<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - DigitalOcean: We can\'t connect to the DigitalOcean API! There might be a temporary problem with their API or you might have added the wrong <a href="%s">API credentials</a>.', 'wp-cloud-server' ), 'admin.php?page=wp-cloud-server-admin-menu&tab=digitalocean&submenu=settings' ); ?>
	    	</p>
    	</div>
		<?php
	}

}

/**
 *  Show info message when new site created
 *
 *  @since  1.0.0
 */
function wpcs_digitalocean_notice_site_creation() {

	if ( ! get_option('wpcs_dismissed_digitalocean_site_creation_notice' ) ) {
		?>
		<div class="notice-success notice is-dismissible wpcs-notice" data-notice="digitalocean_site_creation_notice">
			<p>
				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - DigitalOcean: A New DigitalOcean Cloud Server was Successfully Created! Please visit the %s page for details.', 'wp-cloud-server' ), '<a href="?page=wp-cloud-server-admin-menu&tab=digitalocean&submenu=servers">' . __( 'DigitalOcean Server Information', 'wp-cloud-server' ) . '</a>' ); ?>
			</p>
    	</div>
		<?php
	}

}