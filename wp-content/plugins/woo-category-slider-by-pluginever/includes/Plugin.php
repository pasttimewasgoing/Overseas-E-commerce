<?php

namespace WooCommerceCategorySlider;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 *
 * @package WooCommerceCategorySlider
 */
final class Plugin extends \WooCommerceCategorySlider\ByteKit\Plugin {

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		define( 'WC_CAT_SLIDER_VERSION', $this->get_version() );
		define( 'WC_CAT_SLIDER_FILE', $this->get_file() );
		define( 'WC_CAT_SLIDER_PATH', $this->get_dir_path() );
		define( 'WC_CAT_SLIDER_INCLUDES', WC_CAT_SLIDER_PATH . '/includes' );
		define( 'WC_CAT_SLIDER_URL', plugins_url( '', WC_CAT_SLIDER_FILE ) );
		define( 'WC_CAT_SLIDER_ASSETS_URL', $this->get_assets_url() );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/functions.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( $this->get_file(), array( $this, 'install' ) );
		add_filter( 'plugin_action_links_' . $this->get_basename(), array( $this, 'plugin_action_links' ) );
		add_action( 'before_woocommerce_init', array( $this, 'on_before_woocommerce_init' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wc_slider_load_public_assets' ) );
		add_action( 'init', array( $this, 'wc_slider_register_block' ) );
	}

	/**
	 * Run on plugin activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function install() {
		// Add option for installed time.
		add_option( 'wc_cat_slider_installed', wp_date( 'U' ) );
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array $links The plugin action links.
	 *
	 * @since 2.0.3
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		if ( ! $this->is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ) {
			$links['go_pro'] = '<a href="https://pluginever.com/plugins/woocommerce-category-slider-pro/" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__( 'Go Pro', 'woo-category-slider-by-pluginever' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Run on before WooCommerce init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_before_woocommerce_init() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->get_file(), true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', $this->get_file(), true );
		}
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		$this->set( PostTypes::class );
		$this->set( Controllers\SliderElements::class );
		$this->set( Shortcodes\Shortcodes::class );
		$this->set( RestAPI::class );

		// Admin Class.
		if ( is_admin() ) {
			$this->set( Admin\Admin::class );
			$this->set( Admin\Notices::class );
		}

		// Init action.
		do_action( 'wc_category_slider_init' );
	}

	/**
	 * Register Blocks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wc_slider_register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// Plugin Assets.
		wp_register_script( 'owl-carousel-editor', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/owl.carousel.js', array( 'jquery' ), WC_CAT_SLIDER_VERSION, true );
		wp_register_script( 'imagesLoaded', WC_CAT_SLIDER_ASSETS_URL . 'vendor/imagesLoaded.min.js', array( 'jquery' ), WC_CAT_SLIDER_VERSION, true );
		wp_register_script(
			'wc-category-slider-editor',
			WC_CAT_SLIDER_ASSETS_URL . 'js/wc-category-slider-public.js',
			array(
				'jquery',
				'imagesLoaded',
				'owl-carousel-editor',
			),
			WC_CAT_SLIDER_VERSION,
			true
		);

		wp_register_style( 'wccs-owlcarousel-editor', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/assets/owl.carousel.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style( 'wccs-owltheme-default-editor', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/assets/owl.theme.default.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style( 'wccs-fontawesome-editor', WC_CAT_SLIDER_ASSETS_URL . 'vendor/font-awesome/css/font-awesome.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style(
			'wc-category-slider-editor',
			WC_CAT_SLIDER_ASSETS_URL . 'css/wc-category-slider-public.css',
			array(
				'wccs-fontawesome-editor',
				'wccs-owlcarousel-editor',
				'wccs-owltheme-default-editor',
			),
			WC_CAT_SLIDER_VERSION
		);

		// Plugin Assets End.
		wp_register_script(
			'wc-category-slider-block',
			WC_CAT_SLIDER_ASSETS_URL . 'js/wc-category-slider-block.js',
			array(
				'jquery',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
				'wp-api-fetch',
				'wc-category-slider-editor',
			),
			filemtime( WC_CAT_SLIDER_PATH . 'assets/js/wc-category-slider-block.js' ),
			true
		);

		$inline_scripts = 'var isWCCategorySliderPro=' . ( is_plugin_active( 'wc-category-slider-pro/wc-category-slider-pro.php' ) ? 'true' : 'false' ) . ';';

		wp_add_inline_script( 'wc-category-slider-block', $inline_scripts, 'before' );
		register_block_type(
			'pluginever/wc-category-slider',
			array(
				'editor_script' => 'wc-category-slider-block',
				'editor_style'  => 'wc-category-slider-editor',
			)
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			/**
			 * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
			 * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ). For details see
			 * https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
			 */
			wp_set_script_translations( 'wc-category-slider-block', 'woo-category-slider-by-pluginever' );
		}
	}

	/**
	 * Add all the assets of the public side
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wc_slider_load_public_assets() {
		wp_register_script( 'wccs-owl-carousel', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/owl.carousel.js', array( 'jquery' ), WC_CAT_SLIDER_VERSION, true );
		wp_register_script(
			'imagesLoaded',
			WC_CAT_SLIDER_ASSETS_URL . 'vendor/imagesLoaded.min.js',
			array(
				'jquery',
				'wccs-owl-carousel',
			),
			WC_CAT_SLIDER_VERSION,
			true
		);
		wp_register_script(
			'wc-category-slider',
			WC_CAT_SLIDER_ASSETS_URL . 'js/wc-category-slider-public.js',
			array(
				'jquery',
				'wccs-owl-carousel',
				'imagesLoaded',
			),
			WC_CAT_SLIDER_VERSION,
			true
		);
		wp_register_style( 'wccs-owlcarousel', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/assets/owl.carousel.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style( 'wccs-owltheme-default', WC_CAT_SLIDER_ASSETS_URL . 'vendor/owlcarousel/assets/owl.theme.default.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style( 'wccs-fontawesome', WC_CAT_SLIDER_ASSETS_URL . 'vendor/font-awesome/css/font-awesome.css', array(), WC_CAT_SLIDER_VERSION );
		wp_register_style(
			'wc-category-slider',
			WC_CAT_SLIDER_ASSETS_URL . 'css/wc-category-slider-public.css',
			array(
				'wccs-fontawesome',
				'wccs-owlcarousel',
				'wccs-owltheme-default',
			),
			WC_CAT_SLIDER_VERSION
		);
		wp_enqueue_style( 'wc-category-slider' );
		wp_enqueue_script( 'imagesLoaded' );
		wp_enqueue_script( 'wc-category-slider' );
	}

	/**
	 * Get assets path.
	 *
	 * @param string $file Optional. File name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_assets_path( $file = '' ) {
		return $this->get_dir_path( 'assets/' . $file );
	}

	/**
	 * Get assets url.
	 *
	 * @param string $file Optional. File name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_assets_url( $file = '' ) {
		return $this->get_dir_url( 'assets/' . $file );
	}
}
