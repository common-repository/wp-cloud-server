<?php
/**
 * Plugin Name: 	WooCommerce Custom Product Type
 * Plugin URI:		http://jeroensormani.com
 * Description:		A simple demo plugin on how to add a custom product type.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a custom product tab.
 */
function custom_product_tabs( $tabs) {

	$tabs['web_hosting_plan'] = array(
		'label'		=> __( 'Web Hosting Plan', 'woocommerce' ),
		'target'	=> 'web_hosting_plan',
		'class'		=> array( 'show_if_web_hosting_plan', 'show_if_variable_web_hosting_plan'  ),
		'priority'	=> 12,
	);

	return $tabs;

}
add_filter( 'woocommerce_product_data_tabs', 'custom_product_tabs' );


/**
 * Contents of the rental options product tab.
 */
function rental_options_product_tab_content() {

	global $post;
	
	update_option( 'wpcs_edd_download_id', $post->ID );

	?><div id='web_hosting_plan' class='panel woocommerce_options_panel'><?php

		?><div class='options_group'><?php

			woocommerce_wp_checkbox( array(
				'id' 		=> '_wpcs_enable_hosting',
				'label' 	=> __( 'Enable Web Hosting Plan', 'woocommerce' ),
			) );

			woocommerce_wp_select(
				array(
					'id'          => 'custom_field1',
					'label'       => __( 'Select Module', 'woocommerce' ),
					'options'     => wpcs_woocommerce_update_modules(),
					'desc_tip'    => 'true',
					'description' => __( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'woocommerce' ),
				)
			);
	
			woocommerce_wp_select(
				array(
					'id'          => 'custom_field2',
					'label'       => __( 'Select Server/Template', 'woocommerce' ),
					'options'     => wpcs_woocommerce_update_servers(),
					'desc_tip'    => 'true',
					'description' => __( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'woocommerce' ),
				)
			);

		?></div>

	</div><?php
}
add_action( 'woocommerce_product_data_panels', 'rental_options_product_tab_content' );


/**
 * Save the custom fields.
 */
function save_web_hosting_plan_option_field( $post_id ) {

	if ( isset( $_POST['_wpcs_enable_hosting'] ) ) :
		update_post_meta( $post_id, '_wpcs_enable_hosting', $_POST['_wpcs_enable_hosting'] );
	endif;

	if ( isset( $_POST['custom_field1'] ) ) :
		update_post_meta( $post_id, 'custom_field1', $_POST['custom_field1'] );
	endif;
	
	if ( isset( $_POST['custom_field2'] ) ) :
		update_post_meta( $post_id, 'custom_field2', $_POST['custom_field2'] );
	endif;

}
add_action( 'woocommerce_process_product_meta', 'save_web_hosting_plan_option_field'  );

/**
 * Hide Attributes data panel.
 */
function hide_attributes_data_panel( $tabs) {

	$tabs['attribute']['class'][] = 'hide_if_web_hosting_plan hide_if_variable_web_hosting_plan';

	return $tabs;

}
//add_filter( 'woocommerce_product_data_tabs', 'hide_attributes_data_panel' );

/**
 *  WPCS Purchase Complete Create Service
 * 
 * 	Hooks edd_complete_purchase action
 *
 *  @since 1.0.0
 */
function wpcs_wc_purchase_complete_create_service( $order_id ) {
	
	$order	= wc_get_order( $order_id );
    $user	= $order->get_user();
	
	// Retrieve Order Items
	foreach ( $order->get_items() as $item_id => $item ) {
   		$product_id	= $item->get_product_id();
   		$product	= $item->get_product();
   		$plan_name	= $item->get_name();
   		$allmeta	= $item->get_meta_data();
	}

	// Exit from Create Hosting Plan if NOT Enabled
	$wpcs_cloud_hosting_enabled = (boolean) get_post_meta( $product_id, '_wpcs_enable_hosting', true );

	if ( $wpcs_cloud_hosting_enabled == false ) {
		return;
	} else {
		$data['status'] = 'Exited';
		$data['order'] = $order;
		update_option( 'wpcs_wc_create_hosting_plan_data', $data );
	}

	// Retrieve hosting plan details for product
	$module_name 	= get_post_meta( $product_id, 'custom_field1', true );
	$server_name 	= get_post_meta( $product_id, 'custom_field2', true );
	$user_host_name	= get_post_meta( $order->id, 'server_hostname', true );
	$user_region	= get_post_meta( $order->id, 'web_hosting_region', true );

	// Retrieve website details
	$site_label		= get_post_meta( $order->id, 'site_label', true );
	$domain_name	= get_post_meta( $order->id, 'domain_name', true );
	$admin_user		= get_post_meta( $order->id, 'admin_user', true );
	$admin_pass		= get_post_meta( $order->id, 'admin_password', true );
		
	// Retrieve current user if logged in via EDD
	$user_id		= $order->get_user_id();
	$customer_id	= $order->get_customer_id();
		
	// Define the arguments for the create service action hook
	$data = array(
		'module_name'				=> 	$module_name,
		'plan_name'					=>	$plan_name,
		'server_name'				=> 	$server_name,
		'host_name'					=> 	( $user_host_name ) ? $user_host_name : '',
		'server_location'			=> 	( $user_region ) ? $user_region : '',
		'domain_name'				=> 	( $domain_name ) ? $domain_name : '',
		'site_label'				=> 	( $site_label ) ? $site_label : '',
		'site_url'					=> 	'',
		'site_name'					=> 	'',
		'site_desc'					=> 	'',
		'customer_id'				=>	$customer_id,
		'user_id'					=> 	$user_id,
		'user_first'				=> 	$order->get_billing_first_name(),
		'user_last'					=> 	$order->get_billing_last_name(),
		'user_email'				=> 	$order->get_billing_email(),
		'user_phone'				=> 	$order->get_billing_phone(),
		'user_login'				=> 	'',				
		'user_pass'					=> 	'',
		'user_name'					=> 	( $admin_user ) ? $admin_user : 'admin',
		'user_password'				=> 	( $admin_pass ) ? $admin_pass : '',
		'user_confirm_password'		=> 	'',
		'user_company'				=> 	$order->get_billing_company(),
		'product_id'				=> 	$product_id,
    	'order_items'				=> 	$order,
	);
			
	update_option( 'wpcs_wc_create_hosting_plan_data', $data );
		
	// Executes before the create service functionality
	// do_action( 'wpcs_before_purchase_complete_create_service', $data );

	// Executes the create service functionality depending on the module
	do_action( 'wpcs_wc_purchase_complete_create_service', $module_name, $data );
		
	// Executes after the create service functionality
	// do_action( 'wpcs_after_purchase_complete_create_service', $data );

}
add_action( 'woocommerce_order_status_completed', 'wpcs_wc_purchase_complete_create_service' );

/**
 * Add the field to the checkout
 */
function my_custom_checkout_field( $checkout ) {

	// Retrieve the current cart
	$product = wpcs_wc_product_details();

	if ( !$product ) {
		return;
	}
			
	do_action( 'wpcs_wc_custom_checkout_fields', $product, $checkout );

}
add_action( 'woocommerce_before_order_notes', 'my_custom_checkout_field' );

/**
 * Process the checkout
 */
function my_custom_checkout_field_process() {

	// Retrieve the current cart
	$product = wpcs_wc_product_details();

	if ( !$product ) {
		return;
	}

	do_action( 'wpcs_wc_custom_checkout_field_process', $product );
}
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

/**
 * Display field value on the order edit page
 */
function my_custom_checkout_field_display_admin_order_meta( $order ){
	
	foreach ( $order->get_items() as $item_id => $item ) {
   		$product = $item->get_product();
   		$name = $item->get_name();
	}
	
	echo '<div class="clearfix webhosting" >';
	echo '<h3>'.__('Web Hosting Plan').'</h3>';
	echo '<p><strong>'.__('Hosting Plan').': </strong>' . $name . '</p>';
    //echo '<p><strong>'.__('My Field').':</strong> ' . get_post_meta( $order->id, 'My Field', true ) . '</p>';
	echo '</div>';
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

/**
 * Update the order meta with field value
 */
function my_custom_checkout_field_update_order_meta( $order_id ) {

    if ( ! empty( $_POST['server_hostname'] ) ) {
        update_post_meta( $order_id, 'server_hostname', sanitize_text_field( $_POST['server_hostname'] ) );
	}
	
    if ( ! empty( $_POST['web_hosting_region'] ) ) {
        update_post_meta( $order_id, 'web_hosting_region', sanitize_text_field( $_POST['web_hosting_region'] ) );
	}
	
	do_action( 'wpcs_wc_custom_checkout_field_update_order_meta', $order_id );
}
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

/* To use: 
1. Add this snippet to your theme's functions.php file
2. Change the meta key names in the snippet
3. Create a custom field in the order post – e.g. key = "Tracking Code" value = abcdefg
4. When next updating the status, or during any other event which emails the user, they will see this field in their email
*/
add_filter('woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys');

function my_custom_order_meta_keys( $keys ) {
     $keys[] = 'Tracking Code'; // This will look for a custom field called 'Tracking Code' and add it to emails
     return $keys;
}

/**
 * Get product tax class options.
 *
 * @since 3.0.0
 * @return array
 */
function wpcs_woocommerce_update_modules() {
	
	$modules = get_option( 'wpcs_module_list' );
	$no_modules['No Modules'] = 'No Modules';

	if ( ! empty( $modules ) ) {
		foreach ( $modules as $key => $module ) {
			if ( 'active' == $module['status'] ) {
				$modules_list[$key] = $key;
			}
		}
	}
	return ( isset( $modules_list ) ) ? $modules_list : $no_modules;
}

/**
 * Get product tax class options.
 *
 * @since 3.0.0
 * @return array
 */
function wpcs_woocommerce_update_servers() {
	
	$modules = get_option( 'wpcs_module_list' );
	$modules['No Modules'] = 'No Modules';

	$modules_list['No Modules'] = 'No Modules';
		
	return $modules_list;
}