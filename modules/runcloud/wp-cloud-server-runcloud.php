<?php

/**
 * 
 * Plugin Name:       WP Cloud Server - RunCloud Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the RunCloud Module to the WP Cloud Server plugin.
 * Version:           1.0.1
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - RunCloud Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - RunCloud Module, is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Cloud Server. If not, see <http://www.gnu.org/licenses/>.
 *
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WP_Cloud_Server_RunCloud' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.3.0
     */
    class WP_Cloud_Server_RunCloud
    {
        /**
         * @var WP_Cloud_Server_RunCloud
         * @since 1.3.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_RunCloud_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_RunCloud_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_RunCloud_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_RunCloud_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_RunCloud_Activator
         * @since 1.3.0
         */
        public  $activator ;

        /**
         * Main WP_Cloud_Server_RunCloud Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_RunCloud exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_RunCloud()
         * @return object|WP_Cloud_Server_RunCloud
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance				= new WP_Cloud_Server_RunCloud();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin		= new WP_Cloud_Server_RunCloud_Admin();
                self::$instance->api		= new WP_Cloud_Server_RunCloud_API();
                self::$instance->settings	= new WP_Cloud_Server_RunCloud_Settings();
                self::$instance->cart		= new WP_Cloud_Server_RunCloud_Cart_EDD();
                self::$instance->activator	= new WP_Cloud_Server_RunCloud_Activator();
				
				self::$instance->activator->activate();
            }
			
            
            return self::$instance;
        }
        
        /**
         * Setup plugin constants.
         *
         * @since 1.3.0
         * @return void
         */
        private function setup_constants()
        {
            // Plugin name.
            if ( !defined( 'WPCS_RUNCLOUD_NAME' ) ) {
                define( 'WPCS_RUNCLOUD_NAME', 'WP Cloud Server - RunCloud Module' );
            }
            // Plugin version.
            if ( !defined( 'WPCS_RUNCLOUD_VERSION' ) ) {
                define( 'WPCS_RUNCLOUD_VERSION', '1.0.1' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_RUNCLOUD_PLUGIN_DIR' ) ) {
                define( 'WPCS_RUNCLOUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_RUNCLOUD_PLUGIN_URL' ) ) {
                define( 'WPCS_RUNCLOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_RUNCLOUD_PLUGIN_FILE' ) ) {
                define( 'WPCS_RUNCLOUD_PLUGIN_FILE', __FILE__ );
            }
        }
        
        /**
         * Include required files.
         *
         * @since 1.3.0
         * @return void
         */
        private function includes()
        {
            // Load the RunCloud API Configuration - Unique to the RunCloud API
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-runcloud-api.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/api/wp-cloud-server-runcloud-api-abstraction.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-runcloud-cloud-init.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-runcloud-api-queue.php';
			
            // Load Functions
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/functions/wp-cloud-server-runcloud-functions.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-runcloud-server-template.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-runcloud-server.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-runcloud-template.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-runcloud-web-app.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-runcloud-clients.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/settings/runcloud-template-settings.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-runcloud-template.php';
			
            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-runcloud-activate.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-runcloud-deactivate.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-runcloud-tools.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-runcloud-edd.php';
			
            // Load includes required for the admin dashboard
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-runcloud-admin.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-runcloud-settings.php';
            require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/config/config.php';
            
			require_once WPCS_RUNCLOUD_PLUGIN_DIR . 'includes/background/example-plugin.php';
        }
    }
}
// End if class_exists check.

/**
 * The main function for that returns WP_Cloud_Server_RunCloud
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_RunCloud class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_RunCloud
 */
function WPCS_RunCloud()
{
	if ( ! get_option( 'wpcs_plugin_active' ) ) {
		return;
	}
	
    return WP_Cloud_Server_RunCloud::instance();
}

// Get WPCS_RunCloud Running.
WPCS_RunCloud();