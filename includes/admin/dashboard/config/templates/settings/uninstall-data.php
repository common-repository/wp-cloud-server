<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="content">
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'wpcs_uninstall_data' );
		wpcs_do_settings_sections( 'wpcs_uninstall_data' );
		wpcs_submit_button( __( 'Save Settings', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>