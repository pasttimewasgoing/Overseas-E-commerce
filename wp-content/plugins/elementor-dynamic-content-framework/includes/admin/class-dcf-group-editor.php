<?php
/**
 * Group Editor
 *
 * Handles creation and editing of content groups.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_Editor class.
 *
 * Provides form interface for creating and editing groups.
 */
class DCF_Group_Editor {

	/**
	 * Render the group editor page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Get action and ID from query parameters
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'new';
		$group_id = isset( $_GET['id'] ) ? (int) wp_unslash( $_GET['id'] ) : 0;

		// Load existing group if editing
		$group = null;
		if ( 'edit' === $action && $group_id > 0 ) {
			$group = DCF_Group::get( $group_id );
			if ( ! $group ) {
				wp_die( esc_html__( 'Group not found.', 'elementor-dynamic-content-framework' ) );
			}
		}

		// Handle form submission
		if ( isset( $_POST['dcf_group_nonce'] ) ) {
			self::handle_form_submission( $group );
		}

		// Handle delete action
		if ( 'delete' === $action && $group_id > 0 ) {
			self::handle_delete( $group_id );
		}

		// Handle status toggle
		if ( 'toggle_status' === $action && $group_id > 0 ) {
			self::handle_status_toggle( $group_id );
		}

		// Render the form
		self::render_form( $group );
	}

	/**
	 * Render the editor form
	 *
	 * @param array|null $group Existing group data or null for new group.
	 */
	private static function render_form( $group = null ) {
		$is_edit = null !== $group;
		$title = $is_edit ? __( 'Edit Group', 'elementor-dynamic-content-framework' ) : __( 'Add New Group', 'elementor-dynamic-content-framework' );
		$group_title = $is_edit ? $group['title'] : '';
		$type_id = $is_edit ? $group['type_id'] : 0;
		$status = $is_edit ? $group['status'] : 'draft';

		// Get all types
		$types = DCF_Group_Type::get_all();

		?>
		<div class="wrap dcf-admin-page">
			<h1><?php echo esc_html( $title ); ?></h1>

			<form method="post" class="dcf-group-form">
				<?php wp_nonce_field( 'dcf_group_nonce', 'dcf_group_nonce' ); ?>

				<div class="dcf-form-group">
					<label for="dcf-group-title"><?php esc_html_e( 'Group Title', 'elementor-dynamic-content-framework' ); ?> <span class="required">*</span></label>
					<input type="text" id="dcf-group-title" name="title" value="<?php echo esc_attr( $group_title ); ?>" required>
					<p class="description"><?php esc_html_e( 'A descriptive title for this group.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>

				<div class="dcf-form-group">
					<label for="dcf-group-type"><?php esc_html_e( 'Group Type', 'elementor-dynamic-content-framework' ); ?> <span class="required">*</span></label>
					<select id="dcf-group-type" name="type_id" required <?php echo $is_edit ? 'disabled' : ''; ?>>
						<option value=""><?php esc_html_e( 'Select a type...', 'elementor-dynamic-content-framework' ); ?></option>
						<?php foreach ( $types as $type ) : ?>
							<option value="<?php echo esc_attr( $type['id'] ); ?>" <?php selected( $type_id, $type['id'] ); ?>>
								<?php echo esc_html( $type['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php if ( $is_edit ) : ?>
						<input type="hidden" name="type_id" value="<?php echo esc_attr( $type_id ); ?>">
					<?php endif; ?>
					<p class="description"><?php esc_html_e( 'The type defines the structure of items in this group.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>

				<div class="dcf-form-group">
					<label for="dcf-group-status"><?php esc_html_e( 'Status', 'elementor-dynamic-content-framework' ); ?></label>
					<select id="dcf-group-status" name="status">
						<option value="draft" <?php selected( $status, 'draft' ); ?>><?php esc_html_e( 'Draft', 'elementor-dynamic-content-framework' ); ?></option>
						<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'elementor-dynamic-content-framework' ); ?></option>
						<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'elementor-dynamic-content-framework' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Active groups are available for use in Elementor widgets.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>

				<?php if ( $is_edit ) : ?>
					<div class="dcf-group-info">
						<h3><?php esc_html_e( 'Group Information', 'elementor-dynamic-content-framework' ); ?></h3>
						<p>
							<strong><?php esc_html_e( 'Items:', 'elementor-dynamic-content-framework' ); ?></strong>
							<?php echo esc_html( DCF_Group::get_items_count( $group['id'] ) ); ?>
						</p>
						<p>
							<strong><?php esc_html_e( 'Created:', 'elementor-dynamic-content-framework' ); ?></strong>
							<?php echo esc_html( wp_date( 'Y-m-d H:i', strtotime( $group['created_at'] ) ) ); ?>
						</p>
						<?php if ( isset( $group['updated_at'] ) ) : ?>
							<p>
								<strong><?php esc_html_e( 'Updated:', 'elementor-dynamic-content-framework' ); ?></strong>
								<?php echo esc_html( wp_date( 'Y-m-d H:i', strtotime( $group['updated_at'] ) ) ); ?>
							</p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="dcf-form-actions">
					<button type="submit" class="button button-primary">
						<?php echo $is_edit ? esc_html__( 'Update Group', 'elementor-dynamic-content-framework' ) : esc_html__( 'Create Group', 'elementor-dynamic-content-framework' ); ?>
					</button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-groups' ) ); ?>" class="button">
						<?php esc_html_e( 'Cancel', 'elementor-dynamic-content-framework' ); ?>
					</a>
				</div>
			</form>

			<?php if ( $is_edit ) : ?>
				<hr>
				<h2><?php esc_html_e( 'Content Items', 'elementor-dynamic-content-framework' ); ?></h2>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-items&group_id=' . $group['id'] ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Manage Items', 'elementor-dynamic-content-framework' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>

		<?php
		wp_enqueue_style( 'dcf-admin' );
	}

	/**
	 * Handle form submission
	 *
	 * @param array|null $group Existing group or null.
	 */
	private static function handle_form_submission( $group = null ) {
		// Verify nonce
		if ( ! isset( $_POST['dcf_group_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dcf_group_nonce'] ) ), 'dcf_group_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		// Get form data
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$type_id = isset( $_POST['type_id'] ) ? (int) wp_unslash( $_POST['type_id'] ) : 0;
		$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'draft';

		if ( null !== $group ) {
			// Update existing group
			$result = DCF_Group::update( $group['id'], array(
				'title'  => $title,
				'status' => $status,
			) );

			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}

			wp_redirect( admin_url( 'admin.php?page=dcf-groups&message=updated' ) );
			exit;
		} else {
			// Create new group
			$result = DCF_Group::create( array(
				'title'   => $title,
				'type_id' => $type_id,
				'status'  => $status,
			) );

			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}

			wp_redirect( admin_url( 'admin.php?page=dcf-groups&message=created' ) );
			exit;
		}
	}

	/**
	 * Handle delete action
	 *
	 * @param int $group_id Group ID to delete.
	 */
	private static function handle_delete( $group_id ) {
		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dcf_delete_group_' . $group_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		$result = DCF_Group::delete( $group_id );

		if ( is_wp_error( $result ) ) {
			wp_redirect( admin_url( 'admin.php?page=dcf-groups&message=error' ) );
		} else {
			wp_redirect( admin_url( 'admin.php?page=dcf-groups&message=deleted' ) );
		}
		exit;
	}

	/**
	 * Handle status toggle
	 *
	 * @param int $group_id Group ID.
	 */
	private static function handle_status_toggle( $group_id ) {
		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dcf_toggle_status_' . $group_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'elementor-dynamic-content-framework' ) );
		}

		$group = DCF_Group::get( $group_id );
		if ( ! $group ) {
			wp_redirect( admin_url( 'admin.php?page=dcf-groups' ) );
			exit;
		}

		$new_status = 'active' === $group['status'] ? 'inactive' : 'active';

		DCF_Group::update( $group_id, array( 'status' => $new_status ) );

		wp_redirect( admin_url( 'admin.php?page=dcf-groups&message=updated' ) );
		exit;
	}
}
