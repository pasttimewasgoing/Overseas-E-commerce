<?php
/**
 * Plugin Name: 后台菜单管理器
 * Plugin URI: https://example.com
 * Description: 管理WordPress后台显示的菜单项
 * Version: 1.0.0
 * Author: cyf
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: admin-menu-manager
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('AMM_VERSION', '1.0.0');
define('AMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMM_PLUGIN_URL', plugin_dir_url(__FILE__));

// 加载核心类
require_once AMM_PLUGIN_DIR . 'includes/class-menu-manager.php';
require_once AMM_PLUGIN_DIR . 'includes/class-admin-settings.php';

// 初始化插件
function amm_init() {
    $menu_manager = new AMM_Menu_Manager();
    $admin_settings = new AMM_Admin_Settings();
}
add_action('plugins_loaded', 'amm_init');

// 激活插件时创建默认选项
register_activation_hook(__FILE__, 'amm_activate');
function amm_activate() {
    if (!get_option('amm_hidden_menus')) {
        add_option('amm_hidden_menus', array());
    }
}

// 卸载插件时清理数据
register_deactivation_hook(__FILE__, 'amm_deactivate');
function amm_deactivate() {
    // 可选：保留设置以便重新激活时使用
}
