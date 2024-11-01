<?php

function wpcs_cloudways_template_details_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-template-details' !== $tabs_content ) {
		return;
	}

	$module_data		= get_option( 'wpcs_module_list' );
	$template_data		= get_option( 'wpcs_template_data_backup' );
	$completed_tasks	= get_option( 'wpcs_tasks_completed', array());
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Cloudways Templates', 'wp-cloud-server' ); ?></h2>
	    <table class="uk-table uk-table-striped">
    	    <thead>
        	    <tr>
            	    <th><?php _e( 'Name', 'wp-cloud-server' ); ?></th>
				    <th><?php _e( 'Application', 'wp-cloud-server' ); ?></th>
				    <th><?php _e( 'Region', 'wp-cloud-server' ); ?></th>
				    <th><?php _e( 'Hostname', 'wp-cloud-server' ); ?></th>
				    <th><?php _e( 'Provider', 'wp-cloud-server' ); ?></th>
				    <th><?php _e( 'Sites', 'wp-cloud-server' ); ?></th>
				    <th class="uk-table-shrink"><?php _e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 	    </tr>
    	    </thead>
    	    <tbody>
			    <?php
				$templates = $module_data['Cloudways']['templates'];
			
				if ( ! empty( $templates ) ) { 
					foreach ( $templates as $template ) {
						$server_label = strtolower( str_replace( " ", "-", $template['name'] ) );
					?>
						<tr>
							<td><?php echo $template['name']; ?></td>
							<td><?php echo isset( $template['app_label'] ) ? "{$template['app_label']} {$template['app_version']}" : $template['image']; ?></td>
							<td><?php echo $template['region_name']; ?></td>
							<?php $host_name = ( isset( $template['host_name'] ) ) ? $template['host_name'] : 'Not Set'; ?>
							<td><?php echo $template['host_name_label']; ?></td>
							<td><?php echo $template['cloud_name']; ?></td>
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
						<td colspan="8"><?php _e( 'No Templates Available', 'wp-cloud-server' ); ?></td>
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
						<input type="hidden" name="action" value="handle_edit_cloudways_template">
						<input type="hidden" id="template_name" name="template_name" value="<?php echo $template['name']; ?>">
					    <div class="template-info uk-modal-body" uk-overflow-auto>
						    <div class="content">
								<table class="form-table" role="presentation">
					                <tbody>
						                <tr>
							                <th scope="row">Template Name:</th>
							                <td>
								                <?php wpcs_cloudways_template_name( $template['name'], true ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Application Name:</th>
							                <td>
								                <?php wpcs_cloudways_template_app_name( $template['app_name'], true ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Hostname:</th>
							                <td>
								                <?php wpcs_cloudways_template_host_name( $template['host_name_label'], true ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Cloud Provider:</th>
							                <td>
								                <?php wpcs_cloudways_template_providers( $server_label, $template['cloud_name'], true ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Application:</th>
							                <td>
								                <?php wpcs_cloudways_template_app( $template['image'], true ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Region:</th>
							                <td>
								                <?php wpcs_cloudways_template_regions( $template['region_name'], true, $template['cloud'] ); ?>
							                </td>
						                </tr>
						                <tr>
							                <th scope="row">Size:</th>
							                <td>
								                <?php wpcs_cloudways_template_size( $template['size'], true, $template['cloud'] ); ?>
							                </td>
						                </tr>
										<tr>
											<th scope="row">Database Volume Size:</th>
											<td>
												<?php wpcs_cloudways_template_db_volume_size( $template['db_volume_size'], true); ?>
											</td>
										</tr>
										<tr>
											<th scope="row">Data Volume Size:</th>
											<td>
												<?php wpcs_cloudways_template_data_volume_size( $template['data_volume_size'], true); ?>
											</td>
										</tr>
										<tr>
							                <th scope="row">Project:</th>
							                <td>
								                <?php wpcs_cloudways_template_project( $template['project_name'], true ); ?>
							                </td>
						                </tr>
										<tr>
											<th scope="row">Send Email:</th>
											<td>
												<?php
												$send_email = ( isset( $template['send_email'] ) ) ? $template['send_email'] : false;
												wpcs_cloudways_template_send_email( $send_email, true ); ?>
											</td>
										</tr>
										<tr>
							                <th scope="row">Sites using template:</th>
							                <td>
								                <?php wpcs_cloudways_template_site_counter( $template['site_counter'], true ); ?>
							                </td>
						                </tr>
					                </tbody>
				                </table>
							</div>
			        	</div>
					    <div style="width: 20px; margin: 0 auto;"><span uk-icon="chevron-down"></span></div>
					    <hr class="uk-margin-small-top">
						<a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-template-<?php echo $server_label; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
						<p class = "uk-text-right uk-margin-remove-bottom">
							<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
							<?php wpcs_submit_button( 'Update', 'secondary', 'update_cloudways_template', false ); ?>
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
									<input type="hidden" name="action" value="handle_delete_cloudways_template">
									<input type="hidden" name="wpcs_cloudways_confirm_template_delete" value="true">
									<input type="hidden" name="wpcs_cloudways_confirm_template_id" value="<?php echo $template['name'];?>">
									<?php
									wp_nonce_field( 'wpcs_handle_delete_cloudways_template', 'wpcs_handle_delete_cloudways_template' );
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
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_template_details_template', 10, 3 );