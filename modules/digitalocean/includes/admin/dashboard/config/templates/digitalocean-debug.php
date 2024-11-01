<?php

/**
 * Provide a Admin Area Debug Page for the Digitalocean Module
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

//update_option('wpcs_module_list', array());

?>

	<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'DigitalOcean Debug', 'wp-cloud-server' ); ?></h2>

	<p><?php esc_html_e( 'This page provides debug information for the DigitalOcean Module.', 'wp-cloud-server' ); ?></p>

	<h3><?php esc_html_e( 'DigitalOcean Module Array', 'wp-cloud-server' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_module_list' );
			if ( !empty( $response['DigitalOcean'] ) ) {
				$output	= print_r($response['DigitalOcean'], true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'DigitalOcean API Response', 'wp-cloud-server' ); ?></h3>
	<p><?php esc_html_e( 'This displays an array of the API responses for each area of functionality e.g. Create Server.', 'wp-cloud-server' ); ?></p>
	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_digitalocean_api_last_response' );

			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'DigitalOcean API Data Array', 'wp-cloud-server' ); ?></h3>
	<p><?php esc_html_e( 'This displays an array of the API responses for each area of functionality e.g. Create Server.', 'wp-cloud-server' ); ?></p>
	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			//delete_option( 'wpcs_digitalocean_api_data' );
			$response = get_option( 'wpcs_digitalocean_api_data' );

			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>
<?php
