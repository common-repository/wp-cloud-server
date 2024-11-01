<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';

if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'do_settings_nonce' ) ) ) {
	delete_option( 'wpcs_digitalocean_api_token' );
	delete_option( 'wpcs_dismissed_digitalocean_api_notice' );

	// Delete the API Health Transient so API Health is rechecked
	delete_transient( 'wpcs_digitalocean_api_health' );

	echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
}
?>

<div class="content">
	<form method="post" action="options.php">
		<?php 
		wpcs_settings_fields( 'wpcs_digitalocean_admin_menu', 'digitalocean' );
		wpcs_do_settings_sections( 'wpcs_digitalocean_admin_menu' );
		wpcs_submit_button( 'Save Settings', 'secondary', 'create_digitalocean_api' );
		?>
	</form>
</div>
<p>
	<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=digitalocean&submenu=settings&resetapi=true' ), 'do_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset DigitalOcean API Credentials', 'wp-cloud-server' ) ?></a>
</p>