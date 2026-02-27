<?php
/**
 * The Template for displaying single products
 *
 * @package XSRUPB
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header('shop');
?>

<!-- 面包屑导航 -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="breadcrumb-nav">
            <?php
            if (function_exists('woocommerce_breadcrumb')) {
                woocommerce_breadcrumb(array(
                    'delimiter'   => '<span class="separator">/</span>',
                    'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                    'wrap_after'  => '</nav>',
                    'before'      => '',
                    'after'       => '',
                    'home'        => _x('首页', 'breadcrumb', 'xsrupb'),
                ));
            }
            ?>
        </div>
    </div>
</section>

<!-- 产品详情 -->
<section class="product-detail-section">
    <div class="container">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            
            <?php wc_get_template_part('content', 'single-product'); ?>
            
        <?php endwhile; ?>
    </div>
</section>

<!-- 产品详细信息标签页 -->
<section class="product-tabs-section">
    <div class="container">
        <?php
        /**
         * Hook: woocommerce_after_single_product_summary
         */
        do_action('woocommerce_after_single_product_summary');
        ?>
    </div>
</section>

<!-- 相关产品推荐 -->
<?php
$related_products = wc_get_related_products(get_the_ID(), 4);
if (!empty($related_products)) :
?>
<section class="related-products-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('相关产品推荐', 'xsrupb'); ?></h2>
        <div class="products-grid">
            <?php
            foreach ($related_products as $related_product_id) :
                $post_object = get_post($related_product_id);
                setup_postdata($GLOBALS['post'] =& $post_object);
                wc_get_template_part('content', 'product');
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
get_footer('shop');
