<?php
/**
 * Item Editor
 *
 * Handles creation and editing of content items with dynamic field rendering.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Item_Editor class.
 *
 * Provides interface for managing content items with dynamic schema-based fields.
 */
class DCF_Item_Editor {

	/**
	 * Render the items editor page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Get group ID
		$group_id = isset( $_GET['group_id'] ) ? (int) wp_unslash( $_GET['group_id'] ) : 0;

		if ( $group_id <= 0 ) {
			wp_die( esc_html__( 'Invalid group ID.', 'elementor-dynamic-content-framework' ) );
		}

		// Load group and type
		$group = DCF_Group::get( $group_id );
		if ( ! $group ) {
			wp_die( esc_html__( 'Group not found.', 'elementor-dynamic-content-framework' ) );
		}

		$group_type = DCF_Group_Type::get( $group['type_id'] );
		if ( ! $group_type ) {
			wp_die( esc_html__( 'Group type not found.', 'elementor-dynamic-content-framework' ) );
		}

		// Check if this is a new item action
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		
		if ( 'new' === $action ) {
			// Render new item form
			self::render_new_item_form( $group, $group_type );
			return;
		}

		// Get all items for this group
		$items = DCF_Group::get_items( $group_id );

		// Handle form submission
		if ( isset( $_POST['dcf_item_nonce'] ) ) {
			self::handle_item_submission( $group_id, $group_type );
		}

		// Handle item deletion
		if ( isset( $_GET['action'] ) && 'delete' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			self::handle_item_delete( $group_id );
		}

		// Render the page
		self::render_items_page( $group, $group_type, $items );
	}

	/**
	 * Render new item form
	 *
	 * @param array $group Group data.
	 * @param array $group_type Group type data.
	 */
	private static function render_new_item_form( $group, $group_type ) {
		?>
		<div class="wrap dcf-admin-page">
			<h1><?php echo esc_html( $group['title'] ); ?> - <?php esc_html_e( 'Add New Item', 'elementor-dynamic-content-framework' ); ?></h1>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=dcf-items&group_id=' . $group['id'] ) ); ?>" class="dcf-item-form" enctype="multipart/form-data">
				<?php wp_nonce_field( 'dcf_item_nonce', 'dcf_item_nonce' ); ?>
				<input type="hidden" name="item_id" value="">
				<input type="hidden" name="group_id" value="<?php echo esc_attr( $group['id'] ); ?>">

				<?php
				// Render fields based on schema
				// Schema is now a direct array of fields, not wrapped in 'fields' key
				if ( isset( $group_type['schema'] ) && is_array( $group_type['schema'] ) ) {
					foreach ( $group_type['schema'] as $field ) {
						self::render_field( $field, array() );
					}
				}
				?>

				<div class="dcf-form-actions">
					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Create Item', 'elementor-dynamic-content-framework' ); ?>
					</button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-items&group_id=' . $group['id'] ) ); ?>" class="button">
						<?php esc_html_e( 'Cancel', 'elementor-dynamic-content-framework' ); ?>
					</a>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the items management page
	 *
	 * @param array $group Group data.
	 * @param array $group_type Group type data.
	 * @param array $items Items data.
	 */
	private static function render_items_page( $group, $group_type, $items ) {
		?>
		<div class="wrap dcf-admin-page">
			<h1><?php echo esc_html( $group['title'] ); ?> - <?php esc_html_e( 'Items', 'elementor-dynamic-content-framework' ); ?></h1>

			<div class="dcf-items-toolbar">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-groups&action=edit&id=' . $group['id'] ) ); ?>" class="button">
					<?php esc_html_e( 'Back to Group', 'elementor-dynamic-content-framework' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-items&group_id=' . $group['id'] . '&action=new' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Add Item', 'elementor-dynamic-content-framework' ); ?>
				</a>
				<?php if ( ! empty( $items ) ) : ?>
					<button type="button" id="dcf-save-items-order" class="button">
						<?php esc_html_e( 'Save Order', 'elementor-dynamic-content-framework' ); ?>
					</button>
				<?php endif; ?>
			</div>

			<div class="dcf-item-editor">
				<input type="hidden" id="dcf-group-id" value="<?php echo esc_attr( $group['id'] ); ?>">
				<input type="hidden" id="dcf-group-type-id" value="<?php echo esc_attr( $group_type['id'] ); ?>">

				<div class="dcf-items-list">
					<?php if ( ! empty( $items ) ) : ?>
						<?php foreach ( $items as $item ) : ?>
							<?php self::render_item_form( $item, $group_type ); ?>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="notice notice-info">
							<p><?php esc_html_e( 'No items yet. Click "Add Item" to create your first item.', 'elementor-dynamic-content-framework' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single item form
	 *
	 * @param array $item Item data.
	 * @param array $group_type Group type data.
	 */
	private static function render_item_form( $item, $group_type ) {
		$item_id = $item['id'];
		$item_data = isset( $item['data'] ) ? $item['data'] : array();
		$sort_order = isset( $item['sort_order'] ) ? $item['sort_order'] : 0;

		?>
		<div class="dcf-item" data-item-id="<?php echo esc_attr( $item_id ); ?>">
			<div class="dcf-item-header">
				<span class="dcf-item-handle">☰</span>
				<span class="dcf-item-number">#<?php echo esc_html( $sort_order + 1 ); ?></span>
				<button type="button" class="dcf-item-toggle button button-small">
					<?php esc_html_e( 'Toggle', 'elementor-dynamic-content-framework' ); ?>
				</button>
				<div class="dcf-item-actions">
					<button type="button" class="dcf-duplicate-item button button-small">
						<?php esc_html_e( 'Duplicate', 'elementor-dynamic-content-framework' ); ?>
					</button>
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=dcf-items&group_id=' . $item['group_id'] . '&action=delete&item_id=' . $item_id ), 'dcf_delete_item_' . $item_id ) ); ?>" class="button button-small dcf-button-danger" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'elementor-dynamic-content-framework' ); ?>');">
						<?php esc_html_e( 'Delete', 'elementor-dynamic-content-framework' ); ?>
					</a>
				</div>
			</div>

			<div class="dcf-item-content">
				<form method="post" class="dcf-item-form" enctype="multipart/form-data">
					<?php wp_nonce_field( 'dcf_item_nonce', 'dcf_item_nonce' ); ?>
					<input type="hidden" name="item_id" value="<?php echo esc_attr( $item_id ); ?>">
					<input type="hidden" name="group_id" value="<?php echo esc_attr( $item['group_id'] ); ?>">

					<?php
					// Render fields based on schema
					// Schema is now a direct array of fields, not wrapped in 'fields' key
					if ( isset( $group_type['schema'] ) && is_array( $group_type['schema'] ) ) {
						foreach ( $group_type['schema'] as $field ) {
							self::render_field( $field, $item_data );
						}
					}
					?>

					<div class="dcf-item-form-actions">
						<button type="submit" class="button button-primary">
							<?php esc_html_e( 'Save Item', 'elementor-dynamic-content-framework' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single field based on type
	 *
	 * @param array $field Field definition.
	 * @param array $item_data Item data.
	 */
	private static function render_field( $field, $item_data ) {
		$field_name = isset( $field['name'] ) ? $field['name'] : '';
		$field_label = isset( $field['label'] ) ? $field['label'] : '';
		$field_type = isset( $field['type'] ) ? $field['type'] : 'text';
		$field_value = isset( $item_data[ $field_name ] ) ? $item_data[ $field_name ] : '';

		?>
		<div class="dcf-form-group">
			<label for="dcf-field-<?php echo esc_attr( $field_name ); ?>">
				<?php echo esc_html( $field_label ); ?>
			</label>

			<?php
			switch ( $field_type ) {
				case 'text':
					$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
					$max_length = isset( $field['max_length'] ) ? $field['max_length'] : '';
					?>
					<input type="text" 
						id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
						name="fields[<?php echo esc_attr( $field_name ); ?>]" 
						value="<?php echo esc_attr( $field_value ); ?>"
						<?php echo $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
						<?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>>
					<?php
					break;

				case 'textarea':
					$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
					$rows = isset( $field['rows'] ) ? (int) $field['rows'] : 5;
					?>
					<textarea 
						id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
						name="fields[<?php echo esc_attr( $field_name ); ?>]"
						rows="<?php echo esc_attr( $rows ); ?>"
						<?php echo $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
					><?php echo esc_textarea( $field_value ); ?></textarea>
					<?php
					break;

				case 'image':
					// Handle both numeric ID and array format (after processing)
					if ( is_array( $field_value ) && isset( $field_value['id'] ) ) {
						$media_id = (int) $field_value['id'];
					} elseif ( is_numeric( $field_value ) ) {
						$media_id = (int) $field_value;
					} else {
						$media_id = 0;
					}
					?>
					<div class="dcf-media-field">
						<input type="hidden" 
							id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
							name="fields[<?php echo esc_attr( $field_name ); ?>]" 
							value="<?php echo esc_attr( $media_id ); ?>">
						<button type="button" class="button dcf-item-media-upload" data-field-id="dcf-field-<?php echo esc_attr( $field_name ); ?>" data-media-type="image" data-title="<?php esc_attr_e( 'Select Image', 'elementor-dynamic-content-framework' ); ?>">
							<?php esc_html_e( 'Select Image', 'elementor-dynamic-content-framework' ); ?>
						</button>
						<div class="dcf-media-preview">
							<?php
							if ( $media_id > 0 ) {
								$image_url = wp_get_attachment_url( $media_id );
								if ( $image_url ) {
									echo '<img src="' . esc_url( $image_url ) . '" alt="">';
								}
							}
							?>
						</div>
						<button type="button" class="button dcf-media-remove-button" style="<?php echo $media_id > 0 ? '' : 'display:none;'; ?>">
							<?php esc_html_e( 'Remove', 'elementor-dynamic-content-framework' ); ?>
						</button>
					</div>
					<?php
					break;

				case 'video':
					// Handle both numeric ID and array format (after processing)
					if ( is_array( $field_value ) && isset( $field_value['id'] ) ) {
						$media_id = (int) $field_value['id'];
					} elseif ( is_numeric( $field_value ) ) {
						$media_id = (int) $field_value;
					} else {
						$media_id = 0;
					}
					?>
					<div class="dcf-media-field">
						<input type="hidden" 
							id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
							name="fields[<?php echo esc_attr( $field_name ); ?>]" 
							value="<?php echo esc_attr( $media_id ); ?>">
						<button type="button" class="button dcf-item-media-upload" data-field-id="dcf-field-<?php echo esc_attr( $field_name ); ?>" data-media-type="video" data-title="<?php esc_attr_e( 'Select Video', 'elementor-dynamic-content-framework' ); ?>">
							<?php esc_html_e( 'Select Video', 'elementor-dynamic-content-framework' ); ?>
						</button>
						<div class="dcf-media-preview">
							<?php
							if ( $media_id > 0 ) {
								$video_url = wp_get_attachment_url( $media_id );
								if ( $video_url ) {
									echo '<video src="' . esc_url( $video_url ) . '" controls></video>';
								}
							}
							?>
						</div>
					</div>
					<?php
					break;

				case 'url':
					$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
					?>
					<input type="url" 
						id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
						name="fields[<?php echo esc_attr( $field_name ); ?>]" 
						value="<?php echo esc_attr( $field_value ); ?>"
						<?php echo $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>>
					<?php
					break;

				case 'icon':
					?>
					<input type="text" 
						id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
						name="fields[<?php echo esc_attr( $field_name ); ?>]" 
						value="<?php echo esc_attr( $field_value ); ?>"
						placeholder="<?php esc_attr_e( 'e.g., fas fa-star', 'elementor-dynamic-content-framework' ); ?>">
					<?php
					break;

				case 'gallery':
					$media_ids = ! empty( $field_value ) ? explode( ',', $field_value ) : array();
					?>
					<div class="dcf-media-field">
						<input type="hidden" 
							id="dcf-field-<?php echo esc_attr( $field_name ); ?>" 
							name="fields[<?php echo esc_attr( $field_name ); ?>]" 
							value="<?php echo esc_attr( $field_value ); ?>">
						<button type="button" class="button dcf-item-media-upload" data-field-id="dcf-field-<?php echo esc_attr( $field_name ); ?>" data-media-type="image" data-multiple="true" data-title="<?php esc_attr_e( 'Select Images', 'elementor-dynamic-content-framework' ); ?>">
							<?php esc_html_e( 'Select Images', 'elementor-dynamic-content-framework' ); ?>
						</button>
						<div class="dcf-media-preview">
							<?php
							foreach ( $media_ids as $media_id ) {
								$image_url = wp_get_attachment_url( (int) $media_id );
								if ( $image_url ) {
									echo '<img src="' . esc_url( $image_url ) . '" alt="">';
								}
							}
							?>
						</div>
					</div>
					<?php
					break;

				case 'repeater':
					// Repeater fields are handled separately
					?>
					<div class="dcf-repeater-field">
						<div class="dcf-repeater-items">
							<?php
							if ( is_array( $field_value ) ) {
								foreach ( $field_value as $index => $repeater_item ) {
									self::render_repeater_item( $field_name, $index, $repeater_item, $field );
								}
							}
							?>
						</div>
						<button type="button" class="button dcf-add-repeater-item">
							<?php esc_html_e( 'Add Item', 'elementor-dynamic-content-framework' ); ?>
						</button>
					</div>
					<?php
					break;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render a repeater item
	 *
	 * @param string $field_name Parent field name.
	 * @param int    $index Item index.
	 * @param array  $item_data Item data.
	 * @param array  $field Field definition.
	 */
	private static function render_repeater_item( $field_name, $index, $item_data, $field ) {
		?>
		<div class="dcf-repeater-item">
			<div class="dcf-repeater-item-header">
				<span class="dcf-repeater-handle">☰</span>
				<button type="button" class="button button-small dcf-remove-repeater-item">
					<?php esc_html_e( 'Remove', 'elementor-dynamic-content-framework' ); ?>
				</button>
			</div>
			<div class="dcf-repeater-item-content">
				<?php
				// Render sub-fields if defined
				if ( isset( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
					foreach ( $field['sub_fields'] as $sub_field ) {
						$sub_field_name = $field_name . '[' . $index . '][' . $sub_field['name'] . ']';
						$sub_field_value = isset( $item_data[ $sub_field['name'] ] ) ? $item_data[ $sub_field['name'] ] : '';
						?>
						<div class="dcf-form-group">
							<label><?php echo esc_html( $sub_field['label'] ); ?></label>
							<input type="text" name="<?php echo esc_attr( $sub_field_name ); ?>" value="<?php echo esc_attr( $sub_field_value ); ?>">
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle item form submission
	 *
	 * @param int   $group_id Group ID.
	 * @param array $group_type Group type data.
	 */
	private static function handle_item_submission( $group_id, $group_type ) {
		// Verify nonce
		if ( ! isset( $_POST['dcf_item_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dcf_item_nonce'] ) ), 'dcf_item_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		$item_id = isset( $_POST['item_id'] ) ? (int) wp_unslash( $_POST['item_id'] ) : 0;
		$fields = isset( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array();

		// Debug: Log submitted data
		error_log( 'DCF Item Submission - Item ID: ' . $item_id );
		error_log( 'DCF Item Submission - Fields: ' . print_r( $fields, true ) );

		// Sanitize field data
		$sanitized_data = array();
		foreach ( $fields as $key => $value ) {
			$sanitized_data[ sanitize_text_field( $key ) ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
		}

		// Debug: Log sanitized data
		error_log( 'DCF Item Submission - Sanitized Data: ' . print_r( $sanitized_data, true ) );

		if ( $item_id > 0 ) {
			// Update existing item
			$result = DCF_Group_Item::update( $item_id, array(
				'data' => $sanitized_data,
			) );
		} else {
			// Create new item
			$items_count = DCF_Group::get_items_count( $group_id );
			$result = DCF_Group_Item::create( array(
				'group_id'   => $group_id,
				'data'       => $sanitized_data,
				'sort_order' => $items_count,
			) );
		}

		if ( is_wp_error( $result ) ) {
			// Debug: Log error
			error_log( 'DCF Item Submission Error: ' . $result->get_error_message() );
			error_log( 'DCF Item Submission Error Data: ' . print_r( $result->get_error_data(), true ) );
			
			wp_die( 
				'<h1>' . esc_html__( 'Error Saving Item', 'elementor-dynamic-content-framework' ) . '</h1>' .
				'<p><strong>' . esc_html__( 'Error:', 'elementor-dynamic-content-framework' ) . '</strong> ' . esc_html( $result->get_error_message() ) . '</p>' .
				'<p><a href="javascript:history.back()">' . esc_html__( 'Go Back', 'elementor-dynamic-content-framework' ) . '</a></p>'
			);
		}

		// Debug: Log success
		error_log( 'DCF Item Submission - Success! Result: ' . print_r( $result, true ) );

		wp_redirect( admin_url( 'admin.php?page=dcf-items&group_id=' . $group_id ) );
		exit;
	}

	/**
	 * Handle item deletion
	 *
	 * @param int $group_id Group ID.
	 */
	private static function handle_item_delete( $group_id ) {
		$item_id = isset( $_GET['item_id'] ) ? (int) wp_unslash( $_GET['item_id'] ) : 0;

		if ( $item_id <= 0 ) {
			wp_redirect( admin_url( 'admin.php?page=dcf-items&group_id=' . $group_id ) );
			exit;
		}

		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dcf_delete_item_' . $item_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		DCF_Group_Item::delete( $item_id );

		wp_redirect( admin_url( 'admin.php?page=dcf-items&group_id=' . $group_id ) );
		exit;
	}
}
