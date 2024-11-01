<?php

function wpcs_vultr_license_settings_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-license-settings' !== $tabs_content ) {
		return;
	}
	$license = get_option( 'wpcs_vultr_module_license_key' );
	$status  = get_option( 'wpcs_vultr_module_license_active' );
	
	?>
	<div class="content">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e('Vultr Module License'); ?></h2>
		<p>Enter your license key below and click 'Save Settings', then click 'Activate'. This will then give you access to automatic
		updates and full support!</p>
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="activate_vultr_license">
			<?php wp_nonce_field( 'wpcs_activate_license_nonce', 'wpcs_activate_license_nonce' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key:'); ?>
						</th>
						<td>
							<input class="w-400"id="wpcs_vultr_module_license_key" name="wpcs_vultr_module_license_key" type="text" value="<?php esc_attr_e( $license ); ?>" />
							<?php echo '<p class="text_desc" >[Enter your license key provided in your welcome email]</p>'; ?>
						</td>
					</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('License Status:'); ?>
							</th>
							<td>
								<?php if ( is_object($status) && $status->license == 'valid' ) { ?>
								<span style="color: green;"><?php _e('Active'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e(''); ?>
							</th>
							<td>
								
									<?php wp_nonce_field( 'wpcs_vultr_module_license_key-nonce', 'wpcs_vultr_module_license_key-nonce' ); ?>
									<input type="submit" class="button-secondary" name="wpcs_vultr_module_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
						
							</td>
						</tr>
								<?php } else { ?>
								<span style="color: #ccc;"><?php _e('Inactive'); ?></span>
							</td>
						</tr>
								<?php } ?>

				</tbody>
			</table>
			<hr>
			<?php wpcs_submit_button( 'Save License Key', 'secondary' ); ?>

		</form>
<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_license_settings_template', 10, 3 );