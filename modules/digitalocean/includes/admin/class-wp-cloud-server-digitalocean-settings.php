<?php
/**
 * The Settings functionality for the DigitalOcean Module.
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_DigitalOcean_Settings {
		
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
	private static $module_name = 'DigitalOcean';
	
	/**
	 *  Module Type
	 *
	 *  @var string
	 */
	private static $module_type = 'cloud_provider';
		
	/**
	 *  Module Description
	 *
	 *  @var string
	 */
	private static $module_desc = 'Use DigitalOcean to create and manage new cloud servers.';

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
	private static $api_connected;

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {
 
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_ssh_key_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_create_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_edit_template_setting_sections_and_fields' ) );

		//add_action( 'wpcs_digitalocean_module_activate', array( $this, 'wpcs_digitalocean_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_digitalocean_add_module' ) );
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_digitalocean_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_digitalocean_update_servers' ) );

		self::$api = new WP_Cloud_Server_DigitalOcean_API();

	}
		
	/**
	 *  Add DigitalOcean Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_add_module() {

		$module_data = get_option( 'wpcs_module_list', array() );

		self::$api_connected = ( ( self::$api->wpcs_digitalocean_check_api_health() ) && ( self::$api->wpcs_digitalocean_check_api_setting() ));

		if ( ! array_key_exists( self::$module_name, $module_data) ) {

			if ( ! isset( self::$status )) {
				self::$status = 'inactive';
			}

			$module_data[self::$module_name]['module_name']	= self::$module_name;
			$module_data[self::$module_name]['module_desc']	= self::$module_desc;
			$module_data[self::$module_name]['status']		= self::$status;
			$module_data[self::$module_name]['module_type']	= self::$module_type;

			$module_data[ self::$module_name ]['servers']	= array();
			
			$templates		= get_option( 'wpcs_template_data_backup' );
			$template_data	= ( !empty( $templates[ self::$module_name ]['templates'] ) ) ? $templates[ self::$module_name ]['templates'] : array();
			$module_data[ self::$module_name ]['templates']	= $template_data;
			
			$api_token	= get_option( 'wpcs_digitalocean_api_token' );
			$data		= array( 'user' => $api_token );
			$response	= self::$api->call_api( 'droplets', $data, false, 900, 'GET' );

			if ( !empty( $response ) ) {
				//update_option( 'wpcs_digitalocean_server_attached', true );
			}
			//update_option( 'wpcs_module_list', $module_data );
			
			wpcs_digitalocean_log_event( 'DigitalOcean', 'Success', 'The DigitalOcean Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = self::$api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
		}

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update DigitalOcean Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_update_module_status( $module_name, $new_status ) {
			
		if ( 'DigitalOcean' === $module_name ) {

			if ( self::$status !== $new_status ) {
				
				$module_data = get_option( 'wpcs_module_list' );

				self::$status = $new_status;
				
				$module_data[$module_name]['status'] = $new_status;
				update_option( 'wpcs_module_list', $module_data );

				$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
				wpcs_digitalocean_log_event( 'DigitalOcean', 'Success', 'DigitalOcean Module ' . $message . ' Successfully' );

			}
		}
	}
		
	/**
	 *  Update DigitalOcean Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_digitalocean_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_digitalocean_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_digitalocean_admin_menu', 'wpcs_digitalocean_api_token', $args );

		add_settings_section(
			'wpcs_digitalocean_admin_menu',
			esc_attr__( 'DigitalOcean API Credentials', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_digitalocean_api' ),
			'wpcs_digitalocean_admin_menu'
		);

		add_settings_field(
			'wpcs_digitalocean_api_token',
			esc_attr__( 'API Token:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_api_token' ),
			'wpcs_digitalocean_admin_menu',
			'wpcs_digitalocean_admin_menu'
		);

	}
		
	/**
	 *  DigitalOcean API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_digitalocean_api() {

		echo '<p>';
		echo 'The WP Cloud Server plugin uses the official DigitalOcean REST API. Log-in to the <a class="uk-link" href="https://cloud.digitalocean.com/login" target="_blank">DigitalOcean Dashboard</a>, navigate to the API section, generate, then copy and paste the API Token below;';
		echo '</p>';

	}

	/**
	 *  DigitalOcean API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_api_token() {

		$value = get_option( 'wpcs_digitalocean_api_token' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_digitalocean_api_token" name="wpcs_digitalocean_api_token" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}

	/**
	 *  DigitalOcean API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_ssh_key_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_digitalocean_ssh_key' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_digitalocean_ssh_key', 'wpcs_digitalocean_ssh_key', $args );
		register_setting( 'wpcs_digitalocean_ssh_key', 'wpcs_digitalocean_ssh_key_name' );

		add_settings_section(
			'wpcs_digitalocean_ssh_key',
			esc_attr__( 'Add New SSH Key', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_digitalocean_ssh_key' ),
			'wpcs_digitalocean_ssh_key'
		);
		
		add_settings_field(
			'wpcs_digitalocean_ssh_key_name',
			esc_attr__( 'Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_ssh_key_name' ),
			'wpcs_digitalocean_ssh_key',
			'wpcs_digitalocean_ssh_key'
		);

		add_settings_field(
			'wpcs_digitalocean_ssh_key',
			esc_attr__( 'Public SSH Key:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_ssh_key' ),
			'wpcs_digitalocean_ssh_key',
			'wpcs_digitalocean_ssh_key'
		);

	}
		
	/**
	 *  WP-CLI SSH Key Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_digitalocean_ssh_key() {

		echo '<p>Enter your Public SSH Key below. It will then be uploaded to new Cloud Servers allowing for easy access via SSH.';

	}
	
	/**
	 *  DigitalOcean Field Callback for Template Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_ssh_key_name() {
		
		$api_status		= wpcs_check_cloud_provider_api('DigitalOcean');
		$attributes		= ( $api_status ) ? '' : 'disabled';

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 25rem;' type='text' placeholder='Enter Name for SSH Key' id='wpcs_digitalocean_ssh_key_name' name='wpcs_digitalocean_ssh_key_name' value='{$value}' {$attributes}/>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}

	/**
	 *  WP-CLI SSH Key Field Callback for API Key.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_digitalocean_ssh_key() {
		
		$api_status		= wpcs_check_cloud_provider_api('DigitalOcean');
		$attributes		= ( $api_status ) ? '' : 'disabled';

		$value = get_option( 'wpcs_digitalocean_ssh_key' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' readonly' : '';
		
		echo "<textarea class='uk-textarea' name='wpcs_digitalocean_ssh_key' placeholder='Enter SSH Key ...' rows='7' style='width:100%;' {$attributes}></textarea><br />";

	}

	/**
	 *  Register setting sections and fields for Add Template Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_digitalocean_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_name', $args );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_host_name' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_ssh_key' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_type' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_region' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_size' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_startup_script_name' );
		register_setting( 'wpcs_digitalocean_create_template', 'wpcs_digitalocean_template_enable_backups' );

		add_settings_section(
			'wpcs_digitalocean_create_template',
			esc_attr__( 'Create New Cloud Server Template', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_digitalocean_create_template' ),
			'wpcs_digitalocean_create_template'
		);
		
		add_settings_field(
			'wpcs_digitalocean_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_name' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);
		
		add_settings_field(
			'wpcs_digitalocean_template_host_name',
			esc_attr__( 'Template Host Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_host_name' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);

		add_settings_field(
			'wpcs_digitalocean_template_type',
			esc_attr__( 'Template Image:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_type' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);

		add_settings_field(
			'wpcs_digitalocean_template_region',
			esc_attr__( 'Template Region:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_region' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);

		add_settings_field(
			'wpcs_digitalocean_template_size',
			esc_attr__( 'Template Size:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_size' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);
		
		add_settings_field(
			'wpcs_digitalocean_template_ssh_key',
			esc_attr__( 'SSH Key:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_ssh_key' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);
		
		add_settings_field(
			'wpcs_digitalocean_template_startup_script_name',
			esc_attr__( 'Startup Script:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_startup_script_name' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);
		
		add_settings_field(
			'wpcs_digitalocean_template_enable_backups',
			esc_attr__( 'Enable Server Backups:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_template_enable_backups' ),
			'wpcs_digitalocean_create_template',
			'wpcs_digitalocean_create_template'
		);
		
	}
	
	/**
	 *  Register setting sections and fields for Add Template Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_edit_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_digitalocean_edit_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_name' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_host_name' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_ssh_key' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_type' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_region' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_size' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_startup_script_name' );
		register_setting( 'wpcs_digitalocean_edit_template', 'wpcs_digitalocean_edit_template_enable_backups' );

		add_settings_section(
			'wpcs_digitalocean_edit_template',
			'',
			'',
			'wpcs_digitalocean_edit_template'
		);
	}
	
	/**
	 *  ServerPilot Server Field Callback for Plan Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_digitalocean_template_module() {

		$value 			= get_option( 'wpcs_digitalocean_template_module' );
		$module_data	= get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		?>
		<select class="w-400" name="wpcs_digitalocean_template_module" id="wpcs_digitalocean_template_module">
			<optgroup label="Cloud Provider">
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
	 *  ServerPilot Server Field Callback for Shared Hosting Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_shared_hosting() {

		$value = get_option( 'wpcs_serverpilot_template_shared_hosting' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_serverpilot_template_shared_hosting' name='wpcs_serverpilot_template_shared_hosting' value='1'/>";

	}
	
	/**
	 *  ServerPilot Template Field Callback for AutoSSL Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_autossl() {

		$value = get_option( 'wpcs_serverpilot_template_autossl' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_serverpilot_template_autossl' name='wpcs_serverpilot_template_autossl' value='1'/>";

	}
	
	/**
	 *  DigitalOcean Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_serverpilot_template_connect_server() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input type='checkbox' id='wpcs_serverpilot_template_connect_server' name='wpcs_serverpilot_template_connect_server' value='1'/>";

	}
	
	/**
	 *  DigitalOcean Create Template Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_digitalocean_create_template() {

		echo "<p>Create a new 'Cloud Server Template' for use when creating new Hosting Plans . You can enter a template name, select the Image, Region, Size, and SSH Key. Finally, click 'Create Template' to save your new template!</p>";

	}

	/**
	 *  DigitalOcean Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_type() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_digitalocean_template_type' );
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_digitalocean_template_type" id="wpcs_digitalocean_template_type">
			<optgroup label="Ubuntu">
				<option value="Ubuntu 20.04 x64|ubuntu-20-04-x64"><?php esc_html_e( 'Ubuntu 20.04 x64', 'wp-cloud-server' ); ?></option>
           	 	<option value="Ubuntu 18.04 x64|ubuntu-18-04-x64"><?php esc_html_e( 'Ubuntu 18.04 x64', 'wp-cloud-server' ); ?></option>
           	 	<option value="Ubuntu 16.04 x64|ubuntu-16-04-x64"><?php esc_html_e( 'Ubuntu 16.04 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Debian">
            	<option value="Debian 10 x64|debian-10-x64"><?php esc_html_e( 'Debian 10 x64', 'wp-cloud-server' ); ?></option>
            	<option value="Debian 9 x64|debian-9-x64"><?php esc_html_e( 'Debian 9 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Centos">
            	<option value="CentOS 8 x64|centos-8-x64"><?php esc_html_e( 'CentOS 8 x64', 'wp-cloud-server' ); ?></option>
            	<option value="CentOS 7 x64|centos-7-x64"><?php esc_html_e( 'CentOS 7 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
				<optgroup label="Fedora">
            	<option value="Fedora 32 x64|fedora-32-x64"><?php esc_html_e( 'Fedora 32 x64', 'wp-cloud-server' ); ?></option>
            	<option value="Fedora 31 x64|fedora-31-x64"><?php esc_html_e( 'Fedora 31 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  DigitalOcean Field Callback for Template Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_name() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='Template Name' id='wpcs_digitalocean_template_name' name='wpcs_digitalocean_template_name' value='{$value}'>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}
	
	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_host_name() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_digitalocean_template_host_name' );
		?>
		<select class="w-400" name="wpcs_digitalocean_template_host_name" id="wpcs_digitalocean_template_host_name">
			<?php
			if ( !empty( $host_names ) ) {
				?><optgroup label="Select Hostname"><?php
				foreach ( $host_names as $key => $host_name ) {
			?>
            <option value='<?php echo "{$host_name['hostname']}|{$host_name['label']}" ?>'><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
			<?php } } ?>
			</optgroup>
			<optgroup label="User Choice">
			<option value="[Customer Input]|[Customer Input]"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  DigitalOcean Field Callback for Template Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_region() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_digitalocean_template_region' );
		?>
		<select class="w-400" name="wpcs_digitalocean_template_region" id="wpcs_digitalocean_template_region">
			<optgroup label="Select Region">
            <option value="Amsterdam|ams"><?php esc_html_e( 'Amsterdam', 'wp-cloud-server' ); ?></option>
            <option value="Bangalore|blr"><?php esc_html_e( 'Bangalore', 'wp-cloud-server' ); ?></option>
            <option value="Frankfurt|fra"><?php esc_html_e( 'Frankfurt', 'wp-cloud-server' ); ?></option>
            <option value="London|lon"><?php esc_html_e( 'London', 'wp-cloud-server' ); ?></option>
            <option value="New York|nyc"><?php esc_html_e( 'New York', 'wp-cloud-server' ); ?></option>
            <option value="San Francisco|sfo"><?php esc_html_e( 'San Francisco', 'wp-cloud-server' ); ?></option>
            <option value="Singapore|sgp"><?php esc_html_e( 'Singapore', 'wp-cloud-server' ); ?></option>
            <option value="Toronto|tor"><?php esc_html_e( 'Toronto', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="User Choice">
			<option value="userselected|userselected"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  DigitalOcean Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_size() {

		$plans = call_user_func("wpcs_digitalocean_plans_list");
		?>

		<select class='w-400' name="wpcs_digitalocean_template_size" id="wpcs_digitalocean_template_size">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $key => $type ){ ?>
					<optgroup label='<?php echo $key ?>'>";
            		<?php foreach ( $type as $key => $plan ){
						$value = "{$plan['name']}|{$key}";
						?>
    					<option value="<?php echo $value; ?>"><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
						<?php
					}
				}
			}
			?>
		</select>
		<?php

	}
	
	/**
	 *  DigitalOcean Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_template_ssh_key() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select class="w-400" name="wpcs_digitalocean_template_ssh_key" id="wpcs_digitalocean_template_ssh_key">
			<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
			<?php
			if ( $serverpilot_ssh_keys ) { ?>
				<optgroup label="Select SSH Key">
				<?php foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) {
					?>
            		<option value='<?php echo $ssh_key['name']; ?>'><?php echo $ssh_key['name']; ?></option>
			<?php
				} ?>
				</optgroup>
			<?php }
			?>
		</select>
		<?php

	}
	
	/**
	 *  DigitalOcean Field Callback for Template Startup Script option.
	 *
	 *  @since 2.1.1
	 */
	public function wpcs_field_callback_digitalocean_template_startup_script_name() {

		$api_status				= wpcs_check_cloud_provider_api();
		$attributes				= ( $api_status ) ? '' : 'disabled';
		$startup_scripts		= get_option( 'wpcs_startup_scripts', array() );
	
		?>
		<select class="w-400" name="wpcs_digitalocean_template_startup_script_name" id="wpcs_digitalocean_template_startup_script_name">
			<option value="no-startup-script">-- No Startup Script --</option>
			<optgroup label="Select Startup Script">

			<?php
			if ( ! empty( $startup_scripts ) ) {
				foreach ( $startup_scripts as $key => $script ) {
					?>
            		<option value='<?php echo $script['name']; ?>'><?php echo $script['name']; ?></option>
			<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Server Field Callback for Shared Hosting Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_digitalocean_template_enable_backups() {

		$value = get_option( 'wpcs_digitalocean_template_enable_backups' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_digitalocean_template_enable_backups' name='wpcs_digitalocean_template_enable_backups' value='1'/>";

	}
	
	
	/**
	 *  Register setting sections and fields for Create Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_digitalocean_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_digitalocean_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_name', $args );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_type' );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_region' );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_size' );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_ssh_key' );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_startup_script_name' );
		register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_enable_backups' );

		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_domain_name' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_wp_site_title' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_wp_db_user' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_wp_database' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_admin_user' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_admin_passwd' );
		//register_setting( 'wpcs_digitalocean_create_server', 'wpcs_digitalocean_server_variable_admin_email' );



		add_settings_section(
			'wpcs_digitalocean_create_server',
			esc_attr__( 'Create New DigitalOcean Droplet', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_digitalocean_create_server' ),
			'wpcs_digitalocean_create_server'
		);

		add_settings_field(
			'wpcs_digitalocean_server_name',
			esc_attr__( 'Host Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_server_name' ),
			'wpcs_digitalocean_create_server',
			'wpcs_digitalocean_create_server'
		);

		add_settings_field(
			'wpcs_digitalocean_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_server_type' ),
			'wpcs_digitalocean_create_server',
			'wpcs_digitalocean_create_server'
		);

		add_settings_field(
			'wpcs_digitalocean_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_server_region' ),
			'wpcs_digitalocean_create_server',
			'wpcs_digitalocean_create_server'
		);

		add_settings_field(
			'wpcs_digitalocean_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_digitalocean_server_size' ),
			'wpcs_digitalocean_create_server',
			'wpcs_digitalocean_create_server'
		);
		
	}
	
	/**
	 *  ServerPilot Server Field Callback for Plan Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_plan() {

		$value = get_option( 'wpcs_serverpilot_server_plan' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class="uk-select" name="wpcs_serverpilot_server_plan" id="wpcs_serverpilot_server_plan">
            <option value="economy"><?php esc_html_e( 'Economy', 'wp-cloud-server' ); ?></option>
            <option value="business"><?php esc_html_e( 'Business', 'wp-cloud-server' ); ?></option>
			<option value="first_class"><?php esc_html_e( 'First Class', 'wp-cloud-server' ); ?></option>
		</select>
		<?php

	}
	
	/**
	 *  DigitalOcean Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_serverpilot_server_ssh_key() {

		$value			= get_option( 'wpcs_serverpilot_server_ssh_key' );
		$module_data	= get_option( 'wpcs_module_list' );
		
		$ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select class="uk-select" name="wpcs_serverpilot_server_ssh_key" id="wpcs_serverpilot_server_ssh_key">
			<?php
			if ( !empty( $ssh_keys ) ) {
					foreach ( $ssh_keys as $key => $ssh_key ) {
            			echo "<option value='{$ssh_key['name']}'>{$ssh_key['name']}</option>";
					}
			}
			?>
			<option value="no-ssh-key"><?php esc_html_e( 'No SSH Key', 'wp-cloud-server' ); ?></option>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Server Field Callback for Shared Hosting Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_shared_hosting() {

		$value = get_option( 'wpcs_serverpilot_server_shared_hosting' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input class='uk-checkbox' type='checkbox' id='wpcs_serverpilot_server_shared_hosting' name='wpcs_serverpilot_server_shared_hosting' value='1'/>";

	}
	
	/**
	 *  ServerPilot Template Field Callback for AutoSSL Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_autossl() {

		$value = get_option( 'wpcs_serverpilot_server_autossl' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input class='uk-checkbox' type='checkbox' id='wpcs_serverpilot_server_autossl' name='wpcs_serverpilot_server_autossl' value='1'/>";

	}
	
	/**
	 *  DigitalOcean Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_serverpilot_connect_server() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='uk-checkbox' type='checkbox' id='wpcs_serverpilot_connect_server' name='wpcs_serverpilot_connect_server' value='1'/>";

	}
		
	/**
	 *  DigitalOcean Create Server Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_digitalocean_create_server() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new cloud server. You can enter the server name, then select the image, region, size, SSH Key, startup script, and enable server backups. Finally click \'Create Server \' to create your New Server!', 'wp-cloud-server' );
		echo '</p>';

	}

	/**
	 *  DigitalOcean Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_type() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		?>
		<select style="width: 25rem;" name="wpcs_digitalocean_server_type" id="wpcs_digitalocean_server_type">
			<optgroup label="Ubuntu">
			<option value="Ubuntu 20.04 x64|ubuntu-20-04-x64"><?php esc_html_e( 'Ubuntu 20.04 x64', 'wp-cloud-server' ); ?></option>
            <option value="Ubuntu 18.04 x64|ubuntu-18-04-x64"><?php esc_html_e( 'Ubuntu 18.04 x64', 'wp-cloud-server' ); ?></option>
            <option value="Ubuntu 16.04 x64|ubuntu-16-04-x64"><?php esc_html_e( 'Ubuntu 16.04 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Debian">
            <option value="Debian 10 x64|debian-10-x64"><?php esc_html_e( 'Debian 10 x64', 'wp-cloud-server' ); ?></option>
            <option value="Debian 9 x64|debian-9-x64"><?php esc_html_e( 'Debian 9 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Centos">
            <option value="CentOS 8 x64|centos-8-x64"><?php esc_html_e( 'CentOS 8 x64', 'wp-cloud-server' ); ?></option>
            <option value="CentOS 7 x64|centos-7-x64"><?php esc_html_e( 'CentOS 7 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
			<optgroup label="Fedora">
            <option value="Fedora 32 x64|fedora-32-x64"><?php esc_html_e( 'Fedora 32 x64', 'wp-cloud-server' ); ?></option>
            <option value="Fedora 31 x64|fedora-31-x64"><?php esc_html_e( 'Fedora 31 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  DigitalOcean Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_name() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 25rem;' type='text' placeholder='host-name' id='wpcs_digitalocean_server_name' name='wpcs_digitalocean_server_name' value='{$value}'>";
		echo '<p class="text_desc" >[ You can use: a-z, 0-9, -, and a period (.) ]</p>';

	}
	
	/**
	 *  DigitalOcean Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_cloud_provider() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_digitalocean_server_type' );
		$module_data	= get_option( 'wpcs_module_list' );
		
				?>
		<select style='width: 25rem;' name="wpcs_digitalocean_server_cloud_provider" id="wpcs_digitalocean_server_cloud_provider">
			<optgroup label="Cloud Providers">
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
	 *  DigitalOcean Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_region() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_digitalocean_server_region' );
		?>
		<select style="width: 25rem;" name="wpcs_digitalocean_server_region" id="wpcs_digitalocean_server_region">
			<optgroup label="Select Region">
			<option value="Amsterdam|ams"><?php esc_html_e( 'Amsterdam', 'wp-cloud-server' ); ?></option>
            <option value="Bangalore|blr"><?php esc_html_e( 'Bangalore', 'wp-cloud-server' ); ?></option>
            <option value="Frankfurt|fra"><?php esc_html_e( 'Frankfurt', 'wp-cloud-server' ); ?></option>
            <option value="London|lon"><?php esc_html_e( 'London', 'wp-cloud-server' ); ?></option>
            <option value="New York|nyc"><?php esc_html_e( 'New York', 'wp-cloud-server' ); ?></option>
            <option value="San Francisco|sfo"><?php esc_html_e( 'San Francisco', 'wp-cloud-server' ); ?></option>
            <option value="Singapore|sgp"><?php esc_html_e( 'Singapore', 'wp-cloud-server' ); ?></option>
            <option value="Toronto|tor"><?php esc_html_e( 'Toronto', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  DigitalOcean Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_size() {

		$plans = call_user_func("wpcs_digitalocean_plans_list");
		$value = get_option( 'wpcs_digitalocean_server_size' );
		
		?>

		<select class='w-400' name="wpcs_digitalocean_server_size" id="wpcs_digitalocean_server_size">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $key => $type ){ ?>
					<optgroup label='<?php echo $key ?>'>";
            		<?php foreach ( $type as $key => $plan ){
						$value = "{$plan['name']}|{$key}";
						?>
    					<option value="<?php echo $value; ?>"><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
						<?php
					}
				}
			}
			?>
		</select>
		<?php
	}
	
	/**
	 *  DigitalOcean Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_digitalocean_server_ssh_key() {

		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_digitalocean_server_ssh_key' );
		$module_data	= get_option( 'wpcs_module_list' );
		
		$response = call_user_func( "wpcs_digitalocean_cloud_server_api", null, 'account/keys', null, false, 0, 'GET', false, 'ssh_key_creation' );
	
		?>
		<select style='width: 25rem;' name="wpcs_digitalocean_server_ssh_key" id="wpcs_digitalocean_server_ssh_key">
			<?php if ( !empty( $response['ssh_keys'] ) ) { ?>
				<optgroup label="User SSH Keys">
					<?php foreach ( $response['ssh_keys'] as $key => $ssh_key ) {
            			echo "<option value='{$ssh_key['id']}|{$ssh_key['name']}'>{$ssh_key['name']}</option>";
					} ?>
				</optgroup>
			<?php } ?>
			<option value="no-ssh-key"><?php esc_html_e( 'No SSH Key (Default Root Password)', 'wp-cloud-server' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 *  DigitalOcean Field Callback for Enable Backups Setting.
	 *
	 *  @since 2.1.3
	 */
	public function wpcs_field_callback_digitalocean_server_enable_backups() {

		$value = get_option( 'wpcs_digitalocean_server_enable_backups' );
		$module_data = get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		echo "<input type='checkbox' id='wpcs_digitalocean_server_enable_backups' name='wpcs_digitalocean_server_enable_backups' value='1'>";

	}
			
	/**
	 *  Return true if DigitalOcean Module is Active.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_digitalocean_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return true if DigitalOcean Module API is Active.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_digitalocean_module_api_connected() {

		if( 'active' == self::$api_connected ) {
			return true;
		}
		return false;

	}
	
	/**
	 *  Return true if DigitalOcean Module API is Active. 
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_digitalocean_module_set_api_connected() {

		$api_connected = ( ( self::$api->wpcs_digitalocean_check_api_health() ) && ( self::$api->wpcs_digitalocean_check_api_setting() ) );
		
		return $api_connected;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_digitalocean_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_digitalocean_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Server Template was Successfully Created.', 'wp-cloud-server' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server' );
			}

			add_settings_error(
				'wpcs_digitalocean_template_name',
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
	public function sanitize_digitalocean_server_name( $name ) {

		$output = get_option( 'wpcs_digitalocean_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		if ( '' !== $name ) {
			$lc_name  = strtolower( $name );
			$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
			if ( $invalid ) {

				$type = 'error';
				$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, 0-9, - or a period (.)', 'wp-cloud-server' );
	
			} else {
				$output = $name;
				$type = 'updated';
				$message = __( 'Your New Cloud Server is being Created', 'wp-cloud-server' );
	
			}
		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server' );
		}

			add_settings_error(
				'wpcs_digitalocean_server_name',
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
	 *  @param  string  $token original api token
	 *  @return string  checked token
	 */
	public function sanitize_digitalocean_api_token( $token ) {
		
		$output = get_option( 'wpcs_digitalocean_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_token = sanitize_text_field( $token );

		if ( '' !== $new_token ) {
			
			$output = $new_token;
			$type = 'updated';
			$message = __( 'The DigitalOcean API Token was updated.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid DigitalOcean API Token!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_digitalocean_api_token',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Sanitize WP-CLI SSH Key
	 *
	 *  @since  1.1.0
	 *  @param  string  $key original server name
	 *  @return string  checked ssh key value
	 */
	public function sanitize_digitalocean_ssh_key( $token ) {
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		//$new_token = sanitize_text_field( $token, true );
		$new_token = $token;

		$output = get_option( 'wpcs_digitalocean_ssh_key', '' );

		if ( '' !== $new_token ) {
			
			$output = $new_token;
			$type = 'updated';
			$message = __( 'Your Public SSH Key was updated.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid SSH Key!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_digitalocean_ssh_key',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
}