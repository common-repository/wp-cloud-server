<?php

/**
 * WP Cloud Server - Ploi Module Server Template Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_Server_Template_Settings {

	/**
	 *  Set-up Server Template Actions
	 *
	 *  @since 3.0.6
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_create_server_template_setting_sections_and_fields' ) );

	}
	
	/**
	 *  Register setting sections and fields for Add Server Page.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_ploi_create_server_template_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_ploi_server_template_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_name', $args );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_credentials' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_root_domain' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_size' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_database' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_php_version' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_region' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_type' );
		register_setting( 'wpcs_ploi_create_server_template', 'wpcs_ploi_server_template_webserver_type' );
		

		add_settings_section(
			'wpcs_ploi_create_server_template',
			esc_attr__( 'Create Ploi Server Template', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_section_callback_ploi_create_server_template' ),
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_name',
			esc_attr__( 'Template Name:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_name' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_credentials',
			esc_attr__( 'Credentials:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_credentials' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_root_domain',
			esc_attr__( 'Domain:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_root_domain' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_size' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_database',
			esc_attr__( 'Server Database:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_database' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_php_version',
			esc_attr__( 'PHP Version:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_php_version' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_region',
			esc_attr__( 'Server Region:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_region' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_type',
			esc_attr__( 'Server Type:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_type' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_webserver',
			esc_attr__( 'Webserver Type:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_webserver' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_enable_backups',
			esc_attr__( 'Enable Backups:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_enable_backups' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

		add_settings_field(
			'wpcs_ploi_server_template_install_app',
			esc_attr__( 'Install Application:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_template_enable_backups' ),
			'wpcs_ploi_create_server_template',
			'wpcs_ploi_create_server_template'
		);

	}
	
	/**
	 *  Ploi Create Server Template Callback.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_section_callback_ploi_create_server_template() {

		echo '<p style="max-width: 650px;" >This page allows you to create a template for creating a Ploi server.</p>';

	}

	/**
	 *  Ploi Field Callback for Server Template Name Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_name() {

		echo '<input style="width: 400px" type="text" placeholder="Template Name" id="wpcs_ploi_server_template_name" name="wpcs_ploi_server_template_name" value=""/>';
		echo '<p class="text_desc" >[ You can use any valid text, numeric, and space characters ]</p>';

	}

	/**
	 *  Ploi Field Callback for Server Template Credential Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_credentials() {

		$credentials	= wpcs_ploi_api_user_request( 'server-providers/list' );
	
		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_credentials" id="wpcs_ploi_server_template_credentials">
			<?php
			if ( is_array( $credentials ) ) {
				?>
				<optgroup label="Credentials">
				<?php
				foreach ( $credentials as $credential ) {
					?>
    				<option value="<?php echo $credential['id']; ?>"><?php echo $credential['name']; ?></option>
					<?php
				}
			} else {
				?>
    			<option value="no_value">-- No Credentials Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Template Domain Name Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_root_domain() {

		$host_names		= get_option( 'wpcs_host_names' );
		?>
		<select class="w-400" name="wpcs_ploi_server_template_root_domain" id="wpcs_ploi_server_template_root_domain">
			<?php
			if ( !empty( $host_names ) ) {
				?>
				<optgroup label="Select Hostname">
				<?php
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
	 *  Ploi Field Callback for Server Template Size Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_size() {

		$list = wpcs_ploi_api_user_request( 'server-providers/list' );

		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_size" id="wpcs_ploi_server_template_size">
			<?php
			if ( is_array( $list ) ) {
				?>
				<optgroup label="Plans">
				<?php
				foreach ( $list as $plans ) {
					foreach ( $plans['provider']['plans'] as $key => $plan ) {
						?>
    					<option value="<?php echo $plan['id']; ?>"><?php echo $plan['description']; ?></option>
						<?php
					}
				}
			} else {
				?>
    			<option value="no_value">-- No Plans Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Template Database Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_database() {

		$plans = wpcs_ploi_database_list();
		?>

		<select style="width: 400px" name="wpcs_ploi_server_template_database" id="wpcs_ploi_server_template_database">
			<?php
			if ( !empty( $plans ) ) {
				?>
				<optgroup label="Database">
				<?php
				foreach ( $plans as $label => $id ) {
				?>
    				<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
				<?php
				}
			} else {
				?>
    				<option value="no_value">-- No Databases Settings Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server PHP Version Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_php_version() {

		$plans = wpcs_ploi_plans_list();
		?>

		<select style="width: 400px" name="wpcs_ploi_server_template_php_version" id="wpcs_ploi_server_template_php_version">
			<option value="none"><?php esc_html_e( '-- No PHP Version Installed --', 'wp-cloud-server-ploi' ); ?></option>
			<option value="8.0"><?php esc_html_e( 'PHP 8.0', 'wp-cloud-server-ploi' ); ?></option>	
			<option value="7.4"><?php esc_html_e( 'PHP 7.4', 'wp-cloud-server-ploi' ); ?></option>
            <option value="7.3"><?php esc_html_e( 'PHP 7.3', 'wp-cloud-server-ploi' ); ?></option>
            <option value="7.2"><?php esc_html_e( 'PHP 7.2', 'wp-cloud-server-ploi' ); ?></option>
            <option value="7.1"><?php esc_html_e( 'PHP 7.1', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Template Region Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_region() {
		
		$list = wpcs_ploi_api_user_request( 'server-providers/list' );

		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_region" id="wpcs_ploi_server_template_region">
			<?php
			if ( is_array( $list ) ) {
				?>
				<optgroup label="Regions">
				<?php
				foreach ( $list as $plans ) {
					foreach ( $plans['provider']['regions'] as $key => $plan ) {
						?>
    					<option value="<?php echo $plan['id']; ?>"><?php echo $plan['name']; ?></option>
						<?php
					}
				}
			} else {
				?>
    				<option value="no_value">-- No Regions Available --</option>
				<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Type Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_type() {
		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_type" id="wpcs_ploi_server_template_type">
			<option value="server"><?php esc_html_e( 'Server', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Webserver Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_webserver() {
	
		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_webserver" id="wpcs_ploi_server_template_webserver">
			<option value="nginx"><?php esc_html_e( 'NGINX', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}

	/**
	 *  Ploit Create App Field Callback for App Domain.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_install_app() {
		?>
		<select style="width: 400px" name="wpcs_ploi_server_template_install_app" id="wpcs_ploi_server_template_install_app">
			<option value="no-application"><?php esc_html_e( '-- No Application --', 'wp-cloud-server-ploi' ); ?></option>
			<option value="wordpress"><?php esc_html_e( 'WordPress', 'wp-cloud-server-ploi' ); ?></option>
			<option value="nextcloud"><?php esc_html_e( 'Nextcloud', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php
	}

	/**
	 *  Ploi Field Callback for Server Enable Backups Setting.
	 *
	 *  @since 3.0.6
	 */
	public function wpcs_field_callback_ploi_server_template_enable_backups() {
	
		?>
		<input class="uk-input" type="checkbox" id="wpcs_ploi_server_template_enable_backups" name="wpcs_ploi_server_template_enable_backups" value="1">
		<?php

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  3.0.6
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_ploi_server_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_ploi_server_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Ploi Template was Created.', 'wp-cloud-server-ploi' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_server_template_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}
}