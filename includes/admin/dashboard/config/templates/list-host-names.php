<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$host_names	= get_option( 'wpcs_host_names' );

$del_script_nonce	= isset( $_GET['_wp_host_name_nonce'] ) ? sanitize_text_field( $_GET['_wp_host_name_nonce'] ) : '';
$nonce				= isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : '';
$delhostname		= isset( $_GET['delhostname'] ) ? sanitize_text_field( $_GET['delhostname'] ) : '';
$type				= isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';

if ( !empty( $host_names ) && ( 'delete' == $type ) ) {
	foreach ( $host_names as $key => $host_name ) {
		if ( $delhostname == $host_name['label'] ) {
			unset( $host_names[$key] );
		}	
	}
}

update_option( 'wpcs_host_names', $host_names );

?>

<table class="uk-table uk-table-striped">
	<thead>
        <tr>
            <th class="uk-width-small"><?php echo $page['table']['heading1'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading2'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading3'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading4'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading5'] ; ?></th>
			<th class="uk-width-small"><?php echo $page['table']['heading6'] ; ?></th>
			<th class="uk-table-shrink"><?php echo $page['table']['heading7'] ; ?></th>
        </tr>
    </thead>
    <tbody>
		<?php
		if ( !empty( $host_names ) ) {
			foreach ( $host_names as $key => $host_name ) {
			?>
        		<tr>
            		<td><?php echo $host_name['label']; ?></td>
					<td><?php echo $host_name['hostname']; ?></td>
					<td><?php echo ( 'counter_suffix' == $host_name['suffix'] ) ? 'Integer Counter' : 'Not Applicable'; ?></td>
					<td><?php echo ( isset($host_name['protocol'] ) ) ? $host_name['protocol'] : ''; ?></td>
					<td><?php echo ( isset($host_name['domain'] ) ) ? $host_name['domain'] : ''; ?></td>
					<td><?php echo ( isset($host_name['port']) ) ? $host_name['port'] : ''; ?></td>
					<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-general-settings&type=delete&delhostname=' . $host_name['label'] . '&_wpnonce=' . $nonce . '' ), 'del_host_name_nonce', '_wp_del_host_name_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
        		</tr>
			<?php
			}
		} else {
			?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No Host Names Available', 'wp-cloud-server' ) ?></td>
				</tr>
			<?php
		}
		?>
    </tbody>
</table>