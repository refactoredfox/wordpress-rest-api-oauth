<?php

namespace RefactoredFox\OAuth1\Helpers;

use Exception;
use WordPress\Discovery;
use RefactoredFox\OAuth1\UpdatePlugins as OAuthClient;

function get_requested_url() {
	$scheme = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https' : 'http';
	$here = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
		// Strip the query string
		$here = str_replace( '?' . $_SERVER['QUERY_STRING'], '', $here );
	}

	return $here;
}

function get_server($site_id = null) {
	static $server = null;
	if ( ! empty( $server ) ) {
		return $server;
	}

	// TODO: remove session vars - get_site_post_type_meta()
	$user_credentials = isset($_SESSION['user_credentials']) ? unserialize($_SESSION['user_credentials']) : false;

	$site_meta = array(
		'url' => $user_credentials['url'] . '/wp-json/',
		'client_identifier' => $user_credentials['identifer'],
		'client_secret' => $user_credentials['secret'],
		'api_root' => isset($_SESSION['api_root']) ? $_SESSION['api_root'] : '',
		'auth_urls' => isset($_SESSION['auth_urls']) ? $_SESSION['auth_urls'] : '',
		'callback_uri' => $user_credentials['callback']
	);

	if( empty( $site_meta['client_identifier'] ) || empty( $site_meta['client_secret'] ) ) {
		echo "Site does not have API Keys saved.";
	}

	if( empty( $site_meta['api_root'] ) || empty( $site_meta['auth_urls'] ) ) {
		try {
			date_default_timezone_set('UTC');
			$site = Discovery\discover( $site_meta['url'] );

			if ( empty( $site ) ) {
				echo sprintf( "Couldn't find the API at <code>%s</code>.", htmlspecialchars( $url ) );
			}elseif ( ! $site->supportsAuthentication( 'oauth1' ) ) {
				echo "Site doesn't appear to support OAuth 1.0a authentication.";
			}

			$site_meta['api_root'] = $site->getIndexURL();
			$site_meta['auth_urls'] = $site->getAuthenticationData( 'oauth1' );

			$_SESSION['api_root'] = $site_meta['api_root'];
			$_SESSION['auth_urls'] = $site_meta['auth_urls'];
		}
		catch (Exception $e) {
			// TODO: Better Error Handling
			echo sprintf( "Error while discovering: %s.", htmlspecialchars( $e->getMessage() ) );
		}
	}

	try {
		$server = new OAuthClient(array(
			'identifier' => $site_meta['client_identifier'],
			'secret' => $site_meta['client_secret'],
			'api_root' => $site_meta['api_root'],
			'auth_urls' => $site_meta['auth_urls'],
			'callback_uri' => $site_meta['callback_uri']
		));
	}
	catch(Exception $e) {
		echo sprintf( "Error while discovering: %s.", htmlspecialchars( $e->getMessage() ) );
	}

	return $server;
}
