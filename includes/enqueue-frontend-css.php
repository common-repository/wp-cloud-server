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
function wpcs_enqueue_frontend_styles( $hook ) {
		
	wp_enqueue_style( 'frontend-styles', plugin_dir_url( __FILE__ ) . 'assets/css/shortcode.css', array(), '1.0.0', 'all' );

}
add_action( 'wp_enqueue_scripts', 'wpcs_enqueue_frontend_styles' );