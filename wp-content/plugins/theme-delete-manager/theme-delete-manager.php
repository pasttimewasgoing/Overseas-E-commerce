<?php
/**
 * Plugin Name: 主题删除管理器
 * Plugin URI: https://example.com
 * Description: 为WordPress主题页面添加删除功能
 * Version: 1.0.0
 * Author: cyf
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: theme-delete-manager
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('TDM_VERSION', '1.0.0');
define('TDM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TDM_PLUGIN_URL', plugin_dir_url(__FILE__));

// 加载核心类
require_once TDM_PLUGIN_DIR . 'includes/class-theme-delete.php';

// 初始化插件
function tdm_init() {
    new TDM_Theme_Delete();
}
add_action('plugins_loaded', 'tdm_init');
