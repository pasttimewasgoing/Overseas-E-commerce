<?php
/**
 * Group Model Class
 *
 * Manages content group instances.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/models
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group class.
 *
 * Handles CRUD operations for content groups.
 */
class DCF_Group {

	/**
	 * Create a new content group.
	 *
	 * @param array $data {
	 *     Content group data.
	 *
	 *     @type int    $type_id Type ID (required).
	 *     @type string $title   Title (required).
	 *     @type string $status  Status (active|inactive|draft), default 'draft'.
	 * }
	 * @return int|WP_Error Group ID on success, WP_Error on failure.
	 */
	public static function create( array $data ) {
		global $wpdb;

		// Validate required fields.
		if ( empty( $data['type_id'] ) ) {
			return new WP_Error(
				'missing_type_id',
				__( 'Type ID is required', 'elementor-dynamic-content-framework' )
			);
		}

		if ( empty( $data['title'] ) ) {
			return new WP_Error(
				'missing_title',
				__( 'Title is required', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate type_id exists.
		$type = DCF_Group_Type::get( (int) $data['type_id'] );
		if ( ! $type ) {
			return new WP_Error(
				'invalid_type_id',
				__( 'The specified type does not exist', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate status.
		$status = isset( $data['status'] ) ? $data['status'] : 'draft';
		if ( ! in_array( $status, array( 'active', 'inactive', 'draft' ), true ) ) {
			return new WP_Error(
				'invalid_status',
				__( 'Status must be active, inactive, or draft', 'elementor-dynamic-content-framework' )
			);
		}

		// Prepare data for insertion.
		$table_name = DCF_Database::get_table_name( 'groups' );

		// Insert into database using prepared statement.
		$result = $wpdb->insert(
			$table_name,
			array(
				'type_id' => (int) $data['type_id'],
				'title'   => sanitize_text_field( $data['title'] ),
				'status'  => $status,
			),
			array( '%d', '%s', '%s' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_insert_error',
				__( 'Failed to create group', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		return $wpdb->insert_id;
	}

	/**
	 * Get a content group by ID.
	 *
	 * @param int $group_id Group ID.
	 * @return array|null Group data array or null if not found.
	 */
	public static function get( int $group_id ): ?array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'groups' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d",
			$group_id
		);

		$result = $wpdb->get_row( $query, ARRAY_A );

		return $result ?: null;
	}

	/**
	 * Get all content groups.
	 *
	 * @param array $args {
	 *     Optional query arguments.
	 *
	 *     @type int    $type_id  Filter by type ID.
	 *     @type string $status   Filter by status.
	 *     @type int    $per_page Number of items per page.
	 *     @type int    $page     Page number (1-indexed).
	 * }
	 * @return array Array of group data arrays.
	 */
	public static function get_all( array $args = array() ): array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'groups' );

		// Build WHERE clause.
		$where_clauses = array();
		$where_values  = array();

		if ( ! empty( $args['type_id'] ) ) {
			$where_clauses[] = 'type_id = %d';
			$where_values[]  = (int) $args['type_id'];
		}

		if ( ! empty( $args['status'] ) ) {
			$where_clauses[] = 'status = %s';
			$where_values[]  = $args['status'];
		}

		$where_sql = '';
		if ( ! empty( $where_clauses ) ) {
			$where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
		}

		// Build pagination.
		$limit_sql = '';
		if ( ! empty( $args['per_page'] ) ) {
			$per_page = (int) $args['per_page'];
			$page     = ! empty( $args['page'] ) ? (int) $args['page'] : 1;
			$offset   = ( $page - 1 ) * $per_page;

			$limit_sql = $wpdb->prepare( 'LIMIT %d OFFSET %d', $per_page, $offset );
		}

		// Build query.
		$query = "SELECT * FROM {$table_name} {$where_sql} ORDER BY created_at DESC {$limit_sql}";

		// Prepare query if we have WHERE values.
		if ( ! empty( $where_values ) ) {
			$query = $wpdb->prepare( $query, $where_values );
		}

		$results = $wpdb->get_results( $query, ARRAY_A );

		return $results ?: array();
	}

	/**
	 * Update a content group.
	 *
	 * @param int   $group_id Group ID.
	 * @param array $data     Data to update.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function update( int $group_id, array $data ) {
		global $wpdb;

		// Check if group exists.
		$existing = self::get( $group_id );
		if ( ! $existing ) {
			return new WP_Error(
				'group_not_found',
				__( 'Group not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Prepare update data.
		$update_data   = array();
		$update_format = array();

		// Update title if provided.
		if ( isset( $data['title'] ) && ! empty( $data['title'] ) ) {
			$update_data['title'] = sanitize_text_field( $data['title'] );
			$update_format[]      = '%s';
		}

		// Update status if provided.
		if ( isset( $data['status'] ) ) {
			if ( ! in_array( $data['status'], array( 'active', 'inactive', 'draft' ), true ) ) {
				return new WP_Error(
					'invalid_status',
					__( 'Status must be active, inactive, or draft', 'elementor-dynamic-content-framework' )
				);
			}
			$update_data['status'] = $data['status'];
			$update_format[]       = '%s';
		}

		// Update type_id if provided.
		if ( isset( $data['type_id'] ) ) {
			// Validate type_id exists.
			$type = DCF_Group_Type::get( (int) $data['type_id'] );
			if ( ! $type ) {
				return new WP_Error(
					'invalid_type_id',
					__( 'The specified type does not exist', 'elementor-dynamic-content-framework' )
				);
			}
			$update_data['type_id'] = (int) $data['type_id'];
			$update_format[]        = '%d';
		}

		// If no data to update, return success.
		if ( empty( $update_data ) ) {
			return true;
		}

		// Update in database.
		$table_name = DCF_Database::get_table_name( 'groups' );

		$result = $wpdb->update(
			$table_name,
			$update_data,
			array( 'id' => $group_id ),
			$update_format,
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_update_error',
				__( 'Failed to update group', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		// Invalidate cache for this group.
		DCF_Cache_Manager::invalidate_group( $group_id );

		return true;
	}

	/**
	 * Delete a content group and all associated items.
	 *
	 * @param int $group_id Group ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete( int $group_id ) {
		global $wpdb;

		// Check if group exists.
		$existing = self::get( $group_id );
		if ( ! $existing ) {
			return new WP_Error(
				'group_not_found',
				__( 'Group not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Delete from database (CASCADE will delete associated items).
		$table_name = DCF_Database::get_table_name( 'groups' );

		$result = $wpdb->delete(
			$table_name,
			array( 'id' => $group_id ),
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_delete_error',
				__( 'Failed to delete group', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		// Invalidate cache for this group.
		DCF_Cache_Manager::invalidate_group( $group_id );

		return true;
	}

	/**
	 * Get all items for a content group.
	 *
	 * @param int $group_id Group ID.
	 * @return array Array of item data arrays, ordered by sort_order.
	 */
	public static function get_items( int $group_id ): array {
		global $wpdb;

		// Check cache first.
		$cache_key = "dcf_group_items_{$group_id}";
		$cached    = DCF_Cache_Manager::get( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Query database.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE group_id = %d ORDER BY sort_order ASC, id ASC",
			$group_id
		);

		$results = $wpdb->get_results( $query, ARRAY_A );

		if ( ! $results ) {
			$results = array();
		}

		// Get group to find its type
		$group = self::get( $group_id );
		$group_type = null;
		if ( $group && isset( $group['type_id'] ) ) {
			$group_type = DCF_Group_Type::get( $group['type_id'] );
		}

		// Deserialize data_json for each item.
		foreach ( $results as &$item ) {
			$item['data'] = DCF_Data_Serializer::deserialize( $item['data_json'] );
			
			// Process media fields (convert IDs to full objects)
			if ( $group_type && isset( $group_type['schema'] ) ) {
				$item['data'] = self::process_media_fields( $item['data'], $group_type['schema'] );
			}
		}

		// Cache the results (expiration time is configured in DCF_Cache_Manager).
		DCF_Cache_Manager::set( $cache_key, $results );

		return $results;
	}

	/**
	 * Process media fields in item data.
	 * Converts media IDs to full media objects with URL, dimensions, etc.
	 *
	 * @param array $data Item data.
	 * @param array $schema Group type schema.
	 * @return array Processed data.
	 */
	private static function process_media_fields( array $data, array $schema ): array {
		foreach ( $schema as $field ) {
			$field_name = isset( $field['name'] ) ? $field['name'] : '';
			$field_type = isset( $field['type'] ) ? $field['type'] : '';

			if ( empty( $field_name ) || ! isset( $data[ $field_name ] ) ) {
				continue;
			}

			$field_value = $data[ $field_name ];

			// Process image fields
			if ( 'image' === $field_type && is_numeric( $field_value ) && $field_value > 0 ) {
				$attachment_id = (int) $field_value;
				$data[ $field_name ] = self::get_attachment_data( $attachment_id );
			}

			// Process video fields
			elseif ( 'video' === $field_type && is_numeric( $field_value ) && $field_value > 0 ) {
				$attachment_id = (int) $field_value;
				$data[ $field_name ] = self::get_attachment_data( $attachment_id, 'video' );
			}

			// Process gallery fields
			elseif ( 'gallery' === $field_type && ! empty( $field_value ) ) {
				$gallery_ids = is_string( $field_value ) ? explode( ',', $field_value ) : array();
				$gallery_data = array();

				foreach ( $gallery_ids as $attachment_id ) {
					$attachment_id = (int) trim( $attachment_id );
					if ( $attachment_id > 0 ) {
						$gallery_data[] = self::get_attachment_data( $attachment_id );
					}
				}

				$data[ $field_name ] = $gallery_data;
			}
		}

		return $data;
	}

	/**
	 * Get attachment data as an array.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $type          Media type (image or video).
	 * @return array Attachment data.
	 */
	private static function get_attachment_data( int $attachment_id, string $type = 'image' ): array {
		$url = wp_get_attachment_url( $attachment_id );

		if ( ! $url ) {
			return array(
				'id'  => $attachment_id,
				'url' => '',
			);
		}

		$data = array(
			'id'  => $attachment_id,
			'url' => $url,
			'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		);

		if ( 'image' === $type ) {
			$metadata = wp_get_attachment_metadata( $attachment_id );
			if ( $metadata ) {
				$data['width']  = isset( $metadata['width'] ) ? $metadata['width'] : 0;
				$data['height'] = isset( $metadata['height'] ) ? $metadata['height'] : 0;
			}
		} elseif ( 'video' === $type ) {
			$data['mime_type'] = get_post_mime_type( $attachment_id );
		}

		return $data;
	}

	/**
	 * Get the count of items in a content group.
	 *
	 * @param int $group_id Group ID.
	 * @return int Number of items.
	 */
	public static function get_items_count( int $group_id ): int {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_items' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table_name} WHERE group_id = %d",
			$group_id
		);

		$count = $wpdb->get_var( $query );

		return (int) $count;
	}
}
