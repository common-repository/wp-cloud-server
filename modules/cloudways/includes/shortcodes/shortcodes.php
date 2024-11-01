<?php
/**
 * Shortcodes
 *
 * @package     EDD
 * @subpackage  Shortcodes
 * @copyright   Copyright (c) 2020, DesignedforPixels
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_create_application_shortcode() {
	ob_start();

	wpcs_get_template_part( 'shortcode', 'create-app' );

	$display = ob_get_clean();

	return $display;
}
add_shortcode( 'wpcs_cloudways_create_app', 'wpcs_create_application_shortcode' );