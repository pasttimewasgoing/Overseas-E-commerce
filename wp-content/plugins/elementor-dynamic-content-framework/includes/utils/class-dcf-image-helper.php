<?php
/**
 * Image Helper Class
 *
 * Provides image optimization utilities including lazy loading and responsive srcset generation.
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/includes/utils
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * DCF_Image_Helper Class
 *
 * Handles image optimization features:
 * - Lazy loading attributes
 * - Responsive srcset generation
 * - Image optimization settings
 *
 * @since 1.0.0
 */
class DCF_Image_Helper {

    /**
     * Get image optimization settings
     *
     * @since 1.0.0
     * @return array Image optimization settings
     */
    public static function get_settings() {
        // Get settings from WordPress options
        // This integrates with the plugin settings system (task 14)
        $settings = get_option('dcf_settings', []);
        
        // Default values if settings don't exist
        $defaults = [
            'lazy_loading_enabled' => true,
            'srcset_enabled' => true
        ];
        
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Check if lazy loading is enabled
     *
     * @since 1.0.0
     * @return bool True if lazy loading is enabled
     */
    public static function is_lazy_loading_enabled() {
        $settings = self::get_settings();
        return !empty($settings['lazy_loading_enabled']);
    }


    /**
     * Check if srcset generation is enabled
     *
     * @since 1.0.0
     * @return bool True if srcset is enabled
     */
    public static function is_srcset_enabled() {
        $settings = self::get_settings();
        return !empty($settings['srcset_enabled']);
    }

    /**
     * Generate optimized image HTML
     *
     * @since 1.0.0
     * @param array $image_data Image data array with 'id', 'url', 'alt', 'width', 'height'
     * @param array $args Optional arguments for image rendering
     * @return string Optimized image HTML
     */
    public static function render_image($image_data, $args = []) {
        if (empty($image_data) || !isset($image_data['url'])) {
            return '';
        }

        $defaults = [
            'class' => '',
            'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw'
        ];

        $args = wp_parse_args($args, $defaults);

        $url = esc_url($image_data['url']);
        $alt = isset($image_data['alt']) ? esc_attr($image_data['alt']) : '';
        $width = isset($image_data['width']) ? absint($image_data['width']) : '';
        $height = isset($image_data['height']) ? absint($image_data['height']) : '';
        $class = !empty($args['class']) ? esc_attr($args['class']) : '';

        $attributes = [];
        $attributes[] = sprintf('src="%s"', $url);
        $attributes[] = sprintf('alt="%s"', $alt);

        if ($width) {
            $attributes[] = sprintf('width="%d"', $width);
        }

        if ($height) {
            $attributes[] = sprintf('height="%d"', $height);
        }

        if ($class) {
            $attributes[] = sprintf('class="%s"', $class);
        }

        // Add lazy loading attribute
        if (self::is_lazy_loading_enabled()) {
            $attributes[] = 'loading="lazy"';
        }

        // Generate srcset if image ID is available and srcset is enabled
        if (self::is_srcset_enabled() && isset($image_data['id']) && $image_data['id']) {
            $srcset = self::generate_srcset($image_data['id']);
            if (!empty($srcset)) {
                $attributes[] = sprintf('srcset="%s"', esc_attr($srcset));
                $attributes[] = sprintf('sizes="%s"', esc_attr($args['sizes']));
            }
        }

        return sprintf('<img %s>', implode(' ', $attributes));
    }


    /**
     * Generate responsive srcset attribute
     *
     * @since 1.0.0
     * @param int $attachment_id WordPress attachment ID
     * @return string Srcset attribute value
     */
    public static function generate_srcset($attachment_id) {
        if (empty($attachment_id)) {
            return '';
        }

        // Get image metadata
        $image_meta = wp_get_attachment_metadata($attachment_id);
        if (empty($image_meta)) {
            return '';
        }

        // Use WordPress built-in function to generate srcset
        $srcset = wp_get_attachment_image_srcset($attachment_id, 'full');

        return $srcset ? $srcset : '';
    }

    /**
     * Generate sizes attribute for responsive images
     *
     * @since 1.0.0
     * @param array $args Optional arguments for sizes generation
     * @return string Sizes attribute value
     */
    public static function generate_sizes($args = []) {
        $defaults = [
            'mobile' => '100vw',
            'tablet' => '50vw',
            'desktop' => '33vw',
            'mobile_breakpoint' => 768,
            'tablet_breakpoint' => 1024
        ];

        $args = wp_parse_args($args, $defaults);

        // Build sizes attribute
        $sizes = sprintf(
            '(max-width: %dpx) %s, (max-width: %dpx) %s, %s',
            $args['mobile_breakpoint'],
            $args['mobile'],
            $args['tablet_breakpoint'],
            $args['tablet'],
            $args['desktop']
        );

        return $sizes;
    }

    /**
     * Add lazy loading attribute to image tag
     *
     * @since 1.0.0
     * @param string $html Image HTML
     * @return string Modified image HTML with lazy loading
     */
    public static function add_lazy_loading($html) {
        if (!self::is_lazy_loading_enabled()) {
            return $html;
        }

        // Check if loading attribute already exists
        if (strpos($html, 'loading=') !== false) {
            return $html;
        }

        // Add loading="lazy" before the closing >
        $html = str_replace('<img ', '<img loading="lazy" ', $html);

        return $html;
    }


    /**
     * Add srcset to image tag
     *
     * @since 1.0.0
     * @param string $html Image HTML
     * @param int $attachment_id WordPress attachment ID
     * @param string $sizes Sizes attribute value
     * @return string Modified image HTML with srcset
     */
    public static function add_srcset($html, $attachment_id, $sizes = '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw') {
        if (!self::is_srcset_enabled()) {
            return $html;
        }

        // Check if srcset already exists
        if (strpos($html, 'srcset=') !== false) {
            return $html;
        }

        $srcset = self::generate_srcset($attachment_id);
        if (empty($srcset)) {
            return $html;
        }

        // Add srcset and sizes attributes
        $srcset_attr = sprintf('srcset="%s" sizes="%s"', esc_attr($srcset), esc_attr($sizes));
        $html = str_replace('<img ', '<img ' . $srcset_attr . ' ', $html);

        return $html;
    }

    /**
     * Optimize all images in HTML content
     *
     * @since 1.0.0
     * @param string $content HTML content
     * @return string Optimized HTML content
     */
    public static function optimize_content_images($content) {
        if (empty($content)) {
            return $content;
        }

        // Add lazy loading to all img tags
        if (self::is_lazy_loading_enabled()) {
            $content = preg_replace_callback(
                '/<img([^>]+)>/i',
                function($matches) {
                    $img_tag = $matches[0];
                    
                    // Skip if already has loading attribute
                    if (strpos($img_tag, 'loading=') !== false) {
                        return $img_tag;
                    }
                    
                    // Add loading="lazy"
                    return str_replace('<img', '<img loading="lazy"', $img_tag);
                },
                $content
            );
        }

        return $content;
    }


    /**
     * Get optimized image attributes array
     *
     * @since 1.0.0
     * @param array $image_data Image data array
     * @param array $args Optional arguments
     * @return array Image attributes
     */
    public static function get_image_attributes($image_data, $args = []) {
        if (empty($image_data) || !isset($image_data['url'])) {
            return [];
        }

        $defaults = [
            'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw'
        ];

        $args = wp_parse_args($args, $defaults);

        $attributes = [
            'src' => esc_url($image_data['url']),
            'alt' => isset($image_data['alt']) ? esc_attr($image_data['alt']) : ''
        ];

        if (isset($image_data['width']) && $image_data['width']) {
            $attributes['width'] = absint($image_data['width']);
        }

        if (isset($image_data['height']) && $image_data['height']) {
            $attributes['height'] = absint($image_data['height']);
        }

        // Add lazy loading
        if (self::is_lazy_loading_enabled()) {
            $attributes['loading'] = 'lazy';
        }

        // Add srcset
        if (self::is_srcset_enabled() && isset($image_data['id']) && $image_data['id']) {
            $srcset = self::generate_srcset($image_data['id']);
            if (!empty($srcset)) {
                $attributes['srcset'] = $srcset;
                $attributes['sizes'] = $args['sizes'];
            }
        }

        return $attributes;
    }

    /**
     * Process image field data from content items
     *
     * Converts image field data into optimized HTML output.
     * Handles both single images and gallery arrays.
     *
     * @since 1.0.0
     * @param mixed $field_value Image field value (array or null)
     * @param array $args Optional arguments for rendering
     * @return string Optimized image HTML or empty string
     */
    public static function process_image_field($field_value, $args = []) {
        if (empty($field_value)) {
            return '';
        }

        // Handle single image
        if (isset($field_value['url'])) {
            return self::render_image($field_value, $args);
        }

        // Handle gallery (array of images)
        if (is_array($field_value)) {
            $output = '';
            foreach ($field_value as $image) {
                if (isset($image['url'])) {
                    $output .= self::render_image($image, $args);
                }
            }
            return $output;
        }

        return '';
    }

    /**
     * Get responsive image HTML using WordPress attachment ID
     *
     * This is an alias for render_image() that accepts an attachment ID directly.
     *
     * @since 1.0.0
     * @param int $attachment_id WordPress attachment ID
     * @param string $size Image size (thumbnail, medium, large, full)
     * @param array $args Optional arguments
     * @return string Optimized image HTML
     */
    public static function get_responsive_image($attachment_id, $size = 'full', $args = []) {
        if (empty($attachment_id)) {
            return '';
        }

        // Get image data from attachment
        $image_url = wp_get_attachment_image_url($attachment_id, $size);
        if (!$image_url) {
            return '';
        }

        $image_meta = wp_get_attachment_metadata($attachment_id);
        $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

        $image_data = [
            'id' => $attachment_id,
            'url' => $image_url,
            'alt' => $alt_text,
            'width' => isset($image_meta['width']) ? $image_meta['width'] : '',
            'height' => isset($image_meta['height']) ? $image_meta['height'] : ''
        ];

        return self::render_image($image_data, $args);
    }
}
