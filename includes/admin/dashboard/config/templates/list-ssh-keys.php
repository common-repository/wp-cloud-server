<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$serverpilot_ssh_keys = get_option( 'wpcs_serverpilots_ssh_keys' );

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';
$del_ssh_key_nonce = isset( $_GET['_wp_del_ssh_key_nonce'] ) ? sanitize_text_field( $_GET['_wp_del_ssh_key_nonce'] ) : '';
	
$delsshkey = isset( $_GET['delsshkey'] ) ? sanitize_text_field( $_GET['delsshkey'] ) : '';

if ( !empty( $serverpilot_ssh_keys ) ) {
	foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) {
		if ( $delsshkey == $ssh_key['name'] ) {
			unset( $serverpilot_ssh_keys[$key] );
		}	
	}
}

update_option( 'wpcs_serverpilots_ssh_keys', $serverpilot_ssh_keys );
?>

<table class="uk-table uk-table-striped">
	<thead>
        <tr>
            <th class="uk-width-small"><?php echo $page['table']['heading1'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading2'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading3'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading4'] ; ?></th>
        </tr>
    </thead>
    <tbody>
		<?php
		if ( !empty( $serverpilot_ssh_keys ) ) {
			foreach ( $serverpilot_ssh_keys as $key => $ssh_key ) {
			?>
        		<tr>
            		<td><?php echo $ssh_key['name']; ?></td>
					<td><?php echo $ssh_key['fingerprint']; ?></td>
					<td><div style="width: 300px;" class="uk-text-truncate"><?php echo $ssh_key['public_key']; ?></div></td>
					<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-digitalocean&type=sshkey&delsshkey=' . $ssh_key['name'] . '&_wpnonce=' . $nonce . '' ), 'del_ssh_key_nonce', '_wp_del_ssh_key_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
        		</tr>
			<?php
			}
		} else {
			?>
				<tr>
					<td colspan="9"><?php esc_html_e( 'No SSH Key Information Available', 'wp-cloud-server' ) ?></td>
				</tr>
			<?php
		}
		?>
    </tbody>
</table>