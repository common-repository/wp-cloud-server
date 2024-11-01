<?php

function wpcs_ploi_settings_template ( $tabs_content ) {
	
	if ( 'ploi-settings' !== $tabs_content ) {
		return;
	}
				
	$nonce		= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
	$reset_api	= isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
	
	if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'ploi_settings_nonce' ) ) ) {
		delete_option( 'wpcs_ploi_api_key' );
		echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
	}
	?>

		<div class="content">
			<form method="post" action="options.php">
				<?php 
				settings_fields( 'wpcs_ploi_admin_menu' );
				wpcs_do_settings_sections( 'wpcs_ploi_admin_menu' ); 
				wpcs_submit_button( 'Save Settings', 'secondary', 'create_api' );
				?>
			</form>
		</div>
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=settings&resetapi=true' ), 'ploi_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Ploi API Credentials', 'wp-cloud-server' ) ?></a>
		</p>

<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_settings_template' );