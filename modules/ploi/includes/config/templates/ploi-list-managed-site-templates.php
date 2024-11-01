<?php

/**
 * Provide a admin area servers view for the ploi module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_ploi_list_managed_site_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'ploi-list-managed-site-templates' !== $tabs_content ) {
		return;
	}
	
	$module_data		= get_option( 'wpcs_module_list' );
	$template_data		= get_option( 'wpcs_template_data_backup' );
	$completed_tasks	= get_option( 'wpcs_tasks_completed', array());
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Ploi Site Templates', 'wp-cloud-server' ); ?></h2>
		<table class="uk-table uk-table-striped">
    		<thead>
        		<tr>
					<th><?php _e( 'Name', 'wp-cloud-server' ); ?></th>
					<th><?php _e( 'Server', 'wp-cloud-server' ); ?></th>
					<th><?php _e( 'Domain', 'wp-cloud-server' ); ?></th>
           			<th><?php _e( 'Web Directory', 'wp-cloud-server' ); ?></th>
					<th><?php _e( 'Application', 'wp-cloud-server' ); ?></th>
					<th><?php _e( 'SSL', 'wp-cloud-server' ); ?></th>
					<th><?php _e( 'Sites', 'wp-cloud-server' ); ?></th>
					<th class="uk-table-shrink"><?php _e( 'Manage', 'wp-cloud-server' ); ?></th>
        		</tr>
    		</thead>
    		<tbody>
				<?php
				$template_count = ( isset( $module_data[ 'Ploi' ][ 'site_template_count' ] ) ) ? $module_data[ 'Ploi' ][ 'site_template_count' ] : 0;
				$templates		= $module_data['Ploi']['templates'];
				if ( $template_count > 0 ) { 
					foreach ( $templates as $template ) {
						if ( 'ploi_site_template' == $template['template_name'] ) {
							$site_label = strtolower( str_replace( " ", "-", $template['name'] ) );
							?>
        					<tr>
								<td><?php echo $template['name']; ?></td>
								<td><?php echo $template['server_name']; ?></td>
								<td><?php echo $template['root_domain']; ?></td>
								<td><?php echo $template['web_directory']; ?></td>
								<td><?php echo $template['web_app_name']; ?></td>
								<td>
									<?php
									$ssl = ( isset( $template['enable_ssl'] ) && $template['enable_ssl'] ) ? 'Yes' : 'No';
									echo $ssl;
									?>
								</td>
								<td><?php echo $template['site_counter']; ?></td>
								<td>
								<a class="uk-link" href="#managed-site-template-modal-<?php echo $site_label; ?>" uk-toggle><?php _e( 'Manage', 'wp-cloud-server' ); ?></a>
								</td>
							</tr>
							<?php
						}
					}
				} else {
					?>
					<tr>
						<td colspan="8"><?php _e( 'No Site Templates Available', 'wp-cloud-server' ); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>

		<?php
		if ( ! empty( $templates ) ) { 
			foreach ( $templates as $template ) {
				if ( "ploi_site_template" == $template['template_name'] ) {
				$site_label = strtolower( str_replace( " ", "-", $template['name'] ) );
				?>
				<div id="managed-site-template-modal-<?php echo $site_label; ?>" uk-modal>
    			    <div class="template-modal uk-modal-dialog uk-modal-body">
					    <button class="uk-modal-close-default" type="button" uk-close></button>
        			    <h2><?php esc_html_e( 'Manage Site Template: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo $template['name']; ?></span></h2>
						<hr class="clear">
							<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_edit_ploi_site_template">
						    <div style="height: 600px" class="content uk-overflow-auto">
							<div class="uk-container uk-container-xsmall">
								<table class="form-table" role="presentation">
									<tbody>
										<tr>
											<th scope="row">Template Name:</th>
											<td>
												<?php wpcs_ploi_site_template_name( $site_label, $template['name'] ); ?>
											</td>
										</tr>
												<tr>
													<th scope="row">Server ID:</th>
													<td>		
														<?php wpcs_ploi_site_template_server_id( $site_label, $template['server_name'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Root Domain:</th>
													<td>		
														<?php wpcs_ploi_site_template_root_domain( $site_label, $template['root_domain'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Project Directory:</th>
													<td>		
														<?php wpcs_ploi_site_template_project_directory( $site_label, $template['project_directory'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Web Directory:</th>
													<td>
														<?php wpcs_ploi_site_template_web_directory( $site_label, $template['web_directory'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">System User:</th>
													<td>
														<?php wpcs_ploi_site_template_system_user( $site_label, $template['system_user'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Web Template:</th>
													<td>
														<?php wpcs_ploi_site_template_web_template( $site_label, $template['web_template_label'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Install App:</th>
													<td>
														<?php
														wpcs_ploi_site_template_install_app( $site_label, $template['web_app'] );
														?>
													</td>
												</tr>
												<tr>
													<th scope="row">Install SSL Certificate:</th>
													<td>
														<?php
														wpcs_ploi_site_template_enable_ssl( $site_label, $template['enable_ssl'] );
														?>
													</td>
												</tr>
												<tr>
													<th scope="row">Sites using template:</th>
													<td>
														<?php wpcs_ploi_site_template_site_counter( $site_label, $template['site_counter'] ); ?>
													</td>
												</tr>
									</tbody>
								</table>
							</div>
							</div>
							<!-- <div style="width: 20px; margin: 0 auto;"><span uk-icon="chevron-down"></span></div> -->
					        <hr class="uk-margin-small-top">
							<a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-site-template-<?php echo $site_label; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
							<p class = "uk-text-right uk-margin-remove-bottom">
								<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
								<?php wpcs_submit_button( 'Update', 'secondary', "update_ploi_site_template_{$site_label}", false ); ?>
							</p>
						</form>
						<div id="delete-site-template-<?php echo $site_label; ?>" uk-modal>
    						<div class="server-modal uk-modal-dialog uk-modal-body">
								<div class="uk-modal-body">
									<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete the '{$template['name']}' template! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            						<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        						</div>
								<div class="uk-modal-footer uk-text-right">
									<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
										<input type="hidden" name="action" value="handle_delete_ploi_site_template">
										<input type="hidden" name="wpcs_ploi_confirm_site_template_delete" value="true">
										<input type="hidden" name="wpcs_ploi_confirm_site_template_id" value="<?php echo $template['slug'];?>">
										<?php
										wp_nonce_field( "wpcs_handle_delete_ploi_site_template_{$template['slug']}", "wpcs_handle_delete_ploi_site_template_{$template['slug']}" );
										?>
										<div class="uk-button-group uk-margin-remove-bottom">
											<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-site-template-modal-<?php echo $template['slug']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            								<?php wpcs_submit_button( 'Confirm Delete', 'danger', "delete_template_{$site_label}", false ); ?>
										</div>
									</form>
								</div>
    						</div>
						</div>
					</div>
				</div>
				<?php
				}
			}
		}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_list_managed_site_template', 10, 3 );