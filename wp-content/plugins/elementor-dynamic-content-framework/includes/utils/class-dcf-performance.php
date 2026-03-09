<?php
/**
 * Performance Monitoring Class
 *
 * Handles performance monitoring and tracking for the plugin.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Performance class.
 *
 * Tracks performance metrics including timing, database queries, and layout rendering.
 */
class DCF_Performance {

	/**
	 * Active timers
	 *
	 * @var array
	 */
	private static $timers = array();

	/**
	 * Completed timings
	 *
	 * @var array
	 */
	private static $timings = array();

	/**
	 * Database queries log
	 *
	 * @var array
	 */
	private static $queries = array();

	/**
	 * Layout rendering times
	 *
	 * @var array
	 */
	private static $layout_renders = array();

	/**
	 * Performance thresholds (in milliseconds)
	 *
	 * @var array
	 */
	private static $thresholds = array(
		'query'  => 100,  // 100ms for database queries
		'layout' => 500,  // 500ms for layout rendering
		'total'  => 1000, // 1000ms for total execution
	);

	/**
	 * Initialize performance monitoring
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Allow customization of thresholds via filter
		self::$thresholds = apply_filters( 'dcf_performance_threshold', self::$thresholds );
	}

	/**
	 * Start performance timing
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Timer label
	 */
	public static function start( string $label ): void {
		self::$timers[ $label ] = microtime( true );
	}

	/**
	 * End performance timing and return execution time
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Timer label
	 * @return float Execution time in milliseconds, or 0 if timer not found
	 */
	public static function end( string $label ): float {
		if ( ! isset( self::$timers[ $label ] ) ) {
			DCF_Logger::warning( "Performance timer not found: {$label}" );
			return 0;
		}

		$start_time = self::$timers[ $label ];
		$end_time   = microtime( true );
		$duration   = ( $end_time - $start_time ) * 1000; // Convert to milliseconds

		// Store the timing
		if ( ! isset( self::$timings[ $label ] ) ) {
			self::$timings[ $label ] = array();
		}

		self::$timings[ $label ][] = $duration;

		// Remove from active timers
		unset( self::$timers[ $label ] );

		// Check if duration exceeds threshold
		self::check_threshold( $label, $duration );

		return $duration;
	}

	/**
	 * Log database query
	 *
	 * @since 1.0.0
	 *
	 * @param string $query SQL query
	 * @param float  $time Execution time in milliseconds
	 */
	public static function log_query( string $query, float $time ): void {
		self::$queries[] = array(
			'query'     => $query,
			'time'      => $time,
			'timestamp' => microtime( true ),
		);

		// Check if query time exceeds threshold
		if ( $time > self::$thresholds['query'] ) {
			DCF_Logger::warning(
				"Slow database query detected ({$time}ms)",
				array(
					'query'     => $query,
					'time'      => $time,
					'threshold' => self::$thresholds['query'],
				)
			);
		}

		// Log in debug mode
		if ( get_option( 'dcf_debug_mode', false ) ) {
			DCF_Logger::debug(
				"Database query executed",
				array(
					'query' => $query,
					'time'  => $time,
				)
			);
		}
	}

	/**
	 * Track layout rendering time
	 *
	 * @since 1.0.0
	 *
	 * @param string $layout_slug Layout identifier
	 * @param float  $time Rendering time in milliseconds
	 * @param int    $item_count Number of items rendered
	 */
	public static function track_layout_render( string $layout_slug, float $time, int $item_count = 0 ): void {
		if ( ! isset( self::$layout_renders[ $layout_slug ] ) ) {
			self::$layout_renders[ $layout_slug ] = array(
				'count'      => 0,
				'total_time' => 0,
				'avg_time'   => 0,
				'max_time'   => 0,
				'min_time'   => PHP_FLOAT_MAX,
				'items'      => 0,
			);
		}

		$layout = &self::$layout_renders[ $layout_slug ];
		$layout['count']++;
		$layout['total_time'] += $time;
		$layout['avg_time']    = $layout['total_time'] / $layout['count'];
		$layout['max_time']    = max( $layout['max_time'], $time );
		$layout['min_time']    = min( $layout['min_time'], $time );
		$layout['items']      += $item_count;

		// Check if layout rendering time exceeds threshold
		if ( $time > self::$thresholds['layout'] ) {
			DCF_Logger::warning(
				"Slow layout rendering detected: {$layout_slug} ({$time}ms)",
				array(
					'layout'    => $layout_slug,
					'time'      => $time,
					'items'     => $item_count,
					'threshold' => self::$thresholds['layout'],
				)
			);
		}
	}

	/**
	 * Get performance report
	 *
	 * @since 1.0.0
	 *
	 * @return array Performance metrics
	 */
	public static function get_report(): array {
		$report = array(
			'timings'        => self::get_timing_summary(),
			'queries'        => self::get_query_summary(),
			'layouts'        => self::$layout_renders,
			'thresholds'     => self::$thresholds,
			'memory_usage'   => self::get_memory_usage(),
			'active_timers'  => count( self::$timers ),
		);

		return $report;
	}

	/**
	 * Get timing summary
	 *
	 * @since 1.0.0
	 *
	 * @return array Timing statistics
	 */
	private static function get_timing_summary(): array {
		$summary = array();

		foreach ( self::$timings as $label => $times ) {
			$count = count( $times );
			$total = array_sum( $times );

			$summary[ $label ] = array(
				'count'     => $count,
				'total'     => $total,
				'average'   => $count > 0 ? $total / $count : 0,
				'min'       => $count > 0 ? min( $times ) : 0,
				'max'       => $count > 0 ? max( $times ) : 0,
			);
		}

		return $summary;
	}

	/**
	 * Get query summary
	 *
	 * @since 1.0.0
	 *
	 * @return array Query statistics
	 */
	private static function get_query_summary(): array {
		$count = count( self::$queries );
		$times = array_column( self::$queries, 'time' );
		$total = array_sum( $times );

		$slow_queries = array_filter(
			self::$queries,
			function( $query ) {
				return $query['time'] > self::$thresholds['query'];
			}
		);

		return array(
			'count'        => $count,
			'total_time'   => $total,
			'average_time' => $count > 0 ? $total / $count : 0,
			'slow_queries' => count( $slow_queries ),
			'queries'      => self::$queries,
		);
	}

	/**
	 * Get memory usage
	 *
	 * @since 1.0.0
	 *
	 * @return array Memory usage statistics
	 */
	private static function get_memory_usage(): array {
		return array(
			'current' => memory_get_usage( true ),
			'peak'    => memory_get_peak_usage( true ),
			'limit'   => ini_get( 'memory_limit' ),
		);
	}

	/**
	 * Check if duration exceeds threshold
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Timer label
	 * @param float  $duration Duration in milliseconds
	 */
	private static function check_threshold( string $label, float $duration ): void {
		// Check against total threshold
		if ( $duration > self::$thresholds['total'] ) {
			DCF_Logger::warning(
				"Performance threshold exceeded: {$label} ({$duration}ms)",
				array(
					'label'     => $label,
					'duration'  => $duration,
					'threshold' => self::$thresholds['total'],
				)
			);
		}
	}

	/**
	 * Reset all performance data
	 *
	 * @since 1.0.0
	 */
	public static function reset(): void {
		self::$timers         = array();
		self::$timings        = array();
		self::$queries        = array();
		self::$layout_renders = array();
	}

	/**
	 * Get active timers
	 *
	 * @since 1.0.0
	 *
	 * @return array Active timer labels
	 */
	public static function get_active_timers(): array {
		return array_keys( self::$timers );
	}

	/**
	 * Check if a timer is active
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Timer label
	 * @return bool
	 */
	public static function is_timer_active( string $label ): bool {
		return isset( self::$timers[ $label ] );
	}

	/**
	 * Get elapsed time for an active timer
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Timer label
	 * @return float Elapsed time in milliseconds, or 0 if timer not found
	 */
	public static function get_elapsed_time( string $label ): float {
		if ( ! isset( self::$timers[ $label ] ) ) {
			return 0;
		}

		$start_time = self::$timers[ $label ];
		$current_time = microtime( true );
		return ( $current_time - $start_time ) * 1000;
	}

	/**
	 * Set performance threshold
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Threshold type (query, layout, total)
	 * @param float  $value Threshold value in milliseconds
	 */
	public static function set_threshold( string $type, float $value ): void {
		if ( isset( self::$thresholds[ $type ] ) ) {
			self::$thresholds[ $type ] = $value;
		}
	}

	/**
	 * Get performance threshold
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Threshold type (query, layout, total)
	 * @return float Threshold value in milliseconds, or 0 if not found
	 */
	public static function get_threshold( string $type ): float {
		return self::$thresholds[ $type ] ?? 0;
	}
}
