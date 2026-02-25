<?php
/**
 * TechVision Theme Functions
 */

// 主题设置
function techvision_setup() {
    // 添加主题支持
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('woocommerce');
    
    // 注册导航菜单
    register_nav_menus(array(
        'primary' => __('主导航菜单', 'techvision'),
        'footer' => __('页脚菜单', 'techvision'),
    ));
    
    // 设置内容宽度
    $GLOBALS['content_width'] = 1200;
}
add_action('after_setup_theme', 'techvision_setup');

// 加载样式和脚本
function techvision_scripts() {
    // 主题样式
    wp_enqueue_style('techvision-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // 自定义脚本
    wp_enqueue_script('techvision-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);
    
    // 本地化脚本
    wp_localize_script('techvision-script', 'techvisionData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('techvision-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'techvision_scripts');

// 注册侧边栏
function techvision_widgets_init() {
    register_sidebar(array(
        'name' => __('侧边栏', 'techvision'),
        'id' => 'sidebar-1',
        'description' => __('主侧边栏区域', 'techvision'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('页脚区域1', 'techvision'),
        'id' => 'footer-1',
        'description' => __('页脚第一列', 'techvision'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
    
    register_sidebar(array(
        'name' => __('页脚区域2', 'techvision'),
        'id' => 'footer-2',
        'description' => __('页脚第二列', 'techvision'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
    
    register_sidebar(array(
        'name' => __('页脚区域3', 'techvision'),
        'id' => 'footer-3',
        'description' => __('页脚第三列', 'techvision'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
    
    register_sidebar(array(
        'name' => __('页脚区域4', 'techvision'),
        'id' => 'footer-4',
        'description' => __('页脚第四列', 'techvision'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'techvision_widgets_init');

// 自定义文章类型 - 产品
function techvision_register_product_post_type() {
    $labels = array(
        'name' => '产品',
        'singular_name' => '产品',
        'add_new' => '添加新产品',
        'add_new_item' => '添加新产品',
        'edit_item' => '编辑产品',
        'new_item' => '新产品',
        'view_item' => '查看产品',
        'search_items' => '搜索产品',
        'not_found' => '未找到产品',
        'not_found_in_trash' => '回收站中未找到产品'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-products',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite' => array('slug' => 'products'),
    );
    
    register_post_type('product', $args);
}
add_action('init', 'techvision_register_product_post_type');

// 自定义分类 - 产品分类
function techvision_register_product_taxonomy() {
    $labels = array(
        'name' => '产品分类',
        'singular_name' => '产品分类',
        'search_items' => '搜索分类',
        'all_items' => '所有分类',
        'parent_item' => '父级分类',
        'parent_item_colon' => '父级分类:',
        'edit_item' => '编辑分类',
        'update_item' => '更新分类',
        'add_new_item' => '添加新分类',
        'new_item_name' => '新分类名称',
        'menu_name' => '产品分类',
    );
    
    register_taxonomy('product_category', array('product'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product-category'),
    ));
}
add_action('init', 'techvision_register_product_taxonomy');

// 添加产品标签支持
function techvision_register_product_tags() {
    register_taxonomy('product_tag', 'product', array(
        'label' => '产品标签',
        'rewrite' => array('slug' => 'product-tag'),
        'hierarchical' => false,
    ));
}
add_action('init', 'techvision_register_product_tags');

// 限制摘要长度
function techvision_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'techvision_excerpt_length');

// 自定义摘要结尾
function techvision_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'techvision_excerpt_more');

// 加载主题自定义器
require get_template_directory() . '/inc/customizer.php';

// 加载模板辅助函数
require get_template_directory() . '/inc/template-functions.php';
