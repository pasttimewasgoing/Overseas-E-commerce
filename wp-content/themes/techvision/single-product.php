<?php
/**
 * 单个产品模板
 */
get_header(); ?>

<main id="main" class="site-main single-product-page">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            
            // 检查是否是WooCommerce产品
            if (class_exists('WooCommerce') && get_post_type() === 'product') {
                // 使用WooCommerce模板
                wc_get_template_part('content', 'single-product');
            } else {
                // 自定义产品模板
                $price = get_post_meta(get_the_ID(), 'product_price', true);
                $old_price = get_post_meta(get_the_ID(), 'product_old_price', true);
                $badge = get_post_meta(get_the_ID(), 'product_badge', true);
                $stock = get_post_meta(get_the_ID(), 'product_stock', true);
                $sku = get_post_meta(get_the_ID(), 'product_sku', true);
        ?>
            <article id="product-<?php the_ID(); ?>" <?php post_class('product-single'); ?>>
                <div class="product-layout">
                    <div class="product-images">
                        <div class="product-main-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php else : ?>
                                <div class="placeholder large">
                                    <div class="placeholder-icon">📦</div>
                                    <span><?php _e('暂无图片', 'techvision'); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($badge) : ?>
                                <span class="badge badge-<?php echo esc_attr($badge); ?>">
                                    <?php echo esc_html(strtoupper($badge)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php
                        // 产品图库
                        $gallery = get_post_meta(get_the_ID(), 'product_gallery', true);
                        if ($gallery) :
                            $gallery_ids = explode(',', $gallery);
                            if (!empty($gallery_ids)) :
                        ?>
                            <div class="product-gallery">
                                <?php foreach ($gallery_ids as $image_id) : ?>
                                    <div class="gallery-item">
                                        <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php 
                            endif;
                        endif;
                        ?>
                    </div>
                    
                    <div class="product-summary">
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        
                        <?php if ($sku) : ?>
                            <div class="product-meta">
                                <span class="sku"><?php _e('SKU:', 'techvision'); ?> <?php echo esc_html($sku); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($price) : ?>
                            <div class="product-price-wrap">
                                <?php if ($old_price && (float)$old_price > (float)$price) : ?>
                                    <del class="old-price">¥<?php echo esc_html(number_format((float)$old_price, 2)); ?></del>
                                <?php endif; ?>
                                <span class="product-price">¥<?php echo esc_html(number_format((float)$price, 2)); ?></span>
                            </div>
                        <?php else : ?>
                            <div class="product-price-wrap">
                                <span class="price-unavailable"><?php _e('价格待定', 'techvision'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-description">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php if ($stock !== '0') : ?>
                            <div class="product-actions">
                                <div class="quantity-selector">
                                    <button class="qty-btn minus" onclick="updateQuantity(-1)">-</button>
                                    <input type="number" id="product-quantity" value="1" min="1" max="<?php echo $stock ? esc_attr($stock) : '999'; ?>">
                                    <button class="qty-btn plus" onclick="updateQuantity(1)">+</button>
                                </div>
                                <button class="buy-btn add-to-cart" onclick="addToCart(<?php the_ID(); ?>)">
                                    <?php _e('加入购物车', 'techvision'); ?>
                                </button>
                                <button class="buy-btn buy-now" onclick="buyNow(<?php the_ID(); ?>)">
                                    <?php _e('立即购买', 'techvision'); ?>
                                </button>
                            </div>
                            
                            <?php if ($stock) : ?>
                                <div class="stock-info">
                                    <?php printf(__('库存: %s 件', 'techvision'), esc_html($stock)); ?>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="out-of-stock">
                                <p><?php _e('暂时缺货', 'techvision'); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        // 产品分类和标签
                        $categories = get_the_terms(get_the_ID(), 'product_category');
                        $tags = get_the_terms(get_the_ID(), 'product_tag');
                        ?>
                        
                        <div class="product-meta-info">
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                                <div class="product-categories">
                                    <strong><?php _e('分类:', 'techvision'); ?></strong>
                                    <?php
                                    $cat_links = array();
                                    foreach ($categories as $category) {
                                        $cat_links[] = '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a>';
                                    }
                                    echo implode(', ', $cat_links);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($tags && !is_wp_error($tags)) : ?>
                                <div class="product-tags">
                                    <strong><?php _e('标签:', 'techvision'); ?></strong>
                                    <?php
                                    $tag_links = array();
                                    foreach ($tags as $tag) {
                                        $tag_links[] = '<a href="' . get_term_link($tag) . '">' . esc_html($tag->name) . '</a>';
                                    }
                                    echo implode(', ', $tag_links);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php
                // 产品详细信息标签
                $specifications = get_post_meta(get_the_ID(), 'product_specifications', true);
                $features = get_post_meta(get_the_ID(), 'product_features', true);
                ?>
                
                <?php if ($specifications || $features) : ?>
                    <div class="product-tabs">
                        <div class="tab-buttons">
                            <?php if ($specifications) : ?>
                                <button class="tab-button active" onclick="showProductTab('specifications')">
                                    <?php _e('产品规格', 'techvision'); ?>
                                </button>
                            <?php endif; ?>
                            <?php if ($features) : ?>
                                <button class="tab-button" onclick="showProductTab('features')">
                                    <?php _e('产品特性', 'techvision'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-contents">
                            <?php if ($specifications) : ?>
                                <div id="specifications" class="tab-panel active">
                                    <?php echo wp_kses_post($specifications); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($features) : ?>
                                <div id="features" class="tab-panel">
                                    <?php echo wp_kses_post($features); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php
                // 相关产品
                $related_products = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => 4,
                    'post__not_in' => array(get_the_ID()),
                    'orderby' => 'rand',
                ));
                
                if ($related_products->have_posts()) :
                ?>
                    <div class="related-products">
                        <h2><?php _e('相关产品', 'techvision'); ?></h2>
                        <div class="product-grid">
                            <?php
                            while ($related_products->have_posts()) : $related_products->the_post();
                                $rel_price = get_post_meta(get_the_ID(), 'product_price', true);
                            ?>
                                <div class="product-card">
                                    <div class="product-img">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('medium'); ?>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <?php if ($rel_price) : ?>
                                        <p class="product-price">¥<?php echo esc_html(number_format((float)$rel_price, 2)); ?></p>
                                    <?php endif; ?>
                                    <button class="buy-btn" onclick="location.href='<?php the_permalink(); ?>'"><?php _e('查看详情', 'techvision'); ?></button>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php
                    wp_reset_postdata();
                endif;
                ?>
            </article>
        <?php
            }
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
