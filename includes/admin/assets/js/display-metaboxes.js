/**
 * WP Cloud Server - Display EDD Meta Boxes for Module/Server Dropdown List
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

	$( function() {
		var moduleElement = document.getElementById("custom_field1");
    	if ( moduleElement ) {
        	var module = moduleElement.value;
        }
		var serverElement = document.getElementById("custom_field2");
    	if ( serverElement ) {
        	var server = serverElement.value;
        }
		if ( 'No Module' !== module ){
        	$("#server_value").show();
        }
		var data = {
			'action': 'display_metaboxes',
			'metaboxes_nonce': wpcs_display_metaboxes_script.ajax_display_metaboxes_nonce,
			'module': module,
			'server': server
		};
        $.post( wpcs_display_metaboxes_script.ajaxurl,
			data,
			function( response ) {
            	$('#custom_field2').empty().append(response);
       	 });
	});
});