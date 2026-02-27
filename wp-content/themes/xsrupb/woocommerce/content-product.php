<?php
/**
 * The template for displaying product content within loops
 *
 * @package XSRUPB
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

// 确保产品对象有效
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<div <?php wc_product_class('product-card', $product); ?> data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <div class="product-img">
        <a href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php echo wp_kses_post($product->get_image('medium')); ?>
        </a>
        
        <?php if ($product->is_on_sale()) : ?>
            <span class="badge badge-sale"><?php esc_html_e('SALE', 'xsrupb'); ?></span>
        <?php elseif ($product->is_featured()) : ?>
            <span class="badge badge-hot"><?php esc_html_e('HOT', 'xsrupb'); ?></span>
        <?php endif; ?>
    </div>
    
    <h3 class="product-title">
        <a href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php echo esc_html($product->get_name()); ?>
        </a>
    </h3>
    
    <p class="product-desc">
        <?php echo wp_kses_post(wp_trim_words($product->get_short_description(), 10)); ?>
    </p>
    
    <div class="product-footer">
        <p class="product-price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
        
        <button class="buy-btn add-to-cart-btn" 
                data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                <?php echo $product->is_in_stock() ? '' : 'disabled'; ?>>
            <?php echo $product->is_in_stock() ? esc_html__('购买', 'xsrupb') : esc_html__('缺货', 'xsrupb'); ?>
        </button>
    </div>
</div>
