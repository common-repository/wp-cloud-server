<?php
/*
 *
 * WP Cloud Server Config File
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Set-up Variables
 */

function wpcs_setup_config( $modules, $module_name, $status ) {

$kses_exceptions = array(
	'a'      => array(
		'href' => array(),
	),
	'strong' => array(),
	'br'     => array(),
	'span'   => array(),
);

/*
 * Configure Social Icons for the Footer
 */

$args['share_icons'][] = array(
	'url'   => '//twitter.com/wpcloudserver',
	'title' => esc_html__( 'Follow us on Twitter', 'wp-cloud-server' ),
	'icon'  => 'twitter',
);
$args['share_icons'][] = array(
	'url'   => '//www.facebook.com/wpcloudserver',
	'title' => esc_html__( 'Like us on Facebook', 'wp-cloud-server' ),
	'icon'  => 'facebook',
);
$args['share_icons'][] = array(
	'url'   => '//instagram.com/wpcloudserver',
	'title' => esc_html__( 'Follow us on Instagram', 'wp-cloud-server' ),
	'icon'  => 'instagram',
);

/*
 * Add Footer Content
 */
$version = WPCS_VERSION;
$args['footer_left'] = esc_html__( "WP Cloud Server - Version {$version}", 'wp-cloud-server' );
update_option( 'wpcs_arguments', $args );

/*
 * Module Status
 */
	
if ( ( '' !== $module_name ) && ( '' !== $status ) ) {
	$modules[$module_name]['status'] = $status;
	update_option( 'wpcs_module_list', $modules );
}	
	
$serverpilot_active		= ( isset($modules['ServerPilot']['status']) && ( 'active' == $modules['ServerPilot']['status'] ));
$digitalocean_active	= ( isset($modules['DigitalOcean']['status']) && ( 'active' == $modules['DigitalOcean']['status'] ));
$cloudways_active		= ( isset($modules['Cloudways']['status']) && ( 'active' == $modules['Cloudways']['status'] ) );
$module_config			= get_option( 'wpcs_module_config' );
$display_support_menu	= ( get_option( 'wpcs_menu_display_support_menu', 1 ) )? 'true': 'false';
$ecommerce				= wpcs_cart_active();

$api_status				= wpcs_check_cloud_provider_api( null, null, true, $modules);
$cloud_active			= wpcs_check_cloud_provider_module();

/* 
 *	Menu Configuration
 */

/* ------------------------------------------------------------------------------------------------- */

/* WP Cloud Server Menu */
	
$sub_menu = array(
	'menu_type'			=> '',
	'icon_url'			=> 'dashicons-admin-generic',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'Module Overview',
	'menu_title'		=> 'WP Cloud Server',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-admin-menu',
	'function'			=> 'wpcs_control_panel_output',
	'default_page'		=> '2',
);

$sub_menu['submenus'][] = array(
	'menu_type'			=> 'submenu',
	'icon_url'			=> '',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'Module Overview',
	'menu_title'		=> 'Module Overview',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-admin-menu',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);
	
$sub_menu['submenus'][] = array(
	'menu_type'			=> 'submenu',
	'icon_url'			=> '',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'WP Cloud Server',
	'menu_title'		=> 'General Settings',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-general-settings',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);
	
$sub_menu['submenus'][] = array(
	'menu_type'			=> 'submenu',
	'icon_url'			=> '',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'WP Cloud Server',
	'menu_title'		=> 'Add-on Modules',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-extensions',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);

$sub_menu['content']['module-overview'] = array(
	'id'       			=> 'module-overview',
	'module'			=> '',
	'active'			=> 'true',
	'position'			=> '2',
	'template'			=> 'module-overview',
	'template_path'		=> 'includes/admin/dashboard/config/templates',	
	'menu_header'		=> 'Module Overview',
	'menu_divider'		=> 'yes',
	'menu_item'			=> 'Installed Modules',
	'section_width'		=> 'medium',
	'type'     			=> 'text',
	'title'    			=> esc_html__( 'Installed Modules', 'wp-cloud-server' ),
	'subtitle'			=> '',
	'desc'     			=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;

// Read sub-menus for add-on modules
if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
			$sub_menu['content'][] = $module_config[$sub_menu['menu_slug']][$module['module_name']];
	}
}
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;

$sub_menu['content'][] = array(
	'id'       		=> 'managed-hosting',
	'module'		=> '',
	'active'		=> $display_support_menu,
	'position'		=> '2',
	'template'		=> 'guide-module-overview',
	'template_path'	=> 'includes/admin/dashboard/config/templates/guides',	
	'menu_header'	=> 'Support',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Quick Guide',
	'section_width'	=> 'xsmall',	
	'type'     		=> 'text',
	'title'    		=> esc_html__( 'Module Overview', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;

/* ------------------------------------------------------------------------------------------------- */

/* Managed Hosting Menu */

if ( $cloudways_active ) {

$sub_menu = array(
	'menu_type'			=> 'menu',
	'icon_url'			=> 'dashicons-admin-site-alt3',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-managed-hosting',
	'page_title'		=> 'Cloudways',
	'menu_title'		=> 'Managed Hosting',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-managed-hosting',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> '58.4',
	'default_page'		=> '2',
);
	
$sub_menu['submenus'][] = array(
	'menu_type'			=> 'submenu',
	'icon_url'			=> '',
	'active'			=> 'true',
	'parent_slug'		=> 'wp-cloud-server-managed-hosting',
	'page_title'		=> 'WP Cloud Server',
	'menu_title'		=> 'Cloudways',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-managed-hosting',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;
	
$sub_menu['content'][] = array(
	'id'       		=> 'create-managed-hosting-server',
	'module'		=> 'cloudways',
	'active'		=> 'true',
	'position'		=> '11',
	'template'		=> 'create-managed-hosting-server',
	'template_path'	=> 'includes/admin/dashboard/config/templates/managed-hosting',
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> '',
	'section_width'	=> 'medium',	
	'type'     		=> 'text',
	'api_required'	=> array( 'serverpilot'),
	'title'    		=> esc_html__( 'Create Server', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
);
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;
	
if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		if ( ( 'managed_server' == $module['module_type'] ) ) {
			foreach ( $module_config['wp-cloud-server-managed-hosting']['Cloudways'] as $key => $content ) {
				$sub_menu['content'][] = $content;
			}
		}
	}
}
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;
	
$sub_menu['content'][] = array(
	'id'       		=> 'guide-managed-hosting',
	'active'		=> $display_support_menu,
	'position'		=> '2',
	'template'		=> 'guide-managed-hosting',
	'template_path'	=> 'includes/admin/dashboard/config/templates/guides',	
	'menu_header'	=> 'Support',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Quick Guide',
	'section_width'	=> 'xsmall',	
	'type'     		=> 'text',
	'title'    		=> esc_html__( 'Managed Hosting', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;

}
	
/* ------------------------------------------------------------------------------------------------- */
	
/* Managed Servers */
	
if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		if ( ( 'cloud_portal' == $module['module_type'] )  && ( 'active' == $module['status'] ) ) {
			$active_modules[] = $module['module_name'];
		}
	}
}
	
if ( isset( $active_modules ) && is_array( $active_modules ) ) {
	$parent_module = strtolower( str_replace( " ", "-", $active_modules[0]) );
	foreach ( $active_modules as $key => $module ) {
		$module_ref = strtolower( str_replace( " ", "-", $module) );
		$menu_type	= ( 0 == $key ) ? 'menu' : '';
		$title		= ( 0 == $key ) ? 'Managed Servers' : $module;
		$sub_menu	= array(
			'menu_type'			=> $menu_type,
			'icon_url'			=> 'dashicons-admin-settings',
			'active'			=> 'true',
			'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
			'page_title'		=> $module,
			'menu_title'		=> $title,
			'capability'		=> 'manage_options',
			'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
			'function'			=> 'wpcs_control_panel_output',
			'position'			=> '58.3',
			'default_page'		=> '2',
		);
		
		if ( 0 == $key ) {
		
			$sub_menu['submenus'][] = array(
						'menu_type'			=> 'submenu',
						'icon_url'			=> '',
						'active'			=> 'true',
						'parent_slug'		=> "wp-cloud-servers-{$module_ref}",
						'page_title'		=> $module,
						'menu_title'		=> $module,
						'capability'		=> 'manage_options',
						'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
						'function'			=> 'wpcs_control_panel_output',
						'position'			=> null,
						'default_page'		=> '2',
			);
		
			foreach ( $active_modules as $menu_items => $menu_item ) {
				
				if ( $menu_items > 0 ) {
					$module_lc = strtolower( str_replace( " ", "-", $menu_item) );
					$sub_menu['submenus'][] = array(
						'menu_type'			=> 'submenu',
						'icon_url'			=> '',
						'active'			=> 'true',
						'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
						'page_title'		=> $menu_item,
						'menu_title'		=> $menu_item,
						'capability'		=> 'manage_options',
						'menu_slug'			=> "wp-cloud-servers-{$module_lc}",
						'function'			=> 'wpcs_control_panel_output',
						'position'			=> null,
						'default_page'		=> '2',
					);
				}
			}
		}
		$config[ "wp-cloud-servers-{$module_ref}" ] = $sub_menu;
	}
}

if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		$sub_menu = array();
		if ( ( 'cloud_portal' == $module['module_type'] ) ) {
			$module_ref = strtolower( str_replace( " ", "-", $module['module_name']) );
			foreach ( $module_config["wp-cloud-servers-{$module_ref}"][$module['module_name']] as $key => $content ) {
				$sub_menu[] = $content;
			}
			$sub_menu[] = array(
				'id'       		=> 'guide-managed-servers',
				'active'		=> $display_support_menu,
				'position'		=> '2',
				'template'		=> 'guide-managed-servers',
				'template_path'	=> 'includes/admin/dashboard/config/templates/guides',	
				'menu_header'	=> 'Support',
				'menu_divider'	=> 'yes',
				'menu_item'		=> 'Quick Guide',
				'type'     		=> 'text',
				'section_width'	=> 'xsmall',
				'title'    		=> esc_html__( 'Managed Servers', 'wp-cloud-server' ),
				'subtitle' 		=> '',
				'desc'     		=> '',
			);

			$config[ "wp-cloud-servers-{$module_ref}" ]['content'] = $sub_menu;
		}
	}
}

/* ------------------------------------------------------------------------------------------------- */

/* Cloud Server Menu */

if ( $cloud_active ) {
	
	$active_modules = array();
	
	if ( is_array( $modules ) ) {
		foreach ( $modules as $key => $module ) {
			if ( ( 'cloud_provider' == $module['module_type'] )  && ( 'active' == $module['status'] ) ) {
				$active_modules[] = $module['module_name'];
			}
		}
	}
	
	if ( is_array( $active_modules ) ) {
		$parent_module = strtolower( str_replace( " ", "-", $active_modules[0]) );
		foreach ( $active_modules as $key => $module ) {
			$module_ref = strtolower( str_replace( " ", "-", $module) );
			$menu_type	= ( 0 == $key ) ? 'menu' : '';
			$title		= ( 0 == $key ) ? 'Cloud Servers' : $module;
			$sub_menu	= array(
				'menu_type'			=> $menu_type,
				'icon_url'			=> 'dashicons-laptop',
				'active'			=> 'true',
				'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
				'page_title'		=> $module,
				'menu_title'		=> $title,
				'capability'		=> 'manage_options',
				'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
				'function'			=> 'wpcs_control_panel_output',
				'position'			=> '58.2',
				'default_page'		=> '2',
			);
		
			if ( 0 == $key ) {
		
				$sub_menu['submenus'][] = array(
					'menu_type'			=> 'submenu',
					'icon_url'			=> '',
					'active'			=> 'true',
					'parent_slug'		=> "wp-cloud-servers-{$module_ref}",
					'page_title'		=> $module,
					'menu_title'		=> $module,
					'capability'		=> 'manage_options',
					'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
					'function'			=> 'wpcs_control_panel_output',
					'position'			=> null,
					'default_page'		=> '2',
				);
		
				foreach ( $active_modules as $menu_items => $menu_item ) {
				
					if ( $menu_items > 0 ) {
						$module_lc = strtolower( str_replace( " ", "-", $menu_item) );
						$sub_menu['submenus'][] = array(
							'menu_type'			=> 'submenu',
							'icon_url'			=> '',
							'active'			=> 'true',
							'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
							'page_title'		=> $menu_item,
							'menu_title'		=> $menu_item,
							'capability'		=> 'manage_options',
							'menu_slug'			=> "wp-cloud-servers-{$module_lc}",
							'function'			=> 'wpcs_control_panel_output',
							'position'			=> null,
							'default_page'		=> '2',
						);
					}
				}
			}
			$config[ "wp-cloud-servers-{$module_ref}" ] = $sub_menu;
		}
	}
	
if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		$sub_menu = array();
		if ( ( 'cloud_provider' == $module['module_type'] ) ) {
			$module_ref = strtolower( str_replace( " ", "-", $module['module_name']) );
			foreach ( $module_config["wp-cloud-servers-{$module_ref}"][$module['module_name']] as $key => $content ) {
				$sub_menu[] = $content;
			}
			$sub_menu[] = array(
					'id'       		=> 'guide-cloud-servers',
					'active'		=> $display_support_menu,
					'position'		=> '2',
					'template'		=> 'guide-cloud-servers',
					'template_path'	=> 'includes/admin/dashboard/config/templates/guides',	
					'menu_header'	=> 'Support',
					'menu_divider'	=> 'yes',
					'menu_item'		=> 'Quick Guide',
					'type'     		=> 'text',
					'section_width'	=> 'xsmall',
					'title'    		=> esc_html__( 'Cloud Servers', 'wp-cloud-server' ),
					'subtitle' 		=> '',
					'desc'     		=> '',
			);

			$config[ "wp-cloud-servers-{$module_ref}" ]['content'] = $sub_menu;
		}
	}
}
}

/* ------------------------------------------------------------------------------------------------- */

/* Website Services Menu */

$active_modules = array();
	
if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		if ( ( 'service_provider' == $module['module_type'] )  && ( 'active' == $module['status'] ) ) {
			$active_modules[] = $module['module_name'];
		}
	}
}

if ( isset( $active_modules[0] ) ) {
	$parent_module = strtolower( str_replace( " ", "-", $active_modules[0]) );
	foreach ( $active_modules as $key => $module ) {
		$module_ref		= strtolower( str_replace( " ", "-", $module) );
		$plugin_active	= ( is_plugin_active( "wp-cloud-server-{$module_ref}/wp-cloud-server-{$module_ref}.php" ) ) ? 'true' : 'false';
		$menu_type		= ( 0 == $key ) ? 'menu' : '';
		$title			= ( 0 == $key ) ? 'Web Services' : $module;
		$sub_menu		= array(
			'menu_type'			=> $menu_type,
			'icon_url'			=> 'dashicons-chart-bar',
			'active'			=> $plugin_active,
			'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
			'page_title'		=> $module,
			'menu_title'		=> $title,
			'capability'		=> 'manage_options',
			'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
			'function'			=> 'wpcs_control_panel_output',
			'position'			=> '58.5',
			'default_page'		=> '2',
		);
	
		if ( 0 == $key ) {
	
			$sub_menu['submenus'][] = array(
				'menu_type'			=> 'submenu',
				'icon_url'			=> '',
				'active'			=> 'true',
				'parent_slug'		=> "wp-cloud-servers-{$module_ref}",
				'page_title'		=> $module,
				'menu_title'		=> $module,
				'capability'		=> 'manage_options',
				'menu_slug'			=> "wp-cloud-servers-{$module_ref}",
				'function'			=> 'wpcs_control_panel_output',
				'position'			=> null,
				'default_page'		=> '2',
			);
	
			foreach ( $active_modules as $menu_items => $menu_item ) {
			
				if ( $menu_items > 0 ) {
					$module_lc = strtolower( str_replace( " ", "-", $menu_item) );
					$sub_menu['submenus'][] = array(
						'menu_type'			=> 'submenu',
						'icon_url'			=> '',
						'active'			=> 'true',
						'parent_slug'		=> "wp-cloud-servers-{$parent_module}",
						'page_title'		=> $menu_item,
						'menu_title'		=> $menu_item,
						'capability'		=> 'manage_options',
						'menu_slug'			=> "wp-cloud-servers-{$module_lc}",
						'function'			=> 'wpcs_control_panel_output',
						'position'			=> null,
						'default_page'		=> '2',
					);
				}
			}
		}
		$config[ "wp-cloud-servers-{$module_ref}" ] = $sub_menu;
	}
}

if ( is_array( $modules ) ) {
	foreach ( $modules as $key => $module ) {
		$sub_menu = array();
		if ( ( 'service_provider' == $module['module_type'] ) ) {
			$module_ref = strtolower( str_replace( " ", "-", $module['module_name']) );
			foreach ( $module_config["wp-cloud-servers-{$module_ref}"][$module['module_name']] as $key => $content ) {
				$sub_menu[] = $content;
			}
			$sub_menu[] = array(
				'id'       		=> 'guide-cloud-servers',
				'active'		=> $display_support_menu,
				'position'		=> '2',
				'template'		=> 'guide-cloud-servers',
				'template_path'	=> 'includes/admin/dashboard/config/templates/guides',	
				'menu_header'	=> 'Support',
				'menu_divider'	=> 'yes',
				'menu_item'		=> 'Quick Guide',
				'type'     		=> 'text',
				'section_width'	=> 'xsmall',
				'title'    		=> esc_html__( 'Cloud Servers', 'wp-cloud-server' ),
				'subtitle' 		=> '',
				'desc'     		=> '',
			);

			$config[ "wp-cloud-servers-{$module_ref}" ]['content'] = $sub_menu;
		}
	}
}

/* ------------------------------------------------------------------------------------------------- */

/* General Settings Menu */

$sub_menu = array(
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'General Settings',
	'menu_title'		=> 'General Settings',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-general-settings',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);
	
$sub_menu['content'][] = array(
	'id'       		=> 'hide-support-menus',
	'active'		=> 'true',
	'position'		=> '2',
	'template'		=> 'hide-support-menus',
	'template_path'	=> 'includes/admin/dashboard/config/templates/settings',	
	'menu_header'	=> 'General Settings',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Menu Settings',
	'type'     		=> 'text',
	'title'    		=> '',
	'section_width'	=> 'xsmall',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$sub_menu['content'][] = array(
	'id'       		=> 'reset-log-events',
	'active'		=> 'true',
	'position'		=> '2',
	'template'		=> 'reset-log-events',
	'template_path'	=> 'includes/admin/dashboard/config/templates/settings',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Log Event Settings',
	'type'     		=> 'text',
	'title'    		=> '',
	'section_width'	=> 'xsmall',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$sub_menu['content'][] = array(
	'id'       		=> 'enable-debug-mode',
	'active'		=> 'true',
	'position'		=> '3',
	'template'		=> 'enable-debug-mode',
	'template_path'	=> 'includes/admin/dashboard/config/templates/settings',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Debug Settings',
	'type'     		=> 'text',
	'title'    		=> '',
	'section_width'	=> 'xsmall',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;

$sub_menu['content'][] = array(
	'id'       		=> 'uninstall-data',
	'active'		=> 'true',
	'position'		=> '3',
	'template'		=> 'uninstall-data',
	'template_path'	=> 'includes/admin/dashboard/config/templates/settings',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Uninstall Settings',
	'type'     		=> 'text',
	'title'    		=> '',
	'section_width'	=> 'xsmall',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$sub_menu['content'][] = array(
	'id'       		=> 'shortcodes-server-config',
	'active'		=> $ecommerce,
	'position'		=> '7',
	'template'		=> 'shortcodes-server-config',
	'template_path'	=> 'includes/admin/dashboard/config/templates',	
	'menu_header'	=> 'Shortcode Settings',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Display Client Servers',
	'type'     		=> 'text',
	'section_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;

$sub_menu['content'][] = array(
	'id'       		=> 'shortcodes-website-config',
	'active'		=> $ecommerce,
	'position'		=> '7',
	'template'		=> 'shortcodes-website-config',
	'template_path'	=> 'includes/admin/dashboard/config/templates',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Display Client Websites',
	'type'     		=> 'text',
	'section_width'	=> 'xsmall',
	'title'    		=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);

$config[ $sub_menu['menu_slug'] ] = $sub_menu;
	
/* ------------------------------------------------------------------------------------------------- */

/* Add-on Modules Menu */

$sub_menu = array(
	'parent_slug'		=> 'wp-cloud-server-admin-menu',
	'page_title'		=> 'Add-on Modules',
	'menu_title'		=> 'Add-on Modules',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-server-extensions',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);

$sub_menu['content'][] = array(
	'id'       		=> 'category-development',
	'active'		=> 'true',
	'position'		=> '2',
	'template'		=> 'category-development',
	'template_path'	=> 'includes/admin/dashboard/config/templates/extensions',	
	'menu_header'	=> 'Categories',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Development',
	'type'     		=> 'text',
	'title'    		=> 'Development Modules',
	'section_width'	=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;

$sub_menu['content'][] = array(
	'id'       		=> 'category-monitoring',
	'active'		=> 'true',
	'position'		=> '2',
	'template'		=> 'category-monitoring',
	'template_path'	=> 'includes/admin/dashboard/config/templates/extensions',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Monitoring',
	'type'     		=> 'text',
	'title'    		=> 'Monitoring Modules',
	'section_width'	=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;

$sub_menu['content'][] = array(
	'id'       		=> 'category-pro-modules',
	'active'		=> 'true',
	'position'		=> '2',
	'template'		=> 'category-pro-modules',
	'template_path'	=> 'includes/admin/dashboard/config/templates/extensions',	
	'menu_header'	=> '',
	'menu_divider'	=> '',
	'menu_item'		=> 'Pro Modules',
	'type'     		=> 'text',
	'title'    		=> 'Professional Modules',
	'section_width'	=> '',
	'subtitle' 		=> '',
	'desc'     		=> '',
);
	
$config[ $sub_menu['menu_slug'] ] = $sub_menu;
	
/* ------------------------------------------------------------------------------------------------- */

/* DigitalOcean Menu */

$sub_menu = array(
	'parent_slug'		=> 'wp-cloud-server-cloud-servers',
	'page_title'		=> 'DigitalOcean',
	'menu_title'		=> 'DigitalOcean',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-servers-digitalocean',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);

$sub_menu['content'][] = array(
	'id'       		=> 'list-cloud-servers',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'list-cloud-servers',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_active'	=> 'true',
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Droplets',
	'section_width'	=> 'medium',
	'type'     		=> 'text',
	'api_required'	=> array( 'digitalocean' ),
	'title'    		=> esc_html__( 'DigitalOcean Servers', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> wp_kses( __( 'This page lists the Cloud Servers that have been created, and are currently available.', 'wp-cloud-server' ), $kses_exceptions ),
);
	
/* ------------------------------------------------------------------------------------------------- */

/* Linode Menu */

$sub_menu = array(
	'parent_slug'		=> 'wp-cloud-server-cloud-servers',
	'page_title'		=> 'Linode',
	'menu_title'		=> 'Linode',
	'capability'		=> 'manage_options',
	'menu_slug'			=> 'wp-cloud-servers-linode',
	'function'			=> 'wpcs_control_panel_output',
	'position'			=> null,
	'default_page'		=> '2',
);

$sub_menu['content'][] = array(
	'id'       		=> 'list-cloud-servers',
	'module'		=> 'Linode',
	'active'		=> 'true',
	'position'		=> '9',
	'template'		=> 'list-cloud-servers',
	'template_path'	=> 'includes/admin/dashboard/config/templates',
	'menu_active'	=> 'true',
	'menu_header'	=> 'Manage',
	'menu_divider'	=> 'yes',
	'menu_item'		=> 'Linux Servers',
	'section_width'	=> 'medium',
	'type'     		=> 'text',
	'api_required'	=> array( 'linode' ),
	'title'    		=> esc_html__( 'Linode Servers', 'wp-cloud-server' ),
	'subtitle' 		=> '',
	'desc'     		=> wp_kses( __( 'This page lists the Cloud Servers that have been created, and are currently available.', 'wp-cloud-server' ), $kses_exceptions ),
);

/* Update Config Data */
update_option( 'wpcs_config', $config );
	
}
add_action( 'wpcs_update_config', 'wpcs_setup_config', 10, 3 );