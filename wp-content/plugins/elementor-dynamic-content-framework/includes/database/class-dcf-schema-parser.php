<?php
/**
 * Schema Parser Class
 *
 * Parses and validates JSON Schema strings.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/database
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Schema_Parser class.
 *
 * Handles parsing of Schema JSON strings into PHP arrays.
 */
class DCF_Schema_Parser {

	/**
	 * Last JSON error message.
	 *
	 * @var string
	 */
	private static $last_error = '';

	/**
	 * Parse Schema JSON string.
	 *
	 * @param string $json Schema JSON string.
	 * @return array|WP_Error Array on success, WP_Error on failure.
	 */
	public static function parse( string $json ) {
		// Reset last error.
		self::$last_error = '';

		// Check if JSON is valid.
		if ( ! self::is_valid_json( $json ) ) {
			return new WP_Error(
				'invalid_json',
				sprintf(
					/* translators: %s: JSON error message */
					__( 'Invalid JSON: %s', 'elementor-dynamic-content-framework' ),
					self::$last_error
				)
			);
		}

		// Decode JSON.
		$schema = json_decode( $json, true );

		// Ensure it's an array.
		if ( ! is_array( $schema ) ) {
			return new WP_Error(
				'invalid_schema',
				__( 'Schema must be a JSON array', 'elementor-dynamic-content-framework' )
			);
		}

		return $schema;
	}

	/**
	 * Check if JSON is valid.
	 *
	 * @param string $json JSON string to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid_json( string $json ): bool {
		// Try to decode JSON.
		json_decode( $json );

		// Get last JSON error.
		$error = json_last_error();

		// If no error, JSON is valid.
		if ( JSON_ERROR_NONE === $error ) {
			return true;
		}

		// Store error message.
		self::$last_error = self::get_json_error_message( $error );

		return false;
	}

	/**
	 * Get last JSON error message.
	 *
	 * @return string Error message.
	 */
	public static function get_last_error(): string {
		return self::$last_error;
	}

	/**
	 * Get JSON error message from error code.
	 *
	 * @param int $error_code JSON error code.
	 * @return string Error message.
	 */
	private static function get_json_error_message( int $error_code ): string {
		switch ( $error_code ) {
			case JSON_ERROR_DEPTH:
				return __( 'Maximum stack depth exceeded', 'elementor-dynamic-content-framework' );
			case JSON_ERROR_STATE_MISMATCH:
				return __( 'Underflow or the modes mismatch', 'elementor-dynamic-content-framework' );
			case JSON_ERROR_CTRL_CHAR:
				return __( 'Unexpected control character found', 'elementor-dynamic-content-framework' );
			case JSON_ERROR_SYNTAX:
				return __( 'Syntax error, malformed JSON', 'elementor-dynamic-content-framework' );
			case JSON_ERROR_UTF8:
				return __( 'Malformed UTF-8 characters', 'elementor-dynamic-content-framework' );
			default:
				return __( 'Unknown JSON error', 'elementor-dynamic-content-framework' );
		}
	}
}
