<?php

/**
 * Provide a Admin Area Debug Page for the Digitalocean Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

function wpcs_vultr_api_debug_template ( $tabs_content, $page_content, $page_id ) {
	global $custom_notices;
	
	if ( 'vultr-debug' !== $tabs_content ) {
		return;
	}
	
	$managequeue 	= isset( $_GET['managequeue'] ) ? sanitize_text_field( $_GET['managequeue'] ) : '';
	$type			= isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
?>

	<h2><?php esc_html_e( 'Vultr Debug', 'wp-cloud-server-vultr' ); ?></h2>

	<p><?php esc_html_e( 'This page provides debug information for the Vultr Module.', 'wp-cloud-server-vultr' ); ?></p>

	<h3><?php esc_html_e( 'Vultr Module Array', 'wp-cloud-server-vultr' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_module_list' );
			if ( !empty( $response['Vultr'] ) ) {
				$output	= print_r($response['Vultr'], true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Vultr Module API Response', 'wp-cloud-server-vultr' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			$response = get_option( 'wpcs_vultr_api_last_response' );
			if ( !empty( $response ) ) {
				$output	= print_r($response, true);
			} else {
				$output	= 'No Data Available';	
			}	
			$output = wp_strip_all_tags( $output );
			echo "<pre>{$output}</pre>";
			?>
	</div>

	<h3><?php esc_html_e( 'Vultr API Data Array', 'wp-cloud-server-vultr' ); ?></h3>

	<div style="border: 1px solid #ddd; background: #fff; height: 400px; padding: 15px; overflow: scroll;">
			<?php
			//delete_option( 'wpcs_vultr_api_data' );
			$response = get_option( 'wpcs_vultr_api_data' );
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
		$response = get_option( 'wpcs_vultr_server_complete_queue' );
	
		if ( !empty( $response ) && ( 'delete' == $type ) ) {
			foreach ( $response as $key => $host_name ) {
				if ( $managequeue == $host_name['host_name'] ) {
					unset( $response[$key] );
					update_option( 'wpcs_vultr_server_complete_queue', $response  );
				}	
			}
		}
		?>

		<h3><?php esc_html_e( 'Vultr Complete Server Queue', 'wp-cloud-server-vultr' ); ?></h3>

		<table class="uk-table uk-table-striped">
			<thead>
        		<tr>
            		<th class="uk-width-small">SUBID</th>
					<th class="uk-width-small">User ID</th>
					<th class="uk-width-small">Module</th>
					<th class="uk-width-small">Hostname</th>
					<th class="uk-width-small">Domain</th>
					<th class="uk-width-small">Location</th>
					<th class="uk-width-small">Manage</th>
        		</tr>
    		</thead>
    		<tbody>
		<?php
		if ( !empty( $response ) ) {
			foreach ( $response as $key => $item ) {
			?>
        		<tr>
            		<td><?php echo ( isset( $item['SUBID'] ) ) ? $item['SUBID'] : ''; ?></td>
					<td><?php echo ( isset( $item['user_id'] ) ) ? $item['user_id'] : ''; ?></td>
            		<td><?php echo ( isset( $item['module'] ) ) ? $item['module'] : ''; ?></td>
					<td><?php echo ( isset( $item['host_name'] ) ) ? $item['host_name'] : ''; ?></td>
            		<td><?php echo ( isset( $item['host_name_domain'] ) ) ? $item['host_name_domain'] : ''; ?></td>
					<td><?php echo ( isset( $item['location'] ) ) ? $item['location'] : ''; ?></td>
					<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&type=delete&managequeue=' . $item['host_name'] ), 'vultr_manage_nonce', '_wp_manage_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
				</tr>
			
			<?php } 
		} else {
			?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No Server Actions Currently Queued', 'wp-cloud-server-vultr' ) ?></td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>

<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_api_debug_template', 10, 3 );