<?php

/**
 * 
 * Plugin Name:       WP Cloud Server - UpCloud Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the UpCloud Module to the WP Cloud Server plugin.
 * Version:           1.1.0
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - UpCloud Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - UpCloud Module, is distributed in the hope that it will be useful,
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

if ( !class_exists( 'WP_Cloud_Server_UpCloud' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.3.0
     */
    class WP_Cloud_Server_UpCloud
    {
        /**
         * @var WP_Cloud_Server_UpCloud
         * @since 1.3.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_UpCloud_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_UpCloud_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_UpCloud_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_UpCloud_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_UpCloud_Activator
         * @since 1.3.0
         */
        public  $activator ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_UpCloud_License
         * @since 1.3.0
         */
        public  $license ;
        /**
         * Main WP_Cloud_Server_UpCloud Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_UpCloud exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_UpCloud()
         * @return object|WP_Cloud_Server_UpCloud
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance				= new WP_Cloud_Server_UpCloud();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin		= new WP_Cloud_Server_UpCloud_Admin();
                self::$instance->api		= new WP_Cloud_Server_UpCloud_API();
                self::$instance->settings	= new WP_Cloud_Server_UpCloud_Settings();
                self::$instance->cart		= new WP_Cloud_Server_UpCloud_Cart_EDD();
                self::$instance->activator	= new WP_Cloud_Server_UpCloud_Activator();
				
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
            if ( !defined( 'WPCS_UPCLOUD_NAME' ) ) {
                define( 'WPCS_UPCLOUD_NAME', 'WP Cloud Server - UpCloud Module' );
            }
            // Plugin version.
            if ( !defined( 'WPCS_UPCLOUD_VERSION' ) ) {
                define( 'WPCS_UPCLOUD_VERSION', '1.1.0' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_UPCLOUD_PLUGIN_DIR' ) ) {
                define( 'WPCS_UPCLOUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_UPCLOUD_PLUGIN_URL' ) ) {
                define( 'WPCS_UPCLOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_UPCLOUD_PLUGIN_FILE' ) ) {
                define( 'WPCS_UPCLOUD_PLUGIN_FILE', __FILE__ );
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
            // Load the UpCloud API Configuration - Unique to the UpCloud API
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-upcloud-api.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/api/wp-cloud-server-upcloud-api-abstraction.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-upcloud-cloud-init.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-upcloud-api-queue.php';
			
            // Load Functions
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/functions/wp-cloud-server-upcloud-functions.php';
			
            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-upcloud-activate.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-upcloud-deactivate.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-upcloud-tools.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/class-wp-cloud-server-upcloud-edd.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-upcloud-woocommerce.php';
			
            // Load includes required for the admin dashboard
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-upcloud-admin.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-upcloud-settings.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-upcloud-server.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-upcloud-template.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-upcloud-clients.php';

            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/settings/upcloud-template-settings.php';
            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-upcloud-template.php';
			require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/config/config.php';

            require_once WPCS_UPCLOUD_PLUGIN_DIR . 'includes/background/upcloud-queue.php';
        }
    }
}
// End if class_exists check.
/**
 * The main function for that returns WP_Cloud_Server_UpCloud
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_UpCloud class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_UpCloud
 */
function WPCS_UpCloud()
{
	if ( ! get_option( 'wpcs_plugin_active' ) ) {
		return;
	}
	
    return WP_Cloud_Server_UpCloud::instance();
}

// Get WPCS_UpCloud Running.
WPCS_UpCloud();