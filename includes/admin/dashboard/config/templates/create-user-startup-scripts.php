<?php

/**
 * Provide a Admin Area SSH Key Page for the Digitalocean Module
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

if ( true ) {

	$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
	$sp_response = '';
	$server_script = '';
	$debug_data['api'] = 'no';
	$startup_scripts = array();
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php 
			settings_fields( 'wpcs_startup_script' );
			wpcs_do_settings_sections( 'wpcs_startup_script' );
			?>
			<hr>
			<?php
			wpcs_submit_button( 'Save Startup Script', 'secondary', 'add-startup-script' );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_startup_script_name' ) ) {

		$startup_script_name	= get_option( 'wpcs_startup_script_name' );
		$startup_script_summary	= get_option( 'wpcs_startup_script_summary' );
		$startup_script_value	= get_option( 'wpcs_startup_script' );
		
		// Set-up the data for the new Droplet
		$startup_script_data = array(
			"name"				=>  $startup_script_name,
			"summary"			=>  $startup_script_summary,			
			"startup_script"	=>  $startup_script_value, 
		);

		// Retrieve the Active Module List
		$startup_scripts		= get_option( 'wpcs_startup_scripts' );
			
		// Save the VPS Template for use with a Plan
		$startup_scripts[ $startup_script_data['name'] ] = $startup_script_data;
			
		update_option( 'wpcs_startup_scripts', $startup_scripts );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_startup_script_name' );
	delete_option( 'wpcs_startup_script_summary' );
	delete_option( 'wpcs_startup_script' );

} else {

	if ( ! wpcs_check_cloud_provider() ){
		?>
		<div class="notice-error user-notice clear">
			<p><?php esc_html_e( 'Sorry! The "Add Template" option is only available with a Healthy DigitalOcean API Connection!', 'wp-cloud-server' ); ?></p>
		</div>
		<?php
	} else {
		?>
		<div class="notice-error user-notice clear">
			<p><?php esc_html_e( 'Sorry! You cannot access this page!', 'wp-cloud-server' ); ?></p>
		</div>
		<?php
	}
}