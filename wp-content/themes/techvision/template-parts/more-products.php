<!-- 更多产品 -->
<section class="more-products">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html(get_theme_mod('more_products_title', __('更多产品', 'techvision'))); ?></h2>
        <div class="product-grid">
            <?php
            $products_to_show = get_theme_mod('more_products_count', 5);
            $products_found = false;
            
            // 优先使用WooCommerce产品
            if (class_exists('WooCommerce')) {
                // 首先尝试获取促销产品
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => $products_to_show,
                    'orderby' => 'rand',
                    'meta_query' => array(
                        array(
                            'key' => '_sale_price',
                            'value' => '',
                            'compare' => '!=',
                        ),
                    ),
                );
                
                $products = new WP_Query($args);
                
                if ($products->have_posts()) :
                    $products_found = true;
                    // 显示实际找到的产品数量（有多少显示多少）
                    while ($products->have_posts()) : $products->the_post();
                        techvision_render_product_card(get_the_ID(), 'sale');
                    endwhile;
                    wp_reset_postdata();
                else :
                    // 如果没有促销产品，显示随机产品
                    $random_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => $products_to_show,
                        'orderby' => 'rand',
                    ));
                    
                    if ($random_products->have_posts()) :
                        $products_found = true;
                        // 显示实际找到的产品数量（有多少显示多少）
                        while ($random_products->have_posts()) : $random_products->the_post();
                            techvision_render_product_card(get_the_ID());
                        endwhile;
                        wp_reset_postdata();
                    endif;
                endif;
            }
            
            // 如果没有WooCommerce产品，使用自定义产品
            if (!$products_found) {
                $custom_products = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => $products_to_show,
                    'meta_key' => 'product_badge',
                    'meta_value' => 'sale',
                    'orderby' => 'rand',
                ));
                
                if ($custom_products->have_posts()) :
                    $products_found = true;
                    // 显示实际找到的产品数量（有多少显示多少）
                    while ($custom_products->have_posts()) : $custom_products->the_post();
                        techvision_render_product_card(get_the_ID(), 'sale');
                    endwhile;
                    wp_reset_postdata();
                else :
                    // 显示随机产品
                    $random_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => $products_to_show,
                        'orderby' => 'rand',
                    ));
                    
                    if ($random_products->have_posts()) :
                        $products_found = true;
                        // 显示实际找到的产品数量（有多少显示多少，即使只有1个）
                        while ($random_products->have_posts()) : $random_products->the_post();
                            techvision_render_product_card(get_the_ID());
                        endwhile;
                        wp_reset_postdata();
                    endif;
                endif;
            }
            
            // 如果没有找到任何产品，显示空状态提示
            if (!$products_found) :
                techvision_show_empty_products_message(__('暂无更多商品', 'techvision'));
            endif;
            ?>
        </div>
    </div>
</section>
