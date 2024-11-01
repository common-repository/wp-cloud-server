<?php

/**
 * WP Cloud Server - Vultr Module Admin Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Vultr_Admin {

	/**
	 *  Instance of Vultr API class
	 *
	 *  @var resource
	 */
	private static $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_vultr_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_vultr_ajax_load_scripts' ) );
		//add_action( 'admin_init',  array( $this, 'wpcs_vultr_show_admin_notices' ) );
		add_action( 'wp_ajax_vultr_dashboard_tabs', array( $this, 'wpcs_ajax_process_vultr_dashboard_tabs' ) );

		// Create an instance of the ServerPilot API
		self::$api = new WP_Cloud_Server_Vultr_API();

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    	1.0.0
	 */
	public function wpcs_vultr_enqueue_styles() {

		wp_enqueue_style( 'admin_styles', WPCS_VULTR_PLUGIN_URL . 'includes/admin/assets/css/admin-style.css', array(), '1.0.0', 'all' );

	}
	
	/**
	 *  Load the JS Scripts for Handling Admin and Module notices
	 *
	 *  @since  1.0.0
	 */		
	public function wpcs_vultr_ajax_load_scripts() {
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_tabs_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_vultr_dashboard_tabs_nonce' => wp_create_nonce( 'vultr_dashboard_ui_tabs_nonce' ),
		);

		wp_enqueue_script( 'vultr_dashboard-tabs-update', WPCS_VULTR_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'vultr_dashboard-tabs-update', 'wpcs_vultr_dashboard_tabs_ajax_script', $dashboard_tabs_args );

	}
		
	/**
	 *  Display Admin & Vultr Module Admin Notices
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_vultr_show_admin_notices() {
		
		$module_data = get_option( 'wpcs_module_list' );

		$module_status = ( array_key_exists( 'Vultr', $module_data) ) ? $module_data['Vultr']['status'] : false ;
		
		// Show Vultr API connectivity issue notice.
		if ( current_user_can( 'manage_options' ) && ( 'active' == $module_status ) && ! self::$api->wpcs_vultr_check_api_health() ) {
			add_action( 'admin_notices', array( $this, 'wpcs_vultr_notice_no_api_connection' ) );
		}
		
		// Show module onboarding process in module admin page.
		if ( current_user_can( 'manage_options' ) && ( 'active' == $module_status ) ) {
			add_action( 'wpcs_vultr_module_notices', array( $this, 'wpcs_vultr_notice_onboarding' ) );
		}

		// Show site creation notice.
		if ( current_user_can( 'manage_options' ) && get_option('wpcs_vultr_new_site_created' )) {
			add_action( 'wpcs_vultr_module_notices', array( $this, 'wpcs_vultr_notice_site_creation' ) );
		}

	}
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_vultr_dashboard_tabs() {

		// Check the nonce for the admin notice data
		check_ajax_referer( 'vultr_dashboard_ui_tabs_nonce', 'vultr_dashboard_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['vultr_dashboard_tabs_type'] ) ) {
			$position	= $_POST['vultr_dashboard_tabs_type'];
			$tab_id		= $_POST['vultr_dashboard_tabs_id'];
			update_option( "wpcs_{$tab_id}_current_tab", $position );
		}
			
	}

	/**
	 *  Show onboarding process when there's no server connected to the Vultr service.
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_vultr_notice_onboarding() {

		if (( ! get_option('wpcs_dismissed_vultr_notice' )) && ( ! get_option('wpcs_vultr_server_attached' )) ) {
		?>
			<div class="sp-onboarding-notice spnotice is-dismissible cloudserver-onboarding wpcs-notice" data-spnotice="vultr_notice">
				<div class="top-bar">
					<span class="title">Vultr Module</span>
						<p style="font-color: #fff;">
							<?php esc_attr_e( 'Powered by WP Cloud Server! Please follow the steps detailed below to start using the Vultr Module.', 'wp-cloud-server-vultr' ) ?>
						</p>
	    		</div>
	    		<div class="steps numbers">
	    			<ol>
	    				<li>
	    					<div class="step">
	    						<div class="icon">
	    							<span class="dashicons dashicons-admin-users"></span>
	    						</div>
	    						<div class="content">
	    							<h3><?php esc_attr_e( 'Create a New Vultr Account', 'wp-cloud-server-vultr' ) ?></h3>
	    							<p>
									<?php esc_attr_e( "Vultr allows you to create and manage your own servers. You can select Linux version, location, and the resources such as processors,
									memory, and disk size. Click the button below to create a new account.", 'wp-cloud-server-vultr' ) ?>
									</p>
	    							<p>
										<a href="https://www.vultr.com" target="_blank" class="button"><?php esc_attr_e( 'Create Vultr Account', 'wp-cloud-server-vultr' ) ?></a>
									</p>
	    						</div>
	    					</div>
	    				</li>
	    				<li>
	    					<div class="step">
	    						<div class="icon">
	    							<span class="dashicons dashicons-admin-network"></span>
	    						</div>
	    						<div class="content">
	    							<h3><?php esc_attr_e( 'Enter Vultr API Token', 'wp-cloud-server-vultr' ) ?></h3>
	    							<p>
										<?php echo wp_kses( 'Navigate to the Vultr API page, generate a new API Token. Click the Button below to open the API settings page, paste in the new value, then click on the save changes button.', 'wp-cloud-server-vultr' ) ?>
									</p>
	    							<p>
										<a href="admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=settings" class="button"><?php esc_attr_e( 'Enter the API Token', 'wp-cloud-server-vultr' ) ?></a>
									</p>
	    						</div>
	    					</div>
	    				</li>
	    				<li>
							<?php 	
							include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
							// check for plugin using plugin name
							if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
							?>
	    					<div class="step last">
		    					<div class="icon">
		    						<span class="dashicons dashicons-cart"></span>
		    					</div>
		    					<div class="content">
		    						<h3><?php esc_html_e( 'Create a Hosting Plan', 'wp-cloud-server-vultr' ) ?></h3>
		    						<p>
										<?php esc_html_e( 'All you need now is a page on your website to promote your new WordPress hosting service. Click on the Button below to create a new hosting plan using Easy Digital Downloads. Have fun!', 'wp-cloud-server-vultr' ) ?>
									</p>
		    						<p>
										<a href="<?php echo esc_url( self_admin_url( 'post-new.php?post_type=download' )) ?>" class="button"><?php esc_html_e( 'Create a Hosting Plan', 'wp-cloud-server-vultr' ) ?></a>
									</p>
		    					</div>
		    				</div>
							<?php } else { ?>
							<div class="step last">
		    					<div class="icon">
		    						<span class="dashicons dashicons-cart"></span>
		    					</div>
		    					<div class="content">
		    						<h3><?php esc_html_e( 'Sell Hosting Plans to Clients', 'wp-cloud-server-vultr' ) ?></h3>
		    						<p>
										<?php esc_html_e( 'Use the \'Easy Digital Downloads\' plugin to sell Hosting Plans to Clients and Customers. Create Server Templates to use in Hosting Plans. Click the button below to install and activate the plugin.', 'wp-cloud-server-vultr' ) ?>
									</p>
		    						<p>
										<a class="button" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=digitalocean&submenu=' . $current_page . '&edd_plugin=activate&_wpnonce=' . $current_page_nonce . '' ), 'plugin_nonce', '_wp_plugin_nonce') );?>"><?php esc_attr_e( 'Install & Activate EDD Plugin', 'wp-cloud-server-vultr' ) ?></a>
									</p>
		    					</div>
		    				</div>
							<?php } ?>
	    				</li>
					</ol>
	  			</div>
			</div>
		<?php
		}

	}
	
	/**
	 *  Show API connectivity issue warning
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_vultr_notice_no_api_connection() {

		if ( ! get_option('wpcs_dismissed_vultr_api_notice' ) ) {
		?>
			<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="vultr_api_notice">
    			<p>
    				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - Vultr: We can\'t connect to the Vultr API! There might be a temporary problem with their API or you might have added the wrong <a href="%s">API credentials</a>.', 'wp-cloud-server-vultr' ), 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=settings' ); ?>
	    		</p>
    		</div>
		<?php
		}

	}

	/**
	 *  Show info message when new site created
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_vultr_notice_site_creation() {

		if ( ! get_option('wpcs_dismissed_vultr_site_creation_notice' ) ) {
		?>
			<div class="notice-success notice is-dismissible wpcs-notice" data-notice="vultr_site_creation_notice">
				<p>
					<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - Vultr: A New Vultr Site was Successfully Created! Please visit the %s page for details.', 'wp-cloud-server-vultr' ), '<a href="?page=wp-cloud-server-admin-menu&tab=vultr&submenu=servers">' . __( 'Vultr Server Information', 'wp-cloud-server-vultr' ) . '</a>' ); ?>
				</p>
    		</div>
		<?php
		}

	}
}