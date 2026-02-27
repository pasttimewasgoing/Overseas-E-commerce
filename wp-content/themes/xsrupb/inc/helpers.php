<?php
/**
 * Helper Functions
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 获取产品分类（带缓存）
 * 
 * @return array
 */
function xsrupb_get_product_categories() {
    if (!class_exists('WooCommerce')) {
        return array();
    }
    
    $integration = new XSRUPB_WooCommerce_Integration();
    return $integration->get_product_categories();
}

/**
 * 渲染产品卡片
 * 
 * @param WC_Product $product
 * @return string
 */
function xsrupb_render_product_card($product) {
    $renderer = new XSRUPB_Template_Renderer();
    return $renderer->render_product_card($product);
}

/**
 * 渲染面包屑导航
 * 
 * @return string
 */
function xsrupb_render_breadcrumb() {
    $renderer = new XSRUPB_Template_Renderer();
    return $renderer->render_breadcrumb();
}

/**
 * 清理产品搜索输入
 * 
 * @param string $search_term
 * @return string
 */
function xsrupb_sanitize_search_term($search_term) {
    $search_term = wp_strip_all_tags($search_term);
    $search_term = sanitize_text_field($search_term);
    $search_term = substr($search_term, 0, 100);
    return $search_term;
}
