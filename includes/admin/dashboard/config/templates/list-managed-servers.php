<?php

/**
 * Provide a admin area servers view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

	$servers = wpcs_serverpilot_call_api_list_servers();

	$module_data	= get_option( 'wpcs_module_list' );			
	?>
<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'ServerPilot Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th class="col-name"><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Plan', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Auto Install', 'wp-cloud-server' ); ?></th>
            	<!-- <th><?php esc_html_e( 'Sites', 'wp-cloud-server' ); ?></th> -->
            	<th><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
					
					// Obtain Key for desired PHP Runtime Version (ServerPilot occasionally changes the availability!)
					if ( is_array( $server['available_runtimes'] ) ) {
						$key = ( count( $server['available_runtimes'] ) - 1 );
						$php_version = $server['available_runtimes'][ $key ];
					} else {
						$php_version = 'php7.4';
					}

					?>
        			<tr>
						<td><?php echo $server['name']; ?></td>
						<?php $plan = ( $server['plan'] == 'first_class' ) ? 'First Class' : ucfirst($server['plan']); ?>
						<td><?php echo $plan; ?></td>
            			<td><?php echo date( 'd/M/Y', $server['datecreated']); ?></td>
						<td><?php echo ( 1 == $server['autoupdates'] ) ? 'Enabled' : 'Not Enabled'; ?></td>
						<!-- <td><?php echo '0'; ?></td> -->
						<td><?php echo $server['lastaddress']; ?></td>
						<td><a class="uk-link" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle>Manage</a></td>
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
	if ( !empty( $servers ) ) {
		foreach ( $servers as $key => $server ) {
		
			// Obtain Key for desired PHP Runtime Version (ServerPilot occasionally changes the availability!)
			if ( is_array( $server['available_runtimes'] ) ) {
				$key = ( count( $server['available_runtimes'] ) - 1 );
				$php_version = $server['available_runtimes'][ $key ];
			} else {
				$php_version = 'php7.4';
			}
				    ?>

			        <div id="managed-server-modal-<?php echo $server['id']; ?>" uk-modal>
    			        <div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Server: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo $server['name']; ?></span></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px; padding: 25px 10px;" class="uk-border-rounded">
									<div uk-grid>
  										<div class="uk-width-1-5@m">
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $server['id']; ?>;">
		 										<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menu['id'] == 'installed-websites' ) {
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
													if ( $menu['id'] == 'installed-websites' ) {
														if ( isset($menu['modal_menu_items']) ) {
															foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 														if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 															?>
																	<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_serverpilot_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
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
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['name']}' from your ServerPilot account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_serverpilot_server_action">
							<input type="hidden" name="wpcs_serverpilot_server_action" value="delete">
							<input type="hidden" name="wpcs_serverpilot_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( 'handle_serverpilot_server_action_nonce', 'wpcs_handle_serverpilot_server_action_nonce' ); ?>
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