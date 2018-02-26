<?php
/**
 * Display Template Class for this plugin.
 *
 * @package Refactored Fox Rest API OAuth
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if ( !class_exists( 'RefactoredFox_RestOAuth_Display' ) ) {

	class RefactoredFox_RestOAuth_Display
	{

		//Sterilize Values
		public static function clear_text($value)
		{
			return 	esc_attr(trim(stripslashes($value)));
		}

		//Create html form field
		public static function create_field($args)
		{
			//Set field args defaults
			$defaults = array(
				'type' => '',
				'id' => '',
				'name' => '',
				'class' => false,
				'values' => '',
				'options' => array(),
				'data' => array(),
				'default' => '',
				'checked' => false,
				'disabled' => false
			);

			//Parse defaults into passed in args
			$args = wp_parse_args( $args, $defaults );

			$args['options'] = is_array($args['options']) ? $args['options'] : explode(';',$args['options']);
			$args['options'] = array_map( 'RefactoredFox_RestOAuth_Display::clear_text', $args['options'] );

			switch( $args['type'] ){
				case 'dropdown':
					$html = RefactoredFox_RestOAuth_Display::create_dropdown($args);
				break;

				case 'm_dropdown':
					$html = RefactoredFox_RestOAuth_Display::create_multidropdown($args);
				break;

				case 'checkbox':
					$html = RefactoredFox_RestOAuth_Display::create_checkbox($args);
				break;

				case 'text':
					$html = RefactoredFox_RestOAuth_Display::create_text($args);
				break;

				case 'textarea':
					$html = RefactoredFox_RestOAuth_Display::create_textarea($args);
				break;

				case 'radio':
					$html = RefactoredFox_RestOAuth_Display::create_radio($args);
				break;

				case 'range':
					$html = RefactoredFox_RestOAuth_Display::create_range($args);
				break;

				case 'date':
					$html = RefactoredFox_RestOAuth_Display::create_date($args);
				break;

				case 'custom_user':
					$html = RefactoredFox_RestOAuth_Display::create_users($args);
				break;

				case 'custom_post':
					$html = RefactoredFox_RestOAuth_Display::create_posts($args);
				break;

				default:
					$html = '';
				break;
			}

			return $html;

		}

		//Creates an html select field
		public static function create_dropdown( $args )
		{
			$default = !empty($args['default']) ? $args['default'] : 'Select Option';
			$values = is_array($args['values']) ? $args['values'] : explode(';',$args['values']);
			$values = array_map( 'RefactoredFox_RestOAuth_Display::clear_text', $values );

			$html = '<select id="'.$args['id'].'" name="'.$args['name'].'"';
			$html .= $args['class'] ? ' class="'.$args['class'].'"' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' ><option value="0">'.$default.'</option>';

			foreach( $args['options'] as $option ){
				$html .= '<option value="'.$option.'"';
				$html .= in_array($option, $values) ? ' selected="selected" >' : ' >';
				$html .= $option.'</option>';
			}

			$html .= '</select>';

			return $html;
		}

		//Creates an html select field
		public static function create_multidropdown( $args )
		{
			$values = is_array($args['values']) ? $args['values'] : explode(',',$args['values']);
			$values = array_map( 'RefactoredFox_RestOAuth_Display::clear_text', $values );

			$html = '<select multiple="multiple" id="'.$args['id'].'" name="'.$args['name'].'[]"';
			$html .= $args['class'] ? ' class="'.$args['class'].'"' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' >';

			foreach( $args['options'] as $option ){
				$html .= '<option value="'.$option.'"';
				$html .= in_array($option, $values) ? ' selected="selected" >' : ' >';
				$html .= $option.'</option>';
			}

			$html .= '</select>';

			return $html;
		}

		//Creates a an html checkbox with label
		public static function create_checkbox( $args )
		{
			$value = RefactoredFox_RestOAuth_Display::clear_text($args['values']);
			$html = '<input';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' type="checkbox" id="'.$args['id'].'" name="'.$args['name'].'" value="1"';
			$html .= !empty($value) ? ' checked="checked"' : '';
			$html .= ' />';

			return $html;
		}

		//Creates a single HTML text field
		public static function create_text( $args )
		{
			$values = RefactoredFox_RestOAuth_Display::clear_text($args['values']);
			$html = '<input type="text"';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' id="'.$args['id'].'" name="'.$args['name'].'" value="'.$values.'" />';

			return $html;
		}

		//Creates a double HTML date fields
		public static function create_range( $args )
		{
			$values = is_array($args['values']) ? $args['values'] : explode(',',$args['values']);

			$from = isset($values['from']) && !empty( $values['from'] ) ? date("m/d/Y", strtotime($values['from']) ) : '';
			$to = isset($values['to']) && !empty( $values['to'] ) ? date("m/d/Y", strtotime($values['to']) ) : '';

			$html = '<br/><input type="date"';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' id="'.$args['id'].'-from" name="'.$args['name'].'[from]" value="'.$from.'" />';

			$html .= ' to <input type="date"';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' id="'.$args['id'].'-to" name="'.$args['name'].'[to]" value="'.$to.'" />';

			return $html;
		}

		//Creates a single HTML text field
		public static function create_date( $args )
		{
			$values = date("m/d/y", strtotime($args['values']));
			$values = isset($args['values']) && !empty( $args['values'] ) ? date("m/d/Y", strtotime($args['values']) ) : '';
			$html = '<input type="date"';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' id="'.$args['id'].'" name="'.$args['name'].'" value="'.$values.'" />';

			return $html;
		}

		//Creates an HTML textbox field
		public static function create_textarea( $args )
		{
			$values = RefactoredFox_RestOAuth_Display::clear_text($args['values']);
			$html = '<textarea';
			$html .= $args['class'] ? ' class="'.$args['class'].'" ' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' id="'.$args['id'].'" name="'.$args['name'].'"';
			$html .= ' >'.$values.'</textarea>';

			return $html;
		}

		//Creates an html select field
		public static function create_radio( $args )
		{
			$values = RefactoredFox_RestOAuth_Display::clear_text($args['values']);
			$html = '';

			foreach( $args['options'] as $key => $option ){
				$html .= '<input type="radio" id="'.$args['id'].'-'.$key.'" name="'.$args['name'].'" value="'.$option.'"';
				$html .= $option == $values ? ' checked="checked" />' : ' />';
				$html .= $option.' ';
			}

			return $html;
		}

		//Creates an html select field
		public static function create_users( $args )
		{
			$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );

			$default = 'Select Member';
			$values = is_array($args['values']) ? $args['values'] : explode(';',$args['values']);
			$values = array_map( 'RefactoredFox_RestOAuth_Display::clear_text', $values );

			$html = '<select title="select users" id="'.$args['id'].'" name="'.$args['name'].'"';
			$html .= $args['class'] ? ' class="'.$args['class'].'"' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' ><option value="0">'.$default.'</option>';

			foreach( $users as $option ){
				$html .= '<option value="'.$option->ID.'"';
				$html .= in_array($option->ID, $values) ? ' selected="selected" >' : ' >';
				$html .= $option->display_name.'</option>';
			}

			$html .= '</select>';

			return $html;

		}

		//Creates an html select field
		public static function create_posts( $args )
		{
			$post_type = $args['options'];
			$posts = get_posts( array( 'posts_per_page' => -1, 'post_type' => $post_type ) );

			$default = 'Select Option';
			$values = is_array($args['values']) ? $args['values'] : explode(';',$args['values']);
			$values = array_map( 'RefactoredFox_RestOAuth_Display::clear_text', $values );

			$html = '<select title="select posts" id="'.$args['id'].'" name="'.$args['name'].'"';
			$html .= $args['class'] ? ' class="'.$args['class'].'"' : '';
			foreach( $args['data'] as $k => $v ){
				$html .= ' data-'.$k.'="'.$v.'"';
			}
			$html .= ' ><option value="0">'.$default.'</option>';

			foreach( $posts as $option ){
				$html .= '<option value="'.$option->ID.'"';
				$html .= in_array($option, $values) ? ' selected="selected" >' : ' >';
				$html .= $option->post_title.'</option>';
			}

			$html .= '</select>';

			return $html;

		}

	}

}
?>
