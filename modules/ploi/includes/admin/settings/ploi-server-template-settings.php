<?php
/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_name( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_edit_server_template_name";
	?>
	<input class="w-400" type="text" placeholder="Template Name" id="<?php echo $id; ?>" name="wpcs_ploi_edit_server_template_name" value="<?php echo $value; ?>" readonly>
	<?php
}

/**
*  Update Ploi Template Credentials
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_credentials( $template='', $value='', $edit = false ) {
	$data			= ( '' == $template ) ? '' : "data-template='{$template}'";
	$id				= "wpcs_ploi_edit_server_template_credentials_{$template}";
	$credentials	= wpcs_ploi_api_user_request( 'server-providers/list' );
	?>
	
		<select class='w-400' name="wpcs_ploi_edit_server_template_credentials" id="<?php echo $id; ?>" <?php echo $data; ?>>
		<?php
			if ( is_array( $credentials ) ) {
				?>
				<optgroup label="Select Provider">

				<?php
				foreach ( $credentials as $credential ) {
				?>
    				<option value='<?php echo "{$credential['name']}|{$credential['id']}"; ?>'<?php selected( $value, $credential['name'] ); ?>><?php echo $credential['name']; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="no_value">-- No Credentials Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
	<?php
}

/**
*  Update Ploi Template Credentials
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_root_domain( $template='', $value='', $edit = false ) {

	$host_names		= get_option( 'wpcs_host_names' );
	?>
	<select class="w-400" name="wpcs_ploi_edit_server_template_root_domain" id="wpcs_ploi_edit_server_template_root_domain">
		<?php
		if ( !empty( $host_names ) ) {
		?><optgroup label="Select Hostname"><?php
		foreach ( $host_names as $key => $host_name ) {
			?>
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
function wpcs_ploi_server_template_type( $template='', $value='', $edit = false ) {

	$id		= "wpcs_ploi_edit_server_template_type";
	?>
	<select class='w-400' name="wpcs_ploi_edit_server_template_type" id="<?php echo $id; ?>">
		<optgroup label="Select Server Type">
			<option value="Server|server"<?php selected( $value, 'Server' ); ?>><?php esc_html_e( 'Server', 'wp-cloud-server-ploi' ); ?></option>
			<option value="Load Balancer|load-balancer"<?php selected( $value, 'Load Balancer' ); ?>><?php esc_html_e( 'Load Balancer', 'wp-cloud-server-ploi' ); ?></option>
			<option value="Database Server|database-server"<?php selected( $value, 'Database Server' ); ?>><?php esc_html_e( 'Database Server', 'wp-cloud-server-ploi' ); ?></option>
			<option value="Redis Server|redis-server"<?php selected( $value, 'Redis Server' ); ?>><?php esc_html_e( 'Redis Server', 'wp-cloud-server-ploi' ); ?></option>
		</optgroup>
	</select>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_regions( $template='', $value='', $cloud_provider=null, $edit = false ) {

	$id				= "wpcs_ploi_edit_server_template_region";
	$list			= wpcs_ploi_api_user_request( 'server-providers/list' );
	?>

	<select class='w-400' name="wpcs_ploi_edit_server_template_region" id="<?php echo $id; ?>">
	<?php
			if ( is_array( $list ) ) {
				?>
				<optgroup label="Regions">
				<?php
				foreach ( $list as $plans ) {
					if ( $plans['name'] == $cloud_provider ) {
						foreach ( $plans['provider']['regions'] as $key => $plan ) {
							?>
    						<option value='<?php echo "{$plan['name']}|{$plan['id']}"; ?>'<?php selected( $value, $plan['name'] ); ?>><?php echo $plan['name']; ?></option>
							<?php
						}
					}
				}
			} else {
				?>
    				<option value="no_value">-- No Regions Available --</option>
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
function wpcs_ploi_server_template_size( $template='', $value='', $cloud_provider=null, $edit = false ) {
	$id	= "wpcs_ploi_edit_server_template_size";
	$list = wpcs_ploi_api_user_request( 'server-providers/list' );

						?>
						<select style="width: 400px" name="wpcs_ploi_edit_server_template_size" id="wpcs_ploi_edit_server_template_size">
							<?php
							if ( is_array( $list ) ) {
								?>
								<optgroup label="Plans">
								<?php
								foreach ( $list as $plans ) {
									if ( $plans['name'] == $cloud_provider ) {
										foreach ( $plans['provider']['plans'] as $key => $plan ) {
											?>
											<option value='<?php echo "{$plan['name']}|{$plan['id']}"; ?>'<?php selected( $value, $plan['id'] ); ?>><?php echo $plan['description']; ?></option>
											<?php
										}
									}
								}
							} else {
								?>
									<option value="no_value">-- No Plans Available --</option>
								<?php
							}
							?>
							</optgroup>
						</select>
	<?php
}

/**
*  Update Ploi Template Database
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_database( $template='', $value='', $edit = false ) {

	$id		= "wpcs_ploi_edit_server_template_database";
	$class	= ( $edit ) ? 'wpcs_ploi_edit_edit_server_template_database' : 'wpcs_ploi_edit_server_template_database';
	$plans	= wpcs_ploi_database_list();
	?>
	<select style="width: 25rem;" name="wpcs_ploi_edit_server_template_database" id="<?php echo $id; ?>">
	<optgroup label="Database">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $label => $id ) {
				?>
    				<option value='<?php echo "{$label}|{$id}"; ?>'<?php selected( $value, $label ); ?>><?php echo $label; ?></option>
				<?php
				}
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
function wpcs_ploi_server_template_php_version( $template='', $value='', $edit = false ) {
	?>
	<select style="width: 400px" name="wpcs_ploi_edit_server_template_php_version" id="wpcs_ploi_edit_server_template_php_version">
		<option value="none"><?php esc_html_e( '-- No PHP Version Installed --', 'wp-cloud-server-ploi' ); ?></option>
		<option value="8.0"<?php selected( $value, '8.0' ); ?>><?php esc_html_e( 'PHP 8.0', 'wp-cloud-server-ploi' ); ?></option>
		<option value="7.4"<?php selected( $value, '7.4' ); ?>><?php esc_html_e( 'PHP 7.4', 'wp-cloud-server-ploi' ); ?></option>
        <option value="7.3"<?php selected( $value, '7.3' ); ?>><?php esc_html_e( 'PHP 7.3', 'wp-cloud-server-ploi' ); ?></option>
        <option value="7.2"<?php selected( $value, '7.2' ); ?>><?php esc_html_e( 'PHP 7.2', 'wp-cloud-server-ploi' ); ?></option>
        <option value="7.1"<?php selected( $value, '7.1' ); ?>><?php esc_html_e( 'PHP 7.1', 'wp-cloud-server-ploi' ); ?></option>
	</select>
	<?php
}

/**
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_webserver( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_edit_server_template_webserver";
	?>
	<select style="width: 400px" name="wpcs_ploi_edit_server_template_webserver" id="<?php echo $id; ?>">
		<option value="nginx"<?php selected( $value, 'nginx' ); ?>><?php esc_html_e( 'NGINX', 'wp-cloud-server-ploi' ); ?></option>
	</select>	<?php
}

/**
*  Update Ploi Install Application
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_install_app( $template='', $value='', $edit = false ) {
	$id			= "wpcs_ploi_edit_server_template_install_app";
	$git_repos	= wpcs_github_repos_list();
	?>
	<select style="width: 400px" name="wpcs_ploi_edit_server_template_install_app" id="<?php echo $id; ?>">
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
*  Update Ploi Module Status
*
*  @since 3.0.6
*/
function wpcs_ploi_server_template_site_counter( $template='', $value='', $edit = false ) {
	$id	= "wpcs_ploi_edit_server_template_site_counter";
	?>
	<input class="w-400" type="text" id="<?php echo $id; ?>" name="wpcs_ploi_edit_server_template_site_counter" value="<?php echo $value; ?>" readonly>
	<?php
}