<?php
/**
 * List Layout
 *
 * Implements vertical list layout for content items.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_List_Layout class
 *
 * Renders content items as a vertical list.
 */
class DCF_List_Layout implements DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * @return array Layout configuration
     */
    public static function get_config(): array {
        return [
            'slug' => 'list',
            'name' => __('List', 'elementor-dynamic-content-framework'),
            'render_callback' => [__CLASS__, 'render'],
            'supports' => ['image', 'text', 'textarea', 'url', 'icon', 'video', 'gallery'],
            'settings' => [
                'item_spacing' => [
                    'type' => 'number',
                    'label' => __('Item Spacing (px)', 'elementor-dynamic-content-framework'),
                    'default' => 20,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'description' => __('Space between list items in pixels', 'elementor-dynamic-content-framework')
                ],
                'show_divider' => [
                    'type' => 'switcher',
                    'label' => __('Show Divider', 'elementor-dynamic-content-framework'),
                    'default' => 'no',
                    'description' => __('Show divider line between items', 'elementor-dynamic-content-framework')
                ],
                'divider_color' => [
                    'type' => 'color',
                    'label' => __('Divider Color', 'elementor-dynamic-content-framework'),
                    'default' => '#e0e0e0',
                    'condition' => [
                        'show_divider' => 'yes'
                    ],
                    'description' => __('Color of the divider line', 'elementor-dynamic-content-framework')
                ]
            ]
        ];
    }

    /**
     * Render the list layout
     *
     * @param array $items    Content items array
     * @param array $settings Layout settings
     * @return string HTML output
     */
    public static function render(array $items, array $settings): string {
        if (empty($items)) {
            return '';
        }

        // Generate unique ID for this list instance
        $list_id = 'dcf-list-' . uniqid();

        // Parse settings with defaults
        $item_spacing = isset($settings['item_spacing']) ? absint($settings['item_spacing']) : 20;
        $show_divider = isset($settings['show_divider']) && $settings['show_divider'] === 'yes';
        $divider_color = isset($settings['divider_color']) ? sanitize_hex_color($settings['divider_color']) : '#e0e0e0';

        // Validate ranges
        $item_spacing = max(0, min(100, $item_spacing));

        // Build inline styles
        $inline_styles = self::build_list_styles($list_id, $item_spacing, $show_divider, $divider_color);

        // Start output buffering
        ob_start();
        ?>
        <style><?php echo $inline_styles; ?></style>
        <div class="dcf-list-wrapper <?php echo esc_attr($list_id); ?>">
            <div class="dcf-list">
                <?php foreach ($items as $index => $item): ?>
                    <div class="dcf-list-item <?php echo $show_divider && $index > 0 ? 'has-divider' : ''; ?>">
                        <?php self::render_list_item_content($item); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build CSS styles for the list
     *
     * @param string $list_id       List unique ID
     * @param int    $item_spacing  Item spacing in pixels
     * @param bool   $show_divider  Whether to show divider
     * @param string $divider_color Divider color
     * @return string CSS styles
     */
    private static function build_list_styles(string $list_id, int $item_spacing, bool $show_divider, string $divider_color): string {
        $styles = '';

        // List item spacing
        $styles .= ".{$list_id} .dcf-list-item {";
        $styles .= "margin-bottom: {$item_spacing}px;";
        $styles .= "}";

        $styles .= ".{$list_id} .dcf-list-item:last-child {";
        $styles .= "margin-bottom: 0;";
        $styles .= "}";

        // Divider styles
        if ($show_divider) {
            $styles .= ".{$list_id} .dcf-list-item.has-divider {";
            $styles .= "border-top: 1px solid {$divider_color};";
            $styles .= "padding-top: {$item_spacing}px;";
            $styles .= "}";
        }

        return $styles;
    }

    /**
     * Render individual list item content
     *
     * @param array $item Content item data
     */
    private static function render_list_item_content(array $item): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-list-item-content">';

        // Render each field in the item
        foreach ($data as $field_name => $field_value) {
            if (empty($field_value)) {
                continue;
            }

            self::render_field($field_name, $field_value);
        }

        echo '</div>';
    }

    /**
     * Render a single field based on its type
     *
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     */
    private static function render_field(string $field_name, $field_value): void {
        // Detect field type from value structure
        if (is_array($field_value)) {
            // Check if it's an image field
            if (isset($field_value['url']) && isset($field_value['id'])) {
                self::render_image_field($field_value);
            }
            // Check if it's a gallery field (array of images)
            elseif (isset($field_value[0]) && is_array($field_value[0]) && isset($field_value[0]['url'])) {
                self::render_gallery_field($field_value);
            }
            // Check if it's a video field
            elseif (isset($field_value['type']) && $field_value['type'] === 'url') {
                self::render_video_field($field_value);
            }
            // Check if it's an icon field
            elseif (isset($field_value['library']) && isset($field_value['value'])) {
                self::render_icon_field($field_value);
            }
        } else {
            // Simple text field
            self::render_text_field($field_name, $field_value);
        }
    }

    /**
     * Render image field
     *
     * @param array $image Image data
     */
    private static function render_image_field(array $image): void {
        echo '<div class="dcf-list-item-image">';
        echo DCF_Image_Helper::render_image($image);
        echo '</div>';
    }

    /**
     * Render gallery field
     *
     * @param array $images Array of image data
     */
    private static function render_gallery_field(array $images): void {
        echo '<div class="dcf-list-item-gallery">';
        foreach ($images as $image) {
            if (isset($image['url'])) {
                self::render_image_field($image);
            }
        }
        echo '</div>';
    }

    /**
     * Render video field
     *
     * @param array $video Video data
     */
    private static function render_video_field(array $video): void {
        if (empty($video['url'])) {
            return;
        }

        $url = esc_url($video['url']);
        echo '<div class="dcf-list-item-video">';
        echo '<video controls>';
        echo '<source src="' . $url . '"';
        if (isset($video['mime_type'])) {
            echo ' type="' . esc_attr($video['mime_type']) . '"';
        }
        echo '>';
        echo esc_html__('Your browser does not support the video tag.', 'elementor-dynamic-content-framework');
        echo '</video>';
        echo '</div>';
    }

    /**
     * Render icon field
     *
     * @param array $icon Icon data
     */
    private static function render_icon_field(array $icon): void {
        $library = isset($icon['library']) ? esc_attr($icon['library']) : 'fontawesome';
        $value = isset($icon['value']) ? esc_attr($icon['value']) : '';

        if (empty($value)) {
            return;
        }

        echo '<div class="dcf-list-item-icon">';
        if ($library === 'fontawesome') {
            echo '<i class="' . $value . '"></i>';
        } else {
            // Custom icon handling
            echo '<span class="dcf-icon ' . $value . '"></span>';
        }
        echo '</div>';
    }

    /**
     * Render text field
     *
     * @param string $field_name  Field name
     * @param string $field_value Field value
     */
    private static function render_text_field(string $field_name, string $field_value): void {
        $class = 'dcf-list-item-' . sanitize_html_class($field_name);
        
        // Check if it's a URL
        if (filter_var($field_value, FILTER_VALIDATE_URL)) {
            echo '<div class="' . esc_attr($class) . '">';
            echo '<a href="' . esc_url($field_value) . '" target="_blank" rel="noopener noreferrer">';
            echo esc_html($field_value);
            echo '</a>';
            echo '</div>';
        } else {
            // Regular text
            echo '<div class="' . esc_attr($class) . '">';
            echo wp_kses_post(wpautop($field_value));
            echo '</div>';
        }
    }

    /**
     * Get layout assets (CSS/JS dependencies)
     *
     * @return array {
     *     @type array $css CSS file paths
     *     @type array $js  JavaScript file paths
     * }
     */
    public static function get_assets(): array {
        $plugin_url = plugin_dir_url(dirname(dirname(dirname(__FILE__))));
        
        return [
            'css' => [
                'dcf-list' => $plugin_url . 'assets/css/layouts/list.css'
            ],
            'js' => []
        ];
    }
}
