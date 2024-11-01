/**
 * WP Cloud Server - Handling Dismissible Admin Notices
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

	$( function() {
		$('.wpcs-notice').on('click', '.uk-alert-close', function(event){
			// Read the "data-notice" information to track which notice
        	// is being dismissed and send it via AJAX
        	var type = $( this ).closest( '.uk-alert' ).data( 'notice' );
			//alert( type );
			var data = {
				'action': 'admin_dismiss',
				'admin_nonce': wpcs_admin_ajax_script.ajax_admin_nonce,
				'admin_type': type
			};
        	// Make an AJAX call
        	$.post( wpcs_admin_ajax_script.ajaxurl, data );
			});
		});
});