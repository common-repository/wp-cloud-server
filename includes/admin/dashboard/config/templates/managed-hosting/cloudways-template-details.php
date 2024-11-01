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

$del_tmp_nonce		= isset( $_GET['_wp_del_tmp_nonce'] ) ? sanitize_text_field( $_GET['_wp_del_tmp_nonce'] ) : '';
$module_data		= get_option( 'wpcs_module_list' );
	
$deltemplate		= isset( $_GET['deltemplate'] ) ? sanitize_text_field( $_GET['deltemplate'] ) : '';
$completed_tasks	= get_option('wpcs_tasks_completed', array());

if ( !empty( $module_data['Cloudways']['templates'] ) ) {
	foreach ( $module_data['Cloudways']['templates'] as $key => $template ) {
		if ( $deltemplate == $template['app_label'] ) {
			unset( $module_data['Cloudways']['templates'][$key] );
			$completed_tasks[]=$deltemplate;
		}	
	}
}
update_option('wpcs_tasks_completed', $completed_tasks);
update_option( 'wpcs_module_list', $module_data );
?>

<table class="uk-table uk-table-striped">
    <thead>
        <tr>
			<th class="uk-width-small"><?php esc_html_e( 'Name', 'wp-cloud-server' ); ?></th>
			<th class="uk-table-shrink"><?php esc_html_e( 'Provider', 'wp-cloud-server' ); ?></th>
			<th class="uk-table-shrink"><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></th>
           	<th class="uk-width-small"><?php esc_html_e( 'Size', 'wp-cloud-server' ); ?></th>
            <th class="uk-width-small"><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></th>
			<th class="uk-table-shrink"><?php esc_html_e( 'Plan', 'wp-cloud-server' ); ?></th>
			<!-- <th class="uk-table-shrink"><?php esc_html_e( 'AutoSSL', 'wp-cloud-server' ); ?></th> -->
			<th class="uk-width-small"><?php esc_html_e( 'SSH Key', 'wp-cloud-server' ); ?></th>
			<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
        </tr>
    </thead>
    <tbody>
		<?php
		if ( ! empty( $module_data['Cloudways']['templates'] ) ) { 
			foreach ( $module_data['Cloudways']['templates'] as $template ) {
			?>
        		<tr>
					<td><?php echo $template['name']; ?></td>
					<td><?php echo $template['module']; ?></td>
					<?php $region = ( $template['region_name'] == 'userselected' ) ? '[Customer Input]' : $template['region_name']; ?>
            		<td><?php echo $region; ?></td>
					<?php
					$change	 = array(".00 ", " BW", ",");
					$replace = array("", "", ",");
					$size = str_replace($change, $replace, $template['size_name']);
					?>
					<td><?php echo $size; ?></td>
					<td><?php echo $template['image_name']; ?></td>
					<?php $plan = ( $template['plan'] == 'first_class' ) ? 'First Class' : ucfirst($template['plan']); ?>
					<td><?php echo $plan; ?></td>
					<?php $autossl = ( $template['autossl'] == '1' ) ? 'Enabled' : 'Disabled' ; ?>
					<!-- <td><?php echo $autossl; ?></td> -->
					<?php $ssh_key = ( $template['ssh_key'] == 'no-ssh-key' ) ? '[Password Only]' : $template['ssh_key']; ?>
					<td><?php echo $ssh_key; ?></td>
					<td>
						<?php $url = esc_url( wp_nonce_url( self_admin_url( 'admin.php?page=wp-cloud-server-managed-servers&deltemplate=' . $template['name'] .'&type=template' ), 'del_templates_nonce', '_wp_del_tmp_nonce') );?>
						<a href="<?php echo $url;?>"><?php esc_attr_e( 'Delete', 'wp-cloud-server' ); ?></a>
					</td>
        		</tr>
			<?php
			}
		} else {
		?>
				<tr>
					<td colspan="8"><?php esc_html_e( 'No Templates Available', 'wp-cloud-server' ); ?></td>
				</tr>
				<?php
		}
		?>
    </tbody>
</table>