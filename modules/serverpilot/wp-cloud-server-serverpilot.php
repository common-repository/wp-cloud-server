<?php

/**
 * The ServerPilot Module Class
 *
 * @author     Gary Jordan <gary@designedforpixels.com>
 * @since      1.0.0
 *
 * @package    WP_Cloud_Server_ServerPilot
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Cloud_Server_ServerPilot' ) ) {

	/**
     * Main WP Cloud Server - ServerPilot Module Class.
     *
     * @since 1.0.0
     */
    final class WP_Cloud_Server_ServerPilot {

	    /**
	     * @var WP_Cloud_Server_ServerPilot
	     * @since 1.0.0
	     */
	    private static $instance;

	    /**
	     * WPCS Admin Object.
	     *
	     * @var object|WPCS_ServerPilot_Admin
	     * @since 1.0.0
	     */
		public $admin;
		
	    /**
	     * WPCS Admin Object.
	     *
	     * @var object|WPCS_ServerPilot_API
	     * @since 1.0.0
	     */
        public $api;

	    /**
	     * WPCS Settings Object.
	     *
	     * @var object|WPCS_ServerPilot_Settings
	     * @since 1.0.0
	     */
        public $settings;

	    /**
	     * WPCS Cart Object.
	     *
	     * @var object|WPCS_ServerPilot_Cart
	     * @since 1.0.0
	     */
        public $cart;
    
        /**
	     * Main WP_Cloud_Server_ServerPilot Instance.
	     *
	     * Insures that only one instance of WP_Cloud_Server_ServerPilot exists in memory at any one
	     * time. Also prevents needing to define globals all over the place.
	     *
	     * @since 1.0.0
         * 
	     * @see WPCS()
	     * @return object|WP_Cloud_Server_ServerPilot
	     */
	    public static function instance() {

		    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Cloud_Server_ServerPilot ) ) {

			    self::$instance = new WP_Cloud_Server_ServerPilot;
			    self::$instance->setup_constants();

			    self::$instance->includes();
			    self::$instance->api        = new WP_Cloud_Server_ServerPilot_API();
                self::$instance->settings   = new WP_Cloud_Server_ServerPilot_Settings();
				self::$instance->cart       = new WP_Cloud_Server_ServerPilot_Cart_EDD();
				self::$instance->activator  = new WP_Cloud_Server_ServerPilot_Activator();
                
                self::$instance->activator->activate();
	
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
		    if ( ! defined( 'WPCS_SERVERPILOT_NAME' ) ) {
			    define( 'WPCS_SERVERPILOT_NAME', 'WP Cloud Server - ServerPilot Module' );
		    }

		    // Plugin version.
		    if ( ! defined( 'WPCS_SERVERPILOT_VERSION' ) ) {
			    define( 'WPCS_SERVERPILOT_VERSION', '1.4.0' );
		    }

		    // Plugin Folder Path.
		    if ( ! defined( 'WPCS_SERVERPILOT_PLUGIN_DIR' ) ) {
			    define( 'WPCS_SERVERPILOT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		    }

		    // Plugin Folder URL.
		    if ( ! defined( 'WPCS_SERVERPILOT_PLUGIN_URL' ) ) {
			    define( 'WPCS_SERVERPILOT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		    }

		    // Plugin Root File.
		    if ( ! defined( 'WPCS_SERVERPILOT_PLUGIN_FILE' ) ) {
			    define( 'WPCS_SERVERPILOT_PLUGIN_FILE', __FILE__ );
		    }

	    }

	    /**
	     * Include required files.
	     *
	     * @since 1.0.0
	     * @return void
	     */
	    private function includes() {
        
			// Load includes for the frontend e.g. shortcodes, etc
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/class-wp-cloud-server-serverpilot-activator.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/dashboard/config/config.php';		
			
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/functions/functions.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/functions/admin-notices-functions.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/functions/ajax-functions.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/functions/misc-functions.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/functions/wp-cron-functions.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/scheduled-queues/autossl-queue.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/scheduled-queues/site-creation-queue.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-serverpilot-server.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-serverpilot-template.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-serverpilot-clients.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-serverpilot-app.php';

			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/settings/serverpilot-template-settings.php';
            require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-serverpilot-template.php';

			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-serverpilot-api.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/api/wp-cloud-server-serverpilot-api-abstraction.php';
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-serverpilot-api-queue.php';
			
			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/edd/class-wp-cloud-server-serverpilot-edd.php';
            require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/woocommerce/wp-cloud-server-serverpilot-woocommerce.php';          

		    require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-serverpilot-settings.php';

			require_once WPCS_SERVERPILOT_PLUGIN_DIR . 'includes/background/serverpilot-queue.php';

        }

    }
    
} // End if class_exists check.

/**
 * The main function for that returns WP_Cloud_Server_ServerPilot
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_ServerPilot class
 *
 * @since 1.0.0
 * @return object|WP_Cloud_Server_ServerPilot
 */
function WPCS_ServerPilot() {
	return WP_Cloud_Server_ServerPilot::instance();
}

// Get WPCS_ServerPilot Running.
WPCS_ServerPilot();