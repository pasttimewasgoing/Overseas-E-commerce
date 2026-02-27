/**
 * 产品页面交互功能
 */

(function($) {
    'use strict';

    // 页面加载完成后初始化
    $(document).ready(function() {
        
        // 分类栏折叠/展开功能
        $('.category-title').on('click', function(e) {
            const $categoryTitle = $(this);
            const $categoryGroup = $categoryTitle.closest('.category-group');
            const $categoryList = $categoryGroup.find('.category-list');
            const hasChildren = $categoryTitle.data('has-children') === 'yes';
            
            // 如果点击的是链接，让它正常跳转
            if ($(e.target).is('a')) {
                return true;
            }
            
            // 如果有子分类，切换显示/隐藏
            if (hasChildren && $categoryList.length) {
                e.preventDefault();
                $categoryList.slideToggle(300);
                $categoryTitle.toggleClass('active');
            }
        });
        
        // AJAX 添加到购物车
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            
            if (!productId || $button.prop('disabled')) {
                return;
            }
            
            // 禁用按钮并显示加载状态
            $button.prop('disabled', true);
            const originalText = $button.text();
            $button.text('添加中...');
            
            // 发送 AJAX 请求
            $.ajax({
                url: xsrupb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                    quantity: 1,
                    nonce: xsrupb_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 更新购物车数量
                        updateCartCount(response.data.cart_count);
                        
                        // 显示成功消息
                        showNotification('商品已添加到购物车！', 'success');
                        
                        // 触发 WooCommerce 事件
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                    } else {
                        showNotification(response.data.message || '添加失败，请重试', 'error');
                    }
                },
                error: function() {
                    showNotification('网络错误，请重试', 'error');
                },
                complete: function() {
                    // 恢复按钮状态
                    $button.prop('disabled', false);
                    $button.text(originalText);
                }
            });
        });
        
        // 平滑滚动到产品区域
        $('a[href="#products"]').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#products').offset().top - 100
            }, 800);
        });
        
    });
    
    /**
     * 更新购物车数量显示
     */
    function updateCartCount(count) {
        const $cartBadge = $('.cart-badge');
        if ($cartBadge.length) {
            $cartBadge.text(count);
            
            // 添加动画效果
            $cartBadge.addClass('bounce');
            setTimeout(function() {
                $cartBadge.removeClass('bounce');
            }, 600);
        }
    }
    
    /**
     * 显示通知消息
     */
    function showNotification(message, type) {
        // 移除已存在的通知
        $('.xsrupb-notification').remove();
        
        // 创建通知元素
        const $notification = $('<div>', {
            class: 'xsrupb-notification xsrupb-notification-' + type,
            text: message
        });
        
        // 添加到页面
        $('body').append($notification);
        
        // 显示动画
        setTimeout(function() {
            $notification.addClass('show');
        }, 10);
        
        // 3秒后自动隐藏
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
})(jQuery);
