<?php

/**
 * Provide a admin area servers view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_runcloud_list_website_templates_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-list-website-templates' !== $tabs_content ) {
		return;
	}

	$delete_complete = false;

	$module_data		= get_option( 'wpcs_module_list' );
	$template_data		= get_option( 'wpcs_template_data_backup' );
	$completed_tasks	= get_option( 'wpcs_tasks_completed', array());

	$seltemplate		= isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';
	$provider			= isset( $_GET['provider'] ) ? sanitize_text_field( $_GET['provider'] ) : '';

	$type				= isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
	$type				= ( empty( $provider ) && empty( $seltemplate ) && ( !in_array( $type, array( 'delete', 'edit' )) ) ) ? '' : $type;

	if ( !empty( $provider ) && !empty( $seltemplate ) && ( 'delete' == $type ) ) {
		if ( !empty( $module_data['RunCloud']['templates'] ) ) {
			foreach ( $module_data['RunCloud']['templates'] as $key => $templates ) {
				if ( $seltemplate == $templates['name'] ) {
					unset( $module_data['RunCloud']['templates'][$key] );
					unset( $template_data['RunCloud']['templates'][$key] );
					$completed_tasks[]=$seltemplate;
					$delete_complete = true;
				}	
			}
		update_option( 'wpcs_tasks_completed', $completed_tasks);
		update_option( 'wpcs_module_list', $module_data );
		update_option( 'wpcs_template_data_backup', $template_data);
		}
	}

	if ( ( empty( $provider ) && empty( $seltemplate ) && empty( $type ) ) || ( $delete_complete ) || ( in_array( $seltemplate, $completed_tasks ) ) ) {
	
		// We can clear the 
		if ( empty( $provider ) && empty( $seltemplate ) && empty( $type ) ) {
			delete_option( 'wpcs_tasks_completed' );
		}
		?>
<div class="uk-overflow-auto">
<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'RunCloud Templates', 'wp-cloud-server' ); ?></h2>
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
			
			$templates = $module_data['RunCloud']['templates'];
			
			if ( ! empty( $templates ) ) { 
				foreach ( $templates as $template ) {
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
							<a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers&type=edit&template=' . $template['name'] . '&provider=RunCloud' ), 'serverpilot_del_templates_nonce', '_wp_tmp_nonce') );?>"><?php esc_attr_e( 'Manage', 'wp-cloud-server' ) ?></a>
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
}
if ( !empty( $provider ) && !empty( $seltemplate ) && ( 'edit' == $type ) ) {
	
	if ( !empty($module_data) ) {
		foreach ( $module_data['RunCloud']['templates'] as $key => $template ) {
			if ( $seltemplate == $template['name'] ) {
				$data = $template;
				$data['backups'] = ( isset( $data['backups'] ) ) ? $data['backups'] : 0;
			}	
		}
	}
?>

<div>
	<div class="uk-container uk-container-xsmall">
		<a class="uk-align-right uk-margin-remove-bottom uk-margin-small-top uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers' ), 'client_websites_manage_nonce', '_wp_manage_nonce') );?>"><?php _e( '< Back to Templates', 'wp-cloud-server' ); ?></a>
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( $data['name'], 'wp-cloud-server' ); ?></h2>
		<p><?php _e('This page allows you to edit an existing template. Note that if you edit the template name and then save it you will create a new template, as well as the original!', 'wp-cloud-server' ); ?></p>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_serverpilot_edit_template' );
			wpcs_do_settings_sections( 'wpcs_serverpilot_edit_template' );
			?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php _e( 'Template Name:', 'wp-cloud-server' ); ?></th>
						<td>
							<input style="width: 25rem;" type="text" placeholder="Template Name" id="wpcs_serverpilot_edit_template_name" name="wpcs_serverpilot_edit_template_name" value="<?php  echo $data['name']?>">
							<p class="text_desc"><?php _e( '[You can use any valid text, numeric, and space characters]', 'wp-cloud-server' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Cloud Provider:', 'wp-cloud-server' ); ?></th>
						<td>
		<select class='w-400' name="wpcs_serverpilot_edit_template_module" id="wpcs_serverpilot_edit_template_module">
			<?php
			foreach ( $module_data as $key => $module ) { 
				if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) ) {
			?>
            		<option value="<?php esc_attr_e( $key, 'wp-cloud-server' ); ?>" <?php selected( $data['module'], $key ); ?>><?php esc_html_e( $key, 'wp-cloud-server' ); ?></option>
			<?php 
				}
			}
			?>
		</select>
				</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Template Image:', 'wp-cloud-server' ); ?></th>
						<td>		
							<select style="width: 25rem;" name="wpcs_serverpilot_edit_template_type" id="wpcs_serverpilot_edit_template_type">
								<optgroup label="Ubuntu">
									<option value="Ubuntu 20.04 x64|ubuntu-20-04-x64" <?php selected( $data['image_name'], 'Ubuntu 20.04 x64' ); ?>><?php _e( 'Ubuntu 20.04 x64', 'wp-cloud-server' ); ?></option>
           	 						<option value="Ubuntu 18.04 x64|ubuntu-18-04-x64" <?php selected( $data['image_name'], 'Ubuntu 18.04 x64' ); ?>><?php _e( 'Ubuntu 18.04 x64', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Template Region:', 'wp-cloud-server' ); ?></th>
						<td>		
							<select style="width: 25rem;" name="wpcs_serverpilot_edit_template_region" id="wpcs_serverpilot_edit_template_region">
								<optgroup label="Regions">
           	 						<option value="Amsterdam|ams" <?php selected( $data['region_name'], 'Amsterdam' ); ?>><?php _e( 'Amsterdam', 'wp-cloud-server' ); ?></option>
            						<option value="Bangalore|blr" <?php selected( $data['region_name'], 'Bangalore' ); ?>><?php _e( 'Bangalore', 'wp-cloud-server' ); ?></option>
            						<option value="Frankfurt|fra" <?php selected( $data['region_name'], 'Frankfurt' ); ?>><?php _e( 'Frankfurt', 'wp-cloud-server' ); ?></option>
            						<option value="London|lon" <?php selected( $data['region_name'], 'London' ); ?>><?php _e( 'London', 'wp-cloud-server' ); ?></option>
            						<option value="New York|nyc" <?php selected( $data['region_name'], 'New York' ); ?>><?php _e( 'New York', 'wp-cloud-server' ); ?></option>
            						<option value="San Francisco|sfo" <?php selected( $data['region_name'], 'San Francisco' ); ?>><?php _e( 'San Francisco', 'wp-cloud-server' ); ?></option>
            						<option value="Singapore|sgp" <?php selected( $data['region_name'], 'Singapore' ); ?>><?php _e( 'Singapore', 'wp-cloud-server' ); ?></option>
            						<option value="Toronto|tor" <?php selected( $data['region_name'], 'Toronto' ); ?>><?php _e( 'Toronto', 'wp-cloud-server' ); ?></option>
								</optgroup>
								<optgroup>
									<option value="userselected" <?php selected( $data['region_name'], 'userselected' ); ?>><?php _e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Template Size:', 'wp-cloud-server' ); ?></th>
						<td>
						<?php $plans = call_user_func("wpcs_digitalocean_plans_list"); ?>
						<select class='w-400' name="wpcs_serverpilot_edit_template_size" id="wpcs_serverpilot_edit_template_size">
							<?php
							if ( !empty( $plans ) ) {
								foreach ( $plans as $key => $type ){ ?>
									<optgroup label='<?php echo $key ?>'>";
            							<?php foreach ( $type as $key => $plan ){
										$value = "{$plan['name']}|{$key}";
										?>
    									<option value="<?php echo $value; ?>" <?php selected( $data['size'], $key ); ?>><?php esc_html_e( "{$plan['name']} {$plan['cost']}", 'wp-cloud-server' ); ?></option>
							<?php
										}
								}
							}
							?>
						</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'ServerPilot Plan:', 'wp-cloud-server' ); ?></th>
						<td>
						<select class='w-400' name="wpcs_serverpilot_edit_template_plan" id="wpcs_serverpilot_edit_template_plan">
            					<option value="economy" <?php selected( $data['plan'], 'economy' ); ?>><?php _e( 'Economy ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
            					<option value="business" <?php selected( $data['plan'], 'business' ); ?>><?php _e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
								<option value="first_class" <?php selected( $data['plan'], 'first_class' ); ?>><?php _e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'SSH Key:', 'wp-cloud-server' ); ?></th>
						<td>
						<?php
						$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
						?>
						<select style="width: 25rem;" name="wpcs_serverpilot_edit_template_ssh_key" id="wpcs_serverpilot_edit_template_ssh_key">
							<option value="no-ssh-key" <?php selected( $data['ssh_key'], 'no-ssh-key' ); ?>><?php _e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
							<?php
							if ( isset( $serverpilot_ssh_keys ) ) { ?>
							<optgroup label="User SSH Keys">
								<?php foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) { ?>
            						<option value='<?php echo $ssh_key['name']; ?>' <?php selected( $data['ssh_key'], $ssh_key['name'] ); ?>><?php echo $ssh_key['name']; ?></option>
								<?php
								} ?>
							</optgroup>
							<?php } ?>
						</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Enable Server Backups:', 'wp-cloud-server' ); ?></th>
						<td>
							<input type="checkbox" id="wpcs_serverpilot_edit_template_enable_backups" name="wpcs_serverpilot_edit_template_enable_backups" value="1" <?php checked( $data['backups'], 1 ); ?>>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Enable AutoSSL:', 'wp-cloud-server' ); ?></th>
						<td>
							<input type="checkbox" id="wpcs_serverpilot_edit_template_autossl" name="wpcs_serverpilot_edit_template_autossl" value="1" <?php checked( $data['autossl'], 1 ); ?>>
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
			<?php wpcs_submit_button( 'Update Template', 'secondary', 'update_do_template', false ); ?>
			<a class="uk-button uk-button-danger uk-align-right" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers&type=delete&template=' . $data['name'] . '&provider=' . $data['module'] . '' ), 'digitalocean_del_templates_nonce', '_wp_del_tmp_nonce') );?>" uk-toggle><?php esc_attr_e( 'DELETE TEMPLATE', 'wp-cloud-server' ) ?></a>
			
		</form>
		</div>
</div>

	<?php

	$debug_data = array(
		"name"				=>	get_option( 'wpcs_serverpilot_edit_template_name' ),
		"region"			=>	get_option( 'wpcs_serverpilot_edit_template_region' ),
		"size"				=>	get_option( 'wpcs_serverpilot_edit_template_size' ),
		"image"				=> 	get_option( 'wpcs_serverpilot_edit_template_type' ),
		"module"			=> 	get_option( 'wpcs_serverpilot_edit_template_module' ),
		"plan"				=> 	get_option( 'wpcs_serverpilot_edit_template_plan' ),
		"autossl"			=>	get_option( 'wpcs_serverpilot_edit_template_autossl' ),
		"ssh_key"			=>	get_option( 'wpcs_serverpilot_edit_template_ssh_key' ),
		"backups"			=>	get_option( 'wpcs_serverpilot_edit_template_enable_backups' ),
		"monitor_enabled"	=>	get_option( 'wpcs_serverpilot_edit_template_site_monitor' ),
	);

	if ( get_option( 'wpcs_serverpilot_edit_template_name' ) ) {

		$server_name					= get_option( 'wpcs_serverpilot_edit_template_name' );
		$server_size					= get_option( 'wpcs_serverpilot_edit_template_size' );
		$server_region					= get_option( 'wpcs_serverpilot_edit_template_region' );
		$server_type					= get_option( 'wpcs_serverpilot_edit_template_type' );
		$server_module					= get_option( 'wpcs_serverpilot_edit_template_module' );
		$server_plan					= get_option( 'wpcs_serverpilot_edit_template_plan' );
		$server_autossl					= get_option( 'wpcs_serverpilot_edit_template_autossl' );
		$server_backups					= get_option( 'wpcs_serverpilot_edit_template_enable_backups' );
		$server_ssh_key					= get_option( 'wpcs_serverpilot_edit_template_ssh_key' );
		$server_monitor_enabled			= get_option( 'wpcs_serverpilot_edit_template_site_monitor' );
		
		$server_size_explode			= explode( '|', $server_size );
		$server_size_name				= $server_size_explode[0];
		$server_size					= $server_size_explode[1];
		
		$server_region_explode			= explode( '|', $server_region );
		$server_region_name				= $server_region_explode[0];
		$server_region					= isset( $server_region_explode[1] ) ? $server_region_explode[1] : '';
		
		$server_type_explode			= explode( '|', $server_type );
		$server_type_name				= $server_type_explode[0];
		$server_type					= $server_type_explode[1];
		
		// Need to retrieve the image value depending on the cloud provider
		$server_image					= call_user_func("wpcs_{$server_module}_os_list", $server_type_name );
		
		$server_region					= ( 'userselected' == $server_region_name ) ? 'userselected' : $server_region ;
		$server_module_lc				= strtolower( str_replace( " ", "_", $server_module ) );
		
		$server_enable_backups			= ( $server_backups ) ? true : false;
		
		// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"				=>  $server_name,
			"slug"				=>  sanitize_title( $server_name ),
			"region"			=>	$server_region,
			"region_name"		=>	$server_region_name,
			"size"				=>	$server_size,
			"size_name"			=>	$server_size_name,
			"image"				=>	$server_image,
			"image_name"		=>	$server_type_name,
			"backups"			=>	$server_enable_backups,
			"template_name"		=>  'serverpilot_template',
			"hosting_type"		=>	'Shared',
			"module"			=>  $server_module,
			"plan"				=>	$server_plan,
			"autossl"			=>	$server_autossl,
			"monitor_enabled"	=>	$server_monitor_enabled,
			"ssh_key"			=>	$server_ssh_key,
			"custom_settings"	=>	array(
										"DCID"		=>	$server_region,
										"VPSPLANID"	=>	$server_size,
										"OSID"		=> 	$server_image,
									), 
			);

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
		
		if ( !empty($module_data) ) {
			foreach ( $module_data['RunCloud']['templates'] as $key => $templates ) {
				if ( $server_name == $templates['name'] ) {
					$module_data['RunCloud']['templates'][$key]=$droplet_data;
				}	
			}
		}
			
		if ( !empty($template_data) ) {
			foreach ( $template_data['RunCloud']['templates'] as $key => $templates ) {
				if ( $server_name == $templates['name'] ) {
					$template_data['RunCloud']['templates'][$key]=$droplet_data;
				}	
			}
		}

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );
			
		update_option( 'dotemplate_data', $module_data );
		
		//echo '<script type="text/javascript"> window.location.href =  window.location.href; </script>';

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_serverpilot_edit_template_name' );
	delete_option( 'wpcs_serverpilot_edit_template_region' );
	delete_option( 'wpcs_serverpilot_edit_template_size' );
	delete_option( 'wpcs_serverpilot_edit_template_type' );
	delete_option( 'wpcs_serverpilot_edit_template_module' );
	delete_option( 'wpcs_serverpilot_edit_template_plan' );
	delete_option( 'wpcs_serverpilot_edit_template_autossl' );
	delete_option( 'wpcs_serverpilot_edit_template_ssh_key' );
	delete_option( 'wpcs_serverpilot_edit_template_enable_backups' );
	delete_option( 'wpcs_serverpilot_edit_template_site_monitor' );

	}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_list_website_templates_template', 10, 3 );