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

function wpcs_runcloud_install_managed_website_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-install-managed-website' !== $tabs_content ) {
		return;
	}

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$module_name	=  'RunCloud';
$api_status		= wpcs_check_cloud_provider_api('RunCloud');
$attributes		= ( $api_status ) ? '' : 'disabled';
	
$api			= new WP_Cloud_Server_RunCloud_API();
	
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$runcloud_response	= '';
?>

<div class="content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpcs_runcloud_create_app' );
		wpcs_do_settings_sections( 'wpcs_runcloud_create_app' );
		wpcs_submit_button( 'Create Web App', 'secondary', 'create_app', null, $attributes );
		?>
	</form>
</div>
<?php

$debug_data = array(
	"app_name"				=>	get_option( 'wpcs_runcloud_create_app_name' ),
	"app_server_id"			=>	get_option( 'wpcs_runcloud_create_app_server_id' ),
	"app_application"		=>	get_option( 'wpcs_runcloud_create_app_application' ),
	"app_domain"			=>	get_option( 'wpcs_runcloud_create_app_domain' ),
	"app_user"				=>	get_option( 'wpcs_runcloud_create_app_user' ),
	"app_new_sys_user"		=>	get_option( 'wpcs_runcloud_create_app_system_user_name' ),	
	"app_new_sys_user_pwd"	=>	get_option( 'wpcs_runcloud_create_app_system_user_password' ),	
	"app_web_directory"		=>	get_option( 'wpcs_runcloud_create_app_web_directory' ),
	"app_php_version"		=>	get_option( 'wpcs_runcloud_create_app_php_version' ),
	"app_stack"				=>	get_option( 'wpcs_runcloud_create_app_stack' ),
	"app_stack_mode"		=>	get_option( 'wpcs_runcloud_create_app_stack_mode' ),
	"app_default_app"		=>	get_option( 'wpcs_runcloud_create_app_default_app' ),
);

$app_name = get_option( 'wpcs_runcloud_create_app_name', '' );

if ( '' !== $app_name ) {

	$app_server_id			= get_option( 'wpcs_runcloud_create_app_server_id' );
	$app_application		= get_option( 'wpcs_runcloud_create_app_application' );
	$app_domain				= get_option( 'wpcs_runcloud_create_app_domain' );
	$app_user				= get_option( 'wpcs_runcloud_create_app_user' );
	$app_new_sys_user		= get_option( 'wpcs_runcloud_create_app_system_user_name' );	
	$app_new_sys_user_pwd	= get_option( 'wpcs_runcloud_create_app_system_user_password' );
	$app_web_directory		= get_option( 'wpcs_runcloud_create_app_web_directory' );
	$app_php_version		= get_option( 'wpcs_runcloud_create_app_php_version' );
	$app_stack				= get_option( 'wpcs_runcloud_create_app_stack' );
	$app_stack_mode			= get_option( 'wpcs_runcloud_create_app_stack_mode' );
	$app_default_app		= get_option( 'wpcs_runcloud_create_app_default_app' );
	
	$app_application_explode	= explode( '|', $app_application );
	$app_application_name		= $app_application_explode[0];
	$install_method				= isset( $app_application_explode[1] ) ? $app_application_explode[1] : '';
	
	if ( !empty( $app_new_sys_user ) && !empty( $app_new_sys_user_pwd ) ) {
		
		$user_data = array(
						"username"	=> $app_new_sys_user,
  						"password"	=> $app_new_sys_user_pwd,
			);
		
		$confirm_user_id	= $api->call_api( "servers/{$app_server_id}/users", $user_data, false, 0, 'POST', false, 'runcloud_add_sys_user' );
		$app_user			= $confirm_user_id['id'];
		
		$debug['sys_user']	= $app_user;
	}
	
	$app_data = array(
		"name"							=> $app_name,
  		"domainName"					=> $app_domain,
 	 	"user"							=> $app_user,
  		"publicPath"					=> $app_web_directory,
  		"phpVersion"					=> $app_php_version,
  		"stack"							=> $app_stack,
  		"stackMode"						=> $app_stack_mode,
  		"clickjackingProtection"		=> true,
  		"xssProtection"					=> true,
  		"mimeSniffingProtection"		=> true,
 		"processManager"				=> "ondemand",
  		"processManagerMaxChildren"		=> 50,
  		"processManagerMaxRequests"		=> 500,
  		"openBasedir"					=> "/home/myuser/webapps/testing:/var/lib/php/session:/tmp",
  		"timezone"						=> "UTC",
  		"disableFunctions"				=> "getmyuid,passthru,leak,listen,diskfreespace,tmpfile,link,ignore_user_abort,shell_exec,dl,set_time_limit,exec,system,highlight_file,source,show_source,fpassthru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix,_getppid,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_open,proc_close,proc_nice,proc_terminate,escapeshellcmd,ini_alter,popen,pcntl_exec,socket_accept,socket_bind,socket_clear_error,socket_close,socket_connect,symlink,posix_geteuid,ini_alter,socket_listen,socket_create_listen,socket_read,socket_create_pair,stream_socket_server",
  		"maxExecutionTime"				=> 30,
  		"maxInputTime"					=> 60,
  		"maxInputVars"					=> 1000,
  		"memoryLimit"					=> 256,
  		"postMaxSize"					=> 256,
  		"uploadMaxFilesize"				=> 256,
  		"sessionGcMaxlifetime"			=> 1440,
  		"allowUrlFopen"					=> true,
	);
	
	update_option( 'wpcs_website_install_data', $app_data );
		
	// Send the API POST request to create the new 'app'
	$response = $api->call_api( "servers/{$app_server_id}/webapps/custom", $app_data, false, 0, 'POST', false, 'runcloud_new_site' );
	
	$debug['webapp'] = $response;
	
	$webapp_name	= isset( $response['name'] ) ? $response['name'] : '';
	$webapp_id		= isset( $response['id'] ) ? $response['id'] : '';
	$pull_key_1		= isset( $response['pullKey1'] ) ? $response['pullKey1'] : '';
	$pull_key_2		= isset( $response['pullKey2'] ) ? $response['pullKey2'] : '';
	
	$application_data = array(
		"name"	=> $app_application,
	);
	
	// Install Software Installer
	if ( 'php' == $install_method ) {
	
		$response = $api->call_api( "servers/{$app_server_id}/webapps/{$webapp_id}/installer", $application_data, false, 0, 'POST', false, 'runcloud_application' );
	
	$debug['wordpress'] = $response;
		
	}
	
	// Install via Git
	if ( 'git' == $install_method ) {
		
		// Obtain deployment key
		$key = $api->call_api( "servers/{$app_server_id}/users/{$app_user}/deploymentkey", null, false, 0, 'PATCH', false, 'runcloud_deploy_key' );
		
		$debug['deploy_key']	= $key['deploymentKey'];
		$debug['app']			= $app_application_name;
		
		// Send Deploy Key to GitHub
		$confirm_key = wpcs_runcloud_github_add_deploy_key( $key['deploymentKey'], $app_application_name );
		
		$debug['add_key'] = $confirm_key;
		
		$git_data = array(
			"provider"		=> "github",
  			"repository"	=> "Designed4Pixels/git-deploy-test-site ",
  			"branch"		=> "main"
		);
		
		// Clone the Git Repository
		$response = $api->call_api( "servers/{$app_server_id}/webapps/{$webapp_id}/git", $git_data, false, 0, 'POST', false, 'runcloud_clone_rep' );
		
		$url = "https://manage.runcloud.io/webhooks/git/{$pull_key_1}/{$pull_key_2}";
		
		// Webhook
		$webhook = wpcs_runcloud_github_add_webhook( $url, $app_application_name );
	
		$debug['clone']		= $response;
		$debug['webhook']	= $webhook;
		
		update_option( 'wpcs_deploy_runcloud_git_site', $debug );
		
	}
	
	
	
	
	
	// Set as default web application on this server
	if ( '1' == $app_default_app ) {
		$response = $api->call_api( "servers/{$app_server_id}/webapps/{$webapp_id}/default", null, false, 0, 'POST', false, 'runcloud_application' );
		$debug['default'] = $response;
	}
	
	update_option( 'wpcs_install_wordpress', $debug );
			
	// Executes after the create service functionality
	//do_action( 'wpcs_after_serverpilot_site_completion', $data );	
	//wpcs_serverpilot_log_event( 'RunCloud', $status, $message );

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_runcloud_create_app_name' );
	delete_option( 'wpcs_runcloud_create_app_application' );
	delete_option( 'wpcs_runcloud_create_app_server_id' );
	delete_option( 'wpcs_runcloud_create_app_domain' );
	delete_option( 'wpcs_runcloud_create_app_user' );
	delete_option( 'wpcs_runcloud_create_app_web_directory' );
	delete_option( 'wpcs_runcloud_create_app_php_version' );
	delete_option( 'wpcs_runcloud_create_app_stack' );
	delete_option( 'wpcs_runcloud_create_app_stack_mode' );
	delete_option( 'wpcs_runcloud_create_app_default_app' );
}

}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_install_managed_website_template', 10, 3 );