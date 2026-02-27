<?php
/**
 * XSRUPB Theme Functions
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义主题常量
define('XSRUPB_VERSION', '1.0.0');
define('XSRUPB_THEME_DIR', get_template_directory());
define('XSRUPB_THEME_URI', get_template_directory_uri());

// 加载主题类文件
require_once XSRUPB_THEME_DIR . '/inc/class-theme-init.php';
require_once XSRUPB_THEME_DIR . '/inc/class-asset-manager.php';
require_once XSRUPB_THEME_DIR . '/inc/class-woocommerce-integration.php';
require_once XSRUPB_THEME_DIR . '/inc/class-template-renderer.php';
require_once XSRUPB_THEME_DIR . '/inc/class-ajax-handler.php';
require_once XSRUPB_THEME_DIR . '/inc/class-nav-walker.php';
require_once XSRUPB_THEME_DIR . '/inc/helpers.php';

// 临时调试文件 - 完成后可删除
if (file_exists(XSRUPB_THEME_DIR . '/debug-assets.php')) {
    require_once XSRUPB_THEME_DIR . '/debug-assets.php';
}

// 初始化主题
function xsrupb_init_theme() {
    // 初始化主题设置
    $theme_init = new XSRUPB_Theme_Init();
    
    // 初始化资源管理器
    $asset_manager = new XSRUPB_Asset_Manager();
    
    // 初始化 WooCommerce 集成
    if (class_exists('WooCommerce')) {
        $woocommerce_integration = new XSRUPB_WooCommerce_Integration();
    }
    
    // 初始化 AJAX 处理器
    $ajax_handler = new XSRUPB_AJAX_Handler();
}
add_action('after_setup_theme', 'xsrupb_init_theme');
