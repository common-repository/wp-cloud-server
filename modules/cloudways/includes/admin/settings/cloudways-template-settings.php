<?php

/**
*  Update Cloudways Template Name
*
*  @since 1.0.0
*/
function wpcs_cloudways_template_name( $value='', $edit = false ) {
	$readonly	= ( $edit ) ? 'readonly' : '';
	$class		= ( $edit ) ? 'wpcs_cloudways_edit_template_name' : 'wpcs_cloudways_template_name';
	?>
	<input class="w-400" type="text" placeholder="Template Name" name="<?php echo $class; ?>" id="<?php echo $class; ?>" value="<?php echo $value; ?>" <?php echo $readonly; ?>>
	<p class="text_desc">[ You can use any valid text, numeric, and space characters ]</p>
	<?php
}

/**
*  Update Cloudways Template Hostname
*
*  @since 1.0.0
*/
function wpcs_cloudways_template_host_name( $value='', $edit = false ) {
		$class		= ( $edit ) ? 'wpcs_cloudways_edit_template_host_name' : 'wpcs_cloudways_template_host_name';
		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		?>
		<select class="w-400" name="<?php echo $class; ?>" id="<?php echo $class; ?>">
			<?php
			if ( !empty( $host_names ) ) {
				?><optgroup label="Select Hostname"><?php
				foreach ( $host_names as $key => $host_name ) {
			?>
            <option value="<?php echo "{$host_name['hostname']}|{$host_name['label']}"; ?>" <?php selected( $value, $host_name['label'] ); ?>><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
			<?php } } ?>
			</optgroup>
			<optgroup label="User Choice">
				<option value="[Customer Input]|[Customer Input]" <?php selected( $value, '[Customer Input]' ); ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php
	}

/**
*  Update Cloudways Template Regions
*
*  @since 1.0.0
*/
function wpcs_cloudways_template_regions( $value='', $edit = false, $cloud='' ) {
	$class		= ( $edit ) ? 'wpcs_cloudways_edit_template_region' : 'wpcs_cloudways_template_region';
	$cloud		= ( '' == $cloud ) ? 'do' : $cloud;
	$regions 	= wpcs_cloudways_regions_list();
	?>

		<select class="w-400" name="<?php echo $class; ?>" id="<?php echo $class; ?>">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions[$cloud] as $key => $region ) {
					$region_name	= $region['name'];
					$region_id		= $region['id'];
				?>
    				<option value='<?php echo "{$region_id}|{$region_name}"; ?>' <?php selected( $value, $region_name ); ?>><?php echo $region_name; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Regions Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
function wpcs_cloudways_template_providers( $template='', $value='', $edit = false ) {
		$data		= ( '' == $template ) ? '' : "data-template='{$template}'";
		$id			= ( $edit ) ? "wpcs_cloudways_edit_template_providers_{$template}" : 'wpcs_cloudways_template_providers';
		$name		= ( $edit ) ? "wpcs_cloudways_edit_template_providers" : 'wpcs_cloudways_template_providers';
		$providers	= wpcs_cloudways_providers_list();

		?>

		<select class="w-400" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $data; ?>>
			<optgroup label="Select Cloud Provider">
			<?php
			if ( !empty( $providers ) ) {
				foreach ( $providers as $key => $provider ) {
				?>
    				<option value='<?php echo "{$key}|{$provider['name']}"; ?>' <?php selected( $value, $provider['name'] ); ?>><?php echo $provider['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Providers Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}



/**
*  Update Cloudways Template Size
*
*  @since 3.0.3
*/
function wpcs_cloudways_template_size( $value='', $edit = false, $cloud='' ) {
	$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_size' : 'wpcs_cloudways_template_size';
	$cloud	= ( '' == $cloud ) ? 'do' : $cloud;
	$plans 	= wpcs_cloudways_plans_list();

		?>

		<select class="w-400" name="<?php echo $class; ?>" id="<?php echo $class; ?>">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans[$cloud][0] as $key => $plan ) {
				?>
    				<option value="<?php echo $plan; ?>" <?php selected( $value, $plan ); ?>><?php echo $plan; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Plans Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	function wpcs_cloudways_template_app_name( $value='', $edit = false ) {
		$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_app_name' : 'wpcs_cloudways_template_app_name';
		?>
		<input class="w-400" type="text" placeholder="application-name" name="<?php echo $class; ?>" id="<?php echo $class; ?>" value="<?php echo $value; ?>"/>
		<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>
		<?php
	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	function wpcs_cloudways_template_app( $value='', $edit = false ) {
		$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_app' : 'wpcs_cloudways_template_app';

		$apps = wpcs_cloudways_apps_list();
		?>

		<select  class="w-400" name="<?php echo $class; ?>" id="<?php echo $class; ?>">
			<optgroup label="Select App">
			<?php
			if ( !empty( $apps ) ) {
				foreach ( $apps as $key => $app ) {
					foreach ( $app as $ver ) {
						if ( 'wordpressdefault' !== $ver['application'] ) {
				?>
    				<option value="<?php echo "{$ver['application']}|{$ver['app_version']}|{$ver['label']}"; ?>" <?php selected( $value, $ver['application'] ); ?>><?php echo "{$ver['label']} {$ver['app_version']}"; ?></option>
				<?php
						}
					}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	function wpcs_cloudways_template_project( $value='', $edit = false ) {
		$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_project' : 'wpcs_cloudways_template_project';

		$projects = wpcs_cloudways_project_list();
		?>

		<select class="w-400" name="<?php echo $class; ?>" id="<?php echo $class; ?>">
			<optgroup label="Select Project">
				<?php
				if ( !empty( $projects ) ) {
					foreach ( $projects as $id =>$project ) {
						?>
    					<option value="<?php echo $project; ?>" <?php selected( $value, $project ); ?>><?php echo $project; ?></option>
						<?php
					}
				} else {
					?>
					<option value="false"><?php _e( '-- No Projects Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>
		<?php

	}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_cloudways_template_site_counter( $value='', $edit = false ) {
	$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_site_counter' : 'wpcs_cloudways_template_site_counter';
	?>
	<input class="w-400" type="text" name="<?php echo $class; ?>" id="<?php echo $class; ?>" value="<?php echo $value; ?>" readonly>
	<?php
}

/**
*  Set Database Volume Size
*
*  @since 3.0.3
*/
function wpcs_cloudways_template_db_volume_size( $value='', $edit = false ) {
	$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_db_volume_size' : 'wpcs_cloudways_template_db_volume_size';
		?>
	<input class="w-400" type="text" placeholder="20" name="<?php echo $class; ?>" id="<?php echo $class; ?>" value="<?php echo $value; ?>">
	<p class="text_desc">[ Only required for Amazon or Google Servers ]</p>
	<?php
}

/**
*  Set Database Volume Size
*
*  @since 3.0.3
*/
function wpcs_cloudways_template_data_volume_size( $value='', $edit = false ) {
	$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_data_volume_size' : 'wpcs_cloudways_template_data_volume_size';
		?>
	<input class="w-400" type="text" placeholder="20" name="<?php echo $class; ?>" id="<?php echo $class; ?>" value="<?php echo $value; ?>">
	<p class="text_desc">[ Only required for Amazon or Google Servers ]</p>
	<?php
}

/**
*  Enable New Server Email
*
*  @since 3.0.4
*/
function wpcs_cloudways_template_send_email( $value='', $edit = false ) {
	$class	= ( $edit ) ? 'wpcs_cloudways_edit_template_send_email' : 'wpcs_cloudways_template_send_email';
	?>
	<input type="checkbox" id="<?php echo $class; ?>" name="<?php echo $class; ?>" value="1" <?php checked( $value, true ); ?>>
	<?php
}