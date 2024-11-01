<?php
/**
 * The DigitalOcean Module Class
 *
 * @link       https://designedforpixels.com
 * @author     Gary Jordan <gary@designedforpixels.com>
 * @since      1.0.0
 *
 * @package    WP_Cloud_Server_DigitalOcean
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Cloud_Server_DigitalOcean' ) ) {

	/**
     * Main WP Cloud Server Class.
     *
     * @since 1.0.0
     */
    final class WP_Cloud_Server_DigitalOcean {

	    /**
	     * @var WP_Cloud_Server_DigitalOcean
	     * @since 1.0.0
	     */
	    private static $instance;

	    /**
	     * WPCS Admin Object.
	     *
	     * @var object|WPCS_DigitalOcean_Admin
	     * @since 1.0.0
	     */
		public $admin;
		
	    /**
	     * WPCS Admin Object.
	     *
	     * @var object|WPCS_DigitalOcean_API
	     * @since 1.0.0
	     */
        public $api;

	    /**
	     * WPCS Settings Object.
	     *
	     * @var object|WPCS_DigitalOcean_Settings
	     * @since 1.0.0
	     */
        public $settings;

	    /**
	     * WPCS Cart Object.
	     *
	     * @var object|WPCS_DigitalOcean_Cart
	     * @since 1.0.0
	     */
        public $cart;
    
        /**
	     * Main WP_Cloud_Server_DigitalOcean Instance.
	     *
	     * Insures that only one instance of WP_Cloud_Server_DigitalOcean exists in memory at any one
	     * time. Also prevents needing to define globals all over the place.
	     *
	     * @since 1.0.0
         * 
	     * @see WPCS()
	     * @return object|WP_Cloud_Server_DigitalOcean
	     */
	    public static function instance() {

		    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Cloud_Server_DigitalOcean ) ) {

			    self::$instance = new WP_Cloud_Server_DigitalOcean;
			    self::$instance->setup_constants();

			    self::$instance->includes();
			    self::$instance->api        = new WP_Cloud_Server_DigitalOcean_API();
                self::$instance->settings   = new WP_Cloud_Server_DigitalOcean_Settings();
				self::$instance->cart       = new WP_Cloud_Server_DigitalOcean_Cart_EDD();
				self::$instance->activator  = new WP_Cloud_Server_DigitalOcean_Activator();
                
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
		    if ( ! defined( 'WPCS_DIGITALOCEAN_NAME' ) ) {
			    define( 'WPCS_DIGITALOCEAN_NAME', 'WP Cloud Server - DigitalOcean Module' );
		    }

		    // Plugin version.
		    if ( ! defined( 'WPCS_DIGITALOCEAN_VERSION' ) ) {
			    define( 'WPCS_DIGITALOCEAN_VERSION', '1.3.0' );
		    }

		    // Plugin Folder Path.
		    if ( ! defined( 'WPCS_DIGITALOCEAN_PLUGIN_DIR' ) ) {
			    define( 'WPCS_DIGITALOCEAN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		    }

		    // Plugin Folder URL.
		    if ( ! defined( 'WPCS_DIGITALOCEAN_PLUGIN_URL' ) ) {
			    define( 'WPCS_DIGITALOCEAN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		    }

		    // Plugin Root File.
		    if ( ! defined( 'WPCS_DIGITALOCEAN_PLUGIN_FILE' ) ) {
			    define( 'WPCS_DIGITALOCEAN_PLUGIN_FILE', __FILE__ );
		    }

	    }

	    /**
	     * Include required files.
	     *
	     * @since 1.0.0
	     * @return void
	     */
	    private function includes() {

            // Load Functions
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/functions/admin-notices-functions.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/functions/ajax-functions.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/functions/misc-functions.php';
			
            // Load Startup Scripts
            require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/startup-scripts/digitalocean-scripts.php';
        
			// Load includes for the frontend e.g. shortcodes, etc
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/class-wp-cloud-server-digitalocean-activator.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/api/class-wp-cloud-server-digitalocean-api.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/api/wp-cloud-server-digitalocean-api-abstraction.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/api/scheduled-queue/wp-cloud-server-digitalocean-api-queue.php';
			
            require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/edd/class-wp-cloud-server-digitalocean-edd.php'; 
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/dashboard/config/config.php';

			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/class-wp-cloud-server-digitalocean-settings.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-digitalocean-server.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-delete-digitalocean-template.php';
			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-manage-digitalocean-clients.php';

            require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/settings/digitalocean-template-settings.php';
            require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/admin/admin-post/admin-post-edit-digitalocean-template.php';

			require_once WPCS_DIGITALOCEAN_PLUGIN_DIR . 'includes/background/digitalocean-queue.php';

        }

    }
    
} // End if class_exists check.

/**
 * The main function for that returns WP_Cloud_Server_DigitalOcean
 *
 * The main function responsible for returning a single instance of the WP_Cloud_Server_DigitalOcean class
 *
 * @since 1.4
 * @return object|WP_Cloud_Server_DigitalOcean
 */
function WPCS_DigitalOcean() {
	return WP_Cloud_Server_DigitalOcean::instance();
}

// Get WPCS_DigitalOcean Running.
WPCS_DigitalOcean();