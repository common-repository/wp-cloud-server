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
		$("ul#serverpilot_apps_tabs li").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var data = {
				'action': 'serverpilot_dashboard_app_tabs',
				'serverpilot_dashboard_app_tabs_nonce': wpcs_serverpilot_dashboard_app_tabs_ajax_script.ajax_serverpilot_dashboard_app_tabs_nonce,
				'serverpilot_dashboard_app_tabs_type': type
			};
        	// Make an AJAX call
        	$.post( wpcs_serverpilot_dashboard_app_tabs_ajax_script.ajaxurl, data );
		});
	});
});