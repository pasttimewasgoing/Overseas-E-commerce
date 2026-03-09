<?php
/**
 * System Status Page
 *
 * Displays system information, database status, and performance metrics.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DCF_System_Status class.
 *
 * Renders the system status page with comprehensive system information.
 */
class DCF_System_Status {

	/**
	 * Render the system status page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<div class="dcf-system-status">
				<?php self::render_system_info(); ?>
				<?php self::render_database_status(); ?>
				<?php self::render_plugin_statistics(); ?>
				<?php self::render_performance_metrics(); ?>
				<?php self::render_configuration_status(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render system information section
	 *
	 * @since 1.0.0
	 */
	private static function render_system_info() {
		?>
		<div class="dcf-status-section">
			<h2><?php esc_html_e( 'System Information', 'elementor-dynamic-content-framework' ); ?></h2>
			
			<table class="widefat striped">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'PHP Version', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( phpversion() ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'WordPress Version', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Elementor Version', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( self::get_elementor_version() ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Database Version', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( DCF_Database::get_db_version() ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'WordPress Locale', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( get_locale() ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Site URL', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( get_site_url() ); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render database table status section
	 *
	 * @since 1.0.0
	 */
	private static function render_database_status() {
		global $wpdb;

		$tables = array(
			'group_types' => __( 'Group Types', 'elementor-dynamic-content-framework' ),
			'groups'      => __( 'Groups', 'elementor-dynamic-content-framework' ),
			'group_items' => __( 'Group Items', 'elementor-dynamic-content-framework' ),
		);

		?>
		<div class="dcf-status-section">
			<h2><?php esc_html_e( 'Database Table Status', 'elementor-dynamic-content-framework' ); ?></h2>
			
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Table Name', 'elementor-dynamic-content-framework' ); ?></th>
						<th><?php esc_html_e( 'Status', 'elementor-dynamic-content-framework' ); ?></th>
						<th><?php esc_html_e( 'Row Count', 'elementor-dynamic-content-framework' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tables as $table_key => $table_label ) : ?>
						<?php
						$table_name = DCF_Database::get_table_name( $table_key );
						$exists     = DCF_Database::table_exists( $table_key );
						$row_count  = 0;

						if ( $exists ) {
							$row_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name}" ) );
						}
						?>
						<tr>
							<td><strong><?php echo esc_html( $table_label ); ?></strong></td>
							<td>
								<?php if ( $exists ) : ?>
									<span style="color: green;">✓ <?php esc_html_e( 'Exists', 'elementor-dynamic-content-framework' ); ?></span>
								<?php else : ?>
									<span style="color: red;">✗ <?php esc_html_e( 'Missing', 'elementor-dynamic-content-framework' ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $row_count ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render plugin statistics section
	 *
	 * @since 1.0.0
	 */
	private static function render_plugin_statistics() {
		// Get registered layouts count
		$layouts = DCF_Layout_Registry::get_all();
		$layouts_count = count( $layouts );

		// Get active content groups count
		$active_groups = DCF_Group::get_all( array( 'status' => 'active' ) );
		$active_groups_count = count( $active_groups );

		// Get total content groups count
		$all_groups = DCF_Group::get_all();
		$total_groups_count = count( $all_groups );

		// Get total content group types count
		$all_types = DCF_Group_Type::get_all();
		$types_count = count( $all_types );

		// Get total content items count
		global $wpdb;
		$table_name = DCF_Database::get_table_name( 'group_items' );
		$total_items_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );

		?>
		<div class="dcf-status-section">
			<h2><?php esc_html_e( 'Plugin Statistics', 'elementor-dynamic-content-framework' ); ?></h2>
			
			<table class="widefat striped">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Registered Layouts', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $layouts_count ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Active Content Groups', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $active_groups_count ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Total Content Groups', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $total_groups_count ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Content Group Types', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $types_count ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Total Content Items', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $total_items_count ); ?></td>
					</tr>
				</tbody>
			</table>

			<?php if ( $layouts_count > 0 ) : ?>
				<h3><?php esc_html_e( 'Registered Layouts Details', 'elementor-dynamic-content-framework' ); ?></h3>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Layout Slug', 'elementor-dynamic-content-framework' ); ?></th>
							<th><?php esc_html_e( 'Layout Name', 'elementor-dynamic-content-framework' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $layouts as $layout ) : ?>
							<tr>
								<td><code><?php echo esc_html( $layout['slug'] ); ?></code></td>
								<td><?php echo esc_html( $layout['name'] ?? __( 'N/A', 'elementor-dynamic-content-framework' ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render performance metrics section
	 *
	 * @since 1.0.0
	 */
	private static function render_performance_metrics() {
		// Get cache statistics
		$cache_stats = DCF_Cache_Manager::get_stats();

		?>
		<div class="dcf-status-section">
			<h2><?php esc_html_e( 'Performance Metrics', 'elementor-dynamic-content-framework' ); ?></h2>
			
			<table class="widefat striped">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Cache Hits', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $cache_stats['hits'] ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Cache Misses', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $cache_stats['misses'] ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Cache Hit Rate', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td><?php echo esc_html( $cache_stats['hit_rate'] ); ?>%</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Cache Expiration Time', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td>
							<?php
							$expiration_seconds = DCF_Cache_Manager::get_cache_expiration();
							$expiration_hours   = $expiration_seconds / HOUR_IN_SECONDS;
							echo esc_html( $expiration_hours . ' ' . __( 'hour(s)', 'elementor-dynamic-content-framework' ) );
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render configuration status section
	 *
	 * @since 1.0.0
	 */
	private static function render_configuration_status() {
		// Get plugin settings
		$lazy_loading_enabled = get_option( 'dcf_lazy_loading_enabled', 1 );
		$rest_api_enabled     = get_option( 'dcf_rest_api_enabled', 1 );
		$minification_enabled = get_option( 'dcf_minification_enabled', 1 );
		$default_types_enabled = get_option( 'dcf_default_types_enabled', 1 );

		?>
		<div class="dcf-status-section">
			<h2><?php esc_html_e( 'Configuration Status', 'elementor-dynamic-content-framework' ); ?></h2>
			
			<table class="widefat striped">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Image Lazy Loading', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td>
							<?php if ( $lazy_loading_enabled ) : ?>
								<span style="color: green;">✓ <?php esc_html_e( 'Enabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php else : ?>
								<span style="color: orange;">✗ <?php esc_html_e( 'Disabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'REST API', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td>
							<?php if ( $rest_api_enabled ) : ?>
								<span style="color: green;">✓ <?php esc_html_e( 'Enabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php else : ?>
								<span style="color: orange;">✗ <?php esc_html_e( 'Disabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Asset Minification', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td>
							<?php if ( $minification_enabled ) : ?>
								<span style="color: green;">✓ <?php esc_html_e( 'Enabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php else : ?>
								<span style="color: orange;">✗ <?php esc_html_e( 'Disabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Default Content Types', 'elementor-dynamic-content-framework' ); ?></strong></td>
						<td>
							<?php if ( $default_types_enabled ) : ?>
								<span style="color: green;">✓ <?php esc_html_e( 'Enabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php else : ?>
								<span style="color: orange;">✗ <?php esc_html_e( 'Disabled', 'elementor-dynamic-content-framework' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Get Elementor version
	 *
	 * @since 1.0.0
	 * @return string Elementor version or 'Not Installed'
	 */
	private static function get_elementor_version() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return __( 'Not Installed', 'elementor-dynamic-content-framework' );
		}

		return ELEMENTOR_VERSION;
	}
}
