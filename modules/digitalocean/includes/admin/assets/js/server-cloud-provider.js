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
	
	$.fn.updatedo = function () {
		var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_digitalocean_server_cloud_provider");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_cloud_server_server',
			'select_cloud_server_server_nonce': wpcs_select_cloud_server_server_script.ajax_select_cloud_server_server_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloud_server_server_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_digitalocean_server_region"]').empty().append(response[0]);
			$('select[name="wpcs_digitalocean_server_size"]').empty().append(response[1]);
			$('select[name="wpcs_digitalocean_server_type"]').empty().append(response[2]);
       	});
	};
	
	$( function() {
		$("#wpcs_digitalocean_server_cloud_provider").updatedo();
	});
	
	    $("#wpcs_digitalocean_server_cloud_provider").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_digitalocean_server_cloud_provider");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_cloud_server_server',
			'select_cloud_server_server_nonce': wpcs_select_cloud_server_server_script.ajax_select_cloud_server_server_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloud_server_server_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_digitalocean_server_region"]').empty().append(response[0]);
			$('select[name="wpcs_digitalocean_server_size"]').empty().append(response[1]);
			$('select[name="wpcs_digitalocean_server_type"]').empty().append(response[2]);
       	});
    });
});