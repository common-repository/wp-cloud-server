<?php

/**
 * WP Cloud Server - Linode Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Linode
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Linode_Settings {
		
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
	private static $module_name = 'Linode';
	
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
	private static $module_desc = 'Use Linode to create and manage your Linux servers.';

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

		add_action( 'admin_init', array( $this, 'wpcs_linode_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_linode_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_linode_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_linode_create_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_linode_create_license_setting_sections_and_fields' ) );
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_linode_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_linode_update_servers' ) );					
		add_action( 'wpcs_add_module_tabs', array( $this, 'wpcs_linode_module_tab' ), 10, 3 );
		add_action( 'wpcs_add_module_tabs_content_with_submenu', array( $this, 'wpcs_linode_module_tab_content_with_submenu' ), 10, 3 );
		add_action( 'wpcs_add_log_page_heading_tabs', array( $this, 'wpcs_linode_log_page_tabs' ) );
		add_action( 'wpcs_add_log_page_tabs_content', array( $this, 'wpcs_linode_log_page_tabs_content' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_linode_logged_data' ) );

		self::$api = new WP_Cloud_Server_Linode_API();

	}
		
	/**
	 *  Add Linode Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		self::$api_connected = self::$api->wpcs_linode_check_api_health();
			
		if ( ! array_key_exists( self::$module_name, $module_data) ) {

			if ( ! isset( self::$status )) {
					self::$status = 'inactive';
			}
		
			$module_data[self::$module_name]['module_name']	= self::$module_name;
			$module_data[self::$module_name]['module_desc']=self::$module_desc;
			$module_data[self::$module_name]['status']=self::$status;
			$module_data[self::$module_name]['module_type']=self::$module_type;

			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
			
			wpcs_log_event( 'Linode', 'Success', 'The Linode Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = self::$api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update Linode Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'Linode' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_linode_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_log_event( 'Linode', 'Success', 'Linode Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update Linode Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_linode_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_linode_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_linode_admin_menu', 'wpcs_linode_api_token', $args );

		add_settings_section(
			'wpcs_linode_admin_menu',
			esc_attr__( 'Linode API Credentials', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_section_callback_linode_api' ),
			'wpcs_linode_admin_menu'
		);

		add_settings_field(
			'wpcs_linode_api_token',
			esc_attr__( 'API Token:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_api_token' ),
			'wpcs_linode_admin_menu',
			'wpcs_linode_admin_menu'
		);

	}
		
	/**
	 *  Linode API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_linode_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official Linode REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://cloud.linode.com/login" target="_blank">Linode Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  Linode API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_api_token() {

		$value = get_option( 'wpcs_linode_api_token' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_linode_api_token" name="wpcs_linode_api_token" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_linode_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_name', $args );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_host_name' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_type' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_region' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_size' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_ssh_key' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_startup_script_name' );
		register_setting( 'wpcs_linode_create_template', 'wpcs_linode_template_enable_backups' );

		add_settings_section(
			'wpcs_linode_create_template',
			esc_attr__( 'Add New Linode Server Template', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_section_callback_linode_create_template' ),
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_name' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_host_name',
			esc_attr__( 'Host Name:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_host_name' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_type' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_region' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_size' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_startup_script_name',
			esc_attr__( 'Startup Script:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_startup_script_name' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_ssh_key',
			esc_attr__( 'SSH Key:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_ssh_key' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

		add_settings_field(
			'wpcs_linode_template_enable_backups',
			esc_attr__( 'Enable Backups:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_template_enable_backups' ),
			'wpcs_linode_create_template',
			'wpcs_linode_create_template'
		);

	}
	
	/**
	 *  Linode API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_linode_create_template() {

		echo '<p style="max-width: 650px;" >This page allows you to save \'Templates\' for use when creating Hosting Plans in \'Easy Digital Downloads\'. You can select the Image, Region, and Size, to be used when creating a new Server!</p>';

	}
	

	/**
	 *  Linode Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_type() {

		$images = wpcs_linode_os_list();

		?>
		<select style="width: 400px" name="wpcs_linode_template_type" id="wpcs_linode_template_type">
			<optgroup label="Select Image">
			<?php
			if ( !empty( $images ) ) {
				foreach ( $images as $key => $image ) {
				?>
    				<option value="<?php echo "{$key}|{$image['name']}"; ?>"><?php echo $image['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Linode Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_name() {

		echo '<input style="width: 400px" type="text" placeholder="Template Name" id="wpcs_linode_template_name" name="wpcs_linode_template_name" value=""/>';
		echo '<p class="text_desc" >[ You can use any valid text, numeric, and space characters ]</p>';
	}

	/**
	 *  Vultr Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_host_name() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_linode_template_host_name' );
		?>
		<select class="w-400" name="wpcs_linode_template_host_name" id="wpcs_linode_template_host_name">
			<?php
			if ( !empty( $host_names ) ) {
				?><optgroup label="Select Hostname"><?php
				foreach ( $host_names as $key => $host_name ) {
			?>
            <option value="<?php echo "{$host_name['hostname']}|{$host_name['label']}"; ?>"><?php esc_html_e( "{$host_name['label']}", 'wp-cloud-server' ); ?></option>
			<?php } } ?>
			</optgroup>
			<optgroup label="User Choice">
				<option value="[Customer Input]|[Customer Input]"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Linode Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_region() {
		
		$regions = wpcs_linode_regions_list();

		$value = get_option( 'wpcs_linode_template_region' );
		?>

		<select style="width: 400px" name="wpcs_linode_template_region" id="wpcs_linode_template_region">
			<?php
			if ( !empty( $regions ) ) {
				?><optgroup label="Select Region"><?php
				foreach ( $regions as $region ) {
				?>
    				<option value="<?php echo "{$region['id']}|{$region['name']}"; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
			<optgroup label="User Choice">>
			<?php $selected = (isset( $value ) && $value === 'userselected') ? 'selected' : '' ; ?>
				<option value="userselected|userselected" <?php echo $selected; ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server-linode' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Linode Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_size() {

		$plans = wpcs_linode_plans_list();

		$value = get_option( 'wpcs_linode_template_size' );
		?>

		<select style="width: 400px" name="wpcs_linode_template_size" id="wpcs_linode_template_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $plan ) {
				?>
    				<option value="<?php echo "{$plan['id']}|{$plan['label']}"; ?>"><?php echo $plan['label']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  DigitalOcean Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_template_ssh_key() {

		$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select class="w-400" name="wpcs_linode_template_ssh_key" id="wpcs_linode_template_ssh_key">
			<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
			<?php
			if ( isset( $serverpilot_ssh_keys ) && is_array( $serverpilot_ssh_keys ) ) { ?>
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
	public function wpcs_field_callback_linode_template_startup_script_name() {

		$api_status				= wpcs_check_cloud_provider_api();
		$attributes				= ( $api_status ) ? '' : 'disabled';
		$startup_scripts		= get_option( 'wpcs_startup_scripts', array() );
	
		?>
		<select class="w-400" name="wpcs_linode_template_startup_script_name" id="wpcs_linode_template_startup_script_name">
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
	 *  Linode Template Field Callback for Enable Backup Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_linode_template_enable_backups() {

		$value = get_option( 'wpcs_linode_template_enable_backups' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_linode_template_enable_backups' name='wpcs_linode_template_enable_backups' value='1'/>";

	}

	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_linode_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_name', $args );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_type' );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_region' );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_size' );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_ssh_key' );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_startup_script_name' );
		register_setting( 'wpcs_linode_create_server', 'wpcs_linode_server_enable_backups' );

		add_settings_section(
			'wpcs_linode_create_server',
			esc_attr__( 'Create New Linode Server', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_section_callback_linode_create_server' ),
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_name' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_type' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_region' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_size' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_ssh_key',
			esc_attr__( 'SSH Key:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_ssh_key' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_startup_script_name',
			esc_attr__( 'Startup Script:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_startup_script_name' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);

		add_settings_field(
			'wpcs_linode_server_enable_backups',
			esc_attr__( 'Enable Backups:', 'wp-cloud-server-linode' ),
			array( $this, 'wpcs_field_callback_linode_server_enable_backups' ),
			'wpcs_linode_create_server',
			'wpcs_linode_create_server'
		);
	}
		
	/**
	 *  Linode API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_linode_create_server() {

		echo '<p class="text_desc">';
		echo wp_kses( 'This page allows you to create a new Linode Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-linode' );
		echo '</p>';

	}

	/**
	 *  Linode Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_server_type() {

		$images = wpcs_linode_os_list();

		?>
		<select style="width: 400px" name="wpcs_linode_server_type" id="wpcs_linode_server_type">
			<optgroup label="Select Image">
			<?php
			if ( !empty( $images ) ) {
				foreach ( $images as $key => $image ) {
				?>
    				<option value="<?php echo $key; ?>"><?php echo $image['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Linode Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_server_name() {

		echo '<input style="width: 400px"type="text" placeholder="server-name" id="wpcs_linode_server_name" name="wpcs_linode_server_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}

	/**
	 *  Linode Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_server_region() {

		$regions = wpcs_linode_regions_list();

		?>

		<select style="width: 400px" name="wpcs_linode_server_region" id="wpcs_linode_server_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions as $region ) {
				?>
    				<option value="<?php echo $region['id']; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Linode Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_server_size() {

		$plans = wpcs_linode_plans_list();

		?>

		<select style="width: 400px" name="wpcs_linode_server_size" id="wpcs_linode_server_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $plan ) {
				?>
    				<option value="<?php echo $plan['id']; ?>"><?php echo $plan['label']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Linode Field Callback for Template Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_linode_server_ssh_key() {

		$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select class="w-400" name="wpcs_linode_server_ssh_key" id="wpcs_linode_server_ssh_key">
			<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
			<?php
			if ( isset( $serverpilot_ssh_keys ) && is_array( $serverpilot_ssh_keys ) ) { ?>
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
	 *  Linode Field Callback for Template Startup Script option.
	 *
	 *  @since 2.1.1
	 */
	public function wpcs_field_callback_linode_server_startup_script_name() {

		$api_status				= wpcs_check_cloud_provider_api();
		$attributes				= ( $api_status ) ? '' : 'disabled';
		$startup_scripts		= get_option( 'wpcs_startup_scripts', array() );
	
		?>
		<select class="w-400" name="wpcs_linode_server_startup_script_name" id="wpcs_linode_server_startup_script_name">
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
	 *  Linode Template Field Callback for Enable Backup Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_linode_server_enable_backups() {

		$value = get_option( 'wpcs_linode_server_enable_backups' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_linode_server_enable_backups' name='wpcs_linode_server_enable_backups' value='1'/>";

	}
		
	/**
	 *  Linode Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'Linode' == $module_name ) || ( 'active' == $module_data['Linode']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'Linode' !== $module_name ) && ( 'active' == $module_data['Linode']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'Linode' !== $module_name ) && ( 'active' == $module_data['Linode']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['Linode']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=servers' ), 'linode_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'linode' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Linode', 'wp-cloud-server-linode' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  Linode Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'linode' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_linode_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=servers'), 'linode_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-linode' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=templates'), 'linode_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-linode' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=addserver'), 'linode_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-linode' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=addtemplate'), 'linode_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-linode' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=settings'), 'linode_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-linode' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=debug'), 'linode_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-linode' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'linode_settings_nonce' ) ) ) {
						delete_option( 'wpcs_linode_api_token' );
						delete_option( 'wpcs_dismissed_linode_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_linode_admin_menu' );
						do_settings_sections( 'wpcs_linode_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=linode&submenu=settings&resetapi=true' ), 'linode_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Linode API Credentials', 'wp-cloud-server-linode' ) ?></a>
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
	 *  Linode Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['Linode']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=linode_logs') );?>" class="nav-tab<?php echo ( 'linode_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Linode', 'wp-cloud-server-linode' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  Linode Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_linode_log_page_tabs_content( $active_tab ) {
		
			if ( 'linode_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_linode_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-linode' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-linode' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-linode' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-linode' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-linode' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-linode' ); ?></th>
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
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-linode' ); ?></td>
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
	 *  Return Linode Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_linode_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return Linode Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_linode_module_api_connected() {

		if( 1 == self::$api_connected ) {
			return true;
		}
		return false;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_linode_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_linode_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Linode Template was Created.', 'wp-cloud-server-linode' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-linode' );
			}

			add_settings_error(
				'wpcs_linode_template_name',
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
	public function sanitize_linode_server_name( $name ) {

		$output = get_option( 'wpcs_linode_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-zA-Z0-9.\-_]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, _, or a period (.)', 'wp-cloud-server-linode' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New Linode Server is being Created.', 'wp-cloud-server-linode' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-linode' );
			}

			add_settings_error(
				'wpcs_linode_server_name',
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
	public function sanitize_linode_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_linode_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The Linode API Token was updated.', 'wp-cloud-server-linode' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Linode API Token!', 'wp-cloud-server-linode' );
			}

			add_settings_error(
				'wpcs_linode_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return Linode Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_linode_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_linode_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_linode_logged_data', $data );
		}

	}
	
	/**
	 *  Create Linode License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	public static function wpcs_linode_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_linode_license_settings', 'wpcs_linode_module_license_key', 'wpcs_linode_sanitize_license' );
		register_setting('wpcs_linode_license_settings', 'wpcs_testlinode_module_license_activate' );
	}

	function wpcs_linode_sanitize_license( $new ) {
		$old = get_option( 'wpcs_linode_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_linode_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
}