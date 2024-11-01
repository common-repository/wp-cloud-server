<?php
/**
 * WP Cloud Server ServerPilot Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_serverpilot_config( $modules, $module_name, $status ) {

	if ( !isset( $modules['ServerPilot'] ) ) {
		return;
	}

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
	
if ( ( 'ServerPilot' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

//$modules		= get_option( 'wpcs_module_list' );
$config			= get_option( 'wpcs_module_config' );
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';
$activated		= ( isset( $modules['ServerPilot']['status'] ) && ( 'active' == $modules['ServerPilot']['status'] ) ) ? 'true' : 'false';
$ecommerce		= wpcs_cart_active();

$serverpilot_active		= ( isset($modules['ServerPilot']['status']) && ( 'active' == $modules['ServerPilot']['status'] ) );
$digitalocean_active	= ( isset($modules['DigitalOcean']['status']) && ( 'active' == $modules['DigitalOcean']['status'] ) );
$cloudways_active		= ( isset($modules['Cloudways']['status']) && ( 'active' == $modules['Cloudways']['status'] ) );

$api_status				= wpcs_check_cloud_provider_api( null, null, true, $modules);
$cloud_active			= wpcs_check_cloud_provider_api( null, null, true);

foreach ( $modules as $key => $module ) {
	if ( 'active' == $module['status'] ) {
		$modules_available = true;
	}
}

$module_menu_active = ( isset( $modules_available ) && $modules_available ) ? 'true' : 'false';
	
$page_position = '5';

$sub_menu = array(
	'id'       		=> 'serverpilot-settings',
	'active'		=> $activated,
	'module'		=> 'ServerPilot',
	'position'		=> $page_position,
	'template'		=> 'serverpilot-settings',
	'template_path'	=> 'modules/serverpilot/includes/admin/dashboard/config/templates',
	'menu_active'	=> $module_menu_active,
	'menu_header'	=> 'Modules',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'ServerPilot',
	'section_width'	=> '',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'serverpilot_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'serverpilot-settings',
		'tab2'		=>	'serverpilot-logged-data',
		'tab3'		=>	'serverpilot-debug',
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

$config[ 'wp-cloud-server-admin-menu' ]['ServerPilot'] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'installed-websites',
	'module'		=> 'ServerPilot',
	'active'		=> 'true',
	'position'		=> '11',
	'template'		=> 'installed-websites',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Servers',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'serverpilot', 'cloud_provider'),
	'title'    		=> esc_html__( 'Installed Websites', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'serverpilot_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'list-managed-servers',
						'tab2'		=>	'create-managed-server',
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
					),
					'modal_menu_active'	=> array(
						'menu1'		=>	'true',
					),
					'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
					),
);

$config[ 'wp-cloud-servers-serverpilot' ]['ServerPilot'][0] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'list-managed-apps',
	'module'		=> 'ServerPilot',
	'active'		=> 'true',
	'position'		=> '11',
	'template'		=> 'list-managed-apps',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Apps',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'serverpilot', 'cloud_provider'),
	'title'    		=> esc_html__( 'Installed Websites', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'serverpilot_apps_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Apps',
						'tab2'		=>	'+ Add App',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'list-managed-apps',
						'tab2'		=>	'create-managed-app',
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
						'menu2'		=>	'Databases',
					),
					'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'true',
					),
					'modal_menu_action'	=> array(
						'menu1'		=>	'app_summary',
						'menu2'		=>	'app_databases',
					),
);

$config[ 'wp-cloud-servers-serverpilot' ]['ServerPilot'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'list-managed-server-templates',
	'module'		=> 'ServerPilot',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'list-managed-server-templates',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'serverpilot'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'serverpilot_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'list-managed-server-templates',
				'tab2'		=>	'create-managed-template',
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

$config[ 'wp-cloud-servers-serverpilot' ]['ServerPilot'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'list-managed-client-details',
	'module'		=> 'ServerPilot',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'list-managed-client-details',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'serverpilot'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'serverpilot_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'list-managed-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-serverpilot' ]['ServerPilot'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-ssh-key',
	'module'		=> '',
	'active'		=> 'true',
	'position'		=> '7',
	'template'		=> 'create-ssh-key',
	'template_path'	=> 'modules/serverpilot/includes/admin/dashboard/config/templates',	
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
	'tab_block_id'	=> 'settings_serverpilot_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'serverpilot-list-ssh-keys',
		'tab2'		=>	'serverpilot-create-ssh-key',
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

$config[ 'wp-cloud-servers-serverpilot' ]['ServerPilot'][4] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */
	
update_option( 'wpcs_module_config', $config );
}
add_action( 'wpcs_update_module_config', 'wpcs_setup_serverpilot_config', 10, 3 );

function wpcs_serverpilot_summary_upgrade( $server ) {
	
	if ( is_array( $server['available_runtimes'] ) ) {
		$key = ( count( $server['available_runtimes'] ) - 1 );
		$php_version = $server['available_runtimes'][ $key ];
	} else {
		$php_version = 'php7.4';
	}
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
            						<td><?php esc_html_e( 'Plan', 'wp-cloud-server' ); ?></td>
            						<?php $plan = ( $server['plan'] == 'first_class' ) ? 'First Class' : ucfirst($server['plan']); ?>
									<td><?php echo $plan; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Auto Updates', 'wp-cloud-server' ); ?></td>
           							<td><?php echo ( 1 == $server['autoupdates'] ) ? 'Enabled' : 'Not Enabled'; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo preg_replace( '/^php/', 'PHP ', $php_version ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['lastaddress']) ? $server['lastaddress'] : 'Not Available'; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php echo date( 'd/M/Y', $server['datecreated']); ?></td>
        						</tr>
    						        </tbody>
						        </table>
	</div>
	<?php
}
add_action( 'wpcs_serverpilot_summary_content', 'wpcs_serverpilot_summary_upgrade' );

function wpcs_serverpilot_backup_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Backups', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_serverpilot_backup_content', 'wpcs_serverpilot_backup_upgrade' );

function wpcs_serverpilot_snapshot_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Snapshots', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_serverpilot_snapshot_content', 'wpcs_serverpilot_snapshot_upgrade' );

// Web Application Functionality

function wpcs_serverpilot_app_summary_upgrade( $app ) {
	//$api	= new WP_Cloud_Server_ServerPilot_API;
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'App Summary', 'wp-cloud-server' ); ?></h3>
		<table class="app-info uk-table uk-table-striped">
    						<tbody>
        						<tr>
            						<td><?php esc_html_e( 'App Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$app['name']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Domain', 'wp-cloud-server' ); ?></td>
           							<td><a class="uk-link" href="<?php echo esc_url( $app['domains'][0] ); ?>" target="_blank"><?php echo preg_replace( '/^www./', '', $app['domains'][0] ); ?></td>
       	 						</tr>
									<tr>
            						<td><?php esc_html_e( 'SSL', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo wpcs_sp_api_ssl_status( $app['id'], $app['domains'][0] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'App ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$app['id']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Sys User ID', 'wp-cloud-server' ); ?></td>
									<td><?php echo $app['sysuserid']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $app['serverid']; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php echo date( 'd/M/Y', $app['datecreated'] ); ?></td>
        						</tr>
    						</tbody>
							</table>
					        </div>
	<?php
}
add_action( 'wpcs_serverpilot_app_summary_content', 'wpcs_serverpilot_app_summary_upgrade' );

function wpcs_serverpilot_app_databases_upgrade( $app ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_ServerPilot_API;
	
	$data	= $api->call_api( "dbs", null, false, 900, 'GET', false, 'serverpilot_database_list' );
	
	$rules	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Databases', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'User ID', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'User Name', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty( $rules ) ) {
						foreach ( $rules as $key => $rule ) {
							if ( is_array( $rule ) ) {
								?>
        						<tr>
									<td><?php echo $rule['id']; ?></td>
									<td><?php echo $rule['name']; ?></td>
									<td><?php echo $rule['user']['id']; ?></td>
									<td><?php echo $rule['user']['name']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Backup Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_serverpilot_app_databases_content', 'wpcs_serverpilot_app_databases_upgrade' );

function wpcs_serverpilot_app_ssl_upgrade( $app ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_ServerPilot_API;
	
	$data	= $api->call_api( "dbs", null, false, 900, 'GET', false, 'serverpilot_database_list' );
	
	$rules	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Databases', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'User ID', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'User Name', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty( $rules ) ) {
						foreach ( $rules as $key => $rule ) {
							if ( is_array( $rule ) ) {
								?>
        						<tr>
									<td><?php echo $rule['id']; ?></td>
									<td><?php echo $rule['name']; ?></td>
									<td><?php echo $rule['user']['id']; ?></td>
									<td><?php echo $rule['user']['name']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Backup Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_serverpilot_app_ssl_content', 'wpcs_serverpilot_app_ssl_upgrade' );