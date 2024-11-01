<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_vultr_server_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-server-details' !== $tabs_content ) {
		return;
	}
	
	$servers		= wpcs_vultr_call_api_list_servers( false );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>

	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vultr Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th class="col-name"><?php esc_html_e( 'Server Name', 'wp-cloud-server-vultr' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-cloud-server-vultr' ); ?></th>
				<th><?php esc_html_e( 'Region', 'wp-cloud-server-vultr' ); ?></th>
            	<th><?php esc_html_e( 'vCPUs', 'wp-cloud-server-vultr' ); ?></th>
            	<th><?php esc_html_e( 'Memory', 'wp-cloud-server-vultr' ); ?></th>
            	<th><?php esc_html_e( 'SSD', 'wp-cloud-server-vultr' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server-vultr' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server-vulr' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
				?>
        			<tr>
            			<td><?php echo $server['label']; ?></td>
            			<td><?php echo ucfirst($server['power_status']); ?></td>
						<td><?php echo $server['location']; ?></td>
						<td><?php echo $server['vcpu_count']; ?></td>
						<td><?php echo $server['ram']; ?></td>
						<td><?php echo $server['disk']; ?></td>
						<td><?php echo $server['os']; ?></td>
						<td><a class="uk-link" href="#managed-server-modal-<?php echo $server['SUBID']; ?>" uk-toggle>Manage</a></td>
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

			        <div id="managed-server-modal-<?php echo $server['SUBID']; ?>" uk-modal>
    			        <div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Server: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo "{$server['label']}"; ?></span></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px; padding: 25px 10px;" class="uk-border-rounded">
									<div uk-grid>
  										<div class="uk-width-1-5@m">
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $server['SUBID']; ?>;">
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
                							<ul id="comp-tab-left-<?php echo $server['SUBID']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( isset($menu['modal_menu_items']) ) {
														foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
																<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_vultr_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
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
							<?php if ( 'running' == $server['power_status'] ) {
								$actions = array( 
									'Reboot'		=> 	'reboot', 
									'Shutdown'		=>	'halt',
								);
								foreach ( $actions as $key => $action ) {
									?>
									<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
										<input type="hidden" name="action" value="handle_vultr_server_action">
										<input type="hidden" name="wpcs_vultr_server_action" value="<?php echo $action;?>">
										<input type="hidden" name="wpcs_vultr_server_id" value="<?php echo $server['SUBID'];?>">
										<?php wp_nonce_field( 'handle_vultr_server_action_nonce', 'wpcs_handle_vultr_server_action_nonce' ); ?>
										<?php wpcs_submit_button( $key, 'secondary', 'server_action', false ); ?>
									</form>
									<?php
								}
							} else {
								?>
								<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
									<input type="hidden" name="action" value="handle_vultr_server_action">
									<input type="hidden" name="wpcs_vultr_server_action" value="start">
									<input type="hidden" name="wpcs_vultr_server_id" value="<?php echo $server['SUBID'];?>">
									<?php wp_nonce_field( 'handle_vultr_server_action_nonce', 'wpcs_handle_vultr_server_action_nonce' ); ?>
									<?php wpcs_submit_button( 'Boot', 'secondary', 'server_action', false ); ?>
								</form>
							<?php } ?>
						</div>
					        <a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-server-<?php echo $server['SUBID']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $server['SUBID']; ?>" uk-modal>
    					<div class="server-modal uk-modal-dialog uk-modal-body">
        				<div class="uk-modal-body">
							<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['label']}' from your Vultr account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            				<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        				</div>
						<div class="uk-modal-footer uk-text-right">
							<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
								<input type="hidden" name="action" value="handle_vultr_server_action">
								<input type="hidden" name="wpcs_vultr_server_action" value="destroy">
								<input type="hidden" name="wpcs_vultr_server_id" value="<?php echo $server['SUBID'];?>">
								<?php wp_nonce_field( 'handle_vultr_server_action_nonce', 'wpcs_handle_vultr_server_action_nonce' ); ?>
								<div class="uk-button-group uk-margin-remove-bottom">
									<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal-<?php echo $server['SUBID']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_server_details_template', 10, 3 );