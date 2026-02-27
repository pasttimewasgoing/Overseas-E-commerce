<?php
/**
 * Debug Assets Loading
 * 临时调试文件 - 检查资源加载情况
 * 
 * 使用方法：在浏览器中访问产品页面，然后查看页面源代码
 * 搜索 "XSRUPB DEBUG" 查看调试信息
 */

add_action('wp_footer', function() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) {
        return;
    }
    
    echo "\n<!-- XSRUPB DEBUG INFO -->\n";
    echo "<!-- Current Page Type: ";
    
    if (is_shop()) echo "Shop Page";
    if (is_product_category()) echo "Product Category: " . get_queried_object()->name;
    if (is_product_tag()) echo "Product Tag";
    
    echo " -->\n";
    echo "<!-- WooCommerce Active: " . (class_exists('WooCommerce') ? 'Yes' : 'No') . " -->\n";
    echo "<!-- Theme Version: " . (defined('XSRUPB_VERSION') ? XSRUPB_VERSION : 'Not Defined') . " -->\n";
    echo "<!-- Template: " . get_page_template_slug() . " -->\n";
    
    // 检查已加载的样式
    global $wp_styles;
    echo "<!-- Loaded Styles:\n";
    foreach ($wp_styles->queue as $handle) {
        if (strpos($handle, 'xsrupb') !== false || strpos($handle, 'woocommerce') !== false) {
            $style = $wp_styles->registered[$handle];
            echo "  - $handle: " . $style->src . "\n";
        }
    }
    echo "-->\n";
    
    // 检查已加载的脚本
    global $wp_scripts;
    echo "<!-- Loaded Scripts:\n";
    foreach ($wp_scripts->queue as $handle) {
        if (strpos($handle, 'xsrupb') !== false || strpos($handle, 'woocommerce') !== false) {
            $script = $wp_scripts->registered[$handle];
            echo "  - $handle: " . $script->src . "\n";
        }
    }
    echo "-->\n";
    echo "<!-- END XSRUPB DEBUG INFO -->\n\n";
}, 9999);
