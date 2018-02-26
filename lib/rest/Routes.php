<?php
/**
 * Rest API Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */
namespace RefactoredFox\REST;

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

use RefactoredFox\REST\Controllers\PluginsController;

class Routes {

	/**
	 * Create a new plugin instance.
	 *
	 */
	public function __construct()
	{
		// Add all custom rest routes
		add_action( 'rest_api_init', array( $this, 'register_plugin_rest_routes'), 10 );

	}

	/**
	 * Function to register our new routes from the controller.
	 */
	public function register_plugin_rest_routes() {
		$controller = new PluginsController();
		$controller->register_routes();
	}

}

/**
 * Set the actions on load
 */
$Routes = new Routes();

?>
