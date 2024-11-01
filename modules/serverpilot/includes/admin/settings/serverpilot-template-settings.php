<?php
/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_name( $template='', $value='', $edit = false ) {
	$id	= "wpcs_serverpilot_template_name_{$template}";
	?>
	<input class="w-400" type="text" placeholder="Template Name" id="<?php echo $id; ?>" name="wpcs_serverpilot_template_name" value="<?php echo $value; ?>" readonly>
	<p class="text_desc">[ You can use any valid text, numeric, and space characters ]</p>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_module( $template='', $value='', $edit = false ) {
	$data	= ( '' == $template ) ? '' : "data-template='{$template}'";
	$id		= ( $edit ) ? "wpcs_serverpilot_edit_template_module_{$template}" : 'wpcs_serverpilot_template_module';
	$name	= ( $edit ) ? "wpcs_serverpilot_template_module" : 'wpcs_serverpilot_template_module';
	?>
	<select class="w-400" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $data; ?>>
		<?php
		$module_data	= get_option( 'wpcs_module_list' );
		$cloud_active	= wpcs_check_cloud_provider_api();
		if ( $cloud_active ) {
			?><optgroup label="Select Cloud Provider"><?php
			foreach ( $module_data as $key => $module ) { 
				if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) && ( wpcs_check_cloud_provider_api( $key ) ) ) {
					?>
            		<option value="<?php echo $key ?>" <?php selected( $value, $key ); ?>><?php echo $key ?></option>
					<?php 
				}
			}
			?></optgroup><?php
		} else {
			?>
			<optgroup label="Select Cloud Provider">
            	<option value="DigitalOcean">DigitalOcean</option>
			</optgroup>
			<?php 
		}
		?>
	</select>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_type( $template='', $value='', $edit = false ) {
	$id	= "wpcs_serverpilot_template_type_{$template}";
	?>
	<select class='w-400' name="wpcs_serverpilot_template_type" id="<?php echo $id; ?>">
		<optgroup label="Select Image">
			<option value="Ubuntu 20.04 x64|Ubuntu 20.04 x64" <?php selected( $value, 'Ubuntu 20.04 x64' ); ?>><?php _e( 'Ubuntu 20.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
        	<option value="Ubuntu 18.04 x64|Ubuntu 18.04 x64" <?php selected( $value, 'Ubuntu 18.04 x64' ); ?>><?php _e( 'Ubuntu 18.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
		</optgroup>
	</select>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_regions( $template='', $value='', $cloud_provider=null, $edit = false ) {
		$id				= "wpcs_serverpilot_template_region_{$template}";
		$cloud_provider	= strtolower( str_replace( " ", "_", $cloud_provider ) );
		$cloud_provider	= ( empty( $cloud_provider ) ) ? 'digitalocean' : $cloud_provider;
		$regions		= call_user_func("wpcs_{$cloud_provider}_regions_list");
		?>
		<select class='w-400' name="wpcs_serverpilot_template_region" id="<?php echo $id; ?>">
			<?php				
			if ( $regions ) {
				?><optgroup label="Select Region"><?php
            	foreach ( $regions as $key => $region ){
					$data = "{$region['name']}|{$key}";
					?>
                	<option value="<?php echo $data; ?>" <?php selected( $value, $region['name'] ); ?>><?php echo $region['name']; ?></option>
					<?php
				}
			}
			?>
			</optgroup>
		</select>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_size( $template='', $value='', $cloud_provider=null, $edit = false ) {
	$id	= "wpcs_serverpilot_template_size_{$template}";
		$cloud_provider	= strtolower( str_replace( " ", "_", $cloud_provider ) );
		$cloud_provider	= ( empty( $cloud_provider ) ) ? 'digitalocean' : $cloud_provider;
		$plans = call_user_func("wpcs_{$cloud_provider}_availability_list");
		?>
		<select class='w-400' name="wpcs_serverpilot_template_size" id="<?php echo $id; ?>">
			<optgroup label="Select Server Plan">
				<?php	
				if ( !empty( $plans ) ) {
					foreach ( $plans as $key => $type ){ ?>
						<optgroup label='<?php echo $key ?>'>";
						<?php
						foreach ( $type as $key => $plan ){
							$data = "{$plan['name']}|{$key}";
							?>
    						<option value="<?php echo $data; ?>" <?php selected( $value, $plan['name'] ); ?>><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
							<?php
						}
					}
				}
				?>
			</optgroup>
		</select>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_plan( $template='', $value='', $edit = false ) {
	$id	= "wpcs_serverpilot_template_plan_{$template}";
	?>
	<select class='w-400' name="wpcs_serverpilot_template_plan" id="<?php echo $id; ?>">
		<optgroup label="Select ServerPilot Plan">
    		<option value="economy" <?php selected( $value, "economy" ); ?>><?php _e( 'Economy ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
			<option value="business" <?php selected( $value, "business" ); ?>><?php _e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
			<option value="first_class" <?php selected( $value, "first_class" ); ?>><?php _e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
		</optgroup>
	</select>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_ssh_key( $template='', $value='', $edit = false ) {
	$serverpilot_ssh_keys	= get_option( 'wpcs_serverpilots_ssh_keys' );
	$id						= "wpcs_serverpilot_template_ssh_keys_{$template}";
	?>
	<select style="width: 25rem;" name="wpcs_serverpilot_template_ssh_key" id="<?php echo $id; ?>">
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
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_enable_backups( $template='', $value='', $edit = false ) {
	$id	= "wpcs_serverpilot_template_enable_backups_{$template}";
	?>
	<input type="checkbox" id="<?php echo $id; ?>" name="wpcs_serverpilot_template_enable_backups" value="1" <?php checked( $value, 1 ); ?>>
	<?php
}

/**
*  Update ServerPilot Module Status
*
*  @since 1.0.0
*/
function wpcs_serverpilot_template_autossl( $template='', $value='', $edit = false ) {
	$id	= "wpcs_serverpilot_template_autossl_{$template}";
	?>
	<input class="uk-input" type="checkbox" id="<?php echo $id; ?>" name="wpcs_serverpilot_template_autossl" value="1" <?php checked( $value, 1 ); ?>>
	<?php
}