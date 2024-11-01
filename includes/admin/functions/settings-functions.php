<?php

/**
 * The Settings Functions.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Add link to settings on plugin list
 *
 *  @since 1.0.0
 *  @param array  $links links to show below plugin name
 */
function wpcs_create_server_script_variable_settings( $module ) {

	// Create the variable settings
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_domain_name" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_wp_site_title" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_wp_db_user" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_wp_database" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_admin_user" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_admin_passwd" );
	register_setting( "wpcs_{$module}_create_server", "wpcs_{$module}_server_variable_admin_email" );

	// Display the variables table
	?>
	<ul uk-accordion>
    	<li>
        	<a class="uk-accordion-title" href="#">Advanced Settings</a>
        	<div class="uk-accordion-content">
				<p>The values entered below will only be used if a startup script is selected which has the appropriate variable placeholders in place!
				The placeholders will be substituted for the values below before deploying the server!</p>
				<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row"><?php esc_html_e( 'Domain Name:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='domain.com' id='<?php echo "wpcs_{$module}_server_variable_domain_name" ?>' name='wpcs_{$module}_server_variable_domain_name" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Site Title:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='My New Website' id=<?php echo "wpcs_{$module}_server_variable_wp_site_title" ?>' name='<?php echo "wpcs_{$module}_server_variable_wp_site_title" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Database Name:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='wp-dbasename' id='<?php echo "wpcs_{$module}_server_variable_wp_database" ?>' name='<?php echo "wpcs_{$module}_server_variable_wp_database" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Database User:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='username' id='<?php echo "wpcs_{$module}_server_variable_wp_db_user" ?>' name='<?php echo "wpcs_{$module}_server_variable_wp_db_user" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Admin User:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='wp-admin' id='<?php echo "wpcs_{$module}_server_variable_admin_user" ?>' name='<?php echo "wpcs_{$module}_server_variable_admin_user" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Admin Password:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='*******' id='<?php echo "wpcs_{$module}_server_variable_admin_passwd" ?>' name='<?php echo "wpcs_{$module}_server_variable_admin_passwd" ?>' value=''>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Admin Email:', 'wp-cloud-server' ); ?></th>
									<td>
									<input style='width: 25rem;' type='text' placeholder='admin@domain.com' id='<?php echo "wpcs_{$module}_server_variable_admin_email" ?>' name='<?php echo "wpcs_{$module}_server_variable_admin_email" ?>' value=''>
									</td>
								</tr>
							</tbody>
						</table>
        			</div>
    			</li>
			</ul>
			<?php
}