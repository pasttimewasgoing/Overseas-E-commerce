<?php
/**
 * Asset Manager Class
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XSRUPB_Asset_Manager 类
 * 
 * 负责管理主题静态资源（CSS 和 JavaScript）
 */
class XSRUPB_Asset_Manager {
    
    /**
     * 构造函数
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }
    
    /**
     * 加载前端资源
     * 
     * @return void
     */
    public function enqueue_frontend_assets(): void {
        $this->register_styles();
        $this->register_scripts();
    }
    
    /**
     * 注册和加载 CSS 文件
     * 
     * @return void
     */
    public function register_styles(): void {
        // 主样式表
        wp_enqueue_style(
            'xsrupb-main',
            get_template_directory_uri() . '/assets/css/main.css',
            array(),
            XSRUPB_VERSION
        );
        
        // WooCommerce 样式
        if (class_exists('WooCommerce')) {
            // 产品列表页面样式
            if (is_shop() || is_product_category() || is_product_tag()) {
                wp_enqueue_style(
                    'xsrupb-products',
                    get_template_directory_uri() . '/assets/css/products.css',
                    array('xsrupb-main'),
                    XSRUPB_VERSION
                );
            }
            // 产品详情页面样式
            elseif (is_product()) {
                wp_enqueue_style(
                    'xsrupb-product-detail',
                    get_template_directory_uri() . '/assets/css/product-detail.css',
                    array('xsrupb-main'),
                    XSRUPB_VERSION
                );
            }
            // 其他 WooCommerce 页面（购物车、结账等）
            else {
                wp_enqueue_style(
                    'xsrupb-woocommerce',
                    get_template_directory_uri() . '/assets/css/woocommerce.css',
                    array('xsrupb-main'),
                    XSRUPB_VERSION
                );
            }
        }
        
        // 响应式样式
        wp_enqueue_style(
            'xsrupb-responsive',
            get_template_directory_uri() . '/assets/css/responsive.css',
            array('xsrupb-main'),
            XSRUPB_VERSION
        );
    }
    
    /**
     * 注册和加载 JavaScript 文件
     * 
     * @return void
     */
    public function register_scripts(): void {
        // jQuery（WordPress 自带）
        wp_enqueue_script('jquery');
        
        // 主脚本
        wp_enqueue_script(
            'xsrupb-main',
            get_template_directory_uri() . '/assets/js/main.js',
            array('jquery'),
            XSRUPB_VERSION,
            true // 在页脚加载
        );
        
        // 首页专用脚本
        if (is_front_page()) {
            wp_enqueue_script(
                'xsrupb-home',
                get_template_directory_uri() . '/assets/js/home.js',
                array('jquery', 'xsrupb-main'),
                XSRUPB_VERSION,
                true // 在页脚加载
            );
        }
        
        // WooCommerce 相关脚本
        if (class_exists('WooCommerce')) {
            // 购物车脚本
            wp_enqueue_script(
                'xsrupb-cart',
                get_template_directory_uri() . '/assets/js/cart.js',
                array('jquery', 'xsrupb-main'),
                XSRUPB_VERSION,
                true
            );
            
            // 产品列表页面脚本
            if (is_shop() || is_product_category() || is_product_tag()) {
                wp_enqueue_script(
                    'xsrupb-products',
                    get_template_directory_uri() . '/assets/js/products.js',
                    array('jquery', 'xsrupb-main'),
                    XSRUPB_VERSION,
                    true
                );
                
                // 传递 AJAX 数据到前端
                wp_localize_script('xsrupb-products', 'xsrupb_ajax', $this->localize_script_data());
            }
            
            // 产品详情页面脚本
            if (is_product()) {
                wp_enqueue_script(
                    'xsrupb-product-detail',
                    get_template_directory_uri() . '/assets/js/product-detail.js',
                    array('jquery', 'xsrupb-main'),
                    XSRUPB_VERSION,
                    true
                );
                
                // 传递 AJAX 数据到前端
                wp_localize_script('xsrupb-product-detail', 'xsrupb_ajax', $this->localize_script_data());
            }
            
            // 传递 AJAX 数据到购物车脚本
            wp_localize_script('xsrupb-cart', 'xsrupb_ajax', $this->localize_script_data());
        }
        
        // Swiper 轮播图库（从 CDN 加载）
        wp_enqueue_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
            array(),
            '8.0.0',
            true
        );
        
        wp_enqueue_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
            array(),
            '8.0.0'
        );
        
        // 轮播图脚本
        wp_enqueue_script(
            'xsrupb-carousel',
            get_template_directory_uri() . '/assets/js/carousel.js',
            array('jquery', 'swiper'),
            XSRUPB_VERSION,
            true
        );
    }
    
    /**
     * 准备传递给 JavaScript 的数据
     * 
     * @return array
     */
    public function localize_script_data(): array {
        return array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('xsrupb_nonce'),
            'strings'  => array(
                'add_to_cart_success' => esc_html__('产品已添加到购物车', 'xsrupb'),
                'add_to_cart_error'   => esc_html__('添加到购物车失败', 'xsrupb'),
                'loading'             => esc_html__('加载中...', 'xsrupb'),
            ),
        );
    }
}
