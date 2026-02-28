<?php
/**
 * 首页模板
 *
 * @package XSRUPB
 */

get_header();
?>

<!-- 轮播图 -->
<section class="carousel">
    <div class="container">
        <div class="carousel-wrapper">
            <?php
            // 获取轮播图设置
            $carousel_items = array(
                array(
                    'title' => __('最新电子纸技术', 'xsrupb'),
                    'desc' => __('超低功耗，护眼显示，适合各种应用场景', 'xsrupb'),
                ),
                array(
                    'title' => __('开发套件全新上市', 'xsrupb'),
                    'desc' => __('快速原型开发，助力产品创新', 'xsrupb'),
                ),
                array(
                    'title' => __('智能传感器解决方案', 'xsrupb'),
                    'desc' => __('高精度检测，智能化应用', 'xsrupb'),
                ),
                array(
                    'title' => __('限时优惠活动', 'xsrupb'),
                    'desc' => __('精选产品，超值价格', 'xsrupb'),
                ),
            );
            
            foreach ($carousel_items as $index => $item) :
                $active_class = ($index === 0) ? 'active' : '';
            ?>
            <div class="carousel-item <?php echo esc_attr($active_class); ?>">
                <div class="carousel-img"></div>
                <div class="carousel-caption">
                    <h2><?php echo esc_html($item['title']); ?></h2>
                    <p><?php echo esc_html($item['desc']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            
            <button class="carousel-btn prev" onclick="changeSlide(-1)">❮</button>
            <button class="carousel-btn next" onclick="changeSlide(1)">❯</button>
            <div class="carousel-dots">
                <?php foreach ($carousel_items as $index => $item) : ?>
                    <span class="dot <?php echo ($index === 0) ? 'active' : ''; ?>" onclick="currentSlide(<?php echo esc_attr($index); ?>)"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 新品上线 -->
<section class="new-products">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('新品上线', 'xsrupb'); ?></h2>
        <div class="product-grid">
            <?php
            if (class_exists('WooCommerce')) {
                // 查询带有"新品上线"标签的产品
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_tag',
                            'field'    => 'slug',
                            'terms'    => '新品上线', // 新品上线标签的 slug
                        ),
                    ),
                );
                
                $new_products = new WP_Query($args);
                
                if ($new_products->have_posts()) {
                    while ($new_products->have_posts()) {
                        $new_products->the_post();
                        global $product;
                        ?>
                        <div class="product-card">
                            <div class="product-img">
                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(wc_placeholder_img_src('medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>
                                </a>
                                <span class="badge badge-new"><?php esc_html_e('NEW', 'xsrupb'); ?></span>
                            </div>
                            <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h3>
                            <p class="product-desc"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                            <div class="product-footer">
                                <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <button class="buy-btn" onclick="location.href='<?php echo esc_url(get_permalink()); ?>'"><?php esc_html_e('购买', 'xsrupb'); ?></button>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="empty-placeholder" style="grid-column: 1 / -1;">';
                    echo '<div class="empty-icon">📦</div>';
                    echo '<h3>' . esc_html__('暂无新品', 'xsrupb') . '</h3>';
                    echo '<p>' . esc_html__('暂时没有新品上线，敬请期待', 'xsrupb') . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- 热门推荐 -->
<section class="hot-products">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('热门推荐', 'xsrupb'); ?></h2>
        <div class="tabs">
            <?php
            if (class_exists('WooCommerce')) {
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'number' => 5,
                ));
                
                if (!empty($product_categories) && !is_wp_error($product_categories)) {
                    foreach ($product_categories as $index => $category) {
                        $active_class = ($index === 0) ? 'active' : '';
                        echo '<button class="tab-btn ' . esc_attr($active_class) . '" onclick="switchTab(' . esc_attr($index) . ')">' . esc_html($category->name) . '</button>';
                    }
                }
            }
            ?>
        </div>
        <?php
        if (class_exists('WooCommerce') && !empty($product_categories) && !is_wp_error($product_categories)) {
            foreach ($product_categories as $index => $category) {
                $active_class = ($index === 0) ? 'active' : '';
                ?>
                <div class="tab-content <?php echo esc_attr($active_class); ?>" data-tab="<?php echo esc_attr($index); ?>">
                    <div class="product-grid">
                        <?php
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 5,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ),
                            ),
                        );
                        
                        $category_products = new WP_Query($args);
                        
                        if ($category_products->have_posts()) {
                            while ($category_products->have_posts()) {
                                $category_products->the_post();
                                global $product;
                                ?>
                                <div class="product-card">
                                    <div class="product-img">
                                        <a href="<?php echo esc_url(get_permalink()); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                                            <?php else : ?>
                                                <img src="<?php echo esc_url(wc_placeholder_img_src('medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                            <?php endif; ?>
                                        </a>
                                        <span class="badge badge-hot"><?php esc_html_e('HOT', 'xsrupb'); ?></span>
                                    </div>
                                    <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h3>
                                    <p class="product-desc"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                                    <div class="product-footer">
                                        <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                        <button class="buy-btn" onclick="location.href='<?php echo esc_url(get_permalink()); ?>'"><?php esc_html_e('购买', 'xsrupb'); ?></button>
                                    </div>
                                </div>
                                <?php
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<div class="empty-placeholder" style="grid-column: 1 / -1;">';
                            echo '<div class="empty-icon">📦</div>';
                            echo '<h3>' . esc_html__('暂无商品', 'xsrupb') . '</h3>';
                            echo '<p>' . esc_html__('该分类下暂时没有商品，敬请期待', 'xsrupb') . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</section>

<!-- 视频区域 -->
<section class="video-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('产品视频', 'xsrupb'); ?></h2>
        <div class="video-grid">
            <?php
            $videos = array(
                array(
                    'title' => __('电子纸技术介绍', 'xsrupb'),
                    'desc' => __('了解电子纸显示技术的原理和优势', 'xsrupb'),
                ),
                array(
                    'title' => __('开发套件使用教程', 'xsrupb'),
                    'desc' => __('快速上手开发套件，实现创意项目', 'xsrupb'),
                ),
                array(
                    'title' => __('应用案例展示', 'xsrupb'),
                    'desc' => __('看看我们的产品如何改变生活', 'xsrupb'),
                ),
            );
            
            foreach ($videos as $video) :
            ?>
            <div class="video-card">
                <div class="video-placeholder">
                    <div class="video-img"></div>
                    <div class="play-btn">▶</div>
                </div>
                <h3><?php echo esc_html($video['title']); ?></h3>
                <p><?php echo esc_html($video['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 更多产品 -->
<section class="more-products">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('更多产品', 'xsrupb'); ?></h2>
        <div class="product-grid">
            <?php
            if (class_exists('WooCommerce')) {
                // 查询除了"新品上线"标签之外的所有产品
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 10,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_tag',
                            'field'    => 'slug',
                            'terms'    => 'new-arrival', // 排除新品上线标签
                            'operator' => 'NOT IN',
                        ),
                    ),
                );
                
                $more_products = new WP_Query($args);
                
                if ($more_products->have_posts()) {
                    while ($more_products->have_posts()) {
                        $more_products->the_post();
                        global $product;
                        ?>
                        <div class="product-card">
                            <div class="product-img">
                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(wc_placeholder_img_src('medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>
                                </a>
                                <?php if ($product->is_on_sale()) : ?>
                                    <span class="badge badge-sale"><?php esc_html_e('SALE', 'xsrupb'); ?></span>
                                <?php elseif ($product->is_featured()) : ?>
                                    <span class="badge badge-hot"><?php esc_html_e('HOT', 'xsrupb'); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h3>
                            <p class="product-desc"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                            <div class="product-footer">
                                <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <button class="buy-btn" onclick="location.href='<?php echo esc_url(get_permalink()); ?>'"><?php esc_html_e('购买', 'xsrupb'); ?></button>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="empty-placeholder" style="grid-column: 1 / -1;">';
                    echo '<div class="empty-icon">📦</div>';
                    echo '<h3>' . esc_html__('暂无商品', 'xsrupb') . '</h3>';
                    echo '<p>' . esc_html__('暂时没有更多商品，敬请期待', 'xsrupb') . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <div class="more-container">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="more-btn">
                <?php esc_html_e('查看更多产品', 'xsrupb'); ?> →
            </a>
        </div>
    </div>
</section>

<?php
get_footer();
