<?php

function wpcs_aws_lightsail_list_ssh_keys_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'aws-lightsail-list-ssh-keys' !== $tabs_content ) {
		return;
	}
	
	wpcs_list_ssh_keys( $tabs_content, $page_content, $page_id, 'aws-lightsail' );

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_aws_lightsail_list_ssh_keys_template', 10, 3 );