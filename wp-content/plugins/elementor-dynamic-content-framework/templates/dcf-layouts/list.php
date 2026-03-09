<?php
/**
 * List Layout Template
 *
 * This template can be overridden by copying it to yourtheme/dcf-layouts/list.php
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

<div class="dcf-list-template">
    <?php if (!empty($items)): ?>
        <ul class="dcf-list-items">
            <?php foreach ($items as $item): ?>
                <li class="dcf-list-item-wrapper">
                    <?php
                    // Render item content
                    // This is a placeholder template - actual rendering is done by the layout class
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="dcf-no-items"><?php esc_html_e('No items to display.', 'elementor-dynamic-content-framework'); ?></p>
    <?php endif; ?>
</div>
