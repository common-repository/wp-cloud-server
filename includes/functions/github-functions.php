<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves a list of GitHub Repositories
 *
 * @since  3.0.6
 *
 * @return repos_list List of repositories
 */
function wpcs_github_repos_list() {
	
	if ( function_exists('wpcs_github_call_api_list_repositories') ) {
		$repos = wpcs_github_call_api_list_repositories();
	
		if ( !empty( $repos ) ) {
			foreach ( $repos as $key => $repo ) {
				$repos_list[$repo['name']] = $repo['name'];
			}
		}
	}
	
	return ( isset( $repos_list ) && is_array( $repos_list ) ) ? $repos_list : false;
}

/**
 * Retrieves a GitHub Owner
 *
 * @since  3.0.6
 *
 * @return owner GitHub Owner
 */
function wpcs_github_repo_owner() {
	
	if ( function_exists('wpcs_github_owner') ) {
		$owner = wpcs_github_owner();
	}
	
	return ( isset( $owner ) ) ? $owner : false;
}