/**
 * WP Cloud Server - Handling Dismissible Admin Notices
 *
 * @Author: 			Gary Jordan (@Designed4Pixels)
 * @Date:   			24-06-2019
 * @Last Modified by:   Gary Jordan (@Designed4Pixels)
 * @Last Modified time: 13-08-2019
 *
*/

jQuery(document).ready( function($) {
	$('.notice.is-dismissible').on('click', '.notice-dismiss', function(event){
		// Read the "data-notice" information to track which notice
        // is being dismissed and send it via AJAX
        var type = $( this ).closest( '.wpcs-notice' ).data( 'notice' );
		var data = {
		'action': 'test_response',
		'type': type
		};
        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post( ajaxurl, data );
		});
	});