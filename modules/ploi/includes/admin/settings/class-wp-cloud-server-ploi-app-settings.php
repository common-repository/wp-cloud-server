<?php

/**
 * WP Cloud Server - Ploi Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_App_Settings {

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_create_app_setting_sections_and_fields' ) );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_ploi_create_app_setting_sections_and_fields() {
		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_server_id' );		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_root_domain' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_project_directory' );		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_web_directory' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_system_user' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_web_template' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_install_app' );

		add_settings_section(
			'wpcs_ploi_create_app',
			esc_attr__( 'Install a New Website', 'wp-cloud-server' ),
			'',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_root_domain',
			esc_attr__( 'Root Domain:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_root_domain' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_server_id',
			esc_attr__( 'Server Id:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_server_id' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_project_directory',
			esc_attr__( 'Project Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_project_directory' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_web_directory',
			esc_attr__( 'Web Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_web_directory' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_system_user',
			esc_attr__( 'System User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_system_user' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_web_template',
			esc_attr__( 'Web Template:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_web_template' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_install_app',
			esc_attr__( 'Install Application:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_install_app' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_enable_ssl',
			esc_attr__( 'Install SSL Certifcate:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_enable_ssl' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
				
		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_ploi_create_app_field_setting' );

	}
		
	/**
	 *  ServerPilot Create App Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_ploi_create_app() {

		echo '<p>';
		echo wp_kses( 'This page allows you to add a new WordPress Website to any connected Server. Enter the details below and then click the \'Create New Website\' button to have the new website built and online in a few minutes!', 'wp-cloud-server' );
		echo '</p>';

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_server_id() {
		
		$servers = wpcs_ploi_call_api_list_servers();

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_ploi_create_app_server_id' );
		?>
		<select style='width: 400px' name="wpcs_ploi_create_app_server_id" id="wpcs_ploi_create_app_server_id">
			<optgroup label="Servers">
			<?php
			if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
				foreach ( $servers as $server ) {
					if ( 'server' == $server['type']) {
						?>
            			<option value="<?php echo $server['id']; ?>"><?php echo $server['name']; ?></option>
						<?php
					}
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
	 *  ServerPilot Create App Field Callback for App Name.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_root_domain() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='domain.com' id='wpcs_ploi_create_app_root_domain' name='wpcs_ploi_create_app_root_domain' value='{$value}'/>";

	}

	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_project_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/' id='wpcs_ploi_create_app_project_directory' name='wpcs_ploi_create_app_project_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_web_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/public' id='wpcs_ploi_create_app_web_directory' name='wpcs_ploi_create_app_web_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_system_user() {

		$servers			= wpcs_ploi_call_api_list_servers();

		if ( isset( $servers[0]['id'] ) ) {
			$args['server_id']	= $servers[0]['id'];
			$users				= wpcs_ploi_api_system_users_request( 'system-users/list', $args );
		}

		?>
		<select class='w-400' name="wpcs_ploi_create_app_system_user" id="wpcs_ploi_create_app_system_user">
			<optgroup label="System User">
				<?php
				if ( ( isset( $users ) ) && is_array( $users ) ) {
					?><option value="ploi">ploi</option><?php
					foreach ( $users as $user ) {
						?>
						<option value='<?php echo "{$user['name']}"; ?>'><?php echo $user['name']; ?></option>
						<?php
					}
				} else {
					?>
					<option value="">-- No System Users Available --</option>
					<?php
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
	public function wpcs_field_callback_ploi_create_app_web_template() {

		$servers	= wpcs_ploi_api_web_templates_request( 'templates/list' );
		?>
	
		<select class='w-400' name="wpcs_ploi_create_app_web_template" id="wpcs_ploi_create_app_web_template">
		<optgroup label="Web Templates">
							<?php
							if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
								foreach ( $servers as $server ) {
								?>
									<option value='<?php echo "{$server['label']}|{$server['id']}"; ?>'><?php echo $server['label']; ?></option>
								<?php
								}
							} else {
								?>
								<option value="not_available">-- No Web Templates Available --</option>
							<?php
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
	public function wpcs_field_callback_ploi_create_app_install_app() {

		$git_repos = wpcs_github_repos_list();
		?>
		<select style="width: 400px" name="wpcs_ploi_create_app_install_app" id="wpcs_ploi_create_app_install_app">
			<option value="no-application|app"><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
			<option value="wordpress|app"><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
			<option value="nextcloud|app"><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
			<?php
			if ( !empty( $git_repos ) ) { ?>
				<optgroup label="Install from GIT Repository">
				<?php
				foreach ( $git_repos as $key => $git_repo ) {
					?>
					<option value='<?php echo "{$key}|git"; ?>'><?php echo $git_repo; ?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 *  Delete Logged Data Field Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_create_app_enable_ssl() {
		echo '<input name="wpcs_ploi_create_app_enable_ssl" id="wpcs_ploi_create_app_enable_ssl" type="checkbox" value="1" class="code"/>';
	}
}