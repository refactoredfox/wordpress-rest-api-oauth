<?php

namespace RefactoredFox\OAuth1;

use Exception;
use RefactoredFox\OAuth1\Header as Header;
use RefactoredFox\OAuth1\Server as Server;
use RefactoredFox\REST\Handlers\Plugin;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Signature\SignatureInterface;

class UpdatePlugins extends Server {
	protected $baseUri;

	protected $authURLs = array();

	protected $cachedPluginDetailsResponse = false;

	/**
	 * {@inheritDoc}
	 */
	public function __construct($clientCredentials, SignatureInterface $signature = null)
	{
		parent::__construct($clientCredentials, $signature);
		if (is_array($clientCredentials)) {
			$this->parseConfigurationArray($clientCredentials);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function urlTemporaryCredentials()
	{
		return $this->authURLs->request;
	}

	/**
	 * {@inheritDoc}
	 */
	public function urlAuthorization()
	{
		return $this->authURLs->authorize;
	}

	/**
	 * {@inheritDoc}
	 */
	public function urlTokenCredentials()
	{
		return $this->authURLs->access;
	}

	/**
	 * {@inheritDoc}
	 */
	public function urlPluginDetails()
	{
		return rtrim( $this->baseUri, '/' ) . '/refactoredfox/v2/plugins/';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	protected function fetchPluginDetails(TokenCredentials $tokenCredentials, $force = true)
	{
		if (!$this->cachedPluginDetailsResponse || $force) {
			$url = $this->urlPluginDetails();

			$method = 'GET';
			$keys = array(
				'oauth_consumer_key'    => $this->clientCredentials->getIdentifier(),
				'oauth_consumer_secret' => $this->clientCredentials->getSecret(),
				'oauth_token'           => $tokenCredentials->getIdentifier(),
				'oauth_token_secret'    => $tokenCredentials->getSecret(),
			);

			$oauth = new Header( $keys, $url, $method );
			$header = $oauth->get_header();

			$args = array( 'headers' => array( 'Authorization' => $header ) );

			try {
				$response = wp_remote_get( $url, $args );
			} catch (Exception $e) {
				$response = $e->getResponse();
				$body = $response->getBody();
				$statusCode = $response->getStatusCode();

				throw new Exception(
					"Received error [$body] with status code [$statusCode] when retrieving token credentials."
				);
			}

			$this->cachedPluginDetailsResponse = json_decode( wp_remote_retrieve_body( $response ), true );
		}

		return $this->cachedPluginDetailsResponse;
	}

	/**
	 * Get plugin details by providing valid token credentials.
	 *
	 * @param $data
	 *
	 * @return array
	 * @internal param TokenCredentials $tokenCredentials
	 * @internal param bool $force
	 *
	 */
	public function pluginsDetails($data)
	{
		$plugins = array();

		foreach( $data as $plugin ) {
			$plugins[] = $this->pluginDetails($plugin['data']);
		}

		return $plugins;
	}

	/**
	 * Get plugin details by providing valid token credentials.
	 *
	 * @param $data
	 *
	 * @return Plugin
	 * @internal param TokenCredentials $tokenCredentials
	 * @internal param bool $force
	 *
	 */
	public function pluginDetails($data)
	{
		$plugin = new Plugin();

		$plugin->pid = $data['id'];
		$plugin->name = $data['name'];
		$plugin->currentVersion = $data['current_version'];
		$plugin->newVersion = $data['new_version'];

		$used = array('id', 'name', 'current_version', 'new_version');

		// Save all extra data
		$plugin->extra = array_diff_key($data, array_flip($used));

		return $plugin;
	}

	/**
	 * Get plugin details by providing valid token credentials.
	 *
	 * @param TokenCredentials $tokenCredentials
	 * @param bool $force
	 *
	 * @return array
	 */
	public function getPluginDetails(TokenCredentials $tokenCredentials, $force = false)
	{
		$data = $this->fetchPluginDetails($tokenCredentials, $force);

		return $this->pluginsDetails($data);
	}

	/**
	 * Parse configuration array to set attributes.
	 *
	 * @param array $configuration
	 * @throws Exception
	 */
	private function parseConfigurationArray(array $configuration = array())
	{
		if (!isset($configuration['api_root'])) {
			throw new Exception('Missing WordPress API index URL');
		}
		$this->baseUri = $configuration['api_root'];

		if (!isset($configuration['auth_urls'])) {
			throw new Exception('Missing authorization URLs from API index');
		}
		$this->authURLs = $configuration['auth_urls'];
	}
}
