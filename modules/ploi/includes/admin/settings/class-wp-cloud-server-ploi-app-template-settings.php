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

class WP_Cloud_Server_Ploi_App_Template_Settings {

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_create_app_template_setting_sections_and_fields' ) );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_ploi_create_app_template_setting_sections_and_fields() {
		
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_server_id' );		
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_root_domain' );
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_project_directory' );		
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_web_directory' );
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_system_user' );
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_web_template' );
		register_setting( 'wpcs_ploi_create_app_template', 'wpcs_ploi_create_app_template_install_app' );

		add_settings_section(
			'wpcs_ploi_create_app_template',
			esc_attr__( 'Install a New Website', 'wp-cloud-server' ),
			'',
			'wpcs_ploi_create_app_template'
		);

		add_settings_field(
			'wpcs_ploi_create_app_template_root_domain',
			esc_attr__( 'Root Domain:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_root_domain' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);

		add_settings_field(
			'wpcs_ploi_create_app_template_server_id',
			esc_attr__( 'Server Id:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_server_id' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_template_project_directory',
			esc_attr__( 'Project Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_project_directory' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_template_web_directory',
			esc_attr__( 'Web Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_web_directory' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_template_system_user',
			esc_attr__( 'System User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_system_user' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_template_web_template',
			esc_attr__( 'Web Template:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_web_template' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);

		add_settings_field(
			'wpcs_ploi_create_app_template_install_app',
			esc_attr__( 'Install Application:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_template_install_app' ),
			'wpcs_ploi_create_app_template',
			'wpcs_ploi_create_app_template'
		);
				
		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_ploi_create_app_template_field_setting' );

	}
		
	/**
	 *  ServerPilot Create App Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_section_callback_ploi_create_app_template() {

		echo '<p>';
		echo wp_kses( 'This page allows you to add a new WordPress Website to any connected Server. Enter the details below and then click the \'Create New Website\' button to have the new website built and online in a few minutes!', 'wp-cloud-server' );
		echo '</p>';

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_server_id() {
		
		$servers = wpcs_ploi_call_api_list_servers();

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_ploi_create_app_template_server_id' );
		?>
		<select style='width: 400px' name="wpcs_ploi_create_app_template_server_id" id="wpcs_ploi_create_app_template_server_id">
			<optgroup label="Servers">
			<?php
			if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
				foreach ( $servers as $server ) {
				?>
            		<option value="<?php echo $server['id']; ?>"><?php echo $server['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="not_available">No Servers Available</option>
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
	public function wpcs_field_callback_ploi_create_app_template_root_domain() {

		$host_names		= get_option( 'wpcs_host_names' );
		$api_status		= wpcs_check_cloud_provider_api();
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_digitalocean_template_host_name' );
		?>
		<select class="w-400" name="wpcs_ploi_create_app_template_host_name" id="wpcs_ploi_create_app_template_host_name">
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
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_project_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/' id='wpcs_ploi_create_app_template_project_directory' name='wpcs_ploi_create_app_template_project_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_web_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/public' id='wpcs_ploi_create_app_template_web_directory' name='wpcs_ploi_create_app_template_web_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_system_user() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value			= null;
		$value			= ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='System User' id='wpcs_ploi_create_app_template_system_user' name='wpcs_ploi_create_app_template_system_user' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_web_template() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='Template Name' id='wpcs_ploi_create_app_template_web_template' name='wpcs_ploi_create_app_template_web_template' value='{$value}'/>";

	}

	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	public function wpcs_field_callback_ploi_create_app_template_install_app() {
		$git_repos = wpcs_github_repos_list();
		?>
		<select style="width: 400px" name="wpcs_ploi_site_template_install_app" id="<?php echo $id; ?>">
			<optgroup  label="Select Application">
				<option value="no-application|app|No Application"><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
				<option value="wordpress|app|WordPress"><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
				<option value="nextcloud|app|Nextcloud"><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
				<?php if ( !empty( $git_repos ) ) { ?>
			</optgroup>
			<optgroup label="Select GIT Repository">
				<?php foreach ( $git_repos as $key => $git_repo ) {
					?>
					<option value='<?php echo "{$key}|git|{$git_repo}"; ?>'><?php echo $git_repo; ?></option>
					<?php
				}
			}
			?>
			</optgroup>
		</select>
		<?php
	}
}