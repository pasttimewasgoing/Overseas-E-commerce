<?php
/**
 * 404错误页面模板
 */
get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php _e('页面未找到', 'techvision'); ?></h1>
            </header>
            
            <div class="page-content">
                <p><?php _e('抱歉，您访问的页面不存在。可能已被删除或移动。', 'techvision'); ?></p>
                
                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                        <?php _e('返回首页', 'techvision'); ?>
                    </a>
                    
                    <?php get_search_form(); ?>
                </div>
                
                <div class="widget-area">
                    <h2><?php _e('热门产品', 'techvision'); ?></h2>
                    <?php
                    $popular_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 3,
                        'orderby' => 'rand',
                    ));
                    
                    if ($popular_products->have_posts()) :
                        echo '<div class="product-grid">';
                        while ($popular_products->have_posts()) : $popular_products->the_post();
                            $price = get_post_meta(get_the_ID(), 'product_price', true);
                    ?>
                        <div class="product-card">
                            <div class="product-img">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php endif; ?>
                            </div>
                            <h3><?php the_title(); ?></h3>
                            <?php if ($price) : ?>
                                <p class="product-price">¥<?php echo esc_html($price); ?></p>
                            <?php endif; ?>
                            <button class="buy-btn" onclick="location.href='<?php the_permalink(); ?>'"><?php _e('查看详情', 'techvision'); ?></button>
                        </div>
                    <?php
                        endwhile;
                        echo '</div>';
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
