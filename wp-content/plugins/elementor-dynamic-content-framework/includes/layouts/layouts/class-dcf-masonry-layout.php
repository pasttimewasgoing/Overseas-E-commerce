<?php
/**
 * Masonry Layout
 *
 * Implements masonry waterfall layout with configurable columns.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Masonry_Layout class
 *
 * Renders content items in a masonry waterfall layout with configurable columns.
 */
class DCF_Masonry_Layout implements DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * @return array Layout configuration
     */
    public static function get_config(): array {
        return [
            'slug' => 'masonry',
            'name' => __('Masonry', 'elementor-dynamic-content-framework'),
            'render_callback' => [__CLASS__, 'render'],
            'supports' => ['image', 'text', 'textarea', 'url', 'icon', 'video', 'gallery'],
            'settings' => [
                'columns' => [
                    'type' => 'number',
                    'label' => __('Columns', 'elementor-dynamic-content-framework'),
                    'default' => 3,
                    'min' => 2,
                    'max' => 6,
                    'description' => __('Number of columns in the masonry layout', 'elementor-dynamic-content-framework')
                ],
                'gap' => [
                    'type' => 'number',
                    'label' => __('Gap (px)', 'elementor-dynamic-content-framework'),
                    'default' => 20,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'description' => __('Space between masonry items in pixels', 'elementor-dynamic-content-framework')
                ]
            ]
        ];
    }

    /**
     * Render the masonry layout
     *
     * @param array $items    Content items array
     * @param array $settings Layout settings
     * @return string HTML output
     */
    public static function render(array $items, array $settings): string {
        if (empty($items)) {
            return '';
        }

        // Generate unique ID for this masonry instance
        $masonry_id = 'dcf-masonry-' . uniqid();

        // Parse settings with defaults
        $columns = isset($settings['columns']) ? absint($settings['columns']) : 3;
        $gap = isset($settings['gap']) ? absint($settings['gap']) : 20;

        // Validate column range
        $columns = max(2, min(6, $columns));
        $gap = max(0, min(100, $gap));

        // Build inline styles for masonry layout
        $inline_styles = self::build_masonry_styles($masonry_id, $columns, $gap);

        // Start output buffering
        ob_start();
        ?>
        <style><?php echo $inline_styles; ?></style>
        <div class="dcf-masonry-wrapper <?php echo esc_attr($masonry_id); ?>" data-columns="<?php echo esc_attr($columns); ?>" data-gap="<?php echo esc_attr($gap); ?>">
            <div class="dcf-masonry">
                <?php foreach ($items as $item): ?>
                    <div class="dcf-masonry-item">
                        <?php self::render_masonry_item_content($item); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build CSS styles for the masonry layout
     *
     * @param string $masonry_id Masonry unique ID
     * @param int    $columns    Number of columns
     * @param int    $gap        Gap in pixels
     * @return string CSS styles
     */
    private static function build_masonry_styles(string $masonry_id, int $columns, int $gap): string {
        $styles = '';

        // Masonry container styles using CSS columns
        $styles .= ".{$masonry_id} .dcf-masonry {";
        $styles .= "column-count: {$columns};";
        $styles .= "column-gap: {$gap}px;";
        $styles .= "}";

        // Masonry item styles
        $styles .= ".{$masonry_id} .dcf-masonry-item {";
        $styles .= "break-inside: avoid;";
        $styles .= "margin-bottom: {$gap}px;";
        $styles .= "display: inline-block;";
        $styles .= "width: 100%;";
        $styles .= "}";

        // Responsive styles for tablet (max-width: 1024px)
        $tablet_columns = max(2, $columns - 1);
        $styles .= "@media (max-width: 1024px) {";
        $styles .= ".{$masonry_id} .dcf-masonry {";
        $styles .= "column-count: {$tablet_columns};";
        $styles .= "}";
        $styles .= "}";

        // Responsive styles for mobile (max-width: 768px)
        $mobile_columns = max(1, $columns - 2);
        $styles .= "@media (max-width: 768px) {";
        $styles .= ".{$masonry_id} .dcf-masonry {";
        $styles .= "column-count: {$mobile_columns};";
        $styles .= "}";
        $styles .= "}";

        return $styles;
    }

    /**
     * Render individual masonry item content
     *
     * @param array $item Content item data
     */
    private static function render_masonry_item_content(array $item): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-masonry-item-content">';

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

        echo '<div class="dcf-masonry-item-image">';
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
        echo '<div class="dcf-masonry-item-gallery">';
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
        echo '<div class="dcf-masonry-item-video">';
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

        echo '<div class="dcf-masonry-item-icon">';
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
        $class = 'dcf-masonry-item-' . sanitize_html_class($field_name);
        
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
                'dcf-masonry' => $plugin_url . 'assets/css/layouts/masonry.css'
            ],
            'js' => []
        ];
    }
}
