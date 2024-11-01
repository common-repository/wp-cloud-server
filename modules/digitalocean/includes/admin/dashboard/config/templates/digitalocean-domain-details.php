<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$volumes		= wpcs_digitalocean_call_api_list_domains();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Domains', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'TTL', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Zone File', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th></th>
            				<th></th>
            				<th></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['domains'] ) ) {
							foreach ( $volumes['domains'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['name']; ?></td>
									<td><?php echo $volume['ttl']; ?></td>
									<td colspan="4"><pre style="padding-top: 3px;"><?php echo $volume['zone_file']; ?></pre></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Domain Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php

