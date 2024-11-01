<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_linode_volume_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-volume-details' !== $tabs_content ) {
		return;
	}
	
	$volumes		= wpcs_linode_call_api_list_volumes();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Linode Volumes', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Label', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Linode', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Size (GB)', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Region', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['data'] ) ) {
							foreach ( $volumes['data'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['id']; ?></td>
            						<td><?php
										$d = new DateTime( $volume['created'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $volume['label']; ?></td>
									<td><?php echo $volume['linode_label']; ?></td>
									<td><?php echo $volume['status']; ?></td>
									<td><?php echo $volume['size']; ?></td>
									<td><?php echo $volume['region']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Volume Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_volume_details_template', 10, 3 );
