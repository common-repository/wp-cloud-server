<?php

/**
 * Provide a admin area servers view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_runcloud_create_web_application_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'runcloud-create-web-application-template' !== $tabs_content ) {
		return;
	}

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

$module_name	=  'RunCloud';
$api_status		= wpcs_check_cloud_provider_api('RunCloud', null, false);
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
		wpcs_submit_button( 'Create Template', 'secondary', 'create_app', null, $attributes );
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
	"app_web_directory"		=>	get_option( 'wpcs_runcloud_create_app_web_directory' ),
	"app_php_version"		=>	get_option( 'wpcs_runcloud_create_app_php_version' ),
	"app_stack"				=>	get_option( 'wpcs_runcloud_create_app_stack' ),
	"app_stack_mode"		=>	get_option( 'wpcs_runcloud_create_app_stack_mode' ),
);

$app_name = get_option( 'wpcs_runcloud_create_app_name', '' );

if ( '' !== $app_name ) {

	$app_server_id		= get_option( 'wpcs_runcloud_create_app_server_id' );
	$app_application	= get_option( 'wpcs_runcloud_create_app_application' );
	$app_domain			= get_option( 'wpcs_runcloud_create_app_domain' );
	$app_user			= get_option( 'wpcs_runcloud_create_app_user' );
	$app_web_directory	= get_option( 'wpcs_runcloud_create_app_web_directory' );
	$app_php_version	= get_option( 'wpcs_runcloud_create_app_php_version' );
	$app_stack			= get_option( 'wpcs_runcloud_create_app_stack' );
	$app_stack_mode		= get_option( 'wpcs_runcloud_create_app_stack_mode' );
	

	
			// Set-up the data for the new Droplet
		$droplet_data = array(
			"name"				=>  $app_name,
			"slug"				=>  sanitize_title( $server_name ),
			"region"			=>	'',
			"region_name"		=>	'',
			"size"				=>	'',
			"size_name"			=>	'',
			"image"				=>	'',
			"image_name"		=>	'',
			"backups"			=>	'',
			"template_name"		=>  'runcloud_template',
			"hosting_type"		=>	'Shared',
			"module"			=>  $module_name,
			"plan"				=>	'',
			"autossl"			=>	'',
			"monitor_enabled"	=>	'',
			"ssh_key"			=>	'',
			"web_application_data"	=>	array(
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
  											"disableFunctions"				=> 												"getmyuid,passthru,leak,listen,diskfreespace,tmpfile,link,ignore_user_abort,shell_exec,dl,set_time_limit,exec,system,highlight_file,source,show_source,fpassthru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix,_getppid,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_open,proc_close,proc_nice,proc_terminate,escapeshellcmd,ini_alter,popen,pcntl_exec,socket_accept,socket_bind,socket_clear_error,socket_close,socket_connect,symlink,posix_geteuid,ini_alter,socket_listen,socket_create_listen,socket_read,socket_create_pair,stream_socket_server",
  											"maxExecutionTime"				=> 30,
  											"maxInputTime"					=> 60,
  											"maxInputVars"					=> 1000,
  											"memoryLimit"					=> 256,
  											"postMaxSize"					=> 256,
  											"uploadMaxFilesize"				=> 256,
  											"sessionGcMaxlifetime"			=> 1440,
  											"allowUrlFopen"					=> true,
										), 
		);

		// Retrieve the Active Module List
		$module_data	= get_option( 'wpcs_module_list' );
		$template_data	= get_option( 'wpcs_template_data_backup' );
			
		// Save the VPS Template for use with a Plan
		$module_data[ 'RunCloud' ][ 'templates' ][] = $droplet_data;
		
		// Save backup copy of templates
		$template_data[ 'RunCloud' ][ 'templates' ][] = $droplet_data;

		// Update the Module List
		update_option( 'wpcs_module_list', $module_data );
		
		// Update the Template Backup
		update_option( 'wpcs_template_data_backup', $template_data );

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
	}
}
add_action( 'wpcs_control_panel_tab_content', 'wpcs_runcloud_create_web_application_template', 10, 3 );