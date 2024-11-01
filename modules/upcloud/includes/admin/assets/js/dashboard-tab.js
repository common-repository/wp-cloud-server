/**
 * WP Cloud Server - Handling Vultr Dashboard Tab Actions
 *
 * @link       https://designedforpixels.com
 * @since      1.0.1
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

	$( function() {
		$(".upcloud").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'upcloud_dashboard_tabs',
				'upcloud_dashboard_tabs_nonce': wpcs_upcloud_dashboard_tabs_ajax_script.ajax_upcloud_dashboard_tabs_nonce,
				'upcloud_dashboard_tabs_type': type,
				'upcloud_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_upcloud_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});