<?php

function wpcs_runcloud_server_details_template ( $page ) {
	
	if ( 'runcloud-server-details' !== $page['template'] ) {
		return;
	}
	
	$servers	= get_option( 'wpcs_runcloud_api_data' );
	//$servers	= $data['servers'];
	//$servers		= wpcs_runcloud_call_api_list_servers( false );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'RunCloud Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th class="col-name"><?php esc_html_e( 'Server Name', 'wp-cloud-server-runcloud' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-cloud-server-runcloud' ); ?></th>
				<th><?php esc_html_e( 'Region', 'wp-cloud-server-runcloud' ); ?></th>
            	<th><?php esc_html_e( 'vCPUs', 'wp-cloud-server-runcloud' ); ?></th>
            	<th><?php esc_html_e( 'Memory', 'wp-cloud-server-runcloud' ); ?></th>
            	<th><?php esc_html_e( 'SSD', 'wp-cloud-server-runcloud' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server-runcloud' ); ?></th>
				<th><?php esc_html_e( 'Manage', 'wp-cloud-server-vulr' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				?>
        			<tr>
            			<td><?php echo $server['title']; ?></td>
            			<td><?php echo ucfirst($server['state']); ?></td>
						<td><?php echo $server['zone']; ?></td>
						<td><?php echo $server['core_number']; ?></td>
						<td><?php echo $server['memory_amount']; ?></td>
						<td><?php echo $server['plan']; ?></td>
						<td><?php echo $server['hostname']; ?></td>
						<td><a class="uk-link" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle>Manage</a></td>
        			</tr>
					<?php
				}
			} else {
			?>
					<tr>
						<td colspan="8"><?php esc_html_e( 'No Server Information Available', 'wp-cloud-server' ) ?></td>
					</tr>
			<?php
			}
			?>
    	</tbody>
	</table>
</div>

	<?php
	if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				    ?>

			        <div id="managed-server-modal-<?php echo $server['id']; ?>" uk-modal>
    			        <div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Server', 'wp-cloud-server' ); ?></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
						        <table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['label']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            						<td><?php echo ucfirst($server['power_status']); ?></td>
        						</tr>
        						<tr>
           	 						<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['location']; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></td>
									<td><?php echo $server['os']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['vcpu_count']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
           							<td><?php echo $server['ram'] ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo $server['disk']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['main_ip']) ? $server['main_ip'] : 'Not Available'; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php $d = new DateTime( $server['date_created'] ); echo $d->format('d-m-Y'); ?></td>
        						</tr>
    						        </tbody>
						        </table>
					        </div>
					        <hr>
					        <a class="uk-button uk-button-danger uk-margin-small-right uk-align-right" href="#delete-server-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $server['id']; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['label']}' from your RunCloud account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_runcloud_server_action">
							<input type="hidden" name="wpcs_runcloud_server_action" value="delete">
							<input type="hidden" name="wpcs_runcloud_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( 'handle_runcloud_server_action_nonce', 'wpcs_handle_runcloud_server_action_nonce' ); ?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', 'delete_server', false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>
    <?php
        }
	}
}
add_action( 'wpcs_control_panel_templates', 'wpcs_runcloud_server_details_template' );