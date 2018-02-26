<?php
/*
Plugin Name: WordPress Rest API OAuth
Plugin URI: http://www.refactoredfox.com
Description: Base library for connecting to the WordPress REST API via OAuth1 authentication. Includes a sample custom path that checks plugin version numbers and returns alerts for any out of date plugins.
Version: 0.1.0
Author: Refactored Fox Studios
Author URI: http://www.refactoredfox.com
Contributors: Joe Tercero
License: GPL2

Copyright Refactored Fox Studios 2018

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
|--------------------------------------------------------------------------
| Master TODO List
|--------------------------------------------------------------------------
*/

/*

- Things I would like this plugin to do

-- [X] Set up WP REST API OAuth Tokens for active sites
	-- [X] Tangentially this authorization will enable the portal to monitor plugin and core updates
	-- [X] Set-up Shortcode for OAuth path
	-- [ ] Set-up Widget for OAuth path
	-- [ ] Add front-end templates
	-- [ ] Refactor all classes into auto-loader

*/


/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/

if ( !defined('RF_REST_OAUTH_THEME_DIR') )
	define('RF_REST_OAUTH_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());
if ( !defined('RF_REST_OAUTH_PLUGIN_DIR') )
	define('RF_REST_OAUTH_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
if ( !defined('RF_REST_OAUTH_PLUGIN_URL') )
	define('RF_REST_OAUTH_PLUGIN_URL', plugin_dir_url( __FILE__ ));
if ( !defined('RF_REST_OAUTH_BASE_FILE') )
	define('RF_REST_OAUTH_BASE_FILE', basename( __FILE__ ));
if ( ! defined( 'RF_REST_OAUTH_BASE_DIR' ) )
	define( 'RF_REST_OAUTH_BASE_DIR', dirname( __FILE__ ) );
if ( !defined('RF_REST_OAUTH_PREFIX') )
	define('RF_REST_OAUTH_PREFIX', 'refactoredfox_restoauth');
if ( !defined('RF_REST_OAUTH_VERSION_KEY') )
	define('RF_REST_OAUTH_VERSION_KEY', 'refactoredfox_restoauth_version');
if ( !defined('RF_REST_OAUTH_VERSION_NUM') )
	define('RF_REST_OAUTH_VERSION_NUM', '0.1.0');
if ( !defined('RF_REST_OAUTH_DEBUG') )
	define('RF_REST_OAUTH_DEBUG', true);

/*
|--------------------------------------------------------------------------
| Namspaces
|--------------------------------------------------------------------------
*/

use RefactoredFox\OAuth1\Helpers;

/*
|--------------------------------------------------------------------------
| PLUGIN CLASS
|--------------------------------------------------------------------------
*/

if(!class_exists('RefactoredFox_RestOAuth'))
{
	class RefactoredFox_RestOAuth
	{


	/**
	* Construct the plugin object
	*/
	public function __construct()
	{
		// Require Settings
		require_once(sprintf("%s/www/settings.php", RF_REST_OAUTH_BASE_DIR));

		// Add custom plugin functions
		require_once(sprintf("%s/lib/actions.class.php", RF_REST_OAUTH_BASE_DIR));
		require_once(sprintf("%s/lib/filters.class.php", RF_REST_OAUTH_BASE_DIR));
		require_once(sprintf("%s/lib/ajax.class.php", RF_REST_OAUTH_BASE_DIR));
		require_once(sprintf("%s/lib/utils.class.php", RF_REST_OAUTH_BASE_DIR));
		require_once(sprintf("%s/lib/form.class.php", RF_REST_OAUTH_BASE_DIR));

		// Require plugin shortcodes
		require_once(sprintf("%s/lib/shortcodes.class.php", RF_REST_OAUTH_BASE_DIR));

		// Require plugin widgets
		require_once(sprintf("%s/lib/widget.class.php", RF_REST_OAUTH_BASE_DIR));

		// Require plugin templates
		require_once(sprintf("%s/lib/templates.class.php", RF_REST_OAUTH_BASE_DIR));

	} // END public function __construct

	/**
	* Activate the plugin
	*/
	public static function activate()
	{
		if( get_option(RF_REST_OAUTH_VERSION_KEY) != RF_REST_OAUTH_VERSION_NUM ) {

			update_option( RF_REST_OAUTH_VERSION_KEY, RF_REST_OAUTH_VERSION_NUM );

		}

	} // END public static function activate

	/**
	* Deactivate the plugin
	*/
	public static function deactivate()
	{

		// Do nothing
	} // END public static function deactivate

	} // END class RefactoredFox_RestOAuth
} // END if(!class_exists('RefactoredFox_RestOAuth'))

if(class_exists('RefactoredFox_RestOAuth'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('RefactoredFox_RestOAuth', 'activate'));
	register_deactivation_hook(__FILE__, array('RefactoredFox_RestOAuth', 'deactivate'));

	// instantiate the plugin class
	$refactoredfox_restoauth = new RefactoredFox_RestOAuth();

	if(isset($refactoredfox_restoauth))
	{

		/*
		|--------------------------------------------------------------------------
		| PLUGIN SETTINGS
		|--------------------------------------------------------------------------
		*/

		$RefactoredFox_RestOAuth_Settings = new RefactoredFox_RestOAuth_Settings();

		// Check if WP REST API enabled then initialize
		if ( in_array( 'rest_api', get_option( RF_REST_OAUTH_PREFIX . '_modules', array() ) ) ) {
			require_once(sprintf("%s/vendor/autoload.php", RF_REST_OAUTH_BASE_DIR));
			$Routes = new RefactoredFox\REST\Routes();
		}

		// If Shortcodes then initialize
		$RefactoredFox_RestOAuth_Shortcodes = new RefactoredFox_RestOAuth_Shortcodes();

		// TODO: If Widgets then initialize
		// $RefactoredFox_RestOAuth_Widgets = new RefactoredFox_RestOAuth_Widgets();

		// TODO: If Templates then initialize
		// $RefactoredFox_RestOAuth_Templates = new RefactoredFox_RestOAuth_Templates();

		/*
		|--------------------------------------------------------------------------
		| Custom WordPress Functions
		|--------------------------------------------------------------------------
		*/

		function refactoredfox_restoauth_get_plugin_updates( $url, $identifer, $secret ) {

			if ( !in_array( 'rest_api', get_option( RF_REST_OAUTH_PREFIX . '_modules', array() ) ) ) {
				return '<p>Rest API Currently Disabled.</p>';
			}

			if( !class_exists('RefactoredFox\\REST\\Controllers\\PluginsController') ) {
				return '<p>Rest Commends Not Found.</p>';
			}

			$output = '';
			$url = esc_url($url);

			if( isset($_GET['clear']) ) {
				unset($_SESSION['user_credentials']);
				unset($_SESSION['token_credentials']);
			}

			if( !isset($_SESSION['user_credentials']) ) {
				$_SESSION['user_credentials'] = serialize(array(
					'url' => $url,
					'identifer' => $identifer,
					'secret' => $secret,
					'callback' => get_the_permalink()
				));
			}

			$tokenCredentials = isset($_SESSION['token_credentials']) ? unserialize($_SESSION['token_credentials']) : false;

			$output .= '<h2>Fetching from ' . $url . '</h2>';
			$output .= '<hr/>';

			// Initiate a new oAuth WordPressPlugins Server
			$server = Helpers\get_server();

			if ( $tokenCredentials ) {
				$data = $server->getPluginDetails($tokenCredentials);

				if( $data ) {
					foreach( $data as $plugin ){
						$output .= '<h3>' . $plugin->name. '</h3>';
						$output .= '<p>Current: ' . $plugin->currentVersion . ' | New: ' . $plugin->newVersion . '</p>';
					}
				}else{
					$output .= '<p>Rest Commands Failed. Check Log for Error.</p>';
				}

			}else{

				$output .= '<a href="' . get_the_permalink() . '?authorize">Authorize</a>';
			}

			return $output;
		}


	} // End if Plugin Active

} // End Plugin Class

?>
