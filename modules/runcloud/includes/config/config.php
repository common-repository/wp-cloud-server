<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_runcloud_config( $modules, $module_name, $status ) {

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

require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-settings.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-logged-data.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-debug.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-server-details.php';

require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-list-managed-websites.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-list-managed-servers.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-list-managed-templates.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-list-website-templates.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-install-managed-website.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-connect-managed-server.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-create-managed-template.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-create-web-app-template.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-client-details.php';

require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-list-ssh-keys.php';
require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/templates/runcloud-create-ssh-key.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */
//update_option( 'wpcs_runcloud_server_complete_queue', array() );

if ( ( 'RunCloud' == $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset($modules['RunCloud']['status']) && ( 'active' == $modules['RunCloud']['status'] ) ) ? 'true' : 'false';

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'runcloud-settings',
	'module'		=> 'RunCloud',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'runcloud-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'RunCloud',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'runcloud_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'runcloud-settings',
		'tab2'		=>	'runcloud-logged-data',
		'tab3'		=>	'runcloud-debug',
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

$config[ 'wp-cloud-server-admin-menu' ]['RunCloud'] = $sub_menu;

$runcloud[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'runcloud-managed-servers',
	'module'		=> 'RunCloud',
	'active'		=> $activated,
	'position'		=> '6',
	'template'		=> 'runcloud-managed-servers',
	'template_path'	=> 'includes/config/templates',	
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Servers',
	'type'     		=> 'text',
	'api_required'	=> array( 'runcloud', 'cloud_provider' ),
	'section_width'	=> '',
	'content_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
					'tab_block_id'	=> 'runcloud_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'runcloud-list-managed-servers',
						'tab2'		=>	'runcloud-connect-managed-server',
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
						'menu2'		=>	'Server Health',
						'menu3'		=>	'Web Application',
						'menu4'		=>	'Database',
						'menu5'		=>	'System User',
						'menu6'		=>	'SSH Key',
						'menu7'		=>	'Deployment Key',
						'menu8'		=>	'PHP CLI',
						'menu9'		=>	'Cron Job',
						'menu10'	=>	'Supervisor',
						'menu11'	=>	'Notifications',
						'menu12'	=>	'Services',
						'menu13'	=>	'Security',
						'menu14'	=>	'Activity Log',
					),
			'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'false',
						'menu3'		=>	'true',
						'menu4'		=>	'true',
						'menu5'		=>	'true',
						'menu6'		=>	'false',
						'menu7'		=>	'false',
						'menu8'		=>	'false',
						'menu9'		=>	'false',
						'menu10'	=>	'false',
						'menu11'	=>	'false',
						'menu12'	=>	'true',
						'menu13'	=>	'true',
						'menu14'	=>	'false',
					),
			'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
						'menu2'		=>	'server_health',
						'menu3'		=>	'web_application',
						'menu4'		=>	'database',
						'menu5'		=>	'system_user',
						'menu6'		=>	'ssh_key',
						'menu7'		=>	'deployment_key',
						'menu8'		=>	'php_cli',
						'menu9'		=>	'cron_job',
						'menu10'	=>	'supervisor',
						'menu11'	=>	'notifications',
						'menu12'	=>	'services',
						'menu13'	=>	'security',
						'menu14'	=>	'activity_log',
					),
);

$config[ 'wp-cloud-servers-runcloud' ]['RunCloud'][0] = $sub_menu;
	
$sub_menu = array(
	'id'       		=> 'install-website',
	'module'		=> 'RunCloud',
	'active'		=> 'true',
	'position'		=> '6',
	'template'		=> 'install-website',
	'template_path'	=> 'includes/config/templates',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Web Applications',
	'type'     		=> 'text',
	'api_required'	=> array( 'runcloud', 'cloud_provider' ),
	'section_width'	=> '',
	'content_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
					'tab_block_id'	=> 'runcloud_website_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Web Applications',
						'tab2'		=>	'+ Add Web Application',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'runcloud-list-managed-websites',
						'tab2'		=>	'runcloud-install-managed-website',
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
						'menu2'		=>	'true',
					),
					'modal_menu_action'	=> array(
						'menu1'		=>	'app_summary',
						'menu2'		=>	'app_domains',
					),
);

$config[ 'wp-cloud-servers-runcloud' ]['RunCloud'][1] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'runcloud-template-details',
	'module'		=> 'RunCloud',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'runcloud-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'runcloud', 'cloud_provider' ),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'runcloud_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'runcloud-list-managed-templates',
				'tab2'		=>	'runcloud-create-managed-template',
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

$config[ 'wp-cloud-servers-runcloud' ]['RunCloud'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'runcloud-client-details',
	'module'		=> 'RunCloud',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'runcloud-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'runcloud'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'runcloud_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'runcloud-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-servers-runcloud' ]['RunCloud'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'create-ssh-key',
	'module'		=> '',
	'active'		=> wpcs_cart_active(),
	'position'		=> '7',
	'template'		=> 'create-ssh-key',
	'template_path'	=> 'includes/config/templates/',	
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
	'tab_block_id'	=> 'settings_runcloud_ssh_tabs',
	'tabs'			=> array(
		'tab1'		=>	'SSH Keys',
		'tab2'		=>	'+ Add SSH Key',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'runcloud-list-ssh-keys',
		'tab2'		=>	'runcloud-create-ssh-key',
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

$config[ 'wp-cloud-servers-runcloud' ]['RunCloud'][4] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_runcloud_pro_config' ) ) {
	$config = wpcs_setup_runcloud_pro_config( $config );
}

update_option( 'wpcs_runcloud_module_config', $runcloud );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_runcloud_config', 10, 3 );

function wpcs_runcloud_summary_upgrade( $server ) {
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
            						<td><?php esc_html_e( 'Provider', 'wp-cloud-server' ); ?></td>
									<td><?php echo "{$server['provider']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'OS', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo "{$server['os']} {$server['osVersion']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['ipAddress']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
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
add_action( 'wpcs_runcloud_summary_content', 'wpcs_runcloud_summary_upgrade' );

function wpcs_runcloud_server_health_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Health', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_server_health_content', 'wpcs_runcloud_server_health_upgrade' );

function wpcs_runcloud_web_application_upgrade( $server ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_RunCloud_API;
	$data	= $api->call_api( "servers/{$server['id']}/webapps", null, false, 900, 'GET', false, 'runcloud_web_app_list' );
	$sites	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Web Applications', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Server', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Default', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Stack', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty($sites) ) {
						foreach ( $sites as $key => $site ) {
							if ( is_array( $site ) ) {
								?>
        						<tr>
									<td><?php echo $site['id']; ?></td>
									<td><?php echo $site['name']; ?></td>
									<td><?php echo $server['name']; ?></td>
									<?php $default_app = ( $site['defaultApp'] ) ? 'Yes' : 'No'; ?>
            						<td><?php echo "{$default_app}"; ?></td>
									<td><?php echo $site['phpVersion']; ?></td>
									<td><?php echo $site['stackMode']; ?></td>
									<td><?php echo $site['created_at']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Web Application Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_web_application_content', 'wpcs_runcloud_web_application_upgrade' );

function wpcs_runcloud_database_upgrade( $server ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_RunCloud_API;
	$data	= $api->call_api( "servers/{$server['id']}/databases", null, false, 900, 'GET', false, 'runcloud_web_app_list' );
	$dbases	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Databases', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Collation', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty( $dbases ) ) {
						foreach ( $dbases as $key => $dbase ) {
							if ( is_array( $dbase ) ) {
								?>
        						<tr>
									<td><?php echo $dbase['id']; ?></td>
									<td><?php echo $dbase['name']; ?></td>
									<td><?php echo $dbase['collation']; ?></td>
									<td><?php echo $dbase['created_at']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Database Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_database_content', 'wpcs_runcloud_database_upgrade' );

function wpcs_runcloud_system_user_upgrade( $server ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_RunCloud_API;
	$data	= $api->call_api( "servers/{$server['id']}/users", null, false, 900, 'GET', false, 'runcloud_users_list' );
	$sysusers	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'System Users', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Username', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Key', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Deleteable', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty( $sysusers ) ) {
						foreach ( $sysusers as $key => $sysuser ) {
							if ( is_array( $sysuser ) ) {
								?>
        						<tr>
									<td><?php echo $sysuser['id']; ?></td>
									<td><?php echo $sysuser['username']; ?></td>
									<td><?php echo $sysuser['deploymentKey']; ?></td>
									<td><?php $delete = ( $sysuser['deleteable'] ) ? 'Yes' : 'No'; echo $delete; ?></td>
									<td><?php echo $sysuser['created_at']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No System User Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_system_user_content', 'wpcs_runcloud_system_user_upgrade' );

function wpcs_runcloud_ssh_key_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'SSH Key', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_ssh_key_content', 'wpcs_runcloud_ssh_key_upgrade' );

function wpcs_runcloud_deployment_key_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Deployment Key', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_deployment_key_content', 'wpcs_runcloud_deployment_key_upgrade' );

function wpcs_runcloud_php_cli_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'PHP CLI', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_php_cli_content', 'wpcs_runcloud_php_cli_upgrade' );

function wpcs_runcloud_cron_job_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Cron Job', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_cron_job_content', 'wpcs_runcloud_cron_job_upgrade' );

function wpcs_runcloud_supervisor_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Supervisor', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_supervisor_content', 'wpcs_runcloud_supervisor_upgrade' );

function wpcs_runcloud_notifications_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Notifications', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_notifications_content', 'wpcs_runcloud_notifications_upgrade' );

function wpcs_runcloud_services_upgrade( $server ) {
		// Create instance of the RunCloud API
		$api	= new WP_Cloud_Server_RunCloud_API;
		$data	= $api->call_api( "servers/{$server['id']}/services", null, false, 900, 'GET', false, 'runcloud_services_list' );
		$sysusers	= ( isset( $data ) ) ? $data : array();
		?>
		<div class="uk-overflow-auto">
			<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Services', 'wp-cloud-server' ); ?></h3>
			<table class="uk-table uk-table-striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
					<th><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></th>
					<th><?php esc_html_e( 'CPU', 'wp-cloud-server' ); ?></th>
					<th><?php esc_html_e( 'Running', 'wp-cloud-server' ); ?></th>
					<th><?php esc_html_e( 'Version', 'wp-cloud-server' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
						if ( !empty( $sysusers ) ) {
							foreach ( $sysusers as $key => $sysuser ) {
								if ( is_array( $sysuser ) ) {
									?>
									<tr>
										<td><?php echo $sysuser['name']; ?></td>
										<td><?php echo $sysuser['memory']; ?></td>
										<td><?php echo $sysuser['cpu']; ?></td>
										<td><?php $running = ( $sysuser['running'] ) ? 'Yes' : 'No'; echo $running; ?></td>
										<td><?php echo $sysuser['version']; ?></td>
									</tr>
									<?php
								}
							}
						} else {
						?>
							<tr>
								<td colspan="8"><?php esc_html_e( 'No Services Information Available', 'wp-cloud-server' ) ?></td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
		</div>
		<?php
	}
add_action( 'wpcs_runcloud_services_content', 'wpcs_runcloud_services_upgrade' );

function wpcs_runcloud_security_upgrade( $server ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_RunCloud_API;
	$data	= $api->call_api( "servers/{$server['id']}/security/firewalls", null, false, 900, 'GET', false, 'runcloud_security_list' );
	$rules	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Security', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Type', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Port', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Protocol', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Action', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
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
									<td><?php echo $rule['type']; ?></td>
									<td><?php echo $rule['port']; ?></td>
									<td><?php echo $rule['protocol']; ?></td>
									<td><?php echo $rule['ipAddress']; ?></td>
									<td><?php echo $rule['firewallAction']; ?></td>
									<td><?php echo $rule['created_at']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Security Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_security_content', 'wpcs_runcloud_security_upgrade' );

function wpcs_runcloud_activity_log_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Activity Log', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_activity_log_content', 'wpcs_runcloud_activity_log_upgrade' );

// Web Application Functionality

function wpcs_runcloud_app_summary_upgrade( $site ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'App Summary', 'wp-cloud-server' ); ?></h3>
						        <table class="server-info uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						<td><?php esc_html_e( 'Web App Name', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['name']}"; ?></td>
       							</tr>
        						<tr>
            						<td><?php esc_html_e( 'Web App ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['id']}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Server', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['server_name']}"; ?></td>
       							</tr>
								<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['server_id']}"; ?></td>
       							</tr>
								<tr>
            						<td><?php esc_html_e( 'PHP Version', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['phpVersion']}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Root Path', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['rootPath']}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Public Path', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['publicPath']}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Default App', 'wp-cloud-server' ); ?></td>
									<?php $default_app = ( $site['defaultApp'] ) ? 'Yes' : 'No'; ?>
            						<td><?php echo "{$default_app}"; ?></td>
        						</tr>
								<tr>
            						<td><?php esc_html_e( 'Stack', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['stack']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$site['created_at']}"; ?></td>
        						</tr>
    						        </tbody>
						        </table>
					        </div>
	<?php
}
add_action( 'wpcs_runcloud_app_summary_content', 'wpcs_runcloud_app_summary_upgrade' );

function wpcs_runcloud_app_domains_upgrade( $site ) {
	// Create instance of the RunCloud API
	$api	= new WP_Cloud_Server_RunCloud_API;
	$data	= $api->call_api( "servers/{$site['server_id']}/webapps/{$site['id']}/domains", null, false, 900, 'GET', false, 'runcloud_domain_list' );
	$rules	= ( isset( $data['data'] ) ) ? $data['data'] : array();
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Security', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'ID', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wp-cloud-server' ); ?></th>
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
									<td><a class="uk-link" href="http://<?php echo $rule['name']; ?>"><?php echo $rule['name']; ?></a></td>
									<td><?php echo $rule['created_at']; ?></td>
        						</tr>
								<?php
							}
						}
					} else {
					?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No Domain Name Information Available', 'wp-cloud-server' ) ?></td>
						</tr>
					<?php
				}
			?>
    	</tbody>
	</table>
	</div>
	<?php
}
add_action( 'wpcs_runcloud_app_domains_content', 'wpcs_runcloud_app_domains_upgrade' );