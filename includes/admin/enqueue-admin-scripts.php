<?php

/**
 * Enqueue Admin Scripts.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the stylesheets for the admin area.
 *
 * @since    	1.0.0
 */
function wpcs_enqueue_styles( $hook ) {
		
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'nopage';
		
	wp_enqueue_style( 'admin-styles', plugin_dir_url( __FILE__ ) . 'assets/css/admin-styles.css', array(), '1.0.0', 'all' );
		
	$full_width_page= substr($page, -10, 10);
		
	if ( ( 'full-width' == $full_width_page ) ) {
		wp_enqueue_style( 'modified-wordpress', plugin_dir_url( __FILE__ ) . 'assets/css/modified-wordpress.css', array(), '1.0.0', 'all' );
	}
		
	$sub_menu_page	= substr($page, 0, 15);
		
	if ( ( 'wp-cloud-server' == $sub_menu_page ) ) {
		wp_enqueue_style( 'uikit-styles', WPCS_PLUGIN_URL . 'vendor/uikit/css/uikit.min.css', array(), '1.0.0', 'all' );
		wp_enqueue_style( 'uikit-mods', WPCS_PLUGIN_URL . 'vendor/uikit/css/uikit-modifications.css', array(), '1.0.0', 'all' );
	}

}
add_action( 'admin_enqueue_scripts', 'wpcs_enqueue_styles' );

/**
 * Register the JavaScript for the admin area.
 *
 * @since  1.0.0
 */
function wpcs_enqueue_scripts() {
	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'nopage';
		
	wp_enqueue_script( 'enable-hosting-checkbox', plugin_dir_url( __FILE__ ) . 'assets/js/enable-hosting-checkbox.min.js', array( 'jquery' ), '1.0.0', false );
	wp_enqueue_script( 'general-settings', plugin_dir_url( __FILE__ ) . 'assets/js/general-settings.min.js', array( 'jquery' ), '1.0.0', false );

	$sub_menu_page	= substr( $page, 0, 15 );
		
	if ( ( 'wp-cloud-server' == $sub_menu_page ) ) {
		wp_enqueue_script( 'uikit-icons', WPCS_PLUGIN_URL . 'vendor/uikit/js/uikit.min.js', array( 'jquery' ), '1.0.0', false );
		wp_enqueue_script( 'uikit', WPCS_PLUGIN_URL . 'vendor/uikit/js/uikit-icons.min.js', array( 'jquery' ), '1.0.0', false );
	}

}
add_action( 'admin_enqueue_scripts', 'wpcs_enqueue_scripts' );