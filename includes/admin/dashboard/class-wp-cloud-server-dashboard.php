<?php
/**
 * The Main Control Panel.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_Dashboard {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		add_action( 'wpcs_system_notices', array( $this, 'wpcs_output_uikit_notice' ), 10, 3 );
		add_action( 'wpcs_system_notices', array( $this, 'wpcs_output_api_disabled_notice' ), 10, 3 );
		add_action( 'wpcs_system_notices', array( $this, 'wpcs_output_call_to_action_notice' ), 10, 3 );

		add_action( 'admin_menu', array( $this, 'wpcs_add_menu_page' ) );
		add_action( 'wpcs_user_dashboard_header', array( $this, 'wpcs_setup_dashboard_header' ), 10, 2 );
		add_action( 'wpcs_user_dashboard_left_sidebar', array( $this, 'wpcs_setup_dashboard_left_sidebar' ), 10, 5 );
		add_action( 'wpcs_user_dashboard_main_content', array( $this, 'wpcs_setup_dashboard_main_content' ), 10, 6 );
		add_action( 'wpcs_user_dashboard_footer', array( $this, 'wpcs_setup_dashboard_footer' ) );

		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', [ $this, 'menu_order' ] );

	}
	
	public function menu_order( $menu_order ) {
		// Initialize our custom order array.
		$wpcs_menu_order = [];
		
		update_option( 'wpcs_menu_order', $menu_order );

		// Get the index of our custom separator.
		$wpcs_separator = array_search( 'separator-wp-cloud-server', $menu_order, true );

		// Get index of library menu.
		//$elementor_library = array_search( Source_Local::ADMIN_MENU_SLUG, $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {
			if ( 'wp-cloud-server-admin-menu' === $item ) {
				$wpcs_menu_order[] = 'separator-wp-cloud-server';
				$wpcs_menu_order[] = $item;

				unset( $menu_order[ $wpcs_separator ] );
			} elseif ( ! in_array( $item, [ 'separator-wp-cloud-server' ], true ) ) {
				$wpcs_menu_order[] = $item;
			}
		}
		
		// Return order.
		return $wpcs_menu_order;
	}

	/**
	 *  Add various pages to admin menu
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_add_menu_page() {
		global $menu;
		
		$menu[] = [ '', 'read', 'separator-wp-cloud-server', '', 'wp-menu-separator wp-cloud-server' ];
		
		add_menu_page(
			esc_attr__( 'WP Cloud Server', 'wp-cloud-server' ),
			'WP Cloud Server',
			'manage_options',
			'wp-cloud-server-admin-menu',
			array( $this, 'wpcs_control_panel_output' ),
			'dashicons-cloud',
			'58.1'
		);

		$modules = get_option( 'wpcs_module_list' );

		// Update the Module Config Settings
		do_action( 'wpcs_update_module_config', $modules, '', '' );
		
		// Update the Main Config Settings
		do_action( 'wpcs_update_config', $modules, '', '' );
		
		$submenus							= get_option( 'wpcs_config' );
		$skip_wizard_confirmed				= get_option( 'wpcs_skip_setup_wizard', 'false' );
		$setup_wizard_complete_confirmed	= get_option( 'wpcs_setup_wizard_complete_confirmed', 'false' );
		
		if ( ( $submenus ) && ( ( 'true' == $skip_wizard_confirmed ) || ( 'true' == $setup_wizard_complete_confirmed ) ) ) {
		
			foreach ( $submenus as $menus => $menu_item ) {
				
				if ( isset( $menu_item['menu_type'] ) && ( 'menu' == $menu_item['menu_type'] ) && ( 'false' !== $menu_item['active'] ) ) {
				
					add_menu_page(
						esc_attr__( $menu_item['page_title'], 'wp-cloud-server' ),
						$menu_item['menu_title'],
						$menu_item['capability'],
						$menu_item['menu_slug'],
						array( $this, $menu_item['function'] ),
						isset( $menu_item['icon_url'] ) ? $menu_item['icon_url'] : 'dashicons-controls-volumeon',
						$menu_item['position']
					);
					
				}
				
				if (isset($menu_item['submenus']) ) {
					
					foreach ( $menu_item['submenus'] as $item ) {
					
						if ( isset( $item['menu_type'] ) && ( 'submenu' == $item['menu_type'] ) && ( 'false' !== $item['active'] )) {
				
							add_submenu_page(
								$item['parent_slug'],
								esc_attr__( $item['page_title'], 'wp-cloud-server' ),
								$item['menu_title'],
								$item['capability'],
								$item['menu_slug'],
								array( $this, $item['function'] )
							);
					
						}
					}
				}
			}
		}
		
		// Action Hook for Developers to add in module menus
		do_action( 'wpcs_admin_menu_for_modules' );

	}
	
	/**
	 *  Output the Main Control Panel
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_control_panel_output() {
		
		$debug_enabled		= get_option( 'wpcs_enable_debug_mode' );
		$modules			= get_option( 'wpcs_module_list' );
		$page_previous_id	= get_option( 'wpcs_previous_page_id' );

		$active_tab 		= isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'allmodules';
		$status				= isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		$submenu			= isset( $_GET['submenu'] ) ? sanitize_text_field( $_GET['submenu'] ) : '';
		$module_name		= isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';
		$reset_api			= isset( $_GET['resetapi'] ) ? sanitize_text_field( $_GET['resetapi'] ) : '';
		$page_id			= isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		
		// Update the Module Config Settings
		do_action( 'wpcs_update_module_config', $modules, $module_name, $status );
		
		// Update the Main Config Settings
		do_action( 'wpcs_update_config', $modules, $module_name, $status );

		$page_content		= get_option( 'wpcs_config' );
		$default_page		= $page_content[ $page_id ]['default_page'];
		$current_page		= get_option( 'wpcs_current_page', $default_page );
		$redirect_id		= ( ( $page_id == $page_previous_id ) && ( 'No Data' !== $current_page ) ) ? $current_page : $default_page;

		update_option( 'wpcs_previous_page_id', $page_id );
		update_option( 'wpcs_current_page', $redirect_id );
		
		// Action Hook to allow extension modules to hook into All Modules Page
		do_action( 'wpcs_enter_all_modules_page_before_content', $modules, $debug_enabled );
		
		$skip_wizard_confirmed				= get_option( 'wpcs_skip_setup_wizard', 'false' );
		$setup_wizard_complete_confirmed	= get_option( 'wpcs_setup_wizard_complete_confirmed', 'false' );
		
		if ( ( 'true' == $skip_wizard_confirmed ) || ( 'true' == $setup_wizard_complete_confirmed ) ) {
		
			// Include the Dashboard Page Header
			do_action( 'wpcs_user_dashboard_header', $page_content, $redirect_id );
		
			// Include the Dashboard Left Sidebar
			do_action( 'wpcs_user_dashboard_left_sidebar', $active_tab, $status, $module_name, $page_content, $redirect_id );
	
			// Include the Dashboard Main Content
			do_action( 'wpcs_user_dashboard_main_content', $active_tab, $status, $module_name, $page_content, $redirect_id, $page_previous_id );

      		// Include the Dashboard Footer
			do_action( 'wpcs_user_dashboard_footer' );
			
		}	
	}
		
	/**
	 *  Output the WP Cloud Server Dashboard Header
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_setup_dashboard_header( $main_title = '', $redirect_id ) {

		$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'nopage';
		?>
		
		<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>

		<div id="wpcs-wrapper" style="margin: 10px 20px 0 0;" class="wrap-custom">
			<div class="uk-section uk-section-xsmall uk-padding-remove-top uk-padding-remove-bottom uk-border-rounded-top uk-background-secondary">
    			<div>
					<nav style="border: 1px solid #000; background-color: #000; padding-left: 25px;" class="uk-navbar-container uk-light uk-background-secondary uk-border-rounded-top " uk-navbar>
						<a class="uk-navbar-item uk-logo uk-padding-remove-left" href="#">
							<?php echo esc_attr( "WP Cloud Server -", 'wp-cloud-server' ); ?>&nbsp;
							<span style="color: #A78BFA;"><?php echo esc_attr( "{$main_title[$page_id]['page_title']}", 'wp-cloud-server' ); ?></span>
						</a>
    					<div class="uk-navbar-right">
							<?php
       		 				// Action Hook to allow extension modules to hook into the right-hand navbar
							do_action( 'wpcs_header_nav_bar_right_content' );
							?>
    					</div>
					</nav>
				
				</div>
			</div>
			
			<div style="border: 1px solid #ddd; min-height: 650px;" class="uk-section uk-section-xsmall uk-background-default uk-border-rounded-bottom">
    			<div class="uk-container uk-container-expand">
					<div>
       	 				<div uk-grid>
		<?php
	}
	
	/**
	 *  Output the WP Cloud Server Dashboard Left Sidebar
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_setup_dashboard_left_sidebar( $active_tab, $status, $module_name, $page_content, $redirect_id ) {
		global $settings_page;

		$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		?>
		
		<!-- Display the Left-Hand Dashboard Sidebar Menu -->
        <div style="padding: 0 15px 0 20px; border-right: 1px solid #efefef; height: 880px;" class="uk-width-1-6 uk-overflow-auto">
			<ul id="switcher-menu" class="uk-margin-small-top uk-nav uk-nav-default" data-uk-switcher="connect: #component-nav; active: <?php echo $redirect_id ?>;">
				<?php
				$count = 0;
				foreach ( $page_content[ $page_id ]['content'] as $pages => $page ) {

					$display_menu	= ( ( 'active' == $status ) && ( $module_name == $page['module'] ) ) ? 'true' : $page['active'] ;
					$display_menu	= ( ( 'inactive' == $status ) && ( $module_name == $page['module'] ) ) ? 'false' : $display_menu ;
					$display_header	= ( isset( $page['menu_active'] ) && ( 'true' == $page['menu_active'] ) ) ? 'true' : 'false' ; 

					if ( ( ! empty( $page['menu_header'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) {
						echo "<li class='uk-nav-header'>{$page['menu_header']}</li>";
						++$count;
					}
					
					if ( ( ! empty( $page['menu_divider'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) {
						echo "<li class='uk-nav-divider'></li>";
						++$count;
					}
					
					// If this is a settings page then save the position
					if ( 'settings' == $page['type'] ) {
						$settings_page[$page['module']] = $count;
					}
					if ( '' !== $page['menu_item'] ) {
						$active_class = ( $count == $redirect_id ) ? 'class="uk-active"' : '';
						echo ( 'true' == $display_menu ) ? "<li {$active_class} data-position='{$count}'><a href='#'>{$page['menu_item']}</a></li>" : '';
						$count = ( 'true' == $display_menu ) ? ++$count : $count;
					} else {
						echo '';
					}
					
				}
				?>	
            </ul>
        </div>

		<?php
	}
	
	/**
	 *  Output the WP Cloud Server Dashboard Main Content
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_setup_dashboard_main_content( $active_tab, $status, $module_name, $page_content, $redirect_id, $page_previous_id ) {
		global $settings_page;

		$task	 = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
		$item	 = isset( $_GET['deltemplate'] ) ? sanitize_text_field( $_GET['deltemplate'] ) : '';
		$item	 = isset( $_GET['delsshkey'] ) ? sanitize_text_field( $_GET['delsshkey'] ) : $item;
		$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'nopage';
		$tab	 = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
		?>
		
		<!-- Display the Main Content -->
        <div class="uk-margin-small-top uk-width-expand" >
        	<ul id="component-nav" class="uk-switcher">
				
				<?php
				$counter=0;
				$default = $page_content[ $page_id ]['default_page'];
				$default_tab = "1";
				foreach ( $page_content[ $page_id ]['content'] as $pages => $page ) {
					//$counter++;
					$display_menu	= ( ( 'active' == $status ) && ( $module_name == $page['module'] ) ) ? 'true' : $page['active'] ;
					$display_menu	= ( ( 'inactive' == $status ) && ( $module_name == $page['module'] ) ) ? 'false' : $display_menu ;
					$display_header	= ( isset( $page['menu_active'] ) && ( 'true' == $page['menu_active'] ) ) ? 'true' : 'false' ;
					//echo ( ( ! empty( $page['menu_header'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) ? "<li></li>" : '';
					//echo ( ( ! empty( $page['menu_divider'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) ? "<li></li>" : '';
					
					if ( ( ! empty( $page['menu_header'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) {
						echo "<li></li>";
						++$counter;
					}
					
					if ( ( ! empty( $page['menu_divider'] ) ) && ( ( 'true' == $display_header ) || ( 'true' == $display_menu ) ) ) {
						echo "<li></li>";
						++$counter;
					}

					if ( ( 'true' == $display_menu ) && ( '' !== $page['menu_item'] ) ) {
						$active_class = ( $counter == $redirect_id ) ? 'class="uk-active"' : '';
					?>
				
            			<li <?php echo "{$active_class}"; ?>>
							<div style="height: 880px;" class="uk-overflow-auto">
											
								<?php 
								wpcs_settings_errors();
								wpcs_output_confirmation_notice();
								do_action( 'wpcs_system_notices', $task, $item, $page );
								?>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
									<div class="uk-container uk-container-<?php echo ( isset($page['section_width']) && ( '' !== $page['section_width'] ) ) ? $page['section_width'] : 'medium' ; ?>">
									<?php 
									if ( ! empty( $page['tabs'] ) ) {

										$tab_counter	= 0;
										$module			= strtolower( str_replace( " ", "_", $page['module'] ) );
										$current_tab	= get_option( "wpcs_{$page['tab_block_id']}_current_tab", "0" );
										$redirect_tab	= ( $page_id == $page_previous_id ) ? $current_tab : $default_tab;

										// Create the class name for the tabs
										$tab_class		= explode( '_', $page['tab_block_id'] );
										$tab_class		= ( 'aws' == $tab_class[0] ) ? 'aws_lightsail' : $tab_class[0];

										update_option( "wpcs_{$page['tab_block_id']}_current_tab", $redirect_tab );
										?>
										<ul id="<?php echo $page['tab_block_id']; ?>"  data-tab-id="<?php echo $page['tab_block_id']; ?>" data-uk-tab='<?php echo "connect: #{$page['tab_block_id']}_content; active: {$redirect_tab}"; ?>'>	
										<?php foreach ( $page['tabs'] as $key => $tab ) { ?>
												<?php if ( 'true' == $page['tabs_active'][$key] ) { ?>
													<li class='<?php echo "{$tab_class}"; ?>' data-tab='<?php echo "{$tab_counter}"; ?>'><a href="#"><?php esc_html_e( $page['tabs'][$key], 'wp-cloud-server' ); ?></a></li>
													<?php $tab_counter++; ?>
												<?php } ?>
											
											<?php } ?>
										</ul>
							
										<ul id='<?php echo "{$page['tab_block_id']}_content"; ?>' class="uk-switcher uk-margin">
											<?php foreach ( $page['tabs_content'] as $key => $tab_content ) { ?>
												<?php if ( 'true' == $page['tabs_active'][$key] ) { ?>
													<li>
														<div class="uk-container uk-container-<?php echo ( isset( $page['tabs_width'][$key] ) ) ? $page['tabs_width'][$key] : 'medium' ; ?>">
															<?php if ( ! empty( $page['desc'] ) ) { ?>	
															<p class="uk-text-light uk-width-xlarge"><?php echo $page['desc']; ?></p>
															<?php } ?>
															<?php 
															if (file_exists( WPCS_PLUGIN_DIR . "{$page['template_path']}/{$tab_content}.php" ) ) { 
																require WPCS_PLUGIN_DIR . "{$page['template_path']}/{$tab_content}.php";
															} else { 
																do_action( 'wpcs_control_panel_tab_content', $tab_content, $page_content, $page_id );
															}
															?>
														</div>
													</li>
												<?php
												//} 
											  }
											}
										
											?>
											
										</ul>
										<script>
												(function($){
    												UIkit.switcher("#<?php echo $page['tab_block_id']; ?>").show(<?php echo $redirect_tab; ?>);
												})(jQuery);
										</script>
										
										<?php
											
									 } else { ?>
									
										<?php if ( ! empty( $page['title'] ) ) { ?>
											<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( $page['title'], 'wp-cloud-server' ); ?></h2>
										<?php } ?>
							
										<?php if ( ! empty( $page['subtitle'] ) ) { ?>
											<p class="uk-text-lead"><?php esc_html_e( $page['subtitle'], 'wp-cloud-server' ); ?></p>
										<?php } ?>
							
										<?php if ( ! empty( $page['desc'] ) ) { ?>	
											<p class="uk-text-light"><?php echo $page['desc']; ?></p>
										<?php } ?>
							
										<!-- Page Content -->
							
										<?php
										if (file_exists( WPCS_PLUGIN_DIR . "{$page['template_path']}/{$page['template']}.php" ) ) { 
											require WPCS_PLUGIN_DIR . "{$page['template_path']}/{$page['template']}.php";
										} else { 
											do_action( 'wpcs_control_panel_templates', $page );
										}
										?>

										<!-- End of Page Content -->
							
									<?php } ?>
									</div>
								</div>
							</div>
						</li>		
				
					<?php 
					++$counter;
					} 
				} 
				?>
            </ul>
        </div>

	<?php	
	}
	
	/**
	 *  Output the WP Cloud Server Dashboard Footer
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_setup_dashboard_footer() {
		$args = get_option( 'wpcs_arguments' );
		?>
        				</div>
    				</div>
    			</div>
						<div style="padding: 0 5px 0 5px;">
							<div style="border-top: 1px solid #e8e8e8;">
								<div style="padding: 22px 25px 0 25px;" class="uk-section uk-section-xsmall">
    								<div>
										<p class="uk-margin-remove-bottom uk-text-lighter uk-text-muted uk-float-left"><?php echo $args['footer_left']; ?></p>
									</div>
    								<div class="uk-margin-remove-bottom uk-text-lighter uk-text-muted uk-float-right">
										<?php foreach( $args['share_icons'] as $key => $arg ) { ?>
											<a href="<?php echo $arg['url']; ?>" uk-icon="icon: <?php echo $arg['icon']; ?>"></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
							
        			</div>
				</div>
		<?php
		
	}
	
	/**
	 *  Module Management
	 *
	 * 	Creates the Settings & Activate links on the 'All Modules' tab, based on the $_GET status data captured.
	 * 
	 *  @since 1.0.0
	 */
	public function wpcs_admin_manage_module( $select, $status, $module_name, $module_to_update, $settings ) {

		$module_info		= get_option( 'wpcs_module_list' );
		$module_config		= get_option( 'wpcs_module_config' );

		$data				= get_option( 'wpcs_module_action_response' );
		$status				= isset( $data['action'] ) ? $data['action'] : '';
		$module_to_update	= isset( $data['module'] ) ? $data['module'] : '';

		// Reset the Action Response
		update_option( 'wpcs_module_action_response', array() );

		$switcher_item		= ( ! empty( $settings ) ) ? $settings[$module_name] : 0;
		$setting_link		= "<a class='uk-link' href='#' uk-switcher-item='{$switcher_item}'>Settings</a>";
		
		$update_status		= (( '' !== $status ) && ( $module_name == $module_to_update ));
		$new_status 		= ( $update_status ) ? $status : $module_info[$module_name]['status'];

		$api_connected		= ( ! empty( $module_info[$module_name]['api_connected'] ) ) ? $module_info[$module_name]['api_connected'] : '';
		
		// Retrieve official active module count
		$updated_count 		= wpcs_active_modules();
		 
		// Set temporary module count for link visibility due to module status being updated later
		//if ( 'inactive' == $status )  {
		//	$updated_count = $module_count - 1;
		//} elseif ( 'active' == $status )  {
		//	$updated_count = $module_count + 1;
		//} elseif ( '' == $status )  {
		//	$updated_count = $module_count;
		//}
				
		// Set-up the Module Activation and Setting Links
		if ( 'settings' == $select )  {
			if ('inactive' == $new_status)  {
				wpcs_create_form( 'Activate', $module_name, 'active' );
			//}  elseif ( ( 'active' == $new_status ) && ( $updated_count > 1 ) ) {
			}  elseif ( ( 'active' == $new_status ) ) {
				wpcs_create_form( 'Deactivate', $module_name, 'inactive', $setting_link );					
			//}  elseif ( ( 'active' == $new_status ) && ( $updated_count <= 1 ) ) {
			//	echo $setting_link;					
			}
		}
		
		// Display the Module API Connected Status
		if ( 'api_connected' == $select )  {
			if ( ( 'inactive' == $new_status ) || ( '' == $api_connected ) )  {
				echo '<span style="color: red;">Disconnected</span>';
			} elseif ( '1' == $api_connected ) {
				echo '<span style="color: green;">Connected</span>';
			}			
		}
		
		// Display the Module Status
		if ( 'status' == $select )  {
			if ( 'inactive' == $new_status )  {
				echo '<span style="color: red;">Inactive</span>';
				wpcs_decrement_module_count();
			} elseif ( 'active' == $new_status ) {
				echo '<span style="color: green;">Active</span>';
				wpcs_increment_module_count();
			}			
		}
	}

	/**
	 *  Display General System Notices
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_output_uikit_notice( $task, $item, $page ) {

		$completed_tasks = get_option('wpcs_tasks_completed', array());
		
		$task_array = array(
			'sptemplate'	=> array(
							'type'	=>	'success',
							'message'	=> 'Your Managed Server Template was Successfully Saved',
							),
			'template'	=> array(
							'type'	=>	'success',
							'message'	=> 'The Server Template was Successfully Deleted',
							),
			'sshkey'	=> array(
							'type'	=>	'success',
							'message'	=> 'The SSH Key was Successfully Deleted',
							),
			);
		
		$type		= '';
		$message	= '';
		$output		= '';
		
		if ( current_user_can( 'manage_options' ) && ( array_key_exists($task, $task_array) ) && ( !in_array($item, $completed_tasks) ) ) {
			$type		= $task_array[$task]['type'];
			$message	= $task_array[$task]['message'];
		
			$output  = "<div class='uk-alert-{$type} uk-notice' uk-alert> \n";
        	$output .= "<p><strong>{$message}</strong></p>";
        	$output .= "</div> \n";
			
    		echo $output;
		}
	}
	
	/**
	 *  Display General System Notices
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_output_confirmations_notice( $task, $item, $page ) {

		$completed_tasks = get_option('wpcs_tasks_completed', array());
		
		$task = 'sptemplate';
		
		$task_array = array(
			'sptemplate'	=> array(
							'type'	=>	'success',
							'message'	=> 'Your Managed Server Template was Successfully Saved',
							),
			);
		
		$type		= '';
		$message	= '';
		$output		= '';
		
		if ( current_user_can( 'manage_options' ) && ( in_array('create_template', $completed_tasks) ) ) {
			$type		= $task_array[$task]['type'];
			$message	= $task_array[$task]['message'];
		
			$output  = "<div class='uk-alert-{$type} confirmation' uk-alert> \n";
        	$output .= "<p><strong>{$message}</strong></p>";
        	$output .= "</div> \n";
			
			$pos = array_search('create_template', $completed_tasks);

			// Remove from array
			unset($completed_tasks[$pos]);
			//unset($completed_tasks);
			
    		echo $output;
		}
	}
	
	/**
	 *  Display General System Notices
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_output_api_status_notice( $task, $item, $page ) {

		$completed_tasks	= get_option( 'wpcs_tasks_completed', array() );
		$task				= 'ServerPilot';
		$test				= wpcs_check_cloud_provider_api('ServerPilot');
		
		$task_array			= array(
								'ServerPilot'	=> array(
									'type'	=>	'danger',
									'message'	=> 'The Connection to the ServerPilot Service has been lost. This could be caused because the API is down!',
								),
								'sshkey'	=> array(
									'type'	=>	'success',
									'message'	=> 'The SSH Key was Successfully Deleted',
								),
							);
		
		$type				= '';
		$message			= '';
		$output				= '';
		
		if ( current_user_can( 'manage_options' ) && ( !$test ) && ( ! get_option( 'wpcs_dismissed_serverpilot_api_notice' ) ) ) {
			$type		= $task_array[$task]['type'];
			$message	= $task_array[$task]['message'];
		
			$output  = "<div class='uk-alert-{$type} wpcs-notice' data-notice='serverpilot_api_notice' uk-alert> \n";
        	$output .= "<a class='uk-alert-close' uk-close></a> \n";
        	$output .= "<p>{$message}</p>";
        	$output .= "</div> \n";
			
    		echo $output;
		}
	}
	
	/**
	 *  Display General System Notices
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_output_api_disabled_notice( $task, $item, $page ) {
		
		if ( !isset( $page['api_required'] ) ) {
			return;
		}

		$completed_tasks = get_option('wpcs_tasks_completed', array());

		$current_module	= strtolower( $page['module'] );
		
		$module = ( in_array( $current_module, $page['api_required'] ) ) ? $page['module'] : null;
		$cloud	= ( in_array( 'cloud_provider', $page['api_required'] ) ) ? true : false;
		
		$task	= ( $cloud ) ? 'cloud' : $module;
		$task	= ( ( $task=='cloud' ) && ( !empty($module) ) ) ? 'managed_cloud' : $task;
		$task	= ( ( !$cloud ) && ( empty($module) ) ) ? 'cloud' : $task;
		
		$test	= wpcs_check_cloud_provider_api( $module, null, $cloud );
		
		$task_array = array(
			'Ploi'	=> array(
				'type'	=>	'danger',
				'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to Ploi is required! Please check your API settings.',
				),
			'RunCloud'	=> array(
				'type'	=>	'danger',
				'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to RunCloud is required! Please check your API settings.',
				),
			'Cloudways'	=> array(
				'type'	=>	'danger',
				'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to Cloudways is required! Please check your API settings.',
				),
			'AWS Lightsail'	=> array(
				'type'	=>	'danger',
				'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to AWS Lightsail is required! Please check your API settings.',
				),
			'UpCloud'		=> array(
				'type'	=>	'danger',
				'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to UpCloud is required! Please check your API settings.',
				),
			'Brightbox'		=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to Brightbox is required! Please check your API settings.',
							),
			'Vultr'			=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to Vultr is required! Please check your API settings.',
							),
			'Linode'		=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to Linode is required! Please check your API settings.',
							),
			'ServerPilot'	=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to ServerPilot is required! Please check your API settings.',
							),
			'DigitalOcean'	=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to DigitalOcean is required! Please check your API settings.',
							),
			'cloud'			=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>Some services are not currently available!</strong> A valid connection to a cloud provider, such as DigitalOcean, is required! Please check API settings.',
							),
			'managed_cloud'	=> array(
							'type'	=>	'danger',
							'message'	=> "<strong>Some services are not currently available!</strong> A valid connection to {$page['module']} and a cloud provider, such as DigitalOcean, is required! Please check API settings.",
							),
			);
		
		$type = '';
		$message = '';
		$output = '';
		
		if ( current_user_can( 'manage_options' ) && ( !$test ) && ( array_key_exists( $task, $task_array ) ) ) {
			$type		= $task_array[$task]['type'];
			$message	= $task_array[$task]['message'];

			if ( isset( $task_array[$task]['type'] ) && isset( $task_array[$task]['message'] ) ) {
		
				$output  = "<div class='uk-alert-{$type} wpcs-notice' uk-alert> \n";
        		$output .= "<p>{$message}</p>";
        		$output .= "</div> \n";
			
    			echo $output;
			}
		}
	}
	
	/**
	 *  Display Call-to-Action Notifications
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_output_call_to_action_notice( $task, $item, $page ) {
		
		$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		if ( ( 'wp-cloud-server-web-hosting-plans' !== $page_id ) || ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) ) {
			return;
		}
		
		$nonce				= isset( $_GET['_wp_plugin_nonce'] ) ? sanitize_text_field( $_GET['_wp_plugin_nonce'] ) : ''; 
		$activate_plugin	= isset( $_GET['edd_plugin'] ) ? sanitize_text_field( $_GET['edd_plugin'] ) : '';
			
		if (( 'activate' == $activate_plugin ) && ( current_user_can( 'install_plugins' ) ) && ( wp_verify_nonce( $nonce, 'plugin_nonce' ) ) ) {
			
			// Include required libs for installation
            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php' );
            require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );

            // Get Easy Digital Downloads Plugin Info
            $api = plugins_api( 'plugin_information',
                   array(
                       'slug' => 'easy-digital-downloads',
                       'fields' => array(
                                       'short_description' => false,
                                       'sections' => false,
                                       'requires' => false,
                                       'rating' => false,
                                       'ratings' => false,
                                       'downloaded' => false,
                                       'last_updated' => false,
                                       'added' => false,
                                       'tags' => false,
                                       'compatibility' => false,
                                       'homepage' => false,
                                       'donate_link' => false,
                                   ),
                       )
                  );

           $skin     = new WP_Ajax_Upgrader_Skin();
           $upgrader = new Plugin_Upgrader( $skin );
           $upgrader->install($api->download_link);
           $activate_plugin = isset( $_GET['edd_plugin'] ) ? sanitize_text_field( $_GET['edd_plugin'] ) : '';
			
		   activate_plugin( 'easy-digital-downloads/easy-digital-downloads.php' );
			
		}
		
		$task	= 'DigitalOcean'; 
		
		$task_array = array(
			'ServerPilot'	=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>This service is currently not available!</strong> A valid connection to ServerPilot is required! Please check your API settings.',
							),
			'DigitalOcean'	=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>This service is currently not available!</strong> A valid connection to DigitalOcean is required! Please check your API settings.',
							),
			'cloud'			=> array(
							'type'	=>	'danger',
							'message'	=> '<strong>This service is currently not available!</strong> A valid connection to both ServerPilot and DigitalOcean is required! Please check API settings.',
							),
			);
		
		$type = '';
		$message = '';
		$output = '';
		
		if ( current_user_can( 'manage_options' ) ) {
			$link		= esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-web-hosting-plans&edd_plugin=activate' ), 'plugin_nonce', '_wp_plugin_nonce') );
			$type		= $task_array[$task]['type'];
			$message	= $task_array[$task]['message'];
		
			$output  = "<div class='uk-alert-primary wpcs-notice' uk-alert> \n";
        	$output .= "<div class='uk-container uk-container-xsmall'>";			
        	$output .= "<h3>Sell Web Hosting Plans to Customers and Clients</h3>";
		    $output .= "<p>The Server Templates created on this page can be used alongside the powerful 'Easy Digital Downloads' plugin to sell Web Hosting Plans to Clients and Customers.</p>";
		    $output .= "<p>It looks like you don't currently have 'Easy Digital Downloads' Plugin installed and activated. Simply click the button below to let us install and activate the plugin for you.</p>";
		    $output .= "<p>";
		    $output .= "<a class='uk-button uk-button-secondary' href='{$link}'>Install & Activate EDD Plugin</a>";
		    $output .= "</p>";
        	$output .= "</div> \n";
        	$output .= "</div> \n";
			
    		echo $output;
		}
	}
	
}