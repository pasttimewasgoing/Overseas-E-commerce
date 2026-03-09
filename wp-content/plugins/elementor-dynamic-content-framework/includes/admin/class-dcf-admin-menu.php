<?php
/**
 * Admin Menu Management
 *
 * Handles the registration of admin menus and submenus for the plugin.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

/**
 * Admin Menu Management Class
 *
 * Registers the main menu and all submenus for the Dynamic Content Framework
 * admin interface with proper capability checks.
 *
 * @since 1.0.0
 */
class DCF_Admin_Menu {

	/**
	 * Initialize the admin menu
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		add_action( 'admin_menu', array( $this, 'register_hidden_pages' ), 999 );
		add_action( 'admin_init', array( $this, 'handle_dcf_items_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Handle dcf-items page
	 *
	 * @since 1.0.0
	 */
	public function handle_dcf_items_page() {
		// Check if this is the dcf-items page
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		
		if ( 'dcf-items' !== $page ) {
			return;
		}

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'You must be logged in to access this page.', 'elementor-dynamic-content-framework' ) );
		}

		// Enqueue WordPress media library FIRST
		wp_enqueue_media();

		// Register and enqueue styles
		wp_register_style(
			'dcf-admin',
			DCF_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			DCF_VERSION
		);
		wp_enqueue_style( 'dcf-admin' );

		// Register and enqueue scripts with proper dependencies
		wp_register_script(
			'dcf-item-editor',
			DCF_PLUGIN_URL . 'assets/js/item-editor.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			DCF_VERSION . '.' . time(),
			true
		);
		
		// Localize script data BEFORE enqueuing
		wp_localize_script( 'dcf-item-editor', 'dcfAdmin', array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'dcf_item_editor' ),
			'i18n'     => array(
				'confirmDelete' => __( 'Are you sure you want to delete this item?', 'elementor-dynamic-content-framework' ),
				'selectImage'   => __( 'Select Image', 'elementor-dynamic-content-framework' ),
				'selectFile'    => __( 'Select File', 'elementor-dynamic-content-framework' ),
			),
		) );
		
		wp_enqueue_script( 'dcf-item-editor' );

		// Load admin header
		require_once ABSPATH . 'wp-admin/admin-header.php';
		
		// Render the page
		DCF_Item_Editor::render();
		
		// Load admin footer
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		
		exit;
	}

	/**
	 * Enqueue admin assets
	 *
	 * @since 1.0.0
	 * @param string $hook Current admin page hook
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on DCF admin pages
		if ( strpos( $hook, 'dcf-' ) === false && strpos( $hook, 'dcf_' ) === false ) {
			return;
		}

		// Register and enqueue admin CSS
		wp_register_style(
			'dcf-admin',
			DCF_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			DCF_VERSION
		);
		wp_enqueue_style( 'dcf-admin' );

		// Register and enqueue schema builder script
		wp_register_script(
			'dcf-schema-builder',
			DCF_PLUGIN_URL . 'assets/js/schema-builder.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			DCF_VERSION,
			true
		);

		// Register and enqueue item editor script
		wp_register_script(
			'dcf-item-editor',
			DCF_PLUGIN_URL . 'assets/js/item-editor.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			DCF_VERSION,
			true
		);

		// Register and enqueue admin script
		wp_register_script(
			'dcf-admin',
			DCF_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			DCF_VERSION,
			true
		);
		wp_enqueue_script( 'dcf-admin' );

		// Localize script with translations
		wp_localize_script(
			'dcf-admin',
			'dcfAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'dcf_admin_nonce' ),
				'strings' => array(
					'confirmDelete' => __( 'Are you sure you want to delete this?', 'elementor-dynamic-content-framework' ),
					'error' => __( 'An error occurred. Please try again.', 'elementor-dynamic-content-framework' ),
					'success' => __( 'Operation completed successfully.', 'elementor-dynamic-content-framework' ),
				),
			)
		);
	}

	/**
	 * Register hidden admin pages
	 *
	 * @since 1.0.0
	 */
	public function register_hidden_pages() {
		global $_registered_pages;
		// Register dcf-items page so WordPress recognizes it
		$_registered_pages[ 'admin_page_dcf-items' ] = true;
	}

	/**
	 * Register admin menus and submenus
	 *
	 * @since 1.0.0
	 */
	public function register_menus() {
		// Add main menu with lower permission requirement
		add_menu_page(
			__( 'Dynamic Content Framework', 'elementor-dynamic-content-framework' ),
			__( 'Dynamic Content Framework', 'elementor-dynamic-content-framework' ),
			'read',
			'dcf-dashboard',
			array( $this, 'render_dashboard_page' ),
			'dashicons-layout',
			30
		);

		// Add Group Types submenu
		add_submenu_page(
			'dcf-dashboard',
			__( 'Group Types', 'elementor-dynamic-content-framework' ),
			__( 'Group Types', 'elementor-dynamic-content-framework' ),
			'manage_options',
			'dcf-group-types',
			array( $this, 'render_group_types_page' )
		);

		// Add Groups submenu
		add_submenu_page(
			'dcf-dashboard',
			__( 'Groups', 'elementor-dynamic-content-framework' ),
			__( 'Groups', 'elementor-dynamic-content-framework' ),
			'read',
			'dcf-groups',
			array( $this, 'render_groups_page' )
		);

		// Add Settings submenu
		add_submenu_page(
			'dcf-dashboard',
			__( 'Settings', 'elementor-dynamic-content-framework' ),
			__( 'Settings', 'elementor-dynamic-content-framework' ),
			'manage_options',
			'dcf-settings',
			array( $this, 'render_settings_page' )
		);

		// Add Import/Export submenu
		add_submenu_page(
			'dcf-dashboard',
			__( 'Import/Export', 'elementor-dynamic-content-framework' ),
			__( 'Import/Export', 'elementor-dynamic-content-framework' ),
			'manage_options',
			'dcf-import-export',
			array( $this, 'render_import_export_page' )
		);

		// Add System Status submenu
		add_submenu_page(
			'dcf-dashboard',
			__( 'System Status', 'elementor-dynamic-content-framework' ),
			__( 'System Status', 'elementor-dynamic-content-framework' ),
			'manage_options',
			'dcf-system-status',
			array( $this, 'render_system_status_page' )
		);

		// Rename the first submenu from "Dynamic Content Framework" to "Dashboard"
		global $submenu;
		if ( isset( $submenu['dcf-dashboard'] ) ) {
			$submenu['dcf-dashboard'][0][0] = __( 'Dashboard', 'elementor-dynamic-content-framework' );
		}
	}

	/**
	 * Render dashboard page
	 *
	 * @since 1.0.0
	 */
	public function render_dashboard_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="dcf-dashboard">
				<p><?php esc_html_e( '欢迎使用动态内容框架。使用菜单管理您的内容组和类型。', 'elementor-dynamic-content-framework' ); ?></p>
				
				<div class="dcf-dashboard-widgets">
					<div class="dcf-dashboard-widget">
						<h2><?php esc_html_e( '快速统计', 'elementor-dynamic-content-framework' ); ?></h2>
						<p><?php esc_html_e( '统计信息将在此显示。', 'elementor-dynamic-content-framework' ); ?></p>
					</div>
					
					<div class="dcf-dashboard-widget">
						<h2><?php esc_html_e( '快速开始', 'elementor-dynamic-content-framework' ); ?></h2>
						<ul>
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-group-types' ) ); ?>"><?php esc_html_e( '创建内容组类型', 'elementor-dynamic-content-framework' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-groups' ) ); ?>"><?php esc_html_e( '创建内容组', 'elementor-dynamic-content-framework' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=dcf-settings' ) ); ?>"><?php esc_html_e( '配置设置', 'elementor-dynamic-content-framework' ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render group types page
	 *
	 * @since 1.0.0
	 */
	public function render_group_types_page() {
		// Check if editing or creating a new type
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( 'new' === $action || 'edit' === $action || 'delete' === $action ) {
			DCF_Group_Type_Editor::render();
		} else {
			DCF_Group_Type_List::render();
		}
	}

	/**
	 * Render groups page
	 *
	 * @since 1.0.0
	 */
	public function render_groups_page() {
		// Check if editing or creating a new group
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( 'new' === $action || 'edit' === $action || 'delete' === $action || 'toggle_status' === $action ) {
			DCF_Group_Editor::render();
		} else {
			DCF_Group_List::render();
		}
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		DCF_Settings::render();
	}

	/**
	 * Render import/export page
	 *
	 * @since 1.0.0
	 */
	public function render_import_export_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( '在此导入和导出您的内容组。', 'elementor-dynamic-content-framework' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render system status page
	 *
	 * @since 1.0.0
	 */
	public function render_system_status_page() {
		DCF_System_Status::render();
	}
}
