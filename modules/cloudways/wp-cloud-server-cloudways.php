<?php
/**
 * 
 * Plugin Name:       WP Cloud Server - Cloudways Module
 * Plugin URI:        https://designedforpixels.com/wp-cloud-server/
 * Description:       Adds the Cloudways Module to the WP Cloud Server plugin.
 * Version:           1.1.0
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       /languages
 *
 * WP Cloud Server - Cloudways Module, is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server - Cloudways Module, is distributed in the hope that it will be useful,
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

if ( !class_exists( 'WP_Cloud_Server_Cloudways' ) ) {
    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.3.0
     */
    class WP_Cloud_Server_Cloudways
    {
        /**
         * @var WP_Cloud_Server_Cloudways
         * @since 1.3.0
         */
        private static  $instance ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Cloudways_Admin
         * @since 1.3.0
         */
        public  $admin ;
        /**
         * WPCS Admin Object.
         *
         * @var object|WPCS_Cloudways_API
         * @since 1.3.0
         */
        public  $api ;
        /**
         * WPCS Settings Object.
         *
         * @var object|WPCS_Cloudways_Settings
         * @since 1.3.0
         */
        public  $settings ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Cloudways_Cart
         * @since 1.3.0
         */
        public  $cart ;
        /**
         * WPCS Cart Object.
         *
         * @var object|WPCS_Cloudways_Activator
         * @since 1.3.0
         */
        public  $activator ;

        /**
         * Main WP_Cloud_Server_Cloudways Instance.
         *
         * Insures that only one instance of WP_Cloud_Server_Cloudways exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.3.0
         * 
         * @see WPCS_Cloudways()
         * @return object|WP_Cloud_Server_Cloudways
         */
        public static function instance()
        {
            if ( ! self::$instance ) {
                self::$instance				= new WP_Cloud_Server_Cloudways();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->admin		= new WP_Cloud_Server_Cloudways_Admin();
                self::$instance->api		= new WP_Cloud_Server_Cloudways_API();
                self::$instance->settings	= new WP_Cloud_Server_Cloudways_Settings();
                self::$instance->cart		= new WP_Cloud_Server_Cloudways_Cart_EDD();
                self::$instance->activator	= new WP_Cloud_Server_Cloudways_Activator();
				
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
            if ( !defined( 'WPCS_CLOUDWAYS_NAME' ) ) {
                define( 'WPCS_CLOUDWAYS_NAME', 'WP Cloud Server - Cloudways Module' );
            }
            // Plugin version.
            if ( !defined( 'WPCS_CLOUDWAYS_VERSION' ) ) {
                define( 'WPCS_CLOUDWAYS_VERSION', '1.1.0' );
            }
            // Plugin Folder Path.
            if ( !defined( 'WPCS_CLOUDWAYS_PLUGIN_DIR' ) ) {
                define( 'WPCS_CLOUDWAYS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin Folder URL.
            if ( !defined( 'WPCS_CLOUDWAYS_PLUGIN_URL' ) ) {
                define( 'WPCS_CLOUDWAYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }
            // Plugin Root File.
            if ( !defined( 'WPCS_CLOUDWAYS_PLUGIN_FILE' ) ) {
                define( 'WPCS_CLOUDWAYS_PLUGIN_FILE', __FILE__ );
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
            // Load the Cloudways API Configuration - Unique to the Cloudways API
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-cloudways-api.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/api/wp-cloud-server-cloudways-api-abstraction.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-cloudways-cloud-init.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/api/scheduled-queues/wp-cloud-server-cloudways-api-queue.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/api/scheduled-queues/wp-cloud-server-create-app-queue.php';
						
            // Load Functions
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/functions/ajax-functions.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/functions/wp-cloud-server-cloudways-functions.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-cloudways-app.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-cloudways-server.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-cloudways-template.php';
            
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/settings/cloudways-template-settings.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-create-cloudways-template.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-cloudways-template.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-cloudways-clients.php';
			
            // Load includes for the frontend e.g. shortcodes, etc
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/class-wp-cloud-server-cloudways-activate.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/class-wp-cloud-server-cloudways-deactivate.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/class-wp-cloud-server-cloudways-tools.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/class-wp-cloud-server-cloudways-edd.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/shortcodes/shortcodes.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/forms/admin-post-create-app.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-cloudways-woocommerce.php';
			
            // Load includes required for the admin dashboard
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-cloudways-admin.php';
            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-cloudways-settings.php';
			require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/config/config.php';

            require_once WPCS_CLOUDWAYS_PLUGIN_DIR . 'includes/background/cloudways-queue.php';
        }
    }
}
// End if class_exists check.
/**
 * The main function for that returns WP_Cloud_Server_Cloudways
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_Cloudways class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_Cloudways
 */
function WPCS_Cloudways()
{
	if ( ! get_option( 'wpcs_plugin_active' ) ) {
		return;
	}
	
    return WP_Cloud_Server_Cloudways::instance();
}

// Get WPCS_Cloudways Running.
WPCS_Cloudways();