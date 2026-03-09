<?php
/**
 * Import/Export Class
 *
 * Handles import and export of content groups.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Import_Export class.
 *
 * Handles exporting and importing content groups with their data.
 */
class DCF_Import_Export {

	/**
	 * Initialize import/export functionality
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'handle_export_request' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_import_request' ) );
	}

	/**
	 * Handle export request
	 *
	 * @since 1.0.0
	 */
	public static function handle_export_request() {
		// Check if export action is requested
		if ( ! isset( $_GET['dcf_action'] ) || 'export_group' !== $_GET['dcf_action'] ) {
			return;
		}

		// Check if group ID is provided
		if ( ! isset( $_GET['group_id'] ) ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dcf_export_group' ) ) {
			wp_die( esc_html__( 'Security check failed', 'elementor-dynamic-content-framework' ) );
		}

		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action', 'elementor-dynamic-content-framework' ) );
		}

		$group_id = (int) wp_unslash( $_GET['group_id'] );

		// Export the group
		$export_data = self::export_group( $group_id );

		if ( is_wp_error( $export_data ) ) {
			wp_die( esc_html( $export_data->get_error_message() ) );
		}

		// Generate filename
		$group = DCF_Group::get( $group_id );
		$filename = 'dcf-export-' . sanitize_file_name( $group['title'] ) . '-' . gmdate( 'Y-m-d-H-i-s' ) . '.json';

		// Send headers for file download
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Output JSON
		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		exit;
	}

	/**
	 * Export a content group to JSON
	 *
	 * @since 1.0.0
	 *
	 * @param int $group_id Group ID to export
	 * @return array|WP_Error Export data array or WP_Error on failure
	 */
	public static function export_group( int $group_id ) {
		// Get group
		$group = DCF_Group::get( $group_id );
		if ( ! $group ) {
			return new WP_Error(
				'group_not_found',
				__( 'Group not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Get group type
		$group_type = DCF_Group_Type::get( $group['type_id'] );
		if ( ! $group_type ) {
			return new WP_Error(
				'type_not_found',
				__( 'Group type not found', 'elementor-dynamic-content-framework' )
			);
		}

		// Get all items for the group
		$items = DCF_Group::get_items( $group_id );

		// Build export data structure
		$export_data = array(
			'version'    => '1.0',
			'exported_at' => gmdate( 'Y-m-d H:i:s' ),
			'group_type' => array(
				'name'   => $group_type['name'],
				'slug'   => $group_type['slug'],
				'schema' => $group_type['schema'],
			),
			'group'      => array(
				'title'      => $group['title'],
				'status'     => $group['status'],
				'created_at' => $group['created_at'],
				'updated_at' => $group['updated_at'],
			),
			'items'      => array(),
		);

		// Add items to export data
		foreach ( $items as $item ) {
			$export_data['items'][] = array(
				'data'       => $item['data'],
				'sort_order' => $item['sort_order'],
				'created_at' => $item['created_at'],
				'updated_at' => $item['updated_at'],
			);
		}

		return $export_data;
	}

	/**
	 * Get export button HTML
	 *
	 * @since 1.0.0
	 *
	 * @param int $group_id Group ID
	 * @return string HTML for export button
	 */
	public static function get_export_button( int $group_id ): string {
		$export_url = wp_nonce_url(
			admin_url( 'admin.php?dcf_action=export_group&group_id=' . $group_id ),
			'dcf_export_group'
		);

		return sprintf(
			'<a href="%s" class="button button-small">%s</a>',
			esc_url( $export_url ),
			esc_html__( 'Export', 'elementor-dynamic-content-framework' )
		);
	}

	/**
	 * Handle import request
	 *
	 * @since 1.0.0
	 */
	public static function handle_import_request() {
		// Check if import action is requested
		if ( ! isset( $_POST['dcf_action'] ) || 'import_group' !== $_POST['dcf_action'] ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'dcf_import_group' ) ) {
			wp_die( esc_html__( 'Security check failed', 'elementor-dynamic-content-framework' ) );
		}

		// Check capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action', 'elementor-dynamic-content-framework' ) );
		}

		// Check if file is uploaded
		if ( ! isset( $_FILES['dcf_import_file'] ) ) {
			wp_die( esc_html__( 'No file uploaded', 'elementor-dynamic-content-framework' ) );
		}

		$file = $_FILES['dcf_import_file'];

		// Validate file
		if ( ! isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) ) {
			wp_die( esc_html__( 'File upload failed', 'elementor-dynamic-content-framework' ) );
		}

		// Check file type
		if ( 'application/json' !== $file['type'] && 'text/plain' !== $file['type'] ) {
			wp_die( esc_html__( 'Invalid file type. Please upload a JSON file.', 'elementor-dynamic-content-framework' ) );
		}

		// Read file content
		$file_content = file_get_contents( $file['tmp_name'] );

		if ( false === $file_content ) {
			wp_die( esc_html__( 'Failed to read file', 'elementor-dynamic-content-framework' ) );
		}

		// Get import options
		$conflict_action = isset( $_POST['dcf_conflict_action'] ) ? sanitize_text_field( wp_unslash( $_POST['dcf_conflict_action'] ) ) : 'skip';
		$media_action    = isset( $_POST['dcf_media_action'] ) ? sanitize_text_field( wp_unslash( $_POST['dcf_media_action'] ) ) : 'reference';

		// Import the group
		$result = self::import_group( $file_content, $conflict_action, $media_action );

		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		// Redirect with success message
		wp_safe_remote_post(
			admin_url( 'admin.php?page=dcf-import-export' ),
			array(
				'blocking' => false,
			)
		);

		wp_redirect( admin_url( 'admin.php?page=dcf-import-export&dcf_import_success=1' ) );
		exit;
	}

	/**
	 * Import a content group from JSON
	 *
	 * @since 1.0.0
	 *
	 * @param string $json_content JSON content to import
	 * @param string $conflict_action Action to take on slug conflict ('merge' or 'skip')
	 * @param string $media_action Action to take with media ('import' or 'reference')
	 * @return array|WP_Error Import result array or WP_Error on failure
	 */
	public static function import_group( string $json_content, string $conflict_action = 'skip', string $media_action = 'reference' ) {
		// Validate JSON
		$import_data = json_decode( $json_content, true );

		if ( null === $import_data ) {
			return new WP_Error(
				'invalid_json',
				__( 'Invalid JSON format', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate file structure
		$validation = self::validate_import_data( $import_data );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		// Extract import data
		$group_type_data = $import_data['group_type'];
		$group_data      = $import_data['group'];
		$items_data      = $import_data['items'];

		// Check for existing group type with same slug
		$existing_type = DCF_Group_Type::get_by_slug( $group_type_data['slug'] );

		if ( $existing_type ) {
			if ( 'skip' === $conflict_action ) {
				// Log and skip import
				DCF_Logger::info(
					'Import skipped due to existing group type slug',
					array(
						'slug'           => $group_type_data['slug'],
						'conflict_action' => $conflict_action,
					)
				);

				return new WP_Error(
					'group_type_exists',
					sprintf(
						__( 'Group type with slug "%s" already exists. Import skipped.', 'elementor-dynamic-content-framework' ),
						esc_html( $group_type_data['slug'] )
					)
				);
			} elseif ( 'merge' === $conflict_action ) {
				// Use existing type
				$type_id = $existing_type['id'];
				DCF_Logger::info(
					'Import merged with existing group type',
					array(
						'slug'           => $group_type_data['slug'],
						'type_id'        => $type_id,
						'conflict_action' => $conflict_action,
					)
				);
			}
		} else {
			// Create new group type
			$type_id = DCF_Group_Type::create( array(
				'name'   => $group_type_data['name'],
				'slug'   => $group_type_data['slug'],
				'schema' => $group_type_data['schema'],
			) );

			if ( is_wp_error( $type_id ) ) {
				DCF_Logger::error(
					'Failed to create group type during import',
					array(
						'slug'  => $group_type_data['slug'],
						'error' => $type_id->get_error_message(),
					)
				);

				return $type_id;
			}

			DCF_Logger::info(
				'Group type created during import',
				array(
					'slug'    => $group_type_data['slug'],
					'type_id' => $type_id,
				)
			);
		}

		// Create group
		$group_id = DCF_Group::create( array(
			'type_id' => $type_id,
			'title'   => $group_data['title'],
			'status'  => $group_data['status'],
		) );

		if ( is_wp_error( $group_id ) ) {
			DCF_Logger::error(
				'Failed to create group during import',
				array(
					'title' => $group_data['title'],
					'error' => $group_id->get_error_message(),
				)
			);

			return $group_id;
		}

		DCF_Logger::info(
			'Group created during import',
			array(
				'group_id' => $group_id,
				'title'    => $group_data['title'],
			)
		);

		// Import items
		$imported_items = 0;
		$failed_items   = 0;

		foreach ( $items_data as $index => $item_data ) {
			// Process media if needed
			$processed_data = self::process_import_item_media( $item_data['data'], $media_action );

			// Create item
			$item_id = DCF_Group_Item::create( array(
				'group_id'   => $group_id,
				'data'       => $processed_data,
				'sort_order' => $item_data['sort_order'],
			) );

			if ( is_wp_error( $item_id ) ) {
				$failed_items++;
				DCF_Logger::warning(
					'Failed to create item during import',
					array(
						'group_id' => $group_id,
						'index'    => $index,
						'error'    => $item_id->get_error_message(),
					)
				);
			} else {
				$imported_items++;
			}
		}

		// Log import completion
		DCF_Logger::info(
			'Import completed',
			array(
				'group_id'       => $group_id,
				'imported_items' => $imported_items,
				'failed_items'   => $failed_items,
				'conflict_action' => $conflict_action,
				'media_action'   => $media_action,
			)
		);

		return array(
			'group_id'       => $group_id,
			'type_id'        => $type_id,
			'imported_items' => $imported_items,
			'failed_items'   => $failed_items,
		);
	}

	/**
	 * Validate import data structure
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Import data to validate
	 * @return true|WP_Error
	 */
	public static function validate_import_data( array $data ) {
		// Check required top-level keys
		$required_keys = array( 'version', 'group_type', 'group', 'items' );
		foreach ( $required_keys as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return new WP_Error(
					'missing_key',
					sprintf(
						__( 'Missing required key in import file: %s', 'elementor-dynamic-content-framework' ),
						esc_html( $key )
					)
				);
			}
		}

		// Validate version
		if ( '1.0' !== $data['version'] ) {
			return new WP_Error(
				'unsupported_version',
				sprintf(
					__( 'Unsupported import file version: %s', 'elementor-dynamic-content-framework' ),
					esc_html( $data['version'] )
				)
			);
		}

		// Validate group_type structure
		$type_keys = array( 'name', 'slug', 'schema' );
		foreach ( $type_keys as $key ) {
			if ( ! isset( $data['group_type'][ $key ] ) ) {
				return new WP_Error(
					'invalid_group_type',
					sprintf(
						__( 'Invalid group type structure: missing %s', 'elementor-dynamic-content-framework' ),
						esc_html( $key )
					)
				);
			}
		}

		// Validate group structure
		$group_keys = array( 'title', 'status' );
		foreach ( $group_keys as $key ) {
			if ( ! isset( $data['group'][ $key ] ) ) {
				return new WP_Error(
					'invalid_group',
					sprintf(
						__( 'Invalid group structure: missing %s', 'elementor-dynamic-content-framework' ),
						esc_html( $key )
					)
				);
			}
		}

		// Validate items is array
		if ( ! is_array( $data['items'] ) ) {
			return new WP_Error(
				'invalid_items',
				__( 'Items must be an array', 'elementor-dynamic-content-framework' )
			);
		}

		// Validate each item
		foreach ( $data['items'] as $index => $item ) {
			if ( ! is_array( $item ) ) {
				return new WP_Error(
					'invalid_item',
					sprintf(
						__( 'Item %d is not an array', 'elementor-dynamic-content-framework' ),
						$index
					)
				);
			}

			if ( ! isset( $item['data'] ) || ! is_array( $item['data'] ) ) {
				return new WP_Error(
					'invalid_item_data',
					sprintf(
						__( 'Item %d has invalid data structure', 'elementor-dynamic-content-framework' ),
						$index
					)
				);
			}

			if ( ! isset( $item['sort_order'] ) ) {
				return new WP_Error(
					'invalid_item_sort_order',
					sprintf(
						__( 'Item %d is missing sort_order', 'elementor-dynamic-content-framework' ),
						$index
					)
				);
			}
		}

		return true;
	}

	/**
	 * Process media in import item data
	 *
	 * @since 1.0.0
	 *
	 * @param array  $item_data Item data to process
	 * @param string $media_action Action to take with media ('import' or 'reference')
	 * @return array Processed item data
	 */
	public static function process_import_item_media( array $item_data, string $media_action = 'reference' ): array {
		if ( 'reference' === $media_action ) {
			// Keep media references as-is
			return $item_data;
		}

		if ( 'import' === $media_action ) {
			// Import media files from URLs
			return self::import_media_from_urls( $item_data );
		}

		return $item_data;
	}

	/**
	 * Import media files from URLs in item data
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_data Item data containing media URLs
	 * @return array Item data with imported media
	 */
	public static function import_media_from_urls( array $item_data ): array {
		foreach ( $item_data as $key => $value ) {
			if ( is_array( $value ) ) {
				// Handle image field
				if ( isset( $value['url'] ) && is_string( $value['url'] ) ) {
					$attachment_id = self::import_media_file( $value['url'] );
					if ( $attachment_id ) {
						$item_data[ $key ]['id'] = $attachment_id;
					}
				}

				// Handle repeater fields recursively
				if ( isset( $value[0] ) && is_array( $value[0] ) ) {
					foreach ( $value as $index => $sub_item ) {
						if ( is_array( $sub_item ) ) {
							$item_data[ $key ][ $index ] = self::import_media_from_urls( $sub_item );
						}
					}
				}
			}
		}

		return $item_data;
	}

	/**
	 * Import a media file from URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Media file URL
	 * @return int|false Attachment ID on success, false on failure
	 */
	public static function import_media_file( string $url ) {
		// Check if URL is valid
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			DCF_Logger::warning(
				'Invalid media URL during import',
				array( 'url' => $url )
			);
			return false;
		}

		// Download file
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			DCF_Logger::warning(
				'Failed to download media file during import',
				array(
					'url'   => $url,
					'error' => $response->get_error_message(),
				)
			);
			return false;
		}

		$file_content = wp_remote_retrieve_body( $response );

		if ( empty( $file_content ) ) {
			DCF_Logger::warning(
				'Empty media file downloaded during import',
				array( 'url' => $url )
			);
			return false;
		}

		// Get filename from URL
		$filename = basename( wp_parse_url( $url, PHP_URL_PATH ) );

		if ( empty( $filename ) ) {
			$filename = 'imported-media-' . time();
		}

		// Upload file to WordPress media library
		$upload = wp_upload_bits( $filename, null, $file_content );

		if ( ! empty( $upload['error'] ) ) {
			DCF_Logger::warning(
				'Failed to upload media file during import',
				array(
					'url'   => $url,
					'error' => $upload['error'],
				)
			);
			return false;
		}

		// Create attachment post
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => $upload['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) ) {
			DCF_Logger::warning(
				'Failed to create attachment during import',
				array(
					'url'   => $url,
					'error' => $attachment_id->get_error_message(),
				)
			);
			return false;
		}

		// Generate attachment metadata
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $attach_data );

		DCF_Logger::info(
			'Media file imported successfully',
			array(
				'url'             => $url,
				'attachment_id'   => $attachment_id,
				'filename'        => $filename,
			)
		);

		return $attachment_id;
	}
}
