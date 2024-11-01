/**
 * WP Cloud Server - Handling ServerPilot Dashboard Tab Actions
 *
 * @link       https://designedforpixels.com
 * @since      2.1.1
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

	$( function() {
		$(".serverpilot").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'serverpilot_dashboard_tabs',
				'serverpilot_dashboard_tabs_nonce': wpcs_serverpilot_dashboard_tabs_ajax_script.ajax_serverpilot_dashboard_tabs_nonce,
				'serverpilot_dashboard_tabs_type': type,
				'serverpilot_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_serverpilot_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});