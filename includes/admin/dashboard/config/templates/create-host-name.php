<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="content">
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'wpcs_edd_checkout_settings' );
		wpcs_do_settings_sections( 'wpcs_edd_checkout_settings' );
		?>
		<hr>
		<?php
		wpcs_submit_button( __( 'Save Settings', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>

	<?php

	if ( get_option( 'wpcs_edd_checkout_settings_hostname_label' ) ) {

		$host_name_label		= get_option( 'wpcs_edd_checkout_settings_hostname_label' );
		$host_name				= get_option( 'wpcs_edd_checkout_settings_hostname' );
		$host_name_domain		= get_option( 'wpcs_edd_checkout_settings_hostname_domain' );
		$host_name_suffix		= get_option( 'wpcs_edd_checkout_settings_hostname_suffix' );
		$host_name_protocol		= get_option( 'wpcs_edd_checkout_settings_hostname_protocol' );
		$host_name_port			= get_option( 'wpcs_edd_checkout_settings_hostname_port' );
		
		// Set-up the data for the new Droplet
		$host_name_data = array(
			"label"				=> $host_name_label,
			"hostname"			=> $host_name,
			"domain"			=> $host_name_domain,
			"suffix"			=> $host_name_suffix,
			"protocol"			=> $host_name_protocol,
			"port"				=> $host_name_port,
			"count"				=> 0,
		);

		// Retrieve the Active Module List
		$host_names				= get_option( 'wpcs_host_names' );
			
		// Save the VPS Template for use with a Plan
		$host_names[ $host_name_data['label'] ] = $host_name_data;
			
		update_option( 'wpcs_host_names', $host_names );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_edd_checkout_settings_hostname_label' );
	delete_option( 'wpcs_edd_checkout_settings_hostname' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_domain' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_suffix' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_protocol' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_port' );