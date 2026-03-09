<?php
/**
 * Popup Layout Template
 *
 * This template can be overridden by copying it to yourtheme/dcf-layouts/popup.php
 *
 * @package    Elementor_Dynamic_Content_Framework
 * @subpackage Elementor_Dynamic_Content_Framework/templates/dcf-layouts
 * @version    1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Template variables available:
// $items - Array of content items
// $settings - Layout settings array
?>

<div class="dcf-popup-template">
    <?php if (!empty($items)): ?>
        <div class="dcf-popup-thumbnails-grid">
            <?php foreach ($items as $index => $item): ?>
                <div class="dcf-popup-thumbnail-wrapper" data-index="<?php echo esc_attr($index); ?>">
                    <?php
                    // Render thumbnail
                    // This is a placeholder template - actual rendering is done by the layout class
                    ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dcf-popup-modal-container">
            <?php
            // Modal structure is rendered by the layout class
            ?>
        </div>
    <?php else: ?>
        <p class="dcf-no-items"><?php esc_html_e('No items to display.', 'elementor-dynamic-content-framework'); ?></p>
    <?php endif; ?>
</div>
