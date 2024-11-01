/**
 * WP Cloud Server - Handling AWS Lightsail Dashboard Tab Actions
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
		$(".aws_lightsail").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'aws_lightsail_dashboard_tabs',
				'aws_lightsail_dashboard_tabs_nonce': wpcs_aws_lightsail_dashboard_tabs_ajax_script.ajax_aws_lightsail_dashboard_tabs_nonce,
				'aws_lightsail_dashboard_tabs_type': type,
				'aws_lightsail_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_aws_lightsail_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});