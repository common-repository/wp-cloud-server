<?php

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_upcloud_template_name( $value='' ) {
	?>
	<input class="w-400" type="text" placeholder="Template Name" id="wpcs_upcloud_template_name" name="wpcs_upcloud_template_name" value="<?php echo $value; ?>" readonly>
	<p class="text_desc">[ You can use any valid text, numeric, and space characters ]</p>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_upcloud_template_hostname( $value='' ) {
	$host_names	= get_option( 'wpcs_host_names' );
	?>
	<select class="w-400" name="wpcs_upcloud_template_host_name" id="wpcs_upcloud_template_host_name">
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
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_upcloud_template_type( $value='' ) {
	$images			= wpcs_upcloud_os_list();
	$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_upcloud_template_type" id="wpcs_upcloud_template_type">
				<?php
				if ( !empty( $images ) ) {
					?><optgroup label="Select Image"><?php
					foreach ( $images as $key => $image ) {
					?>
    					<option value="<?php echo "{$key}|{$image['name']}"; ?>" <?php selected( $value, $image['name'] ); ?>><?php echo $image['name']; ?></option>
					<?php
					}
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
function wpcs_upcloud_template_regions( $value='' ) {
		$regions = wpcs_upcloud_regions_list();
		?>
		<select class="w-400" name="wpcs_upcloud_template_region" id="wpcs_upcloud_template_region">
			<?php
			if ( !empty( $regions ) ) {
				?> <optgroup label="Select Region"> <?php
				foreach ( $regions as $key => $region ) {
				?>
    				<option value="<?php echo "{$key}|{$region['name']}"; ?>" <?php selected( $value, $region['name'] ); ?>><?php echo $region['name']; ?></option>
				<?php } ?>
				</optgroup>
				<optgroup label="User Choice">
					<option value="userselected|userselected" <?php selected( $value, 'userselected' ); ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server-upcloud' ); ?></option>
				</optgroup>
				<?php
			} else {
				?>
    				<option value="">-- No Region Information Available --</option>
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
function wpcs_upcloud_template_size( $value='' ) {
		$plans = wpcs_upcloud_plans_list();
		?>

		<select class="w-400" name="wpcs_upcloud_template_size" id="wpcs_upcloud_template_size">
			<?php
			if ( !empty( $plans ) ) {
				?><optgroup label="Select Plan"><?php
				foreach ( $plans as $key => $plan ) {
				?>
    				<option value="<?php echo "{$key}|{$plan['name']}"; ?>" <?php selected( $value, $plan['name'] ); ?>><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Plan Information Available --</option>
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
function wpcs_upcloud_template_ssh_key( $value='' ) {
	$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	?>
	<select style="width: 25rem;" name="wpcs_upcloud_template_ssh_key" id="wpcs_upcloud_template_ssh_key">
		<option value="no-ssh-key" <?php selected( $value, 'no-ssh-key' ); ?>><?php _e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
		<?php
		if ( $serverpilot_ssh_keys ) { ?>
			<optgroup label="Select SSH Key">
			<?php foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) { ?>
				<option value='<?php echo $ssh_key['name']; ?>' <?php selected( $value, $ssh_key['name'] ); ?>><?php echo $ssh_key['name']; ?></option>
				<?php } ?>
			</optgroup>
		<?php } ?>
	</select>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_upcloud_template_startup_script( $value='' ) {
	$startup_scripts		= get_option( 'wpcs_startup_scripts', array() ); ?>
							<select style="width: 25rem;" name="wpcs_upcloud_template_startup_script_name" id="wpcs_upcloud_template_startup_script_name">
								<option value="no-startup-script" <?php selected( $value, 'no-startup-script' ); ?>><?php _e('-- No Startup Script --', 'wp-cloud-server' ); ?></option>
								<optgroup label="Select Startup Script">
									<?php
									if ( ! empty( $startup_scripts ) ) {
										foreach ( $startup_scripts as $key => $script ) {
										?>
										<option value='<?php echo $script['name']; ?>' <?php selected( $value, $script['name'] ); ?>><?php echo $script['name']; ?></option>
										<?php
										}
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
function wpcs_upcloud_template_enable_backups( $value='' ) {
	$value = ( $value ) ? 'checked' : '';
	?>
	<input type="checkbox" id="wpcs_upcloud_template_enable_backups" name="wpcs_upcloud_template_enable_backups" value="1" <?php echo $value; ?>>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_upcloud_template_site_counter( $value='' ) {
		?>
	<input class="w-400" type="text" id="wpcs_upcloud_template_site_counter" name="wpcs_upcloud_template_site_counter" value="<?php echo $value; ?>" readonly>
	<?php
}



