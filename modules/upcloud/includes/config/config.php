<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_upcloud_config( $modules, $module_name, $status ) {

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

require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-settings.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-logged-data.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-debug.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-server-details.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-client-details.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-template-details.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-server.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-template.php';
	
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-backup-details.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-snapshot-details.php';

require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-list-ssh-keys.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-ssh-key.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-user-github-startup-scripts.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-list-user-startup-scripts.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-user-startup-scripts.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-list-host-names.php';
require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/templates/upcloud-create-host-name.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */
//update_option( 'wpcs_upcloud_server_complete_queue', array() );

if ( ( 'UpCloud' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset($modules['UpCloud']['status']) && ( 'active' == $modules['UpCloud']['status'] ) ) ? 'true' : 'false';

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'upcloud-settings',
	'module'		=> 'UpCloud',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'upcloud-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'UpCloud',
	'section_width'	=> '',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'upcloud_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'upcloud-settings',
		'tab2'		=>	'upcloud-logged-data',
		'tab3'		=>	'upcloud-debug',
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

$config[ 'wp-cloud-server-admin-menu' ]['UpCloud'] = $sub_menu;

$upcloud[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'upcloud-server-details',
			'module'		=> 'UpCloud',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'upcloud-server-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> 'Manage',
			'menu_divider'	=> 'yes',
			'menu_item'		=> 'Servers',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'upcloud'),
			'title'    		=> 'UpCloud Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'upcloud_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'upcloud-server-details',
						'tab2'		=>	'upcloud-create-server',
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
		'menu3'		=>	'false',
	),
	'modal_menu_action'	=> array(
		'menu1'		=>	'summary',
		'menu2'		=>	'backup',
		'menu3'		=>	'snapshot',
	),
);

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][0] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'upcloud-backup-details',
			'module'		=> 'UpCloud',
			'active'		=> 'false',
			'position'		=> '9',
			'template'		=> 'upcloud-backup-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Snapshots',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'upcloud'),
			'title'    		=> 'UpCloud Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'upcloud_backup_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Snapshots',
						'tab2'		=>	'Backups',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'upcloud-snapshot-details',
						'tab2'		=>	'upcloud-backup-details',
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

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'upcloud-template-details',
	'module'		=> 'UpCloud',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'upcloud-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'upcloud'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'upcloud_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'upcloud-template-details',
				'tab2'		=>	'upcloud-create-template',
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

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'upcloud-client-details',
	'module'		=> 'UpCloud',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'upcloud-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'upcloud'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'upcloud_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'upcloud-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-ssh-key',
	'module'		=> '',
	'active'		=> 'true',
	'position'		=> '7',
	'template'		=> 'create-ssh-key',
	'template_path'	=> 'includes/admin/dashboard/config/templates',	
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
	'tab_block_id'	=> 'settings_upcloud_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'upcloud-list-ssh-keys',
		'tab2'		=>	'upcloud-create-ssh-key',
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

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][4] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-user-startup-scripts',
	'module'		=> '',
	'active'		=> 'true',
	'position'		=> '7',
	'template'		=> 'create-user-startup-scripts',
	'template_path'	=> 'includes/admin/dashboard/config/templates',	
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
	'tab_block_id'	=> 'settings_upcloud_scripts_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Startup Scripts',
		'tab2'		=>	'+ Add Startup Script',
		'tab3'		=>	'+ Add GitHub Script',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'upcloud-list-user-startup-scripts',
		'tab2'		=>	'upcloud-create-user-startup-scripts',
		'tab3'		=>	'upcloud-create-user-github-startup-scripts',
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

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][5] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-host-name',
	'module'		=> '',
	'active'		=> wpcs_cart_active(),
	'position'		=> '7',
	'template'		=> 'create-host-name',
	'template_path'	=> 'includes/admin/dashboard/config/templates',	
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
	'tab_block_id'	=> 'settings_upcloud_hostname_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'upcloud-list-host-names',
		'tab2'		=>	'upcloud-create-host-name',
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

$config[ 'wp-cloud-servers-upcloud' ]['UpCloud'][6] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_upcloud_pro_config' ) ) {
	$config = wpcs_setup_upcloud_pro_config( $config );
}

update_option( 'wpcs_upcloud_module_config', $upcloud );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_upcloud_config', 10, 3 );

function wpcs_upcloud_summary_upgrade( $server ) {
	$region = wpcs_upcloud_regions_list();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Summary', 'wp-cloud-server' ); ?></h3>
			<table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['title']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            						<td><?php echo ucfirst($server['state']); ?></td>
        						</tr>
        						<tr>
           	 						<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $region[ $server['zone'] ]['name']; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></td>
									<td><?php echo $server['plan']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['core_number']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
									<?php $memory = wpcs_upcloud_convert_mb_to_gb( $server['memory_amount'] ); ?>
           							<td><?php echo "{$memory}GB" ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo "{$server['storage_devices']['storage_device'][0]['storage_size']}GB"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['ip_addresses']['ip_address'][1]['address']) ? $server['ip_addresses']['ip_address'][2]['address'] : 'Not Available';?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['uuid']}"; ?></td>
        						</tr>
    						        </tbody>
						        </table>
	</div>
	<?php
}
add_action( 'wpcs_upcloud_summary_content', 'wpcs_upcloud_summary_upgrade' );

function wpcs_upcloud_backup_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Backups', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_upcloud_backup_content', 'wpcs_upcloud_backup_upgrade' );

function wpcs_upcloud_snapshot_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Snapshots', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_upcloud_snapshot_content', 'wpcs_upcloud_snapshot_upgrade' );