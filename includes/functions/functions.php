<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpcs_deactivation() {
	update_option( 'wpcs_digitalocean_config_updated', false );
	update_option( 'wpcs_serverpilot_config_updated', false );
	update_option( 'wpcs_module_config', array() );
	update_option( 'wpcs_config', array() );
	
	update_option( 'wpcs_plugin_active', false );
}
register_deactivation_hook( __FILE__, 'wpcs_deactivation' );
	
function wpcs_activation() {
	do_action( 'wpcs_plugin_activated');
}
register_activation_hook( __FILE__, 'wpcs_activation' );

function wpcs_nonce_field( $action = -1, $id = '_wpnonce', $name = '_wpnonce', $referer = true, $echo = true ) {
    $name        = esc_attr( $name );
    $nonce_field = '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';
 
    if ( $referer ) {
        $nonce_field .= wp_referer_field( false );
    }
 
    if ( $echo ) {
        echo $nonce_field;
    }
 
    return $nonce_field;
}

function wpcs_settings_fields( $option_group, $id ) {
    echo "<input type='hidden' name='option_page' value='" . esc_attr( $option_group ) . "' />";
    echo '<input type="hidden" name="action" value="update" />';
    wpcs_nonce_field( "$option_group-options", "_wpnonce_$id" );
}


function wpcs_get_submit_button( $text = '', $type = 'default', $name = 'submit', $wrap = true, $other_attributes = null, $class = ' ' ) {
    if ( ! is_array( $type ) ) {
        $type = explode( ' ', $type );
    }
 
    $button_shorthand = array( 'default', 'primary', 'secondary', 'danger' );
    $classes          = array( 'uk-margin-remove-top' );
    foreach ( $type as $t ) {
        //if ( 'secondary' === $t || 'button-secondary' === $t ) {
        //    continue;
        //}
        $classes[] = in_array( $t, $button_shorthand ) ? 'uk-margin-remove-bottom uk-button uk-button-' . $t : $t;
		$classes[] = ( ' ' == $class ) ? ' ' : $class;
    }
    // Remove empty items, remove duplicate items, and finally build a string.
    $class = implode( ' ', array_unique( array_filter( $classes ) ) );
 
    $text = $text ? $text : __( 'Save Changes' );
 
    // Default the id attribute to $name unless an id was specifically provided in $other_attributes
    $id = $name;
    if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
        $id = $other_attributes['id'];
        unset( $other_attributes['id'] );
    }
 
    $attributes = '';
    if ( is_array( $other_attributes ) ) {
        foreach ( $other_attributes as $attribute => $value ) {
            $attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important
        }
    } elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string
        $attributes = $other_attributes;
    }
 
    // Don't output empty name and id attributes.
    $name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
    $id_attr   = $id ? ' id="' . esc_attr( $id ) . '"' : '';
 
    $button  = '<input type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class );
    $button .= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';
 
    if ( $wrap ) {
        $button = '<p class="submit">' . $button . '</p>';
    }
 
    return $button;
}

function wpcs_submit_button( $text = null, $type = 'default', $name = 'submit', $wrap = false, $other_attributes = null ) {
    echo wpcs_get_submit_button( $text, $type, $name, $wrap, $other_attributes );
}

function wpcs_do_settings_sections( $page ) {
    global $wp_settings_sections, $wp_settings_fields;
 
    if ( ! isset( $wp_settings_sections[ $page ] ) ) {
        return;
    }
 
    foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
        if ( $section['title'] ) {
            echo "<h2 class='uk-margin-remove-top uk-heading-divider'>{$section['title']}</h2>\n";
        }
 
        if ( $section['callback'] ) {
            call_user_func( $section['callback'], $section );
        }
 
        if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
            continue;
        }
        echo '<table class="form-table" role="presentation">';
        do_settings_fields( $page, $section['id'] );
        echo '</table>';
		echo '<hr>';
    }
}

function wpcs_get_settings_errors() {
    global $wpcs_settings_errors;
 
    // Check global in case errors have been added on this pageload.
    if ( empty( $wpcs_settings_errors ) ) {
        return array();
    }
	
	update_option( 'wpcs_get_settings_errors', $wpcs_settings_errors );
 
    return $wpcs_settings_errors;
}

function wpcs_add_settings_error( $setting, $code, $message, $type = 'error' ) {
 
    $wpcs_settings_errors[] = array(
        'setting' => $setting,
        'code'    => $code,
        'message' => $message,
        'type'    => $type,
    );
	
	update_option( 'wpcs_settings_errors', $wpcs_settings_errors );
}

function wpcs_settings_errors( $setting = '', $sanitize = false, $hide_on_update = false ) {
 
    $settings_errors = get_settings_errors( $setting, $sanitize );
	
	update_option('wpcs_settings_errors', $settings_errors );
 
    if ( empty( $settings_errors ) ) {
        return;
    }
 
	$output = '';
    foreach ( $settings_errors as $key => $details ) {
		
		$setting = substr( $details['setting'], 0, 5 );
		
		$dismissed = get_option( 'wpcs_dismissed_admin_notices', array() );
		
		if ( 'wpcs_' == $setting )  {
		
        	if ( 'updated' === $details['type'] ) {
            	$details['type'] = 'success';
        	}
		
        	if ( 'error' === $details['type'] ) {
            	$details['type'] = 'danger';
        	}
		
        	if ( 'info' === $details['type'] ) {
            	$details['type'] = 'primary';
        	}
 
        	if ( in_array( $details['type'], array( 'danger', 'success', 'warning', 'primary' ) ) ) {
            	$details['type'] = 'uk-alert-' . $details['type'];
        	}
			if ( !in_array( $details['setting'],  $dismissed ) ) {
        		$output .= "<div class='wpcs-notice {$details['type']}' uk-alert data-notice='{$details['setting']}'> \n";
				if ( in_array( $details['type'],  array( 'uk-alert-danger' ) ) ) {
        			$output .= "<a class='uk-alert-close' uk-close></a> \n";
				}
        		$output .= "<p><strong>{$details['message']}</strong></p>";
        		$output .= "</div> \n";
			}
			
		}

    }
    echo $output;
}

    /**
	 *  Check Cloud Provider Exists
	 *
	 *  @since  1.3.0
	 *  @return boolean  checked value
	 */
	function wpcs_check_cloud_provider_api( $module_name = NULL, $cloud_provider_name = NULL, $check_cloud_providers = true, $modules = null) {
 
		$module_list		= get_option( 'wpcs_module_list' );
		$cloud_available	= false;
		$module_available	= false;

		if ( is_array( $module_list ) ) {

			if ( empty( $cloud_provider_name ) && $check_cloud_providers ) {
				foreach ( $module_list as $key => $module ) {
					if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'active' == $module['status'] )  && ( 1 == $module['api_connected'] )) {
						$cloud_available = true;
					}
				}
			}

			if ( ! empty( $cloud_provider_name ) && $check_cloud_providers ) {
				if ( ( 'cloud_provider' == $module_list[$cloud_provider_name]['module_type'] ) && ( 'active' == $module_list[$cloud_provider_name]['status'] ) && ( 1 == $module['api_connected'] ) ) {
						$cloud_available = true;
				}
			}

			if ( ! empty( $module_name ) ) {
				if ( 'active' == $module_list[$module_name]['status'] )  {
					$module_name_lc 	= strtolower( str_replace( " ", "_", $module_name ) );
					$api_check 			= call_user_func("wpcs_{$module_name_lc}_module_api_connected");
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

    /**
	 *  Check Cloud Provider Module is Active
	 *
	 *  @since  3.0.6
	 *  @return boolean  checked value
	 */
	function wpcs_check_cloud_provider_module() {
 
		$module_list = get_option( 'wpcs_module_list' );

		if ( is_array( $module_list ) ) {
			foreach ( $module_list as $key => $module ) {
				if ( ( 'cloud_provider' == $module['module_type'] ) && ( 'active' == $module['status'] ) ) {
					$cloud_available = true;
				}
			}
		}

		return ( isset( $cloud_available ) ) ? $cloud_available : false ;
	}

    /**
     *  Test if EDD Plugin is Active.
     *
     *  @since 1.0.0
     */
    function wpcs_edd_active() {
	
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
        return is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php');
	}

    /**
     *  Test if EDD or WooCommerce Plugins are Active.
     *
     *  @since 1.0.0
     */
    function wpcs_cart_active() {
	
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$woocommerce	= is_plugin_active( 'woocommerce/woocommerce.php');
		$edd			= is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php');
		
        return ( $woocommerce || $edd );
	}

	/**
	 *  Display General System Notices
	 *
	 *  @since  2.0.0
	 */
	function wpcs_output_confirmation_notice() {
		
		$page_id = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'nopage';
		$error_array = get_option( 'wpcs_setting_errors', array() );
	
		//if ( array_key_exists( 'wpcs_serverpilot_template_name', $error_array ) && ( isset($error_array['wpcs_serverpilot_template_name']['error']) ) && ( 'true' == $error_array['wpcs_serverpilot_template_name']['error'] ) && ( 'wp-cloud-server-managed-servers' == $page_id ) ) {
		foreach ( $error_array as $key => $error ) {
			if ( 'new' == $error['status'] ) {
			?>
				<div class='uk-alert-<?php echo $error['type']; ?> wpcs-notice' uk-alert>
					<p><strong><?php echo $error['message']; ?></strong></p>
    			</div>
			<?php
			$error_array[ $key ]['status'] = 'completed';
			}
		}
		update_option( 'wpcs_setting_errors', $error_array );
			//if ( $error_array['wpcs_serverpilot_template_name']['count'] == 0 ) {
					
				
			//	$error_array['wpcs_serverpilot_template_name']['error'] = 'false';
				//$error_array['wpcs_serverpilot_template_name']['count'] = 0;
				
			//} else {
			//	$error_array['wpcs_serverpilot_template_name']['count']++;
			//}
			//update_option( 'wpcs_template_update_failed', $error_array );
		//}
		
		//$status_array = get_option( 'wpcs_dismissed_admin_notices', array() );
		
		//if ( is_array( $wp_settings_errors ) ) {
		//	foreach ( $status_array as $notified => $dismissed ) {
		//		foreach ( $wp_settings_errors as $index => $error ) {
 		//			if ( $error['setting'] == $notified ) {
 		//				unset( $wp_settings_errors[$index] );
		//				$pos = array_search( 'wpcs_serverpilot_template_name', $error_array );
						//unset($error_array[$error['setting']]);
		//				$error_array['wpcs_serverpilot_template_name'] = 'false';
		//				update_option( 'wpcs_template_update_failed', $error_array );
 		//			}
		//		}
		//	}
		//}
		//$status_array = array();
		//update_option( 'wpcs_dismissed_admin_notices', $status_array );
}

	/**
	 *  Handle the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_woocommerce_ajax_select_server() {

		// Check the nonce for the module notice data
		check_ajax_referer( 'wc_select_server_nonce', 'server_nonce' );

		if ( empty( $_POST['module'] ) ) {
			return;
		}

		// Initial Conditions
		$shared_servers_exist = false;

	    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : "";
			
		// Retrieve Servers and Templates
		$modules_list = get_option( 'wpcs_module_list' );
		$servers	= ( 'No Module' == $module ) ? false : $modules_list[ $module ]['servers'] ;
        $templates	= ( 'No Module' == $module ) ? false : $modules_list[ $module ]['templates'] ;
		$results    = array();
			
		$post_id = get_option( 'wpcs_edd_download_id' );

		// Determine if Shared Hosting Servers Exist
		$server_count = 0;
		if ( $servers && ( 'ServerPilot' == $module  ) ) {
			foreach ( $servers as $key => $server ){
				if ( 'Shared' == $server['hosting_type'] ) {
					$shared_servers_exist = true;
					$server_count++;
				}
			}
		}
			
        if ( $shared_servers_exist || $templates ) {
			if ( $servers && ( 'ServerPilot' == $module  ) ) {
				$server_list[] = '<optgroup label="Shared Hosting Server(s)">';
            	foreach ( $servers as $key => $server ){
					if ( ! array_key_exists('slug', $server) ) {
    					$server['slug'] = sanitize_title( $server['name'] );
					}
					if ( 'Shared' == $server['hosting_type'] ) {
						$server_list[] = '<option value="' . $server['slug'] . '">' . $server['name'] . '</option>';
					}
				}
				if ( $server_count > 1 ) {
					$server_list[] = '<option value="server-selected-by-region">All Servers (Selected at Checkout)</option>';
				}
			}
			
			if ( $templates ) {
				$server_list[] = '<optgroup label="Dedicated Server Template(s)">';
            	foreach ( $templates as $key => $template ){
                	$server_list[] = '<option value="' . $template['name'] . '">' . $template['name'] . '</option>';
				}
			}
			
            $response = json_encode( $server_list );
		} else {
			$server_list[] = '<option value="No Servers">No Servers/Templates Available</option>';
            $response = json_encode( $server_list );
        }

    	// response output
    	header( "Content-Type: application/json" );
    	echo $response;

    	// IMPORTANT: don't forget to "exit"
		exit;
				
	}
add_action( 'wp_ajax_wc_select_server', 'wpcs_woocommerce_ajax_select_server' );

	/**
	 *  Load the scripts for the EDD Meta Box Dropdown Module List
	 *
	 *  @since 1.0.0
	 */
	function wpcs_woocommerce_ajax_load_scripts() {

		// Load the JavaScript for the Server Select Dropdown & set-up the related Ajax script
		$select_server_args = array(
			'ajaxurl'	 					=> admin_url( 'admin-ajax.php' ),
			'ajax_wc_select_server_nonce' 	=> wp_create_nonce( 'wc_select_server_nonce' ),
		);
			
		wp_enqueue_script( 'select-server', WPCS_PLUGIN_URL . 'includes/woocommerce/js/select-server.js', array( 'jquery' ), '1.0.0', false );
		wp_localize_script( 'select-server', 'wpcs_wc_select_server_script', $select_server_args );

	}
add_action( 'admin_enqueue_scripts', 'wpcs_woocommerce_ajax_load_scripts' );