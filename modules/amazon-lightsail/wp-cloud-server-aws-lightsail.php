<?php

/**
 * 
 * Plugin Name:       WP Cloud Server - AWS Lightsail Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the AWS Lightsail Module to the WP Cloud Server plugin.
 * Version:           1.1.0
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - AWS Lightsail Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - AWS Lightsail Module, is distributed in the hope that it will be useful,
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

if ( !class_exists( 'WP_Cloud_Server_AWS_Lightsail' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.0.0
     */
    final class WP_Cloud_Server_AWS_Lightsail
    {
        /**
         * @var WP_Cloud_Server_AWS_Lightsail
         * @since 1.0.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_AWS_Lightsail_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_AWS_Lightsail_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_AWS_Lightsail_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_AWS_Lightsail_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_AWS_Lightsail_Activator
         * @since 1.3.0
         */
        public  $activator ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_AWS_Lightsail_License
         * @since 1.3.0
         */
        public  $license ;
        /**
         * Main WP_Cloud_Server_AWS_Lightsail Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_AWS_Lightsail exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_AWS_Lightsail()
         * @return object|WP_Cloud_Server_AWS_Lightsail
         */
        public static function instance()
        {
            
            if ( !isset( self::$instance ) && !self::$instance instanceof WP_Cloud_Server_AWS_Lightsail ) {
                self::$instance             = new WP_Cloud_Server_AWS_Lightsail();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin      = new WP_Cloud_Server_AWS_Lightsail_Admin();
                self::$instance->api        = new WP_Cloud_Server_AWS_Lightsail_API();
                self::$instance->settings   = new WP_Cloud_Server_AWS_Lightsail_Settings();
                self::$instance->cart       = new WP_Cloud_Server_AWS_Lightsail_Cart_EDD();
                self::$instance->activator  = new WP_Cloud_Server_AWS_Lightsail_Activator();
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
            if ( !defined( 'WPCS_AWS_LIGHTSAIL_NAME' ) ) {
                define( 'WPCS_AWS_LIGHTSAIL_NAME', 'WP Cloud Server - AWS Lightsail Module' );
            }
            // Plugin version.
            if ( !defined( 'WPCS_AWS_LIGHTSAIL_VERSION' ) ) {
                define( 'WPCS_AWS_LIGHTSAIL_VERSION', '1.1.0' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_AWS_LIGHTSAIL_PLUGIN_DIR' ) ) {
                define( 'WPCS_AWS_LIGHTSAIL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_AWS_LIGHTSAIL_PLUGIN_URL' ) ) {
                define( 'WPCS_AWS_LIGHTSAIL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_AWS_LIGHTSAIL_PLUGIN_FILE' ) ) {
                define( 'WPCS_AWS_LIGHTSAIL_PLUGIN_FILE', __FILE__ );
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
            // Load the AWS_Lightsail API Configuration - Unique to the AWS_Lightsail API           
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-aws-lightsail-api.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/api/wp-cloud-server-aws-lightsail-api-abstraction.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-aws-lightsail-cloud-init.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-aws-lightsail-api-queue.php';

			// Load the License Handler functionality
			//require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/license/class-wp-cloud-server-aws-lightsail-license-handler.php';
            //require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/license/wp-cloud-server-aws-lightsail-license-form.php';
            
            // Load Functions
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/functions/wp-cloud-server-aws-lightsail-functions.php';

            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/class-wp-cloud-server-aws-lightsail-activate.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/class-wp-cloud-server-aws-lightsail-deactivate.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/class-wp-cloud-server-aws-lightsail-tools.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/class-wp-cloud-server-aws-lightsail-edd.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-aws-lightsail-woocommerce.php';

            // Load includes required for the admin dashboard
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-aws-lightsail-admin.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-aws-lightsail-settings.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-aws-lightsail-server.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-aws-lightsail-template.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-aws-lightsail-clients.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/settings/aws-lightsail-template-settings.php';
            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-aws-lightsail-template.php';
			require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/config/config.php';

            require_once WPCS_AWS_LIGHTSAIL_PLUGIN_DIR . 'includes/background/aws-lightsail-queue.php';
        }
    }
}
// End if class_exists check.
/**
 * The main function for that returns WP_Cloud_Server_AWS_Lightsail
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_AWS_Lightsail class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_AWS_Lightsail
 */
function WPCS_AWS_Lightsail()
{
    return WP_Cloud_Server_AWS_Lightsail::instance();
}

// Get WPCS_AWS_Lightsail Running.
WPCS_AWS_Lightsail();