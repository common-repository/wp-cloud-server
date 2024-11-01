<?php

function wpcs_vultr_create_user_github_startup_scrpts_template ( $tab_content, $page_content, $page_id ) {
	
	if ( 'vultr-create-user-github-startup-scripts' !== $tab_content ) {
		return;
	}
	
	if ( function_exists('wpcs_create_github_startup_script') && wpcs_check_module_active( 'GitHub' ) ) {
    	wpcs_create_github_startup_script( $tab_content, $page_content, $page_id );
	} else {
    	?>
    	<div class="uk-overflow-auto">
			<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'GitHub Startup Scripts', 'wp-cloud-server' ); ?></h2>
			<div class="uk-alert-upgrade" uk-alert>
				<p>Using GitHub Startup Scripts is available with the GitHub Add-on Module. Please <a href="#">click here</a> for more information.</p>
			</div>
		</div>
   	 	<?php
	}

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_vultr_create_user_github_startup_scrpts_template', 10, 3 );