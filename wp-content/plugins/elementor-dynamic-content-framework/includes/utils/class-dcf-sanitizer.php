<?php
/**
 * Sanitizer Class
 *
 * Handles data sanitization and escaping for the plugin.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Sanitizer class.
 *
 * Provides methods for sanitizing user input and escaping output.
 */
class DCF_Sanitizer {

	/**
	 * Allowed file extensions for uploads
	 *
	 * @var array
	 */
	private static $allowed_extensions = array(
		'image'  => array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ),
		'video'  => array( 'mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv' ),
		'audio'  => array( 'mp3', 'wav', 'ogg', 'm4a', 'flac' ),
		'document' => array( 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx' ),
	);

	/**
	 * Allowed MIME types for uploads
	 *
	 * @var array
	 */
	private static $allowed_mimes = array(
		'jpg|jpeg|jpe'       => 'image/jpeg',
		'gif'                => 'image/gif',
		'png'                => 'image/png',
		'webp'               => 'image/webp',
		'svg'                => 'image/svg+xml',
		'mp4|m4v'            => 'video/mp4',
		'webm'               => 'video/webm',
		'ogv'                => 'video/ogg',
		'mov'                => 'video/quicktime',
		'avi'                => 'video/x-msvideo',
		'mkv'                => 'video/x-matroska',
		'mp3'                => 'audio/mpeg',
		'wav'                => 'audio/wav',
		'ogg|oga'            => 'audio/ogg',
		'm4a'                => 'audio/mp4',
		'flac'               => 'audio/flac',
		'pdf'                => 'application/pdf',
	);

	/**
	 * Maximum file upload size in MB
	 *
	 * @var int
	 */
	private static $max_upload_size_mb = 50;

	/**
	 * Sanitize text input
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text to sanitize
	 * @param int    $max_length Maximum length (0 for unlimited)
	 * @return string Sanitized text
	 */
	public static function sanitize_text( string $text, int $max_length = 0 ): string {
		// Remove all HTML tags and encode special characters
		$sanitized = wp_kses_post( $text );
		
		// Apply WordPress text sanitization
		$sanitized = sanitize_text_field( $sanitized );

		// Enforce max length if specified
		if ( $max_length > 0 ) {
			$sanitized = substr( $sanitized, 0, $max_length );
		}

		return $sanitized;
	}

	/**
	 * Sanitize textarea input
	 *
	 * @since 1.0.0
	 *
	 * @param string $textarea Textarea content to sanitize
	 * @param int    $max_length Maximum length (0 for unlimited)
	 * @return string Sanitized textarea content
	 */
	public static function sanitize_textarea( string $textarea, int $max_length = 0 ): string {
		// Allow basic HTML tags in textarea
		$allowed_html = array(
			'p'      => array(),
			'br'     => array(),
			'strong' => array(),
			'em'     => array(),
			'u'      => array(),
			'a'      => array(
				'href'   => true,
				'title'  => true,
				'target' => true,
				'rel'    => true,
			),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
		);

		$sanitized = wp_kses( $textarea, $allowed_html );

		// Enforce max length if specified
		if ( $max_length > 0 ) {
			$sanitized = substr( $sanitized, 0, $max_length );
		}

		return $sanitized;
	}

	/**
	 * Sanitize email input
	 *
	 * @since 1.0.0
	 *
	 * @param string $email Email to sanitize
	 * @return string Sanitized email
	 */
	public static function sanitize_email( string $email ): string {
		return sanitize_email( $email );
	}

	/**
	 * Sanitize URL input
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL to sanitize
	 * @return string Sanitized URL
	 */
	public static function sanitize_url( string $url ): string {
		return esc_url_raw( $url );
	}

	/**
	 * Sanitize file upload
	 *
	 * @since 1.0.0
	 *
	 * @param array  $file File array from $_FILES
	 * @param string $type File type (image, video, audio, document)
	 * @param int    $max_size_mb Maximum file size in MB (0 for unlimited)
	 * @return array|WP_Error File data or WP_Error on failure
	 */
	public static function sanitize_file_upload( array $file, string $type = 'image', int $max_size_mb = 0 ) {
		// Check if file exists
		if ( empty( $file ) || empty( $file['name'] ) ) {
			return new WP_Error( 'no_file', __( 'No file provided', 'elementor-dynamic-content-framework' ) );
		}

		// Check for upload errors
		if ( ! empty( $file['error'] ) ) {
			return new WP_Error( 'upload_error', __( 'File upload error', 'elementor-dynamic-content-framework' ) );
		}

		// Validate file type
		$validation = self::validate_file_type( $file, $type );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		// Validate file size
		$size_validation = self::validate_file_size( $file, $max_size_mb );
		if ( is_wp_error( $size_validation ) ) {
			return $size_validation;
		}

		// Sanitize filename
		$filename = sanitize_file_name( $file['name'] );

		return array(
			'name'     => $filename,
			'tmp_name' => $file['tmp_name'],
			'size'     => $file['size'],
			'type'     => $file['type'],
		);
	}

	/**
	 * Validate file type
	 *
	 * @since 1.0.0
	 *
	 * @param array  $file File array from $_FILES
	 * @param string $type File type (image, video, audio, document)
	 * @return true|WP_Error
	 */
	public static function validate_file_type( array $file, string $type = 'image' ) {
		// Get file extension
		$file_name = $file['name'];
		$file_ext  = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );

		// Check if file type is supported
		if ( ! isset( self::$allowed_extensions[ $type ] ) ) {
			return new WP_Error(
				'invalid_file_type',
				sprintf(
					__( 'File type "%s" is not supported', 'elementor-dynamic-content-framework' ),
					esc_html( $type )
				)
			);
		}

		// Check if extension is allowed for this type
		if ( ! in_array( $file_ext, self::$allowed_extensions[ $type ], true ) ) {
			return new WP_Error(
				'invalid_extension',
				sprintf(
					__( 'File extension ".%s" is not allowed for %s uploads', 'elementor-dynamic-content-framework' ),
					esc_html( $file_ext ),
					esc_html( $type )
				)
			);
		}

		// Validate MIME type
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		// Check if MIME type is in allowed list
		$allowed_mimes = self::get_allowed_mimes_for_type( $type );
		if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
			return new WP_Error(
				'invalid_mime_type',
				sprintf(
					__( 'File MIME type "%s" is not allowed', 'elementor-dynamic-content-framework' ),
					esc_html( $mime_type )
				)
			);
		}

		return true;
	}

	/**
	 * Validate file size
	 *
	 * @since 1.0.0
	 *
	 * @param array $file File array from $_FILES
	 * @param int   $max_size_mb Maximum file size in MB (0 for unlimited)
	 * @return true|WP_Error
	 */
	public static function validate_file_size( array $file, int $max_size_mb = 0 ) {
		// Get file size in MB
		$file_size_mb = $file['size'] / ( 1024 * 1024 );

		// Use default max size if not specified
		if ( $max_size_mb <= 0 ) {
			$max_size_mb = self::$max_upload_size_mb;
		}

		// Check against specified limit
		if ( $file_size_mb > $max_size_mb ) {
			return new WP_Error(
				'file_too_large',
				sprintf(
					__( 'File size exceeds maximum allowed size of %d MB', 'elementor-dynamic-content-framework' ),
					intval( $max_size_mb )
				)
			);
		}

		// Check against WordPress upload limit
		$wp_max_upload = wp_max_upload_size() / ( 1024 * 1024 );
		if ( $file_size_mb > $wp_max_upload ) {
			return new WP_Error(
				'file_exceeds_wp_limit',
				sprintf(
					__( 'File size exceeds WordPress upload limit of %d MB', 'elementor-dynamic-content-framework' ),
					intval( $wp_max_upload )
				)
			);
		}

		// Check against PHP upload limit
		$php_max_upload = ini_get( 'upload_max_filesize' );
		$php_max_upload_mb = self::convert_to_mb( $php_max_upload );
		if ( $file_size_mb > $php_max_upload_mb ) {
			return new WP_Error(
				'file_exceeds_php_limit',
				sprintf(
					__( 'File size exceeds PHP upload limit of %s', 'elementor-dynamic-content-framework' ),
					esc_html( $php_max_upload )
				)
			);
		}

		return true;
	}

	/**
	 * Get allowed MIME types for a specific file type
	 *
	 * @since 1.0.0
	 *
	 * @param string $type File type (image, video, audio, document)
	 * @return array Array of allowed MIME types
	 */
	private static function get_allowed_mimes_for_type( string $type ): array {
		$allowed_mimes = array();

		foreach ( self::$allowed_mimes as $extensions => $mime_type ) {
			// Map extensions to types
			if ( $type === 'image' && in_array( $extensions, array( 'jpg|jpeg|jpe', 'gif', 'png', 'webp', 'svg' ), true ) ) {
				$allowed_mimes[] = $mime_type;
			} elseif ( $type === 'video' && in_array( $extensions, array( 'mp4|m4v', 'webm', 'ogv', 'mov', 'avi', 'mkv' ), true ) ) {
				$allowed_mimes[] = $mime_type;
			} elseif ( $type === 'audio' && in_array( $extensions, array( 'mp3', 'wav', 'ogg|oga', 'm4a', 'flac' ), true ) ) {
				$allowed_mimes[] = $mime_type;
			} elseif ( $type === 'document' && in_array( $extensions, array( 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx' ), true ) ) {
				$allowed_mimes[] = $mime_type;
			}
		}

		return $allowed_mimes;
	}

	/**
	 * Convert file size string to MB
	 *
	 * @since 1.0.0
	 *
	 * @param string $value File size string (e.g., "128M", "2G")
	 * @return float File size in MB
	 */
	private static function convert_to_mb( string $value ): float {
		$value = trim( $value );
		$last  = strtoupper( substr( $value, -1 ) );

		$value = (int) $value;

		switch ( $last ) {
			case 'G':
				$value *= 1024;
				// Fall through
			case 'M':
				$value *= 1024;
				// Fall through
			case 'K':
				$value /= 1024;
				break;
		}

		return (float) $value;
	}

	/**
	 * Escape HTML content
	 *
	 * @since 1.0.0
	 *
	 * @param string $html HTML content to escape
	 * @return string Escaped HTML
	 */
	public static function escape_html( string $html ): string {
		return wp_kses_post( $html );
	}

	/**
	 * Escape HTML attribute
	 *
	 * @since 1.0.0
	 *
	 * @param string $attr Attribute value to escape
	 * @return string Escaped attribute
	 */
	public static function escape_attr( string $attr ): string {
		return esc_attr( $attr );
	}

	/**
	 * Escape URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL to escape
	 * @return string Escaped URL
	 */
	public static function escape_url( string $url ): string {
		return esc_url( $url );
	}

	/**
	 * Escape JavaScript string
	 *
	 * @since 1.0.0
	 *
	 * @param string $js JavaScript string to escape
	 * @return string Escaped JavaScript string
	 */
	public static function escape_js( string $js ): string {
		return wp_json_encode( $js );
	}

	/**
	 * Sanitize array recursively
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Array to sanitize
	 * @return array Sanitized array
	 */
	public static function sanitize_array( array $data ): array {
		$sanitized = array();

		foreach ( $data as $key => $value ) {
			// Sanitize key
			$key = sanitize_key( $key );

			// Sanitize value based on type
			if ( is_array( $value ) ) {
				$sanitized[ $key ] = self::sanitize_array( $value );
			} elseif ( is_string( $value ) ) {
				$sanitized[ $key ] = self::sanitize_text( $value );
			} else {
				$sanitized[ $key ] = $value;
			}
		}

		return $sanitized;
	}

	/**
	 * Validate JSON string
	 *
	 * @since 1.0.0
	 *
	 * @param string $json JSON string to validate
	 * @return true|WP_Error
	 */
	public static function validate_json( string $json ) {
		$decoded = json_decode( $json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error(
				'invalid_json',
				sprintf(
					__( 'Invalid JSON: %s', 'elementor-dynamic-content-framework' ),
					json_last_error_msg()
				)
			);
		}

		return true;
	}

	/**
	 * Sanitize JSON string
	 *
	 * @since 1.0.0
	 *
	 * @param string $json JSON string to sanitize
	 * @return string|WP_Error Sanitized JSON or WP_Error on failure
	 */
	public static function sanitize_json( string $json ) {
		// Validate JSON first
		$validation = self::validate_json( $json );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		// Decode and re-encode to ensure proper formatting
		$decoded = json_decode( $json, true );
		$sanitized = wp_json_encode( $decoded );

		return $sanitized;
	}

	/**
	 * Set maximum upload size
	 *
	 * @since 1.0.0
	 *
	 * @param int $size_mb Maximum size in MB
	 */
	public static function set_max_upload_size( int $size_mb ): void {
		self::$max_upload_size_mb = max( 1, $size_mb );
	}

	/**
	 * Get maximum upload size
	 *
	 * @since 1.0.0
	 *
	 * @return int Maximum size in MB
	 */
	public static function get_max_upload_size(): int {
		return self::$max_upload_size_mb;
	}

	/**
	 * Add allowed file extension
	 *
	 * @since 1.0.0
	 *
	 * @param string $type File type (image, video, audio, document)
	 * @param string $extension File extension (without dot)
	 */
	public static function add_allowed_extension( string $type, string $extension ): void {
		if ( ! isset( self::$allowed_extensions[ $type ] ) ) {
			self::$allowed_extensions[ $type ] = array();
		}

		$extension = strtolower( $extension );
		if ( ! in_array( $extension, self::$allowed_extensions[ $type ], true ) ) {
			self::$allowed_extensions[ $type ][] = $extension;
		}
	}

	/**
	 * Add allowed MIME type
	 *
	 * @since 1.0.0
	 *
	 * @param string $extensions File extensions (comma-separated)
	 * @param string $mime_type MIME type
	 */
	public static function add_allowed_mime( string $extensions, string $mime_type ): void {
		self::$allowed_mimes[ $extensions ] = $mime_type;
	}
}
