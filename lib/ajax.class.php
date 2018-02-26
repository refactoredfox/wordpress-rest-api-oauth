<?php
/**
 * WP AJAX Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

if ( !defined( 'ABSPATH' ) ) die( '-1' );

if(!class_exists('RefactoredFox_RestOAuth_AJAX'))
{
	class RefactoredFox_RestOAuth_AJAX
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
			add_action( 'wp_ajax_refactoredfox_restoauth_ajax', array(&$this, 'refactoredfox_restoauth_ajax') );

			// register front-end actions
			add_action( 'wp_ajax_nopriv_refactoredfox_restoauth_ajax', array(&$this, 'refactoredfox_restoauth_ajax') );

		} // END public function __construct


		/**
  	 * updates the track id to users usermeta option via ajax
  	 */
		public function refactoredfox_restoauth_ajax()
		{

			echo json_encode( '' );

			die();

		}

	} // END class

	/**
	* Set the ajax on load
	*/
	$RefactoredFox_RestOAuth_AJAX = new RefactoredFox_RestOAuth_AJAX();

} // END if(!class_exists('RefactoredFox_RestOAuth_AJAX'))

?>
