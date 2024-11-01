<?php

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_name( $value='' ) {
	?>
	<input class="w-400" type="text" placeholder="Template Name" id="wpcs_digitalocean_template_name" name="wpcs_digitalocean_template_name" value="<?php echo $value; ?>" readonly>
	<p class="text_desc">[ You can use any valid text, numeric, and space characters ]</p>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_hostname( $value='' ) {
	$host_names	= get_option( 'wpcs_host_names' );
	?>
	<select class="w-400" name="wpcs_digitalocean_template_host_name" id="wpcs_digitalocean_template_host_name">
		<optgroup label="Select Hostname">
			<?php
			if ( !empty( $host_names ) ) {
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
function wpcs_digitalocean_template_type( $value='' ) {
	$images			= wpcs_digitalocean_os_list();
	$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_digitalocean_template_type" id="wpcs_digitalocean_template_type">
			<optgroup label="Ubuntu">
				<option value="Ubuntu 20.04 x64|ubuntu-20-04-x64" <?php selected( $value, 'Ubuntu 20.04 x64' ); ?>><?php esc_html_e( 'Ubuntu 20.04 x64', 'wp-cloud-server' ); ?></option>
           	 	<option value="Ubuntu 18.04 x64|ubuntu-18-04-x64" <?php selected( $value, 'Ubuntu 18.04 x64' ); ?>><?php esc_html_e( 'Ubuntu 18.04 x64', 'wp-cloud-server' ); ?></option>
           	 	<option value="Ubuntu 16.04 x64|ubuntu-16-04-x64" <?php selected( $value, 'Ubuntu 16.04 x64' ); ?>><?php esc_html_e( 'Ubuntu 16.04 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Debian">
            	<option value="Debian 10 x64|debian-10-x64" <?php selected( $value, 'Debian 10 x64' ); ?>><?php esc_html_e( 'Debian 10 x64', 'wp-cloud-server' ); ?></option>
            	<option value="Debian 9 x64|debian-9-x64" <?php selected( $value, 'Debian 9 x64' ); ?>><?php esc_html_e( 'Debian 9 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Centos">
            	<option value="CentOS 8 x64|centos-8-x64" <?php selected( $value, 'CentOS 8 x64' ); ?>><?php esc_html_e( 'CentOS 8 x64', 'wp-cloud-server' ); ?></option>
            	<option value="CentOS 7 x64|centos-7-x64" <?php selected( $value, 'CentOS 7 x64' ); ?>><?php esc_html_e( 'CentOS 7 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
				<optgroup label="Fedora">
            	<option value="Fedora 32 x64|fedora-32-x64" <?php selected( $value, 'Fedora 32 x64' ); ?>><?php esc_html_e( 'Fedora 32 x64', 'wp-cloud-server' ); ?></option>
            	<option value="Fedora 31 x64|fedora-31-x64" <?php selected( $value, 'Fedora 31 x64' ); ?>><?php esc_html_e( 'Fedora 31 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_regions( $value='' ) {
		$regions = wpcs_digitalocean_regions_list();
		?>

		<select class="w-400" name="wpcs_digitalocean_template_region" id="wpcs_digitalocean_template_region">
			<optgroup label="Select Region">
            <option value="Amsterdam|ams" <?php selected( $value, 'Amsterdam' ); ?>><?php esc_html_e( 'Amsterdam', 'wp-cloud-server' ); ?></option>
            <option value="Bangalore|blr" <?php selected( $value, 'Bangalore' ); ?>><?php esc_html_e( 'Bangalore', 'wp-cloud-server' ); ?></option>
            <option value="Frankfurt|fra" <?php selected( $value, 'Frankfurt' ); ?>><?php esc_html_e( 'Frankfurt', 'wp-cloud-server' ); ?></option>
            <option value="London|lon" <?php selected( $value, 'London' ); ?>><?php esc_html_e( 'London', 'wp-cloud-server' ); ?></option>
            <option value="New York|nyc" <?php selected( $value, 'New York' ); ?>><?php esc_html_e( 'New York', 'wp-cloud-server' ); ?></option>
            <option value="San Francisco|sfo" <?php selected( $value, 'San Francisco' ); ?>><?php esc_html_e( 'San Francisco', 'wp-cloud-server' ); ?></option>
            <option value="Singapore|sgp" <?php selected( $value, 'Singapore' ); ?>><?php esc_html_e( 'Singapore', 'wp-cloud-server' ); ?></option>
            <option value="Toronto|tor" <?php selected( $value, 'Toronto' ); ?>><?php esc_html_e( 'Toronto', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="User Choice">
				<option value="userselected|userselected" <?php selected( $value, 'userselected' ); ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_size( $value='' ) {
		$plans = wpcs_digitalocean_plans_list();
		?>

		<select class='w-400' name="wpcs_digitalocean_template_size" id="wpcs_digitalocean_templat_size">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $key => $type ){ ?>
					<optgroup label='<?php echo $key ?>'>";
            		<?php foreach ( $type as $key => $plan ){
						$option = "{$plan['name']}|{$key}";
						?>
    					<option value="<?php echo $option; ?>" <?php selected( $value, $plan['name'] ); ?>><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
						<?php
					}
				}
			}
			?>
		</select>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_ssh_key( $value='' ) {
	$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	?>
	<select style="width: 25rem;" name="wpcs_digitalocean_template_ssh_key" id="wpcs_digitalocean_template_ssh_key">
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
function wpcs_digitalocean_template_startup_script( $value='' ) {
	$startup_scripts		= get_option( 'wpcs_startup_scripts', array() ); ?>
							<select style="width: 25rem;" name="wpcs_digitalocean_template_startup_script_name" id="wpcs_digitalocean_template_startup_script_name">
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
function wpcs_digitalocean_template_enable_backups( $value='' ) {
	?>
	<input type="checkbox" id="wpcs_digitalocean_template_enable_backups" name="wpcs_digitalocean_template_enable_backups" value="1" <?php checked( $value, 1 ); ?>>
	<?php
}

/**
*  Update Vultr Module Status
*
*  @since 1.0.0
*/
function wpcs_digitalocean_template_site_counter( $value='' ) {
		?>
	<input class="w-400" type="text" id="wpcs_digitalocean_template_site_counter" name="wpcs_digitalocean_template_site_counter" value="<?php echo $value; ?>" readonly>
	<?php
}