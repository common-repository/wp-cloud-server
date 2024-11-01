<?php

function wpcs_runcloud_create_ssh_key_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-create-ssh-key' !== $tabs_content ) {
		return;
	}
	
	wpcs_create_ssh_key( $tabs_content, $page_content, $page_id );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_create_ssh_key_template', 10, 3 );