<?php
/**
 * Layout Engine
 *
 * Core layout engine that manages layout registration and rendering.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Layout_Engine class
 *
 * Handles layout registration, validation, and rendering.
 */
class DCF_Layout_Engine {
    /**
     * Register a layout
     *
     * @param string $slug Layout unique identifier
     * @param array  $args {
     *     Layout configuration arguments
     *
     *     @type string   $name            Display name of the layout
     *     @type callable $render_callback Callback function for rendering
     *     @type array    $supports        Array of supported field types
     *     @type array    $settings        Optional. Layout-specific settings
     * }
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    public function register_layout(string $slug, array $args) {
        // Validate required parameters
        $validation_result = $this->validate_layout_config($slug, $args);
        if (is_wp_error($validation_result)) {
            return $validation_result;
        }

        // Register the layout
        $success = DCF_Layout_Registry::register($slug, $args);
        
        if (!$success) {
            return new WP_Error(
                'duplicate_layout',
                sprintf(
                    __('Layout with slug "%s" is already registered.', 'elementor-dynamic-content-framework'),
                    $slug
                )
            );
        }

        return true;
    }

    /**
     * Validate layout configuration
     *
     * @param string $slug Layout slug
     * @param array  $args Layout arguments
     * @return true|WP_Error True if valid, WP_Error otherwise
     */
    private function validate_layout_config(string $slug, array $args) {
        // Validate slug
        if (empty($slug)) {
            return new WP_Error(
                'invalid_slug',
                __('Layout slug cannot be empty.', 'elementor-dynamic-content-framework')
            );
        }

        // Validate name
        if (empty($args['name'])) {
            return new WP_Error(
                'missing_name',
                __('Layout name is required.', 'elementor-dynamic-content-framework')
            );
        }

        // Validate render_callback
        if (empty($args['render_callback'])) {
            return new WP_Error(
                'missing_callback',
                __('Layout render_callback is required.', 'elementor-dynamic-content-framework')
            );
        }

        if (!is_callable($args['render_callback'])) {
            return new WP_Error(
                'invalid_callback',
                __('Layout render_callback must be a valid callable.', 'elementor-dynamic-content-framework')
            );
        }

        // Validate supports
        if (empty($args['supports']) || !is_array($args['supports'])) {
            return new WP_Error(
                'invalid_supports',
                __('Layout supports must be a non-empty array.', 'elementor-dynamic-content-framework')
            );
        }

        return true;
    }

    /**
     * Get all registered layouts
     *
     * @return array Array of all registered layouts
     */
    public function get_layouts(): array {
        return DCF_Layout_Registry::get_all();
    }

    /**
     * Get a specific layout by slug
     *
     * @param string $slug Layout slug
     * @return array|null Layout configuration or null if not found
     */
    public function get_layout(string $slug): ?array {
        return DCF_Layout_Registry::get($slug);
    }

    /**
     * Render a layout
     *
     * @param int    $group_id    Content group ID
     * @param string $layout_slug Layout identifier
     * @param array  $settings    Layout settings
     * @return string Rendered HTML
     */
    public function render(int $group_id, string $layout_slug, array $settings = []): string {
        // Get the layout
        $layout = $this->get_layout($layout_slug);
        
        if (!$layout) {
            return sprintf(
                '<div class="dcf-error">%s</div>',
                esc_html__('Layout not found.', 'elementor-dynamic-content-framework')
            );
        }

        // Enqueue layout-specific assets
        if (class_exists('DCF_Assets')) {
            DCF_Assets::enqueue_layout_assets($layout_slug);
        }

        // Get content items
        $items = DCF_Group::get_items($group_id);
        
        if (empty($items)) {
            return sprintf(
                '<div class="dcf-empty">%s</div>',
                esc_html__('No content items found.', 'elementor-dynamic-content-framework')
            );
        }

        // Apply filter to render arguments
        $render_args = apply_filters('dcf_layout_render_args', [
            'items' => $items,
            'settings' => $settings
        ], $layout_slug, $group_id);

        try {
            // Call the render callback
            $output = call_user_func($layout['render_callback'], $render_args['items'], $render_args['settings']);
            
            // Apply filter to output
            $output = apply_filters('dcf_layout_output', $output, $layout_slug, $group_id, $render_args);
            
            return $output;
        } catch (Exception $e) {
            // Log the error
            if (class_exists('DCF_Logger')) {
                DCF_Logger::error('Layout render error: ' . $e->getMessage(), [
                    'layout' => $layout_slug,
                    'group_id' => $group_id
                ]);
            }
            
            return sprintf(
                '<div class="dcf-error">%s</div>',
                esc_html__('An error occurred while rendering the layout.', 'elementor-dynamic-content-framework')
            );
        }
    }

    /**
     * Load a layout template
     *
     * @param string $slug Template slug
     * @param array  $data Template data
     * @return string Rendered template HTML
     */
    public function load_template(string $slug, array $data): string {
        $template_path = $this->locate_template($slug);
        
        if (!$template_path) {
            return sprintf(
                '<div class="dcf-error">%s</div>',
                esc_html(sprintf(__('Template "%s" not found.', 'elementor-dynamic-content-framework'), $slug))
            );
        }

        // Extract data to make variables available in template
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include $template_path;
        
        // Get the buffered content
        return ob_get_clean();
    }

    /**
     * Locate a layout template
     *
     * Checks theme directory first, then plugin directory.
     *
     * @param string $slug Template slug
     * @return string|false Template path or false if not found
     */
    public function locate_template(string $slug) {
        $template_name = "dcf-layouts/{$slug}.php";
        
        // Check in theme directory first
        $theme_template = locate_template($template_name);
        if ($theme_template) {
            return $theme_template;
        }
        
        // Use DCF_PLUGIN_DIR constant if available, otherwise calculate from __FILE__
        if (defined('DCF_PLUGIN_DIR')) {
            $plugin_root = DCF_PLUGIN_DIR;
        } else {
            $plugin_root = dirname(dirname(dirname(__FILE__))) . '/';
        }
        
        // Check in plugin templates directory (direct path)
        $plugin_template = $plugin_root . 'templates/' . $slug . '.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        // Also check in dcf-layouts subdirectory
        $plugin_template_subdir = $plugin_root . 'templates/dcf-layouts/' . $slug . '.php';
        if (file_exists($plugin_template_subdir)) {
            return $plugin_template_subdir;
        }
        
        return false;
    }
}
