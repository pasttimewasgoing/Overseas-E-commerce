<?php
/**
 * WooCommerce Integration Class
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XSRUPB_WooCommerce_Integration 类
 * 
 * 负责自定义 WooCommerce 模板和功能
 */
class XSRUPB_WooCommerce_Integration {
    
    /**
     * 构造函数
     */
    public function __construct() {
        // 声明 WooCommerce 支持
        add_action('after_setup_theme', array($this, 'add_woocommerce_support'));
        
        // 自定义产品循环
        add_action('woocommerce_before_shop_loop_item', array($this, 'customize_product_loop'));
        
        // 修改产品查询
        add_action('pre_get_posts', array($this, 'modify_product_query'));
        
        // 移除 WooCommerce 默认包装器
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        
        // 禁用 WooCommerce 默认样式
        add_filter('woocommerce_enqueue_styles', '__return_empty_array');
    }
    
    /**
     * 添加 WooCommerce 支持
     * 
     * @return void
     */
    public function add_woocommerce_support(): void {
        add_theme_support('woocommerce', array(
            'thumbnail_image_width' => 300,
            'single_image_width'    => 600,
            'product_grid'          => array(
                'default_rows'    => 3,
                'min_rows'        => 2,
                'max_rows'        => 8,
                'default_columns' => 4,
                'min_columns'     => 2,
                'max_columns'     => 5,
            ),
        ));
        
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
    
    /**
     * 自定义产品循环显示
     * 
     * @return void
     */
    public function customize_product_loop(): void {
        // 可以在这里添加自定义钩子
    }
    
    /**
     * 修改产品查询
     * 
     * @param WP_Query $query 查询对象
     * @return void
     */
    public function modify_product_query($query): void {
        // 只在前端产品归档页面修改查询
        if (!is_admin() && $query->is_main_query() && (is_post_type_archive('product') || is_tax('product_cat'))) {
            // 设置每页显示的产品数量
            $query->set('posts_per_page', 12);
        }
    }
    
    /**
     * 获取产品分类（带缓存）
     * 
     * @return array 产品分类数组
     */
    public function get_product_categories(): array {
        $cache_key = 'xsrupb_product_categories';
        $categories = get_transient($cache_key);
        
        if (false === $categories) {
            $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ));
            
            if (is_wp_error($categories)) {
                $categories = array();
            }
            
            // 缓存 1 小时
            set_transient($cache_key, $categories, HOUR_IN_SECONDS);
        }
        
        return $categories;
    }
}
