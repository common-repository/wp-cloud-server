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
	
	$("#wpcs_serverpilot_template_region").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var cloudProvider = document.getElementById("wpcs_serverpilot_template_module");
    	if ( cloudProvider ) {
        	var cloud_provider= cloudProvider.value;
        }
		var planSize = document.getElementById("wpcs_serverpilot_template_region");
    	if ( planSize ) {
        	var plan_size= planSize.value;
        }

		var data = {
			'action': 'select_vultr_plan',
			'select_vultr_plan_nonce': wpcs_select_vultr_plan_script.ajax_select_vultr_plan_nonce,
			'cloud_provider': cloud_provider,
			'plan_size': plan_size,
			'postid': postid
		};
        $.post( wpcs_select_vultr_plan_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_serverpilot_template_size"]').empty().append(response);
       	});
    });
});