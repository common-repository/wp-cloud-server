<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_vultr_private_network_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-private-network-details' !== $tabs_content ) {
		return;
	}
	
	$volumes		= wpcs_vultr_call_api_list_network();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vultr Networks', 'wp-cloud-server-vultr' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'Description', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'Date Created', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'V4 Subnet', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'V4 Subnet Mask', 'wp-cloud-server-vultr' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes ) ) {
							foreach ( $volumes as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['description']; ?></td>
									<td><?php echo $volume['date_created']; ?></td>
									<td><?php echo $volume['v4_subnet']; ?></td>
									<td><?php echo $volume['v4subnet_mask']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Network Information Available', 'wp-cloud-server' ) ?></td>
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_private_network_details_template', 10, 3 );
