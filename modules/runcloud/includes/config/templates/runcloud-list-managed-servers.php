<?php
/**
 * Provide a admin area servers view for the RunCloud module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_runcloud_list_managed_servers_template ( $tabs_content, $page_content, $page_id ) {

	$force_api = true;
	
	if ( 'runcloud-list-managed-servers' !== $tabs_content ) {
		return;
	}

	$servers = wpcs_runcloud_call_api_data_servers();

	$module_name	= 'RunCloud';
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'RunCloud Servers', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Provider', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $key => $server ) {
					?>
        			<tr>
						<td><?php echo $server['name']; ?></td>
						<?php $status = ( $server['connected'] == true ) ? '<span style="color: green;">Connected</span>' : '<span style="color: red;">Not Connected</span>' ; ?>
						<?php $status = ( $server['online'] == true ) ? $status : '<span style="color: red;">Offline</span>' ; ?>
						<td><?php echo $status; ?></td>
						<td><?php echo $server['created_at']; ?></td>
						<td><?php echo $server['provider']; ?></td>
						<td><?php echo $server['ipAddress']; ?></td>
						<td><?php echo "{$server['os']} {$server['osVersion']}"; ?></td>
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
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $server['name']; ?>;">
		 										<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menu['id'] == 'runcloud-managed-servers' ) {
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
                							<ul id="comp-tab-left-<?php echo $server['name']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menu['id'] == 'runcloud-managed-servers' ) {
														if ( isset($menu['modal_menu_items']) ) {
															foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 														if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 															?>
																	<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_runcloud_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
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
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['name']}' from your RunCloud account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_runcloud_server_action">
							<input type="hidden" name="wpcs_runcloud_server_action" value="delete">
							<input type="hidden" name="wpcs_runcloud_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( "handle_runcloud_server_action_nonce_{$server['id']}", "wpcs_handle_runcloud_server_action_nonce_{$server['id']}" ); ?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', "delete_server_{$server['id']}", false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>
<?php
		}
	}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_list_managed_servers_template', 10, 3 );