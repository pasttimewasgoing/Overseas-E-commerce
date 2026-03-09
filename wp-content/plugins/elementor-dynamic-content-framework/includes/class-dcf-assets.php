<?php
/**
 * Asset Loading System
 *
 * Handles conditional loading of CSS and JavaScript files for the plugin.
 *
 * @package    DCF
 * @subpackage DCF/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Asset Loading System Class
 *
 * Manages frontend and admin asset registration and enqueuing.
 */
class DCF_Assets {

	/**
	 * Track if widget is present on the page
	 *
	 * @var bool
	 */
	private static $widget_present = false;

	/**
	 * Initialize the asset loading system
	 */
	public static function init() {
		// Register frontend assets
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_frontend_assets' ) );
		
		// Conditionally enqueue assets only when widget is present
		add_action( 'elementor/frontend/before_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_assets' ) );
		
		// Register admin assets
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_admin_assets' ) );
	}

	/**
	 * Register frontend assets
	 */
	public static function register_frontend_assets() {
		$version = DCF_VERSION;
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Register Swiper library
		wp_register_style(
			'swiper',
			DCF_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.css',
			array(),
			'8.4.5'
		);

		wp_register_script(
			'swiper',
			DCF_PLUGIN_URL . 'assets/vendor/swiper/swiper-bundle.min.js',
			array(),
			'8.4.5',
			true
		);

		// Register frontend CSS
		wp_register_style(
			'dcf-frontend',
			DCF_PLUGIN_URL . "assets/css/frontend{$min}.css",
			array(),
			$version
		);

		// Register layout-specific CSS
		$layouts = array( 'slider', 'grid', 'masonry', 'list', 'popup' );
		foreach ( $layouts as $layout ) {
			wp_register_style(
				"dcf-layout-{$layout}",
				DCF_PLUGIN_URL . "assets/css/layouts/{$layout}.css",
				array( 'dcf-frontend' ),
				$version
			);
		}

		// Register frontend JavaScript
		wp_register_script(
			'dcf-frontend',
			DCF_PLUGIN_URL . "assets/js/frontend{$min}.js",
			array( 'jquery' ),
			$version,
			true
		);

		// Localize script with AJAX URL and nonce
		wp_localize_script(
			'dcf-frontend',
			'dcfData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'dcf_frontend_nonce' ),
			)
		);
	}

	/**
	 * Enqueue frontend assets when widget is present
	 */
	public static function enqueue_frontend_assets() {
		// Check if any DCF widget is present on the page
		if ( ! self::is_widget_present() ) {
			return;
		}

		// Enqueue core frontend assets
		wp_enqueue_style( 'dcf-frontend' );
		wp_enqueue_script( 'dcf-frontend' );
	}

	/**
	 * Enqueue assets for a specific layout
	 *
	 * @param string $layout_slug Layout slug
	 */
	public static function enqueue_layout_assets( string $layout_slug ) {
		// Enqueue layout-specific CSS
		$style_handle = "dcf-layout-{$layout_slug}";
		if ( wp_style_is( $style_handle, 'registered' ) ) {
			wp_enqueue_style( $style_handle );
		}

		// Enqueue Swiper for slider layout
		if ( $layout_slug === 'slider' ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_script( 'swiper' );
		}

		// Enqueue Masonry for masonry layout
		if ( $layout_slug === 'masonry' ) {
			wp_enqueue_script( 'masonry' );
		}
	}

	/**
	 * Register admin assets
	 *
	 * @param string $hook Current admin page hook
	 */
	public static function register_admin_assets( $hook ) {
		$version = DCF_VERSION;
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Only load on DCF admin pages
		if ( strpos( $hook, 'dcf' ) === false && strpos( $hook, 'dynamic-content-framework' ) === false ) {
			return;
		}

		// Enqueue admin CSS
		wp_enqueue_style(
			'dcf-admin',
			DCF_PLUGIN_URL . "assets/css/admin{$min}.css",
			array(),
			$version
		);

		// Enqueue admin JavaScript
		wp_enqueue_script(
			'dcf-admin',
			DCF_PLUGIN_URL . "assets/js/admin{$min}.js",
			array( 'jquery', 'jquery-ui-sortable' ),
			$version,
			true
		);

		// Enqueue schema builder
		wp_enqueue_script(
			'dcf-schema-builder',
			DCF_PLUGIN_URL . "assets/js/schema-builder{$min}.js",
			array( 'jquery', 'dcf-admin' ),
			$version,
			true
		);

		// Enqueue item editor
		wp_enqueue_script(
			'dcf-item-editor',
			DCF_PLUGIN_URL . "assets/js/item-editor{$min}.js",
			array( 'jquery', 'jquery-ui-sortable', 'dcf-admin' ),
			$version,
			true
		);

		// Enqueue WordPress media library
		wp_enqueue_media();

		// Localize admin scripts
		wp_localize_script(
			'dcf-admin',
			'dcfAdmin',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'dcf_admin_nonce' ),
				'i18n'        => array(
					'confirmDelete' => __( 'Are you sure you want to delete this item?', 'elementor-dynamic-content-framework' ),
					'selectImage'   => __( 'Select Image', 'elementor-dynamic-content-framework' ),
					'selectVideo'   => __( 'Select Video', 'elementor-dynamic-content-framework' ),
					'selectFile'    => __( 'Select File', 'elementor-dynamic-content-framework' ),
				),
			)
		);
	}

	/**
	 * Check if DCF widget is present on the current page
	 *
	 * @return bool
	 */
	private static function is_widget_present(): bool {
		// If already checked and found, return true
		if ( self::$widget_present ) {
			return true;
		}

		// Check if Elementor is active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}

		// Get current post
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return false;
		}

		// Check if post is built with Elementor
		if ( ! \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
			return false;
		}

		// Get Elementor data
		$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		$elements = $document->get_elements_data();

		// Recursively check for DCF widget
		self::$widget_present = self::check_elements_for_widget( $elements );

		return self::$widget_present;
	}

	/**
	 * Recursively check elements for DCF widget
	 *
	 * @param array $elements Elementor elements
	 * @return bool
	 */
	private static function check_elements_for_widget( array $elements ): bool {
		foreach ( $elements as $element ) {
			// Check if this is a DCF widget
			if ( isset( $element['widgetType'] ) && $element['widgetType'] === 'dcf-dynamic-content' ) {
				return true;
			}

			// Check nested elements
			if ( ! empty( $element['elements'] ) ) {
				if ( self::check_elements_for_widget( $element['elements'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Mark that widget is present (called by widget render)
	 */
	public static function mark_widget_present() {
		self::$widget_present = true;
	}
}
