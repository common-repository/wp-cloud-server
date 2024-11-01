/**
 * WP Cloud Server - Delete Logged Data Confirm Delete Show/Hide
 *
 * @Author:             Gary Jordan (@Designed4Pixels)
 * @Date:               24-06-2019
 * @Last Modified by:   Gary Jordan (@Designed4Pixels)
 * @Last Modified time: 13-08-2019
 *
*/

jQuery(document).ready( function($) {

    $("#delete_logged_data").click(function (){
        if ($("#delete_logged_data").prop("checked")){
            $("#confirm-delete").show();
        }else{
            $("#confirm-delete").hide();
        }              
    });
	
});