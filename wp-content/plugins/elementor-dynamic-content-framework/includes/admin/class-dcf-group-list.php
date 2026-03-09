<?php
/**
 * Group List Table
 *
 * Displays a list of content groups with filtering and actions.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_List class.
 *
 * Displays a table of content groups with filtering by type and status.
 */
class DCF_Group_List {

	/**
	 * Render the groups list page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check capability
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Get filter parameters
		$type_id = isset( $_GET['type_id'] ) ? (int) wp_unslash( $_GET['type_id'] ) : 0;
		$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

		// Build query args
		$args = array();
		if ( $type_id > 0 ) {
			$args['type_id'] = $type_id;
		}
		if ( ! empty( $status ) ) {
			$args['status'] = $status;
		}

		// Get groups
		$groups = DCF_Group::get_all( $args );

		// Get all types for filter dropdown
		$types = DCF_Group_Type::get_all();

		?>
		<div class="wrap dcf-admin-page">
			<div class="dcf-admin-header">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-groups&action=new' ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'Add New Group', 'elementor-dynamic-content-framework' ); ?>
				</a>
			</div>

			<?php
			// Display messages
			if ( isset( $_GET['message'] ) ) {
				$message = sanitize_text_field( wp_unslash( $_GET['message'] ) );
				if ( 'created' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group created successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				} elseif ( 'updated' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group updated successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				} elseif ( 'deleted' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group deleted successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				}
			}
			?>

			<!-- Filters -->
			<div class="dcf-filters">
				<form method="get" class="dcf-filter-form">
					<input type="hidden" name="page" value="dcf-groups">

					<select name="type_id" class="dcf-filter-select">
						<option value=""><?php esc_html_e( 'All Types', 'elementor-dynamic-content-framework' ); ?></option>
						<?php foreach ( $types as $type ) : ?>
							<option value="<?php echo esc_attr( $type['id'] ); ?>" <?php selected( $type_id, $type['id'] ); ?>>
								<?php echo esc_html( $type['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<select name="status" class="dcf-filter-select">
						<option value=""><?php esc_html_e( 'All Statuses', 'elementor-dynamic-content-framework' ); ?></option>
						<option value="draft" <?php selected( $status, 'draft' ); ?>><?php esc_html_e( 'Draft', 'elementor-dynamic-content-framework' ); ?></option>
						<option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'elementor-dynamic-content-framework' ); ?></option>
						<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'elementor-dynamic-content-framework' ); ?></option>
					</select>

					<button type="submit" class="button">
						<?php esc_html_e( 'Filter', 'elementor-dynamic-content-framework' ); ?>
					</button>
				</form>
			</div>

			<?php if ( empty( $groups ) ) : ?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'No groups found. Create your first group to get started.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>
			<?php else : ?>
				<table class="dcf-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Title', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Type', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Items', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Status', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Created', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'elementor-dynamic-content-framework' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $groups as $group ) : ?>
							<?php
							$group_type = DCF_Group_Type::get( $group['type_id'] );
							$items_count = DCF_Group::get_items_count( $group['id'] );
							$created_date = isset( $group['created_at'] ) ? wp_date( 'Y-m-d H:i', strtotime( $group['created_at'] ) ) : '—';
							$status_label = ucfirst( $group['status'] );
							$status_class = 'dcf-status-' . $group['status'];
							?>
							<tr>
								<td><strong><?php echo esc_html( $group['title'] ); ?></strong></td>
								<td><?php echo esc_html( $group_type ? $group_type['name'] : '—' ); ?></td>
								<td><?php echo esc_html( $items_count ); ?></td>
								<td><span class="dcf-status-badge <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_label ); ?></span></td>
								<td><?php echo esc_html( $created_date ); ?></td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-groups&action=edit&id=' . $group['id'] ) ); ?>" class="button button-small">
										<?php esc_html_e( 'Edit', 'elementor-dynamic-content-framework' ); ?>
									</a>
									<?php echo wp_kses_post( DCF_Import_Export::get_export_button( $group['id'] ) ); ?>
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=dcf-groups&action=delete&id=' . $group['id'] ), 'dcf_delete_group_' . $group['id'] ) ); ?>" class="button button-small dcf-button-danger" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this group and all its items?', 'elementor-dynamic-content-framework' ); ?>');">
										<?php esc_html_e( 'Delete', 'elementor-dynamic-content-framework' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}
}
