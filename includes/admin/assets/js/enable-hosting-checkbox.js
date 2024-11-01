/**
 * WP Cloud Server - Enable Cloud Hosting Checkbox
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

    $( "#wpcs_cloud_hosting_enabled" ).click( function (){
        if ( $( "#wpcs_cloud_hosting_enabled" ).prop("checked") ){
            $( "#wpcs_edd_hosting_meta_box" ).show();
        } else {
            $( "#wpcs_edd_hosting_meta_box" ).hide();
        }              
    });
	
});