<?php

function wpcs_aws_lightsail_settings_template (  $tabs_content, $page_content, $page_id  ) {
	
	if ( 'aws-lightsail-settings' !== $tabs_content ) {
		return;
	}
				
	$nonce		= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
	$reset_api	= isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
	$module		= isset( $_GET['module'] ) ? $_GET['module'] : '';
	
	if ( ( 'true' == $reset_api ) && ( 'aws_lightsail' == $module ) && ( current_user_can( 'manage_options' ) ) ) {
		delete_option( 'wpcs_aws_lightsail_api_token' );
		delete_option( 'wpcs_aws_lightsail_api_secret_key' );
		delete_option( 'wpcs_dismissed_aws_lightsail_api_notice' );

		// Delete the API Health Transient so API Health is rechecked
		delete_transient( 'wpcs_aws_lightsail_api_health' );

		echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
	}
	?>

		<div class="content">
			<form method="post" action="options.php">
				<?php 
				wpcs_settings_fields( 'wpcs_aws_lightsail_admin_menu', 'aws_lightsail' );
				wpcs_do_settings_sections( 'wpcs_aws_lightsail_admin_menu' );
				wpcs_submit_button( 'Save Settings', 'secondary', 'create_aws_lightsail_api' );
				?>
			</form>
		</div>
		<p>
			<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&module=aws_lightsail&resetapi=true' ), 'aws_lightsail_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset AWS Lightsail API Credentials', 'wp-cloud-server' ) ?></a>
		</p>

<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_aws_lightsail_settings_template', 10, 3 );