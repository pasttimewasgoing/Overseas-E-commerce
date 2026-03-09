<?php
/**
 * Schema Printer Class
 *
 * Serializes Schema arrays to JSON strings.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/database
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Schema_Printer class.
 *
 * Handles serialization of Schema arrays to JSON format.
 */
class DCF_Schema_Printer {

	/**
	 * Print Schema array as JSON string.
	 *
	 * @param array $schema Schema array to serialize.
	 * @param bool  $pretty Whether to format output with indentation.
	 * @return string JSON string.
	 */
	public static function print( array $schema, bool $pretty = true ): string {
		// Set JSON encoding options.
		$options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

		// Add pretty print option if requested.
		if ( $pretty ) {
			$options |= JSON_PRETTY_PRINT;
		}

		// Encode to JSON.
		$json = wp_json_encode( $schema, $options );

		// Return empty array JSON if encoding failed.
		if ( false === $json ) {
			return '[]';
		}

		return $json;
	}
}
