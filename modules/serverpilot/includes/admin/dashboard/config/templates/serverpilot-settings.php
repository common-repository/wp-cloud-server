<?php

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';
$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
			
$reset_api_complete = get_option( 'wpcs_sp_reset_api_complete' );

if ( ( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'settings_nonce' ) ) ) {
	update_option( 'wpcs_sp_reset_api_complete', 'true' );
	delete_option( 'wpcs_sp_api_account_id' );
	delete_option( 'wpcs_sp_api_key' );
	delete_option( 'wpcs_dismissed_sp_api_notice' );

	// Delete the API Health Transient so API Health is rechecked
	delete_transient( 'wpcs_sp_api_health' );
					
	// These need to be removed
	update_option( 'wpcs_setting_up', false );
	echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
}
?>
	

	<div class="content">
		<form method="post" action="options.php">
			<input type="hidden" id="wpcs_sp_api_redirect_enable" name="wpcs_sp_api_redirect_enable" value="<?php echo $page['position']; ?>">
				<?php
				wpcs_settings_fields( 'wpcs_sp_admin_menu', 'serverpilot' );
				wpcs_do_settings_sections( 'wpcs_sp_admin_menu' );
				wpcs_submit_button( 'Save Settings', 'secondary', 'api_setting' );
				?>
		</form>
				
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( "admin.php?page=wp-cloud-server-admin-menu&resetapi=true"), 'settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset ServerPilot API Settings', 'wp-cloud-server' ) ?></a>
		</p>
						
	</div>