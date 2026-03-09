<?php
/**
 * Grid Layout
 *
 * Implements responsive grid layout with configurable columns.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Grid_Layout class
 *
 * Renders content items in a responsive grid with configurable columns.
 */
class DCF_Grid_Layout implements DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * @return array Layout configuration
     */
    public static function get_config(): array {
        return [
            'slug' => 'grid',
            'name' => __('Grid', 'elementor-dynamic-content-framework'),
            'render_callback' => [__CLASS__, 'render'],
            'supports' => ['image', 'text', 'textarea', 'url', 'icon', 'video', 'gallery'],
            'settings' => [
                'columns_desktop' => [
                    'type' => 'number',
                    'label' => __('Columns (Desktop)', 'elementor-dynamic-content-framework'),
                    'default' => 3,
                    'min' => 1,
                    'max' => 6,
                    'description' => __('Number of columns on desktop devices', 'elementor-dynamic-content-framework')
                ],
                'columns_tablet' => [
                    'type' => 'number',
                    'label' => __('Columns (Tablet)', 'elementor-dynamic-content-framework'),
                    'default' => 2,
                    'min' => 1,
                    'max' => 4,
                    'description' => __('Number of columns on tablet devices', 'elementor-dynamic-content-framework')
                ],
                'columns_mobile' => [
                    'type' => 'number',
                    'label' => __('Columns (Mobile)', 'elementor-dynamic-content-framework'),
                    'default' => 1,
                    'min' => 1,
                    'max' => 2,
                    'description' => __('Number of columns on mobile devices', 'elementor-dynamic-content-framework')
                ],
                'gap' => [
                    'type' => 'number',
                    'label' => __('Gap (px)', 'elementor-dynamic-content-framework'),
                    'default' => 20,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'description' => __('Space between grid items in pixels', 'elementor-dynamic-content-framework')
                ]
            ]
        ];
    }

    /**
     * Render the grid layout
     *
     * @param array $items    Content items array
     * @param array $settings Layout settings
     * @return string HTML output
     */
    public static function render(array $items, array $settings): string {
        if (empty($items)) {
            return '';
        }

        // Generate unique ID for this grid instance
        $grid_id = 'dcf-grid-' . uniqid();

        // Parse settings with defaults
        $columns_desktop = isset($settings['columns_desktop']) ? absint($settings['columns_desktop']) : 3;
        $columns_tablet = isset($settings['columns_tablet']) ? absint($settings['columns_tablet']) : 2;
        $columns_mobile = isset($settings['columns_mobile']) ? absint($settings['columns_mobile']) : 1;
        $gap = isset($settings['gap']) ? absint($settings['gap']) : 20;

        // Validate column ranges
        $columns_desktop = max(1, min(6, $columns_desktop));
        $columns_tablet = max(1, min(4, $columns_tablet));
        $columns_mobile = max(1, min(2, $columns_mobile));
        $gap = max(0, min(100, $gap));

        // Build inline styles for responsive columns
        $inline_styles = self::build_responsive_styles($grid_id, $columns_desktop, $columns_tablet, $columns_mobile, $gap);

        // Start output buffering
        ob_start();
        ?>
        <style><?php echo $inline_styles; ?></style>
        <div class="dcf-grid-wrapper <?php echo esc_attr($grid_id); ?>">
            <div class="dcf-grid">
                <?php foreach ($items as $item): ?>
                    <div class="dcf-grid-item">
                        <?php self::render_grid_item_content($item); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build responsive CSS styles for the grid
     *
     * @param string $grid_id         Grid unique ID
     * @param int    $columns_desktop Desktop columns
     * @param int    $columns_tablet  Tablet columns
     * @param int    $columns_mobile  Mobile columns
     * @param int    $gap             Gap in pixels
     * @return string CSS styles
     */
    private static function build_responsive_styles(string $grid_id, int $columns_desktop, int $columns_tablet, int $columns_mobile, int $gap): string {
        $styles = '';

        // Desktop styles (default)
        $styles .= ".{$grid_id} .dcf-grid {";
        $styles .= "display: grid;";
        $styles .= "grid-template-columns: repeat({$columns_desktop}, 1fr);";
        $styles .= "gap: {$gap}px;";
        $styles .= "}";

        // Tablet styles (max-width: 1024px)
        $styles .= "@media (max-width: 1024px) {";
        $styles .= ".{$grid_id} .dcf-grid {";
        $styles .= "grid-template-columns: repeat({$columns_tablet}, 1fr);";
        $styles .= "}";
        $styles .= "}";

        // Mobile styles (max-width: 768px)
        $styles .= "@media (max-width: 768px) {";
        $styles .= ".{$grid_id} .dcf-grid {";
        $styles .= "grid-template-columns: repeat({$columns_mobile}, 1fr);";
        $styles .= "}";
        $styles .= "}";

        return $styles;
    }

    /**
     * Render individual grid item content
     *
     * @param array $item Content item data
     */
    private static function render_grid_item_content(array $item): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-grid-item-content">';

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
        $url = esc_url($image['url']);
        $alt = isset($image['alt']) ? esc_attr($image['alt']) : '';
        $width = isset($image['width']) ? absint($image['width']) : '';
        $height = isset($image['height']) ? absint($image['height']) : '';

        echo '<div class="dcf-grid-item-image">';
        echo '<img src="' . $url . '" alt="' . $alt . '"';
        if ($width) echo ' width="' . $width . '"';
        if ($height) echo ' height="' . $height . '"';
        echo ' loading="lazy">';
        echo '</div>';
    }

    /**
     * Render gallery field
     *
     * @param array $images Array of image data
     */
    private static function render_gallery_field(array $images): void {
        echo '<div class="dcf-grid-item-gallery">';
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
        echo '<div class="dcf-grid-item-video">';
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

        echo '<div class="dcf-grid-item-icon">';
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
        $class = 'dcf-grid-item-' . sanitize_html_class($field_name);
        
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
                'dcf-grid' => $plugin_url . 'assets/css/layouts/grid.css'
            ],
            'js' => []
        ];
    }
}
