<?php
function wpcs_upcloud_settings_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'upcloud-settings' !== $tabs_content ) {
		return;
	}
				
	$nonce		= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
	$reset_api	= isset( $_GET['resetapi'] ) ? sanitize_text_field( $_GET['resetapi'] ) : '';
	$module		= isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';
	
	if ( ( wp_verify_nonce( $nonce, 'upcloud_settings_nonce' ) ) && ( 'upcloud' == $module ) && ( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) ) {
		delete_option( 'wpcs_upcloud_user_name' );
		delete_option( 'wpcs_upcloud_password' );
		delete_option( 'wpcs_dismissed_upcloud_api_notice' );

		// Delete the API Health Transient so API Health is rechecked
		delete_transient( 'wpcs_upcloud_api_health' );
		echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
	}
	?>

		<div class="content">
			<form method="post" action="options.php">
				<?php 
				wpcs_settings_fields( 'wpcs_upcloud_admin_menu', 'upcloud' );
				wpcs_do_settings_sections( 'wpcs_upcloud_admin_menu' );
				wpcs_submit_button( 'Save Settings', 'secondary', 'create_upcloud_api' );
				?>
			</form>
		</div>
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&module=upcloud&resetapi=true' ), 'upcloud_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset UpCloud API Credentials', 'wp-cloud-server' ) ?></a>
		</p>

<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_upcloud_settings_template', 10, 3 );