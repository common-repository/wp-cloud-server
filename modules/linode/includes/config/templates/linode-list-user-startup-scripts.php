<?php

function wpcs_linode_list_user_startup_script_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-list-user-startup-scripts' !== $tabs_content ) {
		return;
	}
	
	wpcs_list_startup_scripts( $tabs_content, $page_content, $page_id, 'linode' );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_list_user_startup_script_template', 10, 3 );