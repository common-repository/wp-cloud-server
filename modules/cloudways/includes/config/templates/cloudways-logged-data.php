<?php

function wpcs_cloudways_logged_data_template ( $tabs_content, $page_content, $page_id ) {
	
	if ( 'cloudways-logged-data' !== $tabs_content ) {
		return;
	}

$logged_data = get_option( 'wpcs_cloudways_logged_data' );

?>
											
<table class="uk-table uk-table-striped">
    <thead>
    	<tr>
        	<th class="col-date"><?php esc_html_e( 'Date', 'wp-cloud-server' ); ?></th>
        	<th class="col-module"><?php esc_html_e( 'Module', 'wp-cloud-server' ); ?></th>
       		<th class="col-status"><?php esc_html_e( 'Status', 'wp-cloud-server' ); ?></th>
			<th class="col-desc"><?php esc_html_e( 'Description', 'wp-cloud-server' ); ?></th>
    	</tr>
    </thead>
    <tbody>
		<?php
		if ( !empty( $logged_data ) ) {
			$formatted_data = array_reverse( $logged_data, true );
			foreach ( $formatted_data as $logged_event ) {	
			?>
    			<tr>
        			<td><?php echo $logged_event['date']; ?></td>
        			<td><?php echo $logged_event['event']; ?></td>
					<td><?php echo $logged_event['status']; ?></td>
					<td><?php echo $logged_event['description']; ?></td>
    			</tr>
			<?php
			}
		} else {
		?>
    			<tr>
        			<td colspan="4"><?php esc_html_e( 'Sorry! No Logged Data Currently Available.', 'wp-cloud-server' ); ?></td>
    			</tr>								
		<?php
		}
		?>								
    </tbody>
</table>
<?php
}

add_action( 'wpcs_control_panel_tab_content', 'wpcs_cloudways_logged_data_template', 10, 3 );