<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
	
	$snapshots		= array();
	$module_data	= get_option( 'wpcs_module_list' );		
	$images			= wpcs_digitalocean_call_api_list_images();

	if (!empty( $images['images'] ) ) {
		foreach ( $images['images'] as $key => $image ) {
			if ( 'snapshot' == $image['type'] ) {
				$snapshots[] = $image;
			}
		}
	}

	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Snapshots', 'wp-cloud-server' ); ?></h3>
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
						if (!empty( $snapshots ) ) {
							foreach ( $snapshots as $key => $image ) {
								?>
        						<tr>
            						<td><?php echo $image['id']; ?></td>
            						<td><?php
										$d = new DateTime( $image['created_at'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $image['type']; ?></td>
									<td><?php echo $image['name']; ?></td>
									<td><?php echo $image['min_disk_size']; ?></td>
									<td><?php echo $image['status']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Snapshot Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
<?php


