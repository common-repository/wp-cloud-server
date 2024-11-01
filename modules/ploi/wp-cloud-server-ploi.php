<?php

/**
 * 
 * Plugin Name:       WP Cloud Server - Ploi Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the Ploi Module to the WP Cloud Server plugin.
 * Version:           1.1.0
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - Ploi Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - Ploi Module, is distributed in the hope that it will be useful,
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

if ( !class_exists( 'WP_Cloud_Server_Ploi' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.3.0
     */
    class WP_Cloud_Server_Ploi
    {
        /**
         * @var WP_Cloud_Server_Ploi
         * @since 1.3.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Ploi_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Ploi_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_Ploi_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_Ploi_API_Settings
         * @since 1.3.0
         */
        public  $api_settings ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_Ploi_App_Settings
         * @since 1.3.0
         */
        public  $app_settings ;
        /**
         * WPCS Server Settings Object.
         *
         * @var object|WPCS_Ploi_Server_Settings
         * @since 1.3.0
         */
        public  $server_settings ;
        /**
         * WPCS Server Settings Object.
         *
         * @var object|WPCS_Ploi_Server_Template_Settings
         * @since 1.3.0
         */
        public  $server_template_settings ;
        /**
         * WPCS Server Settings Object.
         *
         * @var object|WPCS_Ploi_App_Template_Settings
         * @since 1.3.0
         */
        public  $app_template_settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Ploi_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Ploi_Activator
         * @since 1.3.0
         */
        public  $activator ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Ploi_License
         * @since 1.3.0
         */
        public  $license ;
        /**
         * Main WP_Cloud_Server_Ploi Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_Ploi exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_Ploi()
         * @return object|WP_Cloud_Server_Ploi
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance				                = new WP_Cloud_Server_Ploi();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin		                = new WP_Cloud_Server_Ploi_Admin();
                self::$instance->api                        = new WP_Cloud_Server_Ploi_API();
                self::$instance->api_settings               = new WP_Cloud_Server_Ploi_API_Settings();
                self::$instance->app_settings               = new WP_Cloud_Server_Ploi_App_Settings();
                self::$instance->server_settings	        = new WP_Cloud_Server_Ploi_Server_Settings();
                self::$instance->server_template_settings	= new WP_Cloud_Server_Ploi_Server_Template_Settings();
                self::$instance->app_template_settings      = new WP_Cloud_Server_Ploi_App_Template_Settings();
                self::$instance->settings	                = new WP_Cloud_Server_Ploi_Settings();
                self::$instance->cart		                = new WP_Cloud_Server_Ploi_Cart_EDD();
                self::$instance->activator                  = new WP_Cloud_Server_Ploi_Activator();
				
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
            if ( !defined( 'WPCS_PLOI_NAME' ) ) {
                define( 'WPCS_PLOI_NAME', 'WP Cloud Server - Ploi Module' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_PLOI_PLUGIN_DIR' ) ) {
                define( 'WPCS_PLOI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_PLOI_PLUGIN_URL' ) ) {
                define( 'WPCS_PLOI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_PLOI_PLUGIN_FILE' ) ) {
                define( 'WPCS_PLOI_PLUGIN_FILE', __FILE__ );
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
            // Load the Ploi API Configuration - Unique to the Ploi API
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/api/wp-cloud-server-ploi-api-definition.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-ploi-api.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/api/wp-cloud-server-ploi-api-abstraction.php';
			
            // Load Functions
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/functions/wp-cloud-server-ploi-functions.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/functions/wp-cloud-server-ploi-ajax-functions.php';

            // Admin Post
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-ploi-server-template.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-ploi-site-template.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-ploi-server.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-ploi-server-template.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-ploi-site-template.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-ploi-web-app.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-ploi-clients.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-ploi-server-template.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-ploi-site-template.php';

            // Settings
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-api-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-app-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-app-template-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-server-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/class-wp-cloud-server-ploi-server-template-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/ploi-server-template-settings.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/settings/ploi-site-template-settings.php';
			
            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/class-wp-cloud-server-ploi-activate.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/class-wp-cloud-server-ploi-deactivate.php';

            // Set-up the eCommerce functions
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/cart/edd/class-wp-cloud-server-ploi-edd.php';
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/cart/woocommerce/wp-cloud-server-ploi-woocommerce.php';
			
            // Load includes required for the admin dashboard
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-ploi-admin.php';
			require_once WPCS_PLOI_PLUGIN_DIR . 'includes/config/config.php';

            // Configure the Background Queue Process
            require_once WPCS_PLOI_PLUGIN_DIR . 'includes/background/ploi-background-process.php';
        }
    }
}
// End if class_exists check.

/**
 * The main function for that returns WP_Cloud_Server_Ploi
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_Ploi class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_Ploi
 */
function WPCS_Ploi()
{
    return WP_Cloud_Server_Ploi::instance();
}

// Get WPCS_Ploi Running.
WPCS_Ploi();