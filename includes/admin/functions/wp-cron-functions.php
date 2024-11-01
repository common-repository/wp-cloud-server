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
 *  Set-up Cron Job Schedules.
 *
 *  @since 3.0.3
 */
function  wpcs_custom_cron_schedule( $schedules ) {

    $schedules[ 'one_minute' ]      = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    $schedules[ 'thirty_minutes' ]  = array( 'interval' => 0.5 * MINUTE_IN_SECONDS, 'display' => __( 'Thirty Minutes', 'wp-cloud-server' ) );

    return $schedules;
}
add_filter( 'cron_schedules', 'wpcs_custom_cron_schedule' );