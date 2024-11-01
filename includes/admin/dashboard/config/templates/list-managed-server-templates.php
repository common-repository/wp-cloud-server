<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$module_data		= get_option( 'wpcs_module_list' );
$template_data		= get_option( 'wpcs_template_data_backup' );
$completed_tasks	= get_option( 'wpcs_tasks_completed', array());
?>
<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'ServerPilot Templates', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php _e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Provider', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Region', 'wp-cloud-server' ); ?></th>
           		<th><?php _e( 'Size', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Image', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Plan', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php _e( 'Manage', 'wp-cloud-server' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
						$templates = $module_data['ServerPilot']['templates'];
			
			if ( ! empty( $templates ) ) { 
				foreach ( $templates as $template ) {
					$server_label = strtolower( str_replace( " ", "-", $template['name'] ) );
				?>
        			<tr>
						<td><?php echo $template['name']; ?></td>
						<td><?php echo $template['module']; ?></td>
						<?php $region = ( $template['region_name'] == 'userselected' ) ? '[Customer Input]' : $template['region_name']; ?>
            			<td><?php echo $region; ?></td>
						<?php
						$change	 = array(".00 ", " BW", ",");
						$replace = array("", "", ",");
						$size = str_replace($change, $replace, $template['size_name']);
						?>
						<td><?php echo $size; ?></td>
						<td><?php echo $template['image_name']; ?></td>
						<?php $plan = ( $template['plan'] == 'first_class' ) ? 'First Class' : ucfirst($template['plan']); ?>
						<td><?php echo $plan; ?></td>
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
								<input type="hidden" name="action" value="handle_edit_serverpilot_template">
						        	<div class="content">
										<table class="form-table" role="presentation">
											<tbody>
												<tr>
													<th scope="row">Template Name:</th>
													<td>
														<?php wpcs_serverpilot_template_name( $server_label, $template['name'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Cloud Provider:</th>
													<td>		
														<?php wpcs_serverpilot_template_module( $server_label, $template['module'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Image:</th>
													<td>		
														<?php wpcs_serverpilot_template_type( $server_label, $template['image_name'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Region:</th>
													<td>
														<?php wpcs_serverpilot_template_regions( $server_label, $template['region_name'], $template['module'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Server Size:</th>
													<td>
														<?php wpcs_serverpilot_template_size( $server_label, $template['size_name'], $template['module'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">ServerPilot Plan:</th>
													<td>
														<?php wpcs_serverpilot_template_plan( $server_label, $template['plan'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Admin SSH Key:</th>
													<td>
														<?php wpcs_serverpilot_template_ssh_key( $server_label, $template['ssh_key'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Enable Server Backups:</th>
													<td>
														<?php wpcs_serverpilot_template_enable_backups( $server_label, $template['backups'], true ); ?>
													</td>
												</tr>
												<tr>
													<th scope="row">Enable AutoSSL Queue:</th>
													<td>
														<?php wpcs_serverpilot_template_autossl( $server_label, $template['autossl'], true ); ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
					        	<hr>
					        	<a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-template-<?php echo $server_label; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
								<p class = "uk-text-right uk-margin-remove-bottom">
								<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
								<?php wpcs_submit_button( 'Update', 'secondary', "update_serverpilot_template_{$server_label}", false ); ?>
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
							<input type="hidden" name="action" value="handle_delete_serverpilot_template">
							<input type="hidden" name="wpcs_serverpilot_confirm_template_delete" value="true">
							<input type="hidden" name="wpcs_serverpilot_confirm_template_id" value="<?php echo $template['name'];?>">
							<?php
							wp_nonce_field( "wpcs_handle_delete_serverpilot_template_{$server_label}", "wpcs_handle_delete_serverpilot_template_{$server_label}" );
							?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-template-modal-<?php echo $server_label; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', "delete_server_{$server_label}", false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>

	<?php
					}
				}