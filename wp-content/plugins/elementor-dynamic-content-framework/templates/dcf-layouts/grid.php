<?php
/**
 * Grid Layout Template
 *
 * This template can be overridden by copying it to yourtheme/dcf-layouts/grid.php
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/templates/dcf-layouts
 * @var array $items    Content items array
 * @var array $settings Layout settings
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
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
?>

<style>
    .<?php echo esc_attr($grid_id); ?> .dcf-grid {
        display: grid;
        grid-template-columns: repeat(<?php echo $columns_desktop; ?>, 1fr);
        gap: <?php echo $gap; ?>px;
    }

    @media (max-width: 1024px) {
        .<?php echo esc_attr($grid_id); ?> .dcf-grid {
            grid-template-columns: repeat(<?php echo $columns_tablet; ?>, 1fr);
        }
    }

    @media (max-width: 768px) {
        .<?php echo esc_attr($grid_id); ?> .dcf-grid {
            grid-template-columns: repeat(<?php echo $columns_mobile; ?>, 1fr);
        }
    }
</style>

<div class="dcf-grid-wrapper <?php echo esc_attr($grid_id); ?>">
    <div class="dcf-grid">
        <?php foreach ($items as $item): ?>
            <?php
            $data = isset($item['data']) ? $item['data'] : [];
            if (empty($data)) {
                continue;
            }
            ?>
            <div class="dcf-grid-item">
                <div class="dcf-grid-item-content">
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
                                <div class="dcf-grid-item-image">
                                    <?php echo DCF_Image_Helper::render_image($field_value); ?>
                                </div>
                                <?php
                            }
                            // Gallery field
                            elseif (isset($field_value[0]) && is_array($field_value[0]) && isset($field_value[0]['url'])) {
                                ?>
                                <div class="dcf-grid-item-gallery">
                                    <?php foreach ($field_value as $image): ?>
                                        <?php if (isset($image['url'])): ?>
                                            <div class="dcf-grid-item-image">
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
                                <div class="dcf-grid-item-video">
                                    <video controls>
                                        <source src="<?php echo esc_url($field_value['url']); ?>"
                                                <?php if (isset($field_value['mime_type'])) echo 'type="' . esc_attr($field_value['mime_type']) . '"'; ?>>
                                        <?php esc_html_e('Your browser does not support the video tag.', 'elementor-dynamic-content-framework'); ?>
                                    </video>
                                </div>
                                <?php
                            }
                            // Icon field
                            elseif (isset($field_value['library']) && isset($field_value['value'])) {
                                $library = esc_attr($field_value['library']);
                                $icon_value = esc_attr($field_value['value']);
                                ?>
                                <div class="dcf-grid-item-icon">
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
                            $class = 'dcf-grid-item-' . sanitize_html_class($field_name);
                            
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
</div>
