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

	$("[id*='wpcs_ploi_edit_server_template_credentials_']").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var templateNameId = jQuery(this).data( 'template' );
		var element = `wpcs_ploi_edit_server_template_credentials_${templateNameId}`;
		var editServerTemplate = document.getElementById(element);
    	if ( editServerTemplate ) {
        	var edit_server_template = editServerTemplate.value;
        }

		var data = {
			'action': 'ploi_select_edit_server_template',
			'ploi_select_edit_server_template_nonce': wpcs_ploi_select_edit_server_template_ajax_script.ajax_ploi_select_edit_server_template_nonce,
			'edit_server_template': edit_server_template,
			'postid': postid
		};
        $.post( wpcs_ploi_select_edit_server_template_ajax_script.ajaxurl,
			data,
        	function( response ) {
            $('select[name="wpcs_ploi_edit_server_template_size"]').empty().append(response[0]);
			$('select[name="wpcs_ploi_edit_server_template_region"]').empty().append(response[1]);
       	});
    });
});