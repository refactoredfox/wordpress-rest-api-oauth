<?php

namespace RefactoredFox\OAuth1;

class Header {

	private $keys;
	private $url;
	private $method;
	private $credentials;

	public function __construct( $keys, $url, $method ) {
		$this->keys   = $keys;
		$this->url    = $url;
		$this->method = $method;
		$this->set_credentials();
	}

	public function get_header() {

		$header = 'OAuth ';

		$oauth_params = array();

		foreach ( $this->credentials as $key => $value ) {
			$oauth_params[] = "$key=\"" . rawurlencode( $value ) . '"';
		}

		$header .= implode( ', ', $oauth_params );

		return $header;
	}

	private function set_credentials() {

		$credentials = array(
			'oauth_consumer_key'     => $this->keys['oauth_consumer_key'],
			'oauth_nonce'            => wp_generate_password( 12, false, false ),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $this->keys['oauth_token'],
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0'
		);

		// For some reason, this matters!
		ksort( $credentials );

		$this->credentials = $credentials;

		$this->set_oauth_signature();
	}

	private function set_oauth_signature() {

		$string_params = array();

		foreach ( $this->credentials as $key => $value ) {
			$string_params[] = "$key=$value";
		}

		$signature = "$this->method&" . rawurlencode( $this->url ) . '&' . rawurlencode( implode( '&', $string_params ) );

		$hash_hmac_key = rawurlencode( $this->keys['oauth_consumer_secret'] ) . '&' . rawurlencode( $this->keys['oauth_token_secret'] );

		$oauth_signature = base64_encode( hash_hmac( 'sha1', $signature, $hash_hmac_key, true ) );

		$this->credentials['oauth_signature'] = $oauth_signature;
	}
}

?>
