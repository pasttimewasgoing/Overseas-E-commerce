<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package XSRUPB
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container">
        <div class="error-404 not-found" style="text-align: center; padding: 60px 20px;">
            <h1 style="font-size: 6rem; color: #ddd; margin-bottom: 20px;">404</h1>
            <h2 style="font-size: 2rem; margin-bottom: 20px;">
                <?php esc_html_e('页面未找到', 'xsrupb'); ?>
            </h2>
            <p style="color: #666; margin-bottom: 30px;">
                <?php esc_html_e('抱歉，您访问的页面不存在或已被删除。', 'xsrupb'); ?>
            </p>
            
            <div style="margin-bottom: 30px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="button" style="margin-right: 10px;">
                    <?php esc_html_e('返回首页', 'xsrupb'); ?>
                </a>
                
                <?php if (class_exists('WooCommerce')) : ?>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="button">
                    <?php esc_html_e('浏览产品', 'xsrupb'); ?>
                </a>
                <?php endif; ?>
            </div>
            
            <div style="max-width: 500px; margin: 0 auto;">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
