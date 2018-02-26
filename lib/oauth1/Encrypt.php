<?php
/**
 * WP API Class for this plugin.
 *
 * @package JHU API
 */

namespace RefactoredFox\OAuth1;

class Encrypt
{
	private $_cipher;
	private $_key;

	/**
	 * Construct the plugin object
	 */
	public function __construct()
	{
		// set args
		$this->_cipher = 'aes-256-cbc';
		$this->_key = 'WLt2gVDyaq8EEBnaHzKYbm3QnShtncLYbyCNmSjxYz8=';

	} // END public function __construct

	/**
	 * Executes a string encryption
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function encrypt($string) {

		$key = \base64_decode($this->_key);
		$ivlen = \openssl_cipher_iv_length($this->_cipher);
		$iv = \openssl_random_pseudo_bytes($ivlen);

		// Do Encryption - output is raw binary
		$encrypted = \openssl_encrypt($string, $this->_cipher, $key, 0, $iv);

		// Attached IV to encrypted string and return
		return \base64_encode ( $encrypted . '::' . $iv );


	}

	/**
	 * Executes a string decryption
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function decrypt($string) {

		$key = \base64_decode($this->_key);

		// Decouple string and IV
		list($encrypted_data, $iv) = \explode('::', \base64_decode($string), 2);

		$string = \openssl_decrypt($encrypted_data, $this->_cipher, $key, 0, $iv);

		return $string;
	}

} // END class

?>
