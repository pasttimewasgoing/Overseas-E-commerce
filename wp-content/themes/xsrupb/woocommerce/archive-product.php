<?php
/**
 * The Template for displaying product archives
 * 完全基于原型图实现
 *
 * @package XSRUPB
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header();

// 获取当前分类信息
$current_cat = null;
$current_cat_id = 0;
$current_parent_id = 0;

if (is_product_category()) {
    $current_cat = get_queried_object();
    $current_cat_id = $current_cat->term_id;
    $current_parent_id = $current_cat->parent;
}
?>

<!-- 头部横幅 -->
<section class="page-banner">
    <div class="banner-image-placeholder"></div>
    <div class="banner-overlay">
        <div class="banner-content">
            <div class="banner-left">
                <div class="banner-images">
                    <div class="banner-placeholder"></div>
                </div>
            </div>
            <div class="banner-right">
                <div class="banner-text">
                    <h1>电子纸屏的明星产品</h1>
                    <p class="banner-subtitle">E-PAPER DISPLAY</p>
                    <p class="banner-desc">卓尔不凡的表现</p>
                    <p class="banner-desc">让您的产品在同行中脱颖而出</p>
                    <a href="#products" class="banner-btn">热销产品 →</a>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumb">
        <div class="container">
            <?php
            if (function_exists('woocommerce_breadcrumb')) {
                woocommerce_breadcrumb(array(
                    'delimiter'   => ' / ',
                    'wrap_before' => '',
                    'wrap_after'  => '',
                    'before'      => '',
                    'after'       => '',
                    'home'        => _x('首页', 'breadcrumb', 'xsrupb'),
                ));
            }
            ?>
        </div>
    </div>
</section>

<!-- 产品页面主体 -->
<section class="products-page" id="products">
    <div class="container">
        <div class="products-layout">
            <!-- 左侧分类栏 -->
            <aside class="category-sidebar">
                <div class="category-header">
                    <h3><?php esc_html_e('产品类别', 'xsrupb'); ?></h3>
                </div>
                <nav class="category-nav">
                    <?php
                    // 获取所有顶级分类
                    $product_categories = get_terms(array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'parent'     => 0,
                    ));

                    if (!empty($product_categories) && !is_wp_error($product_categories)) :
                        foreach ($product_categories as $category) :
                            // 判断当前分类是否激活
                            $is_current_parent = ($current_cat_id == $category->term_id) || ($current_parent_id == $category->term_id);
                            
                            // 获取子分类
                            $child_cats = get_terms(array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => false,
                                'parent'     => $category->term_id,
                            ));
                            
                            $has_children = !empty($child_cats) && !is_wp_error($child_cats);
                            ?>
                            <div class="category-group">
                                <div class="category-title <?php echo $is_current_parent ? 'active' : ''; ?>" data-has-children="<?php echo $has_children ? 'yes' : 'no'; ?>">
                                    <span>🔸</span>
                                    <?php if ($has_children) : ?>
                                        <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php if ($has_children) : ?>
                                    <ul class="category-list" style="<?php echo $is_current_parent ? 'display: block;' : 'display: none;'; ?>">
                                        <?php foreach ($child_cats as $child_cat) : 
                                            $is_child_current = ($current_cat_id == $child_cat->term_id);
                                        ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_term_link($child_cat)); ?>" 
                                                   class="<?php echo $is_child_current ? 'active' : ''; ?>">
                                                    <?php echo esc_html($child_cat->name); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach;
                    endif;
                    ?>
                </nav>
            </aside>
            
            <!-- 右侧产品区域 -->
            <div class="products-content">
                <!-- 搜索框 -->
                <div class="search-container">
                    <form role="search" method="get" class="product-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="text" 
                               id="mainSearch" 
                               name="s" 
                               placeholder="<?php esc_attr_e('搜索产品...', 'xsrupb'); ?>" 
                               class="main-search-input"
                               value="<?php echo get_search_query(); ?>">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="search-btn"><?php esc_html_e('搜索', 'xsrupb'); ?></button>
                    </form>
                </div>
                
                <?php if (woocommerce_product_loop()) : ?>
                    
                    <?php
                    /**
                     * Hook: woocommerce_before_shop_loop
                     * 移除默认的结果计数和排序
                     */
                    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
                    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
                    do_action('woocommerce_before_shop_loop');
                    ?>
                    
                    <!-- 产品网格 -->
                    <div class="product-grid">
                        <?php
                        if (wc_get_loop_prop('total')) {
                            while (have_posts()) {
                                the_post();
                                
                                /**
                                 * Hook: woocommerce_shop_loop
                                 */
                                do_action('woocommerce_shop_loop');
                                
                                wc_get_template_part('content', 'product');
                            }
                        }
                        ?>
                    </div>
                    
                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                    
                    <!-- 更多按钮 -->
                    <?php if (wc_get_loop_prop('total') > wc_get_loop_prop('per_page')) : ?>
                    <div class="more-container">
                        <button class="more-btn"><?php esc_html_e('查看更多产品', 'xsrupb'); ?></button>
                    </div>
                    <?php endif; ?>
                    
                <?php else : ?>
                    
                    <!-- 无产品时显示占位符 -->
                    <div class="product-grid">
                        <?php for ($i = 0; $i < 12; $i++) : ?>
                        <div class="product-card product-placeholder">
                            <div class="product-img">
                                <div class="placeholder-image"></div>
                            </div>
                            <h3><?php esc_html_e('产品名称', 'xsrupb'); ?></h3>
                            <p class="product-desc"><?php esc_html_e('产品描述信息', 'xsrupb'); ?></p>
                            <div class="product-footer">
                                <p class="product-price">¥0.00</p>
                                <button class="buy-btn" disabled><?php esc_html_e('购买', 'xsrupb'); ?></button>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <div class="no-products-message">
                        <p><?php esc_html_e('该分类暂无产品，敬请期待', 'xsrupb'); ?></p>
                    </div>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
