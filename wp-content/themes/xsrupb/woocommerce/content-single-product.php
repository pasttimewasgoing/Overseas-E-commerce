<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * @package XSRUPB
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product
 */
do_action('woocommerce_before_single_product');

if (!$product) {
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('product-layout', $product); ?>>
    
    <!-- 左侧：产品图片 -->
    <div class="product-gallery">
        <?php
        /**
         * Hook: woocommerce_before_single_product_summary
         */
        do_action('woocommerce_before_single_product_summary');
        ?>
        
        <div class="main-image">
            <?php
            if (has_post_thumbnail()) {
                echo get_the_post_thumbnail($product->get_id(), 'large', array('class' => 'product-main-image'));
            } else {
                echo wc_placeholder_img('large');
            }
            ?>
            
        <?php if ($product->is_on_sale()) : ?>
                <div class="image-badge sale-badge"><?php esc_html_e('促销', 'xsrupb'); ?></div>
            <?php elseif ($product->is_featured()) : ?>
                <div class="image-badge new-badge"><?php esc_html_e('新品', 'xsrupb'); ?></div>
            <?php endif; ?>
        </div>
        
        <?php
        $attachment_ids = $product->get_gallery_image_ids();
        if ($attachment_ids) :
        ?>
        <div class="thumbnail-list">
            <div class="thumbnail active">
                <?php echo get_the_post_thumbnail($product->get_id(), 'thumbnail'); ?>
            </div>
            <?php
            foreach ($attachment_ids as $attachment_id) :
                echo '<div class="thumbnail">';
                echo wp_get_attachment_image($attachment_id, 'thumbnail');
                echo '</div>';
            endforeach;
            ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- 右侧：产品信息 -->
    <div class="product-info">
        <h1 class="product-title"><?php the_title(); ?></h1>
        
        <?php if ($product->get_sku()) : ?>
            <p class="product-code">
                <?php esc_html_e('产品编号:', 'xsrupb'); ?> 
                <span><?php echo esc_html($product->get_sku()); ?></span>
            </p>
        <?php endif; ?>
        
        <?php if (wc_review_ratings_enabled()) : ?>
            <div class="product-rating">
                <?php woocommerce_template_loop_rating(); ?>
                <span class="rating-text">
                    (<?php echo esc_html($product->get_review_count()); ?> <?php esc_html_e('评价', 'xsrupb'); ?>)
                </span>
            </div>
        <?php endif; ?>

        <div class="price-section">
            <span class="current-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            
            <?php if ($product->is_on_sale() && $product->get_regular_price()) : ?>
                <span class="discount-badge">
                    <?php
                    $regular_price = (float) $product->get_regular_price();
                    $sale_price = (float) $product->get_sale_price();
                    if ($regular_price > 0) {
                        $discount = round((($regular_price - $sale_price) / $regular_price) * 100);
                        echo '-' . $discount . '%';
                    }
                    ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($product->get_short_description()) : ?>
            <div class="product-description">
                <?php echo wp_kses_post($product->get_short_description()); ?>
            </div>
        <?php endif; ?>

        <!-- 产品选项和购买表单 -->
        <div class="product-options">
            <?php
            /**
             * Hook: woocommerce_single_product_summary
             * 
             * @hooked woocommerce_template_single_add_to_cart - 30
             */
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            do_action('woocommerce_single_product_summary');
            ?>
            
            <!-- 自定义购买区域 -->
            <div class="custom-purchase-area">
                <?php if ($product->is_type('simple')) : ?>
                    <div class="option-group">
                        <label><?php esc_html_e('数量', 'xsrupb'); ?></label>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn minus">−</button>
                            <input type="number" class="qty-input" value="1" min="1" max="<?php echo esc_attr($product->get_stock_quantity() ?: 999); ?>">
                            <button type="button" class="qty-btn plus">+</button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- 购买按钮 -->
                <div class="action-buttons">
                    <button class="add-to-cart-btn single_add_to_cart_button" 
                            data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                            <?php echo $product->is_in_stock() ? '' : 'disabled'; ?>>
                        <?php echo $product->is_in_stock() ? esc_html__('加入购物车', 'xsrupb') : esc_html__('缺货', 'xsrupb'); ?>
                    </button>
                    <button class="buy-now-btn" 
                            data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                            <?php echo $product->is_in_stock() ? '' : 'disabled'; ?>>
                        <?php esc_html_e('立即购买', 'xsrupb'); ?>
                    </button>
                    <button class="wishlist-btn" title="<?php esc_attr_e('添加到收藏', 'xsrupb'); ?>">❤</button>
                </div>
            </div>
        </div>

        <!-- 产品特点 -->
        <div class="product-features">
            <div class="feature-item">
                <span class="icon">✓</span>
                <span><?php esc_html_e('全球免费配送', 'xsrupb'); ?></span>
            </div>
            <div class="feature-item">
                <span class="icon">✓</span>
                <span><?php esc_html_e('30天退换保证', 'xsrupb'); ?></span>
            </div>
            <div class="feature-item">
                <span class="icon">✓</span>
                <span><?php esc_html_e('1年质保服务', 'xsrupb'); ?></span>
            </div>
            <div class="feature-item">
                <span class="icon">✓</span>
                <span><?php esc_html_e('安全支付保障', 'xsrupb'); ?></span>
            </div>
        </div>
    </div>
    
</div>

<?php
/**
 * Hook: woocommerce_after_single_product
 */
do_action('woocommerce_after_single_product');
