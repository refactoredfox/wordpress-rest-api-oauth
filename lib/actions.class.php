<?php
/**
 * WP Actions Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

if ( !defined( 'ABSPATH' ) ) die( '-1' );

if(!class_exists('RefactoredFox_RestOAuth_Actions'))
{
	class RefactoredFox_RestOAuth_Actions
	{

		/**
		* Start up
		*/
		public function __construct()
		{
			// Plugin Actions
			add_action( 'plugins_loaded', array( $this, 'plugin_update_check'), 10 );
			add_action( 'plugins_loaded', array( 'RefactoredFox_RestOAuth_Templates', 'get_instance' ) );
		}

		// Add update for plugin version option
		function plugin_update_check()
		{
			if (get_option(RF_REST_OAUTH_VERSION_KEY) != RF_REST_OAUTH_VERSION_NUM) {
				RefactoredFox_RestOAuth::activate();
			}
		}

	} // End RefactoredFox_RestOAuth_Actions Class

	/**
	 * Set the actions on load
	 */
	$RefactoredFox_RestOAuth_Actions = new RefactoredFox_RestOAuth_Actions();

}// End RefactoredFox_RestOAuth_Actions If Active

?>
