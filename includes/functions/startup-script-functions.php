<?php
/**
 * WP Cloud Server - Startup Script Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create Startup Script.
*
* @since    3.0.6
*/
function wpcs_create_startup_script( $tabs_content, $page_content, $page_id ) {

	$debug_enabled		= get_option( 'wpcs_enable_debug_mode' );
	$sp_response		= '';
	$server_script		= '';
	$debug_data['api']	= 'no';
	$startup_scripts	= array();
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php 
			settings_fields( 'wpcs_startup_script' );
			wpcs_do_settings_sections( 'wpcs_startup_script' );
			wpcs_submit_button( 'Save Startup Script', 'secondary', 'add-startup-script' );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_startup_script_name' ) ) {

		$startup_script_name	= get_option( 'wpcs_startup_script_name' );
		$startup_script_summary	= get_option( 'wpcs_startup_script_summary' );
		$startup_script_value	= get_option( 'wpcs_startup_script' );
		
		// Set-up the data for the new Droplet
		$startup_script_data = array(
			"name"				=>  $startup_script_name,
			"summary"			=>  $startup_script_summary,			
			"startup_script"	=>  $startup_script_value,
			"type"				=>	'script',
		);

		// Retrieve the Active Module List
		$startup_scripts		= get_option( 'wpcs_startup_scripts' );
			
		// Save the VPS Template for use with a Plan
		$startup_scripts[ $startup_script_data['name'] ] = $startup_script_data;
			
		update_option( 'wpcs_startup_scripts', $startup_scripts );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_startup_script_name' );
	delete_option( 'wpcs_startup_script_summary' );
	delete_option( 'wpcs_startup_script' );
	
}
add_action( 'wpcs_create_startup_script_content', 'wpcs_create_startup_script', 10, 3 );

/**
*  List Startup Scripts
*
*  @since  3.0.6
*/
function wpcs_list_startup_scripts( $tabs_content, $page_content, $page_id, $module ) {
	
	foreach ( $page_content["wp-cloud-servers-{$module}"]['content'] as $key => $page_list ) {
		if ( 'create-user-startup-scripts' == $page_list['id'] ) {
			$page = $page_list;
		}
	}

	$modules		= get_option( 'wpcs_module_list' );
	$github_active	= ( isset( $modules['GitHub']['status'] ) && ( 'active' == $modules['GitHub']['status'] ) ) ? 'true' : 'false';
	
	$startup_scripts	= get_option( 'wpcs_startup_scripts' );

$del_script_nonce	= isset( $_GET['_wp_del_script_nonce'] ) ? sanitize_text_field( $_GET['_wp_del_script_nonce'] ) : '';
$nonce				= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';
$delscript			= isset( $_GET['delscript'] ) ? sanitize_text_field( $_GET['delscript'] ) : '';
$script_name		= isset( $_GET['script'] ) ? sanitize_text_field( $_GET['script'] ) : '';
$type				= isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';

if ( !empty( $startup_scripts ) ) {
	foreach ( $startup_scripts as $key => $script ) {
		if ( $delscript == $script['name'] ) {
			unset( $startup_scripts[$key] );
		}	
	}
}

update_option( 'wpcs_startup_scripts', $startup_scripts );

if ( !empty($script_name) && ( 'view' == $type ) ) {
	foreach ( $startup_scripts as $key => $script ) {
		if ( $script_name == $script['name'] ) {
			$update_script 					= get_option( 'wpcs_update_startup_script' );
			
			if ( isset( $update_script ) ) {

				// Set-up the data for the new Droplet
				$startup_script_data = array(
					"name"					=>  get_option( 'wpcs_update_startup_script_name' ),
					"summary"				=>  get_option( 'wpcs_update_startup_script_summary' ),			
					"startup_script"		=>  $update_script, 
				);
				
				$script['startup_script']	= get_option( 'wpcs_update_startup_script' );
				$script['name']				= get_option( 'wpcs_update_startup_script_name' );
				$script['summary'] 			= get_option( 'wpcs_update_startup_script_summary' );
				
				update_option( 'wpcs_update_startup_scripts_debug', $startup_script_data );

				// Retrieve the Active Module List
				$startup_scripts			= get_option( 'wpcs_startup_scripts' );
			
				// Save the VPS Template for use with a Plan
				$startup_scripts[ $startup_script_data['name'] ] = $startup_script_data;
			
				update_option( 'wpcs_startup_scripts', $startup_scripts );
				
				delete_option( 'wpcs_update_startup_script_name' );
				delete_option( 'wpcs_update_startup_script_summary' );
				delete_option( 'wpcs_update_startup_script' );
			}

			?>
			<div id="startup-script-modal" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
					<button class="uk-modal-close-default" type="button" uk-close></button>
        			<h2 class="uk-modal-title"><?php _e( 'Manage Startup Scripts', 'wp-cloud-server' ); ?></h2>
					<hr class="clear">
					
						<p><?php _e( 'This page allows the startup script to be editted. Note that changing the name will create a new startup script and keep the original!', 'wp-cloud-server' ); ?></p>
					
						<form method="post" action="options.php">
							<div class="server-info uk-modal-body" uk-overflow-auto>
								<?php settings_fields( 'wpcs_update_startup_script' ); ?>
							<table class="form-table" role="presentation">
								<tbody>
									<tr>
										<th scope="row"><?php _e( 'Name:', 'wp-cloud-server' ); ?></th>
											<td>
												<input class="uk-input" name="wpcs_update_startup_script_name" id="wpcs_update_startup_script_name" value="<?php echo $script['name']; ?>">
											</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Description:', 'wp-cloud-server' ); ?></th>
											<td>
												<input class="uk-input" name="wpcs_update_startup_script_summary" id="wpcs_update_startup_script_summary"  value="<?php echo $script['summary']; ?>">
											</td>
									</tr>
								</tbody>
							</table>
							<hr>
							<?php
							$content   = $script['startup_script'];
							$editor_id = 'wpcs_update_startup_script';
							wp_editor( $content, $editor_id );
							?>
							</div>
					<hr>
							<?php
							wpcs_submit_button( 'Update Script', 'secondary', 'update-startup-script' );
							?>
						</form>
					
    			</div>
			</div>
			<div id="modal-group-2" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-header">
           	 			<h2 class="uk-modal-title"><?php _e( 'Confirm Delete Server', 'wp-cloud-server' ); ?></h2>
        			</div>
        <div class="uk-modal-body">
			<p class="uk-text-lead"><?php _e( 'Take care! This will Delete the server from your DigitalOcean Account! It cannot be reversed!', 'wp-cloud-server' ); ?></p>
            <p class="uk-text-lead"><?php _e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        </div>
					<div class="uk-modal-footer uk-text-right">
						<div class="uk-button-group uk-margin-remove-bottom">
							<a class="uk-button uk-button-default uk-margin-small-right" href="#cloud-server-modal" uk-toggle><?php _e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            				<a class="uk-button uk-button-default" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-cloud-servers&action=delete&manageserver=' . $script['name'] ), 'digitalocean_manage_nonce', '_wp_manage_nonce') );?>"><?php esc_attr_e( 'CONFIRM', 'wp-cloud-server' ) ?></a>
						</div>
        			</div>
    </div>
</div>

				<script>
				(function($){
					var modal = UIkit.modal("#startup-script-modal");
					modal.show();
				})(jQuery);
			</script>
<?php
		}
	}
}
?>

<table class="uk-table uk-table-striped">
	<thead>
        <tr>
            <th class="uk-width-small"><?php echo $page['table']['heading1'] ; ?></th>
			<th class="uk-table-expand"><?php echo $page['table']['heading2'] ; ?></th>
			<? if ( 'true' == $github_active ) { ?> 
				<th><?php echo $page['table']['heading3'] ; ?></th>
				<th><?php echo $page['table']['heading4'] ; ?></th>
			<? } ?>
			<th class="uk-width-small"><?php echo $page['table']['heading5'] ; ?></th>
        </tr>
    </thead>
    <tbody>
		<?php
		if ( !empty( $startup_scripts ) ) {
			foreach ( $startup_scripts as $key => $script ) {
				$script_type = ( 'true' !== $github_active ) ? ( 'git' !== $script['type'] ) : true;
				if ( $script_type ) {
					?>
        			<tr>
            		<td><?php echo $script['name']; ?></td>
					<td><?php echo $script['summary']; ?></td>
					<?php if ( 'true' == $github_active ) { ?>
						<td>
							<?php
							$github = ( $script['github_repos'] ) ? $script['github_repos'] : '-';
							echo $github;
							?>
						</td>
						<td>
							<?php
							$github = ( $script['github_file'] ) ? $script['github_file'] : '-';
							echo $github;
							?>
						</td>
						<?php } ?>
						<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( "admin.php?page=wp-cloud-servers-{$module}&type=view&script=" . $script['name'] . '&_wpnonce=' . $nonce . '' ), 'script_nonce', '_wp_script_nonce') );?>"><?php esc_attr_e( 'Edit', 'wp-cloud-server' ) ?></a> | <a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( "admin.php?page=wp-cloud-servers-{$module}&type=script&delscript=" . $script['name'] . '&_wpnonce=' . $nonce . '' ), 'del_script_nonce', '_wp_del_script_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
        			</tr>
				<?php
				}
			}
		} else {
			?>
				<tr>
					<td colspan="5"><?php _e( 'No Startup Scripts Available', 'wp-cloud-server' ) ?></td>
				</tr>
			<?php
		}
		?>
    </tbody>
</table>
		
	<?php
}
add_action( 'wpcs_list_startup_scripts_content', 'wpcs_list_startup_scripts', 10, 3 );