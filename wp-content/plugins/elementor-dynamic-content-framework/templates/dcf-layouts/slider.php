<?php
/**
 * Slider Layout Template
 *
 * This template can be overridden by copying it to yourtheme/dcf-layouts/slider.php
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/templates/dcf-layouts
 * @var array $items    Content items array
 * @var array $settings Layout settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// This template is optional - the DCF_Slider_Layout class handles rendering directly
// This file exists to allow theme developers to override the slider template if needed

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
?>

<div class="dcf-slider-wrapper <?php echo esc_attr($slider_id); ?>" data-swiper-config='<?php echo esc_attr(wp_json_encode($swiper_config)); ?>'>
    <div class="swiper">
        <div class="swiper-wrapper">
            <?php foreach ($items as $item): ?>
                <?php
                $data = isset($item['data']) ? $item['data'] : [];
                if (empty($data)) {
                    continue;
                }
                ?>
                <div class="swiper-slide">
                    <div class="dcf-slide-content">
                        <?php
                        // Render each field in the item
                        foreach ($data as $field_name => $field_value) {
                            if (empty($field_value)) {
                                continue;
                            }

                            // Render based on field type
                            if (is_array($field_value)) {
                                // Image field
                                if (isset($field_value['url']) && isset($field_value['id'])) {
                                    ?>
                                    <div class="dcf-slide-image">
                                        <?php echo DCF_Image_Helper::render_image($field_value); ?>
                                    </div>
                                    <?php
                                }
                                // Gallery field
                                elseif (isset($field_value[0]) && is_array($field_value[0]) && isset($field_value[0]['url'])) {
                                    ?>
                                    <div class="dcf-slide-gallery">
                                        <?php foreach ($field_value as $image): ?>
                                            <?php if (isset($image['url'])): ?>
                                                <div class="dcf-slide-image">
                                                    <?php echo DCF_Image_Helper::render_image($image); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php
                                }
                                // Video field
                                elseif (isset($field_value['type']) && $field_value['type'] === 'url' && !empty($field_value['url'])) {
                                    ?>
                                    <div class="dcf-slide-video">
                                        <video controls>
                                            <source src="<?php echo esc_url($field_value['url']); ?>"
                                                    <?php if (isset($field_value['mime_type'])) echo 'type="' . esc_attr($field_value['mime_type']) . '"'; ?>>
                                            <?php esc_html_e('Your browser does not support the video tag.', 'elementor-dynamic-content-framework'); ?>
                                        </video>
                                    </div>
                                    <?php
                                }
                            } else {
                                // Text field
                                $class = 'dcf-slide-' . sanitize_html_class($field_name);
                                
                                if (filter_var($field_value, FILTER_VALIDATE_URL)) {
                                    // URL field
                                    ?>
                                    <div class="<?php echo esc_attr($class); ?>">
                                        <a href="<?php echo esc_url($field_value); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html($field_value); ?>
                                        </a>
                                    </div>
                                    <?php
                                } else {
                                    // Regular text
                                    ?>
                                    <div class="<?php echo esc_attr($class); ?>">
                                        <?php echo wp_kses_post(wpautop($field_value)); ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
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
