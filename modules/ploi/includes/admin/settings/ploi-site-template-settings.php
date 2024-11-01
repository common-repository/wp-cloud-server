<?php
/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_name( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_site_template_name_{$template}";
	?>
	<input class="w-400" type="text" placeholder="Template Name" id="<?php echo $id; ?>" name="wpcs_ploi_site_template_name" value="<?php echo $value; ?>" readonly>
	<?php
}

/**
*  Update Ploi Template Credentials
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_server_id( $template='', $value='', $edit = false ) {

	$id				= "wpcs_ploi_site_template_server_id_{$template}";
	$credentials	= wpcs_ploi_api_user_request( 'server-providers/list' );
	$servers		= wpcs_ploi_call_api_list_servers();
	?>
	<select class='w-400' name="wpcs_ploi_site_template_server_id" id="<?php echo $id; ?>">
		<optgroup label="Servers">
			<?php
			if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
				foreach ( $servers as $server ) {
					?>
					<option value='<?php echo "{$server['name']}|{$server['id']}"; ?>'<?php selected( $value, $server['name'] ); ?>><?php echo $server['name']; ?></option>
					<?php
				}
			} else {
				?>
				<option value="not_available"<?php selected( $value, "not_available" ); ?>>-- No Servers Available --</option>
				<?php
			}
			?>
		</optgroup>
	</select>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_root_domain( $template='', $value='', $edit = false ) {

	$id			= "wpcs_ploi_site_template_root_domain_{$template}";
	$class		= ( $edit ) ? 'wpcs_ploi_edit_site_template_root_domain' : 'wpcs_ploi_site_template_root_domain';
	$host_names	= get_option( 'wpcs_host_names' );
	?>
	<select class='w-400' name="wpcs_ploi_site_template_root_domain" id="<?php echo $id; ?>">
	<?php if ( !empty( $host_names ) ) { ?>
								<optgroup label="Select Hostname">
									<?php foreach ( $host_names as $key => $host_name ) { ?>
            						<option value='<?php echo "{$host_name['label']}|{$host_name['hostname']}" ?>'<?php selected( $value, $host_name['label'] ); ?>><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
									<?php } } ?>
								</optgroup>
								<optgroup label="User Choice">
									<option value="[Customer Input]|[Customer Input]"<?php selected( $value, '[Customer Input]' ); ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_project_directory( $template='', $value='', $edit = false, $cloud_provider=null ) {

	$id				= "wpcs_ploi_site_template_project_directory_{$template}";
	$class			= ( $edit ) ? 'wpcs_ploi_edit_site_template_project_directory' : 'wpcs_ploi_site_template_project_directory';
	$cloud_provider	= strtolower( str_replace( " ", "_", $cloud_provider ) );
	$cloud_provider	= ( empty( $cloud_provider ) ) ? 'digitalocean' : $cloud_provider;
	$regions		= call_user_func("wpcs_{$cloud_provider}_regions_list");
	$list			= wpcs_ploi_api_user_request( 'server-providers/list' );
	?>
	<input class='w-400' type='text' placeholder='/' id="<?php echo $id; ?>" name='wpcs_ploi_site_template_project_directory' value="<?php echo $value; ?>">
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_web_directory( $template='', $value='', $edit = false, $cloud_provider=null ) {
	$id		= "wpcs_ploi_site_template_web_directory_{$template}";
	?>
	<input class='w-400' type='text' placeholder='/' id="<?php echo $id; ?>" name='wpcs_ploi_site_template_web_directory' value="<?php echo $value; ?>">
	<?php
}

/**
*  Update Ploi Template Database
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_system_user( $template='', $value='', $edit = false ) {

	$id					= "wpcs_ploi_site_template_system_user_{$template}";
	$servers			= wpcs_ploi_call_api_list_servers();

	if ( isset( $servers[0]['id'] ) ) {
		$args['server_id']	= $servers[0]['id'];
		$users				= wpcs_ploi_api_system_users_request( 'system-users/list', $args );
	}
	

	?>
		<select class='w-400' name="wpcs_ploi_site_template_system_user" id="<?php echo $id; ?>">
			<optgroup label="System User">
				<?php
				if ( ( isset( $users ) ) && is_array( $users ) ) {
					?><option value="ploi">ploi</option><?php
					foreach ( $users as $user ) {
						?>
						<option value='<?php echo "{$user['name']}"; ?>'<?php selected( $value, $user['name'] ); ?>><?php echo $user['name']; ?></option>
						<?php
					}
				} else {
					?>
					<option value="ploi">ploi</option>
					<?php
				}
				?>
			</optgroup>
		</select>
	<?php
}

/**
*  Update Ploi Template PHP Version
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_web_template( $template='', $value='', $edit = false ) {

	$id			= "wpcs_ploi_site_template_web_template_{$template}";
	$servers	= wpcs_ploi_api_web_templates_request( 'templates/list' );
	?>
	
		<select class='w-400' name="wpcs_ploi_site_template_web_template" id="<?php echo $id; ?>">
		<optgroup label="Web Templates">
							<?php
							if ( ( isset( $servers ) ) && is_array( $servers ) ) {
								foreach ( $servers as $server ) {
								?>
									<option value='<?php echo "{$server['label']}|{$server['id']}"; ?>'<?php selected( $value, $server['label'] ); ?>><?php echo $server['label']; ?></option>
								<?php
								}
							} else {
								?>
								<option value="not_available">-- No Web Templates Available --</option>
							<?php
							}
							?>
							</optgroup>
						</select>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_install_app( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_site_template_install_app_{$template}";
	$class	= ( $edit ) ? 'wpcs_ploi_edit_site_template_install_app' : 'wpcs_ploi_site_template_install_app';
	$git_repos = wpcs_github_repos_list();
	?>
	<select style="width: 400px" name="wpcs_ploi_site_template_install_app" id="<?php echo $id; ?>">
	<optgroup  label="Select Application">
		<option value="no-application|no-app|No Application"<?php selected( $value, 'No Application' ); ?>><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
		<option value="wordpress|app|WordPress"<?php selected( $value, 'wordpress' ); ?>><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
		<option value="nextcloud|app|Nextcloud"<?php selected( $value, 'nextcloud' ); ?>><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
		<?php if ( !empty( $git_repos ) ) { ?>
				</optgroup>
				<optgroup label="Select GIT Repository">
					<?php foreach ( $git_repos as $key => $git_repo ) {
						?>
						<option value='<?php echo "{$key}|git|{$git_repo}"; ?>' <?php selected( $value, $key );?>><?php echo $git_repo; ?></option>
						<?php
					}
			}
		?>
		</optgroup>
	</select>
	<?php
}

/**
*  Update Ploi Template Enable SSL
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_enable_ssl( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_site_template_enable_ssl_{$template}";
	$class	= ( $edit ) ? 'wpcs_ploi_edit_site_template_enable_ssl' : 'wpcs_ploi_site_template_enable_ssl';
	?>
	<input class="w-400" type="checkbox" id="<?php echo $id; ?>" name="wpcs_ploi_site_template_enable_ssl" value="1" <?php checked( $value, 1 );?>>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_site_template_site_counter( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_site_template_site_counter_{$template}";
	$class	= ( $edit ) ? 'wpcs_ploi_edit_site_template_site_counter' : 'wpcs_ploi_site_template_site_counter';
	?>
	<input class="w-400" type="text" id="<?php echo $id; ?>" name="wpcs_ploi_site_template_site_counter" value="<?php echo $value; ?>" readonly>
	<?php
}