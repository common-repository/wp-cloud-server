<?php

/**
 * Provide a Admin Area Servers Page for the Digitalocean Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

function wpcs_linode_list_server_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-list-server-details' !== $tabs_content ) {
		return;
	}

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

if ( wp_verify_nonce( $nonce, 'linode_servers_nonce' ) ) {

	$debug_enabled 	= get_option( 'wpcs_enable_debug_mode' );
	$servers		= wpcs_linode_call_api_list_servers( false );

	update_option( 'linode_server_api', $servers );
	?>

	<h2><?php esc_html_e( 'Linode Server Information', 'wp-cloud-server' ); ?></h2>

	<table class="wp-list-table widefat fixed striped">
	<thead>
        	<tr>
            	<th class="col-name"><?php esc_html_e( 'Server Name', 'wp-cloud-server-linode' ); ?></th>
				<th><?php esc_html_e( 'Date Created', 'wp-cloud-server-vulr' ); ?></th>
				<th><?php esc_html_e( 'Location', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'vCPUs', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'Memory', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'SSD', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server-linode' ); ?></th>
				<th><?php esc_html_e( 'IP Address', 'wp-cloud-server-vulr' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				?>
        			<tr>
            			<td><?php echo $server['label']; ?></td>
						<td><?php $d = new DateTime( $server['updated'] ); echo $d->format('d-m-Y'); ?></td>
						<td><?php echo wpcs_linode_region_map( $server['region'] ); ?></td>
						<td><?php echo $server['specs']['vcpus']; ?></td>
						<td><?php echo substr_replace( $server['specs']['memory'], 'GB', 1 ) ?></td>
						<td><?php echo substr_replace( $server['specs']['disk'], 'GB', 2 ); ?></td>
						<td><?php echo wpcs_linode_os_list( $server['image'], true ); ?></td>
						<td><?php echo $server['ipv4'][0]; ?></td>
        			</tr>
				<?php
				}
			} else {
			?>
				<tr>
					<td colspan="8"><?php esc_html_e( 'No Server Information Available', 'wp-cloud-server-linode' ) ?></td>
				</tr>
			<?php
			}
			?>
    	</tbody>
	</table>
	<?php
} else {
?>
	<p><?php esc_html_e( 'Sorry! You cannot access this page!', 'wp-cloud-server-linode' ) ?></p>
<?php
}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_list_server_details_template', 10, 3 );