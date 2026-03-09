<?php
/**
 * Layout Interface
 *
 * Defines the contract that all layout classes must implement.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Layout_Interface
 *
 * Interface for all layout implementations.
 */
interface DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * Returns the layout configuration including slug, name, render callback,
     * supported field types, and settings definitions.
     *
     * @return array {
     *     Layout configuration array
     *
     *     @type string   $slug            Unique layout identifier
     *     @type string   $name            Display name of the layout
     *     @type callable $render_callback Callback function for rendering
     *     @type array    $supports        Array of supported field types
     *     @type array    $settings        Layout-specific settings definitions
     * }
     */
    public static function get_config(): array;

    /**
     * Render the layout
     *
     * Generates HTML output for the layout based on content items and settings.
     *
     * @param array $items    Array of content items to render
     * @param array $settings Layout settings from Elementor widget
     * @return string Rendered HTML output
     */
    public static function render(array $items, array $settings): string;

    /**
     * Get layout assets
     *
     * Returns CSS and JavaScript dependencies required by this layout.
     *
     * @return array {
     *     Asset arrays
     *
     *     @type array $css Array of CSS file URLs (key => url)
     *     @type array $js  Array of JavaScript file URLs (key => url)
     * }
     */
    public static function get_assets(): array;
}
