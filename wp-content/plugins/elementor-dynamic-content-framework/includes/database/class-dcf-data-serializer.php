<?php
/**
 * Data Serializer Class
 *
 * Serializes and deserializes Content Item data.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/database
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Data_Serializer class.
 *
 * Handles serialization and deserialization of Content Item data arrays.
 */
class DCF_Data_Serializer {

	/**
	 * Serialize content item data to JSON.
	 *
	 * @param array $data Data array to serialize.
	 * @return string|false JSON string on success, false on failure.
	 */
	public static function serialize( array $data ) {
		// Sanitize all values in the data array.
		$sanitized_data = self::sanitize_data_recursive( $data );

		// Set JSON encoding options.
		$options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

		// Encode to JSON.
		$json = wp_json_encode( $sanitized_data, $options );

		// Log error if encoding failed.
		if ( false === $json ) {
			if ( class_exists( 'DCF_Logger' ) ) {
				DCF_Logger::error(
					'Failed to serialize content item data',
					array(
						'data'  => $data,
						'error' => json_last_error_msg(),
					)
				);
			} else {
				// Fallback to error_log if logger not available.
				error_log( 'DCF: Failed to serialize content item data: ' . json_last_error_msg() );
			}
			return false;
		}

		return $json;
	}

	/**
	 * Deserialize JSON to content item data array.
	 *
	 * @param string $json JSON string to deserialize.
	 * @return array Data array on success, empty array on failure.
	 */
	public static function deserialize( string $json ): array {
		// Handle empty string.
		if ( empty( $json ) ) {
			return array();
		}

		// Decode JSON.
		$data = json_decode( $json, true );

		// Check for JSON errors.
		if ( null === $data && JSON_ERROR_NONE !== json_last_error() ) {
			if ( class_exists( 'DCF_Logger' ) ) {
				DCF_Logger::error(
					'Failed to deserialize content item data',
					array(
						'json'  => $json,
						'error' => json_last_error_msg(),
					)
				);
			} else {
				// Fallback to error_log if logger not available.
				error_log( 'DCF: Failed to deserialize content item data: ' . json_last_error_msg() );
			}
			return array();
		}

		// Ensure we return an array.
		if ( ! is_array( $data ) ) {
			return array();
		}

		return $data;
	}

	/**
	 * Sanitize value handling special characters and Unicode.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	public static function sanitize_value( $value ) {
		// Handle null values.
		if ( null === $value ) {
			return null;
		}

		// Handle arrays recursively.
		if ( is_array( $value ) ) {
			return self::sanitize_data_recursive( $value );
		}

		// Handle objects (convert to array first).
		if ( is_object( $value ) ) {
			return self::sanitize_data_recursive( (array) $value );
		}

		// Handle boolean values.
		if ( is_bool( $value ) ) {
			return $value;
		}

		// Handle numeric values.
		if ( is_numeric( $value ) ) {
			return $value;
		}

		// Handle string values.
		if ( is_string( $value ) ) {
			// Preserve WordPress shortcodes.
			// Shortcodes are in the format [shortcode] or [shortcode attr="value"]
			// We need to preserve them as-is without escaping.
			
			// Check if the string contains shortcodes.
			if ( self::contains_shortcode( $value ) ) {
				// For strings with shortcodes, we only normalize whitespace
				// but preserve the shortcode syntax.
				return $value;
			}

			// For regular strings, ensure proper UTF-8 encoding.
			// mb_convert_encoding ensures the string is valid UTF-8.
			$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );

			// Remove any null bytes.
			$value = str_replace( "\0", '', $value );

			return $value;
		}

		// Return value as-is for other types.
		return $value;
	}

	/**
	 * Recursively sanitize data array.
	 *
	 * @param array $data Data array to sanitize.
	 * @return array Sanitized data array.
	 */
	private static function sanitize_data_recursive( array $data ): array {
		$sanitized = array();

		foreach ( $data as $key => $value ) {
			// Sanitize the key.
			$sanitized_key = sanitize_key( $key );

			// Sanitize the value.
			$sanitized[ $sanitized_key ] = self::sanitize_value( $value );
		}

		return $sanitized;
	}

	/**
	 * Check if a string contains WordPress shortcodes.
	 *
	 * @param string $content Content to check.
	 * @return bool True if contains shortcodes, false otherwise.
	 */
	private static function contains_shortcode( string $content ): bool {
		// Check for shortcode pattern: [shortcode] or [shortcode attr="value"]
		// This regex matches WordPress shortcode syntax.
		return (bool) preg_match( '/\[[\w\-]+(?:\s+[^\]]+)?\]/', $content );
	}
}
