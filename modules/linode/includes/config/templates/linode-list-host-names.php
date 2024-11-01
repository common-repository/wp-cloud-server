<?php

function wpcs_linode_list_host_names_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'linode-list-host-names' !== $tabs_content ) {
		return;
	}
	
	wpcs_list_host_names( $tabs_content, $page_content, $page_id, 'linode' );
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_linode_list_host_names_template', 10, 3 );