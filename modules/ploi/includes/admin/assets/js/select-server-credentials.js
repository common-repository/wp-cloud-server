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
	
	$.fn.updatetmp = function () {
		var postid = jQuery(this).data( 'post' );
		var serverCredentials = document.getElementById("wpcs_ploi_server_credentials");
    	if ( serverCredentials ) {
        	var server_credentials = serverCredentials.value;
        }

		var data = {
			'action': 'ploi_select_server_credentials',
			'ploi_select_server_credentials_nonce': wpcs_ploi_select_server_credentials_ajax_script.ajax_ploi_select_server_credentials_nonce,
			'server_credentials': server_credentials,
			'postid': postid
		};
		
        $.post( wpcs_ploi_select_server_credentials_ajax_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_ploi_server_size"]').empty().append(response[0]);
			$('select[name="wpcs_ploi_server_region"]').empty().append(response[1]);
       	});
	};
	
	$( function() {
		$("#wpcs_ploi_server_credentials").updatetmp();
	});
	
	$("#wpcs_ploi_server_credentials").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var serverCredentials = document.getElementById("wpcs_ploi_server_credentials");
    	if ( serverCredentials ) {
        	var server_credentials = serverCredentials.value;
        }

		var data = {
			'action': 'ploi_select_server_credentials',
			'ploi_select_server_credentials_nonce': wpcs_ploi_select_server_credentials_ajax_script.ajax_ploi_select_server_credentials_nonce,
			'server_credentials': server_credentials,
			'postid': postid
		};
		
        $.post( wpcs_ploi_select_server_credentials_ajax_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_ploi_server_size"]').empty().append(response[0]);
			$('select[name="wpcs_ploi_server_region"]').empty().append(response[1]);
       	});
    });
});