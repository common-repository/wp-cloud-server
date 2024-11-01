/**
 * WP Cloud Server - Update Region and Size from selected Cloud Provider
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
		var cloudProvider = document.getElementById("wpcs_digitalocean_template_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_cloud_server_template',
			'select_cloud_server_template_nonce': wpcs_select_cloud_server_template_script.ajax_select_cloud_server_template_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
		
        $.post( wpcs_select_cloud_server_template_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_digitalocean_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_digitalocean_template_size"]').empty().append(response[1]);
			$('select[name="wpcs_digitalocean_template_type"]').empty().append(response[2]);
       	});
	};
	
	$( function() {
		$("#wpcs_digitalocean_template_module").updatetmp();
	});
	
	$("#wpcs_digitalocean_template_module").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_digitalocean_template_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'select_cloud_server_template',
			'select_cloud_server_template_nonce': wpcs_select_cloud_server_template_script.ajax_select_cloud_server_template_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
		
        $.post( wpcs_select_cloud_server_template_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_digitalocean_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_digitalocean_template_size"]').empty().append(response[1]);
			$('select[name="wpcs_digitalocean_template_type"]').empty().append(response[2]);
       	});
    });
});