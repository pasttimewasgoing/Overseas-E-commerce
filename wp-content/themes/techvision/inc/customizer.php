<?php
/**

 * 主题自定义器设置


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
<<<<<<< HEAD
    
    // 新品上线标题
    $wp_customize->add_setting('new_products_title', array(
        'default' => __('新品上线', 'techvision'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('new_products_title', array(
        'label' => __('新品上线标题', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'text',
    ));
    
    // 新品上线数量
    $wp_customize->add_setting('new_products_count', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('new_products_count', array(
        'label' => __('新品上线显示数量', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 20,
            'step' => 1,
        ),
    ));
    
    // 热门推荐标题
    $wp_customize->add_setting('hot_products_title', array(
        'default' => __('热门推荐', 'techvision'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hot_products_title', array(
        'label' => __('热门推荐标题', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'text',
    ));
    
    // 热门推荐数量
    $wp_customize->add_setting('hot_products_count', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('hot_products_count', array(
        'label' => __('热门推荐每个分类显示数量', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 20,
            'step' => 1,
        ),
    ));
    
    // 更多产品标题
    $wp_customize->add_setting('more_products_title', array(
        'default' => __('更多产品', 'techvision'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('more_products_title', array(
        'label' => __('更多产品标题', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'text',
    ));
    
    // 更多产品数量
    $wp_customize->add_setting('more_products_count', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('more_products_count', array(
        'label' => __('更多产品显示数量', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 20,
            'step' => 1,
        ),
    ));
    
    // 视频区域标题
    $wp_customize->add_setting('video_section_title', array(
        'default' => __('产品视频', 'techvision'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('video_section_title', array(
        'label' => __('产品视频标题', 'techvision'),
        'section' => 'techvision_homepage',
        'type' => 'text',
    ));
    
    // 颜色设置部分
    $wp_customize->add_section('techvision_colors', array(
        'title' => __('颜色设置', 'techvision'),
        'panel' => 'techvision_options',
        'priority' => 20,
    ));
    
    // 主色调
    $wp_customize->add_setting('primary_color', array(
        'default' => '#2563eb',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('主色调', 'techvision'),
        'section' => 'techvision_colors',
    )));
    
    // 联系信息部分
    $wp_customize->add_section('techvision_contact', array(
        'title' => __('联系信息', 'techvision'),
        'panel' => 'techvision_options',
        'priority' => 30,
    ));
    
    // 电话
    $wp_customize->add_setting('contact_phone', array(
        'default' => '400-123-4567',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label' => __('联系电话', 'techvision'),
        'section' => 'techvision_contact',
        'type' => 'text',
    ));
    
    // 邮箱
    $wp_customize->add_setting('contact_email', array(
        'default' => 'info@example.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label' => __('联系邮箱', 'techvision'),
        'section' => 'techvision_contact',
        'type' => 'email',
    ));
    
    // 地址
    $wp_customize->add_setting('contact_address', array(
        'default' => '中国上海市浦东新区',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_address', array(
        'label' => __('联系地址', 'techvision'),
        'section' => 'techvision_contact',
        'type' => 'text',
    ));
    
    // 社交媒体部分
    $wp_customize->add_section('techvision_social', array(
        'title' => __('社交媒体', 'techvision'),
        'panel' => 'techvision_options',
        'priority' => 40,
    ));
    
    // 微信
    $wp_customize->add_setting('social_wechat', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('social_wechat', array(
        'label' => __('微信链接', 'techvision'),
        'section' => 'techvision_social',
        'type' => 'url',
    ));
    
    // 微博
    $wp_customize->add_setting('social_weibo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('social_weibo', array(
        'label' => __('微博链接', 'techvision'),
        'section' => 'techvision_social',
        'type' => 'url',
    ));
    
    // 抖音
    $wp_customize->add_setting('social_douyin', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('social_douyin', array(
        'label' => __('抖音链接', 'techvision'),
        'section' => 'techvision_social',
        'type' => 'url',
    ));
}
add_action('customize_register', 'techvision_customize_register');

// 输出自定义CSS
function techvision_customizer_css() {
    $primary_color = get_theme_mod('primary_color', '#2563eb');
    ?>
    <style type="text/css">
        .logo a,
        .nav-link:hover,
        .product-price,
        .buy-btn,
        .tab-btn.active {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .buy-btn,
        .tab-btn.active {
            background-color: <?php echo esc_attr($primary_color); ?>;
        }
        
        .buy-btn:hover {
            background-color: <?php echo esc_attr(adjustBrightness($primary_color, -20)); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'techvision_customizer_css');

// 调整颜色亮度的辅助函数
function adjustBrightness($hex, $steps) {
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
=======
}
add_action('customize_register', 'techvision_customize_register');
>>>>>>> 45062407 (init wordpress project)
