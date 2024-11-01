<?php

function wpcs_vultr_create_host_name_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'vultr-create-host-name' !== $tabs_content ) {
		return;
	}

	wpcs_create_host_name( $tabs_content, $page_content, $page_id );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_create_host_name_template', 10, 3 );