<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$request_delete = get_option( 'wpcs_delete_logged_data' );
		
$data = array();
		
if ( $request_delete == '1' ) {
			
	// Reset the Logged Data Array
	update_option( 'wpcs_aws_lightsail_logged_data', $data );
	update_option( 'wpcs_cloudways_logged_data', $data );
	update_option( 'wpcs_digitalocean_logged_data', $data );
	update_option( 'wpcs_linode_logged_data', $data );
	update_option( 'wpcs_runcloud_logged_data', $data );
	update_option( 'wpcs_serverpilot_logged_data', $data );
	update_option( 'wpcs_upcloud_logged_data', $data );
	update_option( 'wpcs_vultr_logged_data', $data );
			
	// Allow add-on modules to add functionality triggered by the log reset event
	do_action( 'wpcs_reset_logged_data', $request_delete );
			
	// Reset the Delete Logged Data checkboxes
	update_option( 'wpcs_delete_logged_data', '0' );
}

?>

<div class="content">
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'wp_cloud_server_log_settings' );
		wpcs_do_settings_sections( 'wp_cloud_server_log_settings' );
		wpcs_submit_button( __( 'Confirm Delete', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>
