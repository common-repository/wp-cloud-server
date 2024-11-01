/**
 * WP Cloud Server - Handling DigitalOcean Dashboard Tab Actions
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
		$(".digitalocean").click(function(event) {
			// Read the "data-tab" value from clicked link
        	var type = $(this).data('tab');
			var tabid = $(this).parent().data('tab-id');
			var data = {
				'action': 'digitalocean_dashboard_tabs',
				'digitalocean_dashboard_tabs_nonce': wpcs_digitalocean_dashboard_tabs_ajax_script.ajax_digitalocean_dashboard_tabs_nonce,
				'digitalocean_dashboard_tabs_type': type,
				'digitalocean_dashboard_tabs_id': tabid
			};
        	// Make an AJAX call
        	$.post( wpcs_digitalocean_dashboard_tabs_ajax_script.ajaxurl, data );
		});
	});
	
});