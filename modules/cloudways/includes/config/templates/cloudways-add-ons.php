<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_cloudways_snapshot_details_template( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-add-ons' !== $tabs_content ) {
		return;
	}
	
	do_action( 'wpcs_cloudways_snapshot_content' );			
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_snapshot_details_template', 10, 3 );

function wpcs_cloudways_snapshot_upgrade() {		
	?>
	<div class="uk-overflow-auto">
		<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Cloudways Add-ons', 'wp-cloud-server' ); ?></h2>
		<div class="uk-alert-upgrade" uk-alert>
			<p>Cloudways Snapshots are available with the Cloudways Pro Module. Please <a href="#">click here</a> for more information</p>
		</div>
	</div>
    <?php	
}
add_action( 'wpcs_cloudways_snapshot_content', 'wpcs_cloudways_snapshot_upgrade' );