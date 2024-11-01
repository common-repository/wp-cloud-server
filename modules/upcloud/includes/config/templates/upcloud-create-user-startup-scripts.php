<?php

function wpcs_upcloud_create_user_startup_scrpts_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'upcloud-create-user-startup-scripts' !== $tabs_content ) {
		return;
	}
	
	wpcs_create_startup_script( $tabs_content, $page_content, $page_id );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_upcloud_create_user_startup_scrpts_template', 10, 3 );