<?php
/**
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_setup_cloudways_config( $modules, $module_name, $status ) {

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

// Get the module view tab content
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-settings.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-logged-data.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-debug.php';

// Get the managed hosting tab content
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-client-details.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-server-details.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-application-details.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-template-details.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-create-server.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-create-application.php';
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-create-template.php';
	
require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/templates/cloudways-add-ons.php';

/*
 *
 * ---> BEGIN SECTIONS
 *
 */

/* -> START WordPress Sub Menu. */
//update_option( 'wpcs_cloudways_server_complete_queue', array() );

$modules	= get_option( 'wpcs_module_list' );

$activated	= ( isset($modules['Cloudways']['status']) && ( 'active' == $modules['Cloudways']['status'] ) ) ? 'true' : 'false';

//delete_option( 'wpcs_module_config' );

$config		= get_option( 'wpcs_module_config' );

$page_position = ( $debug_enabled ) ? '10' : '9';
$active_tab		= ( $debug_enabled ) ? 'true' : 'false';

$sub_menu = array(
	'id'       		=> 'cloudways-settings',
	'module'		=> 'Cloudways',
	'active'		=> $activated,
	'position'		=> $page_position,
	'template'		=> 'cloudways-settings',
	'template_path'	=> 'includes/config/templates/',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Cloudways',
	'type'     		=> 'settings',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'cloudways_tabs',
	'tabs'			=> array(
		'tab1'		=>	'Settings',
		'tab2'		=>	'Event Log',
		'tab3'		=>	'Debug',
	),
	'tabs_content'	=> array(
		'tab1'		=>	'cloudways-settings',
		'tab2'		=>	'cloudways-logged-data',
		'tab3'		=>	'cloudways-debug',
	),
	'tabs_active'	=> array(
		'tab1'		=>	'true',
		'tab2'		=>	'true',
		'tab3'		=>	$active_tab,
	),
	'tabs_width'	=> array(
		'tab1'		=>	'xsmall',
		'tab2'		=>	'',
		'tab3'		=>	$active_tab,
	),
);

$config[ 'wp-cloud-server-admin-menu' ]['content'][$sub_menu['id']] = $sub_menu;

$config[ 'wp-cloud-server-admin-menu' ]['Cloudways'] = $sub_menu;

$cloudways[$sub_menu['id']] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'cloudways-server-details',
			'module'		=> 'Cloudways',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'cloudways-server-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Servers',
			'type'     		=> 'text',
			'api_required'	=> array( 'cloudways'),
			'title'    		=> 'Cloudways Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'cloudways_server_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Servers',
						'tab2'		=>	'+ Add Server',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'cloudways-server-details',
						'tab2'		=>	'cloudways-create-server',
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
						'menu2'		=>	'Monitoring',
						'menu3'		=>	'Manage Services',
						'menu4'		=>	'Settings & Packages',
						'menu5'		=>	'Security',
						'menu6'		=>	'Vertical Scaling',
						'menu7'		=>	'Backups',
						'menu8'		=>	'SMTP',
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
					),
			'modal_menu_action'	=> array(
						'menu1'		=>	'summary',
						'menu2'		=>	'monitoring',
						'menu3'		=>	'manage_services',
						'menu4'		=>	'settings_packages',
						'menu5'		=>	'security',
						'menu6'		=>	'vertical_scaling',
						'menu7'		=>	'backups',
						'menu8'		=>	'smtp',
					),
);

$config[ 'wp-cloud-server-managed-hosting' ]['Cloudways'][0] = $sub_menu;
	
$sub_menu = array(
			'id'       		=> 'cloudways-app-details',
			'module'		=> 'Cloudways',
			'active'		=> $activated,
			'position'		=> '9',
			'template'		=> 'cloudways-app-details',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Applications',
			'type'     		=> 'text',
			'api_required'	=> array( 'cloudways'),
			'title'    		=> 'Cloudways Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'cloudways_application_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Applications',
						'tab2'		=>	'+ Add Application',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'cloudways-application-details',
						'tab2'		=>	'cloudways-create-application',
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
					),
					'modal_menu_active'	=> array(
						'menu1'		=>	'true',
						'menu2'		=>	'false',
					),
					'modal_menu_action'	=> array(
						'menu1'		=>	'app_summary',
						'menu2'		=>	'app_backups',
					),
);

$config[ 'wp-cloud-server-managed-hosting' ]['Cloudways'][1] = $sub_menu;

$sub_menu = array(
			'id'       		=> 'cloudways-add-ons',
			'module'		=> 'Cloudways',
			'active'		=> 'false',
			'position'		=> '9',
			'template'		=> 'cloudways-add-ons',
			'template_path'	=> 'includes/config/templates',
			'menu_header'	=> '',
			'menu_divider'	=> '',
			'menu_item'		=> 'Add-ons',
			'section_width'	=> '',
			'type'     		=> 'text',
			'api_required'	=> array( 'cloudways'),
			'title'    		=> 'Cloudways Servers',
			'subtitle' 		=> '',
			'desc'     		=> '',
			'tab_block_id'	=> 'cloudways_addon_tabs',
					'tabs'			=> array(
						'tab1'		=>	'Add-ons',
						'tab2'		=>	'Add-ons',
					),
					'tabs_content'	=> array(
						'tab1'		=>	'cloudways-add-ons',
						'tab2'		=>	'cloudways-add-ons',
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

$config[ 'wp-cloud-server-managed-hosting' ]['Cloudways'][2] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'cloudways-template-details',
	'module'		=> 'Cloudways',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'cloudways-template-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> 'Hosting',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Templates',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'cloudways'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'cloudways_templates_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Templates',
				'tab2'		=>	'+ Add Template',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'cloudways-template-details',
				'tab2'		=>	'cloudways-create-template',
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

$config[ 'wp-cloud-server-managed-hosting' ]['Cloudways'][3] = $sub_menu;

$sub_menu = array(
	'id'       		=> 'cloudways-client-details',
	'module'		=> 'Cloudways',
	'active'		=> wpcs_cart_active(),
	'position'		=> '9',
	'template'		=> 'cloudways-client-details',
	'template_path'	=> 'includes/config/templates',
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Clients',
	'section_width'	=> '',
	'type'     		=> 'text',
	'api_required'	=> array( 'cloudways'),
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
	'tab_block_id'	=> 'cloudways_client_tabs',
			'tabs'			=> array(
				'tab1'		=>	'Clients',
			),
			'tabs_content'	=> array(
				'tab1'		=>	'cloudways-client-details',
			),
			'tabs_active'	=> array(
				'tab1'		=>	'true',
			),
			'tabs_width'	=> array(
				'tab1'		=>	'',
			),
);

$config[ 'wp-cloud-server-managed-hosting' ]['Cloudways'][4] = $sub_menu;

/*
 * <--- END MENU CONFIGURATION
 */

if ( function_exists( 'wpcs_setup_cloudways_pro_config' ) ) {
	$config = wpcs_setup_cloudways_pro_config( $config );
}

update_option( 'wpcs_cloudways_module_config', $cloudways );

update_option( 'wpcs_module_config', $config );

}
add_action( 'wpcs_update_module_config', 'wpcs_setup_cloudways_config', 10, 3 );

function wpcs_cloudways_summary_upgrade( $server ) {
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
            						<td><?php esc_html_e( 'Provider', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['platform']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></td>
           							<td><?php echo "{$server['region']}"; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server FQDN', 'wp-cloud-server' ); ?></td>
           	 						<td><?php echo "{$server['server_fqdn']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['public_ip']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php echo "{$server['id']}"; ?></td>
        						</tr>
        						<tr>
            						<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php echo $server['created_at']; ?></td>
        						</tr>
    						        </tbody>
						        </table>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_summary_content', 'wpcs_cloudways_summary_upgrade' );

function wpcs_cloudways_server_management_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Management', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_server_management_content', 'wpcs_cloudways_server_management_upgrade' );

function wpcs_cloudways_monitoring_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Monitoring', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_monitoring_content', 'wpcs_cloudways_monitoring_upgrade' );

function wpcs_cloudways_manage_services_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Manage Services', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_manage_services_content', 'wpcs_cloudways_manage_services_upgrade' );

function wpcs_cloudways_settings_packages_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Settings & Packages', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_settings_packages_content', 'wpcs_cloudways_settings_packages_upgrade' );

function wpcs_cloudways_security_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Security', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_security_content', 'wpcs_cloudways_security_upgrade' );

function wpcs_cloudways_vertical_scaling_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Vertical Scaling', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_vertical_scaling_content', 'wpcs_cloudways_vertical_scaling_upgrade' );

function wpcs_cloudways_backups_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Server Backups', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_backups_content', 'wpcs_cloudways_backups_upgrade' );

function wpcs_cloudways_smtp_upgrade( $server ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'SMTP', 'wp-cloud-server' ); ?></h3>
	</div>
	<?php
}
add_action( 'wpcs_cloudways_smtp_content', 'wpcs_cloudways_smtp_upgrade' );

// Web Application Functionality

function wpcs_cloudways_app_summary_upgrade( $app ) {
	?>
	<div class="uk-overflow-auto">
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'App Summary', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    						        <tbody>
										 <tr>
            						        <td><?php esc_html_e( 'App ID', 'wp-cloud-server' ); ?></td>
            						        <td><?php echo $app['id']; ?></td>
       							        </tr>
        						        <tr>
            						        <td><?php esc_html_e( 'App Label', 'wp-cloud-server' ); ?></td>
            						        <td><?php echo $app['label']; ?></td>
       							        </tr>
										<tr>
            						        <td><?php esc_html_e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						        <td><?php echo $app['server_label']; ?></td>
       							        </tr>
										<tr>
            								<td><?php esc_html_e( 'App Name', 'wp-cloud-server' ); ?></td>
            								<td><?php echo "{$app['application_name']} {$app['app_version']}"; ?></td>
       									</tr>
        								<tr>
            								<td><?php esc_html_e( 'App FQDN', 'wp-cloud-server' ); ?></td>
            								<td><a href="https://<?php echo $app['app_fqdn']; ?>"><?php echo $app['app_fqdn']; ?></a></td>
       									</tr>
										   <tr>
            								<td><?php esc_html_e( 'App Project', 'wp-cloud-server' ); ?></td>
            								<td><?php echo "{$app['project_id']}"; ?></td>
       									</tr>
        								<tr>
            								<td><?php esc_html_e( 'Server ID', 'wp-cloud-server' ); ?></td>
            								<td><?php echo "{$app['server_id']}"; ?></td>
        								</tr>
        								<tr>
            								<td><?php esc_html_e( 'Date Created', 'wp-cloud-server' ); ?></td>
            								<td><?php echo $app['created_at']; ?></td>
        								</tr>
    						        </tbody>
						        </table>
					        </div>
	<?php
}
add_action( 'wpcs_cloudways_app_summary_content', 'wpcs_cloudways_app_summary_upgrade' );

function wpcs_cloudways_app_backups_upgrade( $app ) {

	// Create instance of the RunCloud API
	// $api	= new WP_Cloud_Server_Cloudways_API;
	
	$parameters = array(
			'server_id' => $app['server_id'],
			'app_id'	=> $app['id'],
		);

	//$data	= $api->call_api( "app/manage/backup", $parameters, false, 900, 'GET', false, 'cloudways_backups_list' );
	//$data	= $api->call_api( "operation/{$data['operation_id']}", $parameters, false, 900, 'GET', false, 'cloudways_backups_list' );
	
	//$rules	= ( isset( $data['operation']['parameters'] ) ) ? $data['operation']['parameters'] : array();
	//$rules = json_decode( $rules, true );
	$rules = array();
	?>
	<div class="uk-overflow-auto">
		
		<h3 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Backups', 'wp-cloud-server' ); ?></h3>
		<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
				<th><?php esc_html_e( 'Date', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
					if ( !empty( $rules ) ) {
						foreach ( $rules as $key => $rule ) {
							if ( is_array( $rule ) ) {
								?>
        						<tr>
									<td><?php echo $rule[0]; ?></td>
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
add_action( 'wpcs_cloudways_app_backups_content', 'wpcs_cloudways_app_backups_upgrade' );