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
		var cloudwaysProvider = document.getElementById("wpcs_cloudways_template_providers");
    	if ( cloudwaysProvider ) {
        	var cloudways_template_provider= cloudwaysProvider.value;
        }

		var data = {
			'action': 'select_cloudways_template_provider',
			'select_cloudways_template_provider_nonce': wpcs_select_cloudways_template_provider_script.ajax_select_cloudways_template_provider_nonce,
			'cloudways_template_provider': cloudways_template_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloudways_template_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_cloudways_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_cloudways_template_size"]').empty().append(response[1]);
       	});
	};
	
	$( function() {
		$("#wpcs_cloudways_template_providers").updatetp();
	});
	
	$("#wpcs_cloudways_template_providers").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var cloudwaysProvider = document.getElementById("wpcs_cloudways_template_providers");
    	if ( cloudwaysProvider ) {
        	var cloudways_template_provider= cloudwaysProvider.value;
        }

		var data = {
			'action': 'select_cloudways_template_provider',
			'select_cloudways_template_provider_nonce': wpcs_select_cloudways_template_provider_script.ajax_select_cloudways_template_provider_nonce,
			'cloudways_template_provider': cloudways_template_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloudways_template_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_cloudways_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_cloudways_template_size"]').empty().append(response[1]);
       	});
    });
	
});