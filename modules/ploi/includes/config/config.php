<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_ploi_config( $modules, $module_name, $status ) {

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

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-settings.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-logged-data.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-debug.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-managed-servers.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-managed-server.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-client-details.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-ssh-keys.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-ssh-key.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-managed-server-templates.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-managed-server-template.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-managed-site-templates.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-managed-site-template.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-managed-websites.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-website.php';

require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-list-host-names.php';
require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/templates/ploi-create-host-name.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */
//update_option( 'wpcs_ploi_server_complete_queue', array() );

if ( ( 'Ploi' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset($modules['Ploi']['status']) && ( 'active' == $modules['Ploi']['status'] ) ) ? 'true' : 'false';

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'ploi-settings',
	'module'		=> 'Ploi',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'ploi-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Ploi',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'ploi_settings_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'License',
		'tab3'		=>	'Event Log',
		'tab4'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'ploi-settings',
		'tab2'		=>	'ploi-license-settings',
		'tab3'		=>	'ploi-logged-data',
		'tab4'		=>	'ploi-debug',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	'false',
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

$config[ 'wp-cloud-server-admin-menu' ]['Ploi'] = $sub_menu;

$ploi[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'ploi-managed-servers',
	'module'		=> 'Ploi',
	'active'		=> $activated,
	'position'		=> '6',
	'template'		=> 'ploi-managed-servers',
	'template_path'	=> 'includes/config/templates',	
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Servers',
	'type'     		=> 'text',
	'api_required'	=> array( 'ploi'),
	'section_width'	=> '',
	'content_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
					'tab_block_id'	=> 'ploi_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'ploi-list-managed-servers',
						'tab2'		=>	'ploi-create-managed-server',
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
						'menu2'		=>	'Sites',
						'menu3'		=>	'PHP',
						'menu4'		=>	'Cronjobs',
						'menu5'		=>	'Network',
						'menu6'		=>	'SSH Keys',
						'menu7'		=>	'Daemons',
						'menu8'		=>	'Monitoring',
						'menu9'		=>	'Status',
						'menu10'	=>	'Manage',
						'menu11'	=>	'Logs',
						'menu12'	=>	'Settings',
					),
			'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'false',
						'menu3'		=>	'false',
						'menu4'		=>	'false',
						'menu5'		=>	'false',
						'menu6'		=>	'false',
						'menu7'		=>	'false',
						'menu8'		=>	'false',
						'menu9'		=>	'false',
						'menu10'	=>	'false',
						'menu11'	=>	'false',
						'menu12'	=>	'false',
					),
			'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
						'menu2'		=>	'sites',
						'menu3'		=>	'php',
						'menu4'		=>	'cronjobs',
						'menu5'		=>	'network',
						'menu6'		=>	'ssh_keys',
						'menu7'		=>	'daemons',
						'menu8'		=>	'monitoring',
						'menu9'		=>	'status',
						'menu10'	=>	'manage',
						'menu11'	=>	'logs',
						'menu12'	=>	'settings',
					),
);

$config[ 'wp-cloud-servers-ploi' ]['Ploi'][0] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'install-website',
	'module'		=> 'Ploi',
	'active'		=> $activated,
	'position'		=> '6',
	'template'		=> 'install-website',
	'template_path'	=> 'includes/config/templates',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Sites',
	'type'     		=> 'text',
	'api_required'	=> array( 'ploi'),
	'section_width'	=> '',
	'content_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
					'tab_block_id'	=> 'ploi_sites_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Sites',
						'tab2'		=>	'+ Add Site',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'ploi-list-managed-websites',
						'tab2'		=>	'ploi-install-website',
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
						'menu2'		=>	'Domain Names',
					),
					'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'false',
					),
					'modal_menu_action'	=> array(
						'menu1'		=>	'web_application',
						'menu2'		=>	'',
					),
);

$config[ 'wp-cloud-servers-ploi' ]['Ploi'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'ploi-template-details',
	'module'		=> 'Ploi',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'ploi-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'ploi'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'ploi_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Server Templates',
				'tab2'		=>	'Site Templates',
				'tab3'		=>	'+ Add Server Template',
				'tab4'		=>	'+ Add Site Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'ploi-list-managed-server-templates',
				'tab2'		=>	'ploi-list-managed-site-templates',
				'tab3'		=>	'ploi-create-managed-server-template',
				'tab4'		=>	'ploi-create-managed-site-template',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
				'tab2'		=>	'true',
				'tab3'		=>	'true',
				'tab4'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
				'tab2'		=>	'',
				'tab3'		=>	'xsmall',
				'tab4'		=>	'xsmall',
			),
);

$config[ 'wp-cloud-servers-ploi' ]['Ploi'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'ploi-client-details',
	'module'		=> 'Ploi',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'ploi-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'ploi'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'ploi_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'ploi-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-ploi' ]['Ploi'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-host-name',
	'module'		=> '',
	'active'		=> wpcs_cart_active(),
	'position'		=> '7',
	'template'		=> 'create-host-name',
	'template_path'	=> 'modules/ploi/includes/admin/config/templates',	
	'menu_header'	=> 'Settings',
	'menu_divider'	=> 'yes',
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
	'tab_block_id'	=> 'settings_ploi_hostnames_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'ploi-list-host-names',
		'tab2'		=>	'ploi-create-host-name',
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

$config[ 'wp-cloud-servers-ploi' ]['Ploi'][4] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

update_option( 'wpcs_ploi_module_config', $ploi );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_ploi_config', 10, 3 );

function wpcs_ploi_summary_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Summary', 'wp-cloud-server' ); ?></h3>
			<table class="server-info uk-table uk-table-striped">
    			<tbody>
										 <tr>
            						<td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['name']}"; ?></td>
       							</tr>
								<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server Type', 'wp-cloud-server' ); ?></td>
									<td><?php echo wpcs_ploi_server_type_map( $server['type'] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
									<td><?php echo ( isset( $server['ip_address'] ) ) ? $server['ip_address'] : 'Not Assigned'; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></td>
									<td><?php echo ( 'none' !== $server['php_version'] ) ? $server['php_version'] : '-'; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Site Count', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['sites_count']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            						<td><?php echo wpcs_ploi_server_status_map( $server['status'] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
									<?php $date = explode(" ", $server['created_at']); ?>
            						<td><?php echo $date[0]; ?></td>
        						</tr>
    						        </tbody>
						        </table>
	</div>
	<?php
}
add_action( 'wpcs_ploi_summary_content', 'wpcs_ploi_summary_upgrade' );

function wpcs_ploi_web_application_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Site Summary', 'wp-cloud-server' ); ?></h3>
			<table class="server-info uk-table uk-table-striped">
    			<tbody>
					<tr>
            			<td><?php esc_html_e( 'Site ID', 'wp-cloud-server' ); ?></td>
            			<td><?php echo "{$server['id']}"; ?></td>
       				</tr>
					<tr>
            			<td><?php esc_html_e( 'Domain', 'wp-cloud-server' ); ?></td>
						<td><a class="uk-link" href="http://<?php echo $server['domain']; ?>"><?php echo $server['domain']; ?></a></td>
       				</tr>
        						<tr>
            						<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
									<td><?php echo wpcs_ploi_server_status_map( $server['status'] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['php_version']}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Project Type', 'wp-cloud-server' ); ?></td>
            						<td><?php echo wpcs_ploi_server_project_map( $server['project_type'] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
									<?php $date = explode(" ", $server['created_at']); ?>
            						<td><?php echo $date[0]; ?></td>
        						</tr>
    						        </tbody>
						        </table>
	</div>
	<?php
}
add_action( 'wpcs_ploi_web_application_content', 'wpcs_ploi_web_application_upgrade' );