<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_vultr_backup_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-backup-details' !== $tabs_content ) {
		return;
	}
	
	$volumes		= wpcs_vultr_call_api_list_backups();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vultr Backups', 'wp-cloud-server-vultr' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'Date Created', 'wp-cloud-server-vultr' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'Status', 'wp-cloud-server-vultr' ); ?></th>
                            <th><?php esc_html_e( 'Size', 'wp-cloud-server-vultr' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $volumes ) ) {
							foreach ( $volumes as $key => $volume ) {
								?>
        						<tr>
            						<td><?php echo $volume['BACKUPID']; ?></td>
                                    <td><?php echo $volume['date_created']; ?></td>
									<td><?php echo $volume['description']; ?></td>
                                    <td><?php echo $volume['status']; ?></td>
                                    <td><?php echo $volume['size']; ?></td>
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
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_backup_details_template', 10, 3 );