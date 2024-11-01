/**
 * WP Cloud Server - Select System User from Server
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";
	
	$.fn.updateserverid = function () {
    	var postid = jQuery(this).data( 'post' );
		var serverId = document.getElementById("wpcs_runcloud_create_app_server_id");
    	if ( serverId ) {
        	var server_id = serverId.value;
        }

		var data = {
			'action': 'runcloud_create_app_server_id',
			'runcloud_create_app_server_id_setting_nonce': wpcs_runcloud_create_app_server_id_script.ajax_runcloud_create_app_server_id_nonce,
			'server_id': server_id,
			'postid': postid
		};
		
        $.post( wpcs_runcloud_create_app_server_id_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_runcloud_create_app_user"]').empty().append(response);
       	});
	};
	
	$( function() {
		$("#wpcs_runcloud_create_app_server_id").updateserverid();
	});
	
	$("#wpcs_runcloud_create_app_server_id").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var serverId = document.getElementById("wpcs_runcloud_create_app_server_id");
    	if ( serverId ) {
        	var server_id = serverId.value;
        }

		var data = {
			'action': 'runcloud_create_app_server_id',
			'runcloud_create_app_server_id_setting_nonce': wpcs_runcloud_create_app_server_id_script.ajax_runcloud_create_app_server_id_nonce,
			'server_id': server_id,
			'postid': postid
		};
		
        $.post( wpcs_runcloud_create_app_server_id_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_runcloud_create_app_user"]').empty().append(response);
       	});
    });
	
});