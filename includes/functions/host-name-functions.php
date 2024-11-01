<?php
/**
 * WP Cloud Server - Host Name Functions
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	3.0.6
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Create Host Name.
*
* @since    3.0.6
*/
function wpcs_create_host_name( $tabs_content, $page_content, $page_id ) {
	?>
		<div class="content">
	<form method="post" action="options.php">
		<?php 
		settings_fields( 'wpcs_edd_checkout_settings' );
		wpcs_do_settings_sections( 'wpcs_edd_checkout_settings' );
		wpcs_submit_button( __( 'Save Settings', 'wp-cloud-server' ), 'secondary', 'submit' );
		?>
	</form>
</div>

	<?php

	if ( get_option( 'wpcs_edd_checkout_settings_hostname_label' ) ) {

		$host_name_label		= get_option( 'wpcs_edd_checkout_settings_hostname_label' );
		$host_name				= get_option( 'wpcs_edd_checkout_settings_hostname' );
		$host_name_domain		= get_option( 'wpcs_edd_checkout_settings_hostname_domain' );
		$host_name_suffix		= get_option( 'wpcs_edd_checkout_settings_hostname_suffix' );
		$host_name_protocol		= get_option( 'wpcs_edd_checkout_settings_hostname_protocol' );
		$host_name_port			= get_option( 'wpcs_edd_checkout_settings_hostname_port' );
		
		// Set-up the data for the new Droplet
		$host_name_data = array(
			"label"				=> $host_name_label,
			"hostname"			=> $host_name,
			"domain"			=> $host_name_domain,
			"suffix"			=> $host_name_suffix,
			"protocol"			=> $host_name_protocol,
			"port"				=> $host_name_port,
			"count"				=> 0,
		);

		// Retrieve the Active Module List
		$host_names				= get_option( 'wpcs_host_names' );
			
		// Save the VPS Template for use with a Plan
		$host_names[ $host_name_data['label'] ] = $host_name_data;
			
		update_option( 'wpcs_host_names', $host_names );

	}

	// Delete the saved settings ready for next new server
	delete_option( 'wpcs_edd_checkout_settings_hostname_label' );
	delete_option( 'wpcs_edd_checkout_settings_hostname' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_domain' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_suffix' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_protocol' );
	delete_option( 'wpcs_edd_checkout_settings_hostname_port' );
		
}
add_action( 'wpcs_create_host_name_content', 'wpcs_create_host_name' );

/**
*  List Host Names
*
*  @since  3.0.6
*/
function wpcs_list_host_names( $tabs_content, $page_content, $page_id, $module ) {
	
	foreach ( $page_content["wp-cloud-servers-{$module}"]['content'] as $key => $page_list ) {
		if ( 'create-host-name' == $page_list['id'] ) {
			$page = $page_list;
		}
	}
	
	$host_names			= get_option( 'wpcs_host_names' );
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
					<td><a class="uk-link" href="<?php echo esc_url( wp_nonce_url( self_admin_url( "admin.php?page=wp-cloud-servers-{$module}&type=delete&delhostname=" . $host_name['label'] . '&_wpnonce=' . $nonce . '' ), 'del_host_name_nonce', '_wp_del_host_name_nonce') );?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ) ?></a></td>
        		</tr>
			<?php
			}
		} else {
			?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No Hostnames Available', 'wp-cloud-server' ) ?></td>
				</tr>
			<?php
		}
		?>
    </tbody>
</table>
	<?php
}
add_action( 'wpcs_list_host_names_content', 'wpcs_list_host_names' );