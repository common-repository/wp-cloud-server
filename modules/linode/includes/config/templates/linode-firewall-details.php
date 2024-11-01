<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_linode_firewall_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-firewall-details' !== $tabs_content ) {
		return;
	}
	
	$volumes		= wpcs_digitalocean_call_api_list_firewalls();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Firewalls', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['firewalls'] ) ) {
							foreach ( $volumes['firewalls'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['id']; ?></td>
									<td><?php echo $volume['name']; ?></td>
									<td><?php echo $volume['status']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Firewall Information Available', 'wp-cloud-server' ) ?></td>
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_firewall_details_template', 10, 3 );
