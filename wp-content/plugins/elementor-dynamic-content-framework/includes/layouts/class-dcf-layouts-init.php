<?php
/**
 * Layouts Initialization
 *
 * Registers all built-in layouts and triggers the hook for third-party layouts.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Layouts_Init class
 *
 * Handles initialization and registration of all layouts.
 */
class DCF_Layouts_Init {
    /**
     * Layout engine instance
     *
     * @var DCF_Layout_Engine
     */
    private $layout_engine;

    /**
     * Constructor
     */
    public function __construct() {
        $this->layout_engine = new DCF_Layout_Engine();
        $this->load_dependencies();
        $this->register_layouts();
    }

    /**
     * Load layout dependencies
     */
    private function load_dependencies() {
        // Load layout interface
        require_once DCF_PLUGIN_DIR . 'includes/layouts/interface-dcf-layout.php';

        // Load built-in layout classes
        $layout_files = [
            'layouts/class-dcf-slider-layout.php',
            'layouts/class-dcf-grid-layout.php',
            'layouts/class-dcf-masonry-layout.php',
            'layouts/class-dcf-list-layout.php',
            'layouts/class-dcf-popup-layout.php',
        ];

        foreach ($layout_files as $file) {
            $file_path = DCF_PLUGIN_DIR . 'includes/layouts/' . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    /**
     * Register all built-in layouts
     */
    private function register_layouts() {
        // Register Slider layout
        if (class_exists('DCF_Slider_Layout')) {
            $config = DCF_Slider_Layout::get_config();
            $result = $this->layout_engine->register_layout($config['slug'], $config);
            
            if (is_wp_error($result)) {
                error_log('DCF: Failed to register Slider layout - ' . $result->get_error_message());
            }
        }

        // Register Grid layout
        if (class_exists('DCF_Grid_Layout')) {
            $config = DCF_Grid_Layout::get_config();
            $result = $this->layout_engine->register_layout($config['slug'], $config);
            
            if (is_wp_error($result)) {
                error_log('DCF: Failed to register Grid layout - ' . $result->get_error_message());
            }
        }

        // Register Masonry layout
        if (class_exists('DCF_Masonry_Layout')) {
            $config = DCF_Masonry_Layout::get_config();
            $result = $this->layout_engine->register_layout($config['slug'], $config);
            
            if (is_wp_error($result)) {
                error_log('DCF: Failed to register Masonry layout - ' . $result->get_error_message());
            }
        }

        // Register List layout
        if (class_exists('DCF_List_Layout')) {
            $config = DCF_List_Layout::get_config();
            $result = $this->layout_engine->register_layout($config['slug'], $config);
            
            if (is_wp_error($result)) {
                error_log('DCF: Failed to register List layout - ' . $result->get_error_message());
            }
        }

        // Register Popup layout
        if (class_exists('DCF_Popup_Layout')) {
            $config = DCF_Popup_Layout::get_config();
            $result = $this->layout_engine->register_layout($config['slug'], $config);
            
            if (is_wp_error($result)) {
                error_log('DCF: Failed to register Popup layout - ' . $result->get_error_message());
            }
        }

        /**
         * Action hook to allow third-party layouts registration
         *
         * @since 1.0.0
         */
        do_action('dcf_register_layouts');
    }

    /**
     * Get layout engine instance
     *
     * @return DCF_Layout_Engine
     */
    public function get_layout_engine() {
        return $this->layout_engine;
    }
}
