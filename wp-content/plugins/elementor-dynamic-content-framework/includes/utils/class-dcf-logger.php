<?php
/**
 * Logger Class
 *
 * Handles logging for the plugin.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Logger class.
 *
 * Handles logging of plugin operations.
 */
class DCF_Logger {

	/**
	 * Log levels
	 */
	const LEVEL_INFO    = 'info';
	const LEVEL_WARNING = 'warning';
	const LEVEL_ERROR   = 'error';
	const LEVEL_DEBUG   = 'debug';

	/**
	 * Log directory
	 *
	 * @var string
	 */
	private static $log_dir = null;

	/**
	 * Initialize logger
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		self::$log_dir = WP_CONTENT_DIR . '/dcf-logs';
		
		// Create log directory if it doesn't exist
		if ( ! is_dir( self::$log_dir ) ) {
			wp_mkdir_p( self::$log_dir );
		}
	}

	/**
	 * Log info message
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	public static function info( string $message, array $context = [] ): void {
		self::log( self::LEVEL_INFO, $message, $context );
	}

	/**
	 * Log warning message
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	public static function warning( string $message, array $context = [] ): void {
		self::log( self::LEVEL_WARNING, $message, $context );
	}

	/**
	 * Log error message
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	public static function error( string $message, array $context = [] ): void {
		self::log( self::LEVEL_ERROR, $message, $context );
	}

	/**
	 * Log debug message
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	public static function debug( string $message, array $context = [] ): void {
		// Only log debug messages if debug mode is enabled
		if ( ! self::is_debug_mode() ) {
			return;
		}

		self::log( self::LEVEL_DEBUG, $message, $context );
	}

	/**
	 * Log message
	 *
	 * @since 1.0.0
	 *
	 * @param string $level Log level
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	private static function log( string $level, string $message, array $context = [] ): void {
		// Also log to WordPress error log
		$log_message = sprintf(
			'[DCF %s] %s',
			strtoupper( $level ),
			$message
		);

		if ( ! empty( $context ) ) {
			$log_message .= ' | Context: ' . wp_json_encode( $context );
		}

		error_log( $log_message );

		// Log to file if directory is writable
		if ( self::$log_dir && is_writable( self::$log_dir ) ) {
			self::write_to_file( $level, $message, $context );
		}
	}

	/**
	 * Write log to file
	 *
	 * @since 1.0.0
	 *
	 * @param string $level Log level
	 * @param string $message Log message
	 * @param array  $context Additional context data
	 */
	private static function write_to_file( string $level, string $message, array $context = [] ): void {
		$log_file = self::$log_dir . '/dcf-' . gmdate( 'Y-m-d' ) . '.log';

		$log_entry = sprintf(
			"[%s] [%s] %s\n",
			gmdate( 'Y-m-d H:i:s' ),
			strtoupper( $level ),
			$message
		);

		if ( ! empty( $context ) ) {
			$log_entry .= 'Context: ' . wp_json_encode( $context ) . "\n";
		}

		// Append to log file
		file_put_contents( $log_file, $log_entry, FILE_APPEND );
	}

	/**
	 * Check if debug mode is enabled
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private static function is_debug_mode(): bool {
		return get_option( 'dcf_debug_mode', false );
	}

	/**
	 * Get logs for a specific date
	 *
	 * @since 1.0.0
	 *
	 * @param string $date Date in Y-m-d format
	 * @return string|false Log content or false if file doesn't exist
	 */
	public static function get_logs( string $date = '' ) {
		if ( empty( $date ) ) {
			$date = gmdate( 'Y-m-d' );
		}

		$log_file = self::$log_dir . '/dcf-' . $date . '.log';

		if ( ! file_exists( $log_file ) ) {
			return false;
		}

		return file_get_contents( $log_file );
	}

	/**
	 * Clear logs
	 *
	 * @since 1.0.0
	 *
	 * @param string $date Date in Y-m-d format, or empty to clear all logs
	 * @return bool
	 */
	public static function clear_logs( string $date = '' ): bool {
		if ( empty( $date ) ) {
			// Clear all logs
			$files = glob( self::$log_dir . '/dcf-*.log' );
			foreach ( $files as $file ) {
				if ( is_file( $file ) ) {
					unlink( $file );
				}
			}
			return true;
		}

		// Clear specific date log
		$log_file = self::$log_dir . '/dcf-' . $date . '.log';
		if ( file_exists( $log_file ) ) {
			return unlink( $log_file );
		}

		return false;
	}
}

// Initialize logger on plugin load
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', array( 'DCF_Logger', 'init' ) );
}
