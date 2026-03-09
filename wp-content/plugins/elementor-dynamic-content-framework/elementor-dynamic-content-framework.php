<?php
/**
 * Plugin Name: Elementor Dynamic Content Framework
 * Plugin URI: https://example.com/elementor-dynamic-content-framework
 * Description: 企业级 WordPress 动态内容框架插件，深度集成 Elementor，通过三层架构（数据层/布局层/渲染层）实现完全解耦的内容管理系统。
 * Version: 1.0.0
 * Author: cyf
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: elementor-dynamic-content-framework
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'DCF_VERSION', '1.0.2' );

/**
 * Plugin directory path.
 */
define( 'DCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'DCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'DCF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_dcf() {
	require_once DCF_PLUGIN_DIR . 'includes/class-dcf-activator.php';
	DCF_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_dcf() {
	require_once DCF_PLUGIN_DIR . 'includes/class-dcf-deactivator.php';
	DCF_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dcf' );
register_deactivation_hook( __FILE__, 'deactivate_dcf' );

/**
 * Load error checker for debugging
 */
if (file_exists(DCF_PLUGIN_DIR . 'check-errors.php')) {
	require_once DCF_PLUGIN_DIR . 'check-errors.php';
}

/**
 * Autoloader for plugin classes.
 */
spl_autoload_register( function ( $class ) {
	// Only autoload classes with DCF_ prefix
	if ( strpos( $class, 'DCF_' ) !== 0 ) {
		return;
	}

	// Convert class name to file path
	$class_file = strtolower( str_replace( '_', '-', $class ) );
	$class_file = 'class-' . $class_file . '.php';

	// Define possible directories
	$directories = array(
		'includes/',
		'includes/database/',
		'includes/models/',
		'includes/cache/',
		'includes/layouts/',
		'includes/layouts/layouts/',
		'includes/widgets/',
		'includes/admin/',
		'includes/api/',
		'includes/utils/',
	);

	// Try to find and load the class file
	foreach ( $directories as $directory ) {
		$file_path = DCF_PLUGIN_DIR . $directory . $class_file;
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
			return;
		}
	}
} );

/**
 * The core plugin class.
 */
require DCF_PLUGIN_DIR . 'includes/dcf-functions.php';
require DCF_PLUGIN_DIR . 'includes/class-dcf-loader.php';
require DCF_PLUGIN_DIR . 'includes/class-dcf-i18n.php';

/**
 * Begins execution of the plugin.
 */
function run_dcf() {
	$loader = new DCF_Loader();
	$loader->run();
}

run_dcf();

/**
 * Global helper function to register a layout.
 *
 * @param string $slug Layout unique identifier
 * @param array  $args Layout configuration
 * @return bool|WP_Error True on success, WP_Error on failure
 */
function dcf_register_layout( string $slug, array $args ) {
	static $layout_engine = null;
	
	if ( $layout_engine === null ) {
		$layout_engine = new DCF_Layout_Engine();
	}
	
	return $layout_engine->register_layout( $slug, $args );
}

/**
 * Global helper function to get all registered layouts.
 *
 * @return array Array of all registered layouts
 */
function dcf_get_layouts(): array {
	return DCF_Layout_Registry::get_all();
}

/**
 * Global helper function to get a specific layout.
 *
 * @param string $slug Layout slug
 * @return array|null Layout configuration or null if not found
 */
function dcf_get_layout( string $slug ): ?array {
	return DCF_Layout_Registry::get( $slug );
}
