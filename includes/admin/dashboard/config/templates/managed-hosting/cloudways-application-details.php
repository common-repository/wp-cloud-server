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

$api = New WP_Cloud_Server_Cloudways_API();

$module_name	= 'ServerPilot';
$debug_enabled	= get_option( 'wpcs_enable_debug_mode' );
$servers		= $api->call_api( 'server', null, false, 900, 'GET' );
$module_data	= get_option( 'wpcs_module_list' );
$manageserver	= isset( $_GET['manageserver'] ) ? sanitize_text_field( $_GET['manageserver'] ) : '';
$action			= isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

// Make sure that we don't rerun the previous action if page is reloaded
$last_action	= get_option( 'wpcs_last_server_action', '' );
if ( $action == $last_action ) {
	$action = '';
} else {
	update_option( 'wpcs_last_server_action', $action );
}

if ( !empty($manageserver) ) {
	foreach ( $servers['data'] as $key => $server ) {
		
		// Obtain Key for desired PHP Runtime Version (ServerPilot occasionally changes the availability!)
		if ( is_array( $server['available_runtimes'] ) ) {
			$key = ( count( $server['available_runtimes'] ) - 1 );
			$php_version = $server['available_runtimes'][ $key ];
		} else {
			$php_version = 'php7.4';
		}
					
		if ( $manageserver == $server['name'] ) {
			
			if ( 'delete' == $action ) {
				$api_call		= "servers/{$server['id']}";
				$request_type	= 'DELETE';
			}
					
			if ( !empty($action) ) {
				$response		= $api->call_api( $api_call, null, false, 900, $request_type );
			}
			

			?>
			<div id="managed-server-modal" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
					<button class="uk-modal-close-default" type="button" uk-close></button>
        			<h2 class="uk-modal-title"><?php _e( 'Manage Cloud Server', 'wp-cloud-server' ); ?></h2>
        			
					<hr class="clear">
					<h3 class="uk-margin-remove-top"><?php _e( 'Server Information', 'wp-cloud-server' ); ?></h3>
					<div class="server-info uk-modal-body" uk-overflow-auto>
						<table class="server-info uk-table uk-table-striped">
    						<thead>
        						<tr>
            						<th><?php _e( 'Feature', 'wp-cloud-server' ); ?></th>
            						<th><?php _e( 'Value', 'wp-cloud-server' ); ?></th>
        						</tr>
    						</thead>
    						<tbody>
        						<tr>
            						<td><?php _e( 'Server Name', 'wp-cloud-server' ); ?></td>
            						<td><?php esc_html_e( $server['name'] ); ?></td>
       							</tr>
        						<tr>
            						<td><?php _e( 'Plan', 'wp-cloud-server' ); ?></td>
            						<?php $plan = ( $server['plan'] == 'first_class' ) ? 'First Class' : ucfirst($server['plan']); ?>
									<td><?php esc_html_e( $plan ); ?></td>
        						</tr>
        						<tr>
            						<td><?php _e( 'Auto Updates', 'wp-cloud-server' ); ?></td>
           							<td><?php echo ( 1 == $server['autoupdates'] ) ? 'Enabled' : 'Not Enabled'; ?></td>
       	 						</tr>
        						<tr>
            						<td><?php _e( 'PHP Version', 'wp-cloud-server' ); ?></td>
           	 						<td><?php esc_html_e( preg_replace( '/^php/', 'PHP ', $php_version ) ); ?></td>
        						</tr>
        						<tr>
            						<td><?php _e( 'IP Address', 'wp-cloud-server' ); ?></td>
            						<td><?php echo isset($server['lastaddress']) ? $server['lastaddress'] : 'Not Available'; ?></td>
        						</tr>
        						<tr>
            						<td><?php _e( 'Server ID', 'wp-cloud-server' ); ?></td>
            						<td><?php esc_html_e( $server['id'] ); ?></td>
        						</tr>
        						<tr>
            						<td><?php _e( 'Date Created', 'wp-cloud-server' ); ?></td>
            						<td><?php esc_html_e( date( 'd/M/Y', $server['datecreated']) ); ?></td>
        						</tr>
    						</tbody>
						</table>
					</div>
					
					<hr>
					<a class="uk-button uk-button-default uk-margin-small-right uk-align-right" href="#modal-group-2" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
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
							<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            				<a class="uk-button uk-button-default" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers&action=delete&manageserver=' . $server['name'] ), 'digitalocean_manage_nonce', '_wp_manage_nonce') );?>"><?php esc_attr_e( 'CONFIRM', 'wp-cloud-server' ) ?></a>
						</div>
        			</div>
    </div>
</div>
<?php if ( 'delete' !== $action ) { ?>
			<script>
				(function($){
					var modal = UIkit.modal("#managed-server-modal");
					modal.show();
				})(jQuery);
			</script>
<?php
			}
		}
	}
}

	update_option( 'sp_server_api', $servers );
	?>

	<table class="uk-table uk-table-striped">
    	<thead>
        	<tr>
            	<th class="col-name"><?php _e( 'Id', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Label', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Status', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Platform', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Cloud', 'wp-cloud-server' ); ?></th>
            	<th><?php _e( 'Region', 'wp-cloud-server' ); ?></th>
				<th><?php _e( 'Manage', 'wp-cloud-server' ); ?></th>
       	 </tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers['servers'] ) ) {
				foreach ( $servers['servers'] as $key => $server ) {
					foreach ( $server['apps'] as $key => $app ) {
					?>
        			<tr>
						<td><?php esc_html_e( $app['id'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $app['label'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $app['application'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $app['app_version'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $app['server_id'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $server['label'], 'wp-cloud-server' ); ?></td>
						<td><?php esc_html_e( $app['created_at'], 'wp-cloud-server' ); ?></td>
					
						<td>
							<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers&type=server&manageserver=' . $server['name'] ), 'digitalocean_manage_nonce', '_wp_manage_nonce') );?>"><?php esc_attr_e( 'Manage', 'wp-cloud-server' ) ?></a>
						</td>
        			</tr>
					<?php
						}
				}
			} else {
			?>
					<tr>
						<td colspan="7"><?php _e( 'No Application Information Available', 'wp-cloud-server' ) ?></td>
					</tr>
			<?php
			}
			?>
    	</tbody>
	</table>