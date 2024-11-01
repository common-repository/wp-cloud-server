<?php

/**
 * WP Cloud Server - RunCloud Module Admin Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_RunCloud_Admin {

	/**
	 *  Instance of RunCloud API class
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

		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_runcloud_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_runcloud_ajax_load_scripts' ) );
		add_action( 'wp_ajax_runcloud_dashboard_tabs', array( $this, 'wpcs_ajax_process_runcloud_dashboard_tabs' ) );
		add_action( 'wp_ajax_runcloud_dashboard_website_tabs', array( $this, 'wpcs_ajax_process_runcloud_dashboard_website_tabs' ) );
		add_action( 'wp_ajax_runcloud_create_app_server_id', array( $this, 'wpcs_ajax_process_runcloud_create_app_server_id' ) );

		// Create an instance of the ServerPilot API
		self::$api = new WP_Cloud_Server_RunCloud_API();

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    	1.0.0
	 */
	public function wpcs_runcloud_enqueue_styles() {

		wp_enqueue_style( 'admin_styles', WPCS_RUNCLOUD_PLUGIN_URL . 'includes/admin/assets/admin-style.css', array(), '1.0.0', 'all' );

	}
	
	/**
	 *  Load the JS Scripts for Handling Admin and Module notices
	 *
	 *  @since  1.0.0
	 */		
	public function wpcs_runcloud_ajax_load_scripts() {
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_tabs_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_runcloud_dashboard_tabs_nonce' => wp_create_nonce( 'runcloud_dashboard_ui_tabs_nonce' ),
		);

		wp_enqueue_script( 'runcloud_dashboard-tabs-update', WPCS_RUNCLOUD_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'runcloud_dashboard-tabs-update', 'wpcs_runcloud_dashboard_tabs_ajax_script', $dashboard_tabs_args );
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_website_tabs_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_runcloud_dashboard_website_tabs_nonce' => wp_create_nonce( 'runcloud_dashboard_ui_website_tabs_nonce' ),
		);

		wp_enqueue_script( 'runcloud_dashboard-website-tabs-update', WPCS_RUNCLOUD_PLUGIN_URL . 'includes/admin/assets/js/dashboard-website-tabs.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'runcloud_dashboard-website-tabs-update', 'wpcs_runcloud_dashboard_website_tabs_ajax_script', $dashboard_website_tabs_args );
		
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$select_system_user_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_runcloud_create_app_server_id_nonce' => wp_create_nonce( 'runcloud_create_app_server_id_setting_nonce' ),
		);

		wp_enqueue_script( 'runcloud_create_app_server_id', WPCS_RUNCLOUD_PLUGIN_URL . 'includes/admin/assets/js/select-system-user.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'runcloud_create_app_server_id', 'wpcs_runcloud_create_app_server_id_script', $select_system_user_args );



	}
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_runcloud_dashboard_tabs() {

		// Check the nonce for the admin notice data
		check_ajax_referer( 'runcloud_dashboard_ui_tabs_nonce', 'runcloud_dashboard_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['runcloud_dashboard_tabs_type'] ) ) {
			$position	= $_POST['runcloud_dashboard_tabs_type'];
			$tab_id		= $_POST['runcloud_dashboard_tabs_id'];
			update_option( "wpcs_{$tab_id}_current_tab", $position );
		}
			
	}
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_runcloud_dashboard_website_tabs() {

		// Check the nonce for the admin notice data
		check_ajax_referer( 'runcloud_dashboard_ui_website_tabs_nonce', 'runcloud_dashboard_website_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['runcloud_dashboard_website_tabs_type'] ) ) {
			$position = $_POST['runcloud_dashboard_website_tabs_type'];
			update_option( 'wpcs_runcloud_website_tabs_current_tab', $position );
		} else {
			update_option( 'wpcs_runcloud_website_tabs_current_tab', 'No Data' );
		}
			
	}
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_runcloud_create_app_server_id() {

		// Check the nonce for the admin notice data
		//check_ajax_referer( 'runcloud_create_app_server_id_setting_nonce', 'runcloud_create_app_server_id_nonce' );

		if ( isset( $_POST['server_id'] ) ) {
			$server_id = $_POST['server_id'];
		}
		
		if ( !isset( $server_id ) ) {
			return;
		}
		
		$class_name = "WP_Cloud_Server_RunCloud_API";
		
		// Create instance of the RunCloud API
		$api = new $class_name();
	
		$sys_users = $api->call_api( "servers/{$server_id}/users", null, false, 900, 'GET', false, 'runcloud_sys_user_list' );

		$sys_users = ( isset($sys_users['data']) ) ? $sys_users['data'] : false;
		
		if ( $sys_users ) {
			$sys_users_list[] = "<option value=''>-- Create New System User --</option>";
			foreach ( $sys_users as $key => $sys_user ) {
				$sys_users_list[] = "<option value='{$sys_user['id']}'>{$sys_user['username']}</option>";
			}
		} else {
			$sys_users_list[] = "<option value='false'>-- No System Users Available --</option>";
		}
		
		$data = array( $sys_users_list );

		$response = json_encode( $sys_users_list );

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;		
	}

	/**
	 *  Show onboarding process when there's no server connected to the RunCloud service.
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_runcloud_notice_onboarding() {

		if (( ! get_option('wpcs_dismissed_runcloud_notice' )) && ( ! get_option('wpcs_runcloud_server_attached' )) ) {
		?>
			<div class="sp-onboarding-notice spnotice is-dismissible cloudserver-onboarding wpcs-notice" data-spnotice="runcloud_notice">
				<div class="top-bar">
					<span class="title">RunCloud Module</span>
						<p style="font-color: #fff;">
							<?php esc_attr_e( 'Powered by WP Cloud Server! Please follow the steps detailed below to start using the RunCloud Module.', 'wp-cloud-server-runcloud' ) ?>
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
	    							<h3><?php esc_attr_e( 'Create a New RunCloud Account', 'wp-cloud-server-runcloud' ) ?></h3>
	    							<p>
									<?php esc_attr_e( "RunCloud allows you to create and manage your own servers. You can select Linux version, location, and the resources such as processors,
									memory, and disk size. Click the button below to create a new account.", 'wp-cloud-server-runcloud' ) ?>
									</p>
	    							<p>
										<a href="https://www.runcloud.com" target="_blank" class="button"><?php esc_attr_e( 'Create RunCloud Account', 'wp-cloud-server-runcloud' ) ?></a>
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
	    							<h3><?php esc_attr_e( 'Enter RunCloud API Token', 'wp-cloud-server-runcloud' ) ?></h3>
	    							<p>
										<?php echo wp_kses( 'Navigate to the RunCloud API page, generate a new API Token. Click the Button below to open the API settings page, paste in the new value, then click on the save changes button.', 'wp-cloud-server-runcloud' ) ?>
									</p>
	    							<p>
										<a href="admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=settings" class="button"><?php esc_attr_e( 'Enter the API Token', 'wp-cloud-server-runcloud' ) ?></a>
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
		    						<h3><?php esc_html_e( 'Create a Hosting Plan', 'wp-cloud-server-runcloud' ) ?></h3>
		    						<p>
										<?php esc_html_e( 'All you need now is a page on your website to promote your new WordPress hosting service. Click on the Button below to create a new hosting plan using Easy Digital Downloads. Have fun!', 'wp-cloud-server-runcloud' ) ?>
									</p>
		    						<p>
										<a href="<?php echo esc_url( self_admin_url( 'post-new.php?post_type=download' )) ?>" class="button"><?php esc_html_e( 'Create a Hosting Plan', 'wp-cloud-server-runcloud' ) ?></a>
									</p>
		    					</div>
		    				</div>
							<?php } else { ?>
							<div class="step last">
		    					<div class="icon">
		    						<span class="dashicons dashicons-cart"></span>
		    					</div>
		    					<div class="content">
		    						<h3><?php esc_html_e( 'Sell Hosting Plans to Clients', 'wp-cloud-server-runcloud' ) ?></h3>
		    						<p>
										<?php esc_html_e( 'Use the \'Easy Digital Downloads\' plugin to sell Hosting Plans to Clients and Customers. Create Server Templates to use in Hosting Plans. Click the button below to install and activate the plugin.', 'wp-cloud-server-runcloud' ) ?>
									</p>
		    						<p>
										<a class="button" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=digitalocean&submenu=' . $current_page . '&edd_plugin=activate&_wpnonce=' . $current_page_nonce . '' ), 'plugin_nonce', '_wp_plugin_nonce') );?>"><?php esc_attr_e( 'Install & Activate EDD Plugin', 'wp-cloud-server-runcloud' ) ?></a>
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
	public function wpcs_runcloud_notice_no_api_connection() {

		if ( ! get_option('wpcs_dismissed_runcloud_api_notice' ) ) {
		?>
			<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="runcloud_api_notice">
    			<p>
    				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - RunCloud: We can\'t connect to the RunCloud API! There might be a temporary problem with their API or you might have added the wrong <a href="%s">API credentials</a>.', 'wp-cloud-server-runcloud' ), 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=settings' ); ?>
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
	public function wpcs_runcloud_notice_site_creation() {

		if ( ! get_option('wpcs_dismissed_runcloud_site_creation_notice' ) ) {
		?>
			<div class="notice-success notice is-dismissible wpcs-notice" data-notice="runcloud_site_creation_notice">
				<p>
					<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - RunCloud: A New RunCloud Site was Successfully Created! Please visit the %s page for details.', 'wp-cloud-server-runcloud' ), '<a href="?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=servers">' . __( 'RunCloud Server Information', 'wp-cloud-server-runcloud' ) . '</a>' ); ?>
				</p>
    		</div>
		<?php
		}

	}
}