<?php

/**
 * Provide a Admin Area Debug Page for the Cloudways Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

function wpcs_cloudways_api_debug_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-debug' !== $tabs_content ) {
		return;
	}
	?>

	<h2><?php esc_html_e( 'Cloudways Debug', 'wp-cloud-server-cloudways' ); ?></h2>

	<p><?php esc_html_e( 'This page provides debug information for the Cloudways Module.', 'wp-cloud-server-cloudways' ); ?></p>

	<h3><?php esc_html_e( 'Cloudways Module Array', 'wp-cloud-server-cloudways' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_module_list' );
			if ( !empty( $response['Cloudways'] ) ) {
				$output	= print_r($response['Cloudways'], true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Cloudways API Responses', 'wp-cloud-server-cloudways' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_cloudways_api_last_response' );
			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Cloudways API Data Array', 'wp-cloud-server-upcloud' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			//delete_option( 'wpcs_cloudways_api_data' );
			$response = get_option( 'wpcs_cloudways_api_data' );
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
		$response = get_option( 'wpcs_cloudways_server_complete_queue' );
		?>

		<h3><?php esc_html_e( 'Cloudways Complete Server Queue', 'wp-cloud-server-cloudways' ); ?></h3>

		<table class="uk-table uk-table-striped">
			<thead>
        		<tr>
					<th class="uk-width-small">Plan Name</th>
            		<th class="uk-width-small">Server ID</th>
					<th class="uk-width-small">User ID</th>
					<th class="uk-width-small">Module</th>
					<th class="uk-width-small">Hostname</th>
        		</tr>
    		</thead>
    		<tbody>
		<?php
		if ( !empty( $response ) ) {
			foreach ( $response as $key => $item ) {
			?>
        		<tr>
					<td><?php echo ( isset( $item['plan_name'] ) ) ? $item['plan_name'] : ''; ?></td>
            		<td><?php echo ( isset( $item['SUBID'] ) ) ? $item['SUBID'] : ''; ?></td>
					<td><?php echo ( isset( $item['user_id'] ) ) ? $item['user_id'] : ''; ?></td>
            		<td><?php echo ( isset( $item['module'] ) ) ? $item['module'] : ''; ?></td>
					<td><?php echo ( isset( $item['host_name'] ) ) ? $item['host_name'] : ''; ?></td>
				</tr>
			
			<?php } 
		} else {
			?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No Server Actions Currently Queued', 'wp-cloud-server-cloudways' ) ?></td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

		<?php
		$status = get_option( 'wpcs_cloudways_create_app_queue' );
		?>

		<h3><?php esc_html_e( 'Cloudways Complete App Queue', 'wp-cloud-server-cloudways' ); ?></h3>

		<table class="uk-table uk-table-striped">
			<thead>
        		<tr>
            		<th>Project Name</th>
					<th>Sel Name</th>
					<th>Sel ID</th>
					<th>Stage</th>
					<th>Operation ID</th>
        		</tr>
    		</thead>
    		<tbody>
		<?php
		if ( !empty( $status ) ) {
			foreach ( $status as $key => $item ) {
			?>
        		<tr>
            		<td><?php echo ( isset( $item['project_name'] ) ) ? $item['project_name'] : ''; ?></td>
					<td><?php echo ( isset( $item['selected_project_name'] ) ) ? $item['selected_project_name'] : ''; ?></td>
					<td><?php echo ( isset( $item['selected_project_id'] ) ) ? $item['selected_project_id'] : ''; ?></td>
					<td><?php echo ( isset( $item['stage'] ) ) ? $item['stage'] : ''; ?></td>
					<td><?php echo ( isset( $item['operation_id'] ) ) ? $item['operation_id'] : ''; ?></td>
				</tr>
			
			<?php } 
		} else {
			?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No App Actions Currently Queued', 'wp-cloud-server-cloudways' ) ?></td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_api_debug_template', 10, 3 );
