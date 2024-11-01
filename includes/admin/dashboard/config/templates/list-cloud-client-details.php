<?php

/**
 * Provide a admin area apps view for the serverpilot module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$clients_data	= get_option( 'wpcs_cloud_server_client_info', array() );

?>
<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'Client Details', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'User ID', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Username', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Plan Name', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Server', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( !empty( $clients_data['DigitalOcean'] ) ) {
				foreach ( $clients_data['DigitalOcean'] as $key => $client_data ) {
					?>
					<tr>
						<td><?php echo $client_data['user_id']; ?></td>
						<td><?php echo $client_data['full_name']; ?></td>
						<td><?php echo $client_data['nickname']; ?></td>
						<td><?php echo $client_data['plan_name']; ?></td>
						<td><?php echo ( isset( $client_data['fqdn'] ) ) ? $client_data['fqdn'] : $client_data['host_name']; ?></td>
						<td><a class="uk-link" href="#client-details-modal-<?php echo $client_data['host_name']; ?>" uk-toggle>Manage</a></td>
					</tr>
					<?php
				}
			} else {
				?>
					<tr>
						<td colspan="6"><?php esc_html_e( 'No Client Details Available', 'wp-cloud-server' ); ?></td>
					</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
<?php
if ( !empty( $clients_data['DigitalOcean'] ) ) {
	foreach ( $clients_data['DigitalOcean'] as $key => $client ) {
		?>
		<div id="client-details-modal-<?php echo $client['host_name']; ?>" uk-modal>
			<div class="server-modal uk-modal-dialog uk-modal-body">
				<button class="uk-modal-close-default" type="button" uk-close></button>
				<h2><?php esc_html_e( 'Manage Client: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo "{$client['first_name']} {$client['last_name']}"; ?></span></h2>
				<hr class="clear">
				<div class="server-info uk-modal-body" uk-overflow-auto>
					<table class="server-info uk-table uk-table-striped">
						<tbody>
							<tr>
								<td><?php esc_html_e( 'User ID', 'wp-cloud-server' ); ?></td>
								<td><?php echo "{$client['user_id']}"; ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></td>
								<td><?php echo "{$client['first_name']} {$client['last_name']}"; ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Plan Name', 'wp-cloud-server' ); ?></td>
								<td><?php echo $client['plan_name']; ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Hostname', 'wp-cloud-server' ); ?></td>
								<td><?php echo $client['host_name']; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<div>
					<a class="uk-button uk-button-danger uk-align-left uk-margin-remove-bottom" href="#delete-digitalocean-client-<?php echo $client['host_name']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
				</div>
			</div>
			<div id="delete-digitalocean-client-<?php echo $client['host_name']; ?>" uk-modal>
				<div class="server-modal uk-modal-dialog uk-modal-body">
					<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete the server from '{$client['first_name']} {$client['last_name']}' client information! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
						<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
					</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_digitalocean_client_action">
							<input type="hidden" name="wpcs_digitalocean_client_action" value="delete">
							<input type="hidden" name="wpcs_digitalocean_client_host_name" value="<?php echo $client['host_name'];?>">
							<input type="hidden" name="wpcs_digitalocean_client_user_id" value="<?php echo $client['user_id'];?>">
							<?php wp_nonce_field( 'handle_digitalocean_client_action_nonce', 'wpcs_handle_digitalocean_client_action_nonce' ); ?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#client-details-modal-<?php echo $client['host_name']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
								<?php wpcs_submit_button( 'Confirm Delete', 'danger', 'delete_server', false ); ?>
							</div>
						</form>
					</div>
			</div>
		</div>

	<?php
	}
}