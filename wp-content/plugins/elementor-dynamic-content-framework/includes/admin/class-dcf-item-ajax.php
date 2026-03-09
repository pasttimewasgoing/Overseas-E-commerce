<?php
/**
 * Item AJAX Handler
 *
 * Handles AJAX requests for item operations.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Item_Ajax class.
 *
 * Handles AJAX operations for content items.
 */
class DCF_Item_Ajax {

	/**
	 * Initialize AJAX handlers
	 */
	public static function init() {
		add_action( 'wp_ajax_dcf_duplicate_item', array( __CLASS__, 'duplicate_item' ) );
	}

	/**
	 * Handle duplicate item AJAX request
	 */
	public static function duplicate_item() {
		// Check nonce
		check_ajax_referer( 'dcf_item_editor', 'nonce' );

		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => __( 'Permission denied', 'elementor-dynamic-content-framework' )
			) );
		}

		// Get item ID
		$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;

		if ( $item_id <= 0 ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid item ID', 'elementor-dynamic-content-framework' )
			) );
		}

		// Duplicate the item
		$new_item_id = DCF_Group_Item::duplicate( $item_id );

		if ( is_wp_error( $new_item_id ) ) {
			wp_send_json_error( array(
				'message' => $new_item_id->get_error_message()
			) );
		}

		// Get the new item data
		$new_item = DCF_Group_Item::get( $new_item_id );

		if ( ! $new_item ) {
			wp_send_json_error( array(
				'message' => __( 'Failed to retrieve duplicated item', 'elementor-dynamic-content-framework' )
			) );
		}

		// Return success with new item data
		wp_send_json_success( array(
			'message' => __( 'Item duplicated successfully', 'elementor-dynamic-content-framework' ),
			'item_id' => $new_item_id,
			'item' => $new_item
		) );
	}
}
