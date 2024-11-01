<?php

/**
 * WP Cloud Server - RunCloud Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_RunCloud_Settings {
		
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
	private static $module_name = 'RunCloud';
	
	/**
	 *  Module Type
	 *
	 *  @var string
	 */
	private static $module_type = 'cloud_portal';
		
	/**
	 *  Module Description
	 *
	 *  @var string
	 */
	private static $module_desc = 'Use RunCloud to create and manage new cloud servers.';

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

		add_action( 'admin_init', array( $this, 'wpcs_runcloud_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_runcloud_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_runcloud_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_runcloud_create_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_runcloud_create_license_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_runcloud_create_app_setting_sections_and_fields' ) );
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_runcloud_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_runcloud_update_servers' ) );					
		add_action( 'wpcs_add_module_tabs', array( $this, 'wpcs_runcloud_module_tab' ), 10, 3 );
		add_action( 'wpcs_add_module_tabs_content_with_submenu', array( $this, 'wpcs_runcloud_module_tab_content_with_submenu' ), 10, 3 );
		add_action( 'wpcs_add_log_page_heading_tabs', array( $this, 'wpcs_runcloud_log_page_tabs' ) );
		add_action( 'wpcs_add_log_page_tabs_content', array( $this, 'wpcs_runcloud_log_page_tabs_content' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_runcloud_logged_data' ) );
		
		// Handle Scheduled Events
		add_action( 'wpcs_runcloud_module_activate', array( $this, 'wpcs_runcloud_module_activate_server_completed_queue' ) );
		add_action( 'wpcs_runcloud_run_server_completed_queue', array( $this, 'wpcs_runcloud_module_run_server_completed_queue' ) );
		
		add_filter( 'cron_schedules', array( $this, 'wpcs_runcloud_custom_cron_schedule' ) );

		self::$api = new WP_Cloud_Server_RunCloud_API();

	}
		
	/**
	 *  Add RunCloud Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		$this->api_connected = self::$api->wpcs_runcloud_check_api_health();
			
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
			
			wpcs_log_event( 'RunCloud', 'Success', 'The RunCloud Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = $this->api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update RunCloud Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'RunCloud' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_runcloud_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_log_event( 'RunCloud', 'Success', 'RunCloud Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update RunCloud Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_runcloud_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_runcloud_admin_menu', 'wpcs_runcloud_api_key' );
		register_setting( 'wpcs_runcloud_admin_menu', 'wpcs_runcloud_api_secret' );

		add_settings_section(
			'wpcs_runcloud_admin_menu',
			esc_attr__( 'RunCloud API Credentials', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_section_callback_runcloud_api' ),
			'wpcs_runcloud_admin_menu'
		);

		add_settings_field(
			'wpcs_runcloud_api_key',
			esc_attr__( 'API Key:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_api_key' ),
			'wpcs_runcloud_admin_menu',
			'wpcs_runcloud_admin_menu'
		);
		
		add_settings_field(
			'wpcs_runcloud_api_secret',
			esc_attr__( 'API Secret:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_api_secret' ),
			'wpcs_runcloud_admin_menu',
			'wpcs_runcloud_admin_menu'
		);

	}
		
	/**
	 *  RunCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_runcloud_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official RunCloud REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://manage.runcloud.io/auth/login" target="_blank">RunCloud Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  RunCloud API Field Callback for User Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_api_key() {

		$value = get_option( 'wpcs_runcloud_api_key' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_runcloud_api_key" name="wpcs_runcloud_api_key" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  RunCloud API Field Callback for Password.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_api_secret() {

		$value = get_option( 'wpcs_runcloud_api_secret' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_runcloud_api_secret" name="wpcs_runcloud_api_secret" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_runcloud_create_template', 'wpcs_runcloud_template_name', $args );
		register_setting( 'wpcs_runcloud_create_template', 'wpcs_runcloud_template_type' );
		register_setting( 'wpcs_runcloud_create_template', 'wpcs_runcloud_template_region' );
		register_setting( 'wpcs_runcloud_create_template', 'wpcs_runcloud_template_size' );

		add_settings_section(
			'wpcs_runcloud_create_template',
			esc_attr__( 'Add New RunCloud Server Template', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_section_callback_runcloud_create_template' ),
			'wpcs_runcloud_create_template'
		);

		add_settings_field(
			'wpcs_runcloud_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_template_name' ),
			'wpcs_runcloud_create_template',
			'wpcs_runcloud_create_template'
		);

		add_settings_field(
			'wpcs_runcloud_template_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_template_type' ),
			'wpcs_runcloud_create_template',
			'wpcs_runcloud_create_template'
		);

		add_settings_field(
			'wpcs_runcloud_template_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_template_region' ),
			'wpcs_runcloud_create_template',
			'wpcs_runcloud_create_template'
		);

		add_settings_field(
			'wpcs_runcloud_template_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_template_size' ),
			'wpcs_runcloud_create_template',
			'wpcs_runcloud_create_template'
		);

	}
	
	/**
	 *  RunCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_runcloud_create_template() {

		echo '<p style="max-width: 650px;" >This page allows you to save \'Templates\' for use when creating Hosting Plans in \'Easy Digital Downloads\'. You can select the Image, Region, and Size, to be used when creating a new Server!</p>';

	}
	

	/**
	 *  RunCloud Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_template_type() {
	
		?>
		<select class="w-400" name="wpcs_runcloud_template_type" id="wpcs_runcloud_template_type">
			<optgroup label="Select Image">
            	<option value="Ubuntu 19.10 x64"><?php esc_html_e( 'Ubuntu 19.10 x64', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="Ubuntu 19.04 x64"><?php esc_html_e( 'Ubuntu 19.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
           	 	<option value="Ubuntu 18.04 x64"><?php esc_html_e( 'Ubuntu 18.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="Ubuntu 16.04 x64"><?php esc_html_e( 'Ubuntu 16.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  RunCloud Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_template_name() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo '<input class="w-400" type="text" placeholder="Template Name" id="wpcs_runcloud_template_name" name="wpcs_runcloud_template_name" value="' . esc_attr( $value ) . '"/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}

	/**
	 *  RunCloud Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_template_region() {
		
		$regions	= wpcs_runcloud_regions_list();
		$value		= get_option( 'wpcs_runcloud_template_region' );
		?>

		<select class="w-400" name="wpcs_runcloud_template_region" id="wpcs_runcloud_template_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions as $region ) {
				print_r( $region );
				?>
    				<option value="<?php echo $region['DCID']; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
			<optgroup label="User Choice">
				<?php $selected = (isset( $value ) && $value === 'userselected') ? 'selected' : '' ; ?>
				<option value="userselected" <?php echo $selected; ?>><?php esc_html_e( '-- User Choice at Checkout --', 'wp-cloud-server-runcloud' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  RunCloud Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_template_size() {

		$plans = wpcs_runcloud_plans_list();

		$value = get_option( 'wpcs_runcloud_template_size' );
		?>

		<select class="w-400" name="wpcs_runcloud_template_size" id="wpcs_runcloud_template_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $plan ) {
				?>
    				<option value="<?php echo $plan['VPSPLANID']; ?>"><?php echo $plan['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_runcloud_create_server', 'wpcs_runcloud_server_name', $args );
		register_setting( 'wpcs_runcloud_create_server', 'wpcs_runcloud_server_module' );
		register_setting( 'wpcs_runcloud_create_server', 'wpcs_runcloud_server_type' );
		register_setting( 'wpcs_runcloud_create_server', 'wpcs_runcloud_server_region' );
		register_setting( 'wpcs_runcloud_create_server', 'wpcs_runcloud_server_size' );

		add_settings_section(
			'wpcs_runcloud_create_server',
			esc_attr__( 'Create New RunCloud Server', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_section_callback_runcloud_create_server' ),
			'wpcs_runcloud_create_server'
		);

		add_settings_field(
			'wpcs_runcloud_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_server_name' ),
			'wpcs_runcloud_create_server',
			'wpcs_runcloud_create_server'
		);
		
		add_settings_field(
			'wpcs_runcloud_server_module',
			esc_attr__( 'Cloud Provider:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_server_module' ),
			'wpcs_runcloud_create_server',
			'wpcs_runcloud_create_server'
		);

		add_settings_field(
			'wpcs_runcloud_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_server_type' ),
			'wpcs_runcloud_create_server',
			'wpcs_runcloud_create_server'
		);

		add_settings_field(
			'wpcs_runcloud_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_server_region' ),
			'wpcs_runcloud_create_server',
			'wpcs_runcloud_create_server'
		);

		add_settings_field(
			'wpcs_runcloud_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-runcloud' ),
			array( $this, 'wpcs_field_callback_runcloud_server_size' ),
			'wpcs_runcloud_create_server',
			'wpcs_runcloud_create_server'
		);

	}
		
	/**
	 *  RunCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_runcloud_create_server() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new RunCloud Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-runcloud' );
		echo '</p>';

	}

	/**
	 *  RunCloud Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_server_type() {

		$value = get_option( 'wpcs_runcloud_server_type' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_runcloud_server_type" id="wpcs_runcloud_server_type">
			<optgroup label="Select Image">
				<option value="Ubuntu 19.10 x64"><?php esc_html_e( 'Ubuntu 19.10 x64', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="Ubuntu 19.04 x64"><?php esc_html_e( 'Ubuntu 19.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="Ubuntu 18.04 x64"><?php esc_html_e( 'Ubuntu 18.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="Ubuntu 16.04 x64"><?php esc_html_e( 'Ubuntu 16.04 x64', 'wp-cloud-server-runcloud' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  RunCloud Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_server_name() {

		$value = get_option( 'wpcs_runcloud_server_name' );
		$value = ( ! empty( $value ) ) ? $value : '';

		echo '<input class="w-400" type="text" placeholder="server-name" id="wpcs_runcloud_server_name" name="wpcs_runcloud_server_name" value="' . esc_attr( $value ) . '"/>';
		echo '<p class="text_desc" >[ You can use letters, numbers, or the space character ]</p>';

	}
	
	/**
	 *  ServerPilot Server Field Callback for Module Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_server_module() {

		$api_status		= wpcs_check_cloud_provider_api('RunCloud');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_runcloud_server_module' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class="uk-select" class="w-400" name="wpcs_runcloud_server_module" id="wpcs_runcloud_server_module" <?php echo $attributes ?>>
			<optgroup label="Select Cloud Provider">
			<?php
			foreach ( $module_data as $key => $module ) { 
				if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) && ( wpcs_check_cloud_provider_api($key) ) ) {
			?>
            		<option value="<?php echo $key ?>"><?php echo $key ?></option>
			<?php 
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  RunCloud Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_server_region() {

		$regions = wpcs_runcloud_regions_list();

		?>

		<select class="w-400" name="wpcs_runcloud_server_region" id="wpcs_runcloud_server_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions as $region ) {
				print_r( $region );
				?>
    				<option value="<?php echo $region['DCID']; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  RunCloud Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_runcloud_server_size() {

		$plans = wpcs_runcloud_plans_list();

		?>

		<select class="w-400" name="wpcs_runcloud_server_size" id="wpcs_runcloud_server_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $plan ) {
				?>
    				<option value="<?php echo $plan['VPSPLANID']; ?>"><?php echo $plan['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}
		
	/**
	 *  RunCloud Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'RunCloud' == $module_name ) || ( 'active' == $module_data['RunCloud']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'RunCloud' !== $module_name ) && ( 'active' == $module_data['RunCloud']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'RunCloud' !== $module_name ) && ( 'active' == $module_data['RunCloud']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['RunCloud']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=servers' ), 'runcloud_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'runcloud' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'RunCloud', 'wp-cloud-server-runcloud' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  RunCloud Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'runcloud' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_runcloud_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=servers'), 'runcloud_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-runcloud' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=templates'), 'runcloud_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-runcloud' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=addserver'), 'runcloud_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-runcloud' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=addtemplate'), 'runcloud_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-runcloud' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=settings'), 'runcloud_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-runcloud' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=debug'), 'runcloud_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-runcloud' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'runcloud_settings_nonce' ) ) ) {
						delete_option( 'wpcs_runcloud_api_token' );
						delete_option( 'wpcs_dismissed_runcloud_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_runcloud_admin_menu' );
						do_settings_sections( 'wpcs_runcloud_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=runcloud&submenu=settings&resetapi=true' ), 'runcloud_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset RunCloud API Credentials', 'wp-cloud-server-runcloud' ) ?></a>
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
	 *  RunCloud Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['RunCloud']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=runcloud_logs') );?>" class="nav-tab<?php echo ( 'runcloud_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'RunCloud', 'wp-cloud-server-runcloud' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  RunCloud Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_runcloud_log_page_tabs_content( $active_tab ) {
		
			if ( 'runcloud_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_runcloud_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-runcloud' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-runcloud' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-runcloud' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-runcloud' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-runcloud' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-runcloud' ); ?></th>
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
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-runcloud' ); ?></td>
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
	 *  Return RunCloud Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_runcloud_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return RunCloud Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	public function wpcs_runcloud_module_api_connected() {

		return $this->api_connected;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_runcloud_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_runcloud_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New RunCloud Template was Created.', 'wp-cloud-server-runcloud' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-runcloud' );
			}

			add_settings_error(
				'wpcs_runcloud_template_name',
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
	public function sanitize_runcloud_server_name( $name ) {

		$output = get_option( 'wpcs_runcloud_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-zA-Z0-9\\s]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, or a space character', 'wp-cloud-server-runcloud' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New RunCloud Server is being Created.', 'wp-cloud-server-runcloud' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-runcloud' );
			}

			add_settings_error(
				'wpcs_runcloud_server_name',
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
	public function sanitize_runcloud_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_runcloud_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The RunCloud API Token was updated.', 'wp-cloud-server-runcloud' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid RunCloud API Token!', 'wp-cloud-server-runcloud' );
			}

			add_settings_error(
				'wpcs_runcloud_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return RunCloud Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_runcloud_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_runcloud_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_runcloud_logged_data', $data );
		}

	}
	
	/**
	 *  Set-up RunCloud Cron Job.
	 *
	 *  @since 1.0.1
	 */
	public function  wpcs_runcloud_custom_cron_schedule( $schedules ) {
    	$schedules[ 'one_minute' ] = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    return $schedules;
	}
	
	/**
	 *  Activates the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_runcloud_module_activate_server_completed_queue() {

		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'wpcs_runcloud_run_server_completed_queue' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'one_minute', 'wpcs_runcloud_run_server_completed_queue' );
			wpcs_runcloud_log_event( 'RunCloud', 'Success', 'RunCloud Server Queue Started' );
		}

	}
	
	/**
	 *  Run the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_runcloud_module_run_server_completed_queue() {
		
		$api			= new WP_Cloud_Server_RunCloud_API();
		$server_queue	= get_option( 'wpcs_runcloud_server_complete_queue', array() );
		
		if ( ! empty( $server_queue ) ) {
			
			foreach ( $server_queue as $key => $queued_server ) {
			
				$server_sub_id		= $queued_server['SUBID'];
				$response			= $queued_server['response'];
				$user_id			= $queued_server['user_id'];
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
				
				$server_module		= strtolower( str_replace( " ", "_", $module_name ) );
				
				// Run Cloud Provider completion function
				$server	= call_user_func("wpcs_{$server_module}_server_complete", $queued_server, $response, $host_name, $server_location );
				
				update_option( 'wpcs_runcloud_server_queue_response', $server );
				
				if ( is_array($server) && ( $server['completed'] ) ) { 
					
					$data = array(
						"plan_name"			=>	$plan_name,
						"module"			=>	$module_name,
						"host_name"			=>	$host_name,
						"host_name_domain"	=>	$host_name_domain,
						"fqdn"				=>	$host_name_fqdn,
						"protocol"			=>	$host_name_protocol,
						"port"				=>	$host_name_port,
						"server_name"		=>	$site_label,
    					"region_name"		=>	$server_location,
						"size_name"			=>	'',
						"image_name"		=> 	'',
						"ssh_key_name"		=> 	$ssh_key_name,
						"user_data"			=>	$user_meta,
					);
					
					// End of provider specific function
					
					$get_user_meta		= get_user_meta( $user_id );
					
					$data['user_id']	= $user_id;
					$data['nickname']	= $get_user_meta['nickname'][0];
					$data['first_name']	= $get_user_meta['first_name'][0];
					$data['last_name']	= $get_user_meta['last_name'][0];
					$data['full_name']	= "{$get_user_meta['first_name'][0]} {$get_user_meta['last_name'][0]}";
					
					// Save Server Data for display in control panel
					$client_data		= get_option( 'wpcs_cloud_server_client_info' );
					$client_data		= ( is_array( $client_data ) ) ? $client_data : array();
					$client_data[]		= $data;
					update_option( 'wpcs_cloud_server_client_info', $client_data );
				
					// Reset the dismissed site creation option and set new site created option
					update_option( 'wpcs_dismissed_runcloud_site_creation_notice', FALSE );
					update_option( 'wpcs_runcloud_new_site_created', TRUE );
					
					// Remove the server from the completion queue
					unset( $server_queue[ $key ] );
					update_option( 'wpcs_runcloud_server_complete_queue', $server_queue );
					
					$debug['app_data'] = $data;
					
					update_option( 'wpcs_runcloud_new_site_data', $debug );
				}
			}
		}
	}
	
	/**
	 *  Create RunCloud License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	public static function wpcs_runcloud_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_runcloud_license_settings', 'wpcs_runcloud_module_license_key', 'wpcs_sanitize_license' );
		register_setting('wpcs_runcloud_license_settings', 'wpcs_runcloud_module_license_activate' );
	}

	function wpcs_sanitize_license( $new ) {
		$old = get_option( 'wpcs_runcloud_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_runcloud_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
	
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_runcloud_create_app_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_create_app_name' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_server_id' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_name', $args );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_application' );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_create_app_domain' ),
            'default' => NULL,
		);		
		
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_domain' );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
		);
		
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_user' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_web_directory' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_php_version' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_default_app' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_system_user_name' );
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_system_user_password' );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_create_app_site_title' ),
            'default' => NULL,
		);		
		
		
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_create_app_stack' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_stack' );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_runcloud_create_app_stack_mode' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_runcloud_create_app', 'wpcs_runcloud_create_app_stack_mode' );

		add_settings_section(
			'wpcs_runcloud_create_app',
			esc_attr__( 'Install a New Website', 'wp-cloud-server' ),
			'',
			'wpcs_runcloud_create_app'
		);

		add_settings_field(
			'wpcs_runcloud_create_app_name',
			esc_attr__( 'Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_name' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_domain',
			esc_attr__( 'Domain:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_domain' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_application',
			esc_attr__( 'Application:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_application' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);

		add_settings_field(
			'wpcs_runcloud_create_app_server_id',
			esc_attr__( 'Server:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_server_id' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_user',
			esc_attr__( 'System User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_user' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_system_user_name',
			esc_attr__( 'New System User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_system_user_name' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);

		add_settings_field(
			'wpcs_runcloud_create_app_system_user_password',
			esc_attr__( 'New System User Password:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_system_user_password' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_web_directory',
			esc_attr__( 'Web Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_web_directory' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_php_version',
			esc_attr__( 'PHP Version:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_php_version' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_create_stack',
			esc_attr__( 'Stack:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_stack' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
		
		add_settings_field(
			'wpcs_runcloud_create_app_stack_mode',
			esc_attr__( 'Stack Mode:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_stack_mode' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);
	
		add_settings_field(
			'wpcs_runcloud_create_app_default_app',
			esc_attr__( 'Default Application:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_runcloud_create_app_default_app' ),
			'wpcs_runcloud_create_app',
			'wpcs_runcloud_create_app'
		);

		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_runcloud_create_app_field_setting' );

	}
		
	/**
	 *  ServerPilot Create App Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_runcloud_create_app() {

		echo '<p>';
		echo wp_kses( 'This page allows you to add a new WordPress Website to any connected Server. Enter the details below and then click the \'Create New Website\' button to have the new website built and online in a few minutes!', 'wp-cloud-server' );
		echo '</p>';

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_server_id() {
		
		$servers = wpcs_runcloud_server_list();
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_server_id" id="wpcs_runcloud_create_app_server_id">
			<optgroup label="Select Server">
			<?php
			if ( ! empty( $servers['data'] ) ) {
				foreach ( $servers['data'] as $key => $server ) {
					?>
					<option value="<?php echo $server['id']; ?>"><?php echo $server['name']; ?></option>
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
	 *  ServerPilot Create App Field Callback for App Name.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_name() {

		$api_status		= wpcs_check_cloud_provider_api('RunCloud', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='App Name' id='wpcs_runcloud_create_app_name' name='wpcs_runcloud_create_app_name' value='{$value}'>";
		echo '<p class="text_desc" >[You can use: lowercase a-z, 0-9, underscore (_), or hyphen (-)]</p>';

	}

	/**
	 *  ServerPilot Create App Field Callback for App Name.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_domain() {

		$api_status		= wpcs_check_cloud_provider_api('RunCloud', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='domain.com' id='wpcs_runcloud_create_app_domain' name='wpcs_runcloud_create_app_domain' value='{$value}'>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_user() {

		$sys_users = wpcs_runcloud_sys_user_list();
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_user" id="wpcs_runcloud_create_app_user">
			<?php print_r( $sys_users ); ?>
				<option value=""><?php _e( '-- Create New System User --', 'wp-cloud-server' ); ?></option>
				<?php
				if ( ! empty( $sys_users ) ) {
					?><optgroup label="Select System User"><?php
					foreach ( $sys_users[0] as $key => $user ) {
						?>
						<option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
						<?php
					}
					?></optgroup><?php
				} else {
					?>
					<option value="false"><?php _e( '-- No System Users Available --', 'wp-cloud-server' ); ?></option>
					<?php
				}
				?>
		</select>
		<?php
	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 3.0.3
	 */
	public function wpcs_field_callback_runcloud_create_app_system_user_name() {
		echo "<input class='w-400' type='text' placeholder='username' id='wpcs_runcloud_create_app_system_user_name' name='wpcs_runcloud_create_app_system_user_name' value=''>";
	}

	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 3.0.3
	 */
	public function wpcs_field_callback_runcloud_create_app_system_user_password() {
		echo "<input class='w-400' type='password' placeholder='*******' id='wpcs_runcloud_create_app_system_user_password' name='wpcs_runcloud_create_app_system_user_password' value=''>";
	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_application() {

		$sys_users = wpcs_runcloud_application_list();
		
		$git_repos = wpcs_github_repos_list();
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_application" id="wpcs_runcloud_create_app_application">
			<?php
			if ( !empty( $sys_users ) || !empty( $git_repos ) ) {
				if ( !empty( $sys_users ) ) { ?>
					<optgroup label="Select PHP Installer">
						<?php foreach ( $sys_users as $key => $user ) {
							?>
							<option value='<?php echo "{$key}|php"; ?>'><?php echo $user; ?></option>
							<?php
						}
				}
				if ( !empty( $git_repos ) ) { ?>
					<optgroup label="Select GIT Repository">
						<?php foreach ( $git_repos as $key => $git_repo ) {
							?>
							<option value='<?php echo "{$key}|git"; ?>'><?php echo $git_repo; ?></option>
							<?php
						}
				}
			} else {
				?>
				<option value="false"><?php _e( '-- No Web Applications Available --', 'wp-cloud-server' ); ?></option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_php_version() {

		$php_versions = wpcs_runcloud_php_version_list();
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_php_version" id="wpcs_runcloud_create_app_php_version">
			<optgroup label="Select PHP Version">
			<?php
			if ( ! empty( $php_versions ) ) {
				foreach ( $php_versions as $key => $php ) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $php; ?></option>
					<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_web_directory() {

		$api_status		= wpcs_check_cloud_provider_api('RunCloud', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';

		echo "<input class='w-400' type='text' placeholder='/public' id='wpcs_runcloud_create_app_web_directory' name='wpcs_runcloud_create_app_web_directory' value=''>";
		echo '<p class="text_desc" >[Leave blank for WordPress, enter /public for Laravel]</p>';
	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_stack() {

		$value = get_option( 'wpcs_runcloud_create_app_stack' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_stack" id="wpcs_runcloud_create_app_stack">
			<optgroup label="Select Stack">
				<option value="hybrid"><?php esc_html_e( 'Hybrid', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="nativenginx"><?php esc_html_e( 'Native NGINX', 'wp-cloud-server-runcloud' ); ?></option>
				<option value="customnginx"><?php esc_html_e( 'Custom NGINX', 'wp-cloud-server-runcloud' ); ?></option>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_runcloud_create_app_stack_mode() {

		$value = get_option( 'wpcs_runcloud_create_app_stack_mode' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_runcloud_create_app_stack_mode" id="wpcs_runcloud_create_app_stack_mode">
			<optgroup label="Select Stack Mode">
				<option value="production"><?php esc_html_e( 'Production', 'wp-cloud-server-runcloud' ); ?></option>
            	<option value="development"><?php esc_html_e( 'Development', 'wp-cloud-server-runcloud' ); ?></option>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Field Callback for Enable Backups Setting.
	 *
	 *  @since 2.1.3
	 */
	public function wpcs_field_callback_runcloud_create_app_default_app() {
	
		echo "<input type='checkbox' id='wpcs_runcloud_create_app_default_app' name='wpcs_runcloud_create_app_default_app' value='1'>";

	}

		/**
	 *  Sanitize Server Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_runcloud_create_app_name( $name ) {

		$output = get_option( 'wpcs_runcloud_create_app_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9\-_]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Web Application Name entered is not Valid. Please try again using lowercase a-z, 0-9, underscore (_) or hyphen (-).', 'wp-cloud-server-runcloud' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New RunCloud Web Application is being Created.', 'wp-cloud-server-runcloud' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Web Application Name!', 'wp-cloud-server-runcloud' );
			}

			add_settings_error(
				'wpcs_runcloud_create_app_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}
}