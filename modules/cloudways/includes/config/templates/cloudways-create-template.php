<?php

function wpcs_cloudways_create_template_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-create-template' !== $tabs_content ) {
		return;
	}

	$api_status		= wpcs_check_cloud_provider_api('Cloudways', null, false);
	$attributes		= ( $api_status ) ? '' : 'disabled';
	$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );

	?>
	<div class="content">
		<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="create_cloudways_template">
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">Template Name:</th>
							<td>
								<?php wpcs_cloudways_template_name(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Application Name:</th>
							<td>
								<?php wpcs_cloudways_template_app_name(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Hostname:</th>
							<td>
								<?php wpcs_cloudways_template_host_name(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Application:</th>
							<td>
								<?php wpcs_cloudways_template_app(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Cloud Provider:</th>
							<td>
								<?php wpcs_cloudways_template_providers(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Region:</th>
							<td>
								<?php wpcs_cloudways_template_regions(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Size:</th>
							<td>
								<?php wpcs_cloudways_template_size(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Project:</th>
							<td>
								<?php wpcs_cloudways_template_project(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Database Volume Size:</th>
							<td>
								<?php wpcs_cloudways_template_db_volume_size(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Data Volume Size:</th>
							<td>
								<?php wpcs_cloudways_template_data_volume_size(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">Send Email:</th>
							<td>
								<?php wpcs_cloudways_template_send_email(); ?>
							</td>
						</tr>
					</tbody>
				</table>
			<hr>
			<?php wpcs_submit_button( 'Create Template', 'secondary', 'create_template', null, $attributes ); ?>
		</form>
	</div>
<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_create_template_template', 10, 3 );