<?php
/**
 * Schema Validator Class
 *
 * Validates Schema arrays against field type specifications.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/database
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Schema_Validator class.
 *
 * Validates Schema definitions and field configurations.
 */
class DCF_Schema_Validator {

	/**
	 * Supported field types.
	 *
	 * @var array
	 */
	private static $supported_field_types = array(
		'text',
		'textarea',
		'image',
		'video',
		'url',
		'icon',
		'gallery',
		'repeater',
	);

	/**
	 * Validate Schema array.
	 *
	 * @param array $schema Schema array to validate.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function validate( array $schema ) {
		// Schema must not be empty.
		if ( empty( $schema ) ) {
			return new WP_Error(
				'empty_schema',
				__( 'Schema cannot be empty', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate each field.
		$field_names = array();
		foreach ( $schema as $index => $field ) {
			// Validate field structure.
			$validation = self::validate_field( $field );
			if ( is_wp_error( $validation ) ) {
				return new WP_Error(
					'invalid_field',
					sprintf(
						/* translators: 1: field index, 2: error message */
						__( 'Field at index %1$d is invalid: %2$s', 'elementor-dynamic-content-framework' ),
						$index,
						$validation->get_error_message()
					)
				);
			}

			// Check for duplicate field names.
			if ( isset( $field['name'] ) ) {
				if ( in_array( $field['name'], $field_names, true ) ) {
					return new WP_Error(
						'duplicate_field_name',
						sprintf(
							/* translators: %s: field name */
							__( 'Duplicate field name: %s', 'elementor-dynamic-content-framework' ),
							$field['name']
						)
					);
				}
				$field_names[] = $field['name'];
			}
		}

		return true;
	}

	/**
	 * Validate single field definition.
	 *
	 * @param array $field Field definition to validate.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function validate_field( array $field ) {
		// Field must have a type.
		if ( ! isset( $field['type'] ) ) {
			return new WP_Error(
				'missing_field_type',
				__( 'Field must have a type', 'elementor-dynamic-content-framework' )
			);
		}

		// Field type must be valid.
		if ( ! self::is_valid_field_type( $field['type'] ) ) {
			return new WP_Error(
				'invalid_field_type',
				sprintf(
					/* translators: %s: field type */
					__( 'Invalid field type: %s', 'elementor-dynamic-content-framework' ),
					$field['type']
				)
			);
		}

		// Field must have a name.
		if ( ! isset( $field['name'] ) || empty( $field['name'] ) ) {
			return new WP_Error(
				'missing_field_name',
				__( 'Field must have a name', 'elementor-dynamic-content-framework' )
			);
		}

		// Field must have a label.
		if ( ! isset( $field['label'] ) || empty( $field['label'] ) ) {
			return new WP_Error(
				'missing_field_label',
				__( 'Field must have a label', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate repeater fields.
		if ( 'repeater' === $field['type'] ) {
			// Repeater must have sub_fields.
			if ( ! isset( $field['sub_fields'] ) || ! is_array( $field['sub_fields'] ) ) {
				return new WP_Error(
					'missing_sub_fields',
					__( 'Repeater field must have sub_fields', 'elementor-dynamic-content-framework' )
				);
			}

			// Check repeater depth.
			if ( ! self::check_repeater_depth( $field, 0 ) ) {
				return new WP_Error(
					'repeater_depth_exceeded',
					__( 'Repeater nesting depth cannot exceed 3 levels', 'elementor-dynamic-content-framework' )
				);
			}

			// Validate sub-fields.
			foreach ( $field['sub_fields'] as $sub_field ) {
				$validation = self::validate_field( $sub_field );
				if ( is_wp_error( $validation ) ) {
					return $validation;
				}
			}
		}

		return true;
	}

	/**
	 * Check if field type is valid.
	 *
	 * @param string $type Field type to check.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid_field_type( string $type ): bool {
		return in_array( $type, self::get_supported_field_types(), true );
	}

	/**
	 * Get supported field types.
	 *
	 * @return array Array of supported field types.
	 */
	public static function get_supported_field_types(): array {
		/**
		 * Filter supported field types.
		 *
		 * @param array $field_types Array of supported field types.
		 */
		return apply_filters( 'dcf_field_types', self::$supported_field_types );
	}

	/**
	 * Check repeater nesting depth.
	 *
	 * @param array $field         Field definition.
	 * @param int   $current_depth Current nesting depth.
	 * @return bool True if depth is valid, false otherwise.
	 */
	public static function check_repeater_depth( array $field, int $current_depth = 0 ): bool {
		// Maximum depth is 3 (0, 1, 2 are valid depths).
		if ( $current_depth > 3 ) {
			return false;
		}

		// If not a repeater, depth is valid.
		if ( ! isset( $field['type'] ) || 'repeater' !== $field['type'] ) {
			return true;
		}

		// Check sub-fields.
		if ( isset( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
			foreach ( $field['sub_fields'] as $sub_field ) {
				if ( ! self::check_repeater_depth( $sub_field, $current_depth + 1 ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
