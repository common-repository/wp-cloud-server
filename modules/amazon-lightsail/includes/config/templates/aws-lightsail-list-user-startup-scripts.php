<?php

function wpcs_aws_lightsail_list_user_startup_script_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'aws-lightsail-list-user-startup-scripts' !== $tabs_content ) {
		return;
	}
	
	wpcs_list_startup_scripts( $tabs_content, $page_content, $page_id, 'aws-lightsail' );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_aws_lightsail_list_user_startup_script_template', 10, 3 );