/**
 * WP Cloud Server - EDD Meta Boxes Select Server from Download List
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";
	
	$.fn.updatesp = function () {
    	var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_serverpilot_server_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_server_cloud_provider',
			'select_server_cloud_provider_nonce': wpcs_select_server_cloud_provider_script.ajax_select_server_cloud_provider_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_select_server_cloud_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_serverpilot_server_region"]').empty().append(response[0]);
			$('select[name="wpcs_serverpilot_server_size"]').empty().append(response[1]);
			$('select[name="wpcs_serverpilot_server_type"]').empty().append(response[2]);
       	});
	};
	
	$( function() {
		$("#wpcs_serverpilot_server_module").updatesp();
	});
	
	$("#wpcs_serverpilot_server_module").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_serverpilot_server_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_server_cloud_provider',
			'select_server_cloud_provider_nonce': wpcs_select_server_cloud_provider_script.ajax_select_server_cloud_provider_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_select_server_cloud_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_serverpilot_server_region"]').empty().append(response[0]);
			$('select[name="wpcs_serverpilot_server_size"]').empty().append(response[1]);
			$('select[name="wpcs_serverpilot_server_type"]').empty().append(response[2]);
       	});
    });
	
});