<!-- 热门推荐 -->
<section class="hot-products">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html(get_theme_mod('hot_products_title', __('热门推荐', 'techvision'))); ?></h2>
        
        <div class="tabs">
            <?php
            // 获取产品分类
            $use_woocommerce = class_exists('WooCommerce');
            
            if ($use_woocommerce) {
                $categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'number' => 5,
                ));
            } else {
                $categories = get_terms(array(
                    'taxonomy' => 'product_category',
                    'hide_empty' => false,
                    'number' => 5,
                ));
            }
            
            if (empty($categories) || is_wp_error($categories)) {
                // 默认分类
                $categories = array(
                    (object)array('name' => __('黑白电子纸显示屏', 'techvision'), 'slug' => 'bw-display'),
                    (object)array('name' => __('多色电子纸显示屏', 'techvision'), 'slug' => 'multi-color'),
                    (object)array('name' => __('彩色电子纸显示屏', 'techvision'), 'slug' => 'color-display'),
                    (object)array('name' => __('开发套件', 'techvision'), 'slug' => 'dev-kit'),
                    (object)array('name' => __('触控与灯光', 'techvision'), 'slug' => 'touch-light'),
                );
            }
            
            foreach ($categories as $index => $category) :
            ?>
                <button class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" onclick="switchTab(<?php echo $index; ?>)">
                    <?php echo esc_html($category->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <?php
        $products_per_tab = get_theme_mod('hot_products_count', 5);
        
        foreach ($categories as $tab_index => $category) :
            $cat_slug = $category->slug;
            $cat_name = $category->name;
        ?>
            <div class="tab-content <?php echo $tab_index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $tab_index; ?>">
                <div class="product-grid">
                    <?php
                    $products_found = false;
                    
                    if ($use_woocommerce) {
                        // WooCommerce产品
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => $products_per_tab,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => $cat_slug,
                                ),
                            ),
                            'meta_key' => '_featured',
                            'meta_value' => 'yes',
                        );
                        
                        $products = new WP_Query($args);
                        
                        if ($products->have_posts()) :
                            $products_found = true;
                            // 显示实际找到的产品数量
                            while ($products->have_posts()) : $products->the_post();
                                techvision_render_product_card(get_the_ID(), 'hot');
                            endwhile;
                            wp_reset_postdata();
                        endif;
                    } else {
                        // 自定义产品
                        $custom_args = array(
                            'post_type' => 'product',
                            'posts_per_page' => $products_per_tab,
                            'meta_key' => 'product_badge',
                            'meta_value' => 'hot',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_category',
                                    'field' => 'slug',
                                    'terms' => $cat_slug,
                                ),
                            ),
                        );
                        
                        $custom_products = new WP_Query($custom_args);
                        
                        if ($custom_products->have_posts()) :
                            $products_found = true;
                            // 显示实际找到的产品数量
                            while ($custom_products->have_posts()) : $custom_products->the_post();
                                techvision_render_product_card(get_the_ID(), 'hot');
                            endwhile;
                            wp_reset_postdata();
                        endif;
                    }
                    
                    // 如果没有找到产品，显示空状态提示
                    if (!$products_found) :
                        techvision_show_empty_products_message(sprintf(__('暂无 %s 商品', 'techvision'), $cat_name));
                    endif;
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
