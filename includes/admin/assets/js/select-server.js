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
	
	    $("#custom_field1").on('change', function(){
		var postid = jQuery(this).data( 'post' );
		var moduleElement = document.getElementById("custom_field1");
    	if ( moduleElement ) {
        	var module= moduleElement.value;
        }

		var data = {
			'action': 'select_server',
			'server_nonce': wpcs_select_server_script.ajax_select_server_nonce,
			'module': module,
			'postid': postid
		};
        $.post( wpcs_select_server_script.ajaxurl,
			data,
        	function( response ) {
            $('#custom_field2').empty().append(response);
       	});
    });
});