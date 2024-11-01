<?php

function wpcs_linode_debug_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-debug' !== $tabs_content ) {
		return;
	}
	?>

	<h2><?php esc_html_e( 'Linode Debug', 'wp-cloud-server-linode' ); ?></h2>

	<p><?php esc_html_e( 'This page provides debug information for the Linode Module.', 'wp-cloud-server-linode' ); ?></p>

	<h3><?php esc_html_e( 'Linode Module Array', 'wp-cloud-server-linode' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_module_list' );
			if ( !empty( $response['Linode'] ) ) {
				$output	= print_r($response['Linode'], true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Linode API Response', 'wp-cloud-server-linode' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_linode_api_internal_response' );
			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Linode API Array', 'wp-cloud-server-linode' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_linode_api_data' );
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_debug_template', 10, 3 );