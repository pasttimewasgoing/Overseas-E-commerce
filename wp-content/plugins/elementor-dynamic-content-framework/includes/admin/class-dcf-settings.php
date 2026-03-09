<?php
/**
 * Plugin Settings Page
 *
 * Handles the plugin settings page and configuration options.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/admin
 */

/**
 * Settings Page Class
 *
 * Manages the plugin settings page with options for lazy loading, cache expiration,
 * REST API, default type creation, and asset minification.
 *
 * @since 1.0.0
 */
class DCF_Settings {

	/**
	 * Settings option name
	 *
	 * @var string
	 */
	private static $option_name = 'dcf_settings';

	/**
	 * Initialize settings
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_clear_cache' ) );
	}

	/**
	 * Register settings and fields
	 *
	 * @since 1.0.0
	 */
	public static function register_settings() {
		// Register the settings
		register_setting(
			'dcf_settings_group',
			self::$option_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'show_in_rest'      => false,
			)
		);

		// Add settings section
		add_settings_section(
			'dcf_settings_section',
			__( 'Dynamic Content Framework Settings', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_section_description' ),
			'dcf_settings_group'
		);

		// Add lazy loading field
		add_settings_field(
			'dcf_lazy_loading',
			__( 'Image Lazy Loading', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_lazy_loading_field' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);

		// Add cache expiration field
		add_settings_field(
			'dcf_cache_expiration',
			__( 'Cache Expiration Time (hours)', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_cache_expiration_field' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);

		// Add REST API field
		add_settings_field(
			'dcf_rest_api_enabled',
			__( 'REST API', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_rest_api_field' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);

		// Add default type creation field
		add_settings_field(
			'dcf_default_types_enabled',
			__( 'Create Default Content Types on Activation', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_default_types_field' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);

		// Add asset minification field
		add_settings_field(
			'dcf_minify_assets',
			__( 'Frontend Asset Minification', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_minify_assets_field' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);

		// Add clear cache button field
		add_settings_field(
			'dcf_clear_cache_button',
			__( 'Cache Management', 'elementor-dynamic-content-framework' ),
			array( __CLASS__, 'render_clear_cache_button' ),
			'dcf_settings_group',
			'dcf_settings_section'
		);
	}

	/**
	 * Render section description
	 *
	 * @since 1.0.0
	 */
	public static function render_section_description() {
		echo wp_kses_post( __( 'Configure the global settings for the Dynamic Content Framework plugin.', 'elementor-dynamic-content-framework' ) );
	}

	/**
	 * Render lazy loading field
	 *
	 * @since 1.0.0
	 */
	public static function render_lazy_loading_field() {
		$settings = self::get_settings();
		$enabled  = isset( $settings['lazy_loading'] ) ? $settings['lazy_loading'] : true;
		?>
		<label>
			<input type="checkbox" name="dcf_settings[lazy_loading]" value="1" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Enable lazy loading for images', 'elementor-dynamic-content-framework' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, images will load only when they become visible in the viewport.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Render cache expiration field
	 *
	 * @since 1.0.0
	 */
	public static function render_cache_expiration_field() {
		$settings   = self::get_settings();
		$expiration = isset( $settings['cache_expiration'] ) ? intval( $settings['cache_expiration'] ) : 1;
		?>
		<input type="number" name="dcf_settings[cache_expiration]" value="<?php echo esc_attr( $expiration ); ?>" min="1" max="720" />
		<p class="description">
			<?php esc_html_e( 'Set the cache expiration time in hours (1-720 hours). Default is 1 hour.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Render REST API field
	 *
	 * @since 1.0.0
	 */
	public static function render_rest_api_field() {
		$settings = self::get_settings();
		$enabled  = isset( $settings['rest_api_enabled'] ) ? $settings['rest_api_enabled'] : true;
		?>
		<label>
			<input type="checkbox" name="dcf_settings[rest_api_enabled]" value="1" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Enable REST API endpoints', 'elementor-dynamic-content-framework' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, REST API endpoints will be available for accessing content data.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Render default types field
	 *
	 * @since 1.0.0
	 */
	public static function render_default_types_field() {
		$settings = self::get_settings();
		$enabled  = isset( $settings['default_types_enabled'] ) ? $settings['default_types_enabled'] : true;
		?>
		<label>
			<input type="checkbox" name="dcf_settings[default_types_enabled]" value="1" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Create default content types on plugin activation', 'elementor-dynamic-content-framework' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, default content types will be created when the plugin is activated.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Render asset minification field
	 *
	 * @since 1.0.0
	 */
	public static function render_minify_assets_field() {
		$settings = self::get_settings();
		$enabled  = isset( $settings['minify_assets'] ) ? $settings['minify_assets'] : true;
		?>
		<label>
			<input type="checkbox" name="dcf_settings[minify_assets]" value="1" <?php checked( $enabled, true ); ?> />
			<?php esc_html_e( 'Enable frontend asset minification', 'elementor-dynamic-content-framework' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, minified CSS and JavaScript files will be used on the frontend.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Render clear cache button
	 *
	 * @since 1.0.0
	 */
	public static function render_clear_cache_button() {
		$nonce = wp_create_nonce( 'dcf_clear_cache_nonce' );
		?>
		<form method="post" style="display: inline;">
			<?php wp_nonce_field( 'dcf_clear_cache_nonce', 'dcf_clear_cache_nonce' ); ?>
			<button type="submit" name="dcf_clear_cache" class="button button-secondary">
				<?php esc_html_e( 'Clear All Caches', 'elementor-dynamic-content-framework' ); ?>
			</button>
		</form>
		<p class="description">
			<?php esc_html_e( 'Click to clear all Dynamic Content Framework caches.', 'elementor-dynamic-content-framework' ); ?>
		</p>
		<?php
	}

	/**
	 * Handle clear cache button
	 *
	 * @since 1.0.0
	 */
	public static function handle_clear_cache() {
		// Check if clear cache button was clicked
		if ( ! isset( $_POST['dcf_clear_cache'] ) ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_POST['dcf_clear_cache_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dcf_clear_cache_nonce'] ) ), 'dcf_clear_cache_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed', 'elementor-dynamic-content-framework' ) );
		}

		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action', 'elementor-dynamic-content-framework' ) );
		}

		// Clear cache
		if ( class_exists( 'DCF_Cache_Manager' ) ) {
			DCF_Cache_Manager::flush_all();
			add_action( 'admin_notices', array( __CLASS__, 'display_cache_cleared_notice' ) );
		}
	}

	/**
	 * Display cache cleared notice
	 *
	 * @since 1.0.0
	 */
	public static function display_cache_cleared_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'All caches have been cleared successfully.', 'elementor-dynamic-content-framework' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Sanitize settings
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Settings array to sanitize
	 * @return array Sanitized settings
	 */
	public static function sanitize_settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			return array();
		}

		$sanitized = array();

		// Sanitize lazy loading
		$sanitized['lazy_loading'] = isset( $settings['lazy_loading'] ) && '1' === $settings['lazy_loading'] ? true : false;

		// Sanitize cache expiration
		if ( isset( $settings['cache_expiration'] ) ) {
			$expiration = intval( $settings['cache_expiration'] );
			$sanitized['cache_expiration'] = max( 1, min( 720, $expiration ) );
		} else {
			$sanitized['cache_expiration'] = 1;
		}

		// Sanitize REST API enabled
		$sanitized['rest_api_enabled'] = isset( $settings['rest_api_enabled'] ) && '1' === $settings['rest_api_enabled'] ? true : false;

		// Sanitize default types enabled
		$sanitized['default_types_enabled'] = isset( $settings['default_types_enabled'] ) && '1' === $settings['default_types_enabled'] ? true : false;

		// Sanitize minify assets
		$sanitized['minify_assets'] = isset( $settings['minify_assets'] ) && '1' === $settings['minify_assets'] ? true : false;

		return $sanitized;
	}

	/**
	 * Get all settings
	 *
	 * @since 1.0.0
	 *
	 * @return array Settings array
	 */
	public static function get_settings() {
		$settings = get_option( self::$option_name, array() );

		// Set defaults if not set
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$defaults = array(
			'lazy_loading'         => true,
			'cache_expiration'     => 1,
			'rest_api_enabled'     => true,
			'default_types_enabled' => true,
			'minify_assets'        => true,
		);

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Get a specific setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Setting key
	 * @param mixed  $default Default value if setting not found
	 * @return mixed Setting value
	 */
	public static function get_setting( $key, $default = null ) {
		$settings = self::get_settings();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Update a specific setting
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Setting key
	 * @param mixed  $value Setting value
	 * @return bool True on success, false on failure
	 */
	public static function update_setting( $key, $value ) {
		$settings         = self::get_settings();
		$settings[ $key ] = $value;
		return update_option( self::$option_name, $settings );
	}

	/**
	 * Render the settings page
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		// Check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page', 'elementor-dynamic-content-framework' ) );
		}

		// Check if settings were saved
		$settings_saved = isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'];
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php if ( $settings_saved ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved successfully.', 'elementor-dynamic-content-framework' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'dcf_settings_group' );
				do_settings_sections( 'dcf_settings_group' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
