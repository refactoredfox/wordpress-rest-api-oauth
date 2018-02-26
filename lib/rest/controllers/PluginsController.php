<?php
/**
 * WP REST API Custom Controller Class.
 *
 * @package Refactored Fox Rest API OAuth
 */
namespace RefactoredFox\REST\Controllers;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

if ( !defined( 'ABSPATH' ) ) die( '-1' );

class PluginsController {

	private $namespace;
	private $resource_name;

	/**
	 * PluginsController constructor.
	 */
	public function __construct()
	{
		require_once './wp-admin/includes/update.php';
		require_once './wp-admin/includes/plugin.php';

		$this->namespace     = 'refactoredfox/v2';
		$this->resource_name = 'plugins';
	}

	/**
	 * Register our routes.
	 */
	public function register_routes()
	{
		register_rest_route( $this->namespace, '/' . $this->resource_name, array(
			// Here we register the readable endpoint for collections.
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			),
			// Register our schema callback.
			'schema' => array( $this, 'get_item_schema' ),
		) );


		// register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?url<url>[\s]+)', array(
		// 	// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
		// 	array(
		// 		'methods'   => 'GET',
		// 		'callback'  => array( $this, 'get_item' ),
		// 		'permission_callback' => array( $this, 'get_item_permissions_check' ),
		// 	),
		// 	// Register our schema callback.
		// 	'schema' => array( $this, 'get_item_schema' ),
		// ) );
	}

	/**
	 * Check permissions for the plugins.
	 *
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return bool|WP_Error
	 */
	public function get_items_permissions_check( $request )
	{
		if ( ! current_user_can( 'update_plugins' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the site resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		return true;
	}

	/**
	 * Grabs all plugins and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_items( $request )
	{
		$plugins = get_plugin_updates();

		$data = array();

		if ( empty( $plugins ) ) {
			return rest_ensure_response( $data );
		}

		foreach ( $plugins as $key => $plugin ) {
			$plugin->id = $key;
			$response = $this->prepare_item_for_response( $plugin, $request );
			$data[] = $this->prepare_response_for_collection( $response );
		}

		// Return all of our comment response data.
		return rest_ensure_response( $data );
	}

	/**
	 * Check permissions for the plugins.
	 *
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return bool|WP_Error
	 */
	public function get_item_permissions_check( $request )
	{
		if ( ! current_user_can( 'update_plugins' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the site resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		return true;
	}

	/**
	 * Grabs named plugin and outputs them as a rest response.
	 *
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_item( $request )
	{
		$plugins = get_plugin_updates();
		$url = (string) $request['url'];

		if ( !array_key_exists( $url, $plugins ) ) {
			return rest_ensure_response( array() );
		}

		$plugin = $plugins[$url];
		$plugin->id = $url;

		$response = $this->prepare_item_for_response( $plugin );

		// Return all of our post response data.
		return $response;
	}

	/**
	 * Matches the plugin data to the schema we want.
	 *
	 * @param $plugin
	 * @param $request
	 *
	 * @return mixed|\WP_REST_Response
	 * @internal param WP_Post $post The comment object whose response is being prepared.
	 *
	 */
	public function prepare_item_for_response( $plugin, $request )
	{
		$plugin_data = array();

		$schema = $this->get_item_schema( $request );

		// We are also renaming the fields to more understandable names.
		if ( isset( $schema['properties']['id'] ) ) {
			$plugin_data['id'] = (string) $plugin->id;
		}

		if ( isset( $schema['properties']['name'] ) ) {
			$plugin_data['name'] = (string) $plugin->Name;
		}

		if ( isset( $schema['properties']['current_version'] ) ) {
			$plugin_data['current_version'] = (string) $plugin->Version;
		}

		if ( isset( $schema['properties']['new_version'] ) ) {
			$plugin_data['new_version'] = (string) $plugin->update->new_version;
		}

		return rest_ensure_response( $plugin_data );
	}

	/**
	 * Prepare a response for inserting into a collection of responses.
	 *
	 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
	 *
	 * @param WP_REST_Response $response Response object.
	 * @return array Response data, ready for insertion into collection data.
	 */
	public function prepare_response_for_collection( $response )
	{
		if ( ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}

		$data = (array) $response->get_data();
		$server = rest_get_server();

		if ( method_exists( $server, 'get_compact_response_links' ) ) {
			$links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
		} else {
			$links = call_user_func( array( $server, 'get_response_links' ), $response );
		}

		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Get our sample schema for a post.
	 *
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return array
	 */
	public function get_item_schema( $request )
	{
		$schema = array(
			// This tells the spec of JSON Schema we are using which is draft 4.
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			// The title property marks the identity of the resource.
			'title'                => 'plugin',
			'type'                 => 'object',
			// In JSON Schema you can specify object properties in the properties attribute.
			'properties'           => array(
				'id' => array(
					'description'  => esc_html__( 'Unique identifier for the object.', 'refactoredfox_restoauth' ),
					'type'         => 'string',
					'context'      => array( 'view', 'edit', 'embed' ),
					'readonly'     => true,
				),
				'name' => array(
					'description'  => esc_html__( 'The name for the object.', 'refactoredfox_restoauth' ),
					'type'         => 'string',
				),
				'current_version' => array(
					'description'  => esc_html__( 'The current version for the object.', 'refactoredfox_restoauth' ),
					'type'         => 'string',
				),
				'new_version' => array(
					'description'  => esc_html__( 'The new version for the object.', 'refactoredfox_restoauth' ),
					'type'         => 'string',
				),
			),
		);

		return $schema;
	}

	// Sets up the proper HTTP status code for authorization.
	public function authorization_status_code()
	{

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}
}

?>
