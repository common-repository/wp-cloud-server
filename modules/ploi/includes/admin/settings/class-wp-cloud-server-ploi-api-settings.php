<?php

/**
 * WP Cloud Server - Ploi Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server_API_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_API_Settings {

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_api_setting_sections_and_fields' ) );

	}
		
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_api_setting_sections_and_fields() {

		$args = array(
            'type' => 'string', 
            'sanitize_callback' => array( $this, 'sanitize_ploi_api_token' ),
            'default' => NULL,
        );

		register_setting( 'wpcs_ploi_admin_menu', 'wpcs_ploi_api_key' );

		add_settings_section(
			'wpcs_ploi_admin_menu',
			esc_attr__( 'Ploi API Credentials', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_section_callback_ploi_api' ),
			'wpcs_ploi_admin_menu'
		);

		add_settings_field(
			'wpcs_ploi_api_key',
			esc_attr__( 'API Key:', 'wp-cloud-server-ploi' ),
			array( $this, 'wpcs_field_callback_ploi_api_key' ),
			'wpcs_ploi_admin_menu',
			'wpcs_ploi_admin_menu'
		);

	}
		
	/**
	 *  Ploi API Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_section_callback_ploi_api() {

		echo '<p>';
		echo 'WP Cloud Server uses the official Ploi REST API. Generate then copy your API credentials via the <a class="uk-link" href="https://cloud.ploi.com/login" target="_blank">Ploi Dashboard</a>.';
		echo '</p>';

	}

	/**
	 *  Ploi API Field Callback for User Name.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_field_callback_ploi_api_key() {

		$value = get_option( 'wpcs_ploi_api_key' );
		$value = ( ! empty( $value ) ) ? $value : '';
		$readonly = ( ! empty( $value ) ) ? ' disabled' : '';

		echo '<input style="width: 25rem;" class="w-400" type="password" id="wpcs_ploi_api_key" name="wpcs_ploi_api_key" value="' . esc_attr( $value ) . '"' . $readonly . '/>';

	}

	/**
	 *  Sanitize API Token
	 *
	 *  @since  1.0.0
	 *  @param  string  $token original API token
	 *  @return string  checked API token
	 */
	public function sanitize_ploi_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_ploi_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The Ploi API Token was updated.', 'wp-cloud-server-ploi' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Ploi API Token!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}
	
}