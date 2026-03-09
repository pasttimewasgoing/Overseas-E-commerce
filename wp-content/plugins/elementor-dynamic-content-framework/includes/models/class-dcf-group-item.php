<?php
/**
 * Group Item Model Class
 *
 * Manages content item data within groups.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/models
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_Item class.
 *
 * Handles CRUD operations for content items.
 */
class DCF_Group_Item {

	/**
	 * Create a new content item.
	 *
	 * @param array $data {
	 *     Content item data.
	 *
	 *     @type int   $group_id   Group ID (required).
	 *     @type array $data       Field data array (required).
	 *     @type int   $sort_order Sort order value (optional, default 0).
	 * }
	 * @return int|WP_Error Item ID on success, WP_Error on failure.
	 */
	public static function create( array $data ) {
		global $wpdb;

		// Validate required fields.
		if ( empty( $data['group_id'] ) ) {
			return new WP_Error(
				'missing_group_id',
				__( 'Group ID is required', 'elementor-dynamic-content-framework' )
			);
		}

		if ( ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
			return new WP_Error(
				'missing_data',
				__( 'Data is required and must be an array', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate group_id exists.
		$group = DCF_Group::get( (int) $data['group_id'] );
		if ( ! $group ) {
			return new WP_Error(
				'invalid_group_id',
				__( 'The specified group does not exist', 'elementor-dynamic-content-framework' )
			);
		}

		// Serialize data to JSON.
		$data_json = DCF_Data_Serializer::serialize( $data['data'] );
		if ( false === $data_json ) {
			return new WP_Error(
				'serialization_error',
				__( 'Failed to serialize item data', 'elementor-dynamic-content-framework' )
			);
		}

		// Get sort_order value.
		$sort_order = isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0;

		// Prepare data for insertion.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		// Insert into database using prepared statement.
		$result = $wpdb->insert(
			$table_name,
			array(
				'group_id'   => (int) $data['group_id'],
				'data_json'  => $data_json,
				'sort_order' => $sort_order,
			),
			array( '%d', '%s', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_insert_error',
				__( 'Failed to create group item', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		// Invalidate cache for this group.
		DCF_Cache_Manager::invalidate_group( (int) $data['group_id'] );

		return $wpdb->insert_id;
	}

	/**
	 * Get a content item by ID.
	 *
	 * @param int $item_id Item ID.
	 * @return array|null Item data array or null if not found.
	 */
	public static function get( int $item_id ): ?array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_items' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d",
			$item_id
		);

		$result = $wpdb->get_row( $query, ARRAY_A );

		if ( ! $result ) {
			return null;
		}

		// Deserialize data_json.
		$result['data'] = DCF_Data_Serializer::deserialize( $result['data_json'] );

		return $result;
	}

	/**
	 * Get all content items for a group, ordered by sort_order.
	 *
	 * @param int $group_id Group ID.
	 * @return array Array of item data arrays, ordered by sort_order ascending.
	 */
	public static function get_by_group( int $group_id ): array {
		global $wpdb;

		$table_name = DCF_Database::get_table_name( 'group_items' );

		// Use prepared statement to prevent SQL injection.
		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE group_id = %d ORDER BY sort_order ASC, id ASC",
			$group_id
		);

		$results = $wpdb->get_results( $query, ARRAY_A );

		if ( ! $results ) {
			return array();
		}

		// Deserialize data_json for each item.
		foreach ( $results as &$item ) {
			$item['data'] = DCF_Data_Serializer::deserialize( $item['data_json'] );
		}

		return $results;
	}

	/**
	 * Update a content item.
	 *
	 * @param int   $item_id Item ID.
	 * @param array $data    Data to update.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function update( int $item_id, array $data ) {
		global $wpdb;

		// Check if item exists.
		$existing = self::get( $item_id );
		if ( ! $existing ) {
			return new WP_Error(
				'item_not_found',
				__( 'Group item not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Prepare update data.
		$update_data   = array();
		$update_format = array();

		// Update data if provided.
		if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
			// Serialize data to JSON.
			$data_json = DCF_Data_Serializer::serialize( $data['data'] );
			if ( false === $data_json ) {
				return new WP_Error(
					'serialization_error',
					__( 'Failed to serialize item data', 'elementor-dynamic-content-framework' )
				);
			}

			$update_data['data_json'] = $data_json;
			$update_format[]          = '%s';
		}

		// Update sort_order if provided.
		if ( isset( $data['sort_order'] ) ) {
			$update_data['sort_order'] = (int) $data['sort_order'];
			$update_format[]           = '%d';
		}

		// Update group_id if provided.
		if ( isset( $data['group_id'] ) ) {
			// Validate group_id exists.
			$group = DCF_Group::get( (int) $data['group_id'] );
			if ( ! $group ) {
				return new WP_Error(
					'invalid_group_id',
					__( 'The specified group does not exist', 'elementor-dynamic-content-framework' )
				);
			}
			$update_data['group_id'] = (int) $data['group_id'];
			$update_format[]         = '%d';
		}

		// If no data to update, return success.
		if ( empty( $update_data ) ) {
			return true;
		}

		// Update in database.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		$result = $wpdb->update(
			$table_name,
			$update_data,
			array( 'id' => $item_id ),
			$update_format,
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_update_error',
				__( 'Failed to update group item', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		// Invalidate cache for the original group.
		DCF_Cache_Manager::invalidate_group( (int) $existing['group_id'] );

		// If group_id was changed, invalidate cache for the new group too.
		if ( isset( $data['group_id'] ) && (int) $data['group_id'] !== (int) $existing['group_id'] ) {
			DCF_Cache_Manager::invalidate_group( (int) $data['group_id'] );
		}

		return true;
	}

	/**
	 * Delete a content item.
	 *
	 * @param int $item_id Item ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function delete( int $item_id ) {
		global $wpdb;

		// Check if item exists.
		$existing = self::get( $item_id );
		if ( ! $existing ) {
			return new WP_Error(
				'item_not_found',
				__( 'Group item not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Delete from database.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		$result = $wpdb->delete(
			$table_name,
			array( 'id' => $item_id ),
			array( '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'db_delete_error',
				__( 'Failed to delete group item', 'elementor-dynamic-content-framework' ),
				array( 'db_error' => $wpdb->last_error )
			);
		}

		// Invalidate cache for this group.
		DCF_Cache_Manager::invalidate_group( (int) $existing['group_id'] );

		return true;
	}

	/**
	 * Batch update sort order for multiple items.
	 *
	 * @param array $order_map Array mapping item_id => sort_order.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function update_order( array $order_map ) {
		global $wpdb;

		if ( empty( $order_map ) ) {
			return new WP_Error(
				'empty_order_map',
				__( 'Order map cannot be empty', 'elementor-dynamic-content-framework' )
			);
		}

		// Track affected groups for cache invalidation.
		$affected_groups = array();

		// Update each item's sort_order.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		foreach ( $order_map as $item_id => $sort_order ) {
			// Validate item exists and get its group_id.
			$item = self::get( (int) $item_id );
			if ( ! $item ) {
				return new WP_Error(
					'item_not_found',
					sprintf(
						/* translators: %d: item ID */
						__( 'Group item with ID %d not found', 'elementor-dynamic-content-framework' ),
						$item_id
					)
				);
			}

			// Track the group for cache invalidation.
			$affected_groups[ $item['group_id'] ] = true;

			// Update sort_order.
			$result = $wpdb->update(
				$table_name,
				array( 'sort_order' => (int) $sort_order ),
				array( 'id' => (int) $item_id ),
				array( '%d' ),
				array( '%d' )
			);

			if ( false === $result ) {
				return new WP_Error(
					'db_update_error',
					sprintf(
						/* translators: %d: item ID */
						__( 'Failed to update sort order for item %d', 'elementor-dynamic-content-framework' ),
						$item_id
					),
					array( 'db_error' => $wpdb->last_error )
				);
			}
		}

		// Invalidate cache for all affected groups.
		foreach ( array_keys( $affected_groups ) as $group_id ) {
			DCF_Cache_Manager::invalidate_group( (int) $group_id );
		}

		return true;
	}

	/**
	 * Duplicate a content item.
	 *
	 * @param int $item_id Item ID to duplicate.
	 * @return int|WP_Error New item ID on success, WP_Error on failure.
	 */
	public static function duplicate( int $item_id ) {
		global $wpdb;

		// Get the original item.
		$original = self::get( $item_id );
		if ( ! $original ) {
			return new WP_Error(
				'item_not_found',
				__( 'Group item not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Get the highest sort_order in the group to place the duplicate at the end.
		$table_name = DCF_Database::get_table_name( 'group_items' );

		$query = $wpdb->prepare(
			"SELECT MAX(sort_order) FROM {$table_name} WHERE group_id = %d",
			$original['group_id']
		);

		$max_sort_order = $wpdb->get_var( $query );
		$new_sort_order = ( null !== $max_sort_order ) ? (int) $max_sort_order + 1 : 0;

		// Create the duplicate item.
		$new_item_id = self::create(
			array(
				'group_id'   => $original['group_id'],
				'data'       => $original['data'],
				'sort_order' => $new_sort_order,
			)
		);

		if ( is_wp_error( $new_item_id ) ) {
			return $new_item_id;
		}

		return $new_item_id;
	}
}
