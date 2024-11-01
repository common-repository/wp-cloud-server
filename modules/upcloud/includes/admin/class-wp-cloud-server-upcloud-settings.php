<?php

/**
 * WP Cloud Server - UpCloud Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_UpCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_UpCloud_Settings {
		
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
	private static $module_name = 'UpCloud';
	
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
	private static $module_desc = 'Use UpCloud to create and manage new cloud servers.';

	/**
	 *  Instance of WPCloudServer API Class
	 *
	 *  @var resource
	 */
	private $api;

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

		add_action( 'admin_init', array( $this, 'wpcs_upcloud_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_upcloud_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_upcloud_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_upcloud_create_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_upcloud_create_license_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_upcloud_edit_template_setting_sections_and_fields' ) );			
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_upcloud_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_upcloud_update_servers' ) );					
		add_action( 'wpcs_add_module_tabs', array( $this, 'wpcs_upcloud_module_tab' ), 10, 3 );
		add_action( 'wpcs_add_module_tabs_content_with_submenu', array( $this, 'wpcs_upcloud_module_tab_content_with_submenu' ), 10, 3 );
		add_action( 'wpcs_add_log_page_heading_tabs', array( $this, 'wpcs_upcloud_log_page_tabs' ) );
		add_action( 'wpcs_add_log_page_tabs_content', array( $this, 'wpcs_upcloud_log_page_tabs_content' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_upcloud_logged_data' ) );
		
		// Handle Scheduled Events
		add_action( 'wpcs_upcloud_module_activate', array( $this, 'wpcs_upcloud_module_activate_server_completed_queue' ) );
		add_action( 'wpcs_upcloud_run_server_completed_queue', array( $this, 'wpcs_upcloud_module_run_server_completed_queue' ) );
		
		add_filter( 'cron_schedules', array( $this, 'wpcs_upcloud_custom_cron_schedule' ) );

		$this->api = new WP_Cloud_Server_UpCloud_API();

	}
		
	/**
	 *  Add UpCloud Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		$this->api_connected = $this->api->wpcs_upcloud_check_api_health();
			
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
			
			wpcs_log_event( 'UpCloud', 'Success', 'The UpCloud Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = $this->api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update UpCloud Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'UpCloud' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_upcloud_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_log_event( 'UpCloud', 'Success', 'UpCloud Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update UpCloud Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_upcloud_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_upcloud_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_upcloud_admin_menu', 'wpcs_upcloud_user_name' );
		register_setting( 'wpcs_upcloud_admin_menu', 'wpcs_upcloud_password' );

		add_settings_section(
			'wpcs_upcloud_admin_menu',
			esc_attr__( 'UpCloud API Credentials', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_section_callback_upcloud_api' ),
			'wpcs_upcloud_admin_menu'
		);

		add_settings_field(
			'wpcs_upcloud_user_name',
			esc_attr__( 'Username:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_user_name' ),
			'wpcs_upcloud_admin_menu',
			'wpcs_upcloud_admin_menu'
		);
		
		add_settings_field(
			'wpcs_upcloud_password',
			esc_attr__( 'Password:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_password' ),
			'wpcs_upcloud_admin_menu',
			'wpcs_upcloud_admin_menu'
		);

	}
		
	/**
	 *  UpCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_upcloud_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official UpCloud REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://hub.upcloud.com/login" target="_blank">UpCloud Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  UpCloud API Field Callback for User Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_user_name() {

		$value = get_option( 'wpcs_upcloud_user_name' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_upcloud_user_name" name="wpcs_upcloud_user_name" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  UpCloud API Field Callback for Password.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_password() {

		$value = get_option( 'wpcs_upcloud_password' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_upcloud_password" name="wpcs_upcloud_password" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_upcloud_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_name', $args );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_type' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_host_name' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_region' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_size' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_ssh_key' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_startup_script_name' );
		register_setting( 'wpcs_upcloud_create_template', 'wpcs_upcloud_template_enable_backups' );

		add_settings_section(
			'wpcs_upcloud_create_template',
			esc_attr__( 'Add New UpCloud Server Template', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_section_callback_upcloud_create_template' ),
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_name' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_host_name',
			esc_attr__( 'Host Name:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_host_name' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_type' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_region' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_size' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_ssh_key',
			esc_attr__( 'SSH Key:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_ssh_key' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_startup_script_name',
			esc_attr__( 'Startup Script:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_startup_script_name' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

		add_settings_field(
			'wpcs_upcloud_template_enable_backups',
			esc_attr__( 'Enable Backups:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_template_enable_backups' ),
			'wpcs_upcloud_create_template',
			'wpcs_upcloud_create_template'
		);

	}
	
	/**
	 *  UpCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_upcloud_create_template() {

		echo '<p>This page allows you to save \'Templates\' for use when creating Hosting Plans in \'Easy Digital Downloads\'. You can select the Image, Region, and Size, to be used when creating a new Server!</p>';

	}
	

	/**
	 *  UpCloud Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_type() {

		$images = wpcs_upcloud_os_list();

		?>
		<select class="w-400" name="wpcs_upcloud_template_type" id="wpcs_upcloud_template_type">
			<optgroup label="Select Image">
			<?php
			if ( !empty( $images ) ) {
				foreach ( $images as $key => $image ) {
				?>
    				<option value="<?php echo "{$key}|{$image['name']}"; ?>"><?php echo $image['name']; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Image Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  UpCloud Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_name() {

		echo '<input class="w-400" type="text" placeholder="Template Name" id="wpcs_upcloud_template_name" name="wpcs_upcloud_template_name" value=""/>';
		echo '<p class="text_desc" >[ You can use any valid text, numeric, and space characters ]</p>';

	}

	/**
	 *  UpCloud Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_region() {
		
		$regions = wpcs_upcloud_regions_list();

		?>
		<select class="w-400" name="wpcs_upcloud_template_region" id="wpcs_upcloud_template_region">
			<?php
			if ( !empty( $regions ) ) {
				?> <optgroup label="Select Region"> <?php
				foreach ( $regions as $key => $region ) {
				?>
    				<option value="<?php echo "{$key}|{$region['name']}"; ?>"><?php echo $region['name']; ?></option>
				<?php } ?>
				</optgroup>
				<optgroup label="User Choice">
					<option value="userselected|userselected"><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server-upcloud' ); ?></option>
				</optgroup>
				<?php
			} else {
				?>
    				<option value="">-- No Region Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  UpCloud Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_size() {

		$plans = wpcs_upcloud_plans_list();
		?>
		<select class="w-400" name="wpcs_upcloud_template_size" id="wpcs_upcloud_template_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $key => $plan ) {
				?>
    				<option value="<?php echo "{$key}|{$plan['name']}"; ?>"><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Plan Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  UpCloud Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_host_name() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_upcloud_template_host_name' );
		?>
		<select class="w-400" name="wpcs_upcloud_template_host_name" id="wpcs_upcloud_template_host_name">
			<optgroup label="Select Hostname">
			<?php
			if ( !empty( $host_names ) ) {
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
	 *  UpCloud Field Callback for SSH Key Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_ssh_key() {

		$ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
		?>
		<select class="w-400" name="wpcs_upcloud_template_ssh_key" id="wpcs_upcloud_template_ssh_key">
			<option value="no-ssh-key"><?php esc_html_e( '-- No SSH Key --', 'wp-cloud-server' ); ?></option>
				<?php if ( !empty( $ssh_keys ) ) { ?>
					<optgroup label="Select SSH Key">
						<?php foreach ( $ssh_keys as $key => $ssh_key ) {
							echo "<option value='{$ssh_key['name']}'>{$ssh_key['name']}</option>";
						} ?>
					</optgroup>
				<?php } ?>
				</select>
		<?php
	}

	/**
	 *  UpCloud Field Callback for Startup Script Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_startup_script_name() {

		$startup_scripts = get_option( 'wpcs_startup_scripts' );
		?>
		<select class="w-400" name="wpcs_upcloud_template_startup_script_name" id="wpcs_upcloud_template_startup_script_name">
			<option value="no-startup-script"><?php esc_html_e( '-- No Startup Script --', 'wp-cloud-server' ); ?></option>
				<?php
				if ( !empty( $startup_scripts ) ) { ?>
					<optgroup label="Select Startup Script">
								
					<?php foreach ( $startup_scripts as $key => $script ) {
						echo "<option value='{$script['name']}'>{$script['name']}</option>";
					} ?>
					</optgroup>
				<?php	
				}
				?>
		</select>
	<?php
	}

	/**
	 *  UpCloud Field Callback for Startup Script Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_template_enable_backups() {

		echo "<input type='checkbox' id='wpcs_upcloud_template_enable_backups' name='wpcs_upcloud_template_enable_backups' value='1'>";

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_edit_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_upcloud_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_name', $args );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_type' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_host_name' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_region' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_size' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_ssh_key' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_startup_script_name' );
		register_setting( 'wpcs_upcloud_edit_template', 'wpcs_upcloud_edit_template_enable_backups' );
		
	}

	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_upcloud_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_name', $args );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_type' );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_region' );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_size' );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_ssh_key' );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_startup_script_name' );
		register_setting( 'wpcs_upcloud_create_server', 'wpcs_upcloud_server_enable_backups' );

		add_settings_section(
			'wpcs_upcloud_create_server',
			esc_attr__( 'Create New UpCloud Server', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_section_callback_upcloud_create_server' ),
			'wpcs_upcloud_create_server'
		);

		add_settings_field(
			'wpcs_upcloud_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_server_name' ),
			'wpcs_upcloud_create_server',
			'wpcs_upcloud_create_server'
		);

		add_settings_field(
			'wpcs_upcloud_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_server_type' ),
			'wpcs_upcloud_create_server',
			'wpcs_upcloud_create_server'
		);

		add_settings_field(
			'wpcs_upcloud_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_server_region' ),
			'wpcs_upcloud_create_server',
			'wpcs_upcloud_create_server'
		);

		add_settings_field(
			'wpcs_upcloud_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-upcloud' ),
			array( $this, 'wpcs_field_callback_upcloud_server_size' ),
			'wpcs_upcloud_create_server',
			'wpcs_upcloud_create_server'
		);

	}

	/**
	 *  UpCloud API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_upcloud_create_server() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new UpCloud Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-upcloud' );
		echo '</p>';

	}
		
	/**
	 *  UpCloud Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_server_type() {

		$images = wpcs_upcloud_os_list();

		?>
		<select class="w-400" name="wpcs_upcloud_server_type" id="wpcs_upcloud_server_type">
			<optgroup label="Select Image">
			<?php
			if ( !empty( $images ) ) {
				foreach ( $images as $key => $image ) {
				?>
    				<option value="<?php echo $key; ?>"><?php echo $image['name']; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Image Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  UpCloud Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_server_name() {

		echo '<input class="w-400" type="text" placeholder="server-name" id="wpcs_upcloud_server_name" name="wpcs_upcloud_server_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}

	/**
	 *  UpCloud Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_server_region() {

		$regions = wpcs_upcloud_regions_list();

		?>
		<select class="w-400" name="wpcs_upcloud_server_region" id="wpcs_upcloud_server_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions as $key => $region ) {
				?>
    				<option value="<?php echo $key; ?>"><?php echo $region['name']; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Region Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  UpCloud Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_upcloud_server_size() {

		$plans = wpcs_upcloud_plans_list();
		?>
		<select class="w-400" name="wpcs_upcloud_server_size" id="wpcs_upcloud_server_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $key => $plan ) {
				?>
    				<option value="<?php echo $key; ?>"><?php echo "{$plan['name']} {$plan['cost']}"; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="">-- No Plan Information Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php
	}
		
	/**
	 *  UpCloud Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'UpCloud' == $module_name ) || ( 'active' == $module_data['UpCloud']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'UpCloud' !== $module_name ) && ( 'active' == $module_data['UpCloud']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'UpCloud' !== $module_name ) && ( 'active' == $module_data['UpCloud']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['UpCloud']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=servers' ), 'upcloud_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'upcloud' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'UpCloud', 'wp-cloud-server-upcloud' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  UpCloud Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'upcloud' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_upcloud_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=servers'), 'upcloud_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-upcloud' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=templates'), 'upcloud_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-upcloud' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=addserver'), 'upcloud_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-upcloud' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=addtemplate'), 'upcloud_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-upcloud' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=settings'), 'upcloud_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-upcloud' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=debug'), 'upcloud_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-upcloud' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'upcloud_settings_nonce' ) ) ) {
						delete_option( 'wpcs_upcloud_api_token' );
						delete_option( 'wpcs_dismissed_upcloud_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_upcloud_admin_menu' );
						do_settings_sections( 'wpcs_upcloud_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=upcloud&submenu=settings&resetapi=true' ), 'upcloud_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset UpCloud API Credentials', 'wp-cloud-server-upcloud' ) ?></a>
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
	 *  UpCloud Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['UpCloud']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=upcloud_logs') );?>" class="nav-tab<?php echo ( 'upcloud_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'UpCloud', 'wp-cloud-server-upcloud' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  UpCloud Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_upcloud_log_page_tabs_content( $active_tab ) {
		
			if ( 'upcloud_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_upcloud_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-upcloud' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-upcloud' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-upcloud' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-upcloud' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-upcloud' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-upcloud' ); ?></th>
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
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-upcloud' ); ?></td>
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
	 *  Return UpCloud Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_upcloud_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return UpCloud Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	function wpcs_upcloud_module_api_connected() {

		return $this->api_connected;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_upcloud_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_upcloud_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New UpCloud Template was Created.', 'wp-cloud-server-upcloud' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-upcloud' );
			}

			add_settings_error(
				'wpcs_upcloud_template_name',
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
	public function sanitize_upcloud_server_name( $name ) {

		$output = get_option( 'wpcs_upcloud_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, and a period (.)', 'wp-cloud-server-upcloud' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New UpCloud Server is being Created.', 'wp-cloud-server-upcloud' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-upcloud' );
			}

			add_settings_error(
				'wpcs_upcloud_server_name',
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
	public function sanitize_upcloud_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_upcloud_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The UpCloud API Token was updated.', 'wp-cloud-server-upcloud' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid UpCloud API Token!', 'wp-cloud-server-upcloud' );
			}

			add_settings_error(
				'wpcs_upcloud_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return UpCloud Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_upcloud_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_upcloud_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_upcloud_logged_data', $data );
		}

	}
	
	/**
	 *  Set-up UpCloud Cron Job.
	 *
	 *  @since 1.0.1
	 */
	public function  wpcs_upcloud_custom_cron_schedule( $schedules ) {
    	$schedules[ 'one_minute' ] = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    return $schedules;
	}
	
	/**
	 *  Activates the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_upcloud_module_activate_server_completed_queue() {

		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'wpcs_upcloud_run_server_completed_queue' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'one_minute', 'wpcs_upcloud_run_server_completed_queue' );
			wpcs_upcloud_log_event( 'UpCloud', 'Success', 'UpCloud Server Queue Started' );
		}

	}
	
	/**
	 *  Run the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_upcloud_module_run_server_completed_queue() {
		
		$api			= new WP_Cloud_Server_UpCloud_API();
		$server_queue	= get_option( 'wpcs_upcloud_server_complete_queue', array() );
		
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
				$server	= call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $response, $host_name, $server_location );
				
				if ( is_array($server) ) { 
					
					$data = array(
						"plan_name"			=>	$plan_name,
						"module"			=>	$module_name,
						"host_name"			=>	$host_name,
						"host_name_domain"	=>	$host_name_domain,
						"fqdn"				=>	$host_name_fqdn,
						"protocol"			=>	$host_name_protocol,
						"port"				=>	$host_name_port,
						"server_name"		=>	$site_label,
    					"region_name"		=>	$server['location'],
						"size_name"			=>	"{$server['ram']},{$server['disk']}",
						"image_name"		=> 	$server['os'],
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
					$client_data	= get_option( 'wpcs_cloud_server_client_info' );
					$client_data	= ( is_array( $client_data ) ) ? $client_data : array();
					$client_data[]	= $data;
					update_option( 'wpcs_cloud_server_client_info', $client_data );
				
					// Reset the dismissed site creation option and set new site created option
					update_option( 'wpcs_dismissed_upcloud_site_creation_notice', FALSE );
					update_option( 'wpcs_upcloud_new_site_created', TRUE );
					
					// Remove the server from the completion queue
					unset( $server_queue[ $key ] );
					update_option( 'wpcs_upcloud_server_complete_queue', $server_queue );
					
					$debug['app_data'] = $data;
				}
			
				update_option( 'wpcs_upcloud_new_site_data', $debug );
			}
		}
	}
	
	/**
	 *  Create UpCloud License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	public static function wpcs_upcloud_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_upcloud_license_settings', 'wpcs_upcloud_module_license_key', 'wpcs_sanitize_license' );
		register_setting('wpcs_upcloud_license_settings', 'wpcs_upcloud_module_license_activate' );
	}

	function wpcs_sanitize_license( $new ) {
		$old = get_option( 'wpcs_upcloud_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_upcloud_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
}