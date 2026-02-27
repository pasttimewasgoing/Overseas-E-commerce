/**
 * Cart JavaScript
 *
 * @package XSRUPB
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * 购物车管理对象
     */
    var XSRUPBCart = {
        
        /**
         * 初始化
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * 绑定事件
         */
        bindEvents: function() {
            // 添加到购物车按钮点击事件
            $(document).on('click', '.add-to-cart-btn', this.handleAddToCart.bind(this));
        },
        
        /**
         * 处理添加到购物车
         */
        handleAddToCart: function(e) {
            e.preventDefault();
            
            var $button = $(e.currentTarget);
            var productId = $button.data('product-id');
            var $productCard = $button.closest('.product-card');
            var quantity = $productCard.find('.qty-input').val() || 1;
            
            // 禁用按钮
            $button.prop('disabled', true).text(xsrupb_ajax.strings.loading);
            
            // 发送 AJAX 请求
            this.addToCart(productId, quantity, $button);
        },
        
        /**
         * 添加到购物车 AJAX 请求
         */
        addToCart: function(productId, quantity, $button, retryCount) {
            var self = this;
            retryCount = retryCount || 0;
            
            $.ajax({
                url: xsrupb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'xsrupb_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: xsrupb_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 更新购物车图标
                        $('.cart-badge').text(response.data.cart_count);
                        
                        // 显示成功消息
                        self.showMessage(xsrupb_ajax.strings.add_to_cart_success, 'success');
                        
                        // 恢复按钮
                        $button.prop('disabled', false).text('购买');
                    } else {
                        self.handleError(response.data.message, $button, productId, quantity, retryCount);
                    }
                },
                error: function(xhr, status, error) {
                    // 自动重试机制（最多 3 次）
                    if (retryCount < 3) {
                        setTimeout(function() {
                            self.addToCart(productId, quantity, $button, retryCount + 1);
                        }, 1000 * (retryCount + 1));
                    } else {
                        self.handleError(xsrupb_ajax.strings.add_to_cart_error, $button);
                    }
                }
            });
        },
        
        /**
         * 处理错误
         */
        handleError: function(message, $button, productId, quantity, retryCount) {
            this.showMessage(message, 'error');
            
            // 恢复按钮
            if ($button) {
                $button.prop('disabled', false).text('购买');
            }
        },
        
        /**
         * 显示消息
         */
        showMessage: function(message, type) {
            // 简单的 alert，可以替换为更好的通知系统
            alert(message);
        }
    };
    
    // 文档就绪时初始化
    $(document).ready(function() {
        XSRUPBCart.init();
    });
    
})(jQuery);
