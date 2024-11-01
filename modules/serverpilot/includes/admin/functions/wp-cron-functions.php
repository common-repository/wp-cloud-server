<?php
/**
 * WP Cron Functions.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Five Minute Cron Schedule.
 *
 *  @since 1.1.0
 */
function  wpcs_serverpilot_custom_cron_schedule( $schedules ) {
    $schedules[ 'five_minutes' ] = array( 'interval' => 5 * MINUTE_IN_SECONDS, 'display' => __( 'Five Minutes', 'wp-cloud-server' ) );
    return $schedules;
}
add_filter( 'cron_schedules', 'wpcs_serverpilot_custom_cron_schedule' );