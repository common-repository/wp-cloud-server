<?php

/**
 * WP Cloud Server - AWS Lightsail Module Admin Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_AWS_Lightsail
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_AWS_Lightsail_Admin {

	/**
	 *  Instance of AWS Lightsail API class
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

		add_action( 'admin_enqueue_scripts',  array( $this, 'wpcs_aws_lightsail_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_aws_lightsail_ajax_load_scripts' ) );
		//add_action( 'admin_init',  array( $this, 'wpcs_aws_lightsail_show_admin_notices' ) );
		add_action( 'wp_ajax_aws_lightsail_dashboard_tabs', array( $this, 'wpcs_ajax_process_aws_lightsail_dashboard_tabs' ) );

		// Create an instance of the ServerPilot API
		self::$api = new WP_Cloud_Server_AWS_Lightsail_API();

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    	1.0.0
	 */
	public function wpcs_aws_lightsail_enqueue_styles() {

		wp_enqueue_style( 'admin_styles', WPCS_AWS_LIGHTSAIL_PLUGIN_URL . 'includes/admin/assets/css/admin-style.css', array(), '1.0.0', 'all' );

	}
	
	/**
	 *  Load the JS Scripts for Handling Admin and Module notices
	 *
	 *  @since  1.0.0
	 */		
	public function wpcs_aws_lightsail_ajax_load_scripts() {
		
		// Load the JavaScript for the dashboard tabs & set-up the related Ajax script
		$dashboard_tabs_args = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_aws_lightsail_dashboard_tabs_nonce' => wp_create_nonce( 'aws_lightsail_dashboard_ui_tabs_nonce' ),
		);

		wp_enqueue_script( 'aws_lightsail_dashboard-tabs-update', WPCS_AWS_LIGHTSAIL_PLUGIN_URL . 'includes/admin/assets/js/dashboard-tab.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'aws_lightsail_dashboard-tabs-update', 'wpcs_aws_lightsail_dashboard_tabs_ajax_script', $dashboard_tabs_args );

	}
		
	/**
	 *  Display Admin & AWS Lightsail Module Admin Notices
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_aws_lightsail_show_admin_notices() {
		
		$module_data = get_option( 'wpcs_module_list' );

		$module_status = ( array_key_exists( 'AWS Lightsail', $module_data) ) ? $module_data['AWS Lightsail']['status'] : false ;
		
		// Show AWS Lightsail API connectivity issue notice.
		if ( current_user_can( 'manage_options' ) && ( 'active' == $module_status ) && ! self::$api->wpcs_aws_lightsail_check_api_health() ) {
			add_action( 'admin_notices', array( $this, 'wpcs_aws_lightsail_notice_no_api_connection' ) );
		}
		
		// Show module onboarding process in module admin page.
		if ( current_user_can( 'manage_options' ) && ( 'active' == $module_status ) ) {
			add_action( 'wpcs_aws_lightsail_module_notices', array( $this, 'wpcs_aws_lightsail_notice_onboarding' ) );
		}

		// Show site creation notice.
		if ( current_user_can( 'manage_options' ) && get_option('wpcs_aws_lightsail_new_site_created' )) {
			add_action( 'wpcs_aws_lightsail_module_notices', array( $this, 'wpcs_aws_lightsail_notice_site_creation' ) );
		}

	}
	
	/**
	 *  Create the Option for the Dashboard Update
	 *
	 *  @since  2.0.0
	 */			
	function wpcs_ajax_process_aws_lightsail_dashboard_tabs() {

		// Check the nonce for the admin notice data
		check_ajax_referer( 'aws_lightsail_dashboard_ui_tabs_nonce', 'aws_lightsail_dashboard_tabs_nonce' );

		// Pick up the notice "admin_type" - passed via the "data-tab" attribute
		if ( isset( $_POST['aws_lightsail_dashboard_tabs_type'] ) ) {
			$position	= $_POST['aws_lightsail_dashboard_tabs_type'];
			$tab_id		= $_POST['aws_lightsail_dashboard_tabs_id'];
			update_option( "wpcs_{$tab_id}_current_tab", $position );
		}
			
	}
	
	/**
	 *  Show API connectivity issue warning
	 *
	 *  @since  1.0.0
	 */
	public function wpcs_aws_lightsail_notice_no_api_connection() {

		if ( ! get_option('wpcs_dismissed_aws_lightsail_api_notice' ) ) {
		?>
			<div class="notice-warning notice is-dismissible wpcs-notice" data-notice="aws_lightsail_api_notice">
    			<p>
    				<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - AWS Lightsail: We can\'t connect to the AWS Lightsail API! There might be a temporary problem with their API or you might have added the wrong <a href="%s">API credentials</a>.', 'wp-cloud-server-aws-lightsail' ), 'admin.php?page=wp-cloud-server-admin-menu&tab=aws-lightsail&submenu=settings' ); ?>
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
	public function wpcs_aws_lightsail_notice_site_creation() {

		if ( ! get_option('wpcs_dismissed_aws_lightsail_site_creation_notice' ) ) {
		?>
			<div class="notice-success notice is-dismissible wpcs-notice" data-notice="aws_lightsail_site_creation_notice">
				<p>
					<?php echo wp_sprintf( wp_kses( 'WP Cloud Server - AWS Lightsail: A New AWS Lightsail Site was Successfully Created! Please visit the %s page for details.', 'wp-cloud-server-aws-lightsail' ), '<a href="?page=wp-cloud-server-admin-menu&tab=aws-lightsail&submenu=servers">' . __( 'AWS Lightsail Server Information', 'wp-cloud-server-aws-lightsail' ) . '</a>' ); ?>
				</p>
    		</div>
		<?php
		}

	}
}