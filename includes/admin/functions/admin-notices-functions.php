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
 *  @since  1.2.1
 */
function wpcs_show_admin_notices() {
		
	$hosting_plan_save_failed = get_option( 'wpcs_dismissed_hosting_plan_save_failed_notice' );

	// Show hosting plan save error notice.
	if ( current_user_can( 'manage_options' ) && get_option('wpcs_hosting_plan_save_failed' ) && !$hosting_plan_save_failed ) {
		add_action( 'admin_notices', array( $this, 'wpcs_edd_hosting_plan_save_failed' ) );
	}

}
	
/**
 *  Display Welcome Notice
 *
 *  @since  2.0.0
 */
function wpcs_show_welcome_notices() {
		
	$module_data = get_option( 'wpcs_module_list' );

	// Show hosting plan save error notice.
	if ( current_user_can( 'manage_options' )) {
		//add_action( 'wpcs_system_notices', array( $this, 'wpcs_welcome_notice' ) );
	}

}
add_action( 'admin_init', 'wpcs_show_welcome_notices' );
	
/**
 *  Display Admin & DigitalOcean Module Admin Notices
 *
 *  @since  2.0.0
 */
function wpcs_welcome_notice() {
		
	$module_data = get_option( 'wpcs_module_list' );
	?>

	<div class="uk-alert-primary" uk-alert>
    	<a class="uk-alert-close" uk-close></a>
    	<p><?php esc_html_e( 'Welcome to the WP Cloud Server Plugin.', 'wp-cloud-server' ) ?></p>
		<div class="content">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_cloud_server_setup_wizard' );
				do_settings_sections( 'wp_cloud_server_setup_wizard' );
				submit_button( 'Next Step' );
				?>
			</form>
		</div>
	</div>

	<?php
}

/**
 *  Show info message when new site created
 *
 *  @since  1.2.1
 */
function wpcs_edd_hosting_plan_save_failed() {

	if ( ! get_option('wpcs_dismissed_hosting_plan_save_failed_notice' ) ) {
		?>
		<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="hosting_plan_save_failed_notice">
			<p>
				<?php esc_html_e( 'WP Cloud Server - Hosting Plan Settings Failed to Save (\'No Module or Server Selected\'). Please select a Module & Server and then Click Update.', 'wp-cloud-server' ) ?>	
			</p>
    	</div>
		<?php
	}
}