<?php

/**
 * Provide a admin area debug view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */
	
?>

<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'ServerPilot Debug', 'wp-cloud-server' ); ?></h2>

<p><?php esc_html_e( 'This page provides debug information for the ServerPilot Module.', 'wp-cloud-server' ); ?></p>

<h3><?php esc_html_e( 'ServerPilot Module Array', 'wp-cloud-server' ); ?></h3>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	$response = get_option( 'wpcs_module_list' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
		$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>

<h3><?php esc_html_e( 'ServerPilot API Response', 'wp-cloud-server' ); ?></h3>

<p><?php esc_html_e( 'This displays an array of the API responses for each area of functionality e.g. Create App.', 'wp-cloud-server' ); ?></p>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	$response = get_option( 'wpcs_serverpilot_api_last_response' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
	$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>

<h3><?php esc_html_e( 'ServerPilot API Data Array', 'wp-cloud-server' ); ?></h3>

<p><?php esc_html_e( 'This displays the API data array.', 'wp-cloud-server' ); ?></p>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	//delete_option( 'wpcs_module_config' );
	//delete_option( 'wpcs_config' );
	$response = get_option( 'wpcs_serverpilot_api_data' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
	$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>

<h3><?php esc_html_e( 'ServerPilot SSL Activation Queue', 'wp-cloud-server' ); ?></h3>

<p><?php esc_html_e( 'This displays an array containing the SSL Activation Queue. Sites listed are pending SSL being activated.', 'wp-cloud-server' ); ?></p>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	$response	= get_option( 'wpcs_sp_api_ssl_queue' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
	$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>

<h3><?php esc_html_e( 'ServerPilot App Creation Queue', 'wp-cloud-server' ); ?></h3>

<p><?php esc_html_e( 'This displays an array containing the App Creation Queue.', 'wp-cloud-server' ); ?></p>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	$response	= get_option( 'wpcs_sp_api_site_creation_queue' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
	$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>

<h3><?php esc_html_e( 'ServerPilot Shared Hosting Servers', 'wp-cloud-server' ); ?></h3>

<p><?php esc_html_e( 'This displays an array containing Servers available for Shared Hosting.', 'wp-cloud-server' ); ?></p>

<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
	<?php
	$response = get_option( 'wpcs_app_server_list' );
	if ( !empty( $response ) ) {
		$output	= print_r($response, true);
	} else {
		$output	= 'No Data Available';	
	}	
	$output = wp_strip_all_tags( $output );
echo "<pre>{$output}</pre>";
	?>
</div>