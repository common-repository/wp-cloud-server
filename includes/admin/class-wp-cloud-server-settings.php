<?php
/**
 * The Settings functionality of the Plugin.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Settings {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_debug_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_server_shortcodes_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_website_shortcode_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_update_uninstall_data_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_ssh_key_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_startup_script_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_update_startup_script_sections_and_fields' ) );
				
	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_debug_setting_sections_and_fields() {
		
		$args = array(
            'type' => 'boolean', 
            'sanitize_callback' => array( $this, 'sanitize_enable_debug_setting' ),
            'default' => NULL,
		);

		register_setting( 'wp_cloud_server_general_settings', 'wpcs_enable_debug_mode', $args );
		
		$args = array(
            'type' => 'boolean', 
            'sanitize_callback' => array( $this, 'sanitize_delete_data_setting' ),
            'default' => NULL,
		);
		
		register_setting( 'wp_cloud_server_log_settings', 'wpcs_delete_logged_data', $args );
			
		$args = array(
            'type' => 'boolean', 
            'sanitize_callback' => array( $this, 'sanitize_display_support_menu_setting' ),
            'default' => 1,
		);
		
		register_setting( 'wp_cloud_server_menu_settings', 'wpcs_menu_display_support_menu', $args );
		
		add_settings_section(
			'wp_cloud_server_general_settings',
			esc_attr__( 'Debug Mode', 'wp-cloud-server' ),
			array( $this, 'section_callback_general_settings' ),
			'wp_cloud_server_general_settings'
		);

		add_settings_field(
			'wpcs_enable_debug_mode',
			esc_attr__( 'Debug Mode', 'wp-cloud-server' ),
			array( $this, 'field_callback_enable_debug_mode' ),
			'wp_cloud_server_general_settings',
			'wp_cloud_server_general_settings'
		);

		add_settings_section(
			'wp_cloud_server_log_settings',
			esc_attr__( 'Delete Logged Data', 'wp-cloud-server' ),
			array( $this, 'section_callback_log_settings' ),
			'wp_cloud_server_log_settings'
		);

		add_settings_field(
			'wpcs_delete_logged_data',
			esc_attr__( 'Delete Logged Data', 'wp-cloud-server' ),
			array( $this, 'field_callback_delete_data' ),
			'wp_cloud_server_log_settings',
			'wp_cloud_server_log_settings'
		);
		
		add_settings_section(
			'wp_cloud_server_menu_settings',
			esc_attr__( 'Menu Settings', 'wp-cloud-server' ),
			array( $this, 'section_callback_menu_settings' ),
			'wp_cloud_server_menu_settings'
		);

		add_settings_field(
			'wpcs_menu_display_support_menu',
			esc_attr__( 'Display Support Menus', 'wp-cloud-server' ),
			array( $this, 'field_callback_display_support_menu' ),
			'wp_cloud_server_menu_settings',
			'wp_cloud_server_menu_settings'
		);
	}
	
	/**
	 *  General Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_general_settings() {
		echo '<p>';
		echo wp_kses( "Enabling Debug Mode adds 'Debug' tabs to the module pages in the 'Module Overview' section, to allow the visibility of API responses, etc .", "wp-cloud-server" );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_enable_debug_mode() {
 		echo '<input name="wpcs_enable_debug_mode" id="wpcs_enable_debug_mode" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'wpcs_enable_debug_mode' ), false ) . ' />';
 	}
	
	/**
	 *  Log Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_menu_settings() {
		echo '<p>';
		echo wp_kses( 'If you have been using the plugin for a while now and don\'t need the support menus. Then unchecking the checkbox will hide the support menus and declutter your menus', 'wp-cloud-server' );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_display_support_menu() {
 		echo '<input name="wpcs_menu_display_support_menu" id="wpcs_menu_display_support_menu" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'wpcs_menu_display_support_menu' ), false ) . ' />';
 	}
	
	/**
	 *  Log Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_log_settings() {
		echo '<p>';
		echo wp_kses( 'If the logged data page is getting too long then check the checkbox below, then click save to reset the logged data.', 'wp-cloud-server' );
		echo '</p>';
	}

	/**
	 *  Delete Logged Data Field Callback.
	 *
	 *  @since 1.0.0
	 */
	function field_callback_delete_data() {
 		echo '<input name="wpcs_delete_logged_data" id="wpcs_delete_logged_data" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'wpcs_delete_logged_data' ), 0 ) . ' />';
 	}
	
	/**
	 *  Managed Server SSH Key Setting.
	 *
	 *  @since 2.0.0
	 */
	public function wpcs_ssh_key_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_ssh_key' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_ssh_key', 'wpcs_ssh_key', $args );
		register_setting( 'wpcs_ssh_key', 'wpcs_ssh_key_name' );

		add_settings_section(
			'wpcs_ssh_key',
			esc_attr__( 'SSH Key Credentials', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_ssh_key' ),
			'wpcs_ssh_key'
		);
		
		add_settings_field(
			'wpcs_ssh_key_name',
			esc_attr__( 'Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ssh_key_name' ),
			'wpcs_ssh_key',
			'wpcs_ssh_key'
		);

		add_settings_field(
			'wpcs_ssh_key',
			esc_attr__( 'Public SSH Key:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ssh_key' ),
			'wpcs_ssh_key',
			'wpcs_ssh_key'
		);

	}
		
	/**
	 *  SSH Key Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_ssh_key() {

		echo '<p>Enter your Public SSH Key below. It will then be saved for selection when creating new servers.</p>';

	}
	
	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ssh_key_name() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='Enter Name for SSH Key' id='wpcs_ssh_key_name' name='wpcs_ssh_key_name' value=''>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}

	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ssh_key() {
		
		echo '<textarea class="w-400" name="wpcs_ssh_key" placeholder="Enter SSH Key ..." rows="7"></textarea><br />';

	}
	
	/**
	 *  Sanitize SSH Key
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_ssh_key( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		//$new_token = sanitize_text_field( $token, true );
		$new_token = $token;

		$output = get_option( 'wpcs_ssh_key', '' );

		if ( '' !== $new_token ) {
			
			$output = $new_token;
			$type = 'updated';
			$message = __( 'Your Public SSH Key was updated.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid SSH Key!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_ssh_key',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Managed Server SSH Key Setting.
	 *
	 *  @since 2.0.0
	 */
	public function wpcs_startup_script_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_startup_script' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_startup_script', 'wpcs_startup_script', $args );
		register_setting( 'wpcs_startup_script', 'wpcs_startup_script_name' );
		register_setting( 'wpcs_startup_script', 'wpcs_startup_script_summary' );

		add_settings_section(
			'wpcs_startup_script',
			esc_attr__( 'Startup Scripts', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_startup_script' ),
			'wpcs_startup_script'
		);
		
		add_settings_field(
			'wpcs_startup_script_name',
			esc_attr__( 'Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_startup_script_name' ),
			'wpcs_startup_script',
			'wpcs_startup_script'
		);
		
		add_settings_field(
			'wpcs_startup_script_summary',
			esc_attr__( 'Summary:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_startup_script_summary' ),
			'wpcs_startup_script',
			'wpcs_startup_script'
		);

		add_settings_field(
			'wpcs_startup_script',
			esc_attr__( 'Startup Script:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_startup_script' ),
			'wpcs_startup_script',
			'wpcs_startup_script'
		);

	}
		
	/**
	 *  SSH Key Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_startup_script() {

		echo '<p>Enter a name for your startup script and then the script in the text area. It will then be saved for selection when creating new servers.</p>';

	}
	
	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 2.1.1
	 */
	public function wpcs_field_callback_startup_script_name() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='Enter Name for Startup Script' id='wpcs_startup_script_name' name='wpcs_startup_script_name' value=''>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}
	
	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 2.1.1
	 */
	public function wpcs_field_callback_startup_script_summary() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='Enter short description' id='wpcs_startup_script_summary' name='wpcs_startup_script_summary' value=''>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}

	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 2.1.1
	 */
	public function wpcs_field_callback_startup_script() {
		
		echo '<textarea class="w-400" name="wpcs_startup_script" placeholder="Enter Startup Script ..." rows="15"></textarea><br />';

	}
	
	/**
	 *  Managed Server SSH Key Setting.
	 *
	 *  @since 2.0.0
	 */
	public function wpcs_update_startup_script_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_startup_script' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_update_startup_script', 'wpcs_update_startup_script', $args );
		register_setting( 'wpcs_update_startup_script', 'wpcs_update_startup_script_name' );
		register_setting( 'wpcs_update_startup_script', 'wpcs_update_startup_script_summary' );
		
		add_settings_section(
			'wpcs_update_startup_script',
			esc_attr__( 'Startup Scripts', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_update_startup_script' ),
			'wpcs_update_startup_script'
		);

	}
	
	/**
	 *  SSH Key Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_update_startup_script() {

		echo '<p>View and edit your start up script below.</p>';

	}
	
	/**
	 *  Sanitize User Startup Script
	 *
	 *  @since  2.1.1
	 *  @param  string  $token original startup script
	 *  @return string  checked startup script
	 */
	public function sanitize_startup_script( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_token = $token;

		$output = get_option( 'wpcs_startup_script', '' );

		if ( '' !== $new_token ) {
			
			$output = $new_token;
			$type = 'updated';
			$message = __( 'Your startup script was saved.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a startup script!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_startup_script',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Sanitize Enable Debug Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_enable_debug_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( 'Your setting was updated.', 'wp-cloud-server' );

		add_settings_error(
			'wpcs_enable_debug_mode',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Sanitize Enable Debug Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_display_support_menu_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( 'Your setting was updated.', 'wp-cloud-server' );

		add_settings_error(
			'wpcs_menu_display_support_menu',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Sanitize Delete Data Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_delete_data_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( "The Logged Event Data was Successfully Deleted.", 'wp-cloud-server' );

		add_settings_error(
			'wpcs_delete_logged_data',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_logged_data() {

		$request_delete = get_option( 'wpcs_delete_logged_data' );
		
		$data = array();
		
		if ( $request_delete == 1 ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_digitalocean_logged_data', $data );
			update_option( 'wpcs_serverpilot_logged_data', $data );
		
			
			// Allow add-on modules to add functionality triggered by the log reset event
			do_action( 'wpcs_reset_logged_data', $data );
			
			// Reset the Delete Logged Data checkboxes
			update_option( 'wpcs_delete_logged_data', '0' );
		}

	}
	
	/**
	 *  Register Server Shortcode setting sections and fields.
	 *
	 *  @since 2.1.0
	 */
	public function wpcs_server_shortcodes_setting_sections_and_fields() {
		
		$args = array(
            'type' => 'boolean', 
            'sanitize_callback' => array( $this, 'sanitize_server_shortcodes_setting' ),
            'default' => NULL,
		);
		
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_plan_name', $args );
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_host_name' );
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_host_name_fqdn' );
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_region_name' );
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_size_name' );
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_image_name' );		
		register_setting( 'wp_cloud_server_shortcodes_settings', 'wpcs_server_shortcodes_login_url' );
		
		add_settings_section(
			'wp_cloud_server_shortcodes_settings',
			esc_attr__( 'Client Servers Shortcode', 'wp-cloud-server' ),
			array( $this, 'section_callback_server_shortcodes_settings' ),
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_plan_name',
			esc_attr__( 'Plan Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_plan_name' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_host_name',
			esc_attr__( 'Host Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_host_name' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_host_name_fqdn',
			esc_attr__( 'Host Name (FQDN):', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_host_name_fqdn' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_region_name',
			esc_attr__( 'Region Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_region_name' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_size_name',
			esc_attr__( 'Size Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_size_name' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_image_name',
			esc_attr__( 'Image Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_image_name' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
		
		add_settings_field(
			'wpcs_server_shortcodes_login_url',
			esc_attr__( 'Server Login Link:', 'wp-cloud-server' ),
			array( $this, 'field_callback_shortcodes_login_url' ),
			'wp_cloud_server_shortcodes_settings',
			'wp_cloud_server_shortcodes_settings'
		);
	}
	
	/**
	 *  General Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_server_shortcodes_settings() {
		echo '<p>';
		echo wp_kses( "The client server shortcode displays a table containing server details for the customer. This config page allows the required data to be selected. Once configured embed the shortcode [client_servers] in to the appropriate page.", "wp-cloud-server" );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_plan_name() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['plan_name'] ) ) ? $value['contents']['plan_name'] : false;
 		echo '<input name="wpcs_server_shortcodes_plan_name" id="wpcs_server_shortcodes_plan_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_cloud_provider() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['cloud_provider'] ) ) ? $value['contents']['cloud_provider'] : false;
 		echo '<input name="wpcs_server_shortcodes_cloud_provider" id="wpcs_server_shortcodes_cloud_provider" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_host_name() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['host_name'] ) ) ? $value['contents']['host_name'] : false;
 		echo '<input name="wpcs_server_shortcodes_host_name" id="wpcs_server_shortcodes_host_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_host_name_fqdn() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['fqdn'] ) ) ? $value['contents']['fqdn'] : false;
 		echo '<input name="wpcs_server_shortcodes_host_name_fqdn" id="wpcs_server_shortcodes_host_name_fqdn" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_region_name() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['region_name'] ) ) ? $value['contents']['region_name'] : false;
 		echo '<input name="wpcs_server_shortcodes_region_name" id="wpcs_server_shortcodes_region_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_size_name() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['size_name'] ) ) ? $value['contents']['size_name'] : false;
 		echo '<input name="wpcs_server_shortcodes_size_name" id="wpcs_server_shortcodes_size_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_shortcodes_image_name() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['image_name'] ) ) ? $value['contents']['image_name'] : false;
 		echo '<input name="wpcs_server_shortcodes_image_name" id="wpcs_server_shortcodes_image_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Field Callback for Template SSH Key Name.
	 *
	 *  @since 2.1.1
	 */
	public function field_callback_shortcodes_login_url() {
		$value = get_option( 'wpcs_server_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['login_url'] ) ) ? $value['contents']['login_url'] : false;
		echo '<input name="wpcs_server_shortcodes_login_url" id="wpcs_server_shortcodes_login_url" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';

	}
	
	/**
	 *  Register Server Shortcode setting sections and fields.
	 *
	 *  @since 2.1.0
	 */
	public function wpcs_website_shortcode_setting_sections_and_fields() {
		
		$args = array(
            'type' => 'boolean', 
            'sanitize_callback' => array( $this, 'sanitize_website_shortcodes_setting' ),
            'default' => NULL,
		);
		
		register_setting( 'wp_cloud_website_shortcode_settings', 'wpcs_website_shortcode_plan_name', $args );
		register_setting( 'wp_cloud_website_shortcode_settings', 'wpcs_website_shortcode_domain_name' );
		register_setting( 'wp_cloud_website_shortcode_settings', 'wpcs_website_shortcode_ip_address' );
		register_setting( 'wp_cloud_website_shortcode_settings', 'wpcs_website_shortcode_php_version' );
		
		add_settings_section(
			'wp_cloud_website_shortcode_settings',
			esc_attr__( 'Client Website Shortcode', 'wp-cloud-server' ),
			array( $this, 'section_callback_website_shortcode_settings' ),
			'wp_cloud_website_shortcode_settings'
		);
		
		add_settings_field(
			'wpcs_website_shortcode_plan_name',
			esc_attr__( 'Plan Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_website_shortcode_plan_name' ),
			'wp_cloud_website_shortcode_settings',
			'wp_cloud_website_shortcode_settings'
		);
		
		add_settings_field(
			'wpcs_website_shortcode_domain_name',
			esc_attr__( 'Domain Name:', 'wp-cloud-server' ),
			array( $this, 'field_callback_website_shortcode_domain_name' ),
			'wp_cloud_website_shortcode_settings',
			'wp_cloud_website_shortcode_settings'
		);
		
		add_settings_field(
			'wpcs_website_shortcode_ip_address',
			esc_attr__( 'IP Address:', 'wp-cloud-server' ),
			array( $this, 'field_callback_website_shortcode_ip_address' ),
			'wp_cloud_website_shortcode_settings',
			'wp_cloud_website_shortcode_settings'
		);
		
		add_settings_field(
			'wpcs_website_shortcode_php_version',
			esc_attr__( 'PHP Version:', 'wp-cloud-server' ),
			array( $this, 'field_callback_website_shortcode_php_version' ),
			'wp_cloud_website_shortcode_settings',
			'wp_cloud_website_shortcode_settings'
		);
	}
	
	/**
	 *  General Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_website_shortcode_settings() {
		echo '<p>';
		echo wp_kses( "The client website shortcode displays a table containing server details for the customer. This config page allows the required data to be selected. Once configured embed the shortcode [client_websites] in to the appropriate page.", "wp-cloud-server" );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_website_shortcode_plan_name() {
		$value = get_option( 'wpcs_website_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['plan_name'] ) ) ? $value['contents']['plan_name'] : false;
 		echo '<input name="wpcs_website_shortcode_plan_name" id="wpcs_website_shortcode_plan_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_website_shortcode_domain_name() {
		$value = get_option( 'wpcs_website_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['domain_name'] ) ) ? $value['contents']['domain_name'] : false;
 		echo '<input name="wpcs_website_shortcode_domain_name" id="wpcs_server_shortcodes_domain_name" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_website_shortcode_ip_address() {
		$value = get_option( 'wpcs_website_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['ip_address'] ) ) ? $value['contents']['ip_address'] : false;
 		echo '<input name="wpcs_website_shortcode_ip_address" id="wpcs_website_shortcode_ip_address" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_website_shortcode_php_version() {
		$value = get_option( 'wpcs_website_shortcodes_enabled_data' );
		$value = ( isset( $value['contents']['php_version'] ) ) ? $value['contents']['php_version'] : false;
 		echo '<input name="wpcs_website_shortcode_php_version" id="wpcs_website_shortcode_php_version" type="checkbox" value="1" class="code" ' . checked( 1, $value, false ) . ' />';
 	}

	/**
	 *  Managed Server SSH Key Setting.
	 *
	 *  @since 2.0.0
	 */
	public function wpcs_update_uninstall_data_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_display_uninstall_data_confirmed_setting' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_uninstall_data', 'wpcs_uninstall_data_confirmed', $args );
		
		add_settings_section(
			'wpcs_uninstall_data',
			esc_attr__( 'Uninstall Data', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_uninstall_data' ),
			'wpcs_uninstall_data'
		);
		
		add_settings_field(
			'wpcs_uninstall_data_confirmed',
			esc_attr__( 'Remove Data on Uninstall?', 'wp-cloud-server' ),
			array( $this, 'field_callback_uninstall_data_confirmed' ),
			'wpcs_uninstall_data',
			'wpcs_uninstall_data'
		);

	}
	
	/**
	 *  General Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_uninstall_data() {
		echo '<p>';
		echo wp_kses( "The WP Cloud Server plugin handles a lot of local data that aids the management of cloud providers and services. Deleting plugins normally deletes all data. Instead we provide the option to keep the data! Change the setting below if you want to delete all data on uninstall.", "wp-cloud-server" );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_uninstall_data_confirmed() {
 		echo '<input name="wpcs_uninstall_data_confirmed" id="_uninstall_data_confirmed" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'wpcs_uninstall_data_confirmed' ), false ) . ' />';
 	}
	
/**
	 *  Sanitize Enable Debug Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_display_uninstall_data_confirmed_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( 'Your setting was updated.', 'wp-cloud-server' );

		add_settings_error(
			'wpcs_uninstall_data_confirmed',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
/**
	 *  Sanitize Enable Debug Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_server_shortcodes_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( 'Your Server Shortcodes setting was updated.', 'wp-cloud-server' );

		add_settings_error(
			'wpcs_server_shortcodes_plan_name',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
/**
	 *  Sanitize Enable Debug Setting
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_website_shortcodes_setting( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$output = $token;
		$type = 'updated';
		$message = __( 'Your Website Shortcode was updated.', 'wp-cloud-server' );

		add_settings_error(
			'wpcs_website_shortcode_plan_name',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}

}