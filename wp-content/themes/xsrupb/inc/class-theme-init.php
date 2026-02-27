<?php
/**
 * Theme Initializer Class
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XSRUPB_Theme_Init 类
 * 
 * 负责初始化主题功能、注册钩子、设置主题支持
 */
class XSRUPB_Theme_Init {
    
    /**
     * 构造函数
     */
    public function __construct() {
        add_action('after_setup_theme', array($this, 'setup_theme'));
        add_action('widgets_init', array($this, 'register_sidebars'));
    }
    
    /**
     * 设置主题功能
     * 
     * 注册主题支持功能、导航菜单、图片尺寸等
     * 
     * @return void
     */
    public function setup_theme(): void {
        // 加载主题文本域
        load_theme_textdomain('xsrupb', get_template_directory() . '/languages');
        
        // 添加主题支持功能
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ));
        
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ));
        
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('responsive-embeds');
        
        // 注册导航菜单
        $this->register_menus();
        
        // 设置内容宽度
        if (!isset($GLOBALS['content_width'])) {
            $GLOBALS['content_width'] = 1200;
        }
        
        // 添加图片尺寸
        add_image_size('xsrupb-product-thumb', 300, 300, true);
        add_image_size('xsrupb-product-medium', 600, 600, true);
        add_image_size('xsrupb-product-large', 1200, 1200, true);
        add_image_size('xsrupb-carousel', 1920, 800, true);
    }
    
    /**
     * 注册导航菜单位置
     * 
     * @return void
     */
    public function register_menus(): void {
        register_nav_menus(array(
            'primary' => esc_html__('主导航菜单', 'xsrupb'),
            'footer'  => esc_html__('页脚菜单', 'xsrupb'),
        ));
    }
    
    /**
     * 注册小工具区域
     * 
     * @return void
     */
    public function register_sidebars(): void {
        // 侧边栏小工具区域
        register_sidebar(array(
            'name'          => esc_html__('侧边栏', 'xsrupb'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('主侧边栏小工具区域', 'xsrupb'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
        
        // 页脚小工具区域 1
        register_sidebar(array(
            'name'          => esc_html__('页脚区域 1', 'xsrupb'),
            'id'            => 'footer-1',
            'description'   => esc_html__('页脚第一个小工具区域', 'xsrupb'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
        
        // 页脚小工具区域 2
        register_sidebar(array(
            'name'          => esc_html__('页脚区域 2', 'xsrupb'),
            'id'            => 'footer-2',
            'description'   => esc_html__('页脚第二个小工具区域', 'xsrupb'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
        
        // 页脚小工具区域 3
        register_sidebar(array(
            'name'          => esc_html__('页脚区域 3', 'xsrupb'),
            'id'            => 'footer-3',
            'description'   => esc_html__('页脚第三个小工具区域', 'xsrupb'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
}
