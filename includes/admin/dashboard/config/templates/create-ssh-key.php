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

	$debug_enabled			= get_option( 'wpcs_enable_debug_mode' );
	$sp_response			= '';
	$server_script			= '';
	$debug_data['api']		= 'no';
	$ssh_keys				= array();
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php 
			settings_fields( 'wpcs_ssh_key' );
			wpcs_do_settings_sections( 'wpcs_ssh_key' );
			?>
			<hr>
			<?php
			wpcs_submit_button( 'Save SSH Key', 'secondary', 'add-ssh-key' );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_ssh_key_name' ) ) {

		$ssh_key_name					= get_option( 'wpcs_ssh_key_name' );	
		$ssh_key_value					= get_option( 'wpcs_ssh_key' );
		
		// Set-up the data for the new Droplet
		$ssh_key_data = array(
			"name"			=>  $ssh_key_name,
			"public_key"	=>  $ssh_key_value, 
		);

		// Retrieve the Active Module List
		$ssh_keys		= get_option( 'wpcs_serverpilots_ssh_keys' );
		$content		= explode(' ', $ssh_key_data['public_key'], 3);
		$fingerprint	= join(':', str_split(md5(base64_decode($content[1])), 2)) . "\n\n";
			
		$ssh_key_data['fingerprint'] = $fingerprint;
			
		// Save the VPS Template for use with a Plan
		$ssh_keys[ $ssh_key_data['name'] ] = $ssh_key_data;
			
		update_option( 'wpcs_serverpilots_ssh_keys', $ssh_keys );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_ssh_key_name' );
	delete_option( 'wpcs_ssh_key' );

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