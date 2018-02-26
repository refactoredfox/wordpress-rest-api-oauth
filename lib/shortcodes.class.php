<?php
/**
 * WP Shortcoedes Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if ( !class_exists( 'RefactoredFox_RestOAuth_Shortcodes' ) ) {

	class RefactoredFox_RestOAuth_Shortcodes {

		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register shortcodes
			add_shortcode( 'monitor_plugins', array(&$this, 'monitor_plugins_shortcode_callback' ));
		}

		//Returns html output
		public static function monitor_plugins_shortcode_callback( $atts )
		{
			$a = shortcode_atts( array(
				'url' => '',
				'identifer' => '',
				'secret' => ''
			), $atts );

			if( empty($a['url']) ){
				return;
			}

			// Create html output
			$output = '';

			$output .= refactoredfox_restoauth_get_plugin_updates($a['url'], $a['identifer'], $a['secret']);

			return $output;
		}

	}

}

?>
