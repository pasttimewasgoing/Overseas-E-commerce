<?php
/**
 * TechVision Theme Functions
 */

// 主题设置
function techvision_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    register_nav_menus(array(
        'primary' => __('主导航菜单', 'techvision'),
        'footer' => __('页脚菜单', 'techvision'),
    ));
}
add_action('after_setup_theme', 'techvision_setup');

// 加载样式
function techvision_scripts() {
    wp_enqueue_style('techvision-style', get_stylesheet_uri(), array(), '1.0.2');
    
    $js_file = get_template_directory() . '/assets/js/main.js';
    if (file_exists($js_file)) {
        wp_enqueue_script('techvision-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.2', true);
    }
}
add_action('wp_enqueue_scripts', 'techvision_scripts');

// 默认菜单回退
function techvision_default_menu() {
    echo '<a href="' . esc_url(home_url('/')) . '">首页</a>';
}

// 加载模板辅助函数
if (file_exists(get_template_directory() . '/inc/template-functions.php')) {
    require_once get_template_directory() . '/inc/template-functions.php';
}

// 加载主题自定义器
if (file_exists(get_template_directory() . '/inc/customizer.php')) {
    require_once get_template_directory() . '/inc/customizer.php';
}
