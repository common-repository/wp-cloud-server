<?php
/**
 * The Settings functionality for the ServerPilot Module.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_ServerPilot_Settings {
		
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
	private static $module_name = 'ServerPilot';
	
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
	private static $module_desc = 'Use ServerPilot to create and manage your WordPress websites.';

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

		//add_action( 'wpcs_serverpilot_module_activate', array( $this, 'wpcs_sp_add_module' ) );
		add_action( 'admin_init', array( $this, 'wpcs_sp_add_module' ) );
		
		add_action( 'admin_init', array( $this, 'wpcs_sp_api_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_serverpilot_create_app_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_serverpilot_add_template_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_serverpilot_create_server_setting_sections_and_fields' ) );
		add_action( 'admin_init', array( $this, 'wpcs_serverpilot_edit_template_setting_sections_and_fields' ) );

		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_serverpilot_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_sp_update_servers' ) );
		add_action( 'wpcs_add_module_overview_menu_items', array( $this, 'wpcs_serverpilot_module_overview_menu_items' ), 10, 3 );
		add_action( 'wpcs_add_module_overview_menu_content', array( $this, 'wpcs_serverpilot_module_overview_menu_content' ), 10, 3 );
				
		self::$api = new WP_Cloud_Server_ServerPilot_API();

	}
		
	/**
	 *  Add ServerPilot Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_add_module() {

		$module_data = get_option( 'wpcs_module_list', array() );

		self::$api_connected = self::$api->wpcs_serverpilot_check_api_health();
		self::$api_connected = ( ( self::$api->wpcs_serverpilot_check_api_health() ) && ( self::$api->wpcs_serverpilot_check_api_setting() ));
			
		if ( ! array_key_exists( self::$module_name, $module_data ) ) {

			if ( ! isset( self::$status ) ) {
					self::$status = 'inactive';					
			}
				
			$module_data[ self::$module_name ]['module_name']	= self::$module_name;
			$module_data[ self::$module_name ]['module_desc']	= self::$module_desc;
			$module_data[ self::$module_name ]['status']		= self::$status;
			$module_data[ self::$module_name ]['module_type']	= self::$module_type;

			$module_data[ self::$module_name ]['servers']		= array();
			
			$templates		= get_option( 'wpcs_template_data_backup' );
			$template_data	= ( !empty( $templates[ self::$module_name ]['templates'] ) ) ? $templates[ self::$module_name ]['templates'] : array();
			$module_data[ self::$module_name ]['templates']	= $template_data;
			
			update_option( 'wpcs_module_list', $module_data );
			
			wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'The ServerPilot Module was Successfully Activated!' );
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
	 *  Return true if ServerPilot Module API is Active.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_serverpilot_module_api_connected() {

		if( 1 == self::$api_connected ) {
			return true;
		}
		return false;

	}
		
	/**
	 *  Update ServerPilot Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_serverpilot_update_module_status( $module_name, $new_status ) {
			
		if ( 'ServerPilot' === $module_name ) {

			if ( self::$status !== $new_status ) {

				$module_data = get_option( 'wpcs_module_list' );

				self::$status = $new_status;
				
				$module_data[$module_name]['status'] = $new_status;
				update_option( 'wpcs_module_list', $module_data );

				$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
				wpcs_serverpilot_log_event( 'ServerPilot', 'Success', 'ServerPilot Module ' . $message . ' Successfully' );

			}
		}
	}
		
	/**
	 *  Add ServerPilot Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_sp_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
				
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_account_id' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_sp_admin_menu', 'wpcs_sp_api_account_id', $args );

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_api_key' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_sp_admin_menu', 'wpcs_sp_api_key', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => NULL,
            'default' => NULL,
            );
		
		register_setting( 'wpcs_sp_admin_menu', 'wpcs_sp_api_redirect_enable', $args );

		add_settings_section(
			'wpcs_sp_admin_menu',
			esc_attr__( 'ServerPilot API Credentials', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_sp_api' ),
			'wpcs_sp_admin_menu'
		);

		add_settings_field(
			'wpcs_sp_api_account_id',
			esc_attr__( 'Account ID:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_sp_api_account_id' ),
			'wpcs_sp_admin_menu',
			'wpcs_sp_admin_menu'
		);

		add_settings_field(
			'wpcs_sp_api_key',
			esc_attr__( 'API Key:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_sp_api_key' ),
			'wpcs_sp_admin_menu',
			'wpcs_sp_admin_menu'
		);

	}
		
	/**
	 *  ServerPilot API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_sp_api() {

		echo '<p>';
		echo 'The WP Cloud Server plugin uses the official ServerPilot REST API. Log-in to the <a class="uk-link" href="https://manage.serverpilot.io/login" target="_blank">ServerPilot Dashboard</a>, navigate to the API section, generate, then copy and paste the API credentials below;';
		echo '</p>';

	}

	/**
	 *  ServerPilot API Field Callback for AccountID.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_sp_api_account_id() {

		$value = get_option( 'wpcs_sp_api_account_id' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_sp_api_account_id" name="wpcs_sp_api_account_id" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}

	/**
	 *  ServerPilot API Field Callback for API Key.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_sp_api_key() {

		$value = get_option( 'wpcs_sp_api_key' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input class="w-400" type="password" id="wpcs_sp_api_key" name="wpcs_sp_api_key" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}
	
	/**
	 *  ServerPilot Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_serverpilot_module_overview_menu_items( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
		$menu_index	 = get_option( 'wpcs_menu_location' );
			
		$state1 = ( ( 'active' == $status ) && ( ( 'ServerPilot' == $module_name ) || ( 'active' == $module_data['ServerPilot']['status'] ) ) );
		$state2 = ( ( 'active' == $status ) && ( ( 'ServerPilot' !== $module_name ) && ( 'active' == $module_data['ServerPilot']['status'] ) ) );
		$state3 = ( ( 'inactive' == $status ) && ( ( 'ServerPilot' !== $module_name ) && ( 'active' == $module_data['ServerPilot']['status'] ) ) );			
		$state4 = ( ( '' == $status ) && ( 'active' == $module_data['ServerPilot']['status'] ));
			
		if ( $state1 | $state2 | $state3 |  $state4 ) {
			
			$new_index	= $menu_index[0] + 2;
			$menu_index['ServerPilot']['logs']		= $menu_index[0] + 3;			
			$menu_index['ServerPilot']['settings'] 	= $menu_index[0] + 4;
			$menu_index[0] = $new_index;
			
			update_option( 'wpcs_menu_location', $menu_index );
		?>
			<li class="uk-nav-header">ServerPilot Module</li>
			<li class="uk-nav-divider"></li>
			<li><a href="#">Event Logs</a></li>
			<li><a href="#">Settings</a></li>
		<?php
		}
	}
				
	/**
	 *  ServerPilot Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_serverpilot_module_overview_menu_content( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
		$module_name = 'ServerPilot';

		$servers = self::$api->call_api( 'servers', null, false, 900, 'GET' );

		update_option( 'sp_server_api', $servers );
			
		$module_data = get_option( 'wpcs_module_list' );
					
		$module_active = ( ( 'active' == $module_data['ServerPilot']['status'] ));
		
		$logged_data = get_option( 'wpcs_serverpilot_logged_data' );
			
		if ( $module_active ) {
		?>

			<!-- Blank list elements required for menu to work with header and divider -->
			<li></li>
			<li></li>

			<!-- ServerPilot Logged Events -->
			<li>
				
				<div><?php do_action( 'wpcs_sp_module_notices' ); ?></div>
				
				<div style="background-color: #fdfdfd; border: 1px double #e8e8e8;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
					<div class="uk-container uk-container-medium">

						<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Logged Events', 'wp-cloud-server' ); ?></h2>
					
						<p><?php _e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server' ); ?>
						</p>

						<table class="uk-table uk-table-striped">
    						<thead>
    							<tr>
        							<th class="uk-width-small"><?php _e( 'Date', 'wp-cloud-server' ); ?></th>
        							<th class="uk-width-small"><?php _e( 'Module', 'wp-cloud-server' ); ?></th>
       			 					<th class="uk-width-small"><?php _e( 'Status', 'wp-cloud-server' ); ?></th>
									<th class="uk-width-small"><?php _e( 'Description', 'wp-cloud-server' ); ?></th>
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
        								<td colspan="4"><?php _e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server' ); ?></td>
    								</tr>								
							<?php
							}
							?>								
    						</tbody>
						</table>
					</div>

						
					</div>
				
				
			</li>

			<!-- ServerPilot Settings -->
			<li>		

			<?php

				$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';
				$reset_api = isset( $_GET['resetapi'] ) ? wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
			
				$menu_index = get_option( 'wpcs_menu_location' );
			
				$reset_api_complete = get_option( 'wpcs_sp_reset_api_complete' );

				if ( ( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'settings_nonce' ) ) ) {
					update_option( 'wpcs_sp_reset_api_complete', 'true' );
					update_option( 'wpcs_sp_api_redirect_enable', "true|{$menu_index['ServerPilot']['settings']}" );
					delete_option( 'wpcs_sp_api_account_id' );
					delete_option( 'wpcs_sp_api_key' );
					delete_option( 'wpcs_dismissed_sp_api_notice' );
					echo '<script type="text/javascript"> window.location.href =  window.location.href.split("&")[0]; </script>';
				}
				?>
	
	<div style="background-color: #fdfdfd; border: 1px double #e8e8e8;" class="uk-section uk-section-small uk-section-default">
					<div class="uk-container uk-container-medium">

				
					<form method="post" action="options.php">
						<input type="hidden" id="wpcs_sp_api_redirect_enable" name="wpcs_sp_api_redirect_enable" value="true|6">
						<?php
						settings_fields( 'wpcs_sp_admin_menu' );
						wpcs_do_settings_sections( 'wpcs_sp_admin_menu' );
						wpcs_submit_button( 'Save Settings', 'secondary', 'api_setting' );
						?>
					</form>
				

				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&resetapi=true'), 'settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset ServerPilot API Credentials', 'wp-cloud-server' ) ?></a>
				</p>
						
		</div>
	</div>
	
				</li>
				<?php
		}
	}
		
	/**
	 *  ServerPilot Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
		$menu_index	 = get_option( 'wpcs_menu_location' );
			
		$state1 = ( ( 'active' == $status ) && ( ( 'ServerPilot' == $module_name ) || ( 'active' == $module_data['ServerPilot']['status'] ) ) );
		$state2 = ( ( 'active' == $status ) && ( ( 'ServerPilot' !== $module_name ) && ( 'active' == $module_data['ServerPilot']['status'] ) ) );
		$state3 = ( ( 'inactive' == $status ) && ( ( 'ServerPilot' !== $module_name ) && ( 'active' == $module_data['ServerPilot']['status'] ) ) );			
		$state4 = ( ( '' == $status ) && ( 'active' == $module_data['ServerPilot']['status'] ));
			
		if ( $state1 | $state2 | $state3 |  $state4 ) {
			
			$new_index	= $menu_index[0] + 6;
			$menu_index['ServerPilot']['servers']	= $menu_index[0] + 3;
			$menu_index['ServerPilot']['websites']	= $menu_index[0] + 4;
			$menu_index['ServerPilot']['templates']	= $menu_index[0] + 5;			
			$menu_index['ServerPilot']['settings'] 	= $menu_index[0] + 6;
			$menu_index[0] = $new_index;
			
			update_option( 'wpcs_menu_location', $menu_index );
		?>
			<li class="uk-nav-header">ServerPilot</li>
			<li class="uk-nav-divider"></li>
			<li><a href="#">Connected Servers</a></li>
			<li><a href="#">Installed Websites</a></li>
			<li><a href="#">Server Templates</a></li>
			<li><a href="#">SSH Keys</a></li>
			<li><a href="#">Settings</a></li>
		<?php
		}
	}

	/**
	 *  Sanitize Account ID
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_serverpilot_account_id( $token ) {

		$output = get_option( 'wpcs_sp_api_account_id', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
		$new_token = sanitize_text_field( $token );	

		if ( '' !== $new_token ) {
			
			$output = $new_token;
			$type = 'updated';
			$message = __( 'The ServerPilot Account ID was updated.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid ServerPilot Account ID!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_sp_api_account_id',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}

	/**
	 *  Sanitize Account ID
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_serverpilot_api_key( $apikey ) {
		
		$output = get_option( 'wpcs_sp_api_key', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_apikey = sanitize_text_field( $apikey );

		if ( '' !== $new_apikey ) {
			
			$output = $new_apikey;
			$type = 'updated';
			$message = __( 'The ServerPilot API Key was updated.', 'wp-cloud-server' );

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid ServerPilot API Key!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_sp_api_account_id',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}
		
		return $output;		

	}
	
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_serverpilot_create_app_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_name' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_name', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_domain' ),
            'default' => NULL,
		);		
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_domain', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
		);
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_server', $args );
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_runtime', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_site_title' ),
            'default' => NULL,
		);		
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_site_title', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_admin_user' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_admin_user', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_password' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_password', $args );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_create_app_email' ),
            'default' => NULL,
		);
		
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_email', $args );
		register_setting( 'wpcs_serverpilot_create_app', 'wpcs_serverpilot_create_app_autossl' );

		add_settings_section(
			'wpcs_serverpilot_create_app',
			esc_attr__( 'Install a New Website', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_serverpilot_create_app' ),
			'wpcs_serverpilot_create_app'
		);

		add_settings_field(
			'wpcs_serverpilot_create_app_name',
			esc_attr__( 'Website Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_name' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);

		add_settings_field(
			'wpcs_serverpilot_create_app_domain',
			esc_attr__( 'Domain Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_domain' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_site_title',
			esc_attr__( 'Site Title:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_site_title' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_admin_user',
			esc_attr__( 'Admin User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_admin_user' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_password',
			esc_attr__( 'Admin Password:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_password' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_email',
			esc_attr__( 'Admin Email:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_email' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_server',
			esc_attr__( 'Select Server:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_server' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_runtime',
			esc_attr__( 'PHP Version:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_runtime' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);

		add_settings_field(
			'wpcs_field_callback_serverpilot_create_app_autossl',
			esc_attr__( 'Enable AutoSSL Queue:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_create_app_autossl' ),
			'wpcs_serverpilot_create_app',
			'wpcs_serverpilot_create_app'
		);
		
		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_serverpilot_create_app_field_setting' );

	}
		
	/**
	 *  ServerPilot Create App Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_serverpilot_create_app() {

		echo '<p>';
		echo wp_kses( 'This page allows you to add a new WordPress Website to any connected Server. Enter the details below and then click the \'Create New Website\' button to have the new website built and online in a few minutes!', 'wp-cloud-server' );
		echo '</p>';

	}

	/**
	 *  ServerPilot Create App Field Callback for App Name.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_name() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='app-name' id='wpcs_serverpilot_create_app_name' name='wpcs_serverpilot_create_app_name' value='{$value}'/>";
		echo '<p class="text_desc" >[You can use: lowercase a-z, 0-9, and a hyphen (-)]</p>';

	}

	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_domain() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='example.com' id='wpcs_serverpilot_create_app_domain' name='wpcs_serverpilot_create_app_domain' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_server() {
		
		$data = get_option( 'wpcs_serverpilot_api_data', array() );

		if ( !isset( $servers['servers']['data'] ) ) {
			// Create instance of the Cloudways API
			$api				= new WP_Cloud_Server_ServerPilot_API();
			$data['servers']	= $api->call_api( 'servers', null, false, 900, 'GET' );
		}
		$servers	= ( ( isset( $data['servers']['data'] ) ) && ( is_array( $data['servers']['data'] ) ) ) ? $data['servers']['data'] : '';
		?>
		<select class="w-400" name="wpcs_serverpilot_create_app_server" id="wpcs_serverpilot_create_app_server">
			<optgroup label="Select Server">
			<?php
			if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
				foreach ( $servers as $server ) {
				?>
            		<option value="<?php echo $server['name']; ?>"><?php echo $server['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="not_available">-- No Servers Available --</option>
			<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Runtime.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_runtime() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_serverpilot_create_app_runtime' );
		
		$data = get_option( 'wpcs_serverpilot_api_data', array() );

		if ( !isset( $servers['servers']['data'] ) ) {
			// Create instance of the Cloudways API
			$api				= new WP_Cloud_Server_ServerPilot_API();
			$data['servers']	= $api->call_api( 'servers', null, false, 900, 'GET' );
		}
		$servers		= ( ( isset( $data['servers']['data'] ) ) && ( is_array( $data['servers']['data'] ) ) ) ? $data['servers']['data'] : '';
		?>
		<select class='w-400' name="wpcs_serverpilot_create_app_runtime" id="wpcs_serverpilot_create_app_runtime">
			<optgroup label="Select PHP Version">
			<?php
			if ( ( ! empty($servers) ) && is_array( $servers[0]['available_runtimes']) ) {
				$available_runtimes = array_reverse( $servers[0]['available_runtimes'] );
				foreach ( $available_runtimes as $php ) {
					?>
            		<option value="<?php echo $php; ?>"><?php echo preg_replace( '/^php/', 'PHP ', $php ); ?></option>
					<?php 
				}
			} else {
				?>
				<option value="not_available">-- No PHP Version Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Create App Field Callback for Site Title.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_site_title() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='My WordPress Website' id='wpcs_serverpilot_create_app_site_title' name='wpcs_serverpilot_create_app_site_title' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for Admin User.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_admin_user() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='admin' id='wpcs_serverpilot_create_app_admin_user' name='wpcs_serverpilot_create_app_admin_user' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for Admin Password.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_password() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='password' placeholder='********' id='wpcs_serverpilot_create_app_password' name='wpcs_serverpilot_create_app_password' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Email.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_email() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='email' placeholder='mail@example.com' id='wpcs_serverpilot_create_app_email' name='wpcs_serverpilot_create_app_email' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App AutoSSL.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_create_app_autossl() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_serverpilot_create_app_autossl' );
		$module_data = get_option( 'wpcs_module_list' );
	
		echo "<input class='w-400' type='checkbox' id='wpcs_serverpilot_create_app_autossl' name='wpcs_serverpilot_create_app_autossl' value='1'/>";

	}
	
	/**
	 *  Register setting sections and fields for Add Template Page.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_serverpilot_add_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_name', $args );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_host_name' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_type' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_region' );
		
		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_template_size' ),
            'default' => NULL,
        );
		
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_size', $args );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_module' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_plan' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_autossl' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_ssh_key' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_provider' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_install_app' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_default_app' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_system_user_name' );
		register_setting( 'wpcs_serverpilot_create_template', 'wpcs_serverpilot_template_system_user_password' );
		
		//add_settings_section(
		//	'wpcs_serverpilot_create_template',
		//	esc_attr__( 'Create New Managed Server Template', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_section_callback_serverpilot_create_template' ),
		//	'wpcs_serverpilot_create_template'
		//);

		//add_settings_field(
		//	'wpcs_serverpilot_template_name',
		//	esc_attr__( 'Template Name:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_name' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);
		
		//add_settings_field(
		//	'wpcs_serverpilot_template_module',
		//	esc_attr__( 'Cloud Provider:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_module' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);

		//add_settings_field(
		//	'wpcs_serverpilot_template_type',
		//	esc_attr__( 'Server Image:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_type' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);

		//add_settings_field(
		//	'wpcs_serverpilot_template_region',
		//	esc_attr__( 'Server Region:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_region' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);

		//add_settings_field(
		//	'wpcs_serverpilot_template_size',
		//	esc_attr__( 'Server Size:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_size' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);
		
		//add_settings_field(
		//	'wpcs_serverpilot_template_plan',
		//	esc_attr__( 'ServerPilot Plan:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_plan' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);
		
		//add_settings_field(
		//	'wpcs_field_callback_serverpilot_template_ssh_key',
		//	esc_attr__( 'Select Admin SSH Key:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_ssh_key' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);
		
		//add_settings_field(
		//	'wpcs_field_callback_serverpilot_template_autossl',
		//	esc_attr__( 'Enable AutoSSL Queue:', 'wp-cloud-server' ),
		//	array( $this, 'wpcs_field_callback_serverpilot_template_autossl' ),
		//	'wpcs_serverpilot_create_template',
		//	'wpcs_serverpilot_create_template'
		//);
		
		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_serverpilot_template_field_setting' );

	}
	
	/**
	 *  ServerPilot Create Template Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_serverpilot_create_template() {

		echo "<p>Create a new 'Cloud Server Template' for use when creating new Hosting Plans . You can enter a template name, select the Image, Region, Size, and SSH Key. Finally, click 'Create Template' to save your new template!</p>";
	}
	
	/**
	 *  ServerPilot Template Field Callback for Module Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_module() {

		$value			= get_option( 'wpcs_serverpilot_template_module' );
		$module_data	= get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$cloud_active	= wpcs_check_cloud_provider_api( null, null, true);
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		?>
		<select class='w-400' name="wpcs_serverpilot_template_module" id="wpcs_serverpilot_template_module">
			<?php
			if ( $cloud_active ) {
				?><optgroup label="Select Cloud Provider"><?php
				foreach ( $module_data as $key => $module ) { 
					if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) ) {
						?>
            			<option value="<?php echo $key ?>"><?php echo $key ?></option>
						<?php 
					}
				}
				?></optgroup><?php
			} else {
				?>
				<optgroup label="Select Cloud Provider">
					<option value="DigitalOcean">DigitalOcean</option>
				</optgroup>
				<?php
			}
			?>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Template Field Callback for Type Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_type() {

		$value			= get_option( 'wpcs_serverpilot_template_type' );
		$module_data	= get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		?>
		<select class='w-400' name="wpcs_serverpilot_template_type" id="wpcs_serverpilot_template_type">
			<optgroup label="Select Image">
				<option value="Ubuntu 20.04 x64"><?php _e( 'Ubuntu 20.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
            	<option value="Ubuntu 18.04 x64"><?php _e( 'Ubuntu 18.04 (LTS) x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Template Field Callback for Name Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_name() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';

		echo "<input class='w-400' type='text' placeholder='Template Name' id='wpcs_serverpilot_template_name' name='wpcs_serverpilot_template_name' value='' {$attributes}>";
		echo '<p class="text_desc" >[You can use any valid text, numeric, and space characters]</p>';

	}

	/**
	 *  ServerPilot Template Field Callback for Region Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_region() {
		
		$regions = call_user_func("wpcs_digitalocean_regions_list");
		$value = get_option( 'wpcs_serverpilot_template_region' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		?>

		<select class='w-400' name="wpcs_serverpilot_template_region" id="wpcs_serverpilot_template_region">
			<?php
			if ( $regions ) {
				?><optgroup label='Select Region'><?php
            	foreach ( $regions as $key => $region ){
					$value = "{$region['name']}|{$key}";
					?>
                	<option value="<?php echo $value; ?>"><?php echo $region['name']; ?></option>
					<?php
				}
			}
			?>
			</optgroup>
			<optgroup label='User Choice'>
			<option value="userselected">-- Checkout Input Field --</option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Template Field Callback for Size Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_size() {

		$plans = call_user_func("wpcs_digitalocean_plans_list");
		$value = get_option( 'wpcs_serverpilot_template_size' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		
		?>

		<select class='w-400' name="wpcs_serverpilot_template_size" id="wpcs_serverpilot_template_size">
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
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Template Field Callback for Plan Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_plan() {

		$value		 = get_option( 'wpcs_serverpilot_template_plan' );
		$module_data = get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		?>
		<select class='w-400' name="wpcs_serverpilot_template_plan" id="wpcs_serverpilot_template_plan">
			<optgroup label="Select Image">
           		<option value="economy"><?php _e( 'Economy ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
            	<option value="business"><?php _e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
				<option value="first_class"><?php _e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  ServerPilot Template Field Callback for AutoSSL Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_autossl() {

		$value = get_option( 'wpcs_serverpilot_template_autossl' );
		$module_data = get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		echo "<input class='w-400' type='checkbox' id='wpcs_serverpilot_template_autossl' name='wpcs_serverpilot_template_autossl' value='1'>";

	}
	
	/**
	 *  ServerPilot Template Field Callback for Plan Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_template_ssh_key() {

		$value		 = get_option( 'wpcs_serverpilot_template_ssh_key' );
		$module_data = get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		
		$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select style="width: 25rem;" name="wpcs_digitalocean_server_ssh_key" id="wpcs_digitalocean_server_ssh_key">
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
	 *  Register setting sections and fields for Add Template Page.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_serverpilot_edit_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_edit_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_name' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_type' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_region' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_size' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_module' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_plan' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_region' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_autossl' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_ssh_key' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_provider' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_install_app' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_default_app' );
		register_setting( 'wpcs_serverpilot_edit_template', 'wpcs_serverpilot_edit_template_enable_backups' );

		add_settings_section(
			'wpcs_serverpilot_edit_template',
			'',
			'',
			'wpcs_serverpilot_edit_template'
		);
	}
	
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_serverpilot_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_serverpilot_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_name', $args );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_module' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_type' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_region' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_size' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_plan' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_shared_hosting' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_ssh_key' );
		register_setting( 'wpcs_serverpilot_create_server', 'wpcs_serverpilot_server_enable_backups' );
		
		add_settings_section(
			'wpcs_serverpilot_create_server',
			esc_attr__( 'Connect New Server', 'wp-cloud-server' ),
			array( $this, 'wpcs_section_callback_serverpilot_create_server' ),
			'wpcs_serverpilot_create_server'
		);

		add_settings_field(
			'wpcs_serverpilot_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_name' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);
		
		add_settings_field(
			'wpcs_serverpilot_server_module',
			esc_attr__( 'Cloud Provider:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_module' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);

		add_settings_field(
			'wpcs_serverpilot_server_type',
			esc_attr__( 'Server Image:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_type' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);

		add_settings_field(
			'wpcs_serverpilot_server_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_region' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);

		add_settings_field(
			'wpcs_serverpilot_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_size' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);
		
		add_settings_field(
			'wpcs_serverpilot_server_ssh_key',
			esc_attr__( 'SSH Key:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_ssh_key' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);
		
		add_settings_field(
			'wpcs_serverpilot_server_enable_backups',
			esc_attr__( 'Enable Backups:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_serverpilot_server_enable_backups' ),
			'wpcs_serverpilot_create_server',
			'wpcs_serverpilot_create_server'
		);

	}
		
	/**
	 *  ServerPilot Create Server Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_serverpilot_create_server() {

		echo '<p>';
		echo wp_kses( "Configure your new server below. Enter a Server Name, select the Cloud Provider, Server Features, and SSH Key. Click the 'Create Server' button and the server is automatically created in just a few minutes.", "wp-cloud-server" );
		echo '</p>';

	}
	
	/**
	 *  ServerPilot Server Field Callback for Module Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_module() {

		$api_status	= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes	= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_serverpilot_server_module' );
		$module_data = get_option( 'wpcs_module_list' );
	
		?>
		<select class='w-400' name="wpcs_serverpilot_server_module" id="wpcs_serverpilot_server_module">
			<?php
			$cloud_active	= wpcs_check_cloud_provider_api();
			if ( $cloud_active ) {
				?><optgroup label="Select Cloud Provider"><?php
				foreach ( $module_data as $key => $module ) { 
					if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'ServerPilot' != $key ) && ( 'active' == $module['status'] ) && ( wpcs_check_cloud_provider_api( $key ) ) ) {
						?>
            			<option value="<?php echo $key ?>"><?php echo $key ?></option>
						<?php 
					}
				}
				?></optgroup><?php
			} else {
				?>
				<optgroup label="Select Cloud Provider">
					<option value="DigitalOcean">DigitalOcean</option>
				</optgroup>
				<?php
			}
			?>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Server Field Callback for Type Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_type() {

		$value 			= get_option( 'wpcs_serverpilot_server_type' );
		$module_data	= get_option( 'wpcs_module_list' );
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
	
		?>
		<select class='w-400' name="wpcs_serverpilot_server_type" id="wpcs_serverpilot_server_type">
			<optgroup label="Select Image">
				<option value="Ubuntu 20.04 x64|ubuntu-20-04-x64"><?php _e( 'Ubuntu 20.04 x64', 'wp-cloud-server' ); ?></option>
            	<option value="Ubuntu 18.04 x64|ubuntu-18-04-x64"><?php _e( 'Ubuntu 18.04 x64', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Server Field Callback for Name Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_name() {
		
		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input class='w-400' type='text' placeholder='server-name' id='wpcs_serverpilot_server_name' name='wpcs_serverpilot_server_name' value='{$value}'>";
		echo '<p class="text_desc" >[You can use: a-z, 0-9, -, and a period (.)]</p>';

	}

	/**
	 *  ServerPilot Server Field Callback for Region Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_region() {

		$api_status	= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes	= ( $api_status ) ? '' : 'disabled';
		$regions	= call_user_func("wpcs_digitalocean_regions_list");
		$value		= get_option( 'wpcs_serverpilot_server_region' );
		?>
		<select class='w-400' name="wpcs_serverpilot_server_region" id="wpcs_serverpilot_server_region">
			<?php
			if ( $regions ) {
				?><optgroup label="Select Region"><?php
            	foreach ( $regions as $key => $region ){
					$value = "{$region['name']}|{$key}";
					?>
                	<option value="<?php echo $value; ?>"><?php echo $region['name']; ?></option>
					<?php
				}
				?></optgroup><?php
			}
			?>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Server Field Callback for Size Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_size() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$plans = call_user_func("wpcs_digitalocean_plans_list");
		$value = get_option( 'wpcs_serverpilot_server_size' );
		
		?>

		<select class='w-400' name="wpcs_serverpilot_server_size" id="wpcs_serverpilot_server_size">
			<optgroup label="Select Plan">
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
			</optgroup>
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
		$ssh_keys		= get_option( 'wpcs_serverpilots_ssh_keys' );
	
		?>
		<select class="w-400" name="wpcs_serverpilot_server_ssh_key" id="wpcs_serverpilot_server_ssh_key">
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
	 *  ServerPilot Field Callback for Enable Backups Setting.
	 *
	 *  @since 2.1.3
	 */
	public function wpcs_field_callback_serverpilot_server_enable_backups() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_serverpilot_server_enable_backups' );
		$module_data	= get_option( 'wpcs_module_list' );
	
		echo "<input type='checkbox' id='wpcs_serverpilot_server_enable_backups' name='wpcs_serverpilot_server_enable_backups' value='1'>";

	}
	
	/**
	 *  ServerPilot Server Field Callback for Plan Setting.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_serverpilot_server_plan() {

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot');
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= get_option( 'wpcs_serverpilot_server_plan' );
		$module_data	= get_option( 'wpcs_module_list' );
	
		?>
		<select class='w-400' name="wpcs_serverpilot_server_plan" id="wpcs_serverpilot_server_plan">
			<optgroup label="Select ServerPilot Plan">
            	<option value="economy"><?php _e( 'Economys ($5/server + $0.50/app)', 'wp-cloud-server' ); ?></option>
            	<option value="business"><?php _e( 'Business ($10/server + $1/app)', 'wp-cloud-server' ); ?></option>
				<option value="first_class"><?php _e( 'First Class ($20/server + $2/app)', 'wp-cloud-server' ); ?></option>
			</optgroup>
		</select>
		<?php

	}
	
	/**
	 *  Sanitize App Name
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original app name
	 *  @return string  checked app name
	 */
	public function sanitize_serverpilot_create_app_name( $name ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		if ( '' !== $name ) {
			$lc_name  = strtolower( $name );
			$invalid  = preg_match('/[^a-z0-9\-]/u', $lc_name);
			if ( $invalid ) {

				$type = 'error';
				$message = __( 'The ServerPilot App Name entered is not Valid. Please try again using characters a-z, 0-9, or a hyphen (-)', 'wp-cloud-server' );
	
			} else {
				$output = $name;
				$type = 'updated';
				$message = __( 'The New ServerPilot App is being Created', 'wp-cloud-server' );
	
			}
		} else {
			$type = 'error';
			$message = __( 'Please enter a ServerPilot App Name!', 'wp-cloud-server' );
		}

			add_settings_error(
				'wpcs_serverpilot_create_app_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}
	
	/**
	 *  Sanitize App Domain
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original domain name
	 *  @return string  checked domain name
	 */
	public function sanitize_serverpilot_create_app_domain( $domain_name ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_domain', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
		
		$domain_name = esc_url( $domain_name );
		
		$url		= preg_replace( '/^https?:\/\//', '', $domain_name );
		$new_url		= preg_replace( '/^www./', '', $url );

		if ( '' !== $new_url ) {
			
			$output = $new_url;
			return $output;

		} else {
			$type = 'error';
			$message = __( 'Please enter a Valid Domain Name!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_serverpilot_create_app_domain',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}

		return $output;

	}
	
	/**
	 *  Sanitize ServerPilot Site Title
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original site title
	 *  @return string  checked site title
	 */
	public function sanitize_serverpilot_create_app_site_title( $site_title ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_site_title', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_site_title = sanitize_text_field( $site_title );

		if ( '' !== $new_site_title ) {
			
			$output		= $new_site_title;
			return $output;

		} else {
			$type = 'error';
			$message	= __( 'Please enter a Valid ServerPilot Site Title!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_serverpilot_create_app_site_title',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}

		return $output;

	}
	
	/**
	 *  Sanitize ServerPilot Admin User
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original admin user
	 *  @return string  checked admin user
	 */
	public function sanitize_serverpilot_create_app_admin_user( $admin_user ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_admin_user', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_admin_user = sanitize_user( $admin_user );

		if ( '' !== $new_admin_user ) {
			
			$output		= $new_admin_user;
			return $output;

		} else {
			$type = 'error';
			$message	= __( 'Please enter a Valid ServerPilot Admin User!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_serverpilot_create_app_admin_user',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}

		return $output;

	}
	
	/**
	 *  Sanitize ServerPilot Password
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original password
	 *  @return string  checked password
	 */
	public function sanitize_serverpilot_create_app_password( $admin_password ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_password', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_admin_password = sanitize_text_field( $admin_password );

		if ( '' !== $new_admin_password ) {
			
			$output	= $new_admin_password;
			return $output;

		} else {
			$type = 'error';
			$message	= __( 'Please enter a Valid ServerPilot Password!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_serverpilot_create_app_password',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}

		return $output;

	}
	
	/**
	 *  Sanitize ServerPilot Email
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original admin email
	 *  @return string  checked admin email
	 */
	public function sanitize_serverpilot_create_app_email( $admin_email ) {
		
		$output = get_option( 'wpcs_serverpilot_create_app_email', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

		$new_admin_email = sanitize_email( $admin_email );

		if ( '' !== $new_admin_email ) {
			
			$output	= $new_admin_email;
			return $output;

		} else {
			$type = 'error';
			$message	= __( 'Please enter a Valid ServerPilot Email!', 'wp-cloud-server' );
		}

		add_settings_error(
			'wpcs_serverpilot_create_app_email',
			esc_attr( 'settings_error' ),
			$message,
			$type
		);

		return $output;
			
		}

		return $output;						

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_serverpilot_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_serverpilot_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New ServerPilot Template was Successfully Created.', 'wp-cloud-server' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server' );
			}

			add_settings_error(
				'wpcs_serverpilot_template_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_serverpilot_template_size( $name ) {
		
		//$name = sanitize_text_field( $name );
		
		$completed_tasks = get_option('wpcs_tasks_completed');
		
		$output = get_option( 'wpcs_serverpilot_template_size', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0;  static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( 'no-server' == $name ) {
			
				$type = 'error';
				$message = __( 'Sorry! There are no servers available for that Region! Please try a new Region then select a server.', 'wp-cloud-server' );

				add_settings_error(
					'wpcs_serverpilot_template_size',
					esc_attr( 'settings_error' ),
					$message,
					$type
				);
			
				return $output;
			
			}

			$output = $name;
			return $output;
		}
		
		return $output;

	}
	
	/**
	 *  Sanitize Server Name
	 *
	 *  @since  1.1.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_serverpilot_server_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_serverpilot_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {
			
			$error = false;

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				
				if ( $invalid ) {
					$error		= true;
					$type		= 'error';
					$message	= __( 'The Server Name entered is not Valid. Please try again using characters a-z, 0-9, - or a period (.)', 'wp-cloud-server' );
				} else {
					$output 	= $name;
					$type		= 'updated';
					$message	= __( 'Your New Cloud Server is being Created', 'wp-cloud-server' );
				}
			} else {
				$error		= true;
				$type 		= 'error';
				$message 	= __( 'Please enter a ServerPilot Server Name!', 'wp-cloud-server' );
			}
	
			add_settings_error(
				'wpcs_serverpilot_server_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;	
		}
			return $output;
	}

	/**
	 *  Return true if ServerPilot Module is Active.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_sp_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}

	/**
	 *  Return ServerPilot Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_serverpilot_module_name() {

		return self::$module_name;

	}
}