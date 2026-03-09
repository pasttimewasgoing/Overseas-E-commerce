<?php
/**
 * Database management class.
 *
 * Handles database table creation, version management, and table existence checks.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/database
 */

/**
 * Database management class.
 *
 * This class defines all code necessary to manage database tables
 * for the plugin.
 */
class DCF_Database {

	/**
	 * Database version.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $db_version The current database version.
	 */
	private static $db_version = '1.0.0';

	/**
	 * Create all database tables.
	 *
	 * Creates the three core tables: group_types, groups, and group_items
	 * with proper indexes and foreign key constraints.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Get table names
		$table_group_types = self::get_table_name( 'group_types' );
		$table_groups      = self::get_table_name( 'groups' );
		$table_group_items = self::get_table_name( 'group_items' );

		// SQL for creating tables
		$sql = array();

		// Create group types table
		$sql[] = "CREATE TABLE IF NOT EXISTS $table_group_types (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL COMMENT '类型名称',
			slug varchar(100) NOT NULL COMMENT '唯一标识符',
			schema_json longtext NOT NULL COMMENT '字段结构 JSON',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY slug (slug),
			KEY created_at (created_at)
		) $charset_collate;";

		// Create groups table
		$sql[] = "CREATE TABLE IF NOT EXISTS $table_groups (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			type_id bigint(20) UNSIGNED NOT NULL COMMENT '关联的类型 ID',
			title varchar(255) NOT NULL COMMENT '内容组标题',
			status enum('active','inactive','draft') NOT NULL DEFAULT 'draft' COMMENT '状态',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY type_id (type_id),
			KEY status (status),
			KEY created_at (created_at),
			CONSTRAINT fk_group_type FOREIGN KEY (type_id) REFERENCES $table_group_types(id) ON DELETE RESTRICT
		) $charset_collate;";

		// Create group items table
		$sql[] = "CREATE TABLE IF NOT EXISTS $table_group_items (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			group_id bigint(20) UNSIGNED NOT NULL COMMENT '关联的内容组 ID',
			data_json longtext NOT NULL COMMENT '字段数据 JSON',
			sort_order int(11) NOT NULL DEFAULT 0 COMMENT '排序值',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY group_id (group_id),
			KEY sort_order (sort_order),
			KEY created_at (created_at),
			CONSTRAINT fk_group_item FOREIGN KEY (group_id) REFERENCES $table_groups(id) ON DELETE CASCADE
		) $charset_collate;";

		// Execute SQL
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		
		$success = true;
		foreach ( $sql as $query ) {
			$result = dbDelta( $query );
			if ( empty( $result ) ) {
				$success = false;
			}
		}

		// Store database version
		if ( $success ) {
			update_option( 'dcf_db_version', self::$db_version );
		}

		return $success;
	}

	/**
	 * Get table name with WordPress prefix.
	 *
	 * @since 1.0.0
	 * @param string $table Table name without prefix (e.g., 'group_types').
	 * @return string Full table name with prefix.
	 */
	public static function get_table_name( $table ) {
		global $wpdb;
		return $wpdb->prefix . 'dcf_' . $table;
	}

	/**
	 * Check if a table exists.
	 *
	 * @since 1.0.0
	 * @param string $table Table name without prefix.
	 * @return bool True if table exists, false otherwise.
	 */
	public static function table_exists( $table ) {
		global $wpdb;
		
		$table_name = self::get_table_name( $table );
		$query      = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
		$result     = $wpdb->get_var( $query );
		
		return $result === $table_name;
	}

	/**
	 * Get the current database version.
	 *
	 * @since 1.0.0
	 * @return string Database version.
	 */
	public static function get_db_version() {
		return get_option( 'dcf_db_version', '0.0.0' );
	}

	/**
	 * Check if database needs upgrade.
	 *
	 * @since 1.0.0
	 * @return bool True if upgrade is needed, false otherwise.
	 */
	public static function needs_upgrade() {
		$current_version = self::get_db_version();
		return version_compare( $current_version, self::$db_version, '<' );
	}

	/**
	 * Verify all tables exist.
	 *
	 * @since 1.0.0
	 * @return bool True if all tables exist, false otherwise.
	 */
	public static function verify_tables() {
		$tables = array( 'group_types', 'groups', 'group_items' );
		
		foreach ( $tables as $table ) {
			if ( ! self::table_exists( $table ) ) {
				return false;
			}
		}
		
		return true;
	}
}
