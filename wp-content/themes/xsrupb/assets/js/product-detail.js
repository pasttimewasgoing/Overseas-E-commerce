/**
 * 产品详情页面交互功能
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // 缩略图切换主图
        $('.thumbnail').on('click', function() {
            const $thumbnail = $(this);
            const $mainImage = $('.main-image img');
            const newImageSrc = $thumbnail.find('img').attr('src');
            const newImageSrcset = $thumbnail.find('img').attr('srcset');
            
            if (newImageSrc) {
                // 更新主图
                $mainImage.attr('src', newImageSrc);
                if (newImageSrcset) {
                    $mainImage.attr('srcset', newImageSrcset);
                }
                
                // 更新激活状态
                $('.thumbnail').removeClass('active');
                $thumbnail.addClass('active');
                
                // 添加淡入效果
                $mainImage.css('opacity', 0);
                setTimeout(function() {
                    $mainImage.css('opacity', 1);
                }, 50);
            }
        });
        
        // 产品标签页切换
        $('.woocommerce-tabs .tabs li a').on('click', function(e) {
            e.preventDefault();
            
            const $tab = $(this).parent();
            const targetPanel = $(this).attr('href');
            
            // 更新标签激活状态
            $('.woocommerce-tabs .tabs li').removeClass('active');
            $tab.addClass('active');
            
            // 显示对应面板
            $('.woocommerce-tabs .panel').hide();
            $(targetPanel).fadeIn(300);
        });
        
        // 数量选择器 - 增加
        $(document).on('click', '.qty-btn.plus', function(e) {
            e.preventDefault();
            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val()) || 1;
            const max = parseInt($input.attr('max')) || 999;
            
            if (currentVal < max) {
                $input.val(currentVal + 1).trigger('change');
            }
        });
        
        // 数量选择器 - 减少
        $(document).on('click', '.qty-btn.minus', function(e) {
            e.preventDefault();
            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val()) || 1;
            const min = parseInt($input.attr('min')) || 1;
            
            if (currentVal > min) {
                $input.val(currentVal - 1).trigger('change');
            }
        });
        
        // 添加到购物车
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            const quantity = parseInt($('.qty-input').val()) || 1;
            
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
                    quantity: quantity,
                    nonce: xsrupb_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 更新购物车数量
                        updateCartCount(response.data.cart_count);
                        
                        // 显示成功消息
                        $button.text('已添加到购物车 ✓');
                        $button.css({
                            'background': '#28a745',
                            'color': '#fff',
                            'border-color': '#28a745'
                        });
                        
                        setTimeout(function() {
                            $button.text(originalText);
                            $button.css({
                                'background': '',
                                'color': '',
                                'border-color': ''
                            });
                        }, 2000);
                        
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
                }
            });
        });
        
        // 立即购买
        $(document).on('click', '.buy-now-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            const quantity = parseInt($('.qty-input').val()) || 1;
            
            if (!productId || $button.prop('disabled')) {
                return;
            }
            
            // 先添加到购物车
            $.ajax({
                url: xsrupb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: xsrupb_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // 跳转到购物车页面
                        window.location.href = wc_cart_params.cart_url || '/cart/';
                    } else {
                        showNotification(response.data.message || '操作失败，请重试', 'error');
                    }
                },
                error: function() {
                    showNotification('网络错误，请重试', 'error');
                }
            });
        });
        
        // 收藏按钮
        $(document).on('click', '.wishlist-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const isFavorite = $button.hasClass('favorited');
            
            if (isFavorite) {
                $button.removeClass('favorited');
                $button.css({
                    'color': '',
                    'border-color': ''
                });
                $button.html('❤');
                showNotification('已从收藏中移除', 'info');
            } else {
                $button.addClass('favorited');
                $button.css({
                    'color': '#ff4757',
                    'border-color': '#ff4757'
                });
                $button.html('❤️');
                showNotification('已添加到收藏', 'success');
            }
        });
        
        // 快速添加相关产品到购物车
        $(document).on('click', '.quick-add-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $productCard = $button.closest('.product-card');
            const productId = $productCard.data('product-id');
            
            if (!productId) {
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
        
        // 图片放大功能（简单版）
        $('.main-image').on('click', function() {
            const $img = $(this).find('img');
            const imgSrc = $img.attr('src');
            
            // 创建模态框
            const $modal = $('<div>', {
                class: 'image-modal',
                html: '<div class="modal-content"><img src="' + imgSrc + '"><span class="close-modal">&times;</span></div>'
            });
            
            $('body').append($modal);
            
            setTimeout(function() {
                $modal.addClass('show');
            }, 10);
            
            // 关闭模态框
            $modal.on('click', function(e) {
                if ($(e.target).hasClass('image-modal') || $(e.target).hasClass('close-modal')) {
                    $modal.removeClass('show');
                    setTimeout(function() {
                        $modal.remove();
                    }, 300);
                }
            });
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
