<?php

function wpcs_cloudways_server_details_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-server-details' !== $tabs_content ) {
		return;
	}

	$servers = get_option( 'wpcs_cloudways_api_data' );

	if ( !isset( $servers['servers'] ) ) {
		// Create instance of the Cloudways API
		$api		= new WP_Cloud_Server_Cloudways_API();
		$servers	= $api->call_api( 'server', null, false, 900, 'GET', false, 'cloudways_server_list' );
	}
	
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Cloudways Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th class="col-name"><?php esc_html_e( 'Id', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Label', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Platform', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Cloud', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers['servers'] ) ) {
				foreach ( $servers['servers'] as $key => $server ) {
					?>
        			<tr>
						<td><?php echo $server['id']; ?></td>
							<td><?php echo $server['label']; ?></td>
							<td><?php echo $server['status']; ?></td>
							<td><?php echo $server['platform']; ?></td>
							<td><?php echo wpcs_cloudways_providers_list($server['cloud']); ?></td>
							<td><?php echo $server['region']; ?></td>
							<td><a class="uk-link" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle>Manage</a>
							</td>
        			</tr>
					<?php
				}
			} else {
			?>
					<tr>
						<td colspan="7"><?php esc_html_e( 'No Server Information Available', 'wp-cloud-server' ) ?></td>
					</tr>
			<?php
			}
			?>
    	</tbody>
	</table>
</div>

	<?php
	if ( !empty( $servers['servers'] ) ) {
		foreach ( $servers['servers'] as $key => $server ) {
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
													if ( $menu['id'] == 'cloudways-server-details' ) {
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
												}
												?>
     										</ul>
  										</div>
  										<div class="uk-width-4-5@m">
                							<ul id="comp-tab-left-<?php echo $server['id']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menu['id'] == 'cloudways-server-details' ) {
														if ( isset($menu['modal_menu_items']) ) {
															foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 														if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 															?>
																	<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_cloudways_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
        															<?php 
																}
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
					        <a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-server-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $server['id']; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['label']}' from your Cloudways account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_delete_cloudways_server">
							<input type="hidden" name="wpcs_cloudways_confirm_server_delete" value="true">
							<input type="hidden" name="wpcs_cloudways_confirm_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( 'wpcs_handle_delete_cloudways_server', 'wpcs_handle_delete_cloudways_server' ); ?>
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
add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_server_details_template', 10, 3 );