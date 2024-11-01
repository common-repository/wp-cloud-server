<?php
/**
 * Deprecated Functions
 *
 * All functions that have been deprecated.
 *
 * @package     WP Cloud Server
 * @subpackage  Deprecated
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Log an event for display in the log tab
 *
 *  @since  1.0.0
 */
function wpcs_log_event( $new_event, $new_status, $new_desc ) {

	// Sanitize the new event details
	$event	= sanitize_text_field( $new_event );
	$status = sanitize_text_field( $new_status );
	$desc	= sanitize_text_field( $new_desc );
			
	$log_count = 0;
	$logged_data = get_option( 'wpcs_logged_data', array() );
			
	if ( is_array( $logged_data ) ) {
		$log_count = count( $logged_data );
	}
			
	// Limit Log to 20 entries
	if ( $log_count >= 20 ) {
		array_shift( $logged_data );	
	}
			
	// Create Date and Time Stamp
	$date = date("D j M Y G:i:s");

	$data = array(
			'date'			=> $date,
    		'event'  		=> $event,
			'status'		=> $status,
			'description'  	=> $desc
			);
			
	// Add new logged event to array
	array_push( $logged_data, $data );
			
	update_option( 'wpcs_logged_data', $logged_data );
		
	// Executes after a log event has been performed
	do_action( 'wpcs_after_log_event', $data );
			
}