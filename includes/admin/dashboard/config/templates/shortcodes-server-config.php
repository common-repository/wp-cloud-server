<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="content">
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">   
		<input type="hidden" name="action" value="handle_shortcodes_server_config">
		<input type="hidden" name="wpcs_server_shortcodes_action" value="update">
        <?php
        wp_nonce_field( 'wpcs_server_shortcodes_nonce', 'wpcs_server_shortcodes_nonce' );
        wpcs_do_settings_sections( 'wp_cloud_server_shortcodes_settings' );
		wpcs_submit_button( __( 'Save Settings', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>
<?php	