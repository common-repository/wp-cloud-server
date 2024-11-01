<?php

/**
 * The Setup Wizard functionality of the Plugin.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.0.0
 *
 * @package    	WP_Cloud_Server_Setup_Wizard
 */

class WP_Cloud_Server_Setup_Wizard {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		add_action( 'wpcs_enter_all_modules_page_before_content', array( $this, 'wpcs_setup_wizard_page_output' ) );
		add_action( 'wpcs_add_wizard_footer_section', array( $this, 'wpcs_wizard_footer_section' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'wpcs_wizard_setting_sections_and_fields' ), 10, 2 );

	}
		
	/**
	 *  Output the WP Cloud Server Setup Wizard Page
	 *
	 *  @since  2.0.0
	 */
	public function wpcs_setup_wizard_page_output() {
		
		$kses_exceptions = array(
			'a' => array(
					'href'		=> array(),
					'class'		=> array(),
					'target'	=> array(),
				),
		);

		$active_tab 			= isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'allmodules';
		$status					= isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		$submenu				= isset( $_GET['submenu'] ) ? sanitize_text_field( $_GET['submenu'] ) : '';
		$module_name			= isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';
		$reset_api				= isset( $_GET['resetapi'] ) ? sanitize_text_field( $_GET['resetapi'] ) : '';
		$skip_wizard_request	= isset( $_GET['skipwizard'] ) ? sanitize_text_field( $_GET['skipwizard'] ) : '';
		$nonce					= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';

		$setup_wizard_complete_confirmed = get_option( 'wpcs_setup_wizard_complete_confirmed', 'false' );
		$setup_wizard_complete	= get_option( 'wpcs_setup_wizard_complete', 'false' );
		$modules				= get_option( 'wpcs_module_list' );
		$debug_enabled			= get_option( 'wpcs_enable_debug_mode' );
		$redirect_enable		= get_option( 'wpcs_sp_api_redirect_enable' );
		
		$redirect_explode		= explode( '|', $redirect_enable );
		$redirect_enabled		= $redirect_explode[0];
		$redirect_id			= ( 'true' == $redirect_enabled ) ? $redirect_explode[1] : '2';
		
		update_option( 'wpcs_sp_api_redirect_enable', 'false' );
		
		if ( ( 'true' == $skip_wizard_request ) && ( current_user_can( 'manage_options' ) ) && ( wp_verify_nonce( $nonce, 'wizard_nonce' ) ) ) {
			update_option( 'wpcs_skip_setup_wizard', 'true' );
		}
		
		$skip_wizard_confirmed				= get_option( 'wpcs_skip_setup_wizard' );
		$setup_wizard_complete_confirmed	= get_option( 'wpcs_setup_wizard_complete_confirmed' );
		
		if ( ( 'true' == $skip_wizard_confirmed ) || ( 'true' == $setup_wizard_complete_confirmed ) ) {
			return;
		}

		?>
		 
		<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>

		<div style="margin: 10px 20px 0 0;" class="wrap-custom">

			<div class="uk-section uk-section-xsmall uk-padding-remove-top uk-padding-remove-bottom uk-background-default">
    			<div>
					<nav style="border: 1px solid #000; background-color: #000;" class="uk-navbar-container uk-container uk-container-expand uk-light uk-border-rounded-top" uk-navbar>
						<a class="uk-navbar-item uk-logo uk-padding-remove-left" href="#">WP Cloud Server -&nbsp;<span style="color: #B794F4;"><?php esc_html_e( 'Setup Wizard', 'wp-cloud-server' ); ?></span></a>
    					<div class="uk-navbar-right">
							<?php do_action( 'wpcs_setup_wizard_navbar_right'  ) ?>
    					</div>
					</nav>
				</div>
			</div>
			
			<div style="border: 1px solid #ddd; min-height: 650px;" class="uk-section uk-section-xsmall uk-background-default uk-border-rounded-bottom">
    			<div class="uk-container uk-container-expand">
       	 			<div uk-grid>
            			<div style="padding: 0 15px 0 20px; border-right: 1px solid #E9D8FD;" class="uk-width-1-6">
							<ul id="switcher-menu" class="uk-margin-small-top uk-nav uk-nav-default" uk-switcher="connect: #component-nav; animation: uk-animation-fade; active: <?php echo $redirect_id ?>;">
                    			<li class="uk-nav-header"><?php esc_html_e( '1. Introduction', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'How to use the Setup Wizard?', 'wp-cloud-server' ); ?></a></li>
								<li class="uk-nav-header"><?php esc_html_e( '2. Cloud Providers', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'DigitalOcean Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'Vultr Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'Linode Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'UpCloud Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'AWS Lightsail Settings', 'wp-cloud-server' ); ?></a></li>
								<li class="uk-nav-header"><?php esc_html_e( '3. Server Management', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'ServerPilot Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'RunCloud Settings', 'wp-cloud-server' ); ?></a></li>
								<li><a href="#"><?php esc_html_e( 'Ploi Settings', 'wp-cloud-server' ); ?></a></li>
								<li class="uk-nav-header"><?php esc_html_e( '4. Managed Hosting', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'Cloudways Settings', 'wp-cloud-server' ); ?></a></li>
								<li class="uk-nav-header"><?php esc_html_e( '5. SSH Key', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'Add SSH Key', 'wp-cloud-server' ); ?></a></li>
								<li class="uk-nav-header"><?php esc_html_e( '6. Complete Setup Wizard', 'wp-cloud-server' ); ?></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><?php esc_html_e( 'Save Settings &amp; Exit', 'wp-cloud-server' ); ?></a></li>
                			</ul>
            			</div>
            			<div class="uk-margin-small-top uk-width-expand@m">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_setup_wizard_settings_action">
							<input type="hidden" id="wpcs_setup_wizard_complete" name="wpcs_setup_wizard_complete" value="true">
							<input name="wpcs_current_page" id="wpcs_current_page" type="hidden" value="2">
							<?php wp_nonce_field( 'handle_setup_wizard_settings_action_nonce', 'wpcs_handle_setup_wizard_settings_action_nonce' ); ?>
                				<ul id="component-nav" class="uk-switcher">
									
									<li></li>
									<li></li>
                    				<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Welcome Page Content-->
											
														<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Welcome to the WP Cloud Server Setup Wizard', 'wp-cloud-server' ); ?></h2>
														
														<p><?php esc_html_e( 'This Setup Wizard takes you step-by-step through configuring the \'WP Cloud Server\' plugin for the first time. Please read the points below about what information you need before you start!', 'wp-cloud-server' ); ?></p>	
														<ul>
															<li><?php esc_html_e( 'a. You will need your own account with any service you wish to use, such as DigitalOcean, Vultr or ServerPilot, etc.', 'wp-cloud-server' ); ?></li>
															<li><?php esc_html_e( 'b. You will need to log-in to each account, generate API Keys/Tokens and copy them for use later.', 'wp-cloud-server' ); ?></li>
															<li><?php esc_html_e( 'c. You will need to generate an SSH Key on the computer you want to access your servers from, and have the Public Key available.', 'wp-cloud-server' ); ?></li>
														</ul>
														
														<p><?php esc_html_e( "NOTE: Each section of the setup wizard is optional. You may not have a need to use a particular service, such as ServerPilot, or maybe you don't want to set-up SSH Keys yet! That is fine. All you need to do is visit the sections that are relevant to you and enter the settings, finally go to 'Complete Setup Wizard' to save your settings. You can change settings later and even add additional SSH Keys if required.", "wp-cloud-server" ); ?></p>
														<p><?php esc_html_e( "To get started click the 'Next' button below, or use the menu on the left. If you prefer doing things manually then click the 'Skip the Setup Wizard' below to exit the setup wizard!", "wp-cloud-server" ); ?></p>
														
														<!-- Setup Wizard - End of Welcome Page Content -->
													
													</div>
												</div>
													
												<?php do_action( 'wpcs_add_wizard_footer_section', 'Start', '5'  ) ?>
											
											</div>
										</div>
									</li>
									<li></li>
									<li></li>
                    				<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - DigitalOcean API Settings Page Content-->
											
														<div class="content">
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'DigitalOcean API Token', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to create and manage your DigitalOcean Servers you will need to generate and copy an API Token from
															inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://cloud.digitalocean.com/login' target='_blank'>DigitalOcean Dashboard</a>. Once you have your new API Token paste it below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Token:', 'wp-cloud-server' ); ?></th>
																		<td>
																		<input style="width: 25rem;" name="wpcs_setup_digitalocean_api_key" placeholder="Enter DigitalOcean API Key here ...." id="wpcs_setup_digitalocean_api_key" type="password" class="uk-input">
																		</td>
																	</tr>
																</tbody>
															</table>
															<p><?php esc_html_e( 'NOTE: We use the official DigitalOcean REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your API Token at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.', 'wp-cloud-server' ); ?></p>
													</div>
														
													<!-- Setup Wizard - End of DigitalOcean API Settings Page Content -->
														
												</div>
											</div>
													
											<?php do_action( 'wpcs_add_wizard_footer_section', '2', '6' ) ?>
														
											</div>
										</div>
									</li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Vultr API Settings Page Content-->
											
														<div class="content">
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Vultr API Token', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to create and manage your Vultr Servers you will need to generate and copy an API Token from
															inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://my.vultr.com' target='_blank'>Vultr Dashboard</a>. Once you have your new API Token paste it below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Token:', 'wp-cloud-server' ); ?></th>
																		<td>
																		<input style="width: 25rem;" name="wpcs_setup_vultr_api_key" placeholder="Enter Vultr API Key here ...." id="wpcs_setup_vultr_api_key" type="password" class="uk-input">
																		</td>
																	</tr>
																</tbody>
															</table>
															<p><?php esc_html_e( 'NOTE: We use the official Vultr REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your API Token at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.', 'wp-cloud-server' ); ?></p>
													</div>
														
													<!-- Setup Wizard - End of DigitalOcean API Settings Page Content -->
														
												</div>
											</div>
													
											<?php do_action( 'wpcs_add_wizard_footer_section', '5', '7' ) ?>
														
											</div>
										</div>
									</li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Linode API Settings Page Content-->
											
														<div class="content">
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Linode API Token', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to create and manage your Linode Servers you will need to generate and copy an API Token from
															inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://login.linode.com/login' target='_blank'>Linode Dashboard</a>. Once you have your new API Token paste it below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Token:', 'wp-cloud-server' ); ?></th>
																		<td>
																		<input style="width: 25rem;" name="wpcs_setup_linode_api_key" placeholder="Enter Linode API Key here ...." id="wpcs_setup_linode_api_key" type="password" class="uk-input">
																		</td>
																	</tr>
																</tbody>
															</table>
															<p><?php esc_html_e( 'NOTE: We use the official Linode REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your API Token at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.', 'wp-cloud-server' ); ?></p>
													</div>
														
													<!-- Setup Wizard - End of Linode API Settings Page Content -->
														
												</div>
											</div>
													
											<?php do_action( 'wpcs_add_wizard_footer_section', '6', '8' ) ?>
														
											</div>
										</div>
									</li>
                    				<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - UpCloud API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'UpCloud API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage your UpCloud Servers you will need to generate a 'Username' and 'Password' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://hub.upcloud.com/login' target='_blank'>UpCloud Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Username:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Username ..." id="wpcs_setup_upcloud_username" name="wpcs_setup_upcloud_username" value="">
																				
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Password:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Password ..." id="wpcs_setup_upcloud_password" name="wpcs_setup_upcloud_password" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official UpCloud REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'Username' and 'Password' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of UpCloud API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '7', '9' ) ?>		
											</div>
										</div>
									</li>
                    				<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - AWS Lightsail API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'AWS Lightsail API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage your AWS Lightsail Servers you will need to generate a 'Access Key' and 'Secret Key' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://lightsail.aws.amazon.com/ls/webapp/home/' target='_blank'>AWS Lightsail Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Access Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Access Key ..." id="wpcs_setup_aws_lightsail_access_key" name="wpcs_setup_aws_lightsail_access_key" value="">
																				
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Secret Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Secret Key ..." id="wpcs_setup_aws_lightsail_secret_key" name="wpcs_setup_aws_lightsail_secret_key" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official AWS Lightsail REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'Access Key' and 'Secret Key' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of AWS Lightsail API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '8', '12' ) ?>		
											</div>
										</div>
									</li>
									<li></li>
									<li></li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - ServerPilot API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'ServerPilot API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage your servers using ServerPilot you will need to generate a 'Client ID' and an 'API Key' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://manage.serverpilot.io' target='_blank'>ServerPilot Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Client ID:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Client ID ..." id="wpcs_setup_serverpilot_client_id" name="wpcs_setup_serverpilot_client_id" value="">
																				
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter API Key ..." id="wpcs_setup_serverpilot_api_key" name="wpcs_setup_serverpilot_api_key" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official ServerPilot REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'Client ID' and 'API Token' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of ServerPilot API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '9', '13' ) ?>		
											</div>
										</div>
									</li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - RunCloud API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'RunCloud API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage your Cloud Servers using RunCloud you will need to generate a 'API Key' and an 'API Secret' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://manage.runcloud.io/auth/login' target='_blank'>RunCloud Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter API Key ..." id="wpcs_setup_runcloud_api_key" name="wpcs_setup_runcloud_api_key" value="">
																				
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Secret:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter API Secret ..." id="wpcs_setup_runcloud_api_secret" name="wpcs_setup_runcloud_api_secret" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official RunCloud REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'API Key' and 'API Secret' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of RunCloud API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '12', '14' ) ?>		
											</div>
										</div>
									</li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Ploi API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Ploi API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage your Cloud Servers using Ploi you will need to generate a 'API Key' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://ploi.io/login' target='_blank'>Ploi Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter API Key ..." id="wpcs_setup_ploi_api_key" name="wpcs_setup_ploi_api_key" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official Ploi REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'API Key' and 'API Secret' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of Ploi API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '13', '17' ) ?>		
											</div>
										</div>
									</li>
									<li></li>
									<li></li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Cloudways API Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Cloudways API Settings', 'wp-cloud-server' ); ?></h2>
															<p><?php echo wp_kses( "To allow the WP Cloud Server plugin to manage hosting using Cloudways you will need to have a valid email and 'API Key' from inside your own <a class='uk-link' style='color: #9F7AEA;' href='https://platform.cloudways.com/login' target='_blank'>Cloudways Dashboard</a>. Once you have them both, copy and then paste below, then click 'Next' below to continue.", $kses_exceptions ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'Email:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter Email ..." id="wpcs_setup_cloudways_email" name="wpcs_setup_cloudways_email" value="">
																				
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'API Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="password" placeholder="Enter API Key ..." id="wpcs_setup_cloudways__api_key" name="wpcs_setup_cloudways_api_key" value="">
																				
																			</td>
																	</tr>
																</tbody>
															</table>							
															<p><?php esc_html_e( "NOTE: We use the official Cloudways REST API to connect to your account and only perform
															the commands requested by you through our interface. You are able to delete your 'Email' and 'API Key' at any time! It is used by the plugin and is never saved or copied outside of your own WordPress installation.", "wp-cloud-server" ); ?></p>
														</div>
													
														<!-- Setup Wizard - End of Cloudways API Key Settings Page Content -->
														
													</div>
												</div>
													<?php do_action( 'wpcs_add_wizard_footer_section', '14', '20' ) ?>		
											</div>
										</div>
									</li>
									<li></li>
									<li></li>
									<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - ServerPilot SSH Key Settings Page Content-->
											
														<div class="content">
															
															<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Add New SSH Key', 'wp-cloud-server' ); ?></h2>
															<p><?php esc_html_e( 'SSH Keys allow you to securely log-in to your Servers over SSH without the need to remember standalone passwords. Once you generate a new SSH Key on your computer you can copy the Public Key and paste it below, then enter a name for reference. This SSH Key will be available for use with any cloud provider, and is selectable when creating new servers.', 'wp-cloud-server' ); ?></p>
															<p><?php esc_html_e( "The SSH Key saved here is to get you started. You can add additional SSH Keys, as well as viewing and deleting existing Keys from inside the general settings. Once you have entered your SSH Key then click 'Next' to continue.", "wp-cloud-server" ); ?></p>
															<table class="form-table" role="presentation">
																<tbody>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'SSH Key Name:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<input style="width: 25rem;" class="uk-input" type="text" placeholder="Enter Name for SSH Key" id="wpcs_setup_serverpilot_ssh_key_name" name="wpcs_setup_serverpilot_ssh_key_name" value="">
																				<p class="text_desc"><?php esc_html_e( '[You can use any valid text, numeric, and space characters]', 'wp-cloud-server' ); ?></p>
																			</td>
																	</tr>
																	<tr>
																		<th scope="row"><?php esc_html_e( 'SSH Public Key:', 'wp-cloud-server' ); ?></th>
																			<td>
																				<textarea class="uk-textarea" name="wpcs_setup_serverpilot_ssh_key" placeholder="Enter SSH Public Key ..." rows="7" style="width:100%;"></textarea>
																			</td>
																	</tr>
																</tbody>
															</table>
														</div>
														
														<!-- Setup Wizard - End of ServerPilot SSH Key Settings Page Content -->
														
													</div>
												</div>								
													
												<?php do_action( 'wpcs_add_wizard_footer_section', '17', '23' ) ?>
														
											</div>
										</div>
									</li>
									<li></li>
									<li></li>
                    				<li>
										<div class="uk-overflow-auto">
											<div style="background-color: #FAF5FF; border: 1px double #E9D8FD; margin-bottom: 20px;" class="uk-section uk-section-small uk-section-default uk-border-rounded">
												<div style=" min-height: 420px;" class="uk-section uk-section-small">
    												<div class="uk-container uk-container-small">
														
														<!-- Setup Wizard - Complete Setup Wizard-->
											
														<h2 class="uk-margin-remove-top uk-heading-divider"><?php esc_html_e( 'Complete Setup Wizard', 'wp-cloud-server' ); ?></h2>
											
														<p><?php esc_html_e( "Thank you for using the Setup Wizard. If you are happy with the settings you have entered then click the 'Save Settings' button below. This will save the settings and close the setup wizard. We hope you enjoy
														using the 'WP Cloud Server' plugin. Please don't hesitate to contact us if you have questions or problems!", "wp-cloud-server" ); ?></p>
														
														<p><?php esc_html_e( "If you're not happy that you entered the correct information you can use the menu or the 'Previous' and 'Next' buttons to make changes. You will also be able to make changes later
														via the module settings pages!", "wp-cloud-server" ); ?></p>
														
														<!-- Setup Wizard - End of Complete Setup Wizard -->
													
													</div>
												</div>
													
												<?php do_action( 'wpcs_add_wizard_footer_section', '20', 'Finish'  ) ?>
											
											</div>
										</div>
									</li>
                				</ul>
							</form>
								<!-- Setup Wizard -->
								<?php
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
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_wizard_footer_section( $previous, $next ) {
		
		$text = 'Next ';
		$link = "<a class='uk-button uk-button-default' href='#' uk-switcher-item='{$next}'>{$text}<span class='uk-margin-small-left' uk-pagination-next></span></a>";
		
		if ( 'Finish' == $next ) {
			$text	= 'Finish ';
			$url	= esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&wizardcomplete=true'), 'wizard_complete_nonce', '_wpnonce') );
			$link = '<input type="submit" name="save_wizard_settings" id="save_wizard_settings" class="uk-button uk-button-default" value="Save Settings">';
		}
		
		$class = ( 'Start' == $previous ) ? "uk-button uk-button-default uk-invisible" : "uk-button uk-button-default";
		
		?>

		<div class="uk-section uk-section-small uk-padding-remove-bottom">
    		<div class="uk-container uk-container-small">
				<ul class="uk-pagination">
    				<li><a class="<?php echo $class; ?>" href="#" uk-switcher-item="<?php echo $previous; ?>"><span class="uk-margin-small-right" uk-pagination-previous></span> Previous</a></li>
    				<li class="uk-margin-auto-left">
						<?php
							if ( 'Finish' == $next ) { 
								echo $link;
							} else {
								echo $link;
							}
						?>
					</li>
				</ul>
													
				<div class="uk-section uk-section-xsmall uk-padding-remove-top uk-padding-remove-bottom">
					<div class="uk-container uk-container-small">
						<p class="uk-margin-auto uk-text-center uk-text-muted">
							<a style="color: #9F7AEA;" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-admin-menu&skipwizard=true'), 'wizard_nonce', '_wpnonce') );?>"><?php esc_attr_e( 'Skip the Setup Wizard', 'wp-cloud-server' ) ?></a>
						</p>
					</div>
				</div>
														
			</div>
		</div>
		
		<?php		
	}
	
	/**
	 *  Register setting sections and fields.
	 *
	 *  @since 1.0.0
	 */
	public function wpcs_wizard_setting_sections_and_fields() {

		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_digitalocean_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_digitalocean_ssh_key_name' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_digitalocean_ssh_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_serverpilot_client_id' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_serverpilot_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_serverpilot_ssh_key_name' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_serverpilot_ssh_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_vultr_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_linode_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_upcloud_username' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_upcloud_password' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_runcloud_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_runcloud_api_secret' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_wizard_complete' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_cloudways_email' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_cloudways_api_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_aws_lightsail_access_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_aws_lightsail_secret_key' );
		register_setting( 'wp_cloud_server_setup_wizard', 'wpcs_setup_ploi_secret_key' );
		
		add_settings_section(
			'wp_cloud_server_setup_wizard',
			esc_attr__( 'WP Cloud Server - Setup Wizard', 'wp-cloud-server' ),
			array( $this, 'section_callback_setup_wizard' ),
			'wp_cloud_server_setup_wizard'
		);

		add_settings_field(
			'wpcs_setup_digitalocean_api_key',
			esc_attr__( 'API Key:', 'wp-cloud-server' ),
			array( $this, 'field_callback_setup_digitalocean_api_key' ),
			'wp_cloud_server_setup_wizard',
			'wp_cloud_server_setup_wizard'
		);
	}
	
	/**
	 *  General Settings Section Callback.
	 *
	 *  @since 1.0.0
	 */
	public function section_callback_setup_wizard() {
		echo '<p>';
		echo wp_kses( 'Welcome to the WP Cloud Server Plugin. Before you can use this plugin we need to take you through the setup wizard.', 'wp-cloud-server' );
		echo '</p>';
	}
	
	/**
	 *  Debug Mode Field Callback.
	 *
	 *  @since 1.0.0
	 */		
	public function field_callback_setup_digitalocean_api_key() {
 		echo '<input name="wpcs_setup_digitalocean_api_key" placeholder="Enter DigitalOcean API Key here ...." id="wpcs_setup_digitalocean_api_key" type="text" class="uk-input" />';
 	}
	
}