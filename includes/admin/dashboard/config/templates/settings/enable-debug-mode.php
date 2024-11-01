<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="content">
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'wp_cloud_server_general_settings' );
		wpcs_do_settings_sections( 'wp_cloud_server_general_settings' );
		wpcs_submit_button( __( 'Save Settings', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>