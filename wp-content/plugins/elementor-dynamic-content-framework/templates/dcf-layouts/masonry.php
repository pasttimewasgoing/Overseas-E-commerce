<?php
/**
 * Masonry Layout Template
 *
 * This template can be overridden by copying it to yourtheme/dcf-layouts/masonry.php
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/templates/dcf-layouts
 * @version    1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Template variables
$items = isset($items) ? $items : [];
$settings = isset($settings) ? $settings : [];

if (empty($items)) {
    return;
}

// Parse settings
$columns = isset($settings['columns']) ? absint($settings['columns']) : 3;
$gap = isset($settings['gap']) ? absint($settings['gap']) : 20;

// Validate ranges
$columns = max(2, min(6, $columns));
$gap = max(0, min(100, $gap));

// Generate unique ID
$masonry_id = 'dcf-masonry-' . uniqid();
?>

<div class="dcf-masonry-layout <?php echo esc_attr($masonry_id); ?>" data-columns="<?php echo esc_attr($columns); ?>" data-gap="<?php echo esc_attr($gap); ?>">
    <style>
        .<?php echo esc_attr($masonry_id); ?> .dcf-masonry-grid {
            column-count: <?php echo esc_attr($columns); ?>;
            column-gap: <?php echo esc_attr($gap); ?>px;
        }
        
        .<?php echo esc_attr($masonry_id); ?> .dcf-masonry-item {
            break-inside: avoid;
            margin-bottom: <?php echo esc_attr($gap); ?>px;
            display: inline-block;
            width: 100%;
        }
        
        /* Responsive styles */
        @media (max-width: 1024px) {
            .<?php echo esc_attr($masonry_id); ?> .dcf-masonry-grid {
                column-count: <?php echo esc_attr(max(2, $columns - 1)); ?>;
            }
        }
        
        @media (max-width: 768px) {
            .<?php echo esc_attr($masonry_id); ?> .dcf-masonry-grid {
                column-count: <?php echo esc_attr(max(1, $columns - 2)); ?>;
            }
        }
    </style>
    
    <div class="dcf-masonry-grid">
        <?php foreach ($items as $item): ?>
            <?php
            $data = isset($item['data']) ? $item['data'] : [];
            if (empty($data)) {
                continue;
            }
            ?>
            
            <div class="dcf-masonry-item">
                <div class="dcf-masonry-item-inner">
                    <?php
                    // Render each field in the item
                    foreach ($data as $field_name => $field_value) {
                        if (empty($field_value)) {
                            continue;
                        }
                        
                        // Render field based on type
                        if (is_array($field_value)) {
                            // Image field
                            if (isset($field_value['url']) && isset($field_value['id'])) {
                                ?>
                                <div class="dcf-masonry-image">
                                    <?php echo DCF_Image_Helper::render_image($field_value); ?>
                                </div>
                                <?php
                            }
                            // Gallery field
                            elseif (isset($field_value[0]) && is_array($field_value[0]) && isset($field_value[0]['url'])) {
                                ?>
                                <div class="dcf-masonry-gallery">
                                    <?php foreach ($field_value as $image): ?>
                                        <?php if (isset($image['url'])): ?>
                                            <?php echo DCF_Image_Helper::render_image($image); ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                            }
                            // Icon field
                            elseif (isset($field_value['library']) && isset($field_value['value'])) {
                                $library = esc_attr($field_value['library']);
                                $icon_value = esc_attr($field_value['value']);
                                ?>
                                <div class="dcf-masonry-icon">
                                    <?php if ($library === 'fontawesome'): ?>
                                        <i class="<?php echo $icon_value; ?>"></i>
                                    <?php else: ?>
                                        <span class="dcf-icon <?php echo $icon_value; ?>"></span>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        } else {
                            // Text field
                            $class = 'dcf-masonry-' . sanitize_html_class($field_name);
                            ?>
                            <div class="<?php echo esc_attr($class); ?>">
                                <?php
                                // Check if it's a URL
                                if (filter_var($field_value, FILTER_VALIDATE_URL)) {
                                    ?>
                                    <a href="<?php echo esc_url($field_value); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($field_value); ?>
                                    </a>
                                    <?php
                                } else {
                                    echo wp_kses_post(wpautop($field_value));
                                }
                                ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
