<?php

/**
 * Provide a admin area servers view for the ploi module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_ploi_create_managed_site_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'ploi-create-managed-site-template' !== $tabs_content ) {
		return;
	}

	$module_data	= get_option( 'wpcs_module_list' );
	$api_status		= wpcs_check_cloud_provider_api('Ploi', null, null, false);
	$attributes		= ( $api_status ) ? '' : 'disabled';
	$host_names		= get_option( 'wpcs_host_names' );
	?>

	<div class="content">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e('Create Ploi Site Template'); ?></h2>
		<p><?php _e('This page allows you to create a template for creating a site on an existing Ploi server.'); ?></p>
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="ploi_managed_site_template">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Template Name:</th>
						<td>
							<input class='w-400' type='text' placeholder='Template Name' id='wpcs_ploi_create_app_template_name' name='wpcs_ploi_create_app_template_name' value=''>
							<p class="text_desc">[You can use any valid text, numeric, and space characters]</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Root Domain:</th>
						<td>
							<select class="w-400" name="wpcs_ploi_create_app_template_root_domain" id="wpcs_ploi_create_app_template_root_domain">
								<?php if ( !empty( $host_names ) ) { ?>
								<optgroup label="Select Hostname">
									<?php foreach ( $host_names as $key => $host_name ) { ?>
            						<option value='<?php echo "{$host_name['label']}|{$host_name['hostname']}" ?>'><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
									<?php } } ?>
								</optgroup>
								<optgroup label="User Choice">
									<option value="[Customer Input]|[Customer Input]"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server:</th>
						<td>
						<?php		
						$servers	= wpcs_ploi_call_api_list_servers();

						if ( isset( $servers[0]['id'] ) ) {
							$args['server_id'] = $servers[0]['id'];
						}
						?>
						<select style='width: 400px' name="wpcs_ploi_create_app_template_server_id" id="wpcs_ploi_create_app_template_server_id">
							<optgroup label="Servers">
							<?php
							if ( ( isset( $servers ) ) && is_array( $servers ) ) {
								foreach ( $servers as $server ) {
								?>
									<option value='<?php echo "{$server['name']}|{$server['id']}"; ?>'><?php echo $server['name']; ?></option>
								<?php
								}
							} else {
								?>
								<option value="not_available">-- No Servers Available --</option>
							<?php
							}
							?>
							</optgroup>
						</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Project Directory:</th>
						<td>
							<input class='w-400' type='text' placeholder='/' id='wpcs_ploi_create_app_template_project_directory' name='wpcs_ploi_create_app_template_project_directory' value=''>
						</td>
					</tr>
					<tr>
						<th scope="row">Web Directory:</th>
						<td>
							<input class='w-400' type='text' placeholder='/public' id='wpcs_ploi_create_app_template_web_directory' name='wpcs_ploi_create_app_template_web_directory' value=''>
						</td>
					</tr>
					<tr>
						<th scope="row">System User:</th>
						<td>
							<?php
							if ( isset( $args ) ) {
								$users = wpcs_ploi_api_system_users_request( 'system-users/list', $args );
							}
							?>
							<select class='w-400' name="wpcs_ploi_create_app_template_system_user" id="wpcs_ploi_create_app_template_system_user">
								<optgroup label="System User">
									<?php
									if ( ( isset( $users ) ) && is_array( $users ) ) {
										?><option value="ploi">ploi</option><?php
										foreach ( $users as $user ) {
											?>
											<option value='<?php echo "{$user['name']}"; ?>'><?php echo $user['name']; ?></option>
											<?php
										}
									} else {
									?>
									<option value="ploi">ploi</option>
									<?php
									}
									?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Web Template:</th>
						<td>
							<?php $webtemplates	= wpcs_ploi_api_web_templates_request( 'templates/list' ); ?>
							<select class='w-400' name="wpcs_ploi_app_template_web_template" id="wpcs_ploi_app_template_web_template">
								<optgroup label="Web Templates">
							<?php
							if ( ( ! empty( $webtemplates ) ) && is_array( $webtemplates ) ) {
								foreach ( $webtemplates as $webtemplate ) {
								?>
									<option value='<?php echo "{$webtemplate['label']}|{$webtemplate['id']}"; ?>'><?php echo $webtemplate['label']; ?></option>
								<?php
								}
							} else {
								?>
								<option value="not_available">-- No Web Templates Available --</option>
							<?php
							}
							?>
							</optgroup>
						</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Select Application:</th>
						<?php $git_repos = wpcs_github_repos_list(); ?>
						<td>
							<select style="width: 400px" name="wpcs_ploi_create_app_template_install_app" id="wpcs_ploi_create_app_template_install_app">
								<optgroup  label="Select Application">
								<option value="no-application|no-app|No Application"><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
								<option value="wordpress|app|WordPress"><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
								<option value="nextcloud|app|Nextcloud"><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
								<?php if ( !empty( $git_repos ) ) { ?>
								</optgroup>
								<optgroup label="Select GIT Repository">
									<?php foreach ( $git_repos as $key => $git_repo ) {
										?>
										<option value='<?php echo "{$key}|git|{$git_repo}"; ?>'><?php echo $git_repo; ?></option>
										<?php
									}
								}
								?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Install SSL Certifcate:</th>
						<td>
							<input name="wpcs_ploi_create_app_template_enable_ssl" id="wpcs_ploi_create_app_template_enable_ssl" type="checkbox" value="1" class="code"/>
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
			<?php wpcs_submit_button( 'Save Site Template', 'secondary', 'save_site_template', false, $attributes ); ?>
		</form>
	</div>
<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_create_managed_site_template', 10, 3 );