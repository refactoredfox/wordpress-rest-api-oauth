<?php
/**
 * WP Template Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if ( !class_exists( 'RefactoredFox_RestOAuth_Templates' ) ) {

	class RefactoredFox_RestOAuth_Templates {

		/**
		 * A reference to an instance of this class.
		 */
		private static $instance;

		/**
		 * The array of templates that this plugin tracks.
		 */
		protected $templates;

		/**
		 * Returns an instance of this class.
		 */
		public static function get_instance() {

			if ( null == self::$instance ) {
				self::$instance = new RefactoredFox_RestOAuth_Templates();
			}

			return self::$instance;

		}

		/**
		 * Initializes the plugin by setting filters and administration functions.
		 */
		private function __construct() {

			$this->templates = array();

			// Add a filter to the attributes metabox to inject template into the cache.
			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
				// 4.6 and older
				add_filter('page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ));
			} else {
				// Add a filter to the wp 4.7 version attributes metabox
				add_filter('theme_page_templates', array( $this, 'add_new_template' ));
			}

			// Add a filter to the save post to inject out template into the page cache
			add_filter('wp_insert_post_data', array( $this, 'register_project_templates' ));

			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path
			add_filter('template_include', array( $this, 'view_project_template'));

			// Add your templates to this array.
			$this->templates = array(
				'page-oauth1.php' => 'Oauth1 Authenticated Page',
			);

		}

		/**
		 * Overwrites page template with specified plugin file
		 *
		 * @param $template
		 *
		 * @return mixed|void
		 */
		public function template_toolkit_page( $template )
		{
			$post_id = get_the_ID();
			// Use custom template
			if ( $post_id == get_option('refactoredfox_restoauth_page') && is_page() ) {
				return $this->get_template_hierarchy( 'refactoredfox_restoauth_page.php' );
			}else {
				return $template;
			}
		}

		/**
		 * Checks for theme folder template before returning plugin file
		 *
		 * @param $template
		 *
		 * @return mixed|void
		 */
		public function get_template_hierarchy( $template )
		{
			// Add the .php file extension if not found.
			if ( strpos($template, '.php') === false ) {
				$template = rtrim( $template ) . '.php';
			}

			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( array( 'refactoredfox_restoauth/' . $template ) ) ) {
				$file = $theme_file;
			}
			else {
				$file = RF_REST_OAUTH_BASE_DIR . '/templates/' . $template;
			}

			return apply_filters( $template, $file );
		}

		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 * @param $posts_templates
		 *
		 * @return array
		 */
		public function add_new_template( $posts_templates ) {
			$posts_templates = array_merge( $posts_templates, $this->templates );
			return $posts_templates;
		}

		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doens't really exist.
		 *
		 * @param $atts
		 *
		 * @return mixed
		 */
		public function register_project_templates( $atts ) {

			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = array();
			}

			// New cache, therefore remove the old one
			wp_cache_delete( $cache_key , 'themes');

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;

		}

		/**
		 * Checks if the template is assigned to the page
		 *
		 * @param $template
		 *
		 * @return mixed|void
		 */
		public function view_project_template( $template ) {

			// Get global post
			global $post;

			// Return template if post is empty
			if ( ! $post ) {
				return $template;
			}

			$post_template = get_post_meta($post->ID, '_wp_page_template', true);

			// Return default template if we don't have a custom one defined
			if ( ! isset( $this->templates[$post_template]) ) {
				return $template;
			}

			$file = $this->get_template_hierarchy( $post_template );

			// Just to be safe, we check if the file exist first
			if ( file_exists( $file ) ) {
				return $file;
			} else {
				echo $file;
			}

			// Return template
			return $template;

		}

	}

}

?>
