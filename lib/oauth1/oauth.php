<?php

namespace RefactoredFox\OAuth1\OAuth;

use Exception;
use RefactoredFox\OAuth1\Helpers;
use RefactoredFox\OAuth1\Encrypt;

function authorize_site() {

	$server = Helpers\get_server();

	try {
		$temporaryCredentials = $server->getTemporaryCredentials();

		// Store the credentials in the session.
		$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
		session_write_close();

		// Second part of OAuth 1.0 authentication is to redirect the
		// resource owner to the login screen on the server.
		$server->authorize($temporaryCredentials);
	} catch ( Exception $e ) {
		echo $e->getMessage();
	}
}

function get_token_credentials() {
	$here = Helpers\get_requested_url();

	$server = Helpers\get_server();

	// Retrieve the temporary credentials from step 2
	$temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

	// Third and final part to OAuth 1.0 authentication is to retrieve token
	// credentials (formally known as access tokens in earlier OAuth 1.0
	// specs).
	$tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

	// Now, we'll store the token credentials and discard the temporary
	// ones - they're irrelevant at this stage.
	unset($_SESSION['temporary_credentials']);
	$_SESSION['token_credentials'] = serialize($tokenCredentials);
	session_write_close();

	// Redirect to the user page
	header("Location: {$here}");
}
