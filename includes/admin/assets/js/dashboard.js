/**
 * WP Cloud Server - Handling Dashboard Actions
 *
 * @link       https://designedforpixels.com
 * @since      2.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";
	
	$( function() {
		$(".uk-alert-success").delay(4000).fadeOut(1500);
	});

	$( function() {
		$("ul#switcher-menu li").click(function(event) {
			// Read the "data-position" value from clicked link
        	var type = $(this).data('position');
			var data = {
				'action': 'dashboard_update',
				'dashboard_nonce': wpcs_dashboard_ajax_script.ajax_dashboard_nonce,
				'dashboard_type': type
			};
        	// Make an AJAX call
        	$.post( wpcs_dashboard_ajax_script.ajaxurl, data );
		});
	});
	
});