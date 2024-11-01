<?php

/**
 * WP Cloud Server - Ploi Module Admin Settings Page
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_Ploi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Ploi_Settings {
		
	/**
	 *  Instance of WPCloudServer Ploi Server Template Settings Class
	 *
	 *  @var resource
	 */
	private static $server_template;

		/**
	 *  Status of this class
	 *
	 *  @var resource
	 */
	public static $status;

	/**
	 *  Module Name
	 *
	 *  @var string
	 */
	private static $module_name = 'Ploi';
	
	/**
	 *  Module Type
	 *
	 *  @var string
	 */
	private static $module_type = 'cloud_portal';
		
	/**
	 *  Module Description
	 *
	 *  @var string
	 */
	private static $module_desc = 'Use Ploi to create and manage new cloud servers.';

	/**
	 *  Instance of WPCloudServer API Class
	 *
	 *  @var resource
	 */
	private static $api;

	/**
	 *  API Status
	 *
	 *  @var resource
	 */
	public static $api_connected;

	/**
	 *  Set variables and place few hooks
	 *
	 *  @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_ploi_add_module' ) );
		
		add_action( 'wpcs_update_module_status', array( $this, 'wpcs_ploi_update_module_status' ), 10, 2 );
		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_ploi_update_servers' ) );
		add_action( 'wpcs_reset_logged_data', array( $this, 'wpcs_reset_ploi_logged_data' ) );			
		
		// Handle Scheduled Events
		add_action( 'wpcs_ploi_module_activate', array( $this, 'wpcs_ploi_module_activate_server_completed_queue' ) );
		add_action( 'wpcs_ploi_run_server_completed_queue', array( $this, 'wpcs_ploi_module_run_server_completed_queue' ) );
		add_filter( 'cron_schedules', array( $this, 'wpcs_ploi_custom_cron_schedule' ) );

		self::$api = new WP_Cloud_Server_Ploi_API();

	}
		
	/**
	 *  Add Ploi Module to Module List
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_add_module() {

		$module_data = get_option( 'wpcs_module_list' );

		self::$api_connected = self::$api->wpcs_ploi_check_api_health();
			
		if ( ! array_key_exists( self::$module_name, $module_data) ) {

			if ( ! isset( self::$status )) {
					self::$status = 'active';
			}
		
			$module_data[self::$module_name]['module_name']	= self::$module_name;
			$module_data[self::$module_name]['module_desc']=self::$module_desc;
			$module_data[self::$module_name]['status']=self::$status;
			$module_data[self::$module_name]['module_type']=self::$module_type;

			$module_data[ self::$module_name ]['servers']	= array();
			
			$templates		= get_option( 'wpcs_template_data_backup' );
			$template_data	= ( !empty( $templates[ self::$module_name ]['templates'] ) ) ? $templates[ self::$module_name ]['templates'] : array();
			$module_data[ self::$module_name ]['templates']	= $template_data;
			
			wpcs_ploi_log_event( 'Ploi', 'Success', 'The Ploi Module was Successfully Activated!' );
		}

		$module_data[self::$module_name]['api_connected'] = self::$api_connected;

		if ( ! array_key_exists( self::$module_name, $module_data) ) {
			$module_data[ self::$module_name ]['servers']	= array();
			$module_data[ self::$module_name ]['templates']	= array();
		}

		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Update Ploi Module Status
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_update_module_status( $module_name, $new_status ) {

		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'Ploi' === $module_name ) {

			self::$status = $new_status;
			$module_data[$module_name]['status'] = $new_status;
			update_option( 'wpcs_module_list', $module_data );

			if ( 'active' == $new_status ) {
				update_option( 'wpcs_dismissed_ploi_module_setup_notice', FALSE );
			}

			$message = ( 'active' == $new_status) ? 'Activated' : 'Deactivated';
			wpcs_ploi_log_event( 'Ploi', 'Success', 'Ploi Module ' . $message . ' Successfully' );
		}

	}
		
	/**
	 *  Update Ploi Server Status
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_ploi_update_servers() {

		$module_data = get_option( 'wpcs_module_list', array() );
			
		// Functionality to be added in future update.
			
		update_option( 'wpcs_module_list', $module_data );

	}
		
	/**
	 *  Ploi Module Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_module_tab( $active_tab, $status, $module_name ) {
			
		$module_data = get_option( 'wpcs_module_list' );
			
		$state1 = (( 'active' == $status ) && (( 'Ploi' == $module_name ) || ( 'active' == $module_data['Ploi']['status'] )));
		$state2 = (( 'active' == $status ) && (( 'Ploi' !== $module_name ) && ( 'active' == $module_data['Ploi']['status'] )));
		$state3 = (( 'inactive' == $status ) && (( 'Ploi' !== $module_name ) && ( 'active' == $module_data['Ploi']['status'] )));			
		$state4 = (( '' == $status) && ( 'active' == $module_data['Ploi']['status'] ));
		
		if ( $state1 || $state2 || $state3 || $state4 ) {
		?>
			<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=servers' ), 'ploi_servers_nonce', '_wpnonce') );?>" class="nav-tab <?php echo ( 'ploi' === $active_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Ploi', 'wp-cloud-server-ploi' ) ?></a>
		<?php
		}
	}
				
	/**
	 *  Ploi Tab Content with Submenu.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_module_tab_content_with_submenu( $active_tab, $submenu, $modules ) {
			
		$debug_enabled = get_option( 'wpcs_enable_debug_mode' );
			
		if ( 'ploi' === $active_tab ) { ?>
			
				<div> <?php do_action( 'wpcs_ploi_module_notices' ); ?> </div>
			
				<div class="submenu-wrapper" style="width: 100%; float: left; margin: 10px 0 30px;">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=servers'), 'ploi_servers_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'servers' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Servers', 'wp-cloud-server-ploi' ) ?></a> | </li>
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=templates'), 'ploi_server_templates_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'templates' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Templates', 'wp-cloud-server-ploi' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=addserver'), 'ploi_add_server_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addserver' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Create Server', 'wp-cloud-server-ploi' ) ?></a> | </li>			
						<?php if ( WP_Cloud_Server_Cart_EDD::wpcs_is_edd_active() ) { ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=addtemplate'), 'ploi_add_template_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'addtemplate' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Add Template', 'wp-cloud-server-ploi' ) ?></a> | </li>
						<?php } ?>
						<li><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=settings'), 'ploi_settings_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'settings' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Settings', 'wp-cloud-server-ploi' ) ?></a> </li>
						<?php if ( '1' == $debug_enabled ) { ?>
						<li> | <a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=debug'), 'ploi_debug_nonce', '_wpnonce') );?>" class="sub-menu <?php echo ( 'debug' === $submenu ) ? 'current' : ''; ?>"><?php esc_attr_e( 'Debug', 'wp-cloud-server-ploi' ) ?></a></li>
						<?php } ?>
				 	</ul>
				</div>

				<?php 
				if ( 'settings' === $submenu ) {
					$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : ''; 
					$reset_api = isset( $_GET['resetapi'] ) ? WP_Cloud_Server_Tools::wpcs_sanitize_true_setting( $_GET['resetapi'] ) : '';
					if (( 'true' == $reset_api ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'ploi_settings_nonce' ) ) ) {
						delete_option( 'wpcs_ploi_api_token' );
						delete_option( 'wpcs_dismissed_ploi_api_notice' );
					}
				?>

				<div class="content">
					<form method="post" action="options.php">
						<?php 
						settings_fields( 'wpcs_ploi_admin_menu' );
						do_settings_sections( 'wpcs_ploi_admin_menu' );
						submit_button();
						?>
					</form>
				</div>
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&tab=ploi&submenu=settings&resetapi=true' ), 'ploi_settings_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Reset Ploi API Credentials', 'wp-cloud-server-ploi' ) ?></a>
				</p> <?php

				} elseif ( 'servers' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-servers-page.php';
				} elseif ( 'templates' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-templates-page.php';
				} elseif ( 'addserver' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-add-server-page.php';
				} elseif ( 'addtemplate' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-add-template-page.php';
				} elseif ( 'debug' === $submenu ) {
					require plugin_dir_path( __DIR__ ) . '/admin/partials/display-admin-debug-page.php';
				}
		}
	}
	
	/**
	 *  Ploi Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_log_page_tabs( $active_tab ) {
		
		$module_data = get_option( 'wpcs_module_list' );
			
		if ( 'active' == $module_data['Ploi']['status'] ) {
		?>
			
			<a href="<?php echo esc_url( self_admin_url( 'admin.php?page=wp-cloud-server-logs-menu&tab=ploi_logs') );?>" class="nav-tab<?php echo ( 'ploi_logs' === $active_tab ) ? ' nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Ploi', 'wp-cloud-server-ploi' ); ?></a>

		<?php
		}
		
	}
	
	/**
	 *  Ploi Log Page Tab.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_ploi_log_page_tabs_content( $active_tab ) {
		
			if ( 'ploi_logs' === $active_tab ) {

					$logged_data = get_option( 'wpcs_ploi_logged_data' );
					?>
			
					<div class="content">
					
						<h3 class="title"><?php esc_html_e( 'Logged Event Data', 'wp-cloud-server-ploi' ); ?></h3>
					
						<p><?php esc_html_e( 'Every time an event occurs, such as a new site being created, connection to add API, or even an error, then a summary will be
						captured here in the logged event data.', 'wp-cloud-server-ploi' ); ?>
						</p>

						<table class="wp-list-table widefat fixed striped">
    						<thead>
    							<tr>
        							<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server-ploi' ); ?></th>
        							<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server-ploi' ); ?></th>
       			 					<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server-ploi' ); ?></th>
									<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server-ploi' ); ?></th>
    							</tr>
    						</thead>
    						<tbody>
							<?php
							if ( !empty( $logged_data ) ) {
								$formatted_data = array_reverse( $logged_data, true );
								foreach ( $formatted_data as $logged_event ) {	
								?>
    								<tr>
        								<td><?php echo $logged_event['date']; ?></td>
        								<td><?php echo $logged_event['event']; ?></td>
										<td><?php echo $logged_event['status']; ?></td>
										<td><?php echo $logged_event['description']; ?></td>
    								</tr>
								<?php
								}
							} else {
							?>
    								<tr>
        								<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server-ploi' ); ?></td>
    								</tr>								
							<?php
							}
							?>								
    						</tbody>
						</table>
					</div>

	<?php
			}
	}
		
	/**
	 *  Return Ploi Module is Active Status.
	 *
	 *  @since 1.0.0
	 */
	public static function wpcs_ploi_module_is_active() {

		if( 'active' == self::$status ) {
			return true;
		}
		return false;

	}
	
	/**
	 *  Sanitize Template Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original template name
	 *  @return string  checked template name
	 */
	public function sanitize_ploi_server_template_name( $name ) {
		
		$name = sanitize_text_field( $name );
		
		$output = get_option( 'wpcs_ploi_server_template_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
			
				$output = $name;
				$type = 'updated';
				$message = __( 'The New Ploi Template was Created.', 'wp-cloud-server-ploi' );

			} else {
				
				$type = 'error';
				$message = __( 'Please enter a Valid Template Name!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_server_template_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);
			
			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Sanitize Server Name
	 *
	 *  @since  1.0.0
	 *  @param  string  $name original server name
	 *  @return string  checked server name
	 */
	public function sanitize_ploi_server_name( $name ) {

		$output = get_option( 'wpcs_ploi_server_name', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $name ) {
				$lc_name  = strtolower( $name );
				$invalid  = preg_match('/[^a-z0-9.\-]/u', $lc_name);
				if ( $invalid ) {

					$type = 'error';
					$message = __( 'The Server Name entered is not Valid. Please try again using characters a-z, A-Z, 0-9, -, and a period (.)', 'wp-cloud-server-ploi' );
	
				} else {
					$output = $name;
					$type = 'updated';
					$message = __( 'The New Ploi Server is being Created.', 'wp-cloud-server-ploi' );
	
				}
			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Server Name!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_server_name',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Sanitize API Token
	 *
	 *  @since  1.0.0
	 *  @param  string  $token original API token
	 *  @return string  checked API token
	 */
	public function sanitize_ploi_api_token( $token ) {

		$new_token = sanitize_text_field( $token );

		$output = get_option( 'wpcs_ploi_api_token', '' );
		
		// Detect multiple sanitizing passes.
    	// Accomodates bug: https://core.trac.wordpress.org/ticket/21989
    	static $pass_count = 0; static $output = ''; $pass_count++;
 
    	if ( $pass_count <= 1 ) {

			if ( '' !== $new_token ) {
			
				$output = $new_token;
				$type = 'updated';
				$message = __( 'The Ploi API Token was updated.', 'wp-cloud-server-ploi' );

			} else {
				$type = 'error';
				$message = __( 'Please enter a Valid Ploi API Token!', 'wp-cloud-server-ploi' );
			}

			add_settings_error(
				'wpcs_ploi_api_token',
				esc_attr( 'settings_error' ),
				$message,
				$type
			);

			return $output;
			
		} 

			return $output;

	}

	/**
	 *  Return Ploi Module Name.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_ploi_module_name() {

		return self::$module_name;

	}
	
	/**
	 *  Clear Logged Data if user requested.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_reset_ploi_logged_data( $request_delete ) {
		
		$data = array();
		
		if ( $request_delete == '1' ) {
			
			// Reset the Logged Data Array
			update_option( 'wpcs_ploi_logged_data', $data );
		}

	}
	
	/**
	 *  Set-up Ploi Cron Job.
	 *
	 *  @since 1.0.1
	 */
	public function  wpcs_ploi_custom_cron_schedule( $schedules ) {
    	$schedules[ 'one_minute' ] = array( 'interval' => 1 * MINUTE_IN_SECONDS, 'display' => __( 'One Minute', 'wp-cloud-server' ) );
    return $schedules;
	}
	
	/**
	 *  Activates the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_ploi_module_activate_server_completed_queue() {

		// Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'wpcs_ploi_run_server_completed_queue' ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'one_minute', 'wpcs_ploi_run_server_completed_queue' );
			wpcs_ploi_log_event( 'Ploi', 'Success', 'Ploi Server Queue Started' );
		}

	}
	
	/**
	 *  Run the SSL Queue.
	 *
	 *  @since 1.1.0
	 */
	public static function wpcs_ploi_module_run_server_completed_queue() {
		
		$api			= new WP_Cloud_Server_Ploi_API();
		$server_queue	= get_option( 'wpcs_ploi_server_complete_queue', array() );
		
		if ( ! empty( $server_queue ) ) {
			
			foreach ( $server_queue as $key => $queued_server ) {
			
				$server_sub_id		= $queued_server['SUBID'];
				$server_name		= $queued_server['server_name'];
				$response			= $queued_server['response'];
				$user_id			= $queued_server['user_id'];
				$domain_name		= $queued_server['domain_name'];
				$host_name			= $queued_server['host_name'];
				$host_name_domain	= $queued_server['host_name_domain'];
				$host_name_fqdn		= $queued_server['fqdn'];
				$host_name_protocol	= $queued_server['protocol'];
				$host_name_port		= $queued_server['port'];
				$site_label			= $queued_server['site_label'];
				$user_meta			= $queued_server['user_meta'];
				$plan_name			= $queued_server['plan_name'];
				$module_name		= $queued_server['module'];
				$ssh_key_name		= $queued_server['ssh_key'];
				$server_location	= $queued_server['location'];
				$enable_ssl			= $queued_server['enable_ssl'];
				
				$server_module		= strtolower( str_replace( " ", "_", $module_name ) );

				// Run Cloud Provider completion function
				$server	= call_user_func("wpcs_ploi_server_complete", $queued_server, $response, $host_name, $server_location );
				
				update_option( 'wpcs_ploi_server_queue_response', $server );
								
				if ( is_array($server) && ( $server['completed'] ) ) {

					$data = array(
						"plan_name"			=>	$plan_name,
						"module"			=>	$module_name,
						"host_name"			=>	$host_name,
						"host_name_domain"	=>	$host_name_domain,
						"domain_name"		=>  $domain_name,
						"fqdn"				=>	$host_name_fqdn,
						"protocol"			=>	$host_name_protocol,
						"port"				=>	$host_name_port,
						"server_name"		=>	$server_name,
						"site_label"		=>	$site_label,
    					"region_name"		=>	$server_location,
						"size_name"			=>	'',
						"image_name"		=> 	'',
						"ssh_key_name"		=> 	$ssh_key_name,
						"user_data"			=>	$user_meta,
						"enable_ssl"		=>  $enable_ssl,
					);
					
					$get_user_meta		= get_user_meta( $user_id );
					
					$data['user_id']		= $user_id;
					$data['nickname']		= $get_user_meta['nickname'][0];
					$data['first_name']		= $get_user_meta['first_name'][0];
					$data['last_name']		= $get_user_meta['last_name'][0];
					$data['full_name']		= "{$get_user_meta['first_name'][0]} {$get_user_meta['last_name'][0]}";
					
					// Save Server Data for display in control panel
					$client_data			= get_option( 'wpcs_cloud_server_client_info' );
					$client_data			= ( is_array( $client_data ) ) ? $client_data : array();
					$client_data['Ploi'][]	= $data;

					update_option( 'wpcs_cloud_server_client_info', $client_data );
				
					// Reset the dismissed site creation option and set new site created option
					update_option( 'wpcs_dismissed_ploi_site_creation_notice', FALSE );
					update_option( 'wpcs_ploi_new_site_created', TRUE );
					
					// Remove the server from the completion queue
					unset( $server_queue[ $key ] );
					update_option( 'wpcs_ploi_server_complete_queue', $server_queue );
					
					$debug['app_data'] = $data;
			
					update_option( 'wpcs_ploi_new_site_data', $debug );
				}
			}
		}
	}
	
	/**
	 *  Create Ploi License Page Settings.
	 *
	 *  @since 1.0.1
	 */
	function wpcs_ploi_create_license_setting_sections_and_fields() {
		// creates our settings in the options table
		register_setting('wpcs_ploi_license_settings', 'wpcs_ploi_module_license_key', 'wpcs_sanitize_license' );
		register_setting('wpcs_ploi_license_settings', 'wpcs_ploi_module_license_activate' );
	}

	function wpcs_sanitize_license( $new ) {
		$old = get_option( 'wpcs_ploi_module_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'wpcs_ploi_module_license_active' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
	
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_ploi_create_app_setting_sections_and_fields() {
		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_server_id' );		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_root_domain' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_project_directory' );		
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_web_directory' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_system_user' );
		register_setting( 'wpcs_ploi_create_app', 'wpcs_ploi_create_app_web_template' );

		add_settings_section(
			'wpcs_ploi_create_app',
			esc_attr__( 'Install a New Website', 'wp-cloud-server' ),
			'',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_server_id',
			esc_attr__( 'Server Id:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_server_id' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);

		add_settings_field(
			'wpcs_ploi_create_app_root_domain',
			esc_attr__( 'Root Domain:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_root_domain' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_project_directory',
			esc_attr__( 'Project Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_project_directory' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_web_directory',
			esc_attr__( 'Web Directory:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_web_directory' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_system_user',
			esc_attr__( 'System User:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_system_user' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
		
		add_settings_field(
			'wpcs_ploi_create_app_web_template',
			esc_attr__( 'Web Template:', 'wp-cloud-server' ),
			array( $this, 'wpcs_field_callback_ploi_create_app_web_template' ),
			'wpcs_ploi_create_app',
			'wpcs_ploi_create_app'
		);
				
		// Action Hook to allow add additional fields in add-on modules
		do_action( 'wpcs_ploi_create_app_field_setting' );

	}
		
	/**
	 *  ServerPilot Create App Settings Section Callback.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_section_callback_ploi_create_app() {

		echo '<p>';
		echo wp_kses( 'This page allows you to add a new WordPress Website to any connected Server. Enter the details below and then click the \'Create New Website\' button to have the new website built and online in a few minutes!', 'wp-cloud-server' );
		echo '</p>';

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Server.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_server_id() {
		
		$servers = wpcs_ploi_call_api_list_servers();

		$api_status		= wpcs_check_cloud_provider_api('ServerPilot', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = get_option( 'wpcs_ploi_create_app_server_id' );
		?>
		<select style='width: 400px' name="wpcs_ploi_create_app_server_id" id="wpcs_ploi_create_app_server_id">
			<optgroup label="Servers">
			<?php
			if ( ( ! empty( $servers ) ) && is_array( $servers ) ) {
				foreach ( $servers as $server ) {
				?>
            		<option value="<?php echo $server['id']; ?>"><?php echo $server['name']; ?></option>
				<?php
				}
			} else {
				?>
				<option value="not_available">No Servers Available</option>
			<?php
			}
			?>
			</optgroup>
		</select>
		<?php

	}

	/**
	 *  ServerPilot Create App Field Callback for App Name.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_root_domain() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='domain.com' id='wpcs_ploi_create_app_root_domain' name='wpcs_ploi_create_app_root_domain' value='{$value}'/>";

	}

	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_project_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/' id='wpcs_ploi_create_app_project_directory' name='wpcs_ploi_create_app_project_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_web_directory() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='/public' id='wpcs_ploi_create_app_web_directory' name='wpcs_ploi_create_app_web_directory' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_system_user() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='System User' id='wpcs_ploi_create_app_system_user' name='wpcs_ploi_create_app_system_user' value='{$value}'/>";

	}
	
	/**
	 *  ServerPilot Create App Field Callback for App Domain.
	 *
	 *  @since 1.1.0
	 */
	function wpcs_field_callback_ploi_create_app_web_template() {

		$api_status		= wpcs_check_cloud_provider_api('Ploi', null, false);
		$attributes		= ( $api_status ) ? '' : 'disabled';
		$value = null;
		$value = ( ! empty( $value ) ) ? $value : '';

		echo "<input style='width: 400px' type='text' placeholder='Template Name' id='wpcs_ploi_create_app_web_template' name='wpcs_ploi_create_app_web_template' value='{$value}'/>";

	}

	/**
	 *  Return Ploi Module API is Active Status.
	 *
	 *  @since 1.3.0
	 */
	public static function wpcs_ploi_module_api_connected() {

		return self::$api_connected;

	}
}