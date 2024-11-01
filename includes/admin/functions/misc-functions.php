<?php

/**
 * The Server Tools Functions.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Add link to settings on plugin list
 *
 *  @since 1.0.0
 *  @param array  $links links to show below plugin name
 */
function wpcs_add_settings_link_to_plugin_list( $links ) {

	if ( current_user_can( 'manage_options' ) ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wp-cloud-server-admin-menu' ) ) . '">' . esc_attr__( 'Modules', 'wp-cloud-server' ) . '</a>';
	}

	return $links;

}
add_filter( 'plugin_action_links_wp-cloud-server/wp-cloud-server.php', 'wpcs_add_settings_link_to_plugin_list' );

/**
 *  Add Plugin Website Link to Plugin Row Meta
 *
 *  @since 2.2.0
 *  @param array  $links links to show below plugin description
 */
function wpcs_plugin_row_meta( $links, $file ) {

	if ( strpos( $file, 'wp-cloud-server.php' ) !== false ) {
		$plugin_data = get_plugin_data( WPCS_PLUGIN_DIR . '/wp-cloud-server.php' );
		$new_links = array(
				'site' => '<a href="' . esc_url( $plugin_data["PluginURI"] ) . '" target="_blank">Visit plugin site</a>',
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}
add_filter( 'plugin_row_meta', 'wpcs_plugin_row_meta', 10, 2 );
		
/**
 *  Return the count of current active modules.
 *
 *  @return int  Count of active modules
 * 
 *  @since 1.0.0
 */
function wpcs_active_modules() {

	$module_list = get_option( 'wpcs_module_list' );
	$count = 0;
			
	foreach ( $module_list as $key => $module ) {
		if ( 'active' == $module_list[ $key ]['status'] ) {
        	$count++;	
		}
	}
			
	update_option( 'wpcs_module_count', $count );
				
	return $count;

}

/**
 *  Increment Active Module Count.
 *
 *  @since 1.0.0
 */
function wpcs_increment_module_count() {

	$module_count = get_option( 'wpcs_module_count' );
			
	++$module_count;
			
	update_option( 'wpcs_module_count', $module_count );

}
		
		
/**
 *  Decrement Active Module Count.
 *
 *  @since 1.0.0
 *  
*/
function wpcs_decrement_module_count() {

	$module_count = get_option( 'wpcs_module_count' );
			
	--$module_count;
			
	update_option( 'wpcs_module_count', $module_count );

}
		
		
/**
 *  Reset Module Count.
 *
 *  @since 1.0.0
 */
function wpcs_reset_count() {

	$module_count = 0;
			
	update_option( 'wpcs_module_count', $module_count );

}
		
/**
 *  Return Module Count.
 *
 *  @since 1.0.0
 * 
 *  @return integer  module count value
 */
function wpcs_module_count() {
	return get_option( 'wpcs_module_count' );
}

/**
 *  Sanitize Domain Name into Site Label.
 *
 *  @since 1.0.0
 * 
 *  @return string  new site label
 */
function wpcs_sanitize_site_label( $domain_name ) {
			
	$url 		= preg_replace( '/^https?:\/\//', '', $domain_name );
	$url 		= preg_replace( '/^www./', '', $url );
	$site_label = preg_replace( '/\./', '-', $url );

	return $site_label;

}
		
/**
 *  Sanitize Domain Name then strip http(s)://. 
 *
 *  @since 1.0.0
 * 
 *  @return array  array of sanitized user data
 */
function wpcs_sanitize_domain_strip_http( $url ) {
			
	$sanitize_url = esc_url( $url );

	$url_no_http = preg_replace( '/^https?:\/\//', '', $sanitize_url );

	return $url_no_http;

}

/**
 *  Sanitize User Data to remove passwords before displaying in debug panel.
 *
 *  @since 1.0.0
 * 
 *  @return array  array of sanitized user data
 */
function wpcs_sanitize_password_data( $user_data, $custom_key_array = array() ) {

	$default_key_array = array(
		'user_pass',
		'user_password',
		'user_confirm_password'				
	);

	$search_key_array = array_merge( $default_key_array, $custom_key_array );

	foreach ( $search_key_array as $key ) {
		if ( isset( $user_data[ $key ] ) ) {
        	$user_data[ $key ] = 'xxxxxx';	
		}
	}

	return $user_data;

}

/**
 *  Remove empty values from array
 *
 *  @since  1.0.0
 *  @param  array  $array original array
 *  @return array  array without empty values
 */
function wpcs_sanitize_callback_no_empty_array_values( $array = array() ) {

	if ( ! is_array( $array ) ) {
		return array();
	}

	return array_filter( $array );

}

/**
 *  Sanitize true setting
 *
 *  @since  1.0.0
 *  @param  boolean  $setting original setting
 *  @return boolean  checked value
 */
function wpcs_sanitize_true_setting( $setting ) {

	if ( $setting !== 'true' ) {
		return '';
	}

	return $setting;

}
    
/**
 *  Check Cloud Provider Exists
 *
 *  @since  1.3.0
 *  @return boolean  checked value
 */
function wpcs_check_cloud_provider_first( $module_name = null ) {

	$module_list = get_option( 'wpcs_module_list' );
        
    if ( is_array( $module_list ) ) {
		foreach ( $module_list as $key => $module ) {
			if ( ( 'cloud_provider' == $module_list[$key]['module_type'] ) && ( 'active' == $module_list[$key]['status'] ) ) {
				$api_check  = call_user_func("WP_Cloud_Server_{$key}_Settings::wpcs_{$key}_module_api_connected" );
				if ( $api_check ) {
					$cloud_status = true;
				}	
			}
		}
	}
		
	if ( !empty( $module_name ) ) {
		$module_api_check  = call_user_func("WP_Cloud_Server_{$module_name}_Settings::wpcs_{$module_name}_module_api_connected" );
		return ( $module_api_check && $cloud_status );
	} else {
		return $cloud_status;
	}

	return false;

}

/**
 *  Check Cloud Provider Exists
 *
 *  @since  1.3.0
 *  @return boolean  checked value
 */
function wpcs_check_cloud_provider( $module_name = null, $cloud_provider_name = null, $check_cloud_providers = true ) {
 
	$function_name = strtolower( str_replace( " ", "_", $module_name ) );
		
	$cloud_available = false;

	$module_available = false;

	$module_list = get_option( 'wpcs_module_list' );

	if ( is_array( $module_list ) ) {

		if ( empty( $cloud_provider_name ) && $check_cloud_providers ) {
			foreach ( $module_list as $key => $module ) {
				if ( ( 'cloud_provider' == $module_list[$key]['module_type'] ) && ( 'active' == $module_list[$key]['status'] ) ) {
					$api_check  = call_user_func("wpcs_{$key}_module_api_connected");
					//$api_check  = call_user_func("WP_Cloud_Server_{$key}_Settings::wpcs_{$key}_module_api_connected" );
					if ( $api_check ) {
						$cloud_available = true;
					}
				}
			}
		}

		if ( ! empty( $cloud_provider_name ) && $check_cloud_providers ) {
			if ( ( 'cloud_provider' == $module_list[$cloud_provider_name]['module_type'] ) && ( 'active' == $module_list[$cloud_provider_name]['status'] ) ) {
				$api_check  = call_user_func("wpcs_{$cloud_provider_name}_module_api_connected");
				//$api_check  = call_user_func("WP_Cloud_Server_{$cloud_provider_name}_Settings::wpcs_{$cloud_provider_name}_module_api_connected" );
				if ( $api_check ) {
					$cloud_available = true;
				}
			}
		}

		if ( ! empty( $module_name ) ) {
			if ( 'active' == $module_list[$module_name]['status'] )  {
				$api_check  = call_user_func("wpcs_{$function_name}_module_api_connected");
				//$api_check  = call_user_func("WP_Cloud_Server_{$function_name}_Settings::wpcs_{$function_name}_module_api_connected" );
				if ( $api_check ) {
					$module_available = true;
				}
			}
		}

		if ( $check_cloud_providers ) {
			return ( empty( $module_name ) ) ? $cloud_available : ( $cloud_available && $module_available ) ;
		} else {
			return ( ! empty( $module_name ) ) ? $module_available : false ;
		}
	}

	return false;
}

function wpcs_delete_template( $provider, $template, $type = 'server') {
	
	$module_data		= get_option( 'wpcs_module_list' );
	$template_data		= get_option( 'wpcs_template_data_backup' );
	$completed_tasks	= get_option( 'wpcs_tasks_completed', array());
		
	foreach ( $module_data[$provider]['templates'] as $key => $templates ) {

		if ( $template == $templates['name'] ) {

			unset( $module_data[$provider]['templates'][$key] );
			unset( $template_data[$provider]['templates'][$key] );

			$template_count	= ( isset( $module_data[ $provider ][ "{$type}_template_count" ] ) ) ? $module_data[ $provider ][ "{$type}_template_count" ] : 0;
			$template_count = ( $template_count > 0 ) ? --$template_count : $template_count;

			$module_data[ $provider ][ "{$type}_template_count" ]	= $template_count;
			$template_data[ $provider ][ "{$type}_template_count" ] = $template_count;

			$completed_tasks[]=$template;
			$delete_complete = true;
		}	
	}
	update_option( 'wpcs_tasks_completed', $completed_tasks);
	update_option( 'wpcs_module_list', $module_data );
	update_option( 'wpcs_template_data_backup', $template_data );
	
	return (isset( $delete_complete ) ) ? $delete_complete : false;
}

function wpcs_mb_to_gb( $mb ) {
	return $gb = $mb * (1/1024);
}

function wpcs_create_form( $label, $module_name, $new_status, $settings_link = '' ) {
	$module = strtolower( str_replace( " ", "_", $module_name ) );
	?>
	<form method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
		<input type="hidden" name="action" value="handle_serverpilot_module_action">
		<input type="hidden" name="wpcs_serverpilot_module_action" value="<?php echo $new_status; ?>">
		<input type="hidden" name="wpcs_serverpilot_module_id" value="<?php echo $module_name; ?>">
		<?php wp_nonce_field( 'handle_serverpilot_module_action_nonce', 'wpcs_handle_serverpilot_module_action_nonce' ); ?>
        <input style="border: none; font-size: 13px; display: inline;" type="submit" name="delete_server" id="delete_server_<?php echo $module; ?>" class="uk-button-link uk-link" value="<?php echo $label; ?>"><?php $link = ( '' !== $settings_link ) ? " | $settings_link" : ''; echo $link; ?>
	</form>
	<?php
}

/**
 *  Check module is Active
 *
 *  @since  3.0.6
 *  @return boolean  module active
 */
function wpcs_check_module_active( $module = null ) {

	$module_list = get_option( 'wpcs_module_list' );

	return ( 'active' == $module_list[$module]['status'] ) ? true : false;

}