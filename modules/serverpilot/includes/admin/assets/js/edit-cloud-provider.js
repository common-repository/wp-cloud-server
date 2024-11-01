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
	
	$.fn.updatetp = function () {
    	var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_serverpilot_edit_template_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'edit_cloud_provider',
			'edit_cloud_provider_nonce': wpcs_edit_cloud_provider_script.ajax_edit_cloud_provider_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_edit_cloud_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_serverpilot_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_serverpilot_template_size"]').empty().append(response[1]);
			$('select[name="wpcs_serverpilot_template_type"]').empty().append(response[2]);
       	});
	};
	
	$( function() {
		$("#wpcs_serverpilot_edit_template_module").updatetp();
	});
	
	$("[id*='wpcs_serverpilot_edit_template_module_']").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var templateNameId = jQuery(this).data( 'template' );
		var element = `wpcs_serverpilot_edit_template_module_${templateNameId}`;
		var cloudProvider = document.getElementById(element);
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }

		var data = {
			'action': 'edit_cloud_provider',
			'edit_cloud_provider_nonce': wpcs_edit_cloud_provider_script.ajax_edit_cloud_provider_nonce,
			'cloud_provider': cloud_provider,
			'postid': postid
		};
        $.post( wpcs_edit_cloud_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_serverpilot_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_serverpilot_template_size"]').empty().append(response[1]);
			$('select[name="wpcs_serverpilot_template_type"]').empty().append(response[2]);
       	});
    });
	
});