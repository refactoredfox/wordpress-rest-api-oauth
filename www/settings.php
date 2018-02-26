<?php
/**
 * Plugin Settings Page Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

if ( !defined( 'ABSPATH' ) ) die( '-1' );

if(!class_exists('RefactoredFox_RestOAuth_Settings'))
{
	class RefactoredFox_RestOAuth_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_main_menu'));
		} // END public function __construct

		/**
		* hook into WP's admin_init action hook
		*/
		public function admin_init()
		{
			$settings_page = RF_REST_OAUTH_PREFIX;
			$section_main = RF_REST_OAUTH_PREFIX . '-main';

			// register your plugin's settings
			register_setting('refactoredfox-main', RF_REST_OAUTH_PREFIX . '_modules');

			// add your settings section
			// Plugin Module Enables
			add_settings_section(
			  $section_main,
			  'REST OAuth Modules',
			  array(&$this, 'settings_section_refactoredfox_restoauth_main'),
			  $settings_page
			);

			// add setting's fields
			// Enable Rest API
			add_settings_field(
				'refactoredfox_restoauth_modules_rest_api',
				'Enable Rest API Plugin Monitoring',
				array(&$this, 'settings_field_input_checkbox'),
				RF_REST_OAUTH_PREFIX,
				$section_main,
				array(
					'field' => RF_REST_OAUTH_PREFIX . '_modules',
					'id' => 'rest_api'
				)
			);

		}

		/**
		* These functions provide section text for sections fields
		*/
		public function settings_section_refactoredfox_restoauth_main()
		{
			// Think of this as help text for the section.
			echo '<p>Using the checkboxes below enable or disable plugin functions</p>';
		}

		/**
		* Get the settings option array and print one of its values
		*/
		public function settings_field_input_checkbox($args)
		{
			$disabled = false;
			$message = '';
			// Get the field name from the $args array
			$field = $args['field'];
			// Get the field name from the $args array
			$id = $args['id'];
			// Get the value of this setting
			$value = get_option($field, array());
			$value = is_array($value) ? $value : array($value);

			switch($id) {
				case 'rest_api':
					if( is_plugin_active( 'rest-api-oauth1/oauth-server.php' ) && class_exists('WP_REST_Server') ) {
						$disabled = true;
					} else {
						$message = 'This module requires the WordPres Rest API and Rest API OAuth Plugin.';
					}
					break;
				case 'restrict_edit':
					$message = 'This module restricts file editing in the dashboard. Disabling may break continuity with the code repository.';
					break;
				default:
					$disabled = true;
			}

			echo sprintf('<input name="%s[]" id="%s_%s" type="checkbox" value="%s" ' . checked( in_array($id, $value), true, 0) . '/>', $field, $field, $id, $id );
			echo $message !== '' ? sprintf('<p class="description">%s</p>', $message) : '';
		}

		/**
		* This function provides text inputs for settings fields
		*/
		public function settings_field_input_text($args)
		{
			// Get the field name from the $args array
			$field = $args['field'];
			// Get the value of this setting
			$value = get_option($field);
			// echo a proper input type="text"
			echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
		}

		/**
		* This function provides textarea inputs for settings fields
		*/
		public function settings_field_input_textarea($args)
		{
			// Get the field name from the $args array
			$field = $args['field'];
			// Get the value of this setting
			$value = get_option($field);
			// echo a proper input type="text"
			echo sprintf('<textarea  name="%s" id="%s">%s</textarea>', $field, $field, $value);

			if($args['desc']){
				echo sprintf('<p>%s</p>', $args['desc']);
			}
		}

		/**
		* This function provides select inputs for settings fields
		*/
		public function settings_field_object_select($args)
		{
			// Get the field name from the $args array
			$field = $args['field'];
			$options = $args['options'];
			// Get the value of this setting
			$value = get_option($field);
			// echo a proper input type="text"
			echo sprintf('<select name="%s" id="%s" />', $field, $field);
				echo sprintf('<option value="%d" />%s</option>', 0, 'Select Page');
			foreach($options as $key => $obj){
				if( $obj->ID == $value ){
					echo sprintf('<option selected="selected" value="%d" />%s</option>', $obj->ID, $obj->post_title);
				}else{
					echo sprintf('<option value="%d" />%s</option>', $obj->ID, $obj->post_title);
				}
			}
			echo '</select>';
		}

		/**
		* Add a Setings Link to Menu
		*/
		public function add_main_menu()
		{
			// REF
			// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
			// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

			// Add a page to manage this plugin's settings
			add_options_page(
			  'REST OAuth API',
			  'Rest OAuth',
			  'manage_options',
			  RF_REST_OAUTH_PREFIX,
			  array(&$this, 'plugin_settings_page'),
			  'dashicons-id-alt',
			  '30.1'
			);
		}


		/**
		* Menu Callbacks
		*/
		public function plugin_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/templates/settings.php", RF_REST_OAUTH_BASE_DIR));
		}

	} // END class

} // END if(!class_exists('RefactoredFox_RestOAuth_Settings'))
?>
