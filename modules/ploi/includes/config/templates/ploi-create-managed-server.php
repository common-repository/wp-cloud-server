<?php

/**
 * Provide a Admin Area Create Server Page
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_ploi_create_managed_server_template ( $tabs_content, $page_content, $page_id ) {

	if ( 'ploi-create-managed-server' !== $tabs_content ) {
		return;
	}

	$api_status		= wpcs_check_cloud_provider_api( 'Ploi', null, null, false );
	$attributes		= ( $api_status ) ? '' : 'disabled';
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wpcs_ploi_create_server' );
			wpcs_do_settings_sections( 'wpcs_ploi_create_server' );
			wpcs_submit_button( 'Create Server', 'secondary', 'create_server', null, $attributes );
			?>
		</form>
	</div>

	<?php

	$server_name = get_option( 'wpcs_ploi_server_name', '' );

	if ( '' !== $server_name ) {

		// Capture the Ploi Settings
		$server_size	       	= get_option( 'wpcs_ploi_server_size' );
		$server_region	        = get_option( 'wpcs_ploi_server_region' );
		$server_credential	    = get_option( 'wpcs_ploi_server_credentials' );
		$server_type	       	= get_option( 'wpcs_ploi_server_server_type' );
		$server_database_type	= get_option( 'wpcs_ploi_server_database_type' );
		$server_webserver_type	= get_option( 'wpcs_ploi_server_webserver_type' );
		$server_php_version	    = get_option( 'wpcs_ploi_server_php_version' );


		
		// Set-up the data for the new Droplet
		$args['api_data'] = array(
			"name"				=> $server_name, 
         	"plan"				=> $server_size, 
         	"region"			=> $server_region, 
         	"credential"		=> $server_credential, 
         	"type"				=> $server_type, 
         	"database_type"		=> $server_database_type, 
         	"webserver_type"	=> $server_webserver_type,
			"php_version"		=> $server_php_version,
		);

		// Send Create Server Request to Ploi API
		$api_response			= wpcs_ploi_api_server_request( 'servers/create', $args );
					
		// Delete the saved settings ready for next new server
		delete_option( 'wpcs_ploi_server_name' );
		delete_option( 'wpcs_ploi_server_size' );
		delete_option( 'wpcs_ploi_server_region' );
		delete_option( 'wpcs_ploi_server_credentials' );
		delete_option( 'wpcs_ploi_server_server_type' );
		delete_option( 'wpcs_ploi_server_database_type' );
		delete_option( 'wpcs_ploi_server_webserver_type' );
		delete_option( 'wpcs_ploi_server_php_version' );	
		
}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_create_managed_server_template', 10, 3 );