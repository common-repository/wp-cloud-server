<?php

/**
 * Provide a Admin Area Add Template Page for the Digitalocean Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_ploi_install_website_template ( $tabs_content, $page_content, $page_id ) {

	if ( 'ploi-install-website' !== $tabs_content ) {
		return;
	}

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$module_name	=  'Ploi';
$api_status		= wpcs_check_cloud_provider_api('Ploi', null, null, false);
$attributes		= ( $api_status ) ? '' : 'disabled';
	
$api			= new WP_Cloud_Server_Ploi_API();
	
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$forge_response	= '';
?>

<div class="content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpcs_ploi_create_app' );
		wpcs_do_settings_sections( 'wpcs_ploi_create_app' );
		wpcs_submit_button( 'Create New Website', 'secondary', 'create_app', null, $attributes );
		?>
	</form>
</div>
<?php

$debug_data = array(
	"server_id"					=>	get_option( 'wpcs_ploi_create_app_server_id' ),
	"root_domain"				=>	get_option( 'wpcs_ploi_create_app_root_domain' ),
	"project_directory"			=>	get_option( 'wpcs_ploi_create_app_project_directory' ),
	"web_directory"				=>	get_option( 'wpcs_ploi_create_app_web_directory' ),
	"system_user"				=>	get_option( 'wpcs_ploi_create_app_system_user' ),
	"web_template"				=>	get_option( 'wpcs_ploi_create_app_web_template' ),
	"install_app"				=>	get_option( 'wpcs_ploi_create_app_install_app' ),
	"enable_ssl"				=>	get_option( 'wpcs_ploi_create_app_enable_ssl' ),
);

$root_domain = get_option( 'wpcs_ploi_create_app_root_domain', '' );

if ( '' !== $root_domain ) {

	$server_id					= get_option( 'wpcs_ploi_create_app_server_id' );
	$project_root				= get_option( 'wpcs_ploi_create_app_project_directory' );
	$web_directory				= get_option( 'wpcs_ploi_create_app_web_directory' );
	$system_user				= get_option( 'wpcs_ploi_create_app_system_user' );
	$web_template				= get_option( 'wpcs_ploi_create_app_web_template' );
	$install_app				= get_option( 'wpcs_ploi_create_app_install_app' );
	$enable_ssl					= get_option( 'wpcs_ploi_create_app_enable_ssl' );


	$site_install_app_explode		= explode( '|', $install_app );
	$site_install_app				= $site_install_app_explode[0];
	$site_install_app_type			= isset( $site_install_app_explode[1] ) ? $site_install_app_explode[1] : '';
	
	$args['api_data'] = array(
		"root_domain"			=> $root_domain,
    	"project_root"			=> ( '' == $project_root ) ? '/' : $project_root,
    	"web_directory"			=> ( '' == $web_directory ) ? '/' : $web_directory,
		"system_user"			=> $system_user,
    	"webserver_template"	=> $web_template,
	);

	// Send Create Site Request to Ploi API
	$args['server_id'] 			= $server_id;
	$response					= wpcs_ploi_api_sites_request( 'sites/create', $args );

	$debug['site_args']			= $args;
	$debug['site_response']		= $response;
	$debug['app_type']			= $site_install_app_type;

	unset( $args['api_data'] );

	// Wait for the GIT install to Complete
	$x							= 1;
	$status						= 'installng-repository';
	$args['site_id'] 			= $response['id'];

	while ( ( "active" !== $status ) && ( $x <= 30 ) ) {

		$actions		= wpcs_ploi_api_sites_request( 'sites/get', $args );
		$status			= $actions['status'];

			$x++;
		$debug['sitecounter']['status']	= $status;
		$debug['sitecounter']['count']	= $x;
	}

	// Send Install Application Request to Ploi API
	if ( ( 'no-application' !== $site_install_app ) && ( 'app' == $site_install_app_type ) && isset( $response['id'] ) ) {

		$args['site_id'] 		= $response['id'];
		$args['api_data']		= ( 'wordpress' == $site_install_app ) ? array( 'create_database' => true ) : null;
		$response				= wpcs_ploi_api_sites_request( "sites/install/{$site_install_app}", $args );

		$debug['app_response']	= $response;
		$debug['app_args']		= $args;

		// Wait 1 second
		sleep(1);
	}

	// Send Install from Repository Request to Ploi API
	if ( ( 'git' == $site_install_app_type ) && isset( $response['id'] ) ) {

		$owner					= wpcs_github_repo_owner();
		$args['site_id'] 		= $response['id'];
		$site_id 				= $response['id'];

		// Install the GitHub Repository
		$args['api_data']		= array(
			"provider"			=> 'github',
			"branch"			=> 'main',
			"name"				=> "{$owner}/{$site_install_app}",
		);

		$response					= wpcs_ploi_api_sites_request( "sites/install/git", $args );

		$debug['git_response']		= $response;
		$debug['git_args']			= $args;

		unset( $args['api_data'] );

		// Wait for the GIT install to Complete
		$x							= 1;
		$status						= 'installng-repository';

		while ( ( "active" !== $status ) && ( $x <= 30 ) ) {

			$actions		= wpcs_ploi_api_sites_request( 'sites/get', $args );
			$status			= $actions['status'];

			$x++;
			$debug['gitcounter']['status']	= $status;
			$debug['gitcounter']['count']	= $x;
		}

		// Wait 1 second
		sleep(1);

		// Update the deploy script
		$args['api_data']		= array(
			"deploy_script"		=> "cd /home/ploi/{$root_domain}",
		);

		$actions		= wpcs_ploi_api_sites_request( 'sites/install/git/deploy/update/script', $args );				

		$debug['script_response']	= $actions;
		$debug['script_args']		= $args;

		// Wait 1 second
		sleep(1);

		// Deploys the site, remember that a deploy will not be done if there is no repository installed. 
		unset( $args['api_data'] );

		$response					= wpcs_ploi_api_sites_request( "sites/install/git/deploy", $args );

		$debug['deploy_response']	= $response;
		$debug['deploy_args']		= $args;

	}

	// Install a Let's Encrypt SSL Certifcate if enabled
	if ( $enable_ssl ) {

		$args['api_data']	= array(
			"certifcate"	=> $root_domain,
			"type"			=> 'letsencrypt',
		);

		$response				= wpcs_ploi_api_sites_request( "sites/certificates/create", $args );

		$debug['ssl_response']	= $response;
		$debug['ssl_args']		= $args;
	}

	update_option( 'wpcs_ploi_site_debug', $debug );

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_ploi_create_app_server_id' );
	delete_option( 'wpcs_ploi_create_app_root_domain' );
	delete_option( 'wpcs_ploi_create_app_project_directory' );
	delete_option( 'wpcs_ploi_create_app_web_directory' );
	delete_option( 'wpcs_ploi_create_app_web_template' );
	delete_option( 'wpcs_ploi_create_app_system_user' );
	delete_option( 'wpcs_ploi_create_app_install_app' );
	delete_option( 'wpcs_ploi_create_app_enable_ssl' );
}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_ploi_install_website_template', 10, 3 );