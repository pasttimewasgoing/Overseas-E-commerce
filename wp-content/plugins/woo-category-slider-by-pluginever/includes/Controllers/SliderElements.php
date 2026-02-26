<?php

namespace WooCommerceCategorySlider\Controllers;

defined( 'ABSPATH' ) || exit();

/**
 * Class SliderElements.
 *
 * @since 1.0.0
 * @package WooCommerceCategorySlider\Controllers
 */
class SliderElements {

	/**
	 * Renders an HTML Dropdown of years
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function year_dropdown( $args ) {
		$args       = wp_parse_args(
			$args,
			array(
				'years_before'     => 5,
				'years_after'      => 0,
				'selected'         => 0,
				'show_option_all'  => false,
				'show_option_none' => false,
			)
		);
		$current    = absint( gmdate( 'Y' ) );
		$start_year = $current - absint( $args['years_before'] );
		$end_year   = $current + absint( $args['years_after'] );
		$selected   = empty( $args['selected'] ) ? gmdate( 'Y' ) : $args['selected'];
		$options    = array();

		while ( $start_year <= $end_year ) {
			$options[ absint( $start_year ) ] = $start_year;
			++$start_year;
		}

		$args['selected'] = $selected;
		$args['options']  = $options;

		$output = self::select( $args );

		return $output;
	}

	/**
	 * HTML select input.
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function select( $args ) {
		$defaults = array(
			'options'          => array(),
			'name'             => '',
			'class'            => '',
			'id'               => '',
			'label'            => '',
			'selected'         => array(),
			'chosen'           => false,
			'wrapper_class'    => '',
			'placeholder'      => __( '- Please Select -', 'woo-category-slider-by-pluginever' ),
			'multiple'         => false,
			'show_option_all'  => _x( 'All', 'all dropdown items', 'woo-category-slider-by-pluginever' ),
			'show_option_none' => _x( 'None', 'no dropdown items', 'woo-category-slider-by-pluginever' ),
			'data'             => array(),
			'attrs'            => array(),
			'readonly'         => false,
			'required'         => false,
			'disabled'         => false,
			'option_disabled'  => false,
			'double_columns'   => true,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['multiple'] ) {
			$args['attrs']['multiple'] = 'multiple';
		}
		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		if ( $args['placeholder'] ) {
			$args['attrs']['placeholder'] = $args['placeholder'];
			$args['data']['placeholder']  = $args['placeholder'];
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		$option_disabled = '';
		if ( isset( $args['option_disabled'] ) && $args['option_disabled'] ) {
			$option_disabled = 'disabled';
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' ever-select-chosen';
			if ( is_rtl() ) {
				$args['class'] .= ' chosen-rtl';
			}
		}

		$name = empty( $args['multiple'] ) ? $args['name'] : "{$args['name']}[]";

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		$output = '';

		$args['wrapper_class'] .= ' ever-form-group';

		if ( $args['double_columns'] ) {
			$args['wrapper_class'] = ' ever-row ever-form-group';
		}

		$output .= '<div class="' . $args['wrapper_class'] . ' ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}

			if ( $args['double_columns'] ) {
				$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
			} else {
				$output .= '<label for="' . $args['id'] . '" class="ever-label">' . $label . '</label>';
			}
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );

		if ( $args['double_columns'] ) {
			$output .= '<div class="ever-col-1">:</div>';
			$output .= '<div class="ever-col-7">';
		}

		$output .= '<select name="' . $name . '" id="' . esc_attr( $args['id'] ) . '" class="ever-field ' . $class . '"' . $attributes . '>';

		if ( ! isset( $args['selected'] ) || ( is_array( $args['selected'] ) && empty( $args['selected'] ) ) || ! $args['selected'] ) {
			$selected = '';
		}

		if ( $args['placeholder'] && ! $args['chosen'] ) {
			$output .= '<option value="" >' . esc_html( $args['placeholder'] ) . '</option>';
		}

		if ( $args['show_option_all'] ) {
			if ( $args['multiple'] && ! empty( $args['selected'] ) ) {
				$selected = selected( true, in_array( 0, $args['selected'], true ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
		}

		if ( ! empty( $args['options'] ) ) {
			if ( $args['show_option_none'] ) {
				if ( $args['multiple'] ) {
					$selected = selected( true, in_array( - 1, $args['selected'], true ), false );
				} elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) && ! empty( $args['selected'] ) ) {
					$selected = selected( $args['selected'], - 1, false );
				}
				$output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
			}

			foreach ( $args['options'] as $key => $option ) {
				if ( $args['multiple'] && is_array( $args['selected'] ) ) {
					$selected = selected( true, in_array( (string) $key, $args['selected'], true ), false );
				} elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) ) {
					$selected = selected( $args['selected'], $key, false );
				}

				$output .= sprintf( '<option value="%s" %s %s>' . esc_html( $option ) . '</option>', esc_attr( $key ), $selected, $option_disabled, esc_html( $option ) );
			}
		}

		$output .= '</select>';
		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="ever-field-description">' . wp_kses_post( $args['desc'] ) . '</span>';
		}
		if ( $args['double_columns'] ) {
			$output .= '</div>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Format html data attributes.
	 *
	 * @param array $data Data attributes.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected static function get_data_attributes( $data ) {
		$data_elements = '';
		foreach ( $data as $key => $value ) {
			$data_elements .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $data_elements;
	}

	/**
	 * Format html attributes
	 *
	 * @param array $data HTML attributes.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected static function get_attributes( $data ) {
		$data_elements = '';
		foreach ( $data as $key => $value ) {
			$data_elements .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $data_elements;
	}

	/**
	 * Renders an HTML Dropdown of all the Users.
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function user_dropdown( $args = array() ) {

		$defaults = array(
			'name'        => 'users',
			'id'          => 'users',
			'placeholder' => __( 'Select a User', 'woo-category-slider-by-pluginever' ),
			'number'      => 30,
			'data'        => array(
				'search-type'        => 'user',
				'search-placeholder' => __( 'Type to search all Users', 'woo-category-slider-by-pluginever' ),
			),
		);

		$args = wp_parse_args( $args, $defaults );

		$user_args = array(
			'number' => $args['number'],
		);
		$users     = get_users( $user_args );
		$options   = array();

		if ( $users ) {
			foreach ( $users as $user ) {
				$options[ $user->ID ] = esc_html( $user->display_name );
			}
		} else {
			$options[0] = __( 'No users found', 'woo-category-slider-by-pluginever' );
		}

		// If a selected user has been specified, we need to ensure it's in the initial list of user displayed.
		if ( ! empty( $args['selected'] ) ) {

			if ( ! array_key_exists( $args['selected'], $options ) ) {

				$user = get_userdata( $args['selected'] );

				if ( $user ) {

					$options[ absint( $args['selected'] ) ] = esc_html( $user->display_name );

				}
			}
		}

		$args['options'] = $options;
		$output          = self::select( $args );

		return $output;
	}

	/**
	 * Renders an HTML textarea.
	 *
	 * @param array $args Arguments for the textarea.
	 *
	 * @since 1.9
	 * @return string textarea
	 */
	public static function textarea( $args = array() ) {
		$defaults = array(
			'name'        => 'textarea',
			'value'       => null,
			'label'       => null,
			'desc'        => null,
			'class'       => '',
			'disabled'    => false,
			'readonly'    => false,
			'placeholder' => null,
			'data'        => array(),
			'attrs'       => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$class  = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$class .= ' ever-field ever-field-textarea';

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		if ( $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		if ( $args['placeholder'] ) {
			$args['attrs']['placeholder'] = $args['placeholder'];
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		$args['data']['gramm_editor'] = 'false';

		$output = '';

		$output .= '<div class="ever-form-group ever-row ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}
			$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );
		$output     .= '<div class="ever-col-1">:</div>';
		$output     .= '<div class="ever-col-7">';
		$output     .= '<textarea name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"' . $attributes . '>' . $args['value'] . '</textarea>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="ever-field-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= '</span>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders an HTML Checkbox.
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function checkbox( $args = array() ) {
		$defaults = array(
			'name'     => 'checkbox',
			'value'    => null,
			'label'    => null,
			'desc'     => null,
			'class'    => '',
			'disabled' => false,
			'readonly' => false,
			'data'     => array(),
			'attrs'    => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= ' ever-checkbox';
		$class          = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		if ( $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		$output = '';

		$output .= '<div class="ever-row ever-form-group ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}
			$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );

		$output .= '<div class="ever-col-1">:</div>';

		$output .= '<div class="ever-col-7">';

		$output .= '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" ' . checked( 1, $args['value'], false ) . ' ' . $attributes . ' />';

		$output .= '</span>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders an HTML Switcher
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function switcher( $args = array() ) {
		$defaults = array(
			'name'           => 'switcher',
			'value'          => '',
			'label'          => 'Switcher',
			'desc'           => null,
			'class'          => '',
			'disabled'       => false,
			'readonly'       => false,
			'double_columns' => true,
			'required'       => false,
			'data'           => array(),
			'attrs'          => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= 'ever-switcher';
		$class          = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		$output = '';

		$output .= '<div class="ever-row ever-form-group ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}
			$output
				.= '<div class="ever-switch">
							<label for="' . $args['id'] . '" class="ever-col-4 ever-label ever-switch-label">' . $label . '</label>';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );

		$checked = 'on' === $args['value'] ? 'checked="checked" ' : '';

		$output .= '<div class="ever-col-1">:</div><div class="ever-col-7 switch-container">';

		$output
			.= '<label class="switch" for="' . $args['id'] . '" class="ever-label">
					  <input type="checkbox" name="' . esc_attr( $args['name'] ) . '" value= "' . $args['value'] . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . ' ' . esc_attr( $args['name'] ) . '" ' . $checked . ' ' . $attributes . '  />
					  <span class="slider round"></span>
					  <span class="switch-text"></span>
					</label>

					';

		if ( $args['desc'] && $args['double_columns'] ) {
			$output .= '<br /><span class="ever-field-description">' . sanitize_text_field( $args['desc'] ) . '</span>';
		}

		$output .= '</div>';

		if ( $args['desc'] && ! $args['double_columns'] ) {
			$output .= '<div class="ever-col-12 switch-desc"><span class="ever-field-description">' . sanitize_text_field( $args['desc'] ) . '</span></div>';
		}

		$output .= '</div></div>';

		return $output;
	}

	/**
	 * HTML Checkboxes.
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function checkboxes( $args ) {
		$defaults = array(
			'name'          => 'checkbox',
			'value'         => null,
			'label'         => null,
			'desc'          => null,
			'class'         => '',
			'wrapper_class' => '',
			'disabled'      => false,
			'readonly'      => false,
			'options'       => array(),
			'data'          => array(),
			'attrs'         => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= ' ever-checkbox';
		$class          = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		$args['wrapper_class'] .= ' ever-form-group';

		if ( $args['double_columns'] ) {
			$args['wrapper_class'] = ' ever-row';
		}

		$output = '';

		$output .= '<div class="' . sanitize_html_class( $args['wrapper_class'] ) . ' ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}

			if ( $args['double_columns'] ) {
				$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
			}

			if ( ! $args['double_columns'] ) {
				$output .= '<label for="' . $args['id'] . '" class="ever-label">' . $label . '</label>';
			}
		}

		if ( $args['double_columns'] ) {
			$output .= '<div class="ever-col-1">:</div>';
			$output .= '<div class="ever-col-7">';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );
		$inline      = ! empty( $args['inline'] ) ? '-inline' : '';

		$value = is_array( $args['value'] ) ? $args['value'] : array( $args['value'] );

		foreach ( $args['options'] as $key => $label ) {
			$checked = in_array( $key, $value, true ) ? ' checked="checked" ' : '';
			$output .= '<div class="ever-checkbox' . $inline . '">';
			$output .= '<label class="ever-label">';
			$output .= '<input type="checkbox" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" class="' . $class . ' ' . esc_attr( $key ) . '" ' . $checked . ' ' . $attributes . ' />';
			$output .= esc_html( $label );
			$output .= '</label>';
			$output .= '</div>';
		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * HTMl radio buttons.
	 *
	 * @param array $args Attribute arguments.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function radios( $args ) {
		$defaults = array(
			'name'     => 'checkbox',
			'value'    => null,
			'label'    => null,
			'desc'     => null,
			'class'    => '',
			'disabled' => false,
			'readonly' => false,
			'options'  => array(),
			'data'     => array(),
			'attrs'    => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= ' ever-checkbox';
		$class          = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );

		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( $args['required'] ) {
			$args['attrs']['required'] = 'required';
		}

		$args['wrapper_class'] .= ' ever-form-group';
		if ( $args['double_columns'] ) {
			$args['wrapper_class'] = ' ever-row';
		}

		$output = '';

		$output .= '<div class=" ' . sanitize_html_class( $args['wrapper_class'] ) . ' ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}

			if ( $args['double_columns'] ) {
				$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
			}

			if ( ! $args['double_columns'] ) {
				$output .= '<label for="' . $args['id'] . '" class="ever-label">' . $label . '</label>';
			}
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );
		$inline      = ! empty( $args['inline'] ) ? '-inline' : '';

		foreach ( $args['options'] as $key => $label ) {
			$output .= '<div class="ever-radio' . $inline . '">';
			$output .= '<label class="ever-label">';
			$output .= '<input type="radio" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $key ) . '" class="' . $class . ' ' . esc_attr( $key ) . '" ' . checked( $key, $args['value'], false ) . ' ' . $attributes . ' />';
			$output .= esc_html( $label );
			$output .= '</label>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders a date picker.
	 *
	 * @param array $args Arguments for the text field.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function date_field( $args = array() ) {

		if ( empty( $args['class'] ) ) {
			$args['class'] = 'ever-date-picker';
		} elseif ( ! strpos( $args['class'], 'ever-date-picker' ) ) {
			$args['class'] .= ' ever-date-picker';
		}

		return self::input( $args );
	}

	/**
	 * Renders an HTML Text field.
	 *
	 * @param array $args Arguments for the text field.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function input( $args = array() ) {

		$defaults = array(
			'id'             => '',
			'name'           => '',
			'value'          => '',
			'type'           => 'text',
			'label'          => '',
			'desc'           => '',
			'placeholder'    => '',
			'wrapper_class'  => '',
			'class'          => 'regular-text',
			'disabled'       => false,
			'double_columns' => true,
			'autocomplete'   => 'false',
			'data'           => array(),
			'attrs'          => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= ' ever-field';

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( ! empty( $args['required'] ) ) {
			$args['attrs']['required'] = 'required';
		}

		if ( $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}
		if ( $args['autocomplete'] ) {
			$args['attrs']['autocomplete'] = esc_attr( $args['autocomplete'] );
		}

		if ( $args['placeholder'] ) {
			$args['attrs']['placeholder'] = $args['placeholder'];
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		$args['wrapper_class'] .= ' ever-form-group ';

		if ( $args['double_columns'] ) {
			$args['wrapper_class'] .= ' ever-row';
		}

		$output = '';

		$output .= '<div class="' . $args['wrapper_class'] . ' ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( ! empty( $args['required'] ) && true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}

			if ( $args['double_columns'] ) {
				$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
			} else {
				$output .= '<label for="' . $args['id'] . '" class="ever-label">' . $label . '</label>';
			}
		}

		if ( $args['double_columns'] ) {
			$output .= '<div class="ever-col-1">:</div>';
			$output .= '<div class="ever-col-7">';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );

		$output .= '<input type="' . esc_attr( $args['type'] ) . '" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $args['value'] ) . '" class="' . $class . '" ' . $attributes . ' />';
		if ( ! empty( $args['after'] ) ) {
			$output .= $args['after'];
		}
		if ( $args['desc'] ) {
			$output .= '<span class="ever-field-description">' . sanitize_text_field( $args['desc'] ) . '</span>';
		}

		if ( $args['double_columns'] ) {
			$output .= '</div>';
		}
		$output .= '</div><!-- .ever-form-group-->';

		return $output;
	}

	/**
	 * Renders an HTML Text field.
	 *
	 * @param array $args Arguments for the text field.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function colorpicker( $args = array() ) {

		$defaults = array(
			'id'             => '',
			'name'           => '',
			'value'          => '',
			'alpha'          => true,
			'type'           => 'text',
			'required'       => false,
			'label'          => '',
			'desc'           => '',
			'placeholder'    => '',
			'wrapper_class'  => '',
			'class'          => 'ever-colorpicker',
			'disabled'       => false,
			'double_columns' => true,
			'autocomplete'   => 'false',
			'data'           => array(),
			'attrs'          => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] .= ' ever-field';

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		if ( empty( $args['id'] ) ) {
			$args['id'] = esc_attr( wc_slider_sanitize_key( str_replace( '-', '_', $args['name'] ) ) );
		}

		if ( ! empty( $args['required'] ) ) {
			$args['attrs']['required'] = 'required';
		}

		if ( $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}
		if ( $args['autocomplete'] ) {
			$args['attrs']['autocomplete'] = esc_attr( $args['autocomplete'] );
		}

		if ( $args['placeholder'] ) {
			$args['attrs']['placeholder'] = $args['placeholder'];
		}

		if ( isset( $args['readonly'] ) && $args['readonly'] ) {
			$args['attrs']['readonly'] = 'readonly';
		}

		if ( isset( $args['disabled'] ) && $args['disabled'] ) {
			$args['attrs']['disabled'] = 'disabled';
		}

		if ( isset( $args['alpha'] ) && $args['alpha'] ) {
			$args['data']['alpha'] = 'true';
		}

		$args['wrapper_class'] .= ' ever-form-group ever-colorpicker-container ';

		if ( $args['double_columns'] ) {
			$args['wrapper_class'] .= ' ever-row';
		}

		$output = '';

		$output .= '<div class="' . $args['wrapper_class'] . ' ' . wc_slider_sanitize_key( $args['name'] ) . '_field">';

		if ( ! empty( $args['label'] ) ) {
			$label = wp_kses_post( $args['label'] );
			if ( ! empty( $args['required'] ) && true === $args['required'] ) {
				$label .= ' <span class="ever-required-field">*</span>';
			}

			if ( $args['double_columns'] ) {
				$output .= '<div class="ever-col-4"><label for="' . $args['id'] . '" class="ever-label">' . $label . '</label></div>';
			} else {
				$output .= '<label for="' . $args['id'] . '" class="ever-label">' . $label . '</label>';
			}
		}

		if ( $args['double_columns'] ) {
			$output .= '<div class="ever-col-1">:</div>';
			$output .= '<div class="ever-col-7">';
		}

		$attributes  = '';
		$attributes .= self::get_data_attributes( $args['data'] );
		$attributes .= self::get_attributes( $args['attrs'] );

		$output .= '<input type="' . esc_attr( $args['type'] ) . '" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $args['value'] ) . '" class="' . $class . '" ' . $attributes . ' />';
		if ( ! empty( $args['after'] ) ) {
			$output .= $args['after'];
		}
		if ( $args['desc'] ) {
			$output .= '<span class="ever-field-description">' . sanitize_text_field( $args['desc'] ) . '</span>';
		}

		if ( $args['double_columns'] ) {
			$output .= '</div>';
		}
		$output .= '</div><!-- .ever-form-group-->';

		return $output;
	}
}
