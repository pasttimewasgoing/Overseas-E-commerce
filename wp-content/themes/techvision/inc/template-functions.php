<?php
/**
 * 模板辅助函数
 */

/**
 * 获取产品图片或占位符
 */
function techvision_get_product_image($post_id = null, $size = 'medium', $with_link = true) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $output = '';
    
    if ($with_link) {
        $output .= '<a href="' . get_permalink($post_id) . '">';
    }
    
    if (has_post_thumbnail($post_id)) {
        $output .= get_the_post_thumbnail($post_id, $size);
    } else {
        // 检查是否是WooCommerce产品
        if (class_exists('WooCommerce') && get_post_type($post_id) === 'product') {
            $output .= '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . get_the_title($post_id) . '">';
        } else {
            // 自定义占位符
            $output .= '<div class="placeholder">';
            $output .= '<div class="placeholder-icon">📦</div>';
            $output .= '<span>' . __('暂无图片', 'techvision') . '</span>';
            $output .= '</div>';
        }
    }
    
    if ($with_link) {
        $output .= '</a>';
    }
    
    return $output;
}

/**
 * 获取产品价格
 */
function techvision_get_product_price($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 检查是否是WooCommerce产品
    if (class_exists('WooCommerce') && get_post_type($post_id) === 'product') {
        $product = wc_get_product($post_id);
        return $product->get_price_html();
    } else {
        // 自定义产品价格
        $price = get_post_meta($post_id, 'product_price', true);
        $old_price = get_post_meta($post_id, 'product_old_price', true);
        
        if (!$price) {
            return '<span class="price-unavailable">' . __('价格待定', 'techvision') . '</span>';
        }
        
        $output = '';
        
        if ($old_price && (float)$old_price > (float)$price) {
            $output .= '<del>¥' . number_format((float)$old_price, 2) . '</del> ';
        }
        
        $output .= '¥' . number_format((float)$price, 2);
        
        return $output;
    }
}

/**
 * 获取产品标题（带链接）
 */
function techvision_get_product_title($post_id = null, $with_link = true) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $title = get_the_title($post_id);
    
    if ($with_link) {
        return '<a href="' . get_permalink($post_id) . '">' . esc_html($title) . '</a>';
    }
    
    return esc_html($title);
}

/**
 * 获取产品描述
 */
function techvision_get_product_description($post_id = null, $words = 10) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $excerpt = get_the_excerpt($post_id);
    
    if (!$excerpt) {
        return '<span class="no-description">' . __('暂无描述', 'techvision') . '</span>';
    }
    
    return wp_trim_words($excerpt, $words);
}

/**
 * 获取产品徽章
 */
function techvision_get_product_badge($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 检查是否是WooCommerce产品
    if (class_exists('WooCommerce') && get_post_type($post_id) === 'product') {
        $product = wc_get_product($post_id);
        
        if ($product->is_on_sale()) {
            return '<span class="badge badge-sale">SALE</span>';
        }
        
        if ($product->is_featured()) {
            return '<span class="badge badge-hot">HOT</span>';
        }
        
        // 检查是否是新产品（30天内）
        $created = strtotime($product->get_date_created());
        $now = time();
        $days = ($now - $created) / (60 * 60 * 24);
        
        if ($days <= 30) {
            return '<span class="badge badge-new">NEW</span>';
        }
    } else {
        // 自定义产品徽章
        $badge = get_post_meta($post_id, 'product_badge', true);
        
        if ($badge) {
            $badge_class = 'badge-' . esc_attr($badge);
            $badge_text = strtoupper($badge);
            return '<span class="badge ' . $badge_class . '">' . esc_html($badge_text) . '</span>';
        }
    }
    
    return '';
}

/**
 * 检查产品是否有库存
 */
function techvision_product_in_stock($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 检查是否是WooCommerce产品
    if (class_exists('WooCommerce') && get_post_type($post_id) === 'product') {
        $product = wc_get_product($post_id);
        return $product->is_in_stock();
    } else {
        // 自定义产品库存
        $stock = get_post_meta($post_id, 'product_stock', true);
        return $stock !== '0' && $stock !== '';
    }
}

/**
 * 渲染产品卡片
 */
function techvision_render_product_card($post_id = null, $badge_type = '') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    ?>
    <div class="product-card">
        <div class="product-img">
            <?php echo techvision_get_product_image($post_id, 'medium', true); ?>
            <?php 
            if ($badge_type) {
                echo '<span class="badge badge-' . esc_attr($badge_type) . '">' . esc_html(strtoupper($badge_type)) . '</span>';
            } else {
                echo techvision_get_product_badge($post_id);
            }
            ?>
        </div>
        <h3><?php echo techvision_get_product_title($post_id, true); ?></h3>
        <p class="product-desc"><?php echo techvision_get_product_description($post_id, 10); ?></p>
        <p class="product-price"><?php echo techvision_get_product_price($post_id); ?></p>
        <?php if (techvision_product_in_stock($post_id)) : ?>
            <button class="buy-btn" onclick="location.href='<?php echo get_permalink($post_id); ?>'">
                <?php _e('立即购买', 'techvision'); ?>
            </button>
        <?php else : ?>
            <button class="buy-btn out-of-stock" disabled>
                <?php _e('暂时缺货', 'techvision'); ?>
            </button>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 显示空产品提示
 */
function techvision_show_empty_products_message($message = '') {
    if (!$message) {
        $message = __('暂无商品', 'techvision');
    }
    ?>
    <div class="empty-products-message">
        <div class="empty-icon">📦</div>
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
}
