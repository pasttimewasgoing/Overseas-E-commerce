<?php
/**
 * Popup Layout
 *
 * Implements modal popup layout with clickable thumbnails.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Popup_Layout class
 *
 * Renders content items as clickable thumbnails that open in modal popups.
 */
class DCF_Popup_Layout implements DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * @return array Layout configuration
     */
    public static function get_config(): array {
        return [
            'slug' => 'popup',
            'name' => __('Popup', 'elementor-dynamic-content-framework'),
            'render_callback' => [__CLASS__, 'render'],
            'supports' => ['image', 'text', 'textarea', 'url', 'icon', 'video', 'gallery'],
            'settings' => [
                'thumbnail_size' => [
                    'type' => 'select',
                    'label' => __('Thumbnail Size', 'elementor-dynamic-content-framework'),
                    'default' => 'medium',
                    'options' => [
                        'small' => __('Small', 'elementor-dynamic-content-framework'),
                        'medium' => __('Medium', 'elementor-dynamic-content-framework'),
                        'large' => __('Large', 'elementor-dynamic-content-framework')
                    ],
                    'description' => __('Size of thumbnail images', 'elementor-dynamic-content-framework')
                ],
                'animation' => [
                    'type' => 'select',
                    'label' => __('Animation', 'elementor-dynamic-content-framework'),
                    'default' => 'fade',
                    'options' => [
                        'fade' => __('Fade', 'elementor-dynamic-content-framework'),
                        'slide' => __('Slide', 'elementor-dynamic-content-framework'),
                        'zoom' => __('Zoom', 'elementor-dynamic-content-framework')
                    ],
                    'description' => __('Popup animation effect', 'elementor-dynamic-content-framework')
                ],
                'columns_desktop' => [
                    'type' => 'number',
                    'label' => __('Columns (Desktop)', 'elementor-dynamic-content-framework'),
                    'default' => 4,
                    'min' => 1,
                    'max' => 6,
                    'description' => __('Number of thumbnail columns on desktop', 'elementor-dynamic-content-framework')
                ],
                'columns_tablet' => [
                    'type' => 'number',
                    'label' => __('Columns (Tablet)', 'elementor-dynamic-content-framework'),
                    'default' => 3,
                    'min' => 1,
                    'max' => 4,
                    'description' => __('Number of thumbnail columns on tablet', 'elementor-dynamic-content-framework')
                ],
                'columns_mobile' => [
                    'type' => 'number',
                    'label' => __('Columns (Mobile)', 'elementor-dynamic-content-framework'),
                    'default' => 2,
                    'min' => 1,
                    'max' => 2,
                    'description' => __('Number of thumbnail columns on mobile', 'elementor-dynamic-content-framework')
                ],
                'gap' => [
                    'type' => 'number',
                    'label' => __('Gap (px)', 'elementor-dynamic-content-framework'),
                    'default' => 15,
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                    'description' => __('Space between thumbnails in pixels', 'elementor-dynamic-content-framework')
                ]
            ]
        ];
    }

    /**
     * Render the popup layout
     *
     * @param array $items    Content items array
     * @param array $settings Layout settings
     * @return string HTML output
     */
    public static function render(array $items, array $settings): string {
        if (empty($items)) {
            return '';
        }

        // Generate unique ID for this popup instance
        $popup_id = 'dcf-popup-' . uniqid();

        // Parse settings with defaults
        $thumbnail_size = isset($settings['thumbnail_size']) ? sanitize_text_field($settings['thumbnail_size']) : 'medium';
        $animation = isset($settings['animation']) ? sanitize_text_field($settings['animation']) : 'fade';
        $columns_desktop = isset($settings['columns_desktop']) ? absint($settings['columns_desktop']) : 4;
        $columns_tablet = isset($settings['columns_tablet']) ? absint($settings['columns_tablet']) : 3;
        $columns_mobile = isset($settings['columns_mobile']) ? absint($settings['columns_mobile']) : 2;
        $gap = isset($settings['gap']) ? absint($settings['gap']) : 15;

        // Validate ranges
        $columns_desktop = max(1, min(6, $columns_desktop));
        $columns_tablet = max(1, min(4, $columns_tablet));
        $columns_mobile = max(1, min(2, $columns_mobile));
        $gap = max(0, min(100, $gap));

        // Build inline styles
        $inline_styles = self::build_popup_styles($popup_id, $columns_desktop, $columns_tablet, $columns_mobile, $gap, $thumbnail_size);

        // Start output buffering
        ob_start();
        ?>
        <style><?php echo $inline_styles; ?></style>
        <div class="dcf-popup-wrapper <?php echo esc_attr($popup_id); ?>" data-animation="<?php echo esc_attr($animation); ?>">
            <div class="dcf-popup-grid">
                <?php foreach ($items as $index => $item): ?>
                    <div class="dcf-popup-thumbnail" data-popup-index="<?php echo esc_attr($index); ?>">
                        <?php self::render_thumbnail($item, $index); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal Container -->
            <div class="dcf-popup-modal" id="<?php echo esc_attr($popup_id); ?>-modal">
                <div class="dcf-popup-overlay"></div>
                <div class="dcf-popup-content-wrapper">
                    <button class="dcf-popup-close" aria-label="<?php echo esc_attr__('Close', 'elementor-dynamic-content-framework'); ?>">
                        <span>&times;</span>
                    </button>
                    <button class="dcf-popup-nav dcf-popup-prev" aria-label="<?php echo esc_attr__('Previous', 'elementor-dynamic-content-framework'); ?>">
                        <span>&lsaquo;</span>
                    </button>
                    <button class="dcf-popup-nav dcf-popup-next" aria-label="<?php echo esc_attr__('Next', 'elementor-dynamic-content-framework'); ?>">
                        <span>&rsaquo;</span>
                    </button>
                    <div class="dcf-popup-content">
                        <?php foreach ($items as $index => $item): ?>
                            <div class="dcf-popup-item" data-index="<?php echo esc_attr($index); ?>">
                                <?php self::render_popup_content($item); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build CSS styles for the popup layout
     *
     * @param string $popup_id        Popup unique ID
     * @param int    $columns_desktop Desktop columns
     * @param int    $columns_tablet  Tablet columns
     * @param int    $columns_mobile  Mobile columns
     * @param int    $gap             Gap in pixels
     * @param string $thumbnail_size  Thumbnail size
     * @return string CSS styles
     */
    private static function build_popup_styles(string $popup_id, int $columns_desktop, int $columns_tablet, int $columns_mobile, int $gap, string $thumbnail_size): string {
        $styles = '';

        // Thumbnail size mapping
        $size_map = [
            'small' => '150px',
            'medium' => '250px',
            'large' => '350px'
        ];
        $thumb_height = isset($size_map[$thumbnail_size]) ? $size_map[$thumbnail_size] : $size_map['medium'];

        // Desktop styles (default)
        $styles .= ".{$popup_id} .dcf-popup-grid {";
        $styles .= "display: grid;";
        $styles .= "grid-template-columns: repeat({$columns_desktop}, 1fr);";
        $styles .= "gap: {$gap}px;";
        $styles .= "}";

        $styles .= ".{$popup_id} .dcf-popup-thumbnail {";
        $styles .= "height: {$thumb_height};";
        $styles .= "}";

        // Tablet styles (max-width: 1024px)
        $styles .= "@media (max-width: 1024px) {";
        $styles .= ".{$popup_id} .dcf-popup-grid {";
        $styles .= "grid-template-columns: repeat({$columns_tablet}, 1fr);";
        $styles .= "}";
        $styles .= "}";

        // Mobile styles (max-width: 768px)
        $styles .= "@media (max-width: 768px) {";
        $styles .= ".{$popup_id} .dcf-popup-grid {";
        $styles .= "grid-template-columns: repeat({$columns_mobile}, 1fr);";
        $styles .= "}";
        $styles .= "}";

        return $styles;
    }

    /**
     * Render thumbnail for popup trigger
     *
     * @param array $item  Content item data
     * @param int   $index Item index
     */
    private static function render_thumbnail(array $item, int $index): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-popup-thumbnail-inner">';

        // Try to find an image field for thumbnail
        $thumbnail_found = false;
        foreach ($data as $field_name => $field_value) {
            if (is_array($field_value) && isset($field_value['url']) && isset($field_value['id'])) {
                // Found an image field
                echo DCF_Image_Helper::render_image($field_value);
                $thumbnail_found = true;
                break;
            }
        }

        // If no image found, show a placeholder with text
        if (!$thumbnail_found) {
            echo '<div class="dcf-popup-thumbnail-placeholder">';
            // Try to find a title or text field
            foreach ($data as $field_name => $field_value) {
                if (!is_array($field_value) && !empty($field_value)) {
                    echo '<span>' . esc_html(wp_trim_words($field_value, 5)) . '</span>';
                    break;
                }
            }
            echo '</div>';
        }

        echo '<div class="dcf-popup-thumbnail-overlay">';
        echo '<span class="dcf-popup-thumbnail-icon">+</span>';
        echo '</div>';

        echo '</div>';
    }

    /**
     * Render full popup content
     *
     * @param array $item Content item data
     */
    private static function render_popup_content(array $item): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-popup-item-content">';

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
        echo '<div class="dcf-popup-item-image">';
        echo DCF_Image_Helper::render_image($image);
        echo '</div>';
    }

    /**
     * Render gallery field
     *
     * @param array $images Array of image data
     */
    private static function render_gallery_field(array $images): void {
        echo '<div class="dcf-popup-item-gallery">';
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
        echo '<div class="dcf-popup-item-video">';
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

        echo '<div class="dcf-popup-item-icon">';
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
        $class = 'dcf-popup-item-' . sanitize_html_class($field_name);
        
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
                'dcf-popup' => $plugin_url . 'assets/css/layouts/popup.css'
            ],
            'js' => [
                'dcf-popup' => $plugin_url . 'assets/js/layouts/popup.js'
            ]
        ];
    }
}
