<?php
/**
 * AJAX Handler Class
 *
 * @package XSRUPB
 * @since 1.0.0
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * XSRUPB_AJAX_Handler 类
 * 
 * 负责处理前端 AJAX 请求
 */
class XSRUPB_AJAX_Handler {
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->register_ajax_actions();
    }
    
    /**
     * 注册 AJAX 动作钩子
     * 
     * @return void
     */
    public function register_ajax_actions(): void {
        // 添加到购物车
        add_action('wp_ajax_add_to_cart', array($this, 'handle_add_to_cart'));
        add_action('wp_ajax_nopriv_add_to_cart', array($this, 'handle_add_to_cart'));
        
        // 旧版本兼容
        add_action('wp_ajax_xsrupb_add_to_cart', array($this, 'handle_add_to_cart'));
        add_action('wp_ajax_nopriv_xsrupb_add_to_cart', array($this, 'handle_add_to_cart'));
        
        // 更新购物车
        add_action('wp_ajax_xsrupb_update_cart', array($this, 'handle_update_cart'));
        add_action('wp_ajax_nopriv_xsrupb_update_cart', array($this, 'handle_update_cart'));
        
        // 产品搜索
        add_action('wp_ajax_xsrupb_product_search', array($this, 'handle_product_search'));
        add_action('wp_ajax_nopriv_xsrupb_product_search', array($this, 'handle_product_search'));
    }
    
    /**
     * 处理添加到购物车请求
     * 
     * @return void
     */
    public function handle_add_to_cart(): void {
        // 验证 nonce
        if (!check_ajax_referer('xsrupb_nonce', 'nonce', false)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('安全验证失败', 'xsrupb')
            ));
            return;
        }
        
        // 获取并验证数据
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
        $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
        
        // 验证购物车项目数据
        $cart_item_data = array(
            'product_id' => $product_id,
            'quantity' => $quantity,
            'variation_id' => $variation_id,
        );
        
        if (!$this->validate_cart_item($cart_item_data)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('无效的产品数据', 'xsrupb')
            ));
            return;
        }
        
        // 添加到购物车
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
        
        if ($cart_item_key) {
            $this->send_json_response(true, array(
                'message' => esc_html__('产品已添加到购物车', 'xsrupb'),
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
            ));
        } else {
            $this->send_json_response(false, array(
                'message' => esc_html__('添加到购物车失败', 'xsrupb')
            ));
        }
    }
    
    /**
     * 处理更新购物车请求
     * 
     * @return void
     */
    public function handle_update_cart(): void {
        // 验证 nonce
        if (!check_ajax_referer('xsrupb_nonce', 'nonce', false)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('安全验证失败', 'xsrupb')
            ));
            return;
        }
        
        // 获取购物车数据
        $cart_data = isset($_POST['cart']) ? $_POST['cart'] : array();
        
        if (empty($cart_data)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('购物车数据为空', 'xsrupb')
            ));
            return;
        }
        
        // 更新购物车
        foreach ($cart_data as $cart_item_key => $values) {
            $quantity = absint($values['qty']);
            
            if ($quantity > 0) {
                WC()->cart->set_quantity($cart_item_key, $quantity);
            } else {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }
        
        $this->send_json_response(true, array(
            'message' => esc_html__('购物车已更新', 'xsrupb'),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total(),
        ));
    }
    
    /**
     * 处理产品搜索请求
     * 
     * @return void
     */
    public function handle_product_search(): void {
        // 验证 nonce
        if (!check_ajax_referer('xsrupb_nonce', 'nonce', false)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('安全验证失败', 'xsrupb')
            ));
            return;
        }
        
        // 获取并清理搜索关键词
        $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $search_term = wp_strip_all_tags($search_term);
        $search_term = substr($search_term, 0, 100);
        
        if (empty($search_term)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('请输入搜索关键词', 'xsrupb')
            ));
            return;
        }
        
        // 搜索产品
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            's' => $search_term,
            'post_status' => 'publish',
        );
        
        $query = new WP_Query($args);
        $products = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                
                if ($product && $product->is_visible()) {
                    $products[] = array(
                        'id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'price' => $product->get_price_html(),
                        'image' => wp_get_attachment_url($product->get_image_id()),
                        'url' => get_permalink($product->get_id()),
                    );
                }
            }
            wp_reset_postdata();
        }
        
        if (empty($products)) {
            $this->send_json_response(false, array(
                'message' => esc_html__('未找到产品', 'xsrupb')
            ));
        } else {
            $this->send_json_response(true, array(
                'products' => $products
            ));
        }
    }
    
    /**
     * 验证购物车项目数据
     * 
     * @param array $cart_item_data 购物车项目数据
     * @return bool
     */
    private function validate_cart_item(array $cart_item_data): bool {
        // 检查必需字段
        if (!isset($cart_item_data['product_id']) || !isset($cart_item_data['quantity'])) {
            return false;
        }
        
        $product_id = $cart_item_data['product_id'];
        $quantity = $cart_item_data['quantity'];
        
        // 验证产品 ID
        if ($product_id <= 0) {
            return false;
        }
        
        $product = wc_get_product($product_id);
        
        if (!$product || !$product->exists()) {
            return false;
        }
        
        // 验证数量
        if ($quantity <= 0) {
            return false;
        }
        
        // 检查库存
        if (!$product->is_in_stock()) {
            return false;
        }
        
        if ($product->managing_stock()) {
            $stock_quantity = $product->get_stock_quantity();
            
            if ($quantity > $stock_quantity) {
                return false;
            }
        }
        
        // 验证变体
        if (isset($cart_item_data['variation_id']) && $cart_item_data['variation_id'] > 0) {
            $variation = wc_get_product($cart_item_data['variation_id']);
            
            if (!$variation || !$variation->is_type('variation')) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 发送 JSON 响应
     * 
     * @param bool $success 是否成功
     * @param mixed $data 响应数据
     * @return void
     */
    public function send_json_response(bool $success, $data): void {
        if ($success) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error($data);
        }
    }
}
