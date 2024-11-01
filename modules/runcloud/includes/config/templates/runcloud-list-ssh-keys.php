<?php

function wpcs_runcloud_list_ssh_keys_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-list-ssh-keys' !== $tabs_content ) {
		return;
	}
	
	wpcs_list_ssh_keys( $tabs_content, $page_content, $page_id, 'runcloud' );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_list_ssh_keys_template', 10, 3 );