<?php
/**
 * WP Filters Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

if ( !defined( 'ABSPATH' ) ) die( '-1' );

if(!class_exists('RefactoredFox_RestOAuth_Filters'))
{
	class RefactoredFox_RestOAuth_Filters
	{

		/**
		* Start up
		*/
		public function __construct()
		{
			$plugin = plugin_basename(__FILE__);

			// Plugin Filters
			add_filter( 'plugin_action_links_$plugin', array( $this, 'plugin_settings_link'), 10, 1 );
			// add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_access'), 10, 1 );

		}

		 // Add the settings link to the plugins page
		 function plugin_settings_link($links)
		 {
			 $settings_link = '<a href="options.php?page=refactoredfox_restoauth">Settings</a>';
			 array_unshift($links, $settings_link);
			 return $links;
		 }

		function restrict_rest_access( $result ) {
			if ( ! empty( $result ) ) {
				return $result;
			}
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
			}
			return $result;
		}

	} // End RefactoredFox_RestOAuth_Filters Class

	/**
	 * Set the filters on load
	 */
	$RefactoredFox_RestOAuth_Filters = new RefactoredFox_RestOAuth_Filters();

} // End RefactoredFox_RestOAuth_Filters If Active

?>
