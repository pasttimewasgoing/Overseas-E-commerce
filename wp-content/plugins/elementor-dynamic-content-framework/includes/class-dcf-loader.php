<?php
/**
 * Register all actions and filters for the plugin.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 */
class DCF_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $actions The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $filters The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();

		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		// Load internationalization class
		require_once DCF_PLUGIN_DIR . 'includes/class-dcf-i18n.php';
		
		// Load database layer
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-database.php';
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-schema-parser.php';
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-schema-validator.php';
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-schema-printer.php';
		require_once DCF_PLUGIN_DIR . 'includes/database/class-dcf-data-serializer.php';
		
		// Load cache layer
		require_once DCF_PLUGIN_DIR . 'includes/cache/class-dcf-cache-manager.php';
		
		// Load models
		require_once DCF_PLUGIN_DIR . 'includes/models/class-dcf-group-type.php';
		require_once DCF_PLUGIN_DIR . 'includes/models/class-dcf-group.php';
		require_once DCF_PLUGIN_DIR . 'includes/models/class-dcf-group-item.php';
		
		// Load utilities
		require_once DCF_PLUGIN_DIR . 'includes/utils/class-dcf-logger.php';
		require_once DCF_PLUGIN_DIR . 'includes/utils/class-dcf-sanitizer.php';
		require_once DCF_PLUGIN_DIR . 'includes/utils/class-dcf-performance.php';
		require_once DCF_PLUGIN_DIR . 'includes/utils/class-dcf-image-helper.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new DCF_i18n();
		$this->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the plugin functionality.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_hooks() {
		// Add initialization hook
		$this->add_action( 'init', $this, 'init_plugin' );
		
		// Register Elementor widgets
		$this->add_action( 'elementor/widgets/register', $this, 'register_elementor_widgets' );
		
		// Enqueue editor styles
		$this->add_action( 'elementor/editor/after_enqueue_styles', $this, 'enqueue_editor_styles' );
		
		// Initialize asset management
		$this->add_action( 'init', $this, 'init_assets' );
		
		// Initialize admin menu (must be early, before admin_menu hook)
		if ( is_admin() ) {
			$this->add_action( 'plugins_loaded', $this, 'init_admin_menu' );
		}
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {
		// Initialize performance monitoring
		DCF_Performance::init();
		
		// Initialize layout system
		$this->init_layouts();
		
		// Initialize REST API
		$this->init_rest_api();
		
		// Plugin initialization logic will be added here
		// This will be expanded in future tasks to load:
		// - Database layer
		// - Elementor widgets
		// - Admin interface
	}

	/**
	 * Initialize layout system
	 *
	 * @since 1.0.0
	 */
	private function init_layouts() {
		// Load layout registry
		require_once DCF_PLUGIN_DIR . 'includes/layouts/class-dcf-layout-registry.php';
		
		// Load layout engine
		require_once DCF_PLUGIN_DIR . 'includes/layouts/class-dcf-layout-engine.php';
		
		// Load and initialize layouts
		require_once DCF_PLUGIN_DIR . 'includes/layouts/class-dcf-layouts-init.php';
		new DCF_Layouts_Init();
	}

	/**
	 * Initialize REST API
	 *
	 * @since 1.0.0
	 */
	private function init_rest_api() {
		// Load REST API controller
		require_once DCF_PLUGIN_DIR . 'includes/api/class-dcf-rest-api.php';
		
		// Initialize REST API
		new DCF_REST_API();
	}

	/**
	 * Register Elementor widgets
	 *
	 * @since 1.0.0
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_elementor_widgets( $widgets_manager ) {
		// Check if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Load the widget class
		require_once DCF_PLUGIN_DIR . 'includes/widgets/class-dcf-elementor-widget.php';

		// Register the widget
		$widgets_manager->register( new DCF_Elementor_Widget() );
	}

	/**
	 * Enqueue editor styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style(
			'dcf-elementor-widget',
			DCF_PLUGIN_URL . 'assets/css/elementor-widget.css',
			array(),
			DCF_VERSION
		);
	}

	/**
	 * Initialize asset management system
	 *
	 * @since 1.0.0
	 */
	public function init_assets() {
		// Load asset management class
		require_once DCF_PLUGIN_DIR . 'includes/class-dcf-assets.php';
		
		// Initialize asset management
		DCF_Assets::init();
	}

	/**
	 * Initialize admin menu system
	 *
	 * @since 1.0.0
	 */
	public function init_admin_menu() {
		// Load admin menu class
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-admin-menu.php';
		
		// Load admin classes
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-group-type-list.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-group-type-editor.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-group-list.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-group-editor.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-item-editor.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-item-ajax.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-settings.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-import-export.php';
		require_once DCF_PLUGIN_DIR . 'includes/admin/class-dcf-system-status.php';
		
		// Initialize settings
		DCF_Settings::init();
		
		// Initialize import/export
		DCF_Import_Export::init();
		
		// Initialize AJAX handlers
		DCF_Item_Ajax::init();
		
		// Initialize admin menu
		$admin_menu = new DCF_Admin_Menu();
		$admin_menu->init();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress action that is being registered.
	 * @param object $component     A reference to the instance of the object on which the action is defined.
	 * @param string $callback      The name of the function definition on the $component.
	 * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress filter that is being registered.
	 * @param object $component     A reference to the instance of the object on which the filter is defined.
	 * @param string $callback      The name of the function definition on the $component.
	 * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array  $hooks         The collection of hooks that is being registered (that is, actions or filters).
	 * @param  string $hook          The name of the WordPress filter that is being registered.
	 * @param  object $component     A reference to the instance of the object on which the filter is defined.
	 * @param  string $callback      The name of the function definition on the $component.
	 * @param  int    $priority      The priority at which the function should be fired.
	 * @param  int    $accepted_args The number of arguments that should be passed to the $callback.
	 * @return array                 The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
