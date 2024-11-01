/**
 * WP Cloud Server - EDD Meta Boxes for Module/Server Dropdown List
 *
 * @Author: 			Gary Jordan (@Designed4Pixels)
 * @Date:   			24-06-2019
 * @Last Modified by:   Gary Jordan (@Designed4Pixels)
 * @Last Modified time: 13-08-2019
 *
*/

jQuery(document).ready( function($) {
	
	$(function(){
		var moduleElement = document.getElementById("custom_field1");
    	if ( moduleElement ) {
        	var module= moduleElement.value;
        }
		if ( 'No Module' !== module ){
        	$("#server_value").show();
            }
			var data = {
			'action': 'module_selection',
			'new_module': module      // We pass php values differently!
			};
        	// Make an AJAX call
        	// Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.post( ajaxurl,
				   	data,
        			function( response ) {
            			if(response['success'] != false) {
                			$.each(response,function(index,value){
                    			$('#custom_field2').empty().append('<option value="'+value+'">'+value+'</option>');
                			});
            			}
       	 			});
	});

    $('#custom_field1').on('change', function(){
		var moduleElement = document.getElementById("custom_field1");
    	if ( moduleElement ) {
        	var module= moduleElement.value;
        }
		if ( 'No Module' !== module ){
        	$("#server_value").show();
            }
				if ( 'No Module' == module ){
        	$("#server_value").hide();
            }
			var data = {
			'action': 'module_selection',
			'new_module': module
			};
        	// Make an AJAX call
        	// Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        	$.post( ajaxurl,
				   	data,
        			function( response ) {
            			if (response['success'] != false) {
                			$.each(response,function(index,value){
                    			$('#custom_field2').empty().append('<option value="'+value+'">'+value+'</option>');
                			});
            			}
       	 			});
    });
	
});
