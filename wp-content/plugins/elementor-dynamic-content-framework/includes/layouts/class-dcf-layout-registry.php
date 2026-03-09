<?php
/**
 * Layout Registry
 *
 * Maintains the registry of all registered layouts.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Layout_Registry class
 *
 * Manages layout registration and retrieval.
 */
class DCF_Layout_Registry {
    /**
     * Registered layouts
     *
     * @var array
     */
    private static $layouts = [];

    /**
     * Register a layout
     *
     * @param string $slug Layout unique identifier
     * @param array  $args Layout configuration
     * @return bool True on success, false on failure
     */
    public static function register(string $slug, array $args): bool {
        // Check if slug already exists
        if (isset(self::$layouts[$slug])) {
            trigger_error(
                sprintf(
                    __('Layout with slug "%s" is already registered. Duplicate registration ignored.', 'elementor-dynamic-content-framework'),
                    $slug
                ),
                E_USER_WARNING
            );
            return false;
        }

        // Store the layout
        self::$layouts[$slug] = array_merge(['slug' => $slug], $args);
        return true;
    }

    /**
     * Get all registered layouts
     *
     * @return array Array of all registered layouts
     */
    public static function get_all(): array {
        return self::$layouts;
    }

    /**
     * Get a specific layout by slug
     *
     * @param string $slug Layout slug
     * @return array|null Layout configuration or null if not found
     */
    public static function get(string $slug): ?array {
        return self::$layouts[$slug] ?? null;
    }

    /**
     * Check if a layout is registered
     *
     * @param string $slug Layout slug
     * @return bool True if registered, false otherwise
     */
    public static function exists(string $slug): bool {
        return isset(self::$layouts[$slug]);
    }

    /**
     * Unregister a layout (for testing purposes)
     *
     * @param string $slug Layout slug
     * @return bool True on success, false if layout doesn't exist
     */
    public static function unregister(string $slug): bool {
        if (!isset(self::$layouts[$slug])) {
            return false;
        }

        unset(self::$layouts[$slug]);
        return true;
    }

    /**
     * Clear all registered layouts (for testing purposes)
     */
    public static function clear(): void {
        self::$layouts = [];
    }
}
