<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_aws_lightsail_config( $modules, $module_name, $status ) {

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

require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-settings.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-logged-data.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-debug.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-license-settings.php';

require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-server-details.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-template-details.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-client-details.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-add-server.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-add-template.php';
	
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-backup-details.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-snapshot-details.php';

require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-list-ssh-keys.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-create-ssh-key.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-create-user-github-startup-scripts.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-list-user-startup-scripts.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-create-user-startup-scripts.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-list-host-names.php';
require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/templates/aws-lightsail-create-host-name.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */

if ( ( 'AWS Lightsail' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules			= get_option( 'wpcs_module_list' );
$activated			= ( isset( $modules[ 'AWS Lightsail']['status'] ) && ( 'active' == $modules[ 'AWS Lightsail']['status'] ) ) ? 'true' : 'false';
$config				= get_option( 'wpcs_module_config' );
$page_position 		= ( $debug_enabled ) ? '10' : '9';
$active_tab			= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'aws-lightsail-settings',
	'module'		=>  'AWS Lightsail',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'aws-lightsail-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=>  'AWS Lightsail',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'aws_lightsail_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'aws-lightsail-settings',
		'tab2'		=>	'aws-lightsail-logged-data',
		'tab3'		=>	'aws-lightsail-debug',
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

$config[ 'wp-cloud-server-admin-menu' ][ 'AWS Lightsail' ] = $sub_menu;

$aws_lightsail[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'aws-lightsail-server-details',
			'module'		=> 'AWS Lightsail',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'aws-lightsail-server-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> 'Manage',
			'menu_divider'	=> 'yes',
			'menu_item'		=>  'Instances',
			'type'     		=> 'text',
			'api_required'	=> array( 'aws lightsail'),
			'title'    		=> 'AWS Lightsail Linux Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'aws_lightsail_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Instances',
						'tab2'		=>	'+ Add Instance',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'aws-lightsail-server-details',
						'tab2'		=>	'aws-lightsail-add-server',
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
						'menu2'		=>	'Storage',
						'menu3'		=>	'Metrics',
						'menu4'		=>	'Networking',
						'menu5'		=>	'Snapshots',
						'menu6'		=>	'History',
					),
			'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'false',
						'menu3'		=>	'false',
						'menu4'		=>	'false',
						'menu5'		=>	'false',
						'menu6'		=>	'false',
					),
			'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
						'menu2'		=>	'storage',
						'menu3'		=>	'metrics',
						'menu4'		=>	'networking',
						'menu5'		=>	'snapshots',
						'menu6'		=>	'history',
					),
		);

$config[ 'wp-cloud-servers-aws-lightsail' ][ 'AWS Lightsail' ][0] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'aws-lightsail-backup-details',
			'module'		=> 'AWS Lightsail',
			'active'		=> 'false',
			'position'		=> '9',
			'template'		=> 'aws-lightsail-backup-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Snapshots',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'upcloud'),
			'title'    		=> 'AWS Lightsail Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'aws_lightsail_backup_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Snapshots',
						'tab2'		=>	'Backups',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'aws-lightsail-snapshot-details',
						'tab2'		=>	'aws-lightsail-backup-details',
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

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'aws-lightsail-template-details',
	'module'		=> 'AWS Lightsail',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'aws-lightsail-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'aws lightsail'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'aws_lightsail_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'aws-lightsail-template-details',
				'tab2'		=>	'aws-lightsail-add-template',
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

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'aws-lightsail-client-details',
	'module'		=> 'AWS Lightsail',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'aws-lightsail-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'aws-lightsail'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'aws-lightsail_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'aws-lightsail-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][3] = $sub_menu;

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
	'tab_block_id'	=> 'settings_aws_lightsail_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'aws-lightsail-list-ssh-keys',
		'tab2'		=>	'aws-lightsail-create-ssh-key',
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

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][4] = $sub_menu;

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
	'tab_block_id'	=> 'settings_aws_lightsail_scripts_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Startup Scripts',
		'tab2'		=>	'+ Add Startup Script',
		'tab3'		=>	'+ Add GitHub Script',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'aws-lightsail-list-user-startup-scripts',
		'tab2'		=>	'aws-lightsail-create-user-startup-scripts',
		'tab3'		=>	'aws-lightsail-create-user-github-startup-scripts',
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

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][5] = $sub_menu;

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
	'tab_block_id'	=> 'settings_aws_lightsail_hostname_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Hostnames',
		'tab2'		=>	'+ Add Hostname',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'aws-lightsail-list-host-names',
		'tab2'		=>	'aws-lightsail-create-host-name',
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

$config[ 'wp-cloud-servers-aws-lightsail' ]['AWS Lightsail'][6] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_aws_lightsail_pro_config' ) ) {
	$config = wpcs_setup_aws_lightsail_pro_config( $config );
}

update_option( 'wpcs_aws_lightsail_module_config', $aws_lightsail );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_aws_lightsail_config', 10, 3 );

function wpcs_aws_lightsail_summary_upgrade( $server ) {
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
            									<td><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></td>
            									<td><?php echo ucfirst($server['state']['name']); ?></td>
        									</tr>
        									<tr>
           	 									<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
            									<td><?php echo wpcs_aws_lightsail_region_map( $server['location']['regionName'] ); ?></td>
       	 									</tr>
        									<tr>
            									<td><?php esc_html_e( 'Image/Application', 'wp-cloud-server' ); ?></td>
												<td><?php echo $server['blueprintName']; ?></td>
        									</tr>
        									<tr>
            									<td><?php esc_html_e( 'VCPUs', 'wp-cloud-server' ); ?></td>
            									<td><?php echo $server['hardware']['cpuCount']; ?></td>
        									</tr>
        									<tr>
            									<td><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></td>
           										<td><?php echo "{$server['hardware']['ramSizeInGb']}GB" ?></td>
       	 									</tr>
        									<tr>
            									<td><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></td>
           	 									<td><?php echo "{$server['hardware']['disks'][0]['sizeInGb']}GB"; ?></td>
        									</tr>
        									<tr>
            									<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            									<td><?php echo isset($server['publicIpAddress']) ? $server['publicIpAddress'] : 'Not Available'; ?></td>
        									</tr>
        									<tr>
            									<td><?php esc_html_e( 'SSH Key', 'wp-cloud-server' ); ?></td>
            									<td><?php echo ( "LightsailDefaultKeyPair" !== $server['sshKeyName'] ) ? $server['sshKeyName'] : 'No SSH Key'; ?></td>
        									</tr>
        									<tr>
            									<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            									<td><?php echo gmdate("Y-m-d", $server['createdAt']) ; ?></td>
        									</tr>
    						        </tbody>
						        </table>
							</div>
	<?php
}
add_action( 'wpcs_aws_lightsail_summary_content', 'wpcs_aws_lightsail_summary_upgrade' );

function wpcs_aws_lightsail_backup_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Backups', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_aws_lightsail_backup_content', 'wpcs_aws_lightsail_backup_upgrade' );

function wpcs_aws_lightsail_snapshot_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Snapshots', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_aws_lightsail_snapshot_content', 'wpcs_aws_lightsail_snapshot_upgrade' );