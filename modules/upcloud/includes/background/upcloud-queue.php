<?php
/*
Plugin Name: Example Background Processing
Plugin URI: https://github.com/A5hleyRich/wp-background-processing
Description: Background processing in WordPress.
Author: Ashley Rich
Version: 0.1
Author URI: https://deliciousbrains.com/
Text Domain: example-plugin
Domain Path: /languages/
*/

class UpCloud_Background_Processing {

	/**
	 * @var UpCloud_Async_Request
	 */
	protected $process_single;

	/**
	 * @var UpCloud_Process
	 */
	protected $process_all;

	/**
	 * Example_Background_Processing constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'wpcs_execute_upcloud_api_queue', array( $this, 'process_handler' ) );
	}

	/**
	 * Init
	 */
	public function init() {
		require_once plugin_dir_path( __FILE__ ) . 'async-requests/class-upcloud-background-request.php';
		require_once plugin_dir_path( __FILE__ ) . 'background-processes/class-upcloud-background-process.php';

		$this->process_single = new UpCloud_Async_Request();
		$this->process_all    = new UpCloud_Process();
	}

	/**
	 * Process handler
	 */
	public function process_handler() {

			//$this->handle_single();

			$this->handle_all();
	}

	/**
	 * Handle single
	 */
	protected function handle_single() {
		$names = $this->get_names();
		$rand  = array_rand( $names, 1 );
		$name  = $names[ $rand ];

		$this->process_single->data( array( 'name' => $name ) )->dispatch();
	}

	/**
	 * Handle all
	 */
	protected function handle_all() {
		$names = $this->get_names();

		foreach ( $names as $name ) {
			$this->process_all->push_to_queue( $name );
		}

		$this->process_all->save()->dispatch();
	}

	/**
	 * Get names
	 *
	 * @return array
	 */
	protected function get_names() {
		return array(
			'zone',
			'plans',
			'storage',
			'servers',
			'prices',
		);
	}

}

new UpCloud_Background_Processing();