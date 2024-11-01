/**
 * WP Cloud Server - Handling Cloudways Dashboard Tab Actions
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
		$(".cloudways").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'cloudways_dashboard_tabs',
				'cloudways_dashboard_tabs_nonce': wpcs_cloudways_dashboard_tabs_ajax_script.ajax_cloudways_dashboard_tabs_nonce,
				'cloudways_dashboard_tabs_type': type,
				'cloudways_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_cloudways_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});