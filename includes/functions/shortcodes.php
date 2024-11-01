<?php

/**
 * Shortcodes for the 'WP Cloud Server' Plugin.
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	2.1.0
 *
 * @package    	WP_Cloud_Server
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_ShortCodes {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    	2.1.0
	 */
	public function __construct() {

		add_shortcode( 'client_websites', array( $this, 'client_website_info_shortcode' ) );
		add_shortcode( 'client_servers', array( $this, 'client_server_info_shortcode' ) );

	}
	
	/**
	 *  Client Website Info Shortcode
	 *
	 *  @since 2.1.0
	 */
	public function client_website_info_shortcode( $atts ) {

		$current_user	= get_current_user_id();
		$alldata		= get_option( 'wpcs_cloud_server_client_info' );
		$enabled_data	= get_option( 'wpcs_website_shortcodes_enabled_data' );
		
		// Extract current user servers from the global data
		if ( !empty( $alldata ) ) {
			foreach ( $alldata as $key => $module ) {
				foreach ( $module as $index => $value ) {
					if ( isset($value['user_id'] ) ) {
						if ( ( $current_user == $value['user_id'] ) &&  ( ( 'ServerPilot' == $value['module'] )  || ( 'Cloudways' == $value['module'] ) ) ) {
							$client[$index] = $value;
						}
					}
				}
			}
		}
		
		$output  		= '<table>';
		$output  		.= '<thead>';
		$output  		.= '        	<tr>';
		
		if ( !empty( $enabled_data['contents'] ) ) {
			foreach ( $enabled_data['contents'] as $key => $value ) {
				if ( ! empty( $value ) ) {
					$output  .= "            	<th>{$enabled_data['fields'][$key]}</th>";
				}
			}
		}
		
		$output  		.= '        	</tr>';
		$output  		.= '</thead>';
 		$output  		.= '<tbody>';
		
		if ( !empty( $client ) ) {
			foreach ( $client as $value ) {
					$output  .= "        	<tr>";
					foreach ( $enabled_data['contents'] as $key => $data ) {
						$contents	= ( isset( $value[$key] ) ) ? $value[$key] : '';
						$output  .= "            	<td>{$contents}</td>";
					}
					$output  .= "        	</tr>";
			}
		} else {
				$output  .= '        	<tr>';
        		$output  .= '    			<td colspan="4">No Website Information Available</td>';
				$output  .= '        	</tr>';
		}
		
 		$output  .= '	</tbody>';
 		$output  .= '</table>';
		
		return $output;
            
	}
	
	/**
	 *  Client Server Info Shortcode
	 *
	 *  @since 2.1.0
	 */
	public function client_server_info_shortcode( $atts ) {

		$current_user	= get_current_user_id();
		$alldata		= get_option( 'wpcs_cloud_server_client_info' );
		$enabled_data	= get_option( 'wpcs_server_shortcodes_enabled_data' );
		
		// Extract current user servers from the global data
		if ( !empty( $alldata ) ) {
			foreach ( $alldata as $key => $module ) {
				foreach ( $module as $index => $value ) {
					if ( isset($value['user_id'] ) ) {
						if ( ( $current_user == $value['user_id'] ) ) {
							$client[$index] = $value;
						}
					}
				}
			}
		}
		
			$output  		= '<table>';
			$output  		.= '<thead>';
			$output  		.= '        	<tr>';
		
			if ( !empty( $enabled_data['contents'] ) ) {
				foreach ( $enabled_data['contents'] as $key => $value ) {
					if ( ! empty( $value ) ) {
						$output  .= "            	<th>{$enabled_data['fields'][$key]}</th>";
					}
				}
			}
		
			$output  		.= '        	</tr>';
			$output  		.= '</thead>';
 			$output  		.= '<tbody>';
		
			if ( !empty( $client ) ) {
				foreach ( $client as $value ) {
					$output  .= "        	<tr>";
					foreach ( $enabled_data['contents'] as $key => $data ) {
						if ( ( 'login_url' !== $key ) && $data ) {
							$contents	= ( isset( $value[$key] ) ) ? $value[$key] : '';
							$output  .= "            	<td>{$contents}</td>";
						}
						if ( ( 'login_url' == $key ) && $data ) {
							$protocol 	= ( isset( $value['protocol'] ) ) ? "{$value['protocol']}://" : "";
							$port		= ( isset( $value['port'] ) ) ? ":{$value['port']}" : "";
							$output	 	.= ( $enabled_data['contents']['login_url'] ) ? "            	<td><a href='{$protocol}{$value['fqdn']}:{$value['port']}' target='_blank'>Login</a></td>" : '' ;
						}
					}
					$output  	.= "        	</tr>";
			}
		} else {
				$output  .= '        	<tr>';
        		$output  .= '    			<td colspan="7">No Server Information Available</td>';
				$output  .= '        	</tr>';
		}
		
 		$output  .= '	</tbody>';
 		$output  .= '</table>';
		
		return $output;
            
	}
}