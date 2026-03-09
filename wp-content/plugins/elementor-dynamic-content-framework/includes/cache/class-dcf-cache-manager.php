<?php
/**
 * Cache Manager Class
 *
 * Manages caching for content groups and items.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/cache
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Cache_Manager class.
 *
 * Handles caching operations using WordPress transients.
 */
class DCF_Cache_Manager {

	/**
	 * Cache group name.
	 *
	 * @var string
	 */
	private static $cache_group = 'dcf';

	/**
	 * Statistics tracking key.
	 *
	 * @var string
	 */
	private static $stats_key = 'dcf_cache_stats';

	/**
	 * Get cached data.
	 *
	 * @param string $key Cache key.
	 * @return mixed|false Cached value or false if not found.
	 */
	public static function get( string $key ) {
		$value = wp_cache_get( $key, self::$cache_group );

		// Track statistics.
		if ( false !== $value ) {
			self::increment_stat( 'hits' );
		} else {
			self::increment_stat( 'misses' );
		}

		return $value;
	}

	/**
	 * Set cached data.
	 *
	 * @param string $key        Cache key.
	 * @param mixed  $value      Value to cache.
	 * @param int    $expiration Expiration time in seconds (default: 3600).
	 * @return bool True on success, false on failure.
	 */
	public static function set( string $key, $value, int $expiration = 3600 ): bool {
		// Get configurable cache expiration time from settings.
		$cache_expiration = self::get_cache_expiration();
		
		// Use provided expiration if specified, otherwise use configured value.
		if ( 3600 === $expiration ) {
			$expiration = $cache_expiration;
		}
		
		return wp_cache_set( $key, $value, self::$cache_group, $expiration );
	}

	/**
	 * Get cache expiration time from settings.
	 *
	 * @return int Cache expiration time in seconds.
	 */
	public static function get_cache_expiration(): int {
		// Get cache expiration from settings (in hours), default to 1 hour.
		$hours = get_option( 'dcf_cache_expiration_hours', 1 );
		
		// Convert hours to seconds.
		return absint( $hours ) * HOUR_IN_SECONDS;
	}

	/**
	 * Delete cached data.
	 *
	 * @param string $key Cache key.
	 * @return bool True on success, false on failure.
	 */
	public static function delete( string $key ): bool {
		return wp_cache_delete( $key, self::$cache_group );
	}

	/**
	 * Flush all plugin caches.
	 *
	 * @return bool True on success.
	 */
	public static function flush_all(): bool {
		// WordPress doesn't provide a way to flush a specific cache group,
		// so we'll need to track keys or use a different approach.
		// For now, we'll use a simple implementation.
		wp_cache_flush();
		return true;
	}

	/**
	 * Invalidate cache for a specific group.
	 *
	 * @param int $group_id Group ID.
	 * @return bool True on success.
	 */
	public static function invalidate_group( int $group_id ): bool {
		$cache_key = "dcf_group_items_{$group_id}";
		return self::delete( $cache_key );
	}

	/**
	 * Get cache statistics.
	 *
	 * @return array {
	 *     Cache statistics.
	 *
	 *     @type int   $hits     Number of cache hits.
	 *     @type int   $misses   Number of cache misses.
	 *     @type float $hit_rate Hit rate percentage.
	 * }
	 */
	public static function get_stats(): array {
		$stats = get_option( self::$stats_key, array() );

		$hits   = isset( $stats['hits'] ) ? (int) $stats['hits'] : 0;
		$misses = isset( $stats['misses'] ) ? (int) $stats['misses'] : 0;
		$total  = $hits + $misses;

		$hit_rate = $total > 0 ? ( $hits / $total ) * 100 : 0.0;

		return array(
			'hits'     => $hits,
			'misses'   => $misses,
			'hit_rate' => round( $hit_rate, 2 ),
		);
	}

	/**
	 * Increment a statistic counter.
	 *
	 * @param string $stat Statistic name ('hits' or 'misses').
	 * @return void
	 */
	private static function increment_stat( string $stat ): void {
		$stats = get_option( self::$stats_key, array() );

		if ( ! isset( $stats[ $stat ] ) ) {
			$stats[ $stat ] = 0;
		}

		$stats[ $stat ]++;

		update_option( self::$stats_key, $stats, false );
	}

	/**
	 * Reset cache statistics.
	 *
	 * @return bool True on success.
	 */
	public static function reset_stats(): bool {
		return delete_option( self::$stats_key );
	}
}
