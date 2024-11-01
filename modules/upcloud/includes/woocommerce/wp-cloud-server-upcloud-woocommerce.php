<?php
/**
 * WooCommerce Functions.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add the field to the checkout
 */
function upcloud_custom_checkout_fields( $server, $checkout ) {

	if ( 'UpCloud' == $server['module'] ) {

		if ( 'userselected' == $server['region'] ) {

			echo '<div id="website-location"><h3>' . __('Website Location') . '</h3>';
	
			woocommerce_form_field( 'web_hosting_region', array(
				'type'          => 'select',
				'class'         => array('web-hosting-region form-row-wide'),
				'label'         => __('Select the location for your website?'),
				'required'    => true,
				'options'     => wpcs_upcloud_regions_array(),
				'default' => 'lon', 
				$checkout->get_value( 'web_hosting_region' ))
			);

			echo '</div>';

		}

		if ( '[Customer Input]' == $server['host_name'] ) {

			echo '<div id="server-host-name"><h3>' . __('Server Host Name') . '</h3>';

			woocommerce_form_field( 'server_hostname', array(
				'type'          => 'text',
				'class'         => array('server-host-name form-row-wide'),
				'label'         => __('Please enter a hostname for your new server?'),
				'required'    	=> true,
				'placeholder'   => __('host-name'),
			), $checkout->get_value( 'server_hostname' ));

			echo '</div>';

		}
	}
}
add_action( 'wpcs_wc_custom_checkout_fields', 'upcloud_custom_checkout_fields',10, 2 );

/**
 * Process the checkout
 */
function upcloud_custom_checkout_field_process( $server ) {

	if ( 'UpCloud' == $server['module'] ) {
		if ( '[Customer Input]' == $server['host_name'] ) {
			if ( ! $_POST['server_hostname'] ) {
				wc_add_notice( __( 'Please enter a valid hostname for your server.' ), 'error' );
			}
		}
	}
}
add_action('wpcs_wc_custom_checkout_field_process', 'upcloud_custom_checkout_field_process');

/**
 * Update the order meta with field value
 */
function upcloud_custom_checkout_field_update_order_meta( $order_id ) {

    return;

}
add_action( 'wpcs_wc_custom_checkout_field_update_order_meta', 'upcloud_custom_checkout_field_update_order_meta' );