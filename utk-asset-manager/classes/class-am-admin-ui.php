<?php
/**
 * Methods to repeatably construct admin form elements
 *
 * @since      1.0.0
 * @subpackage Admin_UI
 * @author     John Galyon
 * @package    COE_AM
 * @license    GPL-2.0+
 */

/**
 *  <div class="row form-group">
 *      <label for="" class="col-sm-3 col-form-label">
 *          <strong></strong><span></span>
 *      </label>
 *      <div class="com-sm-9">
 *          <input type="text">
 *          <p></p>
 *          <span class="help-text"></span>
 *      </div>
 *  </div>
 */
class AM_Admin_UI {

	/**
	 * Create the opening row tag
	 *
	 * @since 1.0.0
	 * @return string opening div.row.form-group
	 */
	public static function make_row(): string {
		return '<div class="row form-group">';
	}

	/**
	 * Output the opening column div for the form element
	 *
	 * @since 0.0.1
	 *
	 * @param false $offset
	 *
	 * @return string
	 */
	public static function make_col( $offset = false ): string {

		/**
		 * We do not want to chance getting a null value for this,
		 * so we allow `filter_var` to default to a false value
		 * on failure, such as if it got bad data.
		 *
		 * @since 0.0.1
		 *
		 * @uses  filter_var
		 * @uses  FILTER_VALIDATE_BOOLEAN
		 *
		 * @returns bool
		 */
		$offset = ( filter_var( $offset, FILTER_VALIDATE_BOOLEAN ) ) ? ' offset-sm-3' : '';

		return '<div class="col-sm-9' . $offset . '">';

	}

	/**
	 * Create the closing row tag
	 *
	 * @since 1.0.0
	 * @return string closing div.row
	 */
	public static function make_closing_divs(): string {
		return '</div></div>';
	}

	/**
	 * Create the opening label tag and, depending on
	 * the parameter values, and add a span containing a colored asterisk.
	 *
	 * @since 0.0.1
	 *
	 * @param string $label_for  name of the field the label is associated with
	 * @param string $label_text the text the label will display
	 * @param false  $required   whether the associated field is required
	 *
	 * @return string html label element
	 */
	public static function make_label( $label_for = '', $label_text = '', $required = false ): string {

		$classes = [ 'col-sm-3', 'col-form-label' ];
		$text    = wp_strip_all_tags( $label_text );
		$span    = ( $required ) ? '<span class="required">*</span>' : '';

		return sprintf( '<label for="%s" class="%s">%s %s</label>', $label_for, implode( ' ', $classes ), $text, $span );

	}

	/**
	 * output the field description
	 *
	 * @since 0.0.1
	 *
	 * @param string $name name of the associated field
	 * @param string $desc the string of text to be output
	 *
	 * @return string html span element
	 */
	public static function make_description( $name = '', $desc = '' ): string {
		return '<span class="' . $name . '-help form-text text-muted">' . $desc . '</span>';
	}

	/**
	 * Set the default parameters for all fields.
	 *
	 * @since 0.1.1
	 *
	 * @param array $additions array of additional settings, such a selections or checkboxes
	 *
	 * @return array
	 */
	public static function set_default_parameters( $additions = [] ): array {
		return array_merge( [
			'classes'     => true,
			'desc'        => '',
			'hidden'      => false,
			'name'        => '',
			'maxlength'   => '',
			'offset'      => false,
			'placeholder' => '',
			'required'    => false,
			'rows'        => 3,
			'selections'  => [],
			'value'       => '',
			'visible'     => true,
		], $additions );
	}

	/**
	 * Construct the attribute string for all fields
	 *
	 * @since 0.0.1
	 *
	 * @param array $args attribute arguments
	 *
	 * @return string
	 */
	public static function make_attributes( $args = [] ): string {

		// Get the default parameters that were set in this class
		$defaults = self::set_default_parameters();

		/**
		 * Merge the supplied values with our our default attributes
		 *
		 * @since 0.0.1
		 * @uses  \wp_parse_args()
		 */
		$args = wp_parse_args( $args, $defaults );

		// Build our attributes string
		$atts = '';
		$atts .= ( empty( $args['classes'] ) ) ? '' : 'class="form-control form-control-sm';
		$atts .= ( empty( $args['desc'] ) ) ? '' : 'aria-describedby="' . $args['name'] . '-description" ';
		$atts .= ( empty( $args['name'] ) ) ? '' : 'id="' . $args['name'] . '" name="' . $args['name'] . '" ';
		$atts .= ( empty( $args['placeholder'] ) ) ? '' : 'placeholder="' . $args['placeholder'] . '" ';
		$atts .= ( empty( $args['required'] ) ) ? '' : 'aria-required="required" required ';
		$atts .= ( empty( $args['rows'] ) ) ? '' : 'rows="' . $args['rows'] . '" ';
		$atts .= ( empty( $args['value'] ) ) ? '' : 'value="' . $args['value'] . '" ';

		return $atts;
	}

	/**
	 * Create the text input
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string input element
	 */
	public function make_text_field( $args = [] ): string {
		$defaults = self::set_default_parameters();
		$atts     = self::make_attributes( $args );
		$args     = wp_parse_args( $args, $defaults );

		$field = '';
		$field .= self::make_row();
		$field .= self::make_label( $args['name'], $args['label_text'], $args['required'] );
		$field .= self::make_col( $args['offset'] );
		$field .= "<input type='text' {$atts}>";
		$field .= self::make_description( $args['name'], $args['desc'] );
		$field .= self::make_closing_divs();

		return $field;

	}

	/**
	 * Create the select field
	 *
	 * @since 0.0.1
	 *
	 * @param array $args supplied arguments array
	 *
	 * @return string select element
	 */
	public function make_select_field( $args = [] ): string {
		$defaults = self::set_default_parameters();
		$atts     = self::make_attributes( $args );
		$args     = wp_parse_args( $args, $defaults );

		$field = '';
		$field .= self::make_row();
		$field .= self::make_label( $args['name'], $args['label_text'], $args['required'] );
		$field .= self::make_col( $args['offset'] );
		$field .= "<select {$atts}>";

		if ( ! empty( $args['selections']['options'] && is_array( $args['selections']['options'] ) ) ) {
			foreach ( $args['selections']['options'] as $option ) {
				$selected    = $args['selections']['selected'];
				$is_selected = '';

				if ( is_numeric( $selected ) ) {
					$selected = coerce_bool( $selected );
				}

				if ( ! empty( $selected ) && is_bool( $selected ) ) {
					$is_selected = 'selected="selected"';
				} else {
					if ( array_key_exists( 'default', $option ) && ! empty( $option['default'] ) ) {
						if ( empty( $selected ) ) {
							$is_selected = 'selected="selected"';
						}
					}
				}

				if ( ! is_numeric( $selected ) && ( ! empty( $selected ) && $selected === $option['value'] ) ) {
					$is_selected = 'selected="selected"';
				}

				$field .= '<option value="' . $option['value'] . '" ' . $is_selected . '>' . $option['text'] . '</option>';
			}
		}

		$field .= '</select>';
		$field .= self::make_description( $args['name'], $args['desc'] );
		$field .= self::make_closing_divs();

		return $field;

	}

	public function make_checkboxes( $args = [] ): string {
		$defaults = self::set_default_parameters( $args );
		$args     = wp_parse_args( $args, $defaults );

		$field = '';
		$field .= self::make_row();
		//$field .= self::make_label( $args['name'], $args['label_text'], $args['required'] );

		$field .= "<span class='col-sm-3 checkbox-span'>{$args['label_text']}</span>";

		$field .= self::make_col( $args['offset'] );

		if ( ! empty( $args['checkboxes']['boxes'] ) && is_array( $args['checkboxes']['boxes'] ) ) {

			foreach ( $args['checkboxes']['boxes'] as $box ) {

				$field .= '<div class="form-check">';
				$field .= "<input type='checkbox' class='form-check-input' id='{$args['name']}'>";
				$field .= "<label for='{$args['name']}' class='mt-1 ml-2'>{$box['label']}</label>";
				$field .= '</div>';

			}

		}
		$field .= self::make_closing_divs();

		return $field;

	}

	/**
	 * Create and output a textarea field
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return string textarea field
	 */
	public function make_textarea_field( $args = array() ) {

		$defaults = self::set_default_parameters();
		$atts     = self::make_attributes( $args );
		$args     = wp_parse_args( $args, $defaults );

		$field = '';
		$field .= self::make_row();
		$field .= self::make_label( $args['name'], $args['label_text'], $args['required'] );
		$field .= self::make_col( $args['offset'] );
		$field .= "<textarea {$atts}></textarea>";
		$field .= self::make_description( $args['name'], $args['desc'] );
		$field .= self::make_closing_divs();

		return $field;

	}

	/**
	 * Output an input group combo with a textarea and a button
	 *
	 * @since 0.0.1
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function make_input_group( $args = [] ): string {

		$defaults = self::set_default_parameters( [
			'btn_text' => '',
		] );
		$args     = wp_parse_args( $args, $defaults );

		$field = '';
		$field .= self::make_row();
		$field .= self::make_label( $args['name'], $args['label_text'], $args['required'] );
		$field .= self::make_col( $args['offset'] );
		$field .= '<div class="input-group">';
		$field .= '<input type="text" class="form-control form-control-sm" id="' . $args['name'] . '" name="' . $args['name'] . '" aria-label="' . $args['label_text'] . '" aria-describedby="' . $args['name'] . '-btn">';
		$field .= '<div class="input-group-addon">';
		$field .= '<button type="button" class="button button-primary input-group-button" id="' . $args['name'] . '-btn">' . $args['btn_text'] . '</button>';
		$field .= self::make_closing_divs();
		$field .= self::make_description( $args['name'], $args['desc'] );
		$field .= self::make_closing_divs();

		return $field;

	}


}
