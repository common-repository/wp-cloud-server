<?php
/**
 * WooCommerce Functions.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add the field to the checkout
 */
function digitalocean_custom_checkout_fields( $server, $checkout ) {

	if ( 'DigitalOcean' == $server['module'] ) {

		if ( 'userselected' == $server['region'] ) {

			echo '<div id="website-location"><h3>' . __('Website Location') . '</h3>';
	
			woocommerce_form_field( 'web_hosting_region', array(
				'type'          => 'select',
				'class'         => array('web-hosting-region form-row-wide'),
				'label'         => __('Select the location for your website?'),
				'required'    => true,
				'options'     => array(
						'ams' => __('Amsterdam'),
						'blr' => __('Bangalore'),
						'fra' => __('Frankfurt'),
						'lon' => __('London'),
						'nyc' => __('New York'),
						'sfo' => __('San Francisco'),
						'sgp' => __('Singapore'),
						'tor' => __('Toronto'),
				),
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
add_action( 'wpcs_wc_custom_checkout_fields', 'digitalocean_custom_checkout_fields',10, 2 );

/**
 * Process the checkout
 */
function digitalocean_custom_checkout_field_process( $server ) {

	if ( '[Customer Input]' == $server['host_name'] ) {

    	// Check if set, if its not set add an error.
    	if ( ! $_POST['server_hostname'] ) {
			wc_add_notice( __( 'Please enter a valid hostname for your server.' ), 'error' );
		}
	}
}
add_action('wpcs_wc_custom_checkout_field_process', 'digitalocean_custom_checkout_field_process');

/**
 * Retrieve 
 */
function wpcs_wc_product_details() {

	$order =  WC()->cart->get_cart_contents();
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		   $product_id = $cart_item['product_id'];
	}

	// Retrieve hosting plan details
	$product = array(
				'id'		=>	$product_id,
				'module'	=>	get_post_meta( $product_id, 'custom_field1', true ),
				'template'	=>	get_post_meta( $product_id, 'custom_field2', true ),
				'enabled'	=>	get_post_meta( $product_id, '_wpcs_enable_hosting', true ),
	);

	// If product not a hosting plan or no module provided then exit
	if ( ( $product['enabled'] == false ) || ( 'No Module' == $product['module'] ) ) {
		return false;
	}

	// Retrieve the module list
	$module_data = get_option( 'wpcs_module_list' );

	// Retrieve the correct template data
	foreach ( $module_data[ $product['module'] ]['templates'] as $server ) {
		if ( $product['template'] == $server['name'] ) {
			
			$server_data = array(
				"id"			=>	$product['id'],
				"name"			=>	$server['name'],
				"host_name"		=>	isset( $server['host_name'] ) ? $server['host_name'] : '',
				"root_domain"	=>	isset( $server['root_domain'] ) ? $server['root_domain'] : '',
				"region"		=>	$server['region'],
				"size"			=>	$server['size'],
				"image"			=> 	$server['image'],
				"module"		=>	$product['module'],
				"template"		=>	$product['template'],
				"enabled"		=>	$product['enabled'],
			);
		}
	}

	if ( !isset( $server_data ) ) {
		foreach ( $module_data[ $product['module'] ]['servers'] as $server ) {
			if ( $product['template'] == $server['name'] ) {
				
				$server_data = array(
					"id"			=>	$product['module'],
					"name"			=>	$server['name'],
					"host_name"		=>	isset( $server['host_name'] ) ? $server['host_name'] : '',
					"region"		=>	$server['region'],
					"size"			=>	$server['size'],
					"image"			=> 	$server['image'],
					"module"		=>	$product['module'],
					"template"		=>	$product['template'],
					"enabled"		=>	$product['enabled'],
				);
			}
		}
	}

	return $server_data;
}