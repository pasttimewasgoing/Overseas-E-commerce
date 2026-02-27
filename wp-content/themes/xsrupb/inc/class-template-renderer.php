<?php
/**
 * Template Renderer Class
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XSRUPB_Template_Renderer 类
 * 
 * 负责渲染主题模板和组件
 */
class XSRUPB_Template_Renderer {
    
    /**
     * 渲染导航菜单
     * 
     * @return void
     */
    public function render_navigation(): void {
        if (has_nav_menu('primary')) {
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => 'nav',
                'container_class' => 'main-navigation',
            ));
        }
    }
    
    /**
     * 渲染产品卡片
     * 
     * @param WC_Product $product 产品对象
     * @return string HTML 字符串
     */
    public function render_product_card($product): string {
        if (!$product || !is_a($product, 'WC_Product')) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
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
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染面包屑导航
     * 
     * @return string HTML 字符串
     */
    public function render_breadcrumb(): string {
        if (!function_exists('woocommerce_breadcrumb')) {
            return '';
        }
        
        ob_start();
        woocommerce_breadcrumb(array(
            'delimiter'   => ' / ',
            'wrap_before' => '<nav class="breadcrumb"><div class="container">',
            'wrap_after'  => '</div></nav>',
            'before'      => '',
            'after'       => '',
            'home'        => esc_html__('首页', 'xsrupb'),
        ));
        return ob_get_clean();
    }
    
    /**
     * 加载模板片段
     * 
     * @param string $slug 模板名称
     * @param string $name 模板变体名称
     * @return void
     */
    public function get_template_part(string $slug, string $name = ''): void {
        get_template_part($slug, $name);
    }
}
