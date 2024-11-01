<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$modules		= get_option( 'wpcs_module_list' );
$hide_inactive	= get_option( 'wpcs_hide_inactive_modules', false );
$link_message	= ( $hide_inactive ) ? 'Show Inactive Modules' : 'Hide Inactive Modules';
$new_action		= ( $hide_inactive ) ? 'false' : 'true';
?>
											
<table id="test-data" class="uk-table uk-table-striped">
    <thead>
    	<tr>
        	<th class="uk-table-small"><?php esc_html_e( 'Module', 'wp-cloud-server' ); ?></th>
       		<th class="uk-table-small"><?php esc_html_e( 'Description', 'wp-cloud-server' ); ?></th>
        	<th class="uk-table-small"><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></th>
			<th class="uk-table-small"><?php esc_html_e( 'API Status', 'wp-cloud-server' ); ?></th>
			<th style="width: 130px;"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
    	</tr>
    </thead>
    <tbody>
		<?php 
			if ( ! empty( $modules ) ) {
				$row_id = 0;
				$module_count = wpcs_active_modules();
				if ( !$hide_inactive || ( $hide_inactive && ( $module_count >= 1 ) ) ) {
					foreach ( $modules as $key => $module ) {
						if ( !$hide_inactive || ( $hide_inactive && ( 'active' == $module['status'] ) ) ) {
							$row_id++;
							?>
    						<tr>
        						<td><?php esc_html_e( $key, 'wp-cloud-server' ); ?></td>
								<td><?php esc_html_e( $module['module_desc'], 'wp-cloud-server' ); ?></td>
        						<td><?php self::wpcs_admin_manage_module( 'status', $status, $key, $module_name, $settings_page ); ?></td>
								<td><?php self::wpcs_admin_manage_module( 'api_connected', $status, $key, $module_name, $settings_page ); ?></td>
								<td><?php self::wpcs_admin_manage_module( 'settings', $status, $key, $module_name, $settings_page ); ?></td>
    						</tr>
							<?php
						}
					}
				} else {
					?>
    				<tr>
					<td colspan="5"><?php esc_html_e( 'No Modules are Activated!', 'wp-cloud-server' ) ?></td>
    				</tr>
					<?php
				}
			}
		?>
    </tbody>
</table>
<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
	<input type="hidden" name="action" value="handle_hide_module_action">
	<input type="hidden" name="wpcs_hide_module_action" value="<?php echo $new_action ?>">
	<?php wp_nonce_field( 'handle_hide_module_action_nonce', 'wpcs_handle_hide_module_action_nonce' ); ?>
    <input style="padding-right: 10px; border: none; font-size: 12px; display: inline;" type="submit" name="delete_server" id="delete_server" class="uk-float-right uk-text-muted uk-button-link" value="<?php echo $link_message ?>">
</form>