<?php

function wpcs_linode_server_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-server-details' !== $tabs_content ) {
		return;
	}
	
	$servers		= wpcs_linode_call_api_list_servers( false );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Linode Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th><?php esc_html_e( 'Server Name', 'wp-cloud-server-linode' ); ?></th>
				<th><?php esc_html_e( 'Region', 'wp-cloud-server-linode' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-cloud-server-linode' ); ?></th>				
            	<th><?php esc_html_e( 'vCPUs', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'Memory', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'SSD', 'wp-cloud-server-linode' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server-linode' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server-vulr' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
			if ( $servers && is_array( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				?>
        			<tr>
            			<td><?php echo $server['label']; ?></td>
						<td><?php echo wpcs_linode_region_map( $server['region'] ); ?></td>
						<td><?php echo ucfirst($server['status']); ?></td>						
						<td><?php echo $server['specs']['vcpus']; ?></td>
						<td><?php echo substr_replace( $server['specs']['memory'], 'GB', 1 ) ?></td>
						<td><?php echo substr_replace( $server['specs']['disk'], 'GB', 2 ); ?></td>
						<td><?php echo wpcs_linode_os_list( $server['image'], true ); ?></td>
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
	if ( $servers && is_array( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				    ?>

			        <div id="managed-server-modal-<?php echo $server['id']; ?>" uk-modal>
    			        <div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Server: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo $server['label']; ?></span></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px; padding: 25px 10px;" class="uk-border-rounded">
									<div uk-grid>
  										<div class="uk-width-1-5@m">
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $server['id']; ?>;">
		 										<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( isset($menu['modal_menu_items']) ) {
		 												foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
                    											<li><a href="#"><?php echo $menu_item; ?></a></li>
        														<?php 
															}
														}
													}
												}
												?>
     										</ul>
  										</div>
  										<div class="uk-width-4-5@m">
                							<ul id="comp-tab-left-<?php echo $server['id']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( isset($menu['modal_menu_items']) ) {
														foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
																<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_linode_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
        														<?php 
															}
														}
													}
												}
												?>
                							</ul>
  										</div>
									</div>
								</div>
							</div>
					        <hr>
							<div class="uk-margin-right-small uk-align-right uk-margin-remove-bottom">
							<?php
							if ( 'running' == $server['status'] ) {
								$actions = array( 
									'Reboot'		=> 	'reboot', 
									'Shutdown'		=>	'shutdown',
								);
								foreach ( $actions as $key => $action ) {
									?>
									<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
										<input type="hidden" name="action" value="handle_linode_server_action">
										<input type="hidden" name="wpcs_linode_server_action" value="<?php echo $action;?>">
										<input type="hidden" name="wpcs_linode_server_id" value="<?php echo $server['id'];?>">
										<?php wp_nonce_field( 'handle_linode_server_action_nonce', 'wpcs_handle_linode_server_action_nonce' ); ?>
										<?php wpcs_submit_button( $key, 'secondary', 'server_action', false ); ?>
									</form>
									<?php
								}
							} else {
								?>
								<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
									<input type="hidden" name="action" value="handle_linode_server_action">
									<input type="hidden" name="wpcs_linode_server_action" value="boot">
									<input type="hidden" name="wpcs_linode_server_id" value="<?php echo $server['id'];?>">
									<?php wp_nonce_field( 'handle_linode_server_action_nonce', 'wpcs_handle_linode_server_action_nonce' ); ?>
									<?php wpcs_submit_button( 'Boot', 'secondary', 'server_action', false ); ?>
								</form>
							<?php } ?>
						</div>
					        <a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-server-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $server['id']; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['label']}' from your Linode account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_linode_server_action">
							<input type="hidden" name="wpcs_linode_server_action" value="delete">
							<input type="hidden" name="wpcs_linode_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( 'handle_linode_server_action_nonce', 'wpcs_handle_linode_server_action_nonce' ); ?>
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_server_details_template', 10, 3 );