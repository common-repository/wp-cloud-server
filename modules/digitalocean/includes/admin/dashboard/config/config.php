<?php
/**
 * WP Cloud Server DigitalOcean Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_digitalocean_config( $modules, $module_name, $status ) {

$debug_enabled = get_option( 'wpcs_enable_debug_mode' );

/**
 * GLOBAL ARGUMENTS
 */

$kses_exceptions = array(
	'a'      => array(
		'href' => array(),
	),
	'strong' => array(),
	'br'     => array(),
);

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */

if ( ( 'DigitalOcean' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$activated		= ( isset( $modules['DigitalOcean']['status'] ) && ( 'active' == $modules['DigitalOcean']['status'] ) ) ? 'true' : 'false';
$github_active	= ( isset( $modules['GitHub']['status'] ) && ( 'active' == $modules['GitHub']['status'] ) ) ? 'true' : 'false';
$config			= get_option( 'wpcs_module_config' );
$page_position	= ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu	= array(
					'id'       		=> 'digitalocean-settings',
					'module'		=> 'DigitalOcean',
					'active'		=> $activated,
					'position'		=> $page_position,
					'template'		=> 'digitalocean-settings',
					'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
					'menu_header'	=> '',
					'menu_divider'	=> '',
					'menu_item'		=> 'DigitalOcean',
					'section_width'	=> '',
					'type'     		=> 'settings',
					'title'    		=> '',
					'subtitle' 		=> '',
					'desc'     		=> '',
					'tab_block_id'	=> 'digitalocean_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Settings',
						'tab2'		=>	'Event Log',
						'tab3'		=>	'Debug',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'digitalocean-settings',
						'tab2'		=>	'digitalocean-logged-data',
						'tab3'		=>	'digitalocean-debug',
					),
					'tabs_active'	=> array(
						'tab1'		=>	'true',
						'tab2'		=>	'true',
						'tab3'		=>	$active_tab,
					),
					'tabs_width'	=> array(
						'tab1'		=>	'xsmall',
						'tab2'		=>	'',
						'tab3'		=>	'',
					),
);

$config[ 'wp-cloud-server-admin-menu' ]['content'][$sub_menu['id']] = $sub_menu;

$config[ 'wp-cloud-server-admin-menu' ]['DigitalOcean'] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'list-cloud-servers',
	'module'		=> 'DigitalOcean',
	'active'		=> $activated,
	'position'		=> '9',
	'template'		=> 'list-cloud-servers',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Droplets',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean' ),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_droplet_tabs',
	'tabs'			=> array(
						'tab1'		=>	'Droplets',
						'tab2'		=>	'+ Add Droplet',
					),
	'tabs_content'	=> array(
						'tab1'		=>	'list-cloud-servers',
						'tab2'		=>	'create-cloud-server',
					),
	'tabs_active'	=> array(
						'tab1'		=>	'true',
						'tab2'		=>	'true',
					),
	'tabs_width'	=> array(
						'tab1'		=>	'',
						'tab2'		=>	'xsmall',
					),
	'modal_menu_items'	=> array(
						'menu1'		=>	'Summary',
						'menu2'		=>	'Backups',
						'menu3'		=>	'Snapshots',
					),
	'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'true',
						'menu3'		=>	'true',
					),
	'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
						'menu2'		=>	'backup',
						'menu3'		=>	'snapshot',
					),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][0] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'digitalocean-volume-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> null,
	'template'		=> 'digitalocean-volume-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Volumes',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_volume_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Volumes',
				'tab2'		=>	'+ Add Volume',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-volume-details',
				'tab2'		=>	'digitalocean-create-volume',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][1] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'digitalocean-database-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'digitalocean-database-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Databases',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_database_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Databases',
				'tab2'		=>	'+ Add Database',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-database-details',
				'tab2'		=>	'digitalocean-create-database',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][2] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'digitalocean-snapshot-images-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'digitalocean-snapshot-images-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Images',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_images_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Snapshots',
				'tab2'		=>	'Backups',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-snapshot-images-details',
				'tab2'		=>	'digitalocean-backup-images-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][3] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'digitalocean-domain-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'digitalocean-domain-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Domains',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_domains_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Domains',
				'tab2'		=>	'+ Add Domain',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-domain-details',
				'tab2'		=>	'digitalocean-create-domain',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][4] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'digitalocean-firewall-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'digitalocean-firewall-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Firewalls',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_firewall_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Firewalls',
				'tab2'		=>	'+ Add Firewall',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-firewall-details',
				'tab2'		=>	'digitalocean-create-firewall',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][5] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'digitalocean-networking-details',
	'module'		=> 'DigitalOcean',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'digitalocean-networking-details',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Networking',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_networking_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Floating IPs',
				'tab2'		=>	'+ Add Floating IP',
				'tab3'		=>	'Load Balancers',
				'tab4'		=>	'+ Add Load Balancer',
				'tab5'		=>	'VPC',
				'tab6'		=>	'+ Add VPC',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'digitalocean-floating-ips-details',
				'tab2'		=>	'digitalocean-floating-ips-details',
				'tab3'		=>	'digitalocean-load-balancers-details',
				'tab4'		=>	'digitalocean-load-balancers-details',
				'tab5'		=>	'digitalocean-vpcs-details',
				'tab6'		=>	'digitalocean-vpcs-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
				'tab3'		=>	'true',
				'tab4'		=>	'false',
				'tab5'		=>	'true',
				'tab6'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
				'tab3'		=>	'',
				'tab4'		=>	'',
				'tab5'		=>	'',
				'tab6'		=>	'',
			),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][6] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'list-cloud-servers',
	'module'		=> 'DigitalOcean',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'list-cloud-servers',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean' ),
	'title'    		=> esc_html__( "DigitalOcean Servers", 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'digitalocean_templates_tabs',
	'tabs'			=> array(
						'tab1'		=>	'Templates',
						'tab2'		=>	'+ Add Template',
					),
	'tabs_content'	=> array(
						'tab1'		=>	'list-cloud-server-templates',
						'tab2'		=>	'create-cloud-template',
					),
	'tabs_active'	=> array(
						'tab1'		=>	'true',
						'tab2'		=>	'true',
					),
	'tabs_width'	=> array(
						'tab1'		=>	'',
						'tab2'		=>	'xsmall',
					),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][7] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'list-cloud-client-details',
	'module'		=> 'DigitalOcean',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'list-cloud-client-details',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean' ),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][8] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-ssh-key',
	'module'		=> '',
	'active'		=> 'true',
	'position'		=> '7',
	'template'		=> 'create-ssh-key',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',	
	'menu_header'	=> 'Settings',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'SSH Keys',
	'type'     		=> 'text',
	'section_width'	=> '',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'table'			=> array(
		'heading1'	=>	'Name',
		'heading2'	=>	'Fingerprint',
		'heading3'	=>	'Public Key',
		'heading4'	=>	'Manage',
	),
	'tab_block_id'	=> 'settings_digitalocean_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'digitalocean-list-ssh-keys',
		'tab2'		=>	'digitalocean-create-ssh-key',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	'true',
	),
	'tabs_width'	=> array(
		'tab1'		=>	'',
		'tab2'		=>	'xsmall',
	),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][9] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-user-startup-scripts',
	'module'		=> '',
	'active'		=> 'false',
	'position'		=> '7',
	'template'		=> 'create-user-startup-scripts',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Startup Scripts',
	'type'     		=> 'text',
	'section_width'	=> '',
	'title'    		=> esc_html__( 'Startup Scripts', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
	'table'			=> array(
		'heading1'	=>	'Name',
		'heading2'	=>	'Description',
		'heading3'	=>	'GitHub Repos',
		'heading4'	=>	'GitHub File',
		'heading5'	=>	'Manage',
	),
	'tab_block_id'	=> 'settings_digitalocean_scripts_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Startup Scripts',
		'tab2'		=>	'+ Add Startup Script',
		'tab3'		=>	'+ Add GitHub Script',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'digitalocean-list-user-startup-scripts',
		'tab2'		=>	'digitalocean-create-user-startup-scripts',
		'tab3'		=>	'digitalocean-create-user-github-startup-scripts',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	'true',
		'tab3'		=>	'true',
	),
	'tabs_width'	=> array(
		'tab1'		=>	'',
		'tab2'		=>	'xsmall',
		'tab3'		=>	'xsmall',
	),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][10] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-host-name',
	'module'		=> '',
	'active'		=> wpcs_cart_active(),
	'position'		=> '7',
	'template'		=> 'create-host-name',
	'template_path'	=> 'modules/digitalocean/includes/admin/dashboard/config/templates',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Hostnames',
	'type'     		=> 'text',
	'section_width'	=> '',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'table'			=> array(
		'heading1'	=>	'Label',
		'heading2'	=>	'Hostname',
		'heading3'	=>	'Suffix',
		'heading4'	=>	'Protocol',
		'heading5'	=>	'Domain',
		'heading6'	=>	'Port',
		'heading7'	=>	'Manage',
	),
	'tab_block_id'	=> 'settings_digitalocean_hostnames_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'digitalocean-list-host-names',
		'tab2'		=>	'digitalocean-create-host-name',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	'true',
	),
	'tabs_width'	=> array(
		'tab1'		=>	'',
		'tab2'		=>	'xsmall',
	),
);

$config[ 'wp-cloud-servers-digitalocean' ]['DigitalOcean'][11] = $sub_menu;

/* END MENU CONFIGURATION */
	
if ( function_exists( 'wpcs_setup_digitalocean_pro_config' ) ) {
	$config = wpcs_setup_digitalocean_pro_config( $config );
}

update_option( 'wpcs_module_config', $config );
	
do_action( 'wpcs_update_module_status', $module_name, $status );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_digitalocean_config', 10, 3 );

/* START OF MENU FUNCTIONS */

function wpcs_digitalocean_snapshot_upgrade( $server ) {		
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Droplet Snapshots', 'wp-cloud-server' ); ?></h3>
	<?php
	$snapshots		= wpcs_digitalocean_call_api_list_snapshots( $server['id'] );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Min Disk Size', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Size (GB)', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if ( !empty( $snapshots['snapshots'] ) ) {
							foreach ( $snapshots['snapshots'] as $key => $snapshot ) {
								?>
        						<tr>
            						<td><?php echo $snapshot['id']; ?></td>
            						<td><?php
										$d = new DateTime( $snapshot['created_at'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $snapshot['name']; ?></td>
									<td><?php echo $snapshot['min_disk_size']; ?></td>
									<td><?php echo $snapshot['size_gigabytes']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="6"><?php esc_html_e( 'No Snapshot Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
    <?php	
}
add_action( 'wpcs_digitalocean_snapshot_content', 'wpcs_digitalocean_snapshot_upgrade' );

function wpcs_digitalocean_backup_upgrade( $server ) {		
	$backups		= wpcs_digitalocean_call_api_list_backups( $server['id'] );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Droplet Backups', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Name', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Region', 'wp-cloud-server-digitalocean' ); ?></th>
							<th><?php esc_html_e( 'Min Disk', 'wp-cloud-server-digitalocean' ); ?></th>
            				<th><?php esc_html_e( 'Size (GB)', 'wp-cloud-server-digitalocean' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if ( !empty( $backups['backups'] ) ) {
							foreach ( $backups['backups'] as $key => $backup ) {
								?>
        						<tr>
            						<td><?php echo $backup['id']; ?></td>
            						<td><?php
										$d = new DateTime( $snapshot['created_at'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $backup['name']; ?></td>
									<td><?php echo $backup['regions'][0]; ?></td>
									<td><?php echo $backup['min_disk_size']; ?></td>
									<td><?php echo $backup['size_gigabytes']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Backup Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
							<?php
						}
						?>
    				</tbody>
				</table>
			</div>
	</div>
    <?php	
}
add_action( 'wpcs_digitalocean_backup_content', 'wpcs_digitalocean_backup_upgrade' );

function wpcs_digitalocean_project_upgrade( $server ) {		
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Droplet Projects', 'wp-cloud-server' ); ?></h2>
		<div class="uk-alert-upgrade" uk-alert>
			<p>DigitalOcean Projects are available with the DigitalOcean Pro Module. Please <a href="#">click here</a> for more information</p>
		</div>
	</div>
    <?php	
}
add_action( 'wpcs_digitalocean_project_content', 'wpcs_digitalocean_project_upgrade' );

function wpcs_digitalocean_summary_upgrade( $server ) {		
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Droplet Summary', 'wp-cloud-server' ); ?></h3>
		<table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            							<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            							<td><?php echo "{$server['name']}"; ?></td>
       								</tr>
        							<tr>
            							<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            							<td><?php echo ucfirst($server['status']); ?></td>
        							</tr>
        							<tr>
           	 							<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            							<td><?php echo "{$server['region']['name']}"; ?></td>
       	 							</tr>
        							<tr>
            							<td><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></td>
            							<?php $value = "{$server['image']['distribution']} {$server['image']['name']}";?>
										<td><?php echo $value; ?></td>
        							</tr>
        							<tr>
            							<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            							<td><?php echo $server['vcpus']; ?></td>
        							</tr>
        							<tr>
            							<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
										<?php $server_memory = wpcs_mb_to_gb( $server['memory'] ); ?>
           								<td><?php echo "{$server_memory}GB"; ?></td>
       	 							</tr>
        							<tr>
            							<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 							<td><?php echo "{$server['disk']}GB"; ?></td>
        							</tr>
        							<tr>
            							<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            							<td><?php echo isset($server['networks']['v4'][0]['ip_address']) ? $server['networks']['v4'][0]['ip_address'] : 'Not Available'; ?></td>
        							</tr>
        							<tr>
            							<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            							<td><?php echo "{$server['id']}"; ?></td>
        							</tr>
        							<tr>
            							<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            							<td><?php $d = new DateTime( $server['created_at'] ); echo $d->format('d-m-Y'); ?></td>
        							</tr>
    						        </tbody>
						</table>
	</div>
    <?php	
}
add_action( 'wpcs_digitalocean_summary_content', 'wpcs_digitalocean_summary_upgrade' );