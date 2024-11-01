<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_linode_config( $modules, $module_name, $status ) {

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

require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-settings.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-logged-data.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-debug.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-server-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-client-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-server.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-template.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-template-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-list-server-details.php';
	
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-backup-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-snapshot-details.php';

require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-volume-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-firewall-details.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-domain-details.php';

require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-list-ssh-keys.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-ssh-key.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-user-github-startup-scripts.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-list-user-startup-scripts.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-user-startup-scripts.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-list-host-names.php';
require_once WPCS_LINODE_PLUGIN_DIR . 'includes/config/templates/linode-create-host-name.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */

if ( ( 'Linode' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset( $modules['Linode']['status'] ) && ( 'active' == $modules['Linode']['status'] ) ) ? 'true' : 'false';

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'linode-settings',
	'module'		=> 'Linode',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'linode-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Linode',
	'section_width'	=> '',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'linode_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'linode-settings',
		'tab2'		=>	'linode-logged-data',
		'tab3'		=>	'linode-debug',
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

$config[ 'wp-cloud-server-admin-menu' ]['Linode'] = $sub_menu;

$linode[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'linode-server-details',
			'module'		=> 'Linode',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'linode-server-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> 'Manage',
			'menu_divider'	=> 'yes',
			'menu_item'		=> 'Linodes',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'linode'),
			'title'    		=> 'Linode Linux Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'linode_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'linode-server-details',
						'tab2'		=>	'linode-create-server',
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

//$config[ 'wp-cloud-server-cloud-servers' ]['Linode'] = $sub_menu;
$config[ 'wp-cloud-servers-linode' ]['Linode'][0] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'linode-volume-details',
	'module'		=> 'Linode',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'linode-volume-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Volumes',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'linode'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'linode_volumes_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Volumes',
				'tab2'		=>	'+ Add Volume',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'linode-volume-details',
				'tab2'		=>	'linode-create-volume',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'linode-domain-details',
	'module'		=> 'Linode',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'linode-domain-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Domains',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'linode'),
	'title'    		=> 'Linode Servers',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'linode_domains_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Domains',
				'tab2'		=>	'+ Add Domain',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'linode-domain-details',
				'tab2'		=>	'linode-create-domain',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'linode-template-details',
	'module'		=> 'Linode',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'linode-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'linode'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'linode_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'linode-template-details',
				'tab2'		=>	'linode-create-template',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'linode-client-details',
	'module'		=> 'Linode',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'linode-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'linode'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'linode_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'linode-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-linode' ]['Linode'][4] = $sub_menu;

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
	'tab_block_id'	=> 'settings_linode_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'linode-list-ssh-keys',
		'tab2'		=>	'linode-create-ssh-key',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][5] = $sub_menu;

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
	'tab_block_id'	=> 'settings_linode_scripts_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Startup Scripts',
		'tab2'		=>	'+ Add Startup Script',
		'tab3'		=>	'+ Add GitHub Script',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'linode-list-user-startup-scripts',
		'tab2'		=>	'linode-create-user-startup-scripts',
		'tab3'		=>	'linode-create-user-github-startup-scripts',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][6] = $sub_menu;

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
	'tab_block_id'	=> 'settings_linode_hostname_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'linode-list-host-names',
		'tab2'		=>	'linode-create-host-name',
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

$config[ 'wp-cloud-servers-linode' ]['Linode'][7] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_linode_pro_config' ) ) {
	$config = wpcs_setup_linode_pro_config( $config );
}

update_option( 'wpcs_linode_module_config', $linode );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_linode_config', 10, 3 );

function wpcs_linode_summary_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Summary', 'wp-cloud-server' ); ?></h3>
	<table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['label']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            						<td><?php echo ucfirst($server['status']); ?></td>
        						</tr>
        						<tr>
           	 						<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            						<td><?php echo wpcs_linode_region_map( $server['region'] ); ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></td>
									<td><?php echo wpcs_linode_os_list( $server['image'], true ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['specs']['vcpus']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
           							<td><?php echo substr_replace( $server['specs']['memory'], 'GB', 1 ) ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo substr_replace( $server['specs']['disk'], 'GB', 2 ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['ipv4'][0]) ? $server['ipv4'][0] : 'Not Available'; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php $d = new DateTime( $server['updated'] ); echo $d->format('d-m-Y'); ?></td>
        						</tr>
    						        </tbody>
						        </table>
</div>
	<?php
}
add_action( 'wpcs_linode_summary_content', 'wpcs_linode_summary_upgrade' );

function wpcs_linode_backup_upgrade( $server ) {

	$backups		= wpcs_linode_call_api_list_backups( $server['id'] );
	$module_data	= get_option( 'wpcs_module_list' );			
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Linode Backups', 'wp-cloud-server' ); ?></h3>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped">
    				<thead>
        				<tr>
							<th><?php esc_html_e( 'ID', 'wp-cloud-server-linode' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wp-cloud-server-linode' ); ?></th>
            				<th><?php esc_html_e( 'Label', 'wp-cloud-server-linode' ); ?></th>
            				<th><?php esc_html_e( 'Status', 'wp-cloud-server-linode' ); ?></th>
        				</tr>
    				</thead>
    				<tbody>
						<?php
						if (!empty( $backups['snapshot']['current'] ) ) {
							foreach ( $backups as $key => $backup ) {
								?>
        						<tr>
            						<td><?php echo $backup['id']; ?></td>
            						<td><?php
										$d = new DateTime( $backup['created'] );
										echo $d->format('d-m-Y');
										?>
									</td>
									<td><?php echo $backup['label']; ?></td>
									<td><?php echo $backup['satus']; ?></td>
									<td><?php echo $backup['type']; ?></td>
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
add_action( 'wpcs_linode_backup_content', 'wpcs_linode_backup_upgrade' );

function wpcs_linode_snapshot_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Snapshots', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_linode_snapshot_content', 'wpcs_linode_snapshot_upgrade' );