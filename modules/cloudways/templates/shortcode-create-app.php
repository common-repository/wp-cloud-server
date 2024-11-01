<?php
/**
 * This template is used to display the profile editor with [edd_profile_editor]
 */
?>
<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
			<input type="hidden" name="action" value="cloudways_create_app">
			<?php wp_nonce_field( 'wpcs_cloudways_create_app', 'wpcs_cloudways_create_app' ); ?>

		<?php do_action( 'edd_profile_editor_fields_top' ); ?>

		<fieldset id="edd_profile_personal_fieldset">

			<legend id="edd_profile_name_label"><?php _e( 'Change your Name', 'easy-digital-downloads' ); ?></legend>

			<p id="edd_profile_first_name_wrap">
				<label for="wpcs_cloudways_create_app_name"><?php _e( 'Name', 'easy-digital-downloads' ); ?></label>
				<input name="wpcs_cloudways_create_app_name" id="wpcs_cloudways_create_app_name" class="text edd-input" type="text" value="" />
			</p>

			<p id="edd_profile_last_name_wrap">
				<label for="wpcs_cloudways_create_app_server"><?php _e( 'Last Name', 'easy-digital-downloads' ); ?></label>
				<?php $servers = wpcs_cloudways_server_list(); ?>
				<select  class="w-400" name="wpcs_cloudways_create_app_server" id="wpcs_cloudways_create_app_server">
           	 		<optgroup label="Server">
					<?php
					if ( !empty( $servers ) ) {
						foreach ( $servers as $id => $server ) {
						?>
    					<option value="<?php echo $id; ?>"><?php echo $server; ?></option>
						<?php
						}
					} else {
						?>
						<option value="false"><?php _e( '-- No Servers Available --', 'wp-cloud-server' ); ?></option>
						<?php
					}
					?>
					</optgroup>
				</select>
			</p>
			<?php
					$apps = wpcs_cloudways_apps_list();
		?>
<p>
		<select  class="w-400" name="wpcs_cloudways_create_app_application" id="wpcs_cloudways_create_app_application">
			<optgroup label="Apps">
			<?php
			if ( !empty( $apps ) ) {
				foreach ( $apps as $key => $app ) {
					foreach ( $app as $ver ) {
				?>
    				<option value="<?php echo "{$ver['application']}|{$ver['app_version']}"; ?>"><?php echo "{$ver['label']} {$ver['app_version']}"; ?></option>
				<?php
					}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select></p>
			
				<?php $projects = wpcs_cloudways_project_list();
		?>
<p>
		<select class="w-400" name="wpcs_cloudways_create_app_project" id="wpcs_cloudways_create_app_project">
			<optgroup label="Projects">
				<?php
				if ( !empty( $projects ) ) {
					foreach ( $projects as $id =>$project ) {
						?>
    					<option value="<?php echo $project; ?>"><?php echo $project; ?></option>
						<?php
					}
					?>
					<option value="">-- No Project --</option>
					<?php
				} else {
					?>
					<option value="false"><?php _e( '-- No Projects Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>

			<?php do_action( 'edd_profile_editor_after_email' ); ?>

	</fieldset>

			<p id="edd_profile_submit_wrap">
				<input name="submit" id="edd_profile_editor_submit" type="submit" class="edd_submit edd-submit" value="<?php _e( 'Save Changes', 'easy-digital-downloads' ); ?>"/>
			</p>

		



		<?php do_action( 'edd_profile_editor_fields_bottom' ); ?>

	</form>
