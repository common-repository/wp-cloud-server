<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$volumes		= wpcs_digitalocean_call_api_list_load_balancers();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Load Balancers', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Created', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'IP', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Status', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Size', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['load_balancers'] ) ) {
							foreach ( $volumes['load_balancers'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['name']; ?></td>
									<td><?php echo $volume['created_at']; ?></td>
									<td><?php echo $volume['ip']; ?></td>
									<td><?php echo $volume['status']; ?></td>
									<td><?php echo $volume['size']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Load Balancers Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php

