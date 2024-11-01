<?php
/**
 * Display servers for the Digitalocean Module
 *
 * @link       	https://designedforpixels.com
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$servers = get_option( 'wpcs_digitalocean_api_data' );

if ( !isset( $servers['droplets'] ) ) {
	$servers = call_user_func( "wpcs_digitalocean_cloud_server_api", $module_name, 'droplets', null, false, 900, 'GET', false );
	update_option( 'wpcs_digitalocean_api_droplet_data', $servers );
}

$module_data	= get_option( 'wpcs_module_list' );
?>

<div class="uk-overflow-auto">
	<h2 class="uk-margin-remove-top uk-heading-divider"><?php _e( 'DigitalOcean Droplets', 'wp-cloud-server' ); ?></h2>
	<table class="uk-table uk-table-striped">
		<thead>
			<tr>
           		<th><?php esc_html_e( "Server Name", 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></th>
				<th><?php esc_html_e( 'Region', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'vCPUs', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Memory', 'wp-cloud-server' ); ?></th>
           	 	<th><?php esc_html_e( 'SSD', 'wp-cloud-server' ); ?></th>
            	<th><?php esc_html_e( 'Image', 'wp-cloud-server' ); ?></th>
				<th class="uk-table-shrink"><?php esc_html_e( 'Manage', 'wp-cloud-server' ); ?></th>
        	</tr>
    	</thead>
    	<tbody>
			<?php
			if ( !empty( $servers['droplets'] ) ) {
				foreach ( $servers['droplets'] as $key => $server ) {
				?>
        			<tr>
            			<td><?php echo $server['name']; ?></td>
						<td><?php echo ucfirst($server['status']); ?></td>
						<td><?php echo $server['region']['name']; ?></td>
						<td><?php echo $server['vcpus']; ?></td>
						<?php $server_memory = wpcs_mb_to_gb( $server['memory'] ); ?>
						<td><?php echo "{$server_memory}GB"; ?></td>
						<td><?php echo "{$server['disk']}GB"; ?></td>
						<?php $value = "{$server['image']['distribution']} {$server['image']['name']}";?>
						<td><?php echo $value; ?></td>
						<td><a class="uk-link" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle>Manage</a></td>
        			</tr>
				<?php
				}
			} else {
			?>
					<tr>
						<td colspan="8"><?php esc_html_e( 'No Server Information Available', 'wp-cloud-server' ) ?></td>
					</tr>
			<?php
			}
			?>
    	</tbody>
	</table>
</div>

<?php
if ( !empty( $servers['droplets'] ) ) {
	foreach ( $servers['droplets'] as $key => $server ) {
	?>
	<div id="managed-server-modal-<?php echo $server['id']; ?>" uk-modal>
    	<div class="server-modal uk-modal-dialog uk-modal-body">
					        <button class="uk-modal-close-default" type="button" uk-close></button>
        			        <h2><?php esc_html_e( 'Manage Server: ', 'wp-cloud-server' ); ?><span style="color: #A78BFA;"><?php echo "{$server['name']}"; ?></span></h2>
					        <hr class="clear">
					        <div class="server-info uk-modal-body" uk-overflow-auto>
								<div style="background-color: #f9f9f9; border: 1px solid #e8e8e8; margin-bottom: 10px; padding: 25px 10px;" class="uk-border-rounded">
									<div uk-grid>
  										<div class="uk-width-1-5@m">
     										<ul class="uk-tab-left" data-uk-tab="connect: #comp-tab-left-<?php echo $server['id']; ?>;">
		 										<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( isset($menu['modal_menu_items']) ) {
		 												foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
                    											<li><a href="#"><?php echo $menu_item; ?></a></li>
        														<?php 
															}
														}
													}
												}
												?>
     										</ul>
  										</div>
  										<div class="uk-width-4-5@m">
                							<ul id="comp-tab-left-<?php echo $server['id']; ?>" class="uk-switcher">			
												<?php
												foreach ( $page_content[ $page_id ]['content'] as $menus => $menu ) {
													if ( isset($menu['modal_menu_items']) ) {
														foreach ( $menu['modal_menu_items'] as $menu_id => $menu_item ) { 
		 													if (  'true' == $menu['modal_menu_active'][$menu_id]) {
		 														?>
																<li><div style="height:600px;" class="uk-overflow-auto"><?php do_action( "wpcs_digitalocean_{$menu['modal_menu_action'][$menu_id]}_content", $server ); ?></div></li>
        														<?php 
															}
														}
													}
												}
												?>
                							</ul>
  										</div>
									</div>
								</div>
							</div>
					        <hr>
							<a class="uk-button uk-button-danger uk-align-left uk-margin-remove-right uk-margin-remove-bottom" href="#delete-server-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'DELETE', 'wp-cloud-server' ) ?></a>
							<div class="uk-margin-right-small uk-align-right uk-margin-remove-bottom" uk-margin>
							<?php
							if ( 'active' == $server['status'] ) {
								$actions = array( 
											'Reboot'		=> 	'reboot', 
											'Power Cycle'	=>	'power_cycle',
											'Power Off'		=>	'power_off',
											);
							
								foreach ( $actions as $key => $action ) {
									?>
									<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
										<input type="hidden" name="action" value="handle_digitalocean_server_action">
										<input type="hidden" name="wpcs_digitalocean_server_action" value="<?php echo $action;?>">
										<input type="hidden" name="wpcs_digitalocean_server_id" value="<?php echo $server['id'];?>">
										<?php wp_nonce_field( 'handle_digitalocean_server_action_nonce', 'wpcs_handle_digitalocean_server_action_nonce' ); ?>
										
            							<?php wpcs_submit_button( $key, 'secondary', 'server_action', false, null, 'uk-margin-right' ); ?>
										
									</form>
									<?php
								}
							} else { ?>
									<form class="uk-margin-remove-bottom uk-align-right uk-margin-small-left" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
										<input type="hidden" name="action" value="handle_digitalocean_server_action">
										<input type="hidden" name="wpcs_digitalocean_server_action" value="power_on">
										<input type="hidden" name="wpcs_digitalocean_server_id" value="<?php echo $server['id'];?>">
										<?php wp_nonce_field( 'handle_digitalocean_server_action_nonce', 'wpcs_handle_digitalocean_server_action_nonce' ); ?>
										
            								<?php wpcs_submit_button( 'Power On', 'secondary', 'server_action', false ); ?>
										
									</form>
							<?php } ?>
						</div>
					        
    			        </div>
			        </div>
					<div id="delete-server-<?php echo $server['id']; ?>" uk-modal>
    			<div class="server-modal uk-modal-dialog uk-modal-body">
        			<div class="uk-modal-body">
						<p class="uk-text-lead"><?php esc_html_e( "Take care! This will delete '{$server['name']}' from your DigitalOcean account! It cannot be reversed!", 'wp-cloud-server' ); ?></p>
            			<p><?php esc_html_e( "Please confirm by clicking the 'CONFIRM' button below!", 'wp-cloud-server' ); ?></p>
        			</div>
					<div class="uk-modal-footer uk-text-right">
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
							<input type="hidden" name="action" value="handle_digitalocean_server_action">
							<input type="hidden" name="wpcs_digitalocean_server_action" value="delete">
							<input type="hidden" name="wpcs_digitalocean_server_id" value="<?php echo $server['id'];?>">
							<?php wp_nonce_field( 'handle_digitalocean_server_action_nonce', 'wpcs_handle_digitalocean_server_action_nonce' ); ?>
							<div class="uk-button-group uk-margin-remove-bottom">
								<a class="uk-button uk-button-default uk-margin-small-right" href="#managed-server-modal-<?php echo $server['id']; ?>" uk-toggle><?php esc_attr_e( 'CANCEL', 'wp-cloud-server' ) ?></a>
            					<?php wpcs_submit_button( 'Confirm Delete', 'danger', 'delete_server', false ); ?>
							</div>
						</form>
					</div>
    </div>
</div>

<?php		
	}
}