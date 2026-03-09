<?php
/**
 * Fired during plugin deactivation.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class DCF_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Note: Database tables are preserved during deactivation as per requirements.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear all plugin caches
		self::clear_caches();
	}

	/**
	 * Clear all plugin caches.
	 *
	 * @since 1.0.0
	 */
	private static function clear_caches() {
		global $wpdb;

		// Get all cache keys for this plugin
		$cache_keys = array(
			'dcf_group_types_all',
			'dcf_layouts_registry',
			'dcf_performance_stats',
		);

		// Delete cache keys
		foreach ( $cache_keys as $key ) {
			wp_cache_delete( $key, 'dcf' );
		}

		// Delete group-specific caches
		$groups = $wpdb->get_col( "SELECT id FROM {$wpdb->prefix}dcf_groups" );
		if ( $groups ) {
			foreach ( $groups as $group_id ) {
				wp_cache_delete( "dcf_group_items_{$group_id}", 'dcf' );
				wp_cache_delete( "dcf_group_type_{$group_id}", 'dcf' );
			}
		}
	}
}
