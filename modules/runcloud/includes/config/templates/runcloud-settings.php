<?php

function wpcs_runcloud_settings_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-settings' !== $tabs_content ) {
		return;
	}
				
	$nonce		= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
	$reset_api	= isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
	
	if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'runcloud_settings_nonce' ) ) ) {
		delete_option( 'wpcs_runcloud_api_key' );
		delete_option( 'wpcs_runcloud_api_secret' );
		delete_option( 'wpcs_dismissed_runcloud_api_notice' );

		// Delete the API Health Transient so API Health is rechecked
		delete_transient( 'wpcs_runcloud_api_health' );
		echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
	}
	?>

		<div class="content">
			<form method="post" action="options.php">
				<?php 
				wpcs_settings_fields( 'wpcs_runcloud_admin_menu', 'runcloud' );
				wpcs_do_settings_sections( 'wpcs_runcloud_admin_menu' );
				wpcs_submit_button( 'Save Settings', 'secondary', 'create_runcloud_api' );
				?>
			</form>
		</div>
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&module=runcloud&resetapi=true' ), 'runcloud_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset RunCloud API Credentials', 'wp-cloud-server' ) ?></a>
		</p>

<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_settings_template', 10, 3 );