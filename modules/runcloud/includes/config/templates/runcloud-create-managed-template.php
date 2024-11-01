<?php

/**
 * Provide a admin area servers view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_runcloud_create_managed_templates_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-create-managed-template' !== $tabs_content ) {
		return;
	}

	$nonce			= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

	$module_name	=  'RunCloud';
	
	$module_data	= get_option( 'wpcs_module_list' );

	$api_status		= wpcs_check_cloud_provider_api('RunCloud');
	$attributes		= ( $api_status ) ? '' : 'disabled';

	$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
	$sp_response	= '';
	$server_script	= '';
	?>

	<div class="content">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e('Create RunCloud Server Template'); ?></h2>
		<p><?php _e('This page allows you to create a template for creating a RunCloud server and then to install a Web Application'); ?></p>
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="runcloud_managed_server_template">
			<?php wp_nonce_field( 'wpcs_runcloud_managed_server_template', 'wpcs_runcloud_managed_server_template' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Template Name:</th>
						<td>
							<input class='w-400' type='text' placeholder='Template Name' id='wpcs_serverpilot_template_name' name='wpcs_serverpilot_template_name' value=''>
							<p class="text_desc">[You can use any valid text, numeric, and space characters]</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Hostname:</th>
						<td>
							<?php
							$host_names	= get_option( 'wpcs_host_names' );
							?>
							<select class="w-400" name="wpcs_serverpilot_template_host_name" id="wpcs_serverpilot_template_host_name">
								<optgroup label="Select Hostname">
									<?php
									if ( !empty( $host_names ) ) {
										foreach ( $host_names as $key => $host_name ) {
										?>
            							<option value="<?php echo "{$host_name['hostname']}|{$host_name['label']}"; ?>"><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
										<?php } } ?>
								</optgroup>
								<optgroup label="User Choice">
									<option value="[Customer Input]|[Customer Input]"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Cloud Provider:</th>
						<td>		
							<select class='w-400' name="wpcs_serverpilot_template_module" id="wpcs_serverpilot_template_module">
								<optgroup label="Select Cloud Provider">
								<?php
								$cloud_active	= wpcs_check_cloud_provider_api();
								if ( $cloud_active ) {
									foreach ( $module_data as $key => $module ) { 
										if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) && ( wpcs_check_cloud_provider_api( $key ) ) ) {
										?>
            							<option value="<?php echo $key ?>"><?php echo $key ?></option>
										<?php 
										}
									}
								} else {
									?>
									<optgroup label="Select Cloud Provider">
            						<option value="DigitalOcean"><?php esc_html_e( 'DigitalOcean', 'wp-cloud-server' ); ?></option>
									<?php 
								}
								?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server Image:</th>
							<td>		
								<select class='w-400' name="wpcs_serverpilot_template_type" id="wpcs_serverpilot_template_type">
									<optgroup label="Select Image">
										<option value="Ubuntu 20.04 x64"><?php _e( 'Ubuntu 20.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
            							<option value="Ubuntu 18.04 x64"><?php _e( 'Ubuntu 18.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
									<optgroup label="Select Cloud Provider">
								</select>
							</td>
					</tr>
					<tr>
						<th scope="row">Server Region:</th>
						<td>
							<select class='w-400' name="wpcs_serverpilot_template_region" id="wpcs_serverpilot_template_region">
								<optgroup label="Select Region">
								<?php
								$regions = call_user_func("wpcs_digitalocean_regions_list");
								if ( $regions ) {
            						foreach ( $regions as $key => $region ){
										$value = "{$region['name']}|{$key}";
										?>
                						<option value="<?php echo $value; ?>"><?php echo $region['name']; ?></option>
										<?php
									}
								}
								?>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server Size:</th>
						<td>
							<select class='w-400' name="wpcs_serverpilot_template_size" id="wpcs_serverpilot_template_size">
								<?php
								$plans = call_user_func("wpcs_digitalocean_plans_list");
								if ( !empty( $plans ) ) {
									foreach ( $plans as $key => $type ){ ?>
										<optgroup label='<?php echo $key ?>'>";
            							<?php foreach ( $type as $key => $plan ){
										$value = "{$plan['name']}|{$key}";
										?>
    										<option value="<?php echo $value; ?>"><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
										<?php
										}
									}
								}
								?>
								</optgroup>
							</select>					
						</td>
					</tr>
					<tr>
						<th scope="row">SSH Key:</th>
						<td>
							<?php
							$value		 = get_option( 'wpcs_serverpilot_template_ssh_key' );
							$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
							?>
							<select class="w-400" name="wpcs_serverpilot_template_ssh_key" id="wpcs_serverpilot_template_ssh_key">
								<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
								<?php if ( !empty( $serverpilot_ssh_keys ) ) { ?>
								<optgroup label="Select SSH Key">
									<?php foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) {
            						echo "<option value='{$ssh_key['name']}'>{$ssh_key['name']}</option>";
									} ?>
								</optgroup>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Select System User:</th>
						<td>
							<select class="w-400" name="wpcs_serverpilot_template_system_user" id="wpcs_serverpilot_template_system_user">
								<option value=""><?php _e( '-- Create New System User --', 'wp-cloud-server' ); ?></option>
								<optgroup label="Select System User">
									<option value="runcloud"><?php _e( 'RunCloud', 'wp-cloud-server' ); ?></option>
									<option value="root"><?php _e( 'Root', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">New System User Name:</th>
						<td>
							<input class='w-400' type='text' placeholder='username' id='wpcs_serverpilot_template_system_user_name' name='wpcs_serverpilot_template_system_user_name' value=''>
						</td>
					</tr>
					<tr>
						<th scope="row">New System User Password:</th>
						<td>
							<input class='w-400' type='password' placeholder='********' id='wpcs_serverpilot_template_system_user_password' name='wpcs_serverpilot_template_system_user_password' value=''>
						</td>
					</tr>
					<tr>
					<th scope="row">Install PHP Script:</th>
					<td>		
						<select class='w-400' name="wpcs_serverpilot_template_install_app" id="wpcs_serverpilot_template_install_app">
							<optgroup label="Select Web Application">
								<option value="false"><?php _e( '-- No Web Application --', 'wp-cloud-server' ); ?></option>
            					<option value="wordpress"><?php _e( 'WordPress', 'wp-cloud-server' ); ?></option>
							</optgroup>
						</select>
					</td>
					</tr>
					<tr>
						<th scope="row">Default Application:</th>
						<td>
							<input class="uk-input" type="checkbox" id="wpcs_serverpilot_template_default_app" name="wpcs_serverpilot_template_default_app" value="1">
						</td>
					</tr>
					<tr>
						<th scope="row">Enable Server Backups:</th>
						<td>
							<input class="uk-input" type="checkbox" id="wpcs_serverpilot_template_enable_backups" name="wpcs_serverpilot_template_enable_backups" value="1">
						</td>
					</tr>
				</tbody>
			</table>
			<table>
				
			</table>
			<hr>
			<?php wpcs_submit_button( 'Create Template', 'secondary', 'create_template', false, $attributes ); ?>
		</form>
<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_create_managed_templates_template', 10, 3 );