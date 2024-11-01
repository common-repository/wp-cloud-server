<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$volumes		= wpcs_digitalocean_call_api_list_volumes();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Volumes', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Droplet', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Size (GB)', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Region', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes['volumes'] ) ) {
							foreach ( $volumes['volumes'] as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['id']; ?></td>
            						<td><?php
										$d = new DateTime( $volume['created_at'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $volume['name']; ?></td>
									<td><?php echo ( empty( $volume['droplet_ids'] ) ) ? "Not Assigned" : $volume['droplet_ids'][0]; ?></td>
									<td><?php echo $volume['size_gigabytes']; ?></td>
									<td><?php echo $volume['region']['name']; ?></td>
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
