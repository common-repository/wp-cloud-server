<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$images			= wpcs_digitalocean_call_api_list_databases();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Databases', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Engine', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Region', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $images['databases'] ) ) {
							foreach ( $images['databases'] as $key => $image ) {
								?>
        						<tr>
            						<td><?php echo $image['id']; ?></td>
            						<td><?php
										$d = new DateTime( $image['created_at'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $image['name']; ?></td>
									<td><?php echo $image['engine']; ?></td>
									<td><?php echo $image['region']; ?></td>
									<td><?php echo $image['status']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Database Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php

