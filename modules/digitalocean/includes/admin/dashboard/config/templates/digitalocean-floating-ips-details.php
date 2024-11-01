<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$volumes		= wpcs_digitalocean_call_api_list_floating_ips();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Floating IPs', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'IP Address', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Assigned', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Region', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['floating_ips'] ) ) {
							foreach ( $volumes['floating_ips'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['ip']; ?></td>
									<td><?php echo $volume['droplet']['name']; ?></td>
									<td><?php echo $volume['region']['name']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Floating IPs Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php

