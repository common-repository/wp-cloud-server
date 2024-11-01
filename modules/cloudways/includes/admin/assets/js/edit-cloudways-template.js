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
		var templateNameId = jQuery(this).data( 'template' );
		var element = `wpcs_cloudways_edit_template_providers_${templateNameId}`;
		var cloudwaysProvider = document.getElementById(element);
    	if ( cloudwaysProvider ) {
        	var cloudways_edit_template_provider= cloudwaysProvider.value;
        }

		var data = {
			'action': 'select_cloudways_edit_template_provider',
			'select_cloudways_edit_template_provider_nonce': wpcs_select_cloudways_edit_template_provider_script.ajax_select_cloudways_edit_template_provider_nonce,
			'cloudways_edit_template_provider': cloudways_edit_template_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloudways_edit_template_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="ccccccwpcs_cloudways_edit_template_region"]').empty().append(response[0]);
			$('select[name="ccccccwpcs_cloudways_edit_template_size"]').empty().append(response[1]);
       	});
	};
	
	$( function() {
		$("[id*='wpcs_cloudways_edit_templateffff_providers_']").updatetp();
	});
	
	$("[id*='wpcs_cloudways_edit_template_providers_']").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var templateNameId = jQuery(this).data( 'template' );
		var element = `wpcs_cloudways_edit_template_providers_${templateNameId}`;
		var cloudwaysProvider = document.getElementById(element);
    	if ( cloudwaysProvider ) {
        	var cloudways_edit_template_provider= cloudwaysProvider.value;
        }

		var data = {
			'action': 'select_cloudways_edit_template_provider',
			'select_cloudways_edit_template_provider_nonce': wpcs_select_cloudways_edit_template_provider_script.ajax_select_cloudways_edit_template_provider_nonce,
			'cloudways_edit_template_provider': cloudways_edit_template_provider,
			'postid': postid
		};
        $.post( wpcs_select_cloudways_edit_template_provider_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_cloudways_edit_template_region"]').empty().append(response[0]);
			$('select[name="wpcs_cloudways_edit_template_size"]').empty().append(response[1]);
       	});
    });
	
});