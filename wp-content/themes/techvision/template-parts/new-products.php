<!-- 新品上线 -->
<section class="new-products">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html(get_theme_mod('new_products_title', __('新品上线', 'techvision'))); ?></h2>
        <div class="product-grid">
            <?php
            $products_to_show = get_theme_mod('new_products_count', 5);
            $products_found = false;
            
            // 优先使用WooCommerce产品
            if (class_exists('WooCommerce')) {
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => $products_to_show,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_tag',
                            'field' => 'slug',
                            'terms' => 'new',
                        ),
                    ),
                );
                
                $products = new WP_Query($args);
                
                if ($products->have_posts()) :
                    $products_found = true;
                    // 显示实际找到的产品数量
                    while ($products->have_posts()) : $products->the_post();
                        techvision_render_product_card(get_the_ID(), 'new');
                    endwhile;
                    wp_reset_postdata();
                endif;
            }
            
            // 如果没有WooCommerce产品，使用自定义产品
            if (!$products_found) {
                $custom_products = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => $products_to_show,
                    'meta_key' => 'product_badge',
                    'meta_value' => 'new',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($custom_products->have_posts()) :
                    $products_found = true;
                    // 显示实际找到的产品数量
                    while ($custom_products->have_posts()) : $custom_products->the_post();
                        techvision_render_product_card(get_the_ID(), 'new');
                    endwhile;
                    wp_reset_postdata();
                endif;
            }
            
            // 如果没有找到任何产品，显示空状态提示
            if (!$products_found) :
                techvision_show_empty_products_message(__('暂无新品上线', 'techvision'));
            endif;
            ?>
        </div>
    </div>
</section>
