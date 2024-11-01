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

function wpcs_ploi_create_managed_templates_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'ploi-create-managed-server-template' !== $tabs_content ) {
		return;
	}

	$module_data	= get_option( 'wpcs_module_list' );
	$api_status		= wpcs_check_cloud_provider_api('Ploi', null, null, false);
	$attributes		= ( $api_status ) ? '' : 'disabled';
	?>

	<div class="content">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e('Create Ploi Server Template'); ?></h2>
		<p><?php _e('This page allows you to create a template for creating a Ploi server and then to install a Web Application'); ?></p>
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="ploi_managed_server_template">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Template Name:</th>
						<td>
							<input class='w-400' type='text' placeholder='Template Name' id='wpcs_ploi_server_template_name' name='wpcs_ploi_server_template_name' value=''>
							<p class="text_desc">[You can use any valid text, numeric, and space characters]</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Credentials:</th>
						<td>
							<?php
							$credentials = wpcs_ploi_api_user_request( 'server-providers/list' );
	
							?>
							<select style="width: 400px" name="wpcs_ploi_server_template_credentials" id="wpcs_ploi_server_template_credentials">
								<?php
								if ( is_array( $credentials ) ) {
									?>
									<optgroup label="Credentials">
									<?php
									foreach ( $credentials as $credential ) {
									?>
										<option value='<?php echo "{$credential['name']}|{$credential['id']}"; ?>'><?php echo $credential['name']; ?></option>
									<?php
									}
								} else {
									?>
										<option value="no_value">-- No Credentials Available --</option>
									<?php
								}
								?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Hostname:</th>
						<td>
						<?php
						$host_names	= get_option( 'wpcs_host_names' );
						?>
						<select class="w-400" name="wpcs_ploi_server_template_root_domain" id="wpcs_ploi_server_template_root_domain">
							<?php
							if ( !empty( $host_names ) ) {
							?><optgroup label="Select Hostname"><?php
							foreach ( $host_names as $key => $host_name ) {
								?>
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
						<th scope="row">Server Size:</th>
						<td>
						<?php		
						$list = wpcs_ploi_api_user_request( 'server-providers/list' );

						?>
						<select style="width: 400px" name="wpcs_ploi_server_template_size" id="wpcs_ploi_server_template_size">
							<?php
							if ( is_array( $list ) ) {
								?>
								<optgroup label="Plans">
								<?php
								foreach ( $list as $plans ) {
									foreach ( $plans['provider']['plans'] as $key => $plan ) {
								?>
									<option value='<?php echo "{$plan['name']}|{$plan['id']}"; ?>'><?php echo $plan['description']; ?></option>
								<?php
									}
								}
							} else {
								?>
									<option value="no_value">-- No Plans Available --</option>
								<?php
							}
							?>
							</optgroup>
						</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Database:</th>
							<td>
							<?php	
							$plans = wpcs_ploi_database_list();

$value = get_option( 'wpcs_ploi_server_template_size' );
?>

<select style="width: 400px" name="wpcs_ploi_server_template_database" id="wpcs_ploi_server_template_database">
	<?php
	if ( !empty( $plans ) ) {
		?>
		<optgroup label="Database">
		<?php
		foreach ( $plans as $label => $id ) {
		?>
			<option value='<?php echo "{$label}|{$id}"; ?>'><?php echo $label; ?></option>
		<?php
		}
	} else {
		?>
			<option value="no_value">-- No Databases Settings Available --</option>
		<?php
	}
	?>
	</optgroup>
</select>
							</td>
					</tr>
					<tr>
						<th scope="row">PHP Version:</th>
						<td>
							<select style="width: 400px" name="wpcs_ploi_server_template_php_version" id="wpcs_ploi_server_template_php_version">
								<option value="none"><?php esc_html_e( '-- No PHP Version Installed --', 'wp-cloud-server-ploi' ); ?></option>
								<option value="8.0"><?php esc_html_e( 'PHP 8.0', 'wp-cloud-server-ploi' ); ?></option>
								<option value="7.4"><?php esc_html_e( 'PHP 7.4', 'wp-cloud-server-ploi' ); ?></option>
            					<option value="7.3"><?php esc_html_e( 'PHP 7.3', 'wp-cloud-server-ploi' ); ?></option>
            					<option value="7.2"><?php esc_html_e( 'PHP 7.2', 'wp-cloud-server-ploi' ); ?></option>
            					<option value="7.1"><?php esc_html_e( 'PHP 7.1', 'wp-cloud-server-ploi' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server Region:</th>
						<td>
						<?php
						$list = wpcs_ploi_api_user_request( 'server-providers/list' );

		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_region" id="wpcs_ploi_server_template_region">
			<?php
			if ( is_array( $list ) ) {
				?>
				<optgroup label="Regions">
				<?php
				foreach ( $list as $plans ) {
					foreach ( $plans['provider']['regions'] as $key => $plan ) {
				?>
    				<option value='<?php echo "{$plan['name']}|{$plan['id']}"; ?>'><?php echo $plan['name']; ?></option>
				<?php
					}
				}
			} else {
				?>
    				<option value="no_value">-- No Regions Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>					
						</td>
					</tr>
					<tr>
						<th scope="row">Server Type:</th>
						<td>
							<select style="width: 400px" name="wpcs_ploi_server_template_type" id="wpcs_ploi_server_template_type">
								<option value="Server|server"><?php esc_html_e( 'Server', 'wp-cloud-server-ploi' ); ?></option>
								<option value="Load Balancer|load-balancer"><?php esc_html_e( 'Load Balancer', 'wp-cloud-server-ploi' ); ?></option>
								<option value="Database Server|database-server"><?php esc_html_e( 'Database Server', 'wp-cloud-server-ploi' ); ?></option>
								<option value="Redis Server|redis-server"><?php esc_html_e( 'Redis Server', 'wp-cloud-server-ploi' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server Webserver:</th>
						<td>
							<select style="width: 400px" name="wpcs_ploi_server_template_webserver" id="wpcs_ploi_server_template_webserver">
								<option value="nginx"><?php esc_html_e( 'NGINX', 'wp-cloud-server-ploi' ); ?></option>
							</select>
						</td>
					</tr>
					</tbody>
			</table>
			<h2 class="uk-heading-divider">Install Application on Server</h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Application:</th>
						<td>
						<select style="width: 400px" name="wpcs_ploi_server_template_install_app" id="wpcs_ploi_server_template_install_app">
						<optgroup label="Select Application">
							<option value="no-application|no-app|No Application"><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
							<option value="wordpress|app|WordPress"><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
							<option value="nextcloud|app|Nextcloud"><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
							<?php
							$git_repos = wpcs_github_repos_list();
							if ( !empty( $git_repos ) ) {
								?>
								</optgroup>
								<optgroup label="Select GIT Repository">
									<?php
									foreach ( $git_repos as $key => $git_repo ) {
										?>
										<option value='<?php echo "{$key}|git|{$git_repo}"; ?>' <?php selected( $value, $key );?>><?php echo $git_repo; ?></option>
										<?php
									}
							}
							?>
						</optgroup>
						</select>
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
			<?php wpcs_submit_button( 'Save Server Template', 'secondary', 'save_server_template', false, $attributes ); ?>
		</form>
	</div>
<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_create_managed_templates_template', 10, 3 );