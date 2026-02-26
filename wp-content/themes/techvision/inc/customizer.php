<?php
/**
 * 主题自定义器设置 - 简化版
 */

function techvision_customize_register($wp_customize) {
    
    // 添加主题设置面板
    $wp_customize->add_panel('techvision_options', array(
        'title' => __('TechVision 主题设置', 'techvision'),
        'priority' => 10,
    ));
    
    // 首页设置部分
    $wp_customize->add_section('techvision_homepage', array(
        'title' => __('首页设置', 'techvision'),
        'panel' => 'techvision_options',
        'priority' => 10,
    ));
    
    // 显示轮播图
    $wp_customize->add_setting('show_carousel', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_carousel', array(
        'label' => __('显示轮播图', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'checkbox',
    ));
    
    // 显示新品上线
    $wp_customize->add_setting('show_new_products', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_new_products', array(
        'label' => __('显示新品上线', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'checkbox',
    ));
    
    // 显示热门推荐
    $wp_customize->add_setting('show_hot_products', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_hot_products', array(
        'label' => __('显示热门推荐', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'checkbox',
    ));
    
    // 显示产品视频
    $wp_customize->add_setting('show_videos', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_videos', array(
        'label' => __('显示产品视频', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'checkbox',
    ));
    
    // 显示更多产品
    $wp_customize->add_setting('show_more_products', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_more_products', array(
        'label' => __('显示更多产品', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'techvision_customize_register');
