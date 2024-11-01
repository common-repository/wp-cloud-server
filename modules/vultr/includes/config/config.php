<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_vultr_config( $modules, $module_name, $status ) {

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

require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-settings.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-license-settings.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-logged-data.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-debug.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-server-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-template-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-server.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-template.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-client-details.php';
	
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-backup-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-snapshot-details.php';

require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-block-storage-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-private-network-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-reserved-ip-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-firewall-details.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-domain-details.php';

require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-list-ssh-keys.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-ssh-key.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-user-github-startup-scripts.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-list-user-startup-scripts.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-user-startup-scripts.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-list-host-names.php';
require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/templates/vultr-create-host-name.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */
//update_option( 'wpcs_vultr_server_complete_queue', array() );

if ( ( 'Vultr' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset($modules['Vultr']['status']) && ( 'active' == $modules['Vultr']['status'] ) ) ? 'true' : 'false';
	
// Check if Vultr Pro Module is active
$vultr_pro_active = check_vultr_pro_plugin();

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'vultr-settings',
	'module'		=> 'Vultr',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'vultr-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Vultr',
	'section_width'	=> '',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'License',
		'tab3'		=>	'Event Log',
		'tab4'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'vultr-settings',
		'tab2'		=>	'vultr-license-settings',		
		'tab3'		=>	'vultr-logged-data',
		'tab4'		=>	'vultr-debug',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	$vultr_pro_active,
		'tab3'		=>	'true',
		'tab4'		=>	$active_tab,
	),
	'tabs_width'	=> array(
		'tab1'		=>	'xsmall',
		'tab2'		=>	'',
		'tab3'		=>	'',
		'tab4'		=>	'',		
	),
);

$config[ 'wp-cloud-server-admin-menu' ]['content'][$sub_menu['id']] = $sub_menu;

$config[ 'wp-cloud-server-admin-menu' ]['Vultr'] = $sub_menu;

$vultr[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'vultr-server-details',
			'module'		=> 'Vultr',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'vultr-server-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> 'Manage',
			'menu_divider'	=> 'yes',
			'menu_item'		=> 'Instances',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'vultr'),
			'title'    		=> 'Vultr Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'vultr_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'vultr-server-details',
						'tab2'		=>	'vultr-create-server',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][0] = $sub_menu;
	
// Upgrade Menus
	
$sub_menu = array(
			'id'       		=> 'vultr-backup-details',
			'module'		=> 'Vultr',
			'active'		=> 'true',
			'position'		=> '9',
			'template'		=> 'vultr-backup-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Snapshots',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'vultr'),
			'title'    		=> 'Vultr Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'vultr_backup_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Snapshots',
						'tab2'		=>	'Backups',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'vultr-snapshot-details',
						'tab2'		=>	'vultr-backup-details',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-block-storage-details',
	'module'		=> 'Vultr',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'vultr-block-storage-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Block Storage',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_volumes_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Block Storage',
				'tab2'		=>	'+ Add Block Storage',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-block-storage-details',
				'tab2'		=>	'vultr-create-block-storage',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-domain-details',
	'module'		=> 'Vultr',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'vultr-domain-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'DNS',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> 'Vultr Servers',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_domain_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Domains',
				'tab2'		=>	'+ Add Domain',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-domain-details',
				'tab2'		=>	'vultr-create-domain',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-firewall-details',
	'module'		=> 'Vultr',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'vultr-firewall-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Firewall',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_firewall_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Firewall',
				'tab2'		=>	'+ Add Firewall',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-firewall-details',
				'tab2'		=>	'vultr-create-firewall',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][4] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-network-details',
	'module'		=> 'Vultr',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'vultr-network-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Network',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_network_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Reserved IPs',
				'tab2'		=>	'+ Add Reserved IPs',
				'tab3'		=>	'Private Networks',
				'tab4'		=>	'+ Add Private Network',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-reserved-ip-details',
				'tab2'		=>	'vultr-private-network-details',
				'tab3'		=>	'vultr-private-network-details',
				'tab4'		=>	'vultr-private-network-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'false',
				'tab3'		=>	'true',
				'tab4'		=>	'false',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
				'tab3'		=>	'',
				'tab4'		=>	'',
			),
);

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][5] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-template-details',
	'module'		=> 'Vultr',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'vultr-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-template-details',
				'tab2'		=>	'vultr-create-template',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][6] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'vultr-client-details',
	'module'		=> 'Vultr',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'vultr-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'vultr'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'vultr_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'vultr-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][7] = $sub_menu;

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
	'tab_block_id'	=> 'settings_vultr_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'vultr-list-ssh-keys',
		'tab2'		=>	'vultr-create-ssh-key',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][8] = $sub_menu;

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
	'tab_block_id'	=> 'settings_vultr_scripts_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Startup Scripts',
		'tab2'		=>	'+ Add Startup Script',
		'tab3'		=>	'+ Add GitHub Script',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'vultr-list-user-startup-scripts',
		'tab2'		=>	'vultr-create-user-startup-scripts',
		'tab3'		=>	'vultr-create-user-github-startup-scripts',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][9] = $sub_menu;

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
	'tab_block_id'	=> 'settings_vultr_hostnames_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'vultr-list-host-names',
		'tab2'		=>	'vultr-create-host-name',
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

$config[ 'wp-cloud-servers-vultr' ]['Vultr'][10] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_vultr_pro_config' ) ) {
	$config = wpcs_setup_vultr_pro_config( $config );
}

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_vultr_config', 10, 3 );

function wpcs_vultr_summary_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Management', 'wp-cloud-server' ); ?></h3>
			<table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['label']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            						<td><?php echo ucfirst($server['power_status']); ?></td>
        						</tr>
        						<tr>
           	 						<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['location']; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></td>
									<td><?php echo $server['os']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['vcpu_count']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
           							<td><?php echo $server['ram'] ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo $server['disk']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['main_ip']) ? $server['main_ip'] : 'Not Available'; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['SUBID']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php $d = new DateTime( $server['date_created'] ); echo $d->format('d-m-Y'); ?></td>
        						</tr>
    						        </tbody>
						        </table>
		</div>
	<?php
}
add_action( 'wpcs_vultr_summary_content', 'wpcs_vultr_summary_upgrade' );

function wpcs_vultr_backup_upgrade( $server ) {

	$backups		= wpcs_vultr_call_api_list_backups( $server['SUBID'] );
	$module_data	= get_option( 'wpcs_module_list' );

	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vultr Backups', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Description', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Size', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-vultr' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if ( !empty( $backups ) ) {
							foreach ( $backups as $key => $backup ) {
								?>
        						<tr>
            						<td><?php echo $backup['BACKUPID']; ?></td>
            						<td><?php
										$d = new DateTime( $backup['date_created'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $backup['description']; ?></td>
									<td><?php echo $backup['size']; ?></td>
									<td><?php echo $backup['status']; ?></td>
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
add_action( 'wpcs_vultr_backup_content', 'wpcs_vultr_backup_upgrade' );

function wpcs_vultr_snapshot_upgrade( $server ) {

	$backups		= wpcs_vultr_call_api_list_snapshots();
	$module_data	= get_option( 'wpcs_module_list' );

	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vultr Snapshots', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-vultr' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Description', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Size', 'wp-cloud-server-vultr' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-vultr' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if ( !empty( $backups ) ) {
							foreach ( $backups as $key => $backup ) {
								?>
        						<tr>
            						<td><?php echo $backup['SNAPSHOTID']; ?></td>
            						<td><?php
										$d = new DateTime( $backup['date_created'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $backup['description']; ?></td>
									<td><?php echo $backup['size']; ?></td>
									<td><?php echo $backup['status']; ?></td>
        						</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No Snapshot Information Available', 'wp-cloud-server' ) ?></td>
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
add_action( 'wpcs_vultr_snapshot_content', 'wpcs_vultr_snapshot_upgrade' );