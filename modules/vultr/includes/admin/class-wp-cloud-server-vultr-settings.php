<?php

/**
 * WP Cloud Server - Vultr Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Vultr
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Vultr_Settings {
		
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
	private static $module_name = 'Vultr';
	
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
	private static $module_desc = 'Use Vultr to create and manage new cloud servers.';

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

		add_action( 'admin_init', array( $this, 'wpcs_vultr_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_vultr_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_vultr_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_vultr_create_template_setting_sections_and_fields' ) );
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_vultr_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_vultr_update_servers' ) );					
		add_action( 'wpcs_add_module_tabs', array( $this, 'wpcs_vultr_module_tab' ), 10, 3 );
		add_action( 'wpcs_add_module_tabs_content_with_submenu', array( $this, 'wpcs_vultr_module_tab_content_with_submenu' ), 10, 3 );
		add_action( 'wpcs_add_log_page_heading_tabs', array( $this, 'wpcs_vultr_log_page_tabs' ) );
		add_action( 'wpcs_add_log_page_tabs_content', array( $this, 'wpcs_vultr_log_page_tabs_content' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_vultr_logged_data' ) );
		
		// Handle Scheduled Events
		add_action( 'wpcs_vultr_module_activate', array( $this, 'wpcs_vultr_module_activate_server_completed_queue' ) );
		add_action( 'wpcs_vultr_run_server_completed_queue', array( $this, 'wpcs_vultr_module_run_server_completed_queue' ) );
		
		add_filter( 'cron_schedules', array( $this, 'wpcs_vultr_custom_cron_schedule' ) );

		self::$api = new WP_Cloud_Server_Vultr_API();

	}
		
	/**
	 *  Add Vultr Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		self::$api_connected = self::$api->wpcs_vultr_check_api_health();
			
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
			
			wpcs_vultr_log_event( 'Vultr', 'Success', 'The Vultr Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = self::$api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update Vultr Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'Vultr' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_vultr_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_log_event( 'Vultr', 'Success', 'Vultr Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update Vultr Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_vultr_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_vultr_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_vultr_admin_menu', 'wpcs_vultr_api_token', $args );

		add_settings_section(
			'wpcs_vultr_admin_menu',
			esc_attr__( 'Vultr API Credentials', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_section_callback_vultr_api' ),
			'wpcs_vultr_admin_menu'
		);

		add_settings_field(
			'wpcs_vultr_api_token',
			esc_attr__( 'API Token:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_api_token' ),
			'wpcs_vultr_admin_menu',
			'wpcs_vultr_admin_menu'
		);

	}
		
	/**
	 *  Vultr API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_vultr_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official Vultr REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://my.vultr.com" target="_blank">Vultr Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  Vultr API Field Callback for API Token.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_api_token() {

		$value = get_option( 'wpcs_vultr_api_token' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_vultr_api_token" name="wpcs_vultr_api_token" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_create_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_vultr_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_name', $args );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_type' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_app' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_region' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_size' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_ssh_key' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_startup_script_name' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_enable_backups' );
		register_setting( 'wpcs_vultr_create_template', 'wpcs_vultr_template_host_name' );

		add_settings_section(
			'wpcs_vultr_create_template',
			esc_attr__( 'Add New Vultr Server Template', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_section_callback_vultr_create_template' ),
			'wpcs_vultr_create_template'
		);

		add_settings_field(
			'wpcs_vultr_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_template_name' ),
			'wpcs_vultr_create_template',
			'wpcs_vultr_create_template'
		);

		add_settings_field(
			'wpcs_vultr_template_host_name',
			esc_attr__( 'Server Hostname:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_template_host_name' ),
			'wpcs_vultr_create_template',
			'wpcs_vultr_create_template'
		);

		add_settings_field(
			'wpcs_vultr_template_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_template_type' ),
			'wpcs_vultr_create_template',
			'wpcs_vultr_create_template'
		);

		if ( check_vultr_pro_plugin() ) {

			add_settings_field(
				'wpcs_vultr_template_app',
				esc_attr__( 'Server App:', 'wp-cloud-server-vultr' ),
				array( $this, 'wpcs_field_callback_vultr_template_app' ),
				'wpcs_vultr_create_template',
				'wpcs_vultr_create_template'
			);

		}

		add_settings_field(
			'wpcs_vultr_template_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_template_region' ),
			'wpcs_vultr_create_template',
			'wpcs_vultr_create_template'
		);

		add_settings_field(
			'wpcs_vultr_template_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_template_size' ),
			'wpcs_vultr_create_template',
			'wpcs_vultr_create_template'
		);

	}
	
	/**
	 *  Vultr API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_vultr_create_template() {

		echo '<p>This page allows you to save \'Templates\' for use when creating Hosting Plans in \'Easy Digital Downloads\'. You can select the Image, Region, and Size, to be used when creating a new Server!</p>';

	}
	

	/**
	 *  Vultr Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_template_type() {

		$images			= wpcs_vultr_os_list();
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_vultr_template_type" id="wpcs_vultr_template_type">
			<?php
				if ( $images && is_array( $images ) ) {
					?>
    				<optgroup label="Select Image">
					<?php
					foreach ( $images as $key => $image ) {
						$options = ( check_vultr_pro_plugin() ) ? array( 'Marketplace App' ) : array( 'Marketplace App', 'Application' );
						if ( !in_array( $image['name'], $options ) ) {
							?>
    						<option value="<?php echo "{$key}|{$image['name']}"; ?>"><?php echo $image['name']; ?></option>
							<?php
						}
					}
				}
				?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Vultr Field Callback for Template App Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_vultr_template_app() {

		$apps			= wpcs_vultr_app_list();
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_vultr_template_app" id="wpcs_vultr_template_app">
			<?php
			if ( $apps && is_array( $apps ) ) {
				?>
    			<optgroup label="Select Application">
					<option value="no-application">-- No Application --</option>
					<?php
					foreach ( $apps as $key => $app ) {
						?>
    					<option value="<?php echo "{$key}|{$app['deploy_name']}"; ?>"><?php echo $app['deploy_name']; ?></option>
						<?php
					}
				}
				?>
			</optgroup>
		</select>
		<p class="text_desc" >[ Select 'Application' for Server Image above ]</p>
		<?php
	}

	/**
	 *  Vultr Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_template_name() {

		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo '<input class="w-400" type="text" placeholder="Template Name" id="wpcs_vultr_template_name" name="wpcs_vultr_template_name" value="' . esc_attr( $value ) . '"/>';
		echo '<p class="text_desc" >[ You can use any valid text, numeric, and space characters ]</p>';

	}

	/**
	 *  Vultr Field Callback for Template Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_template_host_name() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_vultr_template_host_name' );
		?>
		<select class="w-400" name="wpcs_vultr_template_host_name" id="wpcs_vultr_template_host_name">
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
	 *  Vultr Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_template_region() {
		
		$regions = wpcs_vultr_regions_list();

		$value = get_option( 'wpcs_vultr_template_region' );
		?>

		<select class="w-400" name="wpcs_vultr_template_region" id="wpcs_vultr_template_region">
			<optgroup label="Select Region">
			<?php
			if ( !empty( $regions ) ) {
				foreach ( $regions as $region ) {
				?>
    				<option value='<?php echo "{$region['DCID']}|{$region['name']}"; ?>'><?php echo $region['name']; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
			<optgroup label="User Choice">
			<?php $selected = (isset( $value ) && $value === 'userselected') ? 'selected' : '' ; ?>
				<option value="userselected|userselected" <?php echo $selected; ?>><?php esc_html_e( '-- Checkout Input Field --', 'wp-cloud-server-vultr' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Vultr Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_template_size() {

		$plans = wpcs_vultr_plans_list();

		$value = get_option( 'wpcs_vultr_template_size' );
		?>

		<select class="w-400" name="wpcs_vultr_template_size" id="wpcs_vultr_template_size">
			<optgroup label="Select Plan">
			<?php
			if ( !empty( $plans ) ) {
				foreach ( $plans as $plan ) {
					?>
    				<option value="<?php echo "{$plan['VPSPLANID']}|{$plan['vcpu_count']} CPU, {$plan['ram']}GB, {$plan['disk']}GB SSD"; ?>"><?php echo "{$plan['vcpu_count']} CPU, {$plan['ram']}GB, {$plan['disk']}GB SSD (\${$plan['price_per_month']}/month)"; ?></option>
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
	public function wpcs_vultr_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_vultr_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_name', $args );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_type' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_app' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_region' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_size' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_ssh_key' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_startup_script_name' );
		register_setting( 'wpcs_vultr_create_server', 'wpcs_vultr_server_enable_backups' );

		add_settings_section(
			'wpcs_vultr_create_server',
			esc_attr__( 'Create New Vultr Server', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_section_callback_vultr_create_server' ),
			'wpcs_vultr_create_server'
		);

		add_settings_field(
			'wpcs_vultr_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_server_name' ),
			'wpcs_vultr_create_server',
			'wpcs_vultr_create_server'
		);

		add_settings_field(
			'wpcs_vultr_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_server_type' ),
			'wpcs_vultr_create_server',
			'wpcs_vultr_create_server'
		);

		if ( check_vultr_pro_plugin() ) {

			add_settings_field(
				'wpcs_vultr_server_app',
				esc_attr__( 'Server App:', 'wp-cloud-server-vultr' ),
				array( $this, 'wpcs_field_callback_vultr_server_app' ),
				'wpcs_vultr_create_server',
				'wpcs_vultr_create_server'
			);

		}

		add_settings_field(
			'wpcs_vultr_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_server_region' ),
			'wpcs_vultr_create_server',
			'wpcs_vultr_create_server'
		);

		add_settings_field(
			'wpcs_vultr_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-vultr' ),
			array( $this, 'wpcs_field_callback_vultr_server_size' ),
			'wpcs_vultr_create_server',
			'wpcs_vultr_create_server'
		);

	}
		
	/**
	 *  Vultr API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_vultr_create_server() {

		echo '<p>';
		echo wp_kses( 'This page allows you to create a new Vultr Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-vultr' );
		echo '</p>';

	}

	/**
	 *  Vultr Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_server_type() {

		$images			= wpcs_vultr_os_list();
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_vultr_server_type" id="wpcs_vultr_server_type">
				<?php
				if ( !empty( $images ) ) {
					?>
    				<optgroup label="Select Image">
					<?php
					foreach ( $images as $key => $image ) {
						$options = ( check_vultr_pro_plugin() ) ? array( 'Marketplace App' ) : array( 'Marketplace App', 'Application' );
						if ( !in_array( $image['name'], $options ) ) {
							?>
    						<option value="<?php echo $key; ?>"><?php echo $image['name']; ?></option>
							<?php
						}
					}
				}
				?>
			</optgroup>
		</select>
		<?php
	}

	/**
	 *  Vultr Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_server_app() {

		$apps			= wpcs_vultr_app_list();
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class="w-400" name="wpcs_vultr_server_app" id="wpcs_vultr_server_app">
			<?php
			if ( !empty( $apps ) ) {
				?>
    			<optgroup label="Select Application">
					<option value="no-application">-- No Application --</option>
					<?php
					foreach ( $apps as $key => $app ) {
						?>
    					<option value="<?php echo $key; ?>"><?php echo $app['deploy_name']; ?></option>
						<?php
					}
				}
				?>
			</optgroup>
		</select>
		<p class="text_desc" >[ Select 'Application' for Server Image above ]</p>
		<?php
	}

	/**
	 *  Vultr Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_server_name() {

		echo '<input class="w-400" type="text" placeholder="server-name" id="wpcs_vultr_server_name" name="wpcs_vultr_server_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}

	/**
	 *  Vultr Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_server_region() {

		$regions = wpcs_vultr_regions_list();

		?>

		<select class="w-400" name="wpcs_vultr_server_region" id="wpcs_vultr_server_region">
			<optgroup label="Select Region">
			<?php
			if ( $regions && is_array( $regions ) ) {
				foreach ( $regions as $region ) {
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
	 *  Vultr Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_vultr_server_size() {

		$plans = wpcs_vultr_plans_list();
		?>

		<select class="w-400" name="wpcs_vultr_server_size" id="wpcs_vultr_server_size">
			<optgroup label="Select Plan">
			<?php
			if ( $plans && is_array( $plans ) ) {
				foreach ( $plans as $plan ) {
				?>
    				<option value="<?php echo $plan['VPSPLANID']; ?>"><?php echo "{$plan['vcpu_count']} CPU, {$plan['ram']}GB, {$plan['disk']}GB SSD (\${$plan['price_per_month']}/month)"; ?></option>
				<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php

	}
		
	/**
	 *  Vultr Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'Vultr' == $module_name ) || ( 'active' == $module_data['Vultr']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'Vultr' !== $module_name ) && ( 'active' == $module_data['Vultr']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'Vultr' !== $module_name ) && ( 'active' == $module_data['Vultr']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['Vultr']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=servers' ), 'vultr_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'vultr' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Vultr', 'wp-cloud-server-vultr' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  Vultr Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'vultr' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_vultr_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=servers'), 'vultr_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-vultr' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=templates'), 'vultr_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-vultr' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=addserver'), 'vultr_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-vultr' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=addtemplate'), 'vultr_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-vultr' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=settings'), 'vultr_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-vultr' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=debug'), 'vultr_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-vultr' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'vultr_settings_nonce' ) ) ) {
						delete_option( 'wpcs_vultr_api_token' );
						delete_option( 'wpcs_dismissed_vultr_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_vultr_admin_menu' );
						do_settings_sections( 'wpcs_vultr_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=vultr&submenu=settings&resetapi=true' ), 'vultr_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Vultr API Credentials', 'wp-cloud-server-vultr' ) ?></a>
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
	 *  Vultr Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['Vultr']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=vultr_logs') );?>" class="nav-tab<?php echo ( 'vultr_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Vultr', 'wp-cloud-server-vultr' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  Vultr Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_vultr_log_page_tabs_content( $active_tab ) {
		
			if ( 'vultr_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_vultr_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-vultr' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-vultr' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-vultr' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-vultr' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-vultr' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-vultr' ); ?></th>
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
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-vultr' ); ?></td>
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
	 *  Return Vultr Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_vultr_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return Vultr Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_vultr_module_api_connected() {

		if( 'active' == self::$api_connected ) {
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
	public function sanitize_vultr_template_name( $name ) {
		
		//$name = sanitize_text_field( $name );
		
		//$output = get_option( 'wpcs_vultr_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Vultr Template was Successfully Created.', 'wp-cloud-server' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server' );
			}

			add_settings_error(
				'wpcs_vultr_template_name',
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
	public function sanitize_vultr_server_name( $name ) {

		$output = get_option( 'wpcs_vultr_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, and a period (.)', 'wp-cloud-server-vultr' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New Vultr Server is being Created.', 'wp-cloud-server-vultr' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-vultr' );
			}

			add_settings_error(
				'wpcs_vultr_server_name',
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
	public function sanitize_vultr_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_vultr_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The Vultr API Token was updated.', 'wp-cloud-server-vultr' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Vultr API Token!', 'wp-cloud-server-vultr' );
			}

			add_settings_error(
				'wpcs_vultr_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return Vultr Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_vultr_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_vultr_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_vultr_logged_data', $data );
		}

	}
	
	/**
	 *  Set-up Vultr Cron Job.
	 *
	 *  @since 1.0.1
	 */
	public function  wpcs_vultr_custom_cron_schedule( $schedules ) {
    	$schedules[ 'one_minute' ] = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    return $schedules;
	}
	
	/**
	 *  Activates the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_vultr_module_activate_server_completed_queue() {

		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'wpcs_vultr_run_server_completed_queue' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'one_minute', 'wpcs_vultr_run_server_completed_queue' );
			wpcs_vultr_log_event( 'Vultr', 'Success', 'Vultr Server Queue Started' );
		}

	}
	
	/**
	 *  Run the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_vultr_module_run_server_completed_queue() {
		
		$api			= new WP_Cloud_Server_Vultr_API();
		$server_queue	= get_option( 'wpcs_vultr_server_complete_queue', array() );
		
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
				$server	= call_user_func("wpcs_{$server_module}_server_complete", $server_sub_id, $queued_server, $host_name, $server_location );
				
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
					$client_data[$module_name][]	= $data;
					update_option( 'wpcs_cloud_server_client_info', $client_data );
				
					// Reset the dismissed site creation option and set new site created option
					update_option( 'wpcs_dismissed_vultr_site_creation_notice', FALSE );
					update_option( 'wpcs_vultr_new_site_created', TRUE );
					
					// Remove the server from the completion queue
					unset( $server_queue[ $key ] );
					update_option( 'wpcs_vultr_server_complete_queue', $server_queue );
					
					$debug['app_data'] = $data;
				}
			
				update_option( 'wpcs_vultr_new_site_data', $debug );
			}
		}
	}
}