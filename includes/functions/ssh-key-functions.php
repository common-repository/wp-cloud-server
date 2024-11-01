<?php
/**
 * WP Cloud Server - SSH Key Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create SSH Key.
*
* @since    3.0.6
*/
function wpcs_create_ssh_key( $tabs_content, $page_content, $page_id ) {

	$debug_enabled			= get_option( 'wpcs_enable_debug_mode' );
	$sp_response			= '';
	$server_script			= '';
	$debug_data['api']		= 'no';
	$ssh_keys				= array();
	?>

	<div class="content">
		<form method="post" action="options.php">
			<?php 
			settings_fields( 'wpcs_ssh_key' );
			wpcs_do_settings_sections( 'wpcs_ssh_key' );
			wpcs_submit_button( 'Save SSH Key', 'secondary', 'add-ssh-key' );
			?>
		</form>
	</div>

	<?php

	if ( get_option( 'wpcs_ssh_key_name' ) ) {

		$ssh_key_name		= get_option( 'wpcs_ssh_key_name' );	
		$ssh_key_value		= get_option( 'wpcs_ssh_key' );
		
		// Set-up the data for the new Droplet
		$ssh_key_data = array(
			"name"			=>  $ssh_key_name,
			"public_key"	=>  $ssh_key_value, 
		);

		// Retrieve the Active Module List
		$ssh_keys			= get_option( 'wpcs_serverpilots_ssh_keys' );
		$content			= explode(' ', $ssh_key_data['public_key'], 3);
		$fingerprint		= join(':', str_split(md5(base64_decode($content[1])), 2)) . "\n\n";
			
		$ssh_key_data['fingerprint'] = $fingerprint;
			
		// Save the VPS Template for use with a Plan
		$ssh_keys[ $ssh_key_data['name'] ] = $ssh_key_data;
			
		update_option( 'wpcs_serverpilots_ssh_keys', $ssh_keys );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_ssh_key_name' );
	delete_option( 'wpcs_ssh_key' );

}

/**
*  List SSH Keys
*
*  @since  3.0.6
*/
function wpcs_list_ssh_keys( $tabs_content, $page_content, $page_id, $module ) {

	foreach ( $page_content["wp-cloud-servers-{$module}"]['content'] as $key => $page_list ) {
		if ( 'create-ssh-key' == $page_list['id'] ) {
			$page = $page_list;
		}
	}
	
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
					<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( "admin.php?page=wp-cloud-servers-{$module}&type=sshkey&delsshkey=" . $ssh_key['name'] . "&_wpnonce=" . $nonce . '' ), 'del_ssh_key_nonce', '_wp_del_ssh_key_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
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

	<?php
}