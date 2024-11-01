<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  Check Cloud Provider Exists
 *
 *  @since  1.3.0
 *  @return boolean  checked value
 */
function wpcs_check_serverpilot_with_cloud_api( $module_name = null, $cloud_provider_name = null, $check_cloud_providers = true ) {
		
	return wpcs_check_cloud_provider_api( 'serverpilot' );
 
}

function wpcs_serverpilot_module_api_connected() {
	return WPCS_ServerPilot()->settings->wpcs_serverpilot_module_api_connected();
}