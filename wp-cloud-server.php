<?php
/**
 * 
 * Plugin Name:       WP Cloud Server
 * Plugin URI:        https://wpcloudserver.dev/
 * Description:       Create a Powerful Cloud Server Platform from within WordPress.
 * Version:           3.0.8
 * Author:            DesignedforPixels
 * Author URI:        https://designedforpixels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cloud-server
 * Domain Path:       languages
 *
 * WP Cloud Server is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Cloud Server is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Cloud Server. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Cloud_Server' ) ) {

    /**
     * Main WP Cloud Server Class.
     *
     * @since 1.0.0
     */
    final class WP_Cloud_Server {

	    /**
	     * @var WP_Cloud_Server The only version of WP_Cloud_Server
	     * @since 1.0.0
	     */
	    private static $instance;

	    /**
	     * WPCS Admin Object.
	     *
	     * @var object|WPCS_Admin
	     * @since 1.0.0
	     */
        public $admin;

	    /**
	     * WPCS Settings Object.
	     *
	     * @var object|WPCS_Settings
	     * @since 1.0.0
	     */
        public $settings;
    
	    /**
	     * WPCS Shortcodes Object.
	     *
	     * @var object|WPCS_Shortcodes
	     * @since 1.0.0
	     */
        public $shortcodes;

	    /**
	     * WPCS Cart Object.
	     *
	     * @var object|WPCS_Cart
	     * @since 1.0.0
	     */
        public $cart;
    
        /**
	     * Main WP Cloud Server Instance.
	     *
	     * Insures that only one instance of WP Cloud Server exists in memory at any one
	     * time. Also prevents needing to define globals all over the place.
	     *
	     * @since 1.0.0
         * 
	     * @see WPCS()
	     * @return object|WP_Cloud_Server
	     */
	    public static function instance() {

		    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Cloud_Server ) ) {

				self::$instance = new WP_Cloud_Server;
			    self::$instance->setup_constants();
			    self::$instance->includes();				
                self::$instance->settings	= new WP_Cloud_Server_Settings();
                self::$instance->cart		= new WP_Cloud_Server_Cart_EDD();
				self::$instance->setup		= new WP_Cloud_Server_Setup_Wizard();
				self::$instance->dashboard	= new WP_Cloud_Server_Dashboard();
				self::$instance->shortcodes	= new WP_Cloud_Server_ShortCodes();
	
            }
            
		    return self::$instance;
        }
    
        /**
	     * Setup plugin constants.
	     *
	     * @since 1.0.0
	     * @return void
	     */
	    private function setup_constants() {

		    // Plugin name.
		    if ( ! defined( 'WPCS_NAME' ) ) {
			    define( 'WPCS_NAME', 'WP Cloud Server' );
		    }

		    // Plugin version.
		    if ( ! defined( 'WPCS_VERSION' ) ) {
			    define( 'WPCS_VERSION', '3.0.8' );
		    }

		    // Plugin Folder Path.
		    if ( ! defined( 'WPCS_PLUGIN_DIR' ) ) {
			    define( 'WPCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		    }

		    // Plugin Folder URL.
		    if ( ! defined( 'WPCS_PLUGIN_URL' ) ) {
			    define( 'WPCS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		    }

		    // Plugin Root File.
		    if ( ! defined( 'WPCS_PLUGIN_FILE' ) ) {
			    define( 'WPCS_PLUGIN_FILE', __FILE__ );
		    }

	    }

	    /**
	     * Include required files.
	     *
	     * @since 1.0.0
	     * @return void
	     */
	    private function includes() {

			if ( file_exists( WPCS_PLUGIN_DIR . 'includes/deprecated-functions.php' ) ) {
				require_once WPCS_PLUGIN_DIR . 'includes/deprecated-functions.php';
			}

			require_once WPCS_PLUGIN_DIR . 'includes/functions/functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/functions/github-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/functions/ssh-key-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/functions/host-name-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/functions/startup-script-functions.php';

			require_once WPCS_PLUGIN_DIR . 'includes/functions/shortcodes.php';
			require_once WPCS_PLUGIN_DIR . 'includes/enqueue-frontend-css.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/enqueue-admin-scripts.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/functions/admin-notices-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/functions/ajax-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/functions/misc-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/functions/wp-cron-functions.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/functions/settings-functions.php';

			require_once WPCS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-managed-template.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-modules.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-hide-modules.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-shortcodes-server-config.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-shortcodes-website-config.php';

			require_once WPCS_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-settings.php';
			
			require_once WPCS_PLUGIN_DIR . 'includes/woocommerce/functions/wp-cloud-server-woocommerce-functions.php';			
			require_once WPCS_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-woocommerce.php';
			require_once WPCS_PLUGIN_DIR . 'includes/edd/class-wp-cloud-server-edd.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/scheduled-queues/server-completed-queue.php';

			// Set up the Background Processing
			require_once WPCS_PLUGIN_DIR . 'includes/background/class-wp-cloud-server-async-request.php';
			require_once WPCS_PLUGIN_DIR . 'includes/background/class-wp-cloud-server-background-process.php';

			require_once WPCS_PLUGIN_DIR . 'includes/admin/setup-wizard/admin-post/admin-post-setup-wizard-settings.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/setup-wizard/class-wp-cloud-server-setup-wizard.php';

			require_once WPCS_PLUGIN_DIR . 'includes/admin/dashboard/config/config.php';
			require_once WPCS_PLUGIN_DIR . 'includes/admin/dashboard/class-wp-cloud-server-dashboard.php';

			// Load the Modules
			require_once WPCS_PLUGIN_DIR . 'modules/serverpilot/wp-cloud-server-serverpilot.php';
			require_once WPCS_PLUGIN_DIR . 'modules/digitalocean/wp-cloud-server-digitalocean.php';
			require_once WPCS_PLUGIN_DIR . 'modules/vultr/wp-cloud-server-vultr.php';
			require_once WPCS_PLUGIN_DIR . 'modules/linode/wp-cloud-server-linode.php';
			require_once WPCS_PLUGIN_DIR . 'modules/upcloud/wp-cloud-server-upcloud.php';
			require_once WPCS_PLUGIN_DIR . 'modules/runcloud/wp-cloud-server-runcloud.php';
			require_once WPCS_PLUGIN_DIR . 'modules/cloudways/wp-cloud-server-cloudways.php';
			require_once WPCS_PLUGIN_DIR . 'modules/amazon-lightsail/wp-cloud-server-aws-lightsail.php';
			require_once WPCS_PLUGIN_DIR . 'modules/ploi/wp-cloud-server-ploi.php';
			
			require_once WPCS_PLUGIN_DIR . 'includes/install.php';
			
        }

        /**
	     * Load the plugin text domain for translation.
	     *
	     * @since 1.0.0
	     */
	    private function load_plugin_textdomain() {

		    load_plugin_textdomain(
			    'wp-cloud-server',
			    false,
			    WPCS_PLUGIN_DIR . '/languages/'
		    );

	    }
    }
} // End if class_exists check.

/**
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server class
 *
 * @return object|WP_Cloud_Server
 */
function WPCS() {
	update_option( 'wpcs_plugin_active', true );
	return WP_Cloud_Server::instance();
}

// Get WPCS Running.
WPCS();