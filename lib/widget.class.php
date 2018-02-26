<?php
/**
 * WP Widgets Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if(!function_exists("refactoredfox_restoauth_register_widget"))
{
	function refactoredfox_restoauth_register_widget()
	{
		register_widget( 'RefactoredFox_RestOAuth_Widget' );
	}
}

add_action( 'widgets_init', 'refactoredfox_restoauth_register_widget' );

if(!class_exists("RefactoredFox_RestOAuth_Widget")){

	class RefactoredFox_RestOAuth_Widget extends WP_Widget {

		/**
		* Constructor
		*/
		function __construct() {

			$description = __('Refactored Fox Rest API OAuth Widget - Displays a blank widget template', RF_REST_OAUTH_PREFIX);

			parent::__construct(
				'refactoredfox_restoauth_widget', // Base ID
				__( 'Refactored Fox Rest API OAuth Widget', RF_REST_OAUTH_PREFIX ), // Name
				array( 'classname' => 'refactoredfox-widget', 'description' => $description ) // Args
			);
		}

		/**
		 * Widget Display
		 *
		 * @param array $args
		 * @param array $instance
		 */
		function widget($args, $instance) {
			extract( $args );

			$before_widget = '<div>';
			$after_widget .= '</div>';

			$before_title = '<header>'.$before_title;
			$after_title .= '</header>';

			echo $before_widget;
			$title = apply_filters('widget_title', $instance['title'] );

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
				?>

				<?php
			echo $after_widget;
		}

		/**
		 * Widget Update
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 */
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance["title"] = strip_tags( $new_instance["title"] );

			return $instance;
		}

		/**
		 * Widget Form Creation
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		function form($instance) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '' ));
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', RF_REST_OAUTH_PREFIX); ?>:</label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
			</p>
		<?php
		}

		/**
		* Additional Widget Functions
		*/

	}

}
?>
