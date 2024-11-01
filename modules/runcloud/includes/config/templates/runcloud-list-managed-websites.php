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

function wpcs_runcloud_list_managed_websites_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-list-managed-websites' !== $tabs_content ) {
		return;
	}
	
	$servers		= wpcs_runcloud_web_app_list();
	$module_data	= get_option( 'wpcs_module_list' );			
	?>

<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'RunCloud Web Applications', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Server', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Default', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Stack', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
			if ( ( $servers ) && isset( $servers ) && is_array( $servers ) && !empty( $servers ) ) {
				$web_apps_exist = false;
				foreach ( $servers as $key => $server ) {
					if ( !empty($server) ) {
						foreach ( $server as $site ) {
							if ( is_array( $site ) ) {
								?>
        						<tr>
									<td><?php echo $site['id']; ?></td>
									<td><?php echo $site['name']; ?></td>
									<td><?php echo $key; ?></td>
									<?php $default_app = ( $site['defaultApp'] ) ? 'Yes' : 'No'; ?>
            						<td><?php echo "{$default_app}"; ?></td>
									<td><?php echo $site['phpVersion']; ?></td>
									<td><?php echo $site['stackMode']; ?></td>
									<td><?php echo $site['created_at']; ?></td>
									<td><a class="uk-link" href="#managed-server-modal-<?php echo $site['id']; ?>" uk-toggle>Manage</a></td>
        						</tr>
								<?php
								$web_apps_exist = true;
							}
						}
					}
				}
				if ( !$web_apps_exist ) {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Website Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			} else {
				?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Website Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
			}
			?>
    	</tbody>
	</table>
</div>

	<?php
	if ( ( $servers ) && isset( $servers ) && is_array( $servers ) && !empty( $servers ) ) {
			foreach ( $servers as $key => $server ) {
					if ( !empty($server) ) {
						foreach ( $server as $site ) {
							if ( is_array( $site ) ) {
								
								$site['server_id']		= wpcs_runcloud_server_id( $key );
								$site['server_name']	= $key;
				    ?>

			        <div id="managed-server-modal-<?php echo $site['id']; ?>" uk-modal>
    			        <div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Web Application', 'wp-cloud-server' ); ?></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px; padding: 25px 10px;" class="uk-border-rounded">
									<div uk-grid>
  										<div class="uk-width-1-5@m">
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $site['id']; ?>;">
		 										<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menus == 1 ) {
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
                							<ul id="comp-tab-left-<?php echo $site['id']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( $menus == 1 ) {
													if ( isset($menu['modal_menu_items']) ) {
														foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
																<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_runcloud_{$menu['modal_menu_action'][$menu_id]}_content", $site ); ?></div></li>
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
					        <a class="uk-button uk-button-danger uk-margin-small-right uk-align-right" href="#delete-server-<?php echo $site['id']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $site['id']; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$site['name']}' from your RunCloud account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_delete_runcloud_web_apps">
							<input type="hidden" name="wpcs_runcloud_confirm_web_apps_delete" value="true">
							<input type="hidden" name="wpcs_runcloud_confirm_web_apps_id" value="<?php echo $site['id'];?>">
							<input type="hidden" name="wpcs_runcloud_confirm_web_apps_server_id" value="<?php echo $site['server_id'];?>">
							<?php wp_nonce_field( 'wpcs_handle_delete_runcloud_web_apps', 'wpcs_handle_delete_runcloud_web_apps' ); ?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal-<?php echo $site['id']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', 'delete_web_apps', false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>
<?php
		}
	}
					}
			}
	}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_list_managed_websites_template', 10, 3 );