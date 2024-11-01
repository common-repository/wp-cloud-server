<?php

function wpcs_linode_settings_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-settings' !== $tabs_content ) {
		return;
	}
				
	$nonce		= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
	$reset_api	= isset( $_GET['resetapi'] ) ? sanitize_text_field( $_GET['resetapi'] ) : '';
	$module		= isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';
	
	if ( ( wp_verify_nonce( $nonce, 'linode_settings_nonce' ) ) && ( 'linode' == $module ) && ( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) ) {
		delete_option( 'wpcs_linode_api_token' );
		delete_option( 'wpcs_dismissed_linode_api_notice' );

		// Delete the API Health Transient so API Health is rechecked
		delete_transient( 'wpcs_linode_api_health' );
		echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
	}
	?>

		<div class="content">
			<form method="post" action="options.php">
				<?php 
				wpcs_settings_fields( 'wpcs_linode_admin_menu', 'linode' );
				wpcs_do_settings_sections( 'wpcs_linode_admin_menu' );
				wpcs_submit_button( 'Save Settings', 'secondary', 'create_linode_api' );
				?>
			</form>
		</div>
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&module=linode&resetapi=true' ), 'linode_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Linode API Credentials', 'wp-cloud-server' ) ?></a>
		</p>

<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_settings_template', 10, 3 );