<?php
/**
 * Group Type List Table
 *
 * Displays a list of content group types with actions.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_Group_Type_List class.
 *
 * Displays a table of content group types with edit/delete actions.
 */
class DCF_Group_Type_List {

	/**
	 * Render the group types list page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Get all group types
		$types = DCF_Group_Type::get_all();

		?>
		<div class="wrap dcf-admin-page">
			<div class="dcf-admin-header">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-group-types&action=new' ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'Add New Type', 'elementor-dynamic-content-framework' ); ?>
				</a>
			</div>

			<?php
			// Display messages
			if ( isset( $_GET['message'] ) ) {
				$message = sanitize_text_field( wp_unslash( $_GET['message'] ) );
				if ( 'created' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group type created successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				} elseif ( 'updated' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group type updated successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				} elseif ( 'deleted' === $message ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e( 'Group type deleted successfully.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				} elseif ( 'error' === $message ) {
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php esc_html_e( 'An error occurred. Please try again.', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					<?php
				}
			}
			?>

			<?php if ( empty( $types ) ) : ?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'No group types found. Create your first group type to get started.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>
			<?php else : ?>
				<table class="dcf-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Name', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Slug', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Fields', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Groups', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Created', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'elementor-dynamic-content-framework' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $types as $type ) : ?>
							<?php
							$groups_count = DCF_Group_Type::get_groups_count( $type['id'] );
							$fields_count = isset( $type['schema']['fields'] ) ? count( $type['schema']['fields'] ) : 0;
							$created_date = isset( $type['created_at'] ) ? wp_date( 'Y-m-d H:i', strtotime( $type['created_at'] ) ) : '—';
							?>
							<tr>
								<td><strong><?php echo esc_html( $type['name'] ); ?></strong></td>
								<td><code><?php echo esc_html( $type['slug'] ); ?></code></td>
								<td><?php echo esc_html( $fields_count ); ?></td>
								<td><?php echo esc_html( $groups_count ); ?></td>
								<td><?php echo esc_html( $created_date ); ?></td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-group-types&action=edit&id=' . $type['id'] ) ); ?>" class="button button-small">
										<?php esc_html_e( 'Edit', 'elementor-dynamic-content-framework' ); ?>
									</a>
									<?php if ( 0 === $groups_count ) : ?>
										<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=dcf-group-types&action=delete&id=' . $type['id'] ), 'dcf_delete_type_' . $type['id'] ) ); ?>" class="button button-small dcf-button-danger" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this group type?', 'elementor-dynamic-content-framework' ); ?>');">
											<?php esc_html_e( 'Delete', 'elementor-dynamic-content-framework' ); ?>
										</a>
									<?php else : ?>
										<span class="button button-small" disabled title="<?php esc_attr_e( 'Cannot delete type with associated groups', 'elementor-dynamic-content-framework' ); ?>">
											<?php esc_html_e( 'Delete', 'elementor-dynamic-content-framework' ); ?>
										</span>
									<?php endif; ?>
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
