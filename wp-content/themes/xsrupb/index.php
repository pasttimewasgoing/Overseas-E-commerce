<?php
/**
 * The main template file
 *
 * @package XSRUPB
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="container">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h2 class="entry-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php echo esc_html(get_the_title()); ?>
                            </a>
                        </h2>
                    </header>
                    
                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            
            // 分页导航
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('上一页', 'xsrupb'),
                'next_text' => __('下一页', 'xsrupb'),
            ));
        else :
            ?>
            <p><?php esc_html_e('暂无内容', 'xsrupb'); ?></p>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
