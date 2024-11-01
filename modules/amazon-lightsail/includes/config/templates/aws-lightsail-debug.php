<?php

function wpcs_aws_lightsail_debug_template (  $tabs_content, $page_content, $page_id  ) {
	
	if ( 'aws-lightsail-debug' !== $tabs_content ) {
		return;
	}
?>

	<h2><?php esc_html_e( 'AWS Lightsail Debug', 'wp-cloud-server-aws-lightsail' ); ?></h2>

	<p><?php esc_html_e( 'This page provides debug information for the AWS Lightsail Module.', 'wp-cloud-server-aws-lightsail' ); ?></p>

	<h3><?php esc_html_e( 'AWS Lightsail Module Array', 'wp-cloud-server-aws-lightsail' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_module_list' );
			if ( !empty( $response['AWS Lightsail'] ) ) {
				$output	= print_r($response['AWS Lightsail'], true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'AWS Lightsail API Response', 'wp-cloud-server-aws-lightsail' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_aws_lightsail_api_last_response' );
			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'UpCloud API Data Array', 'wp-cloud-server-upcloud' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			//delete_option( 'wpcs_aws_lightsail_api_data' );
			$response = get_option( 'wpcs_aws_lightsail_api_data' );
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
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_aws_lightsail_debug_template', 10, 3 );
