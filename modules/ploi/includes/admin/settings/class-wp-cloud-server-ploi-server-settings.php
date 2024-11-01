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

class WP_Cloud_Server_Ploi_Server_Settings {

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_create_server_setting_sections_and_fields' ) );

	}

	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_create_server_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_ploi_server_name' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_name', $args );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_credentials' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_size' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_database_type' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_php_version' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_region' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_server_type' );
		register_setting( 'wpcs_ploi_create_server', 'wpcs_ploi_server_webserver_type' );

		add_settings_section(
			'wpcs_ploi_create_server',
			esc_attr__( 'Create New Ploi Server', 'wp-cloud-server-ploi' ),
			'',
			'wpcs_ploi_create_server'
		);

		add_settings_field(
			'wpcs_ploi_server_name',
			esc_attr__( 'Server Name:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_name' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);
		
		add_settings_field(
			'wpcs_ploi_server_credentials',
			esc_attr__( 'Credentials:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_credentials' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);

		add_settings_field(
			'wpcs_ploi_server_size',
			esc_attr__( 'Server Size:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_size' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);

		add_settings_field(
			'wpcs_ploi_server_database_type',
			esc_attr__( 'Database:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_database_type' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);

		add_settings_field(
			'wpcs_ploi_server_php_version',
			esc_attr__( 'PHP Version:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_php_version' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);
		
		add_settings_field(
			'wpcs_ploi_server_region',
			esc_attr__( 'Region:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_region' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);
		
		add_settings_field(
			'wpcs_ploi_server_server_type',
			esc_attr__( 'Server Type:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_server_type' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);
		
		add_settings_field(
			'wpcs_ploi_server_webserver_type',
			esc_attr__( 'Webserver Type:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_server_webserver_type' ),
			'wpcs_ploi_create_server',
			'wpcs_ploi_create_server'
		);

	}
		
	/**
	 *  Ploi API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_ploi_create_server() {

		echo '<p class="text_desc">';
		echo wp_kses( 'This page allows you to create a new Ploi Server. You can enter the Server Name, select the Image, Region, and Size, and then click \'Create Server\' to build your new Server.', 'wp-cloud-server-ploi' );
		echo '</p>';

	}
	
	/**
	 *  Ploi Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_credentials() {

		$credentials = wpcs_ploi_api_user_request( 'server-providers/list' );
	
		?>
		<select style="width: 400px" name="wpcs_ploi_server_credentials" id="wpcs_ploi_server_credentials">
			<?php
			if (  $credentials && is_array( $credentials ) ) {
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
	 *  Ploi Field Callback for Server Name Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_name() {

		echo '<input style="width: 400px"type="text" placeholder="server-name" id="wpcs_ploi_server_name" name="wpcs_ploi_server_name" value=""/>';
		echo '<p class="text_desc" >[ You can use: a-z, A-Z, 0-9, -, and a period (.) ]</p>';

	}
	
	/**
	 *  Ploi Field Callback for Server Size Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_size() {

		$list = wpcs_ploi_api_user_request( 'server-providers/list' );
		?>
		<select style="width: 400px" name="wpcs_ploi_server_size" id="wpcs_ploi_server_size">
			<?php
			if ( $list && is_array( $list ) ) {
				?>
				<optgroup label="Plans">
				<?php
				foreach ( $list[0]['provider']['plans'] as $key => $plan ) {
				?>
    				<option value="<?php echo $plan['id']; ?>"><?php echo $plan['description']; ?></option>
				<?php
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
	 *  Ploi Field Callback for Server Database Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_database_type() {

		//$value = get_option( 'wpcs_ploi_server_database_type' );
		$value = '';

				?>
		<select style="width: 400px" name="wpcs_ploi_server_database_type" id="wpcs_ploi_server_database_type">
			<option value="none"><?php esc_html_e( '-- No Database Installed --', 'wp-cloud-server-ploi' ); ?></option>
            <option value="mysql"><?php esc_html_e( 'MySQL', 'wp-cloud-server-ploi' ); ?></option>
            <option value="mariadb"><?php esc_html_e( 'MariaDB', 'wp-cloud-server-ploi' ); ?></option>
            <option value="postgresql"><?php esc_html_e( 'PostgreSQL', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}

	/**
	 *  Ploi Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_php_version() {

		$module_data	= get_option( 'wpcs_module_list' );
		?>
		<select style="width: 400px" name="wpcs_ploi_server_php_version" id="wpcs_ploi_server_php_version">
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
	 *  Ploi Field Callback for Server Region Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_region() {

		$list = wpcs_ploi_api_user_request( 'server-providers/list' );

		?>
		<select style="width: 400px" name="wpcs_ploi_server_region" id="wpcs_ploi_server_region">
			<?php
			if ( is_array( $list ) ) {
				?>
				<optgroup label="Regions">
				<?php
				foreach ( $list[0]['provider']['regions'] as $key => $plan ) {
				?>
    				<option value="<?php echo $plan['id']; ?>"><?php echo $plan['name']; ?></option>
				<?php
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
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_server_type() {
	
		?>
		<select style="width: 400px" name="wpcs_ploi_server_server_type" id="wpcs_ploi_server_server_type">
			<option value="server"><?php esc_html_e( 'Server', 'wp-cloud-server-ploi' ); ?></option>
			<option value="load-balancer"><?php esc_html_e( 'Load Balancer', 'wp-cloud-server-ploi' ); ?></option>
			<option value="database-server"><?php esc_html_e( 'Database Server', 'wp-cloud-server-ploi' ); ?></option>
			<option value="redis-server"><?php esc_html_e( 'Redis Server', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}
	
	/**
	 *  Ploi Field Callback for Server Type Setting.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_server_webserver_type() {
	
		?>
		<select style="width: 400px" name="wpcs_ploi_server_webserver_type" id="wpcs_ploi_server_webserver_type">
			<option value="nginx"><?php esc_html_e( 'NGINX', 'wp-cloud-server-ploi' ); ?></option>
		</select>
		<?php

	}
		


	/**
	 *  Sanitize Server Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_ploi_server_name( $name ) {

		$output = get_option( 'wpcs_ploi_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, and a period (.)', 'wp-cloud-server-ploi' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New Ploi Server is being Created.', 'wp-cloud-server-ploi' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_server_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}
}