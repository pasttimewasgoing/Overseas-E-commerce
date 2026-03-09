<?php
/**
 * Slider Layout
 *
 * Implements slider/carousel layout using Swiper.js
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/layouts/layouts
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Slider_Layout class
 *
 * Renders content items as a carousel with navigation and pagination.
 */
class DCF_Slider_Layout implements DCF_Layout_Interface {
    /**
     * Get layout configuration
     *
     * @return array Layout configuration
     */
    public static function get_config(): array {
        return [
            'slug' => 'slider',
            'name' => __('Slider', 'elementor-dynamic-content-framework'),
            'render_callback' => [__CLASS__, 'render'],
            'supports' => ['image', 'text', 'textarea', 'url', 'icon', 'video', 'gallery'],
            'settings' => [
                'autoplay' => [
                    'type' => 'switcher',
                    'label' => __('Autoplay', 'elementor-dynamic-content-framework'),
                    'default' => 'yes',
                    'description' => __('Enable automatic slide transition', 'elementor-dynamic-content-framework')
                ],
                'speed' => [
                    'type' => 'number',
                    'label' => __('Speed (ms)', 'elementor-dynamic-content-framework'),
                    'default' => 3000,
                    'min' => 1000,
                    'max' => 10000,
                    'step' => 100,
                    'description' => __('Autoplay delay in milliseconds', 'elementor-dynamic-content-framework')
                ],
                'loop' => [
                    'type' => 'switcher',
                    'label' => __('Loop', 'elementor-dynamic-content-framework'),
                    'default' => 'yes',
                    'description' => __('Enable continuous loop mode', 'elementor-dynamic-content-framework')
                ],
                'navigation' => [
                    'type' => 'switcher',
                    'label' => __('Show Navigation', 'elementor-dynamic-content-framework'),
                    'default' => 'yes',
                    'description' => __('Show previous/next navigation arrows', 'elementor-dynamic-content-framework')
                ],
                'pagination' => [
                    'type' => 'switcher',
                    'label' => __('Show Pagination', 'elementor-dynamic-content-framework'),
                    'default' => 'yes',
                    'description' => __('Show pagination dots', 'elementor-dynamic-content-framework')
                ]
            ]
        ];
    }

    /**
     * Render the slider layout
     *
     * @param array $items    Content items array
     * @param array $settings Layout settings
     * @return string HTML output
     */
    public static function render(array $items, array $settings): string {
        if (empty($items)) {
            return '';
        }

        // Generate unique ID for this slider instance
        $slider_id = 'dcf-slider-' . uniqid();

        // Parse settings with defaults
        $autoplay = isset($settings['autoplay']) && $settings['autoplay'] === 'yes';
        $speed = isset($settings['speed']) ? absint($settings['speed']) : 3000;
        $loop = isset($settings['loop']) && $settings['loop'] === 'yes';
        $navigation = isset($settings['navigation']) && $settings['navigation'] === 'yes';
        $pagination = isset($settings['pagination']) && $settings['pagination'] === 'yes';

        // Build Swiper configuration
        $swiper_config = [
            'loop' => $loop,
            'speed' => 500,
            'autoplay' => $autoplay ? ['delay' => $speed, 'disableOnInteraction' => false] : false,
            'navigation' => $navigation ? [
                'nextEl' => ".{$slider_id} .swiper-button-next",
                'prevEl' => ".{$slider_id} .swiper-button-prev"
            ] : false,
            'pagination' => $pagination ? [
                'el' => ".{$slider_id} .swiper-pagination",
                'clickable' => true
            ] : false
        ];

        // Start output buffering
        ob_start();
        ?>
        <div class="dcf-slider-wrapper <?php echo esc_attr($slider_id); ?>" data-swiper-config='<?php echo esc_attr(wp_json_encode($swiper_config)); ?>'>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($items as $item): ?>
                        <div class="swiper-slide">
                            <?php self::render_slide_content($item); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($navigation): ?>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                <?php endif; ?>

                <?php if ($pagination): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render individual slide content
     *
     * @param array $item Content item data
     */
    private static function render_slide_content(array $item): void {
        $data = isset($item['data']) ? $item['data'] : [];
        
        if (empty($data)) {
            return;
        }

        echo '<div class="dcf-slide-content">';

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
            // Check if it's an image field (more flexible check)
            if (isset($field_value['url']) && !empty($field_value['url'])) {
                // It's an image if it has a URL and optionally an ID
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
        // Validate URL exists and is not empty
        if (empty($image['url'])) {
            return;
        }
        
        $url = esc_url($image['url']);
        $alt = isset($image['alt']) ? esc_attr($image['alt']) : '';
        $width = isset($image['width']) ? absint($image['width']) : '';
        $height = isset($image['height']) ? absint($image['height']) : '';

        echo '<div class="dcf-slide-image">';
        echo '<img src="' . $url . '" alt="' . $alt . '"';
        if ($width) echo ' width="' . $width . '"';
        if ($height) echo ' height="' . $height . '"';
        echo '>';
        echo '</div>';
    }

    /**
     * Render gallery field
     *
     * @param array $images Array of image data
     */
    private static function render_gallery_field(array $images): void {
        echo '<div class="dcf-slide-gallery">';
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
        echo '<div class="dcf-slide-video">';
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
     * Render text field
     *
     * @param string $field_name  Field name
     * @param string $field_value Field value
     */
    private static function render_text_field(string $field_name, string $field_value): void {
        $class = 'dcf-slide-' . sanitize_html_class($field_name);
        
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
                'swiper' => 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
                'dcf-slider' => $plugin_url . 'assets/css/layouts/slider.css'
            ],
            'js' => [
                'swiper' => 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
                'dcf-slider' => $plugin_url . 'assets/js/layouts/slider.js'
            ]
        ];
    }
}
