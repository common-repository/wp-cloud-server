<?php

function wpcs_linode_create_user_startup_scrpts_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-create-user-startup-scripts' !== $tabs_content ) {
		return;
	}
	
	wpcs_create_startup_script( $tabs_content, $page_content, $page_id );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_create_user_startup_scrpts_template', 10, 3 );