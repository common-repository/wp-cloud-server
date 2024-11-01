<?php
/**
 * WooCommerce Functions.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add the field to the checkout
 */
function serverpilot_custom_checkout_fields( $server, $checkout ) {

	if ( 'ServerPilot' == $server['module'] ) {

		if ( 'userselected' == $server['region'] ) {

			echo '<div id="website-location"><h3>' . __('Website Location') . '</h3>';
	
			woocommerce_form_field( 'web_hosting_region', array(
				'type'          => 'select',
				'class'         => array('web-hosting-region form-row-wide'),
				'label'         => __('Select the location for your website?'),
				'required'    => true,
				'options'     => wpcs_serverpilot_regions_array(),
				'default' => 'lon', 
				$checkout->get_value( 'web_hosting_region' ))
			);

			echo '</div>';

		}

		echo '<div id="site-label"><h3>' . __('Site Label') . '</h3>';
	
		woocommerce_form_field( 'site_label', array(
			'type'          => 'text',
			'class'         => array('server-host-name form-row-wide'),
			'label'         => __('Please enter a site label for your new website?'),
			'required'    	=> true,
			'placeholder'   => __('site-label'),
			), $checkout->get_value( 'site_label' ));

		echo '</div>';

		echo '<div id="server-host-name"><h3>' . __('Domain Name') . '</h3>';

		woocommerce_form_field( 'domain_name', array(
			'type'          => 'text',
			'class'         => array('domain-name form-row-wide'),
			'label'         => __('Please enter a domain name for your new website?'),
			'required'    	=> true,
			'placeholder'   => __('example.com'),
		), $checkout->get_value( 'domain_name' ));

		echo '</div>';

		echo '<div id="admin-password"><h3>' . __('Admin Password') . '</h3>';
	
		woocommerce_form_field( 'admin_password', array(
			'type'          => 'text',
			'class'         => array('admin-password form-row-wide'),
			'label'         => __('Please enter an admin password for your new website?'),
			'required'    	=> true,
			'placeholder'   => __('**********'),
			), $checkout->get_value( 'admin_password' ));

		echo '</div>';

	}
}
add_action( 'wpcs_wc_custom_checkout_fields', 'serverpilot_custom_checkout_fields',10, 2 );

/**
 * Process the checkout
 */
function serverpilot_custom_checkout_field_process( $server ) {
	
	if ( 'ServerPilot' == $server['module'] ) {

    	// Check if set, if its not set add an error.
    	if ( ! $_POST['site_label'] ) {
			wc_add_notice( __( 'Please enter a site label for your website.' ), 'error' );
		}

    	if ( ! $_POST['domain_name'] ) {
			wc_add_notice( __( 'Please enter a domain name for your website.' ), 'error' );
		}

    	if ( ! $_POST['admin_password'] ) {
			wc_add_notice( __( 'Please enter an admin password for your website.' ), 'error' );
		}
	}
}
add_action('wpcs_wc_custom_checkout_field_process', 'serverpilot_custom_checkout_field_process');

/**
 * Update the order meta with field value
 */
function serverpilot_custom_checkout_field_update_order_meta( $order_id ) {

    if ( ! empty( $_POST['site_label'] ) ) {
        update_post_meta( $order_id, 'site_label', sanitize_text_field( $_POST['site_label'] ) );
	}
	
    if ( ! empty( $_POST['domain_name'] ) ) {
        update_post_meta( $order_id, 'domain_name', sanitize_text_field( $_POST['domain_name'] ) );
	}

    if ( ! empty( $_POST['admin_password'] ) ) {
        update_post_meta( $order_id, 'admin_password', sanitize_text_field( $_POST['admin_password'] ) );
	}

}
add_action( 'wpcs_wc_custom_checkout_field_update_order_meta', 'serverpilot_custom_checkout_field_update_order_meta' );