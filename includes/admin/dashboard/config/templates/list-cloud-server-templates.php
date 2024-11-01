<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

	$module_data		= get_option( 'wpcs_module_list' );
	$template_data		= get_option( 'wpcs_template_data_backup' );
	$completed_tasks	= get_option( 'wpcs_tasks_completed', array());
	
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Templates', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php _e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Size', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Image', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Region', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Hostname', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Sites', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php _e( 'Manage', 'wp-cloud-server' ); ?></th>
			</tr>
    	</thead>
    	<tbody>
			<?php
				
				$templates = $module_data['DigitalOcean']['templates'];
				
				if ( ! empty( $templates ) ) { 
					foreach ( $templates as $template ) {
						$server_label = strtolower( str_replace( " ", "-", $template['name'] ) );
					?>
						<tr>
							<td><?php echo $template['name']; ?></td>
							<td><?php echo $template['size_name']; ?></td>
							<td><?php echo $template['image_name']; ?></td>
							<?php $region = ( $template['region'] == 'userselected' ) ? '[Customer Input]' : $template['region_name']; ?>
							<td><?php echo $region; ?></td>
							<?php $host_name = ( isset( $template['host_name'] ) ) ? $template['host_name'] : 'Not Set'; ?>
							<td><?php echo $template['host_name_label']; ?></td>
							<?php $ssh_key = ( $template['ssh_key_name'] == 'no-ssh-key' ) ? '[Password Only]' : $template['ssh_key_name']; ?>
							<?php $user_data = ( $template['user_data'] == 'no-startup-script' ) ? 'No Script' : $template['user_data']; ?>
							<td><?php echo $template['site_counter']; ?></td>
							<td>
								<a class="uk-link" href="#managed-template-modal-<?php echo $server_label; ?>" uk-toggle>Manage</a>
							</td>
						</tr>
					<?php
					}
				} else {
					?>
					<tr>
						<td colspan="7"><?php _e( 'No Templates Available', 'wp-cloud-server' ); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
</div>

		<?php
				if ( ! empty( $templates ) ) { 
					foreach ( $templates as $template ) {
						$server_label = strtolower( str_replace( " ", "-", $template['name'] ) );
					?>

			        <div id="managed-template-modal-<?php echo $server_label; ?>" uk-modal>
    			        <div class="template-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Template: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo $template['name']; ?></span></h2>
					        <hr class="clear">
							<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
								<input type="hidden" name="action" value="handle_edit_digitalocean_template">
						        	<div class="content">
										<table class="form-table" role="presentation">
											<tbody>
												<tr>
													<th scope="row">Template Name:</th>
													<td>
														<?php wpcs_digitalocean_template_name( $template['name'] ); ?>
													</td>
												</tr>
												
												<tr>
													<th scope="row">Server Hostname:</th>
													<td>		
														<?php wpcs_digitalocean_template_hostname( $template['host_name_label'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Image:</th>
													<td>		
														<?php wpcs_digitalocean_template_type( $template['image_name'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Region:</th>
													<td>
														<?php wpcs_digitalocean_template_regions( $template['region_name'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Size:</th>
													<td>
														<?php wpcs_digitalocean_template_size( $template['size_name'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Admin SSH Key:</th>
													<td>
														<?php wpcs_digitalocean_template_ssh_key( $template['ssh_key_name'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Startup Script:</th>
													<td>
														<?php wpcs_digitalocean_template_startup_script( $template['user_data'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Enable Server Backups:</th>
													<td>
														<?php wpcs_digitalocean_template_enable_backups( $template['backups'] ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Sites using template:</th>
													<td>
														<?php wpcs_digitalocean_template_site_counter( $template['site_counter'] ); ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
					        	<hr>
								
									
					        <a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-template-<?php echo $server_label; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
								<p class = "uk-text-right uk-margin-remove-bottom">
								<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
								<?php wpcs_submit_button( 'Update', 'secondary', 'update_digitalocean_template', false ); ?>
    			        		</p>
								</form>
					<div id="delete-template-<?php echo $server_label; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
				<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete the '{$template['name']}' template! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_delete_digitalocean_template">
							<input type="hidden" name="wpcs_digitalocean_confirm_template_delete" value="true">
							<input type="hidden" name="wpcs_digitalocean_confirm_template_id" value="<?php echo $template['name'];?>">
							<?php
							wp_nonce_field( 'wpcs_handle_delete_digitalocean_template', 'wpcs_handle_delete_digitalocean_template' );
							?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-template-modal-<?php echo $server_label; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', 'delete_server', false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>

	<?php
					}
				}