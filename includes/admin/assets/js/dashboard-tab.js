/**
 * WP Cloud Server - Delete Logged Data Confirm Delete Show/Hide
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
		$(".settings").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'settings_dashboard_tabs',
				'settings_dashboard_tabs_nonce': wpcs_settings_dashboard_tabs_ajax_script.ajax_settings_dashboard_tabs_nonce,
				'settings_dashboard_tabs_type': type,
				'settings_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_settings_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});