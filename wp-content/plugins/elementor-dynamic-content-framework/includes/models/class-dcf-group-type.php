<?php
/**
 * Group Type Model Class
 *
 * Manages content group type definitions and field structures.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/models
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_Type class.
 *
 * Handles CRUD operations for content group types.
 */
class DCF_Group_Type {

	/**
	 * Create a new content group type.
	 *
	 * @param array $data {
	 *     Content group type data.
	 *
	 *     @type string $name   Type name (required).
	 *     @type string $slug   Unique identifier (required).
	 *     @type array  $schema Field definition array (required).
	 * }
	 * @return int|WP_Error Type ID on success, WP_Error on failure.
	 */
	public static function create( array $data ) {
		global $wpdb;

		// Validate required fields.
		if ( empty( $data['name'] ) ) {
			return new WP_Error(
				'missing_name',
				__( 'Type name is required', 'elementor-dynamic-content-framework' )
			);
		}

		if ( empty( $data['slug'] ) ) {
			return new WP_Error(
				'missing_slug',
				__( 'Type slug is required', 'elementor-dynamic-content-framework' )
			);
		}

		if ( empty( $data['schema'] ) || ! is_array( $data['schema'] ) ) {
			return new WP_Error(
				'missing_schema',
				__( 'Schema is required and must be an array', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate schema.
		$validation = DCF_Schema_Validator::validate( $data['schema'] );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		// Check if slug is unique.
		$existing = self::get_by_slug( $data['slug'] );
		if ( $existing ) {
			return new WP_Error(
				'duplicate_slug',
				sprintf(
					/* translators: %s: slug */
					__( 'A type with slug "%s" already exists', 'elementor-dynamic-content-framework' ),
					$data['slug']
				)
			);
		}

		// Serialize schema to JSON.
		$schema_json = DCF_Schema_Printer::print( $data['schema'], true );

		// Prepare data for insertion.
		$table_name = DCF_Database::get_table_name( 'group_types' );

		// Insert into database using prepared statement.
		$result = $wpdb->insert(
			$table_name,
			array(
				'name'        => sanitize_text_field( $data['name'] ),
				'slug'        => sanitize_title( $data['slug'] ),
				'schema_json' => $schema_json,
			),
			array( '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_insert_error',
				__( 'Failed to create group type', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		return $wpdb->insert_id;
	}


	/**
	 * Get a content group type by ID.
	 *
	 * @param int $type_id Type ID.
	 * @return array|null Type data array or null if not found.
	 */
	public static function get( int $type_id ): ?array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_types' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d",
			$type_id
		);

		$result = $wpdb->get_row( $query, ARRAY_A );

		if ( ! $result ) {
			return null;
		}

		// Parse schema JSON.
		$result['schema'] = DCF_Schema_Parser::parse( $result['schema_json'] );
		if ( is_wp_error( $result['schema'] ) ) {
			$result['schema'] = array();
		}

		return $result;
	}

	/**
	 * Get a content group type by slug.
	 *
	 * @param string $slug Type slug.
	 * @return array|null Type data array or null if not found.
	 */
	public static function get_by_slug( string $slug ): ?array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_types' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE slug = %s",
			$slug
		);

		$result = $wpdb->get_row( $query, ARRAY_A );

		if ( ! $result ) {
			return null;
		}

		// Parse schema JSON.
		$result['schema'] = DCF_Schema_Parser::parse( $result['schema_json'] );
		if ( is_wp_error( $result['schema'] ) ) {
			$result['schema'] = array();
		}

		return $result;
	}


	/**
	 * Get all content group types.
	 *
	 * @return array Array of type data arrays.
	 */
	public static function get_all(): array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_types' );

		// Get all types ordered by name.
		$query = "SELECT * FROM {$table_name} ORDER BY name ASC";

		$results = $wpdb->get_results( $query, ARRAY_A );

		if ( ! $results ) {
			return array();
		}

		// Parse schema JSON for each type.
		foreach ( $results as &$result ) {
			$result['schema'] = DCF_Schema_Parser::parse( $result['schema_json'] );
			if ( is_wp_error( $result['schema'] ) ) {
				$result['schema'] = array();
			}
		}

		return $results;
	}

	/**
	 * Update a content group type.
	 *
	 * @param int   $type_id Type ID.
	 * @param array $data    Data to update.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function update( int $type_id, array $data ) {
		global $wpdb;

		// Check if type exists.
		$existing = self::get( $type_id );
		if ( ! $existing ) {
			return new WP_Error(
				'type_not_found',
				__( 'Group type not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Prepare update data.
		$update_data   = array();
		$update_format = array();

		// Update name if provided.
		if ( isset( $data['name'] ) && ! empty( $data['name'] ) ) {
			$update_data['name'] = sanitize_text_field( $data['name'] );
			$update_format[]     = '%s';
		}


		// Update slug if provided.
		if ( isset( $data['slug'] ) && ! empty( $data['slug'] ) ) {
			// Check if new slug is unique (excluding current type).
			$slug_check = self::get_by_slug( $data['slug'] );
			if ( $slug_check && (int) $slug_check['id'] !== $type_id ) {
				return new WP_Error(
					'duplicate_slug',
					sprintf(
						/* translators: %s: slug */
						__( 'A type with slug "%s" already exists', 'elementor-dynamic-content-framework' ),
						$data['slug']
					)
				);
			}

			$update_data['slug'] = sanitize_title( $data['slug'] );
			$update_format[]     = '%s';
		}

		// Update schema if provided.
		if ( isset( $data['schema'] ) && is_array( $data['schema'] ) ) {
			// Validate schema.
			$validation = DCF_Schema_Validator::validate( $data['schema'] );
			if ( is_wp_error( $validation ) ) {
				return $validation;
			}

			// Serialize schema to JSON.
			$update_data['schema_json'] = DCF_Schema_Printer::print( $data['schema'], true );
			$update_format[]            = '%s';
		}

		// If no data to update, return success.
		if ( empty( $update_data ) ) {
			return true;
		}

		// Update in database.
		$table_name = DCF_Database::get_table_name( 'group_types' );

		$result = $wpdb->update(
			$table_name,
			$update_data,
			array( 'id' => $type_id ),
			$update_format,
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_update_error',
				__( 'Failed to update group type', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		return true;
	}


	/**
	 * Delete a content group type.
	 *
	 * @param int $type_id Type ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete( int $type_id ) {
		global $wpdb;

		// Check if type exists.
		$existing = self::get( $type_id );
		if ( ! $existing ) {
			return new WP_Error(
				'type_not_found',
				__( 'Group type not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Check if there are associated groups.
		$groups_count = self::get_groups_count( $type_id );
		if ( $groups_count > 0 ) {
			return new WP_Error(
				'type_has_groups',
				sprintf(
					/* translators: %d: number of groups */
					_n(
						'Cannot delete type. It has %d associated group.',
						'Cannot delete type. It has %d associated groups.',
						$groups_count,
						'elementor-dynamic-content-framework'
					),
					$groups_count
				)
			);
		}

		// Delete from database.
		$table_name = DCF_Database::get_table_name( 'group_types' );

		$result = $wpdb->delete(
			$table_name,
			array( 'id' => $type_id ),
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_delete_error',
				__( 'Failed to delete group type', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		return true;
	}

	/**
	 * Get the count of groups associated with a type.
	 *
	 * @param int $type_id Type ID.
	 * @return int Number of associated groups.
	 */
	public static function get_groups_count( int $type_id ): int {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'groups' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_name} WHERE type_id = %d",
			$type_id
		);

		$count = $wpdb->get_var( $query );

		return (int) $count;
	}
}
