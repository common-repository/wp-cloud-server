<?php

/**
 * WP Cloud Server - Cloudways Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Cloudways
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Cloudways_Settings {
		
	/**
	 *  Status of this class
	 *
	 *  @var resource
	 */
	public static $status;

	/**
	 *  Module Name
	 *
	 *  @var string
	 */
	private static $module_name = 'Cloudways';
	
	/**
	 *  Module Type
	 *
	 *  @var string
	 */
	private static $module_type = 'managed_server';
		
	/**
	 *  Module Description
	 *
	 *  @var string
	 */
	private static $module_desc = 'Use Cloudways to create and manage new cloud servers.';

	/**
	 *  Instance of WPCloudServer API Class
	 *
	 *  @var resource
	 */
	private static $api;

	/**
	 *  API Status
	 *
	 *  @var resource
	 */
	public $api_connected;

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_cloudways_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_cloudways_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_cloudways_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_cloudways_create_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_cloudways_create_license_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_cloudways_create_application_setting_sections_and_fields' ) );
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_cloudways_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_cloudways_update_servers' ) );					
		add_action( 'wpcs_add_module_tabs', array( $this, 'wpcs_cloudways_module_tab' ), 10, 3 );
		add_action( 'wpcs_add_module_tabs_content_with_submenu', array( $this, 'wpcs_cloudways_module_tab_content_with_submenu' ), 10, 3 );
		//add_action( 'wpcs_add_log_page_heading_tabs', array( $this, 'wpcs_cloudways_log_page_tabs' ) );
		//add_action( 'wpcs_add_log_page_tabs_content', array( $this, 'wpcs_cloudways_log_page_tabs_content' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_cloudways_logged_data' ) );
		
		// Handle Scheduled Events
		add_action( 'wpcs_cloudways_module_activate', array( $this, 'wpcs_cloudways_module_activate_server_completed_queue' ) );
		add_action( 'wpcs_cloudways_run_server_completed_queue', array( $this, 'wpcs_cloudways_module_run_server_completed_queue' ) );
		
		add_filter( 'cron_schedules', array( $this, 'wpcs_cloudways_custom_cron_schedule' ) );

		self::$api = new WP_Cloud_Server_Cloudways_API();

	}
		
	/**
	 *  Add Cloudways Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		$this->api_connected = self::$api->wpcs_cloudways_check_api_health();
			
		if ( ! array_key_exists( self::$module_name, $module_data) ) {

			if ( ! isset( self::$status )) {
					self::$status = 'inactive';
			}
		
			$module_data[self::$module_name]['module_name']	= self::$module_name;
			$module_data[self::$module_name]['module_desc']=self::$module_desc;
			$module_data[self::$module_name]['status']=self::$status;
			$module_data[self::$module_name]['module_type']=self::$module_type;

			$module_data[ self::$module_name ]['servers']	= array();
			
			$templates		= get_option( 'wpcs_template_data_backup' );
			$template_data	= ( !empty( $templates[ self::$module_name ]['templates'] ) ) ? $templates[ self::$module_name ]['templates'] : array();
			$module_data[ self::$module_name ]['templates']	= $template_data;
			
			wpcs_cloudways_log_event( 'Cloudways', 'Success', 'The Cloudways Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = $this->api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update Cloudways Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'Cloudways' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_cloudways_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_log_event( 'Cloudways', 'Success', 'Cloudways Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update Cloudways Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_cloudways_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_cloudways_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_cloudways_admin_menu', 'wpcs_cloudways_email' );
		register_setting( 'wpcs_cloudways_admin_menu', 'wpcs_cloudways_api_key' );

		add_settings_section(
			'wpcs_cloudways_admin_menu',
			esc_attr__( 'Cloudways API Credentials', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_section_callback_cloudways_api' ),
			'wpcs_cloudways_admin_menu'
		);

		add_settings_field(
			'wpcs_cloudways_email',
			esc_attr__( 'Email:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_email' ),
			'wpcs_cloudways_admin_menu',
			'wpcs_cloudways_admin_menu'
		);
		
		add_settings_field(
			'wpcs_cloudways_api_key',
			esc_attr__( 'API Key:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_api_key' ),
			'wpcs_cloudways_admin_menu',
			'wpcs_cloudways_admin_menu'
		);

	}
		
	/**
	 *  Cloudways API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_cloudways_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official Cloudways REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://platform.cloudways.com/login" target="_blank">Cloudways Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  Cloudways API Field Callback for User Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_email() {

		$value = get_option( 'wpcs_cloudways_email' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_cloudways_email" name="wpcs_cloudways_email" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Cloudways API Field Callback for Password.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_api_key() {

		$value = get_option( 'wpcs_cloudways_api_key' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_cloudways_api_key" name="wpcs_cloudways_api_key" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_cloudways_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_name', $args );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_host_name' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_type' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_providers' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_region' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_size' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_app_name' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_app' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_project' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_new_project' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_action' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_db_volume_size' );
		register_setting( 'wpcs_cloudways_create_template', 'wpcs_cloudways_template_data_volume_size' );
		
		register_setting( 'wpcs_cloudways_delete_template', 'wpcs_cloudways_confirm_template_delete' );
		register_setting( 'wpcs_cloudways_delete_template', 'wpcs_cloudways_confirm_template_id' );

		add_settings_section(
			'wpcs_cloudways_create_template',
			'',
			'',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_name' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_app_name',
			esc_attr__( 'Application Name:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_app_name' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_host_name',
			esc_attr__( 'Server Hostname:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_host_name' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_app',
			esc_attr__( 'Application:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_app' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_providers',
			esc_attr__( 'Provider:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_providers' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);

		add_settings_field(
			'wpcs_cloudways_template_size',
			esc_attr__( 'Size:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_size' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_region',
			esc_attr__( 'Location:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_region' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_project',
			esc_attr__( 'Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_project' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);

		add_settings_field(
			'wpcs_cloudways_template_new_project',
			esc_attr__( 'New Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_new_project' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		
		add_settings_field(
			'wpcs_cloudways_template_db_volume_size',
			esc_attr__( 'Database Size:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_template_db_volume_size' ),
			'wpcs_cloudways_create_template',
			'wpcs_cloudways_create_template'
		);
		

	}
		
	/**
	 *  Cloudways API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_cloudways_create_template() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new Cloudways server template. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Template\' to build your new Server.', 'wp-cloud-server-cloudways' );
		echo '</p>';

	}



	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_name() {

		echo '<input class="w-400" type="text" placeholder="Template Name" id="wpcs_cloudways_template_name" name="wpcs_cloudways_template_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}
	
	/**
	 *  Vultr Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_host_name() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		?>
		<select class="w-400" name="wpcs_cloudways_template_host_name" id="wpcs_cloudways_template_host_name">
			<?php
			if ( !empty( $host_names ) ) {
				?><optgroup label="Select Hostname"><?php
				foreach ( $host_names as $key => $host_name ) {
			?>
            <option value="<?php echo "{$host_name['hostname']}|{$host_name['label']}"; ?>"><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
			<?php } } ?>
			</optgroup>
			<optgroup label="User Choice">
				<option value="[Customer Input]"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_region() {

		$regions = wpcs_cloudways_regions_list();

		?>

		<select class="w-400" name="wpcs_cloudways_template_region" id="wpcs_cloudways_template_region">
			<?php
			if ( !empty( $regions ) ) {
				?><optgroup label="Select Region"><?php
				foreach ( $regions['do'] as $key => $region ) {
				?>
    				<option value="<?php echo "{$region['id']}|{$region['name']}"; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Regions Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_providers() {

		$providers = wpcs_cloudways_providers_list();

		?>

		<select class="w-400" name="wpcs_cloudways_template_providers" id="wpcs_cloudways_template_providers">
			<optgroup label="Select Cloud Provider">
			<?php
			if ( !empty( $providers ) ) {
				foreach ( $providers as $key => $provider ) {
				?>
    				<option value='<?php echo "{$key}|{$provider['name']}"; ?>'><?php echo $provider['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Providers Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_size() {

		$plans = wpcs_cloudways_plans_list();

		?>

		<select class="w-400" name="wpcs_cloudways_template_size" id="wpcs_cloudways_template_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans['do'][0] as $key => $plan ) {
				?>
    				<option value="<?php echo $plan; ?>"><?php echo $plan; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Plans Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_app_name() {

		echo '<input class="w-400" type="text" placeholder="application-name" id="wpcs_cloudways_template_app_name" name="wpcs_cloudways_template_app_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_app() {

		$apps = wpcs_cloudways_apps_list();
		?>

		<select  class="w-400" name="wpcs_cloudways_template_app" id="wpcs_cloudways_template_app">
			<optgroup label="Select App">
			<?php
			if ( !empty( $apps ) ) {
				foreach ( $apps as $key => $app ) {
					foreach ( $app as $ver ) {
						if ( 'wordpressdefault' !== $ver['application'] ) {
							?>
    						<option value="<?php echo "{$ver['application']}|{$ver['app_version']}"; ?>"><?php echo "{$ver['label']} {$ver['app_version']}"; ?></option>
							<?php
						}
					}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_project() {

		$projects = wpcs_cloudways_project_list();
		?>

		<select class="w-400" name="wpcs_cloudways_template_project" id="wpcs_cloudways_template_project">
			<optgroup label="Select Project">
				<?php
				if ( !empty( $projects ) ) {
					foreach ( $projects as $id =>$project ) {
						?>
    					<option value="<?php echo $project; ?>"><?php echo $project; ?></option>
						<?php
					}
				} else {
					?>
					<option value="false"><?php _e( '-- No Projects Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Cloudways Enter New Project Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_new_project() {

		echo '<input class="w-400" type="text" placeholder="New Project" id="wpcs_cloudways_template_new_project" name="wpcs_cloudways_template_new_project" value=""/>';
		echo '<p class="text_desc" >[ You can use any character, period (.), and spaces ]</p>';

	}
	
	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_template_db_volume_size() {
		?>
		<div class="slidecontainer">
  			<input type="range" min="10" max="1024" value="50" class="slider" name="wpcs_cloudways_template_db_volume_size" id="myRange">
  			<p>Value: <span id="demo"></span></p>
		</div>
		<?php
	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_create_application_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_cloudways_app_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_cloudways_create_application', 'wpcs_cloudways_app_label' );
		register_setting( 'wpcs_cloudways_create_application', 'wpcs_cloudways_app_server_id' );
		register_setting( 'wpcs_cloudways_create_application', 'wpcs_cloudways_app_application' );
		register_setting( 'wpcs_cloudways_create_application', 'wpcs_cloudways_app_project' );
		register_setting( 'wpcs_cloudways_create_application', 'wpcs_cloudways_app_new_project' );
		
		register_setting( 'wpcs_cloudways_delete_application', 'wpcs_cloudways_confirm_app_delete' );
		register_setting( 'wpcs_cloudways_delete_application', 'wpcs_cloudways_confirm_app_id' );
		register_setting( 'wpcs_cloudways_delete_application', 'wpcs_cloudways_confirm_app_server_id' );

		add_settings_section(
			'wpcs_cloudways_create_application',
			esc_attr__( 'Add New Cloudways Application', 'wp-cloud-server-cloudways' ),
			'',
			'wpcs_cloudways_create_application'
		);

		add_settings_field(
			'wpcs_cloudways_app_label',
			esc_attr__( 'Application Name:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_app_label' ),
			'wpcs_cloudways_create_application',
			'wpcs_cloudways_create_application'
		);

		add_settings_field(
			'wpcs_cloudways_app_server_id',
			esc_attr__( 'Server:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_app_server_id' ),
			'wpcs_cloudways_create_application',
			'wpcs_cloudways_create_application'
		);

		add_settings_field(
			'wpcs_cloudways_app_application',
			esc_attr__( 'Application:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_app_application' ),
			'wpcs_cloudways_create_application',
			'wpcs_cloudways_create_application'
		);
		
		add_settings_field(
			'wpcs_cloudways_app_project',
			esc_attr__( 'Select Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_app_project' ),
			'wpcs_cloudways_create_application',
			'wpcs_cloudways_create_application'
		);

		add_settings_field(
			'wpcs_cloudways_app_new_project',
			esc_attr__( 'New Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_app_new_project' ),
			'wpcs_cloudways_create_application',
			'wpcs_cloudways_create_application'
		);
	}
	
	/**
	 *  Cloudways API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_cloudways_create_application() {

		echo '<p>This page allows you to save \'Templates\' for use when creating Hosting Plans in \'Easy Digital Downloads\'. You can select the Image, Region, and Size, to be used when creating a new Server!</p>';

	}
	

	/**
	 *  Cloudways Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_app_server_id() {
		
		$servers = wpcs_cloudways_server_list();

		?>
		<select  class="w-400" name="wpcs_cloudways_app_server_id" id="wpcs_cloudways_app_server_id">
            <optgroup label="Server">
			<?php
			if ( !empty( $servers ) ) {
				foreach ( $servers as $id => $server ) {
				?>
    				<option value="<?php echo $id; ?>"><?php echo $server; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Servers Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_app_label() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo '<input  class="w-400" type="text" placeholder="Template Name" id="wpcs_cloudways_app_label" name="wpcs_cloudways_app_label" value=""/>';
		echo '<p class="text_desc" >[ You can use any valid text, numeric, and space characters ]</p>';

	}

	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_app_application() {
		
		$apps = wpcs_cloudways_apps_list();
		?>

		<select  class="w-400" name="wpcs_cloudways_app_application" id="wpcs_cloudways_app_application">
			<optgroup label="Apps">
			<?php
			if ( !empty( $apps ) ) {
				foreach ( $apps as $key => $app ) {
					foreach ( $app as $ver ) {
						if ( 'wordpressdefault' !== $ver['application'] ) {
							?>
    						<option value="<?php echo "{$ver['application']}|{$ver['app_version']}"; ?>"><?php echo "{$ver['label']} {$ver['app_version']}"; ?></option>
							<?php
						}
					}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_app_project() {

		$projects = wpcs_cloudways_project_list();
		?>

		<select class="w-400" name="wpcs_cloudways_app_project" id="wpcs_cloudways_app_project">
			<optgroup label="Projects">
				<option value="">-- No Project --</option>
				<?php
				if ( !empty( $projects ) ) {
					foreach ( $projects as $id =>$project ) {
						?>
    					<option value="<?php echo "{$id}|{$project}"; ?>"><?php echo $project; ?></option>
						<?php
					}
					?>
					<?php
				} else {
					?>
					<option value="no_project"><?php _e( '-- No Projects Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Cloudways Enter New Project Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_app_new_project() {

		echo '<input class="w-400" type="text" placeholder="Enter New Project" id="wpcs_cloudways_app_new_project" name="wpcs_cloudways_app_new_project" value=""/>';
		echo '<p class="text_desc" >[ You can use any character, period (.), and spaces ]</p>';

	}

	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_cloudways_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_name', $args );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_type' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_providers' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_region' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_size' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_app_name' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_app' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_project' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_db_volume_size' );
		register_setting( 'wpcs_cloudways_create_server', 'wpcs_cloudways_server_data_volume_size' );
		
		register_setting( 'wpcs_cloudways_delete_server', 'wpcs_cloudways_confirm_server_delete' );
		register_setting( 'wpcs_cloudways_delete_server', 'wpcs_cloudways_confirm_app_server_id' );

		add_settings_section(
			'wpcs_cloudways_create_server',
			esc_attr__( 'Create New Cloudways Server', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_section_callback_cloudways_create_server' ),
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_name' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_app_name',
			esc_attr__( 'Application Name:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_app_name' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_app',
			esc_attr__( 'Application:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_app' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_providers',
			esc_attr__( 'Provider:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_providers' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);

		add_settings_field(
			'wpcs_cloudways_server_size',
			esc_attr__( 'Size:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_size' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_region',
			esc_attr__( 'Location:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_region' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_db_volume_size',
			esc_attr__( 'Database Volume Size:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_db_volume_size' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_data_volume_size',
			esc_attr__( 'Data Volume Size:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_data_volume_size' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		
		add_settings_field(
			'wpcs_cloudways_server_project',
			esc_attr__( 'Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_project' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);

		add_settings_field(
			'wpcs_cloudways_server_new_project',
			esc_attr__( 'Add Project:', 'wp-cloud-server-cloudways' ),
			array( $this, 'wpcs_field_callback_cloudways_server_new_project' ),
			'wpcs_cloudways_create_server',
			'wpcs_cloudways_create_server'
		);
		

	}
		
	/**
	 *  Cloudways API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_cloudways_create_server() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new Cloudways Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-cloudways' );
		echo '</p>';

	}



	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_name() {

		echo '<input class="w-400" type="text" placeholder="server-name" id="wpcs_cloudways_server_name" name="wpcs_cloudways_server_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}

	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_region() {

		$regions = wpcs_cloudways_regions_list();

		?>

		<select class="w-400" name="wpcs_cloudways_server_region" id="wpcs_cloudways_server_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions['do'] as $key => $region ) {
				?>
    				<option value="<?php echo $region['id']; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Regions Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_providers() {

		$providers = wpcs_cloudways_providers_list();

		?>

		<select class="w-400" name="wpcs_cloudways_server_providers" id="wpcs_cloudways_server_providers">
			<optgroup label="Select Cloud Provider">
			<?php
			if ( !empty( $providers ) ) {
				foreach ( $providers as $key => $provider ) {
				?>
    				<option value="<?php echo $key; ?>"><?php echo $provider['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Providers Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_size() {

		$plans = wpcs_cloudways_plans_list();

		?>

		<select class="w-400" name="wpcs_cloudways_server_size" id="wpcs_cloudways_server_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans['do'][0] as $key => $plan ) {
				?>
    				<option value="<?php echo $plan; ?>"><?php echo $plan; ?></option>
				<?php
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Plans Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_app_name() {

		echo '<input class="w-400" type="text" placeholder="application-name" id="wpcs_cloudways_server_app_name" name="wpcs_cloudways_server_app_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_app() {

		$apps = wpcs_cloudways_apps_list();
		?>

		<select  class="w-400" name="wpcs_cloudways_server_app" id="wpcs_cloudways_server_app">
			<optgroup label="Select App">
			<?php
			if ( !empty( $apps ) ) {
				foreach ( $apps as $key => $app ) {
					foreach ( $app as $ver ) {
						if ( 'wordpressdefault' !== $ver['application'] ) {
							?>
    						<option value="<?php echo "{$ver['application']}|{$ver['app_version']}"; ?>"><?php echo "{$ver['label']} {$ver['app_version']}"; ?></option>
							<?php
						}
					}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Cloudways Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_cloudways_server_project() {

		$projects = wpcs_cloudways_project_list();
		?>

		<select class="w-400" name="wpcs_cloudways_server_project" id="wpcs_cloudways_server_project">
			<optgroup label="Select Project">
				<?php
				if ( !empty( $projects ) ) {
					foreach ( $projects as $id =>$project ) {
						?>
    					<option value="<?php echo $project; ?>"><?php echo $project; ?></option>
						<?php
					}
				} else {
					?>
					<option value="false"><?php _e( '-- No Projects Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Cloudways Enter New Project Name.
	 *
	 *  @since 3.0.3
	 */
	public function wpcs_field_callback_cloudways_server_new_project() {

		echo '<input class="w-400" type="text" placeholder="New Project" id="wpcs_cloudways_server_new_project" name="wpcs_cloudways_server_new_project" value=""/>';
		echo '<p class="text_desc" >[ You can use any character, period (.), and spaces ]</p>';

	}
	
	/**
	 *  Cloudways Field Callback for Server Database Volume Size.
	 *
	 *  @since 3.0.3
	 */
	public function wpcs_field_callback_cloudways_server_db_volume_size() {
		?>
		<input class="w-400" type="text" placeholder="20" name="wpcs_cloudways_server_db_volume_size" id="wpcs_cloudways_server_db_volume_size" value="">
		<p class="text_desc">[ Only required for Amazon or Google Templates ]</p>
		<?php
	}
	
	/**
	 *  Cloudways Field Callback for Server Database Volume Size.
	 *
	 *  @since 3.0.3
	 */
	public function wpcs_field_callback_cloudways_server_data_volume_size() {
		?>
		<input class="w-400" type="text" placeholder="20" name="wpcs_cloudways_server_data_volume_size" id="wpcs_cloudways_server_data_volume_size" value="">
		<p class="text_desc">[ Only required for Amazon or Google Templates ]</p>
		<?php
	}
		
	/**
	 *  Cloudways Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'Cloudways' == $module_name ) || ( 'active' == $module_data['Cloudways']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'Cloudways' !== $module_name ) && ( 'active' == $module_data['Cloudways']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'Cloudways' !== $module_name ) && ( 'active' == $module_data['Cloudways']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['Cloudways']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=servers' ), 'cloudways_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'cloudways' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Cloudways', 'wp-cloud-server-cloudways' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  Cloudways Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'cloudways' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_cloudways_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=servers'), 'cloudways_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-cloudways' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=templates'), 'cloudways_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-cloudways' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=addserver'), 'cloudways_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-cloudways' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=addtemplate'), 'cloudways_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-cloudways' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=settings'), 'cloudways_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-cloudways' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=debug'), 'cloudways_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-cloudways' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'cloudways_settings_nonce' ) ) ) {
						delete_option( 'wpcs_cloudways_api_token' );
						delete_option( 'wpcs_dismissed_cloudways_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_cloudways_admin_menu' );
						do_settings_sections( 'wpcs_cloudways_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=cloudways&submenu=settings&resetapi=true' ), 'cloudways_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Cloudways API Credentials', 'wp-cloud-server-cloudways' ) ?></a>
				</p> <?php

				} elseif ( 'servers' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-servers-page.php';
				} elseif ( 'templates' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-templates-page.php';
				} elseif ( 'addserver' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-add-server-page.php';
				} elseif ( 'addtemplate' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-add-template-page.php';
				} elseif ( 'debug' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-debug-page.php';
				}
		}
	}
	
	/**
	 *  Cloudways Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['Cloudways']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=cloudways_logs') );?>" class="nav-tab<?php echo ( 'cloudways_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Cloudways', 'wp-cloud-server-cloudways' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  Cloudways Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_cloudways_log_page_tabs_content( $active_tab ) {
		
			if ( 'cloudways_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_cloudways_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-cloudways' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-cloudways' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-cloudways' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-cloudways' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-cloudways' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-cloudways' ); ?></th>
    							</tr>
    						</thead>
    						<tbody>
							<?php
							if ( !empty( $logged_data ) ) {
								$formatted_data = array_reverse( $logged_data, true );
								foreach ( $formatted_data as $logged_event ) {	
								?>
    								<tr>
        								<td><?php echo $logged_event['date']; ?></td>
        								<td><?php echo $logged_event['event']; ?></td>
										<td><?php echo $logged_event['status']; ?></td>
										<td><?php echo $logged_event['description']; ?></td>
    								</tr>
								<?php
								}
							} else {
							?>
    								<tr>
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-cloudways' ); ?></td>
    								</tr>								
							<?php
							}
							?>								
    						</tbody>
						</table>
					</div>

	<?php
			}
	}
		
	/**
	 *  Return Cloudways Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_cloudways_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return Cloudways Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	function wpcs_cloudways_module_api_connected() {

		return $this->api_connected;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_cloudways_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_cloudways_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Cloudways Template was Created.', 'wp-cloud-server-cloudways' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-cloudways' );
			}

			add_settings_error(
				'wpcs_cloudways_template_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Sanitize Server Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_cloudways_server_name( $name ) {

		$output = get_option( 'wpcs_cloudways_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, and a period (.)', 'wp-cloud-server-cloudways' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New Cloudways Server is being Created.', 'wp-cloud-server-cloudways' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-cloudways' );
			}

			add_settings_error(
				'wpcs_cloudways_server_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Sanitize API Token
	 *
	 *  @since  1.0.0
	 *  @param  string  $token original API token
	 *  @return string  checked API token
	 */
	public function sanitize_cloudways_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_cloudways_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The Cloudways API Token was updated.', 'wp-cloud-server-cloudways' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Cloudways API Token!', 'wp-cloud-server-cloudways' );
			}

			add_settings_error(
				'wpcs_cloudways_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return Cloudways Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_cloudways_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_cloudways_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_cloudways_logged_data', $data );
		}

	}
	
	/**
	 *  Set-up Cloudways Cron Job.
	 *
	 *  @since 1.0.1
	 */
	public function  wpcs_cloudways_custom_cron_schedule( $schedules ) {
    	$schedules[ 'one_minute' ] = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    return $schedules;
	}
	
	/**
	 *  Activates the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_cloudways_module_activate_server_completed_queue() {

		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'wpcs_cloudways_run_server_completed_queue' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'one_minute', 'wpcs_cloudways_run_server_completed_queue' );
			wpcs_cloudways_log_event( 'Cloudways', 'Success', 'Cloudways Server Queue Started' );
		}

	}
	
	/**
	 *  Cloudways Server Completed Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_cloudways_module_run_server_completed_queue() {
		
		$api			= new WP_Cloud_Server_Cloudways_API();
		$server_queue	= get_option( 'wpcs_cloudways_server_complete_queue', array() );
		
		if ( ! empty( $server_queue ) ) {
			
			foreach ( $server_queue as $key => $queued_server ) {
			
				$server_sub_id		= $queued_server['SUBID'];
				$response			= $queued_server['response'];
				$user_id			= $queued_server['user_id'];
				$user_email			= $queued_server['user_email'];
				$send_email			= $queued_server['send_email'];
				$domain_name		= $queued_server['domain_name'];
				$host_name			= $queued_server['host_name'];
				$host_name_domain	= $queued_server['host_name_domain'];
				$host_name_fqdn		= $queued_server['fqdn'];
				$host_name_protocol	= $queued_server['protocol'];
				$host_name_port		= $queued_server['port'];
				$site_label			= $queued_server['site_label'];
				$user_meta			= $queued_server['user_meta'];
				$plan_name			= $queued_server['plan_name'];
				$module_name		= $queued_server['module'];
				$ssh_key_name		= $queued_server['ssh_key'];
				$server_location	= $queued_server['location'];
				$region_name		= $queued_server['region_name'];
				$php_version		= "PHP 7.3";
				
				$server_module		= strtolower( str_replace( " ", "_", $module_name ) );
				
				// Run Cloud Provider completion function
				$server	= call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $queued_server, $host_name, $server_location );
				
				if ( is_array($server) && $server['completed'] ) { 
					
					$data = array(
						"plan_name"			=>	$plan_name,
						"module"			=>	$module_name,
						"host_name"			=>	$host_name,
						"host_name_domain"	=>	$host_name_domain,
						"fqdn"				=>	$host_name_fqdn,
						"protocol"			=>	$host_name_protocol,
						"port"				=>	$host_name_port,
						"server_name"		=>	$site_label,
    					"region_name"		=>	$region_name,
						"size_name"			=>	"{$server['size']} RAM, {$server['disk']}GB Disk",
						"image_name"		=> 	$server['os'],
						"ssh_key_name"		=> 	$ssh_key_name,
						"domain_name"		=>	$server['domain_name'],
						"app_user"			=>	$server['app_user'],
						"app_password"		=>	$server['app_password'],
						"ip_address"		=>  $server['ip_address'],
						"php_version"		=>	$php_version,
						"user_data"			=>	$user_meta,
					);
					
					// End of provider specific function
					$get_user_meta		= get_user_meta( $user_id );
					
					// Set-up the User Data
					$data['user_id']	= $user_id;
					$data['nickname']	= $get_user_meta['nickname'][0];
					$data['first_name']	= $get_user_meta['first_name'][0];
					$data['last_name']	= $get_user_meta['last_name'][0];
					$data['full_name']	= "{$get_user_meta['first_name'][0]} {$get_user_meta['last_name'][0]}";
					
					// Save Server Data for display in control panel
					$client_data		= get_option( 'wpcs_cloud_server_client_info' );
					$client_data		= ( is_array( $client_data ) ) ? $client_data : array();
					$client_data[$module_name][] = $data;
					update_option( 'wpcs_cloud_server_client_info', $client_data );
					
					// Send email with password info if enabled
					if ( $send_email ) {
						$to = $user_email;
						$subject = 'Your new server is ready';
						$body  = __( "Dear", "wp-cloud-server" ) . ' ' . $data['first_name'] . "\n\n";
						$body .= __( "Your new website is ready to go. The login details are;", "wp-cloud-server" ) . "\n\n";
						$body .= __( "Domain: ", "wp-cloud-server" ) . ' ' . $data['domain_name'] . "\n\n";
						$body .= __( "The login details are;", "wp-cloud-server" ) . "\n\n";
						$body .= __( "Username: ", "wp-cloud-server" ) . ' ' . $data['app_user'] . "\n\n";
						$body .= __( "Password: ", "wp-cloud-server" ) . ' ' . $data['app_password'] . "\n\n";
						$body .= __( "Thank you.", "wp-cloud-server" ) . "\r\n";			
						wp_mail( $to, $subject, $body );
					}
					
					// Remove the server from the completion queue
					unset( $server_queue[ $key ] );
					update_option( 'wpcs_cloudways_server_complete_queue', $server_queue );
					
					$debug['app_data'] = $data;
					
					update_option( 'wpcs_cloudways_new_site_data', $debug );
				}
			}
		}
	}
	
	/**
	 *  Create Cloudways License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	public static function wpcs_cloudways_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_cloudways_license_settings', 'wpcs_cloudways_module_license_key', 'wpcs_sanitize_license' );
		register_setting('wpcs_cloudways_license_settings', 'wpcs_cloudways_module_license_activate' );
	}

	function wpcs_sanitize_license( $new ) {
		$old = get_option( 'wpcs_cloudways_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_cloudways_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
}