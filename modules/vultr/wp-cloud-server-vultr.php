<?php

/**
 * 
 * Plugin Name:       WP Cloud Server - Vultr Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the Vultr Module to the WP Cloud Server plugin.
 * Version:           1.1.0
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - Vultr Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - Vultr Module, is distributed in the hope that it will be useful,
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

if ( !class_exists( 'WP_Cloud_Server_Vultr' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.3.0
     */
    class WP_Cloud_Server_Vultr
    {
        /**
         * @var WP_Cloud_Server_Vultr
         * @since 1.3.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Vultr_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Vultr_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_Vultr_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Vultr_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Vultr_Activator
         * @since 1.3.0
         */
        public  $activator ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Vultr_License
         * @since 1.3.0
         */
        public  $license ;
        /**
         * Main WP_Cloud_Server_Vultr Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_Vultr exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_Vultr()
         * @return object|WP_Cloud_Server_Vultr
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance				= new WP_Cloud_Server_Vultr();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin		= new WP_Cloud_Server_Vultr_Admin();
                self::$instance->api		= new WP_Cloud_Server_Vultr_API();
                self::$instance->settings	= new WP_Cloud_Server_Vultr_Settings();
                self::$instance->cart		= new WP_Cloud_Server_Vultr_Cart_EDD();
                self::$instance->activator	= new WP_Cloud_Server_Vultr_Activator();
				
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
            if ( !defined( 'WPCS_VULTR_NAME' ) ) {
                define( 'WPCS_VULTR_NAME', 'WP Cloud Server - Vultr Module' );
            }
            // Plugin version.
            if ( !defined( 'WPCS_VULTR_VERSION' ) ) {
                define( 'WPCS_VULTR_VERSION', '1.1.0' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_VULTR_PLUGIN_DIR' ) ) {
                define( 'WPCS_VULTR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_VULTR_PLUGIN_URL' ) ) {
                define( 'WPCS_VULTR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_VULTR_PLUGIN_FILE' ) ) {
                define( 'WPCS_VULTR_PLUGIN_FILE', __FILE__ );
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
            // Load the Vultr API Configuration - Unique to the Vultr API
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-vultr-api.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/api/wp-cloud-server-vultr-api-abstraction.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-vultr-cloud-init.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-vultr-api-queue.php';
			
            // Load Functions
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/functions/wp-cloud-server-vultr-functions.php';
			
            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/class-wp-cloud-server-vultr-activate.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/class-wp-cloud-server-vultr-deactivate.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/class-wp-cloud-server-vultr-tools.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/class-wp-cloud-server-vultr-edd.php';
			require_once WPCS_VULTR_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-vultr-woocommerce.php';
			
            // Load includes required for the admin dashboard
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-vultr-admin.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-vultr-settings.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-vultr-server.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-vultr-template.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-vultr-template.php';
            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-vultr-clients.php';
			require_once WPCS_VULTR_PLUGIN_DIR . 'includes/admin/settings/template-settings.php';
			require_once WPCS_VULTR_PLUGIN_DIR . 'includes/config/config.php';

            require_once WPCS_VULTR_PLUGIN_DIR . 'includes/background/vultr-queue.php';
        }
    }
}
// End if class_exists check.
/**
 * The main function for that returns WP_Cloud_Server_Vultr
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_Vultr class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_Vultr
 */
function WPCS_Vultr()
{
	if ( ! get_option( 'wpcs_plugin_active' ) ) {
		return;
	}
	
    return WP_Cloud_Server_Vultr::instance();
}

// Get WPCS_Vultr Running.
WPCS_Vultr();