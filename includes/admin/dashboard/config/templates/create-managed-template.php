<?php

/**
 * Provide a Admin Area Add Template Page for the ServerPilot Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce			= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

//$module_name 	=  WP_Cloud_Server_ServerPilot_Settings::wpcs_serverpilot_module_name();

$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
$attributes		= ( $api_status ) ? '' : 'disabled';

//if ( wp_verify_nonce( $nonce, 'add_template_nonce' ) && wpcs_check_cloud_provider( $module_name ) ) {
//if ( true ) {

	$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
	$sp_response = '';
	$server_script = '';
	?>

	<div class="content">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e('Create ServerPilot Template'); ?></h2>
		<p><?php _e('Enter your license key below and click \'Save Settings\', then click \'Activate\'. This will then give you access to automatic updates and full support!'); ?></p>
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="handle_managed_template_options">
			<?php wp_nonce_field( 'wpcs_handle_managed_template_options', 'wpcs_handle_managed_template_options' ); ?>
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
						<th scope="row">Cloud Provider:</th>
						<td>		
							<select class='w-400' name="wpcs_serverpilot_template_module" id="wpcs_serverpilot_template_module">
								<?php
								$cloud_active	= wpcs_check_cloud_provider_api();
								if ( $cloud_active ) {
									?><optgroup label='Select Cloud Provider'><?php
									foreach ( $module_data as $key => $module ) { 
										if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) && ( wpcs_check_cloud_provider_api( $key ) ) ) {
										?>
            							<option value="<?php echo $key ?>"><?php echo $key ?></option>
										<?php 
										}
									}
									?></optgroup><?php
								} else {
									?>
									<optgroup label='Select Cloud Provider'>
            							<option value="DigitalOcean">DigitalOcean</option>
									</optgroup>
									<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Server Image:</th>
							<td>		
								<select class='w-400' name="wpcs_serverpilot_template_type" id="wpcs_serverpilot_template_type">
									<optgroup label='Select Image'>
										<option value="Ubuntu 20.04 x64|Ubuntu 20.04 x64"><?php _e( 'Ubuntu 20.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
            							<option value="Ubuntu 18.04 x64|Ubuntu 18.04 x64"><?php _e( 'Ubuntu 18.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
									</optgroup>
								</select>
							</td>
					</tr>
					<tr>
						<th scope="row">Server Region:</th>
						<td>
							<select class='w-400' name="wpcs_serverpilot_template_region" id="wpcs_serverpilot_template_region">
							<?php
							$regions = call_user_func("wpcs_digitalocean_regions_list");
							if ( $regions ) {
								?><optgroup label='Select Region'><?php
            					foreach ( $regions as $key => $region ){
									$value = "{$region['name']}|{$key}";
									?>
                					<option value="<?php echo $value; ?>"><?php echo $region['name']; ?></option>
									<?php
								}
								?></optgroup><?php
							}
							?>
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
						<th scope="row">ServerPilot Plan:</th>
						<td>		
							<select class='w-400' name="wpcs_serverpilot_template_plan" id="wpcs_serverpilot_template_plan">
								<optgroup label='Select ServerPilot Plan'>
            						<option value="economy"><?php _e( 'Economy ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
            						<option value="business"><?php _e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
									<option value="first_class"><?php _e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Select Admin SSH Key:</th>
						<td>
							<select class='w-400' name="wpcs_serverpilot_template_ssh_key" id="wpcs_serverpilot_template_ssh_key">
								<option value="no-ssh-key">-- No SSH Key --</option>
							<?php
							$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
							if ( $serverpilot_ssh_keys ) {
								?><optgroup label='Select SSH Key'><?php
								foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) {
								?>
            					<option value='<?php echo $ssh_key['name']; ?>'><?php echo $ssh_key['name']; ?></option>
								<?php
								}
								?></optgroup><?php
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">Enable Server Backups:</th>
						<td>
							<input class="uk-input" type="checkbox" id="wpcs_serverpilot_template_enable_backups" name="wpcs_serverpilot_template_enable_backups" value="1">
						</td>
					</tr>
					<tr>
						<th scope="row">Enable AutoSSL Queue:</th>
						<td>
							<input class="uk-input" type="checkbox" id="wpcs_serverpilot_template_autossl" name="wpcs_serverpilot_template_autossl" value="1">
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
			<?php wpcs_submit_button( 'Create Template', 'secondary', 'create_template', false, $attributes ); ?>
		</form>
<?php

//}